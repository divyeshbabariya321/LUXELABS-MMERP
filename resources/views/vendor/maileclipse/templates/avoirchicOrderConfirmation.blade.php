<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta http-equiv="x-ua-compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Avoirchic </title>
   </head>
   <style type="text/css">
      @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
   </style>
   <body>
<table style="margin: auto;" width="591px">
<tbody>
<tr>
<td style="text-align: center;"><img src="https://www.avoir-chic.com/media/logo/default/avoirchic-logo.png" /></td>
</tr>
<tr>
<td style="text-align: center; font-size: 21px; font-weight: 600; font-family: 'Poppins'; padding-bottom: 5px;">THANK YOU FOR YOUR PURCHASE!</td>
</tr>
<tr>
<td style="border-top: 1px solid #000; text-align: center; border-bottom: 1px solid #000; font-size: 16px; font-family: 'Poppins'; padding: 5px 0px;">Your job is done it&rsquo;s our turn to take the wheel</td>
</tr>
<tr style="text-align: center;">
<td style="font-size: 17px; font-family: 'Poppins'; font-weight: 500; text-transform: uppercase;">Hi {{ $customer-&gt;name }},</td>
</tr>
<tr style="text-align: center;">
<td style="font-size: 16px; font-family: 'Poppins'; font-weight: 300;">Your order ID: <strong style="font-size: 16px; font-family: 'Poppins'; font-weight: 500;">{{ $order-&gt;order_id }}</strong></td>
</tr>
<tr>
<td style="text-align: center; font-family: 'Poppins'; font-size: 15px; padding-bottom: 10px;"><strong style="font-weight: 500;">Order Date:</strong> <span style="border-right: 1px solid #808080; padding-right: 8px;"> {{ date('F, d Y',strtotime($order-&gt;order_date)) }}</span> <strong style="font-weight: 500; padding-left: 3px;">Expected Shipment Date:</strong>{{ date('F, d Y',strtotime($order-&gt;date_of_delivery)) }}</td>
</tr>
<tr>
<td style="border: 1px solid #000;">@php $subtotal = 0; @endphp @foreach($order_products as $product) @php $subtotal += $product-&gt;price * $product-&gt;qty; @endphp@endforeach
<table width="100%">
<tbody>
<tr style="vertical-align: top;">
<td style="width: 110px; padding: 5px;"><img style="border: 1px solid #e6e6e6;" src="product_image.jpg" /></td>
<td>
<table style="text-align: left; line-height: 16px; padding: 5px;" width="100% ">
<tbody>
<tr>
<td style="font-family: 'Poppins'; font-weight: 600; font-size: 15px;">{{ $product-&gt;name }}</td>
</tr>
<tr>
<td style="font-family: 'Poppins'; font-weight: 400; font-size: 15px;">{{ $product-&gt;short_description }}</td>
</tr>
<tr>
<td style="font-family: 'Poppins'; font-weight: 400; font-size: 15px;">Color: <strong style="font-weight: 500;">{{ $product-&gt;color }}</strong></td>
</tr>
<tr>
<td style="font-family: 'Poppins'; font-weight: 400; font-size: 15px;">Size: <strong style="font-weight: 500;">{{ $product-&gt;size }}</strong></td>
</tr>
<tr>
<td style="font-family: 'Poppins'; font-weight: 400; font-size: 15px;">Quantity: <strong style="font-weight: 500;">{{ $product-&gt;qty }}</strong></td>
</tr>
</tbody>
</table>
</td>
<td style="font-family: 'Poppins'; font-weight: 400; font-size: 15px; text-align: right; padding: 5px;">{{ $product-&gt;currency }} {{ $product-&gt;product_price }}</td>
</tr>
</tbody>
<tfoot>
<tr>
<td style="border-top: 1px solid #000;" colspan="3">
<table style="padding: 5px; line-height: 21px;" width="100%">
<tbody>
<tr>
<td style="font-family: 'Poppins'; font-weight: 300; font-size: 15px;">Subtotal</td>
<td style="font-family: 'Poppins'; text-align: right; font-weight: 300; font-size: 15px;">{{ $order-&gt;store_currency_code }} {{$subtotal}}</td>
</tr>
<tr>
<td style="font-family: 'Poppins'; font-weight: 300; font-size: 15px;">Discount (SINGLE500)</td>
<td style="font-family: 'Poppins'; text-align: right; font-weight: 300; font-size: 15px;">{{ $order-&gt;store_currency_code }} 00.00</td>
</tr>
<tr>
<td style="font-family: 'Poppins'; font-weight: 500; font-size: 15px;">Grand Total</td>
<td style="font-family: 'Poppins'; font-weight: 500; text-align: right; font-size: 15px;">{{ $order-&gt;store_currency_code }} {{$subtotal}}</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tfoot>
</table>
</td>
</tr>
<tr>
<td>
<table style="text-align: center; border: 1px solid #000; margin-top: 10px; padding: 5px 0px 0px; line-height: 20px;" width="100%">
<tbody>
<tr>
<td style="font-size: 17px; font-family: 'Poppins'; font-weight: 500; text-transform: uppercase; padding: 5px;">Shipping Address</td>
</tr>
<tr>
<td style="font-size: 14px; font-family: 'Poppins'; font-weight: 300; text-transform: uppercase;">{{ $customer-&gt;name }}</td>
</tr>
<tr>
<td style="font-size: 14px; font-family: 'Poppins'; font-weight: 300; text-transform: uppercase;">{{ $customer-&gt;email }}</td>
</tr>
<tr>
<td style="font-size: 14px; font-family: 'Poppins'; font-weight: 300; text-transform: uppercase; line-height: 5px; padding-top: 8px;">{{ implode(",",[$customer-&gt;address,$customer-&gt;city,$customer-&gt;pincode,$customer-&gt;country,$customer-&gt;phone]) }}</td>
</tr>
<tr>
<td style="font-size: 14px; font-family: 'Poppins'; font-weight: 300; text-transform: uppercase;">{{ $customer-&gt;city.' '.$customer-&gt;country }}</td>
</tr>
<tr>
<td style="font-size: 14px; font-family: 'Poppins'; font-weight: 300; text-transform: uppercase; padding-bottom: 5px;">{{ $customer-&gt;phone }}</td>
</tr>
</tbody>
<tfoot>
<tr>
<td style="border-top: 1px solid #000;" colspan="1">
<table width="100%">
<tbody>
<tr>
<td style="font-family: 'Poppins'; font-size: 14px; font-weight: 300; padding: 0px 0px;"><strong style="font-weight: 500;">Payment:</strong> {{ $order-&gt;payment_mode }}</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tfoot>
</table>
</td>
</tr>
<tr>
<td style="text-align: center; font-family: 'Poppins'; font-size: 15px; font-weight: 500; padding: 8px 0px;">Still need some help?</td>
</tr>
<tr>
<td>
<table width="100%">
<tbody>
<tr>
<td style="text-align: right;"><a style="background: #367587; padding: 6px 25px; font-weight: 400; font-family: 'Poppins'; font-size: 15px; color: #fff; text-decoration: none; text-transform: uppercase;" href="#">Contact Us</a></td>
<td style="text-align: left;"><a style="background: #367587; padding: 6px 15px; font-weight: 400; font-family: 'Poppins'; font-size: 15px; color: #fff; text-decoration: none; text-transform: uppercase;" href="#">View Account</a></td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td style="text-align: center; font-family: 'Poppins'; font-size: 15; font-weight: 500; text-transform: uppercase; padding: 5px 0px;">The Avoirchic Team</td>
</tr>
</tbody>
</table>
</body>
</html>