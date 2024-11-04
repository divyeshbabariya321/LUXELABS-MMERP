<?php

namespace App\Http\Controllers;

use App\Issue;
use App\OldIncoming;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class OldIncomingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  mixed  $oldincoming  get oldincoming model
     * @return void
     */
    public function __construct(protected OldIncoming $oldincoming) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $issues = new Issue;
        if (! empty($_GET['sr_no'])) {
            $sr_no = $_GET['sr_no'];
            $old_incomings = $this->oldincoming::where('serial_no', $sr_no)->paginate(10)->setPath('');
        } elseif (! empty($_GET['status'])) {
            $status = $_GET['status'];
            $old_incomings = $this->oldincoming::where('status', $status)->paginate(5)->setPath('');
        } else {
            $old_incomings = $this->oldincoming->paginate(10);
        }
        $issues = $issues->orderByDesc('created_at')->with('communications')->get();
        $status = $this->oldincoming->getStatus();

        return view('old-incomings.index', compact('status', 'old_incomings', 'issues'));
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
    public function store(Request $request): RedirectResponse
    {
        $this->oldincoming->saveRecord($request);
        Session::flash('success', 'Record Created');

        return redirect()->back();
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
     * @param  int  $id
     * @param  mixed  $serial_no
     */
    public function edit($serial_no): View
    {
        $old_incoming = $this->oldincoming::where('serial_no', $serial_no)->first();
        $status = $this->oldincoming->getStatus();

        return view('old-incomings.edit', compact('status', 'old_incoming'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @param  mixed  $serial_no
     */
    public function update(Request $request, $serial_no): RedirectResponse
    {
        $this->oldincoming->updateRecord($request, $serial_no);
        Session::flash('success', 'Record Updated');

        return redirect()->to('old-incomings');
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        //
    }
}
