<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = \App\Models\Comment::class;
    public function definition(): array {
        return [
            'article_id' => \App\Models\Article::factory(),
            'user_id'    => null,
            'name'       => $this->faker->name(),
            'email'      => $this->faker->safeEmail(),
            'body'       => $this->faker->sentence(),
            'status'     => 'approved',
            'ip'         => $this->faker->ipv4(),
            'ua'         => 'test',
        ];
    }
}
