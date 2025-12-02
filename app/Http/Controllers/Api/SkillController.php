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
final class SkillController extends Controller
{
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
        $query = Skill::query();

        // Filter by year if provided
        if (request()->has('year') && request('year') !== 'all') {
            $year = (int) request('year');

            // Get experiences active during the specified year
            $relevantExperienceIds = \App\Models\Experience::where(function ($q) use ($year) {
                $q->whereRaw("CAST(SUBSTR(start_date, -4) AS INTEGER) <= ?", [$year])
                    ->where(function ($subQ) use ($year) {
                        $subQ->whereNull('end_date')
                            ->orWhereRaw("CAST(SUBSTR(end_date, -4) AS INTEGER) >= ?", [$year]);
                    });
            })->pluck('id');

            // Get educations active during the specified year
            $relevantEducationIds = \App\Models\Education::where(function ($q) use ($year) {
                $q->whereRaw("CAST(SUBSTR(start_date, -4) AS INTEGER) <= ?", [$year])
                    ->where(function ($subQ) use ($year) {
                        $subQ->whereNull('end_date')
                            ->orWhereRaw("CAST(SUBSTR(end_date, -4) AS INTEGER) >= ?", [$year]);
                    });
            })->pluck('id');

            // Filter skills: show personal skills always OR skills related to experiences/educations
            $query->where(function ($q) use ($relevantExperienceIds, $relevantEducationIds) {
                // Always show personal skills
                $q->where('is_personal', true);

                // Also show skills from relevant experiences
                if ($relevantExperienceIds->isNotEmpty()) {
                    $q->orWhereHas('experiences', function ($subQ) use ($relevantExperienceIds) {
                        $subQ->whereIn('experiences.id', $relevantExperienceIds);
                    });
                }

                // Also show skills from relevant educations
                if ($relevantEducationIds->isNotEmpty()) {
                    $q->orWhereHas('educations', function ($subQ) use ($relevantEducationIds) {
                        $subQ->whereIn('education.id', $relevantEducationIds);
                    });
                }
            });
        }

        // Order by proficiency descending (highest first), then by category and name
        $skills = $query->orderBy('proficiency', 'desc')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return response()->json($skills);
    }
}
