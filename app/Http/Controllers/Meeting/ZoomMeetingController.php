<?php
/**
 * Class ZoomMeetingController | app/Http/Controllers/Meeting/ZoomMeetingController.php
 * Zoom Meetings integration for video call purpose using LaravelZoom's REST API
 *
 * @filesource required php 7 as this file contains tokenizer extension which was not stable prior to this version
 *
 * @see https://github.com/saineshmamgain/laravel-zoom
 * @see ZoomMeetings
 *
 * @author   sololux <sololux@gmail.com>
 */

namespace App\Http\Controllers\Meeting;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WhatsAppController;
use App\Http\Requests\Meeting\CreateMeetingZoomMeetingRequest;
use App\Meetings\ZoomApiLog;
use App\Meetings\ZoomMeetingDetails;
use App\Meetings\ZoomMeetingParticipant;
use App\Meetings\ZoomMeetings;
use App\Models\ZoomMeetingHistory;
use App\Models\ZoomMeetingParticipantHistory;
use App\Models\ZoomMeetingRecordHistory;
use App\Setting;
use App\ZoomOAuthHelper;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use seo2websites\LaravelZoom\LaravelZoom;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class ZoomMeetingController - active record
 *
 * A zoom class used to create meetings
 * This class is used to interact with zoom interface.
 */
class ZoomMeetingController extends Controller
{
    /**
     * Constructor of class
     * Calling env variables and adding in scope
     */
    protected $zoomkey = '';

    protected $zoomsecret = '';

    protected $zoomuser = '';

    public function __construct()
    {
        $this->zoomkey = config('settings.zoom_api_key');
        $this->zoomsecret = config('settings.zoom_api_secret');
        $this->zoomuser = config('settings.zoom_user');
    }

    /**
     * Create a meeting with zoom based on the params send through form
     *
     * @param  Request  $request  Request
     *
     * @Rest\Post("twilio/token")
     *
     * @uses Auth
     * @uses ClientToken
     */
    public function createMeeting(CreateMeetingZoomMeetingRequest $request): JsonResponse
    {

        $input = $request->all();

        $startDate = strtotime(new Carbon($request->get('start_date_time', date('Y-m-d H:i', strtotime('+5 minutes')))));
        $currentDate = strtotime(Carbon::now());

        if ($startDate < $currentDate) {
            $data = ['msg' => 'Start date time should not be less than current date time.'];

            return response()->json([
                'success' => false,
                'data' => $data,
            ]);
        }

        $userId = $this->zoomuser;
        // Default settings for zoommeeting
        $settings = [
            'join_before_host' => true,
            'host_video' => true,
            'participant_video' => true,
            'mute_upon_entry' => false,
            'enforce_login' => false,
            'auto_recording' => 'cloud',
        ];

        // add default setting in meeting
        $input['start_date_time'] = date('Y-m-d H:i', $startDate);
        $input['meeting_duration'] = $request->get('meeting_duration', 5);
        $input['timezone'] = $request->get('timezone', 'Asia/Dubai');
        $input['meeting_agenda'] = $request->get('agenda', '');
        $input['timezone'] = ($input['timezone'] != '') ? $input['timezone'] : 'Asia/Dubai';
        // gethering all data to pass to model function
        $input['timezone'] = ($input['timezone'] != '') ? $input['timezone'] : 'Asia/Dubai';
        $data = [
            'user_id' => $userId,
            'topic' => $input['meeting_topic'],
            'agenda' => $input['meeting_agenda'],
            'settings' => $settings,
            'startTime' => new Carbon($input['start_date_time']),
            'duration' => $input['meeting_duration'],
            'timezone' => $input['timezone'],
        ];
        // Calling model calss
        $meetings = new ZoomMeetings;
        $zoomKey = $this->zoomkey;
        $zoomSecret = $this->zoomsecret;

        $createMeeting = $meetings->createMeeting($zoomKey, $zoomSecret, $data);
        if ($createMeeting) {
            $input['meeting_id'] = empty($createMeeting['body']['id']) ? '' : $createMeeting['body']['id'];
            $input['host_zoom_id'] = $userId;
            $input['meeting_type'] = 'scheduled';
            $input['join_meeting_url'] = empty($createMeeting['body']['join_url']) ? '' : $createMeeting['body']['join_url'];
            $input['start_meeting_url'] = empty($createMeeting['body']['start_url']) ? '' : $createMeeting['body']['start_url'];
            // saving data in db
            $createMeeting = ZoomMeetings::create($input);
            if ($createMeeting) {
                $getUserDetails = $meetings->getUserDetails($input['user_id'], $input['user_type']);
                if (! empty($getUserDetails)) {
                    $phonenumber = isset($getUserDetails->number) ? $getUserDetails->number : $getUserDetails->phone;
                    $msg = 'New meeting has been scheduled for you. Kindly find below the link to join the meeting. '.$input['join_meeting_url'];
                    if (! empty($phonenumber)) {
                        app(WhatsAppController::class)->sendWithThirdApi($phonenumber, $getUserDetails->whatsapp_number, $msg);
                    }
                    $email = $getUserDetails->email;
                    if (! empty($email)) {
                        if ($input['user_type'] == 'supplier') {
                            $getUserDetails->supplier;
                        } else {
                            $getUserDetails->name;
                        }
                    }
                }
                $data = ['msg' => 'New Meeting added successfully.', 'meeting_link' => $input['join_meeting_url'], 'start_meeting' => $input['start_meeting_url']];

                return response()->json([
                    'success' => true,
                    'data' => $data,
                ]);
            } else {
                $data = ['msg' => 'Token is expired. Please try to add the meeting again.'];

                return response()->json([
                    'success' => false,
                    'data' => $data,
                ]);
            }
        } else {
            $data = ['msg' => 'Meeting not added.'];

            return response()->json([
                'success' => false,
                'data' => $data,
            ]);
        }
    }

