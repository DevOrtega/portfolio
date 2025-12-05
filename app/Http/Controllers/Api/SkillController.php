<?php

namespace App\Http\Controllers\Api;

use App\Application\Portfolio\Services\SkillService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * Skill Controller
 * 
 * Handles HTTP requests for skills.
 */
final class SkillController extends Controller
{
    public function __construct(
        private readonly SkillService $skillService
    ) {
    }

    /**
     * @OA\Get(
     *      path="/api/skills",
     *      operationId="getSkills",
     *      tags={"Skills"},
     *      summary="Get list of skills",
     *      description="Returns skills ordered by proficiency. When filtering by year, returns skills associated with experiences/education active in that year, plus personal skills (always shown).",
     *      @OA\Parameter(
     *          name="year",
     *          in="query",
     *          description="Filter skills by year (based on experience and education relationships). Personal skills are always included.",
     *          required=false,
     *          @OA\Schema(type="integer", example=2023)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="Laravel"),
     *                  @OA\Property(property="category", type="string", example="Backend"),
     *                  @OA\Property(property="proficiency", type="integer", example=95),
     *                  @OA\Property(property="is_personal", type="boolean", example=false, description="Indicates if this is a personal/self-taught skill"),
     *                  @OA\Property(property="created_at", type="string", format="date-time"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time")
     *              )
     *          )
     *       )
     * )
     */
    public function index(): JsonResponse
    {
        $year = request()->has('year') && request('year') !== 'all' 
            ? (int) request('year') 
            : null;

        $skills = $this->skillService->getSkillsByYear($year);

        return response()->json($skills->map(fn ($skill) => $skill->toArray())->values());
    }
}
