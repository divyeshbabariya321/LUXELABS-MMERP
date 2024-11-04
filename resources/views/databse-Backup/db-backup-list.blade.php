@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Database Backup Monitoring ({{$dbLists->total()}})</h2>
		</div>
	</div>
	@include('partials.flash_messages')
	<div class="mt-3 col-md-12">
		<form action="{{route('get.backup.monitor.lists')}}" method="get" class="search">
			<div class="col-lg-2">
				{{ html()->multiselect("s_ids[]", \App\Models\DatabaseBackupMonitoring::pluck('server_name', 'server_name')->toArray(), request('s_ids'))->class("form-control globalSelect2")->data('placeholder', "Select Server Name") }}
			</div>
			<div class="col-lg-2">
				{{ html()->multiselect("db_ids[]", \App\Models\DatabaseBackupMonitoring::pluck('database_name', 'database_name')->toArray(), request('db_ids'))->class("form-control globalSelect2")->data('placeholder', "Select DataBase") }}
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search_instance" placeholder="Search Instance" name="search_instance" value="{{ $search_instance ?? '' }}">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search_error" placeholder="Search Error" name="search_error" value="{{ $search_error ?? '' }}">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="date" name="date">
			</div>

			<div class="col-lg-2"><br>
				<button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
				   <img src="{{ asset('images/search.png') }}" alt="Search">
			   </button>
			   <a href="{{route('get.backup.monitor.lists')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
		    </div>
		</form>
	</div>
		<div class="pull-right">
			<button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#db-status-listing"> List Status </button>
			<button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#db-status-create"> Create Status </button>
			</div>
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th width="3%">S.no</th>
			    	<th width="10%">Server Name</th>
			        <th width="10%">Instance</th>
			        <th width="10%">Database Name</th>
			        <th width="30%">Error</th>
					<th width="5%">Is Resolved</th>
			        <th width="10%">date</th>
                    <th width="10%">Status</th>
                </tr>
		    	<tbody>
                    @foreach ($dbLists as $key => $dbList)
                        <tr data-id="{{ $dbList->id }}" style="background-color: {{$dbList->dbStatusColour?->color}};">
                            <td>{{$key+1}}</td>
                            <td>{{$dbList->server_name}}</td>
							<td>{{$dbList->instance}}</td>
							<td>{{$dbList->database_name}}</td>
							<td>
								@if($dbList->error)
								<div>
								{{ strlen($dbList->error) > 10 ? substr($dbList->error, 0, 70).'...' : $dbList->error }}
								<i class="fa fa-eye show_logs show-logs-icon" data-id="{{ $dbList->id }}" style="color: #808080;float: right;"></i>
							    </div>
								@endif
							</td>
							<td>
								<input type="checkbox" name="is_resolved" value="1" data-id="{{ $dbList->id }}" onchange="updateIsResolved(this)">
							</td>
							<td>{{$dbList->date}}</td>
                            <td>
								<select class="form-control change-db-status select2" data-id="{{$dbList->id}}" name="db_backup_status_id">
									<option value="">Select...</option>
									@foreach($dbStatuses as $id => $status)
										@if( $dbList->db_status_id == $status->id )
											<option value="{{$status->id}}" selected>{{ $status->name }}</option>
										@else
											<option value="{{$status->id}}">{{ $status->name }}</option>
										@endif
									@endforeach
								</select>
							</td>
						</tr>                        
                    @endforeach
		    	</tbody>
		    </thead>
		</table>
		{!! $dbLists->appends(Request::except('page'))->links() !!}
	</div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
</div>

<div class="modal" tabindex="-1" role="dialog" id="error_logs_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Error details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="error_logs_div">
                        <table class="table">
                            <thead>
                                <tr>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="db-status-listing" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">List Db Status</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('db-backup-color-update') }}" method="POST">
                <?php echo csrf_field(); ?>
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Status Name</b></td>
                            <td class="text-center"><b>Color Code</b></td>
                            <td class="text-center"><b>Color</b></td>
                        </tr>
                        <?php
                        foreach ($dbStatuses as $status) { ?>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;<?php echo $status->name; ?></td>
                            <td class="text-center"><?php echo $status->color; ?></td>
                            <td class="text-center"><input type="color" name="color_name[<?php echo $status->id; ?>]" class="form-control" data-id="<?php echo $status->id; ?>" id="color_name_<?php echo $status->id; ?>" value="<?php echo $status->color; ?>" style="height:30px;padding:0px;"></td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submit-status-color">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('databse-Backup.partials.db-backup-status-create-modal')

@endsection

@section('scripts')
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>

		$(document).on('click', '.show-logs-icon', function() {
		var id = $(this).data('id');
			$.ajax({
				url: '{{route('db.error.show')}}',
				method: 'GET',
				data: {
					id: id
				},
				success: function(response) {
					$('#error_logs_modal').modal('show');
					$('#error_logs_div').html(response);
				},
				error: function(xhr, status, error) {
					alert("Error occured.please try again");
				}
			});
		});

		$(document).on('change', '.change-db-status', function() {
			let id = $(this).attr('data-id');
			let status = $(this).val();
			$.ajax({
				url: "{{route('db-backup.change.status')}}",
				type: "POST",
				headers: {
					'X-CSRF-TOKEN': "{{ csrf_token() }}"
				},
				dataType: "json",
				data: {
					'db_backup_id': id,
					'status': status
				},
				success: function(response) {
					toastr["success"](response.message, "Message")
					$(`#log-table tr[data-id="${id}"]`).css('background-color', response.colourCode);
			},
				error: function(error) {
					toastr["error"](error.responseJSON.message, "Message")
				}
			});
    	});

</script> 
@endsection