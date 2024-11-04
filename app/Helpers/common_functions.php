<?php

use App\Brand;
use App\Category;
use App\Customer;
use App\DeveloperTask;
use App\EmailAddress;
use App\EmailLog;
use App\ErpLog;
use App\Events\AppointmentFound;
use App\MagentoCommandRunLog;
use App\Models\AppointmentRequest;
use App\Models\GoogleAdsLog;
use App\Models\OrderPurchaseProductStatus;
use App\Models\PurchaseProductOrderStatus;
use App\Models\ScriptsExecutionHistory;
use App\OrderProduct;
use App\Product;
use App\ProductSupplier;
use App\ResourceCategory;
use App\Routes;
use App\ScrapedProducts;
use App\scraperImags;
use App\Services\CommonGoogleTranslateService;
use App\SiteDevelopment;
use App\StoreWebsite;
use App\Supplier;
use App\SupplierDiscountInfo;
use App\Task;
use App\UserFeedbackCategorySopHistoryComment;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Marketing\WhatsappConfig;

function logMagentoCommandRun($magCom, $website, $response, $request = [])
{
    MagentoCommandRunLog::create([
        'command_id' => $magCom->id,
        'user_id' => Auth::user()->id ?? '',
        'website_ids' => $website->id ?? $magCom->website_ids,
        'command_name' => $magCom->command_type,
        'server_ip' => $website->server_ip ?? '',
        'command_type' => $magCom->command_type,
        'response' => $response,
        'request' => json_encode($request),
    ]);
}

function changeTimeZone($dateString, $timeZoneSource = null, $timeZoneTarget = null)
{
    if (empty($timeZoneSource)) {
        $timeZoneSource = config('app.timezone');
    }
    if (empty($timeZoneTarget)) {
        $timeZoneTarget = config('app.timezone');
    }

    $dt = Carbon::createFromFormat('Y-m-d H:i:s', $dateString, $timeZoneSource);
    $dt->setTimezone($timeZoneTarget);

    return $dt->format('Y-m-d H:i:s');
}

function get_translation($target, $text)
{
    $googleTranslateStichoza = new CommonGoogleTranslateService;
    $translatedText = $googleTranslateStichoza->translate($target, $text);

    return $translatedText;
}

/**
 * Create image and text
 *
 * @param  mixed  $path
 * @param  mixed  $uploadPath
 * @param  mixed  $text
 * @param  mixed  $color
 * @param  mixed  $fontSize
 * @param  mixed  $needAbs
 */
function createProductTextImage($path, $uploadPath = '', $text = '', $color = '545b62', $fontSize = '40', $needAbs = true)
{
    $text = wordwrap(strtoupper($text), 24, "\n");

    $img = \IImage::make($path);
    $img->resize(600, null, function ($constraint) {
        $constraint->aspectRatio();
    });
    // use callback to define details
    $img->text($text, 5, 50, function ($font) use ($fontSize, $color) {
        $font->file(public_path('/fonts/HelveticaNeue.ttf'));
        $font->size($fontSize);
        $font->color('#'.$color);
        $font->align('top');
    });

    $name = round(microtime(true) * 1000).'_watermarked';

    if (! file_exists(public_path('uploads'.DIRECTORY_SEPARATOR.$uploadPath.DIRECTORY_SEPARATOR))) {
        mkdir(public_path('uploads'.DIRECTORY_SEPARATOR.$uploadPath.DIRECTORY_SEPARATOR), 0777, true);
    }

    $path = 'uploads'.DIRECTORY_SEPARATOR.$uploadPath.DIRECTORY_SEPARATOR.$name.'.jpg';

    $img->save(public_path($path));

    return ($needAbs) ? public_path($path) : url('/').'/'.$path;
}

function get_folder_number($id)
{
    return floor($id / config('constants.image_per_folder'));
}

function previous_sibling(array $elements, $previous_sibling = 0, &$branch = [])
{
    foreach ($elements as $k => $element) {
        if ($element['previous_sibling'] == $previous_sibling && $previous_sibling != 0) {
            $branch[] = $element;
            previous_sibling($elements, $element['id'], $branch);
        }
    }

    return $branch;
}

/**
 * return all types of short message with postfix
 *
 * @param  mixed  $message
 * @param  mixed  $size
 * @param  mixed  $postfix
 */
