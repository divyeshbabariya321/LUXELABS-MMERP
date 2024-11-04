<?php

use App\UiDevice;
use App\UiResponsivestatusHistory;
use Illuminate\Database\Migrations\Migration;
use Exception;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $devices = UiDevice::whereNull('status')->get();

        foreach ($devices as $device) {
            $old_status = $device->status;
            $device->update(['status' => 10]); // 10 - None in site_development_statuses table

            $dataArray = [
                'id'         => $device->id,
                'uicheck_id' => $device->uicheck_id,
                'device_no'  => $device->device_no,
                'old_status' => $old_status,
                'status'     => 10,
            ];

            $collection = collect($dataArray);
            // Convert the collection to an object
            $object = json_decode(json_encode($collection));

            $this->uicheckResponsiveUpdateHistory($object, $old_status);
        }
    }

    public function uicheckResponsiveUpdateHistory($data, $old_status)
    {
        try {
            // $data['user_id'] = \Auth::user()->id ?? '';
            UiResponsivestatusHistory::create(
                [
                    'user_id'      => 6, // Yogesh user id.
                    'ui_device_id' => $data->id ?? '',
                    'uicheck_id'   => $data->uicheck_id ?? '',
                    'device_no'    => $data->device_no ?? '',
                    'status'       => $data->status ?? '',
                    'old_status'   => $old_status ?? '',
                ]
            );
        } catch (Exception $e) {
            return respException($e);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
