<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = \App\Models\Article::class;
    public function definition(): array {
        $title = $this->faker->sentence(6);
        return [
            'author_id'   => \App\Models\User::factory(),
            'category_id' => \App\Models\Category::factory(),
            'title'       => $title,
            'slug'        => \Str::slug($title).'-'.\Str::random(6),
            'summary'     => $this->faker->sentence(),
            'body'        => '<p>'.$this->faker->paragraph(3).'</p>',
            'status'      => 'published',
            'language'    => 'tr',
            'published_at'=> now(),
        ];
    }
    public function draft(){ return $this->state(fn()=>['status'=>'draft','published_at'=>null]); }
}
