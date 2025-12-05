<?php

declare(strict_types=1);

use App\Infrastructure\Persistence\Eloquent\Models\BusCompanyModel;
use App\Infrastructure\Persistence\Eloquent\Models\BusLineModel;
use App\Infrastructure\Persistence\Eloquent\Models\BusStopModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Bus API', function () {

    describe('GET /api/bus/data', function () {

        it('returns bus data structure', function () {
            $response = $this->getJson('/api/bus/data');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'companies',
                    'bus_lines',
                    'main_lines',
                    'routes',
                    'stops',
                    'map_config',
                    'simulation_config',
                ]);
        });

        it('returns map config with correct structure', function () {
            $response = $this->getJson('/api/bus/data');

            $response->assertStatus(200);
            $data = $response->json();
            
            expect($data['map_config'])->toHaveKey('center');
            expect($data['map_config'])->toHaveKey('bounds');
            expect($data['map_config'])->toHaveKey('min_zoom');
        });

        it('returns simulation config with correct structure', function () {
            $response = $this->getJson('/api/bus/data');

            $response->assertStatus(200);
            $data = $response->json();
            
            expect($data['simulation_config'])->toHaveKey('update_interval');
            expect($data['simulation_config'])->toHaveKey('buses_per_route');
            expect($data['simulation_config'])->toHaveKey('speed');
        });

        it('returns companies when data exists', function () {
            BusCompanyModel::create([
                'code' => 'municipales',
                'name' => 'Guaguas Municipales',
                'primary_color' => '#FFD200',
                'secondary_color' => '#000000',
                'text_color' => '#000000',
            ]);

            $response = $this->getJson('/api/bus/data');

            $response->assertStatus(200);
            $data = $response->json();
            
            expect($data['companies'])->toHaveKey('municipales');
        });

    });

    describe('GET /api/bus/routes', function () {

        it('returns routes array', function () {
            $response = $this->getJson('/api/bus/routes');

            $response->assertStatus(200)
                ->assertJsonStructure(['routes']);
        });

        it('returns empty routes when no data', function () {
            $response = $this->getJson('/api/bus/routes');

            $response->assertStatus(200);
            $data = $response->json();
            
            expect($data['routes'])->toBeArray();
        });

    });

    describe('GET /api/bus/stops', function () {

        it('returns stops structure', function () {
            $response = $this->getJson('/api/bus/stops');

            $response->assertStatus(200)
                ->assertJsonStructure(['stops']);
        });

        it('returns stops with coordinates when data exists', function () {
            BusStopModel::create([
                'name' => 'Test Stop',
                'code' => 'TST001',
                'lat_outbound' => 28.123456,
                'lng_outbound' => -15.432100,
                'lat_inbound' => 28.123457,
                'lng_inbound' => -15.432101,
            ]);

            $response = $this->getJson('/api/bus/stops');

            $response->assertStatus(200);
            $data = $response->json();
            
            expect($data['stops'])->toBeArray();
            expect($data['stops'])->toHaveKey('TST001');
            expect($data['stops']['TST001'])->toHaveKey('outbound');
            expect($data['stops']['TST001'])->toHaveKey('inbound');
        });

    });

    describe('GET /api/bus/companies', function () {

        it('returns companies structure', function () {
            $response = $this->getJson('/api/bus/companies');

            $response->assertStatus(200)
                ->assertJsonStructure(['companies']);
        });

        it('returns company colors when data exists', function () {
            BusCompanyModel::create([
                'code' => 'municipales',
                'name' => 'Guaguas Municipales',
                'primary_color' => '#FFD200',
                'secondary_color' => '#000000',
                'text_color' => '#000000',
            ]);

            BusCompanyModel::create([
                'code' => 'global',
                'name' => 'Global',
                'primary_color' => '#00AA00',
                'secondary_color' => '#FFFFFF',
                'text_color' => '#FFFFFF',
            ]);

            $response = $this->getJson('/api/bus/companies');

            $response->assertStatus(200);
            $data = $response->json();
            
            expect($data['companies'])->toHaveKey('municipales');
            expect($data['companies'])->toHaveKey('global');
        });

    });

});
