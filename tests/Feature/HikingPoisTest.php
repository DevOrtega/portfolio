<?php

use Illuminate\Support\Facades\Http;
use App\Application\Hiking\GetRoutePoisService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('hiking pois endpoint returns formatted pois with new categories', function () {
    Http::fake([
        'overpass-api.de/*' => Http::response([
            'elements' => [
                ['type' => 'node', 'id' => 1, 'lat' => 28.0, 'lon' => -15.5, 'tags' => ['amenity' => 'pharmacy', 'name' => 'Farmacia A']],
                ['type' => 'node', 'id' => 2, 'lat' => 28.1, 'lon' => -15.6, 'tags' => ['tourism' => 'museum', 'name' => 'Museo B']],
                ['type' => 'node', 'id' => 3, 'lat' => 28.2, 'lon' => -15.7, 'tags' => ['tourism' => 'camp_site', 'name' => 'Camping C']],
                ['type' => 'node', 'id' => 4, 'lat' => 28.3, 'lon' => -15.8, 'tags' => ['tourism' => 'hotel', 'stars' => '4', 'name' => 'Hotel D']]
            ]
        ], 200)
    ]);

    $response = $this->postJson('/api/hiking/pois', [
        'route' => [[-15.5, 28.0], [-15.6, 28.1]],
        'radius' => 500
    ]);

    $response->assertStatus(200);
    $response->assertJsonFragment(['category' => 'health', 'name' => 'Farmacia A']);
    $response->assertJsonFragment(['category' => 'culture', 'name' => 'Museo B']);
    $response->assertJsonFragment(['category' => 'camping', 'name' => 'Camping C']);
    $response->assertJsonFragment(['category' => 'accommodation', 'name' => 'Hotel D']);
});

test('hiking pois service sorts by relevance and limits per category', function () {
    // Generate 20 restaurants, some with wikidata (more relevant)
    $elements = [];
    for ($i = 1; $i <= 20; $i++) {
        $tags = ['amenity' => 'restaurant', 'name' => "Restaurante $i"];
        if ($i > 15) {
            $tags['wikidata'] = "Q$i"; // Higher relevance
        }
        $elements[] = [
            'type' => 'node',
            'id' => $i,
            'lat' => 28.0,
            'lon' => -15.5,
            'tags' => $tags
        ];
    }

    Http::fake(['overpass-api.de/*' => Http::response(['elements' => $elements], 200)]);

    $response = $this->postJson('/api/hiking/pois', [
        'route' => [[-15.5, 28.0], [-15.6, 28.1]]
    ]);

    $response->assertStatus(200);
    
    // Should be limited to 15 per category
    $response->assertJsonCount(15);
    
    // The 5 restaurants with wikidata (16-20) MUST be present because they are more relevant
    $data = $response->json();
    $names = collect($data)->pluck('name')->toArray();
    
    expect($names)->toContain('Restaurante 16');
    expect($names)->toContain('Restaurante 20');
});

test('hiking pois endpoint handles overpass failure gracefully', function () {
    Http::fake([
        'overpass-api.de/*' => Http::response('Error', 500)
    ]);

    $response = $this->postJson('/api/hiking/pois', [
        'route' => [[-15.5, 28.0], [-15.6, 28.1]]
    ]);

    // Controller catches exception and returns 500 error json
    $response->assertStatus(500);
    $response->assertJsonStructure(['error']);
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