function show_short_message($message, $size = 50, $postfix = '...')
{
    $message = trim($message);

    $dot = '';

    if (Str::length($message) > $size) {
        $dot = $postfix;
    }

    return Str::substr($message, 0, $size).$dot;
}

/**
 * key is using for to attach customer via session
 */
function attach_customer_key()
{
    return 'customer_list_'.time().'_'.auth()->user()->id;
}

/**
 *  get scraper last log file name
 *
 * @param  mixed  $screaperName
 * @param  mixed  $serverId
 */
function get_server_last_log_file($screaperName = '', $serverId = '')
{
    $d = date('j', strtotime('-1 days'));

    return '/scrap-logs/file-view/'.$screaperName.'-'.$d.'.log/'.$serverId;
}

function getStartAndEndDate($week, $year)
{
    $dto = Carbon::now();
    $dto->setISODate($year, $week);
    $ret['week_start'] = $dto->format('Y-m-d');
    $dto->addDays(6);
    $ret['week_end'] = $dto->format('Y-m-d');

    return $ret;
}

/**
 * Moved function from chat api to here due to duplicates
 */
if (! function_exists('getInstance')) {
    function getInstance($number, $uploadPath = '', $color = '545b62')
    {
        $text = wordwrap(strtoupper($text), 24, "\n");

        $img = \IImage::make($path);
        $img->resize(600, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        // use callback to define details
        $img->text($text, 5, 50, function ($font) use ($fontSize, $color) {
            $font->file(public_path('/fonts/HelveticaNeue.ttf'));
            $font->size($fontSize);
            $font->color('#'.$color);
            $font->align('top');
        });

        $name = round(microtime(true) * 1000).'_watermarked';

        if (! file_exists(public_path('uploads'.DIRECTORY_SEPARATOR.$uploadPath.DIRECTORY_SEPARATOR))) {
            mkdir(public_path('uploads'.DIRECTORY_SEPARATOR.$uploadPath.DIRECTORY_SEPARATOR), 0777, true);
        }

        $path = 'uploads'.DIRECTORY_SEPARATOR.$uploadPath.DIRECTORY_SEPARATOR.$name.'.jpg';

        $img->save(public_path($path));

        return url('/').'/'.$path;
    }
}

function human_error_array($errors)
{
    $list = [];
    if (! empty($errors)) {
        foreach ($errors as $key => $berror) {
            foreach ($berror as $serror) {
                $list[] = "{$key} : ".$serror;
            }
        }
    }

    return $list;
}

/**
 * Get all instances no with array list
 */
function getInstanceNo()
{
    $nos = WhatsappConfig::getWhatsappConfigs();

    $list = [];

    if (! empty($nos)) {
        foreach ($nos as $key => $no) {
            $n = ($key == 0) ? $no['number'] : $key;
            $list[$n] = $n;
        }
    }

    return $list;
}

/**
 * Check if the date is valid
 *
 * @param  mixed  $date
 * @param  mixed  $format
 */
function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);

    return $d && $d->format($format) === $date;
}

/**
 * dropdown returns in helpers
 */
function drop_down_frequency()
{
    return [
        '0' => 'Disabled',
        '1' => 'Just Once',
        '5' => 'Every 5 Minutes',
        '10' => 'Every 10 Minutes',
        '15' => 'Every 15 Minutes',
        '20' => 'Every 20 Minutes',
        '25' => 'Every 25 Minutes',
        '30' => 'Every 30 Minutes',
        '35' => 'Every 35 Minutes',
        '40' => 'Every 40 Minutes',
        '45' => 'Every 45 Minutes',
        '50' => 'Every 50 Minutes',
        '55' => 'Every 55 Minutes',
        '60' => 'Every Hour',
        '360' => 'Every 6 hr',
        '1440' => 'Every 24 hr',
    ];
}

/**
 * format the duration in Hour:minute:seconds format
 *
 * @param  mixed  $seconds_time
 */
function formatDuration($seconds_time)
{
    if ($seconds_time < 24 * 60 * 60) {
        return gmdate('H:i:s', $seconds_time);
    } else {
        $hours = floor($seconds_time / 3600);
        $minutes = floor(($seconds_time - $hours * 3600) / 60);
        $seconds = floor($seconds_time - ($hours * 3600) - ($minutes * 60));

        return "$hours:$minutes:$seconds";
    }
}

