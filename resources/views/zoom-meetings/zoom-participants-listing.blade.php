@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Zoom Participants ({{$zoomParticipants->total()}})</h2>
		</div>
	</div>
	<div class="col-md-12">
		<form action="{{route('list.all-participants')}}" method="get" class="search">
			<div class="col-md-2">
                <h5> <b>Search Name </b></h5>
                <?php 
                    if(request('name')){   $search_name = request('name'); }
                    else{ $search_name = []; }
                ?>
                <select name="name[]" id="name" class="form-control select2" multiple style="max-width: 100%;">
                    <option value="" @if($search_name=='') selected @endif>-- Select a name--</option>
                    @forelse($allNames as $swId => $name)
                    <option value="{{ $name }}" @if(in_array($name, $search_name)) selected @endif>{!! $name !!}</option>
                    @empty
                    @endforelse
                </select>
            </div>
            <div class="col-md-2">
                <h5> <b> Search Email</b> </h5>
                <?php 
                    if(request('email')){   $search_email = request('email'); }
                    else{ $search_email = []; }
                ?>
                <select name="email[]" id="email" class="form-control select2" multiple style="max-width: 100%;">
                    <option value="" @if($search_email=='') selected @endif>-- Select a Email--</option>
                    @forelse($allEmails as $swId => $email)
                    <option value="{{ $email }}" @if(in_array($email, $search_email)) selected @endif>{!! $email !!}</option>
                    @empty
                    @endforelse
                </select>
            </div>            
			<div class="col-lg-2">
                <h5><b> Search Reason</b></h5>
				<input class="form-control" type="text" id="search_reason" placeholder="Search Reason" name="search_reason" value="{{ (request('search_reason') ?? "" )}}">
			</div>
            <div class="col-lg-2">
                <h5><b> Search Duration</b></h5>
				<input class="form-control" type="text" id="duration" placeholder="Search Duration" name="duration" value="{{ (request('duration') ?? "" )}}">
			</div>
            <div class="col-lg-2">
                <h5> <b> Search Join Date </b></h5>
				<input class="form-control" type="date" name="join_time" value="{{ (request('join_time') ?? "" )}}">
			</div>
            <div class="col-lg-2">
                <h5> <b> Search Leave Date </b></h5>
				<input class="form-control" type="date" name="leave_time" value="{{ (request('leave_time') ?? "" )}}">
			</div>
			<div class="col-lg-2"><br>
                <h5> <b> Search Created At </b></h5>
				<input class="form-control" type="date" name="date" value="{{ (request('date') ?? "" )}}">
			</div>

			<div class="col-lg-2"><br><br><br>
				<button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
				   <img src="{{ asset('images/search.png') }}" alt="Search">
			   </button>
			   <a href="{{route('list.all-participants')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
			</div>
		</form>
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th width="3%">ID</th>
			    	<th width="10%">Name</th>
			        <th width="10%">Email</th>
                    <th width="10%">Join Time</th>
                    <th width="10%">Leave Time</th>
			        <th width="10%">Leave Reason</th>
                    <th width="20%">Description</th>
					<th width="10%">Recording Path</th>
                    <th width="10%">Created At</th>
                    <th width="5%">Action</th>
                </tr>
		    	<tbody>
                    @foreach ($zoomParticipants as $key=>$data)
                        <tr>
                            <td>{{$data->id}}</td>
                            <td>{{$data->name}}</td>
                            <td>{{$data->email}}</td>
                            <td>{{$data->join_time}}</td>
                            <td>{{$data->leave_time}}</td>
                            <td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                   {{ strlen($data->leave_reason) > 15 ? substr($data->leave_reason, 0, 15).'...' :  $data->leave_reason }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $data->leave_reason }}
                                </span>
                            </td>							
                            <td>
                                <div class="d-flex align-items-center">
                                    <input type="text" name="description" class="form-control description" placeholder="Description" value="{{ ($data->description ?? "" )}}">
                                    <button class="btn btn-xs btn-image update_description" data-id="{{ $data->id }}"><i class="fa fa-pencil"></i></button>
                                    <button type="button" class="btn btn-xs btn-image load-description-history" data-id="{{ $data->id }}" data-type="description" title="view History" onclick="viewHistories()">
                                        <i class="fa fa-info-circle"></i>
                                    </button>
                                 </div>
                            </td>
                            <td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                   {{ strlen($data->recording_path) > 15 ? substr($data->recording_path, 0, 15).'...' :  $data->recording_path }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{$data->recording_path}}
                                </span>
                            </td>
                            <td>{{ $data->created_at?->format('Y-m-d H:i:s') }}</td>
                            <td>
                                @php
                                    $userRecordPermission = $data->recording?->user_record_permission ? json_decode($data->recording->user_record_permission) : null;
                                @endphp
                                @if ($userRecordPermission && in_array(Auth::user()->id, $userRecordPermission) || Auth::user()->isAdmin())
                                <button type="button" class="btn btn-xs btn-image load-video-preview" data-id="{{ $data->id }}" title="preview">
                                    <img src="/images/view.png" style="cursor: default;">
                                </button>
                                @endif
                            </td>
						</tr>                        
                    @endforeach
		    	</tbody>
		    </thead>
		</table>
		{!! $zoomParticipants->appends(Request::except('page'))->links() !!}
	</div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
