@extends('layouts.app')
@php
  $current_timezone = date_default_timezone_get();
@endphp
@section('favicon' , 'development-issue.png')
@section('title', $title == "devtask" ? 'Development Issue':'Development Task')
@section('styles')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .status-selection .btn-group {
            padding: 0;
            width: 100%;
        }

        .status-selection .multiselect {
            width: 100%;
        }

        .pd-sm {
            padding: 0px 8px !important;
        }

        tr {
            background-color: #f9f9f9;
        }

        .mr-t-5 {
            margin-top: 5px !important;
        }

        #myDiv {
            position: fixed;
            z-index: 99;
            text-align: center;
        }

        #myDiv img {
            position: fixed;
            top: 50%;
            left: 50%;
            right: 50%;
            bottom: 50%;
        }

        .green-notification {
            color: green;
        }

        .red-notification {
            color: grey;
        }

        .table-scrapper, .table-scrapper th, .table-scrapper td {
            font-size: 14px
        }

        .add-scrapper-remarks {
            float: left;
            padding: 10px 2px;
        }

        .add-scrapper-textarea {
            float: left;
            display: inline-block;
            width: 90%;
        }

        #task_Tables td {
            /* word-break: break-all; */
        }

    </style>
@endsection


@section('large_content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">{{ ucfirst($title) }} ({{$issues->total()}})</h2>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $priorities = [
        '1' => 'Critical',
        '2' => 'Urgent',
        '3' => 'Normal'
        ];
    @endphp
    <div id="myDiv" style="z-index: 999999">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;">
    </div>
    <div class="row mb-4">
        <div class="col-md-12">
            @include("development.partials.task-issue-search")
            <div class="pull-right mt-4">

                @if (auth()->user()->isAdmin())
                    <a class="btn btn-secondary" href="{{ route('development.scrapper.index') }}" role="link"> Scrapper
                        Verification Data </a>
                    <button class="btn btn-secondary" style="color:white;" data-toggle="modal"
                            data-target="#dlcolumnvisibilityList"> Column Visiblity
                    </button>
                @endif

                <button type="button" page="csv-download-page" class="btn btn-secondary csv-date-range-modal-btn" data-toggle="modal" data-target="#csv-date-range-modal" data-task-type="devtask">Download Tasks</button>
                <button type="button" page="view-all-task-page" class="btn btn-secondary csv-date-range-modal-btn" data-toggle="modal" data-target="#csv-date-range-modal" data-task-type="devtask">View All Tasks</button>
                @include('task-module.partials.modal-csv-export-date-picker')

                <a class="btn btn-secondary" data-toggle="collapse" href="#plannedFilterCount" role="button"
                   aria-expanded="false" aria-controls="plannedFilterCount">
                    Show Planned count
                </a>
                <a class="btn btn-secondary" data-toggle="collapse" href="#inProgressFilterCount" role="button"
                   aria-expanded="false" aria-controls="inProgressFilterCount">
                    Show In Progress count
                </a>
                <a style="color:white;" class="btn btn-secondary  priority_model_btn">Priority</a>
                @if(auth()->user()->isReviwerLikeAdmin())
                    <a href="javascript:" class="btn btn-secondary" id="newTaskModalBtn" data-toggle="modal"
                       data-target="#newTaskModal">Add New Dev Task </a>
                @endif
                @if(Auth::user()->isAdmin())
                <a class="btn btn-secondary" style="color:white;" data-toggle="modal" data-target="#newStatusModal">Create
                    Status</a>
                @endif
                <a class="btn btn-secondary" style="color:white;" id="make_delete_button">Delete Tasks</a>
            </div>


        </div>
    </div>
    @include("development.partials.task-issue-counter")

    @php
        $query = http_build_query(Request::except('page'));
        $query = url()->current() . (($query == '') ? $query . '?page=' : '?' . $query . '&page=');
    @endphp

    <div class="form-group position-fixed" style="top: 50px; left: 20px;">
        Goto :
        <select onchange="location.href = this.value;" class="form-control" id="page-goto">
            @for($i = 1 ; $i <= $issues->lastPage() ; $i++ )
                <option data-value="{{$i}}"
                        value="{{ $query.$i }}" {{ ($i == $issues->currentPage() ? 'selected' : '') }}>{{ $i }}</option>
            @endfor
        </select>
    </div>
    {{-- <div class="infinite-scroll"> --}}
        <div>
            @if($title == 'issue' && auth()->user()->isReviwerLikeAdmin())
                <table class="table table-bordered table-striped">
                    <tr class="add-new-issue">
                        @include("development.partials.add-new-issue")
                    </tr>
                </table>
            @endif
            <div class="infinite-scroll-products-inner">
                @include("development.partials.task-master")
            </div>
            <?php echo $issues->appends(request()->except('page'))->links(); ?>

            {{-- <img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..."
                 style="display: none" /> --}}
        </div>
    {{-- </div> --}}
    @include("development.partials.create-new-module")
    @include("development.partials.assign-issue-modal")
    @include("development.partials.assign-priority-modal")
    @include("development.partials.chat-list-history-modal")
    @include("development.partials.upload-document-modal")
    @include("partials.plain-modal")
    @include("development.partials.timer-history")

    @include("development.partials.status-update-check-list")
    @include("development.partials.meeting-time-modal")
    @include("development.partials.time-tracked-modal")
    @include("development.partials.add-status-modal")
    @include("development.partials.user_history_modal")
    @include("development.partials.pull-request-history-modal")

    @include("development.partials.development-reminder-modal")
    @include("development.partials.google-drive-files-modal")
    <div id="preview-task-create-get-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Task Remark</h4>
                    <input type="text" name="remark_pop" class="form-control remark_pop"
                           placeholder="Please enter remark" style="width: 200px;">
                    <button type="button" class="btn btn-default sub_remark" data-task_id="">Save</button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th style="width:1%;">ID</th>
                                <th style=" width: 12%">Update By</th>
                                <th style="word-break: break-all; width:12%">Remark</th>
                                <th style="width: 11%">Created at</th>
                                <th style="width: 11%">Action</th>
                            </tr>
                            </thead>
                            <tbody class="task-create-get-list-view">
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

    <div id="create-d-task-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create Task</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('development/create/hubstaff_task') }}" method="post" id="assign_task_form">
                        @csrf
                        <div class="form-group">
                            <input type="hidden" name="id" id="issueId" />
                            <input type="hidden" name="type" id="type" />
                            <label for="task_for">Task For</label>
                            <select name="task_for" class="form-control task_for" style="width:100%;">
                                <option value="">Select</option>
                                <option value="hubstaff">Hubstaff</option>
                                <option value="time_doctor">Time Doctor</option>
                            </select>
                        </div>
                        <div class="form-group time_doctor_account_section">
                            <label for="time_doctor_account">Task Account</label>
                            {{ html()->select("time_doctor_account", ['' => ''])->class("form-control time_doctor_account_modal globalSelect2")->style("width:100%;")->data('ajax', route('select2.time_doctor_accounts_for_task'))->data('placeholder', 'Account') }}
                        </div>
                        <div class="form-group time_doctor_project_section">
                            <label for="time_doctor_project">Time Doctor Project</label>
                            {{ html()->select("time_doctor_project", ['' => ''])->class("form-control time_doctor_project globalSelect2")->style("width:100%;")->data('ajax', route('select2.time_doctor_projects'))->data('placeholder', 'Project') }}
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-default" data-task_id="">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div id="record-voice-notes" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Record & Send Voice Message</h4>
                </div>
                <div class="modal-body">
                    <Style>
                        #rvn_status:after {
                            overflow: hidden;
                            display: inline-block;
                            vertical-align: bottom;
                            -webkit-animation: ellipsis steps(4, end) 900ms infinite;
                            animation: ellipsis steps(4, end) 900ms infinite;
                            content: "\2026";
                            width: 0px;
                        }

                        @keyframes ellipsis {
                            to {
                                width: 40px;
                            }
                        }

                        @-webkit-keyframes ellipsis {
                            to {
                                width: 40px;
                            }
                        }
                    </style>
                    <input type="hidden" name="rvn_id" id="rvn_id" value="">
                    <input type="hidden" name="rvn_tid" id="rvn_tid" value="">
                    <button id="rvn_recordButton" class="btn btn-s btn-secondary">Start Recording</button>
                    <button id="rvn_pauseButton" class="btn btn-s btn-secondary" disabled>Pause Recording</button>
                    <button id="rvn_stopButton" class="btn btn-s btn-secondary" disabled>Stop Recording</button>
                    <div id="formats">Format: start recording to see sample rate</div>
                    <div id="rvn_status">Status: Not started...</div>
                    <div id="recordingsList"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="rvn-btn-close-modal" class="btn btn-default" data-dismiss="modal">Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @include("development.actions-update-modal")
    @include("development.partials.time-history-modal")
    @include('global_componants.files_and_attachments.files_and_attachments', ['module' => 'development_list'])
    @include("development.partials.show-scrapper-logs-modal")

