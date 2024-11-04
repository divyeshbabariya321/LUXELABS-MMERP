<?php

/**
 * Class ZoomMeetings | app/Meetings/Meeting/ZoomMeetings.php
 * Zoom Meetings integration for video call purpose using LaravelZoom's REST API
 *
 * @filesource required php 7 as this file contains tokenizer extension which was not stable prior to this version
 *
 * @see https://github.com/saineshmamgain/laravel-zoom
 * @see ZoomMeetings
 *
 * @author   sololux <sololux@gmail.com>
 */

namespace App\Meetings;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Vendor;
use App\Customer;
use App\Supplier;
use App\Models\ZoomMeeting;
use Illuminate\Database\Eloquent\Model;
use seo2websites\LaravelZoom\LaravelZoom;

/**
 * Class ZoomMeetings - active record
 *
 * A zoom class used to create meetings
 * This class is used to interact with zoom interface.
 */
class ZoomMeetings extends Model
{
    protected $fillable = ['meeting_id', 'meeting_topic', 'meeting_type', 'meeting_agenda', 'join_meeting_url', 'start_meeting_url', 'start_date_time', 'meeting_duration', 'host_zoom_id', 'zoom_recording', 'user_id', 'user_type', 'timezone'];

    /**
     * Create a scheduled and instant meeting with zoom based on the params send through form
     *
     *
     * @return array $meeting
     *
     * @Rest\Post("LaravelZoom")
     *
     * @uses LaravelZoom
     */
    public function createMeeting(string $zoomKey, string $zoomSecret, array $data): array
    {
        $zoom  = new LaravelZoom($zoomKey, $zoomSecret);
        $time  = time() + 7200;
        $token = $zoom->getJWTToken($time);
        if ($token) {
            $meeting = $zoom->createScheduledMeeting($data['user_id'], $data['topic'], $data['startTime'], $data['duration'], $data['timezone'], '', '', $data['agenda'], [], $data['settings']);
            dd($meeting);

            return $meeting;
        } else {
            return false;
        }
    }

    public function getMeetings($zoomKey, $zoomSecret, $data)
    {
        $zoom       = new LaravelZoom($zoomKey, $zoomSecret);
        $meeting1   = $zoom->getJWTToken(time() + 7200);
        $meetingAll = $zoom->getMeetings($data['user_id'], $data['type'], 10);
        echo 'reach';
        print_r($meetingAll);
        exit;
    }

    /**
     * Getting future meetings
     *
     *
     * @return array $meeting
     *
     * @uses vendors
     * @uses customers
     * @uses suppliers
     */
    public function upcomingMeetings(string $type, carbon $date): array
    {
        switch ($type) {
            case 'vendor':
                $meetings = ZoomMeeting::where('zoom_meetings.user_type', '=', $type)
                    ->whereDate('zoom_meetings.start_date_time', '>=', $date)
                    ->join('vendors', 'zoom_meetings.user_id', '=', 'vendors.id')
                    ->select('zoom_meetings.*', 'vendors.name', 'vendors.phone', 'vendors.email', 'vendors.whatsapp_number')
                    ->orderBy('zoom_meetings.start_date_time')
                    ->get();

                return $meetings;
                break;
            case 'customer':
                $meetings = ZoomMeeting::where('zoom_meetings.user_type', '=', $type)
                    ->whereDate('zoom_meetings.start_date_time', '>=', $date)
                    ->join('customers', 'zoom_meetings.user_id', '=', 'customers.id')
                    ->select('zoom_meetings.*', 'customers.name', 'customers.phone', 'customers.email', 'customers.whatsapp_number')
                    ->orderBy('zoom_meetings.start_date_time')
                    ->get();

                return $meetings;
                break;
            case 'supplier':
                $meetings = ZoomMeeting::where('zoom_meetings.user_type', '=', $type)
                    ->whereDate('zoom_meetings.start_date_time', '>=', $date)
                    ->join('suppliers', 'zoom_meetings.user_id', '=', 'suppliers.id')
                    ->select('zoom_meetings.*', 'suppliers.supplier as name', 'suppliers.phone', 'suppliers.email', 'suppliers.whatsapp_number')
                    ->orderBy('zoom_meetings.start_date_time')
                    ->get();

                return $meetings;
                break;
            default:
                break;
        }
    }

