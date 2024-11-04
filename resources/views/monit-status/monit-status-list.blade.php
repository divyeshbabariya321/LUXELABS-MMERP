@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Monit Status ({{count($monitStatus)}})</h2>
		</div>
	</div>
	<div class="mt-3 col-md-12">
		<form action="{{route('monit-status.index')}}" method="get" class="search">
			<div class="col-md-2 pd-sm">
				<input class="form-control" type="text" id="search_uptime" placeholder="Search service name" name="service_name" value="{{ (request('service_name') ?? "" )}}">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search_error" placeholder="Search Memory" name="search_memory" value="{{ (request('search_memory') ?? "" )}}">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search_type" placeholder="Search Status" name="search_status" value="{{ (request('search_status') ?? "" )}}">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search_uptime" placeholder="Search uptime" name="search_uptime" value="{{ (request('search_status') ?? "" )}}">
			</div>
			<div class="col-lg-2">
				<input class="form-control" type="date" name="date" value="{{ (request('date') ?? "" )}}">
			</div>

			<div class="col-lg-2">
				<button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
				   <img src="{{ asset('images/search.png') }}" alt="Search">
			   </button>
			   <a href="{{route('monit-status.index')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
			</div>
		</form>
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th width="3%">ID</th>
			    	<th width="8%">Server Name</th>
			    	<th width="8%">Server Ip</th>
			    	<th width="8%">Service Name</th>
			        <th width="30%">Memory</th>
			        <th width="10%">Status</th>
			        <th width="10%">Uptime</th>
			        <th width="10%">Date</th>
			        <th width="10%">Action</th>
                </tr>
		    	<tbody>
                    @foreach ($monitStatus as $k => $data)
                        <tr>
                            <td>{{$k+1}}</td>
                            <td>@if(!empty($data->assetsManager->ip_name)) {{$data->assetsManager->ip_name}} @endif</td>
                            <td>@if(!empty($data->assetsManager->ip)) {{$data->assetsManager->ip}} @endif</td>
                            <td>{{$data->service_name}}</td>
							<td class="expand-row" style="word-break: break-all">
								<span class="td-mini-container">
								   {{ strlen($data->memory) > 30 ? substr($data->memory, 0, 30).'...' :  $data->memory }}
								</span>
								<span class="td-full-container hidden">
									{{ $data->memory }}
								</span>
							</td>
							@if($data->status==0)
								<td><span style=" background-color: #5cb85c;  border-color: #4cae4c; color: white; padding: 5px;  border-radius: 0;  width: 100%;">{{'Success'}}</span></td>
							@else 
								<td><span style=" background-color: #c9302c;  border-color: #ac2925; color: white; padding: 5px;  border-radius: 0;  width: 100%;">{{'Failed'}}</span></td>
							@endif
							<td>{{$data->uptime}}</td>
							<td>{{$data->created_at}}</td>
							<td>
								<a title="Run Command" class="btn btn-image monitunit-run-btn pd-5 btn-ht" data-id="{{ $data->xmlid }}" href="javascript:;" data-command="monit -I restart {{ $data->service_name }}" data-server="{{ $data->ip }}">
                                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                </a>
                                <button type="button" data-id="{{ $data->xmlid }}" class="btn btn-image pd-5 btn-ht monit-api-history" >
					        		<i class="fa fa-info-circle" aria-hidden="true"></i>
					        	</button>
							</td>
						</tr>                        
                    @endforeach
		    	</tbody>
		    </thead>
		</table>
	</div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
</div>

@include('monit-status.monit-api-history')

@endsection

@section('scripts')
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
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

	$(document).on("click", ".monitunit-run-btn", function(e) {
        e.preventDefault();
        var $this = $(this);
        var id = $this.data('id');
        var command = $this.data('command');
        var server = $this.data('server');

        $.ajax({
            url: "/monit-status/command/run"
            , type: "post"
            , headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , data: {
                id: id,
                command: command,
                server_ip: server
            },
            beforeSend: function() {
                $('#loading-image').show();
            },
        }).done(function(response) {
            if (response.code == '200') {
                toastr['success']('Command Run successfully!!!', 'success');
            } else {
                toastr['error'](response.message, 'error');
            }
            $('#loading-image').hide();
        }).fail(function(errObj) {
            $('#loading-image').hide();
            if (errObj ?.responseJSON ?.message) {
                toastr['error'](errObj.responseJSON.message, 'error');
                return;
            }
            toastr['error'](errObj.message, 'error');
        });
    });

    $(document).on('click','.monit-api-history',function(){
        monit_api_id = $(this).data('id');
		$.ajax({
            method: "GET",
            url: `{{ route('monit-status.api.histories', [""]) }}/` + monit_api_id,
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
						html += "<tr>";
						html += "<td>" + (k + 1) + "</td>";
						html += "<td>" + v.id + "</td>";
						html += "<td>" + v.request_data + "</td>";
						html += "<td>" + v.response_data + "</td>";
						html += "<td>" + v.user.name + "</td>";
						html += "<td>" + v.created_at + "</td>";

						html += "</tr>";
                    });
                    $("#monit-api-histories-list").find(".monit-api-histories-list-data").html(html);
                    $("#monit-api-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
	});
</script> 
@endsection
    