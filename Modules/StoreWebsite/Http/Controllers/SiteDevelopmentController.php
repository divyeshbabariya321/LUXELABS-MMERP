<?php

namespace Modules\StoreWebsite\Http\Controllers;

use App\ChatMessage;
use App\DeveloperTask;
use App\Http\Controllers\WhatsAppController;
use App\Models\SiteDevelopmentCreateTaskUsres;
use App\Role;
use App\RoleUser;
use App\Setting;
use App\SiteDevelopment;
use App\SiteDevelopmentArtowrkHistory;
use App\SiteDevelopmentCategory;
use App\SiteDevelopmentCategoryBuilderIoHistory;
use App\SiteDevelopmentHiddenCategory;
use App\SiteDevelopmentMasterCategory;
use App\SiteDevelopmentStatus;
use App\SiteDevelopmentStatusHistory;
use App\Sop;
use App\StoreDevelopmentRemark;
use App\StoreWebsite;
use App\StoreWebsiteImage;
use App\Task;
use App\TaskCategory;
use App\Uicheck;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Plank\Mediable\Facades\MediaUploader as MediaUploader;
use Plank\Mediable\Media;

class SiteDevelopmentController extends Controller
{
    //
    public function index($id, Request $request)
    {
        $input = $request->input();
        $masterCategories = SiteDevelopmentMasterCategory::pluck('title', 'id')->toArray();
        $designDevCategories = SiteDevelopmentMasterCategory::where('title', 'Design')->orWhere('title', 'Functionality')->pluck('title', 'id')->toArray();
        //Getting Website Details
        //        $website = StoreWebsite::find(1);
        $website = null;
        $selectedWebsites = [];
        if (isset($input['websites']) and $input['websites'] != null) {
            $selectedWebsites = $input['websites'];
            if (in_array('all', $selectedWebsites)) {
                $id = 'all';
            }
        }

        if ($id != 'all') {
            $website = StoreWebsite::find($id);
            if (is_array($selectedWebsites) and count($selectedWebsites) > 0) {
                //$categories = SiteDevelopmentCategory::select('site_development_categories.*', 'site_developments.site_development_master_category_id' , 'site_developments.website_id', DB::raw('(SELECT id from site_developments where site_developments.site_development_category_id = site_development_categories.id AND `website_id` IN (' . implode(',', $selectedWebsites) . ') ORDER BY created_at DESC limit 1) as site_development_id'),'store_websites.website');
                $site_dev = SiteDevelopment::select(DB::raw('site_development_category_id,site_developments.id as site_development_id,website_id'));
                $categories = SiteDevelopmentCategory::select(
                    'site_development_categories.*',
                    'site_developments.site_development_master_category_id', 'site_developments.bug_id',
                    'site_dev.website_id',
                    'site_dev.site_development_id',
                    'store_websites.website'
                )
                    ->joinSub($site_dev, 'site_dev', function ($join) {
                        $join->on('site_development_categories.id', '=', 'site_dev.site_development_category_id');
                    })->whereIn('site_developments.website_id', $selectedWebsites);
            } else {
                $categories = SiteDevelopmentCategory::select(
                    'site_development_categories.*',
                    'site_developments.site_development_master_category_id',
                    'site_developments.website_id', 'site_developments.bug_id',
                    DB::raw(
                        '(SELECT 
                            id 
                        from site_developments 
                        where 
                            site_developments.site_development_category_id = site_development_categories.id 
                            AND `website_id` = '.$id.' 
                        ORDER BY created_at DESC 
                        limit 1
                        ) as site_development_id'
                    ),
                    'store_websites.website'
                );
            }
        } else {
            $site_dev = SiteDevelopment::select(DB::raw('site_development_category_id,site_developments.id as site_development_id,website_id'));
            //->GroupBy(DB::raw('site_developments.website_id, site_developments.site_development_category_id'));

            $categories = SiteDevelopmentCategory::select('site_development_categories.*', 'site_developments.site_development_master_category_id', 'site_dev.website_id', 'site_dev.site_development_id', 'store_websites.website', 'site_developments.bug_id')
                ->joinSub($site_dev, 'site_dev', function ($join) {
                    $join->on('site_development_categories.id', '=', 'site_dev.site_development_category_id');
                });
        }

        if ($request->k != null and $request->k != 'undefined') {
            $categories = $categories->whereIn('site_development_categories.title', $request->k);
        }
        if ($id != 'all') {
            $ignoredCategory = SiteDevelopmentHiddenCategory::where('store_website_id', $id)->pluck('category_id')->toArray();
        } else {
            $ignoredCategory = SiteDevelopmentHiddenCategory::all()->pluck('category_id')->toArray();
        }

        if (request('status') == 'ignored') {
            $categories = $categories->whereIn('site_development_categories.id', $ignoredCategory);
        } else {
            $categories = $categories->whereNotIn('site_development_categories.id', $ignoredCategory);
        }

        //$categories = $categories->paginate(Setting::get('pagination'));
        // $categories = $categories->paginate(20);

        // _p(request()->all(), 1);

        if ($id != 'all') {
            if (is_array($selectedWebsites) and count($selectedWebsites) > 0) {
                $categories->join('site_developments', function ($q) use ($selectedWebsites) {
                    $q->on('site_developments.id', '=', 'site_dev.site_development_id')
                        ->whereIn('site_developments.website_id', $selectedWebsites);
                });
            } else {
                $categories->join('site_developments', function ($q) use ($id) {
                    $q->on('site_developments.site_development_category_id', '=', 'site_development_categories.id')
                        ->where('site_developments.website_id', $id);
                });
            }
            $categories->join('store_websites', function ($q) {
                $q->on('store_websites.id', '=', 'site_developments.website_id');
            });
        } else {
            $categories->join('site_developments', function ($q) {
                $q->on('site_developments.id', '=', 'site_dev.site_development_id');
                //                    $q->on('site_developments.site_development_category_id', '=', 'site_development_categories.id');
                //->whereIn('site_developments.website_id', $ids);
            });
            $categories->join('store_websites', function ($q) {
                $q->on('store_websites.id', '=', 'site_dev.website_id');
            });
        }

