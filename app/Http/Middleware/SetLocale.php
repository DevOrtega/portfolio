<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Accept-Language', 'es');
        
        // Extract only the language code (e.g., 'en' from 'en-US')
        $locale = substr($locale, 0, 2);
        
        // Validate locale
        if (!in_array($locale, ['es', 'en'])) {
            $locale = 'es';
        }
        
        app()->setLocale($locale);
        
        return $next($request);
    }
}
