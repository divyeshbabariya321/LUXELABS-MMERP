@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">deployement Versions ({{$deploymentVersions->total()}})</h2>
		</div>
	</div>
	<div class="mt-3 col-md-12">
		<form action="{{route('deployement-version.index')}}" method="get" class="search">
			<div class="col-lg-2">
				<br>
				<input class="form-control" type="text" id="search_versionNumber" placeholder="Search Version number" name="search_versionNumber" value="{{ $search_versionNumber ?? '' }}">
			</div>
			<div class="col-lg-2">
				<br>
				<input class="form-control" type="text" id="search_jobName" placeholder="Search Job Name" name="search_jobName" value="{{ $search_jobName ?? '' }}">
			</div>
			<div class="col-lg-2">
				<br>
				<input class="form-control" type="text" id="search_branchName" placeholder="Search Branch Name" name="search_branchName" value="{{ $search_branchName ?? '' }}">
			</div>
			<div class="col-lg-2">
				<b>Deployment Date:</b> 
				<input class="form-control" type="date" name="deployment_date" value="{{$deployment_date ?? ''}}">
			</div>
			<div class="col-lg-2">
				<b>PR Date:</b> 
				<input class="form-control" type="date" name="pr_date" value="{{$pr_date ?? ''}}">
			</div>

			<div class="col-lg-2">
				<br>
				<button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
				   <img src="{{ asset('images/search.png') }}" alt="Search">
			   </button>
			   <a href="{{route('deployement-version.index')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
			</div>
		</form>
	</div>
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th width="3%">ID</th>
			    	<th width="10%">Version Number</th>
			        <th width="10%">Build Number</th>
			        <th width="10%">Job Name</th>
			        <th width="10%">Revision</th>
			        <th width="10%">Branch Name</th>
                    <th width="10%">Pull No</th>
                    <th width="10%">Deployment Date</th>
                    <th width="10%">PR Date</th>
					<th width="10%">Action</th>

                </tr>
		    	<tbody>
                    @foreach ($deploymentVersions as $deploymentVersion)
                        <tr>
                            <td>{{$deploymentVersion->id}}</td>				
                            <td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                   {{ strlen($deploymentVersion->version_number) > 30 ? substr($deploymentVersion->version_number, 0, 30).'...' :  $deploymentVersion->version_number }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $deploymentVersion->version_number }}
                                </span>
                            </td>
							<td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                   {{ strlen($deploymentVersion->build_number) > 30 ? substr($deploymentVersion->build_number, 0, 30).'...' :  $deploymentVersion->build_number }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $deploymentVersion->build_number }}
                                </span>
                            </td>
							<td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                   {{ strlen($deploymentVersion->job_name) > 30 ? substr($deploymentVersion->job_name, 0, 30).'...' :  $deploymentVersion->job_name }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $deploymentVersion->job_name }}
                                </span>
                            </td>
							<td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                   {{ strlen($deploymentVersion->revision) > 30 ? substr($deploymentVersion->revision, 0, 30).'...' :  $deploymentVersion->revision }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $deploymentVersion->revision }}
                                </span>
							</td>
							<td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                   {{ strlen($deploymentVersion->branch_name) > 30 ? substr($deploymentVersion->branch_name, 0, 30).'...' :  $deploymentVersion->branch_name }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $deploymentVersion->branch_name }}
                                </span>
							</td>
							<td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                   {{ strlen($deploymentVersion->pull_no) > 30 ? substr($deploymentVersion->pull_no, 0, 30).'...' :  $deploymentVersion->pull_no }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $deploymentVersion->pull_no }}
                                </span>
							</td>
                            <td>{{$deploymentVersion->deployment_date}}</td>
                            <td>{{$deploymentVersion->pr_date}}</td>
							<td><button type="button" title="Deploy" data-id="{{$deploymentVersion->id}}" class="btn btn-xs btn-deploy-verison" data-toggle="modal" data-target="#create-server-modal" style="padding: 0px 5px !important";>
								<i class="fa fa-upload" aria-hidden="true"></i>
							</button>
							<button type="button" title="Restore revision" data-id="{{$deploymentVersion->id}}" class="btn btn-xs btn-restore-verison" style="padding: 0px 5px !important";>
								<i class="fa fa-simplybuilt" aria-hidden="true"></i>
							</button>
							<button type="button" class="btn btn-xs show-developing-log_history-modal" title="Show deploying History" data-id="{{$deploymentVersion->id}}" data-toggle="modal" data-target="#deployemnt-show-history" style="padding: 0px 5px !important";><i class="fa fa-info-circle"></i></button>
						</td>

						</tr>                        
                    @endforeach
		    	</tbody>
		    </thead>-
		</table>
		{!! $deploymentVersions->appends(Request::except('page'))->links() !!}
	</div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
