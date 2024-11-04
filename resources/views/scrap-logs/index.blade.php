@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css">
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
@endsection
@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Scrap Logs</h2>
		</div>
	</div>
	<div class="mt-3 col-md-12">
		<div class="col-lg-1">
			<select name="date" id="datepicker" class="form-control">
				@for($i=0; $i<=31; $i++)
					<option value="{{$i}}" @if((date("d") - 1) == $i) selected @endif>{{$i}}</option>
				@endfor
			</select>
		</div>
		<div class="col-lg-1">
			<select name="month" id="monthpicker" class="form-control">
				@foreach(["","Jan","Feb","Mar","Apr","May","Jun","July","Aug","Sep","Oct","Nov","Dec"] as $mon)
					<option value="{{$mon}}" @if((request("month") - 1) == $mon) selected @endif>{{$mon}}</option>
				@endforeach
			</select>
		</div>
		<div class="col-lg-1">
			<select name="year" id="yearpicker" class="form-control">
				@foreach(["","19","20","21","22","23","24","25"] as $year)
					<option value="{{$year}}" @if((request("year") - 1) == $year) selected @endif>{{$year}}</option>
				@endforeach
			</select>
		</div>
		<div class="col-lg-2">
			<select name="date" id="datepicker" class="form-control server_id-value">
				<option value="">Select Server</option>
					@foreach($servers as $server)
						<option value="{{ $server['server_id'] }}")>{{$server['server_id'] }}</option>
					@endforeach
			</select>
		</div>					
		<div class="col-lg-2">
			<input class="form-control" type="text" id="search" placeholder="Search name" name="search" value="{{ $name }}">
		</div>
        <div class="col-lg-1">
            <select class="form-control" name="download_option">
                <option value="no">No</option>
                <option value="yes">Yes</option>
            </select>
        </div>
        <div class="col-lg-1">
			<button type="button" id="tabledata" class="btn btn-image">
			<img src="/images/filter.png"style="margin-left:17px;">
			</button>
		</div>
		<div class="creat-stutush">
		<div class="text-rights">
			<button class ="btn-dark" type="button" onclick="window.location='{{url('development/issue/create')}}'">Create an Issue</button>
		</div>
		<div class="text-rights">
			<button class ="btn-dark" type="button" data-toggle="modal" id="create-status">Create Status</button>
		</div>
		<div class="text-rights">
			<button class ="btn-dark" type="button" data-toggle="modal" id="logdatahistory">Log History</button>
		</div>

		<div class="col-lg-2 text-rights">
			<button class ="btn-dark" type="button" data-toggle="modal" id="map-log-status">Map Log Status</button>
		</div>


	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th width="10%">S.No</th>
			        <th width="10%">FolderName</th>
			        <th width="30%">FileName</th>
			        <th width="30%">Log Message</th>
			        
			        <th width="">Status</th>
			        <th width="">Remarks</th>
			    </tr>
		    	<tbody>
		    	</tbody>
		    </thead>
		</table>
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
                			<thead>
                				<td>Scraper ID</td>
                				<td>File Name</td>
                				<td>Log Messages</td>
                			</thead>
                			<tbody id="log_popup_body">
                				
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

    <div id="status-create" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create Status</h4>
                </div>
                <form action="{{ action([\App\Http\Controllers\ScrapLogsController::class, 'store']) }}" class="" method="POST">
                	@csrf
	                <div class="modal-body">
	                	<div class="cls_log_popup">
	                		<div class="col-md-12 mb-4">
	                			<input type="text" class="form-control" name="errortext" placeholder="Error Text Here">
	                		</div>
	                		<div class="col-md-12 mb-4">
	                			<input type="text" class="form-control" name="errorstatus" placeholder="Error Status Here">
	                		</div>
	                	</div>
	                </div>
	                <div class="modal-footer">
	                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	                    <button type="submit" class="btn btn-default">Save</button>
	                </div>
	            </form>
            </div>
        </div>
    </div>
    <div id="logdatacounter" class="modal fade" role="dialog">
    	   <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Log Data</h4>
                </div>
                <div class="modal-body">
                	<table class="table table-bordered table-striped">
	                	<thead></thead>
					    <tbody></tbody>
                	</table>
                </div>
            </div>
        </div>
    </div>
	<div id="datacounter" class="modal fade" role="dialog">
    	   <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Same Data Count</h4>
                </div>
                <div class="modal-body">
                	<table class="table table-bordered table-striped">
                	<thead></thead>
				    <tbody></tbody>
                	</table>
                </div>
            </div>
        </div>
    </div>
	<div id="logdatastatus" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    	   <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Log Data Status</h4>
                </div>
                <div class="modal-body">
                	<table id="scrapLogsStatusTable" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th width="10%">ID</th>
								<th width="70%">Log Message</th>
								<th width="20%">Status</th>
							</tr>
						</thead>
						<tbody> 
						</tbody>
                	</table>
                </div>
            </div>
        </div>
    </div>
    <div id="makeRemarkModal" class="modal fade" role="dialog">
		<div class="modal-dialog {{ !empty($type) && ($type == 'scrap' || $type == 'email') ? 'modal-lg' : '' }}">

	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <h4 class="modal-title">Remarks</h4>
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	      </div>

	      <div class="modal-body">
			@if (!empty($type) && ($type == 'scrap' || $type == 'email'))
			    <form id="filter-module-remark">
			        @csrf
			        <div class="form-group">
			            <label for="filter_auto">Remove auto</label>
			            <input type="checkbox" name="filter_auto" class="filter-auto-remark">
			        </div>
			    </form>
			@endif

	        @if(!empty($type) && ($type == 'scrap' || $type == 'email'))
			    <table class="table fixed_header table-bordered">
			        <thead class="thead-dark">
			            <tr>
			                <th width="50%">Comment</th>
			                <th width="10%">Created By</th>
			                <th width="10%">Created At</th>
			            </tr>
			        </thead>
			        <tbody id="remark-list"></tbody>
			    </table>
			@else
			    <div class="list-unstyled" id="remark-list"></div>
			@endif

	        <form id="add-remark">
	          <input type="hidden" name="id" value="">
	          <div class="form-group">
	            <textarea rows="2" name="remark" class="form-control" placeholder="Start the Remark"></textarea>
	          </div>
	          {{-- We dont need following settings for email page --}}
	          @if (empty($type) || $type != 'email')
	            <div class="form-group">
	              <label><input type="checkbox" class="need_to_send" value="1">&nbsp;Need to Send Message ?</label>
	            </div>
	            <div class="form-group">
	              <label><input type="checkbox" class="inlcude_made_by" value="1">&nbsp;Want to include Made By ?</label>
	            </div>
	          @endif
	          <button type="button" class="btn btn-secondary btn-block mt-2" id="{{ (!empty($type) && $type == 'scrap') ? 'scrapAddRemarkbutton' : 'addRemarkButton' }}">Add</button>
	        </form>
	      </div>

	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>

	  </div>
