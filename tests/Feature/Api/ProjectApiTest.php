<?php

use App\Infrastructure\Persistence\Eloquent\Models\ProjectModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Projects API', function () {
    it('returns all projects', function () {
        // Arrange
        ProjectModel::factory()->count(3)->create();

        // Act
        $response = $this->getJson('/api/projects');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'title',
                    'description',
                    'url',
                    'github_url',
                    'image_path',
                    'tags',
                ]
            ]);
    });

    it('returns empty array when no projects exist', function () {
        // Act
        $response = $this->getJson('/api/projects');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(0);
    });

    it('returns projects with correct data structure', function () {
        // Arrange
        $project = ProjectModel::create([
            'title' => 'Test Project',
            'description' => 'Test Description',
            'url' => 'https://example.com',
            'github_url' => 'https://github.com/test/project',
            'image_path' => '/images/test.png',
            'tags' => ['PHP', 'Laravel', 'Vue.js'],
        ]);

        // Act
        $response = $this->getJson('/api/projects');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                [
                    'id' => $project->id,
                    'title' => 'Test Project',
                    'description' => 'Test Description',
                    'url' => 'https://example.com',
                    'github_url' => 'https://github.com/test/project',
                    'image_path' => '/images/test.png',
                    'tags' => ['PHP', 'Laravel', 'Vue.js'],
                ]
            ]);
    });
});

