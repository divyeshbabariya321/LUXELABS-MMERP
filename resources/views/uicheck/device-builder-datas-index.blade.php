@extends('layouts.app')
@section('favicon', '')

@section('title', 'Device builder datas')

@section('styles')
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
    </style>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>

    <div class="row">
        <div class="col-lg-12 margin-tb">

            <h2 class="page-heading">Builder Datas</h2>
            @include('partials.flash_messages')
            <div class="pull-left">
                <div class="form-group">
                    <div class="row">
                        <form class="form-inline message-search-handler" method="get" action="{{route('uicheck.device-builder-datas')}}">
							<div class="form-group m-1">
                                <h5><b>Search website</b></h5>
								<select name="web_ids[]" id="web_ids" class="form-control select2" multiple>
									@forelse($storeWebsites as $uId => $storeWebsite)
									<option value="{{  $storeWebsite->id}}" 
                                        @if(is_array(request('web_ids')) && in_array( $storeWebsite->id, request('web_ids')))
                                            selected
                                        @endif >{{ $storeWebsite->website }}</option>
									@empty
									@endforelse
								</select>
                            </div> &nbsp;&nbsp;
                            <div class="form-group m-1">
                                <h5><b>Search Category</b></h5>
								<select name="cat_name[]" id="cat_name" class="form-control select2" multiple>
									@forelse($siteDevelopmentCategories as $sdcId => $siteDevelopmentCategory)
                                    <option value="{{  $sdcId}}" 
                                    @if(is_array(request('cat_name')) && in_array( $sdcId, request('cat_name')))
                                        selected
                                    @endif >{{ $siteDevelopmentCategory}}</option>
                                                                        @empty
									@endforelse
								</select>
                            </div>&nbsp;&nbsp;
                            <div class="form-group sm-1">  
                                <h5><b>Search Status</b></h5>                          
                              <select name="status[]" id="status" class="form-control select2" multiple>
                                    @forelse($getbuildStatuses as $uId => $status)
                                    <option value="{{  $status->id}}" 
                                        @if(is_array(request('status')) && in_array( $status->id, request('status')))
                                            selected
                                        @endif >{{ $status->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                             </div>
                            <div class="form-group">
                                <label for="button">&nbsp;</label>
                                <button type="submit" style="display: inline-block;width: 10%"
                                    class="btn btn-sm btn-image btn-search-action">
                                    <img src="/images/search.png" style="cursor: default;">
                                </button>
                                <a href="{{route('uicheck.device-builder-datas')}}" class="btn btn-image" id="">
									<img src="/images/resend2.png" style="cursor: nwse-resize;">
								</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="pull-right">
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#buildStatusList"> List Status </button>
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#buildStatusCreate"> Create Status </button>      
                    <button type="button" class="btn btn-secondary" onclick="createBuilderIOTask()"> Create Task </button>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="table-responsive">
                <table class="table table-bordered" id="builder-data-list">
                    <thead>
                        <tr>
                            <th><input type="checkbox" name="select_all" class="select_all"></th>
                            <th>#</th>
                            <th>Task ID</th>
                            <th style="max-width: 150px">Categories</th>
                            <th>Website</th>
                            @if (Auth::user()->isAdmin())
                            <th>User Name</th>
                            @endif
                            <th>Type</th>
                            <th>Device No</th>
                            <th>Title</th>
                            <th>Builder Created Date</th>
                            <th>Builder Last Updated</th>
                            <th>Status</th>
                            <th>Remark</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($builderDatas as $key => $builderData)
                            <tr data-id="{{ $builderData->id }}" style="background-color: {{$builderData->UiBuilderStatusColour?->color}};">
                                <td>
                                    @if ($builderData->task_id == "")
                                    <input type="checkbox" name="bulk_select_row[]" class="d-inline bulk_select_row" value="{{$builderData->id}}">
                                    @endif
                                </td>
                                <td>{{ ++$i }}</td>
                                <td>{{ $builderData->task_id }}</td>
                                <td style="max-width: 150px">
                                    <div data-message="{{ $builderData->category }}" data-title="Category" style="cursor: pointer" class="showFullMessage">
                                        {{ show_short_message($builderData->category, 15) }}
                                    </div>
                                </td>
                                <td>
                                    {{ $builderData->website }}
                                </td>
                                @if (Auth::user()->isAdmin())
                                <td>{{ $builderData->name }}</td>
                                @endif
                                <td>{{ $builderData->uicheck_type_id ? $allUicheckTypes[$builderData->uicheck_type_id] : ''}}</td>
                                <td>{{ $builderData->device_no }}</td>
                                <td>{{ $builderData->title }}</td>
                                <td>{{ $builderData->builder_created_date }}</td>
                                <td>{{ $builderData->builder_last_updated }}</td>
                                <td>
                                    <div class="flex">
                                    <select class="form-control selecte2 build-status">
                                        <option  value="" >Please select</option>
                                        @foreach($getbuildStatuses as $status)
                                            <option  value="{{ $status->id }}" data-id="{{$builderData->id}}" {{$status->id == $builderData->status_id ? 'selected' : '' }}>{{$status->name }}</option>
                                        @endforeach
                                      </select>
                                      <button type="button" class="btn btn-xs btn-image load-status-history ml-2" data-id="{{$builderData->id}}" title="Load Status"> <img src="/images/chat.png" alt="" style="cursor: default; float:right"> </button>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" id="remarks_{{$builderData->id}}" name="remarks" class="form-control" placeholder="Remark" />
                                    <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" data-id="{{$builderData->id}}" onclick="saveRemarks({{$builderData->id}})"><img src="/images/filled-sent.png"></button>
                                    <button type="button" class="btn btn-xs btn-image load-module-remark ml-2" data-id="{{$builderData->id}}" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                </td>
                                <td>
                                    <a target="_blank" href="{{ route('uicheck.get-builder-html', $builderData->id) }}">
                                        <i class="btn btn-xs fa fa-eye" title="View Builder HTML"></i>
                                    </a>
                                    <a href="{{ route('uicheck.get-builder-download-html', $builderData->id) }}">
                                        <i class="btn btn-xs fa fa-download" title="Download Builder HTML"></i>
                                    </a>
                                    <i data-data-id="{{ $builderData->id }}" class="btn btn-xs fa fa-info-circle show-download-history" title="Download History"></i>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {!! $builderDatas->appends(request()->except('page'))->links() !!}
        </div>
    </div>

    <div id="showFullMessageModel" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>

    <div id="modalGetDevMessageHistory" class="modal fade" role="dialog" >
        <div class="modal-dialog modal-xl">
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
                                    <th width="10%" style="word-break: break-all;">Expected start time</th>
                                    <th width="10%" style="word-break: break-all;">Expected completion time</th>
                                    <th width="10%" style="word-break: break-all;">Estimated Time</th>
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

    <div id="buildStatusCreate" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                
                <form id="build_status_create_form" class="form mb-15" >
                <div class="modal-header">
                    <h4 class="modal-title">Create Build Status</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Status Name :</strong>
                        {{ html()->text('name')->placeholder('Status Name')->id('name')->class('form-control')->required() }}
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Status Color :</strong>
                        <input type="color" name="color" class="form-control"  id="color" value="" style="height:30px;padding:0px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Add</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div id="buildStatusList" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">List Status</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{ route('uicheck.device-builder.status.color.update') }}" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="form-group col-md-12">
                        <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                            <tr>
                                <td class="text-center"><b>Status Name</b></td>
                                <td class="text-center"><b>Color Code</b></td>
                                <td class="text-center"><b>Color</b></td>
                            </tr>
                            <?php
                            foreach ($getbuildStatuses as $status) { ?>
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

    <div id="remark-area-list" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Remark History</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
    
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="30%">Remark</th>
                                    <th width="20%">Updated BY</th>
                                    <th width="30%">Created Date</th>
                                </tr>
                            </thead>
                            <tbody class="remark-action-list-view">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="status-area-list" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Status History</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
    
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="30%">Old Status</th>
                                    <th width="30%">New Status</th>
                                    <th width="20%">Updated BY</th>
                                    <th width="30%">Created Date</th>
                                </tr>
                            </thead>
                            <tbody class="status-action-list-view">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="builder-task-create" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <form id="builder-task-create-form" action="<?php echo route('uicheck.store.builder-io-task'); ?>" method="post">
                    <div class="modal-header">
                        <h4 class="modal-title">Create Builder IO Task</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                            <?php echo csrf_field(); ?>
                            <div class="form-group normal-subject">
                                <label for="task_name">Task Name<span class="text-danger">*</span></label>
                                <input type="text" name="task_name" id="task_name" class="form-control" value="Builder IO Task" readonly/>
                            </div>
                            <div class="form-group">
                                <label for="assign_to">Assigned to<span class="text-danger">*</span></label>
                                {{ html()->select("assign_to", ['' => ''])->class("form-control assign_to globalSelect2")->style("width:100%;")->data('ajax', route('select2.user'))->data('placeholder', 'Assign to') }}
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary save-builder-task-window">Save</butto>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for download history -->
    <div class="modal fade" id="downloadHistoryModal" tabindex="-1" role="dialog" aria-labelledby="downloadHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="downloadHistoryModalLabel">Download History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Download history data fetched via AJAX will be displayed here -->
                </div>
            </div>
        </div>
    </div>
    
@endsection
@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script type="text/javascript">
		$('.select2').select2();
        $("#search_user").select2();
        $('#search_task').select2({
            minimumInputLength: 3 // only start searching when the user has input 3 or more characters
        });

        $('.select_all').on('change', function() {
            var isChecked = $(this).prop('checked');
            $('.bulk_select_row').prop('checked', isChecked);
        });

        var selected_rows = [];
        function createBuilderIOTask()
        {
            event.preventDefault();

            selected_rows = [];
            $(".bulk_select_row").each(function () {
                if ($(this).prop("checked") == true) {
                    selected_rows.push($(this).val());
                }
            });

            if (selected_rows.length == 0) {
                alert('Please select any row');
                return false;
            }

            $('#builder-task-create').modal('show');
        }

        $(document).on('submit', '#builder-task-create-form', function (e) {
            e.preventDefault();
            var self = $(this);
            var data = $(this).serializeArray();
            data.push({name: 'selected_rows', value: selected_rows});
            $.ajax({
                url: "{{route('uicheck.store.builder-io-task')}}",
                type: 'POST',
                data: data,
                success: function (response) {
                    if (response.code == 200) {
                        toastr['success'](response.message);
                        $('#builder-task-create').modal('hide');
                        window.location.reload();
                    } else {
                        toastr['error'](response.message);
                    }
                },
                error: function(xhr, status, error) { // if error occured
                    if(xhr.status == 422){
                        var errors = JSON.parse(xhr.responseText).errors;
                        customFnErrors(self, errors);
                    }
                    else{
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    }
                }
            });
        });

        $(document).on("click", ".showFullMessage", function() {
            let title = $(this).data('title');
            let message = $(this).data('message');

            $("#showFullMessageModel .modal-body").html(message);
            $("#showFullMessageModel .modal-title").html(title);
            $("#showFullMessageModel").modal("show");
        });

		$(function() {
			$('input[name="daterange"]').daterangepicker({
				opens: 'left'
			}, function(start, end, label) {
				console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
			});
		});

        function funGetDevHistory(id, uicheckId) {
            //siteLoader(true);
            let mdl = jQuery('#modalGetDevMessageHistory');
            var uicheckId = uicheckId;
            
            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "/uicheck/get/message/history/dev",
                type: 'POST',
                data: {
                    device_no: id,
                    uicheck_id : uicheckId,
                },
                beforeSend: function() {
                    //jQuery("#loading-image").show();
                }
            }).done(function(response) {
                $("#modalCreateLanguage").modal("hide");
                mdl.find('tbody').html(response.html);
                mdl.modal("show");
            }).fail(function (jqXHR, ajaxOptions, thrownError) {      
                toastr["error"](jqXHR.responseJSON.message);
                $("#loading-image").hide();
            });
        }

        $(document).on('submit', '#build_status_create_form', function(e){
            e.preventDefault();
            var self = $(this);
            let formData = new FormData(document.getElementById("build_status_create_form"));
            var button = $(this).find('[type="submit"]');
            $.ajax({
                url: '{{ route("uicheck.device-builder.status.store") }}',
                type: "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                beforeSend: function() {
                    button.html('Loading...');
                    button.prop('disabled', true);
                    button.addClass('disabled');
                },
                success: function(response) {
                    $('#buildStatusCreate #build_status_create_form').trigger('reset');
                    toastr["success"](response.message);
                    location.reload();
                },
                error: function(xhr, status, error) {
                // Parse the JSON response
                var response = xhr.responseJSON;
                if (response && response.message) {
                    toastr["error"](response.message);
                } else {
                    toastr["error"]("An error occurred.");
                }
                
                // Hide the loading image on the save button
                button.html("Save");
                button.prop('disabled', false);
                button.removeClass('disabled');
            },
            });
        });

        // Store Remarks
        function saveRemarks(row_id) {
            var remark = $("#remarks_" + row_id).val();
            $.ajax({
                url: `{{ route('uicheck.store.builder-data-remark') }}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    remarks: remark,
                    ui_device_builder_io_data_id: row_id
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                if (response.status) {
                    $("#remarks_" + row_id).val('');
                    toastr["success"](response.message);
                } else {
                    toastr["error"](response.message);
                }
                $("#loading-image").hide();
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                if (jqXHR.responseJSON.errors !== undefined) {
                    $.each(jqXHR.responseJSON.errors, function(key, value) {
                        // $('#validation-errors').append('<div class="alert alert-danger">' + value + '</div');
                        toastr["warning"](value);
                    });
                } else if(jqXHR.responseJSON.message !== undefined) {
                    toastr["error"](jqXHR.responseJSON.message);
                } else {
                    toastr["error"]("Oops,something went wrong");
                }
                $("#loading-image").hide();
            });
        }

        // Load Remark
        $(document).on('click', '.load-module-remark', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: `{{ route('uicheck.get.builder-data-remark', '') }}/` + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.remarks } </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#remark-area-list").find(".remark-action-list-view").html(html);
                        $("#remark-area-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        $(document).on('change','.build-status',function(e){
            if($(this).val() != "" && ($('option:selected', this).attr('data-id') != "" || $('option:selected', this).attr('data-id') != undefined)){
               var buildId = $('option:selected', this).attr('data-id');
                $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type : "POST",
                url : "{{ route('uicheck.device.update.status') }}",
                data : {
                    statusId : $('option:selected', this).val(),
                    buildId : buildId,
                },
                success: function(response) {
                    toastr["success"](response.message);
                    $(`#builder-data-list tr[data-id="${buildId}"]`).css('background-color', response.colourCode);
               },
                error: function(response) {
                    toastr["error"]("Oops, something went wrong");
                }
              })
            }
        });

        
        $(document).on('click', '.load-status-history', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: `{{ route('uicheck.get.builder-data-status', '') }}/` + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.old_status !== null ? v.old_status.name : ""} </td>
                                        <td> ${v.new_status !== null ? v.new_status.name : ""} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#status-area-list").find(".status-action-list-view").html(html);
                        $("#status-area-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        $(document).on('click', '.show-download-history', function() {
            var dataId = $(this).data('data-id');

            $.ajax({
                url: '/uicheck/get-builder-download-history/' + dataId,
                method: 'GET',
                success: function(response) {
                    $('#downloadHistoryModal .modal-body').html(response);
                    $('#downloadHistoryModal').modal('show');
                },
                error: function() {
                    alert('Error fetching download history data.');
                }
            });
        });
    </script>
@endsection
