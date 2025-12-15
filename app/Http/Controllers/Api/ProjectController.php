<?php

namespace App\Http\Controllers\Api;

use App\Application\Portfolio\Services\ProjectService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * Project Controller
 * 
 * Handles HTTP requests for projects.
 * Uses ProjectService following Hexagonal Architecture.
 */
final class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService
    ) {
    }

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
        $projects = $this->projectService->getAllProjects();

        return response()->json($projects->map(fn($project) => $project->toArray(app()->getLocale())));
    }
}
