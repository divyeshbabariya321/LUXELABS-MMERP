<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <!-- <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0" /> -->
  <title>Your ticket has been received</title>
  <style type="text/css">
    * {box-sizing:border-box; -moz-box-sizing:border-box; -webkit-box-sizing:border-box;}
    body {font-family: arial; font-size: 14px; color: #000000; margin: 0; padding: 0;}
    table {border-collapse: collapse;width: 100%;}
  </style>
</head>
<body>

   <div style="width: 800px; margin: 30px auto; border:2px solid #f4e7e1;">
      <div style="width: 100%;text-align: center; padding-top: 30px;background-color: #f4e7e1;">
      </div>
      <div style="width: 100%;background-color: #f4e7e1;padding: 0 30px;">
        <table>
          <tbody>
            <tr>
              <td>
                <h1>Thanks for creating a ticket.</h1>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div style="width: 100%; padding: 30px;">
        <table border="0" cellpadding="0" cellspacing="0">
          <tbody>
            <tr>
              <td>
                <h3 style="line-height: 1.24;font-size: 17px;font-weight: bold;letter-spacing: -0.1px;color:#898989;margin: 0;padding: 0;">Hello {{ $ticket->name }}</h3>
              </td>
            </tr>
            <tr><td><div style="font-size: 13px;line-height: 1.62;color:#898989;margin: 5px 0;">Your ticket has been created successfully.</div></td></tr>
            <tr><td><div style="font-size: 13px;line-height: 1.62;color:#898989;">Your ticket ID - {{$ticket->ticket_id }}</div></td></tr>
          </tbody>
        </table>
      </div>
      <div style="width: 100%; padding: 0px 30px;">
          <table border="0" cellpadding="0" cellspacing="0">
            <tbody>
              <tr>
                <td style="width: 25%;">
                  <div style="width: 100%; height: 10px; background-color: #898989;"></div>
                </td>
                <td style="width: 25%;">
                  <div style="width: 100%; height: 10px; background-color: #f4e7e1;"></div>
                </td>
                <td style="width: 25%;">
                  <div style="width: 100%; height: 10px; background-color: #f4e7e1;"></div>
                </td>
                <td style="width: 25%;">
                  <div style="width: 100%; height: 10px; background-color: #f4e7e1;"></div>
                </td>
              </tr>
            </tbody>
          </table>
          <table border="0" cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                  <td style="width: 100%;"><div style="font-weight: bold;font-size: 20px;color: #898989;padding-top: 10px;"><b style="color: #000000;">Ordered:</b>
                    {{ date("M d, Y",strtotime($ticket->created_at)) }}</div></td>
                </tr>
            </tbody>
          </table>
      </div>
      <div style="width: 100%;padding: 30px;">
        <table border="0" cellpadding="0" cellspacing="0">
          <tbody>
            <tr>
               <td style="color: #000000;font-size: 13px;padding-top: 5px;padding-bottom: 10px;font-weight: bold;">Team Solo Luxury</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div style="width: 100%;background-color: #f4e7e1;padding: 30px;">
          <table border="0" cellpadding="0" cellspacing="0">
            <tbody>
              <tr>
                <td style="padding-bottom: 25px;">
                    <table align="left" style="width: 70%;">
                        <tbody>
                          <tr>
                             <td>
                              <div style="float: left;margin-top: 3px;">
                              <div style="margin-left: 30px;"><a href="#" style="font-size: 12px; color: #000000;">customercare@sololuxury.com</a></div>
                            </td>
                          </tr>
                        </tbody>
                    </table>
                    <table align="right" style="width: 30%;">
                      <tbody>
                        <tr>
                          <td style="text-align: right;padding-top: 6px;">
                            <a href="#" style="display: inline-block; margin-left: 15px;">
                            <a href="#" style="display: inline-block; margin-left: 15px;">
                            <a href="#" style="display: inline-block; margin-left: 15px;">
                            <a href="#" style="display: inline-block; margin-left: 15px;">
                          </td>
                        </tr>
                      </tbody>
                    </table>
                </td>
              </tr>
              <tr style="border-top: 2px solid #e8dad3;">
                <td style="padding: 25px 0 10px; text-align: center;font-size: 12px;color: #898989;">You are receiving this email as <a href="#" style="color: #000000;">customercare@sololuxury.com</a> is registered on <a href="#" style="color: #000000;">sololuxury.com</a>.</td>
              </tr>
              <tr>
                <td style="text-align: center;font-size: 12px;">2020 sololuxury. <a href="#" style="color: #898989;">Privacy Policy</a> | <a href="#" style="color: #898989;">Terms of Use</a> | <a href="#" style="color: #898989;">Terms of Sale</a></td>
              </tr>
            </tbody>
          </table>
      </div>
   </div>
</body>
</html>
