<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class ExperienceController extends Controller
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
    public function index()
    {
        return response()->json(Experience::all());
    }
}
