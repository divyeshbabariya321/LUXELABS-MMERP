@extends('layouts.app')

@section('favicon' , 'task.png')

@section('title', 'Tasks List')

@section("styles")
  <style>
    #coupon_rules_table_length select{
      height: 14px;
    }
  </style>
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')
<div class="col-md-12">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Tasks List</h2>            
        </div>
    </div>
    <div class="row mb-3">
      <div class="col-md-10 col-sm-12">
        <form action="{{ route('task.list') }}" method="GET" class="form-inline align-items-start" id="searchForm">
          <div class="row full-width" style="width: 100%;">
            <div class="col-md-2 col-sm-12 pd-2">
              <div class="form-group cls_task_subject">
                <label for="" class="mb-2 mt-3">Search</label>
                <input type="text" name="term" placeholder="Search Task Id / Dev Task Id" id="task_search" class="form-control input-sm" value="{{ !empty($_GET['term'])? $_GET['term'] : '' }}">
              </div>
            </div>
            <div class="col-md-2 col-sm-12 pd-2">
              <div class="form-group cls_task_subject">
                <label for="" class="mb-2 mt-3">Task Subject</label>
                <input type="text" class="form-control input-sm" name="task_subject" placeholder="Task Subject" id="task_subject" value="{{ !empty($_GET['task_subject'])? $_GET['task_subject'] : '' }}" />
                @if ($errors->has('task_subject'))
                  <div class="alert alert-danger">{{$errors->first('task_subject')}}</div>
                @endif
              </div>
            </div>
            <div class="col-md-2 col-sm-12">
              <div class="form-group mr-3">
                <label for="" class="mb-2 mt-3">Assigned To</label>
                <select class="form-control" name="user[]" id="user_list_id" multiple>
                  @foreach ($users as $index => $name)
                    <option value="{{ $index }}" {{ isset($user) && in_array($index, $user) ? 'selected' : '' }}>{{ $name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-md-2 col-sm-12 pr-0">
              <div class='form-group mr-3'>
                <div style="display: flex">
                  <label for="csv-date-range" class="col-form-label">Created Date: &nbsp;&nbsp;</label>
                  <input type="checkbox" name="is-filter-created" id="is-filter-created" @if (Request::get('is-filter-created', '') === 'on' ) checked @endif style="margin: 0px;">
                </div> 
                <input type="hidden" name="start_date" value="{{ Request::get('start_date', '') }}"/>
                <input type="hidden" name="end_date" value="{{ Request::get('end_date', '') }}" />
                <input placeholder='Select Date Range' type='text' class="form-control created-at-filter" />
              </div>
            </div>

            <div class="col-md-2 col-sm-12 pr-0">
                <div class='form-group mr-3'>
                  <div style="display: flex">
                    <label for="csv-date-range" class="col-form-label">Tracked Date: &nbsp;&nbsp;</label>
                    <input type="checkbox" name="is-filter-tracked" id="is-filter-tracked" @if (Request::get('is-filter-tracked', '') === 'on' ) checked @endif style="margin: 0px;">
                </div>
                <input type="hidden" name="start_date_tracked" value="{{ Request::get('start_date_tracked', '') }}"/>
                <input type="hidden" name="end_date_tracked" value="{{ Request::get('end_date_tracked', '') }}" />
                <input placeholder='Select Date Range' type='text' class="form-control tracked-at-filter" />
              </div>
            </div>
            <div style="display: flex; margin-top: 35px">
              <div class="pl-0 pt-2"><button type="submit" class="btn btn-image"><img src="{{asset('/images/search.png')}}" /></button></div>
                <div><button type="button" class="btn btn-success excel-export-btn">Export</button></div>
            </div>
          </div>
        </form>
      </div>
    </div>

    @include('partials.flash_messages')

    @php
      $categories = \App\Http\Controllers\TaskCategoryController::getAllTaskCategory();
    @endphp

    <div class="tab-content ">
      <div class="tab-pane active mt-3" id="pending-tasks">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
            <tr>
              <th width="5%">ID</th>
              <th width="10%">Date</th>
              <th width="10%">Category</th>
              <th width="25%">Task</th>
              <th width="10%">Assigned To</th>
              <th width="10%">Lead</th>
              <th width="10%">Lead 2</th>
              <th width="10%">Status</th>
              <th width="10%">Action</th>
            </tr>
            @foreach ($pending_tasks as $task)
              @php
              $status_color = \App\Helpers::getFirstTaskStatusByID($task->status);
              @endphp
              <tr style="background-color: {{$status_color->task_color ?? ""}}!important;">
                <td>{{ $task->id }}</td>
                <td>{{ \Carbon\Carbon::parse($task->created_at)->format('d-m H:i') }}</td>
                <td>{{ $categories[$task->category] ?? '' }}</td>
                <td class="task-subject" data-subject="{{$task['task_subject'] ? $task['task_subject'] : 'Task Details'}}" data-details="{{$task['task_details']}}" data-switch="0">
                  <span class="task-container">
                    {{ $task['task_subject'] ?? 'Task Details' }}
                  </span>
                  <span class="task-container"></span>
                  <br>
                  <span class="text-danger">
                    {{ $task->remarks()->first()->remark ?? '' }}
                  </span>

                </td>

                <td>
                  <select id="assign_to" class="form-control assign-user select2" data-id="{{$task->id}}" data-lead="1" name="master_user_id" id="user_{{$task->id}}">
                    <option value="">Select...</option>
                    <?php $masterUser = isset($task->assign_to) ? $task->assign_to : 0; ?>
                    @foreach($users as $id=>$name)
                      @if( $masterUser == $id )
                        <option value="{{$id}}" selected>{{ $name }}</option>
                      @else
                        <option value="{{$id}}">{{ $name }}</option>
                      @endif
                    @endforeach
                  </select>
                {{--{{ $users[$task->assign_to] ?? 'Unknown User' }}--}}
                </td>
                <td>
                  @if(auth()->user()->isAdmin()  || $isTeamLeader)
                    <select id="master_user_id" class="form-control assign-master-user select2" data-id="{{$task->id}}" data-lead="1" name="master_user_id" id="user_{{$task->id}}">
                      <option value="">Select...</option>
                      <?php $masterUser = isset($task->master_user_id) ? $task->master_user_id : 0; ?>
                      @foreach($users as $id=>$name)
                        @if( $masterUser == $id )
                          <option value="{{$id}}" selected>{{ $name }}</option>
                        @else
                          <option value="{{$id}}">{{ $name }}</option>
                        @endif
                      @endforeach
                    </select>
                  @else
                    @if($task->master_user_id)
                      @if(isset($users[$task->master_user_id]))
                        <p>{{$users[$task->master_user_id]}}</p>
                      @else
                        <p>-</p>
                      @endif
                    @endif
                  @endif
                </td>

                <td>
                  @if(auth()->user()->isAdmin()  || $isTeamLeader)
                    <select id="master_user_id" class="form-control assign-master-user select2" data-id="{{$task->id}}" data-lead="2" name="master_user_id" id="user_{{$task->id}}">
                      <option value="">Select...</option>
                      <?php $masterUser = isset($task->second_master_user_id) ? $task->second_master_user_id : 0; ?>
                      @foreach($users as $id=>$name)
                        @if( $masterUser == $id )
                          <option value="{{$id}}" selected>{{ $name }}</option>
                        @else
                          <option value="{{$id}}">{{ $name }}</option>
                        @endif
                      @endforeach
                    </select>
                  @else
                    @if($task->second_master_user_id)
                      @if(isset($users[$task->second_master_user_id]))
                        <p>{{$users[$task->second_master_user_id]}}</p>
                      @else
                        <p>-</p>
                      @endif
                    @endif
                  @endif
                </td>
                <td>
                  <select id="master_user_id" class="form-control change-task-status select2" data-id="{{$task->id}}" name="master_user_id" id="user_{{$task->id}}">
                    <option value="">Select...</option>
                    <?php $masterUser = isset($task->master_user_id) ? $task->master_user_id : 0; ?>
                    @foreach($taskstatus as $index => $status)
                      @if(auth()->user()->isAdmin() AND $status->name == 'Done')
                        @continue
                      @endif
                      @if( $status->id == $task->status )
                        <option value="{{$status->id}}" selected>{{ $status->name }}</option>
                      @else
                        <option value="{{$status->id}}">{{ $status->name }}</option>
                      @endif
                    @endforeach
                  </select>
                </td>
                <td>
                  <a href class="add-task" data-toggle="modal" data-target="#addRemarkModal" data-id="{{ $task->id }}">Add</a>
                  <span> | </span>
                  <a href class="view-remark" data-toggle="modal" data-target="#viewRemarkModal" data-id="{{ $task->id }}">View</a>
                  <br>
                  <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='task' data-id="{{ $task->id }}" title="Load messages">
                    <img src="/images/chat.png" alt="" style="cursor: nwse-resize;">
                  </button>
                  <button class="btn btn-xs btn-image view-google-drive-button p-2" type="button" title="View Uploaded Files" data-object='task' data-task_id="{{ $task->id }}">
                    <img src="/images/google-drive.png" style="cursor: default; width: 10px;">
                  </button>
                  <button class="btn btn-xs btn-image  mt-2 show-google-document" title="Show created document" data-object='task' data-id="{{ $task->id }}">
                    <i class="fa fa-list" aria-hidden="true"></i>
                  </button>
                </td>
              </tr>
            @endforeach
            @foreach ($developer_tasks as $task)
              @php
                $task_color = \App\TaskStatus::where('name', $task->status)->value('task_color');
              @endphp
                <tr style="background-color: {{$task_color}}!important;">
                  <td>{{ $task->id }} Devtask</td>
                  <td>{{ \Carbon\Carbon::parse($task->created_at)->format('d-m H:i') }}</td>
                  <td></td>
                  <td class="task-subject" data-subject="{{$task['subject'] ? $task['subject'] : 'Task Details'}}" data-details="{{$task['task']}}" data-switch="0">
                  <span class="task-container">
                    {{ $task['subject'] ?? 'Task Details' }}
                  </span>
                    <span class="task-container"></span>
                    <br>
                    <span class="text-danger">
{{--                    {{ $task->remarks()->first()->remark ?? '' }}--}}
                  </span>

                  </td>
                  <td>
                    <select id="assign_to" class="form-control dev-assign-user select2" data-id="{{$task->id}}" data-lead="1" name="master_user_id" id="user_{{$task->id}}">
                      <option value="">Select...</option>
                      <?php $masterUser = isset($task->assigned_to) ? $task->assigned_to : 0; ?>
                      @foreach($users as $id=>$name)
                        @if( $masterUser == $id )
                          <option value="{{$id}}" selected>{{ $name }}</option>
                        @else
                          <option value="{{$id}}">{{ $name }}</option>
                        @endif
                      @endforeach
                    </select>
                    {{--{{ $users[$task->assign_to] ?? 'Unknown User' }}--}}
                  </td>

                  <td>
                    @if(auth()->user()->isAdmin()  || $isTeamLeader)
                      <select id="master_user_id" class="form-control dev-assign-master-user select2" data-id="{{$task->id}}" data-lead="1" name="master_user_id" id="user_{{$task->id}}">
                        <option value="">Select...</option>
                        <?php $masterUser = isset($task->master_user_id) ? $task->master_user_id : 0; ?>
                        @foreach($users as $id=>$name)
                          @if( $masterUser == $id )
                            <option value="{{$id}}" selected>{{ $name }}</option>
                          @else
                            <option value="{{$id}}">{{ $name }}</option>
                          @endif
                        @endforeach
                      </select>
                    @else
                      @if($task->master_user_id)
                        @if(isset($users[$task->master_user_id]))
                          <p>{{$users[$task->master_user_id]}}</p>
                        @else
                          <p>-</p>
                        @endif
                      @endif
                    @endif
                  </td>

                  <td>
                    @if(auth()->user()->isAdmin()  || $isTeamLeader)
                      <select id="master_user_id" class="form-control assign-team-lead select2" data-id="{{$task->id}}" data-lead="2" name="master_user_id" id="user_{{$task->id}}">
                        <option value="">Select...</option>
                        <?php $masterUser = isset($task->team_lead_id) ? $task->team_lead_id : 0; ?>
                        @foreach($users as $id=>$name)
                          @if( $masterUser == $id )
                            <option value="{{$id}}" selected>{{ $name }}</option>
                          @else
                            <option value="{{$id}}">{{ $name }}</option>
                          @endif
                        @endforeach
                      </select>
                    @else
                      @if($task->second_master_user_id)
                        @if(isset($users[$task->second_master_user_id]))
                          <p>{{$users[$task->second_master_user_id]}}</p>
                        @else
                          <p>-</p>
                        @endif
                      @endif
                    @endif
                  </td>
                  <td>
                    <select id="master_user_id" class="form-control dev-change-task-status select2" data-id="{{$task->id}}" name="master_user_id" id="user_{{$task->id}}">
                      <option value="">Select...</option>
                      <?php $masterUser = isset($task->master_user_id) ? $task->master_user_id : 0; ?>
                      @foreach($taskstatus as $index => $status)
                        @if(auth()->user()->isAdmin() AND $status->name == 'Done')
                          @continue
                        @endif
                        @if( $status->name == $task->status )
                          <option value="{{$status->name}}" selected>{{ $status->name }}</option>
                        @else
                          <option value="{{$status->name}}">{{ $status->name }}</option>
                        @endif
                      @endforeach
                    </select>
                  </td>
                  <td>
                    <a href class="add-task" data-toggle="modal" data-target="#devaddRemarkModal" data-id="{{ $task->id }}">Add</a>
                    <span> | </span>
                    <a href class="devview-remark" data-toggle="modal" data-target="#devviewRemarkModal" data-id="{{ $task->id }}">View</a>
                    <br>
                    <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='developer_task' data-id="{{ $task->id }}" title="Load messages">
                      <img src="/images/chat.png" alt="" style="cursor: nwse-resize;">
                    </button>
                    <button class="btn btn-xs btn-image view-google-drive-button p-2" type="button" title="View Uploaded Files" data-object='developer_task' data-task_id="{{ $task->id }}">
                      <img src="/images/google-drive.png" style="cursor: default; width: 10px;">
                    </button>
                    <button class="btn btn-xs btn-image  mt-2 show-google-document" title="Show created document" data-object='developer_task' data-id="{{ $task->id }}">
                      <i class="fa fa-list" aria-hidden="true"></i>
                    </button>
                  </td>
                </tr>
              @endforeach
          </table>
        </div>
        {!! $pending_tasks->appends(Request::except('page'))->links() !!}
      </div>
    </div>
</div>
    <!-- Modal -->
    <div id="addRemarkModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Add New Remark</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>

          </div>
          <div class="modal-body">
            <form id="add-remark">
              <input type="hidden" name="id" value="">
              <textarea rows="1" name="remark" class="form-control"></textarea>
              <button type="button" class="btn btn-secondary mt-2" id="addRemarkButton">Add Remark</button>
          </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>

      </div>
    </div>

    <div id="devaddRemarkModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add New Remark</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>

      </div>
      <div class="modal-body">
        <form id="add-remark">
          <input type="hidden" name="id" value="">
          <textarea rows="1" name="devremark" class="form-control"></textarea>
          <button type="button" class="btn btn-secondary mt-2" id="devaddRemarkButton">Add Remark</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
    </div>

    <!-- Modal -->
    <div id="viewRemarkModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">View Remark</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>

          </div>
          <div class="modal-body">
            <div id="remark-list">

            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>

      </div>
    </div>
    <div id="devviewRemarkModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">View Remark</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>

          </div>
          <div class="modal-body">
            <div id="dev-remark-list">

            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>

      </div>
    </div>
    @include("task-module.partials.modal-status-color")

    <div id="displayGoogleFileUpload" class="modal fade" role="dialog">
      <div class="modal-dialog modal-xl">
    
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Google Drive Uploaded files</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
    
          <div class="modal-body">
            <div class="table-responsive mt-3">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Filename</th>
                    <th>File Creation Date</th>
                    <th>URL</th>
                    <th>Remarks</th>
                  </tr>
                </thead>
                <tbody id="googleDriveUploadedData">
                  
                </tbody>
              </table>
            </div>
           </div>
    
    
        </div>
    
      </div>
    </div>
    <div id="googleTaskDocumentModel" class="modal fade" role="dialog" style="display: none;">
      <div class="modal-dialog modal-lg">
  
          <!-- Modal content-->
          <div class="modal-content">
              <div class="modal-header">
                  <h4 class="modal-title">Google Documents list</h4>
                  <button type="button" class="close" data-dismiss="modal">×</button>
              </div>
              <div class="modal-body">
                  <table class="table table-sm table-bordered">
                      <thead>
                      <tr>
                          <th width="5%">ID</th>
                          <th width="5%">File Name</th>
                          <th width="5%">Created Date</th>
                          <th width="10%">URL</th>
                      </tr>
                      </thead>
                      <tbody>
  
                      </tbody>
                  </table>
              </div>
          </div>
  
      </div>
  </div>
@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script type="text/javascript">
    $(document).on('click', '.task-subject', function() {
      if ($(this).data('switch') == 0) {
        $(this).find('.task-container').text($(this).data('details'));
        $(this).data('switch', 1);
      } else {
        $(this).find('.task-container').text($(this).data('subject'));
        $(this).data('switch', 0);
      }
    });

    $(document).on('change', '.assign-user', function() {
      let id = $(this).attr('data-id');
      let userId = $(this).val();
      if (userId == '') {
        return;
      }
      $.ajax({
        url: "{{route('task.AssignTaskToUser')}}",
        data: {
          user_id: userId,
          issue_id: id
        },
        success: function() {
          toastr["success"]("User assigned successfully!", "Message")
        },
        error: function(error) {
          toastr["error"](error.responseJSON.message, "Message")
        }
      });
    });

    $(document).on('change', '.dev-assign-user', function() {
      let id = $(this).attr('data-id');
      let userId = $(this).val();

      if (userId == '') {
        return;
      }

      $.ajax({
        url: "{{action([\App\Http\Controllers\DevelopmentController::class, 'assignUser'])}}",
        data: {
          assigned_to: userId,
          issue_id: id
        },
        success: function() {
          toastr["success"]("User assigned successfully!", "Message")
        },
        error: function(error) {
          toastr["error"](error.responseJSON.message, "Message")

        }
      });

    });

    $(document).on('change', '.change-task-status', function() {
      let id = $(this).attr('data-id');
      let status = $(this).val();
      $.ajax({
        url: "{{route('task.change.status')}}",
        type: "POST",
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        dataType: "json",
        data: {
          'task_id': id,
          'status': status
        },
        success: function(response) {
          toastr["success"](response.message, "Message")
        },
        error: function(error) {
          toastr["error"](error.responseJSON.message, "Message")
        }
      });
    });

    $(document).on('change', '.assign-master-user', function() {
      let id = $(this).attr('data-id');
      let lead = $(this).attr('data-lead');
      let userId = $(this).val();
      if (userId == '') {
        return;
      }
      $.ajax({
        url: "{{action([\App\Http\Controllers\TaskModuleController::class, 'assignMasterUser'])}}",
        data: {
          master_user_id: userId,
          issue_id: id,
          lead: lead
        },
        success: function() {
          toastr["success"]("Master User assigned successfully!", "Message")
        },
        error: function(error) {
          toastr["error"](error.responseJSON.message, "Message")
        }
      });
    });

    $(document).on('change', '.dev-assign-master-user', function() {
      let id = $(this).attr('data-id');
      let userId = $(this).val();

      if (userId == '') {
        return;
      }

      $.ajax({
        url: "{{action([\App\Http\Controllers\DevelopmentController::class, 'assignMasterUser'])}}",
        data: {
          master_user_id: userId,
          issue_id: id
        },
        success: function() {
          toastr["success"]("Master User assigned successfully!", "Message")
        },
        error: function(error) {
          toastr["error"](error.responseJSON.message, "Message")

        }
      });

    });

    $(document).on('change', '.assign-team-lead', function() {
      let id = $(this).attr('data-id');
      let userId = $(this).val();
      console.log(id);
      console.log(userId);

      if (userId == '') {
        return;
      }

      $.ajax({
        url: "{{action([\App\Http\Controllers\DevelopmentController::class, 'assignTeamlead'])}}",
        data: {
          team_lead_id: userId,
          issue_id: id
        },
        success: function() {
          toastr["success"]("Team lead assigned successfully!", "Message")
        },
        error: function(error) {
          toastr["error"](error.responseJSON.message, "Message")

        }
      });
    });

    $(document).on('change', '.dev-change-task-status', function() {
      var taskId = $(this).data("id");
      var status = $(this).val();
      $.ajax({
        url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'changeTaskStatus']) }}",
        type: 'POST',
        data: {
          task_id: taskId,
          _token: "{{csrf_token()}}",
          status: status
        },
        success: function() {
          toastr['success']('Status Changed successfully!')
        }
      });
    });


    $(document).on('click', '.task-complete', function() {
      var thiss = $(this);
      var id = $(this).data('id');

      $.ajax({
        type: "GET",
        url: "{{ url('/task/complete') }}/" + id,
        beforeSend: function() {
          $(thiss).text('Loading...');
        }
      }).done(function() {
        $(thiss).closest('tr').remove();
      }).fail(function(response) {
        $(thiss).text('Complete');

        alert('Could not complete the task');
        console.log(response);
      });
    });

    $(document).ready(function() {
       $(".select-multiple").multiselect();
       
       $('.instruction-start-time').datetimepicker({
         format: 'YYYY-MM-DD HH:mm'
       });

       $("#user_list_id").select2();
    });

    $('.add-task').on('click', function(e) {
      e.preventDefault();
      var id = $(this).data('id');
      $('#add-remark input[name="id"]').val(id);
    });

    $('#addRemarkButton').on('click', function() {
      var id = $('#add-remark input[name="id"]').val();
      var remark = $('#add-remark textarea[name="remark"]').val();

      $.ajax({
          type: 'POST',
          headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          url: '{{ route('task.addRemark') }}',
          data: {
            id:id,
            remark:remark,
            module_type: 'task'
          },
      }).done(response => {
          alert('Remark Added Success!')
          window.location.reload();
      }).fail(function(response) {
        console.log(response);
      });
    });

    $('#devaddRemarkButton').on('click', function() {
      var id = $('#add-remark input[name="id"]').val();
      var remark = $('#add-remark textarea[name="devremark"]').val();

      $.ajax({
        type: 'GET',
        headers: {
          'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        },
        url: '{{ route('task.devgettaskremark') }}',
        data: {
          task_id:id,
          remark:remark,
          type: 'Dev-task',
          status: 'insert'
        },
      }).done(response => {
        alert('Remark Added Success!')
        window.location.reload();
      }).fail(function(response) {
        console.log(response);
      });
    });


    $(".view-remark").click(function () {
      var id = $(this).attr('data-id');

        $.ajax({
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: '{{ route('task.gettaskremark') }}',
            data: {
              id:id,
              module_type: "task"
            },
        }).done(response => {
            var html='';

            $.each(response, function( index, value ) {
              html+=' <p> '+value.remark+' <br> <small>By ' + value.user_name + ' updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
              html+"<hr>";
            });
            $("#viewRemarkModal").find('#remark-list').html(html);
        });
    });

    $(".devview-remark").click(function () {
      var id = $(this).attr('data-id');

        $.ajax({
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: '{{ route('task.devgettaskremark') }}',
            data: {
              task_id:id,
              status:'view',
              type: "Dev-task"
            },
        }).done(response => {
          var html='';

          $.each(response.remark, function( index, value ) {
            console.log(value.created_at);

            html+=' <p> '+value.remark+' <br> <small>updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
            html+"<hr>";
          });
            $("#devviewRemarkModal").find('#dev-remark-list').html(html);
        });
    });

    $(document).on('click', '.verify-btn', function(e) {
      e.preventDefault();

      var thiss = $(this);
      var id = $(this).data('id');

      $.ajax({
        type: "POST",
        url: "{{ route('instruction.verify') }}",
        data: {
          _token: "{{ csrf_token() }}",
          id: id
        },
        beforeSend: function() {
          $(thiss).text('Verifying...');
        }
      }).done(function(response) {
        $(thiss).parent().html('<span class="badge">Verified</span>');

        $(thiss).remove();
      }).fail(function(response) {
        $(thiss).text('Verify');
        console.log(response);
        alert('Could not verify the instruction!');
      });
    });

    $(document).on('click', '.instruction-edit-button', function() {
      var id = $(this).data('id');
      var start = $(this).data('start');
      var end = $(this).data('end');
      var url = "{{ url('instruction') }}/" + id;

      $('#instructionEditForm').attr('action', url);

      $('#instruction_start_time').val(start.length > 0 ? start : moment().format('YYYY-MM-DD HH:mm'));
      $('#instruction_end_time').val(end.length > 0 ? end : moment().format('YYYY-MM-DD HH:mm'));
    });

    var instructions_array = [];

    $(document).on('click', '.select-instruction', function() {
      var id = $(this).data('id');

      if ($(this).prop('checked')) {
        instructions_array.push(id);
      } else {
        instructions_array.splice(instructions_array.indexOf(id), 1);
      }

      console.log(instructions_array);
    });

    $(document).on('click', '#select-all-instructions', function() {
      if ($(this).prop('checked')) {
        $('.select-instruction').each(function(index, instruction) {
          $(instruction).prop('checked', true);
          var id = $(instruction).data('id');

          instructions_array.push(id);
        });
      } else {
        $('.select-instruction').each(function(index, instruction) {
          $(instruction).prop('checked', false);

          instructions_array = [];
        });
      }
    });

    $('#verifySelectedButton').on('click', function(e) {
      e.preventDefault();

      if (instructions_array.length > 0) {
        $('#selected_instructions').val(JSON.stringify(instructions_array));
      } else {
        alert('Please select atleast 1 instruction');

        return;
      }

      $('#verifySelectedForm').submit();
    });

    var object = "";
    $(document).on("click", ".view-google-drive-button", function (e) {
        e.preventDefault();
        let task_id = $(this).data("task_id");
        object = $(this).data("object");
        
        if(object == "task") {
          route = "{{route('task.files.record')}}";
          data = {
            task_id
          }
        } else {
          route = "{{url('google-drive-screencast/task-files')}}/"+task_id;
          data = {}
        }
        

        $.ajax({
            type: "get",
            url: route,
            data: data,
            success: function (response) {
              if(object == "task") {
                $("#googleDriveUploadedData").html(response.data);
              } else {
                $("#googleDriveUploadedData").html(response);
              }
              $("#displayGoogleFileUpload").modal("show")
            },
            error: function (response) {
                toastr['error']("Something went wrong!");
            }
        });
    });
    $(document).on('click', ".show-google-document", function () {
        let task_id = $(this).data('id');
        object = $(this).data('object');
        if(task_id != "") {
            $.ajax({
                type: "GET",
                url: "{{route('google-docs.task.show')}}",
                data: {
                    task_id,
                    task_type: (object == 'task') ? "TASK" : "DEVTASK"
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function (response) {
                    $("#loading-image").hide();
                    $("#googleTaskDocumentModel tbody").html(response.data);
                    $("#googleTaskDocumentModel").modal('show');
                },
                error: function(response) {
                    toastr["error"]("Something went wrong!");
                    $("#loading-image").hide();
                }
            });
        } else {
            toastr["error"]("Task id not found.");
        }
      });

    $(function() {
      const drp = $(".created-at-filter").daterangepicker({
        timePicker: true,
        startDate: new Date("{{ \Carbon\Carbon::create(Request::get('start_date', now()->startOfDay() ))->format('Y-m-d H:i:s') }}"),
        endDate: new Date("{{ \Carbon\Carbon::create(Request::get('end_date', now()->endOfDay() ))->format('Y-m-d H:i:s') }}"),
        opens: 'left',
        locale: { cancelLabel: 'clear' },
      }, function(start, end, label) {
        console.log(start.format('YYYY-MM-DD HH:mm:ss'),end.format('YYYY-MM-DD HH:mm:ss'));
          $("input[name='start_date']").val(start.format('YYYY-MM-DD HH:mm:ss'));
          $("input[name='end_date']").val(end.format('YYYY-MM-DD HH:mm:ss'));
      });

      const drp2 = $(".tracked-at-filter").daterangepicker({
        timePicker: true,
        startDate: new Date("{{ \Carbon\Carbon::create(Request::get('start_date_tracked', now()->startOfDay() ))->format('Y-m-d H:i:s') }}"),
        endDate: new Date("{{ \Carbon\Carbon::create(Request::get('end_date_tracked', now()->endOfDay() ))->format('Y-m-d H:i:s') }}"),
        opens: 'left',
        locale: { cancelLabel: 'clear' },
      }, function(start, end, label) {
        console.log(start.format('YYYY-MM-DD HH:mm:ss'),end.format('YYYY-MM-DD HH:mm:ss'));
          $("input[name='start_date_tracked']").val(start.format('YYYY-MM-DD HH:mm:ss'));
          $("input[name='end_date_tracked']").val(end.format('YYYY-MM-DD HH:mm:ss'));
      });
    });


    $(".created-at-filter").on('cancel.daterangepicker', function(ev, picker) {
      $(".created-at-filter").val('');
    });

    $(".tracked-at-filter").on('cancel.daterangepicker', function(ev, picker) {
      $(".tracked-at-filter").val('');
    });

    $(".excel-export-btn").click(function(e) {
        e.preventDefault();
        const form = $(this).closest('form');

        window.open(`${window.location.pathname}?${form.serialize()}&export_excel=1`);
    });

    </script>
    @endsection

@push('scripts')
    <script type="text/javascript" src="{{ mix('webpack-dist/js/pages/development/development-list.js') }} "></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endpush
