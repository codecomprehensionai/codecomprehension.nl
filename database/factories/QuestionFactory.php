<?php

namespace Database\Factories;

use App\Enums\QuestionLanguage;
use App\Enums\QuestionLevel;
use App\Enums\QuestionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'language'                  => fake()->randomElement(QuestionLanguage::cases()),
            'type'                      => fake()->randomElement(QuestionType::cases()),
            'level'                     => fake()->randomElement(QuestionLevel::cases()),
            'question'    => fake()->sentence(),
            'answer'      => fake()->sentence(),
            'score_max'  => fake()->numberBetween(1, 5),
        ];
    }
}
