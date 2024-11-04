@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Website Logs View ({{$dataArr->total()}})</h2>
		</div>
	</div>
	<div class="mt-3 col-md-12">
		<form action="{{route('website.search.log.view')}}" method="get" class="search">
			@csrf
			<div class="col-1">
				<b>Search</b> 
			</div>
			<div class="col-md-2 pd-sm">
				{{ html()->multiselect("website_ids[]", $websiteLog, request('website_ids'))->class("form-control globalSelect2")->data('placeholder', "Select Website") }}
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search_error" placeholder="Search Error" name="search_error" value="{{ $search_error ?? '' }}">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search_type" placeholder="Search type" name="search_type" value="{{ $search_type ?? '' }}">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="date" name="date" value="{{ (request('date') ?? "" )}}">
			</div>

			<div class="col-lg-3">
				<button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
				   <img src="{{ asset('images/search.png') }}" alt="Search">
			   </button>
			   <a href="{{route('website.log.view')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
			   <a href="{{route('website.log.truncate')}}" class="btn btn-primary" onclick="return confirm('{{ __('Are you sure you want to Truncate a Data?Note : It will Remove All data') }}')">Truncate Data </a>

			   <button type="button" class="btn custom-button float-right mr-3" data-toggle="modal" data-target="#status-create">Add Status</button>
			</div>
		</form>
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th width="3%">ID</th>
			    	<th width="3%">Website</th>
			    	<th width="15%">User</th>
			        <th width="25%">Error</th>
			        <th width="10%">Type</th>
			        <th width="10%">File Path</th>
			        <th width="10%">Date</th>
			        <th width="10%">Status</th>
					<th width="10%">Insert CodeShortcut</th>
					<th>Action</th>
                </tr>
		    	<tbody>
                    @foreach ($dataArr as $data)
                        <tr>
                            <td>{{$data->id}}</td>
                            <td>{{$data->website_id}}</td>
                            <td>
	                          <div class="d-flex align-items-center">
	                            <select name="user" class="user-dropdown" data-id="{{$data->id}}">
	                              <option value="">Select User</option>
	                              @foreach ($allUsers as $user)
	                                <option value="{{$user->id}}" {{$data->user_id == $user->id ? 'selected' : ''}}>{{$user->name}}</option>
	                              @endforeach
	                            </select>
	                            <button type="button" data-id="{{ $data->id  }}" class="btn btn-image user-history-show p-0 ml-2"  title="User Histories" ><i class="fa fa-info-circle"></i></button>
	                          </div>
	                        </td>
							<td>
								<div>
								{{ strlen($data->error) > 10 ? substr($data->error, 0, 70).'...' : $data->error }}
								<i class="fa fa-eye show_logs show-logs-icon" data-id="{{ $data->id }}" style="color: #808080;float: right;"></i>
							    </div>
							</td>
							<td>{{$data->type}}</td>
							<td>{{$data->file_path}}</td>
							<td>{{$data->created_at}}</td>
							<td>
	                          <div class="d-flex align-items-center">
	                            <select name="status" class="status-dropdown" data-id="{{$data->id}}">
	                              <option value="">Select Status</option>
	                              @foreach ($website_log_statuses as $stat)
	                                <option value="{{$stat->id}}" {{$data->status == $stat->id ? 'selected' : ''}}>{{$stat->status_name}}</option>
	                              @endforeach
	                            </select>
	                            <button type="button" data-id="{{ $data->id  }}" class="btn btn-image status-history-show p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>
	                          </div>
	                        </td>
							<td><button class="btn btn-success insert-code-shortcut" data-id="{{ $data->id }}">Insert Code Shortcut</button>		
							</td>
							<td>
	                            <button style="padding:3px;" title="create quick task" type="button" class="btn btn-image d-inline create-quick-task " data-id="@if ($data) {{ $data->id }} @endif"  data-category_title="Website Logs Page" data-title="@if ($data) {{'Website Logs Page - '.$data->id  }} @endif"><i class="fa fa-plus" aria-hidden="true"></i></button>

	                            <button style="padding-left: 0;padding-left:3px;" type="button" class="btn btn-image d-inline count-dev-customer-tasks" title="Show task history" data-id="@if ($data) {{ $data->id }} @endif" data-category="{{ $data->id }}"><i class="fa fa-info-circle"></i></button>
	                        </td>
						</tr>                        
                    @endforeach
		    	</tbody>
		    </thead>
		</table>
		{!! $dataArr->appends(Request::except('page'))->links() !!}
	</div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
</div>
<div class="modal" tabindex="-1" role="dialog" id="reply_logs_modal">
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
                    <div class="col-md-12" id="reply_logs_div">
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
@endsection
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

      <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Status Name</th>
                </tr>
            <tbody>
                @foreach ($website_log_statuses as $website_log_status)
                    <tr>
                        <td>{{ $website_log_status->id }}</td>
                        <td>{{ $website_log_status->status_name }}</td>
                    </tr>
                @endforeach
            </tbody>
            </thead>
        </table>
    </div>
</br></br>
    </div>

  </div>
</div>

<div id="create-quick-task" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?php echo route('task.create.multiple.task.shortcutwebsitelogs'); ?>" method="post" id="task-create-form">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Create Task</h4>
                </div>
                <div class="modal-body">

                    <input class="form-control" value="55" type="hidden" name="category_id" />
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
                            @foreach ($githubRepo as $repository)
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
@include('website-logs.website-logs-status-history')
@include('website-logs.website-logs-user-history')

