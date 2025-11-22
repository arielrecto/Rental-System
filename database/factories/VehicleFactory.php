<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $brands = ['Yamaha', 'Honda', 'Kawasaki', 'Suzuki', 'Ducati', 'BMW', 'Harley-Davidson'];
        $types = ['Sport', 'Cruiser', 'Naked', 'Adventure', 'Touring', 'Scooter'];

        return [
            'brand' => fake()->randomElement($brands),
            'plate_no' => fake()->regexify('[A-Z]{3}-[0-9]{4}'), // Generates a realistic plate number like 'ABC-1234'
            'country_of_origin' => fake()->country(),
            'manufacturer' => fake()->company(), // Use company() for manufacturer names
            'model' => fake()->word(), // Use word() or words() for model names
            'color' => fake()->colorName(),
            'type' => fake()->randomElement($types),
            'year' => fake()->year(),
            'rental_rate' => fake()->numberBetween(100, 99999),
            'mileage' => fake()->numberBetween(1, 999999),
            'condition' => fake()->randomElement(['New', 'Used', 'Excellent', 'Good', 'Fair']),
            'status' => fake()->randomElement(['Available', 'Rented', 'Under Maintenance']), // The status() method does not exist
            'purchase_cost' => fake()->numberBetween(500, 50000),
            'purchase_date' => fake()->date(),
        ];
    }
}
