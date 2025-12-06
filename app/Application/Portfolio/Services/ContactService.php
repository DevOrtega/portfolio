<?php

namespace App\Application\Portfolio\Services;

use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Application Service: ContactService
 * 
 * Handles business logic for contact form submissions.
 * Follows Single Responsibility Principle - only handles contact-related logic.
 */
final readonly class ContactService
{
    private const MAX_ATTEMPTS = 5;
    private const DECAY_SECONDS = 600; // 10 minutes

    /**
     * Check if the IP has exceeded rate limits
     * 
     * @param string $ip
     * @return array{exceeded: bool, minutes: int}
     */
    public function checkRateLimit(string $ip): array
    {
        $key = $this->getRateLimitKey($ip);
        
        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);
            return [
                'exceeded' => true,
                'minutes' => (int) ceil($seconds / 60),
            ];
        }

        return ['exceeded' => false, 'minutes' => 0];
    }

    /**
     * Record a rate limit hit
     * 
     * @param string $ip
     */
    public function recordAttempt(string $ip): void
    {
        RateLimiter::hit($this->getRateLimitKey($ip), self::DECAY_SECONDS);
    }

    /**
     * Send contact form email
     * 
     * @param array $data Contact form data
     * @return bool Success status
     */
    public function sendContactEmail(array $data): bool
    {
        try {
            $recipientEmail = config('mail.contact_email', 'carloso2103@gmail.com');
            
            Mail::to($recipientEmail)->send(new ContactFormMail($data));
            
            $this->logSuccessfulSubmission($data);
            
            return true;
        } catch (\Exception $e) {
            $this->logFailedSubmission($data, $e);
            throw $e;
        }
    }

    /**
     * Prepare contact data with metadata
     * 
     * @param array $validatedData Validated form data
     * @param string $ip Client IP address
     * @param string|null $userAgent User agent string
     * @return array Prepared data with metadata
     */
    public function prepareContactData(array $validatedData, string $ip, ?string $userAgent): array
    {
        // Remove honeypot field and extract locale
        $locale = $validatedData['locale'] ?? 'es';
        
        unset($validatedData['website'], $validatedData['locale']);
        
        return array_merge($validatedData, [
            'ip_address' => $ip,
            'user_agent' => substr($userAgent ?? '', 0, 500),
            'submitted_at' => now()->toDateTimeString(),
            'locale' => $locale,
        ]);
    }

    /**
     * Get localized success message
     * 
     * @param string $locale
     * @return string
     */
    public function getSuccessMessage(string $locale): string
    {
        return $locale === 'en'
            ? 'Message sent successfully. Thank you for contacting!'
            : 'Mensaje enviado correctamente. ¡Gracias por contactar!';
    }

    /**
     * Get rate limit key for IP
     */
    private function getRateLimitKey(string $ip): string
    {
        return 'contact-form:' . $ip;
    }

    /**
     * Log successful submission
     */
    private function logSuccessfulSubmission(array $data): void
    {
        Log::info('Contact form submitted', [
            'from' => $data['email'],
            'subject' => $data['subject'],
            'ip' => $data['ip_address'],
        ]);
    }

    /**
     * Log failed submission
     */
    private function logFailedSubmission(array $data, \Exception $e): void
    {
        Log::error('Contact form failed', [
            'from' => $data['email'] ?? 'unknown',
            'error' => $e->getMessage(),
            'ip' => $data['ip_address'] ?? 'unknown',
        ]);
    }
}
