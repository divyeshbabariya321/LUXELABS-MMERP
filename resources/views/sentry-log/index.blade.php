@extends('layouts.app')
@section('link-css')
<style type="text/css">
  .float-right-addbtn{
    float: right !important;
    margin-right: 5px;
  }
  .form-group {
    padding: 10px;
  }
  #create-quick-task .form-group {padding: 0px !important;}
</style>
@endsection
@section('content')
<!-- SENTRY ACCOUNT MODEL CONTENT START -->
<div id="sentry_modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Add Sentry Account</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="add_sentry_account">
              <div class="modal-body">
                  <div class="form-group">
                      <strong>Project:</strong>
                      <input type="text" name="project" class="form-control" id="project">
                      <label class="error"></label>
                  </div>
                  <div class="form-group">
                      <strong>Organization:</strong>
                      <input type="text" name="organization" class="form-control" id="organization">
                      <label class="error"></label>
                  </div>
                  <div class="form-group">
                      <strong>Token:</strong>
                      <input type="text" name="token" class="form-control" id="token">
                      <label class="error"></label>
                  </div>
              </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!--SENTRY ACCOUNT MODEL CONTENT END -->
<!-- SENTRY ACCOUNT LISTING MODEL CONTENT START -->
<div id="sentry_account_listing_modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Sentry Account List</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-12">
                  <table class="table table-bordered" width="100%">
                    <thead>
                      <tr>
                          <th width="10%">No</th>
                          <th width="20%">Organization</th>
                          <th width="10%">Project</th>
                          <th width="50%">Token</th>
                      </tr>
                    </thead>
                    <tbody id="account_list">                      
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!--SENTRY ACCOUNT SELECTION MODEL CONTENT END -->

<div class="row">
  <div class="col-lg-12 margin-tb">
    <h2 class="page-heading">
      SENTRY logs
      (<span>{{count($sentryLogsData)}}</span>)
    </h2>
  </div>
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success">
  <p>{{ $message }}</p>
</div>
@endif


<div class="table-responsive">
    <form action="{{route('sentry-log')}}" method="GET">
      @csrf
      <div class="col-md-2">  
        <input type="text" name="keyword" class="form-control" placeholder="Search keyword" value="{{request()->get('keyword')}}">
      </div>
      <div class="col-md-2">  
        <select class="form-control" id="project_list" name="project_list">
         <option value="">Select</option>
         @foreach($projects as $project)
         <option value="{{$project['id']}}">{{$project['name']}}</option>
         @endforeach
        </select>
      </div>
      <div class="col-md-2">
          <button type="submit" class="btn btn-secondary" id="search_log">Search</button>
          <button type="button" class="btn btn-secondary" id="load_page">Cancel</button>
      </div>
    </form>
<div class="col-md-6">
  <button type="button" class="btn btn-secondary float-right-addbtn" data-toggle="modal" data-target="#status-create">Add Status</button>
  <button type="button" class="btn btn-secondary float-right-addbtn" data-toggle="modal" data-target="#newStatusColor"> Status Color</button>
  <button type="button" class="btn btn-secondary float-right-addbtn" id="add_account">+ Add Account</button>
  <button type="button" class="btn btn-secondary float-right-addbtn" id="list_account"> List Account</button>
  <button type="button" class="btn btn-danger float-right-addbtn" id="refresh_logs"> Refresh Logs</button>
