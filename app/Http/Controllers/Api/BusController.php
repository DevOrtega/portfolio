<?php

namespace App\Http\Controllers\Api;

use App\Application\Bus\Services\BusService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Bus",
 *     description="Bus routes and stops data"
 * )
 */
final class BusController extends Controller
{
    public function __construct(
        private readonly BusService $busService
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/bus/data",
     *     operationId="getBusData",
     *     tags={"Bus"},
     *     summary="Get all bus data for the tracker demo",
     *     description="Returns companies, lines, routes, stops and configuration",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="companies", type="object"),
     *             @OA\Property(property="bus_lines", type="object"),
     *             @OA\Property(property="main_lines", type="object"),
     *             @OA\Property(property="routes", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="stops", type="object"),
     *             @OA\Property(property="map_config", type="object"),
     *             @OA\Property(property="simulation_config", type="object")
     *         )
     *     )
     * )
     */
    public function data(): JsonResponse
    {
        return response()->json($this->busService->getBusData());
    }

    /**
     * @OA\Get(
     *     path="/api/bus/routes",
     *     operationId="getBusRoutes",
     *     tags={"Bus"},
     *     summary="Get all bus routes with coordinates",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function routes(): JsonResponse
    {
        return response()->json([
            'routes' => $this->busService->getRoutes(),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/bus/stops",
     *     operationId="getBusStops",
     *     tags={"Bus"},
     *     summary="Get all bus stops",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function stops(): JsonResponse
    {
        return response()->json([
            'stops' => $this->busService->getStops(),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/bus/companies",
     *     operationId="getBusCompanies",
     *     tags={"Bus"},
     *     summary="Get all bus companies",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function companies(): JsonResponse
    {
        return response()->json([
            'companies' => $this->busService->getCompanyColors(),
        ]);
    }
}
