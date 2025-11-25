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
     *      @OA\Parameter(
     *          name="year",
     *          in="query",
     *          description="Filter skills by year (based on experience descriptions)",
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
        $query = Skill::query();

        // Filter by year if provided
        if (request()->has('year') && request('year') !== 'all') {
            $year = (int) request('year');

            // Get experiences active during the specified year
            $relevantExperiences = \App\Models\Experience::where(function ($q) use ($year) {
                $q->whereRaw("CAST(SUBSTR(start_date, -4) AS INTEGER) <= ?", [$year])
                    ->where(function ($subQ) use ($year) {
                        $subQ->whereNull('end_date')
                            ->orWhereRaw("CAST(SUBSTR(end_date, -4) AS INTEGER) >= ?", [$year]);
                    });
            })->get();

            // Extract skill names mentioned in experience descriptions
            $skillNames = [];
            foreach ($relevantExperiences as $exp) {
                $description = strtolower($exp->description);
                $allSkills = Skill::all();

                foreach ($allSkills as $skill) {
                    if (str_contains($description, strtolower($skill->name))) {
                        $skillNames[] = $skill->name;
                    }
                }
            }

            // Filter skills by names found in experiences
            // If no experiences found for this year, show all skills instead of none
            if (!empty($skillNames)) {
                $query->whereIn('name', array_unique($skillNames));
            }
            // If no relevant experiences, don't filter skills (show all)
        }

        // Order by proficiency descending (highest first), then by category and name
        $skills = $query->orderBy('proficiency', 'desc')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return response()->json($skills);
    }
}
