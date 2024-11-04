@extends('layouts.app')

@section('title', 'Meeting Records Info')

@section('styles')
    <style>
        .update_description {
            margin-top: 0.50rem;
        }

        .float-right-addbtn {
            float: right !important;
            margin-top: 1%;
            margin-right: 0.095rem;
        }

        /* CSS to center the video */
        .video-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        /* Optional: Style the video container if needed */
        .video-container video {
            max-width: 100%;
            max-height: 100%;
        }
    </style>
@endsection
@section('content')
    <br>
    <div class="table-responsive">
        <table class="table table-bordered" id="users-table">
            <thead>
                <tr>
                    <th style="width:5%;">No</th>
                    <th style="width:20%;">File Name</th>
                    <th style="width:15%;">Description</th>
                    <th style="width:10%;">Recording Deleted At</th>
                    <th style="width:10%;">Created At</th>
                    <th style="width:15%;">Action</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $i = 0;
                    $base_url = config('env.APP_URL');
                @endphp

                @foreach ($zoomRecordings as $key => $meeting)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $meeting->file_name }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <input type="text" name="description" class="form-control description"
                                    placeholder="Description" value={{ $meeting->description ?? '' }}>
                                <button class="btn btn-xs btn-image update_description" data-id="{{ $meeting->id }}"><i
                                        class="fa fa-pencil"></i></button>
                                <button type="button" class="btn btn-xs btn-image load-description-history"
                                    data-id="{{ $meeting->id }}" data-type="description" title="view History">
                                    <i class="fa fa-info-circle"></i>
                                </button>
                            </div>
                        </td>
                        <td>{{ $meeting->recording_deleted_at }}</td>
                        <td>{{ $meeting->created_at }}</td>
                        <td>
                            @php
                                $userRecordPermission = $meeting->user_record_permission ? json_decode($meeting->user_record_permission) : null;
                            @endphp
                            @if (($userRecordPermission && in_array(Auth::user()->id, $userRecordPermission)) || Auth::user()->isAdmin())
                                <button type="button" class="btn btn-xs btn-image load-video-preview"
                                    data-id="{{ $meeting->id }}" title="preview">
                                    <img src="/images/view.png" style="cursor: default;">
                                </button>
                            @endif
                            @if (Auth::user()->isAdmin())
                                <button type="button" class="btn btn-xs btn-image addPermissionButton" data-toggle="modal"
                                    data-recordId="{{ $meeting->id }}" title="Add user permissions"
                                    data-target="#userPermissionModal">
                                    <img src="/images/add.png">
                                </button>
                                <a class="btn btn-xs btn-image"
                                    href="{{ route('meeting.download.file', ['id' => $meeting->id]) }}"
                                    title="Downlaod Video"><i class="fa fa-download"></i></a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="userPermissionModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add User Recording View Permission</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{ route('meeting.add.user.permission') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="record_id" id="record_id">
                        <div class="form-group custom-select2">
                            <label>Add Permission for Users
                            </label>
                            @php
                                $users = \App\User::select('id', 'name', 'email', 'gmail')
                                    ->whereNotNull('gmail')
                                    ->get();
                            @endphp
                            <select class="form-control select2" id="user_record_permission" multiple="multiple"
                                name="user_record_permission[]">
                                @foreach ($users as $val)
                                    <option value={{ $val->id }} class="form-control">{{ $val->name }}</option>
                                @endforeach
                            </select>
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

    <div id="zoom-record-video-listing" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Recording Video</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="video-container">
                        <video width="640" height="360" controls autoplay>
                            <source src="" type="video/mp4">
                        </video>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="zoom-record-description-listing" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Zoom record Description History</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="25%">Old description</th>
                                    <th width="25%">New description</th>
                                    <th width="25%">Updated by</th>
                                    <th width="25%">Created Date</th>
                                </tr>
                            </thead>
                            <tbody class="zoom-record-description-listing-view">
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
@endsection

@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js">
    </script>
    <script type="text/javascript">
        $('.select2').select2();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).on("click", ".update_description", function() {
            var meetingId = $(this).attr('data-id');
            var description = $(this).parents('td').find('.description').val();
            if (description != '') {
                $.ajax({
                    type: "POST",
                    url: "{{ route('meeting.description.update') }}",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        id: meetingId,
                        description: description
                    },
                    success: function(response) {
                        if (response.code == 200) {
                            toastr['success'](response.message, 'success');
                        } else {
                            toastr['error'](response.message, 'error');
                        }
                    }
                });
            } else {
                toastr['success'](response.message, 'success');
            }
        });

        $(document).ready(function() {

            $(".addPermissionButton").click(function() {
                var recordId = $(this).data("recordid");
                $("#record_id").val(recordId);
            });


            $("form").submit(function(event) {
                event.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr("action"), // Form action URL
                    type: $(this).attr("method"), // Form method (POST)
                    data: formData,
                    success: function(response) {
                        if (response.code == 200) {
                            toastr['success'](response.message, 'success');
                        } else {
                            toastr['error'](response.message, 'error');
                        }
                        $("#userPermissionModal").modal("hide");
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            });
        });


        $(document).on('click', '.load-description-history', function() {
            var id = $(this).attr('data-id');
            var type = $(this).attr('data-type');
            $.ajax({
                url: '{{ route('recording.description.show') }}',
                dataType: "json",
                data: {
                    id: id,
                    type: type,
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.oldvalue ? v.oldvalue: ''} </td>
                                        <td> ${v.newvalue ? v.newvalue : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${new Date(v.created_at).toISOString().slice(0, 10)} </td>
                                    </tr>`;
                        });
                        $("#zoom-record-description-listing").find(
                            ".zoom-record-description-listing-view").html(html);
                        $("#zoom-record-description-listing").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });


        function clearVideo() {
            var videoElement = $("#zoom-record-video-listing").find("video");
            videoElement.attr("src", "");
            videoElement[0].pause();
        }

        $(document).on('click', '.load-video-preview', function() {
            var id = $(this).attr('data-id');
            var modal = $("#zoom-participant-record-video-listing");
            // Clear the video when the modal is closed
            modal.on('hidden.bs.modal', function() {
                clearVideo();
            });

            $("#loading-image").show();

            $.ajax({
                url: '{{ route('recording.video.show') }}',
                dataType: "json",
                data: {
                    id: id,
                },
                success: function(response) {
                    if (response.status) {
                        $("#zoom-record-video-listing").modal("show");
                        var videoElement = $("#zoom-record-video-listing").find("video");
                        videoElement.attr("src", response.videoUrl);
                        videoElement[0].play(); // Start playback if needed
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                },
                error: function(response) {
                    $("#loading-image").hide();
                    toastr["error"](response.responseJSON.error, "Message");
                }
            });
        });
    </script>

@endsection
