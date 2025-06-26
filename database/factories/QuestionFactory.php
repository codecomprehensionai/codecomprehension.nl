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
        $type = fake()->randomElement(QuestionType::cases());

        $answer = match ($type) {
            QuestionType::MultipleChoice => json_encode([
                fake()->randomElement([0, 1, 2, 3]),
                ...(fake()->boolean(30) ? [fake()->randomElement([0, 1, 2, 3])] : [])
            ]),
            QuestionType::CodeExplanation => fake()->paragraph(),
            default => fake()->word(),
        };
        return [
            'language'                  => fake()->randomElement(QuestionLanguage::cases()),
            'type'                      => $type,
            'level'                     => fake()->randomElement(QuestionLevel::cases()),
            'question'    => fake()->sentence(),
            'answer'      => $answer,
            'score_max' => fake()->numberBetween(1, 10),
        ];
    }
}
