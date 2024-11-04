@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', "Assets Manager User Access")

@section('content')
<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Assets Manager User Access</h2>
    </div>
    <br>
    <style type="text/css">
    	.select2-container{width: 100% !important;}
    </style>
    <div class="col-lg-12 margin-tb" style="margin-bottom: 10px;">
	    <div class="">
          	<form class="form-inline" action="{{ route('user-accesses.index') }}" method="GET">
          		<div class="col-lg-2 margin-tb">
		           	<div class="form-group  ml-3">
						Search Username
						<br>
						{{ html()->text("keyword", request("keyword"))->class("form-control")->placeholder("Enter keyword") }}
					</div>
				</div>
				<div class="col-lg-4 margin-tb">
		            <div class="form-group ml-3" style=" width: 100%;">
		              	Select Created By User
		              	<br>
						{{ html()->multiselect("created_by[]", \App\User::orderBy('name')->pluck('name', 'id')->toArray(), request('created_by'))->class("form-control select2") }}
		            </div>
	            </div>
	            <div class="col-lg-3 margin-tb" style=" margin-top: 15px;">
	              	<button type="submit" class="btn ml-2"><i class="fa fa-filter"></i></button>
		            
		            <a href="{{route('user-accesses.index')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
	            </div>
          	</form>
    	</div>
    </div>

    @include('partials.flash_messages', ['extraDiv' => true])

    <div class="col-lg-12 margin-tb">
		<div class="col-md-12 margin-tb" id="page-view-result">
			<div class=" table-horizontal-scroll">
				<table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="4%">ID</th>
                            <th width="9%">Selected User</th>
                            <th width="9%">User Name</th>
                            <th width="9%">Password</th>
                            <th width="8%">Created By</th>
                            <th width="8%">Created Date</th>
                            <th width="8%">Request Data</th>
                            <th width="8%">Response Data</th>
                        </tr>
                    </thead>
				    <tbody>
						<?php 
						if($user_accesses) {
							$i = 1;
							foreach($user_accesses as $user_access) { ?>
								<tr class="trrow">
									<td width="5%" class="expand-row">
										{{$i}}
									</td>
									<td width="10%" class="expand-row">
										{{$user_access->selectedUser}}
									</td>
									<td width="10%" class="expand-row">
										{{$user_access->username}}
									</td>
									<td width="10%" class="expand-row">
										{{$user_access->password}}
										<button type="button"  class="btn btn-copy-password-url btn-sm float-right" data-id="{{$user_access->password}}"><i class="fa fa-clone" aria-hidden="true"></i></button>
									</td>
									<td width="10%" class="expand-row">
										{{$user_access->user->name}}
									</td>
									<td width="10%" class="expand-row">
										{{$user_access->created_at}}
									</td>
									<td width="20%" class="expand-row">
										{{$user_access->request_data}}
									</td>
									<td width="20%" class="expand-row">
										{{$user_access->response_data}}
									</td>
								</tr>
						<?php 
								$i++;
							}  
						} ?>
				    </tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">

$('.select-multiple').select2({width: '100%'});
$('.select2').select2();

$(".btn-copy-password-url").click(function() {
    var password = $(this).data('id');
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(password).select();
    document.execCommand("copy");
    $temp.remove();

    alert("Copied!");
});
</script>
@endsection