    public function getMeetings()
    {
        $zoomKey = $this->zoomkey;
        $zoomSecret = $this->zoomsecret;
        $zoom = new LaravelZoom($zoomKey, $zoomSecret);
        $zoom->getJWTToken(time() + 7200);
        $zoom->getUsers('active', 10);
        $user_id = '-ISK-roPRUyC3-3N5-AT_g';
        $topic = 'Test meeting using erp';
        $agenda = 'Communication with team';
        $startTime = Carbon::tomorrow();
        $duration = 40;
        $timezone = 'Asia/Kolkata';
        $settings = [
            'join_before_host' => true,
            'host_video' => true,
            'participant_video' => true,
            'mute_upon_entry' => false,
            'enforce_login' => false,
            'auto_recording' => 'local',
        ];

        $data = ['user_id' => $user_id, 'topic' => $topic, 'agenda' => $agenda, 'settings' => $settings, 'startTime' => $startTime, 'duration' => $duration, 'timezone' => $timezone, 'type' => 'all'];
        $meetings = new ZoomMeetings;
        $createMeet = $meetings->createMeeting($zoomKey, $zoomSecret, $data);
        echo 'hello';
        print_r($createMeet);
        exit;
        exit;
    }

    public function showData(Request $request): View
    {
        $type = $request->get('type');
        $meetings = new ZoomMeetings;
        $curDate = Carbon::now();
        $upcomingMeetings = $meetings->upcomingMeetings($type, $curDate);
        $pastMeetings = $meetings->pastMeetings($type, $curDate);

        return view('zoom-meetings.showdata', [
            'upcomingMeetings' => $upcomingMeetings,
            'pastMeetings' => $pastMeetings,
            'type' => $type,
        ]);
    }

    public function allMeetings(Request $request): View
    {
        $zoomMeetings = new ZoomMeetings;

        $zoomMeetingIds = ZoomMeetings::distinct()->pluck('meeting_id');
        $timeZones = ZoomMeetings::distinct()->pluck('timezone');

        if ($request->meeting_topic) {
            $zoomMeetings = $zoomMeetings->where('meeting_topic', 'LIKE', '%'.$request->meeting_topic.'%');
        }
        if ($request->meeting_agenda) {
            $zoomMeetings = $zoomMeetings->where('meeting_agenda', 'LIKE', '%'.$request->meeting_agenda.'%');
        }
        if ($request->meeting_ids) {
            $zoomMeetings = $zoomMeetings->WhereIn('meeting_id', $request->meeting_ids);
        }
        if ($request->time_zone) {
            $zoomMeetings = $zoomMeetings->WhereIn('timezone', $request->time_zone);
        }
        if ($request->duration) {
            $zoomMeetings = $zoomMeetings->where('meeting_duration', 'LIKE', '%'.$request->duration.'%');
        }
        if ($request->date) {
            $zoomMeetings = $zoomMeetings->where('start_date_time', 'LIKE', '%'.$request->date.'%');
        }

        $meetings = $zoomMeetings->orderByDesc('zoom_meetings.start_date_time')->paginate(10);

        return view('zoom-meetings.index', [
            'meetingData' => $meetings,
            'zoomMeetingIds' => $zoomMeetingIds,
            'timeZones' => $timeZones,
        ]);
    }

