<?php

namespace App\Http\Controllers\Api;

use App\Application\Portfolio\Services\EducationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * Education Controller
 * 
 * Handles HTTP requests for education records.
 */
final class EducationController extends Controller
{
    public function __construct(
        private readonly EducationService $educationService
    ) {
    }

    /**
     * @OA\Get(
     *      path="/api/education",
     *      operationId="getEducation",
     *      tags={"Education"},
     *      summary="Get list of education",
     *      @OA\Parameter(
     *          name="year",
     *          in="query",
     *          description="Filter education by year",
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

        $education = $this->educationService->getEducationByYear($year);

        return response()->json($education->map(fn ($edu) => $edu->toArray())->values());
    }
}
