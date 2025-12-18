<?php

namespace App\Infrastructure\Services;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;
use App\Domain\Hiking\ElevationProviderInterface;

final readonly class ElevationService implements ElevationProviderInterface
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

        Log::info('Elevation script input', [
            'script' => $this->scriptPath,
            'tif_path' => $this->tifPath,
            'json_input' => $jsonInput
        ]);
        
        // Execute python script
        // Note: Ensure 'python3' is in path
        $result = Process::run(['python3', $this->scriptPath, $jsonInput, $this->tifPath]);

        if ($result->failed()) {
            Log::error('Elevation script failed', [
                'error_output' => $result->errorOutput(),
                'output' => $result->output(),
                'exit_code' => $result->exitCode()
            ]);
            // Return original coordinates with 0 elevation as fallback
            return array_map(fn($c) => [$c[0], $c[1], 0], $coordinates);
        }

        try {
            $output = json_decode($result->output(), true);

            Log::info('Elevation script raw output', ['raw_output' => $result->output()]);
            Log::info('Elevation script decoded output', ['decoded_output' => $output]);

            if (!is_array($output)) {
                Log::warning('Elevation script returned non-array output', ['output_type' => gettype($output)]);
                throw new \Exception("Invalid JSON output: expected array");
            }
            if (empty($output)) {
                Log::warning('Elevation script returned empty array for elevation data.');
                // Fallback: return original coordinates with 0 elevation
                return array_map(fn($c) => [$c[0], $c[1], 0], $coordinates);
            }
            
            // Convert back to [lat, lng, ele]
            // Script returns [lon, lat, ele]
            return array_map(fn($c) => [$c[1], $c[0], $c[2]], $output);
            
        } catch (\Exception $e) {
            Log::error('Elevation script JSON parse or processing error', ['message' => $e->getMessage()]);
            return array_map(fn($c) => [$c[0], $c[1], 0], $coordinates);
        }
    }
}