</div>
  <table class="table table-bordered" id="sentry_log_table" style=" margin-top: 10px; position: relative;  display: inline-block;">
    <thead>
      <tr>
        <th style="width: 3%">#</th>
        <th style="width: 5%">Id</th>
        <th style="width: 8%">Title</th>
        <th style="width: 5%">Issue Type</th>
        <th style="width: 5%">Issue Category</th>
        <th style="width: 5%">Is Unhandled</th>
        <th style="width: 10%">Project</th>
        <th style="width: 5%">Total Event</th>
        <th style="width: 5%">Total User</th>
        <th style="width: 6%">Device Name</th>
        <th style="width: 5%">Os</th>
        <th style="width: 5%">Os Name</th>
        <th style="width: 5%">Release</th>
        <th style="width: 10%">First Seen</th>
        <th style="width: 10%">Last Seen</th>
        <th style="width: 10%">Status</th>
        <th style="width: 10%">Action</th>
      </tr>
    </thead>
    @foreach ($sentryLogsData as $key => $row)
        @php
            if(!empty($row['status_id'])){
                $status_color = $sentyStatus->where('id',$row['status_id'])->first();
                if ($status_color == null) {
                    $status_color = new stdClass();
                }
            } else {
                $status_color = new stdClass();
            }
        @endphp
            <tr style="background-color: {{$status_color->senty_color ?? ""}}!important;">
                <td>{{ $key+1 }}</td>
                <td>{{ $row['id'] }}</td>
                <!-- <td>{{ $row['title'] }}</td> -->
                <td class="expand-row-msg" data-name="name" data-id="{{$row['unique_id']}}">
                    <span class="show-shortt-name-{{$row['unique_id']}}">{{ Str::limit($row['title'], 15, '..')}}</span>
                    <span style="word-break:break-all;" class="show-fulll-name-{{$row['unique_id']}} hidden">{{$row['title']}}</span>
                </td>
                <td>{{ $row['issue_type'] }}</td>
                <td>{{ $row['issue_category'] }}</td>
                <td>{{ ($row['is_unhandled']) ? "true":"false" }}</td>
                <td>{{ $row['project'] }}</td>
                <td>{{ $row['total_events'] }}</td>
                <td>{{ $row['total_user'] }}</td>
                <td>{{ $row['device_name'] }}</td>
                <td>{{ $row['os'] }}</td>
                <td>{{ $row['os_name'] }}</td>
                <td>{{ $row['release_version'] }}</td>
                <td>{{ date("d-m-y H:i:s", strtotime($row['first_seen'])) }}</td>
                <td>{{ date("d-m-y H:i:s", strtotime($row['last_seen'])) }}</td>
                <td>
                  <div class="d-flex align-items-center">
                    <select name="status" class="status-dropdown" data-id="{{$row['unique_id']}}">
                      <option value="">Select Status</option>
                      @foreach ($status as $stat)
                        <option value="{{$stat->id}}" {{$row['status_id'] == $stat->id ? 'selected' : ''}}>{{$stat->status_name}}</option>
                      @endforeach
                    </select>
                    <button type="button" data-id="{{ $row['unique_id']  }}" class="btn btn-image status-history-show p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>
                  </div>
                </td>
                <td>
                    <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn({{$row['unique_id']}})"><i class="fa fa-arrow-down"></i></button>
                </td>
            </tr>
            <tr class="action-btn-tr-{{$row['unique_id']}} d-none">
                <td class="font-weight-bold">Action</td>
                <td colspan="14" class="cls-actions">
                    <button style="padding:3px;" title="create quick task" type="button" class="btn btn-image d-inline create-quick-task " data-id="@if ($row) {{ $row['unique_id'] }} @endif"  data-category_title="row Page" data-title="@if ($row) {{$row['title'].' - SENTRY logs Page - '.$row['unique_id']  }} @endif"><i class="fa fa-plus" aria-hidden="true"></i></button>

                    <button style="padding-left: 0;padding-left:3px;" type="button" class="btn btn-image d-inline count-dev-customer-tasks" title="Show task history" data-id="@if ($row) {{ $row['unique_id'] }} @endif" data-category="{{ $row['unique_id'] }}"><i class="fa fa-info-circle"></i></button>
                </td>
            </tr>
    @endforeach
  </table>
</div>
<div id="status-create" class="modal fade in" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
      <h4 class="modal-title">Add Stauts</h4>
      <button type="button" class="close" data-dismiss="modal">×</button>
      </div>
      <form  method="POST" id="status-create-form">
        @csrf
        @method('POST')
          <div class="modal-body">
            <div class="form-group">
              {{ html()->label('Name', 'status_name')->class('form-control-label') }}
              {{ html()->text('status_name')->class('form-control')->required()->attribute('rows', 3) }}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary status-save-btn">Save</button>
          </div>
        </div>
      </form>
    </div>

  </div>