    public function fetchRecordings(Request $request): JsonResponse
    {
        $tokenResponse = ZoomOAuthHelper::getAccessToken();

        if (isset($tokenResponse['access_token'])) {
            $accessToken = $tokenResponse['access_token'];
            $recordingURL = 'https://api.zoom.us/v2/meetings/'.$request->meetingId.'/recordings';

            try {
                // Fetch recordings for this meeting
                $recordingsResponse = Http::withHeaders([
                    'Authorization' => 'Bearer '.$accessToken,
                ])->get($recordingURL);

                // Log the API request and response to the database
                ZoomApiLog::create([
                    'type' => 'recording',
                    'request_url' => $recordingURL,
                    'request_headers' => json_encode(['Authorization' => 'Bearer '.$accessToken]),
                    'request_data' => '', // Add request data here if needed
                    'response_status' => $recordingsResponse->status(),
                    'response_data' => json_encode($recordingsResponse->json()),
                ]);

                if ($recordingsResponse->successful()) {
                    $meetingRecording = $recordingsResponse->json();

                    Log::info('meetingRecording -->'.json_encode($meetingRecording));

                    // Code copied from app/Meetings/ZoomMeetings.php:saveRecordings()
                    if ($meetingRecording && isset($meetingRecording['recording_files'])) {
                        $folderPath = public_path().'/zoom/0/'.$request->meetingId;
                        $databsePath = '/zoom/0/'.$request->meetingId;
                        Log::info('folderPath -->'.$folderPath);
                        foreach ($meetingRecording['recording_files'] as $recordings) {
                            $checkfile = ZoomMeetingDetails::where('download_url_id', $recordings['id'])->first();
                            if (! $checkfile) {
                                if ($recordings['recording_type'] == 'shared_screen_with_speaker_view') {
                                    Log::info('shared_screen_with_speaker_view');
                                    $fileName = $request->meetingId.'_'.time().'.mp4';
                                    $urlOfFile = $recordings['download_url'];
                                    $filePath = $folderPath.'/'.$fileName;
                                    if (! file_exists($filePath) && ! is_dir($folderPath)) {
                                        mkdir($folderPath, 0777, true);
                                    }
                                    $ch = curl_init($urlOfFile);
                                    curl_exec($ch);
                                    if (! curl_errno($ch)) {
                                        $info = curl_getinfo($ch);
                                        $downloadLink = $info['redirect_url'];
                                    }
                                    curl_close($ch);

                                    if ($downloadLink) {
                                        copy($downloadLink, $filePath);
                                    }

                                    $zoom_meeting_details = new ZoomMeetingDetails;
                                    $zoom_meeting_details->local_file_path = $databsePath.'/'.$fileName;
                                    $zoom_meeting_details->file_name = $fileName;
                                    $zoom_meeting_details->download_url_id = $recordings['id'];
                                    $zoom_meeting_details->meeting_id = $recordings['meeting_id'];
                                    $zoom_meeting_details->file_type = $recordings['file_type'];
                                    $zoom_meeting_details->download_url = $recordings['download_url'];
                                    // $zoom_meeting_details->file_path = $recordings['file_path']; // this field for Zoom On-Premise accounts.
                                    $zoom_meeting_details->file_size = $recordings['file_size'];
                                    $zoom_meeting_details->file_extension = $recordings['file_extension'];
                                    $zoom_meeting_details->save();
                                }
                            }
                        }
                    }

                    return response()->json(['message' => 'Recordings fetched successfully', 'code' => 200]);
                } else {
                    return response()->json(['message' => 'An error occurred while fetch the zoom recordings, Please check the logs', 'code' => 500], 500);
                }
            } catch (Exception $e) {
                // Log the exception to the database
                ZoomApiLog::create([
                    'type' => 'recording',
                    'request_url' => $recordingURL,
                    'request_headers' => json_encode(['Authorization' => 'Bearer '.$accessToken]),
                    'request_data' => '', // Add request data here if needed
                    'response_status' => 500, // Set an appropriate status code for errors
                    'response_data' => json_encode(['error' => $e->getMessage()]),
                ]);

                return response()->json(['message' => 'An error occurred while fetch the zoom recordings, Please check the logs', 'code' => 500], 500);
            }
        }
    }

    public function fetchParticipants(Request $request): JsonResponse
    {
        $tokenResponse = ZoomOAuthHelper::getAccessToken();

        if (isset($tokenResponse['access_token'])) {
            $accessToken = $tokenResponse['access_token'];
            $participantURL = 'https://api.zoom.us/v2/past_meetings/'.$request->meetingId.'/participants';

            try {
                // Fetch participants for this meeting
                $participantsResponse = Http::withHeaders([
                    'Authorization' => 'Bearer '.$accessToken,
                ])->get($participantURL);

                // Log the API request and response to the database
                ZoomApiLog::create([
                    'type' => 'participant',
                    'request_url' => $participantURL,
                    'request_headers' => json_encode(['Authorization' => 'Bearer '.$accessToken]),
                    'request_data' => '', // Add request data here if needed
                    'response_status' => $participantsResponse->status(),
                    'response_data' => json_encode($participantsResponse->json()),
                ]);

                if ($participantsResponse->successful()) {
                    $participants = $participantsResponse->json();

                    if ($participants['total_records'] > 0) {
                        // Store participants in the participants table
                        foreach ($participants['participants'] as $participant) {
                            ZoomMeetingParticipant::updateOrCreate(
                                ['meeting_id' => $request->meetingId, 'email' => $participant['user_email']],
                                [
                                    'name' => $participant['name'],
                                    'join_time' => $participant['join_time'],
                                    'leave_time' => $participant['leave_time'],
                                    'duration' => $participant['duration'],
                                ]
                            );
                        }
                    }

                    return response()->json(['message' => 'Participants fetched successfully', 'code' => 200]);
                } else {
                    return response()->json(['message' => 'An error occurred while fetch the zoom participants, Please check the logs', 'code' => 500], 500);
                }
            } catch (Exception $e) {
                // Log the exception to the database
                ZoomApiLog::create([
                    'type' => 'participant',
                    'request_url' => $participantURL,
                    'request_headers' => json_encode(['Authorization' => 'Bearer '.$accessToken]),
                    'request_data' => '', // Add request data here if needed
                    'response_status' => 500, // Set an appropriate status code for errors
                    'response_data' => json_encode(['error' => $e->getMessage()]),
                ]);

                return response()->json(['message' => 'An error occurred while fetch the zoom participants, Please check the logs', 'code' => 500], 500);
            }
        }
    }

