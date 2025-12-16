<?php

use App\Application\Portfolio\Services\ProjectService;
use App\Domain\Portfolio\Entities\Project;
use App\Domain\Portfolio\Repositories\ProjectRepositoryInterface;

describe('ProjectService', function () {
    it('retrieves all projects', function () {
        // Arrange
        $mockProjects = collect([
            new Project(
                id: 1,
                title: 'Test Project 1',
                titleEn: null,
                description: 'Description 1',
                descriptionEn: null,
                url: 'https://example.com',
                githubUrl: 'https://github.com/test/project1',
                imagePath: '/images/project1.png',
                tags: ['PHP', 'Laravel']
            ),
            new Project(
                id: 2,
                title: 'Test Project 2',
                titleEn: null,
                description: 'Description 2',
                descriptionEn: null,
                url: null,
                githubUrl: 'https://github.com/test/project2',
                imagePath: null,
                tags: ['Vue.js']
            ),
        ]);

        $repository = Mockery::mock(ProjectRepositoryInterface::class);
        $repository->shouldReceive('findAll')
            ->once()
            ->andReturn($mockProjects);

        $service = new ProjectService($repository);

        // Act
        $result = $service->getAllProjects();

        // Assert
        expect($result)->toHaveCount(2)
            ->and($result->first())->toBeInstanceOf(Project::class)
            ->and($result->first()->title)->toBe('Test Project 1');
    });

    it('retrieves a project by id', function () {
        // Arrange
        $mockProject = new Project(
            id: 1,
            title: 'Test Project',
            titleEn: null,
            description: 'Test Description',
            descriptionEn: null,
            url: 'https://example.com',
            githubUrl: 'https://github.com/test/project',
            imagePath: '/images/project.png',
            tags: ['PHP']
        );

        $repository = Mockery::mock(ProjectRepositoryInterface::class);
        $repository->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($mockProject);

        $service = new ProjectService($repository);

        // Act
        $result = $service->getProjectById(1);

        // Assert
        expect($result)->toBeInstanceOf(Project::class)
            ->and($result->title)->toBe('Test Project');
    });

    it('returns null when project not found', function () {
        // Arrange
        $repository = Mockery::mock(ProjectRepositoryInterface::class);
        $repository->shouldReceive('findById')
            ->with(999)
            ->once()
            ->andReturn(null);

        $service = new ProjectService($repository);

        // Act
        $result = $service->getProjectById(999);

        // Assert
        expect($result)->toBeNull();
    });
});