</div>
<div id="sentryShowFullTextModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Full text view</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body sentryShowFullTextBody">
            
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="create-quick-task" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?php echo route('task.create.multiple.task.shortcutpostman'); ?>" method="post">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Create Task</h4>
                </div>
                <div class="modal-body">

                    <input class="form-control" value="57" type="hidden" name="category_id" />
                    <input class="form-control" value="" type="hidden" name="category_title" id="category_title" />
                    <input class="form-control" type="hidden" name="site_id" id="site_id" />
                    <div class="form-group">
                        <label for="">Subject</label>
                        <input class="form-control" type="text" id="hidden-task-subject" name="task_subject" />
                    </div>
                    <div class="form-group">
                        <select class="form-control" style="width:100%;" name="task_type" tabindex="-1" aria-hidden="true">
                            <option value="0">Other Task</option>
                            <option value="4">Developer Task</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="repository_id">Repository:</label>
                        <br>
                        <select style="width:100%" class="form-control  " id="repository_id" name="repository_id">
                            <option value="">-- select repository --</option>
                            @foreach ($githubRepository as $repository)
                            <option value="{{ $repository->id }}">{{ $repository->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Details</label>
                        <input class="form-control text-task-development" type="text" name="task_detail" />
                    </div>

                    <div class="form-group">
                        <label for="">Cost</label>
                        <input class="form-control" type="text" name="cost" />
                    </div>

                    <div class="form-group">
                        <label for="">Assign to</label>
                        <select name="task_asssigned_to" class="form-control assign-to select2">
                            @foreach ($allUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Create Review Task?</label>
                        <div class="form-group">
                            <input type="checkbox" name="need_review_task" value="1" />
                        </div>
                    </div>
                    <!-- <div class="form-group">
                        <label for="">Websites</label>
                        <div class="form-group website-list row">
                           
                        </div>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-default create-task">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="dev_task_statistics" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Dev Task statistics</h2>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body" id="dev_task_statistics_content">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Task type</th>
                                <th>Task Id</th>
                                <th>Assigned to</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="preview-task-image" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered" style="table-layout: fixed">
                        <thead>
                            <tr>
                                <th style="width: 5%;">Sl no</th>
                                <th style=" width: 30%">Files</th>
                                <th style="word-break: break-all; width: 40%">Send to</th>
                                <th style="width: 10%">User</th>
                                <th style="width: 10%">Created at</th>
                                <th style="width: 15%">Action</th>
                            </tr>
                        </thead>
                        <tbody class="task-image-list-view">
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
@include("sentry-log.modal-status-color")
@include('sentry-log.sentry-logs-status-history')
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
               50% 50% no-repeat;display:none;">
    </div>
@endsection
@section("scripts")
<!-- <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script> -->
<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.validate.min.js') }} "></script>
<script type="text/javascript">
    function Showactionbtn(id){
        $(".action-btn-tr-"+id).toggleClass('d-none')
    }
  $(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#add_sentry_account").validate({
      rules: {
        project: "required",
        organization: "required",
        token: "required",
      },
      messages: {
        project: "Please enter project",
        organization: "Please enter organization",
        token: "Please enter token",
      },
      errorPlacement: function(error, element) {
        error.insertAfter(element);
      },  
      submitHandler: function (form) {
          var formdata = $('#add_sentry_account').serialize();
          $.ajax({
              type: "POST",
              url: "{{ route('sentry.adduser') }}",
              data: formdata,
              success: function(response) {
                $('#add_sentry_account').trigger("reset");
                if(response.code == 200){
                  toastr['success'](response.message, 'success');
                } else {
                  toastr['error'](response.message, 'error');
                }
                window.location.reload();
              }
          })
      }
    });

    $(document).on("click", ".status-save-btn", function(e) {
        e.preventDefault();
        var $this = $(this);
        $.ajax({
          url: "{{route('sentry.status.create')}}",
          type: "post",
          data: $('#status-create-form').serialize()
        }).done(function(response) {
          if (response.code = '200') {
            $('#loading-image').hide();
            $('#addPostman').modal('hide');
            toastr['success']('Status  Created successfully!!!', 'success');
            location.reload();
          } else {
            toastr['error'](response.message, 'error');
          }
        }).fail(function(errObj) {
          $('#loading-image').hide();
          toastr['error'](errObj.message, 'error');
        });
      });

    $(document).on("click", "#add_account" , function(){        
      $("#sentry_modal").modal('show');
      $("#project").val('');
      $("#organization").val('');
      $("#token").val('');
      $(".error").html('');
    });

    $(document).on("click", "#list_account" , function(){
      $("#sentry_account_listing_modal").modal('show');
      $.ajax({
          type: "POST",
          url: "{{ route('sentry.display-user') }}",        
          success: function(response) {
            $('#account_list').html( response );
          }
      })
    });

    $(document).on('click', '#refresh_logs', function(e){
       $.ajax({
            type: 'POST',
            url: "{{ route('sentry.refresh-logs') }}",
            beforeSend: function () {
                $("#loading-image").show();                
            },
            data: {
                _token: "{{ csrf_token() }}",                
            }
        }).done(function (response) {      
            $("#loading-image").hide();
            toastr['success'](response.message, 'success');
            $("#load_page").trigger('click');
        }).fail(function (response) {
            $("#loading-image").hide();
            console.log("Sorry, something went wrong");
        });
    });

    $(document).on('click', '#load_page', function(){
      url = "{{ route('sentry-log') }}";
      window.location.href = url;
    });

  });

  $(document).on('click', '.expand-row-msg', function() {
    $('#sentryShowFullTextModel').modal('toggle');
    $(".sentryShowFullTextBody").html("");
    var id = $(this).data('id');
    var name = $(this).data('name');
    var full = '.expand-row-msg .show-fulll-' + name + '-' + id;
    var fullText = $(full).html();
    $(".sentryShowFullTextBody").html(fullText);
  });

