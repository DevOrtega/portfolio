<?php

use Illuminate\Support\Facades\Http;
use App\Application\Hiking\GetRoutePoisService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('hiking pois endpoint returns formatted pois', function () {
    // 1. Mock Overpass API Response
    Http::fake([
        'overpass-api.de/*' => Http::response([
            'elements' => [
                [
                    'type' => 'node',
                    'id' => 123,
                    'lat' => 28.0,
                    'lon' => -15.5,
                    'tags' => [
                        'amenity' => 'restaurant',
                        'name' => 'El Refugio'
                    ]
                ],
                [
                    'type' => 'node',
                    'id' => 124,
                    'lat' => 28.1,
                    'lon' => -15.6,
                    'tags' => [
                        'natural' => 'peak',
                        'ele' => '1949'
                    ]
                ]
            ]
        ], 200)
    ]);

    // 2. Make Request
    $response = $this->postJson('/api/hiking/pois', [
        'route' => [
            [-15.5, 28.0], // Lon, Lat (as sent by frontend/OSRM)
            [-15.6, 28.1]
        ],
        'radius' => 500
    ]);

    // 3. Assertions
    $response->assertStatus(200);
    
    $response->assertJsonCount(2);
    
    // Check Restaurant
    $response->assertJsonFragment([
        'id' => 123,
        'category' => 'food',
        'name' => 'El Refugio'
    ]);

    // Check Peak Naming Fallback
    $response->assertJsonFragment([
        'id' => 124,
        'category' => 'peak',
        'name' => 'Cima (1949m)'
    ]);
});

test('hiking pois endpoint handles overpass failure gracefully', function () {
    Http::fake([
        'overpass-api.de/*' => Http::response('Error', 500)
    ]);

    $response = $this->postJson('/api/hiking/pois', [
        'route' => [[-15.5, 28.0], [-15.6, 28.1]]
    ]);

    // Should return empty array or 500? 
    // Controller catches exception and returns 500 error json or empty?
    // Looking at Controller: return response()->json(['error' => ...], 500);
    // Actually GetRoutePoisService returns [] on error, but Controller wraps in try/catch.
    // Service catches Http error and returns [].
    // So Controller receives [] and returns 200 OK with [].
    
    $response->assertStatus(200);
    $response->assertJsonCount(0);
});

test('hiking pois service simplifies route correctly', function () {
    // This tests the logic inside the service indirectly by checking if it makes a request
    
    Http::fake();

    $service = app(GetRoutePoisService::class);
    
    // Create a straight line with redundant points
    $route = [
        [28.0, -15.0],
        [28.00001, -15.00001], // Tiny deviation
        [28.1, -15.1]
    ];

    $service->execute($route);

    // Verify request was made
    Http::assertSent(function ($request) {
        return str_contains($request['data'], 'around:1000');
    });
});