</div>
<div id="loghistory" class="modal fade" role="dialog">
	  <div class="modal-dialog ">
	  	<!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <h4 class="modal-title">log history</h4>
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	      </div>
	  	<div class="modal-body">
	  		<tbody id="log_history"></tbody>
	  	</div>
	  	<div class="modal-footer">
	  	</div>
	  </div>
	 </div>
</div>
<div id="history" class="modal fade" role="dialog">
	  <div class="modal-dialog ">
	  	<!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <h4 class="modal-title">Last 7 days history</h4>
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	      </div>
	  	<div class="modal-body">
	  		<tbody id="history"></tbody>
	  	</div>
	  	<div class="modal-footer">
	  	</div>
	  </div>
	 </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
@endsection

@section('scripts')
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script>
  function saveStatus(log_status_id) {
	  var log_status = $('#log_status_'+log_status_id).val();
	  if(log_status == '') {
		  alert('Select status');
		  return false;
	  }
	  $.ajax({
				url: BASE_URL+"/scrap-logs/status/save",
				method:"post",
				beforeSend: function () {
                    $("#loading-image").show();
                },
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data:{'id':log_status_id, "log_status":log_status},
				cache: false,
				success: function(data) {
					 $("#loading-image").hide();
					toastr['success']('Products updated successfully', 'success');
				}
			});
  }
	$(document).ready(function() 
	{
		

		tableData(BASE_URL);
		$("#tabledata").click(function(e) {
			tableData(BASE_URL);
		});
		function tableData(BASE_URL) {
			var search = $("input[name='search'").val() != "" ? $("input[name='search'").val() : null;
			var date = $("#datepicker").val() !="" ? $("#datepicker").val() : null;
            var download = "?download=" + $("select[name='download_option'").val();
			var server_id = $('.server_id-value').val();


            if($("select[name='download_option'").val() == "yes") {
                window.location.href = BASE_URL+"/scrap-logs/fetch/"+search+"/"+date+"/"+download;
            }

			$.ajax({
				url: BASE_URL+"/scrap-logs/fetch/"+search+"/"+date,
				method:"get",
				headers: {
				    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				  },
				data:{'server_id':server_id, month : $("#monthpicker").val(),"year" : $("#yearpicker").val()},
				cache: false,
				success: function(data) {

						console.log(data)
						$("#log-table tbody").empty();


						$.each(data.file_list, function(i,row){
							$("#log-table tbody").append("<tr><td>"+(i+1)+"</td><td>"+row['foldername']+"</td><td><a href='scrap-logs/file-view/"+row['filename']+ '/' +row['foldername']+"' target='_blank'>"+row['filename']+"</a>&nbsp;<a href='javascript:;' onclick='openLasttenlogs(\""+row['scraper_id']+"\")'><i class='fa fa-weixin' aria-hidden='true'></i></a></td><td>"+row['log_msg']+"</td><td>"+row['status']+"</td><td><button style='padding:3px;' type='button' class='btn btn-image make-remark d-inline' data-toggle='modal' data-target='#makeRemarkModal' data-name='"+row['scraper_id']+"'><img width='2px;' src='/images/remark.png'/></button><button style='padding:3px;' type='button' class='btn btn-image log-history d-inline' data-toggle='modal' data-target='#loghistory' data-filename='"+row['filename']+"' data-name='"+row['scraper_id']+"'><i class='fa fa-sticky-note'></i></button><button style='padding:3px;' type='button' class='btn btn-image history d-inline' data-toggle='modal' data-target='#history' data-filename='"+row['filename']+"' data-name='"+row['scraper_id']+"'><i class='fa fa-history'></i></button></td></tr>");

						});
						
					}
			});
		}
	});
	function openLasttenlogs(scraper_id){
		$.ajax({
			url: BASE_URL+"/fetchlog",
			method:"get",
			headers: {
			    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			  },
			data:{},
			cache: false,
			success: function(data) {
				$("#log_popup_body").empty();
				$.each(data.file_list, function(i,row){
					$("#log_popup_body").append("<tr><td>"+row['scraper_id']+"</td><td>"+row['filename']+"</td><td>"+row['log_msg']+"</td></tr>");
				});
			}
		});
		$('#chat-list-history').modal("show");
	}
	$(document).on('click', '.make-remark', function (e) {
            e.preventDefault();

            var name = $(this).data('name');

            $('#add-remark input[name="id"]').val(name);

            $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route("scrap.getremark") }}',
                data: {
                    name: name
                },
            }).done(response => {
                var html = '';
                var no = 1;
                $.each(response, function (index, value) {
                    /*html += '<li><span class="float-left">' + value.remark + '</span><span class="float-right"><small>' + value.user_name + ' updated on ' + moment(value.created_at).format('DD-M H:mm') + ' </small></span></li>';
                    html + "<hr>";*/
                    html += '<tr><td>' + value.remark + '</td><td>' + value.user_name + '</td><td>' + moment(value.created_at).format('DD-M H:mm') + '</td></tr>';
                    no++;
                });
                $("#makeRemarkModal").find('#remark-list').html(html);
            });
        });

	$(document).on('click', '.log-history', function (e) {
            e.preventDefault();
            var filename = $(this).data('filename');           
            var url = '{{ route("scarp.loghistory",':filename') }}';
            url = url.replace(":filename", filename);
             $.ajax({
                type: 'GET', 	 	
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: url
            })
             .done(response => {	
             	var html = '<table class="table table-bordered table-striped"><thead><tr><td width="18%">Date</td><td>Log Message</td></thead><tbody>';
			    $.each(response, function (key) {
			    	var log = response[key]['log_messages'];
			    	var date = response[key]['created_at'];
			    	 html += '<tr><td>' + date + '</td><td>' + log + '</td></tr>';
                });

			   html += "</tbody></table>";
           		$("#loghistory .modal-body").html(html);
            }); 	            
	});

	$(document).on('click', '.history', function (e) {
            e.preventDefault();
            var filename = $(this).data('filename');           
            var url = '{{ route("scarp.history",':filename') }}';
            url = url.replace(":filename", filename);
             $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: url
            })
             .done(response => {	
             	var html = '<table class="table scraper-name table-bordered table-striped"><thead><tr><td width="15%">Scraper Name</td><td>Remark</td><td width="20%">Date</td></thead><tbody>'; 		
			    $.each(response, function (key) {
			    	var scraper_name = response[key]['scraper_name'];
			    	var remark = response[key]['remark'];
			    	var date = response[key]['created_at'];
			    	 html += '<tr><td>' + scraper_name + '</td><td>' + remark + '</td><td>' + date + '</td></tr>';
                });
			   html += "</tbody></table>";
           		$("#history .modal-body").html(html);
            }); 	            
	});

	$(document).on('click','#logdatahistory',function(e) {  
		e.preventDefault();
		$('#logdatacounter').modal('show');	
		$.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route("scrap.logdata") }}',
                beforeSend: function() {
                    $("#logdatacounter .modal-body table tbody").html("<h4>Loading data...</h4>");
                }
      	})
      	.done(response => {
      		$("#loading-image").hide();
      		var html = '<table class="table table-bordered table-striped"><thead><tr><th style="width: 20%;">Scraper nameee</th><th>Log Message</th><th style="width: 8%;">Log count</th></thead><tbody>';
      		 $.each(response, function (key) {
      				html += '<tr><td>' + response[key]['scraper_name'] + '</td><td>'+ response[key]['remark'] + '</td><td>' + response[key]['log_count'] + '</td>';
      		 });
      		 html += "</tbody></table>";
      		$("#logdatacounter .modal-body").html(html);
      	});
	});

	$(document).on('click','#create-status',function(e) {  
		$('#status-create').modal('show');
	});

	var scrapLogsStatusTable = null;
	$(document).on('click','#map-log-status',function(e) {  
		$('#logdatastatus').modal('show');
		scrapLogsStatusTable = new DataTable('#scrapLogsStatusTable', {
			processing: true,
			serverSide: true,
			ajax: '{{ route("get.scrap.logs.status") }}',
			columns: [
				{ data: 'id', name: 'id' },
				{ data: 'log_message', name: 'log_message' },
				{ data: 'action', name: 'action', orderable: false, searchable: false }
			],
			dom: 'rtip' 
		});
	});
	$('#logdatastatus').on('hidden.bs.modal', function () {
		scrapLogsStatusTable.destroy();
	});

</script> 
@endsection