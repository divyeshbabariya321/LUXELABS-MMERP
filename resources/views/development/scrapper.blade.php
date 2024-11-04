@extends('layouts.app')

@section('favicon' , 'development-issue.png')

@if($title == "devtask")
    @section('title', 'Development Issue')
@else
    @section('title', 'Development Task')
@endif

@section('styles')
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <style type="text/css">

    </style>
@endsection

<style> 
    .status-selection .btn-group {
        padding: 0;
        width: 100%;
    }
    .status-selection .multiselect {
        width : 100%;
    }
    .pd-sm {
        padding: 0px 8px !important;
    }
    tr {
        background-color: #f9f9f9;
    }
    .mr-t-5 {
        margin-top:5px !important;
    }
    /* START - Pupose : Set Loader image - DEVTASK-4359*/
    #myDiv{
        position: fixed;
        z-index: 99;
        text-align: center;
    }
    #myDiv img{
        position: fixed;
        top: 50%;
        left: 50%;
        right: 50%;
        bottom: 50%;
    }
    /* END - DEVTASK-4359*/
</style>
@section('content')

    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb pr-0">
                    <h2 class="page-heading">Scrapper Task List</h2>
                    <div class="pull-left cls_filter_box">
                        {{ html()->modelForm([], 'GET', url()->current())->class('form-inline')->open() }}

                            <div class="form-group ml-3 cls_filter_inputbox">
                                <select id="module" name="module" class="form-control"placeholder="Module">
                                    <option value=""> Select Module</option>
                                    @foreach ($modules as $key => $item)
                                        <option value="{{ $key }}" {{ @$inputs['module'] == $key ? 'selected' : '' }}> {{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                         
                            <div class="form-group ml-3 cls_filter_inputbox">
                                {{ html()->text('subject', @$inputs['subject'])->class('form-control') }}
                            </div>

                            <div class="form-group ml-3 cls_filter_inputbox">
                                {{ html()->text('task', @$inputs['task'])->class('form-control') }}
                            </div>

                            <div class="form-group ml-3 cls_filter_inputbox" style="width: 194px;">
                                <select name="user_id" id="user_id" class="form-control" aria-placeholder="Select User" style="float: left">
                                    @if (isset($users->id))
                                        <option value="{{ $users->id }}" selected="selected">{{ $users->name }}</option>
                                    @endif
                                </select>
                            </div>

                            <div class="form-group ml-3 cls_filter_inputbox" style="width: 194px;">
                                {{ html()->select('status', ['' => 'Select', 'In Review' => 'In Review', 'In Progress' => 'In Progress'], @$inputs['status'])->class('form-control') }}
                            </div>

                            <div class="form-group  cls_filter_inputbox">
                                <button type="submit" class="btn btn-secondary ml-3" style="width:100px">Search</button>
                            </div>

                            <div class="form-group  cls_filter_inputbox">
                                <button type="button" class="btn btn-secondary ml-3 reset" style="width:100px">Reset</button>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12  pl-5">
	<div class="row mb-3">
		<div class="mt-3 col-md-12">
		    <table class="table table-bordered table-striped"style="table-layout: fixed;">
                    <thead>
                        <tr>
                            <th width="3%">Id</th>
                            <th width="6%">Module</th>
                            <th width="6%">Subject</th>
                            <th width="18%">Communication </th>
                            <th width="7%" >Task</th>
                            <th width="6%" >Assigned To</th>
                            <th width="6%" >Status</th>
                            <th width="3%">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center task_queue_list">
                        @foreach($issues as $i=>$issue) 
                            <tr>
								<td>{{ $issue['id'] }}</td>
								<td>{{ $issue->developerModule ? $issue->developerModule->name : 'Not Specified' }}</td>
								<td class="expand-row-msg" data-name="subject" data-id="{{$i}}">
									<span class="show-short-subject-{{$i}}">{{ Str::limit($issue->subject, 10, '...')}}</span>
									<span style="word-break:break-all;" class="show-full-subject-{{$i}} hidden">{{ $issue->subject }}</span>
								</td>
                                <td class="expand-row">
                                    <!-- class="expand-row" -->
                                    <div style="display:flex; justify-content: space-between;">
                                    <span class="{{ ($issue->message && $issue->message_status == 0) || $issue->message_is_reminder == 1 || ($issue->sent_to_user_id == Auth::id() && $issue->message_status == 0) ? 'text-danger' : '' }}" style="word-break: break-all;">{{  \Illuminate\Support\Str::limit($issue->message, 150, $end='...') }}</span>
                                    <input type="text" class="form-control send-message-textbox addToAutoComplete" data-id="{{$issue->id}}" id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}" style="width: 200px;">
                                    <input class="mt-3 add_to_autocomplete" name="add_to_autocomplete"  type="checkbox" value="true">
                                    {{ html()->select("send_message_" . $issue->id, ["to_developer" => "Send To Developer", "to_master" => "Send To Master Developer", "to_team_lead" => "Send To Team Lead", "to_tester" => "Send To Tester"])->class("form-control send-message-number")->style("width:30% !important;display: inline;") }}
                                    
                                    <button style="display: inline-block;width:10%; padding: 0;" class="btn btn-sm btn-image send-message-open" type="submit" id="submit_message"  data-id="{{$issue->id}}" ><img src="{{config('app.url')}}/images/filled-sent.png"/></button>

                                        <button type="button" class="btn btn-xs btn-image pr-0 load-communication-modal" data-object='developer_task' data-id="{{ $issue->id }}" style="margin-top:-0%;" title="Load messages"><img src="{{config('app.url')}}/images/chat.png" alt=""></button>
                                    
                                        <div class="td-full-container hidden">
                                            <button class="btn btn-secondary m-0 btn-xs" onclick="sendImage({{ $issue->id }} )" >Send Attachment</button>
                                            <button class="btn btn-secondary m-0 btn-xs" onclick="sendUploadImage({{$issue->id}} )">Send Images</button>
                                            <input id="file-input{{ $issue->id }}" type="file" name="files" style="display: none;" multiple/>
                                        </div>
                                    </div>
                                 </td>

								<td class="expand-row-msg Website-task" data-name="task" data-id="{{$i}}">
									<span class="Website-task show-short-task-{{$i}}">{{ Str::limit($issue->task, 20, '...')}}</span>
									<span style="word-break:break-all;" class="Website-task show-full-task-{{$i}} hidden">{{ $issue->task }}</span>
								</td>
								 <td class="Website-task">   @if($issue->assignedUser)
											<p class="Website-task">{{ $issue->assignedUser->name }}</p>
										@else
											<p class="Website-task">Unassigned</p>
										@endif
								</td>
                                <td>
									{{ $issue->status}}
								</td>
                                <td>
                                    <button class="btn btn-xs btn-none-border show_error_logs" data-id="{{ $issue->scraper_id}}" data-assigned="{{$issue->assigned_to}}"><i class="fa fa-eye"></i></button>
                         
                                </td>
							</tr>
                        @endforeach
                    </tbody>
            </table>
			{{$issues->links()}}
        </div>
    </div> 
    </div    
    <div id="ErrorLogModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" style="padding: 0px;width: 90%;max-width: 90%;">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Flow Log Detail</h4>
            </div>
            <div class="modal-body">
              <table class="table table-bordered table-hover" style="table-layout:fixed;">
                <thead>
                    
                    <th style="width:10%">Flow Action</th>
                  <th style="width:20%">Modal Type </th>
                  <th style="width:20%">Leads</th>
                  <th style="width:25%">Message</th>
                  <th style="width:15%">Website</th>
                  <th style="width:10%">Date</th>
                </thead>
                <tbody class="error-log-data">
    
                </tbody>
              </table>
    
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
@endsection
@section('scripts')
   <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>


    $(document).ready(function() {
        $("#user_id").select2({
            ajax: {
                url: '{{ route('user-search') }}',
                dataType: 'json',
                data: function(params) {
                    return {
                        q: params.term, // search term
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
            },
            placeholder: "Select User",
            allowClear: true,
            minimumInputLength: 2,
            width: '100%',
        });
    });
    $(".select2").select2({
        "tags": true
    });

    $(document).on('click', '#submit_message', function (event) {
            let self = this;
            let developer_task_id = $(this).attr('data-id');
            let message = $("#message_" + developer_task_id).val();

            // if (event.which != 13) {
            //     return;
            // }

            $.ajax({
                url: "{{action([\App\Http\Controllers\WhatsAppController::class, 'sendMessage'], 'developer_task')}}",
                type: 'POST',
                data: {
                    _token: "{{csrf_token()}}",
                    message: message,
                    developer_task_id: developer_task_id,
                    status: 2
                },
                success: function () {
                    $(self).removeAttr('disabled');
                    $("#message_" + developer_task_id).removeAttr('disabled');
                    $(self).val('');
                    $("#message_" + developer_task_id).val('');
                    toastr['success']('Message sent successfully!', 'Message');
                },
                error: function () {
                    $(self).removeAttr('disabled');
                },
                beforeSend: function () {
                    $(self).attr('disabled', true);
                    $("#message_" + developer_task_id).attr('disabled', true);
                }
            });
        });



    $(document).on('click', '.reset', function () {
        var url = "{{ route('development.scrap.index') }}";
        window.location.href = url;
    });
	
    $(document).on('click', '.expand-row-msg', function () {
        var name = $(this).data('name');
        var id = $(this).data('id');
        var full = '.expand-row-msg .show-short-'+name+'-'+id;
        var mini ='.expand-row-msg .show-full-'+name+'-'+id;
        $(full).toggleClass('hidden');
        $(mini).toggleClass('hidden');
    });
    $(document).on("click", ".show_error_logs", function() {
        var id = $(this).data('id');
        var assigned = $(this).data('assigned');
        $.ajax({
            method: "GET",
            url: "{{ route('logging.flow.detail') }}" ,
            data: {
                "_token": "{{ csrf_token() }}",
                "scraper_id" : id,
                "assigned" : assigned
            },
            dataType: 'html'
            })
        .done(function(result) {
            $('#ErrorLogModal').modal('show');
            $('.error-log-data').html(result);
    });

});
    </script>
@endsection
