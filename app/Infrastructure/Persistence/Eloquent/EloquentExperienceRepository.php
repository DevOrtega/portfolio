<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Portfolio\Entities\Experience;
use App\Domain\Portfolio\Repositories\ExperienceRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Traits\SpanishDateOrdering;
use App\Models\Experience as ExperienceModel;
use Illuminate\Support\Collection;

/**
 * Eloquent implementation of ExperienceRepositoryInterface
 * 
 * Uses SpanishDateOrdering trait for consistent date ordering.
 */
final class EloquentExperienceRepository implements ExperienceRepositoryInterface
{
    use SpanishDateOrdering;

    public function __construct(
        private readonly ExperienceModel $model
    ) {
    }

    public function findAll(): Collection
    {
        $query = $this->model->newQuery();
        $this->applyDateOrdering($query);
        
        return $query->get()
            ->map(fn (ExperienceModel $model) => $this->toDomainEntity($model));
    }

    public function findByYear(int $year): Collection
    {
        $query = $this->model->newQuery();
        $this->applyYearFilter($query, $year);
        $this->applyDateOrdering($query);
        
        return $query->get()
            ->map(fn (ExperienceModel $model) => $this->toDomainEntity($model));
    }

    public function findById(int $id): ?Experience
    {
        $model = $this->model->find($id);
        
        return $model ? $this->toDomainEntity($model) : null;
    }

    private function toDomainEntity(ExperienceModel $model): Experience
    {
        return new Experience(
            id: $model->id,
            company: $model->company,
            role: $model->role,
            roleEn: $model->role_en,
            startDate: $model->start_date,
            endDate: $model->end_date,
            description: $model->description,
            descriptionEn: $model->description_en,
            createdAt: $model->created_at?->toISOString(),
            updatedAt: $model->updated_at?->toISOString()
        );
    }
}
