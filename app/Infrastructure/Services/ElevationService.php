<?php

namespace App\Infrastructure\Services;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;

final readonly class ElevationService
{
    private string $scriptPath;
    private string $tifPath;

    public function __construct()
    {
        $this->scriptPath = base_path('app/Infrastructure/Scripts/add_elevation.py');
        $this->tifPath = base_path('mdt/136_MDT25_GC.tif');
    }

    /**
     * Add elevation to a list of coordinates.
     * 
     * @param array $coordinates Array of [lat, lng]
     * @return array Array of [lat, lng, elevation]
     */
    public function addElevation(array $coordinates): array
    {
        if (empty($coordinates)) {
            return [];
        }

        // Convert [lat, lng] to [lon, lat] for the script
        $inputCoords = array_map(fn($c) => [$c[1], $c[0]], $coordinates);
        
        $jsonInput = json_encode($inputCoords);
        
        // Execute python script
        // Note: Ensure 'python3' is in path
        $result = Process::run(['python3', $this->scriptPath, $jsonInput, $this->tifPath]);

        if ($result->failed()) {
            Log::error('Elevation script failed', [
                'error' => $result->errorOutput(),
                'output' => $result->output()
            ]);
            // Return 0 elevation as fallback
            return array_map(fn($c) => [$c[0], $c[1], 0], $coordinates);
        }

        try {
            $output = json_decode($result->output(), true);
            if (!is_array($output)) {
                throw new \Exception("Invalid JSON output");
            }
            
            // Convert back to [lat, lng, ele]
            // Script returns [lon, lat, ele]
            return array_map(fn($c) => [$c[1], $c[0], $c[2]], $output);
            
        } catch (\Exception $e) {
            Log::error('Elevation script JSON parse error', ['message' => $e->getMessage()]);
            return array_map(fn($c) => [$c[0], $c[1], 0], $coordinates);
        }
    }
}
