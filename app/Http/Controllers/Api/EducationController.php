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
        $query = Education::query();

        // Filter by year if provided
        if (request()->has('year') && request('year') !== 'all') {
            $year = (int) request('year');

            // Filter education that was active during the specified year
            $query->where(function ($q) use ($year) {
                $q->whereRaw("CAST(SUBSTR(start_date, -4) AS INTEGER) <= ?", [$year])
                    ->where(function ($subQ) use ($year) {
                        $subQ->whereNull('end_date')
                            ->orWhereRaw("CAST(SUBSTR(end_date, -4) AS INTEGER) >= ?", [$year]);
                    });
            });
        }

        // Order by start_date descending (most recent first)
        $education = $query->orderByRaw("CAST(SUBSTR(start_date, -4) AS INTEGER) DESC")
            ->orderByRaw("CASE 
                              WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Ene' THEN 1
                              WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Feb' THEN 2
                              WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Mar' THEN 3
                              WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Abr' THEN 4
                              WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'May' THEN 5
                              WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Jun' THEN 6
                              WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Jul' THEN 7
                              WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Ago' THEN 8
                              WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Sept' THEN 9
                              WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Oct' THEN 10
                              WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Nov' THEN 11
                              WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Dic' THEN 12
                              ELSE 0 END DESC")
            ->get();

        return response()->json($education);
    }
}
