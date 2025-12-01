<?php

namespace App\Application\Auth\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Application Service: AdminAuthService
 * 
 * Handles authentication business logic for admin users.
 */
final class AdminAuthService
{
    /**
     * Maximum login attempts before throttling.
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Decay time in seconds (1 hour = 3600 seconds).
     */
    private const DECAY_SECONDS = 3600;

    /**
     * Token expiration in minutes.
     */
    private const TOKEN_EXPIRATION_MINUTES = 60;

    /**
     * Attempt to authenticate an admin user.
     *
     * @param string $email
     * @param string $password
     * @param string $ipAddress
     * @return array{user: User, token: string, expires_at: string}
     * @throws ValidationException
     */
    public function attemptLogin(string $email, string $password, string $ipAddress): array
    {
        $throttleKey = $this->getThrottleKey($email, $ipAddress);

        // Check if too many attempts
        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            throw ValidationException::withMessages([
                'email' => [
                    __('auth.throttle', [
                        'seconds' => $seconds,
                        'minutes' => ceil($seconds / 60),
                    ]),
                ],
            ])->status(429);
        }

        $user = User::where('email', $email)->first();

        // Validate credentials
        if (!$user || !Hash::check($password, $user->password)) {
            RateLimiter::hit($throttleKey, self::DECAY_SECONDS);

            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        // Check if user is admin
        if (!$user->isAdmin()) {
            RateLimiter::hit($throttleKey, self::DECAY_SECONDS);

            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        // Clear rate limiter on successful login
        RateLimiter::clear($throttleKey);

        // Update last login info
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress,
        ]);

        // Revoke previous tokens for security
        $user->tokens()->delete();

        // Create new token with expiration
        $expiresAt = now()->addMinutes(self::TOKEN_EXPIRATION_MINUTES);
        $token = $user->createToken(
            'admin-token',
            ['admin'],
            $expiresAt
        )->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'expires_at' => $expiresAt->toIso8601String(),
        ];
    }

    /**
     * Logout the admin user.
     *
     * @param User $user
     * @return void
     */
    public function logout(User $user): void
    {
        // Revoke all tokens
        $user->tokens()->delete();
    }

    /**
     * Get current authenticated admin user info.
     *
     * @param User $user
     * @return array{user: User}
     */
    public function getCurrentUser(User $user): array
    {
        return [
            'user' => $user,
        ];
    }

    /**
     * Get throttle key for rate limiting.
     *
     * @param string $email
     * @param string $ipAddress
     * @return string
     */
    private function getThrottleKey(string $email, string $ipAddress): string
    {
        return 'admin-login:' . strtolower($email) . '|' . $ipAddress;
    }

    /**
     * Get remaining attempts for the given credentials.
     *
     * @param string $email
     * @param string $ipAddress
     * @return int
     */
    public function getRemainingAttempts(string $email, string $ipAddress): int
    {
        $throttleKey = $this->getThrottleKey($email, $ipAddress);
        
        return RateLimiter::remaining($throttleKey, self::MAX_ATTEMPTS);
    }
}
