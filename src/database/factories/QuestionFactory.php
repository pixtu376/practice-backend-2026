<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'survey_id' => 1, // Или Survey::factory()
            'type_id' => 1,
            'question_text' => $this->faker->sentence(),
        ];
    }
}