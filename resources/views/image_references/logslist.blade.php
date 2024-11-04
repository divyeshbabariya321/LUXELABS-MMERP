@php
$i = 1;
@endphp
@forelse($CropImageGetRequest as $CropImageGetRequestLogs)
	<tr>
		<td >
			{{$i}}
		</td>	
		<td class="expand-row-msg" data-name="name" data-id="{{$CropImageGetRequestLogs->id}}">
          <span class="show-short-name-{{$CropImageGetRequestLogs->id}}">{{ Str::limit($CropImageGetRequestLogs->request, 100, '..')}}</span>
          <span style="word-break:break-all;" class="show-full-name-{{$CropImageGetRequestLogs->id}} hidden">{{$CropImageGetRequestLogs->request}}</span>
        </td>
        <td class="expand-row-msg" data-name="response" data-id="{{$CropImageGetRequestLogs->id}}">
          <span class="show-short-response-{{$CropImageGetRequestLogs->id}}">{{ Str::limit($CropImageGetRequestLogs->response, 100, '..')}}</span>
          <span style="word-break:break-all;" class="show-full-response-{{$CropImageGetRequestLogs->id}} hidden">{{$CropImageGetRequestLogs->response}}</span>
        </td>
		<td>
			{{$CropImageGetRequestLogs->created_at}}
		</td>
	</tr>
	@php
	$i++;
	@endphp
@empty
	<tr>
		<td colspan="4" style="text-align: center"> <h4>No Data Found </h4></td>
	</tr>
@endforelse