@extends('layouts.app')

@section('favicon', 'task.png')

@section('title', 'Tasks')

@section('styles')

    {{-- <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}

    <link rel="stylesheet" href="{{ mix('webpack-dist/css/bootstrap-datetimepicker.min.css') }} ">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />

    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        .communication_th {
            width: 10% !important;
            min-width: 250px !important;
        }

        #message-wrapper {
            height: 450px;
            overflow-y: scroll;
        }

        .dis-none {
            display: none;
        }

        .pd-5 {
            padding: 3px;
        }

        .cls_task_detailstextarea {
            height: 30px !important;
        }

        .cls_remove_allpadding {
            padding-right: 0px !important;
            padding-left: 0px !important;
        }

        .cls_right_allpadding {
            padding-right: 0px !important;
        }

        .cls_left_allpadding {
            padding-left: 0px !important;
        }

        #addNoteButton {
            margin-top: 2px;
        }

        #saveNewNotes {
            margin-top: 2px;
        }

        .col-xs-12.col-md-2 {
            padding-left: 5px !important;
            padding-right: 5px !important;
            height: 38px;
        }

        .cls_task_subject {
            padding-left: 9px;
        }

        #recurring-task .col-xs-12.col-md-6 {
            padding-left: 5px !important;
            padding-right: 5px !important;
        }

        #appointment-container .col-xs-12.col-md-6 {
            padding-left: 5px !important;
            padding-right: 5px !important;
        }

        #taskCreateForm .form-group {
            margin-bottom: 0px;
        }

        .cls_action_box .btn-image img {
            width: 12px !important;
        }

        .cls_action_box .btn.btn-image {
            padding: 2px;
        }

        .btn.btn-image {
            padding: 5px 3px;
        }

        .td-mini-container {
            margin-top: 9px;
        }

        .td-full-container {
            margin-top: 9px;
        }

        .cls_textbox_notes {
            width: 100% !important;
        }

        .cls_multi_contact .btn-image img {
            width: 12px !important;
        }

        .cls_multi_contact {
            width: 100%;
        }

        .cls_multi_contact_first {
            width: 80%;
            display: inline-block;
        }

        .cls_multi_contact_second {
            width: 7%;
            display: inline-block;
        }

        .cls_categoryfilter_box .btn-image img {
            width: 12px !important;
        }

        .cls_categoryfilter_box {
            width: 100%;
        }

        .cls_categoryfilter_first {
            width: 80%;
            display: inline-block;
        }

        .cls_categoryfilter_second {
            width: 7%;
            display: inline-block;
        }

        .cls_comm_btn {
            margin-left: 3px;
            padding: 4px 8px;
        }

        .btn.btn-image.btn-call-data {
            margin-top: -15px;
        }

        .dis-none {
            display: none;
        }

        .no-due-date {
            background-color: #f1f1f1 !important;
        }

        .over-due-date {
            background-color: #777 !important;
            color: white;
        }

        .over-due-date .btn {
            background-color: #777 !important;
        }

        .over-due-date .btn .fa {
            color: black !important;
        }

        .no-due-date .btn {
            /* background-color: #f1f1f1 !important; */
        }

        .pd-2 {
            padding: 2px;
        }

        .zoom-img:hover {
            -ms-transform: scale(1.5);
            /* IE 9 */
            -webkit-transform: scale(1.5);
            /* Safari 3-8 */
            transform: scale(1.5);
        }

        .status-selection .btn-group {
            padding: 0;
            width: 100%;
        }

        .status-selection .multiselect {
            width: 100%;
        }

        .green-notification {
            color: green;
        }

        .red-notification {
            color: grey;
        }

        select.globalSelect2+span.select2 {
            width: calc(100% - 26px) !important;
        }

        /*start toggle button*/
        .cmn-toggle {
            position: absolute;
            margin-left: -9999px;
            visibility: hidden;
        }

        .cmn-toggle+label {
            display: block;
            position: relative;
            cursor: pointer;
            outline: none;
            user-select: none;
        }

        input.cmn-toggle-round+label {
            padding: 2px;
            width: 40px;
            height: 20px;
            background-color: #dddddd;
            border-radius: 60px;
        }

        input.cmn-toggle-round+label:before,
        input.cmn-toggle-round+label:after {
            display: block;
            position: absolute;
            top: 1px;
            left: 1px;
            bottom: 1px;
            content: "";
        }

        input.cmn-toggle-round+label:before {
            right: 1px;
            background-color: #f1f1f1;
            border-radius: 60px;
            transition: background 0.4s;
        }

        input.cmn-toggle-round+label:after {
            width: 18px;
            background-color: #fff;
            border-radius: 100%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            transition: margin 0.4s;
        }

        input.cmn-toggle-round:checked+label:before {
            background-color: #333333;
        }

        input.cmn-toggle-round:checked+label:after {
            margin-left: 20px;
        }

        .btn.btn-image {
            margin-top: 0px !important;
        }

        /*end toggle button*/

        .tablesorter-header-inner {
            white-space: nowrap;
        }

        .show-finished-task {
            height: auto;
        }

        .time_doctor_project_section_modal,
        .time_doctor_account_section_modal {
            display: none;
        }
    </style>
@endsection

