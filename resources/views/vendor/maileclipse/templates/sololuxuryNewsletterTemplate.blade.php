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
   @php
    $html = '<table style="margin: auto; border-spacing: 0px; height: 527px; width: 721px;" width="728px">
                     <tbody>
                        <tr style="height: 40px;">
                           <td style="text-align: center; padding: 40px 0px 30px 40px; height: 40px; width: 681px;" colspan="3"><a href="../../../images/sololuxury-logo.png"><img src="../../../images/sololuxury-logo.png" width="166" height="36" /></a></td>
                        </tr>
                        <tr style="height: 36px;">
                           <td style="text-align: center; text-decoration: underline; text-underline-offset: 5px; padding: 15px 0px; background: #fbece5; font-family: \'Open Sans\'; font-size: 26px; font-weight: bold; color: #713213; height: 36px; width: 721px;" colspan="3">Huge Discounts From</td>
                        </tr>
                        <tr style="height: 24px;">
                           <td style="text-align: left; font-family: \'Open Sans\'; font-size: 18px; font-weight: 400; color: #4b4b4b; padding: 20px 10px 0px; height: 24px; width: 701px;" colspan="3">On Sale From</td>
                        </tr>
                        <tr style="height: 30px;">
                           <td style="text-align: left; font-family: \'Open Sans\'; font-size: 22px; font-weight: 600; letter-spacing: -1px; color: #713213; padding: 0px 10px 15px; height: 30px; width: 432px;" colspan="2">Boutique Moschino</td>
                           <td style="text-align: right; padding-bottom: 15px; height: 30px; width: 267px;" colspan="1"><a style="text-transform: uppercase; margin-right: 10px; font-family: \'Open Sans\'; font-size: 12px; font-weight: bold; padding: 8px 15px; border-radius: 50px; background: #713213; color: #fff; text-decoration: none;" href="#">Shop Now</a></td>
                        </tr>
                        <tr style="height: 96px;">';
    $i = 1;
    foreach($products as $product) {
        if($i%3==0){   $html .= '</tr><tr style="height: 96px;">';}
        $html .= '<td style="padding-bottom: 45px; height: 96px; width: 224px;">
                  <table>
                     <tbody>
                        <tr>
                           <td><img src="'.$product->images[0].'" width="226" height="260" /></td>
                        </tr>
                        <tr>
                           <td style="text-align: center; font-family: Open Sans; font-size: 18px; font-weight: 500; color: #4b4b4b;">'.$product["name"].'</td>
                        </tr>
                        <tr>
                           <td style="text-align: center; font-family: Open Sans; font-size: 15px; font-weight: 500; color: #4b4b4b;">EUR'.$product["price"].' <span style="font-weight: 400; padding-left: 10px; text-decoration: line-through; font-size: 14px;">EUR'.$product['price_eur_special'].'</span></td>
                        </tr>
                     </tbody>
                  </table>
               </td>';
    $i++;
    } 
    $html .= '</tr>
                        <tr style="height: 37px;">
                           <td style="text-align: left; font-family: \'Open Sans\'; font-size: 15px; font-weight: 500; color: #000000; padding: 0px 0px 15px 10px; height: 37px; width: 442px;" colspan="2">customercare@sololuxury.com</td>
                           <td style="padding: 0px 10px 15px; text-align: right; height: 37px; width: 249px;" colspan="1"><a style="background-color: #ffff; border-radius: 50px; height: 14px; line-height: 14px; display: inline-block; padding: 10px; width: 14px; text-align: center; margin-right: 10px;" href="#"><img src="../../../images/newsletter-facebook.png" width="8" height="13" /></a> <a style="background-color: #ffff; border-radius: 50px; height: 14px; line-height: 14px; display: inline-block; padding: 10px; width: 14px; text-align: center; margin-right: 10px;" href="#"><img src="../../../images/newsletter-twitter.png" width="13" height="11" /></a> <a style="background-color: #ffff; border-radius: 50px; height: 14px; line-height: 14px; display: inline-block; padding: 10px; width: 14px; text-align: center; margin-right: 10px;" href="#"><img src="../../../images/newsletter-insta.png" width="12" height="12" /></a> <a style="background-color: #ffff; border-radius: 50px; height: 14px; line-height: 14px; display: inline-block; padding: 10px; width: 14px; text-align: center;" href="#"><img src="../../../images/newsletter-linkedin.png" width="13" height="12" /></a></td>
                        </tr>
                        <tr style="height: 19px;">
                           <td style="text-align: center; font-family: \'Open Sans\'; font-weight: 500; font-size: 14px; padding-top: 30px; padding-bottom: 10px; background: #fbece5; height: 19px; width: 719px;" colspan="3">You are receiving this email as customercare@sololuxury.com is registered on sololuxury.com</td>
                        </tr>
                        <tr style="height: 19px;">
                           <td style="text-align: center; font-family: \'Open Sans\'; font-weight: 500; font-size: 14px; padding-top: 0px; padding-bottom: 40px; background: #fbece5; height: 19px; width: 719px;" colspan="3">2020 sololuxury.<a style="color: #000; text-decoration: none;" href="#"> Privacy Policy</a> |<a style="color: #000; text-decoration: none;" href="#"> Terms of Use</a> | <a style="color: #000; text-decoration: none;" href="#">Terms of Sale</a></td>
                        </tr>
                     </tbody>
                  </table>'; 
   @endphp
   <body style="background-color:#fef6f2;">
<table style="margin: auto; border-spacing: 0px;" width="789px">
<tbody>
<tr>
<td>
<div>{!!$html!!}</div>
</td>
</tr>
</tbody>
</table>
</body>
</html>