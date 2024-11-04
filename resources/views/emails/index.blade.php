@extends('layouts.app')
@section('large_content')
@section('styles')
<style type="text/css">
	#loading-image {
		position: fixed;
		top: 50%;
		left: 50%;
		margin: -50px 0px 0px -50px;
		z-index: 60;
	}

	.nav-item a {
		color: #555;
	}

	a.btn-image {
		padding: 2px 2px;
	}

	.text-nowrap {
		white-space: nowrap;
	}

	.search-rows .btn-image img {
		width: 12px !important;
	}

	.search-rows .make-remark {
		border: none;
		background: none
	}

	.table-responsive select.select {
		width: 110px !important;
	}
</style>
@endsection
<div id="myDiv">
	<img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
</div>
<div class="row">
	<div class="col-md-12 p-0">
		<h2 class="page-heading">Emails List ({{$totalEmail}})</h2>
	</div>
</div>
@if ($message = Session::get('success'))
	<div class="alert alert-success">
		<p>{{ $message }}</p>
	</div>
@endif


@if ($message = Session::get('danger'))
	<div class="alert alert-danger">
		<p>{{ $message }}</p>
	</div>
@endif


<div class="row">
	<div class="col-lg-12 margin-tb">
		<div class="pull-right mt-3">
			<button type="button" class="btn  custom-button" data-toggle="modal" data-target="#statusModel">Create
				Status</button>
			<button type="button" class="btn  custom-button" data-toggle="modal" data-target="#getCronEmailModal">Cron
				Email</button>
			<button type="button" class="btn  custom-button" data-toggle="modal"
				data-target="#createEmailCategorytModal">Create Category</button>
			<a href="{{ route('syncroniseEmail')}}" class="btn custom-button">Synchronise Emails</a>
		</div>
		<div class="pull-left mt-3" style="margin-bottom:10px;margin-right:5px;">
			<select class="form-control" name="" id="bluck_status" onchange="bulkAction(this,'status');">
				<option value="">Change Status</option>
				<?php
foreach ($email_status as $status) { ?>
				<option value="{{ strtolower($status->email_status) }}" {{ strtolower($status->email_status) == Request::get('status') ? "selected" : "" }}>
					<?php echo $status->email_status; ?>
				</option>
				<?php }
?>
			</select>
		</div>
		<div class="pull-left mt-3" style="margin-bottom:10px;margin-right:5px;">
			<button type="button" class="btn custom-button bulk-dlt" onclick="bulkAction(this,'delete');">Bulk
				Delete</button>
		</div>
		<div class="pull-left " style="margin-left:50px;">
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li
					class="nav-item <?php echo (request('type') == 'incoming' && request('seen') == '1') ? 'active' : '' ?>">
					<!-- Purpose : Add Turnary -  DEVTASK-18283 -->
					<a class="nav-link" id="read-tab" data-toggle="tab" href="#read" role="tab" aria-controls="read"
						aria-selected="true" onclick="load_data('incoming',1)">Read</a>
				</li>
				<li
					class="nav-item <?php echo ((request('type') == 'incoming' && request('seen') == '0') || empty(request('type'))) ? 'active' : '' ?>">
					<a class="nav-link" id="unread-tab" data-toggle="tab" href="#unread" role="tab"
						aria-controls="unread" aria-selected="false" onclick="load_data('incoming',0)">Unread</a>
				</li>
				<li
					class="nav-item <?php echo (request('type') == 'outgoing' && request('seen') == 'both') ? 'active' : '' ?>">
					<!-- Purpose : Add Turnary -  DEVTASK-18283 -->
					<a class="nav-link" id="sent-tab" data-toggle="tab" href="#sent" role="tab" aria-controls="sent"
						aria-selected="false" onclick="load_data('outgoing','both')">Sent</a>
				</li>
				<li
					class="nav-item <?php echo (request('type') == 'bin' && request('seen') == 'both') ? 'active' : '' ?>">
					<a class="nav-link" id="sent-tab" data-toggle="tab" href="#bin" role="tab" aria-controls="bin"
						aria-selected="false" onclick="load_data('bin','both')">Trash</a>
				</li>
				<li
					class="nav-item <?php echo (request('type') == 'draft' && request('seen') == 'both') ? 'active' : '' ?>">
					<a class="nav-link" id="sent-tab" data-toggle="tab" href="#bin" role="tab" aria-controls="bin"
						aria-selected="false" onclick="load_data('draft','both')">Draft</a>
				</li>
				<li
					class="nav-item <?php echo (request('type') == 'pre-send' && request('seen') == 'both') ? 'active' : '' ?>">
					<a class="nav-link" id="sent-tab" data-toggle="tab" href="#bin" role="tab" aria-controls="bin"
						aria-selected="false" onclick="load_data('pre-send','both')">Queue</a>
				</li>
			</ul>
		</div>
	</div>
	<div class="col-md-12">
		<div class="tab-content" id="myTabContent">
			<div class="tab-pane fade show active" id="read" role="tabpanel" aria-labelledby="read-tab">
			</div>
			<div class="tab-pane fade" id="unread" role="tabpanel" aria-labelledby="unread-tab">
			</div>
			<div class="tab-pane fade" id="sent" role="tabpanel" aria-labelledby="sent-tab">
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-12 mb-3 mt-4">
		<div class="pull-left">
			<form class="form-inline">
				<div class="form-group ">
					<input id="term" name="term" type="text" class="form-control" value="<?php if (Request::get('term')) {
					    echo Request::get('term');
					} ?>" placeholder="Search by Keyword">
				</div>
				<!--div class="form-group ml-3">
          <div class='input-group date' id='email-datetime'>
            <input type='text' class="form-control" id="date" name="date" value="{{ isset($date) ? $date : '' }}" />
            <span class="input-group-addon">
            <i class="fa fa-calendar" aria-hidden="true"></i>
            </span>
          </div>
          </div-->
				<div class="form-group px-2">
					<select class="form-control sender_select" name="sender" id="sender"
						style="width: 208px !important;" multiple data-email-sender-dropdown>
						<option value="">Select Sender</option>
					</select>
				</div>
				<div class="form-group px-2">
					<select class="form-control receiver_select" name="receiver" id="receiver"
						style="width: 208px !important;" multiple data-email-receiver-dropdown>
						<option value="">Select Receiver</option>
					</select>
				</div>
				<div class="form-group px-2">
					<select class="form-control mail_box_select" name="mail_box" id="mail_box"
						style="width: 208px !important;" multiple data-email-mailbox-dropdown>
						<option value="">Select Mailbox</option>
					</select>
				</div>
				<div class="form-group px-2">
					<select class="form-control email_status_select" name="status" id="email_status"
						style="width: 208px !important;" multiple>
						<option value="">Select Status</option>
						<?php
foreach ($email_status as $status) { ?>
						<option value="{{ strtolower($status->email_status) }}" {{ strtolower($status->email_status) == Request::get('status') ? 'selected' : '' }}>
							<?php echo $status->email_status; ?>
						</option>
						<?php }
?>
					</select>
				</div>
				<div class="form-group px-2">
					<select class="form-control select_category" name="category" id="category" multiple>
						<option value="">Select Category</option>
						<?php
foreach ($email_categories as $category) { ?>
						<option value="<?php echo $category->id; ?>" <?php if ($category->id == Request::get('category')) {
						    echo 'selected';
						} ?>><?php echo $category->category_name; ?></option>
						<?php }
