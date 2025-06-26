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
        $options = fake()->words(4);

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
            'estimated_answer_duration' => fake()->numberBetween(30, 300),
            'topic' => fake()->word(),
            'tags'  => fake()->words(3),
            'question'    => fake()->sentence(),
            'explanation' => fake()->optional()->paragraph(),
            'code'        => fake()->text(),
            'options'     => $options,
            'answer'      => $answer,
        ];
    }

    /**
     * Create a multiple choice question with specific correct answers
     */
    public function multipleChoice(array $correctIndices = [0]): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => QuestionType::MultipleChoice,
            'options' => ['Option A', 'Option B', 'Option C', 'Option D'],
            'answer' => $correctIndices,
        ]);
    }

    /**
     * Create a code explanation question
     */
    public function codeExplanation(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => QuestionType::CodeExplanation,
            'options' => null,
            'answer' => fake()->paragraph(),
        ]);
    }
}
