<?php foreach($response as $res) { ?> 
  <tr>
    <td>{{ $res->id }}</td>
    <td>{{ $res->request }}</td>
    <td>{{ $res->response }}</td>
    <td>{{ $res->status }}</td>
    <td>{{ $res->created_at }}</td>
  </tr>
<?php } ?>