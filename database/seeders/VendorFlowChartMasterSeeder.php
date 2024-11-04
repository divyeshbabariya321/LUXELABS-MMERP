<?php

namespace Database\Seeders;

use App\Models\VendorFlowChart;
use Illuminate\Database\Seeder;
use App\Models\VendorFlowChartMaster;

class VendorFlowChartMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 2; $i++) {
            $masterRec        = new VendorFlowChartMaster();
            $masterRec->title = 'Flow Chart ' . $i;
            $masterRec->save();
        }

        //Get 1st Record of Master and update Child table

        $master_rec = VendorFlowChartMaster::first();
        if ($master_rec) {
            VendorFlowChart::query()->update(['master_id' => $master_rec->id]);
        }
    }
}
