<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Mail\ContactFormMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
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
        // Additional rate limiting per IP (beyond route throttle)
        $key = 'contact-form:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            
            return response()->json([
                'success' => false,
                'message' => "Demasiados intentos. Por favor, espera {$minutes} minutos.",
            ], 429);
        }
        
        RateLimiter::hit($key, 600); // 10 minutes decay

        try {
            // Get validated and sanitized data
            $data = $request->validated();
            
            // Remove honeypot field and extract locale
            unset($data['website']);
            $locale = $data['locale'] ?? 'es';
            unset($data['locale']);
            
            // Add metadata for security logging
            $data['ip_address'] = $request->ip();
            $data['user_agent'] = substr($request->userAgent() ?? '', 0, 500);
            $data['submitted_at'] = now()->toDateTimeString();
            $data['locale'] = $locale;
            
            // Send email
            Mail::to(config('mail.contact_email', 'carloso2103@gmail.com'))
                ->send(new ContactFormMail($data));
            
            // Log successful submission
            Log::info('Contact form submitted', [
                'from' => $data['email'],
                'subject' => $data['subject'],
                'ip' => $data['ip_address'],
            ]);
            
            // Return message in user's locale
            $successMessage = $locale === 'en' 
                ? 'Message sent successfully. Thank you for contacting!'
                : 'Mensaje enviado correctamente. ¡Gracias por contactar!';
            
            return response()->json([
                'success' => true,
                'message' => $successMessage,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Contact form error', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el mensaje. Por favor, inténtalo más tarde.',
            ], 500);
        }
    }
}
