@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Zabbix Webhook Datas ({{ $zabbixWebhookDatas->total() }})</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-8">
                    <form action="{{ route('zabbix-webhook-data.index') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-4 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control h-100" value="{{ request()->get('keyword') }}">
                            </div>
                            <div class="col-lg-4">
                                <input class="form-control" type="date" name="event_start" value="{{ request()->get('event_start') }}">
                            </div>
                            <div class="col-md-4 pd-sm pl-0 mt-2">
                                 <button type="submit" class="btn btn-image search">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                                <a href="{{ route('zabbix-webhook-data.index') }}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-4">
                    <div class="pull-right">
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#ZabbixStatusList"> List Status </button>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#zabbixStatusCreate"> Create Status </button>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#zabbix-task-create"> Create Task </button>
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
                    <table class="table table-bordered" style="table-layout: fixed;" id="zabbix-webhook-data-list">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="10%">Subject</th>
                            <th width="20%">Message</th>
                            <th width="10%">Event Start</th>
                            <th width="10%">Event Name</th>
                            <th width="10%">Event ID</th>
                            <th width="10%">Host</th>
                            <th width="10%">Severity</th>
                            <th width="20%">Operational Data</th>
                            <th width="20%">Status</th>
                            <th width="20%">Remarks</th>
                            <th width="5%">Action</th>
                        </tr>
                        @foreach ($zabbixWebhookDatas as $key => $zabbixWebhookData)
                            @php
                                $bgColor = "";
                            @endphp
                            @if ($zabbixWebhookData->zabbix_task_id)
                                @php $bgColor = "#f1f1f1 !important"; @endphp
                            @endif
                            <tr data-id="{{ $zabbixWebhookData->id }}" style="background-color: {{$zabbixWebhookData->zabbixStatusColour?->color}};">
                                <td>{{ $zabbixWebhookData->id }}</td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($zabbixWebhookData->subject) > 15 ? substr($zabbixWebhookData->subject, 0, 15).'...' :  $zabbixWebhookData->subject }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $zabbixWebhookData->subject }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($zabbixWebhookData->message) > 30 ? substr($zabbixWebhookData->message, 0, 30).'...' :  $zabbixWebhookData->message }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $zabbixWebhookData->message }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($zabbixWebhookData->event_start) > 15 ? substr($zabbixWebhookData->event_start, 0, 15).'...' :  $zabbixWebhookData->event_start }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $zabbixWebhookData->event_start }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($zabbixWebhookData->event_name) > 15 ? substr($zabbixWebhookData->event_name, 0, 15).'...' :  $zabbixWebhookData->event_name }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $zabbixWebhookData->event_name }}
                                    </span>
                                </td>
                                <td style="word-break: break-all">{{ $zabbixWebhookData->event_id }}</td>
                                <td style="word-break: break-all">{{ $zabbixWebhookData->host }}</td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($zabbixWebhookData->severity) > 15 ? substr($zabbixWebhookData->severity, 0, 15).'...' :  $zabbixWebhookData->severity }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $zabbixWebhookData->severity }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($zabbixWebhookData->operational_data) > 30 ? substr($zabbixWebhookData->operational_data, 0, 30).'...' :  $zabbixWebhookData->operational_data }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $zabbixWebhookData->operational_data }}
                                    </span>
                                </td>
                                <td>
                                    <select class="form-control change-zabbix-status select2" data-id="{{$zabbixWebhookData->id}}" name="zabbix_status_id">
                                        <option value="">Select...</option>
                                        @foreach($zabbixStatuses as $id => $name)
                                            @if( $zabbixWebhookData->zabbix_status_id == $id )
                                                <option value="{{$id}}" selected>{{ $name }}</option>
                                            @else
                                                <option value="{{$id}}">{{ $name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" id="remarks_{{$zabbixWebhookData->id}}" name="remarks" class="form-control" placeholder="Remark" />
                                    <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" data-id="{{$zabbixWebhookData->id}}" onclick="saveRemarks({{$zabbixWebhookData->id}})"><img src="/images/filled-sent.png"></button>
                                    <button type="button" class="btn btn-xs btn-image load-module-remark ml-2" data-id="{{$zabbixWebhookData->id}}" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                </td>
                                <td class="Website-task"title="">
                                    <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$zabbixWebhookData->id}}')"><i class="fa fa-arrow-down"></i></button>
                                </td>
                            </tr>
                            
                            <tr class="action-btn-tr-{{$zabbixWebhookData->id}} d-none">
                                <td>Action</td>
                                <td id="zabbix_webhook_data_action"  colspan="9" >
                                    <button type="button" class="btn btn-xs" title="Task assignee history" data-zabbix_task_id="{{$zabbixWebhookData->zabbix_task_id}}" data-type="developer">
                                        <i class="fa fa-info-circle" style="color: #808080;"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                {!! $zabbixWebhookDatas->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>

{{-- zabbixStatusCreate --}}
@include('zabbix-webhook-data.partials.zabbix-status-create-modal')
{{-- #remark-area-list --}}
@include('zabbix-webhook-data.partials.remark_list')
{{-- #assignee-history-list --}}
@include('zabbix-webhook-data.partials.assignee-history-list')

@include('partials.modals.zabbix-task-create-window')

@include('zabbix-webhook-data.partials.zabbix-status-listing')

<script type="text/javascript">
    $(document).ready(function(){
        
    })

    function Showactionbtn(id) {
        $(".action-btn-tr-" + id).toggleClass('d-none')
    }

    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });

    // Load Remark
    $(document).on('click', '.load-module-remark', function() {
        var id = $(this).attr('data-id');
        $.ajax({
            method: "GET",
            url: `{{ route('zabbix-webhook-data.get_remarks', '') }}/` + id,
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${v.remarks } </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#remark-area-list").find(".remark-action-list-view").html(html);
                    $("#remark-area-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    // Show task assignee histories
    $(document).on('click', '.show-task-assignee-history', function() {
        var zabbix_task_id = $(this).attr('data-zabbix_task_id');

        $.ajax({
            method: "GET",
            url: `{{ route('zabbix-task.get-assignee-histories', '') }}/` + zabbix_task_id,
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${(v.new_assignee !== undefined) ? v.new_assignee.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#assignee-history-list").find(".assignee-history-list-view").html(html);
                    $("#assignee-history-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    // Store Reark
    function saveRemarks(row_id) {
        var remark = $("#remarks_" + row_id).val();
        $.ajax({
            url: `{{ route('zabbix-webhook-data.store.remark') }}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: {
                remarks: remark,
                zabbix_webhook_data_id: row_id
            },
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            if (response.status) {
                $("#remarks_" + row_id).val('');
                toastr["success"](response.message);
            } else {
                toastr["error"](response.message);
            }
            $("#loading-image").hide();
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            if (jqXHR.responseJSON.errors !== undefined) {
                $.each(jqXHR.responseJSON.errors, function(key, value) {
                    // $('#validation-errors').append('<div class="alert alert-danger">' + value + '</div');
                    toastr["warning"](value);
                });
            } else {
                toastr["error"]("Oops,something went wrong");
            }
            $("#loading-image").hide();
        });
    }

    // on status change
    $(document).on('change', '.change-zabbix-status', function() {
        let id = $(this).attr('data-id');
        let status = $(this).val();
        $.ajax({
            url: "{{route('zabbix-webhook-data.change.status')}}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType: "json",
            data: {
                'zabbix_webhook_data': id,
                'status': status
            },
            success: function(response) {
                toastr["success"](response.message, "Message")
                $(`#zabbix-webhook-data-list tr[data-id="${id}"]`).css('background-color', response.colourCode);
           },
            error: function(error) {
                toastr["error"](error.responseJSON.message, "Message")
            }
        });
    });

    $(document).on("click", ".save-zabbix-task-window", function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        $.ajax({
            url: form.attr("action"),
            type: 'POST',
            data: form.serialize(),
            beforeSend: function() {
                $(this).text('Loading...');
            },
            success: function(response) {
                if (response.code == 200) {
                    form[0].reset();
                    toastr['success'](response.message);
                    $("#zabbix-task-create").modal("hide");
                    location.reload();
                } else {
                    toastr['error'](response.message);
                }
            }
        }).fail(function(response) {
            toastr['error'](response.responseJSON.message);
        });
    });
</script>
@endsection