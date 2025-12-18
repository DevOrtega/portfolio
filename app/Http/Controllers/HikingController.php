<?php

namespace App\Http\Controllers;

use App\Application\Hiking\GetHikingRouteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Hiking",
 *     description="Hiking route planning and elevation data"
 * )
 */
class HikingController extends Controller
{
    public function __construct(
        private readonly GetHikingRouteService $getHikingRouteService
    ) {}

    /**
     * Get a hiking route with elevation profile
     * 
     * @OA\Get(
     *     path="/api/hiking/route",
     *     operationId="getHikingRoute",
     *     tags={"Hiking"},
     *     summary="Calculate a hiking route with 3D elevation profile",
     *     description="Calculates the route between two points using OSRM (foot profile) and enriches it with elevation data from local MDT files.",
     *     @OA\Parameter(
     *         name="start",
     *         in="query",
     *         description="Start coordinates (lat,lon)",
     *         required=true,
     *         @OA\Schema(type="string", example="27.9706,-15.6128")
     *     ),
     *     @OA\Parameter(
     *         name="end",
     *         in="query",
     *         description="End coordinates (lat,lon)",
     *         required=true,
     *         @OA\Schema(type="string", example="28.1235,-15.4363")
     *     ),
     *     @OA\Parameter(
     *         name="waypoints[]",
     *         in="query",
     *         description="List of intermediate coordinates (lat,lon)",
     *         required=false,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="string", example="28.05,-15.55")
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="intermediate",
     *         in="query",
     *         description="Legacy intermediate point (lat,lon). Deprecated, use waypoints[] instead.",
     *         deprecated=true,
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="type", type="string", example="Feature"),
     *             @OA\Property(
     *                 property="properties",
     *                 type="object",
     *                 @OA\Property(property="distance_km", type="number", format="float"),
     *                 @OA\Property(property="elevation_gain_m", type="number", format="float"),
     *                 @OA\Property(property="elevation_loss_m", type="number", format="float"),
     *                 @OA\Property(property="max_elevation_m", type="number", format="float"),
     *                 @OA\Property(property="min_elevation_m", type="number", format="float")
     *             ),
     *             @OA\Property(
     *                 property="geometry",
     *                 type="object",
     *                 @OA\Property(property="type", type="string", example="LineString"),
     *                 @OA\Property(
     *                     property="coordinates", 
     *                     type="array",
     *                     description="Array of [lon, lat, ele] coordinates",
     *                     @OA\Items(
     *                         type="array",
     *                         @OA\Items(type="number")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input format"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error or route calculation failed"
     *     )
     * )
     */
    public function route(Request $request): JsonResponse
    {
        $request->validate([
            'start' => 'required|string', // "lat,lon"
            'end' => 'required|string',   // "lat,lon"
            'intermediate' => 'nullable|string', // "lat,lon" (Legacy)
            'waypoints' => 'nullable|array', // ["lat,lon", "lat,lon"]
            'waypoints.*' => 'string',
        ]);

        try {
            $start = array_map('floatval', explode(',', $request->input('start')));
            $end = array_map('floatval', explode(',', $request->input('end')));
            
            $waypoints = [];
            
            // Handle legacy single intermediate point
            if ($request->filled('intermediate')) {
                $intermediate = array_map('floatval', explode(',', $request->input('intermediate')));
                if (count($intermediate) === 2) {
                    $waypoints[] = $intermediate;
                }
            }

            // Handle new multiple waypoints
            if ($request->filled('waypoints')) {
                foreach ($request->input('waypoints') as $wpString) {
                    $wpCoords = array_map('floatval', explode(',', $wpString));
                    if (count($wpCoords) === 2) {
                        $waypoints[] = $wpCoords;
                    }
                }
            }
            
            if (count($start) !== 2 || count($end) !== 2) {
                return response()->json(['error' => 'Invalid coordinates format. Use lat,lon'], 400);
            }

            $result = $this->getHikingRouteService->execute($start, $end, empty($waypoints) ? null : $waypoints);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Hiking route error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to calculate route', 'details' => $e->getMessage()], 500);
        }
    }
}
