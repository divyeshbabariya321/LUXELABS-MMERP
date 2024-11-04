@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">User Access List({{$userAccessLists->total()}})</h2>
		</div>
	</div>
	@include('partials.flash_messages')

    <div class="mt-3 col-md-12">
		<form action="{{route('user-management.user-access-listing')}}" method="get" class="search">
            @csrf
            <div class="col-md-2 pd-sm">
				{{ html()->multiselect("user_ids[]", \App\User::pluck('name', 'id')->toArray(), request('user_ids'))->class("form-control globalSelect2")->data('placeholder', "Select User") }}
			</div>
            <div class="col-md-2 pd-sm">
				{{ html()->multiselect("s_ids[]", \App\UserPemfileHistory::pluck('server_name', 'server_name')->toArray(), request('s_ids'))->class("form-control globalSelect2")->data('placeholder', "Select Server Name") }}
			</div>
            <div class="col-lg-2">
				<input class="form-control" type="text" id="search_username" placeholder="Search UserName" name="search_username" value="{{ $search_username ?? '' }}">
			</div>
            <div class="col-lg-2">
				<input class="form-control" type="text" id="search_event" placeholder="Search Event" name="search_event" value="{{ $search_event ?? '' }}">
			</div>
            <div class="col-lg-2">
				<input class="form-control" type="date" name="date">
			</div>

			<div class="col-lg-2">
				<button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
				   <img src="{{ asset('images/search.png') }}" alt="Search">
			   </button>
			   <a href="{{route('user-management.user-access-listing')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>

			</div>
		</form>
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th width="3%">S.no</th>
			    	<th width="10%">User</th>
			        <th width="10%">Server</th>
			        <th width="10%">User Name</th>
			        <th width="10%">Event</th>
                    <th width="10%">Created By</th>
			        <th width="10%">Date</th>
                    <th width="10%">Action</th>
                </tr>
		    	<tbody>
                    @foreach ($userAccessLists as $key => $userAccessList)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$userAccessList->user->name}}</td>
							<td>{{$userAccessList->server_name}}</td>
							<td>{{$userAccessList->username}}</td>
                            <td>{{$userAccessList->action}}</td>
                            <td>{{$userAccessList->created_by}}</td>
                            <td>{{$userAccessList->created_at}}</td>
							
                            <td>
                             @if($userAccessList->action == 'add')
                                <a title="Download" href="/user-management/download-pem-file/{{$userAccessList->id}}/" class="btn btn-image download-pem-user pd-5" data-id="{{$userAccessList->id}}"><i class="fa fa-download"></i></a>
                             @if(Auth::user()->isAdmin())
                                    <button title="Disable access" type="button" class="btn btn-image disable-pem-user pd-5" data-id="{{$userAccessList->id}}" onclick="return confirm('Are you sure you want to disable access for this user?');"><i class="fa fa-ban"></i></button>
                                @endif
                            @endif
                            @if(Auth::user()->isAdmin())
                                <button title="View Logs" type="button" class="btn btn-image view-pem-logs pd-5" data-id="{{$userAccessList->id}}"><i class="fa fa-info-circle"></i></button>
                            @endif
                            @if($userAccessList->action == 'add' || $userAccessList->action == 'disable')
                                @if(Auth::user()->isAdmin())
                                    <button title="Delete access" type="button" class="btn btn-image delete-pem-user pd-5" data-id="{{$userAccessList->id}}" onclick="return confirm('Are you sure you want to delete ?');"><i class="fa fa-trash"></i></button>
                                @endif
                            @endif
                            </td> 
						</tr>                        
                    @endforeach
		    	</tbody>
		    </thead>
		</table>
		{!! $userAccessLists->appends(Request::except('page'))->links() !!}
	</div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
</div>

<div class="modal" tabindex="-1" role="dialog" id="error_logs_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Error details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="error_logs_div">
                        <table class="table">
                            <thead>
                                <tr>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
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

<div id="user-pem-logs-summary-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Pem file history logs</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">Id</th>
                            <th width="10%">Cmd</th>
                            <th width="40%">Output</th>
                            <th width="30%">Error code</th>
                        </tr>
                    </thead>
                    <tbody class="show-search-password-list" id="user-pem-logs-summary-modal-html">
                        
                    </tbody>
                </table> 
           </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>

$(document).on('click', '.view-pem-logs', function() {
        var pemfileHistoryId = $(this).data('id');
        $.ajax({
            url: "/user-management/user-pemfile-history-logs/" + pemfileHistoryId,
            method: 'GET',
            data: {
            pemfileHistoryId: pemfileHistoryId
            },
            success: function(data) {
                var html = "";
                if (data.code === 200 && data.data && data.data.length > 0) {
                    $.each(data.data, function(index, loglist) {
                        html += "<tr>";
                        html += "<td>" + loglist.id + "</td>";
                        html += "<td>" + loglist.cmd + "</td>";

                        html += "<td class='expand-row-msg'>";
                        html += "<div class='td-mini-container' onclick='expandRow(this)'>";

                        if (typeof loglist.output_string === "string" && loglist.output_string.trim() !== "") {
                        html += loglist.output_string.substring(0, 30) + "...";
                        html += "</div>";
                        html += "<div class='td-full-container hidden'>" + loglist.output_string + "</div>";
                        } else {
                        html += "-";
                        }

                        html += "</div>";
                        html += "</td>";
                        html += "<td>" + (loglist.return_var !== null ? loglist.return_var : "") + "</td>";

                        html += "</tr>";
                    });
                    } else {
                    html += "<tr><td colspan='4'>No data available</td></tr>";
                    }
                    $('#user-pem-logs-summary-modal-html').html(html);
                    $('#user-pem-logs-summary-modal').modal('show');
                }, 
                error: function(xhr, status, error) {
                alert("An error occurred. Please try again.");
                }
            });
});


        $(document).on('click', '.delete-pem-user', function() {
        var id = $(this).data('id');
			$.ajax({
              url:  "/user-management/delete-pem-file/" + id,
				method: 'POST',
				data: {
                    _token: "{{ csrf_token() }}",
					id: id
				},
                    success: function(data) {
                        toastr["error"](data.message);
                },
                 error: function(xhr, status, error) {
                    alert("An error occurred. Please try again.");
                }
			});
		});

        $(document).on('click', '.disable-pem-user', function() {
        var id = $(this).data('id');
			$.ajax({
              url:  "/user-management/disable-pem-file/" + id,
				method: 'POST',
				data: {
                    _token: "{{ csrf_token() }}",
					id: id
				},
                    success: function(data) {
                        toastr["error"](data.message);
                },
                 error: function(xhr, status, error) {
                    alert("An error occurred. Please try again.");
                }
			});
		});

        $(document).on('click', '.download-pem-user', function() {
        var id = $(this).data('id');
			$.ajax({
              url:  "/user-management/download-pem-file/" + id,
				method: 'GET',
				data: {
                    _token: "{{ csrf_token() }}",
					id: id
				},
                    success: function(data) {
                },
                 error: function(xhr, status, error) {
                    alert("An error occurred. Please try again.");
                }
			});
		});

        function expandRow(element) {
            $(element).toggleClass('expanded');
            $(element).parent().find('.td-full-container').toggleClass('hidden');
        }
</script> 

@endsection