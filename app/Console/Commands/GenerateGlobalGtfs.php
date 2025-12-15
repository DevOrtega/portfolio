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
                'stops_outbound' => ['estacionSanTelmo', 'hospitalInsular', 'cruceMelenara', 'aeropuerto', 'cruce_de_arinaga', 'vecindario', 'juanGrande', 'sanAgustin', 'elVeril', 'playaIngles', 'maspalomas', 'arguineguin', 'puertoRico', 'puertoMogan'],
                'stops_inbound' => ['puertoMogan', 'puertoRico', 'arguineguin', 'maspalomas', 'playaIngles', 'elVeril', 'sanAgustin', 'juanGrande', 'vecindario', 'cruce_de_arinaga', 'aeropuerto', 'cruceMelenara', 'hospitalInsular', 'estacionSanTelmo']
            ],
            [
                'id' => 'global-5',
                'line' => '5',
                'name' => 'Las Palmas - Faro de Maspalomas',
                'origin' => 'Las Palmas',
                'destination' => 'Faro de Maspalomas',
                'color' => '#0066CC',
                'stops_outbound' => ['estacionSanTelmo', 'hospitalInsular', 'cruceMelenara', 'aeropuerto', 'cruce_de_arinaga', 'vecindario', 'juanGrande', 'sanAgustin', 'elVeril', 'playaIngles', 'faroMaspalomas'],
                'stops_inbound' => ['faroMaspalomas', 'playaIngles', 'elVeril', 'sanAgustin', 'juanGrande', 'vecindario', 'cruce_de_arinaga', 'aeropuerto', 'cruceMelenara', 'hospitalInsular', 'estacionSanTelmo']
            ],
            [
                'id' => 'global-30',
                'line' => '30',
                'name' => 'Santa Catalina - Faro de Maspalomas',
                'origin' => 'Santa Catalina',
                'destination' => 'Faro de Maspalomas',
                'color' => '#0066CC',
                'stops_outbound' => ['santaCatalina', 'autopistaSur', 'faroMaspalomas'],
                'stops_inbound' => ['faroMaspalomas', 'autopistaSur', 'santaCatalina']
            ],
            [
                'id' => 'global-60',
                'line' => '60',
                'name' => 'Las Palmas - Aeropuerto',
                'origin' => 'Las Palmas',
                'destination' => 'Aeropuerto',
                'color' => '#0066CC',
                'stops_outbound' => ['santaCatalina', 'hospitalInsular', 'autopistaSur', 'aeropuerto'],
                'stops_inbound' => ['aeropuerto', 'autopistaSur', 'hospitalInsular', 'santaCatalina']
            ],
            [
                'id' => 'global-91',
                'line' => '91',
                'name' => 'Las Palmas - Puerto de Mogán (Directo)',
                'origin' => 'Las Palmas',
                'destination' => 'Puerto de Mogán',
                'color' => '#0066CC',
                'stops_outbound' => ['santaCatalina', 'autopistaSur', 'maspalomas', 'arguineguin', 'puertoRico', 'puertoMogan'],
                'stops_inbound' => ['puertoMogan', 'puertoRico', 'arguineguin', 'maspalomas', 'autopistaSur', 'santaCatalina']
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
        // Based on BusDataSeeder::getGlobalStopsData
        // Flattened the structure for GTFS (lat/lng outbound as default)
        return [
            'puerto' => ['lat' => 28.1480, 'lng' => -15.4285],
            'santaCatalina' => ['lat' => 28.1400, 'lng' => -15.4295],
            'mesaYLopez' => ['lat' => 28.1330, 'lng' => -15.4350],
            'leonCastillo35' => ['lat' => 28.1150, 'lng' => -15.4250],
            'triana' => ['lat' => 28.1060, 'lng' => -15.4160],
            'sanJose' => ['lat' => 28.0950, 'lng' => -15.4160],
            'ciudadJusticia' => ['lat' => 28.0850, 'lng' => -15.4180],
            'hoyaDeLaPlata' => ['lat' => 28.0750, 'lng' => -15.4200],
            'teatro' => ['lat' => 28.1040, 'lng' => -15.4140],
            'sanTelmo' => ['lat' => 28.1080, 'lng' => -15.4170],
            'estacionSanTelmo' => ['lat' => 28.1085, 'lng' => -15.4175],
            'hospitalInsular' => ['lat' => 28.0905, 'lng' => -15.4185],
            'rehoyas' => ['lat' => 28.1150, 'lng' => -15.4300],
            'cruzDePiedra' => ['lat' => 28.1200, 'lng' => -15.4350],
            'schamann' => ['lat' => 28.1250, 'lng' => -15.4400],
            'escaleritas' => ['lat' => 28.1200, 'lng' => -15.4450],
            'laMinilla' => ['lat' => 28.1280, 'lng' => -15.4500],
            'hospitalNegrin' => ['lat' => 28.1300, 'lng' => -15.4550],
            'guanarteme' => ['lat' => 28.1350, 'lng' => -15.4450],
            'donZoilo' => ['lat' => 28.1150, 'lng' => -15.4350],
            'laFeria' => ['lat' => 28.1100, 'lng' => -15.4450],
            'sietePalmas' => ['lat' => 28.1050, 'lng' => -15.4550],
            'tamaraceite' => ['lat' => 28.1000, 'lng' => -15.4700],
            'telde' => ['lat' => 27.9941, 'lng' => -15.4166],
            'teldeIntercambiador' => ['lat' => 27.9945, 'lng' => -15.4170],
            'cruceMelenara' => ['lat' => 27.9880, 'lng' => -15.3750],
            'aeropuerto' => ['lat' => 27.9319, 'lng' => -15.3866],
            'vecindario' => ['lat' => 27.8414, 'lng' => -15.4489],
            'juanGrande' => ['lat' => 27.8000, 'lng' => -15.4800],
            'sanAgustin' => ['lat' => 27.7750, 'lng' => -15.5480],
            'elVeril' => ['lat' => 27.7650, 'lng' => -15.5600],
            'playaIngles' => ['lat' => 27.7580, 'lng' => -15.5720],
            'maspalomas' => ['lat' => 27.7609, 'lng' => -15.5865],
            'aguimes' => ['lat' => 27.9050, 'lng' => -15.4445],
            'ingenio' => ['lat' => 27.9170, 'lng' => -15.4360],
            'cruce_de_arinaga' => ['lat' => 27.8680, 'lng' => -15.4100],
            'arinaga' => ['lat' => 27.8590, 'lng' => -15.3950],
            'carrizal' => ['lat' => 27.9030, 'lng' => -15.4290],
            'melenara' => ['lat' => 27.9885, 'lng' => -15.3760],
            'salinetas' => ['lat' => 27.9745, 'lng' => -15.3885],
            'faroMaspalomas' => ['lat' => 27.7350, 'lng' => -15.5915],
            'autopistaSur' => ['lat' => 27.9200, 'lng' => -15.4280],
            'puertoMogan' => ['lat' => 27.8155, 'lng' => -15.7635],
            'arguineguin' => ['lat' => 27.7610, 'lng' => -15.6815],
            'puertoRico' => ['lat' => 27.7870, 'lng' => -15.7085],
            'taurito' => ['lat' => 27.8020, 'lng' => -15.7380],
            'tableroMaspalomas' => ['lat' => 27.7690, 'lng' => -15.5740],
            'sanFernando' => ['lat' => 27.7650, 'lng' => -15.5645],
            'bahiaFeliz' => ['lat' => 27.7940, 'lng' => -15.5120],
            'playaCura' => ['lat' => 27.7915, 'lng' => -15.7150],
            'palmitosPark' => ['lat' => 27.8060, 'lng' => -15.5780],
            'aldeaSanNicolas' => ['lat' => 27.9845, 'lng' => -15.7795],
            'doctoral' => ['lat' => 27.8310, 'lng' => -15.4650],
            'playaArinaga' => ['lat' => 27.8520, 'lng' => -15.3865],
            'castilloRomeral' => ['lat' => 27.8175, 'lng' => -15.4760],
            'sardina_sur' => ['lat' => 27.8715, 'lng' => -15.4375],
            'galdar' => ['lat' => 28.1465, 'lng' => -15.6495],
            'guia' => ['lat' => 28.1395, 'lng' => -15.6335],
            'agaete' => ['lat' => 28.1015, 'lng' => -15.6985],
            'puertoNieves' => ['lat' => 28.0985, 'lng' => -15.7025],
            'moya' => ['lat' => 28.1095, 'lng' => -15.5870],
            'firgas' => ['lat' => 28.1090, 'lng' => -15.5615],
            'banaderos' => ['lat' => 28.1350, 'lng' => -15.5200],
            'cardones' => ['lat' => 28.1280, 'lng' => -15.5180],
            'santaBrigida' => ['lat' => 28.0340, 'lng' => -15.5005],
            'sanMateo' => ['lat' => 28.0135, 'lng' => -15.5320],
            'tejeda' => ['lat' => 27.9940, 'lng' => -15.6140],
            'artenara' => ['lat' => 28.0205, 'lng' => -15.6445],
            'valsequillo' => ['lat' => 27.9870, 'lng' => -15.4955]
        ];
    }
}
