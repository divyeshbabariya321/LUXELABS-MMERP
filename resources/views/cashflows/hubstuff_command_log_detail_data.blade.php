@foreach($messageLogs as $log)
<tr>
    <td>
        {{ isset($users[$log->user_id])?$users[$log->user_id]:'' }}
    </td>
    <td>
        {{ $log->frequency }}
    </td>
    <td>
        {{ $log->start_date }}
    </td>
    <td>
        {{ $log->end_date }}
    </td>
    <td>
        {{ $log->message  }}
    </td>
  
    
</tr>
@endforeach
