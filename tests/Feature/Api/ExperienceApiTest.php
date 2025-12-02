<?php

use App\Models\Experience;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Experience API', function () {
    it('returns all experiences ordered by start date descending', function () {
        // Arrange
        Experience::create([
            'company' => 'Company A',
            'role' => 'Senior Developer',
            'start_date' => 'Ene. 2023',
            'end_date' => null,
            'description' => 'Current position',
        ]);

        Experience::create([
            'company' => 'Company B',
            'role' => 'Junior Developer',
            'start_date' => 'Jun. 2020',
            'end_date' => 'Dic. 2022',
            'description' => 'First job',
        ]);

        Experience::create([
            'company' => 'Company C',
            'role' => 'Mid Developer',
            'start_date' => 'Mar. 2021',
            'end_date' => 'Nov. 2022',
            'description' => 'Second job',
        ]);

        // Act
        $response = $this->getJson('/api/experiences');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'company',
                    'role',
                    'start_date',
                    'end_date',
                    'description',
                    'created_at',
                    'updated_at',
                ]
            ]);

        $data = $response->json();
        
        // Verify ordering: most recent first (2023 > 2021 > 2020)
        expect($data[0]['company'])->toBe('Company A')
            ->and($data[0]['start_date'])->toBe('Ene. 2023')
            ->and($data[1]['company'])->toBe('Company C')
            ->and($data[2]['company'])->toBe('Company B');
    });

    it('returns empty array when no experiences exist', function () {
        // Act
        $response = $this->getJson('/api/experiences');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(0);
    });

    it('handles null end_date for current positions', function () {
        // Arrange
        Experience::create([
            'company' => 'Current Company',
            'role' => 'Developer',
            'start_date' => 'Ene. 2023',
            'end_date' => null,
            'description' => 'Current role',
        ]);

        // Act
        $response = $this->getJson('/api/experiences');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                [
                    'company' => 'Current Company',
                    'end_date' => null,
                ]
            ]);
    });
});
