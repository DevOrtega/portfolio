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
     * Devuelve todas las líneas municipales del GTFS con estructura para el seeder
     * Optimizado para lectura única de archivos CSV.
     * @return array
     */
    public function getAllMunicipalRoutes(): array
    {
        // 1. Cargar Rutas (Routes)
        $routes = $this->readCsv($this->gtfsPath . '/routes.csv');
        $municipalRoutes = [];
        foreach ($routes as $route) {
            // Solo líneas de Guaguas Municipales (agency_id = Guaguas)
            if (isset($route['agency_id']) && strtolower($route['agency_id']) === 'guaguas') {
                $municipalRoutes[] = $route;
            }
        }

        if (empty($municipalRoutes)) {
            return [];
        }

        // 2. Mapear Route ID -> Trip ID representativo
        // Leemos trips.csv una sola vez
        $trips = $this->readCsv($this->gtfsPath . '/trips.csv');
        $routeToTripMap = []; // route_id => trip_id
        
        // Optimización: Crear mapa rápido de búsqueda
        foreach ($trips as $trip) {
            $routeId = $trip['route_id'];
            // Nos quedamos con el primer trip encontrado para cada ruta
            if (!isset($routeToTripMap[$routeId])) {
                $routeToTripMap[$routeId] = $trip['trip_id'];
            }
        }

        // 3. Obtener Stop Times para los trips seleccionados
        // Leemos stop_times.csv una sola vez
        // Esto es 6MB, manejable en memoria.
        $stopTimes = $this->readCsv($this->gtfsPath . '/stop_times.csv');
        $tripStopsMap = []; // trip_id => [ {stop_id, sequence} ]

        // Convertir array de trips seleccionados a hash map para búsqueda rápida O(1)
        $selectedTrips = array_flip($routeToTripMap);

        foreach ($stopTimes as $st) {
            $tripId = $st['trip_id'];
            // Solo guardamos si pertenece a uno de nuestros trips representativos
            if (isset($selectedTrips[$tripId])) {
                $tripStopsMap[$tripId][] = [
                    'stop_id' => $st['stop_id'],
                    'sequence' => (int)$st['stop_sequence']
                ];
            }
        }

        // Ordenar paradas por secuencia para cada trip
        foreach ($tripStopsMap as $tripId => &$stops) {
            usort($stops, fn($a, $b) => $a['sequence'] <=> $b['sequence']);
        }
        unset($stops); // Romper referencia

        // 4. Cargar Paradas (Stops) para obtener coordenadas y nombres
        // Leemos stops.csv una sola vez
        $allStops = $this->readCsv($this->gtfsPath . '/stops.csv');
        $stopsMap = []; // stop_id => data
        foreach ($allStops as $stop) {
            $stopsMap[$stop['stop_id']] = $stop;
        }

        // 5. Construir el resultado final
        $result = [];
        foreach ($municipalRoutes as $route) {
            $routeId = $route['route_id'];
            $tripId = $routeToTripMap[$routeId] ?? null;

            if (!$tripId || !isset($tripStopsMap[$tripId])) {
                continue; // No hay datos de viaje o paradas para esta ruta
            }

            // Construir lista de paradas con detalles
            $routeStopsData = [];
            foreach ($tripStopsMap[$tripId] as $st) {
                $stopId = $st['stop_id'];
                if (isset($stopsMap[$stopId])) {
                    $stop = $stopsMap[$stopId];
                    // Usamos el mismo slug que en seedGtfsStops para coincidir
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

            // Determinar si es nocturna o municipal
            $isNight = str_starts_with(strtoupper($route['route_short_name']), 'L');
            $company = $isNight ? 'night' : 'municipales';
            $type = $isNight ? 'night' : 'urban';

            // OUTBOUND y INBOUND iguales por ahora (simplificación de usar un solo trip)
            $result[] = [
                'line' => $route['route_short_name'],
                'type' => $type,
                'company' => $company,
                'origin' => $origin,
                'destination' => $destination,
                'color' => $color,
                'stopsOutbound' => $routeStopsData,
                'stopsInbound' => $routeStopsData,
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