$(document).on('click', '.expand-row-msg', function() {
    var name = $(this).data('name');
    var id = $(this).data('id');
    console.log(name);
    var full = '.expand-row-msg .show-short-' + name + '-' + id;
    var mini = '.expand-row-msg .show-full-' + name + '-' + id;
    $(full).toggleClass('hidden');
    $(mini).toggleClass('hidden');
});

$(document).on('click', '.create-quick-task', function() {
    var $this = $(this);
    site = $(this).data("id");
    title = $(this).data("title");
    cat_title = $(this).data("category_title");
    development = $(this).data("development");
    if (!title || title == '') {
        toastr["error"]("Please add title first");
        return;
    }
    
    $("#create-quick-task").modal("show");

    var selValue = $(".save-item-select").val();
    if (selValue != "") {
        $("#create-quick-task").find(".assign-to option[value=" + selValue + "]").attr('selected',
            'selected')
        $('.assign-to.select2').select2({
            width: "100%"
        });
    }

    $("#hidden-task-subject").val(title);
    $(".text-task-development").val(development);
    $('#site_id').val(site);
});

$(document).on("click", ".create-task", function(e) {
    e.preventDefault();
    var form = $(this).closest("form");
    $.ajax({
        url: form.attr("action"),
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: form.serialize(),
        beforeSend: function() {
            $(this).text('Loading...');
            $("#loading-image").show();
        },
        success: function(response) {
            $("#loading-image").hide();
            if (response.code == 200) {
                form[0].reset();
                toastr['success'](response.message);
                $("#create-quick-task").modal("hide");
            } else {
                toastr['error'](response.message);
            }
        }
    }).fail(function(response) {
        toastr['error'](response.responseJSON.message);
    });
});

