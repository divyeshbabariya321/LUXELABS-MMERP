@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Site Asset')

@section('styles')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
<style type="text/css">
	.preview-category input.form-control {
		width: auto;
	}

	#loading-image {
		position: fixed;
		top: 50%;
		left: 50%;
		margin: -50px 0px 0px -50px;
	}

	.dis-none {
		display: none;
	}

	.pd-5 {
		padding: 3px;
	}

	.toggle.btn {
		min-height: 25px;
	}

	.toggle-group .btn {
		padding: 2px 12px;
	}

	.latest-remarks-list-view tr td {
		padding: 3px !important;
	}
	#latest-remarks-modal .modal-dialog {
		 max-width: 1100px;
		width:100%;
	}
	.btn-secondary{
		border: 1px solid #ddd;
		color: #757575;
		background-color: #fff !important;
	}
	.modal {
		overflow-y:auto;
	}
	body.overflow-hidden{
		overflow: hidden;
	}

	span.user_point_none button, span.admin_point_none button{
		pointer-events: none;
		cursor: not-allowed;
	}table tr:last-child td {
		 border-bottom: 1px solid #ddd !important;
	 }
	 select.globalSelect2 + span.select2 {
    width: calc(100% - 26px) !important;
}

</style>
@endsection

@section('large_content')

<div id="myDiv">
	<img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
</div>

<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Site Assets</h2>
    </div>
    <br>
	<div class="col-lg-12 margin-tb">
		<div class="row">
			<div class="col-md-6">
				<button type="button" class="btn btn-secondary download_asset_data">Download</button>
			</div>
			<div class="col-md-6 pull-right">
				<form>
					<div class="col-md-3">
						<label for ="storeWebsite" >Select websites</label>
						<select name="store_webs[]" class="form-control select2" multiple="true">
							<option value="">-- Select a website --</option>
							@forelse($all_store_websites as $asw)
								<option value="{{ $asw->id }}" 
								@if(is_array(request('store_webs')) && in_array($asw->id, request('store_webs')))
									selected	
								@endif>{{ $asw->title }}</option>
							@empty
							@endforelse
						</select>
					</div>
					<div class="col-md-3">
						<label for ="storeWebsite" >Select Categories</label>
						<select name="categories[]" class="form-control select2" multiple="true">
							<option value="">-- Select a categories --</option>
							@forelse($categories as $ct)
								<option value="{{ $ct->id }}" 
									@if(is_array(request('categories')) && in_array($ct->id, request('categories')))
									selected
								@endif>{{ $ct->title }}</option>
							@empty
							@endforelse
						</select>
					</div>
					<div class="col-md-3">
						<label for ="storeWebsite" >Select Master categories</label>
						<select name="master_cat[]" class="form-control select2" multiple="true">
							<option value="">-- Select a Master categories --</option>
							@forelse($master_categories as $masterCat)
								<option value="{{ $masterCat->id }}" 
									@if(is_array(request('master_cat')) && in_array($masterCat->id, request('master_cat')))
									selected	
								@endif>{{ $masterCat->title }}</option>
							@empty
							@endforelse
						</select>
					</div>
					<div class="col-md-2"><br>
						<button type="submit" class="btn btn-secondary">Search</button>
						<a href="{{route('site-asset.index')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@if (Session::has('message'))
	{{ Session::get('message') }}
@endif
<br />
<div class="row mt-2">
	<div class="col-md-12 margin-tb infinite-scroll">
		<div class="row">
			<div class="table-responsive">
			<table class="table table-bordered" id="documents-table">
				<thead>
					<tr>
						<th><input type="checkbox" id="checkAll" title="click here to select all" /></th>
						<th width="10%">Categories</th>
						<th width="10%">Master Categories</th>
						@foreach($store_websites as $sw) 
							<th>{{ $sw->title }}</th>
						@endforeach
					</tr>
				</thead>
				<tbody class="infinite-scroll-pending-inner">
					@include("storewebsite::site-asset.partials.data")
				</tbody>
			</table>
			</div>
		</div>
	</div>
