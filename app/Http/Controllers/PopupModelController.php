<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Models\DataTableColumn;
use App\Models\EventCategory;
use App\Models\InformationSchemaTable;
use App\PageInstruction;
use App\ReplyCategory;
use App\ResourceCategory;
use App\StoreWebsite;
use App\TaskCategory;
use App\TaskStatus;
use App\Team;
use App\User;
use App\Vendor;
use App\VendorCategory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PopupModelController extends Controller
{
    public function createEventModel(): JsonResponse
    {
        try {
            $users = User::orderBy('name')->get();
            $vendors = Vendor::all();
            $eventCategories = EventCategory::get();
            $categories = VendorCategory::get();
            $html = view('partials.modals.create-event', compact('users', 'vendors', 'eventCategories', 'categories'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function vendorFlowChartHeaderModel(): JsonResponse
    {
        try {
            $vendorFlowcharts = Vendor::whereNotNull('flowchart_date')->orderBy('name', 'asc')->get();
            $html = view('vendors.partials.vendor-flowchart-header-model', compact('vendorFlowcharts'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function vendorQAHeaderModel(): JsonResponse
    {
        try {
            $vendorQuestionAnswers = Vendor::where('question_status', 1)->orderBy('name', 'asc')->get();
            $html = view('vendors.partials.vendor-qa-header-model', compact('vendorQuestionAnswers'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function vendorRatingQAHeaderModel(): JsonResponse
    {
        try {
            $vendorRatingQuestionAnswers = Vendor::where('rating_question_status', 1)->orderBy('name', 'asc')->get();
            $html = view('vendors.partials.vendor-rating-qa-header-model', compact('vendorRatingQuestionAnswers'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function createResourceModel(): JsonResponse
    {
        try {
            $resorcecategory = ResourceCategory::select('id', 'parent_id', 'title')->where('parent_id', 0)->get();
            $html = view('resourceimg.partials.short-cut-modal-create-resource-center', compact('resorcecategory'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function createVendorShortCutModel(): JsonResponse
    {
        try {
            $html = view('vendors.partials.vendor-shortcut-modals')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function quickInstructionNoteModel(): JsonResponse
    {
        try {
            $url = request()->get('url');
            $pageInstruction = PageInstruction::where('page', $url)->first();
            $html = view('partials.modals.quick-instruction-notes', compact('pageInstruction'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function keywordQuickReplyModel(): JsonResponse
    {
        try {
            $replyCategories = Cache::remember('reply_parent_category', 60 * 60 * 24 * 1, function () {
                return ReplyCategory::select('id', 'name')->with('approval_leads', 'sub_categories')->where('parent_id', 0)->orderby('name', 'ASC')->get();
            });
            $html = view('partials.modals.shortcuts-header', compact('replyCategories'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function createDevTaskModel(): JsonResponse
    {
        try {
            $html = view('partials.modals.quick-create-task-window')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function createZoomMeetingModel(): JsonResponse
    {
        try {
            $vendors = Vendor::all();
            $html = view('partials.modals.quick-zoom-meeting-window', compact('vendors'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function viewAllParticipantsModel(): JsonResponse
    {
        try {
            $html = view('partials.modals.view-all-participants')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function addvoucherModel(): JsonResponse
    {
        try {
            $html = view('partials.modals.add-vochuers-modal')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function listDocumentationModel(): JsonResponse
    {
        try {
            $html = view('partials.modals.list-documetation-shortcut-modal')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function createDocumentationModel(): JsonResponse
    {
        try {
            $html = view('partials.modals.documentation-create-modal')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function githubPrListModel(): JsonResponse
    {
        try {
            $html = view('partials.modals.pull-request-alerts-modal')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function databaseBackupMonitoringModel(): JsonResponse
    {
        try {
            $html = view('databse-Backup.db-errors-list')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function timeDoctorLogsModel(): JsonResponse
    {
        try {
            $html = view('partials.modals.timer-alerts-modal')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function zabbixIssueModel(): JsonResponse
    {
        try {
            $html = view('partials.modals.zabbix-issues-summary')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function liveLaravelLogsModel(): JsonResponse
    {
        try {
            $html = view('partials.modals.live-laravel-logs-summary')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function eventAlertsModel(): JsonResponse
    {
        try {
            $html = view('partials.modals.event-alerts-modal')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function searchCommandModel(): JsonResponse
    {
        try {
            $isAdmin = auth()->user()->isAdmin();
            $users = User::orderBy('name')->get();
            $websites = StoreWebsite::get();
            $html = view('partials.modals.magento-commands-modal', compact('isAdmin', 'users', 'websites'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function createEventShortcutModel(): JsonResponse
    {
        try {
            $vendors = Vendor::all();
            $html = view('partials.modals.shortcut-user-event-modal', compact('vendors'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function userAvailabilityModel(): JsonResponse
    {
        try {
            $users = User::orderBy('name')->get();
            $html = view('user-management.search-user-schedule', compact('users'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => 'Something went wrong']);
        }
    }

    public function searchGoogleDocModel(): JsonResponse
    {
        try {
            $html = view('googledocs.partials.search-doc')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function createGoogleDocModel(): JsonResponse
    {
        try {
            $users = User::orderBy('name')->get();
            $html = view('googledocs.partials.create-doc', compact('users'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function codeShortcutModel(): JsonResponse
    {
        try {
            $html = view('partials.modals.short-cut-notes-alerts-modal')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadListCodeShortcutTitleModel(): JsonResponse
    {
        try {
            $html = view('partials.modals.list-code-shortcode-title')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function shortCutNotesCreate(): JsonResponse
    {
        try {
            $html = view('code-shortcut.partials.short-cut-notes-create')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function googleDriveScreenCastModel(): JsonResponse
    {
        try {
            $html = view('partials.modals.google-drive-screen-cast-modal')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function uploadScreenCastModel(): JsonResponse
    {
        try {
            $html = view('googledrivescreencast.partials.upload')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function passwordCreateModal(): JsonResponse
    {
        try {
            $html = view('partials.modals.password-create-modal ')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function searchPasswordModel(): JsonResponse
    {
        try {
            $users = User::orderBy('name')->get();
            $html = view('passwords.search-password', compact('users'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => 'Something went wrong']);
        }
    }

    public function scriptDocumentErrorLogModel(): JsonResponse
    {
        try {
            $html = view('partials.modals.script-document-error-logs-modal')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function magentoCronErrorStatusModel(): JsonResponse
    {
        try {
            $html = view('partials.modals.magento-cron-error-status-modal')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function jenkinsBuildStatusModel(): JsonResponse
    {
        try {
            $html = view('monitor.partials.jenkins_build_status')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function monitorStatusModel(): JsonResponse
    {
        try {
            $html = view('monitor-server.partials.monitor_status')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function createDatabaseModel(): JsonResponse
    {
        try {
            $storeWebsiteConnections = StoreWebsite::DB_CONNECTION;
            $users = User::orderBy('name')->get();
            $database_table_name = InformationSchemaTable::where('table_schema', config('database.connections.mysql.database'))
                ->get();
            $html = view('database.partial.create-database-model', compact('storeWebsiteConnections', 'users', 'database_table_name'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function permissionRequestModel(): JsonResponse
    {
        try {
            $html = view('permissions.partials.permission-request-model')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function taskAndActivityModel(): JsonResponse
    {
        try {
            $userLists = User::where('is_active', 1)->orderBy('name')->get();
            $html = view('task-module.partials.task-and-activity-model', compact('userLists'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function quickDevTaskModel(): JsonResponse
    {
        try {
            $userLists = User::where('is_active', 1)->orderBy('name')->get();
            $html = view('task-module.partials.quick-dev-task-model', compact('userLists'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function systemRequestModel(): JsonResponse
    {
        try {
            $userLists = User::where('is_active', 1)->orderBy('name')->get();
            $shell_list = shell_exec('bash '.config('env.DEPLOYMENT_SCRIPTS_PATH').'/webaccess-firewall.sh -f list');
            $html = view('partials.modals.system-request-model', compact('userLists', 'shell_list'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function quickAppointmentRequestModel(): JsonResponse
    {
        try {
            $users = User::orderBy('name')->get();
            $html = view('partials.modals.quick-appointment-request-model', compact('users'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadContactModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.modal-contact')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadTaskCategoryModel(): JsonResponse
    {
        try {
            $task_categories = TaskCategory::where('parent_id', 0)->get();
            $html = view('task-module.partials.modal-task-category', compact('task_categories'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadTaskViewModel(): JsonResponse
    {
        try {
            $tasks_view = [];
            $all_task_categories = TaskCategory::all();
            $categories = [];
            foreach ($all_task_categories as $category) {
                $categories[$category->id] = $category->title;
            }

            $model_team = Team::where('user_id', auth()->user()->id)->pluck('id')->toArray(); // Get team IDs directly
            $usersOrderByName = User::select('id', 'name', 'email')->whereNull('deleted_at')->orderBy('name')->get();
            $isTeamLeader = empty($model_team);
            $usrlst = [];
            if ($isTeamLeader && Auth::user()->hasRole('Admin')) {
                $usrlst = $usersOrderByName;
            }

            $users = Helpers::getUserArray($usrlst->toArray());

            $html = view('task-module.partials.modal-task-view', compact('tasks_view', 'categories', 'users'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadWhatsappGroupModel(): JsonResponse
    {
        try {

            $model_team = Team::where('user_id', auth()->user()->id)->pluck('id')->toArray(); // Get team IDs directly
            $usersOrderByName = User::select('id', 'name', 'email')->whereNull('deleted_at')->orderBy('name')->get();
            $isTeamLeader = empty($model_team);
            $usrlst = [];
            if ($isTeamLeader && Auth::user()->hasRole('Admin')) {
                $usrlst = $usersOrderByName;
            }

            $users = Helpers::getUserArray($usrlst->toArray());

            $html = view('task-module.partials.modal-whatsapp-group', compact('users'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadTaskReminderModel(): JsonResponse
    {
        try {

            $html = view('task-module.partials.modal-task-bell')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadConfirmMessageModel(): JsonResponse
    {
        try {

            $html = view('task-module.partials.modal-task-bell')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadCsvExportModel(): JsonResponse
    {
        try {
            $usersForExport = [];

            if (Auth::user()->hasRole('Admin')) {
                $usersForExport = User::select('name', 'id')->get();
            } else {
                $usersForExport = User::select('name', 'id')->where('id', '=', Auth::user()->id)->get();
            }
            $html = view('task-module.partials.modal-csv-export-date-picker', compact('usersForExport'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadreminderMessageModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.modal-reminder')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadtaskStatusModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.modal-task-status')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadTrackedTimeHistoryModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.tracked-time-history')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadTimerHistoryModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.timer-history')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadUserHistoryModel(): JsonResponse
    {
        try {
            $html = view('development.partials.user_history_modal')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadColumnvisibilityModel(): JsonResponse
    {
        try {
            $datatableModel = DataTableColumn::select('column_name')->where('user_id', auth()->user()->id)->where('section_name', 'task-listing')->first();
            $dynamicColumnsToShowTask = [];
            if (! empty($datatableModel->column_name)) {
                $hideColumns = $datatableModel->column_name ?? '';
                $dynamicColumnsToShowTask = json_decode($hideColumns, true);
            }
            $html = view('task-module.partials.column-visibility-modal', compact('dynamicColumnsToShowTask'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadstatusColourModel(): JsonResponse
    {
        try {
            $taskstatus = TaskStatus::get();

            $html = view('task-module.partials.modal-status-color', compact('taskstatus'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadPriorityModel(): JsonResponse
    {
        try {
            $model_team = Team::where('user_id', auth()->user()->id)->pluck('id')->toArray(); // Get team IDs directly
            $isTeamLeader = empty($model_team);
            $usrlst = [];
            if ($isTeamLeader && Auth::user()->hasRole('Admin')) {
                $usersOrderByName = User::select('id', 'name', 'email')->whereNull('deleted_at')->orderBy('name')->get();
                $usrlst = $usersOrderByName;
            }

            $users = Helpers::getUserArray($usrlst->toArray());

            $html = view('task-module.partials.modal-priority', compact('users'))->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadAllTaskCategoryModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.modal-all-task-category')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadchatListHistoryModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.modal-chat-list-history')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadCreateTaskModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.modal-create-task')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadPreviewTaskImageModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.modal-preview-task-image')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadPreviewTaskCreateModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.modal-preview-task-create')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadFileUploadAreaSectionModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.modal-file-upload-area-section')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadSendMessageTextBoxModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.modal-send-message-text-box')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadPreviewDocumentModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.modal-preview-document')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadRecurringHistoryModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.modal-recurring-history')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadTaskCreateLogListingModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.modal-task-create-log-listing')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadCreateDTaskModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.modal-create-d-task')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadTaskGoogleDocModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.modal-task-google-doc')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadTaskGoogleDocListModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.modal-task-google-doc-list')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadUploadeTaskFileModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.modal-uploade-task-file')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadDisplayTaskFileUploadModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.modal-display-task-file-upload')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadRecordVoiceNotesModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.modal-record-voice-notes')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }

    public function loadStatusQuickHistoryModel(): JsonResponse
    {
        try {
            $html = view('task-module.partials.modal-status-quick-history')->render();

            return response()->json(['code' => 200, 'html' => $html]);
        } catch (Exception $e) {
            return response()->json(['code' => 400, 'message' => 'Opps! Something went wrong, Pease try again.']);
        }
    }
}
