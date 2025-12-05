<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Portfolio\Entities\Education;
use App\Domain\Portfolio\Repositories\EducationRepositoryInterface;
use App\Models\Education as EducationModel;
use Illuminate\Support\Collection;

/**
 * Eloquent implementation of EducationRepositoryInterface
 */
final class EloquentEducationRepository implements EducationRepositoryInterface
{
    public function __construct(
        private readonly EducationModel $model
    ) {
    }

    public function findAll(): Collection
    {
        return $this->model
            ->orderByRaw("CAST(SUBSTR(start_date, -4) AS INTEGER) DESC")
            ->orderByRaw("CASE 
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Ene' THEN 1
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Feb' THEN 2
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Mar' THEN 3
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Abr' THEN 4
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'May' THEN 5
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Jun' THEN 6
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Jul' THEN 7
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Ago' THEN 8
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Sept' THEN 9
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Oct' THEN 10
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Nov' THEN 11
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Dic' THEN 12
                ELSE 0 END DESC")
            ->get()
            ->map(fn (EducationModel $model) => $this->toDomainEntity($model));
    }

    public function findByYear(int $year): Collection
    {
        return $this->model
            ->where(function ($q) use ($year) {
                $q->whereRaw("CAST(SUBSTR(start_date, -4) AS INTEGER) <= ?", [$year])
                    ->where(function ($subQ) use ($year) {
                        $subQ->whereNull('end_date')
                            ->orWhereRaw("CAST(SUBSTR(end_date, -4) AS INTEGER) >= ?", [$year]);
                    });
            })
            ->orderByRaw("CAST(SUBSTR(start_date, -4) AS INTEGER) DESC")
            ->orderByRaw("CASE 
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Ene' THEN 1
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Feb' THEN 2
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Mar' THEN 3
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Abr' THEN 4
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'May' THEN 5
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Jun' THEN 6
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Jul' THEN 7
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Ago' THEN 8
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Sept' THEN 9
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Oct' THEN 10
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Nov' THEN 11
                WHEN SUBSTR(start_date, 1, INSTR(start_date, '.') - 1) = 'Dic' THEN 12
                ELSE 0 END DESC")
            ->get()
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
