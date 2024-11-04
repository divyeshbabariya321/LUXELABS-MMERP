@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Ui Check')

@section('styles')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<style type="text/css">
	.preview-category input.form-control {
		width: auto;
	}
	.infinite-scroll table.dataTable {
		width: 100% !important;
		display: inline-block !important;
		overflow-x: scroll !important;
	}
	#loading-image {
		position: fixed;
		top: 50%;
		left: 50%;
		margin: -50px 0px 0px -50px;
	}

	.dis-none {
		display: none;
	}

	.pd-5 {
		padding: 3px;
	}

	.toggle.btn {
		min-height: 25px;
	}

	.toggle-group .btn {
		padding: 2px 12px;
	}

	.latest-remarks-list-view tr td {
		padding: 3px !important;
	}

	#latest-remarks-modal .modal-dialog {
		max-width: 1100px;
		width: 100%;
	}

	.btn-secondary {
		border: 1px solid #ddd;
		color: #757575;
		background-color: #fff !important;
	}

	.modal {
		overflow-y: auto;
	}

	body.overflow-hidden {
		overflow: hidden;
	}

	span.user_point_none button,
	span.admin_point_none button {
		pointer-events: none;
		cursor: not-allowed;
	}

	table tr:last-child td {
		border-bottom: 1px solid #ddd !important;
	}

	select.globalSelect2+span.select2 {
		width: calc(100% - 26px) !important;
	}
</style>
@endsection

@section('large_content')

<div id="myDiv">
	<img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
</div>

<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
		<h2 class="page-heading">Ui Check (0)</h2>
	</div>

	<div class="col-lg-12 margin-tb">
		<div class="row">
			<div class="col-md-12">
				<form>
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<input type="text" name="id" id="id" class="form-control" value="{{old('id')}}" placeholder="Please Enter Uicheck Id" />
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<?php 
								//dd(request('folder_name'));
								if(request('assign_to')){   $assign_toArr = request('assign_to'); }
								else{ $assign_toArr = []; }
							  ?>
								<select name="assign_to[]" id="assign_to" multiple class="form-control select2">
									<option value="" @if(count($assign_toArr)==0) selected @endif>-- Select Assign to --</option>
									@forelse($users as $user)
									<option value="{{ $user->id }}" @if($assign_to==$user->id)
										selected
										@endif>{{ $user->name }}</option>
									@empty
									@endforelse
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<?php 
									if(request('store_webs')){   $store_websArr = request('store_webs'); }
									else{ $store_websArr = []; }
							  	?>
								<select name="store_webs[]" id="store_webiste" multiple class="form-control select2">
									<option value=""  @if(count($store_websArr)==0) selected @endif>-- Select a website --</option>
									@forelse($all_store_websites as $asw)
									<option value="{{ $asw->id }}" @if($search_website==$asw->id)
										selected
										@endif>{{ $asw->title }}</option>
									@empty
									@endforelse
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<?php 
									if(request('store_webs')){   $categoriesArr = request('store_webs'); }
									else{ $categoriesArr = []; }
							  	?>
								<select name="categories[]" id="store-categories" multiple class="form-control select2">
									<option value="" @if(count($categoriesArr)==0) selected @endif>-- Select a categories --</option>
									@forelse($site_development_categories as $ctId => $ctName)
									<option value="{{ $ctId }}" @if($search_category==$ctId) selected @endif>{!! $ctName !!}</option>
									@empty
									@endforelse
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<?php 
									if(request('store_webs')){   $dev_statusArr = request('store_webs'); }
									else{ $dev_statusArr = []; }
							  	?>
								<select name="dev_status[]" id="dev_status" multiple class="form-control select2">
									<option value="" @if(count($dev_statusArr)==0) selected @endif>-- Developer Status --</option>
									@forelse($allStatus as $key => $ds)
									<option value="{{ $key }}" @if($dev_status==$key) selected @endif>{{ $ds }}</option>
									@empty
									@endforelse
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<?php 
									if(request('admin_status')){   $admin_statusArr = request('store_webs'); }
									else{ $admin_statusArr = []; }
							  	?>
								<select name="admin_status[]" id="admin_status" multiple class="form-control select2">
									<option value="" @if(count($admin_statusArr)==0) selected @endif>-- Admin Status --</option>
									@forelse($allStatus as $key => $as)
									<option value="{{ $key }}" @if($dev_status==$key) selected @endif>{{ $as }}</option>
									@empty
									@endforelse
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<select name="order_by" id="order_by" class="form-control select2">
									<option value="">--Order By--</option>
									<option value="website_id">Website</option>
									<option value="issue">Issue</option>
									<option value="communication_message">Communication</option>
									<option value="dev_status_id">Developer Status</option>
									<option value="admin_status_id">Admin Status</option>
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<select name="srch_lock_type" id="srch_lock_type" class="form-control select2">
									<option value="">-- Select (Hide/Show) --</option>
									<option value="0">All</option>
									<option value="1" selected>All Unhide</option>
									<option value="2">Admin & Developer both hidden</option>
									<option value="3">Admin hidden only</option>
									<option value="4">Developer hidden only</option>
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<select name="srch_flags" id="srch_flags" class="form-control select2">
									<option value="">-- Select Flag --</option>
									<option value="0">All</option>
									<option value="Both">Both</option>
									<option value="Language flag">Language flag</option>
									<option value="Translation flag">Translation flag</option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<button type="button" class="btn btn-primary custom-filter">Search</button>
							<button type="button" class="btn btn-secondary" onclick="loadAllHistory(1)">All History</button>
							@if (auth()->user()->isAdmin())
							<button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#newTypeModal">Create Type</button>
							@endif
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<div id="newTypeModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h3>Add new status</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form style="padding:10px;" action="{{ route('uicheck.type.store') }}" method="POST">
				@csrf
				<div class="form-group">
					<input type="text" class="form-control" name="name" placeholder="Name" value="{{ old('name') }}" required>

					@if ($errors->has('name'))
					<div class="alert alert-danger">{{$errors->first('name')}}</div>
					@endif
				</div>

				<button type="submit" class="btn btn-secondary ml-3">Add Type</button>
			</form>
		</div>
	</div>
</div>
@if (Session::has('message'))
{{ Session::get('message') }}
@endif
<br />
<div class="row mt-2">
	<div class="col-md-12 margin-tb infinite-scroll">
		<div class="table-responsive">
			<table class="table table-bordered" id="uicheck_table">
				<thead>
					<tr>
						<th width="5%"><input type="checkbox" id="checkAll" title="click here to select all" /></th>
						<th width="5%">Uicheck Id</th>
						<th width="8%">Categories</th>
						<th width="4%">Website</th>
						@if (Auth::user()->hasRole('Admin'))
						<th width="8%">Assign To</th>
						@endif
						<th width="10%">Issue</th>
						<th width="12%">Communication</th>
						<th width="10%">Developer Status</th>
						<th width="10%">Type</th>
						<th width="10%">Admin Status</th>
						<th width="5%">Actions</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
</div>
<div id="dev_status_model" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h2>Developer Status History</h2>
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>
			<div class="modal-body" id="">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>ID</th>
								<th>User Name</th>
								<th>Old Status</th>
								<th>Status</th>
								<th>Date</th>

							</tr>
						</thead>
						<tbody id="dev_status_tboday">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="admin_status_model" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h2>Admin Status History</h2>
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>
			<div class="modal-body" id="">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>ID</th>
								<th>User Name</th>
								<th>Old Status</th>
								<th>Status</th>
								<th>Date</th>

							</tr>
						</thead>
						<tbody id="admin_status_tboday">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@include('uicheck.upload-document-modal')
