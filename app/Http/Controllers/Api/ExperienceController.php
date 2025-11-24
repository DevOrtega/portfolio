<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * Experience Controller
 * 
 * Handles HTTP requests for work experiences.
 */
final class ExperienceController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/experiences",
     *      operationId="getExperiences",
     *      tags={"Experiences"},
     *      summary="Get list of experiences",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function index(): JsonResponse
    {
        $experiences = Experience::orderBy('start_date', 'desc')->get();
        return response()->json($experiences);
    }
}
