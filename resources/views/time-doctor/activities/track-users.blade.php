@extends('layouts.app')


@section('title', $title)

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<div class="row">
    <div class="col-md-12">
        <h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
    </div>
</div>
<div class="row" id="common-page-layout">
    <div class="col-lg-12 margin-tb">
        <div class="col-md-12">
            <div class="col-md-12 margin-tb">
                <form class="form-check-inline" action="{{route('time-doctor-acitivties.acitivties.userTreckTime')}}" method="get">
                    <div class="row">
                        <div class="form-group col-md-2">
                            {{ html()->select("user_id", ["" => "-- Select User --"] + $users, $user_id)->class("form-control select2") }}
                        </div>
                        <div class="form-group col-md-3">
                            {{ html()->text("developer_task_id", request('developer_task_id'))->class("form-control")->placeholder("Developer Task ID") }}
                        </div>
                        <div class="form-group col-md-3">

                            {{ html()->text("task_id", request('task_id'))->class("form-control")->placeholder("Task ID") }}
                        </div>
                        <div class="form-group col-md-4">
                            <input type="text" value="{{$start_date}}" name="start_date" hidden />
                            <input type="text" value="{{$end_date}}" name="end_date" hidden />
                            <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span> <i class="fa fa-caret-down"></i>
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <button type="submit" name="submit" class="btn mt-2 btn-xs text-dark">
                                <i class="fa fa-search"></i>
                            </button>

                        </div>
                    </div>
                </form>
            </div>            
            <div class="col-md-12 margin-tb p-0">
                <div class="table-responsive">
                    <table class="table table-bordered" style='table-layout: fixed;'>
                        <tr>
                            <th width="10%">Date</th>
                            <th width="10%">User</th>
                            <th width="10%">Working time</th>
                            <th width="10%">TimeDoctor Tracked</th>
                            <th width="10%">Hours Tracked With Task ID</th>
                            <th width="10%">Hours Tracked Without Task ID</th>
                            <th width="10%">Task id</th>
                            <th width="10%">Approved Hours</th>
                            <th width="10%" title="Time Diffrent">Time Diffrent</th>
                            <th width="10%">Tot Hours</th>
                            <th width="10%">Activity Levels</th>                            
                        </tr>
                        @foreach ($userTrack as $index => $user)
                        <tr>
                            <td>{{ $user['date'] }} </td>
                            <td class="expand-row-msg Website-task" data-name="userName" data-id="{{$index}}">
                                <span class="show-short-userName-{{$index}}">{{ Str::limit($user['userName'], 5, '..')}}</span>
                                <span style="word-break:break-all;" class="show-full-userName-{{$index}} hidden Website-task">{{$user['userName']}}</span>
                            </td>
                            <td>
                                @if (isset($user['working_time']) && $user['working_time'] != 0)
                                    {{number_format(($user['working_time']) / 60,2,".",",")}}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                {{number_format($user['time_doctor_tracked_hours'] / 60,2,".",",")}}
                                <form action="">
                                    <input type="hidden" class="user_id" name="user_id" value="{{$user['user_id']}}">
                                    <input type="hidden" class="date" name="date" value="{{$user['date']}}">
                                    <a class="btn btn-xs text-dark show-activities"><i class="fa fa-plus"></i></a>
                                </form>
                            </td>
                            <td>{{number_format($user['hours_tracked_with'] / 60,2,".",",")}}</td>
                            <td>{{number_format($user['hours_tracked_without'] / 60,2,".",",")}}</td>
                            <td>{{$user['task_id']}}</td>
                            <td>{{number_format($user['approved_hours'] / 60,2,".",",")}}</td>
                            <td>{{number_format($user['difference_hours'] / 60,2,".",",")}}</td>
                            <td>{{number_format($user['total_hours'] / 60,2,".",",")}}</td>
                            <td>{{number_format($user['activity_levels'],2,".","," )}} %</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div id="records-modal" class="modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 1200px !important; width: 100% !important;">
        <div class="modal-content" id="record-content">
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', '.expand-row-msg', function() {
            var name = $(this).data('name');
            var id = $(this).data('id');
            var full = '.expand-row-msg .show-short-' + name + '-' + id;
            var mini = '.expand-row-msg .show-full-' + name + '-' + id;
            $(full).toggleClass('hidden');
            $(mini).toggleClass('hidden');
        });
    });

    $(document).on('click', '.expand-row', function() {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });


    $("#activity-available").val(new Date().toUTCString());
    $(".select2").select2({
        tags: true
    });

    $('#starts_at').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $('#custom_hour').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss'
    });
    $('#time_from').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss'
    });
    let r_s = jQuery('input[name="start_date"]').val();
    let r_e = jQuery('input[name="end_date"]').val()

    if (r_s == "0000-00-00 00:00:00") {
        r_s = undefined;
    }

    if (r_e == "0000-00-00 00:00:00") {
        r_e = undefined;
    }

    let start = r_s ? moment(r_s, 'YYYY-MM-DD') : moment().subtract(6, 'days');
    let end = r_e ? moment(r_e, 'YYYY-MM-DD') : moment();

    let r_s_p = "";
    let r_e_p = "";

    let start_p = r_s_p ? moment(r_s_p, 'YYYY-MM-DD') : moment().subtract(0, 'days');
    let end_p = r_e_p ? moment(r_e_p, 'YYYY-MM-DD') : moment();
    

    function cb(start, end, id) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        maxYear: 1,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    cb(start, end);

    $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
        jQuery('input[name="start_date"]').val(picker.startDate.format('YYYY-MM-DD'));
        jQuery('input[name="end_date"]').val(picker.endDate.format('YYYY-MM-DD'));
    });


    function tdDate(start, end, id) {
        $('#TimeDoctorDateRange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        jQuery('input[name="time_doctor_start_date"]').val(start.format('YYYY-MM-DD'));
        jQuery('input[name="time_doctor_end_date"]').val(end.format('YYYY-MM-DD'));
    }

    $('#TimeDoctorDateRange').daterangepicker({
        maxYear: 1,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, tdDate);


    $('#TimeDoctorDateRange').on('apply.daterangepicker', function(ev, picker) {
        jQuery('input[name="time_doctor_start_date"]').val(picker.startDate.format('YYYY-MM-DD'));
        jQuery('input[name="time_doctor_end_date"]').val(picker.endDate.format('YYYY-MM-DD'));
    });

    function hubpaymentdate(start_p, end_p, id) {
        $('#filter_date_range_ span').html(start_p.format('MMMM D, YYYY') + ' - ' + end_p.format('MMMM D, YYYY'));
        jQuery('input[name="range_start"]').val(start_p.format('YYYY-MM-DD'));
        jQuery('input[name="range_end"]').val(end_p.format('YYYY-MM-DD'));
    }

    $('#filter_date_range_').daterangepicker({
        maxYear: 1,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, hubpaymentdate);

    $('#filter_date_range_').on('apply.daterangepicker', function(ev, picker) {
        jQuery('input[name="range_start"]').val(picker.startDate.format('YYYY-MM-DD'));
        jQuery('input[name="range_end"]').val(picker.endDate.format('YYYY-MM-DD'));
    });

    var thisRaw = null;
    $(document).on('click', '.show-activities', function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        var thiss = $(this);
        thisRaw = thiss;
        var type = 'GET';
        $.ajax({
            url: '/time-doctor-activities/activities/details?' + form.serialize(),
            type: type,
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            $('#records-modal').modal('show');
            $('#record-content').html(response);
        }).fail(function(errObj) {
            $("#loading-image").hide();
            toastr['error'](errObj.responseJSON.message, 'error');
        });
    });


    $(document).on('click', '.submit-record', function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        var thiss = $(this);
        var type = 'POST';
        $.ajax({
            url: '/time-doctor-activities/activities/details',
            type: type,
            dataType: 'json',
            data: form.serialize(),
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            thisRaw.closest("tr").find('.replaceme').html(response.totalApproved);
            $('#records-modal').modal('hide');
            thisRaw.closest("tr").find('.show-activities').css("display", "none");
        }).fail(function(errObj) {
            toastr['error'](errObj.responseJSON.message, 'error');
            $("#loading-image").hide();
        });
    });



    $(document).on('click', '.submit-manual-record', function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        var thiss = $(this);
        var type = 'POST';
        $.ajax({
            url: '/time-doctor-activities/activities/manual-record',
            type: type,
            dataType: 'json',
            data: form.serialize(),
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            $('#open-timing-modal').modal('hide');
            toastr['success']('Successful');
        }).fail(function(errObj) {
            toastr['error'](errObj.responseJSON.message, 'error');
            $("#loading-image").hide();
        });
    });

    $(document).on('click', '.selectall', function(e) {
        var cls = '.' + $(this).data("id");
        if ($(this).is(':checked')) {
            $(cls).attr('checked', true);
        } else {
            $(cls).attr('checked', false);
        }
    });

    $(document).on('change', '.select-forword-to', function(e) {
        var person = $(this).data('person');
        $("#hidden-forword-to").val(person);
    });

    $(document).on('click', '.final-submit-record', function(e) {        
        e.preventDefault();
        var vali = false;
        $('.notes-input').each(function() {
            if ($(this).val() == '') {
                toastr['error']('invalid notes', 'error');
                vali = true;
            }
        });

        if (vali == true) {
            return false;
        }
        var status = $(this).data('status');
        // return false;
        var form = $(this).closest("form");
        var thiss = $(this);
        var type = 'POST';
        var data = form.serializeArray();
        data.push({
            name: 'status',
            value: status
        });

        $.ajax({
            url: '/time-doctor-activities/activities/final-submit',
            type: type,
            dataType: 'json',
            // data: form.serialize(),
            data: data,
            beforeSend: function() {
                /*$("#loading-image").show();*/
            }
        }).done(function(response) {
            $("#loading-image").hide();
            thisRaw.closest("tr").find('.replaceme').html(response.totalApproved);
            $('#records-modal').modal('hide');
            $(".show-activities").css("display", "none");
            thisRaw.closest("tr").find('.show-activities').css("display", "none");
        }).fail(function(errObj) {
            toastr['error'](errObj.responseJSON.message, 'error');
            $("#loading-image").hide();
        });
    });


    $(document).on('click', '.submit-fetch-activity', function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        var thiss = $(this);
        var type = 'POST';
        $.ajax({
            url: '/time-doctor-activities/activities/fetch',
            type: type,
            dataType: 'json',
            data: form.serialize(),
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            window.location.reload();
            toastr['success'](response.message, 'success');
        }).fail(function(errObj) {
            $("#loading-image").hide();
            if (errObj.responseJSON) {
                toastr['error'](errObj.responseJSON.message, 'error');
            }
            window.location.reload();
        });
    });


    $(document).on('click', '.expand-row-btn', function() {
        $(this).closest("tr").find(".expand-col").toggleClass('dis-none');
    });

    $(document).on("click", ".show-task-histories", function(e) {
        e.preventDefault();
        var $this = $(this);
        thisRaw = $this;
        $.ajax({
            url: '/time-doctor-activities/activities/task-activity',
            type: 'GET',
            data: {
                "task_id": $this.data("task-id"),
                "user_id": $this.data("user-id")
            },
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            $("#loading-image").hide();
            $('#records-modal').modal('show');
            $('#record-content').html(response);
        }).fail(function(errObj) {
            $("#loading-image").hide();
            if (errObj.responseJSON) {
                toastr['error'](errObj.responseJSON.message, 'error');
            }
        });
    });

    $(document).on('click', '.time-doctor-activity-report-download', function() {
        var user_id = $(this).data('system_user_id');
        $('#timeDoctorActivityReportModel .time-doctor-activity-table').text('');
        $.ajax({
            url: "{{ route('time-doctor-activtity.report') }}",
            type: 'GET',
            data: {
                "user_id": user_id
            },
            success: function(response) {
                if (response.status == true) {
                    array = response.data;
                    for (let i = 0; i < array.length; i++) {
                        j = i + 1;
                        $html = '<tr><td>Excel File-' + j + '</td><td><button class="btn activity-report-download" data-file="' + array[i].activity_excel_file + '"><i class="fa fa-download" aria-hidden="true"></i></button></td></tr>';
                        $('#timeDoctorActivityReportModel .time-doctor-activity-table').append($html);
                    }
                }
            }
        });
    })

    $(document).on('click', '.activity-report-download', function() {
        var file = $(this).data('file');
        var $this = $(this);

        window.location.replace("{{ route('time-doctor-activity-report.download') }}?file=" + file);

    })

    $(document).on('click', '.time-doctor-payment-receipt', function() {
        var $this = $(this);
        var id = $this.data("id");

        $.ajax({
            url: "{{ url('time-doctor-acitivtity.addtocashflow') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                id: id
            },
            cors: true,
            dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            if (response.code == 200) {
                toastr['success'](response.message);
            }
        }).fail(function(errObj) {
            $("#loading-image").hide();
        });
    })




    $(document).on('click', '.list_history_payment_data', function() {
        var $this = $(this);
        var user_id = $this.data("user-id");

        $.ajax({
            url: "{{ route('time-doctor-activity.payment_data') }}",
            type: 'GET',
            data: {
                "user_id": user_id
            },
            success: function(response) {
                if (response.status == true) {
                    var html = '';

                    $.each(response.data, function(key, value) {
                        rpost = '';
                        if (value.command_execution && value.command_execution == "Manually")
                            rpost = '<a style="cursor:pointer;" data-id="' + value.id + '" class="time-doctor-payment-receipt" aria-hidden="true"> Add To Cashflow </a>';
                        html += '<tr>';
                        html += '<td>' + moment(value.start_date).format('DD-MM-YYYY') + '</td>';
                        html += '<td>' + moment(value.end_date).format('DD-MM-YYYY') + '</td>';
                        html += '<td>' + value.total_amount + '</td>';
                        html += '<td>' + (value.command_execution ?? '-') + rpost + '</td>';
                        html += '<td><a style="cursor:pointer;" data-file="' + value.file_path + '" class="fa fa-download time-doctor-payment-download" aria-hidden="true"></a></td>';
                        html += '</tr>';
                    });

                    $('#timeDoctorPaymentReportModel .time-doctor-payment-table').html(html);
                    $('#timeDoctorPaymentReportModel').modal('show');
                }
            }
        });
    })

    $(document).on('click', '.time-doctor-payment-download', function() {
        var file = $(this).data('file');
        var $this = $(this);

        window.location.replace("{{ route('time-doctor-payment-report.download') }}?file=" + file);

    })

    $('#time_doctor_activity_modal').on('shown.bs.modal', function(e) {
        $("#select2insidemodal").select2({
            dropdownParent: $("#time_doctor_activity_modal")
        });
    })

    $(document).on('click', '.execute_time_doctor_payment_command', function() {
        var user_id = $("#select2insidemodal").val();
        var startDate = jQuery('input[name="range_start"]').val();
        var endDate = jQuery('input[name="range_end"]').val();

        if (user_id == '') {
            toastr['error']('Please Select User');
            return false;
        }

        if (startDate == '') {
            toastr['error']('Please Select Date Range');
            return false;
        }

        $.ajax({
            url: "{{ route('time-doctor-activity.command_execution_manually') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                user_id: user_id,
                startDate: startDate,
                endDate: endDate,
            },
            cors: true,
            dataType: 'json',
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            if (response.code == 200) {
                toastr['success'](response.message);
            }
        }).fail(function(errObj) {
            $("#loading-image").hide();
        });
    })
</script>
@endsection