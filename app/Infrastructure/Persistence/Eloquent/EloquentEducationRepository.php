<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Portfolio\Entities\Education;
use App\Domain\Portfolio\Repositories\EducationRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Traits\SpanishDateOrdering;
use App\Models\Education as EducationModel;
use Illuminate\Support\Collection;

/**
 * Eloquent implementation of EducationRepositoryInterface
 * 
 * Uses SpanishDateOrdering trait for consistent date ordering.
 */
final class EloquentEducationRepository implements EducationRepositoryInterface
{
    use SpanishDateOrdering;

    public function __construct(
        private readonly EducationModel $model
    ) {
    }

    public function findAll(): Collection
    {
        $query = $this->model->newQuery();
        $this->applyDateOrdering($query);
        
        return $query->get()
            ->map(fn (EducationModel $model) => $this->toDomainEntity($model));
    }

    public function findByYear(int $year): Collection
    {
        $query = $this->model->newQuery();
        $this->applyYearFilter($query, $year);
        $this->applyDateOrdering($query);
        
        return $query->get()
            ->map(fn (EducationModel $model) => $this->toDomainEntity($model));
    }

    public function findById(int $id): ?Education
    {
        $model = $this->model->find($id);
        
        return $model ? $this->toDomainEntity($model) : null;
    }

    private function toDomainEntity(EducationModel $model): Education
    {
        return new Education(
            id: $model->id,
            institution: $model->institution,
            degree: $model->degree,
            degreeEn: $model->degree_en,
            startDate: $model->start_date,
            endDate: $model->end_date,
            description: $model->description,
            descriptionEn: $model->description_en,
            createdAt: $model->created_at?->toISOString(),
            updatedAt: $model->updated_at?->toISOString()
        );
    }
}
