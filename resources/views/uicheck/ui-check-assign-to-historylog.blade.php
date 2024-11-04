@foreach ($getMessageLog as $messageLog) 
    <tr>
    <td>{{ $messageLog->id }}</td>
    <td>{{ $messageLog->userName }}</td>
    <td>{{ $messageLog->assignToName }}</td>
    <td>{{ $messageLog->created_at }}</td>
    </tr>
    @endforeach