<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <!-- <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0" /> -->
  <title>Your order has been received</title>
  <style type="text/css">
    * {box-sizing:border-box; -moz-box-sizing:border-box; -webkit-box-sizing:border-box;}
    body {font-family: arial; font-size: 14px; color: #000000; margin: 0; padding: 0;}
    table {border-collapse: collapse;width: 100%;}
  </style>
</head>
<body>
<div style="width: 800px; margin: 30px auto; border: 2px solid #f4e7e1;">
<div style="width: 100%; text-align: center; padding-top: 30px; background-color: #f4e7e1;"><img src="{{ asset('images/emails/logo.png') }}" alt="" /></div>
<div style="width: 100%; background-color: #f4e7e1; padding: 0 30px;">
<table>
<tbody>
<tr>
<td>
<h1>Thanks for creating ticket.</h1>
</td>
</tr>
</tbody>
</table>
</div>
<div style="width: 100%; padding: 30px;">
<table style="height: 72px;" border="0" cellspacing="0" cellpadding="0">
<tbody>
<tr style="height: 21px;">
<td style="height: 21px; width: 736.008px;">
<h3 style="line-height: 1.24; font-size: 17px; font-weight: bold; letter-spacing: -0.1px; color: #898989; margin: 0; padding: 0;">Hello {{ $customer-&gt;name }}</h3>
</td>
</tr>
<tr style="height: 30px;">
<td style="height: 30px; width: 736.008px;">
<div style="font-size: 13px; line-height: 1.62; color: #898989; margin: 5px 0;">Your ticket has been created successfully.</div>
</td>
</tr>
<tr style="height: 21px;">
<td style="height: 21px; width: 736.008px;">
<div style="font-size: 13px; line-height: 1.62; color: #898989;">Your ticket ID - {{ $ticket-&gt;id }}</div>
</td>
</tr>
</tbody>
</table>
</div>
<div style="width: 100%; padding: 0px 30px;">
<table border="0" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td style="width: 25%;">
<div style="width: 100%; height: 10px; background-color: #898989;">&nbsp;</div>
</td>
<td style="width: 25%;">
<div style="width: 100%; height: 10px; background-color: #f4e7e1;">&nbsp;</div>
</td>
<td style="width: 25%;">
<div style="width: 100%; height: 10px; background-color: #f4e7e1;">&nbsp;</div>
</td>
<td style="width: 25%;">
<div style="width: 100%; height: 10px; background-color: #f4e7e1;">&nbsp;</div>
</td>
</tr>
</tbody>
</table>
<table border="0" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td style="width: 100%;">
<div style="font-weight: bold; font-size: 20px; color: #898989; padding-top: 10px;"><strong style="color: #000000;">Created date :</strong> {{ date("M d, Y",strtotime($ticket-&gt;created_at)) }}</div>
</td>
</tr>
</tbody>
</table>
</div>
<div style="width: 100%; padding: 30px;">
<table border="0" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td style="color: #898989; font-size: 13px; padding-top: 5px; padding-bottom: 10px;">&nbsp;</td>
</tr>
<tr>
<td style="color: #000000; font-size: 13px; padding-top: 5px; padding-bottom: 10px; font-weight: bold;">Team Solo Luxury</td>
</tr>
</tbody>
</table>
</div>
<div style="width: 100%; background-color: #f4e7e1; padding: 30px;">
<table border="0" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td style="padding-bottom: 25px;">
<table style="width: 70%;" align="left">
<tbody>
<tr>
<td>
<div style="float: left; margin-top: 3px;"><img src="{{ asset('images/emails/mail.png') }}" /></div>
<div style="margin-left: 30px;"><a style="font-size: 12px; color: #000000;" href="#">customercare@sololuxury.com</a></div>
</td>
</tr>
</tbody>
</table>
<table style="width: 30%;" align="right">
<tbody>
<tr>
<td style="text-align: right; padding-top: 6px;"><a style="display: inline-block; margin-left: 15px;" href="#"><img style="width: 6px;" src="{{ asset('images/emails/fb.png') }}" /></a> <a style="display: inline-block; margin-left: 15px;" href="#"><img style="width: 13px;" src="{{ asset('images/emails/tw.png') }}" /></a> <a style="display: inline-block; margin-left: 15px;" href="#"><img style="width: 13px;" src="{{ asset('images/emails/insta.png') }}" /></a> <a style="display: inline-block; margin-left: 15px;" href="#"><img style="width: 13px;" src="{{ asset('images/emails/linkin.png') }}" /></a></td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr style="border-top: 2px solid #e8dad3;">
<td style="padding: 25px 0 10px; text-align: center; font-size: 12px; color: #898989;">You are receiving this email as <a style="color: #000000;" href="#">customercare@sololuxury.com</a> is registered on <a style="color: #000000;" href="#">sololuxury.com</a>.</td>
</tr>
<tr>
<td style="text-align: center; font-size: 12px;">2020 sololuxury. <a style="color: #898989;" href="#">Privacy Policy</a> | <a style="color: #898989;" href="#">Terms of Use</a> | <a style="color: #898989;" href="#">Terms of Sale</a></td>
</tr>
</tbody>
</table>
</div>
</div>
</body>
</html>