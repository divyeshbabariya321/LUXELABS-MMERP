@php
$i = 1;
@endphp
@forelse($VarnishStatsLogs as $VarnishStatsLog)
	<tr>
		<td >
			{{$i}}
		</td>	
		<td>
			{{$VarnishStatsLog->request_data}}
		</td>
		<td>
			{{$VarnishStatsLog->response_data}}
		</td>
		<td>
			{{$VarnishStatsLog->created_at}}
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