</div>
<div id="create-quick-task" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form action="{{ route('task.create.task.shortcut') }}" method="post">
				<?php echo csrf_field(); ?>
				<div class="modal-header">
					<h4 class="modal-title">Create Task</h4>
				</div>
				<div class="modal-body">

					<input class="form-control" type="hidden" name="category_id" id="category_id" />
					<input class="form-control" type="hidden" name="site_id" id="site_id" />
					<div class="form-group">
						<label for="">Subject</label>
						<input class="form-control" type="text" id="hidden-task-subject" name="task_subject" />
					</div>
					<div class="form-group">
						<label for="">Task Type</label>
						<br />
						<select class="form-control" style="width:100%;" name="task_type" tabindex="-1" aria-hidden="true">
							<option value="0">Other Task</option>
							<option value="4">Developer Task</option>
						</select>
					</div>

					<div class="form-group">
                        <label for="repository_id">Repository:</label>
                        <br>
                        <select style="width:100%" class="form-control 	" id="repository_id" name="repository_id">
                        	<option value="">-- select repository --</option>
                            @foreach (\App\Github\GithubRepository::all() as $repository)
                                <option value="{{ $repository->id }}">{{ $repository->name }}</option>
                            @endforeach
                        </select>
                    </div>

					<div class="form-group">
						<label for="">Details</label>
						<input class="form-control" type="text" name="task_detail" />
					</div>

					<div class="form-group">
						<label for="">Cost</label>
						<input class="form-control" type="text" name="cost" />
					</div>

					<div class="form-group">
						<label for="">Assign to</label>
						<select name="task_asssigned_to" class="form-control select2">
							@foreach($allUsers as $user)
							<option value="{{$user->id}}">{{$user->name}}</option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label for="">Create Review Task?</label>
						<div class="form-group">
								<input type="checkbox" name="need_review_task" value="1" />
						</div>
					</div>

                            
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-default create-task">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div id="dev_task_statistics" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h2>Dev Task statistics</h2>
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>
			<div class="modal-body" id="dev_task_statistics_content">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<tbody>
							<tr>
								<th>Task type</th>
								<th>Task Id</th>
								<th>Assigned to</th>
								<th>Description</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="download_asset_data_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
		<form action='{{ route("site-asset.download") }}' method="POST">
			<?php echo csrf_field(); ?>
			<div class="modal-content">
				<div class="modal-body">
					<div class="col-md-6">
						Select the content type for download
					</div>
					<div class="col-md-6">
						<input type='hidden' id="download_website_id" name="download_website_id">
						<select id='media_type' name='media_type' class="form-control" required>
							<option value="">Select type</option>
							<option value="PSDD">PSD - DESKTOP</option>
							<option value="PSDM">PSD - MOBILE</option>
							<option value="PSDA">PSD - APP</option>
							<option value="FIGMA">FIGMA</option>
						</select>
					</div>
				</div>
			<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-default download-document-site-asset-btn">Download</button>
				</div>
			</div>	
		</form>
    </div>
</div>
@endsection


@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
<script type="text/javascript">
$(document).on('click', '.create-quick-task', function() {
	var $this = $(this);
	site = $(this).data("id");
	title = $(this).data("title");
	category_id = $(this).data("category_id");
	if (!title || title == '') {
		toastr["error"]("Please add title first");
		return;
	}

	$("#create-quick-task").modal("show");

	$("#hidden-task-subject").val(title);
	$('#site_id').val(site);
	$('#category_id').val(category_id);
});

$(document).on("click", ".create-task", function(e) {
	e.preventDefault();
	var form = $(this).closest("form");
	$.ajax({
		url: form.attr("action"),
		type: 'POST',
		data: form.serialize(),
		beforeSend: function() {
			$(this).text('Loading...');
		},
		success: function(response) {
			if (response.code == 200) {
				form[0].reset();
				toastr['success'](response.message);
				$("#create-quick-task").modal("hide");
			} else {
				toastr['error'](response.message);
			}
		}
	}).fail(function(response) {
		toastr['error'](response.responseJSON.message);
	});
});


