<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PersonalInfo;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

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
     *       )
     * )
     */
    public function index()
    {
        return response()->json(PersonalInfo::first());
    }
}
