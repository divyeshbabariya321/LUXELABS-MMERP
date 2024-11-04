@extends('layouts.app')

@section('title', 'Daily Planner')

@section('styles')

    <style>
        .width_input {
            width: 200px !important;
        }

        .width_select {
            width: 235px !important;
        }
    </style>

@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Daily Planner - {{ $planned_at }}</h2>
            <div class="pull-left">
                @if (Auth::user()->hasRole('Admin'))
                    <form action="{{ route('dailyplanner.index') }}" class="form-inline" method="POST">
                        @csrf
                        <div class="form-group mr-3">
                            <select class="form-control input-sm ml-3 width_select" name="user_id">
                                <option value="">Select a User</option>
                                @foreach ($users_array as $id => $user)
                                    <option value="{{ $id }}"
                                        {{ isset($userid) && $id == $userid ? 'selected' : '' }}>{{ $user }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group ml-3">
                            <div class='input-group date' id='planned-datetime'>

                                <input type='text' class="form-control input-sm width_input" name="planned_at"
                                    value="{{ $planned_at }}">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-image"><img src="{{ asset('images/filter.png') }}" /></button>
                    </form>
                @endif
            </div>
            <form action="{{ route('dailyplanner.send.vendor.schedule') }}" class="form-inline" method="post">
                <div class="form-group mr-3">
                    @csrf
                    <select class="form-control input-sm ml-3" name="user">
                        <option value="">Select a User</option>
                        @foreach ($users_array as $id => $user)
                            <option value="{{ $id }}">{{ $user }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group ml-3">
                    <div class='input-group date'>
                        <input type='text' class="form-control input-sm width_input" id="send-planned-datetime"
                            name="date" value="">
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
                <div class="form-group ml-3">
                    <button type="submit" class="btn btn-md btn-secondary"> Send schedule </button>
                </div>
                <div class="form-group ml-3">
                    <button type="button" class="btn btn-md btn-secondary" id="showMeetingsButton">Show Meetings</button>
                </div>
                <div class="form-group ml-3">
                    <button type="button" data-toggle="collapse" href="#inProgressFilterCount"
                        class="btn btn-md btn-secondary" id="showMeetingsButton">Spent Time</button>
                </div>
            </form>


        </div>
    </div>

    @include('partials.flash_messages')

    @if (!empty($spentTime))
        <div class="collapse" id="inProgressFilterCount">
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card card-body">
                        <div class="row col-md-12">
                            @foreach ($spentTime as $key => $value)
                                <div class="col-md-2">
                                    <div class="card">
                                        <div class="card-header">
                                            {{ $generalCategories[$key] ?? 'N/A' }}
                                        </div>
                                        <div class="card-body">
                                            {{ $value }} Minutes
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row no-gutters ">
        <div class="col-xs-12 col-md-12 p-5" id="plannerColumn">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th width="10%">Time</th>
                            <th width="25%">Planned</th>
                            <th width="5%">Timezone</th>
                            <th width="10%">Time</th>
                            <th width="10%">Actual Start Time</th>
                            <th width="13%">Actual Complete Time</th>
                            <th width="8%">Remark</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php $count = 0; @endphp
                        @foreach ($time_slots as $time_slot => $data)
                            @if (count($data) > 0)
                                @foreach ($data as $key => $task)
                                    <tr class="{{ $key <= 3 ? '' : "hidden hiddentask$count" }}">
                                        <td class="p-2">
                                            @if ($key == 0)
                                                {{ $time_slot }} <a href="javascript:;" class="show-timer-div"> + </a>
                                            @endif
                                        </td>
                                        <td class="p-2">
                                            <div class="d-flex justify-content-between">
                                                <span>
                                                    @if ($task->activity == '')
                                                        {{ $task->task_subject ?? substr($task->task_details, 0, 20) }}
                                                    @else
                                                        [{{ $task->generalCategory ? $task->generalCategory->name : 'N/A' }}]
                                                        {{ $task->activity }}
                                                    @endif

                                                    @if ($task->pending_for != 0)
                                                        - pending for {{ $task->pending_for }} days
                                                    @endif
                                                </span>

                                            </div>
                                        </td>
                                        <td class="p-2 ">{{ $task->timezone }} </td>
                                        <td class="p-2 ">
                                            {{ changeTimeZone($task->for_datetime, null, $task->timezone) }}</td>
                                        <td class="p-2 task-start-time">
                                            {{ $task->actual_start_date != '0000-00-00 00:00:00' ? \Carbon\Carbon::parse($task->actual_start_date)->format('d-m H:i') : '' }}
                                        </td>
                                        <td class="p-2 task-time">
                                            {{ $task->is_completed ? \Carbon\Carbon::parse($task->is_completed)->format('d-m H:i') : '' }}
                                        </td>
                                        <td class="expand-row table-hover-cell p-2">
                                            <span class="td-mini-container">
                                                {{ $task->remarks()->count() ? $task->remarks()->first()->remark : '' }}
                                            </span>

                                            <span class="td-full-container hidden">
                                                <ul>
                                                    @if ($task->remarks()->count())
                                                        @foreach ($task->remarks as $remark)
                                                            <li>{{ $remark->remark }} on
                                                                {{ \Carbon\Carbon::parse($remark->created_at)->format('d-m H:i') }}
                                                            </li>
                                                        @endforeach
                                                    @endif
                                                </ul>

                                                <span class="d-flex">
                                                    <input type="text" class="form-control input-sm quick-remark-input"
                                                        name="remark" placeholder="Remark" value="">
                                                    <button type="button" class="btn btn-image quick-remark-button"
                                                        data-id="{{ $task->id }}"><img
                                                            src="/images/filled-sent.png" /></button>
                                                </span>
                                            </span>
                                        </td>
                                        <td class="p-2">
                                            @if (
                                                (is_null($task->is_completed) || $task->is_completed == '') &&
                                                    $task->id != '' &&
                                                    (is_null($task->actual_start_date) || $task->actual_start_date == '0000-00-00 00:00:00'))
                                                <button type="button" class="btn btn-image task-actual-start p-0 m-0"
                                                    data-id="{{ $task->id }}"
                                                    data-type="{{ $task->activity != '' ? 'activity' : 'task' }}"><img
                                                        src="/images/youtube_128.png" /></button>
                                            @elseif(is_null($task->is_completed) || ($task->is_completed == '' && $task->id != ''))
                                                <button type="button" class="btn btn-image task-complete p-0 m-0"
                                                    data-id="{{ $task->id }}"
                                                    data-type="{{ $task->activity != '' ? 'activity' : 'task' }}"><img
                                                        src="/images/incomplete.png" /></button>
                                            @endif
                                            @if ($task->id != '')
                                                <button type="button" class="btn btn-image task-reschedule p-0 m-0"
                                                    title="reschedule" data-task="{{ $task }}"
                                                    data-id="{{ $task->id }}"
                                                    data-type="{{ $task->activity != '' ? 'activity' : 'task' }}">
                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                </button>
                                            @endif
                                            @if ($key == 3)
                                                <button type="button" class="btn btn-image show-tasks p-0 m-0"
                                                    data-count="{{ $count }}"
                                                    data-rowspan="{{ count($data) + 2 }}">v</button>
                                            @endif
                                            @if ($task->status != 'stop')
                                                <button type="button" class="btn btn-image task-stop p-0 m-0"
                                                    data-id="{{ $task->id }}" title="Stop"> <i
                                                        class="fa fa-stop"></i> </button>
                                            @endif
                                            <button type="button" class="btn btn-image task-resend p-0 m-0"
                                                data-id="{{ $task->id }}" title="Resend"> <i class="fa fa-send"></i>
                                            </button>
                                            <button type="button" class="btn btn-image task-history p-0 m-0"
                                                data-id="{{ $task->id }}" title="History"> <i
                                                    class="fa fa-history"></i> </button>
                                            @php
                                                $edit = \App\Helpers\EventHelper::getUserEventByDailyActivityID(
                                                    $task->id,
                                                );
                                                $vendor = [];
                                                if ($edit) {
                                                    $vendor = \App\Helpers\EventHelper::getUserEventParticipantByUserEventyID(
                                                        $edit->id,
                                                    );
                                                }
                                            @endphp
                                            <button type="button" class="btn btn-image payment-model-op p-0 m-0"
                                                data-note="{{ $task->activity }}"
                                                data-vendor="{{ json_encode($vendor) }}"
                                                data-date="{{ $task->for_date }}" data-id="{{ $task->id }}"
                                                title="Payment"> <i class="fa fa-money"></i> </button>
                                            @if (!empty($task->id))
                                                <a href="{{ route('calendar.event.edit', $task->id) }}"
                                                    class="btn btn-image p-0 m-0" title="Edit"> <i
                                                        class="fa fa-edit"></i> </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="p-2">{{ $time_slot }} <a href="javascript:;"
                                            class="show-timer-div"> + </a></td>
                                    <td class="p-2"></td>
                                    <td class="p-2 task-start"></td>
                                    <td class="p-2 task-complete"></td>
                                    <td class="p-2"></td>
                                    <td class="p-2"></td>
                                    <td class="p-2"></td>
                                    <td class="p-2"></td>
                                </tr>
                            @endif
                            <tr class="dis-none create-input-table">
                                <td class="p-2"></td>
                                <td class="p-2">
                                    <div class="d-flex" style="vert">
                                        {{ html()->select('general_category_id', [null => '-- Select Category --'] + $generalCategories, isset($task['general_category_id']) ? $task['general_category_id'] : null)->class('form-control  select2-selection--single general_category_id')->style('width:100%; ') }}
                                        &nbsp;&nbsp;
                                        <select
                                            class="selectpicker select2-selection--single form-control input-sm plan-task"
                                            data-live-search="true" data-size="15" name="task" title="Select a Task"
                                            data-timeslot="{{ $time_slot }}"
                                            data-targetid="timeslot{{ $count }}">
                                            @foreach ($tasks as $task)
                                                <option
                                                    data-tokens="{{ $task['id'] }} {{ $task['task_subject'] }} {{ $task['task_details'] }}"
                                                    value="{{ $task['id'] }}">#{{ $task['id'] }}
                                                    {{ $task['task_subject'] }} -
                                                    {{ substr($task['task_details'], 0, 20) }}</option>
                                            @endforeach
                                        </select>
                                        &nbsp;&nbsp;
                                        <input type="text"
                                            class="form-control select2-selection--single input-sm quick-plan-input"
                                            name="task" placeholder="New Plan" data-timeslot="{{ $time_slot }}"
                                            data-targetid="timeslot{{ $count }}" value="">
                                        &nbsp;&nbsp;
                                        <button type="button" class="btn btn-image quick-plan-button"
                                            data-timeslot="{{ $time_slot }}"
                                            data-targetid="timeslot{{ $count }}"><img
                                                src="/images/filled-sent.png" /></button>
                                    </div>
                                </td>
                                <td class="p-2"></td>
                                <td class="p-2"></td>
                                <td class="p-2"></td>
                                <td class="p-2"></td>
                            </tr>

                            @php $count++; @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-xs-12 col-md-4 hidden" id="meetingsColumn">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="3">Meeting & Call</th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th>Time</th>
                            <th>Details</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($meetings as $key => $instruction)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <h3 class="btn @if (Carbon\Carbon::now()->toDateTimeString() <= \Carbon\Carbon::parse($instruction->start)->toDateTimeString()) btn-secondary @endif btn-xs"
                                        onclick="sendImage(22294 )">
                                        {{ \Carbon\Carbon::parse($instruction->start)->format('d-m-Y H:i') }} -
                                        {{ \Carbon\Carbon::parse($instruction->end)->format('d-m-Y H:i') }}</h3>
                                </td>
                                <td>Subject : {{ $instruction->subject }} <br> Description :
                                    {{ $instruction->description }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col text-center">
            <form action="{{ route('dailyplanner.complete') }}" method="POST">
                @csrf

                <button type="submit" class="btn btn-xs btn-secondary">Complete Planner</button>
            </form>
        </div>
    </div>

    @include('partials.modals.reschedule-dailyplanner')
    @include('partials.modals.history-dailyplanner')

@endsection


@section('scripts')

    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script type="text/javascript">

        $(document).ready(function() {
            $(document).on('click', '.payment-model-op', function(e) {
                e.preventDefault();
                var thiss = $(this);
                var type = 'GET';
                $.ajax({
                    url: '/voucher/manual-payment',
                    type: type,
                    data: {
                        date: thiss.data('date'),
                        note: thiss.data('note'),
                        vendor: thiss.data('vendor')
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                    }
                }).done(function(response) {
                    $("#loading-image").hide();
                    $("#modal-container").load("/create-manual-payment", function() {
                        $("#create-manual-payment").modal('show');
                        $("#create-manual-payment-content").html(response);
                    });

                    $('#date_of_payment').datetimepicker({
                        format: 'YYYY-MM-DD'
                    });
                    $('.select-multiple').select2({
                        width: '100%'
                    });

                    $(".currency-select2").select2({
                        width: '100%',
                        tags: true
                    });
                    $(".payment-method-select2").select2({
                        width: '100%',
                        tags: true
                    });

                }).fail(function(errObj) {
                    $("#loading-image").hide();
                });
            });

            $(document).ready(function() {
                $('#planned-datetime').datetimepicker({
                    format: 'YYYY-MM-DD'
                });
                $('#send-planned-datetime').datetimepicker({
                    format: 'YYYY-MM-DD'
                });

                $('#reschedule-planned-datetime').datetimepicker({
                    format: 'YYYY-MM-DD'
                });
                $(".general_category_id").select2({
                    tags: true
                });
            });

            $(document).on('change', '.plan-task', function() {
                var time_slot = $(this).data('timeslot');
                var id = $(this).val();
                var thiss = $(this);
                var target_id = $(this).data('targetid');
                var generalCat = thiss.closest("td").find(".general_category_id").val();

                if (id != '') {
                    $.ajax({
                        type: "POST",
                        url: "{{ url('task') }}/" + id + '/plan',
                        data: {
                            _token: "{{ csrf_token() }}",
                            time_slot: time_slot,
                            planned_at: "{{ $planned_at }}",
                            general_category_id: generalCat
                        }
                    }).done(function(response) {
                        $(thiss).closest('tr').before(row);
                    }).fail(function(response) {
                        alert('Could not plan a task');
                    });
                }
            });

            // $(document).on('click', '.make-remark', function(e) {
            //   e.preventDefault();
            //
            //   var id = $(this).data('id');
            //   $('#add-remark input[name="id"]').val(id);
            //
            //   $.ajax({
            //       type: 'GET',
            //       headers: {
            //           'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            //       },
            //       url: '{{ route('task.gettaskremark') }}',
            //       data: {
            //         id:id,
            //         module_type: "task"
            //       },
            //   }).done(response => {
            //       var html='';
            //
            //       $.each(response, function( index, value ) {
            //         html+=' <p> '+value.remark+' <br> <small>By ' + value.user_name + ' updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
            //         html+"<hr>";
            //       });
            //       $("#makeRemarkModal").find('#remark-list').html(html);
            //   });
            // });

            $(document).on('click', '.quick-remark-button', function(e) {
                e.stopPropagation();

                var id = $(this).data('id');
                var thiss = $(this);
                var remark = $(this).siblings('input[name="remark"]').val();

                $.ajax({
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: '{{ route('task.addRemark') }}',
                    data: {
                        id: id,
                        remark: remark,
                        module_type: 'task'
                    },
                }).done(response => {
                    $(thiss).siblings('input[name="remark"]').val('');

                    var html = ' <li> ' + remark + ' <br> <small>By You updated on ' + moment()
                        .format(
                            'DD-M H:mm') + ' </small></li>';

                    $(thiss).closest('.td-full-container').find('ul').prepend(html);
                }).fail(function(response) {

                    alert('Could not fetch remarks');
                });
            });

            $(document).on('click', '.task-complete', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var thiss = $(this);
                var task_id = $(thiss).data('id');
                var image = $(this).html();
                var current_user = {{ Auth::id() }};
                var type = $(this).data('type');

                if (type == 'activity') {
                    var url = "/dailyActivity/complete/" + task_id;
                } else {
                    var url = "/task/complete/" + task_id;
                }

                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        type: "GET",
                        url: url,
                        data: {
                            type: 'complete'
                        },
                        beforeSend: function() {
                            $(thiss).text('Completing...');
                        }
                    }).done(function(response) {
                        // $(thiss).parent()
                        $(thiss).closest('tr').find('.task-time').text(moment().format(
                            'DD-MM HH:mm'));
                        $(thiss).remove();
                    }).fail(function(response) {
                        $(thiss).html(image);

                        alert('Could not mark as completed!');

                    });
                }
            });

            $(document).on('click', '.task-actual-start', function(e) {
                e.preventDefault();

                var thiss = $(this);
                var task_id = $(thiss).data('id');
                var image = $(this).html();
                var current_user = {{ Auth::id() }};
                var type = $(this).data('type');

                if (type == 'activity') {
                    var url = "/dailyActivity/start/" + task_id;
                } else {
                    var url = "/task/start/" + task_id;
                }

                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        type: "GET",
                        url: url,
                        data: {
                            type: 'start'
                        },
                        beforeSend: function() {
                            $(thiss).text('Sending ...');
                        }
                    }).done(function(response) {
                        $(thiss).closest('tr').find('.task-start-time').text(moment().format(
                            'DD-MM HH:mm'));
                        $(thiss).remove();
                    }).fail(function(response) {
                        $(thiss).html(image);

                        alert('Could not start task!');

                    });
                }
            });

            $(document).on("click", ".task-resend", function(e) {
                e.preventDefault();
                var button = $(this);
                if (!confirm("Are you sure to resend this notification?")) {
                    return false;
                }

                var id = $(this).data("id");
                $.ajax({
                    url: '/dailyplanner/resend-notification',
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id
                    },
                    dataType: 'json',

                    beforeSend: function() {
                        $("#loading-image").show();
                        button.prop('disabled', true);
                    },
                    success: function(result) {
                        $("#loading-image").hide();
                        if (result.code == 200) {
                            toastr['success'](result.message, 'success');
                        } else {
                            toastr['error']('Sorry, Something went wrong please try again!',
                                'error');
                        }
                        button.prop('disabled', false);
                    },
                    error: function() {
                        toastr['error']('Sorry, Something went wrong please try again!',
                            'error');
                        button.prop('disabled', false);
                    }
                });
            });

            $(document).on('click', '.task-stop', function() {
                event.preventDefault();
                var button = $(this);
                var parent_id = $(this).data('id');
                if (!confirm(
                        "Are you sure to stop this notification? It'll delete all future notification.")) {
                    return false;
                }

                $.ajax({
                    _token: "{{ csrf_token() }}",
                    type: 'POST',
                    url: '/calendar/events/stop',
                    data: {
                        parent_id: parent_id
                    },
                    beforeSend: function() {

                    }
                }).done(function(response) {
                    toastr['success'](response.message, 'success');
                    button.remove();
                }).fail(function(response) {

                    toastr['error']('Sorry, Something went wrong please try again!', 'error');
                });


            });

            $(document).on("click", ".task-history", function(e) {
                e.preventDefault();
                var id = $(this).data("id");
                $.ajax({
                    url: '/dailyplanner/history',
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $("#loading-image").show();
                    },
                    success: function(result) {
                        $("#loading-image").hide();
                        if (result.code == 200) {
                            $("#category-history-modal").find(".show-list-records").html(result
                                .html);
                            $("#category-history-modal").modal("show");
                        }
                    },
                    error: function() {
                        $("#loading-image").hide();
                    }
                });
            });

            $(document).on('click', '.show-tasks', function() {
                var count = $(this).data('count');
                // var rowspan = $(this)
                $('.hiddentask' + count).toggleClass('hidden');
            });

            function storeDailyActivity(element, activity, time_slot, target_id, general_category_id) {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('dailyActivity.quick.store') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        activity: activity,
                        time_slot: time_slot,
                        user_id: "{{ isset($userid) && $userid != '' ? $userid : Auth::id() }}",
                        for_date: "{{ $planned_at }}",
                        general_category_id: general_category_id
                    }
                }).done(function(response) {
                    var count = $('#' + target_id).find('td').attr('rowspan');
                    $('#' + target_id).find('td').attr('rowspan', parseInt(count, 10) + 1);

                    $(element).closest('tr').before(row);
                    $(element).val('');
                }).fail(function(response) {

                    alert('Could not create activity');
                });
            }

            $('.quick-plan-input').on('keypress', function(e) {
                var key = e.which;
                var thiss = $(this);
                var time_slot = $(this).data('timeslot');
                var target_id = $(this).data('targetid');
                var general_category_id = $(this).closest('td').find('.general_category_id').val();
                var activity = $(this).val();
                if (key == 13) {
                    e.preventDefault();

                    storeDailyActivity(thiss, activity, time_slot, target_id, general_category_id);
                }
            });

            $('.quick-plan-button').on('click', function(e) {
                var thiss = $(this);
                var time_slot = $(this).data('timeslot');
                var target_id = $(this).data('targetid');
                var activity = $(this).siblings('.quick-plan-input').val();
                var general_category_id = $(this).closest('td').find('.general_category_id').val();

                storeDailyActivity(thiss, activity, time_slot, target_id, general_category_id);

                $(this).siblings('.quick-plan-input').val('');
            });

            $('#showMeetingsButton').on('click', function() {
                $('#meetingsColumn').toggleClass('hidden');
                $('#plannerColumn').toggleClass('col-md-8');
                $('#plannerColumn').toggleClass('col-md-12');
            });

            $(document).on('click', '.expand-row', function() {
                var selection = window.getSelection();
                if (selection.toString().length === 0) {
                    $(this).find('.td-mini-container').toggleClass('hidden');
                    $(this).find('.td-full-container').toggleClass('hidden');
                }
            });

            $(document).on('click', '.quick-remark-input', function(e) {
                e.stopPropagation();
            });

            $(document).on("click", ".show-timer-div", function(e) {
                var $this = $(this);
                $this.closest("tr").nextAll('.create-input-table').first().toggleClass("dis-none");
            });

            $(document).on("click", ".task-reschedule", function() {
                $("#reschedule-type").val($(this).data("type"));
                $("#reschedule-id").val($(this).data("id"));
                $("#planned-at-input").val($(this).data("planned-at"));
                $("#reschedule-daily-planner").modal("show");
            });

            $(document).on("click", ".save-reschedule-planner", function(e) {
                e.preventDefault();

                var $this = $(this);
                var form = $this.closest("form");
                $.ajax({
                    type: form.attr("method"),
                    url: form.attr("action"),
                    data: form.serialize(),
                    beforeSend: function() {
                        $this.text('Sending ...');
                    }
                }).done(function(response) {
                    $this.text("Save");
                    $("#reschedule-daily-planner").modal("hide");
                    toastr['success'](response.message, 'success');
                }).fail(function(response) {
                    $this.text("Save");
                    toastr['error']('Sorry, we could not reschedule your daily planner!',
                        'success');
                });
            });
        });
    </script>
@endsection
