<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class EducationController extends Controller
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
    public function index()
    {
        return response()->json(Education::all());
    }
}
