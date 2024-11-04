@foreach($logs as $log)
<tr>
    <td>{{$log->name}}</td>
    <td>{{$log->action}}</td>
    <td>{{$log->website}}</td>
    <td>{{$log->device}}</td>
    <td>{{$log->action}}</td>
    <td>{{$log->url}}</td>
    <td>{{ print_r(json_decode($log->request))}}</td>
    <td>{{print_r($log->response)}}</td>
    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d-m-y H:i:s')  }}</td>
</tr>
@endforeach