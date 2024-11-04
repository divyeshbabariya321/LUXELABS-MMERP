@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Projects ({{ $projects->total() }})</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-6">
                    <form action="{{ route('project.index') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-4 pd-sm">
                                <h5> Search Keywords </h5>
                                <input type="text" name="keyword" placeholder="keyword" class="form-control" value="{{ request()->get('keyword') }}">
                            </div>
                            <div class="col-md-4">
                                <h5> Search Projects </h5>
                                <?php 
									if(request('store_websites_search')){   $store_websites_search = request('store_websites_search'); }
									else{ $store_websites_search = []; }
								?>
								<select name="store_websites_search[]" id="store_websites_search" class="form-control select2" multiple>
									<option value="" @if($store_websites_search=='') selected @endif>-- Select a Store website --</option>
									@forelse($store_websites as $swId => $swName)
									<option value="{{ $swId }}" @if(in_array($swId, $store_websites_search)) selected @endif>{!! $swName !!}</option>
									@empty
									@endforelse
								</select>
                            </div>
                            
                            <div class="col-md-4 pd-sm pl-0 mt-2"><br><br>
                                 <button type="submit" class="btn btn-image search">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                                <a href="{{ route('project.index') }}" class="btn btn-image" id="">
                                    <img src="/images/resend2.png" style="cursor: nwse-resize;">
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-6">
                    <div class="pull-right">
                        <button type="button" class="btn btn-secondary" onclick="bulkBuildProcess()"> Run Mulitple Build Process </button>
                        <a href="{{ route('project.buildProcessErrorLogs') }}" class="btn btn-secondary"> Build Process Error Logs </a>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#serverenv-create"> Create Serverenv </button>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#projecttype-create"> Create Project Type </button>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#project-create"> Create Project </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<div class="tab-content">
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;" id="project-list">
                        <tr>
                            <th width="2%"></th>
                            <th width="2%">ID</th>
                            <th width="10%">Project Name</th>
                            <th width="10%">Project Type</th>
                            <th width="10%">Job Name</th>
                            <th width="10%">Serverenv</th>
                            <th width="10%">Store Website Names</th>
                            <th width="5%">Action</th>
                        </tr>
                        @foreach ($projects as $key => $project)
                            <tr data-id="{{ $project->id }}">
								<td><input type="checkbox" name="bulk_process_select[]" class="d-inline bulk_build_process_select" value="{{$project->id}}"></td>
                                <td>{{ $project->id }}</td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($project->name) > 30 ? substr($project->name, 0, 30).'...' :  $project->name }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $project->name }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $project->project_type }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($project->job_name) > 30 ? substr($project->job_name, 0, 30).'...' :  $project->job_name }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $project->job_name }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $project->serverenv }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($project->store_website_names) > 30 ? substr($project->store_website_names, 0, 30).'...' :  $project->store_website_names }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $project->store_website_names }}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" data-id="{{ $project->id }}" class="btn btn-xs btn-edit-project">
                                        <i class="fa fa-pencil"></i>
                                    </button>

                                    {{ html()->form('DELETE', route('project.destroy', [$project->id]))->style('display:inline')->open() }}
                                    <button type="submit" class="btn btn-xs">
                                        <i class="fa fa-trash" style="color: #808080;"></i>
                                    </button>
                                    {{ html()->form()->close() }}
                                    <button title="Build Process" data-job="{{ $project->job_name }}" data-id="{{ $project->id }}" type="button" class="btn open-build-process-template" style="padding:1px 0px;">
                                        <a href="javascript:void(0);" style="color:gray;"><i class="fa fa-simplybuilt"></i></a>
                                    </button>

                                    
                                    <button title="Build Process History" data-id="{{ $project->id }}" type="button" class="btn" style="padding:1px 0px;">
                                        <a href="{{route("project.buildProcessLogs", $project->id)}}" style="color:gray;"><i class="fa fa-info-circle"></i></a>
                                    </button>

                                    {{-- Directly initiate the build for (org: LUDXB & Repository:brands-labels & Branch: stage ) --}}
                                    {{ html()->form('POST', route('project.buildProcess', ))->style('display:inline')->id("initiate-build-directly")->open() }}
                                    {{ html()->hidden('project_id', $project->id)->class('form-control') }}
                                    {{ html()->hidden('job_name', $project->job_name)->class('form-control') }}
                                    {{ html()->hidden('organization', 2)->class('form-control') }}
                                    {{ html()->hidden('repository', 353671452)->class('form-control') }}
                                    {{ html()->hidden('branch_name', 'stage')->class('form-control') }}
                                    {{ html()->hidden('initiate_from', 'Project page - stage branch build')->class('form-control') }}
                                    <button title="Please initiate stage branch build" type="submit" class="btn btn-xs">
                                        <i class="fa fa-gear" style="color: #808080;"></i>
                                    </button>
                                    {{ html()->form()->close() }}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                {!! $projects->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>
<div id="build-process-history" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg" style="max-width: 95%;width: 100%;">
		<!-- Modal content-->
		<div class="modal-content ">
			<div class="modal-header">
				<h4 class="modal-title">Build Process History</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="table-responsive mt-3">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>ID</th>
								<th>Build By</th>
								<th>Build Number</th>
								<th>Build Name</th>
								<th>Text</th>
								<th>Status</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody id="build-process-history_tbody">

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@include('project.partials.project-create-modal')
@include('project.partials.project-edit-modal')
@include('project.partials.serverenv-create-modal')
@include('project.partials.projecttype-create-modal')
@include('project.partials.build-process-modal')

<div id="build-multiple-process-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Build Process</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <form id="build-mulitiple-process">
                            <input type="hidden" class="build_process_project_id" name="project_id" value="">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <strong>Organizations:</strong>
                                        <select name="organization" id="build_bulk_organization" class="form-control select2" style="width: 100%!important">
                                            <option value="" selected disabled>-- Select a Organizations --</option>
                                            @forelse($organizations as $organization)
                                            <option value="{{ $organization->id }}" {{ $organization->id === 2 ? 'selected' : '' }}>
                                                {{ $organization->name }}
                                            </option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <strong>Repository:</strong>
                                        <select name="repository" id="build_bulk_repository" class="form-control select2" style="width: 100%!important">
                                            <option value="" selected disabled>-- Select a Repository --</option>
                                        </select>
                                    </div>
                                </div>                            
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <strong>Branch Name:</strong>
                                        <select name="branch_name" id="build_bulk_branch_name" class="form-control select2" style="width: 100%!important">
                                            <option value="" selected disabled>-- Select a Branch --</option>
                                        </select>
                                    </div>
                                </div>    
                                <div class="col-md-12"> 
                                <div class="form-group">
                                    <strong>Meta Package Branch :</strong>
                                    {{ html()->text('metapackage_branch', 'master')->placeholder('metapackage_branch')->id('metapackage_branch')->class('form-control') }}
                                </div>
                                </div>                   
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="hidden" name="initiate_from" value="Project Page - Build Process">
                                        <button data-id=""class="btn btn-secondary update-mulitple-build-process">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('.select2').select2();
    $(document).ready(function(){
        $(document).on("click",".open-build-process-template",function(e) {
            e.preventDefault();
            var id=$(this).attr("data-id");
            var job=$(this).attr("data-job");
            $(".build_process_project_id").val(id);
            $("#build-process #job_name").val(job);
            $('#build-process-modal').modal('show'); 
        });

        $("#build_organization").on('change', function(e) {
            var url = "{{ route('project.getGithubRepo') }}";
            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: "GET",
                url: url,
                data: {
                    build_organization: jQuery('#build_organization').val(),
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
                
            }).done(function(response) {
                jQuery('#build_repository').html(response.data);
                jQuery('#build_branch_name').html("");
                $("#loading-image-preview").hide();
            }).fail(function(response) {});
        });

        $("#build_repository").on('change', function(e) {
            var url = "{{ route('project.getGithubBranches') }}";
            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: "GET",
                url: url,
                data: {
                    build_repository: jQuery('#build_repository').val(),
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
            }).done(function(response) {
                jQuery('#build_branch_name').html(response.data);
                $("#loading-image-preview").hide();
            }).fail(function(response) {});
        
        });
        $(document).on("click", ".open-build-process-history", function(href) {
            $.ajax({
                type: 'POST',
                url: 'project/buildProcessLogs/'+ $(this).data('id'),
                beforeSend: function () {
                    $("#loading-image-preview").show();
                },
                data: {
                    _token: "{{ csrf_token() }}",
                    id: $(this).data('id'),
                },
                dataType: "json"
            }).done(function (response) {
                $("#loading-image-preview").hide();
                if (response.code == 200) {
                    
                    $('#build-process-history_tbody').html(response.data);
                    $('#build-process-history').modal('show');
                    toastr['success'](response.message, 'success');
                }
            }).fail(function (response) {
                $("#loading-image-preview").hide();
                console.log("Sorry, something went wrong");
            });
        });

        $(".btn-edit-project").on('click', function(e) {
            var url = "{{ route('project.edit', '') }}/" + $(this).data("id");
            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: "GET",
                url: url,
            }).done(function(response) {
                $("#project-edit-form #id").val(response.data.id);
                $("#project-edit-form #name").val(response.data.name);
                $("#project-edit-form #job_name").val(response.data.job_name);
                $("#project-edit-form #serverenv").val(response.data.serverenv).trigger('change');
                $("#project-edit-form #project_type").val(response.data.project_type).trigger('change');
                var selectedWebsites = [];
                $(response.data.store_websites).each(function(index, store_websites) {
                    selectedWebsites.push(store_websites.id);
                });
                $("#project-edit-form #assign-new-website").val(selectedWebsites).trigger('change');
                $("#project-edit").modal("show");
            }).fail(function(response) {});
        });

        $(document).on('submit', 'form#initiate-build-directly', function(e){
            e.preventDefault();
            var self = $(this);
            let formData = new FormData(document.getElementById("initiate-build-directly"));
            var button = $(this).find('[type="submit"]');
            $.ajax({
                url: '{{ route("project.buildProcess") }}',
                type: "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                beforeSend: function() {
                    $("#loading-image-preview").show();
                },
                complete: function() {
                    $("#loading-image-preview").hide();
                },
                success: function(response) {
                    if(response.code=='200'){
                        toastr["success"](response.message);
                        // $('#build-process-modal').modal('hide');
                    }else{
                        toastr["error"](response.message);
                    }
                    $("#loading-image-preview").hide();
                },
                error: function(xhr, status, error) { // if error occured
                    $("#loading-image-preview").hide();
                },
            });
        });
    })

    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });
   
    $(document).ready(function () {
        function RepositoryDropdown() {
            var selectedOrganizationId = $('#build_organization').val();
            if (!selectedOrganizationId) {
                $('#build_repository').html('<option value="" selected disabled>-- Select a Repository --</option>');
                return;
            }

            var url = "{{ route('project.getGithubRepo') }}";
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "GET",
                url: url,
                data: {
                    build_organization: selectedOrganizationId,
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
            }).done(function(response) {
                var dynamicOptions = response.data;
                $('#build_repository').html(dynamicOptions);
                var defaultRepoId = 353671452; // Replace with the default Repository ID you want to set
                $("#build_repository").val(defaultRepoId);
                $("#build_repository").trigger("change");
                $("#loading-image-preview").hide();
            }).fail(function(response) {});
        }

        $("#build_organization").on('change', function(e) {
            RepositoryDropdown();
        });

        RepositoryDropdown();
    });

    $(document).ready(function () {
        function branchDropdown() {
            var selectedReponId = 353671452;
            if (!selectedReponId) {
                $('#build_branch_name').html('<option value="" selected disabled>-- Select a Branches --</option>');
                return;
            }

            var url = "{{ route('project.getGithubBranches') }}";
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "GET",
                url: url,
                data: {
                    build_repository: selectedReponId,
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
            }).done(function(response) {
                var dynamicOptions = response.data;
                $('#build_branch_name').html(dynamicOptions);
                var defaultBranchName = "stage"; // Replace with the default Branch Name you want to set
                $("#build_branch_name").val(defaultBranchName);
                $("#build_branch_name").trigger("change");
                $("#loading-image-preview").hide();
            }).fail(function(response) {});
        }

        $("#build_repository").on('change', function(e) {
            branchDropdown();
        });

        branchDropdown();
    });

    function bulkBuildProcess()
    {
        event.preventDefault();
        var selectedIds = [];

		$(".bulk_build_process_select").each(function () {
			if ($(this).prop("checked") == true) {
				selectedIds.push($(this).val());
			}
		});

		if (selectedIds.length == 0) {
			alert('Please select any row');
			return false;
		}

		if(confirm('Are you sure you want to perform this action?')==false)
		{
			console.log(selectedIds);
			return false;
		}

        $("#build-multiple-process-modal").modal("show");

        $(document).on('submit', 'form#build-mulitiple-process', function(e){
            e.preventDefault();
             var self = $(this);           
             var formData = new FormData(document.getElementById("build-mulitiple-process"));
            formData.append('selectedIds', selectedIds);
            var button = $(this).find('[type="submit"]');
         
            $.ajax({
                    type: "post",
                    url: "{{ route('project.Multiple.buildProcess') }}",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    cache: false,
                    data:formData,
                    beforeSend: function() {
                        $(this).attr('disabled', true);
                        $("#loading-image-preview").show();
                    }
                    }).done(function(data) {
                        toastr["success"]("Bulk update values completed successfully!", "Message")
                        $("#loading-image-preview").hide();
                        window.location.reload();
                    }).fail(function() {
                        toastr["error"]("something Went Wrong");
                        $("#loading-image-preview").hide();
                        window.location.reload();
                    });
                });
        }

    $(document).ready(function () {
        function RepositoryDropdown() {
            var selectedOrganizationId = $('#build_bulk_organization').val();
            if (!selectedOrganizationId) {
                $('#build_bulk_repository').html('<option value="" selected disabled>-- Select a Repository --</option>');
                return;
            }

            var url = "{{ route('project.getGithubRepo') }}";
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "GET",
                url: url,
                data: {
                    build_organization: selectedOrganizationId,
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
            }).done(function(response) {
                var dynamicOptions = response.data;
                $('#build_bulk_repository').html(dynamicOptions);
                var defaultRepoId = 353671452; 
                $("#build_bulk_repository").val(defaultRepoId);
                $("#build_bulk_repository").trigger("change");
                $("#loading-image-preview").hide();
            }).fail(function(response) {});
        }

        $("#build_bulk_organization").on('change', function(e) {
            RepositoryDropdown();
        });

        RepositoryDropdown();
    });

    $(document).ready(function () {
        function branchDropdown() {
            var selectedReponId = 353671452;
            if (!selectedReponId) {
                $('#build_bulk_branch_name').html('<option value="" selected disabled>-- Select a Branches --</option>');
                return;
            }

            var url = "{{ route('project.getGithubBranches') }}";
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "GET",
                url: url,
                data: {
                    build_repository: selectedReponId,
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
            }).done(function(response) {
                var dynamicOptions = response.data;
                $('#build_bulk_branch_name').html(dynamicOptions);
                var defaultBranchName = "stage"; 
                $("#build_bulk_branch_name").val(defaultBranchName);
                $("#build_bulk_branch_name").trigger("change");
                $("#loading-image-preview").hide();
            }).fail(function(response) {});
        }

        $("#build_bulk_repository").on('change', function(e) {
            var selectedRepositoryId = $('#build_bulk_repository').val();
            if (selectedRepositoryId == 353671452) {
                $('#metapackage_branch').closest('.form-group').show();
            } else {
                $('#metapackage_branch').closest('.form-group').hide();
            }
            branchDropdown();
        });

        branchDropdown();
    });

</script>
@endsection