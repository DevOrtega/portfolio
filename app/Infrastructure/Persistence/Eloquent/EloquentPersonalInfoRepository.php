<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Portfolio\Entities\PersonalInfo;
use App\Domain\Portfolio\Repositories\PersonalInfoRepositoryInterface;
use App\Models\PersonalInfo as PersonalInfoModel;

/**
 * Eloquent implementation of PersonalInfoRepositoryInterface
 */
final class EloquentPersonalInfoRepository implements PersonalInfoRepositoryInterface
{
    public function __construct(
        private readonly PersonalInfoModel $model
    ) {
    }

    public function get(): ?PersonalInfo
    {
        $model = $this->model->first();
        
        return $model ? $this->toDomainEntity($model) : null;
    }

    public function findById(int $id): ?PersonalInfo
    {
        $model = $this->model->find($id);
        
        return $model ? $this->toDomainEntity($model) : null;
    }

    public function save(PersonalInfo $personalInfo): PersonalInfo
    {
        $model = $this->model->updateOrCreate(
            ['id' => $personalInfo->id],
            [
                'name' => $personalInfo->name,
                'headline' => $personalInfo->headline,
                'headline_en' => $personalInfo->headlineEn,
                'bio' => $personalInfo->bio,
                'bio_en' => $personalInfo->bioEn,
                'email' => $personalInfo->email,
                'linkedin_url' => $personalInfo->linkedinUrl,
                'github_url' => $personalInfo->githubUrl,
                'cv_path' => $personalInfo->cvPath,
            ]
        );

        return $this->toDomainEntity($model);
    }

    private function toDomainEntity(PersonalInfoModel $model): PersonalInfo
    {
        return new PersonalInfo(
            id: $model->id,
            name: $model->name,
            headline: $model->headline,
            headlineEn: $model->headline_en,
            bio: $model->bio,
            bioEn: $model->bio_en,
            email: $model->email,
            linkedinUrl: $model->linkedin_url,
            githubUrl: $model->github_url,
            cvPath: $model->cv_path
        );
    }
}
