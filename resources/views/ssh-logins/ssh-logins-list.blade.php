@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Ssh Logins ({{$logs->total()}})</h2>
		</div>
	</div>
    <div class="mt-3 col-md-12">
		<form action="{{route('get.ssh.logins')}}" method="get" class="search">
            <div class="col-md-2 pd-sm">
				{{ html()->multiselect("ip_ids[]", \App\Models\SshLogin::pluck('ip', 'ip')->toArray(), request('ip_ids'))->class("form-control globalSelect2")->data('placeholder', "Select ip") }}
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search_message" placeholder="Search Message" name="search_message">
			</div>
            <div class="col-lg-2">
				<input class="form-control" type="text" id="user" placeholder="Search User" name="user">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search_status" placeholder="Search Status" name="search_status">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="date" name="date" >
			</div>

			<div class="col-lg-2">
				<button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
				   <img src="{{ asset('images/search.png') }}" alt="Search">
			   </button>
               <a href="{{route('get.ssh.logins')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
			</div>
		</form>
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th width="3%">S.no</th>
			    	<th width="3%">Ip</th>
			        <th width="10%">Login Time</th>
			        <th width="10%">User</th>
			        <th width="30%">Message</th>
			        <th width="10%">Status</th>
                </tr>
		    	<tbody>
                    @foreach ($logs as $key => $data)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$data->ip}}</td>
							<td>{{$data->logintime}}</td>
							<td>{{$data->user}}</td>
							<td>{{$data->message}}</td>
                            <td>{{$data->status}}</td>
						</tr>                        
                    @endforeach
		    	</tbody>
		    </thead>
		</table>
		{!! $logs->appends(Request::except('page'))->links() !!}
	</div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
</div>

@endsection