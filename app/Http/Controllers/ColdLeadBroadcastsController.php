<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreColdLeadBroadcastRequest;
use App\Http\Requests\IndexColdLeadBroadcastRequest;
use Illuminate\Http\RedirectResponse;
use App\ColdLeads;
use Carbon\Carbon;
use App\CompetitorPage;
use App\ColdLeadBroadcasts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ColdLeadBroadcastsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexColdLeadBroadcastRequest $request)
    {
        if (! $request->isXmlHttpRequest()) {
            return view('cold_leads.broadcasts.index');
        }


        if (strlen($request->get('query')) >= 4) {
            $query = $request->get('query');
            $leads = ColdLeadBroadcasts::where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%$query%");
            });
        } else {
            $leads = new ColdLeadBroadcasts;
        }

        $leads       = $leads->orderByDesc('updated_at')->paginate($request->get('pagination'));
        $competitors = CompetitorPage::select('id', 'name')->where('platform', 'instagram')->get();

        return response()->json([
            'leads'       => $leads,
            'competitors' => $competitors,
        ]);
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
    public function store(StoreColdLeadBroadcastRequest $request): RedirectResponse
    {

        $broadcast                  = new ColdLeadBroadcasts();
        $broadcast->name            = $request->get('name');
        $broadcast->number_of_users = $request->get('number_of_users');
        $broadcast->frequency       = $request->get('frequency');
        $broadcast->message         = $request->get('message');
        $broadcast->started_at      = $request->get('started_at');
        $broadcast->status          = $request->get('status');
        $broadcast->save();

        if ($request->hasFile('image')) {
            $file             = $request->file('image');
            $fileName         = Storage::disk('s3')->putFile('', $file);
            $broadcast->image = $fileName;
            $broadcast->save();
        }

        $limit = $request->get('number_of_users');

        $query      = ColdLeads::query();
        $competitor = $request->competitor;

        if (! empty($competitor)) {
            $comp  = CompetitorPage::find($competitor);
            $query = $query->where('because_of', 'LIKE', '%via ' . $comp->name . '%');
        }

        if (! empty($request->gender)) {
            $query = $query->where('gender', $request->gender);
        }

        $coldleads = $query->where('status', 1)->where('messages_sent', '<', 5)->take($limit)->orderBy('messages_sent')->orderBy('id')->get();

        $count = 0;
        $leads = [];

        $now     = $request->started_at ? Carbon::parse($request->started_at) : Carbon::now();
        $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
        $evening = Carbon::create($now->year, $now->month, $now->day, 19, 0, 0);

        if (! $now->between($morning, $evening, true)) {
            if (Carbon::parse($now->format('Y-m-d'))->diffInWeekDays(Carbon::parse($morning->format('Y-m-d')), false) == 0) {
                // add day
                $now->addDay();
                $now     = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
            } else {
                // dont add day
                $now     = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
            }
        }
        $sendingTime = '';

        foreach ($coldleads as $coldlead) {
            $count++;

            // Convert maxTime to unixtime
            if (empty($sendingTime)) {
                $maxTime = strtotime($now);
            } else {
                $now     = $sendingTime ? Carbon::parse($sendingTime) : Carbon::now();
                $morning = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                $evening = Carbon::create($now->year, $now->month, $now->day, 19, 0, 0);

                if (! $now->between($morning, $evening, true)) {
                    if (Carbon::parse($now->format('Y-m-d'))->diffInWeekDays(Carbon::parse($morning->format('Y-m-d')), false) == 0) {
                        // add day
                        $now->addDay();
                        $now     = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                    } else {
                        // dont add day
                        $now     = Carbon::create($now->year, $now->month, $now->day, 9, 0, 0);
                    }
                }
                $sendingTime = $now;
                $maxTime     = strtotime($sendingTime);
            }

            // Add interval
            $maxTime = $maxTime + (3600 / $request->frequency);

            // Check if it's in the future
            if ($maxTime < time()) {
                $maxTime = time();
            }

            $sendAfter   = date('Y-m-d H:i:s', $maxTime);
            $sendingTime = $sendAfter;

            //Giving BroadCast to Least Count
            $count = [];

            $coldlead->status = 2;
            $coldlead->save();
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ColdLeadBroadcasts $coldLeadBroadcasts)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(ColdLeadBroadcasts $coldLeadBroadcasts)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ColdLeadBroadcasts $coldLeadBroadcasts)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ColdLeadBroadcasts $coldLeadBroadcasts)
    {
        //
    }
}
