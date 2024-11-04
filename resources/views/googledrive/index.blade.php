@extends('layouts.app')
@section('favicon' , '')

@section('title', 'Google Drive')

@section('styles')
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
    </style>
@endsection

@section('content')
    <div class="col-md-12">

    <div id="myDiv">
        <img id="loading-image" src="{{asset('/images/pre-loader.gif')}}" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Google Drive</h2>
            <div class="pull-right mb-3">
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#createGoogleDriveModal">
                    + Create Drive
                </button>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered" id="googlefiletranslator-table">
            <thead>
            <tr>
                <th>No</th>
                <th>Date</th>
                <th>Dev Task</th>
                <th>User Module</th>
                <th>Uploaded File</th>
                <th>Remarks</th>
                <th>Created At</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($google_drivedata as $key => $drive_data)
                @php
                    $images = explode(",",$drive_data->upload_file);
                    $task_types = \App\Task::TASK_TYPES;
                @endphp
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $drive_data->date }}</td>
                    <td>{{!empty($drive_data->dev_task) ? $task_types[$drive_data->dev_task] : '' }}</td>
                    <td>{{ $drive_data->user_module }}</td>
                    <td>
                    @foreach ($images as $key => $image)
                        @php
                                   $filename = pathinfo($image, PATHINFO_FILENAME);
                                   $extension = pathinfo( $image, PATHINFO_EXTENSION );
                                   $new_image = asset($image);
                        @endphp
                        @if(!empty($extension == 'mp4'))
                            <img src="{{asset('images/playvideo.png')}}" height="100" width="100" onclick="Videoplay('{{$new_image}}')">
                        @else
                            <img src="{{$new_image}}"  height="100" width="100">
                        @endif
                    @endforeach
                    </td>
                    <td>{{ $drive_data->remarks }}</td>
                    <td>{{ $drive_data->created_at }}</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>

        <div id="createGoogleDriveModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Create Google Drive</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <form action="{{ route('google-drive.create') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="modal-body">
                            <div class="form-group">
                                <strong>Date:</strong>
                                <input type="date" name="google_drive_date" value="{{ old('google_drive_date') }}" class="form-control input-sm" placeholder="Drive Date" required>
                            </div>

{{--                            <div class="form-group">--}}
{{--                                <strong>Dev Task:</strong>--}}
{{--                                <select class="form-control" name="dev_task">--}}
{{--                                    <option>Dev Task</option>--}}
{{--                                    @foreach($developer_task as $task)--}}
{{--                                        <option value="{{ $task->task }}" {{ Request::get('dev_task') && in_array($task->task, Request::get('dev_task')) ? 'selected' : '' }}>{{$task->task}}</option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                                --}}
{{--                                --}}

{{--                                @if ($errors->has('dev_task'))--}}
{{--                                    <div class="alert alert-danger">{{$errors->first('dev_task')}}</div>--}}
{{--                                @endif--}}
{{--                            </div>--}}

                            <div class="form-group">
                                <label for="task_type">Task Type</label>
                                {{ html()->select("dev_task", \App\Task::TASK_TYPES)->class("form-control select2-vendor type-on-change")->style("width:100%;") }}
                            </div>

                            <div class="form-group">
                                <strong>User Module:</strong>
                                <select class="form-control" name="user_module">
                                    <option>User Module</option>
                                    @foreach($user_name as $user)
                                        <option value="{{ $user->name }}" {{ Request::get('user_module') && in_array($user->name, Request::get('user_module')) ? 'selected' : '' }}>{{$user->name}}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('user_module'))
                                    <div class="alert alert-danger">{{$errors->first('user_module')}}</div>
                                @endif
                            </div>

                            <div class="form-group">
                                <strong>Remarks:</strong>
                                <textarea id="remarks" name="remarks" rows="4" cols="64" value="{{ old('remarks') }}" placeholder="Remarks"></textarea>

                                @if ($errors->has('remarks'))
                                    <div class="alert alert-danger">{{$errors->first('remarks')}}</div>
                                @endif
                            </div>

                            <div class="form-group">
                                <strong>Uploaded File:</strong>
                                <input type="file" name="upload_file[]" value="{{ old('upload_file') }}" class="form-control input-sm" placeholder="Upload File" style="height: fit-content;" multiple >

                                @if ($errors->has('upload_file'))
                                    <div class="alert alert-danger">{{$errors->first('upload_file')}}</div>
                                @endif
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-secondary">Create</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>

    <div id="video_play" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>video</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;  </button>
                </div>
                <div class="modal-body">
                    <video  id="v1" width="100%" height="100%" controls="controls">

                    </video>
                </div>
            </div>
        </div>
    </div>

@endsection

<script>
    function Videoplay(image){
        $('#video_play').modal('show');
        $("#v1").html('<source src=" '+ image +' " type="video/mp4"></source>' );
    }
</script>
