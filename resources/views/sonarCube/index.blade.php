@extends('layouts.app')

@section('content')

<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
  </div>
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Sonar Cube ({{$issues->total()}})</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-12">
                    <form method="get">
                        <div class="row">
                            <div class="col-md-3">
                                {{ html()->text("search", request("search", null))->class("form-control")->placeholder("Enter input here..") }}
                            </div>
                            <div class="col-md-2">
                                <label for="status">Status </label>
                                <select class="form-control select2", multiple name="status[]" id="status">
                                    @foreach($issuesFilterStatus as $k=>$v)
                                        <option value="{{$k}}"
                                                @if(is_array(request('status')) && in_array($k, request('status')))
                                                    selected
                                                @endif
                                        >{{$v}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="severity">Severity </label>
                                <select class="form-control select2", multiple name="severity[]" id="severity">
                                    @foreach($issuesFilterSeverity as $k=>$v)
                                        <option value="{{$k}}"
                                        @if(is_array(request('severity')) && in_array($k, request('severity')))
                                            selected
                                        @endif
                                        >{{$v}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="author">Author </label>
                                <select class="form-control select2", multiple name="author[]" id="author">
                                    @foreach($issuesFilterAuthor as $k=>$v)
                                        <option value="{{$k}}"
                                        @if(is_array(request('author')) && in_array($k, request('author')))
                                            selected
                                        @endif
                                        >{{$v}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="project">Project </label>
                                <select class="form-control select2", multiple name="project[]" id="project">
                                    @foreach($issuesFilterProject as $k=>$v)
                                        <option value="{{$k}}"
                                        @if(is_array(request('project')) && in_array($k, request('project')))
                                            selected
                                        @endif
                                        >{{$v}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" style="display: inline-block;width: 10%" class="btn btn-sm btn-image">
                                    <img src="/images/search.png" style="cursor: default;">
                                </button>
                                <a href="{{route('sonarqube.list.page')}}" type="button" class="btn btn-image" id=""><img src="/images/resend2.png"></a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-12">
                    <div class="pull-right">
                        <button type="button" class="btn btn-secondary" onclick="listuserprojects()"> Sonar project deatils </button>
                        <button type="button" class="btn btn-secondary" onclick="listprojects()">list the projects </button>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#sonar-project-create"> Create Project </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<div class="tab-content">
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;" id="project-list">
                        <tr>
                            <th width="5%">S.No</th>
                            <th width="10%">Severity</th>
                            <th width="10%">Component</th>
                            <th width="10%">project</th>
                            <th width="10%">Status</th>
                            <th width="10%">Message</th>
                            <th width="10%">Author</th>
                            <th width="5%">Create Date</th>
                            <th width="5%">close Date</th>
                            <th width="5%" style="overflow-wrap: anywhere;">Action</th>
                        </tr>
                        @foreach ($issues as $key=>$issue)
                            <tr>
                                <td>{{ $issue['id'] }}</td>
                                <td>{{ $issue['severity'] }}</td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($issue['component']) > 30 ? substr($issue['component'], 0, 30).'...' :  $issue['component'] }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $issue['component'] }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($issue['project']) > 30 ? substr($issue['project'], 0, 30).'...' :  $issue['project'] }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $issue['project'] }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $issue['status'] }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($issue['message']) > 30 ? substr($issue['message'], 0, 30).'...' :  $issue['message'] }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $issue['message'] }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $issue['author'] }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ \Carbon\Carbon::parse($issue['creationDate'])->format('m-d F') }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    @if(isset($issue['closeDate']) && $issue['closeDate'])
                                        {{ \Carbon\Carbon::parse($issue['closeDate'])->format('m-d F') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$issue["id"]}}')"><i class="fa fa-arrow-down"></i></button>
                                </td>
                            </tr>

                            <tr class='action-btn-tr-{{$issue["id"]}} d-none'>
                                <td class="font-weight-bold">Action</td>
                                <td colspan="8" class="cls-actions">
                                    <div>
                                        <div class="row cls_action_box" style="margin:0px;">
                                            <button style="padding:3px;" title="create quick task" type="button" class="btn btn-image d-inline create-quick-task " data-id="@if ($issue) {{ $issue['id'] }} @endif"  data-category_title="Sonar Qube Page" data-title="@if ($issue) {{'Sonar Qube Page - '.$issue['id'] }} @endif"><i class="fa fa-plus" aria-hidden="true"></i></button>

                                            <button style="padding-left: 0;padding-left:3px;" type="button" class="btn btn-image d-inline count-dev-customer-tasks" title="Show task history" data-id="@if ($issue) {{ $issue['id'] }} @endif" data-category="{{ $issue['id'] }}"><i class="fa fa-info-circle"></i></button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    {{ $issues->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</div>


<div id="sonar-project-create" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="sonar-project-create-form" class="form mb-15" >
            @csrf
            <div class="modal-header">
                <h4 class="modal-title">Create Project</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <strong>Project display name:</strong>
                    {{ html()->text('project')->placeholder('Project display name')->id('project')->class('form-control')->required() }}
                </div>
                <div class="form-group">
                    <strong>Project key :</strong>
                    {{ html()->text('name')->placeholder('Project key')->id('name')->class('form-control')->required() }}
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

<div id="sonar-project-list-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 class="modal-title"><b>Project Lists</b></h4>
                </div>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-12" id="project-list-modal-html">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="sonar-user-project-list-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 class="modal-title"><b>Sonar User Project Lists</b></h4>
                </div>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-12" id="project-user-list-modal-html">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>

<div id="create-quick-task" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?php echo route('task.create.multiple.task.shortcutsonar'); ?>" method="post">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Create Task</h4>
                </div>
                <div class="modal-body">

                    <input class="form-control" value="58" type="hidden" name="category_id" />
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
                            @foreach ($gitHubRepository as $repository)
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


<script type="text/javascript">

$('select.select2').select2();

function Showactionbtn(id){
    $(".action-btn-tr-"+id).toggleClass('d-none')
}

$(document).on('click', '.expand-row', function () {
    var selection = window.getSelection();
    if (selection.toString().length === 0) {
        $(this).find('.td-mini-container').toggleClass('hidden');
        $(this).find('.td-full-container').toggleClass('hidden');
    }
});

$(document).on('submit', '#sonar-project-create-form', function(e){
    e.preventDefault();
    var self = $(this);
    let formData = new FormData(document.getElementById('sonar-project-create-form'));
    var button = $(this).find('[type="submit"]');
    $.ajax({
        url: '{{ route("sonarqube.createProject") }}',
        type: "POST",
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        cache: false,
        beforeSend: function() {
            button.html(spinner_html);
            button.prop('disabled', true);
            button.addClass('disabled');
        },
        complete: function() {
            button.html('Add');
            button.prop('disabled', false);
            button.removeClass('disabled');
        },
         }).done(function(response) {
            if(response.code == 200)
            {
                toastr["success"](response.message);
                  location.reload();
            } else {
                toastr["error"](response.message);
            }
    }).fail(function(response) {
        toastr["error"]("something went wrong");
    });

});

function listprojects() {
    $.ajax({
        url: '{{ route('sonarqube.list.Project') }}',
        dataType: "json",
    }).done(function(response) {
        $('.ajax-loader').hide();
        $('#project-list-modal-html').empty().html(response.html);
        $('#sonar-project-list-modal').modal('show');
        renderdomainPagination(response.data);
    }).fail(function(response) {
        $('.ajax-loader').hide();
        console.log(response);
    });
}

function listuserprojects() {
    $.ajax({
        url: '{{ route('sonarqube.user.projects') }}',
        dataType: "json",
    }).done(function(response) {
        $('.ajax-loader').hide();
        $('#project-user-list-modal-html').empty().html(response.html);
        $('#sonar-user-project-list-modal').modal('show');
        renderdomainPagination(response.data);
    }).fail(function(response) {
        $('.ajax-loader').hide();
        console.log(response);
    });
}

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
        url: '/sonarqube/countdevtask/' + site_id,
        dataType: "json",
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
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

$(document).on('click', '.expand-row-msg', function() {
    var name = $(this).data('name');
    var id = $(this).data('id');
    console.log(name);
    var full = '.expand-row-msg .show-short-' + name + '-' + id;
    var mini = '.expand-row-msg .show-full-' + name + '-' + id;
    $(full).toggleClass('hidden');
    $(mini).toggleClass('hidden');
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

</script>

@endsection