@include("partials.plain-modal")
<div id="issue_model" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h2>U I Issue History</h2>
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>
			<div class="modal-body" id="">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>ID</th>
								<th>User Name</th>
								<th>Old Issue</th>
								<th>Issue</th>
								<th>Date</th>

							</tr>
						</thead>
						<tbody id="issue_tboday">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="message_model" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h2>U I Message History</h2>
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>
			<div class="modal-body" id="">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>ID</th>
								<th>User Name</th>
								<th>Message</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody id="message_tboday">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="assignTo_model" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h2>U I Assign to History</h2>
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>
			<div class="modal-body" id="">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>ID</th>
								<th>Change by</th>
								<th>Assign to</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody id="assignTo_tboday">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modalAllHistory" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg" style="max-width: none !important;width: 85% !important;">
		<div class="modal-content">
			<div class="modal-header">
				<h2>All History</h2>
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>
			<div class="modal-body" style="overflow-y: scroll;height: 650px;">
				<select class="searAllhistory select2" onchange="loadAllHistory(1)" style="width: 150px;">
					<option value="">- Select -</option>
					<?php foreach ($users as $user) {
						echo '<option value="' . $user['id'] . '">' . $user['name'] . '</option>';
					} ?>
				</select>
				<br /><br />
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th width="6%">Uicheck Id</th>
							<th width="14%">Category</th>
							<th width="15%">Website</th>
							<th width="8%">Type</th>
							<th width="15%">Old Value</th>
							<th width="15%">New Value</th>
							<th width="14%">Added By</th>
							<th width="13%">Date</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
				<div class="row mt-3 text-center cls-load-more">
					<div class="col-md-12">
						<a class="btn btn-primary" href="javascript:void(0);" onclick="loadAllHistory(0)">Load More</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modalDateUpdates" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Update Dates</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<label>Start Date:</label>
						<div class="form-group">
							<div class='input-group date cls-start-due-date'>
								<input type="text" class="form-control input-sm" id="modal_start_time" name="modal_start_time" value="" />
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<label>Expected Completion Time:</label>
						<div class="form-group">
							<div class='input-group date cls-start-due-date'>
								<input type="text" class="form-control input-sm" id="modal_expected_completion_time" name="modal_expected_completion_time" value="" />
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
					</div>
					<?php if (\Auth::user()->hasRole('Admin')) { ?>
						<div class="col-md-12">
							<label>Actual Completion Time:</label>
							<div class="form-group">
								<div class='input-group date cls-start-due-date'>
									<input type="text" class="form-control input-sm" id="modal_actual_completion_time" name="modal_actual_completion_time" value="" />
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</span>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" onclick="funDateUpdatesSubmit('{!! route('uicheck.update.dates') !!}')">Save</button>
			</div>
		</div>
	</div>
</div>

<div id="modalCreateLanguage" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Languages</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="col-md-12">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th width="5%">ID</th>
								<th width="15%" style="word-break: break-all;">Language</th>
								<th width="22%" style="word-break: break-all;">Message</th>
								<th width="22%" style="word-break: break-all;">Estimated Time</th>
								<th width="22%" style="word-break: break-all;">Attachment</th>
								<th width="21%">Status</th>
								
							</tr>
						</thead>
						<tbody>
							@foreach ($languages as $language)
								<tr>
									<td>{{$language->id}}</td>
									<td>{{$language->name}}</td>
									<td><input type="text" name="uilanmessage{{$language->id}}" class="uilanmessage{{$language->id}}" style="margin-top: 0px;width:80% !important;"/>
										<button class="btn pr-0 btn-xs btn-image message-language" onclick="funLanUpdate('{{$language->id}}');"><img src="{{asset('/images/filled-sent.png')}}" style="cursor: nwse-resize; width: 0px;"></button>
										<i class="fa fa-info-circle languageHistorty" onclick="funGetLanHistory('{{$language->id}}');"></i>
									</td>
									<td>
										<input type="time" name="uilanestimated_time{{$language->id}}" class="uilanestimated_time{{$language->id}}" style="margin-top: 0px;width:80% !important;"/>
									</td>
									<td><input type="hidden" class="uicheckId" value=""/>
										<button type="button" data-toggle="tooltip" title="Upload File" data-id="{{$language->id}}" class="btn btn-file-upload pd-5">
											<i class="fa fa-upload" aria-hidden="true"></i>
										</button>
										<button type="button" data-id="{{$language->id}}" data-toggle="tooltip" title="List of Files" class="btn btn-file-list pd-5">
											<i class="fa fa-list" aria-hidden="true"></i>
										</button>	
									</td>
									<td>										
										<select name="uilanstatus" id="uilanstatus{{$language->id}}" class="uilanstatus{{$language->id}} pd-5" style="margin-top: 0px;width:80% !important;">
											<option value="">-- Status --</option>
											@forelse($allStatus as $key => $as)
											<option value="{{ $key }}" @if($dev_status==$key) selected @endif>{{ $as }}</option>
											@empty
											@endforelse
										</select>
									</td>
								</tr>
							@endforeach
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

<div id="file-upload-area-section" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
		   <form action="{{ route("uicheck.create.attachment") }}" method="POST" enctype="multipart/form-data">
			  <input type="hidden" name="id" id="language_id" value="">
			  <input type="hidden" name="uicheck_id" class="uicheck_id" value="">
			  <div class="modal-header">
				  <h4 class="modal-title">Upload File(s)</h4>
			  </div>
			  <div class="modal-body" style="background-color: #999999;">
			  @csrf
			  <div class="form-group">
				  <label for="document">Documents</label>
				  <div class="needsclick dropzone" id="document-dropzone">

				  </div>
			  </div>

			  </div>
			  <div class="modal-footer">
				  <button type="button" class="btn btn-default btn-save-documents">Save</button>
				  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			  </div>
	  </form>
		</div>
	</div>
</div>

<div id="modalGetMessageHistory" class="modal fade" role="dialog" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Ui Language Message History </h4>  
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="col-md-12">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th width="5%">ID</th>
								<th width="8%">Update By</th>
								<th width="25%" style="word-break: break-all;">Message</th>
								<th width="15%" style="word-break: break-all;">Status</th>
								<th width="15%">Created at</th>
							</tr>
						</thead>
						<tbody>
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

<div id="modalHistoryDateUpdates" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Date History</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="col-md-12">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th width="5%">ID</th>
								<th width="15%">Type</th>
								<th width="22%" style="word-break: break-all;">Old Value</th>
								<th width="22%" style="word-break: break-all;">New Value</th>
								<th width="21%">Update By</th>
								<th width="15%">Created at</th>
							</tr>
						</thead>
						<tbody>
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



<div id="file-upload-area-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-body">
            <table class="table table-bordered">
            <thead>
              <tr>
                <th width="5%">No</th>
                <th width="45%">Link</th>
                <th width="25%">Send To</th>
                <th width="25%">Action</th>
              </tr>
            </thead>
            <tbody class="display-document-list">
            </tbody>
        </table>
      </div>
           <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
  </div>



<div id="modalCreateDevice" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">5 Devices</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="col-md-12">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th width="5%">ID</th>
								<th width="15%" style="word-break: break-all;">Language</th>
								<th width="22%" style="word-break: break-all;">Message</th>
								<th width="22%" style="word-break: break-all;">Estimated Time</th>
								<th width="22%" style="word-break: break-all;">Attachment</th>
								<th width="21%">Status</th>
								
							</tr>
						</thead>
						<tbody>
							@for ($i=1; $i<=5; $i++)
								<tr>
									<td>{{$i}}</td>
									<td>Device {{$i}}</td>
									<td><input type="text" name="uidevmessage{{$i}}" class="uidevmessage{{$i}}" style="margin-top: 0px;width:80% !important;"/>
										<button class="btn pr-0 btn-xs btn-image div-message-language" onclick="funDevUpdate('{{$i}}');">
										<img src="{{asset('/images/filled-sent.png')}}" style="cursor: nwse-resize; width: 0px;"></button><i class="fa fa-info-circle devHistorty" onclick="funGetDevHistory('{{$i}}');"></i>
									</td>
									<td>
										<input type="time" name="uidevestimated_time{{$i}}" class="uidevestimated_time{{$i}}" style="margin-top: 0px;width:80% !important;"/>
										<button class="btn pr-0 btn-xs btn-image div-message-language" onclick="funDevUpdate('{{$i}}');">
										<img src="{{asset('/images/filled-sent.png')}}" style="cursor: nwse-resize; width: 0px;"></button><i class="fa fa-info-circle devHistorty" onclick="funGetDevHistory('{{$i}}');"></i>
									</td>
									<td><input type="hidden" class="uicheckId" value=""/>
										<input type="hidden" class="ui_check_id" value=""/>
										<button type="button" data-toggle="tooltip" title="Upload File" data-device_no="{{$i}}" class="btn btn-dev-file-upload pd-5">
											<i class="fa fa-upload" aria-hidden="true"></i>
										</button>
										<button type="button" data-device_no="{{$i}}" data-toggle="tooltip" title="List of Files" class="btn btn-dev-file-list pd-5">
											<i class="fa fa-list" aria-hidden="true"></i>
										</button>	
									</td>
									<td>										
										<select name="uidevstatus" id="uidevstatus{{$i}}" class="uidevstatus{{$i}} pd-5" style="margin-top: 0px;width:80% !important;">
											<option value="">-- Status --</option>
											@forelse($allStatus as $key => $as)
											<option value="{{ $key }}" @if($dev_status==$key) selected @endif>{{ $as }}</option>
											@empty
											@endforelse
										</select>
										<button class="btn pr-0 btn-xs btn-image dev-uicheck-status" onclick="funDevUpdate('{{$i}}');"><img src="{{asset('/images/filled-sent.png')}}" style="cursor: nwse-resize; width: 0px;"></button></td>
								</tr>
							@endfor
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

<div id="modalGetDevMessageHistory" class="modal fade" role="dialog" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Ui Device Message History</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="col-md-12">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th width="5%">ID</th>
								<th width="8%">Update By</th>
								<th width="25%" style="word-break: break-all;">Message</th>
								<th width="25%" style="word-break: break-all;">Estimated Time</th>
								<th width="15%" style="word-break: break-all;">Status</th>
								<th width="15%">Created at</th>
							</tr>
						</thead>
						<tbody>
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

<div id="dev-file-upload-area-section" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
		   <form action="{{ route("uicheck.create.dev.attachment") }}" method="POST" enctype="multipart/form-data">
			  <input type="hidden" name="uicheck_id" class="ui_check_id" value="">
			  <input type="hidden" name="device_no" id="device_no" value="">
			  <div class="modal-header">
				  <h4 class="modal-title">Upload File(s)</h4>
			  </div>
			  <div class="modal-body" style="background-color: #999999;">
			  @csrf
			  <div class="form-group">
				  <label for="document">Documents</label>
				  <div class="needsclick dropzone" id="document-dropzone">

				  </div>
			  </div>

			  </div>
			  <div class="modal-footer">
				  <button type="button" class="btn btn-default btn-dev-save-documents">Save</button>
				  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			  </div>
	  </form>
		</div>
	</div>
</div>

<div id="dev-file-upload-area-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-body">
            <table class="table table-bordered">
            <thead>
              <tr>
                <th width="5%">No</th>
                <th width="45%">Link</th>
				<th width="45%">Uploaded By</th>
                <th width="25%">Action</th>
              </tr>
            </thead>
            <tbody class="dev-display-document-list">
            </tbody>
        </table>
      </div>
           <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
  </div>

@if (Auth::user()->hasRole('Admin'))
<input type="hidden" id="user-type" value="Admin">
@else
<input type="hidden" id="user-type" value="Not Admin">
@endif

<div style="display: none;">
	<select id="dropdownAllStoreSites" class="save-item-select">
		<option value="">- Select -</option>
		<?php foreach ($all_store_websites as $dropValue) {
			echo '<option value="' . $dropValue->id . '">' . $dropValue->website . '</option>';
		} ?>
	</select>

	<select id="dropdownAllUsers" class="save-item-select">
		<option value="">- Select -</option>
		<?php foreach ($allUsers as $dropKey => $dropValue) {
			echo '<option value="' . $dropKey . '">' . $dropValue . '</option>';
		} ?>
	</select>

	<select id="dropdownAllTypes" class="save-item-select">
		<option value="">- Select -</option>
		<?php foreach ($allTypes as $dropKey => $dropValue) {
			echo '<option value="' . $dropKey . '">' . $dropValue . '</option>';
		} ?>
	</select>

	<select id="dropdownAllStatus" class="save-item-select">
		<option value="">- Select -</option>
		<?php foreach ($allStatus as $dropKey => $dropValue) {
			echo '<option value="' . $dropKey . '">' . $dropValue . '</option>';
		} ?>
	</select>

	<select id="dropdownAllStatusAdmin" class="save-item-select-admin" style="width:55% !important;">
		<option value="">- Select -</option>
		<?php foreach ($allStatus as $dropKey => $dropValue) {
			echo '<option value="' . $dropKey . '">' . $dropValue . '</option>';
		} ?>
	</select>
	
</div>

@endsection


@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
	var urlUicheckGet = "{{ route('uicheck.get') }}";
	var urlUicheckHistoryDates = "{{ route('uicheck.history.dates') }}";
	var isAdmin = "{{ Auth::user()->hasRole('Admin') ? 1 : 0 }}";


	function updateTypeId(ele, uicheck_id) {
		jQuery.ajax({
			url: "{{route('uicheck.type.save')}}",
			type: 'POST',
			data: {
				_token: "{{ csrf_token() }}",
				uicheck_id: uicheck_id,
				type: jQuery(ele).val(),
			},
			beforeSend: function() {
				jQuery(this).text('Loading...');
			},
			success: function(response) {
				if (response.code == 200) {
					toastr['success'](response.message);
					oTable.draw(false);
				} else {
					toastr['error'](response.message);
				}
			}
		}).fail(function(response) {
			toastr['error'](response.responseJSON.message);
		});
	}

	function updateAssignTo(ele, uicheck_id) {
		jQuery.ajax({
			url: "{{route('uicheck.user.access')}}",
			type: 'POST',
			data: {
				_token: "{{ csrf_token() }}",
				uicheck_id: uicheck_id,
				id: jQuery(ele).val(),
			},
			beforeSend: function() {
				jQuery(this).text('Loading...');
			},
			success: function(response) {
				if (response.code == 200) {
					toastr['success'](response.message);
					oTable.draw(false);
				} else {
					toastr['error'](response.message);
				}
			}
		}).fail(function(response) {
			toastr['error'](response.responseJSON.message);
		});
	}

	function updateWebsiteId(ele, uicheck_id, site_development_id, category) {
		jQuery.ajax({
			url: "{{ route('uicheck.store') }}",
			type: 'POST',
			data: {
				_token: "{{ csrf_token() }}",
				id: uicheck_id,
				website_id: jQuery(ele).val(),
				site_development_id: site_development_id,
				category: category,
			},
			beforeSend: function() {
				jQuery(this).text('Loading...');
			},
			success: function(response) {
				if (response.code == 200) {
					toastr['success'](response.message);
					oTable.draw(false);
				} else {
					toastr['error'](response.message);
				}
			}
		}).fail(function(response) {
			toastr['error'](response.responseJSON.message);
		});
	}

	function updateDeveloperStatus(ele, uicheck_id, site_development_id, category) {
		jQuery.ajax({
			url: "{{ route('uicheck.store') }}",
			type: 'POST',
			data: {
				_token: "{{ csrf_token() }}",
				developer_status: jQuery(ele).val(),
				id: uicheck_id,
				site_development_id: site_development_id,
				category: category,
			},
			beforeSend: function() {
				jQuery(this).text('Loading...');
			},
			success: function(response) {
				if (response.code == 200) {
					toastr['success'](response.message);
					oTable.draw(false);
				} else {
					toastr['error'](response.message);
				}
			}
		}).fail(function(response) {
			toastr['error'](response.responseJSON.message);
		});
	}

	function updateAdminStatus(ele, uicheck_id, site_development_id, category) {
		jQuery.ajax({
			url: "{{ route('uicheck.store') }}",
			type: 'POST',
			data: {
				_token: "{{ csrf_token() }}",
				admin_status: jQuery(ele).val(),
				id: uicheck_id,
				site_development_id: site_development_id,
				category: category,
			},
			beforeSend: function() {
				jQuery(this).text('Loading...');
			},
			success: function(response) {
				if (response.code == 200) {
					toastr['success'](response.message);
					oTable.draw(false);
				} else {
					toastr['error'](response.message);
				}
			}
		}).fail(function(response) {
			toastr['error'](response.responseJSON.message);
		});
	}

	function loadAllHistory(firstTime) {
		let mdl = jQuery('#modalAllHistory');
		let tbl = mdl.find('table');
		mdl.find('.cls-load-more').removeClass('d-none');
		if (firstTime == 1) {
			tbl.find('tbody').html('');
		}

		siteLoader(1);
		jQuery.ajax({
			url: "{{ route('uicheck.history.all') }}",
			type: 'GET',
			data: {
				lastDate: tbl.find('tbody tr:last').find('.cls-created-date').html(),
				user_id: jQuery(".searAllhistory").val()
			},
			beforeSend: function() {},
			success: function(response) {
				siteLoader(0);
				if (response.html) {
					tbl.find('tbody').append(response.html);
				} else {
					mdl.find('.cls-load-more').addClass('d-none');
				}
				if (firstTime == 1) {
					mdl.modal('show');
				}
			}
		}).fail(function(response) {
			toastr['error'](response.responseJSON.message);
			siteLoader(0);
		});
	}

	function funLockApply(type, id) {
		if (confirm('Are you sure, do you want to perform this action?')) {
			siteLoader(1);
			jQuery.ajax({
				url: "{{ route('uicheck.update.lock') }}",
				type: 'POST',
				data: {
					_token: "{{ csrf_token() }}",
					id: id,
					type: type
				},
				beforeSend: function() {},
				success: function(response) {
					siteLoader(0);
					siteSuccessAlert(response);
					oTable.draw(false);
				}
			}).fail(function(response) {
				siteErrorAlert(response);
				siteLoader(0);
			});
		}
	}

	function showLanguage(uiid){
		let mdl = jQuery('#modalCreateLanguage');
		$(".uicheckId").val(uiid);
		mdl.modal("show");
	}
	function showDevice(uiid){
		let mdl = jQuery('#modalCreateDevice');
		$(".uicheckId").val(uiid);
		$(".ui_check_id").val(uiid);
		
		mdl.modal("show");
	}

	function funLanUpdate(id) {
		siteLoader(true);
		let mdl = jQuery('#modalCreateLanguage');
		let uicheckId = jQuery('.uicheckId').val();
		let uilanmessage = jQuery('.uilanmessage'+id).val();
		let uilanestimated_time = jQuery('.uilanestimated_time'+id).val();
		let uilanstatus = jQuery('.uilanstatus'+id).val();
		jQuery.ajax({
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			url: "{{route('uicheck.set.language')}}",
			type: 'POST',
			data: {
				id: id,
				uicheck_id : uicheckId,
				message : uilanmessage,
				estimated_time : uilanestimated_time,
				uilanstatus : uilanstatus
			},
			beforeSend: function() {
				//jQuery("#loading-image").show();
			}
		}).done(function(response) {
			//siteLoader(false);
			toastr["success"]("Message saved successfully!!!");
			//mdl.find('tbody').html(response.html);
			//mdl.modal("show");
		}).fail(function(errObj) {
			//siteErrorAlert(errObj);
			toastr["error"](errObj);
			//siteLoader(false);
		});
	}

	var uploadedDocumentMap = {}
    Dropzone.options.documentDropzone = {
      url: '{{ route("voucher.upload-documents") }}',
      maxFilesize: 20, // MB
      addRemoveLinks: true,
      headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
      },
      success: function (file, response) {
          $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">')
          uploadedDocumentMap[file.name] = response.name
      },
      removedfile: function (file) {
          file.previewElement.remove()
          var name = ''
          if (typeof file.file_name !== 'undefined') {
            name = file.file_name
          } else {
            name = uploadedDocumentMap[file.name]
          }
          $('form').find('input[name="document[]"][value="' + name + '"]').remove()
      },
      init: function () {

      }
  }

	$(document).on("click",".btn-file-upload",function() {
      var $this = $(this);
      $("#file-upload-area-section").modal("show");
       $("#language_id").val($this.data("id"));
	   
	   $(".uicheck_id").val($(".uicheckId").val());
      // $("#hidden-site-id").val($this.data("site-id"));
      // $("#hidden-site-category-id").val($this.data("site-category-id"));
    });

	$(document).on("click",".btn-save-documents",function(e){
		e.preventDefault();
		var $this = $(this);
		var formData = new FormData($this.closest("form")[0]);
		$.ajax({
		url: 'uicheck/create/attachment',
		type: 'POST',
		headers: {
				'X-CSRF-TOKEN': "{{ csrf_token() }}"
			},
			dataType:"json",
		data: $this.closest("form").serialize(),
		beforeSend: function() {
			$("#loading-image").show();
				}
		}).done(function (data) {
		$("#loading-image").hide();
		toastr["success"]("Document uploaded successfully");
		//location.reload();
		}).fail(function (jqXHR, ajaxOptions, thrownError) {      
		toastr["error"](jqXHR.responseJSON.message);
		$("#loading-image").hide();
		});
	});

	$(document).on("click",".btn-file-list",function(e) {
        e.preventDefault();
        var $this = $(this);
        var id = $(this).data("id");
		var uicheck_id = $(".uicheckId").val();
        $.ajax({
          url: 'uicheck/get/attachment?id='+id+"&uicheck_id="+uicheck_id,
          type: 'GET',
          headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType:"json",
          beforeSend: function() {
            $("#loading-image").show();
                }
        }).done(function (response) {
          $("#loading-image").hide();
          var html = "";
          $.each(response.data,function(k,v){
            html += "<tr>";
              html += "<td>"+v.id+"</td>";
              html += "<td>"+v.url+"</td>";
              html += "<td><div class='form-row'>"+v.user_list+"</div></td>";
              html += '<td><a class="btn-secondary" href="'+v.url+'" data-id="'+v.id+'" target="__blank"><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;<a class="btn-secondary link-delete-document" data-id='+v.id+' href="_blank"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
            html += "</tr>";
          });
          $(".display-document-list").html(html);
          $("#file-upload-area-list").modal("show");
        }).fail(function (jqXHR, ajaxOptions, thrownError) {
			toastr["error"](jqXHR.responseJSON.message);
          $("#loading-image").hide();
        });
      });

	  $(document).on("click",".link-delete-document",function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        var $this = $(this);
        if(confirm("Are you sure you want to delete records ?")) {
          $.ajax({
            url: '/uicheck/delete/attachment',
            type: 'POST',
            headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
              },
              dataType:"json",
            data: { id : id},
            beforeSend: function() {
              $("#loading-image").show();
                  }
          }).done(function (data) {
            $("#loading-image").hide();
            toastr["success"]("Document deleted successfully");
            $this.closest("tr").remove();
          }).fail(function (jqXHR, ajaxOptions, thrownError) {
            toastr["error"](jqXHR.responseJSON.message);
            $("#loading-image").hide();
          });
        }
      });


	function funGetLanHistory(id) {
		//siteLoader(true);
		let mdl = jQuery('#modalGetMessageHistory');
		let uicheckId = jQuery('.uicheckId').val();
		
		jQuery.ajax({
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			url: "/uicheck/get/message/history/language",
			type: 'POST',
			data: {
				id: id,
				uicheck_id : uicheckId,
			},
			beforeSend: function() {
				//jQuery("#loading-image").show();
			}
		}).done(function(response) {
			//siteLoader(false);
			//siteSuccessAlert("Listed successfully!!!");
			//$("#modalCreateLanguage").modal("hide");
			
			mdl.find('tbody').html(response.html);
			
			mdl.modal("show");
		}).fail(function(errObj) {
			//siteErrorAlert(errObj);
			//siteLoader(false);
			toastr["error"](errObj);
		});
	}


	//5 Device
	function funDevUpdate(id) {
		//siteLoader(true);
		let mdl = jQuery('#modalCreateDevice');
		let uicheckId = jQuery('.uicheckId').val();
		let uidevmessage = jQuery('.uidevmessage'+id).val();
		let uidevdatetime = jQuery('.uidevestimated_time'+id).val();
		let uidevstatus = jQuery('.uidevstatus'+id).val();
		let device_no = jQuery('.device_no'+id).val();
		jQuery.ajax({
			
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			url: "{{route('uicheck.set.device')}}",
			type: 'POST',
			data: {
				device_no : id,
				uicheck_id : uicheckId,
				message : uidevmessage,
				uidevdatetime : uidevdatetime,
				uidevstatus : uidevstatus
			},
			beforeSend: function() {
				//jQuery("#loading-image").show();
			}
		}).done(function(response) {
			toastr["success"]("Record updated successfully!!!");
			//mdl.find('tbody').html(response.html);
			//mdl.modal("show");
		}).fail(function(errObj) {
			console.log(errObj);
			toastr["error"](errObj.message);
		});
	}

	function funGetDevHistory(id) {
		//siteLoader(true);
		let mdl = jQuery('#modalGetDevMessageHistory');
		let uicheckId = jQuery('.uicheckId').val();
		
		jQuery.ajax({
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			url: "{{route('uicheck.get.message.dev')}}",
			type: 'POST',
			data: {
				device_no: id,
				uicheck_id : uicheckId,
			},
			beforeSend: function() {
				//jQuery("#loading-image").show();
			}
		}).done(function(response) {
			//siteLoader(false);
			//siteSuccessAlert("Listed successfully!!!");
			$("#modalCreateLanguage").modal("hide");
			mdl.find('tbody').html(response.html);
			mdl.modal("show");
		}).fail(function (jqXHR, ajaxOptions, thrownError) {      
			toastr["error"](jqXHR.responseJSON.message);
			$("#loading-image").hide();
		});
	}
	var uploadedDocumentMapDev = {}
    Dropzone.options.documentDropzone = {
      url: '{{ route("ui.dev.upload-documents") }}',
      maxFilesize: 20, // MB
      addRemoveLinks: true,
      headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
      },
      success: function (file, response) {
          $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">')
          uploadedDocumentMapDev[file.name] = response.name
      },
      removedfile: function (file) {
          file.previewElement.remove()
          var name = ''
          if (typeof file.file_name !== 'undefined') {
            name = file.file_name
          } else {
            name = uploadedDocumentMapDev[file.name]
          }
          $('form').find('input[name="document[]"][value="' + name + '"]').remove()
      },
      init: function () {

      }
  }

	$(document).on("click",".btn-dev-file-upload",function() {
      var $this = $(this);
      $("#dev-file-upload-area-section").modal("show");
       $("#ui_check_id").val($("#ui_check_id").val());
	   $("#device_no").val($this.data("device_no"));
      
    });

	$(document).on("click",".btn-dev-save-documents",function(e){
		e.preventDefault();
		var $this = $(this);
		var formData = new FormData($this.closest("form")[0]);
		$.ajax({
		url: 'uicheck/create/dev/attachment',
		type: 'POST',
		headers: {
				'X-CSRF-TOKEN': "{{ csrf_token() }}"
			},
			dataType:"json",
		data: $this.closest("form").serialize(),
		beforeSend: function() {
			$("#loading-image").show();
				}
		}).done(function (data) {
		$("#loading-image").hide();
		toastr["success"]("Document uploaded successfully");
		//location.reload();
		}).fail(function (jqXHR, ajaxOptions, thrownError) {      
		toastr["error"](jqXHR.responseJSON.message);
		$("#loading-image").hide();
		});
	});

	$(document).on("click",".btn-dev-file-list",function(e) {
        e.preventDefault();
        var $this = $(this);
        var device_no = $(this).data("device_no");
		var ui_check_id = $(".ui_check_id").val();
        $.ajax({
          url: 'uicheck/get/dev/attachment?device_no='+device_no+"&ui_check_id="+ui_check_id,
          type: 'GET',
          headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType:"json",
          beforeSend: function() {
            $("#loading-image").show();
                }
        }).done(function (response) {
          $("#loading-image").hide();
          var html = "";
          $.each(response.data,function(k,v){
            html += "<tr>";
              html += "<td>"+v.id+"</td>";
              html += "<td>"+v.url+"</td>";
              html += "<td><div class='form-row'>"+v.userName+"</div></td>";
              html += '<td><a class="btn-secondary" href="'+v.url+'" data-id="'+v.id+'" target="__blank"><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;<a class="btn-secondary dev-link-delete-document" data-id='+v.id+' href="_blank"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
            html += "</tr>";
          });
          $(".dev-display-document-list").html(html);
          $("#dev-file-upload-area-list").modal("show");
        }).fail(function (jqXHR, ajaxOptions, thrownError) {
          toastr["error"]("Oops,something went wrong");
          $("#loading-image").hide();
        });
      });

	  $(document).on("click",".dev-link-delete-document",function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        var $this = $(this);
        if(confirm("Are you sure you want to delete records ?")) {
          $.ajax({
            url: '/uicheck/dev/delete/attachment',
            type: 'POST',
            headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
              },
              dataType:"json",
            data: { id : id},
            beforeSend: function() {
              $("#loading-image").show();
                  }
          }).done(function (data) {
            $("#loading-image").hide();
            toastr["success"]("Document deleted successfully");
            $this.closest("tr").remove();
          }).fail(function (jqXHR, ajaxOptions, thrownError) {
            toastr["error"]("Oops,something went wrong");
            $("#loading-image").hide();
          });
        }
      });


	// START Print Table Using datatable
	var oTable;
	$(document).ready(function() {
		if ($("#user-type").val() == "Admin") {
			var columns = [{
					data: null,
					width: "5%",
					render: function(data, type, full, meta) {
						return '<input type="checkbox" id="checkAll" title="click here to select all" /><a href="javascript:;" data-id="' + full.uicheck_id + '" class="upload-document-btn"><img width="15px" src="/images/attach.png" alt="" style="cursor: default;"><a><a href="javascript:;" data-id="' + full.uicheck_id + '" class="list-document-btn"><img width="15px" src="/images/archive.png" alt="" style="cursor: default;"><a>';
					}
				},
				{
					data: 'uicheck_id',
					render: function(data, type, full, meta) {
						return data;
					}
				},
				{
					data: 'title',
					render: function(data, type, row, meta) {
						var html = '<div class="col-md-12 mb-1 p-0 d-flex pt-2 mt-1">' + data + '<button type="button" class="btn btn-xs duplicate-category" title="Duplicate Category" data-id="' + row.uicheck_id + '"><i data-id="' + row.uicheck_id + '" class="fa fa-plus"></i></button></div>';
						return html;
					}
				},
				{
					data: null,
					render: function(data, type, row, meta) {
						let sel = jQuery('#dropdownAllStoreSites').clone();
						sel.removeAttr('id');
						sel.addClass('globalSelect2');
						sel.find('option[value=' + row.websiteid + ']').attr('selected', 'selected');
						sel.attr('onchange', 'updateWebsiteId(this, "' + row.uicheck_id + '", "' + row.site_id + '", "' + row.id + '")');
						let html = jQuery("<div />").append(sel).html();
						return html;
					}
				},
				{
					data: null,
					render: function(data, type, row, meta) {
						let sel = jQuery('#dropdownAllUsers').clone();
						sel.removeAttr('id');
						sel.addClass('globalSelect2');
						sel.find('option[value=' + row.accessuser + ']').attr('selected', 'selected');
						sel.attr('onchange', 'updateAssignTo(this, "' + row.uicheck_id + '")');
						let html = jQuery("<div />").append(sel).append("<button type='button' class='btn btn-xs show-assign-history' title='Show Issue History' data-id='" + row.uicheck_id + "'><i data-id='" + row.uicheck_id + "' class='fa fa-info-circle'></i></button>").html();
						return html;
					}
				},
				{
					data: 'issue',
					render: function(data, type, row, meta) {
						var html = '<div class="col-md-12 mb-1 p-0 d-flex pt-2 mt-1"><input style="margin-top: 0px;min-width:76px !important;" type="text" class="form-control " id="issue-' + row.uicheck_id + '" name="issue-' + row.uicheck_id + '" placeholder="Issues" value="' + row.issue + '"><div style="margin-top: 0px;" class="d-flex p-0"><button class="btn pr-0 btn-xs btn-image issue" data-category="' + row.id + '" data-id="' + row.uicheck_id + '" data-site_development_id="' + row.site_id + '"><img src="/images/filled-sent.png" /></button></div><button type="button" class="btn btn-xs show-issue-history" title="Show Issue History" data-id="' + row.uicheck_id + '"><i data-id="' + row.uicheck_id + '" class="fa fa-info-circle"></i></button></div>';
						return html;
					}
				},
				{
					data: null,
					render: function(data, type, row, meta) {
						var html = '<div class="col-md-12 mb-1 p-0 d-flex pl-4 pt-2 mt-1 msg" style="width: 100%;"><input type="text" style="width: 100%; float: left;" class="form-control quick-message-' + row.uicheck_id + ' input-sm" name="message" placeholder="Message" value=""><div class="d-flex p-0"><button style="float: left;padding: 0 0 0 5px" class="btn btn-sm btn-image uicheck-message" title="Send message" data-category="' + row.id + '" data-taskid="' + row.uicheck_id + '"  data-id="' + row.uicheck_id + '" data-site_development_id="' + row.site_id + '"><img src="/images/filled-sent.png" style="cursor: default;"></button></div><button type="button" class="btn btn-xs btn-image show-message-history load-body-class" data-object="uicheck" data-id="' + row.uicheck_id + '" title="Load messages" data-category="' + row.id + '" data-site_development_id="' + row.site_id + '" data-dismiss="modal"><img src="/images/chat.png" alt=""></button></div>';
						return html;
					}
				},
				{
					data: null,
					render: function(data, type, row, meta) {
						let sel = jQuery('#dropdownAllStatus').clone();
						sel.removeAttr('id');
						sel.addClass('globalSelect2');
						sel.find('option[value=' + row.dev_status_id + ']').attr('selected', 'selected');
						sel.attr('onchange', 'updateDeveloperStatus(this, "' + row.uicheck_id + '", "' + row.site_id + '", "' + row.id + '")');
						let html = jQuery("<div />").append(sel).html();
						html += '<button type="button" class="btn btn-xs show-dev-status-history" title="Show Developer Status History" data-id="' + row.uicheck_id + '"><i data-id="' + row.uicheck_id + '" class="fa fa-info-circle"></i></button>';
						return html;
					}
				},
				{
					data: null,
					render: function(data, type, row, meta) {
						let sel = jQuery('#dropdownAllTypes').clone();
						sel.removeAttr('id');
						sel.addClass('globalSelect2');
						sel.find('option[value=' + row.uicheck_type_id + ']').attr('selected', 'selected');
						sel.attr('onchange', 'updateTypeId(this, "' + row.uicheck_id + '")');
						let html = jQuery("<div />").append(sel).html();
						return html;
					}
				},
				{
					data: null,
					render: function(data, type, row, meta) {
						let sel = jQuery('#dropdownAllStatusAdmin').clone();
						let lflag = (row.language_flag == 1) ? 'red': 'gray';
						let tflag = (row.translation_flag == 1) ? 'flagged.png': 'unflagged.png';
						sel.removeAttr('id');
						sel.addClass('globalSelect2');
						sel.find('option[value=' + row.admin_status_id + ']').attr('selected', 'selected');
						sel.attr('onchange', 'updateAdminStatus(this, "' + row.uicheck_id + '", "' + row.site_id + '", "' + row.id + '")');
						let html = jQuery("<div />").append(sel).html();
						html += '<button type="button" class="btn btn-xs show-admin-status-history" title="Show" data-id="' + row.uicheck_id + '"><i data-id="' + row.uicheck_id + '" class="fa fa-info-circle"></i></button> <button type="button" class="btn btn-xs language-flag-btn " id="language-flag-btn-' + row.uicheck_id + '" style="color:'+lflag+';" title="Language flag" data-id="' + row.uicheck_id + '"><i class="fa fa-flag"></i></button> | <button type="button" class="btn btn-xs translation-flag-btn" id="translation-flag-btn-' + row.uicheck_id + '" title="Translation flag" data-id="' + row.uicheck_id + '"><img src="/images/'+tflag+'" style="width:16px;height:16px;"></button>';
						return html;
					}
				},
				{
					data: null,
					render: function(data, type, row, meta) {
						return '<div class="dropdown dropleft">' +
							'<a class="btn btn-secondary btn-sm dropdown-toggle" href="javascript:void(0);" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</a>' +
							'<div class="dropdown-menu" >' +
							'<a class="dropdown-item" href="javascript:void(0);" onclick="funDateModalOpen(\'' + row.uicheck_id + '\')">Dates: Update</a>' +
							'<a class="dropdown-item" href="javascript:void(0);" onclick="funDateUpdatesHistory(\'' + row.uicheck_id + '\')">Dates: View History</a>' +
							'<a class="dropdown-item" href="javascript:void(0);" onclick="funLockApply(\'developer\', \'' + row.uicheck_id + '\')">' + (row.lock_developer ? 'Show for Developer' : 'Hide for Developer') + '</a>' +
							'<a class="dropdown-item" href="javascript:void(0);" onclick="funLockApply(\'admin\', \'' + row.uicheck_id + '\')">' + (row.lock_admin ? 'Show for Admin' : 'Hide for Admin') + '</a>' +
							'<a class="dropdown-item" href="javascript:void(0);" onclick="showLanguage(\'' + row.uicheck_id + '\')">Translation</a>' +
							'<a class="dropdown-item" href="javascript:void(0);" onclick="showDevice(\'' + row.uicheck_id + '\')">5 Devices</a>' +
							'</div>' +
							'</div>';
					}
				},
			];
		} else {
			var columns = [{
					data: null,
					width: "5%",
					render: function(data, type, full, meta) {
						return '<input type="checkbox" id="checkAll" title="click here to select all" /><a href="javascript:;" data-id="' + full.uicheck_id + '" class="upload-document-btn"><img width="15px" src="/images/attach.png" alt="" style="cursor: default;"><a><a href="javascript:;" data-id="' + full.uicheck_id + '" class="list-document-btn"><img width="15px" src="/images/archive.png" alt="" style="cursor: default;"><a>';
					}
				},
				{
					data: 'uicheck_id',
					render: function(data, type, full, meta) {
						return data;
					}
				},
				{
					data: 'title',
					render: function(data, type, full, meta) {
						return data;
					}
				},
				{
					data: null,
					render: function(data, type, row, meta) {
						let sel = jQuery('#dropdownAllStoreSites').clone();
						sel.removeAttr('id');
						sel.addClass('globalSelect2');
						sel.find('option[value=' + row.websiteid + ']').attr('selected', 'selected');
						sel.attr('onchange', 'updateWebsiteId(this, "' + row.uicheck_id + '", "' + row.site_id + '", "' + row.id + '")');
						let html = jQuery("<div />").append(sel).html();
						return html;
					}
				},
				{
					data: 'issue',
					render: function(data, type, row, meta) {
						var html = '<div class="col-md-12 mb-1 p-0 d-flex pt-2 mt-1"><input style="margin-top: 0px;min-width:76px !important;" type="text" class="form-control " id="issue-' + row.uicheck_id + '" name="issue-' + row.uicheck_id + '" placeholder="Issues" value="' + row.issue + '"><div style="margin-top: 0px;" class="d-flex p-0"><button class="btn pr-0 btn-xs btn-image issue" data-category="' + row.id + '" data-id="' + row.uicheck_id + '" data-site_development_id="' + row.site_id + '"><img src="/images/filled-sent.png" /></button></div><button type="button" class="btn btn-xs show-issue-history" title="Show Issue History" data-id="' + row.uicheck_id + '"><i data-id="' + row.uicheck_id + '" class="fa fa-info-circle"></i></button></div>';
						return html;
					}
				},
				{
					data: null,
					render: function(data, type, row, meta) {
						var html = '<div class="col-md-12 mb-1 p-0 d-flex pl-4 pt-2 mt-1 msg" style="width: 100%;"><input type="text" style="width: 100%; float: left;" class="form-control quick-message-' + row.uicheck_id + ' input-sm" name="message" placeholder="Message" value=""><div class="d-flex p-0"><button style="float: left;padding: 0 0 0 5px" class="btn btn-sm btn-image uicheck-message" title="Send message" data-category="' + row.id + '" data-taskid="' + row.uicheck_id + '"  data-id="' + row.uicheck_id + '" data-site_development_id="' + row.site_id + '"><img src="/images/filled-sent.png" style="cursor: default;"></button></div><button type="button" class="btn btn-xs btn-image show-message-history load-body-class" data-object="uicheck" data-id="' + row.uicheck_id + '" title="Load messages" data-category="' + row.id + '" data-site_development_id="' + row.site_id + '" data-dismiss="modal"><img src="/images/chat.png" alt=""></button></div>';
						return html;
					}
				},
				{
					data: null,
					render: function(data, type, row, meta) {
						let sel = jQuery('#dropdownAllStatus').clone();
						sel.removeAttr('id');
						sel.addClass('globalSelect2');
						sel.find('option[value=' + row.dev_status_id + ']').attr('selected', 'selected');
						sel.attr('onchange', 'updateDeveloperStatus(this, "' + row.uicheck_id + '", "' + row.site_id + '", "' + row.id + '")');
						let html = jQuery("<div />").append(sel).html();
						html += '<button type="button" class="btn btn-xs show-dev-status-history" title="Show Developer Status History" data-id="' + row.uicheck_id + '"><i data-id="' + row.uicheck_id + '" class="fa fa-info-circle"></i></button>';
						return html;
					}
				},
				{
					data: null,
					render: function(data, type, row, meta) {
						let sel = jQuery('#dropdownAllTypes').clone();
						sel.removeAttr('id');
						sel.addClass('globalSelect2');
						sel.find('option[value=' + row.uicheck_type_id + ']').attr('selected', 'selected');
						sel.attr('onchange', 'updateTypeId(this, "' + row.uicheck_id + '")');
						let html = jQuery("<div />").append(sel).html();
						return html;
					}
				},
				{
					data: null,
					render: function(data, type, row, meta) {
						var html = '<button type="button" class="btn btn-xs show-admin-status-history" title="Show" data-id="' + row.uicheck_id + '"><i data-id="' + row.uicheck_id + '" class="fa fa-info-circle"></i></button>';
						return html;
					}
				},
				{
					data: null,
					render: function(data, type, row, meta) {
						return '<div class="dropdown dropleft">' +
							'<a class="btn btn-secondary btn-sm dropdown-toggle" href="javascript:void(0);" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</a>' +
							'<div class="dropdown-menu" >' +
							'<a class="dropdown-item" href="javascript:void(0);" onclick="funDateModalOpen(\'' + row.uicheck_id + '\')">Dates: Update</a>' +
							'<a class="dropdown-item" href="javascript:void(0);" onclick="funDateUpdatesHistory(\'' + row.uicheck_id + '\')">Dates: View History</a>' +
							'<a class="dropdown-item" href="javascript:void(0);" onclick="showLanguage(\'' + row.uicheck_id + '\')">Translation</a>' +
							'<a class="dropdown-item" href="javascript:void(0);" onclick="showDevice(\'' + row.uicheck_id + '\')">5 Devices</a>' +
							'</div>' +
							'</div>';
					}
				},
			];
		}
		oTable = $('#uicheck_table').DataTable({
			pageLength: 100,
			lengthMenu: [
				[-1, 10, 25, 50, 100],
				['All', 10, 25, 50, 100]
			],
			responsive: true,
			searchDelay: 500,
			processing: true,
			serverSide: true,
			autoWidth: true,
			scrollX: false,
			// sScrollX:true,
			searching: false,
			order: [
				[0, 'desc']
			],
			targets: 'no-sort',
			bSort: false,
			drawCallback: function(settings) {
				jQuery('.globalSelect2').select2();
				var responseToJson = settings.json;
				$(".page-heading").text("Ui Check (" + responseToJson.recordsTotal + ")");
			},
			ajax: {
				"url": "{{ route('uicheck') }}",
				data: function(d) {
					d.category_name = $('#store_webiste').val();
					d.sub_category_name = $('#store-categories').val();
					d.dev_status = $('#dev_status').val();
					d.admin_status = $('#admin_status').val();
					d.assign_to = $('#assign_to').val();
					d.order_by = $("#order_by").val();
					d.srch_lock_type = $("#srch_lock_type").val();
					d.id = $("#id").val();
					d.srch_flags = $("#srch_flags").val();
					// d.subjects = $('input[name=subjects]').val();					
				},
			},
			columnDefs: [{
				targets: [],
				orderable: false,
				searchable: false
			}],
			columns: columns,
			createdRow: function(row, data, dataIndex) {
				if (data.lock_developer == 1 && data.lock_admin == 1) {
					jQuery(row).addClass('bg-custom-gray');
				} else if (isAdmin == 1 && data.lock_admin == 1) {
					jQuery(row).addClass('bg-custom-gray');
				} else if (isAdmin == 0 && data.lock_developer == 1) {
					jQuery(row).addClass('bg-custom-gray');
				}
			}


		});

		$(document).on("click", ".custom-filter", function(e) {
			oTable.draw(false);
		});


	});
	// END Print Table Using datatable

	// For Document Upload ajax
	$(document).on("submit", "#upload-uicheck-documents", function(e) {
		e.preventDefault();
		var form = $(this);
		var postData = new FormData(form[0]);
		$.ajax({
			method: "post",
			url: `{{ route('uicheck.upload-document') }}`,
			data: postData,
			processData: false,
			contentType: false,
			dataType: "json",
			beforeSend: function() {
				$("#loading-image").show();
			},
			success: function(response) {
				if (response.code == 200) {
					toastr["success"]("Document Uploaded!", "Message")
					$("#upload-document-modal").modal("hide");
				} else {
					toastr["error"](response.error, "Message");
				}
				$("#loading-image").hide();
			}
		});
	});

	$(document).on("click", ".list-document-btn", function() {
		var id = $(this).data("id");
		$.ajax({
			method: "GET",
			url: "{{ action([\App\Http\Controllers\UicheckController::class, 'getDocument']) }}",
			data: {
				id: id
			},
			dataType: "json",
			success: function(response) {
				if (response.code == 200) {
					$("#blank-modal").find(".modal-title").html("Document List");
					$("#blank-modal").find(".modal-body").html(response.data);
					$("#blank-modal").modal("show");
				} else {
					toastr["error"](response.error, "Message");
				}
			}
		});
	});

	$(document).on("click", ".upload-document-btn", function() {
		var id = $(this).data("id");
		$("#upload-document-modal").find("#hidden-identifier").val(id);
		$("#upload-document-modal").modal("show");
	});

	$(document).on("click", ".issue", function(e) {
		e.preventDefault();
		var id = $(this).data("id");
		var issue = $("#issue-" + id).val();
		var site_development_id = $(this).data("site_development_id");
		var category = $(this).data("category");

		$.ajax({
			url: "{{route('uicheck.store')}}",
			type: 'POST',
			data: {
				id: id,
				issue: issue,
				site_development_id: site_development_id,
				category: category,
				"_token": "{{ csrf_token() }}",
			},
			beforeSend: function() {
				$(this).text('Loading...');
			},
			success: function(response) {
				if (response.code == 200) {
					toastr['success'](response.message);
					//$("#create-quick-task").modal("hide");
					//  location.reload();
				} else {
					toastr['error'](response.message);
				}
			}
		}).fail(function(response) {
			toastr['error'](response.responseJSON.message);
		});
	});



	$(document).on('click', '.send-message', function() {
		var thiss = $(this);
		var data = new FormData();
		var task_id = $(this).data('taskid');
		var message = $(this).closest('tr').find('.quick-message-field').val();
		// debugger;
		data.append("task_id", task_id);
		data.append("message", message);
		data.append("status", 1);
		data.append("object_id", "{{$log_user_id}}");

		if (message.length > 0) {
			if (!$(thiss).is(':disabled')) {
				$.ajax({
					url: '/whatsapp/sendMessage/uicheckMessage',
					type: 'POST',
					"dataType": 'json', // what to expect back from the PHP script, if anything
					"cache": false,
					"contentType": false,
					"processData": false,
					"data": data,
					beforeSend: function() {
						$(thiss).attr('disabled', true);
					}
				}).done(function(response) {
					thiss.closest('tr').find('.quick-message-field').val('');
					toastr["success"]("Message successfully send!", "Message")

					// $.post( "/whatsapp/approve/customer", { messageId: response.message.id })
					//   .done(function( data ) {
					//
					//   }).fail(function(response) {
					//     console.log(response);
					//     alert(response.responseJSON.message);
					//   });

					$(thiss).attr('disabled', false);
				}).fail(function(errObj) {
					$(thiss).attr('disabled', false);

					alert("Could not send message");
					console.log(errObj);
				});
			}
		} else {
			alert('Please enter a message first');
		}
	});

	$(document).on("click", ".uicheck-message", function(e) {
		e.preventDefault();
		var id = $(this).data("id");
		var message = $(".quick-message-" + id).val();
		var site_development_id = $(this).data("site_development_id");
		var category = $(this).data("category");

		$.ajax({
			url: "{{route('uicheck.set.message.history')}}",
			type: 'POST',
			data: {
				id: id,
				message: message,
				site_development_id: site_development_id,
				category: category,
				"_token": "{{ csrf_token() }}",
			},
			beforeSend: function() {
				$(this).text('Loading...');
			},
			success: function(response) {
				if (response.code == 200) {
					$(".quick-message-" + id).val("");
					toastr['success'](response.message);
					//$("#create-quick-task").modal("hide");
					//  location.reload();
				} else {
					toastr['error'](response.message);
				}
			}
		}).fail(function(response) {
			toastr['error'](response.responseJSON.message);
		});
	});

	$(".select2").select2();

	$("#checkAll").click(function() {
		$('input:checkbox').not(this).prop('checked', this.checked);
	});

	$(document).on("click", ".show-dev-status-history", function(e) {
		//debugger;
		e.preventDefault();
		var id = $(this).data("id");
		var developer_status = $(this).val();
		var site_development_id = $(this).data("site_development_id");
		var category = $(this).data("category");

		$.ajax({
			url: "{{route('uicheck.dev.status.history')}}",
			type: 'POST',
			data: {
				id: id,
				developer_status: developer_status,
				site_development_id: site_development_id,
				category: category,
				"_token": "{{ csrf_token() }}",
			},
			beforeSend: function() {
				$(this).text('Loading...');
			},
			success: function(response) {
				if (response.code == 200) {
					toastr['success'](response.message);
					$("#dev_status_tboday").html(response.html);
					$("#dev_status_model").modal("show");
					//location.reload();
				} else {
					toastr['error'](response.message);
				}
			}
		}).fail(function(response) {
			toastr['error'](response.responseJSON.message);
		});
	});

	$(document).on("click", ".show-admin-status-history", function(e) {
		e.preventDefault();
		var id = $(this).data("id");
		var developer_status = $(this).val();
		var site_development_id = $(this).data("site_development_id");
		var category = $(this).data("category");

		$.ajax({
			url: "{{route('uicheck.admin.status.history')}}",
			type: 'POST',
			data: {
				id: id,
				developer_status: developer_status,
				site_development_id: site_development_id,
				category: category,
				"_token": "{{ csrf_token() }}",
			},
			beforeSend: function() {
				$(this).text('Loading...');
			},
			success: function(response) {
				if (response.code == 200) {
					toastr['success'](response.message);
					$("#admin_status_tboday").html(response.html);
					$("#admin_status_model").modal("show");
					//location.reload();
				} else {
					toastr['error'](response.message);
				}
			}
		}).fail(function(response) {
			toastr['error'](response.responseJSON.message);
		});
	});

	$(document).on("click", ".show-issue-history", function(e) {
		e.preventDefault();
		var id = $(this).data("id");
		var developer_status = $(this).val();
		var site_development_id = $(this).data("site_development_id");
		var category = $(this).data("category");

		$.ajax({
			url: "{{route('uicheck.get.issue.history')}}",
			type: 'POST',
			data: {
				id: id,
				developer_status: developer_status,
				site_development_id: site_development_id,
				category: category,
				"_token": "{{ csrf_token() }}",
			},
			beforeSend: function() {
				$(this).text('Loading...');
			},
			success: function(response) {
				if (response.code == 200) {
					toastr['success'](response.message);
					$("#issue_tboday").html(response.html);
					$("#issue_model").modal("show");
					//location.reload();
				} else {
					toastr['error'](response.message);
				}
			}
		}).fail(function(response) {
			toastr['error'](response.responseJSON.message);
		});
	});

	$(document).on("click", ".duplicate-category", function(e) {
		e.preventDefault();
		var id = $(this).data("id");
		var developer_status = $(this).val();
		var site_development_id = $(this).data("site_development_id");
		var category = $(this).data("category");

		$.ajax({
			url: "{{route('uicheck.set.duplicate.category')}}",
			type: 'POST',
			data: {
				id: id,
				developer_status: developer_status,
				site_development_id: site_development_id,
				category: category,
				"_token": "{{ csrf_token() }}",
			},
			beforeSend: function() {
				$(this).text('Loading...');
			},
			success: function(response) {
				oTable.draw(false);
				console.log(oTable);
				if (response.code == 200) {
					toastr['success'](response.message);

				} else {
					toastr['error'](response.message);
				}
			}
		}).fail(function(response) {
			toastr['error'](response.responseJSON.message);
		});
	});


	$(document).on("click", ".show-message-history", function(e) {
		e.preventDefault();
		var id = $(this).data("id");
		var developer_status = $(this).val();
		var site_development_id = $(this).data("site_development_id");
		var category = $(this).data("category");

		$.ajax({
			url: "{{route('uicheck.get.message.history')}}",
			type: 'POST',
			data: {
				id: id,
				developer_status: developer_status,
				site_development_id: site_development_id,
				category: category,
				"_token": "{{ csrf_token() }}",
			},
			beforeSend: function() {
				$(this).text('Loading...');
			},
			success: function(response) {
				if (response.code == 200) {
					toastr['success'](response.message);
					$("#message_tboday").html(response.html);
					$("#message_model").modal("show");

					//location.reload();
				} else {
					toastr['error'](response.message);
				}
			}
		}).fail(function(response) {
			toastr['error'](response.responseJSON.message);
		});
	});


	$(document).on("click", ".show-assign-history", function(e) {
		e.preventDefault();
		var id = $(this).data("id");
		var developer_status = $(this).val();
		var site_development_id = $(this).data("site_development_id");
		var category = $(this).data("category");

		$.ajax({
			url: "{{route('uicheck.get.assign.history')}}",
			type: 'POST',
			data: {
				id: id,
				developer_status: developer_status,
				site_development_id: site_development_id,
				category: category,
				"_token": "{{ csrf_token() }}",
			},
			beforeSend: function() {
				$(this).text('Loading...');
			},
			success: function(response) {
				if (response.code == 200) {
					toastr['success'](response.message);
					$("#assignTo_tboday").html(response.html);
					$("#assignTo_model").modal("show");

					//location.reload();
				} else {
					toastr['error'](response.message);
				}
			}
		}).fail(function(response) {
			toastr['error'](response.responseJSON.message);
		});
	});


	$(document).on('click', '.expand-row-msg', function() {
		var name = $(this).data('name');
		var id = $(this).data('id');
		var full = '.expand-row-msg .show-short-' + name + '-' + id;
		var mini = '.expand-row-msg .show-full-' + name + '-' + id;
		$(full).toggleClass('hidden');
		$(mini).toggleClass('hidden');
	});



	var currUiCheckId = null;

	function funDateModalOpen(id) {
		currUiCheckId = id;
		siteLoader(true);
		jQuery.ajax({
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			url: urlUicheckGet,
			type: 'GET',
			data: {
				id: id
			}
		}).done(function(response) {
			jQuery('#modalDateUpdates').modal('show');
			if (jQuery('#modal_start_time').length && response.data.start_time) {
				jQuery('#modal_start_time').val(response.data.start_time);
			}
			if (jQuery('#modal_expected_completion_time').length && response.data.expected_completion_time) {
				jQuery('#modal_expected_completion_time').val(response.data.expected_completion_time);
			}
			if (jQuery('#modal_actual_completion_time').length && response.data.actual_completion_time) {
				jQuery('#modal_actual_completion_time').val(response.data.actual_completion_time);
			}
			siteLoader(false);
		}).fail(function(errObj) {
			if (errObj.responseJSON != undefined) {
				toastr['error'](errObj.responseJSON.message);
			} else if (errObj.message != undefined) {
				toastr['error'](errObj.message);
			} else if (errObj.message != undefined) {
				toastr['error']('Unknown error occured.');
			}
			siteLoader(false);
		});
		jQuery('#modalDateUpdates').modal('show');
		jQuery('#modalDateUpdates').attr('data-row_id', jQuery(ele).attr('data-task_id'));
	}

	function funDateUpdatesSubmit(url) {
		siteLoader(true);
		jQuery.ajax({
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			url: url,
			type: 'POST',
			data: {
				id: currUiCheckId,
				start_time: jQuery('#modal_start_time').length ? jQuery('#modal_start_time').val() : '',
				expected_completion_time: jQuery('#modal_expected_completion_time').length ? jQuery('#modal_expected_completion_time').val() : '',
				actual_completion_time: jQuery('#modal_actual_completion_time').length ? jQuery('#modal_actual_completion_time').val() : '',
			}
		}).done(function(response) {
			jQuery('#modalDateUpdates').modal('hide');
			currUiCheckId = null;
			siteLoader(false);
			siteSuccessAlert(response);
		}).fail(function(errObj) {
			siteErrorAlert(errObj);
			siteLoader(false);
		});
	}

	function funDateUpdatesHistory(id) {
		siteLoader(true);
		let mdl = jQuery('#modalHistoryDateUpdates');
		jQuery.ajax({
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			url: urlUicheckHistoryDates,
			type: 'GET',
			data: {
				id: id
			},
			beforeSend: function() {
				jQuery("#loading-image").show();
			}
		}).done(function(response) {
			siteLoader(false);
			mdl.find('tbody').html(response.html);
			mdl.modal("show");
		}).fail(function(errObj) {
			siteErrorAlert(errObj);
			siteLoader(false);
		});
	}

	jQuery(document).ready(function() {
		applyDateTimePicker(jQuery('.cls-start-due-date'));
	});

	$(document).on("click", ".language-flag-btn", function() {
		var id = $(this).data("id");
		let eleId = "#language-flag-btn-"+id;
		
		$.ajax({
			method: "post",
			url: "{{ route('uicheck.language.flag') }}",
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			data: {
				id: id
			},
			dataType: "json",
			success: function(response) {
				if (response.code == 200) {
					let newData = response.data;
					if(newData[0].language_flag == 1){
						$(eleId).css("color", "red");
					}else{
						$(eleId).css("color", "gray");
					}
				} else {
					toastr["error"](response.error, "Message");
				}
			}
		});
	});

	$(document).on("click", ".translation-flag-btn", function() {
		var id = $(this).data("id");
		let eleId = "#translation-flag-btn-"+id;
		
		$.ajax({
			method: "post",
			url: "{{ route('uicheck.translation.flag') }}",
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			data: {
				id: id
			},
			dataType: "json",
			success: function(response) {
				
				if (response.code == 200) {
					let newData = response.data;
					if(newData[0].translation_flag == 1){
						$(eleId).html('');
						$(eleId).html('<img src="/images/flagged.png" style="width: 16px; height: 16px; cursor: nwse-resize;">');
					}else{
						//debugger;
						$(eleId).html('');
						$(eleId).html('<img src="/images/unflagged.png" style="width: 16px; height: 16px; cursor: nwse-resize;">');
					}
				} else {
					toastr["error"](response.error, "Message");
				}
			}
		});
	});
	
	
</script>


@endsection