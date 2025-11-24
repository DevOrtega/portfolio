<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PersonalInfo;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * Personal Info Controller
 * 
 * Handles HTTP requests for personal information.
 */
class PersonalInfoController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/personal-info",
     *      operationId="getPersonalInfo",
     *      tags={"PersonalInfo"},
     *      summary="Get personal information",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Personal info not found"
     *       )
     * )
     */
    public function index(): JsonResponse
    {
        $personalInfo = PersonalInfo::first();
        
        if (!$personalInfo) {
            return response()->json([
                'message' => 'Personal information not found'
            ], 404);
        }
        
        return response()->json($personalInfo);
    }
}
