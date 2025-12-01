<?php

namespace App\Http\Controllers\Admin;

use App\Application\Auth\Services\AdminAuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Http\Resources\Admin\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Admin Auth",
 *     description="Endpoints de autenticación para el panel de administración"
 * )
 */
class AdminAuthController extends Controller
{
    public function __construct(
        private readonly AdminAuthService $authService
    ) {
    }

    /**
     * @OA\Post(
     *     path="/api/admin/auth/login",
     *     summary="Login de administrador",
     *     tags={"Admin Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login exitoso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="token", type="string"),
     *                 @OA\Property(property="expires_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Credenciales inválidas"),
     *     @OA\Response(response=429, description="Demasiados intentos")
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->attemptLogin(
            $request->validated('email'),
            $request->validated('password'),
            $request->ip()
        );

        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
                'token_type' => 'Bearer',
                'expires_at' => $result['expires_at'],
            ],
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/auth/logout",
     *     summary="Logout de administrador",
     *     tags={"Admin Auth"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout exitoso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'Logged out successfully',
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/auth/me",
     *     summary="Obtener usuario actual",
     *     tags={"Admin Auth"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Usuario actual",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'user' => new UserResource($request->user()),
            ],
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/auth/refresh",
     *     summary="Refrescar token",
     *     tags={"Admin Auth"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token refrescado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Token refreshed successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string"),
     *                 @OA\Property(property="expires_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        // Create new token
        $expiresAt = now()->addMinutes(60);
        $token = $user->createToken(
            'admin-token',
            ['admin'],
            $expiresAt
        )->plainTextToken;

        return response()->json([
            'message' => 'Token refreshed successfully',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => $expiresAt->toIso8601String(),
            ],
        ], Response::HTTP_OK);
    }
}