    public function deleteRecording($meetingId, $recordingId): JsonResponse
    {
        $tokenResponse = ZoomOAuthHelper::getAccessToken();

        if (isset($tokenResponse['access_token'])) {
            $accessToken = $tokenResponse['access_token'];
            $deleteRecordingsURL = "https://api.zoom.us/v2/meetings/{$meetingId}/recordings/$recordingId?action=trash";

            try {
                // Fetch participants for this meeting
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$accessToken,
                ])->delete($deleteRecordingsURL);

                // Log the API request and response to the database
                ZoomApiLog::create([
                    'type' => 'Delete recording',
                    'request_url' => $deleteRecordingsURL,
                    'request_headers' => json_encode(['Authorization' => 'Bearer '.$accessToken]),
                    'request_data' => '', // Add request data here if needed
                    'response_status' => $response->status(),
                    'response_data' => json_encode($response->json()),
                ]);

                if ($response->successful()) {
                    Log::info('##########  Recording for the meeting have been deleted successfully ##############');

                    // Save recroding_deleted_at in DB table
                    $checkfile = ZoomMeetingDetails::where('download_url_id', $recordingId)->first();
                    if ($checkfile) {
                        $checkfile->recording_deleted_at = Carbon::now();
                        $checkfile->save();
                    }

                    return response()->json(['message' => 'Recording for the meeting have been deleted successfully.', 'code' => 200]);
                } else {
                    Log::info('##########  Error deleting the recording for the meeting, Please check the logs ##############');

                    return response()->json(['message' => 'Error deleting the recording for the meeting, Please check the logs', 'code' => 500], 500);
                }
            } catch (Exception $e) {
                // Log the exception to the database
                ZoomApiLog::create([
                    'type' => 'Delete recording',
                    'request_url' => $deleteRecordingsURL,
                    'request_headers' => json_encode(['Authorization' => 'Bearer '.$accessToken]),
                    'request_data' => '', // Add request data here if needed
                    'response_status' => 500, // Set an appropriate status code for errors
                    'response_data' => json_encode(['error' => $e->getMessage()]),
                ]);

                Log::info('##########  Error deleting the recording for the meeting, Please check the ZoomApiLog ##############');

                return response()->json(['message' => 'Error deleting the recording for the meeting, Please check the logs', 'code' => 500], 500);
            }
        }
    }

    public function show(): View
    {
        $type = '';
        $upcomingMeetings = [];
        $pastMeetings = [];

        return view('zoom-meetings.showdata', [
            'upcomingMeetings' => $upcomingMeetings,
            'pastMeetings' => $pastMeetings,
            'type' => $type,
        ]);
    }

    public function listParticipants(Request $request): JsonResponse
    {
        $perPage = 5;

        $participants = ZoomMeetingParticipant::where('meeting_id', $request->meetingId)
            ->latest()
            ->paginate($perPage);

        $html = view('zoom-meetings.participations-listing-modal-html')->with('participants', $participants)->render();

        return response()->json(['code' => 200, 'data' => $participants, 'html' => $html, 'message' => 'Content render']);
    }

    public function allParticipantsList(Request $request): JsonResponse
    {
        $perPage = 10;

        $participants = ZoomMeetingParticipant::latest()
            ->whereNull('description')
            ->paginate($perPage);
        $html = view('zoom-meetings.participations-listing-modal-html')->with('participants', $participants)->render();

        return response()->json(['code' => 200, 'data' => $participants, 'html' => $html, 'message' => 'Content render']);
    }

    public function listErrorLogs(Request $request): View
    {
        $zoomApiLogs = new ZoomApiLog;

        $zoomApiLogTypes = ZoomApiLog::distinct()->pluck('type');
        $zoomrequestUrls = ZoomApiLog::distinct()->pluck('request_url', 'id');

        if ($request->response_status) {
            $zoomApiLogs = $zoomApiLogs->where('response_data', 'LIKE', '%'.$request->response_status.'%');
        }
        if ($request->search_status) {
            $zoomApiLogs = $zoomApiLogs->where('response_status', 'LIKE', '%'.$request->search_status.'%');
        }
        if ($request->zoom_type) {
            $zoomApiLogs = $zoomApiLogs->WhereIn('type', $request->zoom_type);
        }
        if ($request->req_urls) {
            $zoomApiLogs = $zoomApiLogs->WhereIn('id', $request->req_urls);
        }
        if ($request->date) {
            $zoomApiLogs = $zoomApiLogs->where('created_at', 'LIKE', '%'.$request->date.'%');
        }

        $zoomApiLogs = $zoomApiLogs->latest()->paginate(Setting::get('pagination', 10));

        return view('zoom-meetings.zoom-error-logs', compact('zoomApiLogs', 'zoomApiLogTypes', 'zoomrequestUrls'));
    }

    public function listRecordings($meetingId): View
    {
        $zoomRecordings = new ZoomMeetingDetails;

        $zoomRecordings = $zoomRecordings->where('meeting_id', $meetingId)->latest()->paginate(Setting::get('pagination', 10));

        return view('zoom-meetings.zoom-recodring-list', compact('zoomRecordings'));
    }

    public function updateParticipantDescription(Request $request): JsonResponse
    {
        $meetingdata = ZoomMeetingParticipant::find($request->id);
        $meetingOldDescription = $meetingdata->description;
        $meetingdata->description = $request->description;
        $meetingdata->save();

        $zoomMeetingHistory = new ZoomMeetingParticipantHistory;
        $zoomMeetingHistory->zoom_meeting_participant_id = $meetingdata->id;
        $zoomMeetingHistory->type = 'description';
        $zoomMeetingHistory->oldvalue = $meetingOldDescription;
        $zoomMeetingHistory->newvalue = $request->description;
        $zoomMeetingHistory->user_id = Auth::id();
        $zoomMeetingHistory->save();

        return response()->json(['code' => 200, 'message' => 'Description Updated SuccessFully'], 200);
    }

    public function updateMeetingDescription(Request $request): JsonResponse
    {
        $meetingdata = ZoomMeetingDetails::find($request->id);
        $meetingOldDescription = $meetingdata->description;
        $meetingdata->description = $request->description;
        $meetingdata->save();

        $zoomMeetingHistory = new ZoomMeetingRecordHistory;
        $zoomMeetingHistory->zoom_meeting_record_id = $meetingdata->id;
        $zoomMeetingHistory->type = 'description';
        $zoomMeetingHistory->oldvalue = $meetingOldDescription;
        $zoomMeetingHistory->newvalue = $request->description;
        $zoomMeetingHistory->user_id = Auth::id();
        $zoomMeetingHistory->save();

        return response()->json(['code' => 200, 'message' => 'Meeting Added SuccessFully'], 200);
    }

    public function downloadRecords($id): BinaryFileResponse
    {
        $fileName = ZoomMeetingDetails::find($id);
        $file_name = basename($fileName->local_file_path);
        $meetingId = $fileName->meeting_id;

        $filePath = public_path("zoom/0/$meetingId/$file_name");

        if (file_exists($filePath)) {
            return Response::download($filePath);
        } else {
            abort(404, 'The file you are trying to download does not exist.');
        }
    }

    public function updatePersonalMeeting(Request $request): JsonResponse
    {
        $data = $request->except('_token');

        // 1 represents PMI meetings - Only one record is allowed. So I am using meeting_type column in updateORCreate
        ZoomMeetings::updateOrCreate(
            ['meeting_type' => 1],
            ['meeting_id' => $data['meeting_id'], 'meeting_topic' => $data['meeting_topic'], 'join_meeting_url' => $data['join_meeting_url']]
        );

        return response()->json(
            [
                'code' => 200,
                'data' => [],
                'message' => 'Personal meeting update successfully!',
            ]
        );
    }

    public function webhook(Request $request)
    {
        Log::info('zoom Webhook Logs -->'.$request->getContent());

        $zoomData = json_decode($request->getContent(), true);
        if ($zoomData['event'] == 'endpoint.url_validation') {
            $message = 'v0:'.$request->header('x-zm-request-timestamp').':'.$request->getContent();
            $hash = hash_hmac('sha256', $message, config('services.zoom.secret_token'));
            $signature = "v0={$hash}";
            $verified = hash_equals($request->header('x-zm-signature'), $signature);
            if ($verified) {
                $zoomSecret = config('services.zoom.secret_token');
                $zoomPlainToken = $zoomData['payload']['plainToken'];
                $hash = hash_hmac('sha256', $zoomPlainToken, $zoomSecret);
                $reponseData['plainToken'] = $zoomPlainToken;
                $reponseData['encryptedToken'] = $hash;

                return response()->json($reponseData);
            }
        } elseif ($zoomData['event'] == 'meeting.created') {
            return $this->createMeetingWebhook($zoomData);
        } elseif ($zoomData['event'] == 'meeting.participant_joined') {
            return $this->participantJoinedWebhook($zoomData);
        } elseif ($zoomData['event'] == 'meeting.participant_left') {
            return $this->participantLeftWebhook($zoomData);
        } elseif ($zoomData['event'] == 'recording.completed') {
            return $this->recordingCompletedWebhook($zoomData);
        }
    }

    protected function createMeetingWebhook($zoomData)
    {
        if (isset($zoomData['payload']['object']['pmi'])) {
            $meetingId = $zoomData['payload']['object']['pmi'];
        } else {
            $meetingId = $zoomData['payload']['object']['id'];
        }
        ZoomMeetings::updateOrCreate(
            ['meeting_id' => $meetingId],
            [
                'meeting_topic' => $zoomData['payload']['object']['topic'],
                'meeting_type' => $zoomData['payload']['object']['type'],
                'meeting_agenda' => $zoomData['payload']['object']['agenda'] ?? '',
                'join_meeting_url' => $zoomData['payload']['object']['join_url'],
                'start_date_time' => $zoomData['payload']['object']['start_time'] ?? '',
                'meeting_duration' => $zoomData['payload']['object']['duration'] ?? '',
                'timezone' => $zoomData['payload']['object']['timezone'],
                'host_zoom_id' => $zoomData['payload']['object']['host_id'],
            ]
        );

        return response()->json(['message' => 'Meeting created successfully', 'code' => 200], 200);
    }

    protected function participantJoinedWebhook($zoomData)
    {
        ZoomMeetingParticipant::firstOrCreate([
            'meeting_id' => $zoomData['payload']['object']['id'],
            'zoom_user_id' => $zoomData['payload']['object']['participant']['user_id'],
            'participant_uuid' => $zoomData['payload']['object']['participant']['participant_uuid'],
            'join_time' => $zoomData['payload']['object']['participant']['join_time'],
        ],
            [
                'name' => $zoomData['payload']['object']['participant']['user_name'],
                'email' => $zoomData['payload']['object']['participant']['email'],
            ],
        );

        return response()->json(['message' => 'Participant joined successfully', 'code' => 200], 200);
    }

    protected function participantLeftWebhook($zoomData)
    {
        ZoomMeetingParticipant::where([
            'meeting_id' => $zoomData['payload']['object']['id'],
            'zoom_user_id' => $zoomData['payload']['object']['participant']['user_id'],
            'participant_uuid' => $zoomData['payload']['object']['participant']['participant_uuid'],
        ])->update([
            'name' => $zoomData['payload']['object']['participant']['user_name'],
            'email' => $zoomData['payload']['object']['participant']['email'],
            'leave_time' => $zoomData['payload']['object']['participant']['leave_time'] ?? '',
            'leave_reason' => $zoomData['payload']['object']['participant']['leave_reason'] ?? '',
        ]);

        return response()->json(['message' => 'Participant left successfully', 'code' => 200], 200);
    }

    protected function recordingCompletedWebhook($zoomData)
    {
        Log::info('########### recordingCompletedWebhook Started ###########');
        $zoomDataPayloadObject = $zoomData['payload']['object'];

        if ($zoomDataPayloadObject && isset($zoomDataPayloadObject['recording_files'])) {
            $folderPath = public_path().'/zoom/0/'.$zoomDataPayloadObject['id'];
            $segmentPath = $folderPath.'/segments';
            $databsePath = '/zoom/0/'.$zoomDataPayloadObject['id'];
            Log::info('folderPath Testing By Nadesh -->'.$folderPath);
            foreach ($zoomDataPayloadObject['recording_files'] as $recordings) {
                Log::info('########### recording_files Loop Started ###########');
                $checkfile = ZoomMeetingDetails::where('download_url_id', $recordings['id'])->first();
                if (! $checkfile) {
                    if ($recordings['recording_type'] == 'shared_screen_with_speaker_view') {
                        Log::info('shared_screen_with_speaker_view');
                        $fileName = $zoomDataPayloadObject['id'].'_'.time().'.mp4';
                        $urlOfFile = $recordings['download_url'];
                        $filePath = $folderPath.'/'.$fileName;
                        if (! file_exists($filePath) && ! is_dir($folderPath)) {
                            mkdir($folderPath, 0777, true);
                            mkdir($segmentPath, 0777, true);
                        }
                        $ch = curl_init($urlOfFile);
                        curl_exec($ch);
                        Log::info('CURL ERROR By Nadesh -->'.curl_errno($ch));
                        if (! curl_errno($ch)) {
                            $info = curl_getinfo($ch);
                            Log::info('CURL Details -->'.json_encode($info));
                            if (isset($info['redirect_url']) && $info['redirect_url'] != '') {
                                $downloadLink = $info['redirect_url'];
                            } elseif (isset($info['url']) && $info['url'] != '') {
                                $downloadLink = $info['url'];
                            }
                        }
                        curl_close($ch);

                        if ($downloadLink) {
                            copy($downloadLink, $filePath);
                        }

                        $zoom_meeting_details = new ZoomMeetingDetails;
                        $zoom_meeting_details->local_file_path = $databsePath.'/'.$fileName;
                        $zoom_meeting_details->file_name = $fileName;
                        $zoom_meeting_details->download_url_id = $recordings['id'];
                        $zoom_meeting_details->meeting_id = $zoomDataPayloadObject['id'];
                        $zoom_meeting_details->file_type = $recordings['file_type'];
                        $zoom_meeting_details->download_url = $recordings['download_url'];
                        $zoom_meeting_details->file_size = $recordings['file_size'];
                        $zoom_meeting_details->file_extension = $recordings['file_extension'];
                        $zoom_meeting_details->recording_start = $recordings['recording_start'];
                        $zoom_meeting_details->recording_end = $recordings['recording_end'];
                        $zoom_meeting_details->save();

                        // Here we can plan recording split logic.
                        // 1. Once the entire recording completed,
                        // 2. Get the list of participants for this meeting from zoom_meeting_participants table
                        // 3. Split the recording depends on the participants join_time & leave_time
                        // In your Laravel controller or a dedicated splitting script
                        $fullRecordingPath = $filePath;
                        $outputPath = $segmentPath; // Output folder for segments

                        // Create a Carbon instance from the ISO 8601 date // '2023-09-14T12:37:50Z'
                        $carbonStartDate = Carbon::parse($zoom_meeting_details->recording_start);
                        $carbonEndDate = Carbon::parse($zoom_meeting_details->recording_end);

                        // Format the Carbon date in "Y-m-d H:i:s" format
                        $formattedStartDate = $carbonStartDate->format('Y-m-d H:i:s'); // Output: 2023-09-14 12:37:50
                        $formattedEndDate = $carbonEndDate->format('Y-m-d H:i:s'); // Output: 2023-09-14 12:37:50

                        $recordingStartTime = Carbon::parse($formattedStartDate);
                        $recordingEndTime = Carbon::parse($formattedEndDate);

                        Log::info('recordingStartTime -->'.$recordingStartTime);
                        Log::info('recordingEndTime -->'.$recordingEndTime);

                        $meetingParticipants = ZoomMeetingParticipant::where('meeting_id', $zoomDataPayloadObject['id'])
                            ->whereRaw('DATE_FORMAT(join_time, "%Y-%m-%d %H:%i") >= ?', $recordingStartTime->format('Y-m-d H:i'))
                            ->whereRaw('DATE_FORMAT(leave_time, "%Y-%m-%d %H:%i") <= ?', $recordingEndTime->format('Y-m-d H:i'))
                            ->get();
                        if ($meetingParticipants) {
                            $segments = [];
                            foreach ($meetingParticipants as $meetingParticipant) {
                                // Parse join_time and leave_time
                                $joinTime = Carbon::parse($meetingParticipant->join_time);
                                $leaveTime = Carbon::parse($meetingParticipant->leave_time);

                                Log::info('meetingParticipantID -->'.$meetingParticipant->id);
                                Log::info('joinTime -->'.$joinTime);
                                Log::info('leaveTime -->'.$leaveTime);
                                Log::info('max -->'.max($joinTime, $recordingStartTime));
                                Log::info('max Different -->'.max($joinTime, $recordingStartTime)->diff($recordingStartTime)->format('%H:%I:%S'));
                                Log::info('min -->'.min($leaveTime, $recordingEndTime));
                                Log::info('min Different -->'.min($leaveTime, $recordingEndTime)->diff($recordingStartTime)->format('%H:%I:%S'));

                                if ($leaveTime->gte($recordingStartTime)) {
                                    // Participant joined during or after recording started
                                    $startTimestamp = max($joinTime, $recordingStartTime)->diff($recordingStartTime)->format('%H:%I:%S');
                                    $endTimestamp = min($leaveTime, $recordingEndTime)->diff($recordingStartTime)->format('%H:%I:%S');

                                    Log::info('startTimestamp -->'.$startTimestamp);
                                    Log::info('endTimestamp -->'.$endTimestamp);

                                    // Create a unique segment name for each participant
                                    $segmentName = "participant_{$meetingParticipant->id}.mp4";

                                    // Add the segment to the array
                                    $segments[] = [
                                        'start' => $startTimestamp,
                                        'end' => $endTimestamp,
                                        'output' => $segmentName, // Output filename for the segment
                                        'participant' => $meetingParticipant, // Store the participant information for reference
                                    ];

                                    $meetingParticipant->zoom_recording_id = $recordings['id'];
                                    $meetingParticipant->recording_path = "{$databsePath}/segments/{$segmentName}";
                                    $meetingParticipant->save();
                                } else {
                                    Log::info('meetingParticipantID Skipped-->'.$meetingParticipant->id);
                                }
                            }

                            if ($segments) {
                                foreach ($segments as $segment) {
                                    $start = $segment['start'];
                                    $end = $segment['end'];
                                    $segmentFileName = $segment['output']; // Adjust the filename as needed

                                    // Use ffmpeg to extract the segment
                                    $command = "yes | ffmpeg -i $fullRecordingPath -ss $start -to $end -c:v copy -c:a copy $outputPath/$segmentFileName";

                                    Log::info('FFMPEG Command -->'.$command);

                                    // Execute the command and capture its output and return code
                                    $output = [];
                                    $returnCode = -1;

                                    exec($command, $output, $returnCode);

                                    // Check if the command was successful
                                    if ($returnCode != 0) {
                                        $errorMessage = implode(PHP_EOL, $output);
                                        // Log the error
                                        Log::error("Error executing ffmpeg command: $errorMessage and code $returnCode");
                                    }
                                }
                            }
                        }
                    }

                    Log::info('##########  deleteRecording starts ##############');
                    Log::info('##########  Recording Type ############## '.$recordings['recording_type']);
                    $this->deleteRecording($zoomDataPayloadObject['id'], $recordings['id']);
                    Log::info('##########  deleteRecording ends ##############');
                }
            }
        }

        return response()->json(['message' => 'Recording completed successfully', 'code' => 200], 200);
    }

    public function addUserPermission(Request $request): JsonResponse
    {
        $zoomRecord = ZoomMeetingDetails::find($request->record_id);
        $zoomRecord->user_record_permission = $request->user_record_permission;
        $zoomRecord->save();

        return response()->json(['code' => 200, 'message' => 'records View Permission Added SuccessFully'], 200);
    }

    public function listDescriptionHistory(Request $request): JsonResponse
    {
        $histories = ZoomMeetingRecordHistory::with(['user'])
            ->where('zoom_meeting_record_id', $request->id)
            ->Where('type', $request->type)->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history status',
            'status_name' => 'success',
        ], 200);
    }

    public function participantDescriptionHistory(Request $request): JsonResponse
    {
        $histories = ZoomMeetingParticipantHistory::with(['user'])
            ->where('zoom_meeting_participant_id', $request->id)
            ->Where('type', $request->type)->orderByDesc('created_at')->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history status',
            'status_name' => 'success',
        ], 200);
    }

    public function storeMeetingDescription(Request $request): JsonResponse
    {
        $meetingdata = ZoomMeetings::find($request->id);
        $meetingOldDescription = $meetingdata->description;
        $meetingdata->description = $request->description;
        $meetingdata->save();

        $zoomMeetingHistory = new ZoomMeetingHistory;
        $zoomMeetingHistory->zoom_meeting_id = $meetingdata->id;
        $zoomMeetingHistory->type = 'description';
        $zoomMeetingHistory->oldvalue = $meetingOldDescription;
        $zoomMeetingHistory->newvalue = $request->description;
        $zoomMeetingHistory->user_id = Auth::id();
        $zoomMeetingHistory->save();

        return response()->json(['code' => 200, 'message' => 'Meeting Added SuccessFully'], 200);
    }

    public function meetingDescriptionHistory(Request $request): JsonResponse
    {
        $histories = ZoomMeetingHistory::with(['user'])
            ->where('zoom_meeting_id', $request->id)
            ->Where('type', $request->type)->get();

        return response()->json([
            'status' => true,
            'data' => $histories,
            'message' => 'Successfully get history status',
            'status_name' => 'success',
        ], 200);
    }

    public function showVideo(Request $request): JsonResponse
    {
        $fileName = ZoomMeetingDetails::find($request->id);

        if (! $fileName) {
            return response()->json([
                'status' => false,
                'error' => 'Video not Available',
                'status_name' => 'error',
            ], 404);
        }

        $file_name = basename($fileName->local_file_path);
        $meetingId = $fileName->meeting_id;

        $videoUrl = "zoom/0/$meetingId/$file_name";

        if (! file_exists($videoUrl)) {
            return response()->json([
                'status' => false,
                'error' => 'Video not found',
                'status_name' => 'error',
            ], 404);
        }

        $mime_type = mime_content_type($videoUrl);
        if ($mime_type !== 'video/mp4') {
            abort(404);
        }

        return response()->json([
            'status' => true,
            'videoUrl' => asset($videoUrl),
            'message' => 'Successfully preview the Video',
            'status_name' => 'success',
        ], 200);
    }

    public function showParticipantVideo(Request $request): JsonResponse
    {
        $zoomMeetingParticipant = ZoomMeetingParticipant::find($request->id);
        // Check if the participant exists
        if (! $zoomMeetingParticipant) {
            return response()->json([
                'status' => false,
                'error' => 'Participant not found',
                'status_name' => 'error',
            ], 404);
        }

        $file_name = basename($zoomMeetingParticipant->recording_path);
        // Check if the filename is null or empty
        if (! $file_name) {
            return response()->json([
                'status' => false,
                'error' => 'Invalid filename',
                'status_name' => 'error',
            ], 400);
        }

        $meetingId = $zoomMeetingParticipant->meeting_id;

        $videoUrl = "zoom/0/$meetingId/segments/$file_name";

        if (! file_exists($videoUrl)) {
            return response()->json([
                'status' => false,
                'error' => 'Video not found',
                'status_name' => 'error',
            ], 404);
        }

        $mime_type = mime_content_type($videoUrl);
        if ($mime_type !== 'video/mp4') {
            abort(404);
        }

        return response()->json([
            'status' => true,
            'videoUrl' => asset($videoUrl),
            'message' => 'Successfully preview the Video',
            'status_name' => 'success',
        ], 200);
    }

    public function listAllParticipants(Request $request): View
    {
        $zoomParticipants = new ZoomMeetingParticipant;

        $allNames = ZoomMeetingParticipant::distinct()->pluck('name');
        $allEmails = ZoomMeetingParticipant::distinct()->pluck('email');

        if ($request->search_reason) {
            $zoomParticipants = $zoomParticipants->where('leave_reason', 'LIKE', '%'.$request->search_reason.'%');
        }
        if ($request->join_time) {
            $zoomParticipants = $zoomParticipants->where('join_time', 'LIKE', '%'.$request->join_time.'%');
        }
        if ($request->leave_time) {
            $zoomParticipants = $zoomParticipants->where('leave_time', 'LIKE', '%'.$request->leave_time.'%');
        }
        if ($request->name) {
            $zoomParticipants = $zoomParticipants->WhereIn('name', $request->name);
        }
        if ($request->email) {
            $zoomParticipants = $zoomParticipants->WhereIn('email', $request->email);
        }
        if ($request->duration) {
            $zoomParticipants = $zoomParticipants->where('duration', 'LIKE', '%'.$request->duration.'%');
        }
        if ($request->date) {
            $zoomParticipants = $zoomParticipants->where('created_at', 'LIKE', '%'.$request->date.'%');
        }

        $zoomParticipants = $zoomParticipants->latest()->paginate(Setting::get('pagination', 10));

        return view('zoom-meetings.zoom-participants-listing', compact('zoomParticipants', 'allNames', 'allEmails'));
    }
}
