<?php

namespace Database\Factories;

use App\Task;
use App\User;
use Carbon\Carbon;
use App\DeveloperTask;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentReceiptFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $task_ids           = Task::all()->pluck('id');
        $user_ids           = User::all()->pluck('id');
        $developer_task_ids = DeveloperTask::all()->pluck('id');

        $billing_start_date = $this->faker->dateTimeBetween('-5 years', '-2 years')->format('Y-m-d');

        $billing_end_date = Carbon::createFromFormat('Y-m-d', $billing_start_date)->addDay(rand(1, 200));

        $created_at = $this->faker->dateTimeBetween('-10 years', 'now');
        $datee      = Carbon::now()->createFromFormat('Y-m-d', $billing_start_date);

        return [

            'worked_minutes'     => $this->faker->numberBetween(1, 500),
            'payment'            => $this->faker->randomFloat(2, 1, 1000),
            'status'             => $this->faker->randomElement(['done', 'Pending']),
            'task_id'            => $this->faker->randomElement($task_ids),
            'developer_task_id'  => $this->faker->randomElement($developer_task_ids),
            'rate_estimated'     => $this->faker->numberBetween(1, 500),
            'created_at'         => $created_at,
            'updated_at'         => $created_at,
            'remarks'            => $this->faker->sentence(5, true),
            'date'               => $datee,
            'currency'           => $this->faker->currencyCode(),
            'user_id'            => $this->faker->randomElement($user_ids),
            'billing_start_date' => $billing_start_date,
            'billing_end_date'   => $billing_end_date,
            'billing_due_date'   => $this->faker->dateTimeBetween('-2 years', 'now'),
            'by_command'         => $this->faker->numberBetween(0, 3),

        ];
    }
}
