<?php

namespace App\Http\Controllers;

use App\DeveloperTask;
use App\LogRequest;
use App\NotificationToken;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Exception;

class WebNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        return view('home');
    }

    public function storeToken(Request $request): JsonResponse
    {
        $token = $request->token;
        $user_id = auth()->user()->id;
        $isExist = NotificationToken::where('device_token', $token)->where('user_id', $user_id)->exists();
        if (! $isExist) {
            $notificationToken = new NotificationToken;
            $notificationToken->user_id = $user_id;
            $notificationToken->device_token = $token;
            $notificationToken->is_enabled = true;
            $notificationToken->save();
        }

        return response()->json(['Token successfully stored.']);
    }

    public static function sendWebNotification2($sendTo, $issue_id, $title, $body)
    {
        Log::info('Notification process start');
        Log::info('Send Notification Body: '.json_encode(['sendTo' => $sendTo, 'issue_id' => $issue_id, 'title' => $title, 'body' => $body]));
        $url = 'https://fcm.googleapis.com/fcm/send';
        $issue = DeveloperTask::find($issue_id);
        $userId = $issue->assigned_to;
        $users = User::get();
        Log::info('User from assign id--->'.json_encode($users));
        $adminIds = [];
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);

        foreach ($users as $user) {
            if ($user->isAdmin()) {
                $adminIds[] = $user->id;
                Log::info('if user is Admin--->'.json_encode($user));
            }
        }
        if ($sendTo == 'to_developer') {
            $userId = $issue->assigned_to;
            Log::info('if send to developer--->'.json_encode($userId));
        }
        if ($sendTo == 'to_master') {
            if ($issue->master_user_id) {
                $userId = $issue->master_user_id;
                Log::info('if send to master--->'.json_encode($userId));
            }
        }

        if ($sendTo == 'to_team_lead') {
            if ($issue->team_lead_id) {
                $userId = $issue->team_lead_id;
                Log::info('if send to team lead--->'.json_encode($userId));
            }
        }

        if ($sendTo == 'to_tester') {
            if ($issue->tester_id) {
                $userId = $issue->tester_id;
                Log::info('if send to tester--->'.json_encode($userId));
            }
        }
        if (isset($userId) && $userId) {
            Log::info('all user Ids--->'.json_encode($userId));
            $adminIds[] = $userId;
            Log::info('all admin ids after adding userId to it--->'.json_encode($adminIds));
            $adminIds = array_unique($adminIds);
            if (($key = array_search(Auth::User()->id, $adminIds)) !== false) {
                unset($adminIds[$key]);
            }
            Log::info('Distinct Users ids to send notification -->'.json_encode($adminIds));
            $FcmToken = NotificationToken::whereNotNull('device_token')->whereIn('user_id', $adminIds)->pluck('device_token')->all();
        } else {
            Log::info('No user Id selected in else block  -->');
            $FcmToken = NotificationToken::whereNotNull('device_token')->pluck('device_token')->all();
        }
        $serverKey = config('settings.fcm_secret_key');
        Log::info('if send to developer--->'.json_encode($serverKey));
        $data = [
            'registration_ids' => $FcmToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
        ];
        $encodedData = json_encode($data);
        Log::info('Data object -->'.$encodedData);

        $headers = [
            'Authorization:key='.$serverKey,
            'Content-Type: application/json',
        ];
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            // Disabling SSL Certificate support temporarly
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
            // Execute post
            $result = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($result === false) {
                $error = 'Curl failed: '.curl_error($ch);
                Log::error($error);
                exit($error);
            }
            Log::info('Notification Process success -->'.json_encode($result));
            // Close connection
            curl_close($ch);
            LogRequest::log($startTime, $url, 'POST', json_encode($encodedData), json_decode($result), $httpcode, WebNotificationController::class, 'sendWebNotification2');

            // FCM response
            return;
        } catch (Exception $e) {
            Log::error('Error sending notification -->'.$e->getMessage());
        }
    }

    public static function sendBulkNotification($userId, $title, $body)
    {
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $url = 'https://fcm.googleapis.com/fcm/send';
        $FcmToken = NotificationToken::whereNotNull('device_token')->where('user_id', $userId)->pluck('device_token')->all();
        $serverKey = config('settings.fcm_secret_key');
        $data = [
            'registration_ids' => $FcmToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
        ];
        $encodedData = json_encode($data);

        $headers = [
            'Authorization:key='.$serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // Execute post
        $result = curl_exec($ch);
        if ($result === false) {
            exit('Curl failed: '.curl_error($ch));
        }
        // Close connection
        curl_close($ch);

        LogRequest::log($startTime, $url, 'POST', json_encode($encodedData), json_decode($result), $httpcode, WebNotificationController::class, 'sendWebNotification2');
        // FCM response
    }
}
