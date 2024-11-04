<?php

namespace Database\Seeders;

use App\Email;
use Illuminate\Database\Seeder;

class UpdateEmailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emails = Email::all();

        foreach ($emails as $email) {
            $data        = explode('@', $email->from);
            $name        = $data[0];
            $email->name = $name;
            $email->save();
        }
    }
}
