<?php

use App\Models\PersonalInfo;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Personal Info API', function () {
    it('returns personal information', function () {
        // Arrange
        PersonalInfo::create([
            'name' => 'Carlos Ortega',
            'email' => 'carlos@example.com',
            'phone' => '+34 123 456 789',
            'location' => 'Gran Canaria, España',
            'headline' => 'Full Stack Developer',
            'bio' => 'Passionate developer with experience in web technologies.',
            'github_url' => 'https://github.com/devortega',
            'linkedin_url' => 'https://linkedin.com/in/devortega',
            'twitter_url' => 'https://twitter.com/devortega',
        ]);

        // Act
        $response = $this->getJson('/api/personal-info');

                // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'headline',
                'bio',
                'linkedin_url',
                'github_url',
                'cv_path',
            ]);
    });

    it('returns 404 when no personal info exists', function () {
        // Act
        $response = $this->getJson('/api/personal-info');

        // Assert
        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Personal information not found'
            ]);
    });

    it('returns only the first personal info record', function () {
        // Arrange
        PersonalInfo::create([
            'name' => 'First Person',
            'email' => 'first@example.com',
            'headline' => 'Developer',
            'bio' => 'First bio',
        ]);

        PersonalInfo::create([
            'name' => 'Second Person',
            'email' => 'second@example.com',
            'headline' => 'Designer',
            'bio' => 'Second bio',
        ]);

        // Act
        $response = $this->getJson('/api/personal-info');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'name' => 'First Person',
            ]);
    });
});
