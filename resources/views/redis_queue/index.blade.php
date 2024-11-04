@extends('layouts.app')

@section('favicon', 'password-manager.png')

@section('title', 'Queues')

@section('styles')

    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
    </style>
@endsection


@section('content')
    <div id="myDiv">
        <img id="loading-image" src="{{asset('/images/pre-loader.gif')}}" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Queues ({{count($queues)}})</h2>

            @if (Auth::user()->hasRole('Admin'))
                <div class="row mb-3 p-0">
                <div class="col-md-12">
                    <div class="pull-left">
                        <form class="form-inline" action="{{route('redisQueue.list')}}" method="GET">
                            <div class="col-5 pl-2 pr-0">
                                <div class="form-group">
                                    <div class='input-group'>
                                        <input type='text' placeholder="Name.." class="form-control" name="name"  value="{{ isset($_GET['name'])?$_GET['name']:''}}"  />
                                    </div>
                                </div>
                            </div>
                            <div class="col-5 pl-2 pr-0">
                                <div class="form-group">
                                    <div class='input-group'>
                                        <select name="type" id="type" class="form-control">
                                            <option value="">Select Type..</option>
                                            <option value="WEBPUSHQUEUE" {{ !empty(Request::get('type'))?'selected':''}}>WEBPUSHQUEUE</option>
                                            <option value="MAINQUEUE" {{ !empty(Request::get('type'))?'selected':''}}>MAINQUEUE</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2 pl-2">
                                <button type="submit" class="btn btn-image"><img src="{{asset('/images/filter.png')}}" /></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            <div class="row m-auto">
                <div class="p-2 ml-4"><b>Horizon Commands</b></div>
                <div class="col-md-6">
                    <button type="button" onclick="horizonRun('horizon:status')" class="btn btn-secondary" title="Horizon status">
                        <i class="fa fa-line-chart" aria-hidden="true"></i>
                    </button>                    
                    <button type="button" onclick="horizonRun('horizon')" class="btn btn-secondary" title="Horizon run">
                        <i class="fa fa-play" aria-hidden="true"></i>
                    </button>
                    <button type="button" onclick="horizonRun('horizon:pause')" class="btn btn-secondary" title="Horizon pause">
                        <i class="fa fa-pause" aria-hidden="true"></i>
                    </button>
                    <button type="button" onclick="horizonRun('horizon:continue')" class="btn btn-secondary" title="Horizon continue">
                        <i class="fa fa-repeat" aria-hidden="true"></i>
                    </button>
                    <button type="button" onclick="horizonRun('horizon:terminate')" class="btn btn-secondary" title="Horizon terminate">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </button>
                    <button type="button" onclick="horizonRun('horizon:clear')" class="btn btn-secondary" title="Horizon clear">
                        <i class="fa fa-eraser" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="col-md-2 ml-auto text-right">
                    <button type="button" class="btn btn-secondary" data-toggle="modal"
                            data-target="#queueCreateModal">+
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="syncQueues()" title="Sync queue with queue file">
                        <i class="fa fa-refresh" aria-hidden="true"></i>
                    </button>
                    &nbsp
                </div>
            </div>

        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
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
    <div class="col-md-12">

    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="queue-table">
            <thead>
            <tr>
                <th width="5%">#</th>
                <th>Name</th>
                <th>Type</th>
                <th width="12%" class="text-center">Execution</th>
                <th width="7%" class="text-center">Actions</th>
            </tr>
            </thead>
            <tbody>
            @include('redis_queue.data')

            {!! $queues->render() !!}

            </tbody>
        </table>
    </div>

    <div id="queueCreateModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <form action="{{ route('redisQueue.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h4 class="modal-title">Create Queue</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" class="form-control queue-name" value="{{ old('name') }}">

                            @if ($errors->has('name'))
                                <div class="alert alert-danger">{{ $errors->first('name') }}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <strong>Type:</strong>
                            <select class="form-control" id="queueType" name="type">
                                <option value="WEBPUSHQUEUE" @if(old('type') == "WEBPUSHQUEUE") selected @endif>Web Push
                                    Queue
                                </option>
                                <option value="MAINQUEUE" @if(old('type') == "MAINQUEUE") selected @endif>Main Queue
                                </option>
                            </select>
                            @if ($errors->has('type'))
                                <div class="alert alert-danger">{{ $errors->first('type') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Store</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <div id="queueUpdateModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <form action="{{ route('redisQueue.update') }}" method="POST" id="editqueue">
                    @csrf
                    <input type="hidden" name="id" id="queueId"/>
                    <div class="modal-header">
                        <h4 class="modal-title">Update Todo List</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" class="form-control edit-name" value="{{ old('name') }}">

                            @if ($errors->has('name'))
                                <div class="alert alert-danger">{{ $errors->first('name') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <strong>Type:</strong>
                            <select class="form-control edit-type" id="queueType" name="type">
                                <option value="WEBPUSHQUEUE">Web Push Queue</option>
                                <option value="MAINQUEUE">Main Queue</option>
                            </select>
                            @if ($errors->has('type'))
                                <div class="alert alert-danger">{{ $errors->first('type') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Update</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <div id="commandLogsModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h3 class="modal-title">Queue command execution logs</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Queue</th>
                            <th scope="col">Executed By</th>
                            <th scope="col">Command</th>
                            <th scope="col">Server IP</th>
                            <th scope="col">Response</th>
                            <th scope="col">Date</th>
                        </tr>
                        </thead>
                        <tbody id="logData">


                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    </div>

@endsection


@section('scripts')

    <script>


        function editQueue(id) {
            let $this = $(this);
            var id = id;
            $('#queueId').val(id);
            $.ajax({
                url: "{{ route('redisQueue.edit') }}",
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id
                }
            }).done(function (response) {
                if (response.code == '200') {
                    form = $('#editQueue');
                    $('.edit-name').val(response.data.name);
                    $('.edit-type').val(response.data.type);
                    $('#queueUpdateModal').modal('show');
                } else {
                    toastr['error'](response.message, 'error');
                }
            }).fail(function (errObj) {
                $('#loading-image').hide();
                $("#queueUpdateModal").hide();
                toastr['error'](errObj.message, 'error');
            });
            // });
        }

        function deleteQueue(id) {
            let $this = $(this);
            var id = id;
            if (confirm("Are you sure you want to delete this?")) {
                $.ajax({
                    url: "{{ route('redisQueue.delete') }}",
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: id
                    }
                }).done(function (response) {
                    if (response.code == '200') {
                        $('#trId-' + id).remove();
                        toastr['success'](response.message, 'success');
                    } else {
                        toastr['error'](response.message, 'error');
                    }
                }).fail(function (errObj) {
                    toastr['error'](errObj.message, 'error');
                });
            } else {
                return false;
            }
        }

        function queueRun(id, command) {
            if (confirm("Are you sure you want to run this command?")) {
                $.ajax({
                    url: "{{ route('redisQueue.execute') }}",
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: id,
                        command_tail: command
                    }
                }).done(function (response) {
                    if (response.code == '200') {
                        toastr['success'](response.message, 'success');
                    } else {
                        toastr['error'](response.message, 'error');
                    }
                }).fail(function (errObj) {
                    toastr['error'](errObj.message, 'error');
                });
            } else {
                return false;
            }
        }

        function horizonRun(command) {
            if (confirm("Are you sure you want to run this command?")) {
                $.ajax({
                    url: "{{ route('redisQueue.executeHorizon') }}",
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        command_tail: command
                    }
                }).done(function (response) {
                    if (response.code == '200') {
                        toastr['success'](response.message, 'success');
                    } else {
                        toastr['error'](response.message, 'error');
                    }
                }).fail(function (errObj) {
                    toastr['error'](errObj.message, 'error');
                });
            } else {
                return false;
            }
        }

        function queueCommandLogs(id) {
            $.ajax({
                url: "{{ url('system-queue/command-logs') }}" + "/" + id,
                type: "get",
            }).done(function (response) {
                if (response.code == '200') {
                    let html = '';
                    $.each(response.data, function (key, val) {
                        console.log(val);
                        html += '<tr><td>' + val.id + '</td>' +
                            '<td>' + val.queue.name + '</td>' +
                            '<td>' + val.user.name + '</td>' +
                            '<td>' + val.command + '</td>' +
                            '<td>' + val.server_ip + '</td>' +
                            '<td>' + val.response + '</td>' +
                            '<td>' + val.created_at + '</td>' +
                            '</tr>';
                    });
                    console.log(html);
                    $('#logData').html(html);
                    $('#commandLogsModal').modal('show');
                } else {
                    toastr['error']('Something went wrong!', 'error');
                }
            }).fail(function (errObj) {
                toastr['error'](errObj.message, 'error');
            });
        }

        function syncQueues() {
            $.ajax({
                url: "{{ route('redisQueue.sync') }}",
                type: "get",
            }).done(function (response) {
                if (response.code == '200') {
                    toastr['success'](response.message, 'Success');
                } else {
                    toastr['error']('Something went wrong!', 'error');
                }
            }).fail(function (errObj) {
                toastr['error'](errObj.message, 'error');
            });
        }

    </script>
@endsection
