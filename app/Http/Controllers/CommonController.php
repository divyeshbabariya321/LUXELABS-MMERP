<?php

namespace App\Http\Controllers;
use App\Jobs\SendEmail;
use App\EmailAddress;

use App\Charity;
use App\Customer;
use App\Email;
use App\Http\Requests\SendClanaderLinkEmailCommonRequest;
use App\MailinglistTemplate;
use App\Mails\Manual\PurchaseEmail;
use App\Supplier;
use App\User;
use App\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Exception;

class CommonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        //
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

    public function sendCommonEmail(request $request)
    {

        try {
            $this->validate($request, [
                'subject' => 'required|min:3|max:255',
                'message' => 'required',
                'cc.*' => 'nullable|email',
                'bcc.*' => 'nullable|email',
                'sendto' => 'required',
            ]);
            if (! empty($request->datatype == 'multi_user')) {
                $multi_email = explode(',', $request->sendto);
            } else {
                $multi_email = [$request->sendto];
            }
            $email = Mail::to($request->from_mail);
            foreach ($multi_email as $data) {
                if ($request->from_mail) {
                    $mail = EmailAddress::where('from_address', $request->from_mail)->first();
                    if ($mail) {
                        $fromEmail = $mail->from_address;
                        $fromName = $mail->from_name;
                        $config = config('mail');
                        unset($config['sendmail']);
                        $configExtra = [
                            'driver' => $mail->driver,
                            'host' => $mail->host,
                            'port' => $mail->port,
                            'from' => [
                                'address' => $mail->from_address,
                                'name' => $mail->from_name,
                            ],
                            'encryption' => $mail->encryption,
                            'username' => $mail->username,
                            'password' => $mail->password,
                        ];
                        Config::set('mail', array_merge($config, $configExtra));
                        (new \Illuminate\Mail\MailServiceProvider(app()))->register();
                    }
                }

                $file_paths = [];

                if ($request->hasFile('file')) {
                    foreach ($request->file('file') as $file) {
                        $filename = $file->getClientOriginalName();

                        $file->storeAs('documents', $filename, 'files');

                        $file_paths[] = "documents/$filename";
                    }
                }

                $cc = $bcc = [];
                if ($request->has('cc')) {
                    $cc = array_values(array_filter($request->cc));
                }
                if ($request->has('bcc')) {
                    $bcc = array_values(array_filter($request->bcc));
                }

                $emailClass = (new PurchaseEmail($request->subject, $request->message, $file_paths, ['from' => $fromEmail]))->build();

                $email->send(new PurchaseEmail($request->subject, $request->message, $file_paths));

                $params = [
                    'model_id' => $request->id,
                    'from' => $fromEmail,
                    'seen' => 1,
                    'to' => $data,
                    'subject' => $request->subject,
                    'message' => $emailClass->render(),
                    'template' => 'simple',
                    'additional_data' => json_encode(['attachment' => $file_paths]),
                    'cc' => $cc ?: null,
                    'bcc' => $bcc ?: null,
                ];
                if ($request->object) {
                    if ($request->object == 'vendor') {
                        $params['model_type'] = 'Vendor::class';
                    } elseif ($request->object == 'user') {
                        $params['model_type'] = 'User::class';
                    } elseif ($request->object == 'supplier') {
                        $params['model_type'] = 'Supplier::class';
                    } elseif ($request->object == 'customer') {
                        $params['model_type'] = 'Customer::class';
                    } elseif ($request->object == 'order') {
                        $params['model_type'] = 'Order::class';
                    } elseif ($request->object == 'charity') {
                        $params['model_type'] = 'Charity::class';
                    }
                }

                $email = Email::create($params);

                // SendEmail::dispatch($email)->onQueue('send_email');
                if (isset($request->from) && $request->from == 'sop') {
                    return response()->json(['success' => 'You have send email successfully !']);
                } else {
                    return redirect()->back()->withSuccess('You have successfully sent email!');
                }
            }
            //} else {
            /*
            if ($request->from_mail) {
                $mail = EmailAddress::where('from_address', $request->from_mail)->first();
                if ($mail) {
                    $fromEmail = $mail->from_address;
                    $fromName  = $mail->from_name;
                    $config    = config('mail');
                    unset($config['sendmail']);
                    $configExtra = [
                        'driver' => $mail->driver,
                        'host'   => $mail->host,
                        'port'   => $mail->port,
                        'from'   => [
                            'address' => $mail->from_address,
                            'name'    => $mail->from_name,
                        ],
                        'encryption' => $mail->encryption,
                        'username'   => $mail->username,
                        'password'   => $mail->password,
                    ];
                    Config::set('mail', array_merge($config, $configExtra));
                    (new \Illuminate\Mail\MailServiceProvider(app()))->register();
                }
            }

            $file_paths = [];

            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $file) {
                    $filename = $file->getClientOriginalName();

                    $file->storeAs('documents', $filename, 'files');

                    $file_paths[] = "documents/$filename";
                }
            }

            $cc = $bcc = [];
            if ($request->has('cc')) {
                $cc = array_values(array_filter($request->cc));
            }
            if ($request->has('bcc')) {
                $bcc = array_values(array_filter($request->bcc));
            }

            $emailClass = (new PurchaseEmail($request->subject, $request->message, $file_paths, ['from' => $fromEmail]))->build();

            $params = [
                'model_id'        => $request->id,
                'from'            => $fromEmail,
                'seen'            => 1,
                'to'              => $request->sendto,
                'subject'         => $request->subject,
                'message'         => $emailClass->render(),
                'template'        => 'simple',
                'additional_data' => json_encode(['attachment' => $file_paths]),
                'cc'              => $cc ?: null,
                'bcc'             => $bcc ?: null,
            ];
            if ($request->object) {
                if ($request->object == 'vendor') {
                    $params['model_type'] = 'Vendor::class';
                } elseif ($request->object == 'user') {
                    $params['model_type'] = 'User::class';
                } elseif ($request->object == 'supplier') {
                    $params['model_type'] = 'Supplier::class';
                } elseif ($request->object == 'customer') {
                    $params['model_type'] = 'Customer::class';
                } elseif ($request->object == 'order') {
                    $params['model_type'] = 'Order::class';
                } elseif ($request->object == 'charity') {
                    $params['model_type'] = 'Charity::class';
                }
            }

            $email = Email::create($params);


            SendEmail::dispatch($email)->onQueue('send_email');
            if (isset($request->from) && $request->from == 'sop') {
                return response()->json(['success' => 'You have send email successfully !']);
            } else {
                return redirect()->back()->withSuccess('You have successfully sent email!');
            }*/
            //}
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function sendClanaderLinkEmail(SendClanaderLinkEmailCommonRequest $request): RedirectResponse
    {
        $objects = [
            'vendor' => Vendor::class,
            'user' => User::class,
            'supplier' => Supplier::class,
            'customer' => Customer::class,
            'charity' => Charity::class,
        ];
        $multi_email = [];
        if (isset($request->send_to) && count($request->send_to) > 0) {
            $multi_email = $request->send_to;
        }

        try {
            foreach ($multi_email as $data) {
                if ($request->from_mail) {
                    $mail = EmailAddress::where('from_address', $request->from_mail)->first();
                    if ($mail) {
                        $fromEmail = $mail->from_address;
                        $fromName = $mail->from_name;
                        $config = config('mail');
                        unset($config['sendmail']);
                        $configExtra = [
                            'driver' => $mail->driver,
                            'host' => $mail->host,
                            'port' => $mail->port,
                            'from' => [
                                'address' => $mail->from_address,
                                'name' => $mail->from_name,
                            ],
                            'encryption' => $mail->encryption,
                            'username' => $mail->username,
                            'password' => $mail->password,
                        ];
                        Config::set('mail', array_merge($config, $configExtra));
                        (new \Illuminate\Mail\MailServiceProvider(app()))->register();
                    }
                }

                $file_paths = [];

                if ($request->hasFile('file')) {
                    foreach ($request->file('file') as $file) {
                        $filename = $file->getClientOriginalName();

                        $file->storeAs('documents', $filename, 'files');

                        $file_paths[] = "documents/$filename";
                    }
                }

                $cc = $bcc = [];
                if ($request->has('cc')) {
                    $cc = array_values(array_filter($request->cc));
                }
                if ($request->has('bcc')) {
                    $bcc = array_values(array_filter($request->bcc));
                }

                $emailClass = (new PurchaseEmail($request->subject, $request->message, $file_paths, ['from' => $fromEmail]))->build();

                $params = [
                    'model_id' => $request->id ?? null,
                    'from' => $fromEmail,
                    'seen' => 1,
                    'to' => $data,
                    'subject' => $request->subject,
                    'message' => $emailClass->render(),
                    'template' => 'simple',
                    'additional_data' => json_encode(['attachment' => $file_paths]),
                    'cc' => $cc ?: null,
                    'bcc' => $bcc ?: null,
                ];
                if ($request->object) {
                    if ($request->object == 'vendor') {
                        $params['model_type'] = 'Vendor::class';
                    } elseif ($request->object == 'user') {
                        $params['model_type'] = 'User::class';
                    } elseif ($request->object == 'supplier') {
                        $params['model_type'] = 'Supplier::class';
                    } elseif ($request->object == 'customer') {
                        $params['model_type'] = 'Customer::class';
                    } elseif ($request->object == 'order') {
                        $params['model_type'] = 'Order::class';
                    } elseif ($request->object == 'charity') {
                        $params['model_type'] = 'Charity::class';
                    }
                }

                $email = Email::create($params);

                SendEmail::dispatch($email)->onQueue('send_email');
            }

            return redirect()->back()->withSuccess('You have successfully sent email!');
        } catch (Exception $e) {
            $msg = $e->getMessage();

            return redirect()->back()->withErrors($msg);
        }
    }

    public function getMailTemplate(request $request): JsonResponse
    {
        if (isset($request->mailtemplateid)) {
            $data = MailinglistTemplate::select('static_template', 'subject')->where('id', $request->mailtemplateid)->first();
            $static_template = $data->static_template;
            $subject = $data->subject;
            if (! $static_template) {
                return response()->json(['error' => 'unable to get template', 'success' => false]);
            }

            return response()->json(['template' => $static_template, 'subject' => $subject, 'success' => true]);
        }

        return response()->json(['error' => 'unable to get template', 'success' => false]);
    }
}
