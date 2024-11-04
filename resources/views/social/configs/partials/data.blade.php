@if($socialConfigs->isEmpty())

<tr>
 <td>
   No Result Found
 </td>
</tr>
@else

@foreach ($socialConfigs as $i => $socialConfig)

<tr>
	@if(!empty($dynamicColumnsToShow))
		@if (!in_array('Website', $dynamicColumnsToShow))
			<td>@if(isset($socialConfig->storeWebsite)) {{ $socialConfig->storeWebsite->title }} @endif</td>
		@endif
		@if (!in_array('Platform', $dynamicColumnsToShow))
			<td>{{ $socialConfig->platform }}</td>
		@endif
		@if (!in_array('Name', $dynamicColumnsToShow))
			<td>{{ $socialConfig->name }}</td>
		@endif
		@if (!in_array('UserName', $dynamicColumnsToShow))
			<td>{{ $socialConfig->user_name }}</td>
		@endif
		@if (!in_array('Phone Number', $dynamicColumnsToShow))
			<td>{{ $socialConfig->phone_number }}</td>
		@endif
		@if (!in_array('Email', $dynamicColumnsToShow))
			<td>{{ $socialConfig->email }}</td>
		@endif
		@if (!in_array('Password', $dynamicColumnsToShow))
			<td>{{ !empty($socialConfig->password) ? \App\Helpers::getDecryptedData($socialConfig->password) : ''}}</td>
		@endif
		@if (!in_array('API key', $dynamicColumnsToShow))
			<td>{{ $socialConfig->api_key }}</td>
		@endif
		@if (!in_array('API Secret', $dynamicColumnsToShow))
			<td>{{ !empty($socialConfig->api_secret) ? \App\Helpers::getDecryptedData($socialConfig->api_secret) : ''}}</td>
		@endif
		@if (!in_array('Page ID', $dynamicColumnsToShow))
			<td>{{ $socialConfig->page_id }}</td>
		@endif
		@if (!in_array('Account ID', $dynamicColumnsToShow))
			<td>{{ $socialConfig->account_id }}</td>
		@endif
		@if (!in_array('Page Language', $dynamicColumnsToShow))
			<td>{{ $socialConfig->page_language }}</td>
		@endif
		@if (!in_array('Ad Account', $dynamicColumnsToShow))
			<td>{{ $socialConfig->ad_account?->name }}</td>
		@endif
		@if (!in_array('Status', $dynamicColumnsToShow))
			<td>@if($socialConfig->status == 1) Active @elseif($socialConfig->status == 2) Blocked @elseif($socialConfig->status == 3)  Scan Barcode @else Inactive @endif</td>
		@endif
		@if (!in_array('Started At', $dynamicColumnsToShow))
			<td>{{ $socialConfig->created_at->format('d-m-Y') }}</td>
		@endif
		@if (!in_array('Actions', $dynamicColumnsToShow))
			<td>
				<a class="btn btn-secondary btn-sm" href="{{route('social.config.update',$socialConfig->id)}} " title="edit"><i class="fa fa-pencil"></i></a>
				<button onclick="deleteConfig({{ $socialConfig->id }})" class="btn btn-sm btn-secondary"><i class="fa fa-trash-o"></i></button>
				<a class="btn btn-secondary btn-sm" href="{{route('social.post.index',$socialConfig->id)}} " title="Manage Posts"><i class="fa fa-briefcase"></i></a>
				<a class="btn btn-secondary btn-sm" href="{{ route('social.account.posts',$socialConfig->id) }} " title="Webhook Posts"><i class="fa fa-bars"></i></a>
			</td>
		@endif
	@else
		<td>@if(isset($socialConfig->storeWebsite)) {{ $socialConfig->storeWebsite->title }} @endif</td>
		<td>{{ $socialConfig->platform }}</td>
		<td>{{ $socialConfig->name }}</td>
		<td>{{ $socialConfig->user_name }}</td>
		<td>{{ $socialConfig->phone_number }}</td>
		<td>{{ $socialConfig->email }}</td>
		<td>{{ \App\Helpers::getDecryptedData($socialConfig->password)}}</td>
		<td>{{ $socialConfig->api_key }}</td>
		<td>{{ \App\Helpers::getDecryptedData($socialConfig->api_secret)}}</td>
		<td>{{ $socialConfig->page_id }}</td>
		<td>{{ $socialConfig->account_id }}</td>
		<td>{{ $socialConfig->page_language }}</td>
		<td>{{ $socialConfig->ad_account?->name }}</td>
		<td>@if($socialConfig->status == 1) Active @elseif($socialConfig->status == 2) Blocked @elseif($socialConfig->status == 3)  Scan Barcode @else Inactive @endif</td>
		<td>{{ $socialConfig->created_at->format('d-m-Y') }}</td>
		<td>
			<a class="btn btn-secondary btn-sm" href="{{route('social.config.update',$socialConfig->id)}} " title="edit"><i class="fa fa-pencil"></i></a>
			<button onclick="deleteConfig({{ $socialConfig->id }})" class="btn btn-sm btn-secondary"><i class="fa fa-trash-o"></i></button>
			<a class="btn btn-secondary btn-sm" href="{{route('social.post.index',$socialConfig->id)}} " title="Manage Posts"><i class="fa fa-briefcase"></i></a>
			<a class="btn btn-secondary btn-sm" href="{{ route('social.account.posts',$socialConfig->id) }} " title="Webhook Posts"><i class="fa fa-bars"></i></a>
		</td>
	@endif

</tr>

@include('social.configs.partials.edit-modal')
@endforeach

@endif
