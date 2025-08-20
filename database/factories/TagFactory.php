<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TagFactory extends Factory
{
    protected $model = \App\Models\Tag::class;
    public function definition(): array {
        $name = $this->faker->unique()->word();
        return ['name'=>$name,'slug'=>\Str::slug($name),'is_active'=>true];
    }
}
