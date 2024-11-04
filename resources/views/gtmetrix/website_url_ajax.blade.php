@foreach ($list as $key)
    <tr>
		<td>
			<input @if($key->process == '1') checked @endif type="checkbox" name ="multi-run-test-type" class= "multi-run-test" value ="{{ $key->id }}">
		</td>
		<td>
		{{ \Carbon\Carbon::parse($key->created_at)->format('Y-m-d')}}
		</td>
		<td>
		{{ $key->store_name }}
		</td>
		<td>
			<a class="text-dark" href="{{ $key->website_url }}" target="_blank" title="Goto website"> {{ !empty($key->website_url) ? $key->website_url : $key->store_view_id }} </a>
		</td>
		<td class="processToggle">
			@if($key->process == '1')
			<label class="switch">
				<input type="checkbox" checked value ="{{ $key->id }}">
				<span class="slider round"></span>
			</label>
			@else
			<label class="switch">
				<input type="checkbox" value ="{{ $key->id }}">
				<span class="slider round"></span>
			</label>
			@endif
		</td>
		<td>  
			<a id="delete-url" href="javascript:void(0)" data-value="{{ $key->id }}">Delete</a>
			<a id="run-current-url" href="javascript:void(0)" data-value="{{ $key->id }}">Run Current Page</a>
		</td>
	</tr>
@endforeach
