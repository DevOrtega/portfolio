<?php

namespace Database\Seeders;

use App\Infrastructure\Persistence\Eloquent\Models\BusCompanyModel;
use App\Infrastructure\Persistence\Eloquent\Models\BusLineModel;
use App\Infrastructure\Persistence\Eloquent\Models\BusStopModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Infrastructure\Services\GtfsImporter;

class BusDataSeeder extends Seeder
{
    private function seedRouteStops(): void
    {
        $lines = BusLineModel::all()->keyBy('line_number');
        $stops = BusStopModel::all()->keyBy('code');

        $relations = [
            // GLOBAL
            ['line' => '1', 'direction' => 'outbound', 'stops' => ['estacionSanTelmo', 'telde', 'aeropuerto', 'vecindario', 'sanAgustin', 'playaIngles', 'maspalomas', 'arguineguin', 'puertoRico', 'puertoMogan']],
            ['line' => '1', 'direction' => 'inbound', 'stops' => ['puertoMogan', 'puertoRico', 'arguineguin', 'maspalomas', 'playaIngles', 'sanAgustin', 'vecindario', 'aeropuerto', 'telde', 'estacionSanTelmo']],
            ['line' => '5', 'direction' => 'outbound', 'stops' => ['estacionSanTelmo', 'telde', 'aeropuerto', 'vecindario', 'sanAgustin', 'playaIngles', 'faroMaspalomas']],
            ['line' => '5', 'direction' => 'inbound', 'stops' => ['faroMaspalomas', 'playaIngles', 'sanAgustin', 'vecindario', 'aeropuerto', 'telde', 'estacionSanTelmo']],
            ['line' => '30', 'direction' => 'outbound', 'stops' => ['santaCatalina', 'autopistaSur', 'faroMaspalomas']],
            ['line' => '30', 'direction' => 'inbound', 'stops' => ['faroMaspalomas', 'autopistaSur', 'santaCatalina']],
            ['line' => '60', 'direction' => 'outbound', 'stops' => ['santaCatalina', 'autopistaSur', 'aeropuerto']],
            ['line' => '60', 'direction' => 'inbound', 'stops' => ['aeropuerto', 'autopistaSur', 'santaCatalina']],
            ['line' => '91', 'direction' => 'outbound', 'stops' => ['santaCatalina', 'autopistaSur', 'maspalomas', 'arguineguin', 'puertoRico', 'puertoMogan']],
            ['line' => '91', 'direction' => 'inbound', 'stops' => ['puertoMogan', 'puertoRico', 'arguineguin', 'maspalomas', 'autopistaSur', 'santaCatalina']],
            ['line' => '105', 'direction' => 'outbound', 'stops' => ['estacionSanTelmo', 'tamaraceite', 'arucas', 'guia', 'galdar']],
            ['line' => '105', 'direction' => 'inbound', 'stops' => ['galdar', 'guia', 'arucas', 'tamaraceite', 'estacionSanTelmo']],
            // NIGHT
            ['line' => 'L1', 'direction' => 'outbound', 'stops' => ['puerto', 'santaCatalina', 'mesaYLopez', 'leonCastillo35', 'triana', 'sanJose', 'ciudadJusticia', 'hoyaDeLaPlata']],
            ['line' => 'L1', 'direction' => 'inbound', 'stops' => ['hoyaDeLaPlata', 'ciudadJusticia', 'sanJose', 'triana', 'leonCastillo35', 'mesaYLopez', 'santaCatalina', 'puerto']],
            ['line' => 'L2', 'direction' => 'outbound', 'stops' => ['teatro', 'sanTelmo', 'rehoyas', 'cruzDePiedra', 'schamann', 'escaleritas', 'laMinilla', 'hospitalNegrin', 'guanarteme', 'mesaYLopez', 'santaCatalina']],
            ['line' => 'L2', 'direction' => 'inbound', 'stops' => ['santaCatalina', 'mesaYLopez', 'guanarteme', 'hospitalNegrin', 'laMinilla', 'escaleritas', 'schamann', 'cruzDePiedra', 'rehoyas', 'sanTelmo', 'teatro']],
            ['line' => 'L3', 'direction' => 'outbound', 'stops' => ['teatro', 'sanTelmo', 'donZoilo', 'escaleritas', 'laFeria', 'sietePalmas', 'tamaraceite']],
            ['line' => 'L3', 'direction' => 'inbound', 'stops' => ['tamaraceite', 'sietePalmas', 'laFeria', 'escaleritas', 'donZoilo', 'sanTelmo', 'teatro']],
        ];

        $bulkRouteStops = [];
        foreach ($relations as $rel) {
            $lineId = $lines[$rel['line']]->id ?? null;
            if (!$lineId) continue;
            foreach ($rel['stops'] as $order => $stopCode) {
                $stopId = $stops[$stopCode]->id ?? null;
                if (!$stopId) continue;
                $bulkRouteStops[] = [
                    'line_id' => $lineId,
                    'stop_id' => $stopId,
                    'direction' => $rel['direction'],
                    'order' => $order,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        foreach (array_chunk($bulkRouteStops, 1000) as $chunk) {
            DB::table('bus_route_stops')->insert($chunk);
        }
        $this->command->info('✓ Route stops seeded');
    }

    private function seedStops(): void
    {
        // Solo datos legacy (Global, Night, etc.), no GTFS
        $stopsGlobal = $this->getGlobalStopsData();
        $bulk = [];
        foreach ($stopsGlobal as $code => $data) {
            $bulk[] = [
                'code' => $code,
                'name' => ucfirst(str_replace('_', ' ', $code)),
                'lat_outbound' => $data['outbound'][0],
                'lng_outbound' => $data['outbound'][1],
                'lat_inbound' => $data['inbound'][0],
                'lng_inbound' => $data['inbound'][1],
                'zone' => $data['zone'] ?? null,
            ];
        }
        DB::transaction(function () use ($bulk) {
            BusStopModel::upsert($bulk, ['code'], ['name', 'lat_outbound', 'lng_outbound', 'lat_inbound', 'lng_inbound', 'zone']);
        });
        $this->command->info('✓ Stops seeded');
    }
    private function seedLines(): void
    {
        // Inserción legacy con SQL puro para máxima velocidad
        DB::statement('DELETE FROM bus_lines');
        DB::statement('DELETE FROM bus_route_stops');

        // Obtener IDs de compañías
        $companies = DB::table('bus_companies')->pluck('id', 'code');

        // Insertar líneas legacy
        $lines = [
            // GLOBAL
            ['line_number' => '1', 'type' => 'interurban', 'origin' => 'Las Palmas', 'destination' => 'Puerto de Mogán', 'color' => '#0066CC', 'company_code' => 'global'],
            ['line_number' => '5', 'type' => 'interurban', 'origin' => 'Las Palmas', 'destination' => 'Faro de Maspalomas', 'color' => '#0066CC', 'company_code' => 'global'],
            ['line_number' => '30', 'type' => 'interurban', 'origin' => 'Santa Catalina', 'destination' => 'Faro de Maspalomas', 'color' => '#0066CC', 'company_code' => 'global'],
            ['line_number' => '60', 'type' => 'interurban', 'origin' => 'Las Palmas', 'destination' => 'Aeropuerto', 'color' => '#0066CC', 'company_code' => 'global'],
            ['line_number' => '91', 'type' => 'interurban', 'origin' => 'Santa Catalina', 'destination' => 'Puerto de Mogán', 'color' => '#0066CC', 'company_code' => 'global'],
            ['line_number' => '105', 'type' => 'interurban', 'origin' => 'Las Palmas', 'destination' => 'Gáldar', 'color' => '#0066CC', 'company_code' => 'global'],
            // NIGHT
            ['line_number' => 'L1', 'type' => 'night', 'origin' => 'Puerto', 'destination' => 'Hoya de La Plata', 'color' => '#9933FF', 'company_code' => 'night'],
            ['line_number' => 'L2', 'type' => 'night', 'origin' => 'Teatro', 'destination' => 'Santa Catalina', 'color' => '#9933FF', 'company_code' => 'night'],
            ['line_number' => 'L3', 'type' => 'night', 'origin' => 'Teatro', 'destination' => 'Tamaraceite', 'color' => '#9933FF', 'company_code' => 'night'],
        ];

        $bulk = [];
        foreach ($lines as $line) {
            $bulk[] = [
                'line_number' => $line['line_number'],
                'type' => $line['type'],
                'origin' => $line['origin'],
                'destination' => $line['destination'],
                'color' => $line['color'],
                'company_id' => $companies[$line['company_code']] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('bus_lines')->insert($bulk);

        $this->command->info('✓ Lines seeded');
    }

    public function run(): void
    {
        // Disable foreign key checks for truncation
        DB::statement('PRAGMA foreign_keys = OFF');
        DB::table('bus_lines')->truncate();
        DB::table('bus_stops')->truncate();
        DB::table('bus_companies')->truncate();
        DB::statement('PRAGMA foreign_keys = ON');

        $this->seedCompanies();
        $this->seedStops();
        $this->seedLines();
        // Solo insertar datos legacy si la tabla está vacía (no sobrescribir GTFS)
        if (DB::table('bus_route_stops')->count() === 0) {
            $this->seedRouteStops();
        } else {
            $this->command->info('✓ Route stops legacy NO insertados (ya existen datos GTFS)');
        }
    }

    private function seedCompanies(): void
    {
        $companies = [
            [
                'code' => 'municipales',
                'name' => 'Guaguas Municipales',
                'primary_color' => '#FDB913',
                'secondary_color' => '#D49400',
                'text_color' => '#333333',
            ],
            [
                'code' => 'global',
                'name' => 'Global',
                'primary_color' => '#0066CC',
                'secondary_color' => '#004C99',
                'text_color' => '#FFFFFF',
            ],
            [
                'code' => 'night',
                'name' => 'Líneas Nocturnas',
                'primary_color' => '#9933FF',
                'secondary_color' => '#7722CC',
                'text_color' => '#FFFFFF',
            ],
        ];

        foreach ($companies as $company) {
            BusCompanyModel::create($company);
        }

        $this->command->info('✓ Companies seeded');
    }


    // Paradas legacy solo para líneas Global (SUR, NORTE, CENTRO, TELDE, OESTE)
    private function getGlobalStopsData(): array
    {
        return [
            // GLOBAL - INTERURBANAS
            'telde' => ['outbound' => [27.9941, -15.4166], 'inbound' => [27.9941, -15.4166], 'zone' => 'TELDE'],
            'teldeIntercambiador' => ['outbound' => [27.9945, -15.4170], 'inbound' => [27.9945, -15.4170], 'zone' => 'TELDE'],
            'aeropuerto' => ['outbound' => [27.9319, -15.3866], 'inbound' => [27.9319, -15.3866], 'zone' => 'SUR'],
            'vecindario' => ['outbound' => [27.8414, -15.4489], 'inbound' => [27.8414, -15.4489], 'zone' => 'SUR'],
            'sanAgustin' => ['outbound' => [27.7750, -15.5480], 'inbound' => [27.7750, -15.5480], 'zone' => 'SUR'],
            'playaIngles' => ['outbound' => [27.7580, -15.5720], 'inbound' => [27.7580, -15.5720], 'zone' => 'SUR'],
            'maspalomas' => ['outbound' => [27.7609, -15.5865], 'inbound' => [27.7609, -15.5865], 'zone' => 'SUR'],
            'aguimes' => ['outbound' => [27.9050, -15.4445], 'inbound' => [27.9050, -15.4445], 'zone' => 'SUR'],
            'ingenio' => ['outbound' => [27.9170, -15.4360], 'inbound' => [27.9170, -15.4360], 'zone' => 'SUR'],
            'cruce_de_arinaga' => ['outbound' => [27.8680, -15.4100], 'inbound' => [27.8680, -15.4100], 'zone' => 'SUR'],
            'arinaga' => ['outbound' => [27.8590, -15.3950], 'inbound' => [27.8590, -15.3950], 'zone' => 'SUR'],
            'carrizal' => ['outbound' => [27.9030, -15.4290], 'inbound' => [27.9030, -15.4290], 'zone' => 'SUR'],
            'melenara' => ['outbound' => [27.9885, -15.3760], 'inbound' => [27.9885, -15.3760], 'zone' => 'TELDE'],
            'salinetas' => ['outbound' => [27.9745, -15.3885], 'inbound' => [27.9745, -15.3885], 'zone' => 'TELDE'],
            // GLOBAL - SUR Y OESTE
            'faroMaspalomas' => ['outbound' => [27.7350, -15.5915], 'inbound' => [27.7350, -15.5915], 'zone' => 'SUR'],
            'autopistaSur' => ['outbound' => [27.9200, -15.4280], 'inbound' => [27.9200, -15.4280], 'zone' => 'SUR'],
            'puertoMogan' => ['outbound' => [27.8155, -15.7635], 'inbound' => [27.8155, -15.7635], 'zone' => 'OESTE'],
            'arguineguin' => ['outbound' => [27.7610, -15.6815], 'inbound' => [27.7610, -15.6815], 'zone' => 'SUR'],
            'puertoRico' => ['outbound' => [27.7870, -15.7085], 'inbound' => [27.7870, -15.7085], 'zone' => 'OESTE'],
            'taurito' => ['outbound' => [27.8020, -15.7380], 'inbound' => [27.8020, -15.7380], 'zone' => 'OESTE'],
            'tableroMaspalomas' => ['outbound' => [27.7690, -15.5740], 'inbound' => [27.7690, -15.5740], 'zone' => 'SUR'],
            'sanFernando' => ['outbound' => [27.7650, -15.5645], 'inbound' => [27.7650, -15.5645], 'zone' => 'SUR'],
            'bahiaFeliz' => ['outbound' => [27.7940, -15.5120], 'inbound' => [27.7940, -15.5120], 'zone' => 'SUR'],
            'playaCura' => ['outbound' => [27.7915, -15.7150], 'inbound' => [27.7915, -15.7150], 'zone' => 'OESTE'],
            'palmitosPark' => ['outbound' => [27.8060, -15.5780], 'inbound' => [27.8060, -15.5780], 'zone' => 'SUR'],
            'aldeaSanNicolas' => ['outbound' => [27.9845, -15.7795], 'inbound' => [27.9845, -15.7795], 'zone' => 'OESTE'],
            'doctoral' => ['outbound' => [27.8310, -15.4650], 'inbound' => [27.8310, -15.4650], 'zone' => 'SUR'],
            'playaArinaga' => ['outbound' => [27.8520, -15.3865], 'inbound' => [27.8520, -15.3865], 'zone' => 'SUR'],
            'castilloRomeral' => ['outbound' => [27.8175, -15.4760], 'inbound' => [27.8175, -15.4760], 'zone' => 'SUR'],
            'sardina_sur' => ['outbound' => [27.8715, -15.4375], 'inbound' => [27.8715, -15.4375], 'zone' => 'SUR'],
            // GLOBAL - NORTE
            'galdar' => ['outbound' => [28.1465, -15.6495], 'inbound' => [28.1465, -15.6495], 'zone' => 'NORTE'],
            'guia' => ['outbound' => [28.1395, -15.6335], 'inbound' => [28.1395, -15.6335], 'zone' => 'NORTE'],
            'agaete' => ['outbound' => [28.1015, -15.6985], 'inbound' => [28.1015, -15.6985], 'zone' => 'NORTE'],
            'puertoNieves' => ['outbound' => [28.0985, -15.7025], 'inbound' => [28.0985, -15.7025], 'zone' => 'NORTE'],
            'moya' => ['outbound' => [28.1095, -15.5870], 'inbound' => [28.1095, -15.5870], 'zone' => 'NORTE'],
            'firgas' => ['outbound' => [28.1090, -15.5615], 'inbound' => [28.1090, -15.5615], 'zone' => 'NORTE'],
            'bananeros' => ['outbound' => [28.1355, -15.5275], 'inbound' => [28.1355, -15.5275], 'zone' => 'NORTE'],
            'cardones' => ['outbound' => [28.1280, -15.5180], 'inbound' => [28.1280, -15.5180], 'zone' => 'NORTE'],
            // GLOBAL - CENTRO
            'santaBrigida' => ['outbound' => [28.0340, -15.5005], 'inbound' => [28.0340, -15.5005], 'zone' => 'CENTRO'],
            'sanMateo' => ['outbound' => [28.0135, -15.5320], 'inbound' => [28.0135, -15.5320], 'zone' => 'CENTRO'],
            'tejeda' => ['outbound' => [27.9940, -15.6140], 'inbound' => [27.9940, -15.6140], 'zone' => 'CENTRO'],
            'artenara' => ['outbound' => [28.0205, -15.6445], 'inbound' => [28.0205, -15.6445], 'zone' => 'CENTRO'],
            'valsequillo' => ['outbound' => [27.9870, -15.4955], 'inbound' => [27.9870, -15.4955], 'zone' => 'CENTRO'],
        ];
    }

}
