<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\ReturnExchangeStatus;

class ReturnExchangeStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->_createStatuses();
    }

    private function _createStatuses()
    {
        // Set current statuses
        $arrStatus = [
            1 => 'Return request received from customer',
            2 => 'Return request sent to courier',
            3 => 'Return pickup',
            4 => 'Return received in warehouse',
            5 => 'Return accepted',
            6 => 'Return rejected',
        ];

        // Insert all of them
        foreach ($arrStatus as $status) {
            ReturnExchangeStatus::insert(['status_name' => trim($status)]);
        }
    }
}
