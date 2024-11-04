@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
<style type="text/css">
	.preview-category input.form-control {
		width: auto;
	}
	tbody tr .Website-task-warp{
		overflow: hidden !important;
		white-space: normal !important;
		word-break: break-all;
	}
</style>

<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
		<h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
	</div>
	<br>
	<div class="col-lg-12 margin-tb" id="page-view-result">
		<div class="col-lg-12 pl-5 pr-5">
			<div style="display: flex !important; float: right !important;">
				<div style="width: 150px;"> <!-- Add a fixed width to the wrapping div -->
					<a href="#" class="btn btn-xs btn-secondary generate-api-tokens">Generate API Tokens</a>
				</div>
				&nbsp;&nbsp;
				<input type="text" class="api-token-search" name="search" placeholder="Search">
				&nbsp;
				<select class="form-control select2" name="store_ids" id="store_ids" multiple="multiple" style="width: 200px;">
					<option id="opdefault" value=""> Please select Websites</option>
					@if(!empty($storeWebsites))
						@foreach($storeWebsites as $key => $storeWebsite)
							<option data-id="" value="{{ $storeWebsite->id}}"> {{ $storeWebsite->title }} </option>
						@endforeach
					@endif
				</select>
				<button style="display: inline-block; width: 10%" class="btn btn-sm btn-image btn-secondary btn-search-api-token">
					<img src="/images/search.png" style="cursor: default;">
				</button>
				&nbsp;
				<button style="display: inline-block; width: 10%" class="btn btn-sm btn-image btn-secondary btn-refresh-api-token">
					<img src="/images/resend2.png" style="cursor: default;">
				</button>
			</div>
		</div>
	</div>
	
		<div class="col-lg-12 pl-5 pr-5">
			<form action="/store-website/generate-api-token" method="post">
				<?php echo csrf_field(); ?>
				
					<div class="col-md-12">
						<div class="table-responsive mt-3">
							<table class="table table-bordered overlay api-token-table"  >
								<thead>
								<tr>
									<th>Select</th>
									<th>Id</th>
									<th width="15%">Title</th>
									<th width="45%">Api Token</th>
									<th width="30%">Server Ip</th>
									<th width="30%">Actions</th>
								</tr>
								</thead>
								<tbody>
									@include('storewebsite::api-token')
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<!--<button type="submit" class="btn btn-secondary submit float-right float-lg-right">Update Api Token</button>-->
						</div>
					</div>
				
			</form>
		</div>
	</div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>


<div class="modal fade" id="generate-api-token-modal" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><b>Generate API Token</b></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">				
				<input type="hidden" name="gat_store_website_id" id="gat_store_website_id" value="">
                
                <div class="form-group">
					<label for="button">Select User</label>
                    <select class="form-control" name="gat_store_website_users_id" id="gat_store_website_users_id">
						<option id="opdefault" value=""> Please select user</option>
                        @if(!empty($storeWebsiteUsers))
                            @foreach($storeWebsiteUsers as $key => $user)
                                <option data-id="{{$user->store_website_id}}" value="{{ $user->id}}" > {{ $user->first_name }} {{ $user->first_name }} ({{$user->email}}) </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-secondary btn-generate-api-token submit float-right float-lg-right">Generate API Token</button>
                </div>
			</div>
		</div>
	</div>
</div>
<!-- User Permission content-->
<div class="modal fade" id="generate-user-permission-token-modal" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><b>User permission</b></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">	
					@php
   					 $users =  \App\User::select('id', 'name', 'email', 'gmail')->whereNotNull('gmail')->get();
					@endphp

				<div class="form-group custom-select2">
					<label> Select users </label>
				<label> Select users for </label>
				<select class="w-100 js-example-basic-multiple js-states" multiple="multiple" name="user_permission[]" id="permission_user">
					@foreach($users as $val)
						<option data-id="{{ $user->store_website_id }}" value="{{ $val->id }}" class="form-control">{{ $val->name }}</option>
					@endforeach
				</select>
	       	</div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-secondary btn-generate-user-permission submit float-right float-lg-right">Generate Permission</button>
                </div>
			</div>
		</div>
	</div>
</div>

<div id="store_websites_api_token_logs" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content ">
			<div class="modal-header">
				<h4 class="modal-title">Store Websites API Token Logs</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="table-responsive mt-3">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>ID</th>
								<th>Update By</th>
								<th>Store Website</th>
								<th>Store Website User</th>
								<th>Response</th>
								<th>Status Code</th>
								<th>Status</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody id="sw_api_token_Logs">

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@include('api-token-history')

<script type="text/javascript" src="{{ asset('/js/jsrender.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('/js/jquery-ui.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/store-website.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js">
</script>
<script
	src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
</script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="{{ config('app.url') }}/js/bootstrap-multiselect.min.js"></script>

<script type="text/javascript">

