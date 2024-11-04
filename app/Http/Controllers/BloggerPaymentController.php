<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBloggerPaymentRequest;
use App\Http\Requests\StoreBloggerPaymentRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Blogger;
use App\Helpers;
use App\BloggerPayment;
use Illuminate\Http\Request;
use App\Events\BloggerPaymentCreated;
use Exception;

class BloggerPaymentController extends Controller
{
    public function index(Blogger $blogger): View
    {
        $payments = $blogger->payments()->orderBy('payment_date')->paginate(50);

        return view('blogger.payments', [
            'payments'   => $payments,
            'blogger'    => $blogger,
            'currencies' => Helpers::currencies(),
        ]);
    }

    public function store(Blogger $blogger, StoreBloggerPaymentRequest $request): RedirectResponse
    {
        try {
            $status = 0;
            if ($request->get('paid_date') && $request->get('paid_amount')) {
                $status = 1;
            }
            $blogger_payment = $blogger->payments()->create([
                'payment_date'   => $request->get('payment_date'),
                'payable_amount' => $request->get('payable_amount'),
                'paid_date'      => $request->get('paid_date'),
                'paid_amount'    => $request->get('paid_amount'),
                'description'    => $request->get('description'),
                'currency'       => $request->get('currency'),
                'status'         => $status,
            ]);
            event(new BloggerPaymentCreated($blogger, $blogger_payment, $status));
        } catch (Exception $exception) {
            return redirect()->back()->withErrors('Couldn\'t store blogger payment');
        }

        return redirect()->back()->withSuccess('You have successfully added a blogger payment!');
    }

    public function update(Blogger $blogger, BloggerPayment $blogger_payment, UpdateBloggerPaymentRequest $request): RedirectResponse
    {
        try {
            $payment = $blogger->payments()->where('id', $blogger_payment->id)->first();
            $status  = 0;
            if ($request->get('paid_date') && $request->get('paid_amount')) {
                $status = 1;
            }
            $payment->fill([
                'payment_date'   => $request->get('payment_date'),
                'payable_amount' => $request->get('payable_amount'),
                'paid_date'      => $request->get('paid_date'),
                'paid_amount'    => $request->get('paid_amount'),
                'description'    => $request->get('description'),
                'currency'       => $request->get('currency'),
                'status'         => $status,
            ])->save();
            event(new BloggerPaymentCreated($blogger, $payment, $status));
        } catch (Exception $exception) {
            return redirect()->back()->withErrors('Couldn\'t update blogger payment');
        }

        return redirect()->back()->withSuccess('You have successfully updated blogger payment!');
    }

    public function destroy(Blogger $blogger, BloggerPayment $blogger_payment): RedirectResponse
    {
        $payment = $blogger->payments()->where('id', $blogger_payment->id)->firstOrFail();
        try {
            $payment->delete();
        } catch (Exception $exception) {
            return redirect()->back()->withErrors('Couldn\'t delete blogger payment');
        }

        return redirect()->back()->withSuccess('You have successfully deleted blogger payment!');
    }
}
