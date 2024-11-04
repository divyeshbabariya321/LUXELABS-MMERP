<?php

namespace Database\Seeders;

use App\Customer;
use App\EmailLead;
use Illuminate\Database\Seeder;

class EmailLeadsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customer = Customer::all();

        foreach ($customer as $val) {
            $emailLead             = new EmailLead();
            $emailLead->email      = $val->email;
            $emailLead->created_at = date('Y-m-d H:i:s');

            $emailLead->save();
        }
    }
}
