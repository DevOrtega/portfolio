<?php

use App\Models\Education;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Education API', function () {
    it('returns all education records ordered by start date descending', function () {
        // Arrange
        Education::create([
            'institution' => 'University A',
            'degree' => 'Master in Computer Science',
            'start_date' => 'Sept. 2020',
            'end_date' => 'Jun. 2022',
            'description' => 'Master studies',
        ]);

        Education::create([
            'institution' => 'University B',
            'degree' => 'Bachelor in Computer Science',
            'start_date' => 'Sept. 2016',
            'end_date' => 'Jun. 2020',
            'description' => 'Bachelor studies',
        ]);

        Education::create([
            'institution' => 'University C',
            'degree' => 'PhD in Computer Science',
            'start_date' => 'Oct. 2022',
            'end_date' => null,
            'description' => 'Current PhD studies',
        ]);

        // Act
        $response = $this->getJson('/api/education');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'institution',
                    'degree',
                    'start_date',
                    'end_date',
                    'description',
                    'created_at',
                    'updated_at',
                ]
            ]);

        $data = $response->json();
        
        // Verify ordering: most recent first (2022 Oct > 2022 Sept > 2020 > 2016)
        expect($data[0]['institution'])->toBe('University C')
            ->and($data[0]['start_date'])->toBe('Oct. 2022')
            ->and($data[1]['institution'])->toBe('University A')
            ->and($data[2]['institution'])->toBe('University B');
    });

    it('returns empty array when no education records exist', function () {
        // Act
        $response = $this->getJson('/api/education');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(0);
    });

    it('handles null end_date for ongoing education', function () {
        // Arrange
        Education::create([
            'institution' => 'Current University',
            'degree' => 'Ongoing Degree',
            'start_date' => 'Sept. 2023',
            'end_date' => null,
            'description' => 'Currently studying',
        ]);

        // Act
        $response = $this->getJson('/api/education');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                [
                    'institution' => 'Current University',
                    'end_date' => null,
                ]
            ]);
    });

    it('handles optional description field', function () {
        // Arrange
        Education::create([
            'institution' => 'University',
            'degree' => 'Degree',
            'start_date' => 'Ene. 2020',
            'end_date' => 'Dic. 2021',
            'description' => null,
        ]);

        // Act
        $response = $this->getJson('/api/education');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                [
                    'institution' => 'University',
                    'description' => null,
                ]
            ]);
    });
});
