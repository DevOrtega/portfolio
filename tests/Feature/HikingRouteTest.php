<?php

use App\Domain\Hiking\RouteProviderInterface;
use App\Domain\Hiking\ElevationProviderInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('hiking route endpoint returns enriched route with steps and waypoints', function () {
    // 1. Mock RouteProvider
    $this->mock(RouteProviderInterface::class, function ($mock) {
        $mock->shouldReceive('getRoutesWithOptions')
            ->once()
            ->withArgs(function ($coords, $profile, $options) {
                // Verify waypoints are passed
                return count($coords) === 3 // Start + 1 Waypoint + End
                    && $profile === 'foot'
                    && isset($options['steps']) && $options['steps'] === 'true';
            })
            ->andReturn([
                [
                    'geometry' => [
                        'coordinates' => [
                            [-15.6, 27.96],
                            [-15.55, 28.0],
                            [-15.5, 28.05]
                        ]
                    ],
                    'duration' => 3600,
                    'legs' => [
                        ['steps' => [['maneuver' => ['type' => 'turn'], 'name' => 'Path A']]],
                        ['steps' => [['maneuver' => ['type' => 'arrive'], 'name' => 'Peak']]]
                    ]
                ]
            ]);
    });

    // 2. Mock ElevationProvider
    $this->mock(ElevationProviderInterface::class, function ($mock) {
        $mock->shouldReceive('addElevation')
            ->once()
            ->andReturn([
                [27.96, -15.6, 1000],
                [28.0, -15.55, 1200],
                [28.05, -15.5, 1100]
            ]);
    });

    // 3. Make Request
    $response = $this->getJson('/api/hiking/route?start=27.96,-15.6&end=28.05,-15.5&waypoints[]=28.0,-15.55');

    // 4. Assertions
    $response->assertStatus(200);
    
    $response->assertJsonStructure([
        'type',
        'features' => [
            '*' => [
                'type',
                'properties' => [
                    'distance_km',
                    'elevation_gain_m',
                    'difficulty',
                    'legs' // Check legs are present
                ],
                'geometry' => [
                    'coordinates'
                ]
            ]
        ]
    ]);

    // Verify Elevation Logic (Gain 200m)
    $props = $response->json('features.0.properties');
    expect($props['elevation_gain_m'])->toBe(200);
    
    // Verify Steps present
    expect($props['legs'])->toHaveCount(2);
    expect($props['legs'][0]['steps'][0]['name'])->toBe('Path A');
});

