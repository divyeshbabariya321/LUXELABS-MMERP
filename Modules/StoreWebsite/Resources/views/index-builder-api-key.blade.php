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
	@if(session()->has('success'))
	    <div class="col-lg-12 margin-tb">
		    <div class="alert alert-success">
		        {{ session()->get('success') }}
		    </div>
		</div>    
	@endif
	<div class="col-lg-12 pl-5 pr-5">
		<div class="col-md-12">
			<div class="table-responsive mt-3">
				<table class="table table-bordered overlay builder-api-key-table"  >
					<thead>
						<tr>
							<th>Id</th>
							<th>Title</th>
							<th>Api Key</th>
						</tr>
					</thead>
					<tbody>
						@forelse($storeWebsites as $storeWebsite)
						<tr>
							<td>
								{{ $storeWebsite->id }}
							</td>
							<td>{{ $storeWebsite->title }}</td>
							<td>
								<form action="{{ route('store-website.updateBuilderApiKey', $storeWebsite->id) }}" method="POST">
									@csrf
									<div style="display: flex">
										<input type="text" class="form-control" name="builder_io_api_key" value="{{ $storeWebsite->builder_io_api_key }}">
										<button type="submit" title="Update Api Key" class="btn" style="padding:1px 5px;">
											<a href="javascript:;" style="color:gray;"><i class="fa fa-save"></i></a>
										</button>
										<button title="History" data-id="{{ $storeWebsite->id }}" type="button" class="btn api-key-history" style="padding:1px 5px;">
											<a href="javascript:;" style="color:gray;"><i class="fa fa-info-circle"></i></a>
										</button>
									</div>
								</form>
							</td>
						</tr>
						@empty
						<tr>
							<td colspan="3" style="text-align: center">
								<h4>No Data Found </h4>
							</td>
						</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>

<div id="builder-api-key-histories-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Builder Api Key Histories</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="25%">Old Key</th>
                                <th width="25%">New Key</th>
                                <th width="20%">Updated BY</th>
                                <th width="40%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="builder-api-key-list-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

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

	$(document).on('click', '.update-api-token-button', function() {
		var Updatestorwebsitevalue = $(this).data('id');

		// Find the corresponding hidden input field and set its value
		$(this).closest('tr').find('.update_website_api_id').val(Updatestorwebsitevalue);
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
			$(".builder-api-key-table tbody").empty().html(data.tbody);
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
			$(".builder-api-key-table tbody").empty().html(data.tbody);



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
	
	$(document).on('click','.api-key-history',function(){
        store_website_id = $(this).data('id');

		$.ajax({
                method: "GET",
                url: `{{ route('store-website.builder-api-key-histories', [""]) }}/` + store_website_id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
							html += "<tr>";
							html += "<td>" + (k + 1) + "</td>";
							html += "<td class='expand-row' style='word-break: break-all'>";
							html += "<span class='td-mini-container'>" + (v.old != null ? (v.old.length > 30 ? v.old.substr(0, 30) + '...' : v.old) : ' - ' ) + "</span>";
							html += "<span class='td-full-container hidden'>" + (v.old != null ? v.old : ' - ' ) + "</span>";
							html += "</td>";
							html += "<td class='expand-row' style='word-break: break-all'>";
							html += "<span class='td-mini-container'>" + (v.new != null ? (v.new.length > 30 ? v.new.substr(0, 30) + '...' : v.new) : ' - ' ) + "</span>";
							html += "<span class='td-full-container hidden'>" + (v.new != null ? v.new : ' - ' ) + "</span>";
							html += "</td>";
							html += "<td class='expand-row' style='word-break: break-all'>";
							html += "<span class='td-mini-container'>" + (v.user !== undefined ? (v.user.name.length > 20 ? v.user.name.substr(0, 20) + '...' : v.user.name) : ' - ' ) + "</span>";
							html += "<span class='td-full-container hidden'>" + (v.user !== undefined ? v.user.name : ' - ' ) + "</span>";
							html += "</td>";
							html += "<td>" + v.created_at + "</td>";
							html += "</tr>";
                        });
                        $("#builder-api-key-histories-list").find(".builder-api-key-list-view").html(html);
                        $("#builder-api-key-histories-list").modal("show");
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