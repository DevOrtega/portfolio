<?php

namespace App\Providers;

use App\Domain\Portfolio\Repositories\ProjectRepositoryInterface;
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

        // Future bindings for other repositories will go here
        // Example:
        // $this->app->bind(SkillRepositoryInterface::class, EloquentSkillRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
