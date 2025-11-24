<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * Skill Controller
 * 
 * Handles HTTP requests for skills.
 */
class SkillController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/skills",
     *      operationId="getSkills",
     *      tags={"Skills"},
     *      summary="Get list of skills",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function index(): JsonResponse
    {
        $skills = Skill::orderBy('category')->orderBy('name')->get();
        return response()->json($skills);
    }
}
