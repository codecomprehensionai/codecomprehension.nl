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
            'language'                  => fake()->randomElement(QuestionLanguage::cases()),
            'type'                      => fake()->randomElement(QuestionType::cases()),
            'level'                     => fake()->randomElement(QuestionLevel::cases()),
            'estimated_answer_duration' => fake()->numberBetween(30, 300),

            /* Question aidata */
            'topic' => fake()->word(),
            'tags'  => fake()->words(3),

            /* Question content */
            'question'    => fake()->sentence(),
            'explanation' => fake()->optional()->paragraph(),
            'code'        => fake()->optional()->text(),
            'options'     => fake()->optional()->words(4),
            'answer'      => fake()->optional()->sentence(),
        ];
    }
}
