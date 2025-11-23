<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Portfolio\Entities\Project;
use App\Domain\Portfolio\Repositories\ProjectRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\ProjectModel;
use Illuminate\Support\Collection;

/**
 * Eloquent Implementation: EloquentProjectRepository
 * 
 * Implements the ProjectRepositoryInterface using Eloquent ORM.
 * This is the adapter in the Hexagonal Architecture.
 */
final class EloquentProjectRepository implements ProjectRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function findAll(): Collection
    {
        return ProjectModel::all()
            ->map(fn(ProjectModel $model) => $this->toDomain($model));
    }

    /**
     * {@inheritDoc}
     */
    public function findById(int $id): ?Project
    {
        $model = ProjectModel::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    /**
     * {@inheritDoc}
     */
    public function save(Project $project): Project
    {
        $model = $project->id > 0
            ? ProjectModel::findOrFail($project->id)
            : new ProjectModel();

        $model->fill([
            'title' => $project->title,
            'description' => $project->description,
            'url' => $project->url,
            'github_url' => $project->githubUrl,
            'image_path' => $project->imagePath,
            'tags' => $project->tags,
        ]);

        $model->save();

        return $this->toDomain($model);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(int $id): bool
    {
        $model = ProjectModel::find($id);

        return $model ? $model->delete() : false;
    }

    /**
     * Convert Eloquent model to Domain entity
     * 
     * @param ProjectModel $model
     * @return Project
     */
    private function toDomain(ProjectModel $model): Project
    {
        return new Project(
            id: $model->id,
            title: $model->title,
            description: $model->description,
            url: $model->url,
            githubUrl: $model->github_url,
            imagePath: $model->image_path,
            tags: $model->tags ?? []
        );
    }
}