@section('large_content')

    <div class="row">
        <div class="col-lg-12 text-center">
            <h2 class="page-heading">{{ $title }}</h2>
        </div>
    </div>
    <!--- Pre Loader -->
    <img src="/images/pre-loader.gif" id="Preloader" style="display:none;" />
    @include('partials.flash_messages')

    <div class="row">
      <div class="col-xs-12">
        <form class="form-search-data">
              <input type="hidden" name="daily_activity_date" value="{{ $data['daily_activity_date'] }}">
              <input type="hidden" name="type" id="tasktype" value="pending">
              <div class="row">
                  <div class="col-xs-12 col-md-1 pd-2">
                      <div class="form-group cls_task_subject">
                          <input type="text" name="term" placeholder="Search Term" id="task_search"
                              class="form-control input-sm" value="{{ isset($term) ? $term : '' }}">
                      </div>
                  </div>
                  <div class="col-xs-12 col-md-1 pd-2">
                      <div class="form-group">
                          {!! $task_categories_dropdown !!}
                      </div>
                  </div>
                  @if (auth()->user()->checkPermission('activity-list'))
                      <div class="col-xs-12 col-md-2 pd-2">
                          <div class="form-group ml-3">
                              <select id="search_by_user" class="form-control select2" name="selected_user[]" multiple>
                                  <option value="">Select a User</option>
                                  @foreach ($users as $id => $user)
                                      <option value="{{ $id }}" {{ $id == $selected_user ? 'selected' : '' }}>
                                          {{ $user }}</option>
                                  @endforeach
                              </select>
                          </div>
                      </div>
                      <div class="col-xs-12  col-md-1 pd-2">
                          <div class="form-group ml-3">
                              <select id="master_user_id" class="form-control select2" name="search_master_user_id"
                                  id="search_master_user_id">
                                  <option value="">Lead 1 Select</option>
                                  @foreach ($users as $leadId => $leadName)
                                      <option value="{{ $leadId }}">{{ $leadName }}</option>
                                  @endforeach
                              </select>
                          </div>
                      </div>
                      <div class="col-xs-12  col-md-1 pd-2">
                          <div class="form-group ml-3">
                              <select id="master_user_id" class="form-control select2" name="search_second_master_user_id"
                                  id="search_second_master_user_id">
                                  <option value="">Lead 2 Select
                                  <option>
                                      @foreach ($users as $leadId => $leadName)
                                  <option value="{{ $leadId }}">{{ $leadName }}</option>
                  @endforeach
                  </select>
              </div>
              </div>
              @endif
              <div class="col-xs-12 col-md-1 pd-2">
                  <div class="form-group">
                      <select name="is_statutory_query" id="is_statutory_query" class="form-control input-sm">
                          <option @if (request('is_statutory_query') == 0) selected @endif value="0">Other Task
                          </option>
                          <option @if (request('is_statutory_query') == 1) selected @endif value="1">Statutory Task
                          </option>
                          <option @if (request('is_statutory_query') == 2) selected @endif value="2">Calendar Task
                          </option>
                      </select>
                  </div>
              </div>
              <div class="col-xs-12 col-md-1 pd-2">
                  <div class="form-group">
                      <select name="sort_by" id="sort_by" class="form-control input-sm">
                          <option value="">Sort by</option>
                          <option @if (request('sort_by') == 1) selected @endif value="1">Date desc</option>
                          <option @if (request('sort_by') == 2) selected @endif value="2">Date Asc</option>
                      </select>
                  </div>
              </div>
              <div class="col-xs-12 col-md-1 pd-2">
                  <div class="form-group">
                      <select name="filter_by" id="filter_by" class="form-control input-sm">
                          <option value="">Filter by</option>
                          <option @if (request('filter_by') == 1) selected @endif value="1">Pending tasks</option>
                          <option @if (request('filter_by') == 2) selected @endif value="2">Completed by user
                          </option>
                      </select>
                  </div>
              </div>
            
            
              <div class="col-xs-12 col-md-1 pd-2 status-selection">
                  {{ html()->multiselect('filter_status[]', $statuseslist, request()->get('filter_status', $selectStatusList))->class('form-control multiselect') }}
              </div>
            
            
              <div class="col-xs-12 col-md-1 pd-2">
                  <input type="checkbox" checked="checked" name="flag_filter"> Flagged
              </div>
              <button type="button" class="btn btn-image btn-call-data"><img src="{{ asset('images/filter.png') }}" /></button>
              <button type="button" style="height: 30px;" class="btn btn-secondary cls_comm_btn priority_model_btn">Priority
              </button>
              </div>

        </form>
      </div>
    </div>

    @php
        $isAdmin = \App\Helpers::getadminorsupervisor() && !empty($selected_user) ? true : false;
    @endphp

    <div class="row mb-2">
      <div class="col-xs-12">
        <form action="{{ route('task.create.task.shortcut') }}" method="POST" id="taskCreateForm">
              @csrf
              <input type="hidden" name="has_render" value="1">
              <input type="hidden" name="from" value="task-page">
              @if (auth()->user()->isAdmin())
                  <div class="row">
                      <div class="col-xs-12 col-md-1 pd-2">
                          <div class="form-group cls_task_subject">
                              <input type="text" class="form-control input-sm" name="task_subject"
                                  placeholder="Task Subject" id="task_subject" value="{{ old('task_subject') }}"
                                  required />
                              @if ($errors->has('task_subject'))
                                  <div class="alert alert-danger">{{ $errors->first('task_subject') }}</div>
                              @endif
                          </div>
                      </div>
                      <div class="col-xs-12 col-md-2 pd-2">
                          <div class="form-group">
                              <textarea rows="1" class="form-control input-sm cls_task_detailstextarea" name="task_detail"
                                  placeholder="Task Details" id="task_details" required>{{ old('task_detail') }}</textarea>
                              @if ($errors->has('task_detail'))
                                  <div class="alert alert-danger">{{ $errors->first('task_detail') }}</div>
                              @endif
                          </div>
                      </div>
                      <div class="col-xs-12 col-md-1 pd-2">
                          <div class="form-inline">
                              <div class="cls_multi_contact">
                                  <div class="cls_multi_contact_first">
                                      <div class="">
                                          <select id="multi_contacts" style="width: 100%;"
                                              class="form-control input-sm js-example-basic-multiple"
                                              name="assign_to_contacts[]" multiple>
                                              @foreach (Auth::user()->contacts as $contact)
                                                  <option value="{{ $contact['id'] }}">{{ $contact['name'] }}
                                                      - {{ $contact['phone'] }} ({{ $contact['category'] }})
                                                  </option>
                                              @endforeach
                                          </select>
                                          @if ($errors->has('assign_to_contacts'))
                                              <div class="alert alert-danger">{{ $errors->first('assign_to_contacts') }}
                                              </div>
                                          @endif
                                      </div>
                                  </div>
                                  <div class="cls_multi_contact_second">
                                      <button type="button" class="btn btn-image" onclick="showContactModal()"><img
                                              src="{{ asset('images/add.png') }}" /></button>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="col-xs-12 col-md-1 pd-2">
                          <div class="form-inline">
                              <div class="cls_categoryfilter_box">
                                  <div class="cls_categoryfilter_first">
                                      <div class="">
                                          {!! $task_categories_dropdown !!}
                                      </div>
                                  </div>
                                  <div class="cls_categoryfilter_second">
                                      <button type="button" class="btn btn-image"
                                          onclick="showTaskCategorytModal()"><img
                                              src="{{ asset('images/add.png') }}" /></button>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="col-xs-12 col-md-1 pd-2">
                          <div class="form-group">
                              <select id="is_milestone" class="form-control" name="is_milestone" required>
                                  <option value="0">Is milestone</option>
                                  <option value="0">No</option>
                                  <option value="1">Yes</option>
                              </select>
                              @if ($errors->has('is_milestone'))
                                  <div class="alert alert-danger">{{ $errors->first('is_milestone') }}</div>
                              @endif
                          </div>
                      </div>
                      <div class="col-xs-12 col-md-1 pd-2">
                          <div class="form-group">
                              <input type="number" class="form-control" id="no_of_milestone" name="no_of_milestone"
                                  value="{{ old('no_of_milestone') }}" placeholder="No of milestone" />
                              @if ($errors->has('no_of_milestone'))
                                  <div class="alert alert-danger">{{ $errors->first('no_of_milestone') }}</div>
                              @endif
                          </div>
                      </div>
                      <div class="col-xs-12 col-md-1 pd-2">
                          <div class="form-group cls_task_subject">
                              <select name="task_type" class="form-control is_statutory input-sm">
                                  <option value="0">Other Task</option>
                                  <option value="1">Statutory Task</option>
                                  <option value="2">Calendar Task</option>
                                  <option value="3">Discussion Task</option>
                              </select>
                          </div>
                      </div>
                      @if (auth()->user()->isAdmin())
                          <div class="col-xs-12 col-md-2 pd-2">
                              <div class="form-group">
                                  <select id="multi_users" class="form-control input-sm" name="task_asssigned_to[]"
                                      multiple>
                                      @foreach ($data['users'] as $user)
                                          <option value="{{ $user['id'] }}">{{ $user['name'] }}
                                              - {{ $user['email'] }}</option>
                                      @endforeach
                                  </select>
                                  @if ($errors->has('task_asssigned_to'))
                                      <div class="alert alert-danger">{{ $errors->first('task_asssigned_to') }}</div>
                                  @endif
                              </div>
                          </div>
                      @endif
                      <div class="col-xs-12 col-md-2 pd-2">
                          <div class="form-group">
                              <label><input type="checkbox" name="need_review_task" value="1" /> Create Review
                                  Task?</label>
                          </div>
                      </div>
                      <div class="col-xs-12 col-md-1 pd-2">
                          <div class="form-group ml-3">
                              <select name="task_for" class="form-control task_for" style="width:100%;">
                                  <option value="hubstaff" selected>Hubstaff</option>
                                  <option value="time_doctor">Time Doctor</option>
                              </select>
                          </div>
                      </div>
                      <div class="col-xs-12 col-md-1 pd-2 time_doctor_account_section">
                          <div class="form-group ml-3">
                              {{ html()->select('time_doctor_account', ['' => ''])->class('form-control time_doctor_account globalSelect2')->style('width:100%;')->data('ajax', route('select2.time_doctor_accounts_for_task'))->data('placeholder', 'Account') }}
                          </div>
                      </div>
                      <div class="col-xs-12 col-md-1 pd-2 time_doctor_project_section">
                          <div class="form-group ml-3">
                              {{ html()->select('time_doctor_project', ['' => ''])->class('form-control time_doctor_project globalSelect2')->style('width:100%;')->data('ajax', route('select2.time_doctor_projects'))->data('placeholder', 'Project') }}
                          </div>
                      </div>
                      <div class="col-xs-12 col-md-2 pd-2">
                          <div class="form-group">
                              <button type="submit" class="btn btn-secondary cls_comm_btn" id="taskCreateButton">
                                  Create
                              </button>
                              @if (auth()->user()->isAdmin())
                                  <a class="btn btn-secondary cls_comm_btn" data-toggle="collapse"
                                      href="#openFilterCount" role="button" aria-expanded="false"
                                      aria-controls="openFilterCount">
                                      Open Task count
                                  </a>
                              @endif
                          </div>
                      </div>
                  </div>
              @endif
              <div class="row">
                  <div class="col-xs-12 col-md-4" id="recurring-task" style="display:none;">
                      <div class="row">
                          <div class="col-xs-12 col-md-6">
                              <div class="form-group">
                                  <select name="recurring_type" class="form-control input-sm">
                                      <option value="">Select</option>
                                      <option value="EveryHour">EveryHour</option>
                                      <option value="EveryDay">EveryDay</option>
                                      <option value="EveryWeek">EveryWeek</option>
                                      <option value="EveryMonth">EveryMonth</option>
                                      <option value="EveryYear">EveryYear</option>
                                  </select>
                              </div>
                          </div>
                          <div class="col-xs-12 col-md-6">
                              <div class="form-group">
                                  <div class='input-group date' id='sending-datetime'>
                                      <input type='text' class="form-control input-sm" name="sending_time"
                                          value="{{ date('Y-m-d H:i') }}" required />
                                      <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-calendar"></span>
                                      </span>
                                  </div>
                                  @if ($errors->has('sending_time'))
                                      <div class="alert alert-danger">{{ $errors->first('sending_time') }}</div>
                                  @endif
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="col-xs-12 col-md-2" id="calendar-task" style="display: none;">
                      <div class="form-group">
                          <div class='input-group date' id='completion-datetime'>
                              <input type='text' class="form-control input-sm" name="completion_date"
                                  value="{{ date('Y-m-d H:i') }}" />
                              <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-calendar"></span>
                              </span>
                          </div>
                          @if ($errors->has('completion_date'))
                              <div class="alert alert-danger">{{ $errors->first('completion_date') }}</div>
                          @endif
                      </div>
                  </div>
                  <div class="col-xs-12 col-md-4" style="display: none;padding-left: 15px;" id="appointment-container">
                      <div class="row">
                          <div class="col-xs-12 col-md-6">
                              <div class="form-group">
                                  {{ html()->select('task_id', ['0' => '-- Add New --'] + \App\Helpers::getTaskByIsStatury(3))->class('form-control select2-task-disscussion input-sm') }}
                              </div>
                          </div>
                          <div class="col-xs-12 col-md-6">
                              <div class="form-inline flex-fill">
                                  <div class="form-group cls_textbox_notes">
                                      <input type="text" style="width: 100%;" class="form-control input-sm"
                                          name="note[]" placeholder="Note" value="">
                                  </div>
                                  <button type="button" class="btn btn-xs btn-secondary" title="Add Note"
                                      id="addNoteButton">Add Note
                                  </button>&nbsp;
                                  <button type="button" class="btn btn-xs btn-secondary dis-none" id="saveNewNotes">
                                      Save New Notes
                                  </button>&nbsp;
                                  <div id="note-container">
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
        </form>
      </div>
    </div>

    @if (auth()->user()->isAdmin())
        <div class="row">
            <div class="col-md-12">
                <div class="collapse" id="openFilterCount">
                    <div class="card card-body">
                        <?php if (! empty($openTask)) { ?>
                        <div class="row col-md-12">
                            <?php foreach ($openTask as $k => $v) { ?>
                            <div class="col-md-2">
                                <div class="card">
                                    <div class="card-header">
                                        <?php echo $k; ?>
                                    </div>
                                    <div class="card-body">
                                        <?php echo $v; ?>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <?php } else {
                            echo 'Sorry , No data available';
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div id="exTab2" style="overflow: auto">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#1" data-toggle="tab" id="btn-call-data-pending" class="btn-call-data"
                  data-type="pending">Pending Task</a>
          </li>
          <li><a href="#2" data-toggle="tab" class="btn-call-data" data-type="statutory_not_completed">Statutory
                  Activity</a></li>
          <li><a href="#3" data-toggle="tab" class="btn-call-data" data-type="completed">Completed Task</a></li>
          <li><a href="#unassigned-tab" data-toggle="tab">Unassigned Messages</a></li>
          <li>
              <button type="button" onclick="showCsvExportModal()"
                  class="btn btn-xs btn-secondary my-3 csv-date-range-modal-btn" page="csv-download-page"
                  role="link" data-task-type="task"> Download Tasks
              </button>
          </li> &nbsp;
          <li>
              <button type="button" page="view-all-task-page"
                  class="btn btn-xs btn-secondary my-3 csv-date-range-modal-btn" onclick="showCsvExportModal()"
                  data-task-type="task"> View All Tasks
              </button>
          </li>&nbsp;
          <li>
              <button type="button" class="btn btn-xs btn-secondary my-3" id="send_message_button" data-selected="0">
                  Send Message
              </button>
          </li>&nbsp;
          <li>
              <button type="button" class="btn btn-xs btn-secondary my-3" id="view_categories_button">Categories
              </button>
          </li>&nbsp;
          <li>
              <button type="button" class="btn btn-xs btn-secondary my-3" id="make_complete_button">Complete Tasks
              </button>
          </li>&nbsp;
          <li>
              <button type="button" class="btn btn-xs btn-secondary my-3" id="make_delete_button">Delete Tasks
              </button>
          </li>&nbsp;
          @if (Auth::user()->isAdmin())
              <li>
                  <button type="button" class="btn btn-xs btn-secondary my-3" onclick="showTaskStatusModal()">Create
                      Status
                  </button>
              </li>&nbsp;
              <li>
                  <button class="btn btn-xs btn-secondary my-3" style="color:white;" onclick="showStatusColourModal()">
                      Status Color
                  </button>
              </li>&nbsp;
              <li>
                  <button class="btn btn-xs btn-secondary my-3" style="color:white;"
                      onclick="showColumnVisibilityModal()"> Column Visiblity
                  </button>
              </li>
          @endif
        </ul>
        <div class="tab-content ">
            <div class="tab-pane active" id="1">
                <div class="row" style="margin:0px;">
                    <div class="col-12">
                        <img class="infinite-scroll-products-loader center-block"
                            src="{{ asset('/images/loading.gif') }}" alt="Loading..." style="display: none" />
                    </div>
                    <div class="col-12">
                        <table class="table table-sm table-bordered" id="task_Tables">
                            <thead>
                                <tr>
                                    @if (Auth::user()->isAdmin())
                                        @if (!empty($dynamicColumnsToShowTask))
                                            @if (!in_array('ID', $dynamicColumnsToShowTask))
                                                <th width="2%">ID</th>
                                            @endif

                                            @if (!in_array('Date', $dynamicColumnsToShowTask))
                                                <th width="4%">Date</th>
                                            @endif

                                            @if (!in_array('Category', $dynamicColumnsToShowTask))
                                                <th width="4%" class="category">Category</th>
                                            @endif

                                            @if (!in_array('Subject', $dynamicColumnsToShowTask))
                                                <th width="4%">Task Subject</th>
                                            @endif

                                            @if (!in_array('Assign To', $dynamicColumnsToShowTask))
                                                <th width="10%">Assign To</th>
                                            @endif

                                            @if (!in_array('Status', $dynamicColumnsToShowTask))
                                                <th width="8%">Status</th>
                                            @endif

                                            @if (!in_array('Tracked time', $dynamicColumnsToShowTask))
                                                <th width="5%">Tracked time</th>
                                            @endif

                                            @if (!in_array('Communication', $dynamicColumnsToShowTask))
                                                <th class="communication_th">Communication</th>
                                            @endif

                                            @if (!in_array('Estimated Time', $dynamicColumnsToShowTask))
                                                <th width="6%">Estimated Time</th>
                                            @endif

                                            @if (!in_array('Estimated Start Datetime', $dynamicColumnsToShowTask))
                                                <th width="6%">Estimated Datetime</th>
                                            @endif


                                            @if (!in_array('Shortcuts', $dynamicColumnsToShowTask))
                                                <th width="20%">Shortcuts</th>
                                            @endif

                                            @if (!in_array('ICON', $dynamicColumnsToShowTask))
                                                <th width="2%">
                                                    <label><input type="checkbox" class="show-finished-task"
                                                            name="show_finished" value="on"> Finished</label>
                                                </th>
                                            @endif
                                        @else
                                            <th width="2%">ID</th>
                                            <th width="4%">Date</th>
                                            <th width="4%" class="category">Category</th>
                                            <th width="4%">Task Subject</th>
                                            <th width="10%">Assign To</th>
                                            <th width="8%">Status</th>
                                            <th width="5%">Tracked time</th>
                                            <th class="communication_th">Communication</th>
                                            <th width="6%">Estimated Time</th>
                                            <th width="6%">Estimated Datetime</th>
                                            <th width="20%">Shortcuts</th>
                                            <th width="2%">
                                                <label><input type="checkbox" class="show-finished-task"
                                                        name="show_finished" value="on"> Finished</label>
                                            </th>
                                        @endif
                                    @else
                                        <th width="2%">ID</th>
                                        <th width="4%">Date</th>
                                        <th width="4%" class="category">Category</th>
                                        <th width="4%">Task Subject</th>
                                        <th width="10%">Assign To</th>
                                        <th width="8%">Status</th>
                                        <th width="5%">Tracked time</th>
                                        <th class="communication_th">Communication</th>
                                        <th width="6%">Estimated Time</th>
                                        <th width="6%">Estimated Datetime</th>
                                        <th width="2%">
                                            <label><input type="checkbox" class="show-finished-task" name="show_finished"
                                                    value="on"> Finished</label>
                                        </th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="pending-row-render-view">
                                @if (count($data['task']['pending']) > 0)
                                    @foreach ($data['task']['pending'] as $task)
                                        @if (Auth::user()->isAdmin())
                                            @if (!empty($dynamicColumnsToShowTask))
                                                <tr style="background-color: {{ $task->task_status_color ?? '' }}!important;"
                                                    class="{{ \App\Http\Controllers\TaskModuleController::getClasses($task) }} {{ !$task->due_date ? 'no-due-date' : '' }} {{ $task->due_date && (date('Y-m-d H:i') > $task->due_date && !$task->is_completed) ? 'over-due-date' : '' }} {{ $task->is_statutory == 3 ? 'row-highlight' : '' }}"
                                                    id="task_{{ $task->id }}">
                                                    @if (!in_array('ID', $dynamicColumnsToShowTask))
                                                        <td class="p-2">
                                                            @if (auth()->user()->isAdmin())
                                                                <input type="checkbox" name="selected_issue[]"
                                                                    value="{{ $task->id }}"
                                                                    title="Task is in priority"
                                                                    {{ in_array($task->id, $priority) ? 'checked' : '' }}>
                                                            @endif
                                                            <input type="checkbox" title="Select task"
                                                                class="select_task_checkbox" name="task"
                                                                data-id="{{ $task->id }}" value=""></br>
                                                            {{ $task->id }}
                                                        </td>
                                                    @endif

                                                    @if (!in_array('Date', $dynamicColumnsToShowTask))
                                                        <td class="p-2">
                                                            {{ Carbon\Carbon::parse($task->created_at)->format('d-m H:i') }}
                                                            <br>
                                                            @if ($task->customer_id)
                                                                Cus-{{ $task->customer_id }}
                                                                <br>
                                                                @if (Auth::user()->isAdmin())
                                                                    <span>
                                                                        {{ isset($task->customer_name) ? $task->customer_name : '' }}
                                                                    </span>
                                                                @endif
                                                            @endif
                                                        </td>
                                                    @endif

                                                    @if (!in_array('Category', $dynamicColumnsToShowTask))
                                                        <td class="expand-row table-hover-cell p-2">
                                                            @if (isset($categories[$task->category]))
                                                                <span class="td-mini-container">
                                                                    {{ strlen($categories[$task->category]) > 10 ? substr($categories[$task->category], 0, 10) : $categories[$task->category] }}
                                                                </span>

                                                                <span class="td-full-container hidden">
                                                                    {{ $categories[$task->category] }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                    @endif

                                                    @if (!in_array('Subject', $dynamicColumnsToShowTask))
                                                        <td class="expand-row"
                                                            data-subject="{{ $task->task_subject ? $task->task_subject : 'Task Details' }}"
                                                            data-details="{{ $task->task_details }}" data-switch="0">
                                                            <span class="td-mini-container">
                                                                {{ $task->task_subject ? substr($task->task_subject, 0, 15) . (strlen($task->task_subject) > 15 ? '...' : '') : 'Task Details' }}
                                                            </span>
                                                            <span class="td-full-container hidden">
                                                                {{ $task->task_subject ? $task->task_subject : 'Task Details' }}
                                                            </span>
                                                        </td>
                                                    @endif

                                                    @if (!in_array('Assign To', $dynamicColumnsToShowTask))
                                                        <td class="table-hover-cell p-2">
                                                            @php
                                                                $special_task = $task;
                                                                $users_list = \App\Helpers::getTaskUserList(
                                                                    $task,
                                                                    $users,
                                                                );
                                                            @endphp
                                                            {{ strlen($users_list) > 15 ? substr($users_list, 0, 15) : $users_list }}

                                                            @if (auth()->user()->isAdmin() || $isTeamLeader)
                                                                @php
                                                                    $selectBoxId = 'assign_to';
                                                                    $selectClass = 'assign-user';
                                                                    $type = 'assign-user';
                                                                @endphp
                                                                @include(
                                                                    'task-module.partials.select-user',
                                                                    compact(
                                                                        'task',
                                                                        'users',
                                                                        'selectBoxId',
                                                                        'selectClass',
                                                                        'type'))
                                                            @else
                                                                @if ($task->assign_to)
                                                                    @if (isset($users[$task->assign_to]))
                                                                        <p>{{ $users[$task->assign_to] }}</p>
                                                                    @else
                                                                        <p>-</p>
                                                                    @endif
                                                                @endif
                                                            @endif

                                                            <span class="td-full-container hidden">
                                                                {{ $users_list }}
                                                            </span>
                                                            <button style="float:right;padding-right:0px;" type="button"
                                                                class="btn btn-xs show-user-history" title="Show History"
                                                                data-id="{{ $task->id }}"><i
                                                                    class="fa fa-info-circle"></i></button>
                                                            <div class="col-md-12 expand-col-lead{{ $task->id }} dis-none"
                                                                style="padding:0px;">
                                                                <br>
                                                                @if (auth()->user()->isAdmin() || $isTeamLeader)
                                                                    <label for=""
                                                                        style="font-size: 12px;margin-top:10px;">Lead
                                                                        :</label>
                                                                    @php
                                                                        $selectBoxId = 'master_user_id';
                                                                        $selectClass = 'assign-master-user';
                                                                        $type = 'master-user';
                                                                    @endphp
                                                                    @include(
                                                                        'task-module.partials.select-user',
                                                                        compact(
                                                                            'task',
                                                                            'users',
                                                                            'selectBoxId',
                                                                            'selectClass',
                                                                            'type'))
                                                                @else
                                                                    @if ($task->master_user_id)
                                                                        @if (isset($users[$task->master_user_id]))
                                                                            <p>{{ $users[$task->master_user_id] }}</p>
                                                                        @else
                                                                            <p>-</p>
                                                                        @endif
                                                                    @endif
                                                                @endif

                                                                <br>

                                                                @if (auth()->user()->isAdmin() || $isTeamLeader)
                                                                    <label for=""
                                                                        style="font-size: 12px;margin-top:10px;">Lead
                                                                        2 :</label>
                                                                    @php
                                                                        $selectBoxId = 'master_user_id';
                                                                        $selectClass = 'assign-master-user';
                                                                        $type = 'second-master-user';
                                                                    @endphp
                                                                    @include(
                                                                        'task-module.partials.select-user',
                                                                        compact(
                                                                            'task',
                                                                            'users',
                                                                            'selectBoxId',
                                                                            'selectClass',
                                                                            'type'))
                                                                @else
                                                                    @if ($task->second_master_user_id)
                                                                        @if (isset($users[$task->second_master_user_id]))
                                                                            <p>{{ $users[$task->second_master_user_id] }}
                                                                            </p>
                                                                        @else
                                                                            <p>-</p>
                                                                        @endif
                                                                    @endif
                                                                @endif

                                                                @if ($task->is_milestone)
                                                                    <p style="margin-bottom:0px;">Total
                                                                        : {{ $task->no_of_milestone }}</p>
                                                                    @if ($task->no_of_milestone == $task->milestone_completed)
                                                                        <p style="margin-bottom:0px;">Done
                                                                            : {{ $task->milestone_completed }}</p>
                                                                    @else
                                                                        <input type="number" name="milestone_completed"
                                                                            id="milestone_completed_{{ $task->id }}"
                                                                            placeholder="Completed..."
                                                                            class="form-control save-milestone"
                                                                            value="{{ $task->milestone_completed }}"
                                                                            data-id="{{ $task->id }}">
                                                                    @endif
                                                                @else
                                                                    <p>No milestone</p>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    @endif

                                                    @if (!in_array('Status', $dynamicColumnsToShowTask))
                                                        <td>
                                                            <select id="master_user_id"
                                                                class="form-control change-task-status select2"
                                                                data-id="{{ $task->id }}" name="master_user_id"
                                                                id="user_{{ $task->id }}">
                                                                <option value="">Select...</option>
                                                                <?php $masterUser = isset($task->master_user_id) ? $task->master_user_id : 0; ?>
                                                                @foreach ($task_statuses as $index => $status)
                                                                    @if (!auth()->user()->isAdmin() and $status->name == 'Done')
                                                                        @continue
                                                                    @endif
                                                                    @if ($status->id == $task->status)
                                                                        <option value="{{ $status->id }}" selected>
                                                                            {{ $status->name }}</option>
                                                                    @else
                                                                        <option value="{{ $status->id }}">
                                                                            {{ $status->name }}</option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    @endif

                                                    @if (!in_array('Tracked time', $dynamicColumnsToShowTask))
                                                        <td>
                                                            @if (isset($special_task->timeSpent) && $special_task->timeSpent->task_id > 0)
                                                                {{ formatDuration($special_task->timeSpent->tracked) }}
                                                                <button style="float:right;padding-right:0px;"
                                                                    type="button" class="btn btn-xs show-tracked-history"
                                                                    title="Show tracked time History"
                                                                    data-id="{{ $task->id }}"
                                                                    data-type="developer"><i
                                                                        class="fa fa-info-circle"></i></button>
                                                            @endif

                                                            <div class="col-md-12 expand-col" style="padding:0px;">
                                                                @if (!$task->hubstaff_task_id && (auth()->user()->isAdmin() || auth()->user()->id == $task->assign_to))
                                                                    <button type="button"
                                                                        class="btn btn-xs create-hubstaff-task"
                                                                        title="Create Hubstaff task for User"
                                                                        data-id="{{ $task->id }}"
                                                                        data-type="developer">
                                                                        Create D Task
                                                                    </button>
                                                                @endif
                                                                @if (
                                                                    !$task->lead_hubstaff_task_id &&
                                                                        $task->master_user_id &&
                                                                        (auth()->user()->isAdmin() || auth()->user()->id == $task->master_user_id))
                                                                    <button style="margin-top:10px;color:black;"
                                                                        type="button"
                                                                        class="btn btn-secondary btn-xs create-hubstaff-task"
                                                                        title="Create Hubstaff task for Master user"
                                                                        data-id="{{ $task->id }}"
                                                                        data-type="lead">Create
                                                                        L Task
                                                                    </button>
                                                                @endif
                                                                <button style="float:right;padding-right:0px;"
                                                                    type="button"
                                                                    class="btn btn-xs show-hubtask-log-history"
                                                                    title="Show create hubtask Logs"
                                                                    data-id="{{ $task->id }}"><i
                                                                        class="fa fa-info-circle"></i></button>
                                                            </div>
                                                        </td>
                                                    @endif

                                                    @if (!in_array('Communication', $dynamicColumnsToShowTask))
                                                        <td
                                                            class="table-hover-cell p-2 {{ ($task->message && $task->message_status == 0) || $task->message_is_reminder == 1 || ($task->message_user_id == $task->assign_from && $task->assign_from != Auth::id()) ? 'text-danger' : '' }}">
                                                            @if ($task->assign_to == Auth::id() || ($task->assign_to != Auth::id() && $task->is_private == 0))
                                                                <div style="margin-bottom:10px;width: 100%;">
                                                                    <?php $text_box = '100'; ?>
                                                                    <textarea rows="2" class="form-control quick-message-field input-sm" id="getMsg{{ $task->id }}"
                                                                        name="message" placeholder="Message"></textarea>
                                                                    <div class="d-flex">
                                                                        <div style="">
                                                                            <button id="send-message_{{ $task->id }}"
                                                                                class="btn btn-sm btn-image send-message"
                                                                                title="Send message"
                                                                                data-taskid="{{ $task->id }}"><img
                                                                                    src="{{ asset('images/filled-sent.png') }}" />
                                                                            </button>

                                                                            <input type="hidden" name="is_audio"
                                                                                id="is_audio_{{ $task->id }}"
                                                                                value="0">
                                                                        </div>
                                                                        @if (isset($task->message))
                                                                            <div style="max-width: 30px;">
                                                                                <button type="button"
                                                                                    class="btn btn-xs btn-image load-communication-modal"
                                                                                    data-object='task'
                                                                                    data-id="{{ $task->id }}"
                                                                                    title="Load messages"><img
                                                                                        src="{{ asset('images/chat.png') }}"
                                                                                        alt=""></button>
                                                                            </div>
                                                                        @endif
                                                                        <button
                                                                            class="btn btn-image upload-task-files-button ml-2"
                                                                            type="button" title="Uploaded Files"
                                                                            data-task_id="{{ $task->id }}">
                                                                            <i class="fa fa-cloud-upload"
                                                                                aria-hidden="true"></i>
                                                                        </button>
                                                                        <button
                                                                            class="btn btn-image view-task-files-button ml-2"
                                                                            type="button" title="View Uploaded Files"
                                                                            data-task_id="{{ $task->id }}">
                                                                            <img src="/images/google-drive.png"
                                                                                style="cursor: nwse-resize; width: 10px;">
                                                                        </button>
                                                                    </div>
                                                                    @if (isset($task->message))
                                                                        <div style="margin-bottom:10px;width: 100%;">
                                                                            @if (isset($task->is_audio) && $task->is_audio)
                                                                                <audio controls=""
                                                                                    src="{{ \App\Helpers::getAudioUrl($task->message) }}"></audio>
                                                                            @else
                                                                                <div class="d-flex justify-content-between expand-row-msg"
                                                                                    data-id="{{ $task->id }}">
                                                                                    <span
                                                                                        class="td-mini-container-{{ $task->id }}"
                                                                                        style="margin:0px;">
                                                                                        <?php
                                                                                        if (! empty($task->message) && ! empty($task->task_subject)) {
                                                                                            $pos = strpos($task->message, $task->task_subject);
                                                                                            $length = strlen($task->task_subject);
                                                                                            if ($pos) {
                                                                                                $start = $pos + $length + 1;
                                                                                            } else {
                                                                                                $start = 0;
                                                                                            }
                                                                                        } else {
                                                                                            $start = 0;
                                                                                        }
                        ?>
                                                                                        {{ substr($task->message, $start, 100) }}
                                                                                    </span>
                                                                                </div>
                                                                                <div class="expand-row-msg"
                                                                                    data-id="{{ $task->id }}">
                                                                                    <span
                                                                                        class="td-full-container-{{ $task->id }} hidden">
                                                                                        {{ $task->message }}
                                                                                    </span>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                @if (auth()->user()->isAdmin())
                                                                    <div style="margin-bottom:10px;width: 100%;">
                                                                        <div class="expand-col dis-none">
                                                                            <label for="">Lead:</label>
                                                                            <div class="d-flex">
                                                                                <input type="text" style="width: 100%;"
                                                                                    class="form-control quick-message-field input-sm"
                                                                                    id="getMsg{{ $task->id }}"
                                                                                    name="message" placeholder="Message"
                                                                                    value="">
                                                                                <div style="max-width: 30px;">
                                                                                    <button
                                                                                        class="btn btn-sm btn-image send-message-lead"
                                                                                        title="Send message"
                                                                                        data-taskid="{{ $task->id }}">
                                                                                        <img
                                                                                            src="{{ asset('images/filled-sent.png') }}" />
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @else
                                                                Private
                                                            @endif
                                                        </td>
                                                    @endif

                                                    @if (!in_array('Estimated Time', $dynamicColumnsToShowTask))
                                                        <td class="p-2">
                                                            <div style="margin-bottom:10px;width: 100%;">
                                                                <div class="d-flex">
                                                                    <input type="number" class="form-control"
                                                                        name="approximates{{ $task->id }}"
                                                                        value="{{ $task->approximate }}" min="1"
                                                                        autocomplete="off">
                                                                    <div style="max-width: 30px;">
                                                                        <button
                                                                            class="btn btn-sm btn-image send-approximate-lead"
                                                                            title="Send approximate"
                                                                            onclick="funTaskInformationUpdatesTime('approximate',{{ $task->id }})"
                                                                            data-taskid="{{ $task->id }}"><img
                                                                                src="{{ asset('images/filled-sent.png') }}" />
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <?php $time_history = \App\Helpers\DevelopmentHelper::getFirstDeveloperTaskHistoryByDevTaskIDAndModel($task->id); ?>

                                                            @if (!empty($time_history))
                                                                @if (isset($time_history->is_approved) && $time_history->is_approved != 1)
                                                                    <button
                                                                        data-task="{{ $time_history->developer_task_id }}"
                                                                        data-id="{{ $time_history->id }}"
                                                                        title="approve" data-type="TASK"
                                                                        class="btn btn-sm approveEstimateFromshortcutButtonTaskPage">
                                                                        <i class="fa fa-check" aria-hidden="true"></i>
                                                                    </button>
                                                                @endif

                                                                @if ($task->task_start != 1)
                                                                    <button data-task="{{ $task->id }}"
                                                                        title="Start Task" data-type="TASK"
                                                                        class="btn btn-sm startDirectTask"
                                                                        data-task-type="1">
                                                                        <i class="fa fa-play" aria-hidden="true"></i>
                                                                    </button>
                                                                @else
                                                                    <input type="hidden"
                                                                        value="{{ $task->m_start_date }}"
                                                                        class="m_start_date_"
                                                                        id="m_start_date_{{ $task->id }}"
                                                                        data-id="{{ $task->id }}"
                                                                        data-value="{{ $task->m_start_date }}">
                                                                    <button data-task="{{ $task->id }}"
                                                                        title="Start Task" data-type="TASK"
                                                                        class="btn btn-sm startDirectTask"
                                                                        data-task-type="2">
                                                                        <i class="fa fa-stop" aria-hidden="true"></i>
                                                                    </button>
                                                                    <div id="time-counter_{{ $task->id }}"></div>
                                                                @endif

                                                                <button type="button"
                                                                    class="btn btn-xs show-timer-history"
                                                                    title="Show timer History"
                                                                    data-id="{{ $task->id }}">
                                                                    <i class="fa fa-info-circle"></i></button>
                                                            @endif

                                                        </td>
                                                    @endif

                                                    @if (!in_array('Estimated Start Datetime', $dynamicColumnsToShowTask))
                                                        @php
                                                            $single = \App\Helpers\DevelopmentHelper::getTaskById(
                                                                $task->id,
                                                            );
                                                        @endphp
                                                        <td class="p-2">
                                                            <div class="form-group d-flex">
                                                                <div class='input-group date cls-start-due-date'>
                                                                    <input type="text" class="form-control"
                                                                        name="start_dates{{ $task->id }}"
                                                                        value="{{ $single->task_start_date }}"
                                                                        autocomplete="off" />
                                                                    <span class="input-group-addon"><span
                                                                            class="glyphicon glyphicon-calendar"></span></span>
                                                                </div>
                                                                <div style="max-width: 30px;">
                                                                    <button
                                                                        class="btn btn-sm btn-image send-start_date-lead"
                                                                        title="Send approximate"
                                                                        onclick="funTaskInformationUpdatesTime('start_date',{{ $task->id }})"
                                                                        data-taskid="{{ $task->id }}"><img
                                                                            src="{{ asset('images/filled-sent.png') }}" />
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            @if (!empty($single->task_start_date) && $single->task_start_date != '0000-00-00 00:00:00')
                                                                {{ $single->task_start_date }}
                                                            @endif

                                                            <div class="form-group d-flex">
                                                                <div class='input-group date cls-start-due-date'>
                                                                    <input type="text" class="form-control"
                                                                        name="due_dates{{ $task->id }}"
                                                                        value="{{ $single->task_new_due_date }}"
                                                                        autocomplete="off" />
                                                                    <span class="input-group-addon"><span
                                                                            class="glyphicon glyphicon-calendar"></span></span>
                                                                </div>
                                                                <div style="max-width: 30px;">
                                                                    <button
                                                                        class="btn btn-sm btn-image send-start_date-lead"
                                                                        title="Send approximate"
                                                                        onclick="funTaskInformationUpdatesTime('due_date',{{ $task->id }})"
                                                                        data-taskid="{{ $task->id }}"><img
                                                                            src="{{ asset('images/filled-sent.png') }}" />
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            @if (!empty($single->task_new_due_date) && $single->task_new_due_date != '0000-00-00 00:00:00')
                                                                {{ $single->task_new_due_date }}
                                                            @endif
                                                        </td>
                                                    @endif

                                                    @if (!in_array('Shortcuts', $dynamicColumnsToShowTask))
                                                        <td id="shortcutsIds">
                                                            @include('task-module.partials.shortcuts')
                                                        </td>
                                                    @endif

                                                    @if (!in_array('ICON', $dynamicColumnsToShowTask))
                                                        <td class="p-2">
                                                            <button type="button" class="btn btn-secondary btn-sm mt-2"
                                                                onclick="Showactionbtn('{{ $task->id }}')"><i
                                                                    class="fa fa-arrow-down"></i></button>
                                                        </td>
                                                    @endif
                                                </tr>
                                                @if (!in_array('ICON', $dynamicColumnsToShowTask))
                                                    <tr class="action-btn-tr-{{ $task->id }} d-none">
                                                        <td class="font-weight-bold">Action</td>
                                                        <td colspan="5">
                                                            <div>
                                                                <div class="row cls_action_box" style="margin:0px;">
                                                                    @if (auth()->user()->isAdmin())
                                                                        <button type="button"
                                                                            class='btn btn-image whatsapp-group pd-5'
                                                                            data-id="{{ $task->id }}"
                                                                            data-toggle='modal'
                                                                            src="{{ asset('images/whatsapp.png') }}">
                                                                        </button>
                                                                        <button type="button"
                                                                            class='btn delete-single-task pd-5'
                                                                            data-id="{{ $task->id }}"><i
                                                                                class="fa fa-trash"
                                                                                aria-hidden="true"></i></button>
                                                                    @endif
                                                                    <button class='btn pd-5 task-set-reminder'
                                                                        data-id="{{ $task->id }}"
                                                                        data-frequency="{{ !empty($task->reminder_message) ? $task->frequency : '60' }}"
                                                                        data-reminder_message="{{ !empty($task->reminder_message) ? $task->reminder_message : 'Plz update' }}"
                                                                        data-reminder_from="{{ $task->reminder_from }}"
                                                                        data-reminder_last_reply="{{ $task && !empty($task->reminder_last_reply) ? $task->reminder_last_reply : '' }}">
                                                                        <i class="fa fa-bell @if (!empty($task->reminder_message) && $task->frequency > 0) {{ 'green-notification' }} @else {{ 'red-notification' }} @endif"
                                                                            aria-hidden="true"></i>
                                                                    </button>
                                                                    @if (
                                                                        $special_task->users->contains(Auth::id()) ||
                                                                            $task->assign_from == Auth::id() ||
                                                                            $task->master_user_id == Auth::id() ||
                                                                            $task->second_master_user_id == Auth::id())
                                                                        @if (auth()->user()->isAdmin())
                                                                            <button type="button"
                                                                                title="Verify the task by admin"
                                                                                class="btn btn-image task-complete pd-5"
                                                                                data-id="{{ $task->id }}"><img
                                                                                    src="/images/completed-green.png" />
                                                                            </button>
                                                                        @endif

                                                                        @include('task-module.partials.show-status-history-btn')

                                                                        <button type="button"
                                                                            class='btn btn-image ml-1 reminder-message pd-5'
                                                                            data-id="{{ $task->message_id }}"><img
                                                                                src='/images/reminder.png' /></button>
                                                                        <button type="button"
                                                                            data-id="{{ $task->id }}"
                                                                            class="btn btn-file-upload pd-5">
                                                                            <i class="fa fa-upload"
                                                                                aria-hidden="true"></i>
                                                                        </button>
                                                                    @endif
                                                                    <button type="button"
                                                                        class="btn preview-img-btn pd-5"
                                                                        data-id="{{ $task->id }}">
                                                                        <i class="fa fa-list" aria-hidden="true"></i>
                                                                    </button>
                                                                    @if (!$special_task->users->contains(Auth::id()) && $special_task->contacts()->count() == 0)
                                                                        @if ($task->is_private == 1)
                                                                            <button disabled type="button"
                                                                                class="btn btn-image pd-5"><img
                                                                                    src="{{ asset('images/private.png') }}" />
                                                                            </button>
                                                                        @else
                                                                        @endif
                                                                    @endif

                                                                    @if (
                                                                        $special_task->users->contains(Auth::id()) ||
                                                                            ($task->assign_from == Auth::id() && $task->is_private == 0) ||
                                                                            ($task->assign_from == Auth::id() && $special_task->contacts()->count() > 0) ||
                                                                            Auth::id() == 6)
                                                                        <a href="{{ route('task.show', $task->id) }}"
                                                                            class="btn btn-image pd-5" href=""><img
                                                                                src="{{ asset('images/view.png') }}" /></a>
                                                                    @endif

                                                                    @if ($task->is_flagged == 1)
                                                                        <button type="button"
                                                                            class="btn btn-image flag-task pd-5"
                                                                            data-id="{{ $task->id }}"><img
                                                                                src="{{ asset('images/flagged.png') }}" />
                                                                        </button>
                                                                    @else
                                                                        <button type="button"
                                                                            class="btn btn-image flag-task pd-5"
                                                                            data-id="{{ $task->id }}"><img
                                                                                src="{{ asset('images/unflagged.png') }}" />
                                                                        </button>
                                                                    @endif
                                                                    <button class="btn btn-image expand-row-btn-lead"
                                                                        data-task_id="{{ $task->id }}"><img
                                                                            src="/images/forward.png"></button>
                                                                    <button class="btn btn-image set-remark"
                                                                        data-task_id="{{ $task->id }}"
                                                                        data-task_type="TASK"><i class="fa fa-comment"
                                                                            aria-hidden="true"></i>
                                                                    </button>

                                                                    <button class="btn btn-image mt-2 create-task-document"
                                                                        title="Create document"
                                                                        data-id="{{ $task->id }}">
                                                                        <i class="fa fa-file-text" aria-hidden="true"></i>
                                                                    </button>
                                                                    <button
                                                                        class="btn btn-image mt-2 show-created-task-document"
                                                                        title="Show created document"
                                                                        data-id="{{ $task->id }}">
                                                                        <i class="fa fa-list" aria-hidden="true"></i>
                                                                    </button>

                                                                    <a title="Task Information: Update"
                                                                        class="btn btn-image mt-2"
                                                                        href="javascript:void(0);"
                                                                        onclick="funTaskInformationModal(this, '{{ $task->id }}')">
                                                                        <i class="fa fa-info-circle"
                                                                            aria-hidden="true"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @else
                                                <tr style="background-color: {{ $task->task_status_color ?? '' }}!important;"
                                                    class="{{ \App\Http\Controllers\TaskModuleController::getClasses($task) }} {{ !$task->due_date ? 'no-due-date' : '' }} {{ $task->due_date && (date('Y-m-d H:i') > $task->due_date && !$task->is_completed) ? 'over-due-date' : '' }} {{ $task->is_statutory == 3 ? 'row-highlight' : '' }}"
                                                    id="task_{{ $task->id }}">
                                                    <td class="p-2">
                                                        @if (auth()->user()->isAdmin())
                                                            <input type="checkbox" name="selected_issue[]"
                                                                value="{{ $task->id }}" title="Task is in priority"
                                                                {{ in_array($task->id, $priority) ? 'checked' : '' }}>
                                                        @endif
                                                        <input type="checkbox" title="Select task"
                                                            class="select_task_checkbox" name="task"
                                                            data-id="{{ $task->id }}" value=""></br>
                                                        {{ $task->id }}
                                                    </td>
                                                    <td class="p-2">
                                                        {{ Carbon\Carbon::parse($task->created_at)->format('d-m H:i') }}
                                                        <br>
                                                        @if ($task->customer_id)
                                                            Cus-{{ $task->customer_id }}
                                                            <br>
                                                            @if (Auth::user()->isAdmin())
                                                                <span>
                                                                    {{ isset($task->customer_name) ? $task->customer_name : '' }}
                                                                </span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td class="expand-row table-hover-cell p-2">
                                                        @if (isset($categories[$task->category]))
                                                            <span class="td-mini-container">
                                                                {{ strlen($categories[$task->category]) > 10 ? substr($categories[$task->category], 0, 10) : $categories[$task->category] }}
                                                            </span>

                                                            <span class="td-full-container hidden">
                                                                {{ $categories[$task->category] }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="expand-row"
                                                        data-subject="{{ $task->task_subject ? $task->task_subject : 'Task Details' }}"
                                                        data-details="{{ $task->task_details }}" data-switch="0">
                                                        <span class="td-mini-container">
                                                            {{ $task->task_subject ? substr($task->task_subject, 0, 15) . (strlen($task->task_subject) > 15 ? '...' : '') : 'Task Details' }}
                                                        </span>
                                                        <span class="td-full-container hidden">
                                                            {{ $task->task_subject ? $task->task_subject : 'Task Details' }}
                                                        </span>
                                                    </td>

                                                    <td class="table-hover-cell p-2">
                                                        @php
                                                            $special_task = $task;
                                                            $users_list = \App\Helpers::getTaskUserList($task, $users);
                                                        @endphp
                                                        {{ strlen($users_list) > 15 ? substr($users_list, 0, 15) : $users_list }}

                                                        @if (auth()->user()->isAdmin() || $isTeamLeader)
                                                            @php
                                                                $selectBoxId = 'assign_to';
                                                                $selectClass = 'assign-user';
                                                                $type = 'assign-user';
                                                            @endphp
                                                            @include(
                                                                'task-module.partials.select-user',
                                                                compact(
                                                                    'task',
                                                                    'users',
                                                                    'selectBoxId',
                                                                    'selectClass',
                                                                    'type'))
                                                        @else
                                                            @if ($task->assign_to)
                                                                @if (isset($users[$task->assign_to]))
                                                                    <p>{{ $users[$task->assign_to] }}</p>
                                                                @else
                                                                    <p>-</p>
                                                                @endif
                                                            @endif
                                                        @endif

                                                        <span class="td-full-container hidden">
                                                            {{ $users_list }}
                                                        </span>
                                                        <button style="float:right;padding-right:0px;" type="button"
                                                            class="btn btn-xs show-user-history" title="Show History"
                                                            data-id="{{ $task->id }}"><i
                                                                class="fa fa-info-circle"></i>
                                                        </button>
                                                        <div class="col-md-12 expand-col-lead{{ $task->id }} dis-none"
                                                            style="padding:0px;">
                                                            <br>
                                                            @if (auth()->user()->isAdmin() || $isTeamLeader)
                                                                <label for=""
                                                                    style="font-size: 12px;margin-top:10px;">Lead
                                                                    :</label>
                                                                @php
                                                                    $selectBoxId = 'master_user_id';
                                                                    $selectClass = 'assign-master-user';
                                                                    $type = 'master-user';
                                                                @endphp
                                                                @include(
                                                                    'task-module.partials.select-user',
                                                                    compact(
                                                                        'task',
                                                                        'users',
                                                                        'selectBoxId',
                                                                        'selectClass',
                                                                        'type'))
                                                            @else
                                                                @if ($task->master_user_id)
                                                                    @if (isset($users[$task->master_user_id]))
                                                                        <p>{{ $users[$task->master_user_id] }}</p>
                                                                    @else
                                                                        <p>-</p>
                                                                    @endif
                                                                @endif
                                                            @endif

                                                            <br>

                                                            @if (auth()->user()->isAdmin() || $isTeamLeader)
                                                                <label for=""
                                                                    style="font-size: 12px;margin-top:10px;">Lead
                                                                    2 :</label>
                                                                @php
                                                                    $selectBoxId = 'master_user_id';
                                                                    $selectClass = 'assign-master-user';
                                                                    $type = 'second-master-user';
                                                                @endphp
                                                                @include(
                                                                    'task-module.partials.select-user',
                                                                    compact(
                                                                        'task',
                                                                        'users',
                                                                        'selectBoxId',
                                                                        'selectClass',
                                                                        'type'))
                                                            @else
                                                                @if ($task->second_master_user_id)
                                                                    @if (isset($users[$task->second_master_user_id]))
                                                                        <p>{{ $users[$task->second_master_user_id] }}</p>
                                                                    @else
                                                                        <p>-</p>
                                                                    @endif
                                                                @endif
                                                            @endif

                                                            @if ($task->is_milestone)
                                                                <p style="margin-bottom:0px;">Total
                                                                    : {{ $task->no_of_milestone }}</p>
                                                                @if ($task->no_of_milestone == $task->milestone_completed)
                                                                    <p style="margin-bottom:0px;">Done
                                                                        : {{ $task->milestone_completed }}</p>
                                                                @else
                                                                    <input type="number" name="milestone_completed"
                                                                        id="milestone_completed_{{ $task->id }}"
                                                                        placeholder="Completed..."
                                                                        class="form-control save-milestone"
                                                                        value="{{ $task->milestone_completed }}"
                                                                        data-id="{{ $task->id }}">
                                                                @endif
                                                            @else
                                                                <p>No milestone</p>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <select id="master_user_id"
                                                            class="form-control change-task-status select2"
                                                            data-id="{{ $task->id }}" name="master_user_id"
                                                            id="user_{{ $task->id }}">
                                                            <option value="">Select...</option>
                                                            <?php $masterUser = isset($task->master_user_id) ? $task->master_user_id : 0; ?>
                                                            @foreach ($task_statuses as $index => $status)
                                                                @if (!auth()->user()->isAdmin() and $status->name == 'Done')
                                                                    @continue
                                                                @endif
                                                                @if ($status->id == $task->status)
                                                                    <option value="{{ $status->id }}" selected>
                                                                        {{ $status->name }}</option>
                                                                @else
                                                                    <option value="{{ $status->id }}">
                                                                        {{ $status->name }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        @if (isset($special_task->timeSpent) && $special_task->timeSpent->task_id > 0)
                                                            {{ formatDuration($special_task->timeSpent->tracked) }}
                                                            <button style="float:right;padding-right:0px;" type="button"
                                                                class="btn btn-xs show-tracked-history"
                                                                title="Show tracked time History"
                                                                data-id="{{ $task->id }}" data-type="developer"><i
                                                                    class="fa fa-info-circle"></i></button>
                                                        @endif

                                                        <div class="col-md-12 expand-col" style="padding:0px;">
                                                            @if (!$task->hubstaff_task_id && (auth()->user()->isAdmin() || auth()->user()->id == $task->assign_to))
                                                                <button type="button"
                                                                    class="btn btn-xs create-hubstaff-task"
                                                                    title="Create Hubstaff task for User"
                                                                    data-id="{{ $task->id }}"
                                                                    data-type="developer">Create
                                                                    D Task
                                                                </button>
                                                            @endif
                                                            @if (
                                                                !$task->lead_hubstaff_task_id &&
                                                                    $task->master_user_id &&
                                                                    (auth()->user()->isAdmin() || auth()->user()->id == $task->master_user_id))
                                                                <button style="margin-top:10px;color:black;"
                                                                    type="button"
                                                                    class="btn btn-secondary btn-xs create-hubstaff-task"
                                                                    title="Create Hubstaff task for Master user"
                                                                    data-id="{{ $task->id }}"
                                                                    data-type="lead">Create L
                                                                    Task
                                                                </button>
                                                            @endif
                                                            <button style="float:right;padding-right:0px;" type="button"
                                                                class="btn btn-xs show-hubtask-log-history"
                                                                title="Show create hubtask Logs"
                                                                data-id="{{ $task->id }}"><i
                                                                    class="fa fa-info-circle"></i></button>
                                                        </div>
                                                    </td>
                                                    <td
                                                        class="table-hover-cell p-2 {{ ($task->message && $task->message_status == 0) || $task->message_is_reminder == 1 || ($task->message_user_id == $task->assign_from && $task->assign_from != Auth::id()) ? 'text-danger' : '' }}">
                                                        @if ($task->assign_to == Auth::id() || ($task->assign_to != Auth::id() && $task->is_private == 0))
                                                            <div style="margin-bottom:10px;width: 100%;">
                                                                <?php $text_box = '100'; ?>
                                                                <textarea rows="2" class="form-control quick-message-field input-sm" id="getMsg{{ $task->id }}"
                                                                    name="message" placeholder="Message"></textarea>
                                                                <div class="d-flex">
                                                                    <div style="">
                                                                        <button id="send-message_{{ $task->id }}"
                                                                            class="btn btn-sm btn-image send-message"
                                                                            title="Send message"
                                                                            data-taskid="{{ $task->id }}"><img
                                                                                src="{{ asset('images/filled-sent.png') }}" />
                                                                        </button>

                                                                        <input type="hidden" name="is_audio"
                                                                            id="is_audio_{{ $task->id }}"
                                                                            value="0">
                                                                    </div>
                                                                    @if (isset($task->message))
                                                                        <div style="max-width: 30px;">
                                                                            <button type="button"
                                                                                class="btn btn-xs btn-image load-communication-modal"
                                                                                data-object='task'
                                                                                data-id="{{ $task->id }}"
                                                                                title="Load messages"><img
                                                                                    src="{{ asset('images/chat.png') }}"
                                                                                    alt=""></button>
                                                                        </div>
                                                                    @endif
                                                                    <button
                                                                        class="btn btn-image upload-task-files-button ml-2"
                                                                        type="button" title="Uploaded Files"
                                                                        data-task_id="{{ $task->id }}">
                                                                        <i class="fa fa-cloud-upload"
                                                                            aria-hidden="true"></i>
                                                                    </button>
                                                                    <button
                                                                        class="btn btn-image view-task-files-button ml-2"
                                                                        type="button" title="View Uploaded Files"
                                                                        data-task_id="{{ $task->id }}">
                                                                        <img src="/images/google-drive.png"
                                                                            style="cursor: nwse-resize; width: 10px;">
                                                                    </button>
                                                                </div>
                                                                @if (isset($task->message))
                                                                    <div style="margin-bottom:10px;width: 100%;">
                                                                        @if (isset($task->is_audio) && $task->is_audio)
                                                                            <audio controls=""
                                                                                src="{{ \App\Helpers::getAudioUrl($task->message) }}"></audio>
                                                                        @else
                                                                            <div class="d-flex justify-content-between expand-row-msg"
                                                                                data-id="{{ $task->id }}">
                                                                                <span
                                                                                    class="td-mini-container-{{ $task->id }}"
                                                                                    style="margin:0px;">
                                                                                    <?php
                                                                                    if (! empty($task->message) && ! empty($task->task_subject)) {
                                                                                        $pos = strpos($task->message, $task->task_subject);
                                                                                        $length = strlen($task->task_subject);
                                                                                        if ($pos) {
                                                                                            $start = $pos + $length + 1;
                                                                                        } else {
                                                                                            $start = 0;
                                                                                        }
                                                                                    } else {
                                                                                        $start = 0;
                                                                                    }
                        ?>
                                                                                    {{ substr($task->message, $start, 100) }}
                                                                                </span>
                                                                            </div>
                                                                            <div class="expand-row-msg"
                                                                                data-id="{{ $task->id }}">
                                                                                <span
                                                                                    class="td-full-container-{{ $task->id }} hidden">
                                                                                    {{ $task->message }}
                                                                                </span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            @if (auth()->user()->isAdmin())
                                                                <div style="margin-bottom:10px;width: 100%;">
                                                                    <div class="expand-col dis-none">
                                                                        <label for="">Lead:</label>
                                                                        <div class="d-flex">
                                                                            <input type="text" style="width: 100%;"
                                                                                class="form-control quick-message-field input-sm"
                                                                                id="getMsg{{ $task->id }}"
                                                                                name="message" placeholder="Message"
                                                                                value="">
                                                                            <div style="max-width: 30px;">
                                                                                <button
                                                                                    class="btn btn-sm btn-image send-message-lead"
                                                                                    title="Send message"
                                                                                    data-taskid="{{ $task->id }}"><img
                                                                                        src="{{ asset('images/filled-sent.png') }}" />
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @else
                                                            Private
                                                        @endif
                                                    </td>
                                                    <td class="p-2">
                                                        <div style="margin-bottom:10px;width: 100%;">
                                                            <div class="d-flex">
                                                                <input type="number" class="form-control"
                                                                    name="approximates{{ $task->id }}"
                                                                    value="{{ $task->approximate }}" min="1"
                                                                    autocomplete="off">
                                                                <div style="max-width: 30px;">
                                                                    <button
                                                                        class="btn btn-sm btn-image send-approximate-lead"
                                                                        title="Send approximate"
                                                                        onclick="funTaskInformationUpdatesTime('approximate',{{ $task->id }})"
                                                                        data-taskid="{{ $task->id }}"><img
                                                                            src="{{ asset('images/filled-sent.png') }}" />
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <?php
                                                        $time_history = \App\Helpers\DevelopmentHelper::getFirstDeveloperTaskHistoryByDevTaskIDAndModel($task->id); ?>

                                                        @if (!empty($time_history))
                                                            @if (isset($time_history->is_approved) && $time_history->is_approved != 1)
                                                                <button
                                                                    data-task="{{ $time_history->developer_task_id }}"
                                                                    data-id="{{ $time_history->id }}" title="approve"
                                                                    data-type="TASK"
                                                                    class="btn btn-sm approveEstimateFromshortcutButtonTaskPage">
                                                                    <i class="fa fa-check" aria-hidden="true"></i>
                                                                </button>
                                                            @endif

                                                            @if ($task->task_start != 1)
                                                                <button data-task="{{ $task->id }}"
                                                                    title="Start Task" data-type="TASK"
                                                                    class="btn btn-sm startDirectTask" data-task-type="1">
                                                                    <i class="fa fa-play" aria-hidden="true"></i>
                                                                </button>
                                                            @else
                                                                <input type="hidden" value="{{ $task->m_start_date }}"
                                                                    class="m_start_date_"
                                                                    id="m_start_date_{{ $task->id }}"
                                                                    data-id="{{ $task->id }}"
                                                                    data-value="{{ $task->m_start_date }}">
                                                                <button data-task="{{ $task->id }}"
                                                                    title="Start Task" data-type="TASK"
                                                                    class="btn btn-sm startDirectTask" data-task-type="2">
                                                                    <i class="fa fa-stop" aria-hidden="true"></i>
                                                                </button>
                                                                <div id="time-counter_{{ $task->id }}"></div>
                                                            @endif

                                                            <button type="button" class="btn btn-xs show-timer-history"
                                                                title="Show timer History"
                                                                data-id="{{ $task->id }}"><i
                                                                    class="fa fa-info-circle"></i></button>
                                                        @endif
                                                    </td>
                                                    @php
                                                        $single = \App\Helpers\DevelopmentHelper::getTaskById(
                                                            $task->id,
                                                        );
                                                    @endphp
                                                    <td class="p-2">
                                                        <div class="form-group d-flex">
                                                            <div class='input-group date cls-start-due-date'>
                                                                <input type="text" class="form-control"
                                                                    name="start_dates{{ $task->id }}"
                                                                    value="{{ $single->task_start_date }}"
                                                                    autocomplete="off" />
                                                                <span class="input-group-addon"><span
                                                                        class="glyphicon glyphicon-calendar"></span></span>
                                                            </div>
                                                            <div style="max-width: 30px;">
                                                                <button class="btn btn-sm btn-image send-start_date-lead"
                                                                    title="Send approximate"
                                                                    onclick="funTaskInformationUpdatesTime('start_date',{{ $task->id }})"
                                                                    data-taskid="{{ $task->id }}"><img
                                                                        src="{{ asset('images/filled-sent.png') }}" />
                                                                </button>
                                                            </div>
                                                        </div>
                                                        @if (!empty($single->task_start_date) && $single->task_start_date != '0000-00-00 00:00:00')
                                                            {{ $single->task_start_date }}
                                                        @endif

                                                        <div class="form-group d-flex">
                                                            <div class='input-group date cls-start-due-date'>
                                                                <input type="text" class="form-control"
                                                                    name="due_dates{{ $task->id }}"
                                                                    value="{{ $single->task_new_due_date }}"
                                                                    autocomplete="off" />
                                                                <span class="input-group-addon"><span
                                                                        class="glyphicon glyphicon-calendar"></span></span>
                                                            </div>
                                                            <div style="max-width: 30px;">
                                                                <button class="btn btn-sm btn-image send-start_date-lead"
                                                                    title="Send approximate"
                                                                    onclick="funTaskInformationUpdatesTime('due_date',{{ $task->id }})"
                                                                    data-taskid="{{ $task->id }}"><img
                                                                        src="{{ asset('images/filled-sent.png') }}" />
                                                                </button>
                                                            </div>
                                                        </div>
                                                        @if (!empty($single->task_new_due_date) && $single->task_new_due_date != '0000-00-00 00:00:00')
                                                            {{ $single->task_new_due_date }}
                                                        @endif
                                                    </td>
                                                    <td id="shortcutsIds">
                                                        @include('task-module.partials.shortcuts')
                                                    </td>
                                                    <td class="p-2">
                                                        <button type="button" class="btn btn-secondary btn-sm mt-2"
                                                            onclick="Showactionbtn('{{ $task->id }}')"><i
                                                                class="fa fa-arrow-down"></i></button>
                                                    </td>
                                                </tr>
                                                <tr class="action-btn-tr-{{ $task->id }} d-none">
                                                    <td class="font-weight-bold">Action</td>
                                                    <td colspan="5">
                                                        <div>
                                                            <div class="row cls_action_box" style="margin:0px;">
                                                                @if (auth()->user()->isAdmin())
                                                                    <button type="button"
                                                                        class='btn btn-image whatsapp-group pd-5'
                                                                        data-id="{{ $task->id }}"><img
                                                                            src="{{ asset('images/whatsapp.png') }}" />
                                                                    </button>
                                                                    <button type="button"
                                                                        class='btn delete-single-task pd-5'
                                                                        data-id="{{ $task->id }}"><i
                                                                            class="fa fa-trash" aria-hidden="true"></i>
                                                                    </button>
                                                                @endif
                                                                <button class='btn pd-5 task-set-reminder'
                                                                    data-id="{{ $task->id }}"
                                                                    data-frequency="{{ !empty($task->reminder_message) ? $task->frequency : '60' }}"
                                                                    data-reminder_message="{{ !empty($task->reminder_message) ? $task->reminder_message : 'Plz update' }}"
                                                                    data-reminder_from="{{ $task->reminder_from }}"
                                                                    data-reminder_last_reply="{{ $task && !empty($task->reminder_last_reply) ? $task->reminder_last_reply : '' }}">
                                                                    <i class="fa fa-bell @if (!empty($task->reminder_message) && $task->frequency > 0) {{ 'green-notification' }} @else {{ 'red-notification' }} @endif"
                                                                        aria-hidden="true"></i>
                                                                </button>
                                                                @if (
                                                                    $special_task->users->contains(Auth::id()) ||
                                                                        $task->assign_from == Auth::id() ||
                                                                        $task->master_user_id == Auth::id() ||
                                                                        $task->second_master_user_id == Auth::id())
                                                                    @if (auth()->user()->isAdmin())
                                                                        <button type="button"
                                                                            title="Verify the task by admin"
                                                                            class="btn btn-image task-complete pd-5"
                                                                            data-id="{{ $task->id }}"><img
                                                                                src="/images/completed-green.png" />
                                                                        </button>
                                                                    @endif

                                                                    @include('task-module.partials.show-status-history-btn')

                                                                    <button type="button"
                                                                        class='btn btn-image ml-1 reminder-message pd-5'
                                                                        data-id="{{ $task->message_id }}"><img
                                                                            src='/images/reminder.png' /></button>
                                                                    <button type="button" data-id="{{ $task->id }}"
                                                                        class="btn btn-file-upload pd-5">
                                                                        <i class="fa fa-upload" aria-hidden="true"></i>
                                                                    </button>
                                                                @endif
                                                                <button type="button" class="btn preview-img-btn pd-5"
                                                                    data-id="{{ $task->id }}">
                                                                    <i class="fa fa-list" aria-hidden="true"></i>
                                                                </button>
                                                                @if (!$special_task->users->contains(Auth::id()) && $special_task->contacts()->count() == 0)
                                                                    @if ($task->is_private == 1)
                                                                        <button disabled type="button"
                                                                            class="btn btn-image pd-5"><img
                                                                                src="{{ asset('images/private.png') }}" />
                                                                        </button>
                                                                    @else
                                                                    @endif
                                                                @endif

                                                                @if (
                                                                    $special_task->users->contains(Auth::id()) ||
                                                                        ($task->assign_from == Auth::id() && $task->is_private == 0) ||
                                                                        ($task->assign_from == Auth::id() && $special_task->contacts()->count() > 0) ||
                                                                        Auth::id() == 6)
                                                                    <a href="{{ route('task.show', $task->id) }}"
                                                                        class="btn btn-image pd-5" href=""><img
                                                                            src="{{ asset('images/view.png') }}" /></a>
                                                                @endif

                                                                @if ($task->is_flagged == 1)
                                                                    <button type="button"
                                                                        class="btn btn-image flag-task pd-5"
                                                                        data-id="{{ $task->id }}"><img
                                                                            src="{{ asset('images/flagged.png') }}" />
                                                                    </button>
                                                                @else
                                                                    <button type="button"
                                                                        class="btn btn-image flag-task pd-5"
                                                                        data-id="{{ $task->id }}"><img
                                                                            src="{{ asset('images/unflagged.png') }}" />
                                                                    </button>
                                                                @endif
                                                                <button class="btn btn-image expand-row-btn-lead"
                                                                    data-task_id="{{ $task->id }}"><img
                                                                        src="/images/forward.png"></button>
                                                                <button class="btn btn-image set-remark"
                                                                    data-task_id="{{ $task->id }}"
                                                                    data-task_type="TASK"><i class="fa fa-comment"
                                                                        aria-hidden="true"></i>
                                                                </button>

                                                                <button class="btn btn-image mt-2 create-task-document"
                                                                    title="Create document"
                                                                    data-id="{{ $task->id }}">
                                                                    <i class="fa fa-file-text" aria-hidden="true"></i>
                                                                </button>
                                                                <button
                                                                    class="btn btn-image mt-2 show-created-task-document"
                                                                    title="Show created document"
                                                                    data-id="{{ $task->id }}">
                                                                    <i class="fa fa-list" aria-hidden="true"></i>
                                                                </button>

                                                                <a title="Task Information: Update"
                                                                    class="btn btn-image mt-2"
                                                                    href="javascript:void(0);"
                                                                    onclick="funTaskInformationModal(this, '{{ $task->id }}')">
                                                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @else
                                            <tr style="background-color: {{ $task->task_status_color ?? '' }}!important;"
                                                class="{{ \App\Http\Controllers\TaskModuleController::getClasses($task) }} {{ !$task->due_date ? 'no-due-date' : '' }} {{ $task->due_date && (date('Y-m-d H:i') > $task->due_date && !$task->is_completed) ? 'over-due-date' : '' }} {{ $task->is_statutory == 3 ? 'row-highlight' : '' }}"
                                                id="task_{{ $task->id }}">
                                                <td class="p-2">
                                                    @if (auth()->user()->isAdmin())
                                                        <input type="checkbox" name="selected_issue[]"
                                                            value="{{ $task->id }}" title="Task is in priority"
                                                            {{ in_array($task->id, $priority) ? 'checked' : '' }}>
                                                    @endif
                                                    <input type="checkbox" title="Select task"
                                                        class="select_task_checkbox" name="task"
                                                        data-id="{{ $task->id }}" value=""></br>
                                                    {{ $task->id }}
                                                </td>
                                                <td class="p-2">
                                                    {{ Carbon\Carbon::parse($task->created_at)->format('d-m H:i') }}
                                                    <br>
                                                    @if ($task->customer_id)
                                                        Cus-{{ $task->customer_id }}
                                                        <br>
                                                        @if (Auth::user()->isAdmin())
                                                            <span>
                                                                {{ isset($task->customer_name) ? $task->customer_name : '' }}
                                                            </span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td class="expand-row table-hover-cell p-2">
                                                    @if (isset($categories[$task->category]))
                                                        <span class="td-mini-container">
                                                            {{ strlen($categories[$task->category]) > 10 ? substr($categories[$task->category], 0, 10) : $categories[$task->category] }}
                                                        </span>

                                                        <span class="td-full-container hidden">
                                                            {{ $categories[$task->category] }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="expand-row"
                                                    data-subject="{{ $task->task_subject ? $task->task_subject : 'Task Details' }}"
                                                    data-details="{{ $task->task_details }}" data-switch="0">
                                                    <span class="td-mini-container">
                                                        {{ $task->task_subject ? substr($task->task_subject, 0, 15) . (strlen($task->task_subject) > 15 ? '...' : '') : 'Task Details' }}
                                                    </span>
                                                    <span class="td-full-container hidden">
                                                        {{ $task->task_subject ? $task->task_subject : 'Task Details' }}
                                                    </span>
                                                </td>
                                                <td class="table-hover-cell p-2">
                                                    @php
                                                        $special_task = $task;
                                                        $users_list = \App\Helpers::getTaskUserList($task, $users);
                                                    @endphp
                                                    {{ strlen($users_list) > 15 ? substr($users_list, 0, 15) : $users_list }}

                                                    @if (auth()->user()->isAdmin() || $isTeamLeader)
                                                        @php
                                                            $selectBoxId = 'assign_to';
                                                            $selectClass = 'assign-user';
                                                            $type = 'assign-user';
                                                        @endphp
                                                        @include(
                                                            'task-module.partials.select-user',
                                                            compact(
                                                                'task',
                                                                'users',
                                                                'selectBoxId',
                                                                'selectClass',
                                                                'type'))
                                                    @else
                                                        @if ($task->assign_to)
                                                            @if (isset($users[$task->assign_to]))
                                                                <p>{{ $users[$task->assign_to] }}</p>
                                                            @else
                                                                <p>-</p>
                                                            @endif
                                                        @endif
                                                    @endif

                                                    <span class="td-full-container hidden">
                                                        {{ $users_list }}
                                                    </span>
                                                    <button style="float:right;padding-right:0px;" type="button"
                                                        class="btn btn-xs show-user-history" title="Show History"
                                                        data-id="{{ $task->id }}"><i
                                                            class="fa fa-info-circle"></i>
                                                    </button>
                                                    <div class="col-md-12 expand-col-lead{{ $task->id }} dis-none"
                                                        style="padding:0px;">
                                                        <br>
                                                        @if (auth()->user()->isAdmin() || $isTeamLeader)
                                                            <label for=""
                                                                style="font-size: 12px;margin-top:10px;">Lead
                                                                :</label>
                                                            @php
                                                                $selectBoxId = 'master_user_id';
                                                                $selectClass = 'assign-master-user';
                                                                $type = 'master-user';
                                                            @endphp
                                                            @include(
                                                                'task-module.partials.select-user',
                                                                compact(
                                                                    'task',
                                                                    'users',
                                                                    'selectBoxId',
                                                                    'selectClass',
                                                                    'type'))
                                                        @else
                                                            @if ($task->master_user_id)
                                                                @if (isset($users[$task->master_user_id]))
                                                                    <p>{{ $users[$task->master_user_id] }}</p>
                                                                @else
                                                                    <p>-</p>
                                                                @endif
                                                            @endif
                                                        @endif

                                                        <br>

                                                        @if (auth()->user()->isAdmin() || $isTeamLeader)
                                                            <label for=""
                                                                style="font-size: 12px;margin-top:10px;">Lead 2
                                                                :</label>
                                                            @php
                                                                $selectBoxId = 'master_user_id';
                                                                $selectClass = 'assign-master-user';
                                                                $type = 'second-master-user';
                                                            @endphp
                                                            @include(
                                                                'task-module.partials.select-user',
                                                                compact(
                                                                    'task',
                                                                    'users',
                                                                    'selectBoxId',
                                                                    'selectClass',
                                                                    'type'))
                                                        @else
                                                            @if ($task->second_master_user_id)
                                                                @if (isset($users[$task->second_master_user_id]))
                                                                    <p>{{ $users[$task->second_master_user_id] }}</p>
                                                                @else
                                                                    <p>-</p>
                                                                @endif
                                                            @endif
                                                        @endif

                                                        @if ($task->is_milestone)
                                                            <p style="margin-bottom:0px;">Total
                                                                : {{ $task->no_of_milestone }}</p>
                                                            @if ($task->no_of_milestone == $task->milestone_completed)
                                                                <p style="margin-bottom:0px;">Done
                                                                    : {{ $task->milestone_completed }}</p>
                                                            @else
                                                                <input type="number" name="milestone_completed"
                                                                    id="milestone_completed_{{ $task->id }}"
                                                                    placeholder="Completed..."
                                                                    class="form-control save-milestone"
                                                                    value="{{ $task->milestone_completed }}"
                                                                    data-id="{{ $task->id }}">
                                                            @endif
                                                        @else
                                                            <p>No milestone</p>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <select id="master_user_id"
                                                        class="form-control change-task-status select2"
                                                        data-id="{{ $task->id }}" name="master_user_id"
                                                        id="user_{{ $task->id }}">
                                                        <option value="">Select...</option>
                                                        <?php $masterUser = isset($task->master_user_id) ? $task->master_user_id : 0; ?>
                                                        @foreach ($task_statuses as $index => $status)
                                                            @if (!auth()->user()->isAdmin() and $status->name == 'Done')
                                                                @continue
                                                            @endif
                                                            @if ($status->id == $task->status)
                                                                <option value="{{ $status->id }}" selected>
                                                                    {{ $status->name }}</option>
                                                            @else
                                                                <option value="{{ $status->id }}">
                                                                    {{ $status->name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    @if (isset($special_task->timeSpent) && $special_task->timeSpent->task_id > 0)
                                                        {{ formatDuration($special_task->timeSpent->tracked) }}
                                                        <button style="float:right;padding-right:0px;" type="button"
                                                            class="btn btn-xs show-tracked-history"
                                                            title="Show tracked time History"
                                                            data-id="{{ $task->id }}" data-type="developer"><i
                                                                class="fa fa-info-circle"></i>
                                                        </button>
                                                    @endif

                                                    <div class="col-md-12 expand-col" style="padding:0px;">
                                                        @if (!$task->hubstaff_task_id && (auth()->user()->isAdmin() || auth()->user()->id == $task->assign_to))
                                                            <button type="button"
                                                                class="btn btn-xs create-hubstaff-task"
                                                                title="Create Hubstaff task for User"
                                                                data-id="{{ $task->id }}"
                                                                data-type="developer">Create D
                                                                Task
                                                            </button>
                                                        @endif
                                                        @if (
                                                            !$task->lead_hubstaff_task_id &&
                                                                $task->master_user_id &&
                                                                (auth()->user()->isAdmin() || auth()->user()->id == $task->master_user_id))
                                                            <button style="margin-top:10px;color:black;" type="button"
                                                                class="btn btn-secondary btn-xs create-hubstaff-task"
                                                                title="Create Hubstaff task for Master user"
                                                                data-id="{{ $task->id }}" data-type="lead">Create
                                                                L Task
                                                            </button>
                                                        @endif
                                                        <button style="float:right;padding-right:0px;" type="button"
                                                            class="btn btn-xs show-hubtask-log-history"
                                                            title="Show create hubtask Logs"
                                                            data-id="{{ $task->id }}"><i
                                                                class="fa fa-info-circle"></i></button>
                                                    </div>
                                                </td>
                                                <td
                                                    class="table-hover-cell p-2 {{ ($task->message && $task->message_status == 0) || $task->message_is_reminder == 1 || ($task->message_user_id == $task->assign_from && $task->assign_from != Auth::id()) ? 'text-danger' : '' }}">
                                                    @if ($task->assign_to == Auth::id() || ($task->assign_to != Auth::id() && $task->is_private == 0))
                                                        <div style="margin-bottom:10px;width: 100%;">
                                                            <?php $text_box = '100'; ?>
                                                            <textarea rows="2" class="form-control quick-message-field input-sm" id="getMsg{{ $task->id }}"
                                                                name="message" placeholder="Message"></textarea>
                                                            <div class="d-flex">
                                                                <div style="">
                                                                    <button id="send-message_{{ $task->id }}"
                                                                        class="btn btn-sm btn-image send-message"
                                                                        title="Send message"
                                                                        data-taskid="{{ $task->id }}"><img
                                                                            src="{{ asset('images/filled-sent.png') }}" />
                                                                    </button>

                                                                    <input type="hidden" name="is_audio"
                                                                        id="is_audio_{{ $task->id }}"
                                                                        value="0">
                                                                </div>
                                                                @if (isset($task->message))
                                                                    <div style="max-width: 30px;">
                                                                        <button type="button"
                                                                            class="btn btn-xs btn-image load-communication-modal"
                                                                            data-object='task'
                                                                            data-id="{{ $task->id }}"
                                                                            title="Load messages"><img
                                                                                src="{{ asset('images/chat.png') }}"
                                                                                alt=""></button>
                                                                    </div>
                                                                @endif
                                                                <button
                                                                    class="btn btn-image upload-task-files-button ml-2"
                                                                    type="button" title="Uploaded Files"
                                                                    data-task_id="{{ $task->id }}">
                                                                    <i class="fa fa-cloud-upload"
                                                                        aria-hidden="true"></i>
                                                                </button>
                                                                <button class="btn btn-image view-task-files-button ml-2"
                                                                    type="button" title="View Uploaded Files"
                                                                    data-task_id="{{ $task->id }}">
                                                                    <img src="/images/google-drive.png"
                                                                        style="cursor: nwse-resize; width: 10px;">
                                                                </button>
                                                            </div>
                                                            @if (isset($task->message))
                                                                <div style="margin-bottom:10px;width: 100%;">
                                                                    @if (isset($task->is_audio) && $task->is_audio)
                                                                        <audio controls=""
                                                                            src="{{ \App\Helpers::getAudioUrl($task->message) }}"></audio>
                                                                    @else
                                                                        <div class="d-flex justify-content-between expand-row-msg"
                                                                            data-id="{{ $task->id }}">
                                                                            <span
                                                                                class="td-mini-container-{{ $task->id }}"
                                                                                style="margin:0px;">
                                                                                <?php
                                                                                if (! empty($task->message) && ! empty($task->task_subject)) {
                                                                                    $pos = strpos($task->message, $task->task_subject);
                                                                                    $length = strlen($task->task_subject);
                                                                                    if ($pos) {
                                                                                        $start = $pos + $length + 1;
                                                                                    } else {
                                                                                        $start = 0;
                                                                                    }
                                                                                } else {
                                                                                    $start = 0;
                                                                                }
                        ?>
                                                                                {{ substr($task->message, $start, 100) }}
                                                                            </span>
                                                                        </div>
                                                                        <div class="expand-row-msg"
                                                                            data-id="{{ $task->id }}">
                                                                            <span
                                                                                class="td-full-container-{{ $task->id }} hidden">
                                                                                {{ $task->message }}
                                                                            </span>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                        @if (auth()->user()->isAdmin())
                                                            <div style="margin-bottom:10px;width: 100%;">
                                                                <div class="expand-col dis-none">
                                                                    <label for="">Lead:</label>
                                                                    <div class="d-flex">
                                                                        <input type="text" style="width: 100%;"
                                                                            class="form-control quick-message-field input-sm"
                                                                            id="getMsg{{ $task->id }}"
                                                                            name="message" placeholder="Message"
                                                                            value="">
                                                                        <div style="max-width: 30px;">
                                                                            <button
                                                                                class="btn btn-sm btn-image send-message-lead"
                                                                                title="Send message"
                                                                                data-taskid="{{ $task->id }}"><img
                                                                                    src="{{ asset('images/filled-sent.png') }}" />
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @else
                                                        Private
                                                    @endif
                                                </td>
                                                <td class="p-2">
                                                    <div style="margin-bottom:10px;width: 100%;">
                                                        <div class="d-flex">
                                                            <input type="number" class="form-control"
                                                                name="approximates{{ $task->id }}"
                                                                value="{{ $task->approximate }}" min="1"
                                                                autocomplete="off">
                                                            <div style="max-width: 30px;">
                                                                <button class="btn btn-sm btn-image send-approximate-lead"
                                                                    title="Send approximate"
                                                                    onclick="funTaskInformationUpdatesTime('approximate',{{ $task->id }})"
                                                                    data-taskid="{{ $task->id }}"><img
                                                                        src="{{ asset('images/filled-sent.png') }}" />
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <?php $time_history = \App\Helpers\DevelopmentHelper::getFirstDeveloperTaskHistoryByDevTaskIDAndModel($task->id); ?>

                                                    @if (!empty($time_history))
                                                        @if (isset($time_history->is_approved) && $time_history->is_approved != 1)
                                                            <button data-task="{{ $time_history->developer_task_id }}"
                                                                data-id="{{ $time_history->id }}" title="approve"
                                                                data-type="TASK"
                                                                class="btn btn-sm approveEstimateFromshortcutButtonTaskPage">
                                                                <i class="fa fa-check" aria-hidden="true"></i>
                                                            </button>
                                                        @endif

                                                        @if ($task->task_start != 1)
                                                            <button data-task="{{ $task->id }}" title="Start Task"
                                                                data-type="TASK" class="btn btn-sm startDirectTask"
                                                                data-task-type="1">
                                                                <i class="fa fa-play" aria-hidden="true"></i>
                                                            </button>
                                                        @else
                                                            <input type="hidden" value="{{ $task->m_start_date }}"
                                                                class="m_start_date_"
                                                                id="m_start_date_{{ $task->id }}"
                                                                data-id="{{ $task->id }}"
                                                                data-value="{{ $task->m_start_date }}">
                                                            <button data-task="{{ $task->id }}" title="Start Task"
                                                                data-type="TASK" class="btn btn-sm startDirectTask"
                                                                data-task-type="2">
                                                                <i class="fa fa-stop" aria-hidden="true"></i>
                                                            </button>
                                                            <div id="time-counter_{{ $task->id }}"></div>
                                                        @endif

                                                        <button type="button" class="btn btn-xs show-timer-history"
                                                            title="Show timer History"
                                                            data-id="{{ $task->id }}"><i
                                                                class="fa fa-info-circle"></i></button>
                                                    @endif
                                                </td>
                                                @php
                                                    $single = \App\Helpers\DevelopmentHelper::getTaskById($task->id);
                                                @endphp
                                                <td class="p-2">
                                                    <div class="form-group d-flex">
                                                        <div class='input-group date cls-start-due-date'>
                                                            <input type="text" class="form-control"
                                                                name="start_dates{{ $task->id }}"
                                                                value="{{ $single->task_start_date }}"
                                                                autocomplete="off" />
                                                            <span class="input-group-addon"><span
                                                                    class="glyphicon glyphicon-calendar"></span></span>
                                                        </div>
                                                        <div style="max-width: 30px;">
                                                            <button class="btn btn-sm btn-image send-start_date-lead"
                                                                title="Send approximate"
                                                                onclick="funTaskInformationUpdatesTime('start_date',{{ $task->id }})"
                                                                data-taskid="{{ $task->id }}"><img
                                                                    src="{{ asset('images/filled-sent.png') }}" />
                                                            </button>
                                                        </div>
                                                    </div>
                                                    @if (!empty($single->task_start_date) && $single->task_start_date != '0000-00-00 00:00:00')
                                                        {{ $single->task_start_date }}
                                                    @endif

                                                    <div class="form-group d-flex">
                                                        <div class='input-group date cls-start-due-date'>
                                                            <input type="text" class="form-control"
                                                                name="due_dates{{ $task->id }}"
                                                                value="{{ $single->task_new_due_date }}"
                                                                autocomplete="off" />
                                                            <span class="input-group-addon"><span
                                                                    class="glyphicon glyphicon-calendar"></span></span>
                                                        </div>
                                                        <div style="max-width: 30px;">
                                                            <button class="btn btn-sm btn-image send-start_date-lead"
                                                                title="Send approximate"
                                                                onclick="funTaskInformationUpdatesTime('due_date',{{ $task->id }})"
                                                                data-taskid="{{ $task->id }}"><img
                                                                    src="{{ asset('images/filled-sent.png') }}" />
                                                            </button>
                                                        </div>
                                                    </div>
                                                    @if (!empty($single->task_new_due_date) && $single->task_new_due_date != '0000-00-00 00:00:00')
                                                        {{ $single->task_new_due_date }}
                                                    @endif
                                                </td>
                                                <td class="p-2">
                                                    <button type="button" class="btn btn-secondary btn-sm mt-2"
                                                        onclick="Showactionbtn('{{ $task->id }}')"><i
                                                            class="fa fa-arrow-down"></i></button>
                                                </td>
                                            </tr>
                                            <tr class="action-btn-tr-{{ $task->id }} d-none">
                                                <td class="font-weight-bold">Action</td>
                                                <td colspan="5">
                                                    <div>
                                                        <div class="row cls_action_box" style="margin:0px;">
                                                            @if (auth()->user()->isAdmin())
                                                                <button type="button"
                                                                    class='btn btn-image whatsapp-group pd-5'
                                                                    data-id="{{ $task->id }}"
                                                                    src="{{ asset('images/whatsapp.png') }}">
                                                                </button>
                                                                <button type="button"
                                                                    class='btn delete-single-task pd-5'
                                                                    data-id="{{ $task->id }}"><i
                                                                        class="fa fa-trash" aria-hidden="true"></i>
                                                                </button>
                                                            @endif
                                                            <button class='btn pd-5 task-set-reminder'
                                                                data-id="{{ $task->id }}"
                                                                data-frequency="{{ !empty($task->reminder_message) ? $task->frequency : '60' }}"
                                                                data-reminder_message="{{ !empty($task->reminder_message) ? $task->reminder_message : 'Plz update' }}"
                                                                data-reminder_from="{{ $task->reminder_from }}"
                                                                data-reminder_last_reply="{{ $task && !empty($task->reminder_last_reply) ? $task->reminder_last_reply : '' }}">
                                                                <i class="fa fa-bell @if (!empty($task->reminder_message) && $task->frequency > 0) {{ 'green-notification' }} @else {{ 'red-notification' }} @endif"
                                                                    aria-hidden="true"></i>
                                                            </button>
                                                            @if (
                                                                $special_task->users->contains(Auth::id()) ||
                                                                    $task->assign_from == Auth::id() ||
                                                                    $task->master_user_id == Auth::id() ||
                                                                    $task->second_master_user_id == Auth::id())
                                                                @if (auth()->user()->isAdmin())
                                                                    <button type="button"
                                                                        title="Verify the task by admin"
                                                                        class="btn btn-image task-complete pd-5"
                                                                        data-id="{{ $task->id }}"><img
                                                                            src="/images/completed-green.png" />
                                                                    </button>
                                                                @endif

                                                                @include('task-module.partials.show-status-history-btn')

                                                                <button type="button"
                                                                    class='btn btn-image ml-1 reminder-message pd-5'
                                                                    data-id="{{ $task->message_id }}"><img
                                                                        src='/images/reminder.png' /></button>
                                                                <button type="button" data-id="{{ $task->id }}"
                                                                    class="btn btn-file-upload pd-5">
                                                                    <i class="fa fa-upload" aria-hidden="true"></i>
                                                                </button>
                                                            @endif
                                                            <button type="button" class="btn preview-img-btn pd-5"
                                                                data-id="{{ $task->id }}">
                                                                <i class="fa fa-list" aria-hidden="true"></i>
                                                            </button>
                                                            @if (!$special_task->users->contains(Auth::id()) && $special_task->contacts()->count() == 0)
                                                                @if ($task->is_private == 1)
                                                                    <button disabled type="button"
                                                                        class="btn btn-image pd-5"><img
                                                                            src="{{ asset('images/private.png') }}" />
                                                                    </button>
                                                                @else
                                                                @endif
                                                            @endif

                                                            @if (
                                                                $special_task->users->contains(Auth::id()) ||
                                                                    ($task->assign_from == Auth::id() && $task->is_private == 0) ||
                                                                    ($task->assign_from == Auth::id() && $special_task->contacts()->count() > 0) ||
                                                                    Auth::id() == 6)
                                                                <a href="{{ route('task.show', $task->id) }}"
                                                                    class="btn btn-image pd-5" href=""><img
                                                                        src="{{ asset('images/view.png') }}" /></a>
                                                            @endif

                                                            @if ($task->is_flagged == 1)
                                                                <button type="button"
                                                                    class="btn btn-image flag-task pd-5"
                                                                    data-id="{{ $task->id }}"><img
                                                                        src="{{ asset('images/flagged.png') }}" />
                                                                </button>
                                                            @else
                                                                <button type="button"
                                                                    class="btn btn-image flag-task pd-5"
                                                                    data-id="{{ $task->id }}"><img
                                                                        src="{{ asset('images/unflagged.png') }}" />
                                                                </button>
                                                            @endif
                                                            <button class="btn btn-image expand-row-btn-lead"
                                                                data-task_id="{{ $task->id }}"><img
                                                                    src="/images/forward.png"></button>
                                                            <button class="btn btn-image set-remark"
                                                                data-task_id="{{ $task->id }}"
                                                                data-task_type="TASK"><i class="fa fa-comment"
                                                                    aria-hidden="true"></i>
                                                            </button>

                                                            <button class="btn btn-image mt-2 create-task-document"
                                                                title="Create document" data-id="{{ $task->id }}">
                                                                <i class="fa fa-file-text" aria-hidden="true"></i>
                                                            </button>
                                                            <button class="btn btn-image mt-2 show-created-task-document"
                                                                title="Show created document"
                                                                data-id="{{ $task->id }}">
                                                                <i class="fa fa-list" aria-hidden="true"></i>
                                                            </button>

                                                            <a title="Task Information: Update"
                                                                class="btn btn-image mt-2" href="javascript:void(0);"
                                                                onclick="funTaskInformationModal(this, '{{ $task->id }}')">
                                                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="2">
                <div class="row" style="margin:0px;">
                    <div class="col-12">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th width="8%">ID</th>
                                    <th width="7%">Date</th>
                                    <th width="8%" class="category">Category</th>
                                    <th width="14%">Task Details</th>
                                    <th width="5%">Assign to</th>
                                    <th width="5%">Reccuring</th>
                                    <th width="8%">ED</th>
                                    <th width="35%">Communication</th>
                                    <th width="10%">Actions &nbsp;
                                        <input type="checkbox" class="show-finished-task" name="show_finished"
                                            value="on">
                                        <label>Finished</label>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="statutory-row-render-view infinite-scroll-statutory-inner">
                                @if (count($data['task']['statutory_not_completed']) > 0)
                                    @foreach ($data['task']['statutory_not_completed'] as $task)
                                        @php $row_type = "statutory"; @endphp
                                        @include(
                                            'task-module.partials.task-row',
                                            compact('task', 'row_type'))
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="3">
                <div class="row" style="margin:0px;">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="8%">Date</th>
                                <th width="8%" class="category">Category</th>
                                <th width="20%">Task Details</th>
                                <th width="8%">Assign to</th>
                                <th width="8%">Completed On</th>
                                <th width="30%">Communication</th>
                                <th width="13%">Action</th>
                            </tr>
                        </thead>
                        <tbody class="completed-row-render-view infinite-scroll-completed-inner">
                            @if (count($data['task']['completed']) > 0)
                                @foreach ($data['task']['completed'] as $task)
                                    @php $row_type = "completed"; @endphp
                                    @include('task-module.partials.task-row', compact('task', 'row_type'))
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane" id="unassigned-tab">
                <div class="row">
                    <div class="col-xs-12 col-md-4 my-3">
                        <div class="border">
                            <form action="{{ route('task.assign.messages') }}" method="POST">
                                @csrf
                                <input type="hidden" name="selected_messages" id="selected_messages"
                                    value="">
                                <div class="form-group">
                                    <select class="selectpicker form-control input-sm" data-live-search="true"
                                        data-size="15" name="task_id" title="Choose a Task" required>
                                        @foreach ($data['task']['pending'] as $task)
                                            <option
                                                data-tokens="{{ $task->id }} {{ $task->task_subject }} {{ $task->task_details }} {{ array_key_exists($task->assign_from, $users) ? $users[$task->assign_from] : '' }} {{ array_key_exists($task->assign_to, $users) ? $users[$task->assign_to] : '' }}"
                                                value="{{ $task->id }}">{{ $task->id }}
                                                from {{ @$users[$task->assign_from] }} {{ $task->task_subject }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-xs btn-secondary" id="assignMessagesButton">
                                        Assign
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-8">
                        <div class="border">
                            <div class="row">
                                <div class="col-12 my-3" id="message-wrapper">
                                    <div id="message-container"></div>
                                </div>
                                <div class="col-xs-12 text-center">
                                    <button type="button" id="load-more-messages" data-nextpage="1"
                                        class="btn btn-xs btn-secondary">Load More
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div id="loading-image"
        style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
    </div>

    @include('task-module.task-update-modal')
    @include('task-module.partials.time-history-modal')


@endsection

@section('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>

    <script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-multiselect.min.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/recorder.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/record-voice-notes.js') }} "></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script>
        function Showactionbtn(id) {
            $(".action-btn-tr-" + id).toggleClass("d-none");
        }
        var main_page = 1;
        var isLoading_main = false;
        $(document).ready(function() {

          $(window).scroll(function() {
              if (($(window).scrollTop() + $(window).outerHeight()) >= ($(document).height() - 2500)) {
                  loadMore();
              }
          });
          function loadMore() {
              if (isLoading_main)
                  return;
              isLoading_main = true;
              type = $("#tasktype").val();
              var $loader = $('.infinite-scroll-products-loader');
              main_page = main_page + 1;
              $.ajax({
                  url: "/task?page=" + main_page,
                  type: 'GET',
                  data: $('.form-search-data').serialize(),
                  beforeSend: function() {
                      $loader.show();
                  },
                  success: function(data) {
                      $loader.hide();
                      if (type == "pending") {
                          $(".pending-row-render-view").append(data);
                      }
                      if (type == "statutory_not_completed") {
                          $(".statutory-row-render-view").append(data);
                      }
                      if (type == "completed") {
                          $(".completed-row-render-view").append(data);
                      }
                      if ('' === data.trim())
                          return;
                      isLoading_main = false;
                  },
                  error: function() {
                      $loader.hide();
                      isLoading_main = false;
                  }
              });
          }
        });
        $(document).on("click", ".previewDoc", function() {
            showPreviewDocumentModal();
            $("#previewDocSource").attr("src", "");
            var docUrl = $(this).data("docurl");
            var type = $(this).data("type");
            var type = jQuery.trim(type);
            if (type == "image") {
                $("#previewDocSource").attr("src", docUrl);
            } else {
                $("#previewDocSource").attr("src", "https://docs.google.com/gview?url=" + docUrl +
                "&embedded=true");
            }
        });
        $("#previewDoc").on("hidden", function() {
            $("#previewDocSource").attr("src", "");
        });
        var taskSuggestions = @json($search_suggestions, true);
        var searchSuggestions = @json($search_term_suggestions, true);
        var cached_suggestions = localStorage["message_suggestions"];
        var suggestions = [];
        $(document).on("click", ".expand-row-msg", function() {
            var id = $(this).data("id");
            var full = ".expand-row-msg .td-full-container-" + id;
            var mini = ".expand-row-msg .td-mini-container-" + id;
            $(full).toggleClass("hidden");
            $(mini).toggleClass("hidden");
        });
        $(document).on("click", ".expand-row", function() {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find(".td-mini-container").toggleClass("hidden");
                $(this).find(".td-full-container").toggleClass("hidden");
            }
        });
        $("#completion-datetime, #reminder-datetime, #sending-datetime #due-datetime").datetimepicker({
            format: "YYYY-MM-DD HH:mm"
        });
        $(".due-datetime").datetimepicker({
            format: "YYYY-MM-DD HH:mm"
        });
        $("#daily_activity_date").datetimepicker({
            format: "YYYY-MM-DD"
        });
        let users = @json($users);
        let isAdmin = <?php echo $isAdmin ? 1 : 0; ?>;
        $("#add-row").click(function() {
            table.addRow({});
        });
        $(".add-task").click(function() {
            var taskId = $(this).attr("data-id");
            $("#add-new-remark").find("input[name=\"id\"]").val(taskId);
        });
        $(".view-remark").click(function() {
            var taskId = $(this).attr("data-id");
            $.ajax({
                type: "GET",
                headers: {
                    "X-CSRF-TOKEN": jQuery("meta[name=\"csrf-token\"]").attr("content")
                },
                url: "{{ route('task.gettaskremark') }}",
                data: {
                    id: taskId,
                    module_type: "task"
                }
            }).done(response => {
                console.log(response);
                var html = "";
                $.each(response, function(index, value) {
                    html += " <p> " + value.remark + " <br> <small>By " + value.user_name +
                        " updated on " + moment(value.created_at).format("DD-M H:mm") +
                        " </small></p>";
                    html + "<hr>";
                });
                $("#view-remark-list").find("#remark-list").html(html);
            });
        });
        $(document).ready(function() {
            $(".multiselect").multiselect({
                nonSelectedText: "Status Filter",
                allSelectedText: "All",
                includeSelectAllOption: true
            });
            $(".js-example-basic-multiple").select2();
            $("#master_user_id").select2({
                width: "100%"
            });
            $("#search_by_user").select2({
                width: "100%"
            });

            $("#search_by_user").change(function() {
                $("#priority_user_id").select2({
                    tags: true,
                    width: "100%"
                }).val($(this).val()).trigger("change");
            });

            $("#priority_user_id").select2({
                tags: true,
                width: "100%"
            });
            var isLoading = false;
            var page = 1;
            $("#task_reminder_from").datetimepicker({
                format: "YYYY-MM-DD HH:mm"
            });
            var TaskToRemind = null;
            $(document).on("click", ".task-set-reminder", function() {
                showTaskReminderModal();
                let taskId = $(this).data("id");
                let frequency = $(this).data("frequency");
                let message = $(this).data("reminder_message");
                let reminder_from = $(this).data("reminder_from");
                let reminder_last_reply = $(this).data("reminder_last_reply");
                $("#frequency").val(frequency);
                $("#reminder_message").val(message);
                $("#taskReminderModal").find("#task_reminder_from").val(reminder_from);
                if (reminder_last_reply == 1) {
                    $("#taskReminderModal").find("#reminder_last_reply").prop("checked", true);
                } else {
                    $("#taskReminderModal").find("#reminder_last_reply_no").prop("checked", true);
                }
                TaskToRemind = taskId;
            });
            $(document).on("click", ".task-submit-reminder", function() {
                var taskReminderModal = $("#taskReminderModal");
                let frequency = $("#frequency").val();
                let message = $("#reminder_message").val();
                let task_reminder_from = taskReminderModal.find("#task_reminder_from").val();
                let reminder_last_reply = (taskReminderModal.find("#reminder_last_reply").is(":checked")) ?
                    1 : 0;
                $.ajax({
                    url: "{{ route('task.reminder.update') }}",
                    type: "POST",
                    success: function() {
                        toastr["success"]("Reminder updated successfully!");
                        $(".set-reminder img").css("background-color", "");
                        if (frequency > 0) {
                            $(".task-set-reminder img").css("background-color", "red");
                        }
                    },
                    data: {
                        task_id: TaskToRemind,
                        frequency: frequency,
                        message: message,
                        reminder_from: task_reminder_from,
                        reminder_last_reply: reminder_last_reply,
                        _token: "{{ csrf_token() }}"
                    }
                });
            });
            $(document).on("click", ".btn-call-data", function(e) {
                e.preventDefault();
                main_page = 1;
                isLoading_main = false;
                var type = $(this).data("type");
                if (type && type != "") {
                    type = $("#tasktype").val(type);
                }

                isLoading = true;
                type = $("#tasktype").val();
                var $loader = $(".infinite-scroll-products-loader");

                page = 1;
                $.ajax({
                    url: "{{ url('task') }}",
                    type: "GET",
                    data: $(".form-search-data").serialize(),
                    beforeSend: function() {
                        $loader.show();
                    },
                    success: function(response) {
                        $loader.hide();
                        if (type == "pending") {
                            $(".pending-row-render-view").html(response);
                        }
                        if (type == "statutory_not_completed") {
                            $(".statutory-row-render-view").html(response);
                        }
                        if (type == "completed") {
                            $(".completed-row-render-view").html(response);
                        }
                        isLoading = false;
                    },
                    error: function() {
                        $loader.hide();
                        isLoading = false;
                    }
                });
            });

            function getPriorityTaskList(id) {
                console.log("id", id);
                var selected_issue = [0];
                $("input[name =\"selected_issue[]\"]").each(function() {
                    if ($(this).prop("checked") == true) {
                        selected_issue.push($(this).val());
                    }
                });
                $.ajax({
                    url: "{{ route('task.list.by.user.id') }}",
                    type: "POST",
                    data: {
                        user_id: id,
                        _token: "{{ csrf_token() }}",
                        selected_issue: selected_issue
                    },
                    success: function(response) {
                        $(".show_task_priority").html(response.html);
                        if (IS_ADMIN_USER) {
                            $(".show_task_priority").sortable();
                        }
                    },
                    error: function() {
                        alert("There was error loading priority task list data");
                    }
                });
            }

            $(document).on("click", ".delete_priority", function(e) {
                var id = $(this).data("id");
                $("input[value =\"" + id + "\"]").prop("checked", false);
                $(this).closest("tr").remove();
            });
            $(".priority_model_btn").click(function() {
                showPriorityModal();
                $(".show_task_priority").html("");
                if (IS_ADMIN_USER) {
                    getPriorityTaskList($("#priority_user_id").val());
                } else {
                    getPriorityTaskList('{{ auth()->user()->id }}');
                }
            });
            $("#priority_user_id").change(function() {
                $("#sel_user_id").val($(this).val());
                if ($("#priority_model").is(":visible")) {
                    getPriorityTaskList($(this).val());
                }
            });
            $(document).on("submit", "#priorityForm", function(e) {
                e.preventDefault();
                if (IS_ADMIN_USER) {
                    $.ajax({
                        url: "{{ route('task.set.priority') }}",
                        type: "POST",
                        data: $(this).serialize(),
                        success: function(response) {
                            return false;
                        },
                        error: function() {
                            alert("There was error loading priority task list data");
                        }
                    });
                }
            });
            $("#task_subject, #task_details").autocomplete({
                source: function(request, response) {
                    var results = $.ui.autocomplete.filter(taskSuggestions, request.term);
                    response(results.slice(0, 10));
                }
            });
            $("#task_search").autocomplete({
                source: function(request, response) {
                    var results = $.ui.autocomplete.filter(searchSuggestions, request.term);
                    response(results.slice(0, 10));
                }
            });
            var hash = window.location.hash.substr(1);
            if (hash == "3") {
                $("a[href=\"#3\"]").click();
            }
            $(".selectpicker").selectpicker({
                selectOnTab: true
            });
            $("#multi_users").select2({
                placeholder: "Select a User"
            });
            $("#multi_contacts").select2({
                placeholder: "Select a Contact"
            });
            $(document).on("change", ".is_statutory", function() {
                if ($(".is_statutory").val() == 1) {
                    $("#calendar-task").hide();
                    $("#appointment-container").hide();
                    if (!isAdmin)
                        $("select[name=\"task_asssigned_to\"]").html(
                            "<option value=\"${current_userid}\">${ current_username }</option>");
                    $("#recurring-task").show();
                } else if ($(".is_statutory").val() == 2) {
                    $("#calendar-task").show();
                    $("#recurring-task").hide();
                    $("#appointment-container").hide();
                } else if ($(".is_statutory").val() == 3) {
                    $("#calendar-task").hide();
                    $("#recurring-task").hide();
                    $("#appointment-container").show();
                } else {
                    $("#calendar-task").hide();
                    $("#appointment-container").hide();
                    let select_html = "";
                    for (user of users)
                        select_html += `<option value="${user["id"]}">${user["name"]}</option>`;
                    $("select[name=\"task_asssigned_to\"]").html(select_html);
                    $("#recurring-task").hide();
                }
            });
            jQuery("#userList").select2({
                placeholder: "All user"
            });
            let r_s = "";
            let r_e = "{{ date('y - m - d ') }}";
            let start = r_s ? moment(r_s, "YYYY-MM-DD") : moment().subtract(6, "days");
            let end = r_e ? moment(r_e, "YYYY-MM-DD") : moment();
            jQuery("input[name=\"range_start\"]").val(start.format("YYYY-MM-DD"));
            jQuery("input[name=\"range_end\"]").val(end.format("YYYY-MM-DD"));

            function cb(start, end) {
                $("#reportrange span").html(start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY"));
            }

            $("#reportrange").daterangepicker({
                startDate: start,
                maxYear: 1,
                endDate: end,
                ranges: {
                    "Today": [moment(), moment()],
                    "Yesterday": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                    "Last 7 Days": [moment().subtract(6, "days"), moment()],
                    "Last 30 Days": [moment().subtract(29, "days"), moment()],
                    "This Month": [moment().startOf("month"), moment().endOf("month")],
                    "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1,
                        "month").endOf("month")]
                }
            }, cb);
            cb(start, end);
            $("#reportrange").on("apply.daterangepicker", function(ev, picker) {
                jQuery("input[name=\"range_start\"]").val(picker.startDate.format("YYYY-MM-DD"));
                jQuery("input[name=\"range_end\"]").val(picker.endDate.format("YYYY-MM-DD"));
            });
            $(".table").tablesorter();
            var container = $("div#message-container");
            var suggestion_container = $("div#suggestion-container");
            var erpUser = "{{ Auth::id() }}";
            var addElapse = false;

            function errorHandler(error) {
                console.error("error occured: ", error);
            }

            function approveMessage(element, message) {
                if (!$(element).attr("disabled")) {
                    $.ajax({
                        type: "POST",
                        url: "/whatsapp/approve/user",
                        data: {
                            _token: "{{ csrf_token() }}",
                            messageId: message.id
                        },
                        beforeSend: function() {
                            $(element).attr("disabled", true);
                            $(element).text("Approving...");
                        }
                    }).done(function(data) {
                        element.remove();
                        console.log(data);
                    }).fail(function(response) {
                        $(element).attr("disabled", false);
                        $(element).text("Approve");
                        console.log(response);
                        alert(response.responseJSON.message);
                    });
                }
            }

            function renderMessage(message, tobottom = null) {
                var domId = "waMessage_" + message.id;
                var current = $("#" + domId);
                var is_admin = "{{ Auth::user()->hasRole('Admin') }}";
                var is_hod_crm = "{{ Auth::user()->hasRole('HOD of CRM') }}";
                var users_array = @json($users);
                var leads_assigned_user = "";
                if (current.get(0)) {
                    return false;
                }
                // CHAT MESSAGES
                var row = $("<div class='talk-bubble'></div>");
                var body = $("<span id='message_body_" + message.id + "'></span>");
                var text = $("<div class='talktext'></div>");
                var edit_field = $(
                    "<textarea name=\"message_body\" rows=\"8\" class=\"form-control\" id=\"edit-message-textarea" +
                    message.id + "\" style=\"display: none;\">" + message.message + "</textarea>");
                var p = $("<p class='collapsible-message'></p>");
                var forward = $(
                    "<button class=\"btn btn-image forward-btn\" data-toggle=\"modal\" data-target=\"#forwardModal\" data-id=\"" +
                    message.id + "\"><img src=\"/images/forward.png\" /></button>");
                if (message.status == 0 || message.status == 5 || message.status == 6) {
                    var meta = $("<em>" + users_array[message.user_id] + " " + moment(message.created_at).format(
                        "DD-MM H:mm") + " </em>");
                    var mark_read = $("<a href data-url='/whatsapp/updatestatus?status=5&id=" + message.id +
                        "' style='font-size: 9px' class='change_message_status'>Mark as Read </a><span> | </span>"
                        );
                    var mark_replied = $("<a href data-url=\"/whatsapp/updatestatus?status=6&id=" + message.id +
                        "\" style=\"font-size: 9px\" class=\"change_message_status\">Mark as Replied </a>");
                    p.appendTo(text);
                    meta.appendTo(text);
                    if (message.status == 0) {
                        mark_read.appendTo(meta);
                    }
                    if (message.status == 0 || message.status == 5) {
                        mark_replied.appendTo(meta);
                    }
                    text.appendTo(row);
                    if (tobottom) {
                        row.appendTo(container);
                    } else {
                        row.prependTo(container);
                    }
                    forward.appendTo(meta);
                } else if (message.status == 4) {
                    var row = $("<div class='talk-bubble' data-messageid='" + message.id + "'></div>");
                    var chat_friend = (message.assigned_to != 0 && message.assigned_to != leads_assigned_user &&
                        message.user_id != message.assigned_to) ? " - " + users_array[message.assigned_to] : "";
                    var meta = $("<em>" + users_array[message.user_id] + " " + chat_friend + " " + moment(message
                            .created_at).format("DD-MM H:mm") + " <img id='status_img_" + message.id +
                        "' src='/images/1.png' /> &nbsp;</em>");
                    p.appendTo(text);
                    $(images).appendTo(text);
                    meta.appendTo(text);
                    text.appendTo(row);
                    if (tobottom) {
                        row.appendTo(container);
                    } else {
                        row.prependTo(container);
                    }
                } else {
                    if (message.sent == 0) {
                        var meta_content = "<em>" + (parseInt(message.user_id) !== 0 ? users_array[message
                                .user_id] : "Unknown") + " " + moment(message.created_at).format("DD-MM H:mm") +
                            " </em>";
                    } else {
                        var meta_content = "<em>" + (parseInt(message.user_id) !== 0 ? users_array[message
                                .user_id] : "Unknown") + " " + moment(message.created_at).format("DD-MM H:mm") +
                            " <img id='status_img_" + message.id + "' src='/images/1.png' /></em>";
                    }
                    var error_flag = "";
                    if (message.error_status == 1) {
                        error_flag = "<a href='#' class='btn btn-image fix-message-error' data-id='" + message.id +
                            "'><img src='/images/flagged.png' /></a><a href='#' class='btn btn-xs btn-secondary ml-1 resend-message' data-id='" +
                            message.id + "'>Resend</a>";
                    } else if (message.error_status == 2) {
                        error_flag = "<a href='#' class='btn btn-image fix-message-error' data-id='" + message.id +
                            "'><img src='/images/flagged.png' /><img src='/images/flagged.png' /></a><a href='#' class='btn btn-xs btn-secondary ml-1 resend-message' data-id='" +
                            message.id + "'>Resend</a>";
                    }
                    var meta = $(meta_content);
                    edit_field.appendTo(text);
                    if (!message.approved) {
                        var approveBtn = $(
                            "<button class='btn btn-xs btn-secondary btn-approve ml-3'>Approve</button>");
                        var editBtn =
                            " <a href=\"#\" style=\"font-size: 9px\" class=\"edit-message whatsapp-message ml-2\" data-messageid=\"" +
                            message.id + "\">Edit</a>";
                        approveBtn.click(function() {
                            approveMessage(this, message);
                        });
                        if (is_admin || is_hod_crm) {
                            approveBtn.appendTo(meta);
                            $(editBtn).appendTo(meta);
                        }
                    }
                    forward.appendTo(meta);
                    $(error_flag).appendTo(meta);
                }
                row.attr("id", domId);
                p.attr("data-messageshort", message.message);
                p.attr("data-message", message.message);
                p.attr("data-expanded", "true");
                p.attr("data-messageid", message.id);
                if (message.message) {
                    p.html(message.message);
                } else if (message.media_url) {
                    var splitted = message.content_type.split("/");
                    if (splitted[0] === "image" || splitted[0] === "m") {
                        var a = $("<a></a>");
                        a.attr("target", "_blank");
                        a.attr("href", message.media_url);
                        var img = $("<img></img>");
                        img.attr("src", message.media_url);
                        img.attr("width", "100");
                        img.attr("height", "100");
                        img.appendTo(a);
                        a.appendTo(p);
                    } else if (splitted[0] === "video") {
                        $("<a target='_blank' href='" + message.media_url + "'>" + message.media_url + "</a>")
                            .appendTo(p);
                    }
                }
                var has_product_image = false;
                if (message.images) {
                    var images = "";
                    message.images.forEach(function(image) {
                        images += image.product_id !== "" ? "<a href=\"/products/" + image.product_id +
                            "\" data-toggle=\"tooltip\" data-html=\"true\" data-placement=\"top\" title=\"<strong>Special Price: </strong>" +
                            image.special_price + "<br><strong>Size: </strong>" + image.size +
                            "<br><strong>Supplier: </strong>" + image.supplier_initials + "\">" : "";
                        images += "<div class=\"thumbnail-wrapper\"><img src=\"" + image.image +
                            "\" class=\"message-img thumbnail-200\" /><span class=\"thumbnail-delete whatsapp-image\" data-image=\"" +
                            image.key + "\">x</span></div>";
                        images += image.product_id !== "" ?
                            "<input type=\"checkbox\" name=\"product\" style=\"width: 20px; height: 20px;\" class=\"d-block mx-auto select-product-image\" data-id=\"" +
                            image.product_id + "\" /></a>" : "";
                        if (image.product_id !== "") {
                            has_product_image = true;
                        }
                    });
                    images += "<br>";
                    if (has_product_image) {
                        var show_images_wrapper = $("<div class=\"show-images-wrapper hidden\"></div>");
                        var show_images_button = $(
                            "<button type=\"button\" class=\"btn btn-xs btn-secondary show-images-button\">Show Images</button>"
                            );
                        $(images).appendTo(show_images_wrapper);
                        $(show_images_wrapper).appendTo(text);
                        $(show_images_button).appendTo(text);
                    } else {
                        $(images).appendTo(text);
                    }
                }
                p.appendTo(body);
                body.appendTo(text);
                meta.appendTo(text);
                var select_box = $(
                    "<input type=\"checkbox\" name=\"selected_message\" class=\"select-message\" data-id=\"" +
                    message.id + "\" />");
                select_box.appendTo(meta);
                if (has_product_image) {
                    var create_lead = $(
                        "<a href=\"#\" class=\"btn btn-xs btn-secondary ml-1 create-product-lead\">+ Lead</a>");
                    var create_order = $(
                        "<a href=\"#\" class=\"btn btn-xs btn-secondary ml-1 create-product-order\">+ Order</a>"
                        );
                    create_lead.appendTo(meta);
                    create_order.appendTo(meta);
                }
                text.appendTo(row);
                if (message.status == 7) {
                    if (tobottom) {
                        row.appendTo(suggestion_container);
                    } else {
                        row.prependTo(suggestion_container);
                    }
                } else {
                    if (tobottom) {
                        row.appendTo(container);
                    } else {
                        row.prependTo(container);
                    }
                }
                return true;
            }

            function pollMessages(page = null, tobottom = null, addElapse = null) {
                var qs = "";
                qs += "?erpUser=" + erpUser;
                if (page) {
                    qs += "&page=" + page;
                }
                if (addElapse) {
                    qs += "&elapse=3600";
                }
                var anyNewMessages = false;
                return new Promise(function(resolve, reject) {
                    $.getJSON("/whatsapp/pollMessagesCustomer" + qs, function(data) {
                        data.data.forEach(function(message) {
                            var rendered = renderMessage(message, tobottom);
                            if (!anyNewMessages && rendered) {
                                anyNewMessages = true;
                            }
                        });
                        if (page) {
                            $("#load-more-messages").text("Load More");
                            can_load_more = true;
                        }
                        if (anyNewMessages) {
                            anyNewMessages = false;
                        }
                        if (!addElapse) {
                            addElapse = true; // load less messages now
                        }
                        resolve();
                    });
                });
            }

            function startPolling() {
                setTimeout(function() {
                    pollMessages(null, null, addElapse).then(function() {
                        startPolling();
                    }, errorHandler);
                }, 1000);
            }

            $("a[href=\"#unassigned-tab\"]").on("click", function() {
                startPolling();
            });
            var can_load_more = true;
            $("#message-wrapper").scroll(function() {
                var top = $("#message-wrapper").scrollTop();
                var document_height = $(document).height();
                var window_height = $("#message-container").height();
                console.log($("#message-wrapper").scrollTop());
                console.log($(document).height());
                console.log($("#message-container").height());
                if (top >= (window_height - 1500)) {
                    console.log("should load", can_load_more);
                    if (can_load_more) {
                        var current_page = $("#load-more-messages").data("nextpage");
                        $("#load-more-messages").data("nextpage", current_page + 1);
                        var next_page = $("#load-more-messages").data("nextpage");
                        console.log(next_page);
                        $("#load-more-messages").text("Loading...");
                        can_load_more = false;
                        pollMessages(next_page, true);
                    }
                }
            });
            $(document).on("click", "#load-more-messages", function() {
                var current_page = $(this).data("nextpage");
                $(this).data("nextpage", current_page + 1);
                var next_page = $(this).data("nextpage");
                $("#load-more-messages").text("Loading...");
                pollMessages(next_page, true);
            });
        });

        $(document).on("click", ".send-message", function() {
            var thiss = $(this);
            var data = new FormData();
            var task_id = $(this).data("taskid");
            if ($(this).hasClass("onpriority")) {
                var message = $("#getMsgPopup" + task_id).val();
            } else {
                var message = $("#getMsg" + task_id).val();
            }
            if (message != "") {
                $("#message_confirm_text").html(message);
                $("#confirm_task_id").val(task_id);
                $("#confirm_message").val(message);
                $("#confirm_status").val(1);
                showConfirmMessageModal();
            }
        });
        $(document).on("click", ".confirm-messge-button", function() {
            var thiss = $(this);
            var data = new FormData();
            var task_id = $("#confirm_task_id").val();
            var message = $("#confirm_message").val();
            var status = $("#confirm_status").val();
            var is_audio = $("#is_audio_" + task_id).val();
            data.append("task_id", task_id);
            data.append("message", message);
            data.append("status", status);
            data.append("is_audio", is_audio);
            var checkedValue = [];
            var i = 0;
            $(".send_message_recepients:checked").each(function() {
                checkedValue[i++] = $(this).val();
            });
            data.append("send_message_recepients", checkedValue);
            if (message.length > 0) {
                if (!$(thiss).is(":disabled")) {
                    $.ajax({
                        url: "{{ route('whatsapp.send', 'task') }}",
                        type: "POST",
                        "dataType": "json",
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function() {
                            $(thiss).attr("disabled", true);
                        }
                    }).done(function(response) {
                        $(thiss).siblings("input").val("");
                        $("#getMsg" + task_id).val("");
                        $("#confirmMessageModal").modal("hide");
                        if (cached_suggestions) {
                            suggestions = JSON.parse(cached_suggestions);
                            if (suggestions.length == 10) {
                                suggestions.push(message);
                                suggestions.splice(0, 1);
                            } else {
                                suggestions.push(message);
                            }
                            localStorage["message_suggestions"] = JSON.stringify(suggestions);
                            cached_suggestions = localStorage["message_suggestions"];
                            console.log("EXISTING");
                            console.log(suggestions);
                        } else {
                            suggestions.push(message);
                            localStorage["message_suggestions"] = JSON.stringify(suggestions);
                            cached_suggestions = localStorage["message_suggestions"];
                            console.log("NOT");
                            console.log(suggestions);
                        }
                        $(thiss).attr("disabled", false);
                    }).fail(function(errObj) {
                        $("#confirmMessageModal").modal("hide");
                        $(thiss).attr("disabled", false);
                        alert("Could not send message");
                        console.log(errObj);
                    });
                }
            } else {
                alert("Please enter a message first");
            }
        });
        $(document).on("click", ".send-message-lead", function() {
            var thiss = $(this);
            var task_id = $(this).data("taskid");
            var message = $(this).siblings("input").val();
            if (message.length > 0) {
                if (!$(thiss).is(":disabled")) {
                    $.ajax({
                        url: "/whatsapp/sendMessage/task_lead",
                        type: "POST",
                        dataType: "json",
                        data: {
                            task_id: task_id,
                            message: message,
                            status: 2
                        },
                        beforeSend: function() {
                            $(thiss).attr("disabled", true);
                        }
                    }).done(function(response) {
                        console.log(response);
                        $(thiss).siblings("input").val("");
                        $(thiss).attr("disabled", false);
                    }).fail(function(errObj) {
                        console.log(errObj);
                        $(thiss).attr("disabled", false);
                        toastr["error"](errObj.responseJSON.message);
                    });
                }
            } else {
                alert("Please enter a message first");
            }
        });
        $(document).on("click", ".expand-row-btn", function() {
            $(this).closest("tr").find(".expand-col").toggleClass("dis-none");
        });
        $(document).on("click", ".expand-row-btn-lead", function() {
            var id = $(this).data("task_id");
            $(".expand-col-lead" + id).toggleClass("dis-none");
        });
        $(document).on("click", ".set-remark", function(e) {
            $(".remark_pop").val("");
            var task_id = $(this).data("task_id");
            $(".sub_remark").attr("data-task_id", task_id);
        });
        $(document).on("click", ".set-remark, .sub_remark", function(e) {
            var thiss = $(this);
            var task_id = $(this).data("task_id");
            var remark = $(".remark_pop").val();
            if (task_id != "") {
                $.ajax({
                    type: "POST",
                    url: "{{ route('task.create.get.remark') }}",
                    headers: {
                        "X-CSRF-TOKEN": jQuery("meta[name=\"csrf-token\"]").attr("content")
                    },
                    data: {
                        task_id: task_id,
                        remark: remark,
                        type: "TASK"
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                    }
                }).done(function(response) {
                    showPreviewTaskCreateModal();
                    if (response.code == 200) {
                        $("#loading-image").hide();
                        $(".task-create-get-list-view").html(response.data);
                        $(".remark_pop").val("");
                        toastr["success"](response.message);
                    } else {
                        $("#loading-image").hide();
                        $(".task-create-get-list-view").html("");
                        toastr["error"](response.message);
                    }
                }).fail(function(response) {
                    showPreviewTaskCreateModal();
                    $("#loading-image").hide();
                    $(".task-create-get-list-view").html("");
                    toastr["error"](response.message);
                });
            } else {
                toastr["error"]("Task not Found!");
            }
        });
        $(document).on("click", ".copy_remark", function(e) {
            var thiss = $(this);
            var remark_text = thiss.data("remark_text");
            copyToClipboard(remark_text);
            toastr["success"]("Copied the text: " + remark_text);
        });

        function copyToClipboard(text) {
            var sampleTextarea = document.createElement("textarea");
            document.body.appendChild(sampleTextarea);
            sampleTextarea.value = text; //save main text in it
            sampleTextarea.select(); //select textarea contenrs
            document.execCommand("copy");
            document.body.removeChild(sampleTextarea);
        }

        $(document).on("click", ".make-private-task", function() {
            var task_id = $(this).data("taskid");
            var thiss = $(this);
            $.ajax({
                type: "POST",
                url: "{{ url('task') }}/" + task_id + "/makePrivate",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    $(thiss).text("Changing...");
                }
            }).done(function(response) {
                if (response.task.is_private == 1) {
                    $(thiss).html("<img src=\"/images/private.png\" />");
                } else {
                    $(thiss).html("<img src=\"/images/not-private.png\" />");
                }
            }).fail(function(response) {
                $(thiss).html("<img src=\"/images/not-private.png\" />");
                console.log(response);
                alert("Could not make task private");
            });
        });
        $(document).on("click", ".collapsible-message", function() {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                var short_message = $(this).data("messageshort");
                var message = $(this).data("message");
                var status = $(this).data("expanded");
                if (status == false) {
                    $(this).addClass("expanded");
                    $(this).html(message);
                    $(this).data("expanded", true);
                    $(this).closest(".talktext").find(".message-img").removeClass("thumbnail-200");
                    $(this).closest(".talktext").find(".message-img").parent().css("width", "auto");
                } else {
                    $(this).removeClass("expanded");
                    $(this).html(short_message);
                    $(this).data("expanded", false);
                    $(this).closest(".talktext").find(".message-img").addClass("thumbnail-200");
                    $(this).closest(".talktext").find(".message-img").parent().css("width", "200px");
                }
            }
        });
        var selected_messages = [];
        $(document).on("click", ".select-message", function() {
            var message_id = $(this).data("id");
            if ($(this).prop("checked")) {
                selected_messages.push(message_id);
            } else {
                var index = selected_messages.indexOf(message_id);
                selected_messages.splice(index, 1);
            }
            console.log(selected_messages);
        });
        $("#assignMessagesButton").on("click", function(e) {
            e.preventDefault();
            if (selected_messages.length > 0) {
                $("#selected_messages").val(JSON.stringify(selected_messages));
                if ($(this).closest("form")[0].checkValidity()) {
                    $(this).closest("form").submit();
                } else {
                    $(this).closest("form")[0].reportValidity();
                }
            } else {
                alert("Please select atleast 1 message");
            }
        });
        var timer = 0;
        var delay = 200;
        var prevent = false;
        $(document).on("click", ".task-complete", function(e) {
            e.preventDefault();
            e.stopPropagation();
            var thiss = $(this);
            timer = setTimeout(function() {
                if (!prevent) {
                    var task_id = $(thiss).data("id");
                    var image = $(thiss).html();
                    var url = "/task/complete/" + task_id;
                    var current_user = <?php echo Auth::id(); ?>;
                    if (!$(thiss).is(":disabled")) {
                        $.ajax({
                            type: "GET",
                            url: url,
                            data: {
                                type: "complete"
                            },
                            beforeSend: function() {
                                $(thiss).text("Completing...");
                            }
                        }).done(function(response) {
                            if (response.task.is_verified != null) {
                                $(thiss).html("<img src=\"/images/completed.png\" />");
                            } else if (response.task.is_completed != null) {
                                $(thiss).html("<img src=\"/images/completed-green.png\" />");
                            } else {
                                $(thiss).html("<img src=\"/images/incomplete.png\" />");
                            }
                            $(thiss).attr("disabled", true);
                        }).fail(function(response) {
                            $(thiss).html(image);
                            alert("Could not mark as completed!");
                            toastr["error"](response.responseJSON.message);
                            console.log(response);
                        });
                    }
                }
                prevent = false;
            }, delay);
        });
        $(document).on("click", ".task-verify", function(e) {
            e.preventDefault();
            e.stopPropagation();
            clearTimeout(timer);
            prevent = true;
            var thiss = $(this);
            var task_id = $(this).data("id");
            var image = $(this).html();
            var url = "/task/complete/" + task_id;
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    type: "clear"
                },
                beforeSend: function() {
                    $(thiss).text("Clearing...");
                }
            }).done(function(response) {
                if (response.task.is_verified != null) {
                    $(thiss).html("<img src=\"/images/completed.png\" />");
                } else if (response.task.is_completed != null) {
                    $(thiss).html("<img src=\"/images/completed-green.png\" />");
                } else {
                    $(thiss).html("<img src=\"/images/incomplete.png\" />");
                }
                $(thiss).attr("disabled", true);
            }).fail(function(response) {
                $(thiss).html(image);
                alert("Could not clear the task!");
                console.log(response);
            });
        });
        $(document).on("click", ".resend-message", function() {
            var id = $(this).data("id");
            var thiss = $(this);
            $.ajax({
                type: "POST",
                url: "{{ url('whatsapp') }}/" + id + "/resendMessage",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    $(thiss).text("Sending...");
                }
            }).done(function(response) {
                $(thiss).html("<img src=\"/images/resend.png\" />");
            }).fail(function(response) {
                $(thiss).html("<img src=\"/images/resend.png\" />");
                console.log(response);
                alert("Could not resend message");
            });
        });
        $(document).on("click", "#addNoteButton", function() {
            var note_html = `<div class="form-group d-flex">
            <input type="text" class="form-control input-sm" name="note[]" placeholder="Note" value="">
            <button type="button" class="btn btn-image remove-note">x</button>
          </div>`;
            $("#note-container").append(note_html);
        });
        $(document).on("click", ".remove-note", function() {
            $(this).closest(".form-group").remove();
        });
        $(document).on("click", ".reminder-message", function() {
            showReminderMessageModal();
            var id = $(this).data("id");
            $("#reminderMessageModal").find("input[name=\"message_id\"]").val(id);
        });
        $(document).on("click", ".convert-task-appointment", function() {
            var thiss = $(this);
            var id = $(this).data("id");
            $.ajax({
                type: "POST",
                url: "{{ url('task') }}/" + id + "/convertTask",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    $(thiss).text("Converting...");
                }
            }).done(function(response) {
                $(thiss).closest("tr").addClass("row-highlight");
                $(thiss).remove();
            }).fail(function(response) {
                $(thiss).html("<img src=\"/images/details.png\" />");
                console.log(response);
                alert("Could not convert a task");
            });
        });
        $(document).on("click", ".flag-task", function() {
            var task_id = $(this).data("id");
            var thiss = $(this);
            $.ajax({
                type: "POST",
                url: "{{ route('task.flag') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    task_id: task_id
                },
                beforeSend: function() {
                    $(thiss).text("Flagging...");
                }
            }).done(function(response) {
                if (response.is_flagged == 1) {
                    $(thiss).html("<img src=\"/images/flagged.png\" />");
                } else {
                    $(thiss).html("<img src=\"/images/unflagged.png\" />");
                }
            }).fail(function(response) {
                $(thiss).html("<img src=\"/images/unflagged.png\" />");
                alert("Could not flag task!");
                console.log(response);
            });
        });
        var selected_tasks = [];
        $(document).on("click", ".select_task_checkbox", function() {
            var checked = $(this).prop("checked");
            var id = $(this).data("id");
            if (checked) {
                selected_tasks.push(id);
            } else {
                var index = selected_tasks.indexOf(id);
                selected_tasks.splice(index, 1);
            }
            console.log(selected_tasks);
        });
        $("#view_categories_button").on("click", function() {
            showAllTaskCategoryModal();
            $.ajax({
                type: "GET",
                url: "{{ url('task/categories') }}",
                data: {
                    _token: "{{ csrf_token() }}"
                }
            }).done(function(response) {
                console.log(response);
                $("#category-list-area").html(response);
            }).fail(function(response) {
                console.log("failed");
            });
        });
        $(document).on("click", ".submit-category-status", function(e) {
            e.preventDefault();
            var form = $(this).closest("form");
            $.ajax({
                type: "POST",
                url: form.attr("action"),
                data: form.serialize()
            }).done(function(response) {
                toastr["success"](response.message);
                $("#allTaskCategoryModal").modal("hide");
            }).fail(function(response) {});
        });
        $("#view_tasks_button").on("click", function() {
            var selected = $(this).data("selected");
            $(this).data("selected", 0);
            if (selected_tasks.length > 0) {
                $.ajax({
                    type: "POST",
                    url: "{{ url('task/loadView') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        selected_tasks: selected_tasks
                    }
                }).done(function(response) {
                    $("#task_view_body").html(response.view);
                    showTaskViewModal();
                }).fail(function(response) {
                    console.log(response);
                    alert("Could not load tasks view");
                });
            } else {
                alert("Please select atleast 1 task!");
            }
        });
        $("#send_message_button").on("click", function() {
            showSendMessageTextBoxModal()
        });
        $(".btn-send-brodcast-message").on("click", function() {
            if (selected_tasks.length > 0) {
                $.ajax({
                    type: "POST",
                    url: "{{ url('tasks/send-brodcast') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        selected_tasks: selected_tasks,
                        message: $(".message-for-brodcast").val()
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                    }
                }).done(function(response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        toastr["success"](response.message);
                        $("#send-message-text-box").modal("hide");
                    } else {
                        toastr["error"](response.message);
                    }
                }).fail(function(response) {
                    $("#loading-image").hide();
                    console.log(response);
                    toastr["error"](
                        "Request has been failed due to the server , please contact administrator");
                });
            } else {
                $("#loading-image").hide();
                toastr["error"]("Please select atleast 1 task!");
            }
        });
        $("#taskCreateButton").on("click", function(e) {
            e.preventDefault();
            var form = $(this).closest("form");
            var users = $("#multi_users").val();
            var contacts = $("#multi_contacts").val();
            var category = form.find("select[name=\"category\"]").val();
            console.log(users, contacts, category);
            if ($("#taskCreateForm")[0].checkValidity()) {
                if (users.length == 0 && contacts.length == 0) {
                    alert("Please select atleast one user or contact");
                } else {
                    if (category == "1") {
                        alert("Category is required!");
                    } else {
                        $.ajax({
                            type: "POST",
                            beforeSend: function() {
                                $("#loading-image").show();
                            },
                            url: form.attr("action"),
                            data: form.serialize(),
                            dataType: "json"
                        }).done(function(response) {
                            toastr["success"](response.message);
                            $("#loading-image").hide();
                            setTimeout(() => {
                                $('#btn-call-data-pending').click();
                            }, 1000);
                            if (response.code == 200) {}

                        }).fail(function(response) {
                            console.log(response);
                        });
                    }
                }
            } else {
                $("#taskCreateForm")[0].reportValidity();
            }
        });
        $("#task_category_selection").on("change", function() {
            var category_id = $(this).val();
            var is_approved = $(this).find("option:selected").data("approved");
            var is_admin = "{{ Auth::user()->hasRole('Admin') }}";
            if (is_admin == 1 && !is_approved) {
                $("#approveTaskCategoryButton").parent().removeClass("hidden");
            } else {
                $("#approveTaskCategoryButton").parent().addClass("hidden");
            }
            $("#deleteTaskCategoryButton").attr("data-id", category_id);
            $("#approveTaskCategoryButton").attr("data-id", category_id);
        });
        $("#deleteTaskCategoryButton").on("click", function() {
            var id = $(this).attr("data-id");
            if (id == "") {
                alert("Please select category first");
            } else {
                $.ajax({
                    type: "POST",
                    url: "{{ url('task_category') }}/" + id,
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: "DELETE"
                    }
                }).done(function() {
                    window.location.reload();
                }).fail(function(response) {
                    console.log(response);
                    alert("Could not delete a category");
                });
            }
        });
        $("#approveTaskCategoryButton").on("click", function() {
            var id = $(this).attr("data-id");
            if (id == "") {
                alert("Please select category first");
            } else {
                $.ajax({
                    type: "POST",
                    url: "{{ url('task_category') }}/" + id + "/approve",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                }).done(function() {
                    window.location.reload();
                }).fail(function(response) {
                    console.log(response);
                    alert("Could not approve a category");
                });
            }
        });
        $(document).on("click", ".whatsapp-group", function(e) {
            e.preventDefault();
            showWhatsappMessageModal()
            var id = $(this).attr("data-id");
            $("#task_id").val(id);
            $("#Preloader").show();
            $.ajax({
                type: "POST",
                async: false,
                url: "{{ route('task.add.whatsapp.group') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                }
            }).done(function(response) {
                console.log(response);
                $("#group_id").val(response.group_id);
                $("#Preloader").hide();
            });
        });
        $(document).on("keyup", ".save-milestone", function(event) {
            if (event.keyCode != 13) {
                return;
            }
            let id = $(this).attr("data-id");
            let total = $(this).val();
            $.ajax({
                url: "{{ action([\App\Http\Controllers\TaskModuleController::class, 'saveMilestone']) }}",
                data: {
                    total: total,
                    task_id: id
                },
                success: function() {
                    toastr["success"]("Milestone updated successfully!", "Message");
                },
                error: function(error) {
                    toastr["error"](error.responseJSON.message, "Message");
                    console.log(error.responseJSON.message);
                }
            });
        });
        $(document).on("change", ".select2-task-disscussion", function() {
            var $this = $(this);
            if ($this.val() != 0) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('task.json.details') }}",
                    data: {
                        task_id: $this.val()
                    },
                    dataType: "json"
                }).done(function(response) {
                    if (response.code == 200) {
                        $("#saveNewNotes").removeClass("dis-none");
                    } else {
                        alert(response.message);
                        $("#saveNewNotes").addClass("dis-none");
                    }
                }).fail(function(response) {
                    alert("Could not update!!");
                });
            } else {
                $("#saveNewNotes").addClass("dis-none");
            }
        });
        $(document).on("click", "#saveNewNotes", function() {
            var $this = $(this);
            $.ajax({
                beforeSend: function() {
                    toastr["info"]("Sending data!!", "info");
                },
                type: "POST",
                url: "{{ route('task.json.saveNotes') }}",
                data: $("#taskCreateForm").serialize(),
                dataType: "json"
            }).done(function(response) {
                if (response.code == 200) {
                    location.reload();
                }
            }).fail(function(response) {
                alert("Could not update!!");
            });
        });
        $(document).on("click", ".delete-task-btn", function() {
            var $this = $(this);
            var taskId = $this.data("id");
            if (taskId > 0) {
                $.ajax({
                    beforeSend: function() {
                        $("#loading-image").show();
                    },
                    type: "POST",
                    url: "/tasks/deleteTask",
                    headers: {
                        "X-CSRF-TOKEN": jQuery("meta[name=\"csrf-token\"]").attr("content")
                    },
                    data: {
                        id: taskId
                    },
                    dataType: "json"
                }).done(function(response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        $this.closest("td").remove();
                    }
                }).fail(function(response) {
                    $("#loading-image").hide();
                    alert("Could not update!!");
                });
            }
        });
        $(document).on("click", ".show-finished-task", function() {
            var $this = $(this);
            if ($this.is(":checked")) {
                $this.closest("table").find("tbody tr").hide();
                $this.closest("table").find("tbody tr").filter(function() {
                    return $(this).find(".task-complete img").attr("src") === "/images/completed-green.png";
                }).show();
            } else {
                $this.closest("table").find("tbody tr").show();
            }
        });
        $(document).on("change", "#is_milestone", function() {
            var is_milestone = $("#is_milestone").val();
            if (is_milestone == "1") {
                $("#no_of_milestone").attr("required", "required");
            } else {
                $("#no_of_milestone").removeAttr("required");
            }
        });
        $(document).on("change", ".assign-master-user", function() {
            let id = $(this).attr("data-id");
            let lead = $(this).attr("data-lead");
            let userId = $(this).val();
            if (userId == "") {
                return;
            }
            $.ajax({
                url: '{{ route('task.asign.master-user') }}',
                data: {
                    master_user_id: userId,
                    issue_id: id,
                    lead: lead
                },
                success: function() {
                    toastr["success"]("Master User assigned successfully!", "Message");
                },
                error: function(error) {
                    toastr["error"](error.responseJSON.message, "Message");
                }
            });
        });
        $(document).on("click", ".btn-file-upload", function() {
            var $this = $(this);
            var task_id = $this.data("id");
            showFileUploadAreaSectionModal();
            $("#hidden-task-id").val(task_id);
            $("#loading-image").hide();
        });
        var uploadedDocumentMap = {};
        Dropzone.options.documentDropzone = {
            url: '{{ route('task.upload-documents') }}',
            maxFilesize: 20, // MB
            addRemoveLinks: true,
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            success: function(file, response) {
                $("form").append("<input type=\"hidden\" name=\"document[]\" value=\"" + response.name + "\">");
                uploadedDocumentMap[file.name] = response.name;
            },
            removedfile: function(file) {
                file.previewElement.remove();
                var name = "";
                if (typeof file.file_name !== "undefined") {
                    name = file.file_name;
                } else {
                    name = uploadedDocumentMap[file.name];
                }
                $("form").find("input[name=\"document[]\"][value=\"" + name + "\"]").remove();
            },
            init: function() {}
        };
        $(document).on("click", ".btn-save-documents", function(e) {
            e.preventDefault();
            var $this = $(this);
            var formData = new FormData($this.closest("form")[0]);
            $.ajax({
                url: "/task/save-documents",
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                dataType: "json",
                data: $this.closest("form").serialize(),
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(data) {
                $("#loading-image").hide();
                if (data.code == 500) {
                    toastr["error"](data.message);
                } else {
                    toastr["success"]("Document uploaded successfully");
                }
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                toastr["error"](jqXHR.responseJSON.message);
                $("#loading-image").hide();
            });
        });
        $(document).on("click", ".preview-img-btn", function(e) {
            e.preventDefault();
            id = $(this).data("id");
            if (!id) {
                alert("No data found");
                return;
            }
            $.ajax({
                url: "/task/preview-img/" + id,
                type: "GET",
                success: function(response) {
                    showPreviewTaskImageModal();
                    $(".task-image-list-view").html(response);
                    initialize_select2();
                },
                error: function() {}
            });
        });

        function humanizeDuration(input, units) {
            var duration = moment().startOf("day").add(units, input),
                format = "";
            if (duration.hour() > 0) {
                format += "H:";
            }
            if (duration.minute() > 0) {
                format += "m:";
            }
            format += "s";
            return duration.format(format);
        }

        $(document).on("click", ".show-tracked-history", function() {
            showTimeTrackedModal();
            var issueId = $(this).data("id");
            var type = $(this).data("type");
            $("#time_tracked_div table tbody").html("");
            $.ajax({
                url: "{{ route('task.time.tracked.history') }}",
                data: {
                    id: issueId,
                    type: type
                },
                success: function(data) {
                    console.log(data);
                    if (data != "error") {
                        $.each(data.histories, function(i, item) {
                            var sec = parseInt(item["total_tracked"]);
                            $("#time_tracked_div table tbody").append(
                                "<tr>\
                                        <td>" + moment(item["starts_at_date"]).format("DD-MM-YYYY") + "</td>\
                                        <td>" + ((item["name"] != null) ? item["name"] : "") + "</td>\
                                        <td>" + humanizeDuration(sec, "s") + "</td>\
                                    </tr>"
                            );
                        });
                    }
                }
            });
        });

        $(document).on("click", ".show-timer-history", function() {
            showTimerHistoryModal();
            var issueId = $(this).data("id");
            $("#timer_tracked_modal table tbody").html("");
            $.ajax({
                url: "{{ route('task.timer.history') }}",
                data: {
                    id: issueId
                },
                success: function(data) {
                    console.log(data);
                    if (data != "error") {
                        $.each(data.histories, function(i, item) {
                            $("#timer_tracked_modal table tbody").append(
                                "<tr>\
                                        <td>" + moment(item["start_date"]).format("DD-MM-YYYY HH:mm:ss") + "</td>\
                                        <td>" + ((item["end_date"] != null) ? moment(item["end_date"]).format(
                                    "DD-MM-YYYY HH:mm:ss") : "Not Stop") + "</td>\
                                    </tr>"
                            );
                        });
                    }
                }
            });
        });
        $(document).on("click", ".create-hubstaff-task", function() {
            showCreatedTaskModal();
            var issueId = $(this).data("id");
            var type = $(this).data("type");
            $("#issueId").val(issueId);
            $("#type").val(type);

            $(this).css("display", "none");
        });
        $(document).on("keyup", ".search-category", function() {
            var input, filter, ul, li, a, i, txtValue;
            input = document.getElementById("myInput");
            filter = input.value.toUpperCase();
            ul = document.getElementById("myUL");
            li = ul.getElementsByTagName("li");
            for (i = 0; i < li.length; i++) {
                a = li[i].getElementsByTagName("a")[0];
                txtValue = a.textContent || a.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "";
                } else {
                    li[i].style.display = "none";
                }
            }
        });
        $(document).on("click", "#make_complete_button", function() {
            if (selected_tasks.length > 0) {
                var x = window.confirm("Are you sure you want to complete these tasks");
                if (!x) {
                    return;
                }
                $.ajax({
                    type: "POST",
                    url: "{{ url('task/bulk-complete') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        selected_tasks: selected_tasks
                    }
                }).done(function(response) {
                    location.reload();
                }).fail(function(response) {
                    console.log(response);
                    alert("Could not complete tasks");
                });
            } else {
                alert("Please select atleast 1 task!");
            }
        });
        $(document).on("click", "#make_delete_button", function() {
            if (selected_tasks.length > 0) {
                var x = window.confirm("Are you sure you want to bin these tasks");
                if (!x) {
                    return;
                }
                $.ajax({
                    type: "POST",
                    url: "{{ url('task/bulk-delete') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        selected_tasks: selected_tasks
                    }
                }).done(function(response) {
                    location.reload();
                }).fail(function(response) {
                    console.log(response);
                    alert("Could not delete tasks");
                });
            } else {
                alert("Please select atleast 1 task!");
            }
        });
        $(document).on("click", ".delete-single-task", function() {
            var id = $(this).data("id");
            if (!id) {
                return;
            }
            console.log(id);
            selected_tasks.push(id);
            console.log(selected_tasks);
            var x = window.confirm("Are you sure you want to bin these tasks");
            if (!x) {
                return;
            }
            $.ajax({
                type: "POST",
                url: "{{ url('task/bulk-delete') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    selected_tasks: selected_tasks
                }
            }).done(function(response) {
                location.reload();
            }).fail(function(response) {
                console.log(response);
                alert("Could not delete task");
            });
        });
        $(document).on("click", ".link-send-document", function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            var user_id = $(this).closest("tr").find(".send-message-to-id").val();
            var doc_id = $(this).data("media-id");
            $.ajax({
                url: "/task/send-document",
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                dataType: "json",
                data: {
                    id: id,
                    user_id: user_id,
                    doc_id: doc_id
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(data) {
                $("#loading-image").hide();
                toastr["success"]("Document sent successfully");
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                toastr["error"](jqXHR.responseJSON.message);
                $("#loading-image").hide();
            });
        });
        $(document).on("click", ".link-send-task", function(e) {
            var id = $(this).data("id");
            var task_id = $(this).data("media-id");
            var taskdata = $(this).parent().find("#selector_id").val();
            console.log(task_id, taskdata);
            var type = $(this).parent().find("#selector_id option[value=\"" + taskdata + "\"]").html().includes(
                "DEVTASK") ? "DEVTASK" : "TASK";
            if ($(this).parent().find("#selector_id").val() == "") {
                toastr["error"]("Please Select Task Or DevTask", "Message");
                return false;
            }
            $.ajax({
                url: "/task/send",
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                dataType: "json",
                data: {
                    id: id,
                    task_id: task_id,
                    taskdata: taskdata,
                    type: type
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#loading-image").hide();
                    toastr["success"]("File sent successfully");
                },
                error: function(error) {
                    toastr["error"];
                }
            });
        });
        $(document).on("click", ".send-to-sop-page", function() {
            var id = $(this).data("id");
            var task_id = $(this).data("media-id");
            $.ajax({
                url: "/task/send-sop",
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
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
                    toastr["success"]("File Added Successfully In Sop");
                },
                error: function(error) {
                    toastr["error"];
                }
            });
        });
        // on status change
        $(document).on("change", ".change-task-status", function() {
            let id = $(this).attr("data-id");
            let status = $(this).val();
            $.ajax({
                url: "{{ route('task.change.status') }}",
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                dataType: "json",
                data: {
                    "task_id": id,
                    "status": status
                },
                success: function(response) {
                    toastr["success"](response.message, "Message");
                },
                error: function(error) {
                    toastr["error"](error.responseJSON.message, "Message");
                }
            });
        });
        $(document).on("change", ".assign-user", function() {
            let id = $(this).attr("data-id");
            let userId = $(this).val();
            if (userId == "") {
                return;
            }
            $.ajax({
                url: "{{ route('task.AssignTaskToUser') }}",
                data: {
                    user_id: userId,
                    issue_id: id
                },
                success: function() {
                    toastr["success"]("User assigned successfully!", "Message");
                },
                error: function(error) {
                    toastr["error"](error.responseJSON.message, "Message");
                }
            });
        });
        $(document).on("click", ".show-user-history", function() {
            showUserHistoryModal();
            var issueId = $(this).data("id");
            $("#user_history_div table tbody").html("");
            $.ajax({
                url: "{{ route('task/user/history') }}",
                data: {
                    id: issueId
                },
                success: function(data) {
                    $.each(data.users, function(i, item) {
                        $("#user_history_div table tbody").append(
                            "<tr>\
                                        <td>" + moment(item["created_at"]).format("DD/MM/YYYY") + "</td>\
                                        <td>" + ((item["user_type"] != null) ? item["user_type"] : "-") + "</td>\
                                        <td>" + ((item["old_name"] != null) ? item["old_name"] : "-") + "</td>\
                                        <td>" + ((item["new_name"] != null) ? item["new_name"] : "-") + "</td>\
                                        <td>" + item["updated_by"] + "</td>\
                                    </tr>"
                        );
                    });
                }
            });
        });
        $(document).on("click", ".cmn-toggle", function() {
            let id = $(this).attr("task-id");
            var showstatus = "";
            $.ajax({
                url: "{{ route('task.CommunicationTaskStatus') }}",
                data: {
                    task_id: id
                },
                success: function(response) {
                    if (response.communication_status == 1) {
                        $("#getMsg" + id).prop("readonly", true);
                        $("#sendMsg" + id).prop("readonly", true);
                        showstatus = "Off";
                    }
                    if (response.communication_status == 0) {
                        $("#getMsg" + id).prop("readonly", false);
                        $("#sendMsg" + id).prop("readonly", false);
                        showstatus = "On";
                    }
                    toastr["success"]("Communication message status is " + showstatus + " successfully",
                        "Message");
                },
                error: function(error) {
                    toastr["error"](error.responseJSON.message, "Message");
                }
            });
        });
        $(document).on("click", ".recurring-history-btn", function() {
            var task_id = $(this).data("id");
            console.log(task_id);
            $.ajax({
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": jQuery("meta[name=\"csrf-token\"]").attr("content")
                },
                url: "{{ route('task.recurringHistory') }}",
                data: {
                    task_id: task_id
                }
            }).done(response => {
                if (response.success == true) {
                    showRecurringHistoryModal();
                    $("#recurring-history-modal").find(".recurring-history-list-view").html("");
                    $("#recurring-history-modal").find(".recurring-history-list-view").html(response.html);
                }
            }).fail(function(response) {
                alert("Could not fetch payments");
            });
        });

        $(document).on("change", ".task_for_modal", function(e) {
            var getTask = $(this).val();
            if (getTask == "time_doctor") {
                $(".time_doctor_project_section_modal").show();
                $(".time_doctor_account_section_modal").show();
            } else {
                $(".time_doctor_project_section_modal").hide();
                $(".time_doctor_account_section_modal").hide();
            }
        });

        $(document).on("submit", "#assign_task_form", function(event) {
            event.preventDefault();
            $.ajax({
                url: "{{ route('task.create.hubstaff_task') }}",
                type: "POST",
                data: $(this).serialize(),
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    toastr["success"]("created successfully!");
                    $("#create-d-task-modal").modal("hide");
                    $("#loading-image").hide();
                },
                error: function(error) {
                    toastr["error"](error.responseJSON.message);
                    $("#create-d-task-modal").modal("hide");
                    $("#loading-image").hide();
                }
            });
        });

        function applyDateTimePicker(eles) {
            if (eles.length) {
                eles.datetimepicker({
                    format: "YYYY-MM-DD HH:mm:ss",
                    sideBySide: true
                });
            }
        }

        $(document).ready(function() {
            applyDateTimePicker(jQuery(".cls-start-due-date"));
            $(document).on("click", ".create-task-document", function() {
                let task_id = $(this).data("id");
                if (task_id != "") {
                    $("#task_id").val($(this).data("id"));
                    showTaskGoogleDocModal();
                } else {
                    toastr["error"]("Task id not found.");
                }
            });

            $(document).on("click", ".show-created-task-document", function() {
                let task_id = $(this).data("id");
                if (task_id != "") {
                    showTaskGoogleDocListModal();
                    $.ajax({
                        type: "GET",
                        url: "{{ route('google-docs.task.show') }}",
                        data: {
                            task_id,
                            task_type: "TASK"
                        },
                        beforeSend: function() {
                            $("#loading-image").show();
                        },
                        success: function(response) {
                            $("#loading-image").hide();
                            if (typeof response.data != "undefined") {
                                $("#taskGoogleDocListModal tbody").html(response.data);
                            } else {
                                $("#taskGoogleDocListModal tbody").html(response);
                            }
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

            $(document).on("click", "#btnCreateTaskDocument", function() {
                let doc_type = $("#doc-type").val();
                let doc_name = $("#doc-name").val();
                let task_id = $("#task_id").val();

                if (doc_type.trim() == "") {
                    toastr["error"]("Select document type.");
                    return;
                }
                if (doc_name.trim() == "") {
                    toastr["error"]("Insert document name.");
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: "{{ route('google-docs.task') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        doc_type,
                        doc_name,
                        task_id,
                        task_type: "TASK",
                        attach_task_detail: true
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                        $("#btnCreateTaskDocument").attr("disabled", true);
                    },
                    success: function(response) {
                        if (response.status == true) {
                            toastr["success"](response.message);
                        } else {
                            toastr["error"](response.message);
                        }
                        $("#loading-image").hide();
                        $("#btnCreateTaskDocument").removeAttr("disabled");
                        $("#taskGoogleDocModal").modal("hide");
                        $("#doc-type").val(null);
                        $("#doc-name").val(null);
                        $("#doc-category").val(null);
                        $("#task_id").val(null);
                    },
                    error: function(response) {
                        toastr["error"]("Something went wrong!");
                        $("#loading-image").hide();
                        $("#btnCreateTaskDocument").removeAttr("disabled");
                    }
                });

            });

            $(document).on("click", ".upload-task-files-button", function(e) {
                e.preventDefault();
                showUploadeTaskFileModal();
                let task_id = $(this).data("task_id");
                $("#uploadeTaskFileModal #upload_task_id").val(task_id || 0);
            });

            $(document).on("click", ".view-task-files-button", function(e) {
                e.preventDefault();
                let task_id = $(this).data("task_id");
                showDisplayTaskFileUploadModal();
                $.ajax({
                    type: "get",
                    url: "{{ route('task.files.record') }}",
                    data: {
                        task_id
                    },
                    success: function(response) {
                        if (typeof response.data != "undefined") {
                            $("#taskFileUploadedData").html(response.data);
                        } else {
                            $("#taskFileUploadedData").html(response);
                        }
                    },
                    error: function(response) {
                        toastr["error"]("Something went wrong!");
                    }
                });
            });

            $(document).on("click", ".btn-trigger-rvn-modal", function() {
                var id = $(this).attr("data-id");
                var tid = $(this).attr("data-tid");
                showRecordVoiceNotesModal();
                $("#record-voice-notes #rvn_id").val(id);
                $("#record-voice-notes #rvn_tid").val(tid);
            });
            $("#record-voice-notes").on("hidden.bs.modal", function() {
                $("#rvn_stopButton").trigger("click");
                $("#formats").html("Format: start recording to see sample rate");
                $("#rvn_id").val(0);
                $("#rvn_tid").val(0);
                setTimeout(function() {
                    $("#recordingsList").html("");
                }, 2500);
            });
            $(document).on("click", ".show-hubtask-log-history", function() {
                var id = $(this).attr("data-id");
                $.ajax({
                    url: '{{ route('task.log.histories.show', '') }}/' + id,
                    dataType: "json",
                    data: {
                        id: id
                    },
                    success: function(response) {
                        if (response.status) {
                            showTaskCreateLogListingModal();
                            var html = "";
                            $.each(response.data, function(k, v) {
                                html += `<tr>
                                            <td> ${k + 1} </td>
                                            <td> ${v.task ? v.task.task_subject : ""} </td>
                                            <td> ${v.error_message ? v.error_message : ""} </td>
                                            <td> ${(v.user !== undefined) ? v.user.name : " - "} </td>
                                            <td> ${v.created_at} </td>
                                        </tr>`;
                            });
                            $("#task-create-log-listing").find(".task-log-listing-view").html(
                                html);
                        } else {
                            toastr["error"](response.error, "Message");
                        }
                    }
                });
            });
        });
    </script>
    <script type="text/javascript" src="{{ asset('js/common-helper.js') }} "></script>
    <script type="text/javascript">
        var siteHelpers = {

            quickCategoryAdd: function(ele) {
                var quickCategory = ele.closest("#shortcutsIds").find(".quickCategory");
                var quickCategoryId = quickCategory.children("option:selected").data("id");
                var textBox = ele.closest("div").find(".quick_category");
                if (textBox.val() == "") {
                    alert("Please Enter Category!!");
                    return false;
                }
                var params = {
                    method: "post",
                    data: {
                        _token: $("meta[name=\"csrf-token\"]").attr("content"),
                        name: textBox.val(),
                        quickCategoryId: quickCategoryId
                    },
                    url: "/add-reply-category"
                };

                if (quickCategoryId != "") {
                    siteHelpers.sendAjax(params, "afterQuickSubCategoryAdd");
                } else {
                    siteHelpers.sendAjax(params, "afterQuickCategoryAdd");
                }
            },
            afterQuickSubCategoryAdd: function(response) {
                $(".quick_category").val("");
                $(".quickSubCategory").append("<option value=\"[]\" data-id=\"" + response.data.id + "\">" +
                    response.data.name + "</option>");
            },
            afterQuickCategoryAdd: function(response) {
                $(".quick_category").val("");
                $(".quickCategory").append("<option value=\"[]\" data-id=\"" + response.data.id + "\">" + response
                    .data.name + "</option>");
            },
            deleteQuickCategory: function(ele) {
                var quickCategory = ele.closest("#shortcutsIds").find(".quickCategory");
                if (quickCategory.val() == "") {
                    alert("Please Select Category!!");
                    return false;
                }
                var quickCategoryId = quickCategory.children("option:selected").data("id");
                if (!confirm("Are sure you want to delete category?")) {
                    return false;
                }
                var params = {
                    method: "post",
                    data: {
                        _token: $("meta[name=\"csrf-token\"]").attr("content"),
                        id: quickCategoryId
                    },
                    url: "/destroy-reply-category"
                };
                siteHelpers.sendAjax(params, "pageReload");
            },
            deleteQuickSubCategory: function(ele) {
                var quickSubCategory = ele.closest("#shortcutsIds").find(".quickSubCategory");
                if (quickSubCategory.val() == "") {
                    alert("Please Select Sub Category!!");
                    return false;
                }
                var quickSubCategoryId = quickSubCategory.children("option:selected").data("id");
                if (!confirm("Are sure you want to delete sub category?")) {
                    return false;
                }
                var params = {
                    method: "post",
                    data: {
                        _token: $("meta[name=\"csrf-token\"]").attr("content"),
                        id: quickSubCategoryId
                    },
                    url: "/destroy-reply-category"
                };
                siteHelpers.sendAjax(params, "pageReload");
            },
            deleteQuickComment: function(ele) {
                var quickComment = ele.closest("#shortcutsIds").find(".quickCommentEmail");
                if (quickComment.val() == "") {
                    alert("Please Select Quick Comment!!");
                    return false;
                }
                var quickCommentId = quickComment.children("option:selected").data("id");
                if (!confirm("Are sure you want to delete comment?")) {
                    return false;
                }
                var params = {
                    method: "DELETE",
                    data: {
                        _token: $("meta[name=\"csrf-token\"]").attr("content")
                    },
                    url: "/reply/" + quickCommentId
                };
                siteHelpers.sendAjax(params, "pageReload");
            },
            pageReload: function(response) {
                location.reload();
            },
            quickCommentAdd: function(ele) {
                var textBox = ele.closest("div").find(".quick_comment");
                var quickCategory = ele.closest("#shortcutsIds").find(".quickCategory");
                var quickSubCategory = ele.closest("#shortcutsIds").find(".quickSubCategory");
                if (textBox.val() == "") {
                    alert("Please Enter New Quick Comment!!");
                    return false;
                }
                if (quickCategory.val() == "") {
                    alert("Please Select Category!!");
                    return false;
                }
                var quickCategoryId = quickCategory.children("option:selected").data("id");
                var quickSubCategoryId = quickSubCategory.children("option:selected").data("id");
                var formData = new FormData();
                formData.append("_token", $("meta[name=\"csrf-token\"]").attr("content"));
                formData.append("reply", textBox.val());
                formData.append("category_id", quickCategoryId);
                formData.append("sub_category_id", quickSubCategoryId);
                formData.append("model", "Approval Lead");
                var params = {
                    method: "post",
                    data: formData,
                    url: "/reply"
                };
                siteHelpers.sendFormDataAjax(params, "afterQuickCommentAdd");
            },
            afterQuickCommentAdd: function(reply) {
                $(".quick_comment").val("");
                $(".quickCommentEmail").append($("<option>", {
                    value: reply,
                    text: reply
                }));
            },
            changeQuickCategory: function(ele) {

                var selectedOption = ele.find("option:selected");
                var dataValue = selectedOption.data("value");

                ele.closest("#shortcutsIds").find(".quickSubCategory").empty();
                ele.closest("#shortcutsIds").find(".quickSubCategory").append($("<option>", {
                    value: "",
                    text: "Select Sub Category"
                }));
                dataValue.forEach(function(category) {
                    ele.closest("#shortcutsIds").find(".quickSubCategory").append($("<option>", {
                        value: category.name,
                        text: category.name,
                        "data-id": category.id
                    }));
                });

                if (ele.val() != "") {
                    var replies = JSON.parse(ele.val());
                    ele.closest("#shortcutsIds").find(".quickCommentEmail").empty();
                    ele.closest("#shortcutsIds").find(".quickCommentEmail").append($("<option>", {
                        value: "",
                        text: "Quick Reply"
                    }));
                    replies.forEach(function(reply) {
                        ele.closest("#shortcutsIds").find(".quickCommentEmail").append($("<option>", {
                            value: reply.reply,
                            text: reply.reply,
                            "data-id": reply.id
                        }));
                    });
                }
            },
            changeQuickComment: function(ele) {
                $("#getMsg" + ele.attr("data-id")).val(ele.val());

                var userEmaillUrl = "/email/email-frame-info/" + $("#reply_email_id").val();;
                var senderName = "Hello " + $("#sender_email_address").val().split("@")[0] + ",";

                $("#reply-message").val(senderName);

                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": $("meta[name=\"csrf-token\"]").attr("content")
                    },
                    url: userEmaillUrl,
                    type: "get"
                }).done(function(response) {
                    $("#reply-message").val(senderName + "\n\n" + ele.val() + "\n\n" + response);
                }).fail(function(errObj) {});

            },
            changeQuickSubCategory: function(ele) {
                var selectedOption = ele.find("option:selected");
                var dataValue = selectedOption.data("id");

                var userEmaillUrl = "/email/email-replise/" + dataValue;

                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": $("meta[name=\"csrf-token\"]").attr("content")
                    },
                    url: userEmaillUrl,
                    type: "get"
                }).done(function(response) {

                    if (response != "") {
                        var replies = JSON.parse(response);
                        ele.closest("#shortcutsIds").find(".quickCommentEmail").empty();
                        ele.closest("#shortcutsIds").find(".quickCommentEmail").append($("<option>", {
                            value: "",
                            text: "Quick Reply"
                        }));
                        replies.forEach(function(reply) {
                            ele.closest("#shortcutsIds").find(".quickCommentEmail").append($(
                                "<option>", {
                                    value: reply.reply,
                                    text: reply.reply,
                                    "data-id": reply.id
                                }));
                        });
                    }

                }).fail(function(errObj) {});
            }
        };

        $.extend(siteHelpers, common);

        $(document).on("click", ".quick_category_add", function() {
            siteHelpers.quickCategoryAdd($(this));
        });
        $(document).on("click", ".delete_category", function() {
            siteHelpers.deleteQuickCategory($(this));
        });
        $(document).on("click", ".delete_sub_category", function() {
            siteHelpers.deleteQuickSubCategory($(this));
        });
        $(document).on("click", ".delete_quick_comment", function() {
            siteHelpers.deleteQuickComment($(this));
        });
        $(document).on("click", ".quick_comment_add", function() {
            siteHelpers.quickCommentAdd($(this));
        });
        $(document).on("change", ".quickCategory", function() {
            siteHelpers.changeQuickCategory($(this));
        });
        $(document).on("change", ".quickCommentEmail", function() {
            siteHelpers.changeQuickComment($(this));
        });
        $(document).on("change", ".quickSubCategory", function() {
            siteHelpers.changeQuickSubCategory($(this));
        });

        function funTaskInformationUpdatesTime(type, id) {
            if (type == "start_date") {
                if (confirm("Are you sure, do you want to update?")) {
                    siteLoader(1);
                    let mdl = funGetTaskInformationModal();
                    jQuery.ajax({
                        headers: {
                            "X-CSRF-TOKEN": jQuery("meta[name=\"csrf-token\"]").attr("content")
                        },
                        url: "{{ route('task.update.start-date') }}",
                        type: "POST",
                        data: {
                            task_id: id,
                            value: $("input[name=\"start_dates" + id + "\"]").val()
                        }
                    }).done(function(res) {
                        siteLoader(0);
                        siteSuccessAlert(res);
                    }).fail(function(err) {
                        siteLoader(0);
                        siteErrorAlert(err);
                    });
                }
            } else if (type == "due_date") {
                if (confirm("Are you sure, do you want to update?")) {
                    siteLoader(1);
                    let mdl = funGetTaskInformationModal();
                    jQuery.ajax({
                        headers: {
                            "X-CSRF-TOKEN": jQuery("meta[name=\"csrf-token\"]").attr("content")
                        },
                        url: "{{ route('task.update.due-date') }}",
                        type: "POST",
                        data: {
                            task_id: id,
                            value: $("input[name=\"due_dates" + id + "\"]").val()
                        }
                    }).done(function(res) {
                        siteLoader(0);
                        siteSuccessAlert(res);
                    }).fail(function(err) {
                        siteLoader(0);
                        siteErrorAlert(err);
                    });
                }
            } else if (type == "cost") {
                if (confirm("Are you sure, do you want to update?")) {
                    siteLoader(1);
                    let mdl = funGetTaskInformationModal();
                    jQuery.ajax({
                        headers: {
                            "X-CSRF-TOKEN": jQuery("meta[name=\"csrf-token\"]").attr("content")
                        },
                        url: "{{ route('task.update.cost') }}",
                        type: "POST",
                        data: {
                            task_id: id,
                            cost: mdl.find("input[name=\"cost\"]").val()
                        }
                    }).done(function(res) {
                        siteLoader(0);
                        siteSuccessAlert(res);
                    }).fail(function(err) {
                        siteLoader(0);
                        siteErrorAlert(err);
                    });
                }
            } else if (type == "approximate") {
                if (confirm("Are you sure, do you want to update?")) {
                    siteLoader(1);
                    let mdl = funGetTaskInformationModal();
                    jQuery.ajax({
                        headers: {
                            "X-CSRF-TOKEN": jQuery("meta[name=\"csrf-token\"]").attr("content")
                        },
                        url: "{{ route('task.update.approximate') }}",
                        type: "POST",
                        data: {
                            task_id: id,
                            approximate: $("input[name=\"approximates" + id + "\"]").val(),
                            remark: mdl.find("textarea[name=\"remark\"]").val()
                        }
                    }).done(function(res) {
                        siteLoader(0);
                        siteSuccessAlert(res);
                    }).fail(function(err) {
                        siteLoader(0);
                        siteErrorAlert(err);
                    });
                }
            }
        }

        $(document).on("click", ".approveEstimateFromshortcutButtonTaskPage", function(event) {
            var element = $(this);
            if (confirm("Are you sure, do you want to approve this task?")) {
                event.preventDefault();
                let type = $(this).data("type");
                let task_id = $(this).data("task");
                let history_id = $(this).data("id");
                $.ajax({
                    url: "/task/time/history/approve",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        approve_time: history_id,
                        developer_task_id: task_id,
                        user_id: 0
                    },
                    success: function(response) {
                        element.hide("slow");
                        element.closest("tr").css("background", "transparent");
                        toastr["success"]("Successfully approved", "success");
                    },
                    error: function(error) {
                        toastr["error"](error.responseJSON.message);
                    }
                });
            }
        });

        $(document).on("click", ".startDirectTask", function(event) {

            let task_type = $(this).data("task-type");

            if (task_type == 1) {
                var msg = "Are you sure, do you want to start this task?";
            } else {
                var msg = "Are you sure, do you want to end this task?";
            }

            if (confirm(msg)) {
                event.preventDefault();
                let type = $(this).data("type");
                let task_id = $(this).data("task");

                $.ajax({
                    url: "/task/time/history/start",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        developer_task_id: task_id,
                        task_type: task_type
                    },
                    success: function(response) {
                        if (task_type == 1) {
                            toastr["success"]("Successfully start", "success");
                        } else {
                            toastr["success"]("Successfully end", "success");
                        }
                        window.location.reload();
                    },
                    error: function(error) {
                        toastr["error"](error.responseJSON.message);
                    }
                });
            }
        });

        $(document).ready(function() {
            $(".m_start_date_").each(function() {
                var elementId = $(this).attr("id");

                var task_id = $("#" + elementId).attr("data-id");
                var inputTime = $("#" + elementId).attr("data-value");

                startTime = new Date(inputTime);

                (function(startTime, id) {
                    setInterval(function() {
                        updateTimeCounter(startTime, id);
                    }, 1000);
                })(startTime, task_id);
            });
        });

        function updateTimeCounter(startTime, id) {
            if (startTime && !isNaN(startTime.getTime())) {
                var currentTime = new Date();

                var timeDifference = currentTime - startTime;

                var hours = Math.floor(timeDifference / (60 * 60 * 1000));
                var minutes = Math.floor((timeDifference % (60 * 60 * 1000)) / (60 * 1000));
                var seconds = Math.floor((timeDifference % (60 * 1000)) / 1000);

                var counterText = pad(hours) + ":" + pad(minutes) + ":" + pad(seconds);

                $("#time-counter_" + id).text(counterText);
            }
        }

        // Function to pad single digits with leading zeros
        function pad(number) {
            return (number < 10 ? "0" : "") + number;
        }

        @php
            $filtered_statuses = [];
            foreach ($task_statuses as $status) {
                $filtered_statuses[] = ['id' => $status['id'], 'name' => $status['name']];
            }
        @endphp

        const taskStatusList = @json($filtered_statuses);

        function findStatusNameById(id) {
            const foundStatus = taskStatusList.find(status => status.id === id);
            return foundStatus ? foundStatus.name : null;
        }

        $(document).on('click', '.show-status-history', function() {
            var data = $(this).data('history');
            var issueId = $(this).data('id');

            showStatusQuickHistoryModal();
            $('#status_quick_history_modal table tbody').html('');
            $.ajax({
                url: "{{ route('development/status/history') }}",
                data: {
                    id: issueId,
                    type: 'task'
                },
                success: function(data) {
                    if (data != 'error') {
                        $.each(data, function(i, item) {
                            if (item['is_approved'] == 1) {
                                var checked = 'checked';
                            } else {
                                var checked = '';
                            }
                            $('#status_quick_history_modal table tbody').append(
                                '<tr>\
                                      <td>' + moment(item['created_at']).format('DD/MM/YYYY') + '</td>\
                                      <td>' + ((item['old_value'] != null) ? findStatusNameById(parseInt(item[
                                    'old_value'])) : '-') + '</td>\
                                      <td>' + findStatusNameById(parseInt(item['new_value'])) + '</td>\
                                      <td>' + item['name'] + '</td>\
                                  </tr>'
                            );
                        });
                    }
                }
            });
        });

        $(document).on('click', '.expand-row', function() {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });
    </script>
@endsection
