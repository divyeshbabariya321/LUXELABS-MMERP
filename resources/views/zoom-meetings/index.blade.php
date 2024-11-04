@extends('layouts.app')
@section('title', 'Meetings')
@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}

    {{-- <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" /> --}}
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->

    <style>
        #message-wrapper {
            height: 450px;
            overflow-y: scroll;
        }

        .gap-5 {
            gap: 5px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 text-center">
            <h2 class="page-heading"> Meetings</h2>
        </div>
            <div class="mt-3 col-md-12">
                <form action="{{route('meetings.all.data')}}" method="get" class="search">
                    <div class="col-md-2 pd-sm">
                        <h5><b>Search Meeting Id </b></h5>
                        <?php 
                            if(request('meeting_ids')){   $meeting_search = request('meeting_ids'); }
                            else{ $meeting_search = []; }
                        ?>
                        <select name="meeting_ids[]" id="meeting_ids" class="form-control select2" multiple>
                            <option value="" @if($meeting_search=='') selected @endif>-- Select a Meeting ids --</option>
                            @forelse($zoomMeetingIds as $swId => $zoomMeetingId)
                            <option value="{{ $zoomMeetingId }}" @if(in_array($zoomMeetingId, $meeting_search)) selected @endif>{!! $zoomMeetingId !!}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>
                    <div class="col-md-2 pd-sm">
                        <h5><b>Search Time Zone</b></h5>
                        <?php 
                        if(request('time_zone')){   $time_zone_search = request('time_zone'); }
                        else{ $time_zone_search = []; }
                    ?>
                        <select name="time_zone[]" id="time_zone" class="form-control select2" multiple>
                            @forelse($timeZones as $id => $timeZone)
                                <option value="{{ $timeZone }}" @if(!empty($time_zone_search) && in_array($timeZone, $time_zone_search)) selected @endif>{!! $timeZone !!}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>            
                    <div class="col-lg-2">
                        <h5><b> Search Meeting Topic</b></h5>
                        <input class="form-control" type="text" name="meeting_topic"  id="meeting_topic" placeholder="Search Meeting Topic" value="{{ (request('meeting_topic') ?? "" )}}">
                    </div>
                    <div class="col-lg-2">
                        <h5><b> Search Meeting Agenda </b></h5>
                        <input class="form-control" type="text" id="meeting_agenda" placeholder="Search Meeting Agenda" name="meeting_agenda" value="{{ (request('meeting_agenda') ?? "" )}}">
                    </div>
                    <div class="col-lg-2">
                        <h5><b> Search Meeting Duration </b></h5>
                        <input class="form-control" type="text" id="duration" placeholder="Search Meeting Duration" name="duration" value="{{ (request('duration') ?? "" )}}">
                    </div>
                    <div class="col-lg-2">
                        <h5><b> Search Start Date </b></h5>
                        <input class="form-control" type="date" name="date" value="{{ (request('date') ?? "" )}}">
                    </div>
        
                    <div class="col-lg-2"><br><br>
                        <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                           <img src="{{ asset('images/search.png') }}" alt="Search">
                       </button>
                       <a href="{{route('meetings.all.data')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                    </div>
                </form>
        </div>
    </div>
    <div class="clearboth"></div>
    <div class="row" style="margin:10px;">
        <!-- <h4>List Of Upcoming Meetings</h4> -->
        <div class="col-lg-12">
            <div class=" pull-right">
                <a href="{{ route('list.all-participants') }}" target="_blank" class="btn btn-secondary">View All Participations</a>&nbsp;
                <a href="{{ route('meeting.list.error-logs') }}" target="_blank" class="btn btn-secondary"> View Api Logs</a>                
            </div>
        </div>
        <div class="col-lg-12 margin-tb">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Meeting Id</th>
                        <th>Description</th>
                        <th>Meeting Topic</th>
                        <th>Meeting Agenda</th>
                        <th>Join Meeting URL</th>
                        <th>Start Date Time</th>
                        <th>Meeting Duration</th>
                        <th>Timezone</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($meetingData)
                        @foreach($meetingData as $meetings)
                            <tr>
                                <td class="p-2">{{ $meetings->id }}</td>
                                <td class="p-2">{{ $meetings->meeting_id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <input type="text" name="description" class="form-control description" placeholder="Description" value={{ ($meetings->description ?? "" )}}>
                                        <button class="btn btn-xs btn-image update_description_meeting" data-id="{{ $meetings->id }}">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-xs btn-image load-description-meeting-history" data-id="{{ $meetings->id }}" data-type="description" title="view History">
                                            <i class="fa fa-info-circle"></i>
                                        </button>
                                    </div>
                                </td>
                                
                                <td class="p-2">{{ $meetings->meeting_topic }}</td>
                                <td class="p-2">{{ $meetings->meeting_agenda }}</td>
                                <td class="p-2"><a href="{{ $meetings->join_meeting_url }}" target="_blank">Link</a></td>
                                <td class="p-2">
                                    @if ($meetings->start_date_time != '0000-00-00 00:00:00')
                                        {{ Carbon\Carbon::parse($meetings->start_date_time)->format('M, d-Y H:i') }}
                                    @else
                                        {{ "-" }}
                                    @endif
                                </td>
                                <td class="p-2">{{ $meetings->meeting_duration }} mins</td>
                                <td class="p-2">{{ $meetings->timezone }}</td>
                                <td class="p-2">                                    
                                    <a href="{{ route('meeting.list.recordings', ['id' => $meetings->meeting_id]) }}" target="_blank" title="view Recordings Details">
                                        <i class="fa fa-video-camera" style="color: #808080;"></i>
                                    </a>  
                                    
                                    <button type="button" class="btn btn-xs Participants"
                                        data-meeting_id="{{ $meetings->meeting_id }}" title="view Participants" onclick="viewParticipants()">
                                            <i class="fa fa-users" style="color: #808080;"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            {!! $meetingData->appends(request()->except('page'))->links() !!}
        </div>
    </div>
@include('zoom-meetings.personal-meeting-update-modal')
@endsection

<div id="participants-list-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 class="modal-title"><b>Participants Lists</b></h4>
                </div>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-12" id="participants-list-modal-html">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="zoom-meeting-description-listing" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Zoom Meeting Description History</h4>
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
                        <tbody class="zoom-meeting-description-listing-view">
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

@section('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script> --}}
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script> --}}

    <script type="text/javascript">
        $('.select2').select2();

        $(document).on('click', '#sync_meetings', function(e){
            $("#loading-image-preview").show();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('vendor.meetings.recordings.sync') }}",          
                success: function(response) {
                    $("#loading-image-preview").hide();
                    toastr['success'](response.message, 'success');
                    window.location.reload();
                }
            });
        });

        $('.fetch-zoom-meeting-recordings').click(function() {
            var $this = $(this);
            var meetingId = $this.data('meeting_id');

            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('meeting.fetch.recordings')}}",
                type: 'POST',
                data: {
                    meetingId : meetingId,
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
            }).done(function(response) {
                $("#loading-image-preview").hide();
                toastr["success"](response.message);
            }).fail(function(errObj) {
                $("#loading-image-preview").hide();
                toastr["error"](errObj.responseJSON.message);
            });
        });

        $('.fetch-zoom-meeting-participants').click(function() {
            var $this = $(this);
            var meetingId = $this.data('meeting_id');

            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('meeting.fetch.participants')}}",
                type: 'POST',
                data: {
                    meetingId : meetingId,
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
            }).done(function(response) {
                $("#loading-image-preview").hide();
                toastr["success"](response.message);
            }).fail(function(errObj) {
                $("#loading-image-preview").hide();
                toastr["error"](errObj.responseJSON.message);
            });
        });

        function viewParticipants(pageNumber = 1) {
            var button = document.querySelector('.btn.btn-xs.Participants'); 
            var meetingId = button.getAttribute('data-meeting_id');

                $.ajax({
                    url: "{{route('meeting.list.participants')}}",
                    type: 'GET',
                    dataType: "json",
                    data: {
                        meetingId: meetingId,
                        page:pageNumber,
                    },
                    beforeSend: function() {
                    $("#loading-image-preview").show();
                }
                }).done(function(response) {
                    $('#participants-list-modal-html').empty().html(response.html);
                    $('#participants-list-modal').modal('show');
                    renderdomainPagination(response.data);
                    $("#loading-image-preview").hide();
                }).fail(function(response) {
                    $('.loading-image-preview').show();
                    console.log(response);
                });
        }

        function renderdomainPagination(response) {
            var paginationContainer = $(".pagination-container-participation");
            var currentPage = response.current_page;
            var totalPages = response.last_page;
            var html = "";
            var maxVisiblePages = 10;

            if (totalPages > 1) {
                html += "<ul class='pagination'>";
                if (currentPage > 1) {
                html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changeParticipantsPage(" + (currentPage - 1) + ")'>Previous</a></li>";
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
                    html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changeParticipantsPage(1)'>1</a></li>";
                    if (startPage > 2) {
                    html += "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                    }
                }
                }

                for (var i = startPage; i <= endPage; i++) {
                html += "<li class='page-item " + (currentPage == i ? "active" : "") + "'><a class='page-link' href='javascript:void(0);' onclick='changeParticipantsPage(" + i + ")'>" + i + "</a></li>";
                }
                html += "</ul>";
            }
            paginationContainer.html(html);
         }

        function changeParticipantsPage(pageNumber) {
            viewParticipants(pageNumber);
        }

        $(document).on("click", ".update_description_meeting", function(){
            var meetingId = $(this).attr('data-id');
            var description = $(this).parents('td').find('.description').val();
            if(description != ''){
                $.ajax({
                    type: "POST",
                    url: "{{ route('meeting.store.description') }}",
                    data: {'_token': "{{ csrf_token() }}",id:meetingId,description:description},
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

        $(document).on('click', '.load-description-meeting-history', function() {
            var id = $(this).attr('data-id');
            var type = $(this).attr('data-type');
            $.ajax({
                url: '{{ route("meeting.description.show") }}',
                dataType: "json",
                data: {
                    id:id,
                    type:type,
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
                        $("#zoom-meeting-description-listing").find(".zoom-meeting-description-listing-view").html(html);
                        $("#zoom-meeting-description-listing").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

    </script>
@endsection