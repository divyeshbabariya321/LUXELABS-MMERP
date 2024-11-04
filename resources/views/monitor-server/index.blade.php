@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Website Monitors List ({{ $monitorServers->total() }})</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-12">
                    <form action="{{ route('monitor-server.index') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-2 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control h-100" value="{{ request()->get('keyword') }}">
                            </div>
                            <div class="col-md-2 pd-sm">
                                <select id="status" class="form-control h-100" name="status">
                                    <option value="on">On</option>
                                    <option value="off" selected="selected">Off</option>
                                </select>		
                            </div>
                         
                            <a href="{{route('monitor-server.log.history.truncate')}}" class="btn btn-primary" onclick="return confirm('{{ __('Are you sure you want to Truncate a Data? Note : It will Remove all the histories)') }}')">Truncate Data </a>		
                            <div class="col-md-2 pd-sm pl-0 mt-2">
                                 <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                            </div>
                        </div>
                    </form>
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

<div class="tab-content ">
    <!-- Pending task div start -->
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;" id="quick-reply-list">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="12%">HOST/IP</th>
                            <th width="12%">Check Name</th>
                            <th width="8%">Type</th>
                            <th width="5%">Status</th>
                            <th width="10%">Root cause</th>
                            <th width="10%">Response Time</th>
                            <th width="10%">Last Online</th>
                            <th width="10%">Last Offline</th>
                            <th width="10%">SSL Expiry</th>
                            <th width="10%">Last Downtime</th>
                            <th width="10%">SSL Certificate Expired Time</th>
                            <th width="5%">Action</th>
                        </tr>
                        @foreach ($monitorServers as $key => $monitorServer)
                            <tr class="quick-website-task-{{ $monitorServer->server_id }}" data-id="{{ $monitorServer->server_id }}">
                                <td id="monitor_server_id">{{ $monitorServer->server_id }}</td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       <a href="{{$monitorServer->ip}}" target="_blank"> {{ strlen($monitorServer->ip) > 15 ? substr($monitorServer->ip, 0, 15).'...' :  $monitorServer->ip }}</a>
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $monitorServer->ip }}
                                    </span>
                                </td>
                                <td style="word-break: break-all">{{ $monitorServer->label }}</td>
                                <td>{{ $monitorServer->type }}</td>
                                <td>
                                    <span class="badge {{ $monitorServer->status == 'off' ? "badge-danger" : "badge-success"}}">{{ $monitorServer->status }}</span>
                                </td>
                                <td>{{ $monitorServer->error }}</td>
                                <td>{{ $monitorServer->rtime }}</td>
                                <td>{{ $monitorServer->last_online }}</td>
                                <td>{{ $monitorServer->last_offline }}</td>
                                <td>{{ $monitorServer->ssl_cert_expired_time }}</td>
                                <td>{{ $monitorServer->last_offline_duration }}</td>
                                <td>{{ $monitorServer->ssl_cert_expired_time }}</td>
                                <td class="Website-task"title="">
                                    <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$monitorServer->server_id}}')"><i class="fa fa-arrow-down"></i></button>
                                </td>
                            </tr>
                            
                            <tr class="action-btn-tr-{{$monitorServer->server_id}} d-none">
                                <td>Action</td>
                                <td id="monitor_server_action"  colspan="9" >
                                    <button type="button" class="btn btn-xs show-server-uptimes" title="Server Uptimes" data-id="{{$monitorServer->server_id}}" data-type="developer">
                                        <i class="fa fa-info-circle" style="color: #808080;"></i>
                                    </button>
                                    <button type="button" class="btn btn-xs show-server-users" title="Server Users" data-id="{{$monitorServer->server_id}}" data-type="developer">
                                        <i class="fa fa-info-circle" style="color: #808080;"></i>
                                    </button>
                                    <button type="button" class="btn btn-xs show-server-history" title="Server History" data-id="{{$monitorServer->server_id}}" data-type="developer">
                                        <i class="fa fa-info-circle" style="color: #808080;"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                    {!! $monitorServers->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="monitor_server_uptimes">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Server Uptimes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="monitor_server_uptimes_data">
                        <input type="hidden" class="monitor_server_id">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Latency</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                            </tfoot>
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

<div class="modal" tabindex="-1" role="dialog" id="monitor_server_users">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Server Users</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="monitor_server_users_data">
                        <input type="hidden" class="monitor_server_id">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                            </tfoot>
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

<div class="modal" tabindex="-1" role="dialog" id="monitor_server_history">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Server History <i class="fa fa-history" aria-hidden="true"></i></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="monitor_server_history_data">
                        <input type="hidden" class="monitor_server_id">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                            </tfoot>
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

<script type="text/javascript">

    $(document).ready(function(){
        // Show server uptimes
        $(document).on("click",".show-server-uptimes",function(e) {
            e.preventDefault();
            var $this = $(this);
            $.ajax({
                url: "{{ route('monitor-server.get-server-uptimes', '') }}/" + $this.attr('data-id'),
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
            }).done( function(response) {
                    $("#loading-image-preview").hide();
                    if(response.code == 200) {
                        var html = '';
                        console.log(response);
                        $.each(response.data.data,function(idnex, val){
                            html += '<tr><td>'+val.date+'</td><td>'+val.status+'</td><td>'+val.latency+'</td></tr>';
                        })

                        $('#monitor_server_uptimes_data .monitor_server_id').val('');
                        $('#monitor_server_uptimes_data .monitor_server_id').val($this.attr('data-id'));
                        $('#monitor_server_uptimes_data table tbody').html('');
                        $('#monitor_server_uptimes_data table tbody').html(html);
                        $('#monitor_server_uptimes_data table tfoot').html('');
                        $('#monitor_server_uptimes_data table tfoot').html(response.paginate);
                        $('#monitor_server_uptimes').modal('show');

                    }else{
                    toastr["error"]('Something went wrong!');
                    }
            }).fail(function(errObj) {
                    $("#loading-image-preview").hide();
            });
        });

        // Show server users
        $(document).on("click",".show-server-users",function(e) {
            e.preventDefault();
            var $this = $(this);
            $.ajax({
                url: "{{ route('monitor-server.get-server-users', '') }}/" + $this.attr('data-id'),
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
            }).done( function(response) {
                    $("#loading-image-preview").hide();
                    if(response.code == 200) {
                        var html = '';
                        console.log(response);
                        $.each(response.data.data,function(idnex, val){
                            html += '<tr><td>'+val.user_name+'</td><td>'+val.name+'</td><td>'+val.mobile+'</td><td>'+val.email+'</td></tr>';
                        })

                        $('#monitor_server_users_data .monitor_server_id').val('');
                        $('#monitor_server_users_data .monitor_server_id').val($this.attr('data-id'));
                        $('#monitor_server_users_data table tbody').html('');
                        $('#monitor_server_users_data table tbody').html(html);
                        $('#monitor_server_users_data table tfoot').html('');
                        $('#monitor_server_users_data table tfoot').html(response.paginate);
                        $('#monitor_server_users').modal('show');

                    }else{
                        toastr["error"]('Something went wrong!');
                    }
            }).fail(function(errObj) {
                    $("#loading-image-preview").hide();
            });
        });

        //Paginate the logs as well
        $(document).on("click","#monitor_server_uptimes_data table tfoot a",function(e) {
            e.preventDefault();
            var id  = $('#monitor_server_uptimes_data .monitor_server_id').val();
            var url = $(this).attr('href');
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
            }).done( function(response) {
                $("#loading-image-preview").hide();
                if(response.code == 200) {
                    var html    =   '';
                    $.each(response.data.data,function(idnex, val){
                        html += '<tr><td>'+val.date+'</td><td>'+val.status+'</td><td>'+val.latency+'</td></tr>';
                    })

                    $('#monitor_server_uptimes_data table tbody').html('');
                    $('#monitor_server_uptimes_data table tbody').html(html);
                    $('#monitor_server_uptimes_data table tfoot').html('');
                    $('#monitor_server_uptimes_data table tfoot').html(response.paginate);
                    $('#monitor_server_uptimes').modal('show');
                    $("#monitor_server_uptimes").animate({ scrollTop: 0 }, "slow");

                } else{
                    toastr["error"]('Something went wrong!');
                }
            }).fail(function(errObj) {
                $("#loading-image-preview").hide();
            });

        });

        //Paginate the logs as well
        $(document).on("click","#monitor_server_users_data table tfoot a",function(e) {
            e.preventDefault();
            var id  = $('#monitor_server_users_data .monitor_server_id').val();
            var url = $(this).attr('href');
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
            }).done( function(response) {
                $("#loading-image-preview").hide();
                if(response.code == 200) {
                    var html    =   '';
                    $.each(response.data.data,function(idnex, val){
                            html += '<tr><td>'+val.user_name+'</td><td>'+val.name+'</td><td>'+val.mobile+'</td><td>'+val.email+'</td></tr>';
                        })

                    $('#monitor_server_users_data table tbody').html('');
                    $('#monitor_server_users_data table tbody').html(html);
                    $('#monitor_server_users_data table tfoot').html('');
                    $('#monitor_server_users_data table tfoot').html(response.paginate);
                    $('#monitor_server_users').modal('show');
                    $("#monitor_server_users").animate({ scrollTop: 0 }, "slow");

                } else{
                    toastr["error"]('Something went wrong!');
                }
            }).fail(function(errObj) {
                $("#loading-image-preview").hide();
            });

        });
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

    // Show server Logs history
    $(document).on("click",".show-server-history",function(e) {
            e.preventDefault();
            var $this = $(this);
            $.ajax({
                url: "{{ route('monitor-server.get-server-history', '') }}/" + $this.attr('data-id'),
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
            }).done( function(response) {
                    $("#loading-image-preview").hide();
                    if(response.code == 200) {
                        var html = '';
                        console.log(response);
                        $.each(response.data.data,function(idnex, val){
                            html += '<tr><td>'+val.type+'</td><td>'+val.message+'</td></tr>';
                        })

                        $('#monitor_server_history_data .monitor_server_id').val('');
                        $('#monitor_server_history_data .monitor_server_id').val($this.attr('data-id'));
                        $('#monitor_server_history_data table tbody').html('');
                        $('#monitor_server_history_data table tbody').html(html);
                        $('#monitor_server_history_data table tfoot').html('');
                        $('#monitor_server_history_data table tfoot').html(response.paginate);
                        $('#monitor_server_history').modal('show');

                    }else{
                        toastr["error"]('Something went wrong!');
                    }
            }).fail(function(errObj) {
                    $("#loading-image-preview").hide();
            });
        });

        //Paginate server Logs history
        $(document).on("click","#monitor-server.get-server-history tfoot a",function(e) {
            e.preventDefault();
            var id  = $('#monitor-server.get-server .monitor_server_id').val();
            var url = $(this).attr('href');
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
            }).done( function(response) {
                $("#loading-image-preview").hide();
                if(response.code == 200) {
                    var html    =   '';
                    $.each(response.data.data,function(idnex, val){
                        html += '<tr><td>'+val.type+'</td><td>'+val.message+'</td></tr>';
                    })

                    $('#monitor_server_history_data table tbody').html('');
                    $('#monitor_server_history_data table tbody').html(html);
                    $('#monitor_server_history_datatable tfoot').html('');
                    $('#monitor_server_history_data table tfoot').html(response.paginate);
                    $('#monitor_server_history').modal('show');
                    $("#monitor_server_history").animate({ scrollTop: 0 }, "slow");

                } else{
                    toastr["error"]('Something went wrong!');
                }
            }).fail(function(errObj) {
                $("#loading-image-preview").hide();
            });

        });
</script>
@endsection