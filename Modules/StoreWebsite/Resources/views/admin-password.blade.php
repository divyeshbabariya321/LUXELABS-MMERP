@forelse($storeWebsiteUsers as $storeWebsiteUser)
	<tr>
		<td >
			{{$storeWebsiteUser->id}}
		</td>	

		<td width="30%">
			<div style="display: flex">
				<select name="store_website_id[edit:{{$storeWebsiteUser->id}}]" id="website_mode" class="form-control websiteMode">					               
			       <option value="">-- Select a website--</option>
					@foreach($storeWebsites as $key => $storeWebsite)
						<option {{$storeWebsiteUser->store_website_id == $storeWebsite->id  ? 'selected' : ''}} value="{{ $storeWebsite->id }}">{{ $storeWebsite->title }}</option>
					@endforeach
				</select>
			</div>
		</td>	

		<td width="30%">
			<div style="display: flex">
				<input type="text" class="form-control" name="username[edit:{{$storeWebsiteUser->id}}]" value="{{$storeWebsiteUser->username}}">
				<button type="button" data-id="" class="btn btn-copy-api-token btn-sm" data-value="{{$storeWebsiteUser->username}}">
					<i class="fa fa-clone" aria-hidden="true"></i>
				</button>
			</div>
		</td>
		<td width="30%">
			<div style="display: flex">
				<input type="text" class="form-control" name="password[edit:{{$storeWebsiteUser->id}}]" value="{{$storeWebsiteUser->password}}">
				<button type="button" data-id="" class="btn btn-copy-server-ip btn-sm" data-value="{{$storeWebsiteUser->password}}">
					<i class="fa fa-clone" aria-hidden="true"></i>
				</button>
			</div>
		</td>
	</tr>
@empty
	<tr>
		<td colspan="4" style="text-align: center"> <h4>No Data Found </h4></td>
	</tr>
@endforelse