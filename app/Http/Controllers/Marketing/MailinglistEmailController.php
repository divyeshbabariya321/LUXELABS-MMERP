<?php

namespace App\Http\Controllers\Marketing;

use App\GmailDataList;
use App\Http\Controllers\Controller;
use App\Image;
use App\LogRequest;
use App\Mailinglist;
use App\MailinglistEmail;
use App\MailinglistTemplate;
use App\StoreWebsite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class MailinglistEmailController extends Controller
{
    public function index(Request $request): View
    {
        $audience = Mailinglist::all();
        $templates = MailinglistTemplate::all();
        $images = Image::all();
        $images_gmail = GmailDataList::all();
        $query = MailinglistEmail::with('audience', 'template');
        $term = $request->term;
        $date = $request->date;
        if ($request->term != null) {
            $query = $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', '%'.$request->term.'%')
                    ->orWhere('html', 'like', '%'.$request->term.'%');
            });
        }

        if (! empty($request->date)) {
            $query = $query->where(function ($q) use ($request) {
                $q->where('created_at', 'like', '%'.$request->date.'%')
                    ->orWhere('scheduled_date', 'like', '%'.$request->date.'%');
            });
        }

        $mailings = $query->orderByDesc('created_at')->get();

        return view('marketing.mailinglist.sending-email.index', compact('audience', 'templates', 'images', 'images_gmail', 'mailings', 'term', 'date'));
    }

    public function ajaxIndex(Request $request): JsonResponse
    {
        $content = null;

        $mtemplate = MailinglistTemplate::find($request->id);
        if (! empty($mtemplate)) {
            $content = @(string) view($mtemplate->mail_tpl);
        }

        return response()->json(['template_html' => $content]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->all();

        $validator = Validator::make($request->all(), [
            'template_id' => 'required',
            'scheduled_date' => 'required',
            'mailinglist_id' => 'required',
            'subject' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->getMessageBag()->toArray()]);
        }
        //getting mailing list

        $mailing_item = new MailinglistEmail;
        $mailing_item->mailinglist_id = $data['mailinglist_id'];
        $mailing_item->template_id = $data['template_id'];
        $mailing_item->html = $data['html'];
        $mailing_item->subject = $data['subject'];
        $mailing_item->scheduled_date = $data['scheduled_date'];
        $mailing_item->html = $data['html'];

        $list = Mailinglist::find($data['mailinglist_id']);
        $website = StoreWebsite::where('id', $list->website_id)->first();
        $api_key = (isset($website->send_in_blue_api) && $website->send_in_blue_api != '') ? $website->send_in_blue_api : config('env.SEND_IN_BLUE_API');
        $startTime = date('Y-m-d H:i:s', LARAVEL_START);
        $paramters = [
            'name' => $mailing_item->subject,
            'subject' => $mailing_item->subject,
            'run_at' => $mailing_item->scheduled_date,
            'template_content' => $mailing_item->html,
        ];

        if ($list->service) {
            if ($list->service && isset($list->service->name)) {
                if ($list->service->name == 'AcelleMail') {
                    $curl = curl_init();
                    $url = "http://165.232.42.174/api/v1/campaign/create/' . $list->remote_id . '?api_token=' . config('env.ACELLE_MAIL_API_TOKEN')";

                    curl_setopt_array($curl, [
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => $paramters,
                    ]);

                    $response = curl_exec($curl);
                    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    $response = json_decode($response); //decode response
                    $paramters = [];

                    LogRequest::log($startTime, $url, 'POST', json_encode($paramters), $response, $httpcode, Controllers\MailinglistEmailController::class, 'store');

                    if (! empty($response->campaign)) {
                        $mailing_item->api_template_id = $response->campaign;
                    }
                } else {
                    if (! empty($data['html'])) {
                        $curl = curl_init();
                        $data = [
                            'sender' => [
                                'name' => 'Luxury Unlimited',
                                'id' => 1,
                            ],
                            'htmlContent' => $this->utf8ize($mailing_item->html),
                            'templateName' => $mailing_item->subject,
                            'subject' => $mailing_item->subject,
                        ];

                        $url = 'https://api.sendinblue.com/v3/smtp/templates';
                        curl_setopt_array($curl, [
                            CURLOPT_URL => $url,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data),
                            CURLOPT_HTTPHEADER => [
                                'api-key: '.$api_key,
                                'Content-Type: application/json',
                            ],
                        ]);
                        $response = curl_exec($curl);
                        $response = json_decode($response); //response decode
                        if (! empty($response->id)) {
                            $mailing_item->api_template_id = $response->id;
                        }
                        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                        curl_close($curl);

                        LogRequest::log($startTime, $url, 'POST', json_encode($data), $response, $httpcode, Controllers\MailinglistEmailController::class, 'store');
                    }
                }
            }
        }
        $mailing_item->save();

        return response()->json([
            'item' => view('partials.mailing-template.template', [
                'item' => $mailing_item,
            ])->render(),
        ]);
    }

    public function utf8ize($d)
    {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = utf8ize($v);
            }
        } elseif (is_string($d)) {
            return utf8_encode($d);
        }

        return $d;
    }

    public function showMailingList(Request $request): JsonResponse
    {
        $data = MailinglistEmail::where('id', $request->id)->first();

        return response()->json([
            'html' => $data,
        ]);
    }

    public function getStats(Request $request)
    {
        dd($request);
    }
}
