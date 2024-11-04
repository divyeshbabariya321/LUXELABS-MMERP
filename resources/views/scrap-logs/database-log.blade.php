@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection

@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Databse Logs</h2>
		</div>
	</div>
	@if ($errors->any())
		<div class="col-sm-12">
			<div class="alert alert-warning" role="alert">
				@foreach ($errors->all() as $error)
					<span><p>{{ $error }}</p></span>
				@endforeach
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
			</div>
		</div>
	@endif

	@include('partials.flash_messages', ['withButton' => true])
	<form action="{{ action([\App\Http\Controllers\ScrapLogsController::class, 'databaseLog']) }}" method="get">
		<div class="mt-3 col-md-12">
			<div class="col-lg-2">
				<input class="form-control" type="text" id="search" placeholder="Search name" name="search" value="{{ $search }}">
			</div>
			<div class="col-lg-2">
				<button type="submit" id="tabledata" class="btn btn-image">
				    <img src="/images/filter.png">
				</button>
			</div>
		</div>
	</form>
	<?php $typeBtn = $logBtn->type ?? ''; ?>
	<button type='button' class='btn custom-button float-right mr-3 truncate'>Truncate Log</button>
	<a href="/admin/database-log/enable" class="btn custom-button float-right mr-3 " style="@if ($typeBtn == 'Enable')  background-color: #28a745 !important; @endif">Enabled</a>
	<a href="/admin/database-log/disable" class="btn custom-button float-right mr-3 "  style="@if ($typeBtn == 'Disable') background-color: #ffc107 !important; @endif">Disable</a>
	<button style='padding:3px;' type='button' class='btn custom-button float-right mr-3 history' data-toggle='modal' data-target='#slow_loh_history_model'>Log History</button>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
				<td width="5%">Index</td>
				<td width="10%">Date Time</td>
				<td width="10%">Time Taken</td>
				<td width="10%">Url</td>
				<td width="10%">Sql</td>
				<td width="85%">Log Messages</td>
			</thead>
			<tbody id="log_popup_body">
				@php $count = 1;  @endphp
				@foreach($databaseLogs as $key => $databaseLog)
					<tr>
						<td>{{$key+1}}</td>
						<?php $timeCol = false;

							$dateResult = '';
							$dateTime = '';

							if(Str::contains($databaseLog->log_message, '# Time: ') || Str::contains($databaseLog->log_message, 'Time: ')){

								$timeCol = true;
								$dateString = $databaseLog->log_message;
								$prefix = "# Time:";
								$index = explode(" ",$dateString);
								//$dateResult = date('d M Y H:s:i', "1652880141");
								$dateTime = $index[3];
							}
							if(Str::contains($databaseLog->log_message, "SET timestamp=")){
								$dateString = $databaseLog->log_message;
								$prefix = "SET timestamp=";
								$index = explode("=",$dateString);//strpos($dateString, $prefix) + strlen($prefix);
								$dateStr = str_replace(';', '', $index[1]);
								$dateResult = date('d M Y', (int)$dateStr);
							}
							if(Str::contains($databaseLog->log_message, "exceeded")){
								$dateResult = date('d M Y H:s:i', strtotime(substr($databaseLog->log_message,1,19)));
							}
							?>
						@if($dateResult || $dateTime)
							<td>{{$dateResult.' '.$dateTime}}</td>
						@else
							<td></td>
						@endif
						<td>{{$databaseLog->time_taken}}</td>
						<td>{{$databaseLog->url}}</td>
						<td>{{$databaseLog->sql_data}}</td>
						@if(Str::contains($databaseLog->log_message, "exceeded"))
							<td>{{substr($databaseLog->log_message,32)}}</td>
						@else
							<td>{{$databaseLog->log_message}}</td>
						@endif
	    			</tr>
				@endforeach
			</tbody>
		</table>
		<div class="d-flex justify-content-center">
			{!! $databaseLogs->links() !!}
		</div>
	</div>

	<div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Log Messages</h4>
                </div>
                <div class="modal-body">
                	<div class="cls_log_popup">
                		<table class="table">
                		</table>
                	</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

	<div id="slow_loh_history_model" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Slow Log History</h4>
                </div>
                <div class="modal-body">
                	<div class="cls_log_popup">
                		<table class="table">
							<thead>
								<tr>
									<th>ID</th>
									<th>Date</th>
									<th>User Name</th>
									<th>Type</th>
								</tr>
							</thead>
							<tbody class="slow_loh_history_tbody">
								
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
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script>
	// $(document).ready(function(){
	// 	tableData(BASE_URL);
	// 	$("#tabledata").click(function(e) {
	// 		tableData(BASE_URL);
	// 	});
	// 	function tableData(BASE_URL) {
	// 		var search = $("input[name='search'").val() != "" ? $("input[name='search'").val() : null;
	// 		var date = $("#datepicker").val() !="" ? $("#datepicker").val() : null;
 //            var download = "?download=" + $("select[name='download_option'").val();

 //            if($("select[name='download_option'").val() == "yes") {
 //                window.location.href = BASE_URL+'?search='+search;
 //            }

	// 		$.ajax({
	// 			url: BASE_URL+"/database-log/"+search+"/"+date,
	// 			method:"get",
	// 			headers: {
	// 			    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	// 			  },
	// 			data:{},
	// 			cache: false,
	// 			success: function(data) {
	// 					console.log(data)
	// 					$("tbody").empty();
	// 					$.each(data.file_list, function(i,row){
	// 						$("tbody").append("<tr><td>"+(i+1)+"</td><td>"+row['foldername']+"</td><td><a href='scrap-logs/file-view/"+row['filename']+ '/' +row['foldername']+"' target='_blank'>"+row['filename']+"</a>&nbsp;<a href='javascript:;' onclick='openLasttenlogs(\""+row['scraper_id']+"\")'><i class='fa fa-weixin' aria-hidden='true'></i></a></td><td>"+row['log_msg']+"</td></tr>");
	// 					});
						
	// 				}
	// 		});
	// 	}
	// });
	$(document).on('click', '.history', function (e) {
		
		$.ajax({
			url: BASE_URL+"/database-log/history",
			method:"get",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
			data:{},
			cache: false,
			success: function(data) {
				$(".slow_loh_history_tbody").empty();
				$.each(data.data, function(i,row){
					$(".slow_loh_history_tbody").append("<tr><td>"+row['id']+"</td><td>"+row['created_at']+"</td><td>"+row['userName']+"</td><td>"+row['type']+"</td></tr>");
				});
				toastr['success'](data.message, 'success');
			}
		});
	});
	$(document).on('click', '.truncate', function (e) {
		$.ajax({
			url: BASE_URL + "/admin/database-log/truncate",
			method: "get",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {},
			cache: false,
			success: function (data) {
				console.log(data);
				if(data.code == 200){
					toastr['success'](data.message, 'success');
					location.reload();
				}else{
					toastr['error'](data.message, 'error');
				}
			}
		});
	});
</script> 
@endsection