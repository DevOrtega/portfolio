<?php

use App\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Skills API', function () {
    it('returns all skills ordered by category and name', function () {
        // Arrange
        Skill::create([
            'name' => 'Vue.js',
            'category' => 'Frontend',
            'proficiency' => 90,
        ]);

        Skill::create([
            'name' => 'Laravel',
            'category' => 'Backend',
            'proficiency' => 85,
        ]);

        Skill::create([
            'name' => 'React',
            'category' => 'Frontend',
            'proficiency' => 75,
        ]);

        // Act
        $response = $this->getJson('/api/skills');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'category',
                    'proficiency',
                    'created_at',
                    'updated_at',
                ]
            ]);

        $data = $response->json();
        
        // Verify ordering: Backend first, then Frontend, and within Frontend: React, Vue.js
        expect($data[0]['category'])->toBe('Backend')
            ->and($data[0]['name'])->toBe('Laravel')
            ->and($data[1]['category'])->toBe('Frontend')
            ->and($data[1]['name'])->toBe('React')
            ->and($data[2]['category'])->toBe('Frontend')
            ->and($data[2]['name'])->toBe('Vue.js');
    });

    it('returns empty array when no skills exist', function () {
        // Act
        $response = $this->getJson('/api/skills');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(0);
    });

    it('returns skills with correct proficiency values', function () {
        // Arrange
        Skill::create([
            'name' => 'PHP',
            'category' => 'Backend',
            'proficiency' => 95,
        ]);

        // Act
        $response = $this->getJson('/api/skills');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                [
                    'name' => 'PHP',
                    'proficiency' => 95,
                ]
            ]);
    });
});
