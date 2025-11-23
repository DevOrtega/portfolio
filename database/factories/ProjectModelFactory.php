<?php

namespace Database\Factories;

use App\Infrastructure\Persistence\Eloquent\Models\ProjectModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Persistence\Eloquent\Models\ProjectModel>
 */
class ProjectModelFactory extends Factory
{
    protected $model = ProjectModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'url' => fake()->boolean(70) ? fake()->url() : null,
            'github_url' => fake()->boolean(80) ? 'https://github.com/' . fake()->userName() . '/' . fake()->slug(2) : null,
            'image_path' => fake()->boolean(60) ? '/images/' . fake()->slug() . '.png' : null,
            'tags' => fake()->randomElements(['PHP', 'Laravel', 'Vue.js', 'JavaScript', 'TypeScript', 'Node.js', 'React'], fake()->numberBetween(1, 4)),
        ];
    }
}
