@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Email Run Logs ({{$emailJobs->total()}})</h2>
		</div>
	</div>
	<div class="mt-3 col-md-12">
		<form action="{{route('email-addresses.run-histories-listing')}}" method="get" class="search">
			<div class="col-md-2 pd-sm">
                <label> Search From Name </label>
                <input class="form-control" type="text" id="search_name" placeholder="Search Name" name="search_name" value="{{ request('search_name') ?? '' }}">
			</div>
			<div class="col-lg-2">
                <label> Search Mesage </label>
				<input class="form-control" type="text" id="search_message" placeholder="Search Error" name="search_message" value="{{ request('search_message') ?? '' }}">
			</div>
			<div class="col-lg-2">
                <label> Search Status </label>
                <select name="status" id="status" class="form-control globalSelect">
                    <option  Value="">Search Status</option>
                    <option  Value="success" {{ (request('status') == "success") ? "selected" : "" }} >Success</option>
                    <option value="failed" {{ (request('status') == "failed") ? "selected" : "" }}>Failed</option>
                </select>
            </div> 
			<div class="col-lg-2">
                <label> Search Date </label>
				<input class="form-control" type="date" name="date" value="{{ (request('date') ?? "" )}}">
			</div>

			<div class="col-lg-2"><br>
				<button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
				   <img src="{{ asset('images/search.png') }}" alt="Search">
			   </button>
			   <a href="{{route('email-addresses.run-histories-listing')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
			</div>
		</form>
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th>ID</th>
			    	<th>From Name</th>
                    <th>Status</th>
			        <th>Message</th>
			        <th>Date</th>
                </tr>
		    	<tbody>
                    @foreach ($emailJobs as $data)
                        <tr>
                            <td>{{$data->id}}</td>
                            <td>{{ $data->email_from_name }}</td>
                            <td>{{$data->is_success == 0 ? "Failed" : "Success"}}</td>
                            <td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                    {{ strlen($data->message) > 30 ? substr($data->message, 0, 30).'...' :  $data->message }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $data->message }}
                                </span>
                            </td>
							<td>{{$data->created_at}}</td>
						</tr>                        
                    @endforeach
		    	</tbody>
		    </thead>
		</table>
		{!! $emailJobs->appends(Request::except('page'))->links() !!}
	</div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
</div>

@endsection

<script>
     $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });
</script>