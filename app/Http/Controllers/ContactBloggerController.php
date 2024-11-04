<?php

namespace App\Http\Controllers;
use App\Mails\Manual\ContactBlogger;
use App\Jobs\SendEmail;
use App\Helpers;

use App\Http\Requests\UpdateContactBloggerRequest;
use App\Http\Requests\StoreContactBloggerRequest;
use Illuminate\Http\RedirectResponse;
use App\Email;
use Illuminate\Http\Request;
use App\BloggerEmailTemplate;

class ContactBloggerController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            session()->flash('active_tab', 'contact_tab');

            return $next($request);
        });
    }

    public function store(StoreContactBloggerRequest $request): RedirectResponse
    {
        $blogger_contact         = new ContactBlogger($request->all());
        $blogger_contact->status = 'pending';
        $blogger_contact->save();

        $email_template = BloggerEmailTemplate::first();
        $subject        = $request->get('email_subject') ?: $email_template?->subject;
        $message        = $request->get('email_message') ?: $email_template?->message;
        $from_email     = Helpers::getFromEmail();

        $emailClass = (new ContactBlogger($subject, $message, $from_email))->build();

        $email = Email::create([
            'model_id'         => $blogger_contact->id,
            'model_type'       => ContactBlogger::class,
            'from'             => $from_email,
            'to'               => $blogger_contact->email,
            'subject'          => $emailClass->subject,
            'message'          => $emailClass->render(),
            'template'         => 'contact-blogger',
            'additional_data'  => '',
            'status'           => 'pre-send',
            'store_website_id' => null,
            'is_draft'         => 0,
        ]);
        
        SendEmail::dispatch($email)->onQueue('send_email');

        return redirect()->back()->withSuccess('Information stored successfully along with push email to the blogger!');
    }

    public function update(ContactBlogger $contact_blogger, UpdateContactBloggerRequest $request): RedirectResponse
    {
        $contact_blogger->fill($request->all());
        $contact_blogger->save();

        return redirect()->back()->withSuccess('Information updated successfully!');
    }

    public function destroy(ContactBlogger $contact_blogger): RedirectResponse
    {
        $contact_blogger->delete();

        return redirect()->back()->withSuccess('Information deleted successfully!');
    }
}