?>
					</select>
				</div>
				<div class="form-group px-2">
					<select class="form-control model_type_select" name="email_model_type" id="email_model_type"
						multiple>
						@foreach($emailModelTypes as $m => $module)
							<option value="{{$m}}">{{$module}}</option>
						@endforeach
          </select>
				</div>
				<input type='hidden' class="form-control" id="type" name="type" value="" />
				<input type='hidden' class="form-control" id="seen" name="seen" value="" />
				<button type="submit" class="btn btn-image ml-3 search-btn"><i class="fa fa-filter"
						aria-hidden="true"></i></button>
			</form>
		</div>
	</div>
</div>


<button type="button" class="btn custom-button" data-toggle="modal" data-target="#newModelColor">Model Color</button>
<button class="btn custom-button my-3" style="color:white;" data-toggle="modal"
	data-target="#emailcolumnvisibilityList"> Column Visiblity</button>

@php 
		$columns_array = [
		['id' => 'created_at', 'name' => 'Date'],
		['id' => 'from', 'name' => 'Sender'],
		['id' => 'to', 'name' => 'Receiver'],
		['id' => 'model_type', 'name' => 'Model Type'],
		['id' => 'type', 'name' => 'Mail Type'],
		['id' => 'subject', 'name' => 'Subject'],
		['id' => 'message', 'name' => 'Body'],
		['id' => 'status', 'name' => 'Status'],
		['id' => 'is_draft', 'name' => 'Draft'],
		['id' => 'error_message', 'name' => 'Error Message'],
		['id' => 'email_category_id', 'name' => 'Category']
	];
@endphp

<a href="{{ url('email/category/mappings') }}" class="btn custom-button float-right mb-2">View Category Mappings</a>
<div class="table-responsive mt-3" style="margin-top:20px;">
	<table class="table table-bordered" style="border: 1px solid #ddd;" id="email-table">
		<thead>
			<tr>
				<th width="1%">#</th>
				<th width="6%"
					class="{{ (!empty($dynamicColumnsToShowb) && in_array('Date', $dynamicColumnsToShowb)) ? 'd-none' : ''}}">
					Date
				</th>
				<th width="4%"
					class="{{ (!empty($dynamicColumnsToShowb) && in_array('Sender', $dynamicColumnsToShowb)) ? 'd-none' : ''}}">
					Sender</th>
				<th width="4%"
					class="{{ (!empty($dynamicColumnsToShowb) && in_array('Receiver', $dynamicColumnsToShowb)) ? 'd-none' : ''}}">
					Receiver</th>
				<th width="4%"
					class="{{ (!empty($dynamicColumnsToShowb) && in_array('Model Type', $dynamicColumnsToShowb)) ? 'd-none' : ''}}">
					Model Type</th>
				<th width="3%"
					class="{{ (!empty($dynamicColumnsToShowb) && in_array('Mail Type', $dynamicColumnsToShowb)) ? 'd-none' : ''}}">
					Mail Type</th>
				<th width="5%"
					class="{{ (!empty($dynamicColumnsToShowb) && in_array('Subject', $dynamicColumnsToShowb)) ? 'd-none' : ''}}">
					Subject</th>
				<th width="5%"
					class="{{ (!empty($dynamicColumnsToShowb) && in_array('Body', $dynamicColumnsToShowb)) ? 'd-none' : ''}}">
					Body
				</th>

				<th width="6%"
					class="{{ (!empty($dynamicColumnsToShowb) && in_array('Status', $dynamicColumnsToShowb)) ? 'd-none' : ''}}">
					Status</th>
				<th width="1%"
					class="{{ (!empty($dynamicColumnsToShowb) && in_array('Draft', $dynamicColumnsToShowb)) ? 'd-none' : ''}}">
					Draft</th>
				<th width="8%"
					class="{{ (!empty($dynamicColumnsToShowb) && in_array('Error Message', $dynamicColumnsToShowb)) ? 'd-none' : ''}}">
					Error Message</th>
				<th width="6%"
					class="{{ (!empty($dynamicColumnsToShowb) && in_array('Category', $dynamicColumnsToShowb)) ? 'd-none' : ''}}">
					Category</th>
				<th width="2%">Action</th>
			</tr>
		</thead>
		<tbody>

		</tbody>
	</table>
	<div class="pagination-custom">
	</div>
</div>

@include('emails.partials.email-column-visibility-modal', ['columns_array' => $columns_array])

<div id="replyMail" class="modal fade" role="dialog">
	<div class="modal-dialog  modal-lg ">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Email reply</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div id="reply-mail-content">
			</div>
		</div>
	</div>
</div>
<div id="replyAllMail" class="modal fade" role="dialog">
	<div class="modal-dialog  modal-lg ">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Email reply all</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div id="reply-all-mail-content">
			</div>
		</div>
	</div>
</div>
<div id="forwardMail" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Email forward</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div id="forward-mail-content">
			</div>
		</div>
	</div>
</div>
<div id="viewMail" class="modal fade" role="dialog">
	<div class="modal-dialog  modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">View Email</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p><strong>Subject : </strong> <span id="emailSubject"></span> </p>
				<p><strong>Message : </strong> <span id="emailMsg"></span> </p>
				<iframe src="" id="eFrame" scrolling="no" style="width:100%;" frameborder="0"
					onload="autoIframe('eFrame');"></iframe>
			</div>
		</div>
	</div>
</div>
<div id="viewMore" class="modal fade" role="dialog">
	<div class="modal-dialog  modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">View More</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<p><span id="more-content"></span> </p>
			</div>
		</div>
	</div>
</div>
<div id="createEmailCategorytModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Create Email Category</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form action="{{ url('email/category') }}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="form-group">
						<input type="text" name="category_name" id="category_name" value="{{ old('category_name') }}"
							class="form-control" placeholder="Category Name">
					</div>

					<div class="form-group">
						<select class="form-control" id="category_priority" name="category_priority">
							<option value="HIGH">High</option>
							<option value="MEDIUM">Medium</option>
							<option value="LOW">Low</option>
							<option value="UNDEFINED">Undefined</option>
						</select>
					</div>

					<div class="form-group">
						<select class="form-control" id="category_type" name="category_type">
							<option value="read">Read</option>
							<option value="unread">Unread</option>
							<option value="sent">Sent</option>
							<option value="trash">Trash</option>
							<option value="draft">Draft</option>
							<option value="queue">Queue</option>
						</select>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-secondary">Create</button>
					</div>
			</form>
		</div>
	</div>
</div>
</div>
<div id="getCronEmailModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Cron Email</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="table-responsive mt-3">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Signature</th>
								<th>Status</th>
								<th>Start Time</th>
								<th>End Time</th>
								<th>Updated</th>
							</tr>
						</thead>
						<tbody>
							@if(empty($reports))
								<tr>
									<td colspan="5">
										No Result Found
									</td>
								</tr>
							@else


														@foreach ($reports as $report)
															  <tr>
																						<td>
																							{{ $report->signature }}
																						</td>
																						<td>
																							{{ !empty($report->last_error) ? 'Failed' : 'Success' }}
																						</td>
																						<td>
																							{{ $report->start_time }}
																						</td>
																						<td>{{ $report->end_time }}</td>
																						<td>{{ $report->updated_at->format('Y-m-d H:i:s')  }}</td>
																					</tr>
														@endforeach
									@if ($reports->lastPage() > 1)
										<ul class="pagination cronEmailPagination">
											@for ($i = 1; $i <= $reports->lastPage(); $i++)
												<li class="cronEmailActive{{ $i }} {{ ($i == 1) ? ' active' : '' }}">
													<a class="cronEmailPage" data-id="{{ $i }}" href="#">{{ $i }}</a>
												</li>
											@endfor
										</ul>
									@endif


							@endif


						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<div id="statusModel" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Create Email Status</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form action="{{ url('email/status') }}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="form-group">
						<input type="text" name="email_status" value="{{ old('email_status') }}" class="form-control"
							placeholder="Status" id="email_status">
					</div>

					<div class="form-group">
						<select class="form-control" id="status_type" name="status_type">
							<option value="read">Read</option>
							<option value="unread">Unread</option>
							<option value="sent">Sent</option>
							<option value="trash">Trash</option>
							<option value="draft">Draft</option>
							<option value="queue">Queue</option>
						</select>
					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-secondary">Create</button>
					</div>
			</form>
		</div>
	</div>
