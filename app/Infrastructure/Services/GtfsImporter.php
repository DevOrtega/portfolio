<?php

namespace App\Infrastructure\Services;

class GtfsImporter
{
    private string $gtfsPath;

    public function __construct(?string $gtfsPath = null)
    {
        $this->gtfsPath = $gtfsPath ?? base_path('gtfs');
    }

    /**
     * Devuelve todas las líneas del GTFS con estructura para el seeder
     * @param string|null $targetAgency Filtro opcional por agency_id
     * @return array
     */
    public function getRoutes(?string $targetAgency = null): array
    {
        // 1. Cargar Rutas (Routes)
        $routes = $this->readCsv($this->gtfsPath . '/routes.csv');
        $filteredRoutes = [];
        foreach ($routes as $route) {
            $agency = isset($route['agency_id']) ? strtolower($route['agency_id']) : '';
            if ($targetAgency && $agency !== strtolower($targetAgency)) {
                continue;
            }
            $filteredRoutes[] = $route;
        }

        if (empty($filteredRoutes)) {
            return [];
        }

        // 2. Mapear Route ID -> Trip ID representativo
        $trips = $this->readCsv($this->gtfsPath . '/trips.csv');
        $routeToTripMap = []; 
        foreach ($trips as $trip) {
            $routeId = $trip['route_id'];
            if (!isset($routeToTripMap[$routeId])) {
                $routeToTripMap[$routeId] = $trip['trip_id'];
            }
        }

        // 3. Obtener Stop Times
        $stopTimes = $this->readCsv($this->gtfsPath . '/stop_times.csv');
        $tripStopsMap = []; 
        $selectedTrips = array_flip($routeToTripMap);

        foreach ($stopTimes as $st) {
            $tripId = $st['trip_id'];
            if (isset($selectedTrips[$tripId])) {
                $tripStopsMap[$tripId][] = [
                    'stop_id' => $st['stop_id'],
                    'sequence' => (int)$st['stop_sequence']
                ];
            }
        }

        foreach ($tripStopsMap as $tripId => &$stops) {
            usort($stops, fn($a, $b) => $a['sequence'] <=> $b['sequence']);
        }
        unset($stops);

        // 4. Cargar Paradas
        $allStops = $this->readCsv($this->gtfsPath . '/stops.csv');
        $stopsMap = [];
        foreach ($allStops as $stop) {
            $stopsMap[$stop['stop_id']] = $stop;
        }

        // 5. Construir resultado
        $result = [];
        foreach ($filteredRoutes as $route) {
            $routeId = $route['route_id'];
            $tripId = $routeToTripMap[$routeId] ?? null;

            if (!$tripId || !isset($tripStopsMap[$tripId])) {
                continue;
            }

            $routeStopsData = [];
            foreach ($tripStopsMap[$tripId] as $st) {
                $stopId = $st['stop_id'];
                if (isset($stopsMap[$stopId])) {
                    $stop = $stopsMap[$stopId];
                    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $stop['stop_name']));
                    $routeStopsData[] = $slug;
                }
            }

            $color = isset($route['route_color']) ? ('#' . ltrim($route['route_color'], '#')) : '#FDB913';
            $origin = '';
            $destination = '';
            if (isset($route['route_long_name'])) {
                $parts = preg_split('/\s*-\s*/', $route['route_long_name']);
                $origin = $parts[0] ?? '';
                $destination = $parts[1] ?? '';
            }

            // Determine metadata based on agency
            $agency = strtolower($route['agency_id'] ?? '');
            
            if ($agency === 'global') {
                $company = 'global';
                $type = 'interurban';
                // Override origin/dest if not parsed correctly from long_name
                // In our generator we set long_name correctly "Origin - Dest"
            } else {
                // Default logic for Municipales
                $isNight = str_starts_with(strtoupper($route['route_short_name']), 'L');
                $company = $isNight ? 'night' : 'municipales';
                $type = $isNight ? 'night' : 'urban';
            }

            $result[] = [
                'line' => $route['route_short_name'],
                'type' => $type,
                'company' => $company,
                'origin' => $origin,
                'destination' => $destination,
                'color' => $color,
                'stopsOutbound' => $routeStopsData,
                'stopsInbound' => $routeStopsData, // Simplification
            ];
        }

        return $result;
    }

    /**
     * Obtiene todas las paradas del GTFS formateadas para inserción
     * @return array
     */
    public function getAllStops(): array
    {
        $stops = $this->readCsv($this->gtfsPath . '/stops.csv');
        $result = [];
        foreach ($stops as $stop) {
            $code = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $stop['stop_name']));
            // Evitar duplicados por código (mismo nombre)
            if (!isset($result[$code])) {
                $result[$code] = [
                    'code' => $code,
                    'name' => $stop['stop_name'],
                    'lat' => (float)$stop['stop_lat'],
                    'lng' => (float)$stop['stop_lon'],
                ];
            }
        }
        return array_values($result);
    }

    /**
     * Lee un archivo CSV y devuelve un array asociativo
     */
    private function readCsv(string $file): array
    {
        $rows = [];
        if (!file_exists($file)) return $rows;
        $handle = fopen($file, 'r');
        $headers = fgetcsv($handle);
        
        // Limpiar BOM de los headers si existe
        if ($headers && isset($headers[0])) {
             $headers[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $headers[0]);
        }

        while (($data = fgetcsv($handle)) !== false) {
            // Asegurarse de que el número de columnas coincida con los headers
            if (count($headers) === count($data)) {
                $rows[] = array_combine($headers, $data);
            }
        }
        fclose($handle);
        return $rows;
    }
    
    // Método legacy mantenido por compatibilidad si fuera necesario, 
    // pero ya no usado por la carga masiva optimizada.
    public function getStopsForLine(string $routeShortName): array
    {
        return [];
    }
}