<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assignment>
 */
class AssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lti_id'                => fake()->uuid(),
            'lti_lineitem_endpoint' => sprintf(
                'https://uvadlo-dev.test.instructure.com/api/lti/courses/%s/line_items/%s',
                fake()->numberBetween(1000, 9999),
                fake()->numberBetween(10, 99)
            ),
            'course_id'   => Course::factory(),
            'title'       => fake()->sentence(),
            'description' => fake()->paragraph(),
            'deadline_at' => fake()->dateTimeBetween('now', '+1 month'),
        ];
    }
}
