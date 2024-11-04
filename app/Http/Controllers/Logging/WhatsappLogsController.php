<?php

namespace App\Http\Controllers\Logging;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WhatsappLogsController extends Controller
{
    public function getWhatsappLog(Request $request)
    {
        $path = base_path().'/';

        $files = Storage::disk('logs')->files('whatsapp');
        $array = [];
        $files = array_reverse($files);
        array_pop($files);
        array_pop($files);
        foreach ($files as $file) {
            $path = base_path().'/';
            $content = Storage::disk('logs')->get($file);
            $escaped = str_replace('/', '\/', $path);
            $rows = preg_split('/\r\n|\r|\n/', $content);
            $rows = array_reverse($rows);
            foreach ($rows as $key => $row) {
                if ($row && $row !== '') {
                    $data = [];
                    $date = substr($row, 1, 19);
                    if (isset($_REQUEST['date']) && $_REQUEST['date'] != '') {
                        $date1 = substr($date, 0, 10);
                        if ($date1 == $_REQUEST['date']) {
                            $data['date'] = $date;
                            // $message = substr($row, 155, strlen($row));
                            $message = substr($row, 35, strlen($row));
                            $data['error_message1'] = $message;
                            $data['error_message2'] = '';

                            $sent_message = strpos($message, '"sent":true');

                            if ($sent_message) {
                                $data['sent_message_status'] = 'Yes';
                            } else {
                                $data['sent_message_status'] = 'No';
                            }

                            array_push($array, $data);
                        }
                    } elseif (isset($request->date) && $request->date != '') {
                        $date1 = substr($date, 0, 10);
                        if ($date1 == $request->date) {
                            $data['date'] = $date;
                            $message = substr($row, 35, strlen($row));
                            $data['error_message1'] = $message;
                            $data['error_message2'] = '';

                            $sent_message = strpos($message, '"sent":true');

                            if ($sent_message) {
                                $data['sent_message_status'] = 'Yes';
                            } else {
                                $data['sent_message_status'] = 'No';
                            }

                            array_push($array, $data);
                        }
                    } else {
                        $data['date'] = $date;
                        $message = substr($row, 35, strlen($row));
                        $data['error_message1'] = $message;
                        $data['error_message2'] = '';

                        $sent_message = strpos($message, '"sent":true');

                        if ($sent_message) {
                            $data['sent_message_status'] = 'Yes';
                        } else {
                            $data['sent_message_status'] = 'No';
                        }

                        array_push($array, $data);
                    }
                }
            }
        }

        /* chat api*/
        $files = Storage::disk('logs')->files('chatapi');
        $chatapiarray = [];
        $files = array_reverse($files);
        foreach ($files as $file) {
            $total_log = 0;
            $yesterday = strtotime('yesterday');
            $today = strtotime('today');
            $path = base_path().'/';
            $content = Storage::disk('logs')->get($file);
            $escaped = str_replace('/', '\/', $path);
            $matches = [];
            $rows = preg_split('/\n+/', $content);

            $finaldata = [];
            foreach ($rows as $row) {
                if (substr($row, 0, 1) === '[') {
                    $row_cnt = 0;
                    $date = preg_match('#\[(.*?)\]#', $row, $match);

                    if (isset($_REQUEST['date']) && $_REQUEST['date'] != '') {
                        $date2 = substr($match[1], 0, 10);
                        if ($date2 == $_REQUEST['date']) {
                            $finaldata['date'] = isset($match[1]) ? $match[1] : '';
                            $message = substr($row, 35, strlen($row));
                            $finaldata['error_message1'] = isset($message) ? $message : '';
                            $row_cnt = 1;

                            $sent_message = strpos($message, '"sent":true');

                            if ($sent_message) {
                                $finaldata['sent_message_status'] = 'Yes';
                            } else {
                                $finaldata['sent_message_status'] = 'No';
                            }
                        }
                    } elseif (isset($request->date) && $request->date != '') {
                        $date2 = substr($match[1], 0, 10);
                        if ($date2 == $request->date) {
                            $finaldata['date'] = isset($match[1]) ? $match[1] : '';
                            $message = substr($row, 35, strlen($row));
                            $finaldata['error_message1'] = isset($message) ? $message : '';
                            $row_cnt = 1;

                            $sent_message = strpos($message, '"sent":true');

                            if ($sent_message) {
                                $finaldata['sent_message_status'] = 'Yes';
                            } else {
                                $finaldata['sent_message_status'] = 'No';
                            }
                        }
                    } else {
                        $finaldata['date'] = isset($match[1]) ? $match[1] : '';

                        $message = substr($row, 35, strlen($row));
                        $finaldata['error_message1'] = isset($message) ? $message : '';
                        $row_cnt = 1;

                        $sent_message = strpos($message, '"sent":true');

                        if ($sent_message) {
                            $finaldata['sent_message_status'] = 'Yes';
                        } else {
                            $finaldata['sent_message_status'] = 'No';
                        }
                    }
                }

                if (substr($row, 0, 7) === 'Message' && $row_cnt == 1) {
                    $message = substr($row, 8, strlen($row));
                    $message = str_replace('\n', ' ', $message);
                    $finaldata['error_message2'] = $message;
                    $finaldata['file'] = 'chatapi';
                    $finaldata['resend_details'] = '';
                    $finaldata['type'] = 2;

                    array_push($chatapiarray, $finaldata);
                    $finaldata = [];
                }
            }
        }
        $chatapiarray = array_reverse($chatapiarray);

        $f_array = array_merge($chatapiarray, $array);
        $farray = [];
        foreach ($f_array as $key => $value) {
            if (isset($_REQUEST['message_sent']) && $_REQUEST['message_sent'] != '' && isset($value['sent_message_status'])) {
                if ($_REQUEST['message_sent'] == $value['sent_message_status']) {
                    $farray[] = $value;
                }
            } elseif (isset($request->message_sent) && $request->message_sent != '' && isset($value['sent_message_status'])) {
                if ($request->message_sent == $value['sent_message_status']) {
                    $farray[] = $value;
                }
            } else {
                $farray[] = $value;
            }
        }

        usort($farray, function ($element1, $element2) {
            $datetime1 = strtotime($element1['date']);
            $datetime2 = strtotime($element2['date']);

            return $datetime2 - $datetime1;
        });
        /* end chat api */
        $page = $request->page;
        if ($page == null) {
            $page = 1;
        }

        $array = array_slice($farray, ($page * 10 - 10), 10);

        $roles = Auth::user()->roles->pluck('name')->toArray();

        $isAdmin = in_array('Admin', $roles);

        $quick_task_users = User::all();

        if ($request->ajax()) {
            $page = $request->page - 1;
            $sr = $page * 10 - 9;

            return view('logging.whatsapp-grid', compact('array', 'sr', 'isAdmin'));
        }

        return view('logging.whatsapp-logs', compact('array', 'isAdmin', 'quick_task_users'));
    }
}
