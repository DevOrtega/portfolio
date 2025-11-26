<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * Project Controller
 * 
 * Handles HTTP requests for projects.
 */
class ProjectController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/projects",
     *      operationId="getProjects",
     *      tags={"Projects"},
     *      summary="Get list of projects",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function index(): JsonResponse
    {
        $projects = Project::all();

        return response()->json($projects);
    }
}
