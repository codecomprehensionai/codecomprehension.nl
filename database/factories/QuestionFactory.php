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
            /* Question metadata */
            'language'  => fake()->randomElement(QuestionLanguage::cases()),
            'type'      => fake()->randomElement(QuestionType::cases()),
            'level'     => fake()->randomElement(QuestionLevel::cases()),
            'score_max' => fake()->randomFloat(1, 1, 10),

            /* Question content */
            'question' => fake()->sentence(),
            'answer'   => fake()->optional()->sentence(),
        ];
    }
}
