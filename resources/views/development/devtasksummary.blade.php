@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Development Task Summary')

@section('content')
<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
		<h2 class="page-heading">Development Task Summary <span class="count-text"></span></h2>
		<br>
	</div>

	<div class="col-lg-12 margin-tb margin-l">
		<form class="filterTaskSummary" action="{{ route('development.tasksSummary') }}" method="GET">
			<div class="row filter_drp">
				<div class="form-group col-lg-4">
					<select class="form-control globalSelect2" data-ajax="{{ route('usersList') }}" name="users_filter[]" data-placeholder="Search Users By Name" multiple >
						@if($userslist)
							@foreach ($userslist as $id => $user)
								<option value="{{ $user['id'] }}" selected>{{ $user['name'] }}</option>
							@endforeach
						@endif
                    </select>
				</div>
				<div class="form-group col-lg-4">
					<select class="form-control globalSelect2" data-ajax="{{ route('statusList') }}" name="status_filter[]" data-placeholder="Search Status By Name" multiple >
						@if($statuslist)
							@foreach ($statuslist as $id => $status)
								<option value="{{ $status['id'] }}" selected>{{ $status['name'] }}</option>
							@endforeach
						@endif
                    </select>
				</div>
				<div class="form-group col-lg-4">
					<button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
				</div>
			</div>
		</form>
	</div>

	<div class="col-lg-12 margin-tb">
		<div class="row">
			
			<div class=" col-md-12">						
				<div class="row">
					<table class="table table-bordered tbl-tasks-summary">
						<thead>
						<tr>
							<th><b>Users</b></th>
							@foreach($getTaskStatus as $status)
								<th class="taskStatusName"><b><?= $status->name ?></b></th>
							@endforeach
						</tr>
						</thead>
					
						@foreach($arrUserNameId as $user)
						<tr>
							<td>{{ $user['name']; }} </td>
							@foreach($arrStatusCount[$user['userid']] as $key => $value)
								<td class="taskStatusCnt" data-status="{{$key}}">{{ $value }}<button type="button" class="btn btn-xs show-task-history" title="Show Task List" data-userid={{$user["userid"]}} data-id="{{$key}}" data-type="severity" data-sevid="1"><i class="fa fa-info"></i></button> </td>
							@endforeach
						</tr>
						@endforeach
					
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
<div id="loading-image-task-summary">
</div>

<div class="common-modal modal" role="dialog">
	<div class="modal-dialog" role="document">

	</div>

</div>
	
	
<div id="taskHistoryModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h3>Development Task List</h3>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body scrollable">
				<table class="table tblTaskHistory" border="1">
				<tr class="headTbltasklist">
					<td><b>Task Id</b></td>
					<td><b>Date</b></td>
					<td><b>Task Subject</b></td>
					<td><b>Task Details</b></td>
				</tr>
				<tbody class="tbltasklist">

				</tbody>
			</table>
			</div>
			
		</div>
	</div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
	$(document).on('click', '.show-task-history', function() {
		var taskStatusId = $(this).data('id');
		var userId = $(this).data('userid');
		$(".tbltasklist").html("");

		$.ajax({
			url: "{{ route('development.tasksList') }}",
			data: {
				_token: "{{ csrf_token() }}",
				taskStatusId: taskStatusId,
				userId: userId
			},
			type: "post",
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
				return;
			}
			
			if(response.data.length > 0){
				var html ="";
				$.each(response.data, function (i,item){
					html+="<tr>"
					html+=" <td>"+ item.id +"</td>"
					html+=" <td>"+ moment(item.created_at).format('DD-MM-YYYY');  +"</td>"
					html+=" <td>"+ item.subject +"</td>"
					html+=" <td>"+ item.task  +"</td>"
					html+="</tr>"
				})
				$('.tbltasklist').html(html);
			}
			$('.loading-image-preview').hide(); //hide loading animation once data is received
			$('#loading-image-preview').css("display","none");		
			$('#taskHistoryModal').modal('toggle');
		})
		.fail(function(jqXHR, ajaxOptions, thrownError)
		{
			alert('No response from server');
		});
	});
</script>
@endsection