function get_field_by_number($no, $field = 'name')
{
    $no = explode('@', $no);

    if (! empty($no[0])) {
        $customer = Customer::where('phone', $no[0])->first();
        if ($customer) {
            return $customer->{$field}.' (Customer)';
        }

        $vendor = Vendor::where('phone', $no[0])->first();
        if ($vendor) {
            return $vendor->{$field}.' (Vendor)';
        }

        $supplier = Supplier::where('phone', $no[0])->first();
        if ($supplier) {
            return $supplier->{$field}.'(Supplier)';
        }
    }

    return '';
}

function splitTextIntoSentences($text)
{
    return preg_split('/(?<=[.?!])\s+(?=[a-z])/i', $text);
}

function isJson($string)
{
    json_decode($string);

    return json_last_error() == JSON_ERROR_NONE;
}

function array_find($needle, array $haystack)
{
    foreach ($haystack as $key => $value) {
        if (stripos($value, $needle) !== false) {
            return true;
        }
    }

    return false;
}

function GUID()
{
    if (function_exists('com_create_guid') === true) {
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

function replace_dash($string)
{
    $string = str_replace(' ', '_', strtolower($string)); // Replaces all spaces with hyphens.
    $string = str_replace('-', '_', strtolower($string)); // Replaces all spaces with hyphens.

    return preg_replace('/\s+/', '_', strtolower($string));
}

function replaceSpaceWithDash($string)
{
    $string = str_replace(' ', '-', strtolower($string)); // Replaces all spaces with hyphens.

    return preg_replace('/\s+/', '-', strtolower($string));
}

function storeERPLog($erpData)
{
    if (! empty($erpData)) {
        $erpData['request'] = json_encode($erpData['request']);
        $erpData['response'] = json_encode($erpData['response']);
        ErpLog::create($erpData);
    }
}

function getStr($srt)
{
    preg_match("/\[(.*)\]/", $srt, $matches);
    if ($matches && $matches[0] !== '') {
        return true;
    }

    return false;
}

function string_convert($msg2)
{
    return explode('||', $msg2);
}

function convertToThumbUrl($url, $extension)
{
    $arr = explode('/', $url);
    $arr[count($arr) - 1] = 'thumbnail/'.$arr[count($arr) - 1];

    $converted_str = implode('/', $arr);

    return str_replace('.'.$extension, '_thumb.'.$extension, $converted_str); // if product name is abc.jpg than thumb url name is abc_thumb.jpg name with in /thumbnaiil folder of relateable folder path.
}

function resizeCropImage($max_width, $max_height, $source_file, $dst_dir = null, $quality = 80)
{
    if ($dst_dir === null) {
        $dst_dir = $source_file;
    }
    $imgsize = getimagesize($source_file);
    $width = $imgsize[0];
    $height = $imgsize[1];
    $mime = $imgsize['mime'];

    switch ($mime) {
        case 'image/gif':
            $image_create = 'imagecreatefromgif';
            $image = 'imagegif';
            break;

        case 'image/png':
            $image_create = 'imagecreatefrompng';
            $image = 'imagepng';
            $quality = 7;
            break;

        case 'image/jpeg':
            $image_create = 'imagecreatefromjpeg';
            $image = 'imagejpeg';
            $quality = 80;
            break;

        default:
            return false;
            break;
    }

    $dst_img = imagecreatetruecolor($max_width, $max_height);
    $src_img = $image_create($source_file);

    imagealphablending($dst_img, false);
    imagesavealpha($dst_img, true);

    $width_new = round($height * $max_width / $max_height);
    $height_new = round($width * $max_height / $max_width);
    //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
    if ($width_new > $width) {
        //cut point by height
        $h_point = (($height - $height_new) / 2);
        //copy image
        $imagecopyresampled = imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
        // return true;
    } else {
        //cut point by width
        $w_point = (($width - $width_new) / 2);
        $imagecopyresampled = imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
        // return true;
    }
    $image($dst_img, $dst_dir, $quality);

    if ($dst_img) {
        $imagedestroy = imagedestroy($dst_img);
    }

    if ($src_img) {
        $imagedestroy = imagedestroy($src_img);
    }

    return @file_get_contents($dst_dir);
}

function _p($data, $exit = 0)
{
    print_r($data);
    if ($exit) {
        exit('');
    }
}

function _pq($q, $exit = 0)
{
    print_r($q->toSql());
    print_r($q->getBindings());
    if ($exit) {
        exit('');
    }
}

function dateRangeArr($stDate, $enDate)
{
    $data = [];
    while ($stDate <= $enDate) {
        $data[] = [
            'date' => $stDate,
            'day' => strtolower(date('l', strtotime($stDate))),
        ];
        $stDate = date('Y-m-d', strtotime($stDate.'+1 day'));
    }

    return $data;
}

function pad0($curr)
{
    return $curr < 10 ? '0'.$curr : $curr;
}

function nextHour($curr)
{
    $curr++;
    if ($curr == 24) {
        $curr = '0';
    }

    return $curr < 10 ? '0'.$curr : $curr;
}

function hourlySlots($stTime, $enTime, $lunchTime = null)
{
    $slots = [];
    if ($stTime < $enTime) {
        $stTime = date('Y-m-d H:i:00', strtotime($stTime));
        $enTime = date('Y-m-d H:i:00', strtotime($enTime));
    } else {
        $stTime = date('Y-m-d H:i:00', strtotime($stTime));
        $enTime = date('Y-m-d H:i:00', strtotime($enTime.' + 1 day'));
    }

    if ($stTime >= $lunchTime && $lunchTime <= $enTime) {
        if ($lunchTime < date('H:i:00', strtotime($stTime))) {
            $lunchTime = date('Y-m-d H:i:00', strtotime($lunchTime.' + 1 day'));
        } else {
            $lunchTime = date('Y-m-d H:i:00', strtotime($lunchTime));
        }
    }

    if ($lunchTime && ($stTime <= $lunchTime && $lunchTime <= $enTime)) {
        $stTime1 = $stTime;
        $enTime1 = date('Y-m-d H:i:00', strtotime($lunchTime));
        $slots = array_merge_recursive($slots, hourlySlots($stTime1, $enTime1));
        $stTime = date('Y-m-d H:i:00', strtotime($lunchTime.' +1 hour'));

        $temp = hourlySlots($lunchTime, $stTime);
        foreach ($temp as $key => $value) {
            $temp[$key]['type'] = 'LUNCH';
        }
        $slots = array_merge_recursive($slots, $temp);

        $slots = array_merge_recursive($slots, hourlySlots($stTime, $enTime));
    } else {
        while ($stTime < $enTime) {
            $stSlot = $stTime;
            $enSlot = date('Y-m-d H:i:00', strtotime($stSlot.' +1 hour'));
            if ($enSlot > $enTime) {
                $enSlot = $enTime;
            }
            $diff = strtotime($enSlot) - strtotime($stSlot);
            $slots[] = [
                'st' => $stSlot,
                'en' => $enSlot,
                'mins' => round($diff / 60),
                'type' => 'AVL',
            ];
            // hourlySlots
            $stTime = date('Y-m-d H:i:00', strtotime($stTime.' +1 hour'));
        }
    }

    return $slots;
}

function getHourlySlots($stTime, $enTime)
{
    $return = [];
    if (date('Y-m-d', strtotime($stTime)) != date('Y-m-d', strtotime($enTime))) {
        $st1 = $stTime;
        $en1 = date('Y-m-d 23:59:59', strtotime($stTime));
        $return = array_merge_recursive($return, getHourlySlots($st1, $en1));

        $st1 = date('Y-m-d 00:00:00', strtotime($enTime));
        $en1 = $enTime;
        $return = array_merge_recursive($return, getHourlySlots($st1, $en1));
    } else {
        while ($stTime < $enTime) {
            $stSlot = $stTime;
            $enSlot = date('Y-m-d H:i:00', strtotime($stSlot.' +1 hour'));
            if ($enSlot > $enTime) {
                $enSlot = $enTime;
            }
            $enSlot = date('Y-m-d H:i:00', strtotime($enSlot.' -1 minute'));
            $return[] = [
                'st' => $stSlot,
                'en' => $enSlot,
                'mins' => round((strtotime($enSlot) - strtotime($stSlot)) / 60),
            ];
            $stTime = date('Y-m-d H:i:00', strtotime($stTime.' +1 hour'));
        }
    }

    return $return;
}

function siteJs($path)
{
    return config('app.url').'/js/pages/'.$path.'?v='.date('YmdH');
}

function makeDropdown($options = [], $selected = [], $keyValue = 1)
{
    if (! is_array($selected)) {
        $selected = is_numeric($selected) ? (int) $selected : $selected;
    }
    $return = [];
    if (count($options)) {
        foreach ($options as $k => $v) {
            if (is_array($v)) {
                $return[] = '<optgroup label="'.$k.'">';
                $return[] = makeDropdown($v, $selected);
                $return[] = '</optgroup>';
            } else {
                $value = $keyValue ? $k : $v;
                $sel = '';
                if (is_array($selected)) {
                    if (in_array($value, $selected)) {
                        $sel = 'selected';
                    }
                } elseif ($selected === $value) {
                    $sel = 'selected';
                }
                $return[] = '<option value="'.$value.'" '.$sel.'>'.trim(strip_tags($v)).'</option>';
            }
        }
    }

    return implode('', $return);
}

function exMessage($e)
{
    return 'Error on line '.$e->getLine().' in '.$e->getFile().': '.$e->getMessage();
}

function respException($e, $data = [])
{
    return response()->json(array_merge_recursive(['message' => exMessage($e)], $data), 500);
}

function isDeveloperTaskId($id)
{
    return substr($id, 0, 3) == 'DT-' ? str_replace('DT-', '', $id) : 0;
}

function isRegularTaskId($id)
{
    return substr($id, 0, 2) == 'T-' ? str_replace('T-', '', $id) : 0;
}

function respJson($code, $message = '', $data = [])
{
    return response()->json(array_merge_recursive(['message' => $message], $data), $code);
}

function dailyHours($type = null)
{
    $data = [];
    for ($i = 0; $i < 24; $i++) {
        $temp = pad0($i).':00:00';
        $data[$temp] = $temp;
    }

    return $data;
}

function reqValidate($data, $rules = [], $messages = [])
{
    $validator = Validator::make($data, $rules, $messages);

    return $validator->errors()->all();
}

function loginId()
{
    return Auth::id() ?: 0;
}

function isAdmin()
{
    $user = auth()->user();

    return $user && $user->isAdmin();
}

function printNum($num)
{
    return number_format($num, 2, '.', ',');
}

function readFullFolders($dir, &$results = [])
{
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if (! is_dir($path)) {
            $results[] = $path;
        } elseif ($value != '.' && $value != '..') {
            readFullFolders($path, $results);
        }
    }

    return $results;
}

function readFolders($data)
{
    $return = [];
    foreach ($data as $key => $filePath) {
        $fileName = basename($filePath);
        $return[] = rtrim(str_replace($fileName, '', $filePath), '/');
    }
    $return = array_values(array_unique($return));
    sort($return);

    return $return;
}

function getCommunicationData($sdc, $sw)
{
    $site_dev = SiteDevelopment::where(['site_development_category_id' => $sdc->id, 'website_id' => $sw->id])->orderByDesc('id')->get()->pluck('id');
    $query = DeveloperTask::join('users', 'users.id', 'developer_tasks.assigned_to')->whereIn('site_developement_id', $site_dev)->where('status', '!=', 'Done')->select('developer_tasks.id', 'developer_tasks.task as subject', 'developer_tasks.status', 'users.name as assigned_to_name');
    $query = $query->addSelect(DB::raw("'Devtask' as task_type,'developer_task' as message_type"));
    $taskStatistics = $query->orderByDesc('developer_tasks.id')->get();
    $query1 = Task::join('users', 'users.id', 'tasks.assign_to')->whereIn('site_developement_id', $site_dev)->whereNull('is_completed')->select('tasks.id', 'tasks.task_subject as subject', 'tasks.assign_status', 'users.name as assigned_to_name');
    $query1 = $query1->addSelect(DB::raw("'Othertask' as task_type,'task' as message_type"));
    $othertaskStatistics = $query1->orderByDesc('tasks.id')->get();
    $merged = $othertaskStatistics->merge($taskStatistics);

    return $merged;
}

function insertGoogleAdsLog($input)
{
    if (is_array($input)) {
        $input['user_id'] = auth()->id();
        $input['user_ip_address'] = request()->ip();

        GoogleAdsLog::create($input);
    }

    return true;
}

function getMediaUrl($media)
{
    if ($media->disk == 's3') {
        return $media->getTemporaryUrl(Carbon::now()->addMinutes(config('constants.temporary_url_expiry_time')));
    } else {
        return $media->getUrl();
    }
}

function checkCurrentUriIsEnableForEmailAlert($uri)
{
    return Routes::where(['url' => $uri, 'email_alert' => 1])->exists();
}

function replaceLinksWithAncherTags($text, $color)
{
    $pattern = '/(\w+\:\/\/[^\s]+)/i';
    preg_match_all($pattern, $text, $matches);

    return preg_replace($pattern, "<a style='color: $color' href='$1'>$1</a>", $text);
}

function getAppointments($userId = false)
{
    $currentDateTimeFormatted = Carbon::now()->format('Y-m-d H:i:s');

    $newAppointments = AppointmentRequest::with([
        'user' => function ($query) {
            $query->select('id', 'name');
        }])
        ->where('requested_time', '<=', $currentDateTimeFormatted)
        ->where('requested_time_end', '>=', $currentDateTimeFormatted)
        ->where('request_status', 0)
        // ->when($userId, fn ($q) => $q->where('user_id', $userId)->orWhere('requested_user_id', $userId))
        ->orderByDesc('id')
        ->groupBy(['requested_user_id'])
        ->distinct('requested_user_id')
        ->get();

    $reactedUnseenAppointments = AppointmentRequest::with([
        'userrequest' => function ($query) {
            $query->select('id', 'name');
        }])
        ->where('request_status', '!=', 0)
        ->where('is_view', 0)
        // ->when($userId, fn ($q) => $q->where('user_id', $userId)->orWhere('requested_user_id', $userId))
        ->orderByDesc('id')
        ->groupBy(['user_id'])
        ->distinct('user_id')
        ->get();

    $userAppointments = [];

    $newAppointments->each(function ($appointment) use (&$userAppointments) {
        $userId = $appointment->requested_user_id;

        if (! isset($userAppointments[$userId]['newAppointments'])) {
            $userAppointments[$userId]['newAppointments'] = [];
        }

        $userAppointments[$userId]['newAppointments'][] = $appointment;
        $userAppointments[$userId]['userId'] = $userId;
    });

    $reactedUnseenAppointments->each(function ($appointment) use (&$userAppointments) {
        $userId = $appointment->user_id;

        if (! isset($userAppointments[$userId]['reactedUnseenAppointments'])) {
            $userAppointments[$userId]['reactedUnseenAppointments'] = [];
        }

        $userAppointments[$userId]['reactedUnseenAppointments'][] = $appointment;
        $userAppointments[$userId]['userId'] = $userId;
    });

    return $userAppointments;
}

function broadcastAppointments($userAppointments)
{
    foreach ($userAppointments as $appointment) {
        event(new AppointmentFound($appointment));
    }
}

function UTCToLocal($dateTime, $format = 'M d Y')
{
    return Carbon::parse($dateTime, 'UTC')->timezone(config('timezone'))->format($format);
}

function checkUser($link)
{
    //Check if user is Admin
    $authcheck = auth()->user()->isAdmin();
    //Return True if user is Admin
    if ($authcheck) {
        return true;
    }
    //Check User Role and Permission
    $permission_check = auth()->user()->hasPermission($link);
    //Return True If User Has Role
    if ($permission_check) {
        return true;
    }

    //Return False When user doesnt have permission
    return false;
}

function parseSemrushResponse($response)
{
    $response1 = explode("\n", $response);
    $final = [];
    foreach ($response1 as $new) {
        $new = explode(';', $new);
        $final[] = $new;
    }

    return json_encode($final);
}

function websiteName($websiteId)
{
    return StoreWebsite::where('id', $websiteId)->pluck('website')->first();
}

function isCurrentUriIsEnableForEmailAlert()
{
    $currentRoutes = Route::current();
    $checkCurrentUriEnabled = checkCurrentUriIsEnableForEmailAlert($currentRoutes->uri);

    return $checkCurrentUriEnabled;
}

function getScriptsExecutionHistory()
{
    $records = ScriptsExecutionHistory::select('*', DB::raw('MAX(id) AS id'))->where('run_status', 'Failed')->orderByDesc('id');
    $records = $records->groupBy('script_document_id')->get();

    return $records;
}

function createEmailAlertLog($email)
{
    EmailLog::create(
        [
            'email_id' => $email->id,
            'email_log' => 'Email alert available',
            'message' => 'Email alert for: '.$email->to.' from: '.$email->from,
            'is_error' => 0,
            'source' => EmailLog::EMAIL_ALERT,
        ]
    );
}

function generateTreeView($tree)
{
    $html = '<ul>';
    foreach ($tree as $node) {
        $html .= '<li>'.htmlspecialchars($node->title);
        if (isset($node->children) && is_array($node->children)) {
            $html .= generateTreeView($node->children);
        }
        $html .= '</li>';
    }
    $html .= '</ul>';

    return $html;
}

function getSupplierDiscountInfo($orderSupplierDiscountInfoId)
{
    return SupplierDiscountInfo::join('suppliers', 'suppliers.id', 'supplier_discount_infos.supplier_id')->where('supplier_discount_infos.id', $orderSupplierDiscountInfoId)->select(['suppliers.*'])->get();
}

function getSupplierDiscountInfoByProductAndSupplier($supplierProductId, $supplierId)
{
    return SupplierDiscountInfo::where('product_id', $supplierProductId)->where('supplier_id', $supplierId)->first();
}

function getProductSupplier($orderProductId)
{
    return ProductSupplier::join('suppliers', 'suppliers.id', 'product_suppliers.supplier_id')->where('product_suppliers.product_id', $orderProductId)->select(['suppliers.*', 'product_suppliers.supplier_link'])->get();
}
function getOrderPurchaseProductStatus($orderPurchaseProductStatusId)
{
    return OrderPurchaseProductStatus::where('id', $orderPurchaseProductStatusId)->first();
}

function getOrderProduct($orderProductId)
{
    return OrderProduct::find($orderProductId);
}
function getPurchaseProductOrderStatus($purchaseStatus)
{
    return PurchaseProductOrderStatus::where('status_alias', $purchaseStatus)->first();
}

function getScrappedProduct($productSku)
{
    return ScrapedProducts::from('scraped_products as sp')
        ->select('sp.id', 's.website', 'sp.url', 's.supplier')
        ->join('scrapers AS sc', 'sc.scraper_name', '=', 'sp.website')
        ->join('suppliers AS s', 's.id', '=', 'sc.supplier_id')
        ->where('sp.last_inventory_at', '>', DB::raw('DATE_SUB(NOW(), INTERVAL sc.inventory_lifetime DAY)'))
        ->where('sp.sku', $productSku)
        ->get();
}

function getSupplierArr($product)
{
    return Supplier::from('suppliers')
        ->select('suppliers.id', 'suppliers.supplier', 'ps.product_id', 'suppliers.website')
        ->join('product_suppliers as ps', function ($join) use ($product) {
            $join->on('suppliers.id', '=', 'ps.supplier_id')
                ->where('ps.product_id', '=', $product['id']);
        })
        ->leftJoin('purchase_product_supplier', function ($join) {
            $join->on('purchase_product_supplier.supplier_id', '=', 'suppliers.id')
                ->on('purchase_product_supplier.product_id', '=', 'ps.product_id');
        })
        ->get();
}

function getProduct($productId)
{
    return Product::find($productId);
}

function getEmailAddress()
{
    return EmailAddress::all();
}

function getAllStoreWebsite()
{
    return StoreWebsite::all();
}

function getLastUserFeedbackSopHistoryCommentBySopHistoryId($sopHistoryId)
{
    return UserFeedbackCategorySopHistoryComment::select('comment', 'id')->where('sop_history_id', $sopHistoryId)->whereNotNull('sop_history_id')->orderBy('id', 'DESC')->first();
}

// function getResourceCategory()
// {
//     return ResourceCategory::where('parent_id', 0)->get();
// }

function getCategory($categoryId)
{
    return Category::where('id', $categoryId)->value('title');
}

function getCustomerById($custId)
{
    return Customer::find($custId);
}

function getTaskCustomer($taskCustomerId)
{
    return Customer::find($taskCustomerId);
}
function getTaskById($id)
{
    return Task::find($id);
}

function getSegmentPrice($brand_segment, $category)
{
    return Brand::getSegmentPrice($brand_segment, $category);
}
function getScraperImageByProdUrlAndId($productId, $productUrl)
{
    return scraperImags::where('id', '!=', $productId)->where('url', $productUrl)->first();
}
/**
 * Use this function for replace space by any sign
 * Use this function for replace underscore by any sign
 *
 * @param  type  $string
 * @param  type  $from
 * @param  type  $to
 * @return type
 */
function replace_by_sign($string, $from = '_', $to = '-')
{
    $result = str_replace($from, $to, strtolower(trim($string)));

    return preg_replace('/\s+/', $to, $result);
}

function getCloudPlateform()
{
    return [
        'aws' => 'AWS ECS',
        'do' => 'Digital Ocean Kubernates',
    ];
}