@section('scripts')
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() 
	{
		$("#tabledata").click(function(e) {
			var BASE_URL = window.location.origin+'/';
			tableData(BASE_URL);
		});
		function tableData(BASE_URL) {
			/*
            var search = $("input[name='search'").val() != "" ? $("input[name='search'").val() : null;
			var date = $("#datepicker").val() !="" ? $("#datepicker").val() : null;
            var download = "?download=" + $("select[name='download_option'").val();
			var server_id = $('.server_id-value').val();
            */

            if($("select[name='download_option'").val() == "yes") {
                window.location.href = BASE_URL+"/scrap-logs/fetch/"+search+"/"+date+"/"+download;
            }

			$.ajax({
				url: "{{route('website.file.list.log')}}",//BASE_URL+"/scrap-logs/fetch/"+search+"/"+date,
				method:"get",
				headers: {
				    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				  },
				data:{ 
                    /*'server_id':server_id, month : $("#monthpicker").val(),"year" : $("#yearpicker").val()*/
                },
				cache: false,
				success: function(data) {
                    console.log(data)
                    $("#log-table tbody").empty();

                    $.each(data.file_list, function(i,row){
                        $("#log-table tbody").append("<tr><td>"+(i+1)+"</td><td>"+row['foldername']+"</td><td><a href='scrap-logs/file-view/"+row['filename']+ '/' +row['foldername']+"' target='_blank'>"+row['filename']+"</a>&nbsp;<a href='javascript:;' onclick='openLasttenlogs(\""+row['scraper_id']+"\")'><i class='fa fa-weixin' aria-hidden='true'></i></a></td><td>"+row['log_msg']+"</td><td>"+row['status']+"</td><td><button style='padding:3px;' type='button' class='btn btn-image make-remark d-inline' data-toggle='modal' data-target='#makeRemarkModal' data-name='"+row['scraper_id']+"'><img width='2px;' src='/images/remark.png'/></button><button style='padding:3px;' type='button' class='btn btn-image log-history d-inline' data-toggle='modal' data-target='#loghistory' data-filename='"+row['filename']+"' data-name='"+row['scraper_id']+"'><i class='fa fa-sticky-note'></i></button><button style='padding:3px;' type='button' class='btn btn-image history d-inline' data-toggle='modal' data-target='#history' data-filename='"+row['filename']+"' data-name='"+row['scraper_id']+"'><i class='fa fa-history'></i></button></td></tr>");

                    });
                }
			});
		}
	});

		$(document).on('click', '.show-logs-icon', function() {
		var id = $(this).data('id');
			$.ajax({
				url: '{{route('website.error.show')}}',
				method: 'GET',
				data: {
					id: id
				},
				success: function(response) {
					$('#reply_logs_modal').modal('show');
					$('#reply_logs_div').html(response);
				},
				error: function(xhr, status, error) {
					alert("Error occured.please try again");
				}
			});
		});

		$(document).on('click', '.insert-code-shortcut', function() {
			var id = $(this).data('id');
			$.ajax({
				url: '{{route('website.insert.code.shortcut')}}',
				method: 'GET',
				data: {
					id: id
				},
				success: function(response) {
					toastr['success'](response.message);
				},
				error: function(xhr, status, error) {
					toastr['error'](response.message);
				}
			});
		});

		$(document).on("click", ".status-save-btn", function(e) {
            e.preventDefault();
            var $this = $(this);
            $.ajax({
              url: "{{route('website.status.create')}}",
              type: "post",
              data: $('#status-create-form').serialize()
            }).done(function(response) {
              if (response.code = '200') {
                $('#loading-image').hide();
                $('#status-create').modal('hide');
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

            $("#loading-image").show();

            var $this = $(this);
            $.ajax({
              url: "{{route('task.create.multiple.task.shortcutwebsitelogs')}}",
                type: 'POST',
                data: $('#task-create-form').serialize()
            }).done(function(response) {
              $("#loading-image").hide();
                if (response.code == 200) {
                    $('#loading-image').hide();
                    $('#create-quick-task').modal('hide');
                    toastr['success'](response.message);
                    location.reload();
                } else {
                    toastr['error'](response.message);
                }
            }).fail(function(errObj) {
              $('#loading-image').hide();
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
                url: '/website/countdevtask/' + site_id,
                dataType: "json",
                headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
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

        $(document).on("change", ".status-dropdown", function(e) {
          e.preventDefault();
          var WebsiteLogId = $(this).data('id');
          var selectedStatus = $(this).val();
          console.log("Dropdown data-id:", WebsiteLogId);
          console.log("Selected status:", selectedStatus);


          // Make an AJAX request to update the status
          $.ajax({
            url: '/website/updatestatus',
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: {
              WebsiteLogId: WebsiteLogId,
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
                url: `{{ route('website.status.histories', [""]) }}/` + id,
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
                        $("#website-logs-status-histories-list").find(".website-logs-status-histories-list-view").html(html);
                        $("#website-logs-status-histories-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        $(document).on("change", ".user-dropdown", function(e) {
          e.preventDefault();
          var WebsiteLogId = $(this).data('id');
          var selectedUser = $(this).val();
          console.log("Dropdown data-id:", WebsiteLogId);
          console.log("Selected user:", selectedUser);


          // Make an AJAX request to update the status
          $.ajax({
            url: '/website/updateuser',
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: {
              WebsiteLogId: WebsiteLogId,
              selectedUser: selectedUser
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

        $(document).on('click', '.user-history-show', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: `{{ route('website.user.histories', [""]) }}/` + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${(v.old_value != null) ? v.old_value.name : ' - ' } </td>
                                        <td> ${(v.new_value != null) ? v.new_value.name : ' - ' } </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#website-logs-user-histories-list").find(".website-logs-user-histories-list-view").html(html);
                        $("#website-logs-user-histories-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });
</script> 
@endsection
    