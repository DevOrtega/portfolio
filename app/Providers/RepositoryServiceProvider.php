<?php

namespace App\Providers;

use App\Domain\Bus\Repositories\BusCompanyRepositoryInterface;
use App\Domain\Bus\Repositories\BusLineRepositoryInterface;
use App\Domain\Bus\Repositories\BusStopRepositoryInterface;
use App\Domain\Portfolio\Repositories\EducationRepositoryInterface;
use App\Domain\Portfolio\Repositories\ExperienceRepositoryInterface;
use App\Domain\Portfolio\Repositories\PersonalInfoRepositoryInterface;
use App\Domain\Portfolio\Repositories\ProjectRepositoryInterface;
use App\Domain\Portfolio\Repositories\SkillRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\EloquentEducationRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentExperienceRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentPersonalInfoRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentSkillRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentBusCompanyRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentBusLineRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentBusStopRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentProjectRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Repository Service Provider
 * 
 * Binds repository interfaces to their concrete implementations.
 * This enables Dependency Injection and follows the Dependency Inversion Principle.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Project Repository
        $this->app->bind(
            ProjectRepositoryInterface::class,
            EloquentProjectRepository::class
        );

        // Bind Experience Repository
        $this->app->bind(
            ExperienceRepositoryInterface::class,
            EloquentExperienceRepository::class
        );

        // Bind Education Repository
        $this->app->bind(
            EducationRepositoryInterface::class,
            EloquentEducationRepository::class
        );

        // Bind Skill Repository
        $this->app->bind(
            SkillRepositoryInterface::class,
            EloquentSkillRepository::class
        );

        // Bind PersonalInfo Repository
        $this->app->bind(
            PersonalInfoRepositoryInterface::class,
            EloquentPersonalInfoRepository::class
        );

        // Bind Bus Repositories
        $this->app->bind(
            BusCompanyRepositoryInterface::class,
            EloquentBusCompanyRepository::class
        );

        $this->app->bind(
            BusStopRepositoryInterface::class,
            EloquentBusStopRepository::class
        );

        $this->app->bind(
            BusLineRepositoryInterface::class,
            EloquentBusLineRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
