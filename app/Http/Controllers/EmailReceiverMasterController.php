<?php

namespace App\Http\Controllers;

use App\Models\EmailReceiverMaster;
use App\Http\Requests\StoreEmailReceiverMasterRequest;
use App\Http\Requests\UpdateEmailReceiverMasterRequest;

class EmailReceiverMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEmailReceiverMasterRequest $request)
    {
        $check_mod      = EmailReceiverMaster::where('module_name', $request->module_name)->first();
        $update_request = UpdateEmailReceiverMasterRequest::create($request);
        if ($check_mod) {
            return $this->update($update_request, $check_mod);
        } else {
            $emailReceiverMaster              = new EmailReceiverMaster();
            $emailReceiverMaster->module_name = $request->module_name;
            $emailReceiverMaster->email       = $request->receiver_email ? $request->receiver_email : '';
            if ($emailReceiverMaster->email) {
                $emailReceiverMaster->configs = $request->configs;
            } else {
                $emailReceiverMaster->configs = null;
            }

            if ($emailReceiverMaster->save()) {
                return ['status' => true, 'message' => 'Success'];
            } else {
                return ['status' => false, 'message' => 'Error'];
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(EmailReceiverMaster $emailReceiverMaster)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(EmailReceiverMaster $emailReceiverMaster)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEmailReceiverMasterRequest $request, EmailReceiverMaster $emailReceiverMaster)
    {
        $emailReceiverMaster->email = $request->receiver_email ? $request->receiver_email : '';
        if ($emailReceiverMaster->email) {
            $emailReceiverMaster->configs = $request->configs;
        } else {
            $emailReceiverMaster->configs = null;
        }

        if ($emailReceiverMaster->save()) {
            return ['status' => true, 'message' => 'Success'];
        } else {
            return ['status' => false, 'message' => 'Error'];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmailReceiverMaster $emailReceiverMaster)
    {
        //
    }
}
