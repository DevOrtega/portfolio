<?php

use App\Models\Skill;
use App\Models\Experience;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Skills API', function () {
    it('returns all skills ordered by proficiency, category and name', function () {
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
                    'is_personal',
                    'created_at',
                    'updated_at',
                ]
            ]);

        $data = $response->json();
        
        // Verify ordering: by proficiency desc first
        expect($data[0]['proficiency'])->toBe(90)
            ->and($data[0]['name'])->toBe('Vue.js')
            ->and($data[1]['proficiency'])->toBe(85)
            ->and($data[1]['name'])->toBe('Laravel')
            ->and($data[2]['proficiency'])->toBe(75)
            ->and($data[2]['name'])->toBe('React');
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

    it('returns personal skills with is_personal flag', function () {
        // Arrange
        Skill::create([
            'name' => 'Home Assistant',
            'category' => 'IoT',
            'proficiency' => 75,
            'is_personal' => true,
        ]);

        // Act
        $response = $this->getJson('/api/skills');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                [
                    'name' => 'Home Assistant',
                    'is_personal' => true,
                ]
            ]);
    });

    it('always shows personal skills when filtering by year', function () {
        // Arrange
        $experience = Experience::create([
            'company' => 'Test Company',
            'role' => 'Developer',
            'start_date' => 'Ene. 2023',
            'end_date' => 'Dic. 2023',
            'description' => 'Test description',
        ]);

        $workSkill = Skill::create([
            'name' => 'Laravel',
            'category' => 'Backend',
            'proficiency' => 90,
            'is_personal' => false,
        ]);

        $personalSkill = Skill::create([
            'name' => 'Home Assistant',
            'category' => 'IoT',
            'proficiency' => 75,
            'is_personal' => true,
        ]);

        $experience->skills()->attach($workSkill->id);

        // Act
        $response = $this->getJson('/api/skills?year=2023');

        // Assert
        $response->assertStatus(200);
        $data = $response->json();
        
        $skillNames = array_column($data, 'name');
        expect($skillNames)->toContain('Laravel')
            ->and($skillNames)->toContain('Home Assistant');
    });

    it('filters skills by year based on experience relationship', function () {
        // Arrange
        $experience2023 = Experience::create([
            'company' => 'Company 2023',
            'role' => 'Developer',
            'start_date' => 'Ene. 2023',
            'end_date' => 'Dic. 2023',
            'description' => 'Test',
        ]);

        $experience2020 = Experience::create([
            'company' => 'Company 2020',
            'role' => 'Junior',
            'start_date' => 'Ene. 2020',
            'end_date' => 'Dic. 2020',
            'description' => 'Test',
        ]);

        $skill2023 = Skill::create([
            'name' => 'Vue.js',
            'category' => 'Frontend',
            'proficiency' => 90,
        ]);

        $skill2020 = Skill::create([
            'name' => 'jQuery',
            'category' => 'Frontend',
            'proficiency' => 70,
        ]);

        $experience2023->skills()->attach($skill2023->id);
        $experience2020->skills()->attach($skill2020->id);

        // Act - Filter by 2023
        $response = $this->getJson('/api/skills?year=2023');

        // Assert
        $response->assertStatus(200);
        $data = $response->json();
        
        $skillNames = array_column($data, 'name');
        expect($skillNames)->toContain('Vue.js')
            ->and($skillNames)->not->toContain('jQuery');
    });
});
