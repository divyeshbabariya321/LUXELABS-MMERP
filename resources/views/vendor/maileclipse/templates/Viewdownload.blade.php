<!DOCTYPE html>
<html>
<head>
	<title>SOP Data Content</title>
	<style>
    
    #sopdata {
      font-family: Arial, Helvetica, sans-serif;
      border-collapse: collapse;
      width: 100%;
      font-size: 14px;
      page-break-inside: avoid;
    }
    #sopdata td, #sopdata th {
  border: 1px solid #ddd;
  padding: 5px;
}

#sopdata tr{background-color: #f2f2f2;}




    </style>
</head>
<body>
   
    <table id="sopdata">
        <tbody>
            <tr>
                <th colspan="2" style="background-color: #808080;  text-align: center; font-size: 20px;  color: white;" >SOP Data Details</th>
            </tr>
        </tbody>
    </table>
            <div class="name" style="font-style: bold; font-size: 20px; margin-top:10px;"> Name: &nbsp;{{ $usersop->name }}</div>
                                       
            <div class="content">{!! $usersop->content !!}</div>
</body>
</html>