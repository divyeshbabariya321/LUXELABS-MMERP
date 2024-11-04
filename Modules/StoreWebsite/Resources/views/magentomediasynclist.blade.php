@php
$i = 1;
@endphp
@forelse($MagentoMediaSyncLogs as $MagentoMediaSyncLog)
	<tr>
		<td >
			{{$i}}
		</td>	
		<td>
			{{$MagentoMediaSyncLog->sourcestorewebsite->title}}
		</td>
		<td>
			{{$MagentoMediaSyncLog->deststorewebsite->title}}
		</td>
		<td>
			{{$MagentoMediaSyncLog->request_data}}
		</td>
		<td>
			{{$MagentoMediaSyncLog->response_data}}
		</td>
		<td>
			{{$MagentoMediaSyncLog->user->name}}
		</td>
		<td>
			{{$MagentoMediaSyncLog->created_at}}
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