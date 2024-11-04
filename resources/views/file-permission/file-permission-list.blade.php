@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">File Permissions ({{$filePermissions->total()}})</h2>
		</div>
	</div>
    <div class="mt-3 col-md-12">
		<form action="{{route('get.file.permissions')}}" method="get" class="search">
            <div class="col-md-2 pd-sm">
				{{ html()->multiselect("server_ids[]", \App\Models\FilePermission::pluck('server', 'server')->toArray(), request('server_ids'))->class("form-control globalSelect2")->data('placeholder', "Select Server") }}
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search_instance" placeholder="Search Instance" name="search_instance">
			</div>
            <div class="col-lg-2">
				<input class="form-control" type="text" id="search_owner" placeholder="Search Owner" name="search_owner">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search_gp_owner" placeholder="Search Group Owner" name="search_gp_owner">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search_permission" placeholder="Search Permission" name="search_permission">
			</div>


			<div class="col-lg-2">
				<button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
				   <img src="{{ asset('images/search.png') }}" alt="Search">
			   </button>
			   <a href="{{route('get.file.permissions')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>

			</div>
		</form>
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th width="3%">S.no</th>
			    	<th width="3%">Server</th>
			        <th width="10%">Instance</th>
			        <th width="10%">Owner</th>
			        <th width="30%">Group Owner</th>
			        <th width="10%">Permission</th>
                </tr>
		    	<tbody>
                    @foreach ($filePermissions as $key => $filePermission)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$filePermission->server}}</td>
							<td>{{$filePermission->instance}}</td>
							<td>{{$filePermission->owner}}</td>
							<td>{{$filePermission->groupowner}}</td>
                            <td>{{$filePermission->permission}}</td>
						</tr>                        
                    @endforeach
		    	</tbody>
		    </thead>
		</table>
		{!! $filePermissions->appends(Request::except('page'))->links() !!}
	</div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
</div>

@endsection