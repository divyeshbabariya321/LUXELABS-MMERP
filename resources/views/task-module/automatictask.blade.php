@extends('layouts.app')

@section('favicon', 'vendor.png')

@section('title', 'Flag Dev Task')

@section('styles')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <style type="text/css">
        .numberSend {
            width: 160px;
            background-color: transparent;
            color: transparent;
            text-align: center;
            border-radius: 6px;
            position: absolute;
            z-index: 1;
            left: 19%;
            margin-left: -80px;
            display: none;
        }

        .input-sm {
            width: 60px;
        }

        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }

        .cls_filter_inputbox {
            width: 14%;
            text-align: center;
        }

        .message-chat-txt {
            color: #333 !important;
        }

        .cls_remove_leftpadding {
            padding-left: 0px !important;
        }

        .cls_remove_rightpadding {
            padding-right: 0px !important;
        }

        .cls_action_btn .btn {
            padding: 6px 12px;
        }

        .cls_remove_allpadding {
            padding-left: 0px !important;
            padding-right: 0px !important;
        }

        .cls_quick_message {
            width: 100% !important;
            height: 35px !important;
        }

        .cls_filter_box {
            width: 100%;
        }

        .select2-selection.select2-selection--single {
            height: 35px;
        }

        .cls_action_btn .btn-image img {
            width: 13px !important;
        }

        .cls_action_btn .btn {
            padding: 6px 2px;
        }

        .cls_textarea_subbox {
            width: 100%;
        }

        .btn.btn-image.delete_quick_comment {
            padding: 4px;
        }

        .vendor-update-status-icon {
            padding: 0px;
        }

        .cls_commu_his {
            width: 100% !important;
        }

        .vendor-update-status-icon {
            margin-top: -7px;
        }

        .clsphonebox .btn.btn-image {
            padding: 5px;
        }

        .clsphonebox {
            margin-top: -8px;
        }

        .send-message1 {
            padding: 0px 10px;
        }

        .load-communication-modal {
            margin-top: -6px;
            margin-left: 4px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 35px;
        }

        .select2-selection__arrow {
            display: none;
        }

        .cls_mesg_box {
            margin-top: -7px;
            font-size: 12px;
        }

        .pd-rt {
            padding-right: 0px !important;
        }

        .table-bordered {
            border: 1px solid #ddd !important;
        }

        .status-selection .btn-group {
            padding: 0;
            width: 100%;
        }

        .status-selection .multiselect {
            width: 100%;
        }

        .multiselect {
            width: 200px;
        }

        .multiselect .selectBox {
            position: relative;
        }

        .multiselect .selectBox select {
            width: 100%;
            font-weight: bold;
        }

        .multiselect .overSelect {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
        }

        .multiselect #checkboxes {
            display: none;
            border: 1px #dadada solid;
            position: absolute;
            z-index: 999;
            background: #fff;
            width: 200px;
            padding: 5px;
            color: black;
        }

        .multiselect #checkboxes label {
            display: block;
            color: #333;
        }

        .multiselect #checkboxes label input {
            margin-right: 5px;
        }

        div.checkbox1 {
            height: 250px;
            overflow: scroll;

            body {
                height: 100%;
            }

    </style>
@endsection

@section('large_content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>
    <div class="row">
        <div class="col-md-12 p-0">
            <h2 class="page-heading">{{ $title }} (<span id="filter_table_count"> {{ $count }} </span>) </h2>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">


            <?php $base_url = URL::to('/'); ?>
            <div class=" cls_filter_box" style="margin-left: -13px;">                
                @csrf
                @if (auth()->user()->isReviwerLikeAdmin())

                    <div class="row ml-1">
                        <div class="col-md-3">
                            <input name="term" type="text" class="form-control" value="{{ isset($term) ? $term : '' }}"
                                placeholder="search" id="term">
                        </div>

                        <div class="col-md-3">
                            <select id="task_status" class="form-control globalSelect2" name="task_status">
                                <option value="">Select Task Status</option>
                                @if (!empty($task_statuses))
                                    @foreach ($task_statuses as $index => $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-3">
                            <select id="assigned_to" class="form-control globalSelect2" name="assigned_to">
                                <option value="">Select Assigned To</option>
                                @if (!empty($task_statuses))
                                    @foreach ($users as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-3">
                            <button type="button" class="btn btn-image" onclick="submitSearch()"><img
                                    src="{{asset('/images/filter.png')}}" /></button>

                            <button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img
                                    src="{{asset('/images/resend2.png')}}" /></button>

                            <a data-toggle="modal" data-target="#reminderMessageModal" class="btn pd-5 task-set-reminder">
                                <i class="fa fa-bell  red-notification " aria-hidden="true"></i>
                            </a>
                            <a class="btn btn-secondary assignTask" style="color:white;">Assign Task</a>
                        </div>
                    </div>

                @endif
                
            </div>
        </div>

    </div>

    @include('partials.flash_messages')
    <div class="infinite-scroll">
        <div class="table-responsive mt-3">
            <table id="filter_table" class="table table-bordered table-striped"
                style="table-layout:fixed;margin-bottom:0px;">
                <thead>
                    <tr>
                        <th width="10px"><input type="checkbox" onchange="checkAll(this)" name="chk[]"></th>
                        <th width="25px">ID</th>
                        <th width="35px">Created At</th>
                        <th width="40px">Website</th>
                        <th width="25px">Parent Task</th>
                        <th width="30px">Subject</th>
                        <th width="40px">Assigned To</th>
                        <th width="30px">Tracked Time</th>
                        <th width="30px">Estimated Time</th>
                        <th width="30px">Delivery Date</th>
                        <th width="95px">Communication</th>
                        <th width="35px">Status</th>
                        <th width="30px">Action</th>

                    </tr>
                </thead>

                <tbody class="infinite-scroll-pending-inner">
                    @include('task-module.partials.flagsummarydata')
                </tbody>
            </table>
        </div>

    </div>
    @include('development.partials.upload-document-modal')
    @include('development.partials.time-tracked-modal')
    @include('partials.plain-modal')


    <div id="python-action-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Action History</h4>

                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="python-action-history_div">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Old Status</th>
                                        <th>New Status</th>
                                        <th>Updated by</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="status_quick_history_modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Status History</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="row">
                    <div class="col-md-12" id="status_history_div">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Old Status</th>
                                    <th>New Status</th>
                                    <th>Updated by</th>
                                    <th></th>
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
                </form>
            </div>
        </div>
    </div>

    <div id="show-task-model-table" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Assign Task</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form class="form-inline form-search-data" action="{{ route('task.AssignMultipleTaskToUser') }}"
                        method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-control mb-2 mr-sm-2 select2 col-md-10" id="user_assigned_to"
                                    name="user_assigned_to" required>
                                    <option value="">Assigned To</option>
                                    @foreach ($users as $k => $_dev)
                                        <option value="{{ $k }}">{{ $_dev }}</option>
                                    @endforeach
                                </select>
                                <input type='hidden' name='taskIDs[]' id="tsk_id" value="">
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-secondary mb-2">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('development.partials.time-history-modal')
    @include('task-module.partials.tracked-time-history')
    @include('development.partials.user_history_modal')
    <img class="infinite-scroll-products-loader center-block" src="{{ asset('/images/loading.gif') }}" alt="Loading..."
        style="display: none" />


@endsection

@section('scripts')
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/zoom-meetings.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/jquery-ui.js') }} "></script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>

    <script>

        function Taskbtn(id){
            $(".action-taskbtn-tr-"+id).toggleClass('d-none')
        }

        $(document).on('click', '.task-submit-reminder', function() {
            var task_message_form = $("#task_message_form").serialize();
            $.ajax({
                url: "{{ route('development.taskmessage') }}",
                type: 'POST',
                data: task_message_form,
                success: function() {
                    toastr['success']('message updated successfully!');
                },
                error: function() {
                    toastr['error']('Something went wrong, Please try again!');
                }
            });
        });

        function submitSearch() {
            var src = `{{ route('development.automatic.tasks') }}`;
            var term = $('#term').val();
            var task_status = $('#task_status').val()

            var assigned_to = $('#assigned_to').val();

            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    term: term,
                    assigned_to: assigned_to,
                    task_status: task_status,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },

            }).done(function(data) {
                $("#loading-image").hide();
                $("#filter_table tbody").empty().html(data.tbody);
                $("#filter_table_count").text(data.count);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }

            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });

        }

        function resetSearch() {
            src = `{{ route('development.automatic.tasks') }}`;
            blank = '';
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    blank: blank,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },

            }).done(function(data) {
                $("#loading-image").hide();
                $('#term').val('')
                $('#task_status').val('')
                $("#filter_table tbody").empty().html(data.tbody);
                $("#filter_table_count").text(data.count);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }

            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        }

        var isLoading = false;
        var page = 1;
        $(document).ready(function() {

            $(window).scroll(function() {
                if (($(window).scrollTop() + $(window).outerHeight()) >= ($(document).height() - 2500)) {
                    loadMore();
                }
            });

            function loadMore() {
                if (isLoading)
                    return;
                isLoading = true;
                var $loader = $('.infinite-scroll-products-loader');
                page = page + 1;
                $.ajax({
                    url: "{{ route('development.automatic.tasks') }}?ajax=1&page=" + page,
                    type: 'GET',
                    data: $('.form-search-data').serialize(),
                    beforeSend: function() {
                        $loader.show();
                    },
                    success: function(data) {
                        console.log(data);
                        $loader.hide();
                        $("#filter_table tbody").append(data.tbody);
                        $('#vendor-body').append(data);
                        isLoading = false;
                        if (data.tbody == "") {
                            isLoading = true;
                        }

                    },
                    error: function() {
                        $loader.hide();
                        isLoading = false;
                    }
                });
            }
        });

        $(document).on("click", ".assignTask", function(e) {
            e.preventDefault();

            $IDs = $(".table input:checkbox:checked").map(function() {
                return $(this).val();
            }).get();

            if ($IDs == '') {
                alert('Please select any task');
                return false;
            }
            var model = $("#show-task-model-table");
            $("#tsk_id").val($IDs);
            model.modal("show");
        });
    </script>

    <script type="text/javascript">
        $(document).on('click', '.expand-row-msg', function() {
            var id = $(this).data('id');
            console.log(id);
            var full = '.expand-row-msg .td-full-container-' + id;
            var mini = '.expand-row-msg .td-mini-container-' + id;
            $(full).toggleClass('hidden');
            $(mini).toggleClass('hidden');
        });
        $(document).on('change', '.assign-master-user', function() {
            let id = $(this).attr('data-id');
            let userId = $(this).val();

            if (userId == '') {
                return;
            }

            $.ajax({
                url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'assignMasterUser']) }}",
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

        $(document).on('change', '.set-responsible-user', function() {
            let id = $(this).attr('data-id');
            let userId = $(this).val();

            if (userId == '') {
                return;
            }

            $.ajax({
                url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'assignResponsibleUser']) }}",
                data: {
                    responsible_user_id: userId,
                    issue_id: id
                },
                success: function() {
                    toastr["success"]("User assigned successfully!", "Message")
                }
            });
        });

        $(document).on('change', '.assign-user', function() {
            let id = $(this).attr('data-id');
            let userId = $(this).val();

            if (userId == '') {
                return;
            }

            $.ajax({
                url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'assignUser']) }}",
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

        $(document).on('change', '.task-module', function() {
            let id = $(this).attr('data-id');
            let moduleID = $(this).val();

            if (moduleID == '') {
                return;
            }

            $.ajax({
                url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'changeModule']) }}",
                data: {
                    module_id: moduleID,
                    issue_id: id
                },
                success: function() {
                    toastr["success"]("Module assigned successfully!", "Message")
                }
            });

        });
    </script>

    <script type="text/javascript">
        function resolveIssue(obj, task_id) {
            let id = task_id;
            let status = $(obj).val();
            let self = this;

            $.ajax({
                url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'resolveIssue']) }}",
                data: {
                    issue_id: id,
                    is_resolved: status
                },
                success: function() {
                    toastr["success"]("Status updatedd!", "Message")
                },
                error: function(error) {
                    toastr["error"](error.responseJSON.message);
                }
            });
        }
    </script>


    <script type="text/javascript">
        var vendorToRemind = null;
        $('#vendor-search').select2({
            tags: true,
            width: '100%',
            ajax: {
                url: BASE_URL + '/vendor-search',
                dataType: 'json',
                delay: 750,
                data: function(params) {
                    return {
                        q: params.term, // search term
                    };
                },
                processResults: function(data, params) {
                    for (var i in data) {
                        data[i].id = data[i].name ? data[i].name : data[i].text;
                    }
                    params.page = params.page || 1;

                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
            },
            placeholder: 'Search by name',
            escapeMarkup: function(markup) {
                return markup;
            },
            minimumInputLength: 1,
            templateResult: function(customer) {

                if (customer.name) {
                    //return "<p> <b>Id:</b> " + customer.id + (customer.name ? " <b>Name:</b> " + customer.name : "") + (customer.phone ? " <b>Phone:</b> " + customer.phone : "") + "</p>";
                    return "<p>" + customer.name + "</p>";
                }
            },
            templateSelection: (customer) => customer.text || customer.name,

        });

        var vendorToRemind = null;
        $('#vendor-phone-number').select2({
            tags: true,
            width: '100%',
            ajax: {
                url: BASE_URL + '/vendor-search-phone',
                dataType: 'json',
                delay: 750,
                data: function(params) {
                    return {
                        q: params.term, // search term
                    };
                },
                processResults: function(data, params) {
                    for (var i in data) {
                        data[i].id = data[i].phone ? data[i].phone : data[i].text;
                    }
                    params.page = params.page || 1;

                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
            },
            placeholder: 'Search by phone number',
            escapeMarkup: function(markup) {
                return markup;
            },
            minimumInputLength: 1,
            templateResult: function(customer) {

                if (customer.name) {
                    return "<p style='color:#BABABA;'>" + customer.phone + "</p>";
                }
            },
            templateSelection: (customer) => customer.text || customer.phone,

        });

        $(document).on('click', '.emailToAllModal', function() {
            var select_vendor = [];
            $('.select_vendor').each(function() {
                if ($(this).prop("checked")) {
                    select_vendor.push($(this).val());
                }
            });

            if (select_vendor.length === 0) {
                alert('Please Select vendors!!');
                return false;
            }

            $('#emailToAllModal').find('form').find('input[name="vendor_ids"]').val(select_vendor.join());

            $('#emailToAllModal').modal("show");

        });

        $(document).on('click', '.send-email-to-vender', function() {
            $('#emailToAllModal').find('form').find('input[name="vendor_ids"]').val($(this).data('id'));
            $('#emailToAllModal').modal("show");
        });

        $(document).on('click', '.set-reminder', function() {
            let vendorId = $(this).data('id');
            let frequency = $(this).data('frequency');
            let message = $(this).data('reminder_message');
            let reminder_from = $(this).data('reminder_from');
            let reminder_last_reply = $(this).data('reminder_last_reply');

            $('#frequency').val(frequency);
            $('#reminder_message').val(message);
            $("#reminderModal").find("#reminder_from").val(reminder_from);
            if (reminder_last_reply == 1) {
                $("#reminderModal").find("#reminder_last_reply").prop("checked", true);
            } else {
                $("#reminderModal").find("#reminder_last_reply_no").prop("checked", true);
            }
            vendorToRemind = vendorId;
        });

        $(document).on('click', '.save-reminder', function() {
            var reminderModal = $("#reminderModal");
            let frequency = $('#frequency').val();
            let message = $('#reminder_message').val();
            let reminder_from = reminderModal.find("#reminder_from").val();
            let reminder_last_reply = (reminderModal.find('#reminder_last_reply').is(":checked")) ? 1 : 0;

            $.ajax({
                url: "{{ action([\App\Http\Controllers\VendorController::class, 'updateReminder']) }}",
                type: 'POST',
                success: function() {
                    toastr['success']('Reminder updated successfully!');
                },
                data: {
                    vendor_id: vendorToRemind,
                    frequency: frequency,
                    message: message,
                    reminder_from: reminder_from,
                    reminder_last_reply: reminder_last_reply,
                    _token: "{{ csrf_token() }}"
                }
            });
        });

        $(document).on('click', '.edit-vendor', function() {
            var vendor = $(this).data('vendor');
            var url = "{{ url('vendors') }}/" + vendor.id;

            $('#vendorEditModal form').attr('action', url);
            $('#vendor_category option[value="' + vendor.category_id + '"]').attr('selected', true);
            $('#vendor_name').val(vendor.name);
            $('#vendor_address').val(vendor.address);
            $('#vendor_phone').val(vendor.phone);
            $('#vendor_email').val(vendor.email);
            $('#vendor_social_handle').val(vendor.social_handle);
            $('#vendor_website').val(vendor.website);
            $('#vendor_login').val(vendor.login);
            $('#vendor_password').val(vendor.password);
            $('#vendor_gst').val(vendor.gst);
            $('#vendor_account_name').val(vendor.account_name);
            $('#vendor_account_iban').val(vendor.account_iban);
            $('#vendor_account_swift').val(vendor.account_swift);
        });

        $(document).on('click', '.create-agent', function() {
            var id = $(this).data('id');

            $('#agent_vendor_id').val(id);
        });

        $(document).on('click', '.edit-agent-button', function() {
            var agent = $(this).data('agent');
            var url = "{{ url('agent') }}/" + agent.id;

            $('#editAgentModal form').attr('action', url);
            $('#agent_name').val(agent.name);
            $('#agent_address').val(agent.address);
            $('#agent_phone').val(agent.phone);
            $('#agent_email').val(agent.email);
        });

        $(document).on('click', '.make-remark', function(e) {
            e.preventDefault();

            var id = $(this).data('id');
            $('#add-remark input[name="id"]').val(id);

            $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('task.gettaskremark') }}',
                data: {
                    id: id,
                    module_type: "vendor"
                },
            }).done(response => {
                var html = '';

                $.each(response, function(index, value) {
                    html += ' <p> ' + value.remark + ' <br> <small>By ' + value.user_name +
                        ' updated on ' + moment(value.created_at).format('DD-M H:mm') +
                        ' </small></p>';
                    html + "<hr>";
                });
                $("#makeRemarkModal").find('#remark-list').html(html);
            });
        });

        $('#addRemarkButton').on('click', function() {
            var id = $('#add-remark input[name="id"]').val();
            var remark = $('#add-remark').find('textarea[name="remark"]').val();

            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('task.addRemark') }}',
                data: {
                    id: id,
                    remark: remark,
                    module_type: 'vendor'
                },
            }).done(response => {
                $('#add-remark').find('textarea[name="remark"]').val('');

                var html = ' <p> ' + remark + ' <br> <small>By You updated on ' + moment().format(
                    'DD-M H:mm') + ' </small></p>';

                $("#makeRemarkModal").find('#remark-list').append(html);
            }).fail(function(response) {
                console.log(response);

                alert('Could not fetch remarks');
            });
        });

        $(document).on('click', '.expand-row', function() {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });

        $(document).on('click', '.load-email-modal', function() {
            var id = $(this).data('id');
            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('vendors.email') }}',
                data: {
                    id: id
                },
            }).done(function(response) {
                var html = '<div class="speech-wrapper">';
                response.forEach(function(message) {
                    var content = '';
                    content += 'To : ' + message.to + '<br>';
                    content += 'From : ' + message.from + '<br>';
                    if (message.cc) {
                        content += 'CC : ' + message.cc + '<br>';
                    }
                    if (message.bcc) {
                        content += 'BCC : ' + message.bcc + '<br>';
                    }
                    content += 'Subject : ' + message.subject + '<br>';
                    content += 'Message : ' + message.message + '<br>';
                    if (message.attachment.length) {
                        content += 'Attachment : ';
                    }
                    for (var i = 0; i < message.attachment.length; i++) {
                        var imageUrl = message.attachment[i];
                        imageUrl = imageUrl.trim();

                        // Set empty imgSrc
                        var imgSrc = '';

                        // Set image type
                        var imageType = imageUrl.substr(imageUrl.length - 4).toLowerCase();

                        // Set correct icon/image
                        if (imageType == '.jpg' || imageType == 'jpeg') {
                            imgSrc = imageUrl;
                        } else if (imageType == '.png') {
                            imgSrc = imageUrl;
                        } else if (imageType == '.gif') {
                            imgSrc = imageUrl;
                        } else if (imageType == 'docx' || imageType == '.doc') {
                            imgSrc = '/images/icon-word.svg';
                        } else if (imageType == '.xlsx' || imageType == '.xls' || imageType ==
                            '.csv') {
                            imgSrc = '/images/icon-excel.svg';
                        } else if (imageType == '.pdf') {
                            imgSrc = '/images/icon-pdf.svg';
                        } else if (imageType == '.zip' || imageType == '.tgz' || imageType ==
                            'r.gz') {
                            imgSrc = '/images/icon-zip.svg';
                        } else {
                            imgSrc = '/images/icon-file-unknown.svg';
                        }

                        // Set media
                        if (imgSrc != '') {
                            content += '<div class="col-4"><a href="' + message.attachment[i] +
                                '" target="_blank"><label class="label-attached-img" for="cb1_' +
                                i + '"><img src="' + imgSrc +
                                '" style="max-width: 100%;"></label></a></div>';
                        }
                    }
                    if (message.inout == 'in') {
                        html +=
                            '<div class="bubble"><div class="txt"><p class="name"></p><p class="message">' +
                            content + '</p><br/><span class="timestamp">' + message.created_at.date
                            .substr(0, 19) + '</span></div><div class="bubble-arrow"></div></div>';
                    } else if (message.inout == 'out') {
                        html +=
                            '<div class="bubble alt"><div class="txt"><p class="name alt"></p><p class="message">' +
                            content + '</p><br/><span class="timestamp">' + message.created_at.date
                            .substr(0, 19) +
                            '</span></div> <div class="bubble-arrow alt"></div></div>';
                    }
                });

                html += '</div>';

                $("#email-list-history").find(".modal-body").html(html);
                $("#email-list-history").modal("show");
            }).fail(function(response) {
                console.log(response);

                alert('Could not load email');
            });
        });

        $(document).on("keyup", '.search_email_pop', function() {
            var value = $(this).val().toLowerCase();
            $(".speech-wrapper .bubble").filter(function() {
                $(this).toggle($(this).find('.message').text().toLowerCase().indexOf(value) > -1)
            });
        });

        $(document).on('click', '.send-message', function() {
            var thiss = $(this);
            var data = new FormData();
            var vendor_id = $(this).data('vendorid');
            var message = $(this).siblings('input').val();

            data.append("vendor_id", vendor_id);
            data.append("message", message);
            data.append("status", 1);

            if (message.length > 0) {
                if (!$(this).is(':disabled')) {
                    $.ajax({
                        url: BASE_URL + '/whatsapp/sendMessage/vendor',
                        type: 'POST',
                        "dataType": 'json', // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function() {
                            $(this).attr('disabled', true);
                        }
                    }).done(function(response) {
                        this.closest('tr').find('.chat_messages').html(this.siblings('input').val());
                        $(this).siblings('input').val('');

                        $(this).attr('disabled', false);
                    }).fail(function(errObj) {
                        $(this).attr('disabled', false);

                        alert("Could not send message");
                        console.log(errObj);
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });

        $(document).on('click', '.send-message1', function() {
            var thiss = $(this);
            var data = new FormData();
            var vendor_id = $(this).data('vendorid');
            var message = $("#messageid_" + vendor_id).val();
            data.append("vendor_id", vendor_id);
            data.append("message", message);
            data.append("status", 1);

            if (message.length > 0) {
                if (!$(this).is(':disabled')) {
                    $.ajax({
                        url: BASE_URL + '/whatsapp/sendMessage/vendor',
                        type: 'POST',
                        "dataType": 'json', // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function() {
                            $(thiss).attr('disabled', true);
                        }
                    }).done(function(response) {
                        //thiss.closest('tr').find('.message-chat-txt').html(thiss.siblings('textarea').val());
                        $("#message-chat-txt-" + vendor_id).html(message);
                        $("#messageid_" + vendor_id).val('');

                        $(this).attr('disabled', false);
                    }).fail(function(errObj) {
                        $(this).attr('disabled', false);

                        alert("Could not send message");
                        console.log(errObj);
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });

        $(document).on('change', '.update-category-user', function() {
            let catId = $(this).attr('data-categoryId');
            let userId = $(this).val();

            $.ajax({
                url: '{{ action([\App\Http\Controllers\VendorController::class, 'assignUserToCategory']) }}',
                data: {
                    user_id: userId,
                    category_id: catId
                },
                success: function(response) {
                    toastr['success']('User assigned to category completely!')
                }
            });

        });

        $(document).on('click', '.add-cc', function(e) {
            e.preventDefault();

            if ($('#cc-label').is(':hidden')) {
                $('#cc-label').fadeIn();
            }

            var el = `<div class="row cc-input">
            <div class="col-md-10">
                <input type="text" name="cc[]" class="form-control mb-3">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-image cc-delete-button"><img src="/images/delete.png"></button>
            </div>
            </div>`;

            $('#cc-list').append(el);
        });

        $(document).on('click', '.cc-delete-button', function(e) {
            e.preventDefault();
            var parent = $(this).parent().parent();

            parent.hide(300, function() {
                parent.remove();
                var n = 0;

                $('.cc-input').each(function() {
                    n++;
                });

                if (n == 0) {
                    $('#cc-label').fadeOut();
                }
            });
        });

        // bcc

        $(document).on('click', '.add-bcc', function(e) {
            e.preventDefault();

            if ($('#bcc-label').is(':hidden')) {
                $('#bcc-label').fadeIn();
            }

            var el = `<div class="row bcc-input">
            <div class="col-md-10">
                <input type="text" name="bcc[]" class="form-control mb-3">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-image bcc-delete-button"><img src="/images/delete.png"></button>
            </div>
        </div>`;

            $('#bcc-list').append(el);
        });

        $(document).on('click', '.bcc-delete-button', function(e) {
            e.preventDefault();
            var parent = $(this).parent().parent();

            parent.hide(300, function() {
                parent.remove();
                var n = 0;

                $('.bcc-input').each(function() {
                    n++;
                });

                if (n == 0) {
                    $('#bcc-label').fadeOut();
                }
            });
        });

        $(document).on('click', '.block-twilio', function() {
            var vendor_id = $(this).data('id');
            var thiss = $(this);

            $.ajax({
                type: "POST",
                url: "{{ route('vendors.block') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    vendor_id: vendor_id
                },
                beforeSend: function() {
                    $(thiss).text('Blocking...');
                }
            }).done(function(response) {
                if (response.is_blocked == 1) {
                    $(thiss).html('<img src="/images/blocked-twilio.png" />');
                } else {
                    $(thiss).html('<img src="/images/unblocked-twilio.png" />');
                }
            }).fail(function(response) {
                $(thiss).html('<img src="/images/unblocked-twilio.png" />');

                alert('Could not block customer!');

                console.log(response);
            });
        });

        $(document).on('click', '.call-select', function() {
            var id = $(this).data('id');
            $('#show' + id).toggle();
            console.log('#show' + id);
        });

        $(document).ready(function() {
            src = "{{ route('vendors.index') }}";
            $(".search").autocomplete({
                source: function(request, response) {
                    id = $('#id').val();
                    name = $('#name').val();
                    email = $('#email').val();
                    phone = $('#phone').val();
                    address = $('#address').val();
                    category = $('#category').val();

                    $.ajax({
                        url: src,
                        dataType: "json",
                        data: {
                            id: id,
                            name: name,
                            phone: phone,
                            email: email,
                            address: address,
                            category: category,
                        },
                        beforeSend: function() {
                            $("#loading-image").show();
                        },

                    }).done(function(data) {
                        $("#loading-image").hide();
                        console.log(data);
                        $("#vendor-table tbody").empty().html(data.tbody);
                        if (data.links.length > 10) {
                            $('ul.pagination').replaceWith(data.links);
                        } else {
                            $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                        }
                        $(".select2-quick-reply").select2({});

                    }).fail(function(jqXHR, ajaxOptions, thrownError) {
                        alert('No response from server');
                    });
                },
                minLength: 1,

            });


            $(document).ready(function() {
                src = "{{ route('vendors.index') }}";
                $("#search_id").autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: src,
                            dataType: "json",
                            data: {
                                term: request.term
                            },
                            beforeSend: function() {
                                $("#loading-image").show();
                            },

                        }).done(function(data) {
                            $("#loading-image").hide();
                            $("#vendor-table tbody").empty().html(data.tbody);
                            if (data.links.length > 10) {
                                $('ul.pagination').replaceWith(data.links);
                            } else {
                                $('ul.pagination').replaceWith(
                                    '<ul class="pagination"></ul>');
                            }
                            $(".select2-quick-reply").select2({});

                        }).fail(function(jqXHR, ajaxOptions, thrownError) {
                            alert('No response from server');
                        });
                    },
                    minLength: 1,

                });
            });

            $(document).on("change", ".quickComment", function(e) {

                var message = $(this).val();

                if ($.isNumeric(message) == false) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        },
                        url: BASE_URL + "/vendors/reply/add",
                        dataType: "json",
                        method: "POST",
                        data: {
                            reply: message
                        }
                    }).done(function(data) {

                    }).fail(function(jqXHR, ajaxOptions, thrownError) {
                        alert('No response from server');
                    });
                }
                $(this).closest("td").find(".quick-message-field").val($(this).find("option:selected")
                    .text());

            });

            $(".select2-quick-reply").select2({
                tags: true
            });

            $(document).on("click", ".delete_quick_comment", function(e) {
                var deleteAuto = $(this).closest(".d-flex").find(".quickComment").find("option:selected")
                    .val();
                if (typeof deleteAuto != "undefined") {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        },
                        url: BASE_URL + "/vendors/reply/delete",
                        dataType: "json",
                        method: "GET",
                        data: {
                            id: deleteAuto
                        }
                    }).done(function(data) {
                        if (data.code == 200) {
                            $(".quickComment").empty();
                            $.each(data.data, function(k, v) {
                                $(".quickComment").append("<option value='" + k + "'>" + v +
                                    "</option>");
                            });
                            $(".quickComment").select2({
                                tags: true
                            });
                        }

                    }).fail(function(jqXHR, ajaxOptions, thrownError) {
                        alert('No response from server');
                    });
                }
            });
        });

        function createUserFromVendor(id, email) {
            $('#vendor_id').attr('data-id', id);
            if (email) {
                $('#createUser').attr('data-email', email);
            }
            $('#createUser').modal('show');
        }

        $('#createUser').on('hidden.bs.modal', function() {
            $('#createUser').removeAttr('data-email');
        })

        $(document).on("click", "#vendor_id", function() {
            $('#createUser').modal('hide');
            id = $(this).attr('data-id');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: BASE_URL + "/vendors/create-user",
                dataType: "json",
                method: "POST",
                data: {
                    id: id
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
            }).done(function(data) {
                $("#loading-image").hide();
                if (data.code == 200) {
                    alert(data.data);
                }

            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                $("#loading-image").hide();
                alert('No response from server');
            });
        });

        function inviteGithub() {
            $('#createUser').modal('hide');
            const email = $('#createUser').attr('data-email');

            $.ajax({
                    type: "POST",
                    url: "/vendors/inviteGithub",
                    data: {
                        _token: "{{ csrf_token() }}",
                        email
                    }
                })
                .done(function(data) {
                    alert(data.message);
                })
                .fail(function(error) {
                    alert(error.responseJSON.message);
                });

            console.log(email);
        }

        function inviteHubstaff() {
            $('#createUser').modal('hide');
            const email = $('#createUser').attr('data-email');
            console.log(email);

            $.ajax({
                    type: "POST",
                    url: BASE_URL + "/vendors/inviteHubstaff",
                    data: {
                        _token: "{{ csrf_token() }}",
                        email
                    }
                })
                .done(function(data) {
                    alert(data.message);
                })
                .fail(function(error) {
                    alert(error.responseJSON.message);
                })
        }

        $('#reminder_from').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });

        $(document).on("change", ".vendor-update-status", function() {
            var $this = $(this);
            $.ajax({
                type: "POST",
                url: BASE_URL + "vendors/change-status",
                data: {
                    _token: "{{ csrf_token() }}",
                    vendor_id: $this.data("id"),
                    status: $this.prop('checked')
                }
            }).done(function(data) {
                if (data.code == 200) {
                    toastr["success"](data.message);
                }
            }).fail(function(error) {

            })
        });
        $(document).on("click", ".vendor-update-status-icon", function() {
            var $this = $(this);
            var vendor_id = $(this).attr("data-id");
            var hdn_vendorstatus = $("#hdn_vendorstatus_" + vendor_id).val();
            $.ajax({
                type: "POST",
                url: BASE_URL + "vendors/change-status",
                data: {
                    _token: "{{ csrf_token() }}",
                    vendor_id: $this.data("id"),
                    status: hdn_vendorstatus
                }
            }).done(function(data) {
                if (data.code == 200) {
                    //toastr["success"](data.message);
                    if (hdn_vendorstatus == "true") {
                        var img_url = BASE_URL + 'images/do-disturb.png';
                        $("#btn_vendorstatus_" + vendor_id).html('<img src="' + img_url + '" />');
                        $("#btn_vendorstatus_" + vendor_id).attr("title", "On");
                        $("#hdn_vendorstatus_" + vendor_id).val('false');
                    } else {
                        var img_url = BASE_URL + 'images/do-not-disturb.png';
                        $("#btn_vendorstatus_" + vendor_id).html('<img src="' + img_url + '" />');
                        $("#btn_vendorstatus_" + vendor_id).attr("title", "Off");
                        $("#hdn_vendorstatus_" + vendor_id).val('true');
                    }

                }
            }).fail(function(error) {

            })
        });

        $('ul.pagination').show();
        $('.infinite-scroll').jscroll({
            autoTrigger: true,
            // debug: true,
            loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
            padding: 20,
            float: 'right',
            nextSelector: '.pagination li.active + li a',
            contentSelector: 'div.infinite-scroll',
            callback: function() {
                $('ul.pagination').first().remove();
                $('ul.pagination').show();
            }
        });

        $(document).on('click', '.create_broadcast', function() {
            var vendors = [];
            $(".select_vendor").each(function() {
                if ($(this).prop("checked") == true) {
                    vendors.push($(this).val());
                }
            });
            if (vendors.length == 0) {
                alert('Please select vendor');
                return false;
            }
            $("#create_broadcast").modal("show");
        });

        $("#send_message").submit(function(e) {
            e.preventDefault();
            var vendors = [];
            $(".select_vendor").each(function() {
                if ($(this).prop("checked") == true) {
                    vendors.push($(this).val());
                }
            });
            if (vendors.length == 0) {
                alert('Please select vendor');
                return false;
            }

            if ($("#send_message").find("#message_to_all_field").val() == "") {
                alert('Please type message ');
                return false;
            }

            $.ajax({
                type: "POST",
                url: "{{ route('vendors/send/message') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    message: $("#send_message").find("#message_to_all_field").val(),
                    vendors: vendors
                }
            }).done(function() {
                window.location.reload();
            }).fail(function(response) {
                $(thiss).text('No');

                alert('Could not say No!');
                console.log(response);
            });
        });

        function sendImage(id) {
            $.ajax({
                url: "{{ action([\App\Http\Controllers\WhatsAppController::class, 'sendMessage'], 'issue') }}",
                type: 'POST',
                data: {
                    issue_id: id,
                    type: 1,
                    message: '',
                    _token: "{{ csrf_token() }}",
                    status: 2
                },
                success: function() {
                    toastr["success"]("Message sent successfully!", "Message");

                },
                beforeSend: function() {
                    $(self).attr('disabled', true);
                },
                error: function() {
                    alert('There was an error sending the message...');
                    $(self).removeAttr('disabled', true);
                }
            });

        }

        function sendUploadImage(id) {

            $('#file-input' + id).trigger('click');

            $('#file-input' + id).change(function() {
                event.preventDefault();
                let image_upload = new FormData();
                let TotalImages = $(this)[0].files.length; //Total Images
                let images = $(this)[0];

                for (let i = 0; i < TotalImages; i++) {
                    image_upload.append('images[]', images.files[i]);
                }
                image_upload.append('TotalImages', TotalImages);
                image_upload.append('status', 2);
                image_upload.append('type', 2);
                image_upload.append('issue_id', id);
                if (TotalImages != 0) {

                    $.ajax({
                        method: 'POST',
                        url: "{{ action([\App\Http\Controllers\WhatsAppController::class, 'sendMessage'], 'issue') }}",
                        data: image_upload,
                        async: true,
                        contentType: false,
                        processData: false,
                        beforeSend: function() {
                            $("#loading-image").show();
                        },
                        success: function(images) {
                            $("#loading-image").hide();
                            alert('Images send successfully');
                        },
                        error: function() {
                            console.log(`Failed`)
                        }
                    })
                }
            })
        }

        // $('#filecount').filestyle({htmlIcon: '<span class="oi oi-random"></span>',badge: true, badgeName: "badge-danger"});

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
                url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'uploadDocument']) }}",
                data: postData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function(response) {
                    if (response.code == 200) {
                        toastr["success"]("Status updated!", "Message")
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
                url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'getDocument']) }}",
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
                }
            });
        });



        $(document).on('click', '.send-message-open', function(event) {
            var textBox = $(this).closest(".communication-td").find(".send-message-textbox");
            var sendToStr = $(this).closest(".communication-td").next().find(".send-message-number").val();
            let issueId = textBox.attr('data-id');
            let message = textBox.val();
            if (message == '') {
                return;
            }

            let self = textBox;

            $.ajax({
                url: "{{ action([\App\Http\Controllers\WhatsAppController::class, 'sendMessage'], 'issue') }}",
                type: 'POST',
                data: {
                    "issue_id": issueId,
                    "message": message,
                    "sendTo": sendToStr,
                    "_token": "{{ csrf_token() }}",
                    "status": 2
                },
                dataType: "json",
                success: function(response) {
                    toastr["success"]("Message sent successfully!", "Message");
                    $('#message_list_' + issueId).append('<li>' + response.message.created_at + " : " +
                        response.message.message + '</li>');
                    $(self).removeAttr('disabled');
                    $(self).val('');
                },
                beforeSend: function() {
                    $(self).attr('disabled', true);
                },
                error: function() {
                    alert('There was an error sending the message...');
                    $(self).removeAttr('disabled', true);
                }
            });
        });

        //Popup for add new task
        $(document).on('click', '#newTaskModalBtn', function() {
            if ($("#newTaskModal").length > 0) {
                $("#newTaskModal").remove();
            }

            $.ajax({
                url: "{{ action([\App\Http\Controllers\DevelopmentController::class, 'openNewTaskPopup']) }}",
                type: 'GET',
                dataType: "JSON",
                success: function(resp) {
                    console.log(resp);
                    if (resp.status == 'ok') {
                        $("body").append(resp.html);
                        $('#newTaskModal').modal('show');
                        $('select.select2').select2({
                            tags: true
                        });
                    }
                }
            });
        });
    </script>

    <script>
        $(document).on('change', '.assign-task-user', function() {
            let id = $(this).attr('data-id');
            let userId = $(this).val();
            if (userId == '') {
                return;
            }
            $.ajax({
                url: "{{ route('task.AssignTaskToUser') }}",
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

        $(document).on('click', '.flag-task', function() {
            var task_id = $(this).data('id');
            var task_type = $(this).data('type');
            var thiss = $(this);

            $.ajax({
                type: "POST",
                url: "{{ route('task.flag') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    task_id: task_id,
                    task_type: task_type
                },
                beforeSend: function() {
                    $(thiss).text('Flagging...');
                }
            }).done(function(response) {
                if (response.is_flagged == 1) {
                    // var badge = $('<span class="badge badge-secondary">Flagged</span>');
                    //
                    // $(thiss).parent().append(badge);
                    $(thiss).html('<img src="/images/flagged.png" />');
                } else {
                    $(thiss).html('<img src="/images/unflagged.png" />');
                    // $(thiss).parent().find('.badge').remove();
                }

                // $(thiss).remove();
            }).fail(function(response) {
                $(thiss).html('<img src="/images/unflagged.png" />');

                alert('Could not flag task!');

                console.log(response);
            });
        });

        //START - Purpose : Remind , Revise button Events - DEVTASK-4354
        $(document).on('click', '.remind_btn', function() {
            var issueId = $('#approve-time-btn input[name="developer_task_id"]').val();
            var userId = $('#approve-time-btn input[name="user_id"]').val();

            $('#time_history_div table tbody').html('');
            $.ajax({
                url: "{{ route('task.time.history.approve.sendRemindMessage') }}",
                type: 'POST',
                data: {
                    id: issueId,
                    user_id: userId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    toastr['success'](data.message, 'success');
                }
            });
            $('#time_history_modal').modal('hide');
        });

        $(document).on('click', '.revise_btn', function() {
            var issueId = $('#approve-time-btn input[name="developer_task_id"]').val();
            var userId = $('#approve-time-btn input[name="user_id"]').val();

            $('#time_history_div table tbody').html('');
            $.ajax({
                url: "{{ route('task.time.history.approve.sendMessage') }}",
                type: 'POST',
                data: {
                    id: issueId,
                    user_id: userId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    toastr['success'](data.message, 'success');
                }
            });
            $('#time_history_modal').modal('hide');
        });
        //END - DEVTASK-4354

        $(document).on('change', '.change-task-status', function() {

            let id = $(this).attr('data-id');
            let status = $(this).val();

            $.ajax({
                url: "{{ route('task.change.status') }}",
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

        function checkAll(ele) {
            var checkboxes = document.getElementsByClassName('rowCheckbox');
            if (ele.checked) {
                for (var i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].type == 'checkbox') {
                        checkboxes[i].checked = true;
                    }
                }
            } else {
                for (var i = 0; i < checkboxes.length; i++) {
                    console.log(i)
                    if (checkboxes[i].type == 'checkbox') {
                        checkboxes[i].checked = false;
                    }
                }
            }
        }

        var expanded = false;

        function showSelectCheckboxes() {
            var checkboxes = document.getElementById("checkboxes");
            if (!expanded) {
                checkboxes.style.display = "block";
                expanded = true;
            } else {
                checkboxes.style.display = "none";
                expanded = false;
            }
        }

        $("#select_all").click(function() {
            $(".devCheckbox").prop('checked', $(this).prop('checked'));
        });

        $(".devCheckbox").change(function() {
            if ($('.devCheckbox:checked').length == $('.devCheckbox').length) {
                $('#select_all').prop('checked', true);
            } else {
                $('#select_all').prop('checked', false);
            }
        });
    </script>

    <script>
        $(document).on('click', '.show-time-history-task', function() {
            var data = $(this).attr('data-history');
            var userId = $(this).attr('data-user_id');
            var issueId = $(this).attr('data-id');
            $('#time_history_div table tbody').html('');

            //START - Purpose : Display Hide Remind, Revise Button - DEVTASK-4354
            const hasText = $(this).siblings('input').val();

            if (!hasText || hasText == 0) {
                $('#time_history_modal .revise_btn').prop('disabled', true);
                $('#time_history_modal .remind_btn').prop('disabled', false);
            } else {
                $('#time_history_modal .revise_btn').prop('disabled', false);
                $('#time_history_modal .remind_btn').prop('disabled', true);
            }
            //END - DEVTASK-4354

            $.ajax({
                url: "{{ route('task.time.history') }}",
                data: {
                    id: issueId
                },
                success: function(data) {
                    // if(data != 'error') {
                    //     $.each(data, function(i, item) {
                    //         $('#time_history_div table tbody').append(
                    //             '<tr>\
                    //                 <td>'+ moment(item['created_at']).format('DD/MM/YYYY') +'</td>\
                    //                 <td>'+ ((item['old_value'] != null) ? item['old_value'] : '-') +'</td>\
                    //                 <td>'+item['new_value']+'</td>\
                    //             </tr>'
                    //         );
                    //     });
                    // }

                    if (data != 'error') {
                        $('input[name="developer_task_id"]').val(issueId);
                        $.each(data, function(i, item) {
                            if (item['is_approved'] == 1) {
                                var checked = 'checked';
                            } else {
                                var checked = '';
                            }
                            $('#time_history_div table tbody').append(
                                `<tr> 
                                    <td>${ moment(item['created_at']).format('DD/MM/YYYY') }</td> 
                                    <td>${ ((item['old_value'] != null) ? item['old_value'] : '-') }</td> 
                                    <td>${ item['new_value'] }</td><td>${ item['name'] } </td> 
                                    <td><input type="radio" name="approve_time" value="${ item['id'] }" ${ checked } class="approve_time"/></td> 
                                </tr>`
                            );
                        });

                        $('#time_history_div table tbody').append(
                            '<input type="hidden" name="user_id" value="' + userId + '" class=" "/>'
                        );
                    }
                }
            });
            $('#time_history_modal').modal('show');
        });

        $(document).on('click', '.show-status-history', function() {
            var data = $(this).data('history');
            var issueId = $(this).data('id');
            var type = $(this).data('type');
            $('#status_quick_history_modal table tbody').html('');
            $.ajax({
                url: "{{ route('development/status/history') }}",
                data: {
                    id: issueId,
                    type: type
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
                                    <td>' + ((item['old_value'] != null) ? item['old_value'] : '-') + '</td>\
                                    <td>' + item['new_value'] + '</td>\
                                    <td>' + item['name'] + '</td>\
                                </tr>'
                            );
                        });
                    }
                }
            });
            $('#status_quick_history_modal').modal('show');
        });

        $(document).on('click', '.show-user-history', function() {
            var issueId = $(this).data('id');
            var type = $(this).data('type');
            $('#user_history_div table tbody').html('');
            $.ajax({
                url: "{{ route('task/user/history') }}",
                data: {
                    id: issueId,
                    type: type
                },
                success: function(data) {
                    $.each(data.users, function(i, item) {
                        $('#user_history_div table tbody').append(
                            `<tr> 
                                <td>${ moment(item['created_at']).format('DD/MM/YYYY') }</td>
                                <td>${ ((item['user_type'] != null) ? item['user_type'] : '-') }</td>
                                <td>${ ((item['old_name'] != null) ? item['old_name'] : '-') }</td>
                                <td>${ ((item['new_name'] != null) ? item['new_name'] : '-') }</td>
                                <td>${ item['updated_by'] }</td>
                            </tr>`
                        );
                    });
                }
            });
            $('#user_history_modal').modal('show');
        });

        $(document).on('click', '.show-date-history', function() {
            var data = $(this).data('history');
            var type = $(this).data('type');

            var issueId = $(this).data('id');
            $('#date_history_modal table tbody').html('');
            $.ajax({
                url: "{{ route('development/date/history') }}",
                data: {
                    id: issueId,
                    type: type
                },
                success: function(data) {
                    console.log(data);
                    if (data != 'error') {
                        $("#developer_task_id").val(issueId);
                        $.each(data, function(i, item) {
                            if (item['is_approved'] == 1) {
                                var checked = 'checked';
                            } else {
                                var checked = '';
                            }
                            $('#date_history_modal table tbody').append(
                                `<tr>
                                    <td>${moment(item['created_at']).format('DD/MM/YYYY') }</td>
                                    <td>${((item['old_value'] != null) ? item['old_value'] : '-') }</td>
                                    <td>${item['new_value'] }</td><td>${item['name']}</td>
                                    <td><input type="radio" name="approve_date" value="${item['id'] }" ${checked } class="approve_date"/></td>
                                 </tr>`
                            );
                        });
                    }
                }
            });
            $('#date_history_modal').modal('show');
        });

        $(document).on('click', '.show-tracked-history', function() {
            var issueId = $(this).data('id');
            var type = $(this).data('type');
            $('#time_tracked_div table tbody').html('');
            $.ajax({
                url: "{{ route('development/tracked/history') }}",
                data: {
                    id: issueId,
                    type: type
                },
                success: function(data) {
                    if (data != 'error') {
                        $.each(data.histories, function(i, item) {
                            var sec = parseInt(item['total_tracked']);
                            $('#time_tracked_div table tbody').append(`<tr>
                                    <td> ${ moment(item['created_at']).format('DD-MM-YYYY') }</td>
                                    <td> ${ ((item['name'] != null) ? item['name'] : '') }</td>
                                    <td> ${ humanizeDuration(sec, 's') }</td>
                                </tr>`);
                        });
                    }
                }
            });
            $('#time_tracked_modal').modal('show');
        });

        $(document).on('click', '.show-tracked-history_task', function() {
            var issueId = $(this).data('id');
            var type = $(this).data('type');
            $('#time_tracked_div table tbody').html('');
            $.ajax({
                url: "{{ route('task.time.tracked.history') }}",
                data: {
                    id: issueId,
                    type: type
                },
                success: function(data) {
                    console.log(data);
                    if (data != 'error') {
                        $.each(data.histories, function(i, item) {
                            var sec = parseInt(item['total_tracked']);
                            $('#time_tracked_div table tbody').append(
                                `<tr>
                                    <td>${ moment(item['starts_at_date']).format('DD-MM-YYYY') }</td>
                                    <td>${ ((item['name'] != null) ? item['name'] : '') }</td>
                                    <td>${ humanizeDuration(sec, 's') }</td>
                                </tr>`
                            );
                        });
                    }
                }
            });
            $('#time_tracked_modal').modal('show');
        });
    </script>
@endsection
