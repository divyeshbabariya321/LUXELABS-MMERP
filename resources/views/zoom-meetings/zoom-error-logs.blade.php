@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Zoom Api Logs ({{$zoomApiLogs->total()}})</h2>
		</div>
	</div>
	<div class="mt-3 col-md-12">
		<form action="{{route('meeting.list.error-logs')}}" method="get" class="search">
			<div class="col-md-2 pd-sm">
                <h5> <b>Search Type </b></h5>
                <?php 
                    if(request('zoom_type')){   $zoom_type_search = request('zoom_type'); }
                    else{ $zoom_type_search = []; }
                ?>
                <select name="zoom_type[]" id="zoom_type" class="form-control select2" multiple>
                    <option value="" @if($zoom_type_search=='') selected @endif>-- Select a Zoom Type --</option>
                    @forelse($zoomApiLogTypes as $swId => $zoomApiLogType)
                    <option value="{{ $zoomApiLogType }}" @if(in_array($zoomApiLogType, $zoom_type_search)) selected @endif>{!! $zoomApiLogType !!}</option>
                    @empty
                    @endforelse
                </select>
            </div>
            <div class="col-md-2 pd-sm">
                <h5> <b> Search Request Url</b> </h5>
                <?php 
                    $zoom_url_search = request('zoom_type') ? request('req_urls') : [];
                ?>
                <select name="req_urls[]" id="req_urls" class="form-control select2" multiple>
                    @forelse($zoomrequestUrls as $id => $url)
                        <option value="{{ $id }}" @if(!empty($zoom_url_search) && in_array($id, $zoom_url_search)) selected @endif>{!! $url !!}</option>
                    @empty
                    @endforelse
                </select>
            </div>            
			<div class="col-lg-2">
                <h5><b> Search Status</b></h5>
				<input class="form-control" type="text" id="search_status" placeholder="Search Status" name="search_status" value="{{ (request('search_status') ?? "" )}}">
			</div>
            <div class="col-lg-2">
                <h5> <b>Search Response Status </b></h5>
				<input class="form-control" type="text" id="response_status" placeholder="Search Response Status" name="response_status" value="{{ (request('response_status') ?? "" )}}">
			</div>
			<div class="col-lg-2">
                <h5> <b> Search Date </b></h5>
				<input class="form-control" type="date" name="date" value="{{ (request('date') ?? "" )}}">
			</div>

			<div class="col-lg-2"><br><br>
				<button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
				   <img src="{{ asset('images/search.png') }}" alt="Search">
			   </button>
			   <a href="{{route('meeting.list.error-logs')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
			</div>
		</form>
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th width="3%">ID</th>
			    	<th width="3%">Type</th>
			        <th width="20%">Request Url</th>
                    <th width="10%">Response Data</th>
			        <th width="20%">Response Headers</th>
			        <th width="20%">Response Status</th>
			        <th width="3%">Status</th>
					<th width="5%">Created At</th>
                </tr>
		    	<tbody>
                    @foreach ($zoomApiLogs as $key=>$data)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$data->type}}</td>
                            <td>{{$data->request_url}}</td>
                            <td>{{$data->request_data}}</td>
                            <td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                   {{ strlen($data->request_headers) > 30 ? substr($data->request_headers, 0, 60).'...' :  $data->request_headers }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $data->request_headers }}
                                </span>
                            </td>
                            <td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                   {{ strlen($data->response_data) > 30 ? substr($data->response_data, 0, 60).'...' :  $data->response_data }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $data->response_data }}
                                </span>
                            </td>
							<td>{{$data->response_status}}</td>
                            <td>{{ $data->created_at->format('Y-m-d') }}</td>
						</tr>                        
                    @endforeach
		    	</tbody>
		    </thead>
		</table>
		{!! $zoomApiLogs->appends(Request::except('page'))->links() !!}
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
</script> 
@endsection
    