</div>
</div>
<div id="UpdateMail" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Email List</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form action="{{ url('email/update_email') }}" method="POST">
				@csrf
				<div class="modal-body">
					<div class="form-group">
						<input type="hidden" name="email_id" id="email_id">
						<select class="form-control" name="status" id="email_status">
							<option value="">Select Status</option>
							<?php
foreach ($email_status as $status) { ?>
							<option value="<?php echo $status->id; ?>"><?php echo $status->email_status; ?></option>
							<?php }
?>
						</select>
					</div>
					<div class="form-group">
						<select class="form-control" name="category" id="email_category">
							<option value="">Select Category</option>
							<?php
foreach ($email_categories as $category) { ?>
							<option value="<?php echo $category->id; ?>"><?php echo $category->category_name; ?>
							</option>
							<?php }
?>
						</select>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-secondary">Store</button>
					</div>
			</form>
		</div>
	</div>
</div>
</div>
{{-- Showing file status models --}}
<div id="showFilesStatusModel" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Files status</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label for="Status">Files status :</label>
					<div id="filesStatus" class="form-group"> </div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="labelingModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Assign Platform</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form action="{{ action([\App\Http\Controllers\EmailController::class, 'platformUpdate']) }}" method="POST"
				class="form-group labeling-form">
				@csrf
				<input type="hidden" name="id" value="">
				<div class="modal-body">
					<div class="form-group">
						<div class="col-md-12">
							<label for="Status" class="form-control">Platform</label>
						</div>
						<div class="col-md-12 mb-5">
							<select name="platform" class="form-control select2">
								<option value="">Select Platforms</option>
								@foreach($digita_platfirms as $digita_platfirm)
									  <option value="{{ $digita_platfirm->id }}"> {{ $digita_platfirm->platform }} -->
																{{ $digita_platfirm->sub_platform }}
															</option>
								@endforeach
              </select>
						</div>
					</div>
					<!-- <div class="form-group">
            <label for="Status">Sub Platform</label>
            <select name="sub-platform" class="form-control">

            </select>
            </div> -->
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-secondary">Submit</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<div id="excelImporter" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Excel Importer</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<select name="supplier" class="form-control" id="supplier_excel_import">
					<option value="">Select a supplier</option>
					<option value="birba_excel">Birba</option>
					<option value="brunarosso_excel">Bruna Rosso</option>
					<option value="colognese_excel">Colognese (Dior)</option>
					<option value="cologneseSecond_excel">Colognese (Balenciaga, Chloe, Valentino)</option>
					<option value="cologneseThird_excel">Colognese (Saint Laurent)</option>
					<option value="cologneseFourth_excel">Colognese (SS20 Shoes)</option>
					<option value="distributionet_excel">Distributionet</option>
					<option value="gru_excel">Gruppo Pritelli</option>
					<option value="maxim_gucci_excel">Maxim Gucci</option>
					<option value="ines_excel">Ines</option>
					<option value="le-lunetier_excel">Le Lunetier</option>
					<option value="lidia_excel">Lidia</option>
					<option value="lidiafirst_excel">Lidia (Salvatore)</option>
					<option value="modes_excel">Modes</option>
					<option value="mv1_excel">MV1</option>
					<option value="master">Master</option>
					<option value="tory_excel">Tory Outlet</option>
					<option value="tessabit_excel">Tessabit</option>
					<option value="valenti_excel">Valenti</option>
					<option value="valentisecond_excel">Valenti New Format</option>
					<option value="dna_excel">DNA Excel</option>
				</select>
				<input type="hidden" id="excel_import_email_id">
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-secondary" onclick="importExcel()">Store</button>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="emailEvents" class="modal fade" role="dialog">
	<div class="modal-dialog  modal-lg ">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Email reply</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="table-responsive mt-3">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Date</th>
								<th>Event</th>
							</tr>
						</thead>
						<tbody id="emailEventData">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="emailLogs" class="modal fade" role="dialog">
	<div class="modal-dialog  modal-lg ">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Email Logs</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="table-responsive mt-3">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Date</th>
								<th>Message</th>
								<th>Details</th>
							</tr>
						</thead>
						<tbody id="emailLogsData">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="addReceiverReplyModal" class="modal fade" role="dialog">
	<div class="modal-dialog  modal-lg ">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Add Receiver Email</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form action="#" method="post" id="addReceiverReplyForm">
					@csrf

					<div class="table-responsive">
						<table class="table">
							<tbody id="replyReceiverEmailBody">
								<tr id="reply_receiver_email_row_id_1">
									<td><input type="email" id="reply_receiver_email_id_1"
											name="reply_receiver_emails[]" class="form-control" required></td>
									<td><button type="button" class="btn btn-primary"
											data-reply-add-receiver-email-btn><i class="fa fa-plus"></i></button></td>
								</tr>
							</tbody>
						</table>

						<button class="btn btn-primary" type="submit">Add</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<div id="addReceiverReplyAllModal" class="modal fade" role="dialog">
	<div class="modal-dialog  modal-lg ">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Add Receiver Email</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form action="#" method="post" id="addReceiverReplyAllForm">
					@csrf

					<div class="table-responsive">
						<table class="table">
							<tbody id="replyAllReceiverEmailBody">
								<tr id="reply_all_receiver_email_row_id_1">
									<td><input type="email" id="reply_all_receiver_email_id_1"
											name="reply_all_receiver_emails[]" class="form-control" required></td>
									<td><button type="button" class="btn btn-primary"
											data-reply-all-add-receiver-email-btn><i class="fa fa-plus"></i></button>
									</td>
								</tr>
							</tbody>
						</table>

						<button class="btn btn-primary" type="submit">Add</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- #DEVTASK - 23369 --}}
<div id="assignModel" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Assign Model To Email</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<select name="model_name" class="form-control" id="model_name">
					<option value="">Select a model</option>
					<option value="customer">Customer</option>
					<option value="vendor">Vendor</option>
					<option value="supplier">Supplier</option>
					<option value="user">User</option>
				</select>
				<input type="hidden" id="assign_model_email_id" value="">
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-secondary" onclick="assignModel()">Assign</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="newModelColor" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Model Color</h4>

				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form action="{{ route('updateModelColor') }}" method="POST">
				<?php echo csrf_field(); ?>

				<div class="form-group col-md-12">
					<table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
						<th>
						<td class="text-center"><b>Model Name</b></td>
						<td class="text-center"><b>Color Code</b></td>
						<td class="text-center"><b>Color</b></td>
						</th>

						<tbody class="tbody">
							<?php
