<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\Account;
use Carbon\Carbon;
use App\AutoCommentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\UsersAutoCommentHistory;
use Illuminate\Support\Facades\Auth;

class UsersAutoCommentHistoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $autoCommentHistories = new AutoCommentHistory();
        $user                 = Auth::user();
        if (! $user->hasRole('Admin')) {
            $autoCommentHistories = $autoCommentHistories->whereIn('id', UsersAutoCommentHistory::where('user_id', $user->id)
                ->pluck('auto_comment_history_id')
                ->toArray()
            );
        }

        $accounts = Account::where('platform', 'instagram')->where('manual_comment', 1)->where('blocked', 0)->get();

        $comments = $autoCommentHistories->orderByDesc('created_at')->paginate(25);

        return view('instagram.auto_comments.user_ach', compact('comments', 'accounts'));
    }

    public function assignPosts(): RedirectResponse
    {
        $user = Auth::user();

        $autoCommentHistory = AutoCommentHistory::where('status', 0)
            ->whereNotIn('id', UsersAutoCommentHistory::pluck('auto_comment_history_id')->toArray())
            ->take(25)
            ->get();

        $productsAttached = 0;

        foreach ($autoCommentHistory as $ach) {
            UsersAutoCommentHistory::insert([
                'user_id'                 => $user->id,
                'auto_comment_history_id' => $ach->id,
                'created_at'              => Carbon::now()->toDateTimeString(),
                'updated_at'              => Carbon::now()->toDateTimeString(),
            ]);
            $productsAttached++;
        }

        return redirect()->back()->with('message', 'Successfully added ' . $productsAttached . ' posts to comment!');
    }

    public function sendMessagesToWhatsappToScrap(Request $request): JsonResponse
    {
        $posts = $request->get('posts');
        $user  = Auth::user();

        $message = 'The comments to be posted on posts are: ';

        foreach ($posts as $postId) {
            $post = AutoCommentHistory::find($postId);
            $message .= "\n Post Url: instagram://media?id=$post->post_id \n Comment: $post->comment \n\n";
        }

        $myRequest = new Request();
        $myRequest->setMethod('POST');
        $myRequest->request->add([
            'vendor_id'      => $user->id,
            'message'        => $message,
            'is_vendor_user' => 'yes',
            'status'         => 1,
        ]);

        app(WhatsAppController::class)->sendMessage($myRequest, 'vendor');

        return response()->json([
            'status' => 'success',
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
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        //
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