        if (is_array(request('assignto')) and count(request('assignto')) > 0) {
            $categories->leftJoin('developer_tasks', function ($q) {
                $q->on('site_developments.id', '=', 'developer_tasks.site_developement_id');
            })->leftJoin('users as u1', function ($q) {
                $q->on('u1.id', '=', 'developer_tasks.assigned_to');
            });

            $categories->leftJoin('tasks', function ($q) {
                $q->on('site_developments.id', '=', 'tasks.site_developement_id');
            })->leftJoin('users as u2', function ($q) {
                $q->on('u2.id', '=', 'tasks.assign_to');
            });

            $categories->where(function ($query) {
                $query->whereIn('u1.id', request('assignto'))
                    ->orWhere(function ($q) {
                        $q->whereIn('u2.id', request('assignto'));
                    });
            });
        }
        /* Status filter */
        if ($request->status) {
            //$categories->where('site_developments.status' , $request->status);
            if ($id != 'all') {
                if (is_array($selectedWebsites) and count($selectedWebsites) > 0) {
                    $categories->havingRaw('(SELECT status from site_developments where site_developments.site_development_category_id = site_development_categories.id AND `website_id` IN ('.implode(',', $selectedWebsites).') ORDER BY created_at DESC) = '.$request->status);
                } else {
                    $categories->havingRaw('(SELECT status from site_developments where site_developments.site_development_category_id = site_development_categories.id AND `website_id` = '.$id.' ORDER BY created_at DESC) = '.$request->status);
                }
            } else {
                $categories->havingRaw('(SELECT status from site_developments where site_developments.site_development_category_id = site_development_categories.id  ORDER BY created_at DESC limit 1) = '.$request->status);
            }
        }
        if ($id != 'all') {
            if (! is_array($selectedWebsites)) {
                $categories->groupBy('site_development_categories.id');
            }
        } else {
            //$categories->groupBy('site_developments.website_id', 'site_development_categories.id');
        }
        if ($request->order) {
            if ($request->order == 'title') {
                $categories->orderBy('site_development_categories.title');
            } elseif ($request->order == 'communication') {
                $categories = $categories->leftJoin('store_development_remarks', 'store_development_remarks.store_development_id', '=', 'site_developments.id');
                $categories = $categories->orderByDesc('store_development_remarks.created_at');
            }
        } else {
            $categories->orderBy('title');
        }

        // _p($categories->get(50)->toArray(), 1);

        //main data listing
        $categories = $categories->paginate(25);

        //for filtration category

        //get filter category data
        $filter_category = SiteDevelopmentCategory::orderBy('title')->pluck('title')->toArray();

