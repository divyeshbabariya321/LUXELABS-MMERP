@if(count($watsonJourney) != 0)
    @foreach ($watsonJourney as $key=>$log )
        <tr>
            <td>{{ $key+1 }}</td>
            <td>{{$log->chat_id}}</td> 
            <td>@if($log->chat_entered == 1) Yes @else No @endif</td>
            <td>{{$log->message_received}}</td>
            <td>@if($log->reply_found_in_database == 1) Yes @else No @endif</td>
            <td>@if($log->reply_searched_in_watson == 1) Yes @else No @endif</td>
            <td>{{$log->reply}}</td>
            <td>@if($log->response_sent_to_cusomer == 1) Yes @else No @endif</td>
            <td>{{@$log->sender_name}}</td>
            <td>{{@$log->sender_phone}}</td>
        </tr>
    @endforeach
@else
<tr>
    <td colspan="10"  align="center">No Data Found</td>
</tr>
@endif