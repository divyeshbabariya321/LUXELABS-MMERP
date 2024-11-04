<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(TwilioCredentialSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(CustomerTableSeeder::class);
        $this->call(ChatMessageSeeder::class);
        $this->call(LandingPageStatusSeeder::class);
        $this->call(EmailLeadsSeeder::class);

        $this->call(PaymentReceiptSeeder::class);
        $this->call(TeamSeeder::class);
        $this->call(SettingsUpdate::class);
        $this->call(ScheduleQuerySeeder::class);
    }
}