        foreach ($categories as $category) {
            $finalArray = [];
            $site_developement_id = $category->site_development_id;
            $taskStatistics['Devtask'] = DeveloperTask::where('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select();

            $query = DeveloperTask::join('users', 'users.id', 'developer_tasks.assigned_to')->where('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select('developer_tasks.id', 'developer_tasks.task as subject', 'developer_tasks.status', 'users.name as assigned_to_name');
            $query = $query->addSelect(DB::raw("'Devtask' as task_type,'developer_task' as message_type"));
            $taskStatistics = $query->orderByDesc('developer_tasks.id')->get();
            //print_r($taskStatistics);
            $othertask = Task::where('site_developement_id', $site_developement_id)->whereNull('is_completed')->select();

            $bug_id = $category->bug_id;

            $query1 = Task::join('users', 'users.id', 'tasks.assign_to')->where(function ($qry) use ($site_developement_id, $bug_id) {
                if ($site_developement_id != null && $site_developement_id != '' && $site_developement_id != 0) {
                    if ($bug_id != null && $bug_id != '') {
                        $qry->whereRaw('FIND_IN_SET(?,task_bug_ids)', $bug_id)->orwhere('site_developement_id', $site_developement_id);
                    } else {
                        $qry->where('site_developement_id', $site_developement_id);
                    }
                } elseif ($bug_id != null && $bug_id != '') {
                    $qry->whereRaw('FIND_IN_SET(?,task_bug_ids)', $bug_id);
                } else {
                    $qry->where('site_developement_id', $site_developement_id);
                }
            })->whereNull('is_completed')->select('tasks.id', 'tasks.task_subject as subject', 'tasks.assign_status', 'users.name as assigned_to_name');
            $query1 = $query1->addSelect(DB::raw("'Othertask' as task_type,'task' as message_type"));
            $othertaskStatistics = $query1->orderByDesc('tasks.id')->get();
            $merged = $othertaskStatistics->merge($taskStatistics);
            // print_r($othertaskStatistics);exit;
            foreach ($merged as $m) {
                /*if($m['task_type'] == 'task' ) {
                $object = Task::find($m['id']);
                } else {
                $object = DeveloperTask::find($m['id']);
                }*/
                $chatMessage = $m->whatsappAll()->orderByDesc('id')->pluck('message')->first();
                $m['message'] = $chatMessage;
            }
            $category->assignedTo = $merged;
        }

        //Getting   Roles Developer
        $role = Role::where('name', 'LIKE', '%Developer%')->first();

        //User Roles with Developers
        $roles = RoleUser::select('user_id')->where('role_id', $role->id)->get();

        foreach ($roles as $role) {
            $userIDs[] = $role->user_id;
        }

        if (! isset($userIDs)) {
            $userIDs = [];
        }

        $allStatus = SiteDevelopmentStatus::pluck('name', 'id')->toArray();

        //dd($allStatus);
        if ($id != 'all') {
            $statusCount = SiteDevelopment::join('site_development_statuses as sds', 'sds.id', 'site_developments.status');
            if (is_array($selectedWebsites) and count($selectedWebsites) > 0) {
                $statusCount = $statusCount->whereIn('site_developments.website_id', $selectedWebsites);
            } else {
                $statusCount = $statusCount->where('site_developments.website_id', $id)->groupBy('sds.id');
            }
            $statusCount = $statusCount->where('site_developments.status', $request->status)
                ->select(['sds.name', DB::raw('count(sds.id) as total')])
                ->orderByDesc('name')
                ->get();
        } else {
            $statusCount = SiteDevelopment::join('site_development_statuses as sds', 'sds.id', 'site_developments.status')
                ->where('site_developments.status', $request->status)
                //->groupBy("sds.id")
                ->select(['sds.name', DB::raw('count(sds.id) as total')])
                ->orderByDesc('name')
                ->get();
        }

        $allUsers = User::where('is_active', '1')->select('id', 'name')->orderBy('name')->get();
        $users_all = $allUsers;
        $users = User::select('id', 'name')->whereIn('id', $userIDs)->get();
        $store_websites = StoreWebsite::pluck('title', 'id')->toArray();

        $sd_create_task_user = SiteDevelopmentCreateTaskUsres::where('id', 1)->first();

        $login_user_id = Auth::user()->id;

        $user_ids = [];
        if (isset($sd_create_task_user->user_ids) && ! empty($sd_create_task_user->user_ids)) {
            $user_ids = explode(',', $sd_create_task_user->user_ids);
        }

        if ($request->ajax() && $request->pagination == null) {
            return response()->json([
                'tbody' => view('storewebsite::site-development.partials.data', compact('input', 'masterCategories', 'categories', 'users', 'website', 'users_all', 'allStatus', 'ignoredCategory', 'statusCount', 'allUsers', 'store_websites', 'user_ids', 'login_user_id'))->render(),
                'links' => (string) $categories->render(),
            ], 200);
        }

        return view('storewebsite::site-development.index', compact('input', 'masterCategories', 'categories', 'users', 'users_all', 'website', 'allStatus', 'ignoredCategory', 'statusCount', 'allUsers', 'store_websites', 'designDevCategories', 'filter_category', 'user_ids', 'login_user_id'));
    }

    public function SendTask(Request $request): JsonResponse
    {
        $id = $request->id;

        // $user_id = Auth::id();
        if ($request->type == 'TASK') {
            $task = Task::find($request->taskdata);
            $user = User::find($task->assign_to);
        } else {
            $task = DeveloperTask::find($request->taskdata);
            $user = User::find($task->user_id);
        }
        $taskdata = $request->taskdata;

        $media = \Plank\Mediable\Media::find($request->id);

        $admin = Auth::user();

        $userid = Auth::id();
        $msg = getMediaUrl($media);
        if ($user && $user->phone) {
            if ($request->type == 'TASK') {
                $params = ChatMessage::create([
                    'id' => $id,
                    'user_id' => $userid,
                    'task_id' => $request->task_id,

                    'sent_to_user_id' => $user->id,

                    'erp_user' => $task->assign_to,
                    'contact_id' => $task->assign_to,
                    'message' => getMediaUrl($media),

                ]);
                $params = ChatMessage::create([
                    'id' => $id,
                    'user_id' => $user->id,
                    'task_id' => $taskdata,

                    'sent_to_user_id' => $userid,

                    'erp_user' => $task->assign_to,
                    'contact_id' => $task->assign_to,
                    'message' => getMediaUrl($media),

                ]);
            } else {
                $params = ChatMessage::create([
                    'id' => $id,
                    'user_id' => $userid,
                    'task_id' => $request->task_id,
                    'developer_task_id' => $task->id,
                    'sent_to_user_id' => $user->id,
                    'issue_id' => $task->id,
                    'erp_user' => $task->assign_to,
                    'contact_id' => $task->assign_to,
                    // 'approved' => '1',
                    // 'status' => '2',
                    'message' => getMediaUrl($media),

                ]);
            }

            if ($params) {
                app(WhatsAppController::class)->sendWithThirdApi($user->phone, $user->whatsapp_number, $msg);

                return response()->json([
                    'message' => 'Successfully Send File',
                ], 200);
            }

            return response()->json([
                'message' => 'Something Was Wrong',
            ], 500);

            return response()->json(['message' => 'Sorry required fields is missing like id , userid'], 500);
        }
    }

    public function addMasterCategory(Request $request): JsonResponse
    {
        if ($request->text) {
            //Cross Check if title is present
            $categoryCheck = SiteDevelopmentMasterCategory::where('title', $request->text)->first();

            if (empty($categoryCheck)) {
                //Save the Category
                $develop = new SiteDevelopmentMasterCategory;
                $develop->title = $request->text;
                $develop->save();

                return response()->json(['code' => 200, 'messages' => 'Category Saved Sucessfully']);
            } else {
                return response()->json(['code' => 500, 'messages' => 'Category Already Exist']);
            }
        } else {
            return response()->json(['code' => 500, 'messages' => 'Please Enter Text']);
        }
    }

    public function addCategory(Request $request): JsonResponse
    {
        if ($request->text) {
            //Cross Check if title is present
            $categoryCheck = SiteDevelopmentCategory::where('title', $request->text)->first();
            $websiteId = $request->websiteId;

            if (empty($categoryCheck)) {
                //Save the Category
                $develop = new SiteDevelopmentCategory;
                $develop->title = $request->text;
                $develop->master_category_id = $request->master_category_id;
                $develop->save();

                $all_website = StoreWebsite::get();

                $site_id = 0;

                foreach ($all_website as $key => $value) {
                    $site = new SiteDevelopment;
                    $site->site_development_category_id = $develop->id;
                    $site->site_development_master_category_id = $develop->master_category_id;
                    $site->website_id = $value->id;
                    $site->save();

                    if ($websiteId == $value->id) {
                        $site_id = $site->id;
                    }
                }
                $requests = [
                    '_token' => $request->_token,
                    'task_subject' => $request->text,
                    'task_detail' => 'TEST'.$request->websiteId.' '.$request->text.' '.$request->master_category_id,
                    'task_asssigned_to' => 6,
                    // 'task_asssigned_from' => 10410,
                    'category_id' => 49,
                    'site_id' => $site_id,
                    'task_type' => 0,
                    'repository_id' => null,
                    'cost' => null,
                    'task_id' => null,
                    'customer_id' => null,
                ];
                $check = (new Task)->createTaskFromSortcuts($requests);

                return response()->json(['code' => 200, 'messages' => 'Category Saved Sucessfully']);
            } else {
                $all_website = StoreWebsite::get();
                $i = 1;

                $site_id = 0;

                foreach ($all_website as $key => $value) {
                    $develop = SiteDevelopment::where('site_development_category_id', $categoryCheck->id)->where('website_id', $value->id)->first();
                    if (empty($develop)) {
                        $i = 0;
                        $site = new SiteDevelopment;
                        $site->site_development_category_id = $categoryCheck->id;
                        $site->site_development_master_category_id = $categoryCheck->master_category_id;
                        $site->website_id = $value->id;
                        $site->save();

                        if ($websiteId == $value->id) {
                            $site_id = $site->id;
                        }
                    }
                }
                $designCategoryId = TaskCategory::where('title', 'like', 'Design%')->pluck('id')->first();

                $requests = [
                    '_token' => $request->_token,
                    'task_subject' => $request->text,
                    'task_detail' => 'TEST'.$request->websiteId.' '.$request->text.' '.$request->master_category_id,
                    'task_asssigned_to' => 6,
                    'category_id' => $designCategoryId,
                    'site_id' => $site_id,
                    'task_type' => 0,
                    'repository_id' => null,
                    'cost' => null,
                    'task_id' => null,
                    'customer_id' => null,
                ];
                $check = (new Task)->createTaskFromSortcuts($requests);

                if ($i == 1) {
                    return response()->json(['code' => 500, 'messages' => 'Category Already Exist']);
                } else {
                    return response()->json(['code' => 200, 'messages' => 'Category Saved Sucessfully']);
                }
            }
        } else {
            return response()->json(['code' => 500, 'messages' => 'Please Enter Text']);
        }
    }

    public function createTask(Request $request): JsonResponse
    {
        if ($request->task_category == 'Design') {
            $masterCategoryId = SiteDevelopmentMasterCategory::where('title', 'Design')->pluck('id')->first();
        } else {
            $masterCategoryId = SiteDevelopmentMasterCategory::where('title', 'Design')->pluck('id')->first();
        }
        $website = StoreWebsite::where('id', $request->websiteId)->pluck('title')->first();

        $categories = SiteDevelopmentCategory::leftJoin('site_developments', 'site_developments.site_development_category_id', '=', 'site_development_categories.id')
            ->select('site_development_categories.title', 'site_development_categories.id', 'site_developments.id as site_development_id')->where('site_development_master_category_id', $masterCategoryId)
            ->where('website_id', $request->websiteId)->orderBy('site_development_categories.title')->get();

        foreach ($categories as $category) {
            $requests = [
                '_token' => $request->_token,
                'task_subject' => $category['title'],
                'task_detail' => $website.' '.$category['title'].$request->task_category,
                'task_asssigned_to' => 6,
                'category_id' => 49,
                'site_id' => $category->site_development_id,
                'task_type' => 0,
                'repository_id' => null,
                'cost' => null,
                'task_id' => null,
                'customer_id' => null,
                'is_flow_task' => 0,
            ];
            $task = Task::where(['category' => 49, 'site_developement_id' => $category->site_development_id])->first();
            if ($task == null) {
                $check = (new Task)->createTaskFromSortcuts($requests);
            }
        }

        return response()->json(['code' => 200, 'messages' => 'Task created Sucessfully']);
    }

    public function copyTasksFromWebsite(Request $request): JsonResponse
    {
        /*$siteDevelopmentCategoryIds = SiteDevelopment::where('website_id', $request->copy_to_website)->pluck('site_development_category_id')->toArray();
        $siteDevelopment = SiteDevelopment::where('website_id', $request->copy_to_website)->select('id as site_developement_id','site_development_category_id')->get();
        */
        $tasks = Task::leftJoin('site_developments', 'site_developments.id', '=', 'tasks.site_developement_id')
            ->where('site_developments.website_id', $request->copy_from_website)->select('tasks.*')->get(); //dd($tasks);
        foreach ($tasks as $task) {
            $attachment = ChatMessage::whereNotNull('task_id')->where('task_id', $task->id)->where('message', 'like', '%.zip%')->orderByDesc('id')->first();
            $siteDev = SiteDevelopment::where('id', $task['site_developement_id'])->first(); ///dd($siteDev);
            if ($siteDev != null) {
                $site_development_id = SiteDevelopment::where(['site_development_category_id' => $siteDev['site_development_category_id'], 'website_id' => $request->copy_to_website])->pluck('id')->first();
                if ($site_development_id != null) {
                    $requests = [
                        '_token' => $request->_token,
                        'task_subject' => $task['task_subject'],
                        'task_detail' => $task['task_details'],
                        'task_asssigned_to' => $request->task_asssigned_to,
                        'category_id' => $task['category'],
                        'site_id' => $site_development_id,
                        'task_type' => 0,
                        'repository_id' => null,
                        'cost' => null,
                        'task_id' => null,
                        'customer_id' => null,
                        'is_flow_task' => 0,
                    ];
                    $taskNew = Task::where(['category' => $task['category'], 'site_developement_id' => $site_development_id])->first();
                    if ($taskNew == null) {
                        $createdTask = (new Task)->createTaskFromSortcuts($requests);
                        if (isset($attachment['message'])) {
                            $params = ChatMessage::create([
                                'user_id' => \Auth::user()->id,
                                'task_id' => $createdTask['id'],

                                'sent_to_user_id' => $request->task_asssigned_to,

                                'erp_user' => \Auth::user()->id,
                                'contact_id' => \Auth::user()->id,
                                'message' => $attachment['message'],

                            ]);
                        }
                        if ($task->hasMedia(config('constants.attach_image_tag'))) {
                            foreach ($task->getMedia(config('constants.attach_image_tag')) as $media) {
                                $imageExtensions = ['zip'];
                                $explodeImage = explode('.', getMediaUrl($media));
                                $extension = end($explodeImage);

                                if (in_array($extension, $imageExtensions)) {
                                    $createdTask->attachMedia($media, config('constants.media_tags'));

                                    if (! empty($media->filename)) {
                                        Media::where('filename', $media->filename)->update(['user_id' => Auth::id()]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return response()->json(['code' => 200, 'messages' => 'Task copied Sucessfully']);
    }

    public function addSiteDevelopment(Request $request): JsonResponse
    {
        if ($request->site) {
            $site = SiteDevelopment::find($request->site);
        } else {
            $site = new SiteDevelopment;
        }

        if ($request->type == 'title') {
            $site->title = $request->text;
        }

        if ($request->type == 'description') {
            $site->description = $request->text;
        }

        if ($request->type == 'status') {
            $site->status = $request->text;
        }

        if ($request->type == 'developer') {
            $site->developer_id = $request->text;
        }

        if ($request->type == 'designer_id') {
            $site->designer_id = $request->text;
        }

        if ($request->type == 'html_designer') {
            $site->html_designer = $request->text;
        }

        if ($request->type == 'site_development_master_category_id') {
            $site->site_development_master_category_id = $request->text;
            SiteDevelopment::where(['site_development_category_id' => $request->category])->update(['site_development_master_category_id' => $request->text]);
        }

        if ($request->type == 'tester_id') {
            $site->tester_id = $request->text;
        }

        if ($request->type == 'artwork_status') {
            $old_artwork = $site->artwork_status;
            if (! $old_artwork || $old_artwork == '') {
                $old_artwork = 'Yes';
            }
            $new_artwork = $request->text;
            $site->artwork_status = $request->text;
        }

        $site->site_development_category_id = $request->category;
        $site->website_id = $request->websiteId;
        $site->save();
        $html = '';
        if ($request->type == 'status') {
            $id = $site->id;
            $siteDev = SiteDevelopment::where('id', $id)->first();
            $status = ($siteDev) ? $siteDev->status : 0;
            if ($siteDev && $status > 0) {
                $siteDevelopmentStatusHistory = SiteDevelopmentStatusHistory::create([
                    'site_development_id' => $id,
                    'status_id' => $siteDev->status,
                    'user_id' => auth()->user()->id,
                ]);

                $siteDev->update(['status' => $status]);
            }

            if ($status == 3) {
                $html .= "<i class='fa fa-ban save-status' data-text='4' data-site=".$siteDev->id.' data-category='.$siteDev->site_development_category_id."  data-type='status' aria-hidden='true' style='color:red;'' title='Deactivate'></i>";
            } elseif ($status == 4 || $status == 0) {
                $html .= "<i class='fa fa-ban save-status' data-text='3' data-site=".$siteDev->id.' data-category='.$siteDev->site_development_category_id."  data-type='status' aria-hidden='true' style='color:black;' title='Activate'></i>";
            }
        }

        if ($request->type == 'artwork_status') {
            $history = new SiteDevelopmentArtowrkHistory;
            $history->date = date('Y-m-d');
            $history->site_development_id = $site->id;
            $history->from_status = $old_artwork;
            $history->to_status = $new_artwork;
            $history->username = Auth::user()->name;
            $history->save();
        }

        return response()->json(['code' => 200, 'messages' => 'Site Development Saved Sucessfully', 'html' => $html]);
    }

    public function getArtworkHistory($site_id): JsonResponse
    {
        $site = SiteDevelopment::find($site_id);
        $histories = [];
        if ($site) {
            $histories = SiteDevelopmentArtowrkHistory::where('site_development_id', $site->id)->get();
        }

        return response()->json(['code' => 200, 'data' => $histories]);
    }

    public function statusHistory($site_id): JsonResponse
    {
        $site = SiteDevelopment::find($site_id);
        $histories = [];
        if ($site) {
            $hist = $site->statusHistories()->latest()->get();
            if (! $hist->isEmpty()) {
                foreach ($hist as $h) {
                    $histories[] = [
                        'id' => $h->id,
                        'status_name' => $h->status->name,
                        'user_name' => $h->user->name,
                        'created_at' => (string) $h->created_at,
                    ];
                }
            }
        }

        return response()->json(['code' => 200, 'data' => $histories]);
    }

    public function editCategory(Request $request): JsonResponse
    {
        $category = SiteDevelopmentCategory::find($request->categoryId);
        if ($category) {
            $category->title = $request->category;
            $category->save();
        }

        return response()->json(['code' => 200, 'messages' => 'Category Edited Sucessfully']);
    }

    public function disallowCategory(Request $request): JsonResponse
    {
        $category = $request->category;
        $store_website_id = $request->store_website_id;

        if ($category != null && $store_website_id != null) {
            if ($request->status) {
                SiteDevelopmentHiddenCategory::where('store_website_id', $request->store_website_id)->where('category_id', $request->category)->delete();
            } else {
                $siteDevHiddenCat = SiteDevelopmentHiddenCategory::updateOrCreate(
                    ['store_website_id' => $request->store_website_id, 'category_id' => $request->category],
                    ['store_website_id' => $request->store_website_id, 'category_id' => $request->category]
                );
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Data updated Sucessfully']);
        }

        return response()->json(['code' => 500, 'data' => [], 'message' => 'Required field missing like store website or category']);
    }

    public function uploadDocuments(Request $request): JsonResponse
    {
        $path = storage_path('tmp/uploads');

        if (! file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        $name = uniqid().'_'.trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name' => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function saveDocuments(Request $request): JsonResponse
    {
        $site = null;
        $documents = $request->input('document', []);
        if (! empty($documents)) {
            if ($request->id) {
                $site = SiteDevelopment::find($request->id);
            }

            if (! $site || $request->id == null) {
                $site = new SiteDevelopment;
                $site->title = '';
                $site->description = '';
                $site->website_id = $request->store_website_id;
                $site->site_development_category_id = $request->site_development_category_id;
                $site->save();
            }

            foreach ($request->input('document', []) as $file) {
                $path = storage_path('tmp/uploads/'.$file);
                $media = MediaUploader::fromSource($path)
                    ->toDirectory('site-development/'.floor($site->id / config('constants.image_per_folder')))
                    ->upload();
                $site->attachMedia($media, config('constants.media_tags'));
            }

            return response()->json(['code' => 200, 'data' => [], 'message' => 'Done!']);
        } else {
            return response()->json(['code' => 500, 'data' => [], 'message' => 'No documents for upload']);
        }
    }

    public function listDocuments(Request $request, $id): JsonResponse
    {
        $site = SiteDevelopment::find($request->id);

        $userList = [];

        if ($site->developer) {
            $userList[$site->developer->id] = $site->developer->name;
        }

        if ($site->designer) {
            $userList[$site->designer->id] = $site->designer->name;
        }

        $userList = array_filter($userList);
        // create the select box design html here
        $usrSelectBox = '';
        if (! empty($userList)) {
            $usrSelectBox = (string) html()->select('send_message_to', $userList, null)->class('form-control send-message-to-id');
        }

        $records = [];
        if ($site) {
            if ($site->hasMedia(config('constants.media_tags'))) {
                foreach ($site->getMedia(config('constants.media_tags')) as $media) {
                    $records[] = [
                        'id' => $media->id,
                        'url' => getMediaUrl($media),
                        'site_id' => $site->id,
                        'user_list' => $usrSelectBox,
                    ];
                }
            }
        }

        return response()->json(['code' => 200, 'data' => $records]);
    }

    public function previewTaskImage($id): View
    {
        $task = Task::find($id);

        $records = [];
        if ($task) {
            $userList = User::pluck('name', 'id')->all();
            $task = Task::find($id);
            $userName = '';
            $mediaDetail = [];
            // $usrSelectBox = "";
            // if (!empty($userList)) {
            //     $usrSelectBox = (string) \Form::select("send_message_to", $userList, null, ["class" => "form-control send-message-to-id"]);
            // }
            if ($task->hasMedia(config('constants.attach_image_tag'))) {
                foreach ($task->getMedia(config('constants.attach_image_tag')) as $media) {
                    $imageExtensions = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg', 'svgz', 'cgm', 'djv', 'djvu', 'ico', 'ief', 'jpe', 'pbm', 'pgm', 'pnm', 'ppm', 'ras', 'rgb', 'tif', 'tiff', 'wbmp', 'xbm', 'xpm', 'xwd'];
                    $explodeImage = explode('.', getMediaUrl($media));
                    $extension = end($explodeImage);

                    if (in_array($extension, $imageExtensions)) {
                        $isImage = true;
                    } else {
                        $isImage = false;
                    }

                    $mediaDetail = Media::where('id', $media->id)->first();
                    if ($mediaDetail) {
                        $userName = User::where('id', $mediaDetail->user_id)->pluck('name')->first();
                    } else {
                        $userName = '';
                    }

                    $records[] = [
                        'media_id' => $id,
                        'id' => $media->id,
                        'url' => getMediaUrl($media),
                        'task_id' => $task->id,
                        'isImage' => $isImage,
                        'userList' => $userList,
                        'userName' => $userName,
                        'created_at' => $media->created_at,
                    ];
                }
            }
        }

        $records = array_reverse($records);
        $title = 'Preview images';

        return view('storewebsite::site-development.partials.preview-task-images', compact('title', 'records'));
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

        return response()->json(['code' => 500, 'message' => 'No document found']);
    }

    public function sendDocument(Request $request): JsonResponse
    {
        if ($request->id != null && $request->site_id != null && $request->user_id != null) {
            $media = \Plank\Mediable\Media::find($request->id);
            $user = User::find($request->user_id);
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

        return response()->json(['code' => 200, 'message' => 'Sorry required fields is missing like id, siteid , userid']);
    }

    public function SendTaskSOP(Request $request): JsonResponse
    {
        $media = \Plank\Mediable\Media::find($request->id);
        $user = User::find($request->user_id);

        $task = Task::find($request->task_id);
        $username = User::find($task->assign_to);

        // dd($username->name);
        $userid = Auth::id();

        if ($username) {
            $params = Sop::create([
                'name' => $username->name,
                'content' => getMediaUrl($media),

            ]);

            return response()->json(['message' => 'File Added Successfully In Sop', 'success' => true]);
        } else {
            return response()->json(['message' => 'Task is not assigned to any user', 'success' => false]);
        }
    }

    public function remarks(Request $request, $id): JsonResponse
    {
        // $response = StoreDevelopmentRemark::join("users as u","u.id","store_development_remarks.user_id")->where("store_development_id",$id)
        // ->select(["store_development_remarks.*",DB::raw("u.name as created_by")])
        // ->orderBy("store_development_remarks.created_at","desc")
        // ->get();
        $data = SiteDevelopment::where('site_development_category_id', $request->cat_id)->where('website_id', $request->website_id)->get();
        $response = [];
        foreach ($data as $val) {
            $remarks = StoreDevelopmentRemark::join('users as usr', 'usr.id', 'store_development_remarks.user_id')
                ->where('store_development_remarks.store_development_id', $val->id)
                ->select('store_development_remarks.*', 'usr.name as created_by')
                ->get()->toArray();
            array_push($response, $remarks);
        }

        return response()->json(['code' => 200, 'data' => $response, 'site_id' => $id]);
    }

    public function saveRemarks(Request $request, $id): JsonResponse
    {
        StoreDevelopmentRemark::create([
            'remarks' => $request->remark,
            'store_development_id' => $id,
            'user_id' => \Auth::user()->id,
        ]);

        $site_devs = SiteDevelopment::where('site_development_category_id', $request->cat_id)->where('website_id', $request->website_id)->get()->pluck('id')->toArray();

        // $response = StoreDevelopmentRemark::whereIn('store_development_id',$site_devs)->orderBy('id', 'DESC')->get();
        $response = StoreDevelopmentRemark::join('users as u', 'u.id', 'store_development_remarks.user_id')->where('store_development_id', $id)
            ->select(['store_development_remarks.*', DB::raw('u.name as created_by')])
            ->orderBy('store_development_remarks.remarks')
            ->get();

        return response()->json(['code' => 200, 'data' => $response]);
    }

    public function previewImage($site_id)
    {
        $site = SiteDevelopment::find($site_id);
        $records = [];
        if ($site) {
            // $userList = [];

            // if ($site->developer_id) {
            //     $userList[$site->publisher->id] = $site->publisher->name;
            // }

            // if ($site->designer_id) {
            //     $userList[$site->creator->id] = $site->creator->name;
            // }
            // if ($site->designer_id) {
            //     $userList[$site->creator->id] = $site->creator->name;
            // }
            // $userList = array_filter($userList);

            if ($site->hasMedia(config('constants.media_tags'))) {
                foreach ($site->getMedia(config('constants.media_tags')) as $media) {
                    $records[] = [
                        'id' => $media->id,
                        'url' => getMediaUrl($media),
                        'site_id' => $site->id,
                    ];
                }
            }
        }
        $title = 'Preview images';

        return response()->json(['code' => 200, 'data' => $records, 'title' => $title]);
        // return view('content-management.preview-website-images', compact('title','records'));
    }

    public function latestRemarks(Request $request, $id): JsonResponse
    {
        if ($request->status != '') {
            // $remarks = DB::select(DB::raw('select * from (SELECT max(store_development_remarks.id) as remark_id,remarks,site_development_categories.title,store_development_remarks.created_at,site_development_categories.id as category_id, users.name as username,
            // store_development_remarks.store_development_id,site_developments.id as site_id,store_development_remarks.user_id, site_developments.title as sd_title, sw.website as sw_website,site_developments.status as status
            // FROM `store_development_remarks` inner join site_developments on site_developments.id = store_development_remarks.store_development_id inner join site_development_categories on site_development_categories.id = site_developments.site_development_category_id
            // left join store_websites as sw on sw.id = site_developments.website_id
            // join users on users.id = store_development_remarks.user_id
            // where site_developments.website_id = ' . $id . ' and status =' . $request->status . ' group by store_development_id) as latest join store_development_remarks on store_development_remarks.id = latest.remark_id order by title asc'));
            $remarks = DB::table(function ($query) use ($id, $request) {

                $status = $request->status;

                $query->selectRaw('MAX(store_development_remarks.id) AS remark_id, remarks, site_development_categories.title, store_development_remarks.created_at, site_development_categories.id AS category_id, users.name AS username, store_development_remarks.store_development_id, site_developments.id AS site_id, store_development_remarks.user_id, site_developments.title AS sd_title, sw.website AS sw_website, site_developments.status AS status')
                    ->from('store_development_remarks')
                    ->join('site_developments', 'site_developments.id', '=', 'store_development_remarks.store_development_id')
                    ->join('site_development_categories', 'site_development_categories.id', '=', 'site_developments.site_development_category_id')
                    ->leftJoin('store_websites AS sw', 'sw.id', '=', 'site_developments.website_id')
                    ->join('users', 'users.id', '=', 'store_development_remarks.user_id')
                    ->where('site_developments.website_id', $id)
                    ->where('site_developments.status', $status)
                    ->groupBy('store_development_remarks.store_development_id');
            }, 'latest')
                ->join('store_development_remarks', 'store_development_remarks.id', '=', 'latest.remark_id')
                ->orderBy('latest.title')
                ->get();

        } else {
            // $remarks = DB::select(DB::raw('select * from (SELECT max(store_development_remarks.id) as remark_id,remarks,site_development_categories.title,store_development_remarks.created_at,site_development_categories.id as category_id, users.name as username,
            // store_development_remarks.store_development_id,site_developments.id as site_id,store_development_remarks.user_id, site_developments.title as sd_title, sw.website as sw_website,site_developments.status as status
            // FROM `store_development_remarks` inner join site_developments on site_developments.id = store_development_remarks.store_development_id inner join site_development_categories on site_development_categories.id = site_developments.site_development_category_id
            // left join store_websites as sw on sw.id = site_developments.website_id
            // join users on users.id = store_development_remarks.user_id
            // where site_developments.website_id = '.$id.' group by store_development_id) as latest join store_development_remarks on store_development_remarks.id = latest.remark_id order by title asc'));

            // $remarks = DB::select(DB::raw('select *,SDR.remarks As latest_remarks from (SELECT max(store_development_remarks.id) as remark_id,remarks,site_development_categories.title,store_development_remarks.created_at,site_development_categories.id as category_id, users.name as username,
            // store_development_remarks.store_development_id,site_developments.id as site_id,store_development_remarks.user_id, site_developments.title as sd_title, sw.website as sw_website,site_developments.status as status
            // FROM `store_development_remarks` inner join site_developments on site_developments.id = store_development_remarks.store_development_id inner join site_development_categories on site_development_categories.id = site_developments.site_development_category_id
            // left join store_websites as sw on sw.id = site_developments.website_id
            // join users on users.id = store_development_remarks.user_id
            // where site_developments.website_id = ' . $id . ' group by category_id) as latest inner join store_development_remarks as SDR on SDR.id = latest.remark_id order by title asc'));
            $remarks = DB::table(function ($query) use ($id) {
                $query->selectRaw('MAX(store_development_remarks.id) AS remark_id, remarks, site_development_categories.title, store_development_remarks.created_at, site_development_categories.id AS category_id, users.name AS username, store_development_remarks.store_development_id, site_developments.id AS site_id, store_development_remarks.user_id, site_developments.title AS sd_title, sw.website AS sw_website, site_developments.status AS status')
                    ->from('store_development_remarks')
                    ->join('site_developments', 'site_developments.id', '=', 'store_development_remarks.store_development_id')
                    ->join('site_development_categories', 'site_development_categories.id', '=', 'site_developments.site_development_category_id')
                    ->leftJoin('store_websites AS sw', 'sw.id', '=', 'site_developments.website_id')
                    ->join('users', 'users.id', '=', 'store_development_remarks.user_id')
                    ->where('site_developments.website_id', $id)
                    ->groupBy('category_id');
            }, 'latest')
                ->join('store_development_remarks AS SDR', 'SDR.id', '=', 'latest.remark_id')
                ->select('latest.*', 'SDR.remarks AS latest_remarks')
                ->orderBy('latest.title')
                ->get();

        }
        $username = [];
        foreach ($remarks as $remark) {
            $user = User::find($remark->user_id);
            array_push($username, $user->name);
        }
        $allStatus = SiteDevelopmentStatus::get();
        // $remarks = StoreDevelopmentRemark::join('site_developments','site_developments.id','store_development_remarks.store_development_id')
        // ->join('site_development_categories','site_development_categories.id','site_developments.site_development_category_id')
        // ->orderBy('store_development_remarks.created_at','DESC')
        // ->groupBy('site_developments.site_development_category_id')
        // ->select('store_development_remarks.*','site_development_categories.title')->get();

        // $response = StoreDevelopmentRemark::join("users as u","u.id","store_development_remarks.user_id")->where("store_development_id",$id)
        // ->select(["store_development_remarks.*",DB::raw("u.name as created_by")])
        // ->orderBy("store_development_remarks.created_at","desc")
        // ->get();
        return response()->json(['code' => 200, 'data' => $remarks, 'username' => $username, 'status' => $allStatus]);
    }

    public function allartworkHistory($website_id): JsonResponse
    {
        $histories = SiteDevelopment::join('site_development_artowrk_histories', 'site_development_artowrk_histories.site_development_id', 'site_developments.id')
            ->join('site_development_categories', 'site_development_categories.id', 'site_developments.site_development_category_id')
            ->where('site_developments.website_id', $website_id)
            ->select('site_development_artowrk_histories.*', 'site_development_categories.title')
            ->get();
        $title = 'Multi site artwork histories';

        return response()->json(['code' => 200, 'data' => $histories]);
    }

    public function taskCount($site_developement_id): JsonResponse
    {
        $taskStatistics['Devtask'] = DeveloperTask::where('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select();

        $query = DeveloperTask::join('users', 'users.id', 'developer_tasks.assigned_to')->where('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select('developer_tasks.id', 'developer_tasks.task as subject', 'developer_tasks.status', 'users.name as assigned_to_name');
        $query = $query->addSelect(DB::raw("'Devtask' as task_type,'developer_task' as message_type"));
        $taskStatistics = $query->get();
        //print_r($taskStatistics);
        $othertask = Task::where('site_developement_id', $site_developement_id)->whereNull('is_completed')->select();
        $query1 = Task::join('users', 'users.id', 'tasks.assign_to')->where('site_developement_id', $site_developement_id)->whereNull('is_completed')->select('tasks.id', 'tasks.task_subject as subject', 'tasks.assign_status', 'users.name as assigned_to_name');
        $query1 = $query1->addSelect(DB::raw("'Othertask' as task_type,'task' as message_type"));
        $othertaskStatistics = $query1->get();
        $merged = $othertaskStatistics->merge($taskStatistics);

        return response()->json(['code' => 200, 'taskStatistics' => $merged]);
    }

    public function taskRelation($site_developement_id): JsonResponse
    {
        $othertask = Task::where('site_developement_id', $site_developement_id)->select('id', 'parent_task_id')->get();

        return response()->json(['code' => 200, 'othertask' => $othertask]);
    }

    public function deleteDevTask(Request $request): JsonResponse
    {
        $id = $request->input('id');
        if ($request->tasktype == 'Devtask') {
            $task = DeveloperTask::find($id);
        } elseif ($request->tasktype == 'Othertask') {
            $task = Task::find($id);
        }

        if ($task) {
            $task->delete();
        }

        if ($request->ajax()) {
            return response()->json(['code' => 200]);
        }
    }

    public function adminRemarkFlag(Request $request): JsonResponse
    {
        $remarks = StoreDevelopmentRemark::find($request->remark_id);

        if ($remarks->admin_flagged == 0) {
            $remarks->admin_flagged = 1;
        } else {
            $remarks->admin_flagged = 0;
        }

        $remarks->save();

        return response()->json(['admin_flagged' => $remarks->admin_flagged]);
    }

    public function userRemarkFlag(Request $request): JsonResponse
    {
        $remarks_user = StoreDevelopmentRemark::find($request->remark_id);

        if ($remarks_user->user_flagged == 0) {
            $remarks_user->user_flagged = 1;
        } else {
            $remarks_user->user_flagged = 0;
        }

        $remarks_user->save();

        return response()->json(['user_flagged' => $remarks_user->user_flagged]);
    }

    public function siteDevlopmentStatusUpdate(Request $request): JsonResponse
    {
        $allStatus = SiteDevelopmentStatus::get();
        $site = SiteDevelopment::find($request->site_id);
        $site->status = $request->status;
        $site->save();

        return response()->json(['message' => 'Status updated successfully', 'status' => $allStatus, 'site' => $site]);
    }

    public function checkSiteAsset(Request $request): JsonResponse
    {
        $checkSite = SiteDevelopment::find($request->siteDevelopmentId);
        if (! empty($checkSite)) {
            return response()->json(['code' => 200, 'status' => $checkSite->is_site_asset]);
        } else {
            return response()->json(['code' => 404, 'status' => 'Data Not Found']);
        }
    }

    public function checkUi(Request $request): JsonResponse
    {
        $checkSite = SiteDevelopment::find($request->siteDevelopmentId);
        if (! empty($checkSite)) {
            return response()->json(['code' => 200, 'status' => $checkSite->is_ui]);
        } else {
            return response()->json(['code' => 404, 'status' => 'Data Not Found']);
        }
    }

    public function setcheckUi(Request $request): JsonResponse
    {
        //dd($request->categoryId);
        $siteDevelopments = SiteDevelopment::where('site_development_category_id', $request->categoryId)->get();
        $uidata = Uicheck::where('site_development_category_id', $request->categoryId)->delete();
        if ($request->status == '1') {
            Uicheck::create([
                'site_development_id' => $request->siteDevelopmentId,
                'site_development_category_id' => $request->categoryId,
            ]);
        }
        //dd($siteDevelopments);
        foreach ($siteDevelopments as $siteDevelopment) {
            $siteDevelopment->is_ui = $request->status;
            $siteDevelopment->save();
        }

        return response()->json(['code' => 200, 'status' => 'Data Updated Successully']);
    }

    public function checkSiteList(Request $request): JsonResponse
    {
        $checkSite = SiteDevelopment::find($request->siteDevelopmentId);
        if (! empty($checkSite)) {
            return response()->json(['code' => 200, 'status' => $checkSite->is_site_list]);
        } else {
            return response()->json(['code' => 404, 'status' => 'Data Not Found']);
        }
    }

    public function setSiteAsset(Request $request): JsonResponse
    {
        $siteDevelopments = SiteDevelopment::where('site_development_category_id', $request->categoryId)->get();
        foreach ($siteDevelopments as $siteDevelopment) {
            $siteDevelopment->is_site_asset = $request->status;
            $siteDevelopment->save();
        }

        return response()->json(['code' => 200, 'status' => 'Data Updated Successully']);
    }

    public function setSiteList(Request $request): JsonResponse
    {
        $siteDevelopments = SiteDevelopment::where('site_development_category_id', $request->categoryId)->get();
        foreach ($siteDevelopments as $siteDevelopment) {
            $siteDevelopment->is_site_list = $request->status;
            $siteDevelopment->save();
        }

        return response()->json(['code' => 200, 'status' => 'Data Updated Successully']);
    }

    public function saveSiteAssetData(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $data = [
                'store_website_id' => $request->siteDevelopmentId,
                'category_id' => $request->categoryId,
                'media_id' => $request->media_id,
                'media_type' => $request->media_type,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $id = StoreWebsiteImage::create($data);
            if ($id) {
                return response()->json(['code' => 200, 'status' => 'Data Updated Successully']);
            }

            return response()->json(['code' => 500, 'status' => 'Unable to store']);
        }

        return response()->json(['code' => 404, 'status' => 'Method Invocation is invalid']);
    }

    public function storeWebsiteCategory(Request $request)
    {
        $show = $request->show;
        $pagination = $request->pagination;
        $page = $request->page;
        $selectedCategoryIds = $request->category_id;

        $allCategories = SiteDevelopmentCategory::pluck('title', 'id')->toArray();
        $masterCategories = SiteDevelopmentMasterCategory::pluck('title', 'id')->toArray();
        $site_dev = SiteDevelopment::select(DB::raw('site_development_category_id,site_developments.id as site_development_id,website_id'));
        $categories = SiteDevelopmentCategory::select('site_development_categories.id', 'site_development_categories.builder_io', 'site_development_categories.master_category_id as site_development_master_category_id', 'site_development_categories.title', 'site_dev.website_id', 'site_dev.site_development_id', 'store_websites.website', 'site_development_categories.created_at', 'site_development_categories.updated_at',
            DB::raw('count(site_developments.id) as cnt')
        )
            ->joinSub($site_dev, 'site_dev', function ($join) {
                $join->on('site_development_categories.id', '=', 'site_dev.site_development_category_id');
            })->join('site_developments', function ($q) use ($show) {
                $q->on('site_developments.id', '=', 'site_dev.site_development_id');
                if ($show == '1') {
                    $q->where('site_developments.site_development_master_category_id', '>', 0);
                }
                if ($show == '0') {
                    $q->where('site_developments.site_development_master_category_id', null);
                }
            })->join('store_websites', 'store_websites.id', '=', 'site_developments.website_id')
            ->where(function ($query) use ($request) {
                if (isset($request->search_master_category_id) && $request->search_master_category_id != '') {
                    $query->where('site_development_categories.master_category_id', $request->search_master_category_id);
                }
                if (isset($request->category_id) && $request->category_id != '') {
                    $query->whereIn('site_development_categories.id', $request->category_id);
                }

                return $query;
            })
            ->groupBy('site_development_categories.id')
            // ->orderBy('website')
            ->orderByDesc('site_development_categories.id');
        $categories = $categories->paginate(25);

        $title = 'Store website Category';

        return view('storewebsite::site-development.partials.store-website-category', compact('selectedCategoryIds', 'show', 'masterCategories', 'categories', 'title', 'pagination', 'page', 'allCategories'));
    }

    public function updateMasterCategory(Request $request): JsonResponse
    {
        SiteDevelopmentCategory::where(['id' => $request->category])->update(['master_category_id' => $request->text]);
        SiteDevelopment::where(['site_development_category_id' => $request->category])->update(['site_development_master_category_id' => $request->text]);

        return response()->json(['code' => 200, 'messages' => 'Master Category Saved Sucessfully']);
    }

    public function updateBulkMasterCategory(Request $request): JsonResponse
    {
        $categories = $request->categories;
        $masterCategoryId = $request->master_category_id;
        if (count($categories)) {
            foreach ($categories as $categoryId) {
                SiteDevelopmentCategory::where(['id' => $categoryId])->update(['master_category_id' => $masterCategoryId]);
                SiteDevelopment::where(['site_development_category_id' => $categoryId])->update(['site_development_master_category_id' => $masterCategoryId]);
            }
        }

        return response()->json(['code' => 200, 'messages' => 'Master Categories Saved Sucessfully']);
    }

    public function updateBuilderIO(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $selectedValue = $request->input('selectedValue');

        $siteDevelopmentCategory = SiteDevelopmentCategory::findOrFail($id);

        $history = new SiteDevelopmentCategoryBuilderIoHistory;
        $history->site_development_category_id = $id;
        $history->old_value = $siteDevelopmentCategory->builder_io;
        $history->new_value = $selectedValue;
        $history->user_id = Auth::user()->id;
        $history->save();

        $siteDevelopmentCategory->builder_io = $selectedValue;
        $siteDevelopmentCategory->save();

        return response()->json(['message' => 'Updated successfully']);
    }

    public function builderIOHistories($id): JsonResponse
    {
        $datas = SiteDevelopmentCategoryBuilderIoHistory::with(['user'])
            ->where('site_development_category_id', $id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $datas,
            'message' => 'History get successfully',
            'status_name' => 'success',
        ], 200);
    }

    public function createTaskUsers(Request $request)
    {
        $post = $request->all();
        $validator = Validator::make($post, [
            'user_ids' => 'required',
        ]);

        if ($validator->fails()) {
            $outputString = '';
            $messages = $validator->errors()->getMessages();
            foreach ($messages as $k => $errr) {
                foreach ($errr as $er) {
                    $outputString .= "$k : ".$er.'<br>';
                }
            }

            return response()->json(['code' => 500, 'error' => $outputString]);
        }

        $siteDevHiddenCat = SiteDevelopmentCreateTaskUsres::updateOrCreate(
            ['id' => 1],
            ['user_ids' => implode(',', $request->user_ids)]
        );

        return redirect()->back()->with('success', 'Updated successfully');
    }
}
