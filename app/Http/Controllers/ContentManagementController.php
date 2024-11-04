<?php

namespace App\Http\Controllers;
use App\StoreSocialContentRemark;
use App\GmailDataList;
use App\ChatMessage;

use App\Http\Requests\AddSocialAccountContentManagementRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Task;
use App\User;
use App\Helpers;
use App\Setting;
use App\StoreWebsite;
use App\PaymentReceipt;
use App\Social\SocialPost;
use App\StoreSocialAccount;
use App\StoreSocialContent;
use App\Social\SocialConfig;
use Illuminate\Http\Request;
use App\Helpers\SocialHelper;
use App\Social\SocialPostLog;
use App\ContentManageentEmail;
use App\StoreSocialContentReview;
use App\StoreSocialContentStatus;
use App\StoreSocialContentHistory;
use App\StoreSocialContentCategory;
use App\StoreSocialContentMilestone;
use Illuminate\Support\Facades\Storage;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Facebook\Exceptions\FacebookResponseException;

class ContentManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $title = 'List | Content Management';

        $websites                 = StoreWebsite::whereNull('deleted_at');
        $content_management_email = ContentManageentEmail::first();
        $keyword                  = request('keyword');
        if (! empty($keyword)) {
            $websites = $websites->where(function ($q) use ($keyword) {
                $q->where('website', 'LIKE', "%$keyword%")
                    ->orWhere('title', 'LIKE', "%$keyword%")
                    ->orWhere('description', 'LIKE', "%$keyword%");
            });
        }

        $websites = $websites->get();

        $gmail_data = GmailDataList::get();

        foreach ($websites as $w) {
            $w->facebookAccount = StoreSocialAccount::where('platform', 'facebook')->where('store_website_id', $w->id)->first();
        }

        return view('content-management.index', compact('title', 'websites', 'gmail_data', 'content_management_email'));
    }

    public function getAttachImages(Request $request): View
    {
        $website = StoreWebsite::where('id', $request->websiteId)->first();
        $media   = $website->getMedia('website-image-attach');

        return view('content-management.attach-images', compact('media'));
    }

    public function downloadAttachImages(Request $request): JsonResponse
    {
        $content = file_get_contents($request->image_url);
        $random  = bin2hex(random_bytes(20)) . '.png';

        $path = Storage::put('uploads/gmail_media/' . $random, $content);

        return response()->json(['random' => $random, 'image_path' => $path, 'status' => true]);
    }

    public function pagePost(Request $request): View
    {
        $imageUrl = $request['imageurl'];
        $query    = SocialConfig::where('store_website_id', $request['websiteId'])->get();

        return view('content-management.select-page', compact('query', 'imageUrl'));
    }

    public function viewAddSocialAccount(): View
    {
        $websites = StoreWebsite::whereNull('deleted_at')->get();

        return view('content-management.add-social-account', compact('websites'));
    }

    public function postSocialAccount(Request $request): RedirectResponse
    {
        if (isset($request['store_website_id'])) {
            $post            = new SocialPost;
            $post->config_id = $request['store_website_id'];
            $post->caption   = $request['message'];
            $post->post_body = '';
            $post->post_by   = Auth::user()->id;
            $post->save();

            $config = SocialConfig::find($request['store_website_id']);

            $message = $request['message'];
            try {
                $access_token     = $config->page_token;
                $page_id          = $config->page_id;
                $source           = $request['imageurl'];
                $image_upload_url = 'https://graph.facebook.com/' . $page_id . '/photos';

                $fbImage = [
                    'access_token' => $access_token,
                    'url'          => $source,
                    'caption'      => $message,
                ];

                $response = SocialHelper::httpPostRequest($image_upload_url, $fbImage);
                $response = json_decode($response);

                if (isset($response->error->message)) {
                    $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $response->error->message);
                } else {
                    $post->posted_on = $request->input('date');
                    $post->status    = 1;
                    if (isset($response->post_id)) {
                        $post->ref_post_id = $response->post_id;
                    }
                    $post->save();
                    $this->socialPostLog($config->id, $post->id, $config->platform, 'success', 'post saved success');
                }
            } catch (FacebookResponseException   $e) {
                Log::info($e); // handle exception
                $this->socialPostLog($config->id, $post->id, $config->platform, 'error', $e->getMessage());
            }
        }

        return redirect()->back();
    }

    public function socialPostLog($config_id, $post_id, $platform, $title, $description)
    {
        $Log                  = new SocialPostLog();
        $Log->config_id       = $config_id;
        $Log->post_id         = $post_id;
        $Log->platform        = $platform;
        $Log->log_title       = $title;
        $Log->log_description = $description;
        $Log->modal           = 'SocialPost';
        $Log->save();

        return true;
    }

    public function addSocialAccount(AddSocialAccountContentManagementRequest $request): RedirectResponse
    {

        $input             = $request->except('_token');
        $input['password'] = Crypt::encrypt(request('password'));
        StoreSocialAccount::create($input);

        return redirect()->back();
    }

    public function manageContent($id, Request $request)
    {
        //Getting Website Details
        $website    = StoreWebsite::find($id);
        $categories = StoreSocialContentCategory::orderByDesc('id');

        if ($request->k != null) {
            $categories = $categories->where('title', 'like', '%' . $request->k . '%');
        }
        $ignoredCategory = [];

        $categories = $categories->paginate(Setting::get('pagination'));

        if (! isset($userIDs)) {
            $userIDs = [];
        }

        $allStatus = StoreSocialContentStatus::all();

        $statusCount = [];

        $users = User::select('id', 'name')->get();

        if ($request->ajax() && $request->pagination == null) {
            return response()->json([
                'tbody' => view('content-management.data', compact('categories', 'users', 'website', 'allStatus', 'ignoredCategory', 'statusCount'))->render(),
                'links' => (string) $categories->render(),
            ], 200);
        }

        return view('content-management.manage-content', compact('categories', 'users', 'website', 'allStatus', 'ignoredCategory', 'statusCount'));
    }

    public function getTaskList($id, Request $request): JsonResponse
    {
        $contentManagemment = StoreSocialContent::where('store_social_content_category_id', $request->category_id)->where('store_website_id', $id)->first();
        $taskLists          = [];
        if ($contentManagemment) {
            if ($contentManagemment->creator_id) {
                $taskLists = Task::where('assign_to', $contentManagemment->creator_id)->where('is_completed', null)->orderByDesc('id')->get();
            }
        }

        return response()->json(['taskLists' => $taskLists], 200);
    }

    public function saveContentCategory(Request $request): JsonResponse
    {
        $isExist = StoreSocialContentCategory::where('title', $request->title)->first();
        if (! $isExist) {
            StoreSocialContentCategory::create(['title' => $request->text]);

            return response()->json(['message' => 'Successful'], 200);
        }

        return response()->json(['message' => 'Error'], 500);
    }

    public function saveContent(Request $request): JsonResponse
    {
        $social_content = StoreSocialContent::where('store_social_content_category_id', $request->category)->where('store_website_id', $request->websiteId)->first();
        $msg            = null;
        $type           = null;
        if (! $social_content) {
            $social_content                                 = new StoreSocialContent;
            $social_content->store_social_content_status_id = 0;
        }
        if ($request->type == 'status') {
            $social_content->store_social_content_status_id = $request->text;
        }
        if ($request->type == 'platform') {
            $social_content->platform = $request->text;
        }
        if ($request->type == 'creator') {
            $social_content->creator_id = $request->text;
        }
        if ($request->type == 'publisher') {
            $social_content->publisher_id = $request->text;
        }
        if ($request->type == 'request_date') {
            $newValue = $request->request_date;
            if ($social_content->request_date) {
                $oldValue = $social_content->request_date;
            } else {
                $oldValue = '';
            }
            $msg                          = 'Request date changed from ' . $oldValue . ' to ' . $newValue;
            $type                         = 'request_date';
            $social_content->request_date = $request->request_date;
        }
        if ($request->type == 'due_date') {
            $newValue = $request->due_date;
            if ($social_content->due_date) {
                $oldValue = $social_content->due_date;
            } else {
                $oldValue = '';
            }
            $msg                      = 'Due date changed from ' . $oldValue . ' to ' . $newValue;
            $type                     = 'due_date';
            $social_content->due_date = $request->due_date;
        }
        if ($request->type == 'publish_date') {
            $newValue = $request->publish_date;
            if ($social_content->publish_date) {
                $oldValue = $social_content->publish_date;
            } else {
                $oldValue = '';
            }
            $msg                          = 'Publish date changed from ' . $oldValue . ' to ' . $newValue;
            $type                         = 'publish_date';
            $social_content->publish_date = $request->publish_date;
        }

        $social_content->store_social_content_category_id = $request->category;
        $social_content->store_website_id                 = $request->websiteId;
        $social_content->save();

        if ($msg) {
            $h                          = new StoreSocialContentHistory;
            $h->type                    = $type;
            $h->store_social_content_id = $social_content->id;
            $h->message                 = $msg;
            $h->username                = Auth::user()->name;
            $h->save();
        }

        return response()->json(['code' => 200, 'messages' => 'Social content Saved Sucessfully']);
    }

    public function editCategory(Request $request): JsonResponse
    {
        $category = StoreSocialContentCategory::find($request->categoryId);
        if ($category) {
            $category->title = $request->category;
            $category->save();
        }

        return response()->json(['code' => 200, 'messages' => 'Category Edited Sucessfully']);
    }

    public function showHistory(Request $request): JsonResponse
    {
        $social_content = StoreSocialContent::where('store_social_content_category_id', $request->category)->where('store_website_id', $request->websiteId)->first();
        if ($social_content) {
            $h = StoreSocialContentHistory::where('store_social_content_id', $social_content->id)->where('type', $request->type)->get();

            return response()->json(['history' => $h], 200);
        }

        return response()->json(['message' => ''], 500);
    }

    public function uploadDocuments(Request $request): JsonResponse
    {
        $path = storage_path('tmp/uploads');

        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->store($path . $name);

        return response()->json([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function saveDocuments(Request $request): JsonResponse
    {
        $site = null;

        if (! $request->task_id || $request->task_id == '') {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'Select one task']);
        }
        $documents = $request->input('document', []);
        if (! empty($documents)) {
            if ($request->id) {
                $site = StoreSocialContent::find($request->id);
            }

            if (! $site || $request->id == null) {
                $site                                   = new StoreSocialContent;
                $site->store_social_content_status_id   = 0;
                $site->store_website_id                 = $request->store_website_id;
                $site->store_social_content_category_id = $request->store_social_content_category_id;
                $site->save();
            }
            $count = 0;
            foreach ($request->input('document', []) as $file) {
                $path  = storage_path('tmp/uploads/' . $file);
                $media = MediaUploader::fromSource($path)
                    ->toDirectory('site-development/' . floor($site->id / config('constants.image_per_folder')))
                    ->upload();
                $site->attachMedia($media, config('constants.media_tags'));
                $count++;
            }
            $task = Task::find($request->task_id);
            if ($task && $task->is_milestone) {
                $content_milestone                          = new StoreSocialContentMilestone;
                $content_milestone->task_id                 = $request->task_id;
                $content_milestone->ono_of_content          = $count;
                $content_milestone->store_social_content_id = $site->id;
                $content_milestone->status                  = 0;
                $content_milestone->save();
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Done!']);
        } else {
            return response()->json(['code' => 400, 'data' => [], 'message' => 'No documents for upload']);
        }
    }

    public function listDocuments(Request $request, $id): JsonResponse
    {
        $site = StoreSocialContent::find($request->id);

        $userList = [];

        if ($site->publisher_id) {
            $userList[$site->publisher->id] = $site->publisher->name;
        }

        if ($site->creator_id) {
            $userList[$site->creator->id] = $site->creator->name;
        }

        $userList = array_filter($userList);
        // create the select box design html here
        $usrSelectBox = '';
        if (!empty($userList)) {
            $usrSelectBox = (string) html()->select('send_message_to', $userList, null)->class('form-control send-message-to-id');
        }

        $records = [];
        if ($site) {
            if ($site->hasMedia(config('constants.media_tags'))) {
                foreach ($site->getMedia(config('constants.media_tags')) as $media) {
                    $reviews     = StoreSocialContentReview::where('file_id', $media->id)->get();
                    $fullReviews = '';
                    if (count($reviews) > 0) {
                        foreach ($reviews as $r) {
                            $fullReviews = $fullReviews . '<p style="margin:0px">*' . $r->review . '</p>';
                        }
                    }
                    $records[] = [
                        'id'          => $media->id,
                        'url'         => getMediaUrl($media),
                        'site_id'     => $site->id,
                        'user_list'   => $usrSelectBox,
                        'fullReviews' => $fullReviews,
                    ];
                }
            }
        }

        return response()->json(['code' => 200, 'data' => $records]);
    }

    public function saveReviews(Request $request): JsonResponse
    {
        $review            = new StoreSocialContentReview;
        $review->file_id   = $request->id;
        $review->review    = $request->message;
        $review->review_by = Auth::user()->name;
        $review->save();

        return response()->json(['code' => 200, 'message' => 'Successfull']);
    }

    public function deleteDocument(Request $request): JsonResponse
    {
        if ($request->id != null) {
            $media = \Plank\Mediable\Media::find($request->id);
            if ($media) {
                $media->delete();

                return response()->json(['code' => 200, 'message' => 'Document delete succesfully']);
            }
        }

        return response()->json(['code' => 404, 'message' => 'No document found']);
    }

    public function sendDocument(Request $request): JsonResponse
    {
        if ($request->id != null && $request->site_id != null && $request->user_id != null) {
            $media = \Plank\Mediable\Media::find($request->id);
            $user  = User::find($request->user_id);
            if ($user) {
                if ($media) {
                    ChatMessage::sendWithChatApi(
                        $user->phone,
                        null,
                        'Please find attached file',
                        getMediaUrl($media)
                    );

                    return response()->json(['code' => 200, 'message' => 'Document send succesfully']);
                }
            } else {
                return response()->json(['code' => 200, 'message' => 'User or site is not available']);
            }
        }

        return response()->json(['code' => 400, 'message' => 'Sorry required fields is missing like id, siteid , userid']);
    }

    public function remarks(Request $request, $id): JsonResponse
    {
        $response = StoreSocialContentRemark::join('users as u', 'u.id', 'store_social_content_remarks.user_id')
            ->where('store_social_content_id', $id)
            ->select(['store_social_content_remarks.*', DB::raw('u.name as created_by')])
            ->orderByDesc('store_social_content_remarks.created_at')
            ->get();

        return response()->json(['code' => 200, 'data' => $response]);
    }

    public function saveRemarks(Request $request, $id): JsonResponse
    {
        StoreSocialContentRemark::create([
            'remarks'                 => $request->remark,
            'store_social_content_id' => $id,
            'user_id'                 => Auth::user()->id,
        ]);

        $response = StoreSocialContentRemark::join('users as u', 'u.id', 'store_social_content_remarks.user_id')
            ->where('store_social_content_id', $id)
            ->select(['store_social_content_remarks.*', DB::raw('u.name as created_by')])
            ->orderByDesc('store_social_content_remarks.created_at')
            ->get();

        return response()->json(['code' => 200, 'data' => $response]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        dd('a');
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
        dd('b');
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

    public function previewImage($id): View
    {
        $contents = StoreSocialContent::where('store_website_id', $id)->get();
        $records  = [];
        foreach ($contents as $site) {
            if ($site) {
                $userList = [];

                if ($site->publisher_id) {
                    $userList[$site->publisher->id] = $site->publisher->name;
                }

                if ($site->creator_id) {
                    $userList[$site->creator->id] = $site->creator->name;
                }
                $userList = array_filter($userList);

                if ($site->hasMedia(config('constants.media_tags'))) {
                    foreach ($site->getMedia(config('constants.media_tags')) as $media) {
                        $records[] = [
                            'id'        => $media->id,
                            'url'       => getMediaUrl($media),
                            'site_id'   => $site->id,
                            'user_list' => $userList,
                        ];
                    }
                }
            }
        }
        $title = 'Preview images';

        return view('content-management.preview-website-images', compact('title', 'records'));
    }

    public function previewCategoryImage($id): View
    {
        $site    = StoreSocialContent::find($id);
        $records = [];
        if ($site) {
            $userList = [];

            if ($site->publisher_id) {
                $userList[$site->publisher->id] = $site->publisher->name;
            }

            if ($site->creator_id) {
                $userList[$site->creator->id] = $site->creator->name;
            }
            $userList = array_filter($userList);

            if ($site->hasMedia(config('constants.media_tags'))) {
                foreach ($site->getMedia(config('constants.media_tags')) as $media) {
                    $records[] = [
                        'id'        => $media->id,
                        'url'       => getMediaUrl($media),
                        'site_id'   => $site->id,
                        'user_list' => $userList,
                    ];
                }
            }
        }
        $title = 'Preview images';

        return view('content-management.preview-website-images', compact('title', 'records'));
    }

    public function getTaskMilestones($id): View
    {
        $site           = StoreSocialContent::find($id);
        $taskMilestones = null;
        if ($site) {
            $taskMilestones = StoreSocialContentMilestone::where('store_social_content_id', $id)->get();
        }
        $title = 'Task milestones';

        return view('content-management.task-milestones', compact('title', 'taskMilestones'));
    }

    public function submitMilestones(Request $request): JsonResponse
    {
        $countMilestone = 0;
        if (count($request->store_social_content_milestone_id) > 0) {
            foreach ($request->store_social_content_milestone_id as $id) {
                $milestone = StoreSocialContentMilestone::find($id);
                if (! $milestone->status) {
                    $task = Task::find($milestone->task_id);
                    if ($task && $task->is_milestone && $task->milestone_completed < $task->no_of_milestone) {
                        if (! $task->cost || $task->cost == '') {
                            return response()->json([
                                'message' => 'Please provide task cost first',
                            ], 500);
                        }
                        $countMilestone = 1;
                        $milestone->update(['status' => 1]);
                    } else {
                        return response()->json([
                            'message' => 'Total milestone exceeded for this task',
                        ], 500);
                    }
                } else {
                    return response()->json([
                        'message' => 'Already approved',
                    ], 500);
                }
            }
        } else {
            return response()->json([
                'message' => 'select some data first',
            ], 500);
        }
        if ($countMilestone) {
            $newCompleted              = $task->milestone_completed + 1;
            $individualPrice           = $task->cost / $task->no_of_milestone;
            $task->milestone_completed = $newCompleted;
            $task->save();
            $payment_receipt                 = new PaymentReceipt;
            $payment_receipt->date           = date('Y-m-d');
            $payment_receipt->worked_minutes = $task->approximate;
            $payment_receipt->rate_estimated = $individualPrice;
            $payment_receipt->status         = 'Pending';
            $payment_receipt->task_id        = $task->id;
            $payment_receipt->user_id        = $task->assign_to;
            $payment_receipt->save();
        }

        return response()->json([
            'message' => 'Successful',
        ], 200);
    }

    public function viewAllContents(Request $request): View
    {
        $contents           = StoreSocialContent::query();
        $users              = Helpers::getUserArray(User::all());
        $records            = [];
        $selected_publisher = $request->publisher_id;
        $selected_creator   = $request->creator_id;
        $selected_website   = $request->store_website_id;
        $selected_category  = $request->store_social_content_category_id;

        if ($selected_publisher) {
            $contents = $contents->where('publisher_id', $selected_publisher);
        }
        if ($selected_creator) {
            $contents = $contents->where('creator_id', $selected_creator);
        }
        if ($selected_website) {
            $contents = $contents->where('store_website_id', $selected_website);
        }
        if ($selected_category) {
            $contents = $contents->where('store_social_content_category_id', $selected_category);
        }
        $contents = $contents->get();
        foreach ($contents as $site) {
            if ($site) {
                if ($site->hasMedia(config('constants.media_tags'))) {
                    foreach ($site->getMedia(config('constants.media_tags')) as $media) {
                        $siteName  = null;
                        $creator   = null;
                        $publisher = null;
                        $category  = null;
                        if ($site->publisher_id) {
                            $publisher = $site->publisher->name;
                        }

                        if ($site->creator_id) {
                            $creator = $site->creator->name;
                        }

                        $store_website = StoreWebsite::find($site->store_website_id);
                        if ($store_website) {
                            $siteName = $store_website->title;
                        }
                        $categoryContent = StoreSocialContentCategory::find($site->store_social_content_category_id);
                        if ($categoryContent) {
                            $category = $categoryContent->title;
                        }

                        $records[] = [
                            'id'               => $media->id,
                            'url'              => getMediaUrl($media),
                            'site_id'          => $site->id,
                            'siteName'         => $siteName,
                            'creator'          => $creator,
                            'publisher'        => $publisher,
                            'store_website_id' => $site->store_website_id,
                            'category'         => $category,
                            'extension'        => strtolower($media->extension),
                        ];
                    }
                }
            }
        }
        $store_websites = StoreWebsite::pluck('title', 'id');
        $categories     = StoreSocialContentCategory::pluck('title', 'id');

        return view('content-management.all-contents', compact('records', 'users', 'selected_publisher', 'selected_creator', 'store_websites', 'selected_website', 'categories', 'selected_category'));
    }

    public function emailStore(Request $request): JsonResponse
    {
        $email = ContentManageentEmail::first();
        if ($email) {
            $email->email = $request->email;
            $email->save();

            return response()->json(['status' => true, 'message' => 'Email Updated successfully']);
        }
        $email        = new ContentManageentEmail;
        $email->email = $request->email;
        $email->save();

        return response()->json(['status' => true, 'message' => 'Email Saved successfully']);
    }
}
