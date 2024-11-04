@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
    <style type="text/css">
        .preview-category input.form-control {
            width: auto;
        }
        .break{
            word-break: break-all !important;
        }
    </style>
	

<style>
th {border: 1px solid black;}
table{border-collapse: collapse;}
.ui-icon, .ui-widget-content .ui-icon {background-image: none;}

#bug_tracking_maintable {
	font-size:12px;
}
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
	padding:5px;
}
#bug_tracking_maintable .btn {
	padding: 1px 3px 0px 4px !important;
	margin-top:0px !important;
}
.chat-list-history {
	z-index:999;
}
.scrollable {     
    max-height:550px;
    margin: 0;
    padding: 0;
    overflow: auto;
}
</style>
	<div class="row" id="common-page-layout">
		<div class="col-lg-12 margin-tb">
			<h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
		</div>
		<br>
		<div class="col-lg-12 margin-tb">
			<div class="row">
				
				<div class=" col-md-10">						
					<div class="row">
						<div style="width: 100%;text-align: center;margin-left: 19%;margin-top: 20px;font-size: 16px;font-weight: bold;">
							Bug Severity Summary 
						</div>
						<table class="table table-bordered" style="margin-left: 19%;margin-top:20px;">
							<thead>
							<tr>
								<th style="text-align:center;"><b>Websites</b></th>
								<th style="text-align:center;"><b>Critical</b></th>
								<th style="text-align:center;"><b>High</b></th>
								<th style="text-align:center;"><b>Medium</b></th>
								<th style="text-align:center;"><b>Low</b></th>
							</tr>
							</thead>
						
						<?php
                            for ($m = 0; $m < count($bug_tracker); $m++) {
                                ?>
							<tr>
								<td><?php echo $bug_tracker[$m]['title']; ?> </td>
								<td style="text-align:right;">
									<?php if ($bug_tracker[$m]['critical'] > 0) { ?>									
										<?php echo $bug_tracker[$m]['critical']; ?> <button style="margin-left:10px;" type="button" class="btn btn-xs show-bugs-history" title="Show Bug List" data-id="<?php echo $bug_tracker[$m]['website_id']; ?>" data-type="severity" data-sevid="1"><i class="fa fa-info-circle"></i></button>									
									<?php } else {
									    echo ' - ';
									} ?>	
								</td>
								<td style="text-align:right;">
									<?php if ($bug_tracker[$m]['high'] > 0) { ?>									
										<?php echo $bug_tracker[$m]['high']; ?> <button style="margin-left:10px;" type="button" class="btn btn-xs show-bugs-history" title="Show Bug List" data-id="<?php echo $bug_tracker[$m]['website_id']; ?>"  data-type="severity" data-sevid="2"><i class="fa fa-info-circle"></i></button>									
									<?php } else {
									    echo ' - ';
									} ?>	
								</td>
								<td style="text-align:right;">
									<?php if ($bug_tracker[$m]['medium'] > 0) { ?>									
										<?php echo $bug_tracker[$m]['medium']; ?> <button style="margin-left:10px;" type="button" class="btn btn-xs show-bugs-history" title="Show Bug List" data-id="<?php echo $bug_tracker[$m]['website_id']; ?>"  data-type="severity" data-sevid="3"><i class="fa fa-info-circle"></i></button>									
									<?php } else {
									    echo ' - ';
									} ?>	
								</td>
								<td style="text-align:right;">
									<?php if ($bug_tracker[$m]['low'] > 0) { ?>									
										<?php echo $bug_tracker[$m]['low']; ?> <button style="margin-left:10px;" type="button" class="btn btn-xs show-bugs-history" title="Show Bug List" data-id="<?php echo $bug_tracker[$m]['website_id']; ?>"  data-type="severity" data-sevid="4"><i class="fa fa-info-circle"></i></button>									
									<?php } else {
									    echo ' - ';
									} ?>	
								</td>
							</tr>	
						<?php
                            }
						?>
						
						</table>
										
					</div>
				</div>
				
				
				<div class=" col-md-11">					
					<div class="row">
						<div style="width: 100%;text-align: center;margin-left: 5%;margin-top: 20px;font-size: 16px;font-weight: bold;">
							Bug Status Summary 
						</div>
						<table class="table table-bordered" style="margin-left: 5%;margin-top:20px;margin-bottom: 100px !important;">
							<thead>
							<tr>
								<th style="text-align:center;"><b>Websites</b></th>
								<th style="text-align:center;width: 100px;"><b>New</b></th>
								<th style="text-align:center;width: 100px;"><b>Open</b></th>
								<th style="text-align:center;width: 100px;"><b>Close</b></th>
								<th style="text-align:center;width: 100px;"><b>In Test</b></th>
								<th style="text-align:center;width: 100px;"><b>Bug</b></th>
								<th style="text-align:center;"><b>In Progress</b></th>
								<th style="text-align:center;"><b>Completed</b></th>
								<th style="text-align:center;"><b>Discussing</b></th>
								<th style="text-align:center;"><b>Deployed</b></th>
								<th style="text-align:center;"><b>Discuss With Lead</b></th>
								<th style="text-align:center;"><b>Un Resolved</b></th>
							</tr>
							</thead>
						
						<?php
						    for ($m = 0; $m < count($bug_tracker); $m++) {
						        ?>
							<tr>
								<td><?php echo $bug_tracker[$m]['title']; ?> </td>
								<td style="text-align:right;">
									<?php if ($bug_status_tracker[$m]['new'] > 0) { ?>									
										<?php echo $bug_status_tracker[$m]['new']; ?> <button style="margin-left:10px;" type="button" class="btn btn-xs show-bugs-history" title="Show Bug List" data-id="<?php echo $bug_tracker[$m]['website_id']; ?>"  data-type="status" data-statusid="1"><i class="fa fa-info-circle"></i></button>									
									<?php } else {
									    echo ' - ';
									} ?>	
								</td>
								<td style="text-align:right;">
									<?php if ($bug_status_tracker[$m]['open'] > 0) { ?>									
										<?php echo $bug_status_tracker[$m]['open']; ?> <button style="margin-left:10px;" type="button" class="btn btn-xs show-bugs-history" title="Show Bug List" data-id="<?php echo $bug_tracker[$m]['website_id']; ?>" data-type="status"  data-statusid="2"><i class="fa fa-info-circle"></i></button>									
									<?php } else {
									    echo ' - ';
									} ?>	
								</td>
								<td style="text-align:right;">
									<?php if ($bug_status_tracker[$m]['close'] > 0) { ?>									
										<?php echo $bug_status_tracker[$m]['close']; ?> <button style="margin-left:10px;" type="button" class="btn btn-xs show-bugs-history" title="Show Bug List" data-id="<?php echo $bug_tracker[$m]['website_id']; ?>" data-type="status"  data-statusid="3"><i class="fa fa-info-circle"></i></button>									
									<?php } else {
									    echo ' - ';
									} ?>	
								</td>
								<td style="text-align:right;">
									<?php if ($bug_status_tracker[$m]['intest'] > 0) { ?>									
										<?php echo $bug_status_tracker[$m]['intest']; ?> <button style="margin-left:10px;" type="button" class="btn btn-xs show-bugs-history" title="Show Bug List" data-id="<?php echo $bug_tracker[$m]['website_id']; ?>" data-type="status"  data-statusid="4"><i class="fa fa-info-circle"></i></button>									
									<?php } else {
									    echo ' - ';
									} ?>	
								</td>
								<td style="text-align:right;">
									<?php if ($bug_status_tracker[$m]['bug'] > 0) { ?>									
										<?php echo $bug_status_tracker[$m]['bug']; ?> <button style="margin-left:10px;" type="button" class="btn btn-xs show-bugs-history" title="Show Bug List" data-id="<?php echo $bug_tracker[$m]['website_id']; ?>" data-type="status"  data-statusid="5"><i class="fa fa-info-circle"></i></button>									
									<?php } else {
									    echo ' - ';
									} ?>	
								</td>
								<td style="text-align:right;">
									<?php if ($bug_status_tracker[$m]['inprogress'] > 0) { ?>									
										<?php echo $bug_status_tracker[$m]['inprogress']; ?> <button style="margin-left:10px;" type="button" class="btn btn-xs show-bugs-history" title="Show Bug List" data-id="<?php echo $bug_tracker[$m]['website_id']; ?>" data-type="status"  data-statusid="6"><i class="fa fa-info-circle"></i></button>									
									<?php } else {
									    echo ' - ';
									} ?>	
								</td>
								<td style="text-align:right;">
									<?php if ($bug_status_tracker[$m]['completed'] > 0) { ?>									
										<?php echo $bug_status_tracker[$m]['completed']; ?> <button style="margin-left:10px;" type="button" class="btn btn-xs show-bugs-history" title="Show Bug List" data-id="<?php echo $bug_tracker[$m]['website_id']; ?>" data-type="status"  data-statusid="7"><i class="fa fa-info-circle"></i></button>									
									<?php } else {
									    echo ' - ';
									} ?>	
								</td>
								<td style="text-align:right;">
									<?php if ($bug_status_tracker[$m]['discussing'] > 0) { ?>									
										<?php echo $bug_status_tracker[$m]['discussing']; ?> <button style="margin-left:10px;" type="button" class="btn btn-xs show-bugs-history" title="Show Bug List" data-id="<?php echo $bug_tracker[$m]['website_id']; ?>" data-type="status"  data-statusid="8"><i class="fa fa-info-circle"></i></button>									
									<?php } else {
									    echo ' - ';
									} ?>	
								</td>
								<td style="text-align:right;">
									<?php if ($bug_status_tracker[$m]['deployed'] > 0) { ?>									
										<?php echo $bug_status_tracker[$m]['deployed']; ?> <button style="margin-left:10px;" type="button" class="btn btn-xs show-bugs-history" title="Show Bug List" data-id="<?php echo $bug_tracker[$m]['website_id']; ?>" data-type="status"  data-statusid="9"><i class="fa fa-info-circle"></i></button>									
									<?php } else {
									    echo ' - ';
									} ?>	
								</td>
								<td style="text-align:right;">
									<?php if ($bug_status_tracker[$m]['discusswithlead'] > 0) { ?>									
										<?php echo $bug_status_tracker[$m]['discusswithlead']; ?> <button style="margin-left:10px;" type="button" class="btn btn-xs show-bugs-history" title="Show Bug List" data-id="<?php echo $bug_tracker[$m]['website_id']; ?>" data-type="status"  data-statusid="10"><i class="fa fa-info-circle"></i></button>									
									<?php } else {
									    echo ' - ';
									} ?>	
								</td>
								<td style="text-align:right;">
									<?php if ($bug_status_tracker[$m]['unresolved'] > 0) { ?>									
										<?php echo $bug_status_tracker[$m]['unresolved']; ?> <button style="margin-left:10px;" type="button" class="btn btn-xs show-bugs-history" title="Show Bug List" data-id="<?php echo $bug_tracker[$m]['website_id']; ?>" data-type="status"  data-statusid="12"><i class="fa fa-info-circle"></i></button>									
									<?php } else {
									    echo ' - ';
									} ?>	
								</td>
							</tr>	
						<?php
						    }
						?>
						
						</table>
										
					</div>
				</div>
				
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-success" id="alert-msg" style="display: none;">
					<p></p>
				</div>
			</div>
		</div>
			<div class="col-md-12 margin-tb" id="page-view-result">

			</div>
		</div>
	</div>
	<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
          50% 50% no-repeat;display:none;">
	</div>

	<div class="common-modal modal" role="dialog">
		<div class="modal-dialog" role="document">

		</div>

	</div>
	
	
	<div id="BugWebsiteModal" class="modal fade" role="dialog">
															
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content" style="width: 850px;">
				<div class="modal-header">
					<h3>Bugs List</h3>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body scrollable">
					<table class="table" border="1" style="font-size: 13px;">
					<tr>
						<td style="text-align: center;"><b>Bug Id</b></td>						
						<td style="text-align: center;"><b>Date</b></td>
						<td style="text-align: center;"><b>Summary</b></td>							 
						<td style="text-align: center;"><b>Type</b></td>
						<td style="text-align: center;"><b>Assign To</b></td>
						<td style="text-align: center;"><b>Severity</b></td>
						<td style="text-align: center;"><b>Status</b></td>
						<td style="text-align: center;"><b>Comm</b></td>
						<td style="text-align: center;"><b>Action</b></td>
					</tr>
					<tbody class="tbhbugslist">

					</tbody>
				</table>
				</div>
				
			</div>
		</div>
	</div>


	<script type="text/javascript" src="{{ asset('/js/jsrender.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/jquery.validate.min.js') }}"></script>
	<script src="{{ asset('/js/jquery-ui.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/bug-tracker.js') }} "></script>
	<script type="text/javascript">
	
		$(document).on('click', '.load-conv-modal', function() {
			$('#BugWebsiteModal').css('z-index',9);	
		});
		
	
		
		$('#chat-list-history').on("hide.bs.modal", function() {
			$('#BugWebsiteModal').css('z-index',99999999);	
		})
		
		
		$(document).on('click', '.show-bugs-history', function() {
			
			
			
			$(".tbhbugslist").html("");
			var id = $(this).data('id');
			var type = $(this).data('type');
			var serv_id = $(this).data('sevid');
			var status_id = $(this).data('statusid');
			
			if(type == 'severity') {
				var urlchange = "/bug-tracking/website-history?type="+type+"&id="+id+"&servid="+serv_id;
			} else {
				var urlchange = "/bug-tracking/website-history?type="+type+"&id="+id+"&statusid="+status_id;
			}
			
			$.ajax({
			   url: urlchange,
			   type: "get",
			   datatype: "html",			  
			   beforeSend: function()
			   {
				  $('#loading-image-preview').css("display","block");
			   }
			})
			.done(function(response)
			{
				$('#loading-image-preview').css("display","none");
				
				
				if(response.data.length == 0){
					console.log(response.data.length);
					//notify user if nothing to load
					//$('.ajax-loading').html("No more records!");
					return;
				}
				
				if(response.data.length >0){

					var html ="";

					$.each(response.data, function (i,item){
						
						if(item.bug_type_id != null) {
							var bug_type_id = item.bug_type_id;
						} else {
							var bug_type_id = ' -';
						}
						
						if(item.bug_severity_id != null) {
							var bug_severity_id = item.bug_severity_id;
						} else {
							var bug_severity_id = ' -';
						}
						
						if(item.bug_status_id != null) {
							var bug_status_id = item.bug_status_id;
						} else {
							var bug_status_id = ' -';
						}
						
						html+="<tr>"
						html+=" <td>"+ item.id +"</td>"  
						html+=" <td>"+ item.created_at_date  +"</td>"	
						html+=" <td>"+ item.summary_short +"</td>"
						html+=" <td>"+ bug_type_id  +"</td>"
						html+=" <td>"+ item.assign_to  +"</td>"
						html+=" <td>"+ bug_severity_id  +"</td>"
						html+=" <td>"+ bug_status_id  +"</td>"
						html+=" <td><button type='button' class='btn btn-xs btn-image load-communication-modal load-conv-modal' data-object='bug' data-id='"+item.id+"' title='Load messages'><img src='../images/chat.png' alt=''></button></td>"
						html+=" <td><a href='/bug-tracking?bug_main_id="+item.id+"' target='_blank'><button type='button' title='Push' data-id='"+item.id+"' class='btn btn-push'><i class='fa fa-eye' aria-hidden='true'></i></button></a></td>"

						html+="</tr>"
					})

					$('.tbhbugslist').html(html);
				}
				
				

				$('.loading-image-preview').hide(); //hide loading animation once data is received
				$('#loading-image-preview').css("display","none");		
				$('#BugWebsiteModal').modal('toggle');	
				
				
			   
		   })
		   .fail(function(jqXHR, ajaxOptions, thrownError)
		   {
			  alert('No response from server');
		   });
		});
	</script>
	
@endsection