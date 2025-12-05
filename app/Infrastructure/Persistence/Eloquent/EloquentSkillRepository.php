<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Portfolio\Entities\Skill;
use App\Domain\Portfolio\Repositories\SkillRepositoryInterface;
use App\Models\Education as EducationModel;
use App\Models\Experience as ExperienceModel;
use App\Models\Skill as SkillModel;
use Illuminate\Support\Collection;

/**
 * Eloquent implementation of SkillRepositoryInterface
 */
final class EloquentSkillRepository implements SkillRepositoryInterface
{
    public function __construct(
        private readonly SkillModel $model
    ) {
    }

    public function findAll(): Collection
    {
        return $this->model
            ->orderBy('proficiency', 'desc')
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->map(fn (SkillModel $model) => $this->toDomainEntity($model));
    }

    public function findByYear(int $year): Collection
    {
        // Get experiences active during the specified year
        $relevantExperienceIds = ExperienceModel::where(function ($q) use ($year) {
            $q->whereRaw("CAST(SUBSTR(start_date, -4) AS INTEGER) <= ?", [$year])
                ->where(function ($subQ) use ($year) {
                    $subQ->whereNull('end_date')
                        ->orWhereRaw("CAST(SUBSTR(end_date, -4) AS INTEGER) >= ?", [$year]);
                });
        })->pluck('id');

        // Get educations active during the specified year
        $relevantEducationIds = EducationModel::where(function ($q) use ($year) {
            $q->whereRaw("CAST(SUBSTR(start_date, -4) AS INTEGER) <= ?", [$year])
                ->where(function ($subQ) use ($year) {
                    $subQ->whereNull('end_date')
                        ->orWhereRaw("CAST(SUBSTR(end_date, -4) AS INTEGER) >= ?", [$year]);
                });
        })->pluck('id');

        // Filter skills: show personal skills always OR skills related to experiences/educations
        $query = $this->model->where(function ($q) use ($relevantExperienceIds, $relevantEducationIds) {
            // Always show personal skills
            $q->where('is_personal', true);

            // Also show skills from relevant experiences
            if ($relevantExperienceIds->isNotEmpty()) {
                $q->orWhereHas('experiences', function ($subQ) use ($relevantExperienceIds) {
                    $subQ->whereIn('experiences.id', $relevantExperienceIds);
                });
            }

            // Also show skills from relevant educations
            if ($relevantEducationIds->isNotEmpty()) {
                $q->orWhereHas('educations', function ($subQ) use ($relevantEducationIds) {
                    $subQ->whereIn('education.id', $relevantEducationIds);
                });
            }
        });

        return $query
            ->orderBy('proficiency', 'desc')
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->map(fn (SkillModel $model) => $this->toDomainEntity($model));
    }

    public function findById(int $id): ?Skill
    {
        $model = $this->model->find($id);
        
        return $model ? $this->toDomainEntity($model) : null;
    }

    public function findPersonalSkills(): Collection
    {
        return $this->model
            ->where('is_personal', true)
            ->orderBy('proficiency', 'desc')
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->map(fn (SkillModel $model) => $this->toDomainEntity($model));
    }

    private function toDomainEntity(SkillModel $model): Skill
    {
        return new Skill(
            id: $model->id,
            name: $model->name,
            category: $model->category,
            proficiency: $model->proficiency,
            isPersonal: $model->is_personal ?? false,
            createdAt: $model->created_at?->toISOString(),
            updatedAt: $model->updated_at?->toISOString()
        );
    }
}
