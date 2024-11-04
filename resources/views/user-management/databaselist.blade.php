@php
$i = 1;
@endphp
@forelse($UserDatabaseLog as $databaseLog)
	<tr>
		<td >
			{{$i}}
		</td>	
		<td>
			{{$databaseLog->request_data}}
		</td>
		<td>
			{{$databaseLog->response_data}}
		</td>
		<td>
			{{$databaseLog->created_at}}
		</td>
	</tr>
	@php
	$i++;
	@endphp
@empty
	<tr>
		<td colspan="7" style="text-align: center"> <h4>No Data Found </h4></td>
	</tr>
@endforelse