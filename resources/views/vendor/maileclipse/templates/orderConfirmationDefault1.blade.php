<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>SOLO LUXURY </title>
</head>
<style type="text/css">
	@import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap');


	@media (max-width: 767px) {
		table.main_table {
			width: 100% !important;
		}

		td.email {
			padding-left: 20px !important;
			width: 50% !important;
		}

		td.social_icon {
			padding: 0px 20px 25px !important;
			width: 50% !important;
		}

		.padding_marg {
			padding-left: 20px !important;
			padding-right: 20px !important;
		}

		.padding_bottom {
			padding-bottom: 20px !important;
		}
	}
</style>

<body style="background-color:#fef6f2;">
<table class="main_table" style="margin: auto; border-spacing: 0px;" width="789px">
<tbody>
<tr>
<td>@php $total = $product_total = 0; @endphp @foreach ($order->order_product as $order_product) @php $product = $order_product->product @endphp @if($product) @php $product_total += $order_product->product_price; @endphp @php $total += $order_product->order_price; @endphp @endif @endforeach
<table style="margin: auto; border-spacing: 0px;" width="100%">
<tbody>
<tr>
<td style="text-align: center; padding: 40px 0px 15px;" colspan="3"><img src="logo.png" /></td>
</tr>
<tr>
<td class="padding_bottom" style="text-align: center; font-family: 'Open Sans'; font-size: 28px; text-transform: uppercase; font-weight: bold; color: #713213; padding-bottom: 40px;" colspan="3">You're all sorted.</td>
</tr>
<tr>
<td class="padding_marg" style="text-align: left; font-family: 'Open Sans'; font-weight: bold; font-size: 20px; color: #010101; padding: 0px 30px 10px;">Hello {{$customer->name}}</td>
</tr>
<tr>
<td class="padding_marg" style="text-align: left; color: #010101; font-weight: 500; font-size: 15px; font-family: 'Open Sans'; padding: 0px 30px 5px;">You've got great taste! We're so glad you chose noon.</td>
</tr>
<tr>
<td class="padding_marg" style="text-align: left; color: #010101; font-weight: 500; font-size: 15px; font-family: 'Open Sans'; padding: 0px 30px 30px;" colspan="3">Your order {{$order->order_id}} has been received and is currently being processed by our crew.</td>
</tr>
<tr>
<td class="padding_marg" style="padding: 10px 30px 5px;" colspan="3">&nbsp;</td>
</tr>
<tr>
<td style="text-align: left; font-family: 'Open Sans'; font-size: 18px; font-weight: 600; padding-left: 30px;" colspan="2">Order:</td>
<td style="text-align: right; font-family: 'Open Sans'; font-size: 18px; font-weight: 600; padding-right: 30px;" colspan="1">{{date("M|d|Y",strtotime($order->created_at))}}</td>
</tr>
<tr>
<td style="padding: 30px 0px 15px;" colspan="3">
<table class="padding_marg" style="border-spacing: 0px; padding: 30px; background: #fbece5;" width="100%">
<tbody>
<tr>
<td style="padding: 0px;" colspan="2">
<table style="border-spacing: 0px;" width="80%">
<tbody>
<tr>
<td style="text-align: left; font-family: 'Open Sans'; font-size: 16px; font-weight: bold; padding: 0px 0 10px;"><span style="display: inline-block; border-bottom: 1px solid #d0c3bd; width: 220px; padding-bottom: 8px;"> Order Summery </span></td>
</tr>
<tr>
<td style="font-family: 'Open Sans'; font-size: 13px; font-weight: 500; color: #010101; padding-bottom: 5px;">Order No: <span style="padding-left: 24px;"> {{$order->order_id}} </span></td>
</tr>
<tr>
<td style="font-family: 'Open Sans'; font-size: 13px; font-weight: 500; color: #010101; padding-bottom: 5px;">Order Total: <span style="padding-left: 10px;"> {{$order->currency}}{{$product_total}} </span></td>
</tr>
<tr>
<td style="font-family: 'Open Sans'; font-size: 13px; font-weight: 500; color: #010101; padding-bottom: 5px;">Payment: <span style="padding-left: 28px;"> {{ucwords($order->payment_mode)}} </span></td>
</tr>
</tbody>
</table>
</td>
<td width="100%">&nbsp;</td>
<td colspan="1">
<table style="border-spacing: 0px;" width="80%">
<tbody>
<tr>
<td style="text-align: left; font-family: 'Open Sans'; font-size: 16px; font-weight: bold; padding: 0px 0 10px;"><span style="display: inline-block; border-bottom: 1px solid #d0c3bd; width: 220px; padding-bottom: 8px;">Shipping Address </span></td>
</tr>
<tr>
<td style="font-family: 'Open Sans'; font-size: 13px; font-weight: bold; color: #010101; padding-bottom: 5px;">{{$order->customer->name}}</td>
</tr>
<tr>
<td style="font-family: 'Open Sans'; font-size: 13px; line-height: 20px; font-weight: 500; color: #010101; padding-bottom: 5px;">{{implode(",",[$customer->address,$customer->city,$customer->pincode,$customer->country,$customer->phone])}}</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td class="padding_marg" style="padding: 0px 30px 15px;" colspan="3"><span style="display: block; padding: 10px 0px; border-bottom: 1px solid #e2cfc5; width: 100%; font-family: 'Open Sans'; font-size: 22px; font-weight: bold;"> Confirmed Items </span></td>
</tr>
<tr>
<td class="padding_marg" style="padding: 0px 30px 20px;" colspan="3">@php $total = $product_total = 0; @endphp @foreach ($order->order_product as $order_product) @php $product = $order_product->product @endphp @if($product)@php $product_total += $order_product->product_price; @endphp @php $total += $order_product->order_price; @endphp @endif @endforeach
<table style="border-bottom: 1px solid #e2cfc5; padding-bottom: 10px;" width="100%">
<tbody>
<tr>
<td style="width: 120px; padding-left: 10px;"><img style="border: 1px solid #bb9887;" src="{{ ($order_product->product and $order_product->product->getMedia(config('constants.attach_image_tag'))->first()) ? getMediaUrl($order_product->product->getMedia(config('constants.attach_image_tag'))->first()) : asset('images/no-image.jpg') }}" /></td>
<td style="vertical-align: top; padding: 0px; width: 50%;">
<table width="100%">
<tbody>
<tr>
<td style="font-family: 'Open Sans'; font-size: 16px; font-weight: 600;">{{ ($product->brands) ? ucwords($product->brands->name) : "" }}</td>
</tr>
<tr>
<td style="font-family: 'Open Sans'; font-size: 15px; font-weight: 500; color: #010101; padding-bottom: 10px;">{{ $product->name }}</td>
</tr>
<tr>
<td style="font-family: 'Open Sans'; font-weight: 500; font-size: 14px; color: #010101;">Receive it by {{ date("M d, Y",strtotime($order->estimated_delivery_date)) }}</td>
</tr>
</tbody>
</table>
</td>
<td>
<table width="100%">
<tbody>
<tr>
<td style="font-family: 'Open Sans'; font-size: 14px; font-weight: 400; display: flex; align-items: center; text-align: center; justify-content: center;">Qty <span style="height: 30px; width: 40px; display: inline-block; font-family: 'Open Sans'; font-weight: 400; font-size: 15px; color: #010101; line-height: 30px; text-align: center; background: #fff; border: 1px solid #bbb3af; margin-left: 10px;"> {{ $order_product->qty }} </span></td>
</tr>
</tbody>
</table>
</td>
<td>
<table width="100%">
<tbody>
<tr>
<td style="font-family: 'Open Sans'; font-size: 20px; font-weight: bold; text-align: right;">{{ $order->currency }} {{$order_product->order_price}}</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td class="padding_marg" style="text-align: right; font-family: 'Open Sans'; font-size: 16px; font-weight: 500; padding: 0px 30px 5px;" colspan="3">Sub Total : <span style="font-weight: 600; font-size: 20px; padding-left: 15px;"> {{ $order->currency }}{{$product_total}} </span></td>
</tr>
<tr>
<td class="padding_marg" style="text-align: right; font-family: 'Open Sans'; font-size: 16px; font-weight: 400; padding: 0px 30px 30px;" colspan="3"><span style="font-weight: 600;"> Total </span> (Inclusive of VAT) : <span style="font-weight: 600; font-size: 20px; padding-left: 15px;"> {{ $order->currency }}{{$product_total}} </span></td>
</tr>
<tr>
<td class="padding_marg" style="font-family: 'Open Sans'; font-size: 15px; font-weight: 500; text-align: left; padding: 15px 30px 5px;" colspan="3">We'll let you know when your order is on its way to you so you can really get excited about it.</td>
</tr>
<tr>
<td class="padding_marg" style="font-family: 'Open Sans'; font-size: 15px; font-weight: bold; text-align: left; padding: 0px 30px 40px;" colspan="3">Team Solo Luxury</td>
</tr>
<tr>
<td class="email" style="text-align: left; font-family: 'Open Sans'; font-size: 15px; font-weight: 500; color: #000; padding: 0 0 25px; padding-left: 30px;" colspan="2">customercare@sololuxury.com</td>
<td class="social_icon" style="padding: 0 30px 25px; text-align: right;" colspan="1"><a style="background-color: #ffff; border-radius: 50px; height: 14px; line-height: 14px; display: inline-block; padding: 10px; width: 14px; text-align: center; margin-right: 10px;" href="#"><img src="facebook.png" /></a> <a style="background-color: #ffff; border-radius: 50px; height: 14px; line-height: 14px; display: inline-block; padding: 10px; width: 14px; text-align: center; margin-right: 10px;" href="#"><img src="twitter.png" /></a> <a style="background-color: #ffff; border-radius: 50px; height: 14px; line-height: 14px; display: inline-block; padding: 10px; width: 14px; text-align: center; margin-right: 10px;" href="#"><img src="insta.png" /></a> <a style="background-color: #ffff; border-radius: 50px; height: 14px; line-height: 14px; display: inline-block; padding: 10px; width: 14px; text-align: center;" href="#"><img src="linkedin.png" /></a></td>
</tr>
<tr>
<td style="text-align: center; font-family: 'Open Sans'; font-weight: 500; font-size: 14px; padding-top: 30px; padding-bottom: 10px; background: #fbece5;" colspan="3">You are receiving this email as customercare@sololuxury.com is registered on sololuxury.com</td>
</tr>
<tr>
<td style="text-align: center; font-family: 'Open Sans'; font-weight: 500; font-size: 14px; padding-top: 0px; padding-bottom: 40px; background: #fbece5;" colspan="3">2020 sololuxury.<a style="color: #000; text-decoration: none;" href="#"> Privacy Policy</a> |<a style="color: #000; text-decoration: none;" href="#"> Terms of Use</a> | <a style="color: #000; text-decoration: none;" href="#">Terms of Sale</a></td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</body>

</html>