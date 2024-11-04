<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserActionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\User;
use App\Customer;
use App\ColdLeads;
use App\UserActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Tracker\Vendor\Laravel\Models\Session;

class UserActionsController extends Controller
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
    public function store(StoreUserActionRequest $request): JsonResponse
    {

        $action          = new UserActions();
        $action->user_id = Auth::user()->id;
        $action->page    = $request->get('url');
        $action->details = strip_tags($request->get('data'));
        $action->action  = $request->get('type');
        $action->date    = date('Y-m-d');
        $action->save();

        return response()->json('success');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\UserActions $userActions
     * @param mixed            $id
     */
    public function show($id): View
    {
        $user    = User::find($id);
        $actions = $user->actions()->orderByDesc('created_at')->get();

        $tracks = Session::where('user_id', $id)->orderByDesc('created_at')->get();

        $routeActions = [
            'users.index'      => 'Viewed Users Page',
            'users.show'       => 'Viewed A User',
            'customer.index'   => 'Viewed Customer Page',
            'customer.show'    => 'Viewed A Customer Page',
            'cold-leads.index' => 'Viewed Cold Leads Page',
            'home'             => 'Landed Homepage',
            'purchase.index'   => 'Viewed Purchase Page',
        ];

        $models = [
            'users.show'      => new User(),
            'customer.show'   => new Customer(),
            'cold-leads.show' => new ColdLeads(),
        ];

        return view('users.track', compact('actions', 'tracks', 'routeActions', 'models'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(UserActions $userActions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserActions $userActions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserActions $userActions)
    {
        //
    }
}
