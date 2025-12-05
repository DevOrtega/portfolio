<?php

namespace App\Http\Controllers\Api;

use App\Application\Portfolio\Services\ExperienceService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * Experience Controller
 * 
 * Handles HTTP requests for work experiences.
 */
final class ExperienceController extends Controller
{
    public function __construct(
        private readonly ExperienceService $experienceService
    ) {
    }

    /**
     * @OA\Get(
     *      path="/api/experiences",
     *      operationId="getExperiences",
     *      tags={"Experiences"},
     *      summary="Get list of experiences",
     *      @OA\Parameter(
     *          name="year",
     *          in="query",
     *          description="Filter experiences by year",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function index(): JsonResponse
    {
        $year = request()->has('year') && request('year') !== 'all' 
            ? (int) request('year') 
            : null;

        $experiences = $this->experienceService->getExperiencesByYear($year);

        return response()->json($experiences->map(fn ($exp) => $exp->toArray())->values());
    }
}