foreach ($modelColors as $bugstatus) { ?>
							<tr>
								<td>&nbsp;&nbsp;&nbsp;<?php echo $bugstatus->model_name; ?></td>
								<td class="text-center"><?php echo $bugstatus->color_code; ?></td>
								<td class="text-center"><input type="color"
										name="color_name[<?php echo $bugstatus->id; ?>]" class="form-control"
										data-id="<?php echo $bugstatus->id; ?>"
										id="color_name_<?php echo $bugstatus->id; ?>"
										value="<?php echo $bugstatus->color_code; ?>" style="height:30px;padding:0px;">
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal" @if($modelColors->isEmpty())
					disabled @endif>Close</button>
					<button type="submit" class="btn btn-secondary">Save changes</button>
				</div>
			</form>
		</div>

	</div>
</div>
{{-- END HERE --}}

{{-- #DEVTASK - 23409 --}}

<div id="categoryLog" class="modal fade" role="dialog">
	<div class="modal-dialog  modal-lg ">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Email Category Change Logs</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body emailCategoryLogs">

			</div>
		</div>
	</div>
</div>

<div id="statusLog" class="modal fade" role="dialog">
	<div class="modal-dialog  modal-lg ">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Email Status Change Logs</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body emailStatusLogs">

			</div>
		</div>
	</div>
</div>

{{-- END Here --}}

@include('partials.modals.remarks')
@endsection
@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/simulator.js') }} "></script>

<link href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>

<script type="text/javascript">
	var csrftoken = "{{ csrf_token() }}";
	$(window).scroll(function () {
		if ($(window).scrollTop() == $(document).height() - $(window).height()) {
			console.log('ajax call or some other logic to show data here');
			$(".pagination-custom").find(".pagination").find(".active").next().find("a").click();
		}
	});
	$('.model_type_select').select2({
		placeholder: "Select Model Type",
	});
	$('.sender_select').select2({
		placeholder: "Select sender",
	});
	$('.receiver_select').select2({
		placeholder: "Select Receiver",
	});
	$('.mail_box_select').select2({
		placeholder: "Select Mailbox",
	});
	$('.email_status_select').select2({
		placeholder: "Select Status",
	});
	$('.select_category').select2({
		placeholder: "Select Category",
	});

	$('.select2-search__field').css('width', '100%')

	$(document).on('click', '[data-reply-add-receiver-btn]', function () {
		$('.reply-row-receiver-items').remove();

		$('#addReceiverReplyModal').modal('show');
	});

	var replyReceiverCount = 1;

	$(document).on('click', '[data-reply-add-receiver-email-btn]', function () {
		replyReceiverCount += 1;
		var receiverHtml = '<tr class="reply-row-receiver-items" id="reply_receiver_email_row_id_' + replyReceiverCount + '">';
		receiverHtml += '<td><input type="email" id="reply_receiver_email_id_' + replyReceiverCount + '" name="reply_receiver_emails[]" class="form-control" required></td>';
		receiverHtml += '<td><button type="button" class="btn btn-danger" data-reply-remove-receiver-email-btn row-id="' + replyReceiverCount + '"><i class="fa fa-close"></i></button></td>';
		receiverHtml += '</tr>';

		$('#replyReceiverEmailBody').append(receiverHtml);
	});

	$(document).on('click', '[data-reply-remove-receiver-email-btn]', function () {
		var rowId = Number($(this).attr('row-id'));

		$('#reply_receiver_email_row_id_' + rowId).remove();
	});

	//option A
	$("#addReceiverReplyForm").submit(function (e) {
		e.preventDefault();

		var receiverEmails = [];

		$("input[name='reply_receiver_emails[]']").each(function () {
			var receiverEmail = $(this).val();

			receiverEmails.push(receiverEmail);
		});

		var currentReceiverEmails = $('#reply_receiver_email').val();
		var currentReceiverEmailsArr = currentReceiverEmails.split('');

		if (currentReceiverEmailsArr[0] == '[') {
			currentReceiverEmails = currentReceiverEmails.replace(/'/g, '"');
			currentReceiverEmails = JSON.parse(currentReceiverEmails);
		} else {
			currentReceiverEmails = currentReceiverEmails.split(',');
		}

		if (receiverEmails.length > 0) {
			$.each(receiverEmails, function (k, v) {
				currentReceiverEmails.push(v);
			});
		}

		$('#reply_receiver_email').val("[" + currentReceiverEmails.map(value => `"${value}"`).join(',') + "]");

		$('#addReceiverReplyModal').modal('hide');
	});

	//////////////////////////////////////////////////////////////////////////////

	$(document).on('click', '[data-reply-all-add-receiver-btn]', function () {
		$('.reply-all-row-receiver-items').remove();

		$('#addReceiverReplyAllModal').modal('show');
	});

	var replyAllReceiverCount = 1;

	$(document).on('click', '[data-reply-all-add-receiver-email-btn]', function () {
		replyAllReceiverCount += 1;
		var receiverHtml = '<tr class="reply-all-row-receiver-items" id="reply_all_receiver_email_row_id_' + replyAllReceiverCount + '">';
		receiverHtml += '<td><input type="email" id="reply_all_receiver_email_id_' + replyAllReceiverCount + '" name="reply_all_receiver_emails[]" class="form-control" required></td>';
		receiverHtml += '<td><button type="button" class="btn btn-danger" data-reply-all-remove-receiver-email-btn row-id="' + replyAllReceiverCount + '"><i class="fa fa-close"></i></button></td>';
		receiverHtml += '</tr>';

		$('#replyAllReceiverEmailBody').append(receiverHtml);
	});

	$(document).on('click', '[data-reply-all-remove-receiver-email-btn]', function () {
		var rowId = Number($(this).attr('row-id'));

		$('#reply_all_receiver_email_row_id_' + rowId).remove();
	});

	//option A
	$("#addReceiverReplyAllForm").submit(function (e) {
		e.preventDefault();

		var receiverEmails = [];

		$("input[name='reply_all_receiver_emails[]']").each(function () {
			var receiverEmail = $(this).val();

			receiverEmails.push(receiverEmail);
		});

		var currentReceiverEmails = $('#reply_all_receiver_email').val();
		var currentReceiverEmailsArr = currentReceiverEmails.split('');

		if (currentReceiverEmailsArr[0] == '[') {
			currentReceiverEmails = currentReceiverEmails.replace(/'/g, '"');
			currentReceiverEmails = JSON.parse(currentReceiverEmails);
		} else {
			currentReceiverEmails = currentReceiverEmails.split(',');
		}

		if (receiverEmails.length > 0) {
			$.each(receiverEmails, function (k, v) {
				currentReceiverEmails.push(v);
			});
		}

		$('#reply_all_receiver_email').val("[" + currentReceiverEmails.map(value => `"${value}"`).join(',') + "]");

		$('#addReceiverReplyAllModal').modal('hide');
	});

	/////////////////////////////////////////////////////////////////////////////

	$(document).on('click', '.expand-row', function () {
		var selection = window.getSelection();
		if (selection.toString().length === 0) {
			$(this).find('.td-mini-container').toggleClass('hidden');
			$(this).find('.td-full-container').toggleClass('hidden');
		}
	});

	$(".pagination-custom").on("click", ".page-link", function (e) {
		e.preventDefault();

		var activePage = $(this).closest(".pagination").find(".active").text();
		var clickedPage = $(this).text();
		if (clickedPage == "‹" || clickedPage < activePage) {
			$('html, body').animate({ scrollTop: ($(window).scrollTop() - 50) + "px" }, 200);
			get_data_pagination($(this).attr("href"));
		} else {
			get_data_pagination($(this).attr("href"));
		}

	});

	function get_data_pagination(url) {
		$.ajax({
			url: url,
			type: 'get',
			beforeSend: function () {
				$("#loading-image").show();
			},
		}).done(function (response) {
			$("#loading-image").hide();
			$("#email-table tbody").empty().append(response.tbody);
			$(".pagination-custom").html(response.links);

		}).fail(function (errObj) {
			$("#loading-image").hide();
		});
	}

	function fetchEvents(originId) {
		if (originId == '') {
			$('#emailEventData').html('<tr><td>No Data Found.</td></tr>');
			$('#emailEvents').modal('show');
			return;
		} else {
			$.get(window.location.origin + "/email/events/" + originId, function (data, status) {
				$('#emailEventData').html(data);
				$('#emailEvents').modal('show');
			});
		}
	}

	function fetchEmailLog(email_id) {
		if (email_id == '') {
			$('#emailLogsData').html('<tr><td>No Data Found.</td></tr>');
			$('#emailLogs').modal('show');
			return;
		} else {
			$.get(window.location.origin + "/email/emaillog/" + email_id, function (data, status) {
				$('#emailLogsData').html(data);
				$('#emailLogs').modal('show');
			});
		}
	}

	//$("#unread-tab").trigger("click");

	var searchSuggestions = {!! json_encode(array_values($search_suggestions), true) !!};
	var _parentElement = $("#forwardMail")

	// Limit dropdown to 10 emails and use appenTo to view dropdown on top of modal window.
	var options = {
		source: function (request, response) {
			var results = $.ui.autocomplete.filter(searchSuggestions, request.term);
			response(results.slice(0, 10));
		},
		appendTo: _parentElement
	};

	// Following is required to load autocomplete on dynamic DOM
	var selector = '#forward-email';
	$(document).on('keydown.autocomplete', selector, function () {
		$(this).autocomplete(options);
	});

	$(document).ready(function () {
		$('#email-datetime').datetimepicker({
			format: 'YYYY-MM-DD'
		});
		$("select[name='platform']").select2();
	});


	$(document).on('click', '.search-btn', function (e) {
		e.preventDefault();
		table.draw();
	});

	function get_data() {
		var term = $("#term").val();
		var date = $("#date").val();
		var type = $("#type").val();
		var seen = $("#seen").val();
		var sender_name = $("#sender").val();
		var sender = sender_name.toString();
		var receiver_name = $("#receiver").val();
		var receiver = receiver_name.toString();
		var status_name = $("#email_status").val();
		var status = status_name.toString();
		var category_name = $("#category").val();
		var category = category_name.toString();
		var mail_box_name = $("#mail_box").val();
		var mail_box = mail_box_name.toString();
		var email_model_type = $('#email_model_type').val().toString();

		$.ajax({
			url: 'email',
			type: 'get',
			data: {
				term: term,
				date: date,
				type: type,
				seen: seen,
				sender: sender,
				receiver: receiver,
				status: status,
				category: category,
				mail_box: mail_box,
				email_model_type: email_model_type
			},
			beforeSend: function () {
				$("#loading-image").show();
			},
		}).done(function (response) {
			$("#loading-image").hide();
			$("#email-table tbody").empty().html(response.tbody);
			$(".pagination-custom").html(response.links);

		}).fail(function (errObj) {
			$("#loading-image").hide();
		});
	}

	$(document).ready(function () {
		getEmailFilterOptions();
	});

	function getEmailFilterOptions() {
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: 'email/filter-options',
			type: 'post',
			// beforeSend: function () {
			//   $("#loading-image").show();
			// },
		}).done(function (response) {
			var sender = '{{ Request::get('sender') }}';
			var senderDropdownHtml = '';

			$.each(response.senderDropdown, function (k, v) {
				senderDropdownHtml += "<option value='" + v.from + "' " + (sender == v.from ? 'selected' : '') + ">" + v.from + "</option>";
			});
			$('[data-email-sender-dropdown]').append(senderDropdownHtml);

			var receiverName = '{{ $receiver }}';
			var from = '{{ $from }}';
			var receiver = '{{ Request::get('receiver') }}';
			var receiverDropdownHtml = '';

			$.each(response.receiverDropdown, function (k, v) {
				if (receiverName.length != 0 && from == 'order_data') {
					receiverDropdownHtml += "<option value='" + v.to + "' " + (receiverName == v.to ? 'selected' : '') + ">" + v.to + "</option>";
				} else {
					receiverDropdownHtml += "<option value='" + v.to + "' " + (receiver == v.to ? 'selected' : '') + ">" + v.to + "</option>";
				}
			});
			$('[data-email-receiver-dropdown]').append(receiverDropdownHtml);

			var mailBox = '{{ Request::get('mail_box')}}';
			var mailboxDropdownHtml = '';

			$.each(response.mailboxDropdown, function (k, v) {
				mailboxDropdownHtml += "<option value='" + k + "' " + (mailBox == k ? 'selected' : '') + ">" + v + "</option>";
			});
			$('[data-email-mailbox-dropdown]').append(mailboxDropdownHtml);

			$('[data-email-sender-dropdown]').select2('destroy').select2({
				placeholder: "Select sender"
			});

			$('[data-email-receiver-dropdown]').select2('destroy').select2({
				placeholder: "Select Receiver"
			});

			$('[data-email-mailbox-dropdown]').select2('destroy').select2({
				placeholder: "Select Mailbox"
			});

			$('.select2-search__field').css('width', '100%')
			// toastr['success'](response.message);
			// $("#loading-image").hide();
		}).fail(function (errObj) {
			// $("#loading-image").hide();
		});
	}


	$(document).on('click', '.resend-email-btn', function (e) {
		e.preventDefault();
		var $this = $(this);
		var type = $(this).data('type');
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/email/resendMail/' + $this.data("id"),
			type: 'post',
			data: {
				type: type
			},
			beforeSend: function () {
				$("#loading-image").show();
			},
		}).done(function (response) {
			toastr['success'](response.message);
			$("#loading-image").hide();
		}).fail(function (errObj) {
			$("#loading-image").hide();
		});
	});

	$(document).on('click', '.reply-email-btn', function (e) {
		e.preventDefault();
		var $this = $(this);
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/email/replyMail/' + $this.data("id"),
			type: 'get',
			beforeSend: function () {
				$("#loading-image").show();
			},
		}).done(function (response) {
			$("#loading-image").hide();
			// toastr['success'](response.message);
			$("#reply-mail-content").html(response);
		}).fail(function (errObj) {
			$("#loading-image").hide();
		});
	});

	$(document).on('click', '.reply-all-email-btn', function (e) {
		e.preventDefault();
		var $this = $(this);
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/email/replyAllMail/' + $this.data("id"),
			type: 'get',
			beforeSend: function () {
				$("#loading-image").show();
			},
		}).done(function (response) {
			$("#loading-image").hide();
			// toastr['success'](response.message);
			$("#reply-all-mail-content").html(response);
		}).fail(function (errObj) {
			$("#loading-image").hide();
		});
	});



	$(document).on('click', '.forward-email-btn', function (e) {
		e.preventDefault();
		var $this = $(this);
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/email/forwardMail/' + $this.data("id"),
			type: 'get',
			// beforeSend: function () {
			//     $("#loading-image").show();
			// },
		}).done(function (response) {
			$("#forward-mail-content").html(response);
		}).fail(function (errObj) {
			// $("#loading-image").hide();
		});
	});

	$(document).on('click', '.submit-reply', function (e) {
		e.preventDefault();
		var receiverEmail = $("#reply_receiver_email").val();
		var replySubject = $("#reply_subject").val();
		var message = $("#reply-message-1").val();
		var reply_email_id = $("#reply_email_id_1").val();
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/email/replyMail',
			type: 'post',
			data: {
				'receiver_email': receiverEmail,
				'subject': replySubject,
				'message': message,
				'reply_email_id': reply_email_id
			},
			beforeSend: function () {
				$("#loading-image").show();
			},
		}).done(function (response) {
			$("#replyMail").modal('hide');
			$("#loading-image").hide();
			toastr['success'](response.message);
		}).fail(function (errObj) {
			$("#replyMail").modal('hide');
			$("#loading-image").hide();
			toastr['error'](response.errors[0]);

		});
	});

	$(document).on('click', '.submit-reply-all', function (e) {
		e.preventDefault();
		var receiverEmail = $("#reply_all_receiver_email").val();
		var replySubject = $("#reply_all_subject").val();
		var message = $("#reply-all-message").val();
		var reply_email_id = $("#reply_all_email_id").val();

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/email/replyAllMails',
			type: 'post',
			data: {
				'receiver_email': receiverEmail,
				'subject': replySubject,
				'message': message,
				'reply_email_id': reply_email_id
			},
			beforeSend: function () {
				$("#loading-image").show();
			},
		}).done(function (response) {
			$("#replyAllMail").modal('hide');
			$("#loading-image").hide();
			toastr['success'](response.message);
		}).fail(function (errObj) {
			$("#replyAllMail").modal('hide');
			$("#loading-image").hide();
			toastr['error'](response.errors[0]);
		});
	});

	$(document).on('click', '.submit-forward', function (e) {
		e.preventDefault();
		email = $("#forward-email").val();
		forward_email_id = $("#forward_email_id").val();
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/email/forwardMail',
			type: 'post',
			data: {
				email: email,
				forward_email_id: forward_email_id
			},
			beforeSend: function () {
				$("#loading-image").show();
			},
		}).done(function (response) {
			$("#loading-image").hide();
			if (response.success) {
				toastr['success'](response.message);
				$("#forwardMail").modal('hide');
			} else {
				$.each(response.errors, function (key, value) {
					$("#forwardMail input[name='" + key + "']").addClass('is-invalid').after('<div class="invalid-feedback">' + value[0] + '</div>');
				});
			}

		}).fail(function (errObj) {
			$("#forwardMail").modal('hide');
			$("#loading-image").hide();
			toastr['error'](response.errors[0]);


		});
	});

	$(document).on('click', '.mailupdate', function (e) {

		$("#UpdateMail #email_category").val("").trigger('change');
		$("#UpdateMail #email_status").val("").trigger('change');

		var email_id = $(this).data('id');
		var status = $(this).data('status');
		var category = $(this).data('category');
		if (category) {
			$("#UpdateMail #email_category").val(category).trigger('change');
		}
		if (status) {
			$("#UpdateMail #email_status").val(status).trigger('change');
		}

		$('#UpdateMail #email_id').val(email_id);

	});


	$(document).on('click', '.make-remark', function (e) {
		e.preventDefault();

		var email_id = $(this).data('id');


		$('#add-remark input[name="id"]').val(email_id);


		$.ajax({
			type: 'GET',
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			url: '{{ route('email.getremark') }}',
			data: {
				email_id: email_id
			},
			beforeSend: function () {
				$("#loading-image").show();
			},
		}).done(response => {
			var html = '';
			var no = 1;
			$.each(response, function (index, value) {
				html += '<tr><th scope="row">' + no + '</th><td>' + value.remarks + '</td><td>' + value.user_name + '</td><td>' + moment(value.created_at).format('DD-M H:mm') + '</td></tr>';
				no++;
			});
			$("#makeRemarkModal").find('#remark-list').html(html);
			$("#loading-image").hide();
		}).fail(function (response) {
			$("#loading-image").hide();
			toastr['error'](response.errors[0]);
		});;
	});

	$('#addRemarkButton').on('click', function () {
		var id = $('#add-remark input[name="id"]').val();
		var remark = $('#add-remark').find('textarea[name="remark"]').val();

		$.ajax({
			type: 'POST',
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			url: '{{ route('email.addRemark') }}',
			data: {
				id: id,
				remark: remark
			},
		}).done(response => {
			$('#add-remark').find('textarea[name="remark"]').val('');
			var no = $("#remark-list").find("tr").length + 1;
			html = '<tr><th scope="row">' + no + '</th><td>' + remark + '</td><td>You</td><td>' + moment().format('DD-M H:mm') + '</td></tr>';
			$("#makeRemarkModal").find('#remark-list').append(html);
		}).fail(function (response) {
			alert('Could not fetch remarks');
		});

	});

	$(document).on('click', '.bin-email-btn', function (e) {
		e.preventDefault();
		var $this = $(this);
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/email/' + $this.data("id"),
			type: 'delete',
			beforeSend: function () {
				$("#loading-image").show();
			},
		}).done(function (response) {

			// Delete current row from UI
			$('#' + $this.data("id") + "-email-row").remove()

			$("#loading-image").hide();
			toastr['success'](response.message);
		}).fail(function (errObj) {
			$("#loading-image").hide();
			toastr['error'](response.errors[0]);
		});
	});

	$(document).on('click', '.cronEmailPage', function (e) {
		var page = $(this).attr('data-id');
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/cron/gethistory/' + page,
			dataType: 'json',
			type: 'post',
			beforeSend: function () {
				$("#loading-image").show();
			},
		}).done(function (response) {
			// Show data in modal
			$('#getCronEmailModal tbody').html(response.data);
			$('.cronEmailPagination li').removeClass('active');
			$('.cronEmailActive' + page).addClass('active');
			$('#getCronEmailModal').modal('show');

			$("#loading-image").hide();
		}).fail(function (errObj) {
			$("#loading-image").hide();
		});
	});

	$(document).on('click', '.readmore', function () {
		$(this).parent('.lesstext').hide();
		$(this).parent('.lesstext').next('.alltext').show();
	});
	$(document).on('click', '.readless', function () {
		$(this).parent('.alltext').hide();
		$(this).parent('.alltext').prev('.lesstext').show();
	});

	$(document).on('change', '.status', function (e) {
		if ($(this).val() != "" && ($('option:selected', this).attr('data-id') != "" || $('option:selected', this).attr('data-id') != undefined)) {
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: "POST",
				url: "{{ route('changeStatus') }}",
				data: {
					status_id: $('option:selected', this).val(),
					email_id: $('option:selected', this).attr('data-id')
				},
				success: function (response) {
					table.draw();
				},
				error: function (response) {
					//
				}
			})
		}
	});

	$(document).on('change', '.email-category', function (e) {
		if ($(this).val() != "" && ($('option:selected', this).attr('data-id') != "" || $('option:selected', this).attr('data-id') != undefined)) {
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: "POST",
				url: "{{ route('changeEmailCategory') }}",
				data: {
					category_id: $('option:selected', this).val(),
					email_id: $('option:selected', this).attr('data-id')
				},
				success: function (response) {
					table.draw();
				},
				error: function (response) {
					//
				}
			})
		}
	});
	// To set the iframe height based on content
	if (autoIframe == undefined || (autoIframe !== undefined && typeof autoIframe !== 'function')) {
		function autoIframe(frameId) {
			try {
				frame = document.getElementById(frameId);
				innerDoc = (frame.contentDocument) ?
					frame.contentDocument : frame.contentWindow.document;
				objToResize = (frame.style) ? frame.style : frame;
				objToResize.height = innerDoc.body.scrollHeight + 10 + 'px';
			}
			catch (err) {
				window.status = err.message;
			}
		}
	}
	// toggle iframe and short message view
	function toggleMiniFullView(emailId) {
		$('#td-mini-container-' + emailId).toggleClass('hidden');
		$('#td-full-container-' + emailId).toggleClass('hidden');
	}
	function toggleMsgView(emailId) {
		var divID = '#' + $(this).attr('id') + '_div';
		$('#listFrame-' + emailId).attr('src', "");
		var emailUrl = '/email/email-frame/' + emailId;
		$('#listFrame-' + emailId).attr('src', emailUrl);
		toggleMiniFullView(emailId);
	}
	// identify click event within iframe
	let interval = window.setInterval(trackClick, 100);
	function trackClick() {
		var elem = document.activeElement;
		if (elem && elem.tagName == 'IFRAME') {
			var emailId = elem.id.split('-')[1];
			toggleMiniFullView(emailId);
			window.focus();
		}
	}
	function opnMsg(email) {
		$('#eFrame').attr('src', "");
		var emailUrl = '/email/email-frame/' + email.id;
		$('#emailSubject').html(email.subject);
		$('#eFrame').attr('src', emailUrl);
		// Mark email as seen as soon as its opened
		if (email.seen == 0 || email.seen == '0') {
			// Mark email as read
			var $this = $(this);
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url: '/email/' + email.id + '/mark-as-read',
				type: 'put'
			}).done(function (response) {

			}).fail(function (errObj) {

			});
		}

	}

	function markEmailRead(email_id) {

	}

	function load_data(type, seen) {
		$('#type').val(type);
		$('#seen').val(seen);

		table.draw();
	}

	function excelImporter(id) {
		$('#excel_import_email_id').val(id)
		$('#excelImporter').modal('toggle');
	}

	function showFilesStatus(id) {

		if (id) {
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: { id },
				url: '/email/' + id + '/get-file-status',
				type: 'post',

				beforeSend: function () {
					$("#loading-image").show();
				},
			}).done(function (response) {
				if (response.status === true) {
					$("#filesStatus").html(response.mail_status);
					$('#showFilesStatusModel').modal('toggle');
				} else {
					alert('Something went wrong')
				}

				$("#loading-image").hide();
			}).fail(function (errObj) {
				$("#loading-image").hide();
				alert('Something went wrong')
			});
		} else {
			alert('Something went wrong')
		}

		// $('#excelImporter').modal('toggle');
	}

	function importExcel() {
		id = $('#excel_import_email_id').val()
		supplier = $('#supplier_excel_import option:selected').val()
		if (supplier) {
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: {
					supplier,
					id
				},
				url: '/email/' + id + '/excel-import',
				type: 'post'
			}).done(function (response) {
				$('#excelImporter').modal('toggle');
				toastr['success'](response.message);
			}).fail(function (errObj) {
				$('#excelImporter').modal('toggle');
				alert('Something went wrong')
			});
		} else {
			alert('Please Select Supplier')

		}
	}

	function bulkAction(ele, type) {
		let action_type = type;
		var val = [];
		$('.ads_Checkbox:checked').each(function (i) {
			val[i] = $(this).val();
		});

		if (val.length <= 0) {
			alert('Please select at least one checkbox');
		}

		if (val.length > 0) {
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: "POST",
				url: "{{ route('bluckAction') }}",
				data: {
					action_type: action_type,
					ids: val,
					status: $('#bluck_status').val()
				},
				success: function (response) {
					table.draw();
				},
				error: function (response) {

				}
			})

		}

	}

	function opnModal(message) {
		if (message != '') {
			$(document).find('#more-content').html($('<div>').html(message).text());
		}
	}
	$(document).on('click', '.make-label', function (event) {
		event.preventDefault();
		$('.labeling-form input[name="id"]').val($(this).data('id'));
	})

	function Showactionbtn(id) {
		$(".action-btn-tr-" + id).toggleClass('d-none')
	}

	var prev_id = 0;
	$(document).on('click', '.toggle-action', function () {
		var id = $(this).data('id');
		$('.action-btn-tr').remove();
		if (prev_id != id) {
			var tr = '<tr class="action-btn-tr" id="action-btn-tr-' + id + '"><th>Action</th><td colspan="11">' + $('#action-' + id).html() + '</td></tr>';
			$(tr).insertAfter($(this).closest('tr'));
			prev_id = id;
		}
		else {
			prev_id = 0;
		}
	});

	function openAssignModelPopup(ele) {
		var emailId = $(ele).data('id');
		$('#assign_model_email_id').val(emailId);
		$('#assignModel').modal();
	}

	function assignModel() {
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: "POST",
			url: "{{ route('assignModel') }}",
			data: {
				email_id: $('#assign_model_email_id').val(),
				model_name: $('#model_name').val()
			},
			success: function (response) {
				$('#assignModel').modal('hide');
				if (response.type == 'success') {
					alert("User - ( " + $('#model_name').val() + " ) has been successfully created.");
				}
				table.draw();
			},
			error: function (response) {

			}
		})
	}

	$('#searchModel').keypress(function () {
		let modelname = $(this).val();

		if (modelname.length > 3) {
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: "POST",
				url: "{{ route('getModelNames') }}",
				data: {
					model_name: modelname
				},
				success: function (response) {
					if (response.type == 'success') {
						$('.tbody').html('');
						$('.tbody').append(response.html);
					}
				},
				error: function (response) {

				}
			})
		}
	});


	$(document).ready(function () {
		let $emailContents = $(document).find('.emailBodyContent');

		$emailContents.each(function () {
			let $iframe = $(this).find('iframe');
			let body = $(this).attr('data-body');
			$($iframe).contents().find("body").html(body);
		});

		$('#statusModel form').submit(function (e) {
			$("#loading-image").show();
			e.preventDefault();
			var formData = {
				status_type: $('#statusModel #status_type').val(),
				email_status: $('#statusModel #email_status').val()
			};
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: "POST",
				url: "{{ url('email/status') }}",
				data: formData,
				success: function (response) {
					$("#loading-image").hide();
					if (response.status) {
						toastr['success'](response.message);
						setTimeout(function () {
							window.location.href = "{{ route('email.index') }}";
						}, 2000);
					} else {
						if (response.validation) {
							$.each(response.message, function (key, value) {
								$("#statusModel #" + key).addClass('is-invalid').after('<div class="invalid-feedback">' + value[0] + '</div>');
							});
						} else {
							toastr['error'](response.message);
						}
					}
				},
				error: function (response) {
					$("#loading-image").hide();
					toastr['error'](response.message);
				}
			})
		});

		$('#createEmailCategorytModal form').submit(function (e) {
			$("#loading-image").show();
			e.preventDefault();
			var formData = {
				category_name: $('#createEmailCategorytModal #category_name').val(),
				category_priority: $('#createEmailCategorytModal #category_priority').val(),
				category_type: $('#createEmailCategorytModal #category_type').val()
			};
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: "POST",
				url: "{{  url('email/category') }}",
				data: formData,
				success: function (response) {
					$("#loading-image").hide();
					if (response.status) {
						toastr['success'](response.message);
						setTimeout(function () {
							window.location.href = "{{ route('email.index') }}";
						}, 2000);
					} else {
						if (response.validation) {
							$.each(response.message, function (key, value) {
								$("#createEmailCategorytModal #" + key).addClass('is-invalid').after('<div class="invalid-feedback">' + value[0] + '</div>');
							});
						} else {
							toastr['error'](response.message);
						}
					}
				},
				error: function (response) {
					$("#loading-image").hide();
					toastr['error'](response.message);
				}
			})
		});

		$('#UpdateMail form').submit(function (e) {
			$("#loading-image").show();
			e.preventDefault();
			var formData = {
				status: $('#UpdateMail #email_status').val(),
				category: $('#UpdateMail #email_category').val(),
				email_id: $('#UpdateMail #email_id').val(),
			};
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: "POST",
				url: "{{  url('email/update_email') }}",
				data: formData,
				success: function (response) {
					$("#loading-image").hide();
					if (response.status) {
						toastr['success'](response.message);
						setTimeout(function () {
							window.location.href = "{{ route('email.index') }}";
						}, 2000);
					} else {
						if (response.validation) {
							$.each(response.message, function (key, value) {
								$("#UpdateMail select[name='" + key + "']").addClass('is-invalid').after('<div class="invalid-feedback">' + value[0] + '</div>');
							});
						} else {
							toastr['error'](response.message);
						}
					}
				},
				error: function (response) {
					$("#loading-image").hide();
					toastr['error'](response.message);
				}
			})
		});
	})

	function openEmailCategoryChangeLogModelPopup(ele) {
		var email_id = $(ele).data('id');
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: "POST",
			url: "{{ route('getEmailCategoryChangeLogs') }}",
			data: {
				email_id: email_id,
			},
			success: function (response) {
				if (response.type == 'success') {
					$('.emailCategoryLogs').html('');
					$('.emailCategoryLogs').append(response.html);
					$('#categoryLog').modal();
				}
			},
			error: function (response) {

			}
		})
	}

	function openEmailStatusChangeLogModelPopup(ele) {
		var email_id = $(ele).data('id');
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: "POST",
			url: "{{ route('getEmailStatusChangeLogs') }}",
			data: {
				email_id: email_id,
			},
			success: function (response) {
				if (response.type == 'success') {
					$('.emailStatusLogs').html('');
					$('.emailStatusLogs').append(response.html);
					$('#statusLog').modal();
				}
			},
			error: function (response) {

			}
		})
	}

	function displayReplyCategory(ele) {
		let replyType = $(ele).val();
		if (replyType == 'reply') {
			$('#reply-category-div').show();
			$('#store-website-div').hide();
			$('#parent-category-div').hide();
			$('#category-div').hide();
			$('#sub-category-div').hide();
			$('#reply-list-reply-div').hide();
		} else {
			$('#reply-category-div').hide();
			$('#store-website-div').show();
			$('#parent-category-div').show();
			$('#category-div').show();
			$('#sub-category-div').show();
			$('#reply-list-reply-div').show();
		}
	}

	function displayReplayList(ele) {
		let categoryId = $(ele).val();
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: "POST",
			url: "{{ route('getReplyListByCategory') }}",
			data: {
				category_id: categoryId,
			},
			success: function (response) {
				if (response.type == 'success') {
					$('.reply-div').html('');
					$('.reply-div').append(response.html);
					$('.reply-div').show();
				}
			},
			error: function (response) {

			}
		})
	}

	function displayReplayListFromQuickReply(ele) {
		let storeWebsiteId = $('#storeWebsite').val();
		let parentCategoryId = $('#parentCategory').val();
		let categoryId = $('#categoryDrpDwn').val();
		let subCategoryId = $(ele).val();

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: "POST",
			url: "{{ route('getReplyListFromQuickReply') }}",
			data: {
				storeWebsiteId: storeWebsiteId,
				parentCategoryId: parentCategoryId,
				categoryId: categoryId,
				subCategoryId: subCategoryId
			},
			success: function (response) {
				if (response.type == 'success') {
					$('.reply-div').html('');
					$('.reply-div').append(response.html);
					$('.reply-div').show();
				}
			},
			error: function (response) {

			}
		})
	}

	function setAsReply(ele) {
		var reply = $(ele).val();
		$('.reply-message-textarea').text('')
		$('.reply-message-textarea').text(reply);
	}

	function createVendorPopup(email) {
		var username = email.split('@')[0];

		$('#vendorShortcutCreateModal #popup_email').val(email);
		$('#vendorShortcutCreateModal #popup_gmail').val(email);
		$('#vendorShortcutCreateModal #popup_name').val(username);
		$('#create-vendor-id').trigger('click');
	}

  var table = $('#email-table').DataTable({
    processing: true,
    serverSide: true,
    // info: false,
    searching: false,
    lengthChange: false,
    pageLength: 10,
    searchDelay: 1000,
    language: {
        zeroRecords: "Record not found",
    },
    order: [[ 1 , "desc"]],
    "aoColumnDefs": [
        {
          "aTargets": '_all',
          className: 'dt-left',
          createdCell: function (td, cellData, rowData, row, col) {
            switch(col) {
              case 1:
                @if(in_array('Date', $dynamicColumnsToShowb))
                $(td).addClass('d-none');
                @endif
                break;
              case 2:
                @if(in_array('Sender', $dynamicColumnsToShowb))
                $(td).addClass('d-none');
                @endif
                $(td).attr('data-toggle', 'modal');
                $(td).attr('data-target', '#viewMore');
                $(td).attr('onclick', 'opnModal("'+(rowData.from_full??'')+'")');
                break;
              case 3:
                @if(in_array('Receiver', $dynamicColumnsToShowb))
                $(td).addClass('d-none');
                @endif
                $(td).attr('data-toggle', 'modal');
                $(td).attr('data-target', '#viewMore');
                $(td).attr('onclick', 'opnModal("'+(rowData.to_full??'')+'")');
                break;
              case 4:
                @if(in_array('Model Type', $dynamicColumnsToShowb))
                $(td).addClass('d-none');
                @endif
                break;
              case 5:
                @if(in_array('Mail Type', $dynamicColumnsToShowb))
                $(td).addClass('d-none');
                @endif
                break;
              case 6:
                @if(in_array('Subject', $dynamicColumnsToShowb))
                $(td).addClass('d-none');
                @endif
                $(td).attr('data-toggle', 'modal');
                $(td).attr('data-target', '#view-quick-email');
                $(td).attr('onclick', 'openQuickMsg('+JSON.stringify(rowData)+')');
                break;
              case 7:
                @if(in_array('Body', $dynamicColumnsToShowb))
                $(td).addClass('d-none');
                @endif
                $(td).attr('data-toggle', 'modal');
                $(td).attr('data-target', '#view-quick-email');
                $(td).attr('onclick', 'openQuickMsg('+JSON.stringify(rowData)+')');
                break;
              case 8:
                @if(in_array('Status', $dynamicColumnsToShowb))
                $(td).addClass('d-none');
                @endif
                break;
              case 9:
                @if(in_array('Draft', $dynamicColumnsToShowb))
                $(td).addClass('d-none');
                @endif
                break;
              case 10:
                @if(in_array('Error Message', $dynamicColumnsToShowb))
                $(td).addClass('d-none');
                @endif
                $(td).attr('data-toggle', 'modal');
                $(td).attr('data-target', '#viewMore');
                $(td).attr('onclick', 'opnModal("'+(rowData.error_message_full??'')+'")');
                break;
              case 11:
                @if(in_array('Category', $dynamicColumnsToShowb))
                $(td).addClass('d-none');
                @endif
                break;
            }
          }
        },
        { "bSortable": false, "aTargets": [0, 7, 8, 10, 11, 12] },
        { "bSearchable": false, "aTargets": [0, 12] }
    ],
    ajax: {
        url: "{{ url('email') }}",
        data: function(d) {
          d.term = $("#term").val();
          d.date = $("#date").val();
          d.type = $("#type").val();
          d.seen = $("#seen").val();
          d.sender = $("#sender").val().toString();
          d.receiver = $("#receiver").val().toString();
          d.status = $("#email_status").val().toString();
          d.category = $("#category").val().toString();
          d.mail_box =  $("#mail_box").val().toString();
          d.email_model_type = $('#email_model_type').val().toString();
        },
        error: function(response) {
            console.log(response);
        }
    },
    columns: @json($columns)
  });
  table.on('draw', function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
</script>
@endsection