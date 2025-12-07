<?php

namespace App\Infrastructure\Services;


class GtfsImporter
{
    /**
     * Devuelve todas las líneas municipales del GTFS con estructura para el seeder
     * @return array
     */
    public function getAllMunicipalRoutes(): array
    {
        $routes = $this->readCsv($this->gtfsPath . '/routes.csv');
        $result = [];
        foreach ($routes as $route) {
            // Solo líneas de Guaguas Municipales (agency_id = Guaguas)
            if (isset($route['agency_id']) && strtolower($route['agency_id']) !== 'guaguas') continue;
            $routeId = $route['route_id'];
            $color = isset($route['route_color']) ? ('#' . ltrim($route['route_color'], '#')) : '#FDB913';
            $origin = '';
            $destination = '';
            if (isset($route['route_long_name'])) {
                $parts = preg_split('/\s*-\s*/', $route['route_long_name']);
                $origin = $parts[0] ?? '';
                $destination = $parts[1] ?? '';
            }
            // OUTBOUND
            $stopsOutbound = array_map(
                fn($stop) => strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $stop['stop_name'])),
                $this->getStopsForLine($routeId)
            );
            // INBOUND (si hay trip_id diferente para inbound, aquí se asume igual que outbound)
            $stopsInbound = $stopsOutbound;
            $result[] = [
                'line' => $route['route_short_name'],
                'type' => 'urban',
                'company' => 'municipales',
                'origin' => $origin,
                'destination' => $destination,
                'color' => $color,
                'stopsOutbound' => $stopsOutbound,
                'stopsInbound' => $stopsInbound,
            ];
        }
        return $result;
    }
    private string $gtfsPath;

    public function __construct(string $gtfsPath)
    {
        $this->gtfsPath = $gtfsPath ?? base_path('gtfs');
    }

    /**
     * Obtiene la secuencia de paradas y coordenadas para una línea y dirección (por defecto la primera trip_id encontrada)
     * @param string $routeShortName
     * @return array [ ['stop_id' => ..., 'stop_name' => ..., 'lat' => ..., 'lon' => ...], ... ]
     */
    public function getStopsForLine(string $routeShortName): array
    {
        $trips = $this->readCsv($this->gtfsPath . '/trips.csv');
        $routeTrips = array_filter($trips, fn($t) => $t['route_id'] === $routeShortName);
        if (empty($routeTrips)) return [];
        $trip = array_values($routeTrips)[0]; // Usar la primera trip encontrada
        $tripId = $trip['trip_id'];

        $stopTimes = $this->readCsv($this->gtfsPath . '/stop_times.csv');
        $tripStops = array_filter($stopTimes, fn($s) => $s['trip_id'] === $tripId);
        usort($tripStops, fn($a, $b) => (int)$a['stop_sequence'] <=> (int)$b['stop_sequence']);
        $stopIds = array_column($tripStops, 'stop_id');

        $stops = $this->readCsv($this->gtfsPath . '/stops.csv');
        $stopsById = [];
        foreach ($stops as $stop) {
            $stopsById[$stop['stop_id']] = $stop;
        }

        $result = [];
        foreach ($stopIds as $stopId) {
            if (isset($stopsById[$stopId])) {
                $stop = $stopsById[$stopId];
                $result[] = [
                    'stop_id' => $stopId,
                    'stop_name' => $stop['stop_name'],
                    'lat' => (float)$stop['stop_lat'],
                    'lon' => (float)$stop['stop_lon'],
                ];
            }
        }
        return $result;
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
        while (($data = fgetcsv($handle)) !== false) {
            $rows[] = array_combine($headers, $data);
        }
        fclose($handle);
        return $rows;
    }
}
