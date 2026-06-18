<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        $categories = [
            'Food & Dining', 'Shopping', 'Transport', 'Fuel', 'Groceries',
            'Bills', 'Utilities', 'Healthcare', 'Education', 'Entertainment',
            'Travel', 'Rent', 'Investment', 'Salary', 'Subscription', 'Other',
        ];

        return [
            'user_id' => User::factory(),
            'amount' => fake()->randomFloat(2, 1, 1000),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement($categories),
            'merchant' => fake()->company(),
            'payment_method' => fake()->randomElement(['Cash', 'Credit Card', 'Debit Card', 'UPI', 'Bank Transfer']),
            'expense_date' => fake()->dateTimeBetween('-6 months', 'today')->format('Y-m-d'),
            'notes' => fake()->optional()->sentence(),
            'ai_confidence' => null,
            'is_recurring' => fake()->boolean(20),
        ];
    }
}
