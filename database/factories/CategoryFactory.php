<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class CategoryFactory extends Factory
{
    protected $model = \App\Models\Category::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return ['name' => $name, 'slug' => Str::slug($name), 'is_active' => true];
    }
}