@endsection
@section('scripts')

    <script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-datetimepicker.min.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/jquery-ui.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.jscroll.min.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-multiselect.min.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-filestyle.min.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/recorder.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/record-voice-notes.js') }} "></script>

    <script>
      jQuery(document).ready(function() {
        applyDateTimePicker(jQuery(".cls-start-due-date"));
      });

      function applyDateTimePicker(eles) {
        if (eles.length) {
          eles.datetimepicker({
            format: "YYYY-MM-DD HH:mm:ss",
            sideBySide: true
          });
        }
      }

      function funGetTaskInformationModal() {
        return jQuery("#modalTaskInformationUpdates");
      }

      function funDevTaskInformationUpdatesTime(type, id) {
        if (type == "start_date") {
          if (confirm("Are you sure, do you want to update?")) {
            // siteLoader(1);
            let mdl = funGetTaskInformationModal();
            jQuery.ajax({
              headers: {
                "X-CSRF-TOKEN": jQuery("meta[name=\"csrf-token\"]").attr("content")
              },
              url: "{{ route('development.update.start-date') }}",
              type: "POST",
              data: {
                id: id,
                value: $("input[name=\"start_dates" + id + "\"]").val(),
                estimatedEndDateTime: $("input[name=\"estimate_date" + id + "\"]").val()
              }
            }).done(function(res) {
              // siteLoader(0);
              siteSuccessAlert(res);
            }).fail(function(err) {
              // siteLoader(0);
              siteErrorAlert(err);
            });
          }
        } else if (type == "estimate_date") {
          if (confirm("Are you sure, do you want to update?")) {
            // siteLoader(1);
            let mdl = funGetTaskInformationModal();
            jQuery.ajax({
              headers: {
                "X-CSRF-TOKEN": jQuery("meta[name=\"csrf-token\"]").attr("content")
              },
              url: "{{ route('development.update.estimate-date') }}",
              type: "POST",
              data: {
                id: id,
                value: $("input[name=\"estimate_date" + id + "\"]").val(),
                remark: mdl.find("input[name=\"remark\"]").val()
              }
            }).done(function(res) {
              // siteLoader(0);
              siteSuccessAlert(res);
            }).fail(function(err) {
              // siteLoader(0);
              siteErrorAlert(err);
            });
          }
        } else if (type == "cost") {
          if (confirm("Are you sure, do you want to update?")) {
            // siteLoader(1);
            let mdl = funGetTaskInformationModal();
            jQuery.ajax({
              headers: {
                "X-CSRF-TOKEN": jQuery("meta[name=\"csrf-token\"]").attr("content")
              },
              url: "{{ route('development.update.cost') }}",
              type: "POST",
              data: {
                id: currTaskInformationTaskId,
                value: mdl.find("input[name=\"cost\"]").val()
              }
            }).done(function(res) {
              // siteLoader(0);
              siteSuccessAlert(res);
            }).fail(function(err) {
              // siteLoader(0);
              siteErrorAlert(err);
            });
          }
        } else if (type == "estimate_minutes") {
          if (confirm("Are you sure, do you want to update?")) {
            // siteLoader(1);
            let mdl = funGetTaskInformationModal();
            jQuery.ajax({
              headers: {
                "X-CSRF-TOKEN": jQuery("meta[name=\"csrf-token\"]").attr("content")
              },
              url: "{{ route('development.update.estimate-minutes') }}",
              type: "POST",
              data: {
                issue_id: id,
                estimate_minutes: $("input[name=\"estimate_minutes" + id + "\"]").val(),
                remark: mdl.find("textarea[name=\"remark\"]").val()
              }
            }).done(function(res) {
              // siteLoader(0);
              siteSuccessAlert(res);
            }).fail(function(err) {
              // siteLoader(0);
              siteErrorAlert(err);
            });
          }
        } else if (type == "lead_estimate_time") {
          if (confirm("Are you sure, do you want to update?")) {
            // siteLoader(1);
            let mdl = funGetTaskInformationModal();
            jQuery.ajax({
              headers: {
                "X-CSRF-TOKEN": jQuery("meta[name=\"csrf-token\"]").attr("content")
              },
              url: "{{ route('development.update.lead-estimate-minutes') }}",
              type: "POST",
              data: {
                issue_id: currTaskInformationTaskId,
                lead_estimate_time: mdl.find("input[name=\"lead_estimate_time\"]").val(),
                remark: mdl.find("input[name=\"lead_remark\"]").val()
              }
            }).done(function(res) {
              // siteLoader(0);
              siteSuccessAlert(res);
            }).fail(function(err) {
              // siteLoader(0);
              siteErrorAlert(err);
            });
          }
        }
      }

      $(document).on("click", ".set-remark", function(e) {
        $(".remark_pop").val("");
        var task_id = $(this).data("task_id");
        $(".sub_remark").attr("data-task_id", task_id);
      });

      $(document).on("click", ".set-remark, .sub_remark", function(e) {
        var thiss = $(this);
        var task_id = $(this).data("task_id");
        var remark = $(".remark_pop").val();
        $.ajax({
          type: "POST",
          url: "{{route('task.create.get.remark')}}",
          headers: {
            "X-CSRF-TOKEN": jQuery("meta[name=\"csrf-token\"]").attr("content")
          },
          data: {
            task_id: task_id,
            remark: remark,
            type: "Dev-task"
          },
          beforeSend: function() {
            $("#loading-image").show();
          }
        }).done(function(response) {
          if (response.code == 200) {
            $("#loading-image").hide();
            $("#preview-task-create-get-modal").modal("show");
            $(".task-create-get-list-view").html(response.data);
            $(".remark_pop").val("");
            toastr["success"](response.message);
          } else {
            $("#loading-image").hide();
            $("#preview-task-create-get-modal").modal("show");
            $(".task-create-get-list-view").html("");
            toastr["error"](response.message);
          }

        }).fail(function(response) {
          $("#loading-image").hide();
          $("#preview-task-create-get-modal").modal("show");
          $(".task-create-get-list-view").html("");
          toastr["error"](response.message);
        });
      });
      $(document).on("click", ".copy_remark", function(e) {
        var thiss = $(this);
        var remark_text = thiss.data("remark_text");
        copyToClipboard(remark_text);
        /* Alert the copied text */
        toastr["success"]("Copied the text: " + remark_text);
        //alert("Copied the text: " + remark_text);
      });

      function copyToClipboard(text) {
        var sampleTextarea = document.createElement("textarea");
        document.body.appendChild(sampleTextarea);
        sampleTextarea.value = text; //save main text in it
        sampleTextarea.select(); //select textarea contenrs
        document.execCommand("copy");
        document.body.removeChild(sampleTextarea);
      }


      $(document).ready(function() {

        $("#development_reminder_from").datetimepicker({
          format: "YYYY-MM-DD HH:mm"
        });

        var developmentToRemind = null;
        $(document).on("click", ".development-set-reminder", function() {
          let developmentId = $(this).data("id");
          let frequency = $(this).data("frequency");
          let message = $(this).data("reminder_message");
          let reminder_from = $(this).data("reminder_from");
          let reminder_last_reply = $(this).data("reminder_last_reply");

          $("#frequency").val(frequency);
          $("#reminder_message").val(message);
          $("#developmentReminderModal").find("#development_reminder_from").val(reminder_from);
          if (reminder_last_reply == 1) {
            $("#developmentReminderModal").find("#reminder_last_reply").prop("checked", true);
          } else {
            $("#developmentReminderModal").find("#reminder_last_reply_no").prop("checked", true);
          }
          developmentToRemind = developmentId;
        });

        $(document).on("click", ".development-submit-reminder", function() {
          var developmentReminderModal = $("#developmentReminderModal");
          let frequency = $("#frequency").val();
          let message = $("#reminder_message").val();
          let development_reminder_from = developmentReminderModal.find("#development_reminder_from").val();
          let reminder_last_reply = (developmentReminderModal.find("#reminder_last_reply").is(":checked")) ? 1 : 0;

          $.ajax({
            url: "{{action([\App\Http\Controllers\DevelopmentController::class, 'updateDevelopmentReminder'])}}",
            type: "POST",
            success: function() {
              toastr["success"]("Reminder updated successfully!");
              $(".set-reminder img").css("background-color", "");
              if (frequency > 0) {
                $(".development-set-reminder img").css("background-color", "red");
              }
            },
            data: {
              development_id: developmentToRemind,
              frequency: frequency,
              message: message,
              reminder_from: development_reminder_from,
              reminder_last_reply: reminder_last_reply,
              _token: "{{ csrf_token() }}"
            }
          });
        });


        $(document).on("click", ".assign-issue-button", function() {
          var issue_id = $(this).data("id");
          var url = "{{ url('development') }}/" + issue_id + "/assignIssue";

          $("#assignIssueForm").attr("action", url);
        });

        $(".multiselect").multiselect({
          allSelectedText: "All",
          includeSelectAllOption: true
        });


        $("select.select2").select2({
          tags: true,
          width: "100%"
        });


        $(".assign-team-lead.select2").select2({
          width: "100%"
        });

        $(".assign-tester.select2").select2({
          width: "100%"
        });

        $(".assign-master-user.select2").select2({
          width: "100%"
        });

        $(".assign-user.select2").select2({
          width: "100%"
        });

        $.each($(".resolve-issue"), function(k, v) {
          if (!$(v).hasClass("select2-hidden-accessible")) {
            $(v).select2({
              width: "100%"
            });
          }
        });

        $("select#priority_user_id").select2({
          tags: true,
          width: "100%"
        });

        $(".estimate-time").datetimepicker({
          format: "HH:mm"
        });

        $("#estimate_date_picker").datepicker({
          dateformat: "yyyy-mm-dd"
        });

      });

      function getPriorityTaskList(id) {
        var selected_issue = [0];

        $("input[name =\"selected_issue[]\"]").each(function() {
          if ($(this).prop("checked") == true) {
            selected_issue.push($(this).val());
          }
        });

        $.ajax({
          url: "{{route('development.issue.list.by.user.id')}}",
          type: "POST",
          data: {
            user_id: id,
            _token: "{{csrf_token()}}",
            selected_issue: selected_issue
          },
          success: function(response) {
            $(".show_issue_priority").html(response.html);
              <?php if (auth()->user()->isAdmin()) { ?>
              $(".show_issue_priority").sortable();
              <?php } ?>
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
        $("#priority_user_id").val("");
        $("#sel_user_id").val("0");

        $(".show_issue_priority").html("");
          <?php if (auth()->user()->isAdmin()) { ?>
          $("#priority_user_id").show();
        getPriorityTaskList($("#priority_user_id").val());
          <?php } else { ?>
          $("#priority_user_id").hide();
        getPriorityTaskList('{{auth()->user()->id}}');
          <?php } ?>
          $("#priority_model").modal("show");
      });

      $("#priority_user_id").change(function() {
        $("#sel_user_id").val($(this).val());
        getPriorityTaskList($(this).val());
      });

      $(document).on("submit", "#priorityForm", function(e) {
        e.preventDefault();
          <?php if (auth()->user()->isAdmin()) { ?>
          $.ajax({
            url: "{{route('development.issue.set.priority')}}",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
              toastr["success"]("Priority successfully update!!", "success");
              $("#priority_model").modal("hide");
            },
            error: function() {
              alert("There was error loading priority task list data");
            }
          });
          <?php } ?>
      });

      $(document).on("click", ".send-message", function(event) {

        var textBox = $(this).closest(".panel-footer").find(".send-message-textbox");
        var sendToStr = $(this).closest(".panel-footer").find(".send-message-number").val();

        let issueId = textBox.attr("data-id");
        let message = textBox.val();
        if (message == "") {
          return;
        }

        let self = textBox;

        $.ajax({
          url: "{{action([\App\Http\Controllers\WhatsAppController::class, 'sendMessage'], 'issue')}}",
          type: "POST",
          data: {
            "issue_id": issueId,
            "message": message,
            "sendTo": sendToStr,
            "_token": "{{csrf_token()}}",
            "status": 2
          },
          dataType: "json",
          success: function(response) {
            toastr["success"]("Message sent successfully!", "Message");
            $("#message_list_" + issueId).append("<li>" + response.message.created_at + " : " + response.message.message + "</li>");
            $(self).removeAttr("disabled");
            $(self).val("");
          },
          beforeSend: function() {
            $(self).attr("disabled", true);
          },
          error: function() {
            alert("There was an error sending the message...");
            $(self).removeAttr("disabled", true);
          }
        });
      });


      $(document).on("click", ".send-message-open", function(event) {
        var textBox = $(this).closest(".expand-row").find(".send-message-textbox");
        var is_audio = $(this).closest(".expand-row").find(".is_audio").val();
        var sendToStr = $(this).closest(".expand-row").find(".send-message-number").val();
        var add_autocomplete = $(this).closest(".expand-row").find("[name=add_to_autocomplete]").is(":checked");

        let issueId = textBox.attr("data-id");
        let message = textBox.val();
        if (message == "") {
          return;
        }

        let self = textBox;

        $.ajax({
          url: "{{action([\App\Http\Controllers\WhatsAppController::class, 'sendMessage'], 'issue')}}",
          type: "POST",
          data: {
            "issue_id": issueId,
            "message": message,
            "sendTo": sendToStr,
            "_token": "{{csrf_token()}}",
            "status": 2,
            "add_autocomplete": add_autocomplete,
            "is_audio": is_audio
          },
          dataType: "json",
          success: function(response) {
            $("#loading-image").hide(); //Purpose : Hide loader - DEVTASK-4359
            toastr["success"]("Message sent successfully!", "Message");
            if (response.message) {
              var created_at = response.message.created_at;
              var message = response.message.message;
            } else {
              var created_at = "";
              var message = "";
            }
            $("#message_list_" + issueId).append("<li>" + created_at + " : " + message + "</li>");
            $(self).removeAttr("disabled");
            $(self).val("");
          },
          beforeSend: function() {
            $("#loading-image").show(); //Purpose : Show loader - DEVTASK-4359
            $(self).attr("disabled", true);
          },
          error: function() {
            $("#loading-image").hide(); //Purpose : Hide loader - DEVTASK-4359
            alert("There was an error sending the message...");
            $(self).removeAttr("disabled", true);
          }
        });
      });

      $(document).on("change", ".set-responsible-user", function() {
        let id = $(this).attr("data-id");
        let userId = $(this).val();

        if (userId == "") {
          return;
        }

        $.ajax({
          url: "{{action([\App\Http\Controllers\DevelopmentController::class, 'assignResponsibleUser'])}}",
          data: {
            responsible_user_id: userId,
            issue_id: id
          },
          success: function() {
            toastr["success"]("User assigned successfully!", "Message");
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
          url: "{{action([\App\Http\Controllers\DevelopmentController::class, 'assignUser'])}}",
          data: {
            assigned_to: userId,
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

      $(document).on("change", ".task-module", function() {
        let id = $(this).attr("data-id");
        let moduleID = $(this).val();

        if (moduleID == "") {
          return;
        }

        $.ajax({
          url: "{{action([\App\Http\Controllers\DevelopmentController::class, 'changeModule'])}}",
          data: {
            module_id: moduleID,
            issue_id: id
          },
          success: function() {
            toastr["success"]("Module assigned successfully!", "Message");
          }
        });

      });

      $(document).on("change", ".assign-master-user", function() {
        let id = $(this).attr("data-id");
        let userId = $(this).val();

        if (userId == "") {
          return;
        }

        $.ajax({
          url: "{{action([\App\Http\Controllers\DevelopmentController::class, 'assignMasterUser'])}}",
          data: {
            master_user_id: userId,
            issue_id: id
          },
          success: function() {
            toastr["success"]("Master User assigned successfully!", "Message");
          },
          error: function(error) {
            toastr["error"](error.responseJSON.message, "Message");

          }
        });

      });


      $(document).on("keyup", ".save-milestone", function(event) {
        if (event.keyCode != 13) {
          return;
        }
        let id = $(this).attr("data-id");
        let total = $(this).val();

        $.ajax({
          url: "{{action([\App\Http\Controllers\DevelopmentController::class, 'saveMilestone'])}}",
          data: {
            total: total,
            issue_id: id
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

      $(document).on("change", ".save-language", function(event) {

        let id = $(this).attr("data-id");
        let language = $(this).val();

        $.ajax({
          url: "{{action([\App\Http\Controllers\DevelopmentController::class, 'saveLanguage'])}}",
          data: {
            language: language,
            issue_id: id
          },
          success: function() {
            toastr["success"]("Language updated successfully!", "Message");
          }
        });
      });

      $(document).on("click", ".expand-row", function() {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
          $(this).find(".td-mini-container").toggleClass("hidden");
          $(this).find(".td-full-container").toggleClass("hidden");
        }
      });

      $(document).on("keyup", ".estimate-time-change", function() {
        if (event.keyCode != 13) {
          return;
        }
        let issueId = $(this).data("id");
        let est_time_remark = $("#est_time_remark_" + issueId).val();
        let estimate_minutes = $("#estimate_minutes_" + issueId).val();
        if ((est_time_remark !== "") && (estimate_minutes !== "")) {
          $.ajax({
            url: "{{action([\App\Http\Controllers\DevelopmentController::class, 'saveEstimateMinutes'])}}",
            data: {
              estimate_minutes: estimate_minutes,
              remark: est_time_remark,
              issue_id: issueId
            },
            success: function() {
              $("#est_time_remark_" + issueId).val("");
              toastr["success"]("Estimate Minutes updated successfully!", "Message");
            }
          });
        } else {
          toastr["warning"]("Remark and EST Time fields are required", "Message");
        }

      });


      $(document).on("click", ".pull-request-history", function() {

        var issueId = $(this).data("id");
        $("#pull-request-history_div table tbody").html("");
        $.ajax({
          url: "{{ route('development/pull/history') }}",
          data: {
            id: issueId
          },
          success: function(data) {
            console.log(data.pullrequests);
            $.each(data.pullrequests, function(i, item) {
              $("#pull-request-history_div table tbody").append(
                "<tr>\
                            <td>" + moment(item["created_at"]).format("DD/MM/YYYY") + "</td>\
                                    <td>" + ((item["user_id"] != null) ? item["user_id"] : "-") + "</td>\
                                    <td>" + ((item["pull_request_id"] != null) ? item["pull_request_id"] : "-") + "</td>\
                                </tr>"
              );
            });
          }
        });
        $("#pull-request-history_modal").modal("show");
      });
      $(document).on("click", ".show-status-history", function() {
        var data = $(this).data("history");
        var issueId = $(this).data("id");
        $("#status_history_modal table tbody").html("");
        $.ajax({
          url: "{{ route('development/status/history') }}",
          data: {
            id: issueId
          },
          success: function(data) {
            if (data != "error") {
              $.each(data, function(i, item) {
                $("#status_history_modal table tbody").append(
                  "<tr>\
                          <td>" + moment(item["created_at"]).format("DD/MM/YYYY") + "</td>\
                                    <td>" + ((item["old_value"] != null) ? item["old_value"] : "-") + "</td>\
                                    <td>" + item["new_value"] + "</td>\
                                    <td>" + item["name"] + "</td>\
                                </tr>"
                );
              });
            }
          }
        });
        $("#status_history_modal").modal("show");
      });

      function humanizeDuration(input, units) {
        // units is a string with possible values of y, M, w, d, h, m, s, ms
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
        var issueId = $(this).data("id");
        var type = $(this).data("type");
        $("#time_tracked_div table tbody").html("");
        $.ajax({
          url: "{{ route('development/tracked/history') }}",
          data: {
            id: issueId,
            type: type
          },
          success: function(data) {
            if (data != "error") {
              $.each(data.histories, function(i, item) {
                var sec = parseInt(item["total_tracked"]);
                $("#time_tracked_div table tbody").append(
                  "<tr>\
                          <td>" + moment(item["created_at"]).format("DD-MM-YYYY") + "</td>\
                                    <td>" + ((item["name"] != null) ? item["name"] : "") + "</td>\
                                    <td>" + humanizeDuration(sec, "s") + "</td>\
                                </tr>"
                );
              });
            }
          }
        });
        $("#time_tracked_modal").modal("show");
      });
      $(document).on("click", ".download", function(event) {
        event.preventDefault();
        document.getElementById("download").value = 2;
        $("form.search").submit();

      });

      $(document).on("click", ".create-hubstaff-task", function() {
        var issueId = $(this).data("id");
        var type = $(this).data("type");
        $("#issueId").val(issueId);
        $("#type").val(type);
        $("#create-d-task-modal").modal("show");

        $(this).css("display", "none");
      });

      $(document).on("submit", "#assign_task_form", function(event) {
        event.preventDefault();
        $.ajax({
          url: "{{route('development/create/hubstaff_task')}}",
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

      $(document).on("change", ".change-task-status", function() {
        var taskId = $(this).data("id");
        var status = $(this).val();
        $.ajax({
          url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'changeTaskStatus']) }}",
          type: "POST",
          data: {
            task_id: taskId,
            _token: "{{csrf_token()}}",
            status: status
          },
          success: function() {
            toastr["success"]("Status Changed successfully!");
          }
        });
      });

      function sendImage(id) {

        $.ajax({
          url: "{{action([\App\Http\Controllers\WhatsAppController::class, 'sendMessage'], 'issue')}}",
          type: "POST",
          data: {
            issue_id: id,
            type: 1,
            message: "",
            _token: "{{csrf_token()}}",
            status: 2
          },
          success: function() {
            toastr["success"]("Message sent successfully!", "Message");

          },
          beforeSend: function() {
            $(self).attr("disabled", true);
          },
          error: function() {
            alert("There was an error sending the message...");
            $(self).removeAttr("disabled", true);
          }
        });

      }

      function sendUploadImage(id) {

        $("#file-input" + id).trigger("click");

        $("#file-input" + id).change(function() {
          event.preventDefault();
          let image_upload = new FormData();
          let TotalImages = $(this)[0].files.length; //Total Images
          let images = $(this)[0];

          for (let i = 0; i < TotalImages; i++) {
            image_upload.append("images[]", images.files[i]);
          }
          image_upload.append("TotalImages", TotalImages);
          image_upload.append("status", 2);
          image_upload.append("type", 2);
          image_upload.append("issue_id", id);
          if (TotalImages != 0) {

            $.ajax({
              method: "POST",
              url: "{{action([\App\Http\Controllers\WhatsAppController::class, 'sendMessage'], 'issue')}}",
              data: image_upload,
              async: true,
              contentType: false,
              processData: false,
              beforeSend: function() {
                $("#loading-image").show();
              },
              success: function(images) {
                $("#loading-image").hide();
                alert("Images send successfully");
              },
              error: function() {
                console.log(`Failed`);
              }
            });
          }
        });
      }

      //Popup for add new task
      $(document).on("click", "#newTaskModalBtn", function() {
        if ($("#newTaskModal").length > 0) {
          $("#newTaskModal").remove();
        }

        $.ajax({
          url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'openNewTaskPopup']) }}",
          type: "GET",
          dataType: "JSON",
          success: function(resp) {
            console.log(resp);
            if (resp.status == "ok") {
              $("body").append(resp.html);
              $("#newTaskModal").modal("show");
              $("select.select2").select2({
                tags: true
              });
            }
          }
        });
      });

      function resolveIssue(obj, task_id) {
        let id = task_id;
        let status = $(obj).val();
        let checkList = @json($checkList);

        if (status == "") {
          return;
        }

        if (checkList[status]) {
          $("#status_checklist").html(" to mark task as " + status);
          $("#checklist_issue_id").val(id);
          $("#checklist_is_resolved").val(status);
          let html = "";
          $.each(checkList[status], function(index, value) {
            html += "<tr>";
            html += "<td>" + value.name + "</td>";
            html += "<td><textarea required class='form-control' name='checklist[" + value.id + "]'></textarea></td>";
            html += "</tr>";
            $(".show_checklist").html(html);
          });
          $("#status_update_checklist").modal("show");
        } else {
          $.ajax({
            url: "{{action([\App\Http\Controllers\DevelopmentController::class, 'resolveIssue'])}}",
            data: {
              issue_id: id,
              is_resolved: status
            },
            success: function() {
              toastr["success"]("Status updated!", "Message");
            },
            error: function(error) {
              toastr["error"](error.responseJSON.message);
            }
          });
        }
      }

      $(document).on("submit", "#statusUpdateChecklistForm", function(e) {
        e.preventDefault();
        $.ajax({
          url: "{{action([\App\Http\Controllers\DevelopmentController::class, 'resolveIssue'])}}",
          data: $(this).serialize(),
          success: function(response) {
            toastr["success"]("Status updated!", "Message");
            $("#status_update_checklist").modal("hide");
          },
          error: function(error) {
            toastr["error"](error.responseJSON.message);
          }
        });
      });

      $("#filecount").filestyle({
        htmlIcon: "<span class=\"oi oi-random\"></span>",
        badge: true,
        badgeName: "badge-danger"
      });

      $(document).on("click", ".upload-document-btn", function() {
        var id = $(this).data("id");
        $("#upload-document-modal").find("#hidden-identifier").val(id);
        $("#upload-document-modal").modal("show");
      });

      $(document).on("submit", "#upload-task-documents", function(e) {
        e.preventDefault();
        var form = $(this);
        var postData = new FormData(form[0]);
        $.ajax({
          method: "post",
          url: "{{action([\App\Http\Controllers\DevelopmentController::class, 'uploadDocument'])}}",
          data: postData,
          processData: false,
          contentType: false,
          dataType: "json",
          success: function(response) {
            if (response.code == 200) {
              toastr["success"]("Status updated!", "Message");
              $("#upload-document-modal").modal("hide");
            } else {
              toastr["error"](response.error, "Message");
            }
          }
        });
      });

      $(document).on("click", ".list-document-btn", function() {
        var id = $(this).data("id");
        $.ajax({
          method: "GET",
          url: "{{action([\App\Http\Controllers\DevelopmentController::class, 'getDocument'])}}",
          data: {
            id: id
          },
          dataType: "json",
          success: function(response) {
            if (response.code == 200) {
              $("#blank-modal").find(".modal-title").html("Document List");
              $("#blank-modal").find(".modal-body").html(response.data);
              $("#blank-modal").modal("show");
            } else {
              toastr["error"](response.error, "Message");
            }
          },
          error: function(error) {
            toastr["error"]("Unauthorized permission development-get-document", "Message");

          }
        });
      });


      $(document).on("change", "#is_milestone", function() {

        var is_milestone = $("#is_milestone").val();
        if (is_milestone == "1") {
          $("#no_of_milestone").attr("required", "required");
        } else {
          $("#no_of_milestone").removeAttr("required");
        }
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

      $(document).on("click", "#make_delete_button", function() {
        if (selected_tasks.length > 0) {
          var x = window.confirm("Are you sure you want to bin these tasks");
          if (!x) {
            return;
          }
          $.ajax({
            type: "POST",
            url: "{{action([\App\Http\Controllers\DevelopmentController::class, 'deleteBulkTasks'])}}",
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

      $(document).on("change", ".assign-team-lead", function() {
        let id = $(this).attr("data-id");
        let userId = $(this).val();
        console.log(id);
        console.log(userId);

        if (userId == "") {
          return;
        }

        $.ajax({
          url: "{{action([\App\Http\Controllers\DevelopmentController::class, 'assignTeamlead'])}}",
          data: {
            team_lead_id: userId,
            issue_id: id
          },
          success: function() {
            toastr["success"]("Team lead assigned successfully!", "Message");
          },
          error: function(error) {
            toastr["error"](error.responseJSON.message, "Message");

          }
        });
      });

      $(document).on("change", ".assign-tester", function() {
        let id = $(this).attr("data-id");
        let userId = $(this).val();
        console.log(id);
        console.log(userId);

        if (userId == "") {
          return;
        }

        $.ajax({
          url: "{{action([\App\Http\Controllers\DevelopmentController::class, 'assignTester'])}}",
          data: {
            tester_id: userId,
            issue_id: id
          },
          success: function() {
            toastr["success"]("Tester assigned successfully!", "Message");
          },
          error: function(error) {
            toastr["error"](error.responseJSON.message, "Message");

          }
        });
      });
      var task_id = 0;
      $(document).on("click", ".meeting-timing-popup", function() {
        let id = $(this).attr("data-id");
        let type = $(this).attr("data-type");
        $("#meeting_time_div table tbody").html("");
        $.ajax({
          url: "{{action([\App\Http\Controllers\DevelopmentController::class, 'getMeetingTimings'])}}",
          data: {
            type: type,
            issue_id: id
          },
          success: function(response) {
            task_id = response.issue_id;
            var developerTime = response.developerTime;
            var master_devTime = response.master_devTime;
            var testerTime = response.testerTime;
            $("#hidden_issue_id").val(task_id);
            $("#developer_task_id").val(task_id);
            $("#developer_approved_time").html(developerTime);
            $("#master_approved_time").html(master_devTime);
            $("#tester_approved_time").html(testerTime);

            $.each(response.timings, function(i, item) {
              if (item["approve"] == 1) {
                var checked = "checked";
              } else {
                var checked = "";
              }
              $("#meeting_time_div table tbody").append(
                "<tr>\
                            <td>" + moment(item["created_at"]).format("DD/MM/YYYY") + "</td>\
                                    <td>" + item["type"] + "</td>\
                                    <td>" + item["name"] + "</td>\
                                    <td>" + ((item["old_time"] != null) ? item["old_time"] : "-") + "</td>\
                                    <td>" + ((item["time"] != null) ? item["time"] : "-") + "</td>\
                                    <td>" + item["updated_by"] + "</td>\
                                    <td>" + item["note"] + "</td>\
                                    </td><td><input type=\"checkbox\" name=\"approve_time\" value=\"" + item["id"] + "\" " + checked + " class=\"approve_time\"/></td>\
                                </tr>"
              );
            });
          },
          error: function(error) {
            toastr["error"](error.responseJSON.message, "Message");

          }
        });
        $("#meeting_time_modal").modal("show");
        $("#meeting_hidden_task_id").val(id);
        $("#hidden_type").val(type);
      });
      $(document).on("submit", "#search-time-form", function() {
        event.preventDefault();
        var type = $("#user_type_id").val();
        var timing_type = $("#timing_type_id").val();
        $("#meeting_time_div table tbody").html("");
        console.log(task_id);
        $.ajax({
          url: "{{action([\App\Http\Controllers\DevelopmentController::class, 'getMeetingTimings'])}}",
          data: {
            type: type,
            issue_id: task_id,
            timing_type: timing_type
          },
          success: function(response) {
            task_id = response.issue_id;
            var developerTime = response.developerTime;
            var master_devTime = response.master_devTime;
            var testerTime = response.testerTime;
            $("#hidden_issue_id").val(task_id);
            $("#developer_task_id").val(task_id);
            $("#developer_approved_time").val(developerTime);
            $("#master_approved_time").val(master_devTime);
            $("#tester_approved_time").val(testerTime);
            $.each(response.timings, function(i, item) {
              if (item["approve"] == 1) {
                var checked = "checked";
              } else {
                var checked = "";
              }
              $("#meeting_time_div table tbody").append(
                "<tr>\
                            <td>" + moment(item["created_at"]).format("DD/MM/YYYY") + "</td>\
                                    <td>" + item["type"] + "</td>\
                                    <td>" + item["name"] + "</td>\
                                    <td>" + ((item["old_time"] != null) ? item["old_time"] : "-") + "</td>\
                                    <td>" + ((item["time"] != null) ? item["time"] : "-") + "</td>\
                                    <td>" + item["updated_by"] + "</td>\
                                    <td>" + item["note"] + "</td>\
                                    </td><td><input type=\"checkbox\" name=\"approve_time\" value=\"" + item["id"] + "\" " + checked + " class=\"approve_time\"/></td>\
                                </tr>"
              );
            });
          },
          error: function(error) {
            toastr["error"](error.responseJSON.message, "Message");

          }
        });
        $("#meeting_time_modal").modal("show");
        $("#hidden_type").val(type);
      });


      $(document).on("submit", "#add-time-form", function(event) {
        event.preventDefault();
        $.ajax({
          url: "{{route('development/time/meeting/store')}}",
          type: "POST",
          data: $(this).serialize(),
          success: function(response) {
            toastr["success"]("Successfully done", "success");
            $("#meeting_time_modal").modal("hide");
            $("#add-time-form").trigger("reset");
          },
          error: function(error) {
            toastr["error"](error.responseJSON.message);
          }
        });

      });

      $(document).on("submit", "#approve-meeting-time-btn", function(event) {
        event.preventDefault();
          <?php if (auth()->user()->isAdmin()) { ?>
          $.ajax({
            url: "/development/time/meeting/approve/" + task_id,
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
              toastr["success"]("Successfully approved", "success");
              $("#meeting_time_modal").modal("hide");
            },
            error: function() {
              toastr["error"](error.responseJSON.message);
            }
          });
          <?php } ?>
      });


      $(document).on("click", ".show-user-history", function() {
        var issueId = $(this).data("id");
        $("#user_history_div table tbody").html("");
        $.ajax({
          url: "{{ route('development/user/history') }}",
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
        $("#user_history_modal").modal("show");
      });


      function fetchGoogleDriveFileData(task_id) {
        if (task_id == "") {
          $("#googleDriveFileData").html("<tr><td>No Data Found.</td></tr>");
          $("#driveFiles").modal("show");
          return;
        } else {
          $.get(window.location.origin + "/google-drive-screencast/task-files/" + task_id, function(data, status) {
            $("#googleDriveFileData").html(data);
            $("#driveFiles").modal("show");
          });
        }
      }

      $(document).ready(function() {
        $(document).on("click", ".create-task-document", function() {
          let task_id = $(this).data("id");
          if (task_id != "") {
            $("#task_id").val($(this).data("id"));
            $("#taskGoogleDocModal").modal("show");
          } else {
            toastr["error"]("Task id not found.");
          }
        });
        $(document).on("click", ".show-created-task-document", function() {
          let task_id = $(this).data("id");
          if (task_id != "") {
            $.ajax({
              type: "GET",
              url: "{{route('google-docs.task.show')}}",
              data: {
                task_id,
                task_type: "DEVTASK"
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

                $("#taskGoogleDocListModal").modal("show");
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
            url: "{{route('google-docs.task')}}",
            data: {
              _token: "{{csrf_token()}}",
              doc_type,
              doc_name,
              task_id,
              task_type: "DEVTASK",
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
        $(document).on("click", ".btn-trigger-rvn-modal", function() {
          var id = $(this).attr("data-id");
          var tid = $(this).attr("data-tid");
          $("#record-voice-notes #rvn_id").val(id);
          $("#record-voice-notes #rvn_tid").val(tid);
          $("#record-voice-notes").modal("show");
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
      });

      function Showactionbtn(id) {
        $(".action-btn-tr-" + id).toggleClass("d-none");
      }
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
          $(".quickSubCategory").append("<option value=\"[]\" data-id=\"" + response.data.id + "\">" + response.data.name + "</option>");
        },
        afterQuickCategoryAdd: function(response) {
          $(".quick_category").val("");
          $(".quickCategory").append("<option value=\"[]\" data-id=\"" + response.data.id + "\">" + response.data.name + "</option>");
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
          $("#send_message_" + ele.attr("data-id")).val(ele.val());

          var userEmaillUrl = "/email/email-frame-info/" + $("#reply_email_id").val();
          ;
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
          }).fail(function(errObj) {
          });

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
                ele.closest("#shortcutsIds").find(".quickCommentEmail").append($("<option>", {
                  value: reply.reply,
                  text: reply.reply,
                  "data-id": reply.id
                }));
              });
            }

          }).fail(function(errObj) {
          });
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

      $(document).on("click", ".approveEstimateFromshortcutButtonTaskPage", function(event) {
        var element = $(this);
        if (confirm("Are you sure, do you want to approve this task?")) {
          event.preventDefault();
          let type = $(this).data("type");
          let task_id = $(this).data("task");
          let history_id = $(this).data("id");
          $.ajax({
            url: "/development/time/history/approve",
            type: "POST",
            data: {
              _token: "{{csrf_token()}}",
              approve_time: history_id,
              developer_task_id: task_id,
              user_id: 0
            },
            success: function(response) {
              element.closest("tr").hide("slow");

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
          let task_id = $(this).data("task");

          $.ajax({
            url: "/development/time/history/start",
            type: "POST",
            data: {
              _token: "{{csrf_token()}}",
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

      let timezone = "<?= $current_timezone; ?>";
      function currentDateTime() {
        
        let now = new Date();

        // Format the date and time to the specified timezone
        let formatter = new Intl.DateTimeFormat('en-US', {
            timeZone: timezone,
            year: 'numeric',
            // month: 'long',
            month: 'numeric',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });

        let dt = formatter.format(now);
        let currentTime = new Date(dt);
        return currentTime;
      }

      $(document).ready(function() {
        $(".m_start_date_").each(function() {
          var elementId = $(this).attr("id"); // Get ID of each element

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
          var currentTime = currentDateTime();
          var timeDifference = currentTime - startTime;
          var hours = Math.floor(timeDifference / (60 * 60 * 1000));
          var minutes = Math.floor((timeDifference % (60 * 60 * 1000)) / (60 * 1000));
          var seconds = Math.floor((timeDifference % (60 * 1000)) / 1000);
          var counterText = pad(hours) + ":" + pad(minutes) + ":" + pad(seconds);

          $("#time-counter_" + id).text(counterText);
        }
      }

      function pad(number) {
        return (number < 10 ? "0" : "") + number;
      }

      $(document).on("click", ".show-timer-history", function() {
        var issueId = $(this).data("id");
        $("#timer_tracked_modal table tbody").html("");
        $.ajax({
          url: "{{ route('development.timer.history') }}",
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
                                    <td>" + ((item["end_date"] != null) ? moment(item["end_date"]).format("DD-MM-YYYY HH:mm:ss") : "Not Stop") + "</td>\
                                </tr>"
                );
              });
            }
          }
        });
        $("#timer_tracked_modal").modal("show");
      });

      $(document).on("click", ".show-scrapper-logs", function () {
        var id = $(this).data("id");
        var url = "{{ route('development.get-scrapper-logs-by-task', ['id' => ':id']) }}";
        url = url.replace(':id', id);

        $.ajax({
            method: "GET",
            url: url,
            dataType: "json",
            success: function (response) {
                if (response.code == 200) {
                    $('#showScrapperLogsModal table tbody').html('');

                    $.each(response.data, function(i, item) {
                        $('#showScrapperLogsModal table tbody').append(
                            `<tr>
                                <td>${ moment(item['created_at']).format('DD-MM-YYYY HH:mm:ss') }</td>
                                <td>${ item.log }</td>
                                <td>${ item.user.name }</td>
                            </tr>`
                        );
                    });

                    $("#showScrapperLogsModal").modal("show");
                } else {
                    toastr["error"]('Something went wrong', "Message");
                }
            }
        });
    });

    $(document).ready(function () {
        $(document).on("click", ".task-container-row", function (e) {
            $(this).find('span:nth-child(1)').toggleClass('hidden');
            $(this).find('span:nth-child(2)').toggleClass('hidden');
        });

        $(document).on("click", ".task-subject-container", function (e) {
            $(this).find('p:nth-child(1)').toggleClass('hidden');
            $(this).find('p:nth-child(2)').toggleClass('hidden');
        });

        $(document).on("click", ".expand-row-btn-lead", function (e) {
            let btnDataId = $(this).attr('data-id');
            $(".expand-column-lead").each(function (index, el) {
                if ($(el).attr('data-id') == btnDataId) {
                    $(el).toggleClass('hidden');
                }
            });
        });
    });
    </script>
@endsection
@push('scripts')
    <script type="text/javascript" src="{{ mix('webpack-dist/js/pages/development/development-list.js') }} "></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush
