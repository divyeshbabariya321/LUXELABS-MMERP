@extends('layouts.app')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style type="text/css">
    #config-refactor-data-list .select2.select2-container.select2-container--default {
        width: 130px !important;
    }
</style>
@endsection
@section('content')
@php $types = \App\ConfigRefactorSection::$types; @endphp
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Config Refactor ({{ $configRefactors->total() }})</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-8">
                    <form action="{{ route('config-refactor.index') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-3 pd-sm">
                                <?php 
                                    if(request('section')){   $section = request('section'); }
                                    else{ $section = ''; }
                                ?>
                                <select name="section" id="section" class="form-control select2">
                                    <option value="" @if($section=='') selected @endif>-- Select a section --</option>
                                    @forelse($configRefactorSections as $id => $name)
                                    <option value="{{ $id }}" @if($section==$id) selected @endif>{!! $name !!}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                            <div class="col-md-3 pd-sm">
                                <?php 
                                    if(request('section_type')){   $sectionType = request('section_type'); }
                                    else{ $sectionType = ''; }
                                ?>
                                <select name="section_type" id="section_type" class="form-control select2">
                                    <option value="" @if($sectionType=='') selected @endif>-- Select a section type --</option>
                                    @forelse($types as $id => $name)
                                    <option value="{{ $id }}" @if($sectionType==$id) selected @endif>{!! $name !!}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                            <div class="col-md-3 pd-sm">
                                <?php 
                                    if(request('status')){   $status = request('status'); }
                                    else{ $status = ''; }
                                ?>
                                <select name="status" id="status" class="form-control select2">
                                    <option value="" @if($status=='') selected @endif>-- Select a status --</option>
                                    @forelse($configRefactorStatuses as $id => $name)
                                    <option value="{{ $id }}" @if($status==$id) selected @endif>{!! $name !!}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                            <div class="col-md-3 pd-sm pl-0 mt-2">
                                 <button type="submit" class="btn btn-image search">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                                <a href="{{ route('config-refactor.index') }}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-4">
                    <div class="pull-right">
                        <button type="button" class="btn btn-secondary" id="duplicate-config-refactor-button"> Duplicate Config Refactor </button>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#config-refactor-create"> Create Config Refactor </button>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#configRefactorStatusCreate"> Create Status </button>
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
                <div class="table-responsive" style="overflow-x: auto!important">
                    <table class="table table-bordered" style="width: 135%;max-width:unset" id="config-refactor-data-list">
                        <tr>
                            <th style="width: auto"></th>
                            <th style="width: auto">ID</th>
                            <th style="width: 6%">Store Website</th>
                            <th style="width: 7%">Section Name</th>
                            <th style="width: 5%">Section Type</th>
                            <th style="width: auto">User</th>
                            <th style="width: auto">Step1 Status</th>
                            <th style="width: auto">Step1 Remark</th>
                            <th style="width: auto">Step2 Status</th>
                            <th style="width: auto">Step2 Remark</th>
                            <th style="width: auto">Step3 Status</th>
                            <th style="width: auto">Step3 Remark</th>
                            <th style="width: auto">Step3.1 Status</th>
                            <th style="width: auto">Step3.1 Remark</th>
                            <th style="width: auto">Step3.2 Status</th>
                            <th style="width: auto">Step3.2 Remark</th>
                        </tr>
                        @foreach ($configRefactors as $key => $configRefactor)
                            <tr data-id="{{ $configRefactor->id }}">
                                <td><input type="checkbox" name="bulk_duplicate[]" class="d-inline bulk_duplicate" value="{{$configRefactor->id}}"></td>
                                <td>{{ ++$i }}</td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($configRefactor->storeWebsite?->title) > 12 ? substr($configRefactor->storeWebsite?->title, 0, 12).'...' :  $configRefactor->storeWebsite?->title }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $configRefactor->storeWebsite?->title }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($configRefactor->configRefactorSection->name) > 12 ? substr($configRefactor->configRefactorSection->name, 0, 12).'...' :  $configRefactor->configRefactorSection->name }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $configRefactor->configRefactorSection->name }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($types[$configRefactor->configRefactorSection->type]) > 30 ? substr($types[$configRefactor->configRefactorSection->type], 0, 30).'...' :  $types[$configRefactor->configRefactorSection->type] }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $types[$configRefactor->configRefactorSection->type] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                    <select class="form-control change-config-refactor-user select2" data-id="{{$configRefactor->id}}" data-column="user_id" name="user_id">
                                        <option value="">Select...</option>
                                        @foreach($users as $id => $user)
                                            @if( $configRefactor->user_id == $id )
                                                <option value="{{$id}}" selected>{{ $user }}</option>
                                            @else
                                                <option value="{{$id}}">{{ $user }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-xs btn-image load-refactor-users ml-2" data-id="{{$configRefactor->id}}" title="Load user histories"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                    <select class="form-control change-config-refactor-status select2" data-id="{{$configRefactor->id}}" data-column="step_1_status" name="step_1_status">
                                        <option value="">Select...</option>
                                        @foreach($configRefactorStatuses as $id => $name)
                                            @if( $configRefactor->step_1_status == $id )
                                                <option value="{{$id}}" selected>{{ $name }}</option>
                                            @else
                                                <option value="{{$id}}">{{ $name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-xs btn-image load-refactor-status ml-2" data-id="{{$configRefactor->id}}" data-column="step_1_status" title="Load histories"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <input type="text" id="step_1_remark_{{$configRefactor->id}}" name="step_1_remark" class="form-control" placeholder="Remark" />
                                        <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" data-id="{{$configRefactor->id}}" onclick="saveRemarks({{$configRefactor->id}}, 'step_1_remark')"><img src="/images/filled-sent.png"></button>
                                        <button type="button" class="btn btn-xs btn-image load-refactor-remark ml-2" data-id="{{$configRefactor->id}}" data-column="step_1_remark" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                    <select class="form-control change-config-refactor-status select2" data-id="{{$configRefactor->id}}" data-column="step_2_status" name="step_2_status">
                                        <option value="">Select...</option>
                                        @foreach($configRefactorStatuses as $id => $name)
                                            @if( $configRefactor->step_2_status == $id )
                                                <option value="{{$id}}" selected>{{ $name }}</option>
                                            @else
                                                <option value="{{$id}}">{{ $name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-xs btn-image load-refactor-status ml-2" data-id="{{$configRefactor->id}}" data-column="step_2_status" title="Load histories"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <input type="text" id="step_2_remark_{{$configRefactor->id}}" name="step_2_remark" class="form-control" placeholder="Remark" />
                                        <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" data-id="{{$configRefactor->id}}" onclick="saveRemarks({{$configRefactor->id}}, 'step_2_remark')"><img src="/images/filled-sent.png"></button>
                                        <button type="button" class="btn btn-xs btn-image load-refactor-remark ml-2" data-id="{{$configRefactor->id}}" data-column="step_2_remark" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                    <select class="form-control change-config-refactor-status select2" data-id="{{$configRefactor->id}}" data-column="step_3_status" name="step_3_status">
                                        <option value="">Select...</option>
                                        @foreach($configRefactorStatuses as $id => $name)
                                            @if( $configRefactor->step_3_status == $id )
                                                <option value="{{$id}}" selected>{{ $name }}</option>
                                            @else
                                                <option value="{{$id}}">{{ $name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-xs btn-image load-refactor-status ml-2" data-id="{{$configRefactor->id}}" data-column="step_3_status" title="Load histories"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <input type="text" id="step_3_remark_{{$configRefactor->id}}" name="step_3_remark" class="form-control" placeholder="Remark" />
                                        <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" data-id="{{$configRefactor->id}}" onclick="saveRemarks({{$configRefactor->id}}, 'step_3_remark')"><img src="/images/filled-sent.png"></button>
                                        <button type="button" class="btn btn-xs btn-image load-refactor-remark ml-2" data-id="{{$configRefactor->id}}" data-column="step_3_remark" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                    <select class="form-control change-config-refactor-status select2" data-id="{{$configRefactor->id}}" data-column="step_3_1_status" name="step_3_1_status">
                                        <option value="">Select...</option>
                                        @foreach($configRefactorStatuses as $id => $name)
                                            @if( $configRefactor->step_3_1_status == $id )
                                                <option value="{{$id}}" selected>{{ $name }}</option>
                                            @else
                                                <option value="{{$id}}">{{ $name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-xs btn-image load-refactor-status ml-2" data-id="{{$configRefactor->id}}" data-column="step_3_1_status" title="Load histories"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <input type="text" id="step_3_1_remark_{{$configRefactor->id}}" name="step_3_1_remark" class="form-control" placeholder="Remark" />
                                        <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" data-id="{{$configRefactor->id}}" onclick="saveRemarks({{$configRefactor->id}}, 'step_3_1_remark')"><img src="/images/filled-sent.png"></button>
                                        <button type="button" class="btn btn-xs btn-image load-refactor-remark ml-2" data-id="{{$configRefactor->id}}" data-column="step_3_1_remark" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                    <select class="form-control change-config-refactor-status select2" data-id="{{$configRefactor->id}}" data-column="step_3_2_status" name="step_3_2_status">
                                        <option value="">Select...</option>
                                        @foreach($configRefactorStatuses as $id => $name)
                                            @if( $configRefactor->step_3_2_status == $id )
                                                <option value="{{$id}}" selected>{{ $name }}</option>
                                            @else
                                                <option value="{{$id}}">{{ $name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-xs btn-image load-refactor-status ml-2" data-id="{{$configRefactor->id}}" data-column="step_3_2_status" title="Load histories"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <input type="text" id="step_3_2_remark_{{$configRefactor->id}}" name="step_3_2_remark" class="form-control" placeholder="Remark" />
                                        <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" data-id="{{$configRefactor->id}}" onclick="saveRemarks({{$configRefactor->id}}, 'step_3_2_remark')"><img src="/images/filled-sent.png"></button>
                                        <button type="button" class="btn btn-xs btn-image load-refactor-remark ml-2" data-column="step_3_2_remark" data-id="{{$configRefactor->id}}" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    {!! $configRefactors->appends(request()->except('page'))->links() !!}
                </div>
            </div>
        </div>
    </div>
</div>
{{-- config-refactor-duplicate --}}
@include('config-refactor.partials.config-refactor-duplicate-modal')
{{-- config-refactor-create --}}
@include('config-refactor.partials.config-refactor-create-modal')
{{-- configRefactorStatusCreate --}}
@include('config-refactor.partials.config-refactor-status-create-modal')
{{-- #remark-area-list --}}
@include('config-refactor.partials.remark_list')
{{-- #status-area-list --}}
@include('config-refactor.partials.status_list')
{{-- #user-area-list --}}
@include('config-refactor.partials.users_list')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
    $('.select2').select2();

    $(document).ready(function(){
        var selected_config_refactors = [];

        $(document).on('click', '.bulk_duplicate', function () {
            var checked = $(this).prop('checked');
            var id = $(this).val();
             if (checked) {
                selected_config_refactors.push(id);
            } else {
                var index = selected_config_refactors.indexOf(id);
                selected_config_refactors.splice(index, 1);
            }
        });

        $(document).on("click","#duplicate-config-refactor-button",function(e){
            e.preventDefault();
            if(selected_config_refactors.length < 1) {
                toastr['error']("Select some rows first");
                return;
            }

            $('#config-refactor-duplicate').modal('show');
        });

        $(document).on('submit', '#config-refactor-duplicate-form', function (e) {
            e.preventDefault();
            var data = $(this).serializeArray();
            data.push({name: 'config_refactors', value: selected_config_refactors});
            $.ajax({
                url: "{{route('config-refactor.duplicate-create')}}",
                type: 'POST',
                data: data,
                success: function (response) {
                    if(!response.status) {
                        toastr['error'](response.message, 'error');    
                    } else {
                        toastr['success']('Successful', 'success');
                        location.reload();
                    }
                },
                error: function () {
                    alert('There was error loading priority task list data');
                }
            });
        });
    });

    $(document).on("click", ".save-config-refactor-window", function(e) {
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
                    $("#config-refactor-create").modal("hide");
                    location.reload();
                } else {
                    toastr['error'](response.message);
                }
            }
        }).fail(function(response) {
            toastr['error'](response.responseJSON.message);
        });
    });

    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });

    // Load users Histories
    $(document).on('click', '.load-refactor-users', function() {
        var id = $(this).attr('data-id');

        $.ajax({
            method: "GET",
            url: `{{ route('config-refactor.get_users_histories', [""]) }}/` + id,
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${(v.old_user_name != null) ? v.old_user_name : ' - ' } </td>
                                    <td> ${(v.new_user_name != null) ? v.new_user_name : ' - ' } </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#user-area-list").find(".user-action-list-view").html(html);
                    $("#user-area-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    // Load Status Histories
    $(document).on('click', '.load-refactor-status', function() {
        var id = $(this).attr('data-id');
        var column = $(this).attr('data-column');

        $.ajax({
            method: "GET",
            url: `{{ route('config-refactor.get_status_histories', ["", ""]) }}/` + id + '/' + column,
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${(v.old_status_name != null) ? v.old_status_name : ' - ' } </td>
                                    <td> ${(v.new_status_name != null) ? v.new_status_name : ' - ' } </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#status-area-list").find(".status-action-list-view").html(html);
                    $("#status-area-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    // Load Remark
    $(document).on('click', '.load-refactor-remark', function() {
        var id = $(this).attr('data-id');
        var column = $(this).attr('data-column');

        $.ajax({
            method: "GET",
            url: `{{ route('config-refactor.get_remarks', ["", ""]) }}/` + id + '/' + column,
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

    // Store Remarks
    function saveRemarks(row_id, column) {
        var remark = $("#" + column + "_" + row_id).val();
        $.ajax({
            url: `{{ route('config-refactor.store.remark') }}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: {
                id: row_id,
                column: column,
                remark: remark,
            },
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            if (response.status) {
                $("#" + column + "_" + row_id).val('');
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
    $(document).on('change', '.change-config-refactor-status', function() {
        let id = $(this).attr('data-id');
        let column = $(this).attr('data-column');
        let status = $(this).val();

        $.ajax({
            url: "{{route('config-refactor.change.status')}}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType: "json",
            data: {
                'id': id,
                'column': column,
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

    // on user change
    $(document).on('change', '.change-config-refactor-user', function() {
        let id = $(this).attr('data-id');
        let user_id = $(this).val();

        $.ajax({
            url: "{{route('config-refactor.change.user')}}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType: "json",
            data: {
                'id': id,
                'user_id': user_id
            },
            success: function(response) {
                toastr["success"](response.message, "Message")
            },
            error: function(error) {
                toastr["error"](error.responseJSON.message, "Message")
            }
        });
    });
</script>
@endsection