</div>

<div id="create-server-modal" class="modal fade in" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <form action="">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="server">Servers:</label><br>
                        <select name="server" id="server" class="form-control">
                            <option value="qa">Qa</option>
                            <option value="production">Production</option>
                            <option value="live">Live</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary save-server-btn">Save </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="deployemnt-show-history" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 95%;width: 100%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><span class="modal-type">DeploymentVersion</span> History</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">

                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="50%"><span class="modal-type">Remark</span></th>
                                <th width="20%">Updated BY</th>
                                <th width="20%">Created Date</th>
                                <th width="20%">Action</th>
                            </tr>
                        </thead>
                        <tbody class="deployemnt-show-list-view">
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script type="text/javascript">

    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });

	$(document).ready(function () {
		$(document).on('click', '.btn-deploy-verison', function () {
			var deployVerId = $(this).data('id');

			$(document).on('click', '.save-server-btn', function () {
				var  selectedServerValue = $("#server").val();
				var selectedValue = $(this).val();
				$.ajax({
					url: "{{ route('deployement-version-jenkis') }}",
					type: 'GET',
					headers: {
						'X-CSRF-TOKEN': "{{ csrf_token() }}"
					},
					data: {
						deployVerId: deployVerId,
						selectedValue : selectedValue,
					},
					dataType: "json",
					beforeSend: function () {
						$("#loading-image").show();
					}
					}).done(function (response) {
						toastr['success'](response.message, 'success');
					$("#loading-image").hide();
					}).fail(function (response, ajaxOptions, thrownError) {
					toastr["error"](response.message);
					$("#loading-image").hide();
				});
		});
	});
});

	$(document).on('click', '.show-developing-log_history-modal ', function() {

		var id = $(this).attr('data-id');
		$.ajax({
			url: "/deploye-version/history/" + id,
			method: "GET",
			dataType: "json",
			beforeSend: function() {
				$("#loading-image").show();
			},
			success: function(response) {
				if (response.status) {
					$("#loading-image").hide();
					var html = "";
					$.each(response.data, function(k, v) {
						remarkText = v.deployversion !== null ? v.deployversion.version_number : 'Version not available';
						if(v.build_number!='' && v.build_number!=null){
							remarkText+="<br><br><b>Build Number</b><br>"+v.build_number;
						}
						if(v.error_message!='' && v.error_message!=null){
							remarkText+="<br><br><b>Error Message:</b><br>"+v.error_message;
						}
						if(v.error_code!='' && v.error_code!=null){
							remarkText+="<br><br><b>Error Code:</b><br>"+v.error_code;
						}
						html += `<tr>
									<td> ${k + 1} </td>
									<td> 
										${remarkText}
									</td>
									<td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
									<td> ${v.created_at} </td>
									<td><i class='fa fa-copy copy_remark' data-remark_text='${remarkText}'></i></td>
								</tr>`;
					});
					$("#deployemnt-show-history").find(".deployemnt-show-list-view").html(html);
				} else {
					toastr["error"](response.error, "Message");
				}
				$("#loading-image").hide();
			}
		});
	});

	$(document).on('click', '.btn-restore-verison', function () {
		var deployVersionId = $(this).data('id');
			$.ajax({
				url: "{{ route('deployement-restore-revision') }}",
				type: 'PoST',
				headers: {
					'X-CSRF-TOKEN': "{{ csrf_token() }}"
				},
				data: {
					deployVersionId: deployVersionId,
				},
				dataType: "json",
				beforeSend: function () {
					$("#loading-image").show();
				}
				}).done(function (response) {
					if(response.code == 200){
						toastr['success'](response.message, 'success');
					}else {
						toastr["error"](response.message);
					}
				$("#loading-image").hide();
				}).fail(function (response, ajaxOptions, thrownError) {
				$("#loading-image").hide();
			});
	});

</script>