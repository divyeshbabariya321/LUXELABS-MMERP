@php
$i = 1;
@endphp
@forelse($storeWebsiteAdminUrls as $storeWebsiteAdminUrl)
	<tr>
		<td>
			{{$i}}
		</td>	

		<td>
			{{$storeWebsiteAdminUrl->storewebsite->title}}
		</td>	

		<td>
			<div style="display: flex">
				<a href ="{{$storeWebsiteAdminUrl->admin_url}}" target="_blank" style="display:flex; gap:5px">
					<input type="text" class="form-control" name="admin_url[edit:{{$storeWebsiteAdminUrl->id}}]" value="{{$storeWebsiteAdminUrl->admin_url}}">
				</a>
				<button type="button" data-id="" class="btn btn-copy-api-token btn-sm" data-value="{{$storeWebsiteAdminUrl->admin_url}}">
					<i class="fa fa-clone" aria-hidden="true"></i>
				</button>
			</div>
		</td>
		<td>
			{{$storeWebsiteAdminUrl->request_data}}
		</td>
		<td>
			{{$storeWebsiteAdminUrl->response_data}}
		</td>
		<td>
			{{$storeWebsiteAdminUrl->user->name}}
		</td>
		<td>
			{{$storeWebsiteAdminUrl->created_at}}
		</td>
		<td>
			{{$storeWebsiteAdminUrl->status==1?'Active':'In Active'}}
		</td>
		<td>
			<button type="button" data-id="{{$storeWebsiteAdminUrl->store_website_id}}" class="btn admin-url-history" style="padding:1px 0px;">
        		<i class="fa fa-info-circle" aria-hidden="true"></i>
        	</button>
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