$('.select2').select2();

    $(document).ready(function() {
        $("#permission_user").select2();
    });

	$(document).on("click",".btn-copy-api-token",function() {
		var apiToken = $(this).data('value');
		var $temp = $("<input>");
		$("body").append($temp);
		$temp.val(apiToken).select();
		document.execCommand("copy");
		$temp.remove();
		alert("Copied!");
	});

	$(document).on("click",".btn-copy-server-ip",function() {
		var serverip = $(this).data('value');
		var $temp = $("<input>");
		$("body").append($temp);
		$temp.val(serverip).select();
		document.execCommand("copy");
		$temp.remove();
		alert("Copied!");
	});

	$(document).on('click','.generate-api-token',function(){
		var store_website_id=$(this).attr('data-id');
		$("#gat_store_website_id").val('');
		$("#gat_store_website_users_id").val('');
		$("#gat_store_website_id").val(store_website_id);
		$("#gat_store_website_users_id").children('option').hide();
   	 	$("#gat_store_website_users_id").children("option[data-id=" + store_website_id + "]").show()
   	 	$("#gat_store_website_users_id #opdefault").show()
	});

	$(document).on("click", ".btn-test-api-token", function() {
		var store_website_id=$(this).attr('data-id');
		
		$.ajax({
			type: 'POST',
			url: '/store-website/api-token/test-api-token/'+store_website_id,
			beforeSend: function () {
				$("#loading-image").show();
			},
			data: {
				_token: "{{ csrf_token() }}",
				store_website_id: store_website_id,
			},
			dataType: "json"
		}).done(function (response) {
			$("#loading-image").hide();
			if (response.success) {
				toastr['success'](response.message, 'success');
			}else{
				toastr['error'](response.message, 'error');
			}
		}).fail(function (response) {
			$("#loading-image").hide();
			
			toastr['error']('Sorry, something went wrong', 'error');
		});
	});
	$(document).on("click", ".api-token-logs", function() {
		var store_website_id=$(this).attr('data-id');
		$.ajax({
			type: 'POST',
			url: '/store-website/api-token/get-api-token-logs/'+store_website_id,
			beforeSend: function () {
				$("#loading-image").show();
			},
			data: {
				_token: "{{ csrf_token() }}",
				store_website_id: store_website_id,
			},
			dataType: "json"
		}).done(function (response) {
			$("#loading-image").hide();
			if (response.code == 200) {
				$('#sw_api_token_Logs').html(response.data);
			 	$('#store_websites_api_token_logs').modal('show');
			}
		}).fail(function (response) {
			$("#loading-image").hide();
			toastr['error']('Sorry, something went wrong', 'error');
		});
	});

	$(document).on('click','.btn-generate-api-token',function(){
		
		var store_website_id=$("#gat_store_website_id").val();
		var store_website_users_id=$("#gat_store_website_users_id").val();
		if(store_website_id==''){
			toastr['error']('Website id is not found!', 'error');
			return;
		}
		if(store_website_users_id==''){
			toastr['error']('Please select user!', 'error');
			return;
		}
		src = '/store-website/api-token/generate-api-token'
		
		$.ajax({
			url: src,
			dataType: "json",
			type: "POST",
			data: {
				"store_website_id": store_website_id,
				"store_website_users_id": store_website_users_id,
				"_token": "{{ csrf_token() }}",
				
			},
			beforeSend: function () {
				$("#loading-image").show();
			},
		}).done(function (response) {

			$("#loading-image").hide();
			if(response.success){
				$("#api_token_"+store_website_id).val(response.token);
				toastr['success'](response.message, 'success');
			}else{
				toastr['error'](response.message, 'error');
			}
			$('#generate-api-token-modal').modal('hide');
		}).fail(function (jqXHR, ajaxOptions, thrownError) {
			toastr['error']('No response from server', 'error');
			$('#generate-api-token-modal').modal('hide');
		});
	})

	$(document).on('click','.btn-search-api-token',function(){
		src = '/store-website/get-api-token'
		search = $('.api-token-search').val();
		store_ids = $('#store_ids').val();
		$.ajax({
			url: src,
			dataType: "json",
			type: "GET",
			data: {
				search : search,
				store_ids:store_ids
			},
			beforeSend: function () {
				$("#loading-image").show();
			},
		}).done(function (data) {
			$("#loading-image").hide();
			$(".api-token-table tbody").empty().html(data.tbody);
		}).fail(function (jqXHR, ajaxOptions, thrownError) {
			alert('No response from server');
		});
	})

	$(document).on('click','.btn-refresh-api-token',function(){
		src = '/store-website/get-api-token'
		$.ajax({
			url: src,
			dataType: "json",
			type: "GET",
			data: {

			},
			beforeSend: function () {
				$("#loading-image").show();
			},

		}).done(function (data) {
			$("#loading-image").hide();
			$(".api-token-search").val("");
			$(".api-token-table tbody").empty().html(data.tbody);



		}).fail(function (jqXHR, ajaxOptions, thrownError) {
			alert('No response from server');
		});

	})

	var selectedStoreWebsites = [];
	$(document).on('click', '.selectedStoreWebsite', function () {
		var checked = $(this).prop('checked');
		var id = $(this).val();
		if (checked) {
			selectedStoreWebsites.push(id);
		} else {
			var index = selectedStoreWebsites.indexOf(id);
			selectedStoreWebsites.splice(index, 1);
		}
	});

	$(document).on("click",".generate-api-tokens",function(e){
		e.preventDefault();
		if(selectedStoreWebsites.length < 1) {
			toastr['error']("Select some rows first");
			return;
		}
		var x = window.confirm("Are you sure, you want to generate API Tokens ?");
		if(!x) {
			return;
		}

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/store-website/api-token/bulk-generate-api-token',
			type: "POST",
			data: {ids : selectedStoreWebsites}
		}).done(function(response) {
			toastr['success'](response.message);
			window.location.reload();
		}).fail(function(errObj) {
		});
	});

	var storwebsitevalue; // Declare the variable outside the event functions

	$(document).on('click', '.user-api-token', function() {
		storwebsitevalue = $(this).data('id');
		var selectedUsers = $(this).data('seletedusers');
		var userCount = selectedUsers.length;
		
		$("#generate-user-permission-token-modal #permission_user").val("").trigger('change');
		
		if (userCount >= 2) {
			var permission_users = selectedUsers.split(',');
			$("#generate-user-permission-token-modal #permission_user").val(permission_users).trigger('change');
		} else {
			$("#generate-user-permission-token-modal #permission_user").val(selectedUsers).trigger('change');
		}             
	});

	$(document).on('click','.btn-generate-user-permission',function(){
		var selectedUsers = $('#permission_user').val();
		var store_website_id = storwebsitevalue; // Use storwebsitevalue directly
        if(selectedUsers==''){
			toastr['error']('Select User!', 'error');
			return;
		}
		
		$.ajax({
			url: 'user-permission/update',
			dataType: "json",
			type: "POST",
			data: {
				"store_website_id": store_website_id,
				"users_id": selectedUsers,
				"_token": "{{ csrf_token() }}",
				
			},
			beforeSend: function () {
				$("#loading-image").show();
			},
		}).done(function (response) {

			$("#loading-image").hide();
			if(response){
				toastr['success'](response.message, 'success');
			}else{
				toastr['error'](response.message, 'error');
			}
			$('#generate-user-permission-token-modal').modal('hide');
		}).fail(function (jqXHR, ajaxOptions, thrownError) {
			toastr['error']('No response from server', 'error');
			$('#generate-user-permission-token-modal').modal('hide');
		});
	})
	
	$(document).on('click','.api-token-history',function(){
        store_website_id = $(this).data('id');
		$.ajax({
                method: "GET",
                url: `{{ route('store-website.token.histories', [""]) }}/` + store_website_id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
							html += "<tr>";
							html += "<td>" + (k + 1) + "</td>";
							html += "<td class='expand-row' style='word-break: break-all'>";
							html += "<span class='td-mini-container'>" + (v.old_api_token != null ? (v.old_api_token.length > 15 ? v.old_api_token.substr(0, 15) + '...' : v.old_api_token) : ' - ' ) + "</span>";
							html += "<span class='td-full-container hidden'>" + (v.old_api_token != null ? v.old_api_token : ' - ' ) + "</span>";
							html += "</td>";
							html += "<td class='expand-row' style='word-break: break-all'>";
							html += "<span class='td-mini-container'>" + (v.new_api_token != null ? (v.new_api_token.length > 15 ? v.new_api_token.substr(0, 15) + '...' : v.new_api_token) : ' - ' ) + "</span>";
							html += "<span class='td-full-container hidden'>" + (v.new_api_token != null ? v.new_api_token : ' - ' ) + "</span>";
							html += "</td>";
							html += "<td class='expand-row' style='word-break: break-all'>";
							html += "<span class='td-mini-container'>" + (v.user !== undefined ? (v.user.name.length > 15 ? v.user.name.substr(0, 15) + '...' : v.user.name) : ' - ' ) + "</span>";
							html += "<span class='td-full-container hidden'>" + (v.user !== undefined ? v.user.name : ' - ' ) + "</span>";
							html += "</td>";
							html += "<td>" + v.created_at + "</td>";
							html += "</tr>";
                        });
                        $("#api-token-histories-list").find(".api-token-list-view").html(html);
                        $("#api-token-histories-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
	});

		$(document).on('click', '.expand-row', function() {
			$(this).find('.td-mini-container').toggleClass('hidden');
			$(this).find('.td-full-container').toggleClass('hidden');
		});

</script>

@endsection