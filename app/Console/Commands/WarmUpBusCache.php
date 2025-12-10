<?php

namespace App\Console\Commands;

use App\Application\Bus\Services\BusService;
use App\Infrastructure\Services\OsrmService;
use Illuminate\Console\Command;

class WarmUpBusCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bus:cache-warmup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pre-fetch and cache all OSRM routes for bus lines to speed up the API';

    /**
     * Execute the console command.
     */
    public function handle(BusService $busService, OsrmService $osrmService): void
    {
        $this->info('Starting bus route cache warmup...');

        $this->info('Fetching all configured bus routes...');
        // We get the routes from the DB, but they might not have OSRM data yet.
        // Actually, BusService::getRoutes() calls the repository which calls OsrmService.
        // So simply calling getRoutes() will trigger the caching logic in OsrmService!
        
        $startTime = microtime(true);
        
        // This will iterate all lines and trigger getRoute() for each
        $routes = $busService->getRoutes();
        
        $duration = round(microtime(true) - $startTime, 2);
        
        $count = count($routes);
        $this->info("Processed {$count} routes in {$duration} seconds.");
        
        $this->info('✓ Cache warmup completed successfully.');
    }
}
