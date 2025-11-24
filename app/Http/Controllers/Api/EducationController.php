<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * Education Controller
 * 
 * Handles HTTP requests for education records.
 */
final class EducationController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/education",
     *      operationId="getEducation",
     *      tags={"Education"},
     *      summary="Get list of education",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function index(): JsonResponse
    {
        $education = Education::orderBy('start_date', 'desc')->get();
        return response()->json($education);
    }
}