    /**
     * Getting past meetings with recordings
     *
     *
     * @return array $meeting
     *
     * @uses vendors
     * @uses customers
     * @uses suppliers
     */
    public function pastMeetings(string $type, carbon $date): array
    {
        switch ($type) {
            case 'vendor':
                $meetings = ZoomMeeting::where('zoom_meetings.user_type', '=', $type)
                    ->whereDate('zoom_meetings.start_date_time', '<', $date)
                    ->join('vendors', 'zoom_meetings.user_id', '=', 'vendors.id')
                    ->select('zoom_meetings.*', 'vendors.name', 'vendors.phone', 'vendors.email', 'vendors.whatsapp_number')
                    ->orderBy('zoom_meetings.start_date_time')
                    ->get();

                return $meetings;
                break;
            case 'customer':
                $meetings = ZoomMeeting::where('zoom_meetings.user_type', '=', $type)
                    ->whereDate('zoom_meetings.start_date_time', '<', $date)
                    ->join('customers', 'zoom_meetings.user_id', '=', 'customers.id')
                    ->select('zoom_meetings.*', 'customers.name', 'customers.phone', 'customers.email', 'customers.whatsapp_number')
                    ->orderBy('zoom_meetings.start_date_time')
                    ->get();

                return $meetings;
                break;
            case 'supplier':
                $meetings = ZoomMeeting::where('zoom_meetings.user_type', '=', $type)
                    ->whereDate('zoom_meetings.start_date_time', '<', $date)
                    ->join('suppliers', 'zoom_meetings.user_id', '=', 'suppliers.id')
                    ->select('zoom_meetings.*', 'suppliers.supplier as name', 'suppliers.phone', 'suppliers.email', 'suppliers.whatsapp_number')
                    ->orderBy('zoom_meetings.start_date_time')
                    ->get();

                return $meetings;
                break;
            default:
                break;
        }
    }

