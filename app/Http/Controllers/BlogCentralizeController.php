<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlogCentralize;
use App\Models\EmailReceiverMaster;
use App\Http\Requests\StoreBlogCentralizeRequest;
use App\Http\Requests\UpdateBlogCentralizeRequest;

class BlogCentralizeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page_val          = config('constants.paginate');
        $allblogCentralize = BlogCentralize::orderByDesc('id')->paginate($page_val)->appends(request()->except(['page']));
        $emailReceivRec    = EmailReceiverMaster::where('module_name', 'blog')->first();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('blog-centralize.partial_index', compact('allblogCentralize'))->render(),
                'links' => (string) $allblogCentralize->render(),
                'count' => $allblogCentralize->total(),
            ], 200);
        } else {
            return view('blog-centralize.index', compact('allblogCentralize', 'emailReceivRec'));
        }
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
    public function store(StoreBlogCentralizeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(BlogCentralize $blogCentralize)
    {
        return ['status' => true, 'data' => $blogCentralize];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(BlogCentralize $blogCentralize)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBlogCentralizeRequest $request, BlogCentralize $blogCentralize)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(BlogCentralize $blogCentralize)
    {
        //
    }
}