$(document).on("click", ".count-dev-customer-tasks", function() {

    var $this = $(this);
    // var user_id = $(this).closest("tr").find(".ucfuid").val();
    var site_id = $(this).data("id");
    var category_id = $(this).data("category");
    $("#site-development-category-id").val(category_id);
    $.ajax({
        type: 'get',
        url: 'sentry-log/countdevtask/' + site_id,
        dataType: "json",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            $("#loading-image").show();
        },
        success: function(data) {
            $("#dev_task_statistics").modal("show");
            var table = `<div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th width="4%">Tsk Typ</th>
                        <th width="4%">Tsk Id</th>
                        <th width="7%">Asg to</th>
                        <th width="12%">Desc</th>
                        <th width="12%">Sts</th>
                        <th width="33%">Communicate</th>
                        <th width="10%">Action</th>
                    </tr>`;
            for (var i = 0; i < data.taskStatistics.length; i++) {
                var str = data.taskStatistics[i].subject;
                var res = str.substr(0, 100);
                var status = data.taskStatistics[i].status;
                if (typeof status == 'undefined' || typeof status == '' || typeof status ==
                    '0') {
                    status = 'In progress'
                };
                table = table + '<tr><td>' + data.taskStatistics[i].task_type + '</td><td>#' +
                    data.taskStatistics[i].id +
                    '</td><td class="expand-row-msg" data-name="asgTo" data-id="' + data
                    .taskStatistics[i].id + '"><span class="show-short-asgTo-' + data
                    .taskStatistics[i].id + '">' + data.taskStatistics[i].assigned_to_name
                    .replace(/(.{6})..+/, "$1..") +
                    '</span><span style="word-break:break-all;" class="show-full-asgTo-' + data
                    .taskStatistics[i].id + ' hidden">' + data.taskStatistics[i]
                    .assigned_to_name +
                    '</span></td><td class="expand-row-msg" data-name="res" data-id="' + data
                    .taskStatistics[i].id + '"><span class="show-short-res-' + data
                    .taskStatistics[i].id + '">' + res.replace(/(.{7})..+/, "$1..") +
                    '</span><span style="word-break:break-all;" class="show-full-res-' + data
                    .taskStatistics[i].id + ' hidden">' + res + '</span></td><td>' + status +
                    '</td><td><div class="col-md-10 pl-0 pr-1"><textarea rows="1" style="width: 100%; float: left;" class="form-control quick-message-field input-sm" name="message" placeholder="Message"></textarea></div><div class="p-0"><button class="btn btn-sm btn-xs send-message" title="Send message" data-taskid="' +
                    data.taskStatistics[i].id +
                    '"><i class="fa fa-paper-plane"></i></button></div></td><td><button type="button" class="btn btn-xs load-communication-modal load-body-class" data-object="' +
                    data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i]
                    .id +
                    '" title="Load messages" data-dismiss="modal"><i class="fa fa-comments"></i></button>';
                table = table + '<a href="javascript:void(0);" data-task-type="' + data
                    .taskStatistics[i].task_type + '" data-id="' + data.taskStatistics[i].id +
                    '" class="delete-dev-task-btn btn btn-xs"><i class="fa fa-trash"></i></a>';
                table = table +
                    '<button type="button" class="btn btn-xs  preview-img pd-5" data-object="' +
                    data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i]
                    .id + '" data-dismiss="modal"><i class="fa fa-list"></i></button></td>';
                table = table + '</tr>';
            }
            table = table + '</table></div>';
            $("#loading-image").hide();
            $(".modal").css("overflow-x", "hidden");
            $(".modal").css("overflow-y", "auto");
            $("#dev_task_statistics_content").html(table);
        },
        error: function(error) {
            console.log(error);
            $("#loading-image").hide();
        }
    });


});

