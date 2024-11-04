<?php

namespace Database\Factories;

use App\Task;
use App\Team;
use App\User;
use App\TeamUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $created_at = $this->faker->dateTimeBetween('-10 years', 'now');

        $task_ids = Task::all()->pluck('id');
        $user_ids = User::all()->pluck('id');
        $team_ids = Team::all()->pluck('id');
        $user_id  = $this->faker->randomElement($user_ids);

        TeamUser::insert([
            'user_id' => $user_id,
            'team_id' => $this->faker->randomElement($team_ids),
        ]);

        return [
            'name'       => $this->faker->word(),
            'user_id'    => $user_id,
            'created_at' => $created_at,
            'updated_at' => $created_at,
        ];
    }
}
