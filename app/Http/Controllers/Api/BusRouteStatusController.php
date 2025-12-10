<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class BusRouteStatusController extends Controller
{
    /**
     * Get OSRM and route status metrics
     */
    public function index(): JsonResponse
    {
        $totalRoutes = DB::table('bus_lines')->count();
        
        // Count routes with OSRM data
        $routesWithOsrm = DB::table('bus_lines')
            ->where(function ($query) {
                $query->whereNotNull('osrm_route_outbound')
                      ->orWhereNotNull('osrm_route_inbound');
            })
            ->count();
        
        // Get recent failure and success counts
        $recentFailures = Cache::get('osrm_failures_' . date('Y-m-d-H'), 0);
        $recentSuccesses = Cache::get('osrm_success_count_' . date('Y-m-d-H'), 0);
        $lastSuccess = Cache::get('osrm_last_success');
        
        // Calculate success rate
        $totalAttempts = $recentFailures + $recentSuccesses;
        $successRate = $totalAttempts > 0 ? round(($recentSuccesses / $totalAttempts) * 100, 2) : 0;
        
        // Get route quality distribution
        $routeQuality = $this->getRouteQualityDistribution();
        
        // Get system health
        $systemHealth = $this->getSystemHealth($successRate, $recentFailures);
        
        return response()->json([
            'timestamp' => Carbon::now()->toISOString(),
            'summary' => [
                'total_routes' => $totalRoutes,
                'routes_with_osrm' => $routesWithOsrm,
                'osrm_coverage' => $totalRoutes > 0 ? round(($routesWithOsrm / $totalRoutes) * 100, 2) : 0,
                'success_rate' => $successRate,
                'last_success' => $lastSuccess,
                'recent_failures' => $recentFailures,
                'recent_successes' => $recentSuccesses,
                'system_health' => $systemHealth
            ],
            'route_quality' => $routeQuality,
            'alerts' => $this->getActiveAlerts($recentFailures, $successRate)
        ]);
    }
    
    /**
     * Get route quality distribution
     */
    private function getRouteQualityDistribution(): array
    {
        // This would require additional database columns to track route quality metrics
        // For now, return basic distribution
        return [
            'excellent' => 0,  // >95% OSRM success
            'good' => 0,         // 85-95% OSRM success
            'fair' => 0,         // 70-85% OSRM success
            'poor' => 0,         // <70% OSRM success
            'unknown' => 100      // Routes without quality data
        ];
    }
    
    /**
     * Get system health status
     */
    private function getSystemHealth(float $successRate, int $recentFailures): string
    {
        if ($recentFailures > 20) {
            return 'critical';
        } elseif ($recentFailures > 10 || $successRate < 80) {
            return 'warning';
        } elseif ($successRate > 95) {
            return 'excellent';
        } else {
            return 'good';
        }
    }
    
    /**
     * Get active alerts
     */
    private function getActiveAlerts(int $recentFailures, float $successRate): array
    {
        $alerts = [];
        
        if ($recentFailures > 15) {
            $alerts[] = [
                'type' => 'critical',
                'message' => 'Critical OSRM failure rate detected',
                'threshold' => 15,
                'current' => $recentFailures
            ];
        } elseif ($recentFailures > 10) {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'High OSRM failure rate detected',
                'threshold' => 10,
                'current' => $recentFailures
            ];
        }
        
        if ($successRate < 80 && $recentFailures > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'Low OSRM success rate',
                'threshold' => 80,
                'current' => round($successRate, 2)
            ];
        }
        
        return $alerts;
    }
}
