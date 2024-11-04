@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
<style type="text/css">
	.preview-category input.form-control {
		width: auto;
	}

	.h .lable-class {justify-content: left;}
</style>

<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
		<h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
	</div>
	<br>
	<div class="col-lg-12 margin-tb">
		<div class="row">
			<div class="col col-md-5">
				<div class="row">

					<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-add-action" data-toggle="modal" data-target="#colorCreateModal">
						<img src="/images/add.png" style="cursor: default;">
					</button>					
					<button style="display: inline-block;" class="btn btn-secondary btn-add-default-store m-2" data-toggle="modal" data-target="#sync-website">
						Sync Website
					</button>
					<button style="display: inline-block;width: 29%" class="btn btn-secondary m-2" data-toggle="modal" data-target="#copyWebsiteModal">
						Copy Website Store View
					</button>
					<button style="display: inline-block;width: 29%" class="btn btn-secondary m-2" data-toggle="modal" data-target="#deleteWebsiteModal">
						Delete Website Store View
					</button>
				</div>
			</div>
			<hr style="width:100%">
			<div class="col  col-md-12">
				<div class="h" style="margin-bottom:10px;">
					<div class="row" style="display: inline-block; width: 100%;">
						<form class="form-inline message-search-handler" method="get">
							<div class="col">
								<div class="col col-md-2">
									<div class="form-group">
										<label class="lable-class" for="keyword">Update Websites:</label>
										<select class="form-control" id="updateStoreWebsite">
											<option value="">-- Select a website to update--</option>
											@foreach($storeWebsites as $key => $storeWebsite)
												<option value="{{ $key }}">{{ $storeWebsite }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="col col-md-4">
									<div class="form-group" style="width: 100%;">
										<label class="lable-class" for="keyword">Websites:</label>
                                        {{ html()->select('website_store[]', [] + $storeWebsites, request('website_store'))->class('form-control select2-vendor globalSelect2')->id('srch_website_store')->multiple()->attribute('onchange', 'loadWebsiteStoresDropdown()') }}
									</div>
								</div>
								<div class="col col-md-4">
									<div class="form-group" style="width: 100%;">
										<label class="lable-class" for="keyword">Website Stores:</label>
                                        {{ html()->select('website_store_id[]', [], request('website_store_id'))->class('form-control select2-vendor globalSelect2')->multiple()->id('srch_website_store_id') }}
									</div>
								</div>
								<div class="col col-md-2">
									<div class="form-group">
										<label class="lable-class" for="keyword">Keyword:</label>
                                        {{ html()->text('keyword', request('keyword'))->class('form-control')->placeholder('Enter keyword') }}									</div>
									<div class="form-group">
										<label for="button">&nbsp;</label>
										<button type="submit" style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
											<img src="/images/search.png" style="cursor: default;">
										</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-success" id="alert-msg" style="display: none;">
					<p></p>
				</div>
			</div>
		</div>
		<div class="col-md-12 margin-tb" id="page-view-result">

		</div>
	</div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
          50% 50% no-repeat;display:none;">
</div>
<div id="sync-website" class="modal" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Sync Website</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form>

				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-secondary move-stores">Move Store</button>
			</div>
		</div>
	</div>
</div>

<div id="copyWebsiteModal" class="modal" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Copy Website Store View</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<select id="selCopyWebsite" name="selCopyWebsite" class="form-control">
					<option value="">--Select Website--</option>	
					@foreach($storeWebsites as $key => $val)
						<option value="{{ $key }}">{{ $val }}</option>
					@endforeach
				</select>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-secondary copy-website-store-view-btn">Copy website store view</button>
			</div>
		</div>
	</div>
</div>

<div id="deleteWebsiteModal" class="modal" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Delete Website Store View</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<select id="selDeleteServer" name="selDeleteServer" class="form-control">
					<option value="">--Select Server--</option>
					@foreach($storeServers as $storeServer)
						<option value="{{ $storeServer->server_id }}">Server {{ $storeServer->server_id }}</option>
					@endforeach
				</select>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-secondary delete-website-store-view-btn">Delete website store view Store</button>
			</div>
		</div>
	</div>
</div>

<div class="common-modal modal" role="dialog">
	<div class="modal-dialog" role="document">
	</div>
</div>


@include("storewebsite::website-store-view.templates.list-template")
@include("storewebsite::website-store-view.templates.create-website-template")
@include("storewebsite::website-store-view.templates.create-group-template")

<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/website-store-view.js') }}"></script>

<script type="text/javascript">
	var urlWebsiteStoresDropdown = "{{ route('store-website.website-stores.dropdown') }}";

	page.init({
		bodyView: $("#common-page-layout"),
		baseUrl: "<?php echo url('/'); ?>"
	});
	var agents = [];
	$.ajax({
		type: "GET",
		url: "/store-website/website-store-views/agents",
	}).done(function(response) {
		agents = response;
	});
	var groups = [];
	$.ajax({
		type: "GET",
		url: "/store-website/website-store-views/groups",
	}).done(function(response) {
		groups = response;
	});

	$(document).on("click", ".btn-create-group", function(e) {

		let code = $(this).closest('tr').children('.code_div').text() == '1' ? '1' : $(this).closest('tr').children('.code_div').text().split('-')[1];
		$('.modal-body #name').val($(this).closest('tr').children('.name_div').text().trim() + '_' + code);
		let html_groups = `<div class="form-group col-md-12 group"><select name="group" class="form-control select-2"><option value="">Choose Theme</option>`;
		for (let i = 0; i < groups.responseData.length; i++) {
			html_groups += `<option value="${groups.responseData[i].id}">${groups.responseData[i].name}</option>`;
		}
		html_groups += '</select></div>';

		$('.modal-body .name_div').after(html_groups);
		let options = `<select name="agents[]" class="form-control select-2"> `;
		for (let i = 0; i < agents.responseData.length; i++) {
			options += `<option value="${agents.responseData[i].id}">${agents.responseData[i].id}</option>`;
		}
		options += '</select>';
		html_agents = `
				<div class="abc">
					<div class="form-group col-md-7 agents">
						${options}
					</div> 
					<div class="form-group col-md-4 priorities">
						<select name="priorites[]" class="form-control select-2"> 
							<option value="first">first</option> 
							<option value="normal" selected>normal</option> 
							<option value="last">last</option> 
							<option value="supervisor">supervisor</option> 
						</select>
					</div>
					<div class="form-group col-md-1">
						<button type="button" title="Remove" data-id="" class="btn btn-remove-priority">
							<i class="fa fa-close" aria-hidden="true"></i>
						</button>
					</div>
				</div>
			`;
		$('.modal-body').append(html_agents);
	});
	$(document).on('click', '.btn-remove-priority', function() {
		$(this).closest('.abc').remove();
	});
	$(document).on('click', '.btn-add-priority', function() {
		$.ajax({
			type: "GET",
			url: "/store-website/website-store-views/agents",
		}).done(function(response) {
			let options = `<select name="agents[]" class="form-control select-2"> `;
			for (let i = 0; i < response.responseData.length; i++) {
				options += `<option value="${response.responseData[i].id}">${response.responseData[i].id}</option>`;
			}
			options += '</select>';
			var html = `
				<div class="abc">
					<div class="form-group col-md-7 agents">
						${options}
					</div> 
					<div class="form-group col-md-4 priorities">
						<select name="priorites[]" class="form-control select-2"> 
							<option value="first">first</option> 
							<option value="normal" selected>normal</option> 
							<option value="last">last</option> 
							<option value="supervisor">supervisor</option> 
						</select>
					</div>
					<div class="form-group col-md-1">
						<button type="button" title="Remove" data-id="" class="btn btn-remove-priority">
							<i class="fa fa-close" aria-hidden="true"></i>
						</button>
					</div>
				</div>
			`;
			$('.modal-body').append(html);
		}).fail(function(response) {

		});
	});


	function loadWebsiteStoresDropdown() {
		jQuery.ajax({
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			type: "GET",
			url: urlWebsiteStoresDropdown,
			data: {
				srch_website_store: jQuery('#srch_website_store').val(),
			}
		}).done(function(response) {
			jQuery('#srch_website_store_id').html(response.data);
		}).fail(function(response) {});
	}


	$(document).on('click', '.copy-website-store-view-btn', function() {
		var website_id = $('#selCopyWebsite').val();
		if(website_id == "" || website_id == undefined ) {
			toastr["error"]("Please select the website", "error");
			return false;
		}
		$.ajax({
			type: "GET",
			url: "/store-website/copy-website-store-views/"+ website_id,
			beforeSend: function () {
				$("#loading-image").show();
			},
		}).done(function(response) {
			$("#loading-image").hide();
			if(response.code == 200) {
				toastr['success'](response.message, 'Success');
				$('#copyWebsiteModal').modal('hide');
			} else {
				toastr['error'](response.message, 'error');
			}
		}).fail(function(response) {

		});
	});

	$(document).on('click', '.delete-website-store-view-btn', function() {
		var serverid = $('#selDeleteServer').val();
		if(serverid == "" || serverid == undefined ) {
			toastr["error"]("Please select the server", "error");
			return false;
		}

		if (confirm("Are you sure want to delete") == true) {
			$.ajax({
				type: "GET",
				url: "/store-website/delete-store-views/"+serverid,
				beforeSend: function () {
					$("#loading-image").show();
				},
			}).done(function(response) {
				$("#loading-image").hide();
				if(response.code == 200) {
					toastr['success'](response.message, 'Success');
					$('#deleteWebsiteModal').modal('hide');
				} else {
					toastr['error'](response.message, 'error');
				}
			}).fail(function(response) {
				toastr['error'](response.message, 'error');
			});
		} else {
			$('#selDeleteServer').val('');
			$('#deleteWebsiteModal').modal('hide');
		}
		
	});
	
</script>
@endsection
