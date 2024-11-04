@extends('layouts.app')

@section('content')

<div class="row">
  <div class="col-lg-12 margin-tb">
    <h2 class="page-heading">Calls History</h2>
  </div>

  <div class="col-lg-12 margin-tb margin-l" style="margin-left: 1%;">
		<form class="filterTaskSummary" action="{{ route('order.calls-history') }}" method="GET">
			<div class="row filter_drp">
				<div class="form-group col-lg-2">
					<select class="form-control globalSelect2" data-ajax="{{ route('order.customerList') }}" name="customer_filter[]" data-placeholder="Search Customer By Name" multiple >
          @if($customer)
            @foreach ($customer as $customer)
              <option value="{{ $customer['id'] }}" selected>{{ $customer['name'] }}</option>
            @endforeach
					@endif
          </select>
				</div>
				<div class="form-group col-lg-2">
					<select class="form-control globalSelect2" data-ajax="{{ route('order.callhistoryStatusList') }}" name="status_filter[]" data-placeholder="Search Status By Name" multiple >
          @if($callHistoryStatus)
            @foreach ($callHistoryStatus as $callHistoryStatus)
              <option value="{{ $callHistoryStatus['status'] }}" selected>{{ $callHistoryStatus['status'] }}</option>
            @endforeach
					@endif
          </select>
				</div>
        <div class="form-group col-lg-2">
					<select class="form-control globalSelect2" data-ajax="{{ route('order.storeWebsiteList') }}" name="storewebsite_filter[]" data-placeholder="Search Store Website" multiple >
          @if($storeWebsite)
            @foreach ($storeWebsite as $storeWebsite)
              <option value="{{ $storeWebsite['id'] }}" selected>{{ $storeWebsite['website'] }}</option>
            @endforeach
					@endif
          </select>
				</div>
        <div class="form-group col-lg-2">
          <input type="text" class="form-control" value="{{$customer_num}}" name="phone_number" id="phone_number" placeholder="Phone Number">
				</div>
				<div class="form-group col-lg-2">
					<button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
				</div>
			</div>
		</form>
	</div>
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success">
    <p>{{ $message }}</p>
</div>
@endif

<div class="table-responsive">
  <table class="table table-bordered">
    <thead>
      <th>Customer Name</th>
      <th>Phone Number</th>
      <th>Status</th>
      <th>Store Website</th>
      <th>Call Time</th>
    </thead>
    <tbody>
      @foreach ($calls as $call)
        <tr>
          <td><a href="{{ $call->customer ? route('customer.show', $call->customer->id) : '#' }}" target="_blank">{{ $call->customer ? $call->customer->name : 'Non Existing Customer' }}</a></td>
          <td>{{ $call->customer ? $call->customer->phone : '' }}</td>
          <td>{{ $call->status }}</td>
          @if ($call->store_website)
            <td>{{ $call->store_website->title }} ({{ $call->store_website->website }})</td>
          @else
            <td> - </td>
          @endif
          <td>{{ \Carbon\Carbon::parse($call->created_at)->format('H:i d-m') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

{!! $calls->appends(Request::except('page'))->links() !!}

@endsection

@section('scripts')
  
@endsection
