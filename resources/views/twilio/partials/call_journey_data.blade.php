@foreach ($call_Journeies as $key => $call_Journey)
    <tr>
        <td>{{ $key + 1 }}</td>
        <td>{{ $call_Journey->customer_name ? $call_Journey->customer_name : ' - ' }}</td>
        <td>{{ $call_Journey->website ? $call_Journey->website : ' - ' }}</td>
        <td>{{ $call_Journey->phone }}</td>
        <td>{{ isset($call_Journey->twilio_credential_id) ? $call_Journey->twilio_credential_id : '-' }}</td>
        <td>{{ $call_Journey->account_sid }}</td>
        <td>{{ $call_Journey->call_sid }}</td>
        <td>{{ $call_Journey->call_entered == 1 ? 'Yes' : 'No' }}</td>
        <td>{{ $call_Journey->handled_by_chatbot == 1 ? 'Yes' : 'No' }}</td>
        <td>{{ $call_Journey->called_in_working_hours == 1 ? 'Yes' : 'No' }}</td>
        <td>{{ $call_Journey->agent_available == 1 ? 'Yes' : 'No' }}</td>
        <td>{{ $call_Journey->agent_online == 1 ? 'Yes' : 'No' }}</td>
        <td>{{ $call_Journey->call_answered == 1 ? 'Yes' : 'No' }}</td>
    </tr>
@endforeach
