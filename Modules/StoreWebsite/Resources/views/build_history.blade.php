@foreach($buildHistory as $history)
<tr>
	<td>{{$history['UserName']}}</td>
	<td>{{$history['status']}}</td>
	<td>{!! $history['text'] !!}</td>
	<td>{{$history['created_at']}}</td>
</tr>
@endforeach