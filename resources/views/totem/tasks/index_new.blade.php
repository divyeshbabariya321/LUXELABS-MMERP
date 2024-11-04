@extends('layouts.app')

@section('title', 'Totem Cron Module')

@section('styles')
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
<style>
    #ckbCheckAll{
        height: 12px !important;
        margin-left: 6px !important;
    }
    .switch {
        position: relative;
        display: inline-block;
        width: 55px;
        height: 25px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 17px;
        width: 20px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
.ajax-loader{
    position: fixed;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.2);
    z-index: 1060;
}
.inner_loader {
	top: 30%;
    position: absolute;
    left: 40%;
    width: 100%;
    height: 100%;
}
.pd-5 {
  padding:5px !important;
}
.pd-3 {
  padding:3px !important;
}
.status-select-cls .multiselect {
  width:100%;
}
.btn-ht {
  height:30px;
}
.status-select-cls .btn-group {
  width:100%;
  padding: 0;
}
.table.table-bordered.order-table a{
color:black!important;
}
.fa-info-circle{
    padding-left:10px;
    cursor: pointer;
}
table tr td {
  word-wrap: break-word;
}
.fa-list-ul{
    cursor: pointer;
}

.fa-upload{
    cursor: pointer;
}
.fa-refresh{
    cursor: pointer;
    color:#000;
}
.red{
    color: red
}
#addEditTaskModal .modal-dialog{
    max-width: 1050px;
    width: 100%;
}
.uk-margin-remove th, .uk-margin-remove td{
    padding: 4px 10px;
}
.btn-default:focus{
    outline: none;
    border:1px solid #ddd;
    box-shadow: none !important;
    background: #fff;
    color: #757575;
}
.select2-container--default .select2-results__group{display: inline-block !important;}
</style>
@endsection
@section('large_content')
    <script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.jscroll.min.js') }} "></script>
	<div class="ajax-loader" style="display: none;">
		<div class="inner_loader">
		<img src="{{ asset('/images/loading2.gif') }}">
		</div>
	</div>

    <div class="row">
        <div class="col-12" style="padding:0px;">
            <h2 class="page-heading flex" style="padding: 8px 5px 8px 10px;border-bottom: 1px solid #ddd;line-height: 32px;">Cron Tasks {{'('.$total_tasks.')'}}</h2>
        </div>
        <div class="col-7 pl-2" style="padding-left:0px;">
                <div class="form-group col-md-2 pd-3">
                    <button type="button" class="btn btn-secondary website-template-btn add-torterm" data-toggle="modal" data-target="#addEditTaskModal">
                        <span class="glyphicon glyphicon-th-plus"></span> Add Task
                    </button>
                </div>
                <form class="form-inline" id="filter-form" action="" method="GET">

                    <div class="form-group col-md-2 pd-3">
                        <input list="tasks-lists" style="width:100%;" id="totem__search__form" name="q" type="text" class="form-control" value="{{ isset($_REQUEST['q']) ? $_REQUEST['q'] : '' }}" placeholder="Search Description...">
                        <datalist id="tasks-lists">
                            @foreach($tasks as $description)
                            <option value="{{$description}}">
                            @endforeach
                        </datalist>
                    </div>
                    <div class="form-group col-md-2 pd-3">
                        <select name="developer_module" id="developer_module" class="form-control">
                            <option value="">Select Module</option>
                            @foreach($developer_module as  $module)
                                <option {{ isset($_REQUEST['developer_module']) && $_REQUEST['developer_module']  == $module->id ? 'selected' : '' }} value="{{$module->id}}">{{$module->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2 pd-3">
                        <select name="frequency" id="filter_frequency" class="form-control">
                            <option value="">Select Frequency</option>
                            @foreach ($frequencies as $key => $frequency)
                                <option {{ request()->frequency==$frequency['interval'] ? 'selected' : '' }} value="{{$frequency['interval']}}">{{$frequency['label']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2 pd-3">
                        <select id="is_active" class="form-control">
                            <option value="">Select Status</option>
                            <option value="1" {{ isset($_REQUEST['is_active']) && $_REQUEST['is_active']  == "1" ? 'selected' : '' }} >Active</option>
                            <option value="0" {{ isset($_REQUEST['is_active']) && $_REQUEST['is_active']  == "0" ? 'selected' : '' }} >In Active</option>

                        </select>
                    </div>
                    <div class="form-group col-md-2 pd-3">
                        <button type="submit" class="btn btn-image ml-0"><img src="{{asset('images/filter.png')}}" /></button>
                        <a id="reset-filter" class="fa fa-refresh" aria-hidden="true"></a>
                    </div>
                </form>


        </div>
        <div class="col-5 pl-2" style="padding-left:0px;">
            @if(auth()->user()->isAdmin() || auth()->user()->isCronManager())
                    <div class="form-group" style="display: flex;">
                        <h4 class="pt-2">Assign Cron to User</h4>
                        <div>
                            <form class="post-assign-cron" action="" method="POST">
                                @csrf

                                <input type="hidden" id="taskId" name="task-id"/>
                                <select class="js-select2" multiple="multiple" name="users_id[]" >
                                    @foreach ($users as $key => $userData)
                                        <option data-badge="" value="{{ $userData->id }}">
                                            {{ $userData->name }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        <div style="margin-top: 10px"><button class="btn btn-secondary grant_button" bulk-assign="1">Grant Access</button></div>
                    </div>
                @endif
        </div>
    </div>



    <div class="row">
        <div class="col-md-12 pl-1" style="display: flex">
            <button id="enableAllData"  class="btn btn-secondary m-2 enable-disable" cron-status="1">Enable</button>
            <button id="disableAllData" class="btn btn-secondary m-2 enable-disable" cron-status="0" >Disable</button>
        </div>
        <div class="col-md-12">
            <div class="table-responsive mt-2" >
                <table class="table table-bordered" id="tasks-table" style="min-height: 100px;">
                    <thead>
                    <tr>
                        @if(auth()->user()->isAdmin() || auth()->user()->isCronManager())
                            <th width="2%">
                                <input type="checkbox" id="ckbCheckAll"/>
                            </th>
                        @endif
                            <th width="2%" class="category">#</th>
                            <th width="10%" class="category" >Description</th>
                            <th width="5%" class="category" >Module</th>
                            <th width="5%" class="category">Average Runtime</th>
                            <th width="5%" class="category">Last Run</th>
                            <th width="5%" class="category">Next Run</th>
                            <th width="5%" class="category">Frequencies</th>
                            <th width="7%">Action</th>
                        @if(auth()->user()->isAdmin() || auth()->user()->isCronManager())
                            <th width="5%" class="">Enable & Disable</th>
                            @endif

                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="modal fade" id="view_task_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Task details</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="table-responsive mt-2">
                    <table class="table table-bordered order-table" style="border: 1px solid #ddd !important; color:black;">
                        <thead>
                        <tr>
                            <th width="5%">Description</th>
                            <th width="5%">Command</th>
                            <th width="5%">Parameters</th>
                            <th width="5%">Cron Expression</th>
                            <th width="5%">Timezone</th>
                            <th width="5%">Created At</th>
                            <th width="5%">Updated At</th>
                            <th width="5%">Email Notification</th>
                            <th width="5%">SMS Notification</th>
                            <th width="5%">Slack Notification</th>
                            <th width="5%">Average Run Time</th>
                            <th width="5%">Next Run Schedule</th>
                        </tr>
                        </thead>

                        <tbody>
                        </tbody>
                    </table>
                </div>
            </form>
            <br>
            <h4 class="modal-title notes d-none">Notes</h4>

                <ul>
                    <li class="dont_overlap d-none">
                        <span class="uk-float-left">Doesn't Overlap with another instance of this task</span><br>
                    </li>
                    <li class="run_in_maintenance d-none">
                        <span class="uk-float-left">Runs in maintenance mode</span><br>
                    </li>
                    <li class="run_on_one_server d-none">
                        <span class="uk-float-left">Runs on a single server<br>
                    </li>
                </ul>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
    </div>

    <div id="addFrequencyModal" class="modal fade" role="dialog" style="z-index: 999999999">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Frequency</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <select id="frequency" class="form-control select2" placeholder="Select a type of frequency">
                            @foreach (collect($frequencies) as $key => $frequency)
                                <option value="{{ json_encode($frequency) }}">{{$frequency['label']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-default add_freq">Add</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="view_execution_history" tabindex="-1" role="dialog" aria-hidden="true" style="overflow-y:auto;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">EXECUTION RESULTS</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="table-responsive mt-2">
                <table class="table table-bordered order-table" style="border: 1px solid #ddd !important; color:black;">
                    <thead>
                    <tr>
                        <th width="5%">Executes At</th>
                        <th width="5%">Duration</th>
                        <th width="5%">Status</th>
                    </tr>
                    </thead>

                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
    </div>

    <div class="modal fade" id="view_assign_user" tabindex="-1" role="dialog" aria-hidden="true" style="overflow-y:auto;">
        <form class="post-assign-cron">
            @csrf
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Assign Cron to User</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" id="taskId" name="task-id"/>
                <select class="js-select2" multiple="multiple" name="users_id[]" >
                        @foreach ($users as $key => $userData)
                            <option data-badge="" value="{{ $userData->id }}">
                                {{ $userData->name }}</option>
                        @endforeach
                    </select>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-default user_submit_btn">Submit</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
            </div>
        </form>
    </div>

    <div class="modal fade" id="view_command_query" tabindex="-1" role="dialog" aria-hidden="true" style="overflow-y:auto;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">COMMAND QUERIES</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive mt-2">
                        <table class="table table-bordered order-table" style="border: 1px solid #ddd !important; color:black;">
                            <thead>
                            <tr>
                                <th width="5%">Command Name</th>
                                <th width="5%">Queries</th>
                                <th width="5%">Description</th>
                            </tr>
                            </thead>

                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="view_cron_assign_history" tabindex="-1" role="dialog" aria-hidden="true" style="overflow-y:auto;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">COMMAND ASSIGN HISTORY</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive mt-2">
                        <table class="table table-bordered order-table" style="border: 1px solid #ddd !important; color:black;">
                            <thead>
                            <tr>
                                <th width="5%">Assign By</th>
                                <th width="5%">Assign To</th>
                            </tr>
                            </thead>

                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="view_execution_error" tabindex="-1" role="dialog" aria-hidden="true" style="overflow-y:auto;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">EXECUTION ERROR</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive mt-2">
                    <table class="table table-bordered order-table" style="border: 1px solid #ddd !important; color:black;">
                        <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="5%">Signature</th>
                            <th width="5%">Error</th>
                            <th width="5%">Error Count</th>
                            <th width="5%">Status</th>
                            <th width="5%">Date</th>
                        </tr>
                        </thead>

                        <tbody >
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
    </div>

    <div id="addEditTaskModal" class="modal fade" role="dialog" data-id = '' style="overflow-y:auto;">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create Task</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form class="taskForm" action="/" method="POST">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label>Description</label><i class="fa fa-info-circle" title="Provide a descriptive name for your task"></i>
                            <input class="form-control" placeholder="e.g. Daily Backups" name="description" id="description" value="" type="text">
                            <p class="d-none"></p>
                        </div>
                        <div class="form-group">
                            <label>Command</label><i class="fa fa-info-circle" title="Select an artisan command to schedule"></i>
                            <select id="command" name="command" class="form-control select2" width="100%" placeholder="Click here to select one of the available commands">
                                <option value="">Select a command</option>
                                @forelse ($commands as $k => $command)
                                    <option value="{{$command->getName()}}">
                                        {{$command->getDescription()}}
                                    </option>
                                <p class="d-none"></p>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Parameters (Optional)</label><i class="fa fa-info-circle" title="Command parameters required to run the selected command"></i>
                            <input class="form-control" placeholder="e.g. --type=all for options or name=John for arguments" name="parameters" id="parameters" value="" type="text">
                            <p class="d-none"></p>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label>Timezone</label><i class="fa fa-info-circle" title="Select a timezone for your task. App timezone is selected by default"></i>
                            <select id="timezone" name="timezone" class="form-control select2" placeholder="Select a timezone">
                                @foreach ($timezones as $key => $timezone)
                                    <option value="{{$timezone}}">
                                        {{$timezone}}
                                    </option>
                                <p class="d-none"></p>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Module</label><i class="fa fa-info-circle" title="Select a module"></i>
                            <select id="developer_module_id" name="developer_module_id" class="form-control select2" placeholder="Select a module">
                                @foreach($developer_module as  $module)
                                    <option value="{{$module->id}}">{{$module->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Type</label><i class="fa fa-info-circle" title="Choose whether to define a cron expression or to add frequencies"></i><br>
                            <label>
                                <input type="radio" name="type" value="expression"> Expression
                            </label>
                            <label>
                                <input type="radio" name="type" value="frequency" checked> Frequencies
                            </label>
                        </div>
                        <div class="form-group cron_expression d-none">
                            <label>CRON EXPRESSION</label><i class="fa fa-info-circle" title="Add a cron expression for your task"></i><br>
                            <input class="form-control" placeholder="e.g * * * * * to run this task all the time" name="expression" id="expression" value="" type="text">
                         </div>
                        <div class="form-group frequencies">
                            <label>FREQUENCIES</label><i class="fa fa-info-circle" title="Add   to your task. These frequencies will be converted into a cron expression while scheduling the task"></i><br>
                            <button type="button" class="btn btn-default btn-sm add-remark add-remark-s" data-toggle="modal" data-target="#addFrequencyModal">Add Frequency</button>
                            <div class="table-responsive mt-2" style="width: fit-content">
                            <table class="uk-table table-bordered uk-table-divider uk-margin-remove">
                                <thead>
                                    <tr>
                                        <th>Frequency</th>
                                        <th colspan="2">Parameters</th>
                                    </tr>
                                </thead>
                                <tbody class="freq">
                                    <td class="default_td">No Frequencies Found</td>
                                </tbody>
                            </table>
                            </div>
                        </div>

                        <hr>
                        <div class="form-group">
                            <label>Email Notification (optional)</label><i class="fa fa-info-circle" title="Add an email address to receive notifications when this task gets executed. Leave empty if you do not wish to receive email notifications"></i>
                            <input id="email" class="form-control" placeholder="e.g. john.doe@name.tld" name="notification_email_address" value="" type="text">
                            <p class="d-none"></p>
                        </div>
                        <div class="form-group">
                            <label>SMS Notification (optional)</label><i class="fa fa-info-circle" title="Add a phone number to receive SMS notifications. Leave empty if you do not wish to receive sms notifications"></i>
                            <input  id="phone" placeholder="e.g. 18701234567" class="form-control" name="notification_phone_number" value="" type="text">
                            <p class="d-none"></p>
                        </div>
                        <div class="form-group">
                            <label>Slack Notification (optional)</label><i class="fa fa-info-circle" title="Add a slack web hook url to recieve slack notifications. Phone numbers should include country code and are digits only. Leave empty if you do not wish to receive slack notifications"></i>
                            <input  id="slack" placeholder="e.g. https://hooks.slack.com/TXXXXX/BXXXXX/XXXXXXXXXX" class="form-control" name="notification_slack_webhook" value="" type="text">
                            <p class="d-none"></p>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label>Miscellaneous Options</label>
                            <br>
                            <input type="hidden" name="dont_overlap" id="dont_overlap" value="0" checked>
                            <input type="checkbox" name="dont_overlap" id="dont_overlap" value="1" class="mr-2">Don't Overlap
                            <i class="fa fa-info-circle" title="Add a slack web hook url to recieve slack notifications. Phone numbers should include country code and are digits only. Leave empty if you do not wish to receive slack notifications"></i>
                            <br>

                            <input type="hidden" name="run_in_maintenance" id="run_in_maintenance" value="0" checked>
                            <input type="checkbox" name="run_in_maintenance" id="run_in_maintenance" value="1" class="mr-2">Run in maintenance mode
                            <i class="fa fa-info-circle" title="Add a slack web hook url to recieve slack notifications. Phone numbers should include country code and are digits only. Leave empty if you do not wish to receive slack notifications"></i>
                            <br>

                            <input type="hidden" name="run_on_one_server" id="run_on_one_server" value="0" checked>
                            <input type="checkbox" name="run_on_one_server" id="run_on_one_server" value="1" class="mr-2">Run on a single server
                            <i class="fa fa-info-circle" title="Add a slack web hook url to recieve slack notifications. Phone numbers should include country code and are digits only. Leave empty if you do not wish to receive slack notifications"></i>

                            <p class="d-none"></p>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label>Cleanup Options</label><i class="fa fa-info-circle" title="Determine if an over-abundance of results will be removed after a set limit or age. Set non-zero value to enable."></i>
                            <input class="form-control" name="auto_cleanup_num" value="0" type="number"><br>
                                    <label>
                                        <input type="radio" name="auto_cleanup_type" value="days" checked> Days
                                    </label><br>
                                    <label>
                                        <input type="radio" name="auto_cleanup_type" value="results"> Results
                                    </label>
                            <p class="d-none"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-default submit_btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
      </div>


      <div id="showResultModal" class="modal fade" role="dialog" style="z-index: 999999999">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Output</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <h5></h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="show-development-history" class="modal fade" role="dialog" style="z-index: 999999999">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Development Task</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <style>
        .select2-container{ width: 100% !important; padding: 10px}
        .select2-results__option {
            padding-right: 20px;
            vertical-align: middle;
        }
        .select2-results__option:before {
            content: "";
            display: inline-block;
            position: relative;
            height: 20px;
            width: 20px;
            border: 2px solid #e9e9e9;
            border-radius: 4px;
            background-color: #fff;
            margin-right: 20px;
            vertical-align: middle;
        }
        .select2-results__option[aria-selected=true]:before {
            font-family:fontAwesome;
            content: "\f00c";
            color: #fff;
            background-color: #f77750;
            border: 0;
            display: inline-block;
            padding-left: 3px;
        }

        .select2-selection .select2-selection--multiple:after {
            content: 'hhghgh';
        }
        .select-icon .select2-selection__placeholder .badge {
            display: none;
        }
        .select-icon .placeholder {
            display: none;
        }
        .select-icon .select2-results__option:before,
        .select-icon .select2-results__option[aria-selected=true]:before {
            display: none !important;
            /* content: "" !important; */
        }
        .select-icon  .select2-search--dropdown {
            display: none;
        }
    </style>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet" />
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>

<link href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>

<script type="text/javascript">
    $(".js-select2").select2({
        closeOnSelect : false,
        placeholder : "Select user",
        allowHtml: true,
        allowClear: true,
        tags: true,
    });
    $('.icons_select2').select2({
        templateSelection: iformat,
        templateResult: iformat,
        allowHtml: true,
        placeholder: "Placeholder",
        dropdownParent: $( '.select-icon' ),//обавили класс
        allowClear: true,
        multiple: false
    });
    function iformat(icon, badge,) {
        var originalOption = icon.element;
        var originalOptionBadge = $(originalOption).data('badge');
        return $('<span><i class="fa ' + $(originalOption).data('icon') + '"></i> ' + icon.text + '<span class="badge">' + originalOptionBadge + '</span></span>');
    }
     $(function() {
        $(document).on("change","#ckbCheckAll",function (e) {
            e.preventDefault();
            var status = $(this).is(":checked") ? true : false;
            console.log(status);
            $(".checkBoxClass").prop("checked",status);
        });
     });

    $('#command').select2({
        dropdownParent: $('#addEditTaskModal')
    });

    $(".enable-disable").click(function () {
        let type = $(this).attr('cron-status')
        var selectedIds = []
        $(".checkBoxClass:checked").each(function () {
            selectedIds.push($(this).attr('data-id'))
        })
        if(selectedIds.length == 0){
            toastr['error']('Please select at least one task to enable   ');
        return;
        }
        $.ajax({
            type: "POST",
            url: "/totem/tasks/enable-disable",
            beforeSend : function() {
                $(".ajax-loader").show();
            },
            dataType : "json",
            data:{
                ids:selectedIds,
                active: type,
                _token: "{{ csrf_token() }}",
            },
            success: function (response) {
                if(response.status){
                    toastr['success'](response.message);
                }else{
                    toastr['error'](response.message);
                }
                table.draw();
                $('#ckbCheckAll').prop('checked', false);
                $(".ajax-loader").hide();
            },
            error: function () {
                $(".ajax-loader").hide();
                toastr['error']('Something went wrong!');
            }
        });
    });
    $('#developer_module_id').select2({
        dropdownParent: $('#addEditTaskModal')
    });

    var freq = 0;
    $('#addEditTaskModal').on('hidden.bs.modal', function (e) {
        $('.error').remove();
        $(this).attr('data-id', '');
        $('#addEditTaskModal .modal-title').html('Create task');
        $('.freq').html('<tr><td class="default_td">No Frequencies Found</td></tr>');
    });

    $(document).delegate('.view-task', 'click', function(e) {
        let expression = $(this).attr('data-expression');
        $.ajax({
            type: "GET",
            url: "/totem/tasks/"+$(this).data('id'),
            dataType : "json",
            success: function (response) {
                var html_content = '';
                html_content += '<tr class="supplier-10">';
                html_content += '<td>' + response.task.description.substring(0, 80) + '</td>';
                html_content += '<td>' + response.task.command + '</td>';
                var parameters = response.task.parameters != null ? response.task.parameters : 'N/A';
                html_content += '<td>' + parameters + '</td>';
                html_content += '<td>' + expression + '</td>';
                html_content += '<td>' + response.task.timezone + '</td>';
                html_content += '<td>' + response.task.created_at + '</td>';
                html_content += '<td>' + response.task.updated_at + '</td>';
                var notification_email_address = response.task.notification_email_address == null ? 'N/A' : response.task.notification_email_address;
                html_content += '<td>' + notification_email_address + '</td>';
                var notification_phone_number = response.task.notification_phone_number == null ? 'N/A' : response.task.notification_phone_number;
                html_content += '<td>' + notification_phone_number + '</td>';
                var notification_slack_webhook = response.task.notification_slack_webhook == null ? 'N/A' : response.task.notification_slack_webhook;
                html_content += '<td>' + notification_slack_webhook + '</td>';
                html_content += '<td>' + response.results + ' seconds' + '</td>';
                html_content += '<td>' + response.task.upcoming + '</td>';
                html_content += '</tr>';

                if(response.task.dont_overlap || response.task.run_in_maintenance || response.task.run_on_one_server){
                    $("#view_task_modal .notes").removeClass('d-none');
                }

                if(response.task.dont_overlap){
                    $("#view_task_modal .dont_overlap").removeClass('d-none');
                }

                if(response.task.run_in_maintenance){
                    $("#view_task_modal .run_in_maintenance").removeClass('d-none');
                }

                if(response.task.run_on_one_server){
                    $("#view_task_modal .run_on_one_server").removeClass('d-none');
                }
                $("#view_task_modal tbody").html(html_content);
                $('#view_task_modal').modal('show');
            },
            error: function () {
                toastr['error']('Something went wrong!');
            }
        });
    });

    $(document).off().on("click",".command-execution-error",function(e) {

        $.ajax({
            type: "POST",
            url: "/totem/tasks/"+$(this).data('id')+"/get-error",
            dataType : "json",
            data:{
                id:$(this).data('id'),
                _token: "{{ csrf_token() }}",
             },
            dataType : "json",
            success: function (response) {
                var t = ''
                //$( response.data ).each(function(key, val ) {
                var d = response.data;
                for(let i=0; i< response.data.length; i++){
                   // console.log( key + ": " + val);
                    t += '<tr><td>'+d[i].id+'</td>';
                    t += '<td>'+d[i].signature+'</td>'
                    t += '<td>'+d[i].error+'</td>'
                    t += '<td>'+d[i].error_count+'</td>'
                    t += '<td>'+d[i].status+'</td>'
                    t += '<td>'+d[i].module+'</td></tr>'
                 };
                $("#view_execution_error tbody").html(t);
                $('#view_execution_error').modal('show');
                toastr['success'](response.message);
            },
            error: function () {
                toastr['error']('Something went wrong!');
            }
        });
    });

    $(document).delegate('.execution-history', 'click', function(e) {
        e.preventDefault();
        let url = "{{ route('totem.task.execution-history',['task'=>':id'])}}";
        url = url.replace(':id',$(this).data('id'));

        $.ajax({
            type: "GET",
            url: url,
            success: function (response) {
                var html_content = '';
                var results = response.task;
                for(let i=0; i< results.length; i++){
                    html_content += '<tr>';
                    html_content += '<td>' + results[i].ran_at + '</td>';
                    html_content += '<td>' + (results[i].duration / 1000).toFixed(2) + ' seconds</td>';
                    html_content += `<td id="show-result" data-output="${results[i].result}"><i class="fa fa-info-circle"></i></td>`;
                    html_content += '</tr>';
                }
                if(results.length == 0){
                    html_content += '<tr class="text-center"><td colspan="3"><h5>' + 'Not executed yet.' + '</h5></td></tr>';
                }
                $("#view_execution_history tbody").html(html_content);
                $('#view_execution_history').modal('show');
            },
            error: function () {
                toastr['error']('Something went wrong!');
            }
        });
    });

    $(document).delegate('.command-schedule', 'click', function(e) {
        $.ajax({
            type: "GET",
            url: "query-command/"+$(this).data('id'),
            success: function (response) {
                var html_content = '';
                for(let i=0; i< response.length; i++){
                    html_content += '<tr>';
                    html_content += '<td>' + response[i].schedule_name + '</td>';
                    html_content += '<td>' + response[i].query + '</td>';
                    html_content += '<td>' + response[i].description + '</td>';
                    html_content += '</tr>';
                }
                if(response.length == 0){
                    html_content += '<tr class="text-center"><td colspan="3"><h5>' + 'No Data to show.' + '</h5></td></tr>';
                }
                $("#view_command_query tbody").html(html_content);
                $('#view_command_query').modal('show');
            },
            error: function () {
                toastr['error']('Something went wrong!');
            }
        });
    });

    $(document).delegate('.show-cron-history', 'click', function(e) {
        $.ajax({
            type: "GET",
            url: "cron-history/"+$(this).data('id'),
            success: function (response) {
                console.log(response, "response")
                var html_content = response.map(r => {
                    return `<tr><td>${r.assign_by_name}</td><td>${r.assign_to_name}</td></tr>`
                }).join('');

                if(response.length == 0){
                    html_content += '<tr class="text-center"><td colspan="3"><h5>' + 'No Data to show.' + '</h5></td></tr>';
                }
                $("#view_cron_assign_history tbody").html(html_content);
                $('#view_cron_assign_history').modal('show');
            },
            error: function () {
                toastr['error']('Something went wrong!');
            }
        });
    });


    $(document).delegate('.assign-user', 'click', function(e) {
        $('#taskId').val($(this).attr('task-id'));
        console.log($(this).attr('assing-id'))
        console.log($(this).attr('task-id'))
        $('#view_assign_user').modal('show');


    });

    $(document).on("click","#show-result",function(e) {
        let results = $(this).attr('data-output');
        $("#showResultModal .modal-body h5").html(results);
        $('#showResultModal').modal('show');
    });

    $(document).delegate('.execute-task', 'click', function(e) {
        thiss = $(this);
        thiss.html(`<img src="/images/loading_new.gif" style="cursor: pointer; width: 0px;">`);
        $.ajax({
            type: "GET",
            url: "/totem/tasks/"+$(this).data('id')+"/execute",
            dataType : "json",
            success: function (response) {
                toastr['success']('Task executed successfully!');
                thiss.html(`<img src="/images/send.png" style="cursor: pointer; width: 0px;">`);
            },
            error: function (response) {
                if(response.status == 200){
                    toastr['success']('Task executed successfully!');
                }else{
                    toastr['error']('Something went wrong!');
                }
                thiss.html(`<img src="/images/send.png" style="cursor: pointer; width: 0px;">`);
            }
        });
    });

    $(document).delegate('.delete-tasks', 'click', function(e) {
        if(confirm('Do you really want to delete this task?')){
            $.ajax({
            type: "POST",
            url: "/totem/tasks/"+$(this).data('id')+"/delete",
            data: {
				_token: "{{ csrf_token() }}",
            },
            dataType : "json",
            success: function (response) {
                if(response.status){
                    toastr['success'](response.message);
                }else{
                    toastr['error'](response.message);
                }
                setTimeout(function(){
                    window.location.reload(1);
                }, 1000);
            },
            error: function () {
                toastr['error']('Something went wrong!');
            }
        });
        }
    });

    $(document).on("click",".active-task",function(e) {
        let active = $(this).attr('data-active');
        $.ajax({
            type: "POST",
            url: "/totem/tasks/"+$(this).data('id')+"/status",
            data: {
                active: active,
				_token: "{{ csrf_token() }}",
            },
            dataType : "json",
            success: function (response) {
                if(response.status){
                    toastr['success'](response.message);
                }else{
                    toastr['error'](response.message);
                }
                setTimeout(function(){
                    window.location.reload(1);
                }, 1000);
            },
            error: function () {
                toastr['error']('Something went wrong!');
            }
        });
    });

    $('#frequency').change(function(){
        $('.added_params').remove();
        let params = JSON.parse($(this).val());
        if(params.parameters){
            let html = '';
            for(let i=0; i<params.parameters.length; i++){
                html += `
                <div class="form-group added_params">
                    <input type="text" value="" name="${params.parameters[i].name}" placeholder="${params.parameters[i].label}" class="form-control">
                </div>
                `;
            }
            $(this).closest('.modal-body').append(html);
        }
    });

    $('input[name="type"]').change(function(){
        if($(this).val() == 'expression'){
            $('.cron_expression').removeClass('d-none');
            $('.frequencies').addClass('d-none');
        }else{
            $('.frequencies').removeClass('d-none');
            $('.cron_expression').addClass('d-none');
        }
    });

    $(document).on('click', '.remove_td', function(){
        $(this).closest('tr').remove();
    });

    $('.add_freq').click(function(){
        $('.default_td').remove();
        let freq_type = JSON.parse($('#addFrequencyModal').find('select').val());
        let input_fields = $('#addFrequencyModal').find('input');
        let tr = `
                    <tr>
                    <td data-id="${freq}">${freq_type.label}
                        <input type="hidden" name="frequencies[${freq}][interval]" value="${freq_type.interval}">
                        <input type="hidden" name="frequencies[${freq}][label]" value="${freq_type.label}">
                    </td>
                    <td data-id="${freq}">
                    `;
        for(let i=0; i<input_fields.length; i++){
            tr += `${i!=0 ? ',' : ''} ${$(input_fields[i]).val()}
                        <input type="hidden" name="frequencies[${freq}][parameters][${i}][name]" value="${$(input_fields[i]).attr('name')}">
                        <input type="hidden" name="frequencies[${freq}][parameters][${i}][value]" value="${$(input_fields[i]).val()}">
                    `;
        }
        if(input_fields.length == 0){
            tr += `No Parameters`;
        }
        tr += ` </td>
                <td>
                    <a class="remove_td">
                        <i class="fa fa-window-close"></i>
                    </a>
                </td>
                </tr>`;
        $('.freq').append(tr);
        freq++;
        $('#addFrequencyModal').modal('hide');
    });

    $('.submit_btn').click(function(){

        $('.error').remove();
        let url = $('#addEditTaskModal').attr('data-id') == '' ? '/totem/tasks/create' : `/totem/tasks/${$('#addEditTaskModal').attr('data-id')}/edit`
        var form_data =  $('.taskForm').serialize();
        $.ajax({
            type: "POST",
            url: url,
            data: form_data,
            dataType : "json",
            success: function (response) {
                if(response.task){
                    toastr['success']('Task Updated Successfully.');
                }else{
                    // toastr['error']('Something went wrong!');
                }
                table.draw();
                $('.modal').modal('hide');
            },
            error: function (response) {
                if(response.status == 200){
                    toastr['success']('Task Created Successfully.');
                    setTimeout(function(){
                        window.location.reload(1);
                    }, 1000);
                }else{
                    debugger;
                    let errors = response.responseJSON.errors;
                    let error = '';
                    for (var key in errors) {
                        if($(`input[name="${key}"]`).length == 0){
                             error = `<p class="error" style="color:red;margin-top:-15px">${errors[key][0]}</p>`;
                            $(`select[name="${key}"]`).parent().after(error);
                        }else{
                            error = `<p class="error" style="color:red">${errors[key][0]}</p>`;
                            $(`input[name="${key}"]`).after(error);
                        }
                        if(key == 'frequencies'){
                            error = `<p class="error" style="color:red;margin-top:-15px">${errors[key][0]}</p>`;
                            $('.frequencies').after(error);
                        }
                    }
                    toastr['error'](error);
                }
                toastr['error'](errors.message);
            }
        });

    });
    $('.grant_button').click(function(){
        let type = $(this).attr('bulk-assign');
        if (!type){
            const form_data =  $('.post-assign-cron').serialize();
            return;
            $.ajax({
                type: "POST",
                url: '/totem/tasks/assign-users',
                data: form_data,
                dataType : "json",
                success: function (response) {
                    if(response.status){
                        toastr['success']('Cron assigned Succesfully.');
                    }else{
                        toastr['error']('Something went wrong!');
                    }
                    setTimeout(function(){
                        window.location.reload(1);
                    }, 1000);
                },
                error: function (response) {
                    if(response.status == 200){
                        toastr['success']('Task Created Successfully.');
                        setTimeout(function(){
                            window.location.reload(1);
                        }, 1000);
                    }else{
                        debugger;
                        let errors = response.responseJSON.errors;
                        let error = '';
                        for (var key in errors) {
                            if($(`input[name="${key}"]`).length == 0){
                                error = `<p class="error" style="color:red;margin-top:-15px">${errors[key][0]}</p>`;
                                $(`select[name="${key}"]`).parent().after(error);
                            }else{
                                error = `<p class="error" style="color:red">${errors[key][0]}</p>`;
                                $(`input[name="${key}"]`).after(error);
                            }
                            if(key == 'frequencies'){
                                error = `<p class="error" style="color:red;margin-top:-15px">${errors[key][0]}</p>`;
                                $('.frequencies').after(error);
                            }
                        }
                        toastr['error'](error);
                    }
                    toastr['error'](errors.message);
                }
            });

        }
        else {
            const form_data =  $('.post-assign-cron').serialize();
            console.log(form_data);
            $.ajax({
                type: "POST",
                url: '/totem/tasks/bulk-assign',
                data: form_data,
                dataType : "json",
                success: function (response) {
                    if(response.status){
                        toastr['success']('Cron assigned Succesfully.');
                    }else{
                        toastr['error']('Something went wrong!');
                    }
                    setTimeout(function(){
                        window.location.reload(1);
                    }, 1000);
                },
                error: function (response) {
                    if(response.status == 200){
                        toastr['success']('Task Created Successfully.');
                        setTimeout(function(){
                            window.location.reload(1);
                        }, 1000);
                    }else{
                        debugger;
                        let errors = response.responseJSON.errors;
                        let error = '';
                        for (var key in errors) {
                            if($(`input[name="${key}"]`).length == 0){
                                error = `<p class="error" style="color:red;margin-top:-15px">${errors[key][0]}</p>`;
                                $(`select[name="${key}"]`).parent().after(error);
                            }else{
                                error = `<p class="error" style="color:red">${errors[key][0]}</p>`;
                                $(`input[name="${key}"]`).after(error);
                            }
                            if(key == 'frequencies'){
                                error = `<p class="error" style="color:red;margin-top:-15px">${errors[key][0]}</p>`;
                                $('.frequencies').after(error);
                            }
                        }
                        toastr['error'](error);
                    }
                    toastr['error'](errors.message);
                }
            });

        }

    });

    $(document).delegate('.task-history', 'click', function() {
        $.ajax({
            type: "GET",
            url: "/totem/tasks/"+$(this).data('id')+"/development-task",
            beforeSend : function() {
                $(".ajax-loader").show();
            },
            success: function (response) {
                $(".ajax-loader").hide();
                $("#show-development-history").find(".modal-body").html(response);
                $("#show-development-history").modal("show");
            },
            error: function (response) {
                $(".ajax-loader").hide();
                if(response.status != 200){
                    toastr['error']('Something went wrong!');
                }
            }
        });
    });
    $('.add-torterm').click(function(){
        $('#addEditTaskModal').modal('show');
    });
    $('.add-remark-s').click(function(){
        $('#addFrequencyModal').modal('show');
    });
    $(document).delegate('.edit-task', 'click', function(){
        freq = 0;
        $('#addEditTaskModal').attr('data-id', $(this).data('id'));
        $('#addEditTaskModal .modal-title').html('Edit task');
        $.ajax({
            type: "GET",
            url: "/totem/tasks/"+$(this).data('id'),
            dataType : "json",
            success: function (response) {
                let task_fields = response.task;

                for (var key in task_fields) {
                    console.log(key);
                    if($(`input[name="${key}"]`).length != 0){
                        $(`input[name="${key}"]`).val(task_fields[key]);
                        if($(`input[type="checkbox"]`) && task_fields[key] == 1)
                            $(`input[name="${key}"]`).prop( "checked", true );


                    }else if(task_fields[key]  != null && $(`select[name="${key}"]`).length != 0){
                        $(`select[name="${key}"]`).val(task_fields[key]);
                        $(`select[name="${key}"]`).val(task_fields[key]).trigger("change");

                    }
                    if(key == 'frequencies'){
                        if(task_fields[key].length){
                            $('.default_td').remove();
                        }
                        for(let i=0; i<task_fields[key].length; i++){
                            let interval = task_fields[key][i].interval;
                            let label = task_fields[key][i].label;
                            let parameters = task_fields[key][i].parameters;
                            let tr = `
                                        <tr>
                                        <td data-id="${freq}">${label}
                                            <input type="hidden" name="frequencies[${freq}][interval]" value="${interval}">
                                            <input type="hidden" name="frequencies[${freq}][label]" value="${label}">
                                        </td>
                                        <td data-id="${freq}">
                                        `;
                            for(let j=0; j<task_fields[key][i].parameters; j++){
                                tr += `${j!=0 ? ',' : ''} ${$(task_fields[i]).val()}
                                            <input type="hidden" name="frequencies[${freq}][parameters][${j}][name]" value="${$(task_fields[j]).attr('name')}">
                                            <input type="hidden" name="frequencies[${freq}][parameters][${j}][value]" value="${$(task_fields[j]).val()}">
                                        `;
                            }
                            if(task_fields[key][i].parameters.length == 0){
                                tr += `No Parameters`;
                            }
                            tr += ` </td>
                                    <td>
                                        <a class="remove_td">
                                            <i class="fa fa-window-close"></i>
                                        </a>
                                    </td>
                                    </tr>`;
                            $('.freq').append(tr);
                            freq++;
                        }
                    }

                }
                $('#addEditTaskModal').modal('show');
            },
            error: function (response) {
                if(response.status != 200){
                    toastr['error']('Something went wrong!');
                }
            }
        });
    });

    var table = $('#tasks-table').DataTable({
        processing: true,
        serverSide: true,
        // info: false,
        searching: true,
        lengthChange: false,
        pageLength: 50,
        searchDelay: 1000,
        language: {
            zeroRecords: "Record not found",
        },
        order: [[ 2 , "asc"]],
        "aoColumnDefs": [
            { "aTargets": '_all', className: 'dt-left' },
            { "bSortable": false, "aTargets": @json($nonSortableColumnIndex) },
            { "bSearchable": false, "aTargets": @json($nonSortableColumnIndex) }
        ],
        ajax: {
            url: "{{ route('totem.tasks.all') }}",
            data: function(d) {
                d.description = $('#totem__search__form').val();
                d.filter_frequency = $('#filter_frequency').val();
                d.developer_module = $('#developer_module').val();
                d.is_active = $('#is_active').val();
            },
            error: function(response) {
                console.log(response);
            }
        },
        columns: @json($columns),
        rowCallback: function (row, data) {
            if (data.is_active=='1') {
                $(row).addClass('red');
            }
        }
    });

    table.on('draw', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    $('#filter-form').submit(function(e) {
        e.preventDefault();
        table.draw();
    });
    $('#reset-filter').click(function(e) {
        e.preventDefault();
        $('#filter-form')[0].reset();
        table.draw();
    });

</script>
@endsection
