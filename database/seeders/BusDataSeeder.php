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
    public function run(): void
    {
        // Disable foreign key checks for truncation
        DB::statement('PRAGMA foreign_keys = OFF');
        DB::table('bus_route_stops')->truncate();
        DB::table('bus_lines')->truncate();
        DB::table('bus_stops')->truncate();
        DB::table('bus_companies')->truncate();
        DB::statement('PRAGMA foreign_keys = ON');

        $this->seedCompanies();

        // 1. Seed Municipales (from standard GTFS path)
        $this->command->info('Seeding Municipales from GTFS...');
        $importerMunicipales = new GtfsImporter();
        $this->seedGtfsStops($importerMunicipales);
        $this->seedGtfsLines($importerMunicipales);

        // 2. Seed Global (from generated global GTFS path)
        $this->command->info('Seeding Global from Generated GTFS...');
        $importerGlobal = new GtfsImporter(base_path('gtfs/global'));
        $this->seedGtfsStops($importerGlobal);
        $this->seedGtfsLines($importerGlobal);
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

    private function seedGtfsStops(GtfsImporter $importer): void
    {
        $stops = $importer->getAllStops();
        $bulk = [];
        foreach ($stops as $stop) {
            $bulk[] = [
                'code' => $stop['code'],
                'name' => $stop['name'],
                'lat_outbound' => $stop['lat'],
                'lng_outbound' => $stop['lng'],
                'lat_inbound' => $stop['lat'], 
                'lng_inbound' => $stop['lng'],
                'zone' => 'URBAN', // Default zone
            ];
        }
        
        if (!empty($bulk)) {
            DB::transaction(function () use ($bulk) {
                 BusStopModel::upsert($bulk, ['code'], ['name', 'lat_outbound', 'lng_outbound', 'lat_inbound', 'lng_inbound', 'zone']);
            });
            $this->command->info('✓ GTFS Stops seeded: ' . count($bulk));
        }
    }

    private function seedGtfsLines(GtfsImporter $importer): void
    {
        $routes = $importer->getRoutes();
        $companies = DB::table('bus_companies')->pluck('id', 'code');
        $stops = DB::table('bus_stops')->pluck('id', 'code');
        
        foreach ($routes as $route) {
            $companyCode = $route['company']; // 'global', 'night', 'municipales'
            $companyId = $companies[$companyCode] ?? null;

            if (!$companyId) {
                // Fallback logic
                if ($companyCode === 'night') $companyId = $companies['night'];
                else $companyId = $companies['municipales'];
            }
            
            if (!$companyId) {
                $this->command->warn("Company ID not found for code: {$companyCode}. Skipping line {$route['line']}");
                continue;
            }

            // Insert Line
            $lineId = DB::table('bus_lines')->insertGetId([
                'line_number' => $route['line'],
                'type' => $route['type'],
                'origin' => $route['origin'],
                'destination' => $route['destination'],
                'color' => $route['color'],
                'company_id' => $companyId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert Route Stops (Outbound)
            $bulkStops = [];
            foreach ($route['stopsOutbound'] as $order => $stopCode) {
                if (isset($stops[$stopCode])) {
                    $bulkStops[] = [
                        'line_id' => $lineId,
                        'stop_id' => $stops[$stopCode],
                        'direction' => 'outbound',
                        'order' => $order,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            // Insert Route Stops (Inbound)
             foreach ($route['stopsInbound'] as $order => $stopCode) {
                if (isset($stops[$stopCode])) {
                    $bulkStops[] = [
                        'line_id' => $lineId,
                        'stop_id' => $stops[$stopCode],
                        'direction' => 'inbound',
                        'order' => $order,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($bulkStops)) {
                DB::table('bus_route_stops')->insert($bulkStops);
            }
        }
        $this->command->info('✓ GTFS Lines seeded: ' . count($routes));
    }
}