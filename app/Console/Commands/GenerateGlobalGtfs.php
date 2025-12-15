<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateGlobalGtfs extends Command
{
    protected $signature = 'bus:generate-global-gtfs';
    protected $description = 'Generate fake GTFS files for Guaguas Global based on hardcoded data';

    public function handle(): void
    {
        $this->info('Generating Global GTFS files...');

        $basePath = base_path('gtfs/global');
        if (!File::exists($basePath)) {
            File::makeDirectory($basePath, 0755, true);
        }

        // 1. Agency
        $this->generateAgency($basePath);

        // 2. Stops
        $stops = $this->getGlobalStops();
        $this->generateStops($basePath, $stops);

        // 3. Routes
        $routes = $this->getGlobalRoutes();
        $this->generateRoutes($basePath, $routes);

        // 4. Calendar (Service availability)
        $this->generateCalendar($basePath);

        // 5. Trips & Stop Times
        $this->generateTripsAndStopTimes($basePath, $routes, $stops);

        $this->info('Global GTFS generation completed!');
    }

    private function generateAgency(string $basePath): void
    {
        $headers = ['agency_id', 'agency_name', 'agency_url', 'agency_timezone', 'agency_lang', 'agency_phone'];
        $data = [
            ['global', 'Global', 'https://guaguasglobal.com', 'Atlantic/Canary', 'es', '928252630'],
        ];
        $this->writeCsv("$basePath/agency.csv", $headers, $data);
    }

    private function generateStops(string $basePath, array $stops): void
    {
        $headers = ['stop_id', 'stop_name', 'stop_lat', 'stop_lon'];
        $data = [];
        foreach ($stops as $code => $stop) {
            $data[] = [
                $code,
                ucfirst(str_replace('_', ' ', $code)), // Simple name generation
                $stop['lat'],
                $stop['lng']
            ];
        }
        $this->writeCsv("$basePath/stops.csv", $headers, $data);
    }

    private function generateRoutes(string $basePath, array $routes): void
    {
        $headers = ['route_id', 'agency_id', 'route_short_name', 'route_long_name', 'route_type', 'route_color', 'route_text_color'];
        $data = [];
        foreach ($routes as $route) {
            $data[] = [
                $route['id'],
                'global',
                $route['line'],
                $route['name'],
                3, // Bus
                str_replace('#', '', $route['color']),
                'FFFFFF'
            ];
        }
        $this->writeCsv("$basePath/routes.csv", $headers, $data);
    }

    private function generateCalendar(string $basePath): void
    {
        $headers = ['service_id', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'start_date', 'end_date'];
        $data = [
            ['DAILY', 1, 1, 1, 1, 1, 1, 1, '20240101', '20251231']
        ];
        $this->writeCsv("$basePath/calendar.csv", $headers, $data);
    }

    private function generateTripsAndStopTimes(string $basePath, array $routes, array $allStops): void
    {
        $tripsHeaders = ['route_id', 'service_id', 'trip_id', 'trip_headsign', 'direction_id', 'shape_id'];
        $stopTimesHeaders = ['trip_id', 'arrival_time', 'departure_time', 'stop_id', 'stop_sequence'];
        
        $tripsData = [];
        $stopTimesData = [];

        foreach ($routes as $route) {
            // Outbound
            $tripIdOut = "T-{$route['line']}-Out";
            $tripsData[] = [
                $route['id'],
                'DAILY',
                $tripIdOut,
                $route['destination'],
                0,
                '' // shape_id (optional)
            ];
            
            foreach ($route['stops_outbound'] as $index => $stopCode) {
                // Fake times (just incrementing)
                $time = gmdate('H:i:s', 3600 * 8 + $index * 600); // Start 08:00, +10 mins each
                $stopTimesData[] = [
                    $tripIdOut, $time, $time, $stopCode, $index + 1
                ];
            }

            // Inbound
            $tripIdIn = "T-{$route['line']}-In";
            $tripsData[] = [
                $route['id'],
                'DAILY',
                $tripIdIn,
                $route['origin'],
                1,
                ''
            ];

            foreach ($route['stops_inbound'] as $index => $stopCode) {
                $time = gmdate('H:i:s', 3600 * 9 + $index * 600);
                $stopTimesData[] = [
                    $tripIdIn, $time, $time, $stopCode, $index + 1
                ];
            }
        }

        $this->writeCsv("$basePath/trips.csv", $tripsHeaders, $tripsData);
        $this->writeCsv("$basePath/stop_times.csv", $stopTimesHeaders, $stopTimesData);
    }

    private function writeCsv(string $path, array $headers, array $data): void
    {
        $fp = fopen($path, 'w');
        fputcsv($fp, $headers);
        foreach ($data as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
        $this->info("Generated: " . basename($path));
    }

    // ================= DATA DEFINITIONS =================

    private function getGlobalRoutes(): array
    {
        return [
            [
                'id' => 'global-1',
                'line' => '1',
                'name' => 'Las Palmas - Puerto de Mogán',
                'origin' => 'Las Palmas',
                'destination' => 'Puerto de Mogán',
                'color' => '#0066CC',
                'stops_outbound' => [
                    'estacionSanTelmo', 'hospitalInsular', 'waypoint_jinamar', 'waypoint_taliarte', 'cruceMelenara', 
                    'aeropuerto', 'carrizal', 'cruceVargas', 'avenidaCanarias', 'doctoral', 'juanGrande', 
                    'bahiaFeliz', 'sanAgustin', 'playaIngles', 'faroMaspalomas', 'arguineguin', 
                    'patalavaca', 'anfi', 'puertoRico', 'amadores', 'tauro', 'playaCura', 'taurito', 'puertoMogan'
                ],
                'stops_inbound' => [
                    'puertoMogan', 'taurito', 'playaCura', 'tauro', 'amadores', 'puertoRico', 'anfi', 'patalavaca', 
                    'arguineguin', 'faroMaspalomas', 'playaIngles', 'sanAgustin', 'bahiaFeliz', 'juanGrande', 
                    'doctoral', 'avenidaCanarias', 'cruceVargas', 'carrizal', 'aeropuerto', 
                    'cruceMelenara', 'waypoint_taliarte', 'waypoint_jinamar', 'hospitalInsular', 'estacionSanTelmo'
                ]
            ],
            [
                'id' => 'global-5',
                'line' => '5',
                'name' => 'Las Palmas - Faro de Maspalomas',
                'origin' => 'Las Palmas',
                'destination' => 'Faro de Maspalomas',
                'color' => '#0066CC',
                'stops_outbound' => [
                    'estacionSanTelmo', 'hospitalInsular', 'waypoint_jinamar', 'waypoint_taliarte', 'cruceMelenara', 
                    'aeropuerto', 'carrizal', 'cruceVargas', 'avenidaCanarias', 'doctoral', 'juanGrande', 
                    'bahiaFeliz', 'sanAgustin', 'playaIngles', 'faroMaspalomas'
                ],
                'stops_inbound' => [
                    'faroMaspalomas', 'playaIngles', 'sanAgustin', 'bahiaFeliz', 'juanGrande', 
                    'doctoral', 'avenidaCanarias', 'cruceVargas', 'carrizal', 'aeropuerto', 
                    'cruceMelenara', 'waypoint_taliarte', 'waypoint_jinamar', 'hospitalInsular', 'estacionSanTelmo'
                ]
            ],
            [
                'id' => 'global-30',
                'line' => '30',
                'name' => 'Santa Catalina - Faro de Maspalomas (Directo)',
                'origin' => 'Santa Catalina',
                'destination' => 'Faro de Maspalomas',
                'color' => '#0066CC',
                'stops_outbound' => ['santaCatalina', 'estacionSanTelmo', 'hospitalInsular', 'waypoint_jinamar', 'waypoint_taliarte', 'waypoint_gando_norte', 'aeropuerto', 'waypoint_arinaga_hwy', 'bahiaFeliz', 'sanAgustin', 'playaIngles', 'faroMaspalomas'],
                'stops_inbound' => ['faroMaspalomas', 'playaIngles', 'sanAgustin', 'bahiaFeliz', 'waypoint_arinaga_hwy', 'aeropuerto', 'waypoint_gando_norte', 'waypoint_taliarte', 'waypoint_jinamar', 'hospitalInsular', 'estacionSanTelmo', 'santaCatalina']
            ],
            [
                'id' => 'global-60',
                'line' => '60',
                'name' => 'Las Palmas - Aeropuerto (Directo)',
                'origin' => 'Las Palmas',
                'destination' => 'Aeropuerto',
                'color' => '#0066CC',
                'stops_outbound' => ['estacionSanTelmo', 'hospitalInsular', 'waypoint_jinamar', 'waypoint_taliarte', 'aeropuerto'],
                'stops_inbound' => ['aeropuerto', 'waypoint_taliarte', 'waypoint_jinamar', 'hospitalInsular', 'estacionSanTelmo']
            ],
            [
                'id' => 'global-91',
                'line' => '91',
                'name' => 'Las Palmas - Puerto de Mogán (Directo)',
                'origin' => 'Las Palmas',
                'destination' => 'Puerto de Mogán',
                'color' => '#0066CC',
                'stops_outbound' => ['estacionSanTelmo', 'hospitalInsular', 'waypoint_jinamar', 'waypoint_taliarte', 'aeropuerto', 'waypoint_arinaga_hwy', 'arguineguin', 'puertoRico', 'puertoMogan'],
                'stops_inbound' => ['puertoMogan', 'puertoRico', 'arguineguin', 'waypoint_arinaga_hwy', 'aeropuerto', 'waypoint_taliarte', 'waypoint_jinamar', 'hospitalInsular', 'estacionSanTelmo']
            ],
            [
                'id' => 'global-105',
                'line' => '105',
                'name' => 'Las Palmas - Gáldar',
                'origin' => 'Las Palmas',
                'destination' => 'Gáldar',
                'color' => '#0066CC',
                'stops_outbound' => ['estacionSanTelmo', 'tamaraceite', 'banaderos', 'arucas', 'guia', 'galdar'],
                'stops_inbound' => ['galdar', 'guia', 'arucas', 'banaderos', 'tamaraceite', 'estacionSanTelmo']
            ],
        ];
    }

    private function getGlobalStops(): array
    {
        return [
            'estacionSanTelmo' => ['lat' => 28.1085, 'lng' => -15.4175],
            'santaCatalina' => ['lat' => 28.1400, 'lng' => -15.4295],
            'hospitalInsular' => ['lat' => 28.0905, 'lng' => -15.4185],
            'waypoint_jinamar' => ['lat' => 28.0450, 'lng' => -15.4150],
            'waypoint_taliarte' => ['lat' => 28.0000, 'lng' => -15.3900],
            'cruceMelenara' => ['lat' => 27.9880, 'lng' => -15.3750],
            'waypoint_gando_norte' => ['lat' => 27.9600, 'lng' => -15.3800],
            'aeropuerto' => ['lat' => 27.9355, 'lng' => -15.3905],
            
            // Town stops for L1
            'carrizal' => ['lat' => 27.9030, 'lng' => -15.4290],
            'cruceVargas' => ['lat' => 27.8880, 'lng' => -15.4350],
            'avenidaCanarias' => ['lat' => 27.8480, 'lng' => -15.4430], // Vecindario
            'doctoral' => ['lat' => 27.8310, 'lng' => -15.4650],
            
            // Highway stops for Direct Lines
            'waypoint_arinaga_hwy' => ['lat' => 27.8715, 'lng' => -15.4440],
            'cruceArinaga' => ['lat' => 27.8715, 'lng' => -15.4440], // Same as waypoint for now

            'juanGrande' => ['lat' => 27.8000, 'lng' => -15.4800],
            'bahiaFeliz' => ['lat' => 27.7800, 'lng' => -15.5350],
            'sanAgustin' => ['lat' => 27.7750, 'lng' => -15.5480],
            'elVeril' => ['lat' => 27.7650, 'lng' => -15.5600],
            'playaIngles' => ['lat' => 27.7580, 'lng' => -15.5720],
            'faroMaspalomas' => ['lat' => 27.7350, 'lng' => -15.5915],
            
            // South Coast stops (GC-500)
            'arguineguin' => ['lat' => 27.7610, 'lng' => -15.6815],
            'patalavaca' => ['lat' => 27.7700, 'lng' => -15.6900],
            'anfi' => ['lat' => 27.7730, 'lng' => -15.6950],
            'puertoRico' => ['lat' => 27.7870, 'lng' => -15.7085],
            'amadores' => ['lat' => 27.7900, 'lng' => -15.7250],
            'tauro' => ['lat' => 27.7980, 'lng' => -15.7350],
            'playaCura' => ['lat' => 27.8000, 'lng' => -15.7450],
            'taurito' => ['lat' => 27.8050, 'lng' => -15.7550],
            'puertoMogan' => ['lat' => 27.8155, 'lng' => -15.7635],

            // Other Lines
            'vecindario' => ['lat' => 27.8414, 'lng' => -15.4489],
            'autopistaSur' => ['lat' => 27.9200, 'lng' => -15.4280],
            'maspalomas' => ['lat' => 27.7609, 'lng' => -15.5865],
            'tamaraceite' => ['lat' => 28.1000, 'lng' => -15.4700],
            'banaderos' => ['lat' => 28.1350, 'lng' => -15.5200],
            'arucas' => ['lat' => 28.1180, 'lng' => -15.5220],
            'guia' => ['lat' => 28.1395, 'lng' => -15.6335],
            'galdar' => ['lat' => 28.1465, 'lng' => -15.6495],
            'cardones' => ['lat' => 28.1280, 'lng' => -15.5180],
            'santaBrigida' => ['lat' => 28.0340, 'lng' => -15.5005],
            'sanMateo' => ['lat' => 28.0135, 'lng' => -15.5320],
            'tejeda' => ['lat' => 27.9940, 'lng' => -15.6140],
            'artenara' => ['lat' => 28.0205, 'lng' => -15.6445],
            'valsequillo' => ['lat' => 27.9870, 'lng' => -15.4955]
        ];
    }
}