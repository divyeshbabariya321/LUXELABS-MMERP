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
	<div class="col-lg-12 margin-tb">
		<div class="row">
			<div class="col col-md-12">
				<div class="row">
					<button style="display: inline-block;" class="btn pl-5 btn-sm btn-image btn-add-action-password">
						<img src="/images/add.png" style="cursor: nwse-resize;">
					</button>
					<button type="button" class="btn btn-secondary remove-admin-password-btn">
		                Remove Admin Passwords
		          	</button> 
				</div>
			</div>
			<hr style=" width: 100%;">
			<div class="col col-md-12">
				<div class="h pl-5 pr-5" style="margin-bottom:10px;">
					<form class="form-inline message-search-handler" method="get" style="width: 100%; display: inline-block;">
						<div class="row">
							<div class="col col-md-12">
								<div class="col col-lg-6 pl-0">
									<div class="form-group" style="display: contents;">
										<b>Select Store Website: </b>
                                        <select class="form-control globalSelect2" multiple="true" id="storewebsiteid" name="storewebsiteid[]" placeholder="Select Store Website">
                                            @foreach($storeWebsites as $val)
                                            	<option value="{{ $val->id }}" @if(in_array($val->id, $request->input('storewebsiteid', []))) selected @endif>{{ $val->title }}</option>
                                            @endforeach
                                        </select>
	                                </div>
								</div>
								<div class="col col-lg-3 p-0">
									<div class="form-group">
										<button style="padding-top: 30px;" class="btn btn-sm btn-image btn-secondary" type="submit">
											<img src="/images/search.png" style="cursor: default;">
										</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-12 pl-5 pr-5">
		<form action="/store-website/generate-api-token" method="post">
			<?php echo csrf_field(); ?>
			
			<div class="col-md-12">
				<div class="table-responsive mt-3">
					<table class="table table-bordered overlay admin-password-table" id="tblAdminPassword">
						<thead>
						<tr>
							<th><input type="checkbox" name="select_all_admin_passsword" class="select_all_admin_passsword"></th>
							<th>Id</th>
							<th width="30%">Website</th>
							<th width="30%">Username</th>
							<th width="30%">Password</th>	
							<th>Action</th>											
						</tr>
						</thead>
						<tbody>
							@include('storewebsite::admin-passwordlist')
						</tbody>
					</table>
				</div>
			</div>
		</form>
	</div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div class="common-modal modal" role="dialog">
	<div class="modal-dialog" role="document" style="width: 1000px; max-width: 1000px;">
	</div>
</div>

@include("storewebsite::templates.create-website-password-template")

<script type="text/javascript" src="{{ asset('/js/jsrender.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('/js/jquery-ui.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/store-website.js') }}"></script>

<script type="text/javascript">
	page.init({
		bodyView: $("#common-page-layout"),
		baseUrl: "<?php echo url('/'); ?>"
	});

	$(document).on('click', '.expand-row-msg', function () {
		var name = $(this).data('name');
		var id = $(this).data('id');
		var full = '.expand-row-msg .show-short-'+name+'-'+id;
		var mini ='.expand-row-msg .show-full-'+name+'-'+id;
		$(full).toggleClass('hidden');
		$(mini).toggleClass('hidden');
    });

	
	$(document).on('click', '.btn_expand_inactive', function () {
		var cls = $(this).parent();
		if(cls.height() != '297')
			$(cls).css('height', 'auto');
		else
			$(cls).css('height', '44px');
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

	$(document).ready(function() {
		$('.select_all_admin_passsword').on('change', function() {
            var isChecked = $(this).prop('checked');
            $('.admin_password_check').prop('checked', isChecked);
        });
    });

    $(document).on("click",".remove-admin-password-btn",function() {
		var selectedCheckboxes = [];
        var fileIDs = [];

        if ($('.select_all_admin_passsword').prop('checked')) {
            $('.admin_password_check').each(function() {
                var fileID = $(this).data('id');
                var checkboxValue = $(this).val();

                fileIDs.push(fileID);
                selectedCheckboxes.push(checkboxValue);
            });
        } else {
            $('input[name="admin_password_check"]:checked').each(function() {
                var fileID = $(this).data('id');
                var checkboxValue = $(this).val();

                fileIDs.push(fileID);
                selectedCheckboxes.push(checkboxValue);
            });
        }

        if (selectedCheckboxes.length === 0) {
            alert('Please select at least one checkbox.');
            return;
        }  

        var formData = {
            ids: selectedCheckboxes 
        };

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: '{{ route('delete-admin-passwords') }}',
            data: formData,
            success: function(response) {
                toastr["success"]("Admin Password has been deleted successfully");
               	location.reload();
            },
            error: function(error) {
                console.error('Error:', error);
                location.reload();
            }
        });      
	});

    $(document).on("click", ".btn-magento-user-request", function () {
      $("#request-response-"+$(this).attr("data-id")).toggle();
    });
</script>

@endsection