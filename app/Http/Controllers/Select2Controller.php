<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\CodeShortCutPlatform;
use App\Customer;
use App\DeveloperTask;
use App\DocumentCategory;
use App\EmailAddress;
use App\Marketing\WhatsappConfig;
use App\Models\CodeShortcutFolder;
use App\Models\VoucherCouponPlatform;
use App\Models\ZabbixWebhookData;
use App\ProductSupplier;
use App\SiteDevelopmentCategory;
use App\SopCategory;
use App\StoreWebsite;
use App\Supplier;
use App\Task;
use App\TaskCategory;
use App\TimeDoctor\TimeDoctorAccount;
use App\TimeDoctor\TimeDoctorMember;
use App\TimeDoctor\TimeDoctorProject;
use App\User;
use App\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Select2Controller extends Controller
{
    public function customers(Request $request): JsonResponse
    {
        $customers = Customer::select('id', 'name', 'email');

        if (! empty($request->q)) {
            $customers->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%'.$request->q.'%')
                    ->orWhere('email', 'LIKE', '%'.$request->q.'%');
            });
        }

        $customers = $customers->paginate(30);

        $result['total_count'] = $customers->total();
        $result['incomplete_results'] = $customers->nextPageUrl() !== null;

        foreach ($customers as $customer) {
            $result['items'][] = [
                'id' => $customer->id,
                'text' => $customer->name,
            ];
        }

        return response()->json($result);
    }

    public function suppliers(Request $request): JsonResponse
    {
        $suppliers = Supplier::select('id', 'supplier');

        if (! empty($request->q)) {
            $suppliers->where(function ($q) use ($request) {
                $q->where('supplier', 'LIKE', '%'.$request->q.'%')
                    ->orWhere('email', 'LIKE', '%'.$request->q.'%');
            });
        }
        $suppliers = $suppliers->paginate(30);
        $result['total_count'] = $suppliers->total();
        $result['incomplete_results'] = $suppliers->nextPageUrl() !== null;

        foreach ($suppliers as $supplier) {
            $result['items'][] = [
                'id' => $supplier->id,
                'text' => $supplier->supplier,
            ];
        }

        return response()->json($result);
    }

    public function scrapedBrand(Request $request)
    {
        $scrapedBrandsRaw = Supplier::selectRaw('scraped_brands_raw')->whereNotNull('scraped_brands_raw')->get();
        $rawBrands = [];

        foreach ($scrapedBrandsRaw as $value) {
            array_push($rawBrands, array_unique(array_filter(array_column(json_decode($value->scraped_brands_raw, true), 'name'))));
        }

        $finalBrands = [];

        foreach ($rawBrands as $brand) {
            $finalBrands += $brand;
        }
        $finalBrands = array_unique($finalBrands);
        if (! empty($request->q)) {
            $finalBrands = array_filter($finalBrands, function ($ele) use ($request) {
                return strpos(strtolower($ele), strtolower($request->q));
            });
        }
        foreach ($finalBrands as $supplier) {
            if (strip_tags($supplier)) {
                $result['items'][] = [
                    'id' => strip_tags($supplier),
                    'text' => strip_tags($supplier),
                ];
            }
            $result['total_count'] = count($finalBrands);
        }

        return response()->json($result);
    }

    public function updatedbyUsers(Request $request): JsonResponse
    {
        $suppliers = User::select('id', 'name');

        $suppliers = $suppliers->paginate(30);

        $result['total_count'] = $suppliers->total();
        $result['incomplete_results'] = $suppliers->nextPageUrl() !== null;

        foreach ($suppliers as $supplier) {
            $result['items'][] = [
                'id' => $supplier->id,
                'text' => $supplier->name,
            ];
        }

        return response()->json($result);
    }

    public function users(Request $request): JsonResponse
    {
        $users = User::select('id', 'name', 'email');

        if (! empty($request->q)) {
            $users->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%'.$request->q.'%')
                    ->orWhere('email', 'LIKE', '%'.$request->q.'%');
            });
        }

        $users = $users->orderBy('name')->paginate(30);

        $result['total_count'] = $users->total();
        $result['incomplete_results'] = $users->nextPageUrl() !== null;

        foreach ($users as $user) {
            $text = $user->name;

            if ($request->get('format') === 'name-email') {
                $text = $user->name.' - '.$user->email;
            }

            $result['items'][] = [
                'id' => $user->id,
                'text' => $text,
            ];
        }

        return response()->json($result);
    }

    public function users_vendors(Request $request): JsonResponse
    {
        $users = User::select('id', 'name', 'email');

        if (! empty($request->q)) {
            $users->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%'.$request->q.'%')
                    ->orWhere('email', 'LIKE', '%'.$request->q.'%');
            });
        }

        $users = $users->orderBy('name')->paginate(30);

        $result['total_count'] = $users->total();
        $result['incomplete_results'] = $users->nextPageUrl() !== null;

        foreach ($users as $user) {
            $text = $user->name;

            if ($request->get('format') === 'name-email') {
                $text = $user->name.' - '.$user->email;
            }

            $result['items'][] = [
                'id' => $user->id,
                'text' => $text,
            ];
        }

        $vendors = Vendor::select('id', 'name', 'email');
        if (! empty($request->q)) {
            $vendors->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%'.$request->q.'%')
                    ->orWhere('email', 'LIKE', '%'.$request->q.'%');
            });
        }
        $vendors = $vendors->paginate(30);

        $result_vendors['vendors_total_count'] = $vendors->total();
        $result_vendors['vendors_incomplete_results'] = $vendors->nextPageUrl() !== null;

        foreach ($vendors as $user) {
            $text = $user->name;

            if ($request->get('format') === 'name-email') {
                $text = $user->name.' - '.$user->email;
            }

            $result_vendors['items'][] = [
                'id' => $user->id,
                'text' => $text,
            ];
        }

        array_push($result, $result_vendors);

        return response()->json($result);
    }

    public function allBrand(Request $request): JsonResponse
    {
        if (isset($request->sort)) {
            $brands = Brand::select('id', 'name')->orderBy('name');
        } else {
            $brands = Brand::select('id', 'name');
        }

        if (! empty($request->q)) {
            $brands->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', $request->q.'%');
            });
        }

        $brands = $brands->paginate(30);

        $result['total_count'] = $brands->total();
        $result['incomplete_results'] = $brands->nextPageUrl() !== null;

        foreach ($brands as $brand) {
            $result['items'][] = [
                'id' => $brand->id,
                'text' => $brand->name,
            ];
        }

        return response()->json($result);
    }

    public function allTasks(Request $request): JsonResponse
    {
        $tasks = DeveloperTask::select('id', 'subject')->where('subject', '<>', '');

        if (! empty($request->q)) {
            $tasks->where(function ($q) use ($request) {
                $q->where('id', 'LIKE', $request->q.'%')->orwhere('subject', 'LIKE', $request->q.'%')->get();
            });
        }
        $tasks = $tasks->paginate(30);

        if (! count($tasks)) {
            $tasks = Task::select('id', 'task_subject')->where('task_subject', '<>', '');

            if (! empty($request->q)) {
                $tasks->where(function ($q) use ($request) {
                    $q->where('id', 'LIKE', $request->q.'%')->orwhere('task_subject', 'LIKE', $request->q.'%')->get();
                });
            }
            $tasks = $tasks->paginate(30);
        }
        $result['total_count'] = $tasks->total();
        $result['incomplete_results'] = $tasks->nextPageUrl() !== null;

        foreach ($tasks as $task) {
            $result['items'][] = [
                'id' => $task->id,
                'text' => get_class($task) == DeveloperTask::class ? '#DEVTASK-'.$task->id.'-'.$task->subject : '#TASK-'.$task->id.'-'.$task->task_subject,
            ];
        }

        return response()->json($result);
    }

    public function allCategory(Request $request): JsonResponse
    {
        $category = Category::select('id', 'title');

        if (! empty($request->q)) {
            $category->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', $request->q.'%');
            });
        }

        $category = $category->paginate(30);

        $result['total_count'] = $category->total();
        $result['incomplete_results'] = $category->nextPageUrl() !== null;

        foreach ($category as $cat) {
            $result['items'][] = [
                'id' => $cat->id,
                'text' => $cat->title,
            ];
        }

        return response()->json($result);
    }

    public function customersByMultiple(Request $request): JsonResponse
    {
        $term = request()->get('q', null);
        $customers = Customer::select('id', 'name', 'phone')->where('name', 'like', "%{$term}%")->orWhere('phone', 'like', "%{$term}%")->orWhere('id', 'like', "%{$term}%");

        $customers = $customers->paginate(30);

        $result['total_count'] = $customers->total();
        $result['incomplete_results'] = $customers->nextPageUrl() !== null;

        foreach ($customers as $customer) {
            $result['items'][] = [
                'id' => $customer->id,
                'text' => '<strong>Name</strong>: '.$customer->name.' <strong>Phone</strong>: '.$customer->phone,
            ];
        }

        return response()->json($result);
    }

    public function allWebsites(Request $request): JsonResponse
    {
        $websites = StoreWebsite::select('id', 'title');

        $websites = $websites->paginate(30);

        $result['total_count'] = $websites->total();
        $result['incomplete_results'] = $websites->nextPageUrl() !== null;

        foreach ($websites as $website) {
            $result['items'][] = [
                'id' => $website->id,
                'text' => $website->title,
            ];
        }

        return response()->json($result);
    }

    public function timeDoctorAccounts(Request $request): JsonResponse
    {
        $time_doctor_accounts = TimeDoctorAccount::select('id', 'time_doctor_email');

        if (! empty($request->q)) {
            $time_doctor_accounts->where(function ($q) use ($request) {
                $q->where('time_doctor_email', 'LIKE', '%'.$request->q.'%');
            });
        }

        $time_doctor_accounts = $time_doctor_accounts->orderBy('time_doctor_email')->paginate(30);

        $result['total_count'] = $time_doctor_accounts->total();
        $result['incomplete_results'] = $time_doctor_accounts->nextPageUrl() !== null;

        foreach ($time_doctor_accounts as $account) {
            $text = $account->time_doctor_email;

            $result['items'][] = [
                'id' => $account->id,
                'text' => $text,
            ];
        }

        return response()->json($result);
    }

    public function timeDoctorAccountsForTask(Request $request): JsonResponse
    {
        $time_doctor_accounts = TimeDoctorAccount::select('id', 'time_doctor_email');

        if (! empty($request->q)) {
            $time_doctor_accounts->where(function ($q) use ($request) {
                $q->where('time_doctor_email', 'LIKE', '%'.$request->q.'%');
            });
        }

        // If I am the member of TimeDoctor, then get my latest time_doctor_account_id
        if (isset(Auth::user()->id)) {
            $myTimeDoctorMember = TimeDoctorMember::where('user_id', Auth::user()->id)->latest()->first();
            if ($myTimeDoctorMember) {
                // Check record exist, Otherwise it will ignore the below condition & get all the remaining accounts as usual.
                $accountExists = TimeDoctorAccount::where('id', $myTimeDoctorMember->time_doctor_account_id)->where('auth_token', '!=', '')->exists();
                if ($accountExists) {
                    $time_doctor_accounts = $time_doctor_accounts->where('id', $myTimeDoctorMember->time_doctor_account_id);
                }
            }
        }

        $time_doctor_accounts = $time_doctor_accounts->where('auth_token', '!=', '');

        $time_doctor_accounts = $time_doctor_accounts->orderBy('time_doctor_email')->paginate(30);

        $result['total_count'] = $time_doctor_accounts->total();
        $result['incomplete_results'] = $time_doctor_accounts->nextPageUrl() !== null;

        foreach ($time_doctor_accounts as $account) {
            $text = $account->time_doctor_email;

            $result['items'][] = [
                'id' => $account->id,
                'text' => $text,
            ];
        }

        return response()->json($result);
    }

    public function timeDoctorProjects(Request $request): JsonResponse
    {
        $time_doctor_projects = TimeDoctorProject::select('time_doctor_project_id', 'time_doctor_project_name');

        if (! empty($request->q)) {
            $time_doctor_projects->where(function ($q) use ($request) {
                $q->where('time_doctor_project_name', 'LIKE', '%'.$request->q.'%');
            });
        }
        if (! empty($request->account_id)) {
            $time_doctor_projects->where('time_doctor_account_id', $request->account_id);
        }

        $time_doctor_projects = $time_doctor_projects->orderBy('time_doctor_project_id')->paginate(30);

        $result['total_count'] = $time_doctor_projects->total();
        $result['incomplete_results'] = $time_doctor_projects->nextPageUrl() !== null;

        foreach ($time_doctor_projects as $project) {
            $text = $project->time_doctor_project_name;

            $result['items'][] = [
                'id' => $project->time_doctor_project_id,
                'text' => $text,
            ];
        }

        return response()->json($result);
    }

    public function timeDoctorProjectsAjax(Request $request)
    {
        $time_doctor_projects = TimeDoctorProject::select('time_doctor_project_id', 'time_doctor_project_name');

        if (! empty($request->q)) {
            $time_doctor_projects->where(function ($q) use ($request) {
                $q->where('time_doctor_project_name', 'LIKE', '%'.$request->q.'%');
            });
        }
        if (! empty($request->account_id)) {
            $time_doctor_projects->where('time_doctor_account_id', $request->account_id);
        }

        $time_doctor_projects = $time_doctor_projects->orderBy('time_doctor_project_id')->get();
        $response_str = "<option value=''>Select Project</option>";
        foreach ($time_doctor_projects as $project) {
            $response_str .= "<option value='".$project->time_doctor_project_id."'>".$project->time_doctor_project_name.'</option>';
        }

        return $response_str;
    }

    public function taskCategory(Request $request): JsonResponse
    {
        if (! empty($request->q)) {
            $taskCategories = TaskCategory::where('is_approved', 1)
                ->where('parent_id', 0)
                ->where('title', 'LIKE', $request->q.'%')
                ->get()
                ->toArray();
        } else {
            $taskCategories = TaskCategory::where('is_approved', 1)
                ->where('parent_id', 0)
                ->get()
                ->toArray();
        }

        $result = [];

        if (empty($taskCategories)) {
            $result['items'][] = [
                'id' => '',
                'text' => 'Category not available',
            ];
        } else {
            foreach ($taskCategories as $cat) {
                $result['items'][] = [
                    'id' => $cat['id'],
                    'text' => $cat['title'],
                ];
            }
        }

        return response()->json($result);
    }

    public function zabbixWebhookData(Request $request): JsonResponse
    {
        $zabbixWebhookDatas = ZabbixWebhookData::select('id', 'subject')->whereNull('zabbix_task_id');

        if (! empty($request->q)) {
            $zabbixWebhookDatas->where(function ($q) use ($request) {
                $q->where('subject', 'LIKE', '%'.$request->q.'%');
            });
        }

        $zabbixWebhookDatas = $zabbixWebhookDatas->latest()->get();

        foreach ($zabbixWebhookDatas as $zabbixWebhookData) {
            $result['items'][] = [
                'id' => $zabbixWebhookData->id,
                'text' => $zabbixWebhookData->subject,
            ];
        }

        return response()->json($result);
    }

    public function sopCategories(Request $request): JsonResponse
    {
        $sopCategories = SopCategory::select('id', 'category_name');

        if (! empty($request->q)) {
            $sopCategories->where(function ($q) use ($request) {
                $q->where('category_name', 'LIKE', '%'.$request->q.'%');
            });
        }

        $sopCategories = $sopCategories->latest()->get();

        foreach ($sopCategories as $sopCategory) {
            $result['items'][] = [
                'id' => $sopCategory->id,
                'text' => $sopCategory->category_name,
            ];
        }

        return response()->json($result);
    }

    public function shortcutplatform(Request $request): JsonResponse
    {
        $dataPlatforms = CodeShortCutPlatform::select('id', 'name')->get();

        if (! empty($request->q)) {
            $dataPlatforms->where(function ($q) use ($request) {
                $q->where('subject', 'LIKE', '%'.$request->q.'%');
            });
        }

        $result = [];

        if (empty($dataPlatforms)) {
            $result['items'][] = [
                'id' => '',
                'text' => 'Supplier not available',
            ];
        } else {
            foreach ($dataPlatforms as $dataPlatform) {
                $result['items'][] = [
                    'id' => $dataPlatform->id,
                    'text' => $dataPlatform->name,
                ];
            }
        }

        return response()->json($result);
    }

    public function shortcutSuppliers(Request $request): JsonResponse
    {
        $dataSuppliers = Supplier::select('id', 'supplier')->get();

        if (! empty($request->q)) {
            $dataSuppliers->where(function ($q) use ($request) {
                $q->where('subject', 'LIKE', '%'.$request->q.'%');
            });
        }

        $result = [];

        if (empty($dataSuppliers)) {
            $result['items'][] = [
                'id' => '',
                'text' => 'Supplier not available',
            ];
        } else {
            foreach ($dataSuppliers as $dataSupplier) {
                $result['items'][] = [
                    'id' => $dataSupplier->id,
                    'text' => $dataSupplier->supplier,
                ];
            }
        }

        return response()->json($result);
    }

    public function shortcutFolders(Request $request): JsonResponse
    {
        $dataFolderNames = CodeShortcutFolder::select('id', 'name')->get();

        if (! empty($request->q)) {
            $dataFolderNames->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%'.$request->q.'%');
            });
        }

        $result = [];

        if (empty($dataFolderNames)) {
            $result['items'][] = [
                'id' => '',
                'text' => 'FolderName not available',
            ];
        } else {
            foreach ($dataFolderNames as $dataFolderName) {
                $result['items'][] = [
                    'id' => $dataFolderName->id,
                    'text' => $dataFolderName->name,
                ];
            }
        }

        return response()->json($result);
    }

    public function productColors(Request $request): JsonResponse
    {
        $uniqueColorsQuery = ProductSupplier::distinct('color');

        if (! empty($request->q)) {
            $uniqueColorsQuery->where('color', 'LIKE', '%'.$request->q.'%');
        }

        $uniqueColors = $uniqueColorsQuery->pluck('color');

        $result = [];

        if ($uniqueColors->isEmpty()) {
            $result['items'][] = [
                'id' => '',
                'text' => 'Supplier not available',
            ];
        } else {
            foreach ($uniqueColors as $uniqueColor) {
                $result['items'][] = [
                    'id' => $uniqueColor,
                    'text' => $uniqueColor,
                ];
            }
        }

        return response()->json($result);
    }

    public function producsizeSystem(Request $request): JsonResponse
    {
        $uniqueSizeQuery = ProductSupplier::distinct('size_system');

        if (! empty($request->q)) {
            $uniqueSizeQuery->where('size_system', 'LIKE', '%'.$request->q.'%');
        }

        $uniqueSizeSystems = $uniqueSizeQuery->pluck('size_system');

        $result = [];

        if ($uniqueSizeSystems->isEmpty()) {
            $result['items'][] = [
                'id' => '',
                'text' => 'Supplier not available',
            ];
        } else {
            foreach ($uniqueSizeSystems as $uniqueSizeSystem) {
                $result['items'][] = [
                    'id' => $uniqueSizeSystem,
                    'text' => $uniqueSizeSystem,
                ];
            }
        }

        return response()->json($result);
    }

    public function shortcutdocumentCategory(Request $request): JsonResponse
    {
        $categories = DocumentCategory::select('id', 'name')->get();

        if (! empty($request->q)) {
            $categories->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%'.$request->q.'%');
            });
        }

        $result = [];

        if (empty($categories)) {
            $result['items'][] = [
                'id' => '',
                'text' => 'category not available',
            ];
        } else {
            foreach ($categories as $category) {
                $result['items'][] = [
                    'id' => $category->id,
                    'text' => $category->name,
                ];
            }
        }

        return response()->json($result);
    }

    public function vochuerPlatform(Request $request): JsonResponse
    {
        $query = VoucherCouponPlatform::select();

        if (! empty($request->q)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%'.$request->q.'%');
            });
        }

        $platforms = $query->pluck('name', 'id');

        $result = [];

        if (empty($platforms)) {
            $result['items'][] = [
                'id' => '',
                'text' => 'platforms not available',
            ];
        } else {
            foreach ($platforms as $key => $plat) {
                $result['items'][] = [
                    'id' => $key,
                    'text' => $plat,
                ];
            }
        }

        return response()->json($result);
    }

    public function vochuerEmail(Request $request): JsonResponse
    {
        $query = EmailAddress::select();

        if (! empty($request->q)) {
            $query->where(function ($q) use ($request) {
                $q->where('from_address', 'LIKE', '%'.$request->q.'%');
            });
        }

        $vocherEmails = $query->pluck('id', 'from_address');

        $result = [];

        if (empty($vocherEmails)) {
            $result['items'][] = [
                'id' => '',
                'text' => 'Emails not available',
            ];
        } else {
            foreach ($vocherEmails as $key => $email) {
                $result['items'][] = [
                    'id' => $email,
                    'text' => $key,
                ];
            }
        }

        return response()->json($result);
    }

    public function vochuerWhatsappconfig(Request $request): JsonResponse
    {
        $query = WhatsappConfig::select();

        if (! empty($request->q)) {
            $query->where(function ($q) use ($request) {
                $q->where('number', 'LIKE', '%'.$request->q.'%');
            });
        }

        $whatsapp_configs = $query->pluck('number', 'id');

        $result = [];

        if (empty($whatsapp_configs)) {
            $result['items'][] = [
                'id' => '',
                'text' => 'Whatsapp number not available',
            ];
        } else {
            foreach ($whatsapp_configs as $key => $number) {
                $result['items'][] = [
                    'id' => $key,
                    'text' => $number,
                ];
            }
        }

        return response()->json($result);
    }

    public function magentoCreateFromCategory(Request $request): JsonResponse
    {
        $magnetoCategories = SiteDevelopmentCategory::wherenotNull('title');

        if (! empty($request->q)) {
            $magnetoCategories->where('title', 'LIKE', '%'.$request->q.'%');
        }

        $magnetoCategories = $magnetoCategories->pluck('title', 'id');
        $result = [];

        if ($magnetoCategories->isEmpty()) {
            $result['items'][] = [
                'id' => '',
                'text' => 'Categories not available',
            ];
        } else {
            foreach ($magnetoCategories as $key => $title) {
                $result['items'][] = [
                    'id' => $key,
                    'text' => $title,
                ];
            }
        }

        return response()->json($result);
    }
}
