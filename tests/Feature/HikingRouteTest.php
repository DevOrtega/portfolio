<?php

use App\Infrastructure\Services\OsrmService;
use App\Infrastructure\Services\ElevationService;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('hiking route endpoint returns 3d coordinates', function () {
    // Mock OSRM response
    Http::fake([
        '*router.project-osrm.org*' => Http::response([
            'code' => 'Ok',
            'routes' => [[
                'geometry' => [
                    'coordinates' => [
                        [-15.6, 27.96], // OSRM returns [lon, lat]
                        [-15.5, 28.0],
                        [-15.4, 28.1]
                    ]
                ]
            ]]
        ], 200)
    ]);

    // We can't easily mock the ElevationService process call in a simple integration test 
    // without mocking the Service class itself, because Process::fake is for Facades, 
    // but the service might be using the class directly or we want to test the python integration?
    // Actually, testing the python integration in a CI/CD might fail if deps aren't there.
    // But here I have installed them.
    // Let's try to mock ElevationService to isolate the test from the Python script execution 
    // for reliability, OR use Process::fake if I used the Facade.
    
    // In ElevationService I used Process::run. So I can use Process::fake.
    
    /*
    Process::fake([
        '*add_elevation.py*' => Process::result(
            json_encode([
                [-15.6, 27.96, 1000],
                [-15.5, 28.0, 500],
                [-15.4, 28.1, 10]
            ])
        )
    ]);
    */
    
    // However, I want to verify the Python script actually works in this environment.
    // So I won't mock the Process if I want an integration test.
    // But for a unit test of the Controller/Service flow, mocking is better.
    // Let's create a test that hits the real Python script but mocks OSRM.
    
    $this->withoutExceptionHandling();

    $response = $this->getJson('/api/hiking/route?start=27.9706,-15.6128&end=28.1235,-15.4363');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'type',
        'properties' => [
            'distance_km',
            'elevation_gain_m',
            'elevation_loss_m'
        ],
        'geometry' => [
            'type',
            'coordinates'
        ]
    ]);
    
    // Check that we have Z coordinates (3 elements per point)
    $coords = $response->json('geometry.coordinates');
    expect($coords[0])->toHaveCount(3);
    // Check that elevation is not 0 for the mountain point (approx)
    // Roque Nublo should be > 1000m
    expect($coords[0][2])->toBeGreaterThan(1000);
    
});
