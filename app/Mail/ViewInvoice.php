<?php

namespace App\Mail;

use Illuminate\Support\Facades\File;
use Dompdf\Dompdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ViewInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;

    public $orderItems;

    public $orders;

    public $orderTotal;

    public $invoice;

    public $billing;

    public $shipping;

    public $duty_tax;

    public $param;

    /**
     * Create a new message instance.
     *
     * @param mixed $params
     *
     * @return void
     */
    public function __construct($params)
    {
        $this->param = $params;
        if (! empty($params['orders'])) {
            $this->orders = $params['orders'];
        }

        if (! empty($params['invoice'])) {
            $this->invoice = $params['invoice'];
        }

        if (! empty($params['orders'])) {
            $this->orderItems = $this->viewOrderProductBlock($this->orders);
        }

        if (! empty($params['orders'])) {
            $order_pro         = $this->orders[0]->order_product;
            $website_code_data = $this->viewDutyTax($this->orders);

            $duty_shipping = 0;
            if ($website_code_data != null) {
                $product_qty = count($order_pro);

                $code = $website_code_data->website_code->code;

                $duty_countries     = $website_code_data->website_code->duty_of_country;
                $shipping_countries = $website_code_data->website_code->shipping_of_country($code);

                $duty_amount     = ($duty_countries->default_duty * $product_qty);
                $shipping_amount = ($shipping_countries->price * $product_qty);

                if ($duty_amount + $shipping_amount != '' && $duty_amount + $shipping_amount != 'undefined' && $duty_amount + $shipping_amount != null) {
                    $duty_shipping = $duty_amount + $shipping_amount;
                }
            }

            $this->duty_tax = $duty_shipping;
        }

        $this->customer = $this->orders !== null ? $this->getCustomerDetails($this->orders[0]) : null;
        $this->billing  = $this->orders !== null ? $this->getBillingDetails($this->orders[0]) : null;
        $this->shipping = $this->orders !== null ? $this->getShippingDetails($this->orders[0]) : null;
    }

    public function preview()
    {
        return view('maileclipse::templates.viewInvoice', [
            'orderItems'   => $this->orderItems,
            'customer'     => $this->customer,
            'orders'       => $this->orders,
            'orderTotal'   => $this->orderTotal,
            'invoice'      => $this->invoice,
            'buyerDetails' => $this->customer,
            'order'        => $this->orders[0],
            'billing'      => $this->billing,
            'shipping'     => $this->shipping,
        ]);
    }

    //TODO download function - added by jammer
    public function download()
    {
        $html = view('maileclipse::templates.orderInvoice', [
            'orderItems'   => $this->orderItems,
            'customer'     => $this->customer,
            'orders'       => $this->orders,
            'order'        => $this->orders[0],
            'orderTotal'   => $this->orderTotal,
            'buyerDetails' => $this->customer,
            'billing'      => $this->billing,
            'shipping'     => $this->shipping,
            'invoice'      => $this->invoice,
            'duty_tax'     => $this->duty_tax,
        ]);
        $pdf = new Dompdf();
        $pdf->loadHtml($html);
        $pdf->render();
        if (array_key_exists('savePDF', $this->param)) {
            $path = public_path() . '/pdf';
            File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
            $file = time() . 'invoice.pdf';
            file_put_contents('pdf/' . $file, $pdf->output());

            return $file;
        } else {
            $pdf->stream(date('Y-m-d H:i:s') . 'invoice.pdf');
        }
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this->view('maileclipse::templates.viewInvoice');
    }

    public function viewOrderProductBlock($orders)
    {
        $string = '';
        if (! empty($orders)) {
            foreach ($orders as $order) {
                foreach ($order->order_product as $products) {
                    if ($products->product) {
                        $string .= '<tr>
                                    <td>' . $products->product->name . ' ' . $products->product->short_description . '</td>
                                    <td>' . $products->made_in . '</td>
                                    <td>' . $products->qty . '</td>
                                    <td>INR</td>
                                    <td>INR ' . $products->product_price . '</td>
                                    </tr>';
                    }
                    $this->orderTotal += $products->product_price;
                }
            }
        }

        return $string;
    }

    public function getCustomerDetails($order)
    {
        if (isset($order->customer)) {
            return $order->customer;
        }

        return false;
    }

    public function getBillingDetails($order)
    {
        return $order->billingAddress();
    }

    public function getShippingDetails($order)
    {
        return $order->shippingAddress();
    }

    public function viewDutyTax($order)
    {
        return $order[0]->duty_tax;
    }
}