$(document).on("click", ".count-dev-customer-tasks", function() {
	var $this = $(this);
	var site_id = $(this).data("id");
	$.ajax({
		type: 'get',
		url: '/site-development/countdevtask/' + site_id,
		dataType: "json",
		beforeSend: function() {
			$("#loading-image").show();
		},
		success: function(data) {
			$("#dev_task_statistics").modal("show");
			var table = `<div class="table-responsive">
				<table class="table table-bordered table-striped">
					<tr>
						<th width="4%">Tsk Typ</th>
						<th width="4%">Tsk Id</th>
						<th width="7%">Asg to</th>
						<th width="12%">Desc</th>
						<th width="12%">Sts</th>
						<th width="33%">Communicate</th>
						<th width="10%">Action</th>
					</tr>`;
			for (var i = 0; i < data.taskStatistics.length; i++) {
				var str = data.taskStatistics[i].subject;
				var res = str.substr(0, 100);
				var status = data.taskStatistics[i].status;
				if(typeof status=='undefined' || typeof status=='' || typeof status=='0' ){ status = 'In progress'};
				table = table + '<tr><td>' + data.taskStatistics[i].task_type + '</td><td>#' + data.taskStatistics[i].id + '</td><td class="expand-row-msg" data-name="asgTo" data-id="'+data.taskStatistics[i].id+'"><span class="show-short-asgTo-'+data.taskStatistics[i].id+'">'+data.taskStatistics[i].assigned_to_name.replace(/(.{6})..+/, "$1..")+'</span><span style="word-break:break-all;" class="show-full-asgTo-'+data.taskStatistics[i].id+' hidden">'+data.taskStatistics[i].assigned_to_name+'</span></td><td class="expand-row-msg" data-name="res" data-id="'+data.taskStatistics[i].id+'"><span class="show-short-res-'+data.taskStatistics[i].id+'">'+res.replace(/(.{7})..+/, "$1..")+'</span><span style="word-break:break-all;" class="show-full-res-'+data.taskStatistics[i].id+' hidden">'+res+'</span></td><td>' + status + '</td><td><div class="col-md-10 pl-0 pr-1"><textarea rows="1" style="width: 100%; float: left;" class="form-control quick-message-field input-sm" name="message" placeholder="Message"></textarea></div><div class="p-0"><button class="btn btn-sm btn-xs send-message" title="Send message" data-taskid="'+ data.taskStatistics[i].id +'"><i class="fa fa-paper-plane"></i></button></div></td><td><button type="button" class="btn btn-xs load-communication-modal load-body-class" data-object="' + data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i].id + '" title="Load messages" data-dismiss="modal"><i class="fa fa-comments"></i></button>';
				table = table + '<a href="javascript:void(0);" data-task-type="'+data.taskStatistics[i].task_type +'" data-id="' + data.taskStatistics[i].id + '" class="delete-dev-task-btn btn btn-xs"><i class="fa fa-trash"></i></a>';
				table = table + '<button type="button" class="btn btn-xs  preview-img pd-5" data-object="' + data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i].id + '" data-dismiss="modal"><i class="fa fa-list"></i></button></td>';
				table = table + '</tr>';
			}
			table = table + '</table></div>';
			$("#loading-image").hide();
			$(".modal").css("overflow-x", "hidden");
			$(".modal").css("overflow-y", "auto");
			$("#dev_task_statistics_content").html(table);
		},
		error: function(error) {
			$("#loading-image").hide();
		}
	});
});
$(".select2").select2();

$("#checkAll").click(function(){
	$('input:checkbox').not(this).prop('checked', this.checked);
});

$(document).on("click", ".download_asset_data", function() {
	if($("input:checkbox:checked").length > 0)
	{
		var sList = [];
		var i = 0;
		$("input:checkbox:checked").each(function (){
			sList[i] = $(this).val();
			i++; 
		});
		$("#download_website_id").val(JSON.stringify(sList));
		$("#download_asset_data_modal").modal('show');
	}else{
		toastr["error"]("Please select a checkbox", "error");
	}
});

</script>

@endsection