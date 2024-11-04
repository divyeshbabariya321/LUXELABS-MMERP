<?php

namespace App\Observers;

use App\Customer;
use App\Mails\Manual\ProductInStock;
use App\OutOfStockSubscribe;
use App\Product;
use Illuminate\Support\Facades\Mail;

class ProductObserver
{
    /**
     * Handle the out of stock subscribe "created" event.
     *
     * @param  \App\OutOfStockSubscribe  $outOfStockSubscribe
     */
    public function created(Product $product): void
    {
        //
    }

    /**
     * Handle the out of stock subscribe "updated" event.
     *
     * @param  \App\OutOfStockSubscribe  $outOfStockSubscribe
     */
    public function updated(Product $product): void
    {
        if ($product->stock_status == 1 && $product->isDirty('stock_status')) {
            $customerIds = OutOfStockSubscribe::where('product_id', $product->id)->where('status', 0)
                ->pluck('customer_id');
            $data['productName'] = $product['name'];
            $customerEmails = Customer::whereIn('id', $customerIds)->pluck('email')->toArray();
            foreach ($customerEmails as $customerEmail) {
                $email_to = $customerEmail;
                // Mail::send('emails.product_in_stock', $data, function ($message) use ($email_to) {
                //     $message->to($email_to, '')->subject('Product back to stock');
                // });
                Mail::to($email_to)->send(new ProductInStock($data));
            }
            OutOfStockSubscribe::where('product_id', $product->id)->where('status', 0)->update(['status' => 1]);
        }
    }

    /**
     * Handle the out of stock subscribe "deleted" event.
     */
    public function deleted(OutOfStockSubscribe $outOfStockSubscribe): void
    {
        //
    }

    /**
     * Handle the out of stock subscribe "restored" event.
     */
    public function restored(OutOfStockSubscribe $outOfStockSubscribe): void
    {
        //
    }

    /**
     * Handle the out of stock subscribe "force deleted" event.
     */
    public function forceDeleted(OutOfStockSubscribe $outOfStockSubscribe): void
    {
        //
    }
}
