<?php

namespace App\Http\Controllers\Api;

use App\Application\Portfolio\Services\ContactService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use Illuminate\Http\JsonResponse;

/**
 * Contact Controller
 * 
 * Handles HTTP requests for contact form submissions.
 * Uses ContactService following Hexagonal Architecture.
 */
final class ContactController extends Controller
{
    public function __construct(
        private readonly ContactService $contactService
    ) {
    }

    /**
     * Handle contact form submission.
     * 
     * @OA\Post(
     *     path="/api/contact",
     *     summary="Send contact form message",
     *     tags={"Contact"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","subject","message"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="subject", type="string", example="Project Inquiry"),
     *             @OA\Property(property="message", type="string", example="Hello, I would like to discuss a project...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Mensaje enviado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too many requests"
     *     )
     * )
     */
    public function send(ContactRequest $request): JsonResponse
    {
        // Check rate limiting
        $rateLimit = $this->contactService->checkRateLimit($request->ip());
        
        if ($rateLimit['exceeded']) {
            return response()->json([
                'success' => false,
                'message' => "Demasiados intentos. Por favor, espera {$rateLimit['minutes']} minutos.",
            ], 429);
        }
        
        // Record attempt
        $this->contactService->recordAttempt($request->ip());

        try {
            // Prepare contact data with metadata
            $data = $this->contactService->prepareContactData(
                $request->validated(),
                $request->ip(),
                $request->userAgent()
            );
            
            // Send email via service
            $this->contactService->sendContactEmail($data);
            
            return response()->json([
                'success' => true,
                'message' => $this->contactService->getSuccessMessage($data['locale']),
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el mensaje. Por favor, inténtalo más tarde.',
            ], 500);
        }
    }
}
