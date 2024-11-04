<?php

namespace App\Http\Controllers;

use App\Agent;
use App\Http\Requests\StoreAgentRequest;
use App\Http\Requests\UpdateAgentRequest;
use App\Old;
use App\Old;
use App\Supplier;
use App\Supplier;
use App\Vendor;
use App\Vendor;
use Illuminate\Http\RedirectResponse;

class AgentController extends Controller
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
     */
    public function store(StoreAgentRequest $request): RedirectResponse
    {

        $data = $request->except('_token');

        Agent::create($data);

        if ($request->model_type == Supplier::class) {
            return redirect()->route('supplier.show', $request->model_id)->withSuccess('You have successfully added an agent!');
        } elseif ($request->model_type == Vendor::class) {
            return redirect()->route('vendors.show', $request->model_id)->withSuccess('You have successfully added an agent!');
        } elseif ($request->model_type == Old::class) {
            return redirect()->route('old.show', $request->model_id)->withSuccess('You have successfully added an agent!');
        }
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAgentRequest $request, int $id): RedirectResponse
    {

        $data = $request->except('_token');

        $agent = Agent::find($id);
        $agent->update($data);

        if (in_array($agent->model_type, [Supplier::class, Vendor::class])) {
            return redirect()->back()->withSuccess('You have successfully updated an agent!');
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        Agent::find($id)->delete();

        return redirect()->back()->withSuccess('You have successfully deleted and agent!');
    }
}
