<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GtfsImportCommand extends Command
{
    protected $signature = 'gtfs:import';
    protected $description = 'Importa datos GTFS directamente a la base de datos';
    // Eliminado: protected $importLogs = [];
    public function handle()
    {
        // DEBUG: Mostrar ejemplos de datos clave para los joins
        $this->info('Ejemplo gtfs_trips:');
        $this->line(json_encode(DB::table('gtfs_trips')->select('trip_id', 'route_id', 'direction_id')->limit(3)->get()));
        $this->info('Ejemplo gtfs_routes:');
        $this->line(json_encode(DB::table('gtfs_routes')->select('route_id', 'route_short_name', 'agency_id')->limit(3)->get()));
        $this->info('Ejemplo gtfs_stop_times:');
        $this->line(json_encode(DB::table('gtfs_stop_times')->select('trip_id', 'stop_id', 'stop_sequence')->limit(3)->get()));
        $this->info('Ejemplo bus_lines:');
        $this->line(json_encode(DB::table('bus_lines')->select('id', 'line_number')->limit(3)->get()));
        $this->info('Ejemplo bus_stops:');
        $importLogs = [];
        $dbPath = database_path('database.sqlite');
        $gtfsPath = base_path('gtfs');


        $this->info('Recreando tabla gtfs_routes...');
        DB::statement('DROP TABLE IF EXISTS gtfs_routes');
        DB::statement('CREATE TABLE gtfs_routes (
            route_id TEXT,
            agency_id TEXT,
            route_short_name TEXT,
            route_long_name TEXT,
            route_desc TEXT,
            route_type TEXT,
            route_url TEXT,
            route_color TEXT,
            route_text_color TEXT
        )');
        $this->info('Importando GTFS: routes.csv...');
        $this->importCsv($dbPath, "$gtfsPath/routes.csv", 'gtfs_routes');
        $routesCount = DB::table('gtfs_routes')->count();
        $this->info("gtfs_routes: $routesCount registros");

        $this->info('Recreando tabla gtfs_stops...');
        DB::statement('DROP TABLE IF EXISTS gtfs_stops');
        DB::statement('CREATE TABLE gtfs_stops (
            stop_id TEXT,
            stop_code TEXT,
            stop_name TEXT,
            stop_desc TEXT,
            stop_lat REAL,
            stop_lon REAL,
            zone_id TEXT,
            stop_url TEXT,
            location_type TEXT,
            parent_station TEXT,
            stop_timezone TEXT,
            wheelchair_boarding TEXT
        )');
        $this->importCsv($dbPath, "$gtfsPath/stops.csv", 'gtfs_stops');
        $stopsCount = DB::table('gtfs_stops')->count();
        $this->info("gtfs_stops: $stopsCount registros");

        $this->info('Importando stop_times.csv...');
        $this->importCsv($dbPath, "$gtfsPath/stop_times.csv", 'gtfs_stop_times');
        $stopTimesCount = DB::table('gtfs_stop_times')->count();
        $this->info("gtfs_stop_times: $stopTimesCount registros");

        $this->info('Recreando tabla gtfs_trips...');
        DB::statement('DROP TABLE IF EXISTS gtfs_trips');
        DB::statement('CREATE TABLE gtfs_trips (
            route_id TEXT,
            service_id TEXT,
            trip_id TEXT,
            trip_headsign TEXT,
            trip_short_name TEXT,
            direction_id TEXT,
            block_id TEXT,
            shape_id TEXT,
            wheelchair_accessible TEXT
        )');
        $this->info('Importando trips.csv...');
        $this->importCsv($dbPath, "$gtfsPath/trips.csv", 'gtfs_trips');
        $tripsCount = DB::table('gtfs_trips')->count();
        $this->info("gtfs_trips: $tripsCount registros");


        // Poblar tablas finales con SQL puro

        $this->info('Adaptando tabla bus_stops...');
        $columns = DB::getSchemaBuilder()->getColumnListing('bus_stops');
        $alterSql = [];
        if (!in_array('lat_outbound', $columns)) {
            $alterSql[] = 'ADD COLUMN lat_outbound DECIMAL(10,6)';
        }
        if (!in_array('lng_outbound', $columns)) {
            $alterSql[] = 'ADD COLUMN lng_outbound DECIMAL(10,6)';
        }
        if (!in_array('lat_inbound', $columns)) {
            $alterSql[] = 'ADD COLUMN lat_inbound DECIMAL(10,6)';
        }
        if (!in_array('lng_inbound', $columns)) {
            $alterSql[] = 'ADD COLUMN lng_inbound DECIMAL(10,6)';
        }
        if (!in_array('zone', $columns)) {
            $alterSql[] = 'ADD COLUMN zone TEXT';
        }
        if (count($alterSql) > 0) {
            foreach ($alterSql as $sql) {
                DB::statement("ALTER TABLE bus_stops $sql");
            }
        }

        $this->info('Limpiando tabla bus_stops...');
        DB::statement('DELETE FROM bus_stops');
        $this->info('Insertando datos en bus_stops...');
        DB::statement('INSERT INTO bus_stops (code, name, zone, lat_outbound, lng_outbound, lat_inbound, lng_inbound, created_at, updated_at)
            SELECT stop_id, stop_name, NULL, CAST(stop_lat AS REAL), CAST(stop_lon AS REAL), CAST(stop_lat AS REAL), CAST(stop_lon AS REAL), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM gtfs_stops');
        $busStopsCount = DB::table('bus_stops')->count();
        $this->info("bus_stops: $busStopsCount registros");

        // Líneas municipales: columnas correctas y origen/destino extraídos
        $this->info('Limpiando tabla bus_lines...');
        DB::statement('DELETE FROM bus_lines');
        $companyId = DB::table('bus_companies')->where('code', 'municipales')->value('id');
        $this->info('Insertando datos en bus_lines...');
        DB::statement('INSERT INTO bus_lines (line_number, type, origin, destination, color, company_id, created_at, updated_at) 
            SELECT 
                route_short_name,
                "urban",
                substr(route_long_name, 1, instr(route_long_name, " - ")-1),
                substr(route_long_name, instr(route_long_name, " - ")+3),
                "#FDB913",
                ?,
                CURRENT_TIMESTAMP,
                CURRENT_TIMESTAMP
            FROM gtfs_routes WHERE agency_id = "Guaguas"', [$companyId]);
        $busLinesCount = DB::table('bus_lines')->count();
        $this->info("bus_lines: $busLinesCount registros");

        // Insertar relaciones bus_route_stops después de poblar bus_lines
        $this->info('Insertando relaciones bus_route_stops OUTBOUND...');
        DB::statement('DELETE FROM bus_route_stops');
        DB::insert('INSERT INTO bus_route_stops (line_id, stop_id, direction, "order", created_at, updated_at)
            SELECT DISTINCT bl.id, bs.id, "outbound", st.stop_sequence, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
            FROM gtfs_stop_times st
            JOIN gtfs_trips t ON st.trip_id = t.trip_id
            JOIN gtfs_routes r ON t.route_id = r.route_id
            JOIN bus_lines bl ON bl.line_number = r.route_short_name
            JOIN bus_stops bs ON bs.code = st.stop_id
            WHERE r.agency_id = "Guaguas" AND t.direction_id = "1"');
        $outCount = DB::table('bus_route_stops')->where('direction', 'outbound')->count();
        $importLogs[] = "bus_route_stops OUTBOUND tras insert: $outCount";

        $this->info('Insertando relaciones bus_route_stops INBOUND...');
        DB::insert('INSERT INTO bus_route_stops (line_id, stop_id, direction, "order", created_at, updated_at)
            SELECT DISTINCT bl.id, bs.id, "inbound", st.stop_sequence, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
            FROM gtfs_stop_times st
            JOIN gtfs_trips t ON st.trip_id = t.trip_id
            JOIN gtfs_routes r ON t.route_id = r.route_id
            JOIN bus_lines bl ON bl.line_number = r.route_short_name
            JOIN bus_stops bs ON bs.code = st.stop_id
            WHERE r.agency_id = "Guaguas" AND t.direction_id = "0"');
        $inCount = DB::table('bus_route_stops')->where('direction', 'inbound')->count();
        $importLogs[] = "bus_route_stops INBOUND tras insert: $inCount";
        // Mostrar todos los logs diferidos al final
        $this->info('Resumen de conteos tras inserts:');
        foreach ($importLogs as $log) {
            $this->info($log);
        }

        // Mostrar los primeros 5 registros OUTBOUND e INBOUND para depuración
        $outbound = DB::table('bus_route_stops')->where('direction', 'outbound')->limit(5)->get();
        $inbound = DB::table('bus_route_stops')->where('direction', 'inbound')->limit(5)->get();
        $this->info('Primeros OUTBOUND: ' . json_encode($outbound));
        $this->info('Primeros INBOUND: ' . json_encode($inbound));

        // Mostrar si hay errores de inserción (por ejemplo, si la tabla está vacía tras el insert)
        if ($outCount === 0 && $inCount === 0) {
            $this->error('¡No se insertaron relaciones en bus_route_stops! Revisa los joins y los datos fuente.');
        }

        $this->info('Importación GTFS completada.');
    }

    private function importCsv($dbPath, $csvPath, $table)
    {
        // Convertir ruta absoluta a relativa si es necesario y limpiar comillas
        $projectRoot = base_path();
        if (strpos($csvPath, $projectRoot) === 0) {
            $csvPath = ltrim(str_replace($projectRoot, '', $csvPath), DIRECTORY_SEPARATOR);
        }
        $csvPath = str_replace(['"', "'", '"'], '', $csvPath); // Eliminar cualquier comilla

        // Usar archivo SQL temporal para modo batch
        $sql = [];

        if (basename($csvPath) === 'stop_times.csv' && $table === 'bus_route_stops') {
            $tmpTable = 'tmp_stop_times';
            $sql[] = "DROP TABLE IF EXISTS $tmpTable;";
            $sql[] = "CREATE TABLE $tmpTable (trip_id TEXT, arrival_time TEXT, departure_time TEXT, stop_id TEXT, stop_sequence INTEGER, stop_headsign TEXT, pickup_type INTEGER, drop_off_type INTEGER, shape_dist_traveled TEXT, timepoint INTEGER);";
            $sql[] = ".mode csv";
            $sql[] = ".import $csvPath $tmpTable";
            $sql[] = "DELETE FROM $tmpTable WHERE rowid = (SELECT MIN(rowid) FROM $tmpTable);";
            $sql[] = "INSERT INTO bus_route_stops (trip_id, arrival_time, departure_time, stop_id, stop_sequence) SELECT trip_id, arrival_time, departure_time, stop_id, stop_sequence FROM $tmpTable;";
            $sql[] = "DROP TABLE $tmpTable;";
        } else {
            $sql[] = ".mode csv";
            $sql[] = ".import $csvPath $table";
            $sql[] = "DELETE FROM $table WHERE rowid = (SELECT MIN(rowid) FROM $table);";
        }

        // Escribir archivo SQL temporal
        $tmpFile = tempnam(sys_get_temp_dir(), 'sqlite_batch_');
        file_put_contents($tmpFile, implode("\n", $sql));

        // Ejecutar en modo batch
        exec("sqlite3 $dbPath < $tmpFile");

        // Eliminar archivo temporal
        unlink($tmpFile);
    }
}
