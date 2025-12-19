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
        private readonly GetHikingRouteService $getHikingRouteService,
        private readonly \App\Application\Hiking\GetRoutePoisService $getRoutePoisService
    ) {}

    /**
     * Get POIs along a route
     *
     * @OA\Post(
     *     path="/api/hiking/pois",
     *     tags={"Hiking"},
     *     summary="Find POIs near a route",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="route",
     *                 type="array",
     *                 @OA\Items(type="array", @OA\Items(type="number"))
     *             ),
     *             @OA\Property(property="radius", type="integer", example=1000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of POIs",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=123456),
     *                 @OA\Property(property="type", type="string", example="node"),
     *                 @OA\Property(property="lat", type="number", format="float", example=28.01),
     *                 @OA\Property(property="lon", type="number", format="float", example=-15.6),
     *                 @OA\Property(property="name", type="string", example="Restaurante El Paso"),
     *                 @OA\Property(property="category", type="string", example="food"),
     *                 @OA\Property(
     *                     property="tags", 
     *                     type="object", 
     *                     example={"amenity": "restaurant", "cuisine": "regional"}
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function pois(Request $request): JsonResponse
    {
        $request->validate([
            'route' => 'required|array',
            'route.*' => 'array|min:2',
            'radius' => 'nullable|integer|min:100|max:5000'
        ]);

        try {
            // route is [[lat, lon], ...] or [[lon, lat], ...]?
            // Our Service expects [lat, lon].
            // OSRM returns [lon, lat]. Frontend usually sends what it has.
            // Let's assume frontend sends what OSRM gave: [lon, lat].
            // We need to swap them if they come as [lon, lat].
            // How to detect? 
            // Lat is usually [-90, 90], Lon [-180, 180]. 
            // In Gran Canaria: Lat ~28, Lon ~-15.
            // If p[0] is -15, it's Lon. If p[0] is 28, it's Lat.
            
            $route = $request->input('route');
            $radius = $request->input('radius', 1000);

            // Simple heuristic to ensure [lat, lon] order
            if (!empty($route) && isset($route[0][0])) {
                // If first coord is negative (Gran Canaria Lon is negative), 
                // and second is positive (Lat is positive), SWAP.
                // Or generally, if abs(val1) < abs(val2) in GC context? No.
                // Lat ~ 28, Lon ~ -15.
                // If first element > 0 (28) -> Lat.
                // If first element < 0 (-15) -> Lon.
                
                // If we receive [lon, lat] ([-15, 28]), we swap to [28, -15].
                // If we receive [lat, lon] ([28, -15]), we keep it.
                
                $first = $route[0];
                if ($first[0] < 0 && $first[1] > 0) {
                     // Swap all
                     $route = array_map(fn($p) => [$p[1], $p[0]], $route);
                }
            }

            $pois = $this->getRoutePoisService->execute($route, $radius);

            return response()->json($pois);

        } catch (\Exception $e) {
            Log::error('Hiking POIs error', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch POIs'], 500);
        }
    }

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
