@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
    <style type="text/css">
        .preview-category input.form-control {
            width: auto;
        }

        .break {
            word-break: break-all !important;
        }
    </style>


    <style>
        th {
            border: 1px solid black;
        }

        table {
            border-collapse: collapse;
        }

        .ui-icon, .ui-widget-content .ui-icon {
            background-image: none;
        }

        #bug_tracking_maintable {
            font-size: 12px;
        }

        .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {
            padding: 5px;
        }

        #bug_tracking_maintable .btn {
            padding: 1px 3px 0px 4px !important;
            margin-top: 0px !important;
        }

        #change_dropdown_div .bootstrap-select {
            width: 160px;
        }

        .btn-change-status-bug, .btn-change-assignee-bug, .btn-change-severity-bug {
            height: 30px;
            margin-top: 2px;
        }

        .bug-task-note {
            height: 30px;
            width: 100%;
            text-align: center;
            margin-top: 0px;
            background: #ebe7e2;
            font-weight: bold;
            padding-top: 5px;
        }
    </style>
    <div class="row" id="common-page-layout">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
        </div>
        <br>
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class=" col-md-12">
                    <div class="h" style="margin-bottom:10px;">
                        <div class="row">
                            <form class="form-inline message-search-handler" method="get">
                                <div class="col">

                                    <div class="form-group col-md-2 cls_filter_inputbox p-2 mr-2" style="width: 150px;">
                                        @php
                                            $bug_type = request('bugtype');
                                        @endphp

                                        <select class="form-control selectpicker" name="bug_type[]" multiple
                                                id="bug_type" title="Select BugType">
                                            <option value="">Select BugType</option>
                                            @foreach ($bugTypes as $bugtype)
                                                {
                                                <option value="{{ $bugtype->id }}"
                                                        @if($bug_type == $bugtype->id) selected @endif>{{ $bugtype->name }}</option>
                                                }
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2 cls_filter_inputbox p-2 mr-2" style="width: 200px;">
                                        @php
                                            $bug_environment = request('bug_enviornment');
                                        @endphp
                                        <select class="form-control selectpicker" name="bug_enviornment[]" multiple
                                                id="bug_enviornment" title="Select BugEnvironment">
                                            <option value="">Select BugEnvironment</option>
                                            @foreach ($bugEnvironments as $bugenvironment)
                                                {
                                                <option value="{{ $bugenvironment->id }}"
                                                        @if ($bug_environment == $bugenvironment->id) selected @endif>{{ $bugenvironment->name }}</option>
                                                }
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2 cls_filter_inputbox p-2 mr-2" style="width: 170px;">
                                        @php
                                            $bug_severity = request('bug_severity');
                                        @endphp
                                        <select class="form-control selectpicker" name="bug_severity[]" multiple
                                                id="bug_severity" title="Select BugSeverity">
                                            <option value="">Select BugSeverity</option>
                                            @foreach ($bugSeveritys as $bugseverity)
                                                {
                                                <option value="{{ $bugseverity->id }}"
                                                        @if ($bug_severity == $bugseverity->id) selected @endif>{{ $bugseverity->name }}</option>
                                                }
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2 cls_filter_inputbox p-2 mr-2" style="width: 160px;">
                                        @php
                                            $bug_status = request('bugstatus');
                                        @endphp
                                        <select class="form-control selectpicker" name="bug_status[]" multiple
                                                id="bug_status" title="Select BugStatus">
                                            <option value="">Select BugStatus</option>
                                            @foreach ($bugStatuses as $bugstatus)
                                                {
                                                <option value="{{ $bugstatus->id }}"
                                                        @if ($bug_status == $bugstatus->id) selected @endif>{{ $bugstatus->name }}</option>
                                                }
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group  cls_filter_inputbox p-2 mr-3"
                                         style="width: 200px;margin-bottom: 10px;">
                                        @php
                                            $module_id = request('module_id');
                                        @endphp
                                        <select class="form-control selectpicker" name="module_id[]" multiple
                                                id="module_id" title="Select Module">
                                            <option value="">Select Module</option>
                                            @foreach($filterCategories as  $filterCategory)
                                                <option value="{{$filterCategory}}">{{$filterCategory}} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group" style="width: 120px;margin-bottom: 10px;">
                                        <input name="bug_id" type="text" class="form-control" placeholder="Bug ID"
                                               id="bug-id-search" data-allow-clear="true" style="width: 120px;"
                                               value="@php if(isset($_REQUEST['bug_main_id']) && $_REQUEST['bug_main_id']>0) { echo $_REQUEST['bug_main_id']; } @endphp" />
                                    </div>
                                    <div class="form-group" style="width: 200px;margin-bottom: 10px;">
                                        <input name="step_to_reproduce" type="text" class="form-control"
                                               placeholder="Search Reproduce" id="bug-search" data-allow-clear="true" />
                                    </div>

                                    <div class="form-group" style="width: 200px;margin-bottom: 10px;">
                                        <input name="summary" type="text" class="form-control"
                                               placeholder="Search Summary" id="bug-summary" data-allow-clear="true" />
                                    </div>

                                    <div class="form-group cls_filter_inputbox p-2 mr-2" style="width: 200px;">
                                        @php
                                            $website = request('website');
                                        @endphp
                                        <select class="form-control selectpicker" name="website[]" multiple id="website"
                                                title="Select Website">
                                            <option value="">Select Website</option>
                                            @foreach($filterWebsites as  $filterWebsite)

                                                <option value="{{$filterWebsite->id}}">{{$filterWebsite->title}} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2 cls_filter_inputbox p-2 mr-2" style="width: 150px;">
                                        @php
                                            $assign_to_user = request('assign_to_user');
                                        @endphp
                                        <select class="form-control selectpicker" name="assign_to_user[]" multiple
                                                id="assign_to_user" title="Select Assign to">
                                            <option value="">Select Assign to</option>
                                            @foreach($users as  $user)

                                                <option value="{{$user->id}}">{{$user->name}} </option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="form-group col-md-2 cls_filter_inputbox p-2 mr-2" style="width: 160px;">
                                        @php
                                            $created_by = request('created_by');
                                        @endphp
                                        <select class="form-control selectpicker" name="created_by[]" multiple
                                                id="created_by" title="Select Created by">
                                            <option value="">Select Created by</option>
                                            @foreach($users as  $user)

                                                <option value="{{$user->id}}">{{$user->name}} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group" style="width: 200px;">
                                        <input name="date" type="date" class="form-control" placeholder="Search Date"
                                               id="bug-date" data-allow-clear="true" />
                                    </div>
                                    <div class="form-group">
                                        <label for="button">&nbsp;</label>
                                        <button type="submit" style="display: inline-block;width: 10%"
                                                class="btn btn-sm btn-image btn-search-action">
                                            <img src="/images/search.png" style="cursor: default;">
                                        </button>
                                        <a href="/bug-tracking" class="btn btn-image" id=""><img
                                                    src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                                        <button type="submit" class="btn btn-secondary btn-xs btn-sorting-action"
                                                value="sort-comm" style="color:white;">Sort By Comm
                                        </button>&nbsp;&nbsp;
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col col-md-4">
                    <div class="row">

                        <button style="display: inline-block;width: 10%;margin-left:10px;"
                                class="btn btn-sm btn-image btn-add-action"
                                data-toggle="modal" data-target="#bugtrackingCreateModal">
                            <img src="/images/add.png" style="cursor: default;">
                        </button>
                        <div class="pull-left">
                            <button class="btn btn-secondary btn-xs btn-add-environment" style="color:white;"
                                    data-toggle="modal" data-target="#newEnvironment"> Environment
                            </button>&nbsp;&nbsp;
                            <button class="btn btn-secondary btn-xs btn-add-type" style="color:white;"
                                    data-toggle="modal" data-target="#newType"> Type
                            </button>&nbsp;&nbsp;
                            <button class="btn btn-secondary btn-xs btn-add-status" style="color:white;"
                                    data-toggle="modal" data-target="#newStatus"> Status
                            </button>&nbsp;&nbsp;
                            <button class="btn btn-secondary btn-xs btn-add-severity" style="color:white;"
                                    data-toggle="modal" data-target="#newSeverity"> Severity
                            </button>&nbsp;&nbsp;
                            <button class="btn btn-secondary btn-xs btn-add-status-color" style="color:white;"
                                    data-toggle="modal" data-target="#newStatusColor"> Status Color
                            </button>&nbsp;&nbsp;
                            <button type="button" class="btn btn-secondary btn-x" data-toggle="modal"
                                    data-target="#bugdatatablecolumnvisibilityList">Column Visiblity
                            </button>
                        </div>&nbsp;&nbsp;
                    </div>
                </div>
                <div class="col col-md-6">
                    <div class="row">
                        <div class="pull-left" id="change_dropdown_div">

                            @php
                                $bug_status = request('bugstatus');
                            @endphp
                            <select class="form-control selectpicker change_bug_status_top" name="change_bug_status[]"
                                    id="change_bug_status_top" title="Select Bug Status">

                                @foreach ($bugStatuses as $bugstatus)
                                    {
                                    <option value="{{ $bugstatus->id }}">{{ $bugstatus->name }}</option>
                                    }
                                @endforeach
                            </select>
                            &nbsp;&nbsp;
                            <button type="button" class="btn btn-secondary btn-xs btn-change-status-bug">
                                <span class="glyphicon glyphicon-pencil"></span> Status&nbsp;
                            </button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            @php
                                $assign_to_user = request('assign_to_user');
                            @endphp
                            <select class="form-control selectpicker change_assign_to_top select2"
                                    name="change_assign_to_user[]" id="change_assign_to_top" title="Select Assign To">

                                @foreach($users as  $user)
                                    <option value="{{$user->id}}">{{$user->name}} </option>
                                @endforeach
                            </select>
                            &nbsp;&nbsp;
                            <button type="button" class="btn btn-secondary btn-xs btn-change-assignee-bug">
                                <span class="glyphicon glyphicon-pencil"></span> Assignee&nbsp;
                            </button>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            @php
                                $bug_severity = request('bug_severity');
                            @endphp
                            <select class="form-control selectpicker change_bug_severity_top"
                                    name="change_bug_severity[]" id="change_bug_severity_top"
                                    title="Select Bug Severity">

                                @foreach ($bugSeveritys as $bugseverity)
                                    {
                                    <option value="{{ $bugseverity->id }}"
                                            @if ($bug_severity == $bugseverity->id) selected @endif>{{ $bugseverity->name }}</option>
                                    }
                                @endforeach
                            </select>
                            &nbsp;&nbsp;
                            <button type="button" class="btn btn-secondary btn-xs btn-change-severity-bug">
                                <span class="glyphicon glyphicon-pencil"></span> Severity&nbsp;
                            </button> &nbsp;

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

    <div class="common-modal modal" role="dialog">
        <div class="modal-dialog" role="document">

        </div>
    </div>


    @include("bug-tracking.templates.list-template")
    @include("bug-tracking.create")
    @include("bug-tracking.edit")
    @include("bug-tracking.templates.create-bug-tracking-template")
    @include("bug-tracking.templates.bug-environment")
    @include("bug-tracking.templates.bug-severity")
    @include("bug-tracking.templates.bug-status")
    @include("bug-tracking.templates.bug-type")
    @include("bug-tracking.templates.bug-status-color")
    @include("bug-tracking.templates.column-visibility-modal")

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
    <style>
        #newHistoryModal .table th {
            border-color: #ddd;
        }

    </style>
    <div id="newHistoryModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl">
            <!-- Modal content-->
            <div class="modal-content mx-auto" style="width: 963px;">
                <div class="modal-header">
                    <h3>Bug Tracker History</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <tr style="background-color:#3333;">
                            <th>Created At</th>
                            <th>Type of Bug</th>
                            <th>Summary</th>
                            <th>Expected Result</th>
                            <th>Environment</th>
                            <th>Assigned To</th>
                            <th>Status</th>
                            <th>Severity</th>
                            <th>Module/Feature</th>
                            <th>Updated By</th>
                        </tr>
                        <tbody class="tbh">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="newuserHistoryModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h3>User History</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <table class="table">
                    <tr>

                        <th>Date</th>
                        <th>New User</th>
                        <th>Old User</th>
                        <th>Updated By</th>
                    </tr>
                    <tbody class="tbhuser">

                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div id="newstatusHistoryModal" class="modal fade" role="dialog">

        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Status History</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <table class="table">
                    <tr>

                        <th>Date</th>
                        <th>New Status</th>
                        <th>Old Status</th>
                        <th>Updated By</th>
                    </tr>
                    <tbody class="tbhstatus">

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="newSeverityHistoryModal" class="modal fade" role="dialog">

        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Severity History</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table" border="1">
                        <tr>
                            <td style="text-align: center;"><b>Created Date</b></td>
                            <td style="text-align: center;"><b>Old Severity</b></td>
                            <td style="text-align: center;"><b>New Severity</b></td>
                            <td style="text-align: center;"><b>Updated By</b></td>
                        </tr>
                        <tbody class="tbhseverity">

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <div id="newCommunictionModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Communication</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                        <th></th>
                        <th></th>
                        </thead>
                        <tbody class="tbhc">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="create-quick-task" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('task.create.multiple.task.shortcut.bugtrack') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Create Task</h4>
                    </div>
                    <div class="bug-task-note"> Note: Task already created for this Bug ID</div>
                    <div class="modal-body">
                        <input class="form-control" value="52" type="hidden" name="category_id" />
                        <input class="form-control" value="" type="hidden" name="category_title" id="category_title" />
                        <input class="form-control" type="hidden" name="site_id" id="site_id" />
                        <input class="form-control" type="hidden" name="website_id" id="website_id" />
                        <div class="form-group">
                            <label for="">Subject</label>
                            <input class="form-control" type="text" id="hidden-task-subject" name="task_subject" />
                        </div>
                        <div class="form-group">
                            <select class="form-control" style="width:100%;" name="task_type" tabindex="-1"
                                    aria-hidden="true">
                                <option value="0">Other Task</option>
                                <option value="4">Developer Task</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="repository_id">Repository:</label>
                            <br>
                            <select style="width:100%" class="form-control 	" id="repository_id"
                                    name="repository_id">
                                <option value="">-- select repository --</option>
                                @foreach ($githubRepositories as $repository)
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
                        <div class="form-group">
                            <label for="">Websites</label>
                            <div class="form-group website-list row">

                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Last Users</label>
                            <div class="form-group task-users-list row" style="margin-left: 0px;">

                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Bug Ids</label>
                            <input class="form-control text-task-bugids" type="text" name="task_bug_ids"
                                   readonly="true" />
                        </div>
                        <div class="form-group">
                            <label for="">Bug List</label>
                            <div class="form-group" id="bugs_list_html">

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-default create-task">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="uploadeBugsScreencastModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Upload Screencast/File to Google Drive</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{ route('bug-tracking.upload-file') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="bug_id" id="bug_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <strong>Upload File</strong>
                            <input type="file" name="file[]" id="fileInput" class="form-control input-sm"
                                   placeholder="Upload File" style="height: fit-content;" multiple required>
                            @if ($errors->has('file'))
                                <div class="alert alert-danger">{{$errors->first('file')}}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <strong>File Creation Date:</strong>
                            <input type="date" name="file_creation_date" value="{{ old('file_creation_date') }}"
                                   class="form-control input-sm" placeholder="Drive Date" required>
                        </div>

                        <div class="form-group">
                            <label>Remarks:</label>
                            <textarea id="remarks" name="remarks" rows="4" cols="64" value="{{ old('remarks') }}"
                                      placeholder="Remarks" required class="form-control"></textarea>

                            @if ($errors->has('remarks'))
                                <div class="alert alert-danger">{{$errors->first('remarks')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Upload</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <div id="displayBugsUpload" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Google Drive Bug files</h4>
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
                            <tbody id="googleDriveBugData">

                            </tbody>
                        </table>
                    </div>
                </div>


            </div>

        </div>
    </div>

    <div id="bugtrackingShowFullTextModel" class="modal fade" role="dialog">
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
                        <div class="modal-body bugtrackingmanShowFullTextBody">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        @if($errors->any())
                @php
                    $error = $errors->all()
                @endphp
          toastr["error"]("{{$error[0] ?? 'Something went wrong.'}}");
        @endif

                @if ($message = Session::get('success'))
          toastr["success"]("{{$message}}");
        @endif
                @if ($message = Session::get('error'))
          toastr["error"]("{{$message}}");
        @endif

        var page_bug = 0;
        var total_limit_bug = 19;
        var action_bug = "inactive";

    </script>
    <script type="text/javascript" src="{{ asset('/js/jsrender.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('/js/jquery-ui.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/bug-tracker.js') }} "></script>


    <script type="text/javascript">

      $(document).ready(function() {
        setTimeout(() => {
          page.init({
              bodyView: $("#common-page-layout"),
              baseUrl: "<?php echo url('/'); ?>"
            });
        }, 2000);
      })
      $(document).ready(function() {
        $(".js-example-basic-multiple").select2();


      });
    </script>
    <script type="text/javascript">
      $(document).ready(function() {
        $("body").tooltip({ selector: "[data-toggle=tooltip]" });
      });

      $(".change_assign_to_top").select2({
        width: "150px",
        placeholder: "Select Assign To"
      });
      /*
      $('#assign_to_user').select2({
              width: "150px",
              height: 30px,
              placeholder: 'Select Assign To'
      });

      $('#created_by').select2({
              width: "150px",
              height: "30px",
              placeholder: 'Select Created By'
      });
      */

      $(document).on("click", ".expand-row-msg-chat", function() {
        var id = $(this).data("id");
        var full = ".expand-row-msg-chat .td-full-container-" + id;
        var mini = ".expand-row-msg-chat .td-mini-container-" + id;
        $(full).toggleClass("hidden");
        $(mini).toggleClass("hidden");
      });

      $(document).ready(function() {
        $(document).on("click", ".expand-row,.expand-row-msg", function() {
          var selection = window.getSelection();
          if (selection.toString().length === 0) {
            $(this).find(".td-mini-container").toggleClass("hidden");
            $(this).find(".td-full-container").toggleClass("hidden");
          }
        });
      });


      // Bug tracking ajax starts
      function GetParameterValues(param) {
        var url = window.location.href.slice(window.location.href.indexOf("?") + 1).split("&");
        for (var i = 0; i < url.length; i++) {
          var urlparam = url[i].split("=");
          if (urlparam[0] == param) {
            return urlparam[1];
          }
        }
      }


      $(window).scroll(function() {
        let urlString_bug = window.location.href;
        let paramString_bug = urlString_bug.split("?")[1];
        let queryString_bug = new URLSearchParams(paramString_bug);
        var arr = 0;
        for (let pair of queryString_bug.entries()) {
          console.log("Key is:" + pair[0]);
          console.log("Value is:" + pair[1]);
          arr = 1;
        }

        //console.log("arr="+arr);
        //console.log("window="+$(window).height());
        //console.log("table="+$("#bug_tracking_maintable").height());
        //console.log("action="+action_bug);

        if (arr == 0) {
          if ($(window).scrollTop() + $(window).height() > $("#page-view-result").height() && action_bug == "inactive") {

            action_bug = "active";
            page_bug++;
            setTimeout(function() {
              console.log("coming");
              load_more(page_bug);

            }, 1000);
            console.log("act=" + action_bug);
          }
        }

      });

      function load_more(page_bug) {


        $.ajax({
          url: "/bug-tracking/record-tracking-ajax?page=" + page_bug + "&" + $(".message-search-handler").serialize(),
          type: "get",
          datatype: "html",
          beforeSend: function() {
            $("#loading-image-preview").css("display", "block");

          }
        })
          .done(function(data) {
            $("#loading-image-preview").css("display", "none");


            if (data.length == 0) {
              console.log("len=" + data.length);
              //notify user if nothing to load
              action_bug = "inactive";
              //$('.ajax-loading').html("No more records!");
              page_bug = 0;
              console.log("if=" + action_bug);
              return;
            }
            $(".loading-image-preview").hide(); //hide loading animation once data is received
            $("#loading-image-preview").css("display", "none");
            $("#bug_tracking_maintable > tbody:last").append(data);
            action_bug = "inactive";
            console.log("in success=" + action_bug);


          })
          .fail(function(jqXHR, ajaxOptions, thrownError) {
            alert("No response from server");
          });
      }

      // Bug tracking ajax ends


      $(document).on("click", ".chkBugChangeCommon", function() {

        if ($(this).is(":checked")) {
          $("input[name='chkBugNameChange[]']").attr("checked", true);
        } else {
          $("input[name='chkBugNameChange[]']").attr("checked", false);
        }

      });


      $(document).on("click", ".expand-row-msg", function() {
        // $('#bugtrackingShowFullTextModel').modal('toggle');
        // $(".bugtrackingmanShowFullTextBody").html("");
        // var id = $(this).data('id');
        // var name = $(this).data('name');
        // var full = '.expand-row-msg .show-full-' + name + '-' + id;
        // var fullText = $(full).html();
        // console.log(id,name,fullText,full)
        // $(".bugtrackingmanShowFullTextBody").html(fullText.replaceAll("\n", "<br>"));
      });
      $(document).on("click", ".btn-copy-url", function() {
        var url = $(this).data("id");
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(url).select();
        document.execCommand("copy");
        $temp.remove();
        alert("Copied!");
      });

      $(document).on("click", ".create-quick-task", function() {

        var $this = $(this);
        site = $(this).data("id");
        title = $(this).data("title");
        cat_title = $(this).data("category_title");
        development = $(this).data("development");
        bug_type_id = $(this).data("bug_type_id");
        module_id = $(this).data("module_id");
        website_id = $(this).data("website_id");
        $(".website-list").html("");
        $("#bugs_list_html").html("");
        $("#hidden-task-subject").val("");
        $(".text-task-development").val("");
        $("#site_id").val("");
        $("#website_id").val("");
        $(".text-task-bugids").val("");
        $(".text-task-bugids").val("");
        $(".text-task-development").val("");
        bug_id_val = $(this).data("id");

        $(".bug-task-note").hide();
        $.ajax({
          url: "/bug-tracking/checkbug",
          type: "POST",
          headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
          },
          data: {
            bug_id: bug_id_val
          },
          beforeSend: function() {
            $("#loading-image").show();
          }
        }).done(function(response) {

          $("#loading-image").hide();
          // inner Ajax starts

          if (response.data > 0) {
            $(".bug-task-note").show();
            if (!confirm("Task already created for this bug id, Would you like to create again")) {

              return false;
            }

          }


          if (!title || title == "") {
            toastr["error"]("Please add title first");
            return;
          }

          //debugger;
          let val = $("#change_website1").select2("val");
          $.ajax({
            url: "/bug-tracking/websitelist",
            type: "POST",
            headers: {
              "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            data: {
              id: val,
              cat_title: cat_title,
              bug_type_id: bug_type_id,
              module_id: module_id,
              website_id: website_id
            },
            beforeSend: function() {
              $("#loading-image").show();
            }
          }).done(function(response) {
            $("#loading-image").hide();
            //$this.siblings('input').val("");				
            $(".website-list").html(response.data.websiteCheckbox);
            //$('.text-task-development').val(response.data.bug_ids);		
            $("#bugs_list_html").html(response.data.bug_html);
            //toastr["success"]("Remarks fetched successfully");
            $(".task-users-list").html(response.data.bug_users_worked);
          }).fail(function(jqXHR, ajaxOptions, thrownError) {
            toastr["error"]("Oops,something went wrong");
            $("#loading-image").hide();
          });


          $("#create-quick-task").modal("show");


          var selValue = $(".save-item-select").val();
          if (selValue != "") {
            $("#create-quick-task").find(".assign-to option[value=" + selValue + "]").attr("selected",
              "selected");
            $(".assign-to.select2").select2({
              width: "100%"
            });
          }

          $("#hidden-task-subject").val(title);
          $(".text-task-development").val(development);
          $("#site_id").val(site);
          $("#website_id").val(website_id);


          // inner Ajax ends


        }).fail(function(jqXHR, ajaxOptions, thrownError) {
          toastr["error"]("Oops,something went wrong");
          $("#loading-image").hide();
        });


        // $.ajax({
        // 		url: '/site-development/get-user-involved/'+site,
        // 		dataType: "json",
        // 		type: 'GET',
        // 	}).done(function (response) {
        // 		var option = '<option value="" > Select user </option>';
        // 		$.each(response.data,function(k,v){
        // 			option = option + '<option value="'+v.id+'" > '+v.name+' </option>';
        // 		});

        // 	}).fail(function (jqXHR, ajaxOptions, thrownError) {
        // 	    toastr["error"](jqXHR.responseJSON.message);
        // });
      });

      $(document).on("click", ".create-task", function(e) {
        e.preventDefault();
        var form = $(this).closest("form");

        var values = new Array();
        var web = new Array();
        $.each($("input[name='chkBugId[]']:checked"), function() {
          values.push($(this).val());
          // or you can do something to the actual checked checkboxes by working directly with  'this'
          // something like $(this).hide() (only something useful, probably) :P
        });
        $.each($(".website-list input[type='checkbox']:checked"), function() {
          web.push($(this).val());
          // or you can do something to the actual checked checkboxes by working directly with  'this'
          // something like $(this).hide() (only something useful, probably) :P
        });

        if (values.length == 0 && $("input[name='chkBugId[]']").length > 0) {
          toastr["error"]("Please select atleast 1 bugs list ");
          return;
        }
        if (web.length == 0) {
          toastr["error"]("Please select website ");
          return;
        }

        $.ajax({
          url: form.attr("action"),
          type: "POST",
          data: form.serialize(),
          beforeSend: function() {
            $(this).text("Loading...");
          },
          success: function(response) {
            if (response.code == 200) {
              form[0].reset();
              toastr["success"](response.message);
              $("#create-quick-task").modal("hide");
            } else {
              toastr["error"](response.message);
            }
          }
        }).fail(function(response) {
          toastr["error"](response.responseJSON.message);
        });
      });

      $(document).on("click", ".cls-checkbox-bugsids", function(e) {

        var values = new Array();
        var bugvalues = new Array();
        $.each($("input[name='chkBugId[]']:checked"), function() {
          values.push($(this).val());
          var det = $(this).val() + " - " + $(this).attr("data-summary");
          bugvalues.push(det);
          // or you can do something to the actual checked checkboxes by working directly with  'this'
          // something like $(this).hide() (only something useful, probably) :P
        });
        var bugid = $(this).val();
        var prevlist = $(".text-task-development").val();


        $(".text-task-development").val(bugvalues);
        $(".text-task-bugids").val(values);

      });
      //$(document).on('click', '.btn-add-action', function() {	
      //  var bugid =  $('input[type="checkbox"]:checked').val();
      //	if(bugid>0) {
      //		$('#parent_id_bug').val(bugid);  
      //	}

      //});

      //$(document).on('click', 'input[type="checkbox"]', function() {	
      //   $('input[type="checkbox"]').not(this).prop("checked", false);
      //});


      $(document).on("click", ".btn-add-action", function() {
        var bugid = $(".chkBugNameCls:checkbox:checked").val();
        if (bugid > 0) {
          var user = $(".chkBugNameCls:checkbox:checked").attr("data-user");
          $("#parent_id_bug").val(bugid);
          $("#assign_to_bug").val(user);
        }

      });

      $(document).on("click", ".chkBugNameCls", function() {
        $(".chkBugNameCls").not(this).prop("checked", false);
      });

      $(document).on("click", ".count-dev-customer-tasks", function() {

        var $this = $(this);
        // var user_id = $(this).closest("tr").find(".ucfuid").val();
        var site_id = $(this).data("id");
        var category_id = $(this).data("category");
        $("#site-development-category-id").val(category_id);
        $.ajax({
          type: "get",
          url: "/bug-tracking/countdevtask/" + site_id,
          dataType: "json",
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
              if (typeof status == "undefined" || typeof status == "" || typeof status ==
                "0") {
                status = "In progress";
              }
              ;
              table = table + "<tr><td>" + data.taskStatistics[i].task_type + "</td><td>#" +
                data.taskStatistics[i].id +
                "</td><td class=\"expand-row-msg\" data-name=\"asgTo\" data-id=\"" + data
                  .taskStatistics[i].id + "\"><span class=\"show-short-asgTo-" + data
                  .taskStatistics[i].id + "\">" + data.taskStatistics[i].assigned_to_name
                  .replace(/(.{6})..+/, "$1..") +
                "</span><span style=\"word-break:break-all;\" class=\"show-full-asgTo-" + data
                  .taskStatistics[i].id + " hidden\">" + data.taskStatistics[i]
                  .assigned_to_name +
                "</span></td><td class=\"expand-row-msg\" data-name=\"res\" data-id=\"" + data
                  .taskStatistics[i].id + "\"><span class=\"show-short-res-" + data
                  .taskStatistics[i].id + "\">" + res.replace(/(.{7})..+/, "$1..") +
                "</span><span style=\"word-break:break-all;\" class=\"show-full-res-" + data
                  .taskStatistics[i].id + " hidden\">" + res + "</span></td><td>" + status +
                "</td><td><div class=\"col-md-10 pl-0 pr-1\"><textarea rows=\"1\" style=\"width: 100%; float: left;\" class=\"form-control quick-message-field input-sm\" name=\"message\" placeholder=\"Message\"></textarea></div><div class=\"p-0\"><button class=\"btn btn-sm btn-xs send-message\" title=\"Send message\" data-taskid=\"" +
                data.taskStatistics[i].id +
                "\"><i class=\"fa fa-paper-plane\"></i></button></div></td><td><button type=\"button\" class=\"btn btn-xs load-communication-modal load-body-class\" data-object=\"" +
                data.taskStatistics[i].message_type + "\" data-id=\"" + data.taskStatistics[i]
                  .id +
                "\" title=\"Load messages\" data-dismiss=\"modal\"><i class=\"fa fa-comments\"></i></button>";
              table = table + "<a href=\"javascript:void(0);\" data-task-type=\"" + data
                  .taskStatistics[i].task_type + "\" data-id=\"" + data.taskStatistics[i].id +
                "\" class=\"delete-dev-task-btn btn btn-xs\"><i class=\"fa fa-trash\"></i></a>";
              table = table +
                "<button type=\"button\" class=\"btn btn-xs  preview-img pd-5\" data-object=\"" +
                data.taskStatistics[i].message_type + "\" data-id=\"" + data.taskStatistics[i]
                  .id + "\" data-dismiss=\"modal\"><i class=\"fa fa-list\"></i></button></td>";
              table = table + "</tr>";
            }
            table = table + "</table></div>";
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
            type: "get",
            url: "/site-development/deletedevtask",
            headers: {
              "X-CSRF-TOKEN": jQuery("meta[name=\"csrf-token\"]").attr("content")
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
            alert("Could not update!!");
          });
        }

      });

      $(document).on("click", ".send-message", function() {
        var thiss = $(this);
        var data = new FormData();
        var task_id = $(this).data("taskid");
        var bid = $(this).data("id");

        var message = $(this).closest("tr").find(".quick-message-field").val();
        var mesArr = $(this).closest("tr").find(".quick-message-field");
        $.each(mesArr, function(index, value) {
          if ($(value).val()) {
            message = $(value).val();
          }
        });

        data.append("task_id", task_id);
        data.append("message", message);
        data.append("status", 1);

        if (message.length > 0) {
          if (!$(thiss).is(":disabled")) {
            $.ajax({
              url: "/whatsapp/sendMessage/task",
              type: "POST",
              "dataType": "json", // what to expect back from the PHP script, if anything
              "cache": false,
              "contentType": false,
              "processData": false,
              "data": data,
              beforeSend: function() {
                $(thiss).attr("disabled", true);
              }
            }).done(function(response) {
              thiss.closest("tr").find(".quick-message-field").val("");

              toastr["success"]("Message successfully send!", "Message");
              // $.post( "/whatsapp/approve/customer", { messageId: response.message.id })
              //   .done(function( data ) {
              //
              //   }).fail(function(response) {
              //     console.log(response);
              //     alert(response.responseJSON.message);
              //   });						
              $("#getMsg" + bid).val("");

              $(thiss).attr("disabled", false);
            }).fail(function(errObj) {
              $(thiss).attr("disabled", false);
              $("#getMsg" + bid).val("");
              //alert("Could not send message");
              console.log(errObj);

            });
          }
        } else {
          alert("Please enter a message first");
        }
      });

      $(document).on("click", ".preview-img", function(e) {
        e.preventDefault();
        id = $(this).data("id");
        if (!id) {
          alert("No data found");
          return;
        }
        $.ajax({
          url: "/site-development/preview-img-task/" + id,
          type: "GET",
          success: function(response) {
            $("#preview-task-image").modal("show");
            $(".task-image-list-view").html(response);
            initialize_select2();
          },
          error: function() {
          }
        });
      });


      $(window).on("load", function() {
        $("th").resizable();
      });

      var uriv = window.location.href.toString();
      if (uriv.indexOf("?") > 0) {
        var clean_uri = uriv.substring(0, uriv.indexOf("?"));
        $("#bug-id-search").val("");
        window.history.replaceState({}, document.title, clean_uri);
      }


      $(document).ready(function() {
        $(document).on("click", ".upload-bugs-files-button", function(e) {
          e.preventDefault();
          let bug_id = $(this).data("bug_id");
          $("#uploadeBugsScreencastModal #bug_id").val(bug_id || 0);
          $("#uploadeBugsScreencastModal").modal("show");
        });

        $(document).on("click", ".view-bugs-files-button", function(e) {
          e.preventDefault();
          let bug_id = $(this).data("bug_id");
          $.ajax({
            type: "get",
            url: "{{route('bug-tracking.files.record')}}",
            data: {
              bug_id
            },
            success: function(response) {
              $("#googleDriveBugData").html(response.data);
              $("#displayBugsUpload").modal("show");
            },
            error: function(response) {

            }
          });
        });
      });

      function Showactionbtn(id) {
        $(".action-btn-tr-" + id).toggleClass("d-none");
      }
    </script>
@endsection