    /**
     * Get meeting recordings based on meeting id
     *
     *
     * @Rest\Post("LaravelZoom")
     *
     * @uses LaravelZoom
     *
     * @param mixed $zoomKey
     * @param mixed $zoomSecret
     * @param mixed $date
     *
     * @return array $meeting
     */
    public function getRecordings($zoomKey, $zoomSecret, $date): array
    {
        Log::info('Get recording getRecordings ');
        $allMeetingRecords = ZoomMeetings::WhereNull('zoom_recording')->whereNotNull('meeting_id')->whereDate('start_date_time', '<', $date)->get();

        $zoom  = new LaravelZoom($zoomKey, $zoomSecret);
        $token = $zoom->getJWTToken(time() + 36000);
        Log::info('Find recording-->' . count($allMeetingRecords));
        if (0 != count($allMeetingRecords)) {
            foreach ($allMeetingRecords as $meetings) {
                $meetingId = $meetings->meeting_id;
                Log::info('Get Recording ' . json_encode($meetings));
                Log::info('Get meetingId ' . $meetingId);
                $recordingAll = $zoom->getMeetingRecordings($meetingId);
                Log::info(json_encode($recordingAll));
                if ($recordingAll) {
                    if ('200' == $recordingAll['status']) {
                        $recordingFiles = $recordingAll['body']['recording_files'];
                        Log::info('recordingFiles -->' . json_encode($recordingFiles));
                        if ($recordingFiles) {
                            $folderPath = public_path() . '/zoom/0/' . $meetings->id;
                            Log::info('folderPath -->' . $folderPath);
                            foreach ($recordingFiles as $recordings) {
                                if ('shared_screen_with_speaker_view' == $recordings['recording_type']) {
                                    Log::info('shared_screen_with_speaker_view');
                                    $fileName  = $meetingId . '.mp4';
                                    $urlOfFile = $recordings['download_url'];
                                    $filePath  = $folderPath . '/' . $fileName;
                                    if (! file_exists($filePath)) {
                                        mkdir($folderPath, 0777, true);
                                    }
                                    copy($urlOfFile, $filePath);
                                    $meetings->zoom_recording = $fileName;
                                    $meetings->save();
                                } else {
                                    if ('audio_only' == $recordings['recording_type']) {
                                        $fileNameAudio = $meetingId . '-audio.mp4';
                                        if (! isset($filePath) || empty($filePath)) {
                                            $filePath = $folderPath . '/' . $fileNameAudio;
                                        }
                                        $filePathAudio  = $folderPath . '/' . $fileNameAudio;
                                        $urlOfAudioFile = $recordings['download_url'];
                                        if (! file_exists($filePath)) {
                                            mkdir($folderPath, 0777, true);
                                        }
                                        copy($urlOfAudioFile, $filePathAudio);
                                    } else {
                                        // Not saving any other files currently
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Delete meeting recordings based on meeting id
     *
     *
     * @Rest\Post("LaravelZoom")
     *
     * @uses LaravelZoom
     *
     * @param mixed $zoomKey
     * @param mixed $zoomSecret
     * @param mixed $date
     *
     * @return array $meeting
     */
    public function deleteRecordings($zoomKey, $zoomSecret, $date): array
    {
        $allMeetingRecords = ZoomMeetings::where('is_deleted_from_zoom', '!=', 1)->whereNotNull('zoom_recording')->whereNotNull('meeting_id')->whereDate('start_date_time', '<', $date)->get();
        $zoom              = new LaravelZoom($zoomKey, $zoomSecret);
        $token             = $zoom->getJWTToken(time() + 36000);
        if (0 != count($allMeetingRecords)) {
            foreach ($allMeetingRecords as $meetings) {
                $meetingId     = $meetings->meeting_id;
                $folderPath    = public_path() . '/zoom/0/' . $meetings->id;
                $fileName      = $meetingId . '.mp4';
                $filePath      = $folderPath . '/' . $fileName;
                $fileNameAudio = $meetingId . '-audio.mp4';
                $filePathAudio = $folderPath . '/' . $fileNameAudio;
                if (file_exists($filePath) && file_exists($filePathAudio)) {
                    $recordingDelete = $zoom->deleteRecordings($meetingId);
                    if ($recordingDelete && '204' == $recordingDelete['status']) {
                        $meetings->is_deleted_from_zoom = 1;
                        $meetings->save();
                    }
                }
            }
        }
    }

    public function getUserDetails($user_id, $user_type)
    {
        switch ($user_type) {
            case 'vendor':
                $vendor = Vendor::find($user_id);

                return $vendor;
                break;
            case 'customer':
                $customer = Customer::find($user_id);

                return $customer;
                break;
            case 'supplier':
                $supplier = Supplier::find($user_id);

                return $supplier;
                break;
            default:
                break;
        }
    }

    public function saveRecordings($zoomKey, $zoomSecret, $date, $zoommeetingid, $zoomuserid)
    {
        Log::info('Get recording getRecordings ');
        $allMeetingRecords = ZoomMeetings::WhereNull('zoom_recording')->whereNotNull('meeting_id')->whereDate('start_date_time', '<', $date)->get();

        $zoom  = new LaravelZoom($zoomKey, $zoomSecret);
        $token = $zoom->getJWTToken(time() + 36000);
        Log::info('Find recording-->' . count($allMeetingRecords));
        $meetingId = $zoommeetingid;
        Log::info('Get meetingId ' . $meetingId);
        $recordingAll = $zoom->getRecordings($zoomuserid, 10);
        Log::info(json_encode($recordingAll));
        if ($recordingAll) {
            if ('200' == $recordingAll['status']) {
                if ($recordingAll) {
                    $folderPath  = public_path() . '/zoom/0/' . $meetingId;
                    $databsePath = '/zoom/0/' . $meetingId;
                    Log::info('folderPath -->' . $folderPath);
                    foreach ($recordingAll['body']['meetings'] as $meetings) {
                        if ($meetings['id'] == $meetingId) {
                            $recordingFiles = $meetings['recording_files'];
                            Log::info('recordingFiles -->' . json_encode($recordingFiles));
                            foreach ($meetings['recording_files'] as $recordings) {
                                $checkfile = ZoomMeetingDetails::where('download_url_id', $recordings['id'])->first();
                                if (! $checkfile) {
                                    if ('shared_screen_with_speaker_view' == $recordings['recording_type']) {
                                        Log::info('shared_screen_with_speaker_view');
                                        $fileName  = $meetingId . '_' . time() . '.mp4';
                                        $urlOfFile = $recordings['download_url'];
                                        $filePath  = $folderPath . '/' . $fileName;
                                        if (! file_exists($filePath) && ! is_dir($folderPath)) {
                                            mkdir($folderPath, 0777, true);
                                        }
                                        $ch = curl_init($urlOfFile);
                                        curl_exec($ch);
                                        if (! curl_errno($ch)) {
                                            $info         = curl_getinfo($ch);
                                            $downloadLink = $info['redirect_url'];
                                        }
                                        curl_close($ch);

                                        if ($downloadLink) {
                                            copy($downloadLink, $filePath);
                                        }

                                        $zoom_meeting_details                  = new ZoomMeetingDetails();
                                        $zoom_meeting_details->file_path       = $databsePath . '/' . $fileName;
                                        $zoom_meeting_details->file_name       = $fileName;
                                        $zoom_meeting_details->download_url_id = $recordings['id'];
                                        $zoom_meeting_details->save();
                                    } else {
                                        if ('audio_only' == $recordings['recording_type']) {
                                            $fileNameAudio = $meetingId . '_' . time() . '-audio.mp4';
                                            if (! isset($filePath) || empty($filePath)) {
                                                $filePath = $folderPath . '/' . $fileNameAudio;
                                            }
                                            $filePathAudio  = $folderPath . '/' . $fileNameAudio;
                                            $urlOfAudioFile = $recordings['download_url'];
                                            if (! file_exists($filePath) && ! is_dir($folderPath)) {
                                                mkdir($folderPath, 0777, true);
                                            }
                                            $ch = curl_init($urlOfAudioFile);
                                            curl_exec($ch);
                                            if (! curl_errno($ch)) {
                                                $info         = curl_getinfo($ch);
                                                $downloadLink = $info['redirect_url'];
                                            }
                                            curl_close($ch);

                                            if ($downloadLink) {
                                                copy($downloadLink, $filePathAudio);
                                            }
                                            /*copy($urlOfAudioFile, $filePathAudio);*/
                                        } else {
                                            // Not saving any other files currently
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }
}
