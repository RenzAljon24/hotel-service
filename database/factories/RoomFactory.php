<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    protected $model = \App\Models\Room::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'room_number' => $this->faker->unique()->numberBetween(1, 100), // Unique room number
            'type' => $this->faker->randomElement(['single', 'double', 'suite']), // Random type
            'description' => $this->faker->text(300), // Random description
            'price' => $this->faker->randomFloat(2, 50, 500), // Price between 50 and 500
            'availability' => $this->faker->boolean, // Random availability
            'image' => $this->faker->imageUrl(640, 480, 'room', true, 'Faker'), // Generating a dummy image URL
        ];
    }
}