</div>
<div class="modal" tabindex="-1" role="dialog" id="show_full_log_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Full Log</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="show_full_log_modal_content">
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="zoom-participant-description-listing" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Zoom Participant Description History</h4>
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
                        <tbody class="zoom-participant-description-listing-view">
                        </tbody>
                    </table>
                    <div class="pagination-container-description"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div id="zoom-participant-record-video-listing" class="modal fade" role="dialog">
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

@endsection

@section("styles")
<style>
    /* CSS to make specific modal body scrollable */
    #show_full_log_modal .modal-body {
      max-height: 400px; /* Maximum height for the scrollable area */
      overflow-y: auto; /* Enable vertical scrolling when content exceeds the height */
    }
</style>

@endsection

@section('scripts')
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>

    $('.select2').select2();

    $(document).ready(function() 
	{

        $(document).on('click', '.expand-row', function () {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });
   
    });

    $(document).on("click", ".update_description", function(){
        var participantId = $(this).attr('data-id');
        var description = $(this).parents('td').find('.description').val();
        if(description != ''){
            $.ajax({
                type: "POST",
                url: "{{ route('participant.description.update') }}",
                data: {'_token': "{{ csrf_token() }}",id:participantId,description:description},
                success: function(response) {
                if(response.code == 200){
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


    function viewHistories(pageNumber = 1) {
        var id = $('.load-description-history').attr('data-id');
        var type = $('.load-description-history').attr('data-type');

        $.ajax({
            url: '{{ route("participant.description.show") }}',
            dataType: "json",
            data: {
                id: id,
                type: type,
                page:pageNumber,
            },
            success: function (response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data.data, function (k, v) {
                        html += `<tr>
                                    <td>${k + 1}</td>
                                    <td>${v.oldvalue ? v.oldvalue : ''}</td>
                                    <td>${v.newvalue ? v.newvalue : ''}</td>
                                    <td>${(v.user !== undefined) ? v.user.name : ' - '}</td>
                                    <td> ${new Date(v.created_at).toISOString().slice(0, 19)} </td>
                                </tr>`;
                    });
                    $("#zoom-participant-description-listing").find(".zoom-participant-description-listing-view").html(html);
                    $("#zoom-participant-description-listing").modal("show");
                    renderzoomPagination(response);
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    }


    function renderzoomPagination(response) {
        var paginationContainer = $(".pagination-container-description");
        var currentPage = response.data.current_page;
        console.log('Current Page :',currentPage);
        var totalPages = response.data.last_page;
        console.log('Total pages:',totalPages);
        var html = "";
        var maxVisiblePages = 10;

        if (totalPages > 1) {
            html += "<ul class='pagination'>";
            if (currentPage > 1) {
            html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='historiesPage(" + (currentPage - 1) + ")'>Previous</a></li>";
            }
            var startPage = 1;
            var endPage = totalPages;

            if (totalPages > maxVisiblePages) {
                if (currentPage <= Math.ceil(maxVisiblePages / 2)) {
                    endPage = maxVisiblePages;
                } else if (currentPage >= totalPages - Math.floor(maxVisiblePages / 2)) {
                    startPage = totalPages - maxVisiblePages + 1;
                } else {
                    startPage = currentPage - Math.floor(maxVisiblePages / 2);
                    endPage = currentPage + Math.ceil(maxVisiblePages / 2) - 1;
                }

                if (startPage > 1) {
                    html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='historiesPage(1)'>1</a></li>";
                    if (startPage > 2) {
                        html += "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                    }
                }
            }

            for (var i = startPage; i <= endPage; i++) {
                html += "<li class='page-item " + (currentPage == i ? "active" : "") + "'><a class='page-link' href='javascript:void(0);' onclick='historiesPage(" + i + ")'>" + i + "</a></li>";
            }
            html += "</ul>";
        }
        paginationContainer.html(html);
    }

    function historiesPage(pageNumber) {
        viewHistories(pageNumber);
    }

    // Function to clear the video source and stop playback
    function clearVideo() {
        var videoElement = $("#zoom-participant-record-video-listing").find("video");
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
            url: '{{ route("participant-recording.video.show") }}',
            dataType: "json",
            data: {
                id: id,
            },
            success: function(response) {
                $("#loading-image").hide();
                if (response.status) {
                    modal.modal("show");
                    var videoElement = modal.find("video");
                    videoElement.attr("src", response.videoUrl);
                    videoElement[0].play(); // Start playback if needed
                    modal.modal("show");
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
    