<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta http-equiv="x-ua-compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>SOLO LUXURY  </title>
   </head>
   <style type="text/css">
      @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap');
   </style>
   <body style="background-color:#fef6f2;">
<table style="margin: auto; border-spacing: 0px;" width="789px">
<tbody>
<tr>
<td>
<table style="margin: auto; border-spacing: 0px;" width="728px">
<tbody>
<tr>
<td style="text-align: center; padding: 40px 0px 30px;" colspan="3"><img src="https://www.sololuxury.com/media/logo/default/logo.png" /></td>
</tr>
<tr>
<td style="text-align: center; font-size: 24px; color: #713213; font-weight: 600; font-family: 'Open Sans'; text-transform: capitalize; padding: 0px 0px 5px;"><span style="border-bottom: 1px solid #d3bbaf; padding-bottom: 2px;">You have received Store Credit </span></td>
</tr>
<tr>
<td style="text-align: center; font-family: 'Open Sans'; font-size: 20px; font-weight: bold; color: #5c5c5c; padding: 20px 0px 5px;">Hello {{ $customer-&gt;name }}</td>
</tr>
<tr>
<td style="text-align: center; font-family: 'Open Sans'; font-weight: 400; font-size: 16px; padding-bottom: 20px;">You have received Store Credit as detailed Below.</td>
</tr>
<tr>
<td colspan="1">
<table width="100%">
<tbody>
<tr>
<td style="color: #8a3a07; text-align: right; width: 50%; font-family: 'Open Sans'; padding-right: 50px; font-weight: 600; font-size: 16px;">Amount:</td>
<td>{{ $customer-&gt;credit }}</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td style="padding-bottom: 20px; padding-top: 15px;" colspan="1">
<table width="100%">
<tbody>
<tr>
<td style="color: #8a3a07; text-align: right; width: 50%; font-family: 'Open Sans'; font-weight: 600; padding-right: 50px; font-size: 16px;">Date:</td>
<td>{{ date("Y-m-d H:i:s") }}</td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td style="text-align: center; font-family: 'Open Sans'; font-weight: 500; color: #343a40; font-size: 17px; line-height: 24px; padding-bottom: 20px;">You can use the store credit to make any purchases <br />on our website <a style="color: #713213; text-decoration: none;" href="#"> SoloLuxury.com </a> This store credit will appear in My <br />Accounts section on our website.</td>
</tr>
<tr>
<td style="text-align: center; font-size: 17px; font-family: 'Open Sans'; font-weight: 400; line-height: 28px; padding-bottom: 20px;">Subject to applicable terms and conditions.<br />Store Credit is automatically applied at check out.</td>
</tr>
<tr>
<td style="text-align: center; font-family: 'Open Sans'; font-weight: 400; font-size: 14px; padding-top: 30px; padding-bottom: 5px; border-top: 1px solid #fbece5;" colspan="3">Thanks you for choosing SOLO LUXURY</td>
</tr>
<tr>
<td style="text-align: center; font-family: 'Open Sans'; font-weight: 400; font-size: 14px; padding-top: 0px; padding-bottom: 40px;" colspan="3">Happy Shopping</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</body>
</html>