$(document).on('click', '.send-message', function() {
    var thiss = $(this);
    var data = new FormData();
    var task_id = $(this).data('taskid');
    var message = $(this).closest('tr').find('.quick-message-field').val();
    var mesArr = $(this).closest('tr').find('.quick-message-field');
    $.each(mesArr, function(index, value) {
        if ($(value).val()) {
            message = $(value).val();
        }
    });

    data.append("task_id", task_id);
    data.append("message", message);
    data.append("status", 1);

    if (message.length > 0) {
        if (!$(thiss).is(':disabled')) {
            $.ajax({
                url: '/whatsapp/sendMessage/task',
                type: 'POST',
                "dataType": 'json', // what to expect back from the PHP script, if anything
                "cache": false,
                "contentType": false,
                "processData": false,
                "data": data,
                beforeSend: function() {
                    $(thiss).attr('disabled', true);
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
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

$(document).on("click", ".delete-dev-task-btn", function() {
    var x = window.confirm("Are you sure you want to delete this ?");
    if (!x) {
        return;
    }
    var $this = $(this);
    var taskId = $this.data("id");
    var tasktype = $this.data("task-type");
    if (taskId > 0) {
        $.ajax({
            beforeSend: function() {
                $("#loading-image").show();
            },
            type: 'get',
            url: "/site-development/deletedevtask",
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: taskId,
                tasktype: tasktype
            },
            dataType: "json"
        }).done(function(response) {
            $("#loading-image").hide();
            if (response.code == 200) {
                $this.closest("tr").remove();
            }
        }).fail(function(response) {
            $("#loading-image").hide();
            alert('Could not update!!');
        });
    }

});

$(document).on('click', '.preview-img', function(e) {
    e.preventDefault();
    id = $(this).data('id');
    if (!id) {
        alert("No data found");
        return;
    }
    $.ajax({
        url: "/task/preview-img-task/" + id,
        type: 'GET',
        success: function(response) {
            $("#preview-task-image").modal("show");
            $(".task-image-list-view").html(response);
            initialize_select2()
        },
        error: function() {}
    });
});

$(document).on("click", ".send-to-sop-page", function() {
    var id = $(this).data("id");
    var task_id = $(this).data("media-id");

    $.ajax({
        url: '/task/send-sop',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        dataType: "json",
        data: {
            id: id,
            task_id: task_id
        },
        beforeSend: function() {
            $("#loading-image").show();
        },
        success: function(response) {
            $("#loading-image").hide();
            if (response.success) {
                toastr["success"](response.message);
            } else {
                toastr["error"](response.message);
            }

        },
        error: function(error) {
            toastr["error"];
        }

    });
});

$(document).on('click', '.previewDoc', function() {
    $('#previewDocSource').attr('src', '');
    var docUrl = $(this).data('docurl');
    var type = $(this).data('type');
    var type = jQuery.trim(type);
    if (type == "image") {
        $('#previewDocSource').attr('src', docUrl);
    } else {
        $('#previewDocSource').attr('src', "https://docs.google.com/gview?url=" + docUrl +
            "&embedded=true");
    }
    $('#previewDoc').modal('show');
});

$(document).on("click", ".btn-show-request-url", function () {
    $(".add_more_urls_div").toggle();
});

$(document).on('input', '#searchInput', function(e) {
    e.preventDefault();
    const searchInput = $(this).val();
    const urlInputs = $(".urlInput");

    urlInputs.each(function() {
        const urlInput = $(this);
        const url = urlInput.val();
        if (url.includes(searchInput)) {
            urlInput.removeClass("hidden");
        } else {
            urlInput.addClass("hidden");
        }
    });
});

$(document).on("change", ".status-dropdown", function(e) {
  e.preventDefault();
  var SantryLogId = $(this).data('id');
  var selectedStatus = $(this).val();
  console.log("Dropdown data-id:", SantryLogId);
  console.log("Selected status:", selectedStatus);


  // Make an AJAX request to update the status
  $.ajax({
    url: 'sentry-log/updatestatus',
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    data: {
      SantryLogId: SantryLogId,
      selectedStatus: selectedStatus
    },
    success: function(response) {
      toastr['success']('Status Change successfully!!!', 'success');
      console.log(response);
    },
    error: function(xhr, status, error) {
      // Handle the error here
      console.error(error);
    }
  });
});

$(document).on('click', '.status-history-show', function() {
    var id = $(this).attr('data-id');
    $.ajax({
        method: "GET",
        url: `{{ route('sentry.status.histories', [""]) }}/` + id,
        dataType: "json",
        success: function(response) {
            if (response.status) {
                var html = "";
                $.each(response.data, function(k, v) {
                    html += `<tr>
                                <td> ${k + 1} </td>
                                <td> ${(v.old_value != null) ? v.old_value.status_name : ' - ' } </td>
                                <td> ${(v.new_value != null) ? v.new_value.status_name : ' - ' } </td>
                                <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                <td> ${v.created_at} </td>
                            </tr>`;
                });
                $("#sentry-logs-status-histories-list").find(".sentry-logs-status-histories-list-view").html(html);
                $("#sentry-logs-status-histories-list").modal("show");
            } else {
                toastr["error"](response.error, "Message");
            }
        }
    });
});
</script>
@endsection