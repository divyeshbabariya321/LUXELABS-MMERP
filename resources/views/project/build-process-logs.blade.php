@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Build process logs</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="mt-3 col-md-12">
                    <form action="{{ route('project.buildProcessLogs') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-2 pd-sm">
                                <h5>Search projects</h5>	
                                <select class="form-control globalSelect2" multiple="true" id="project-select" name="projects[]" placeholder="Select projects">
                                    @foreach($projects as $project)
                                    <option value="{{ $project->id }}" @if(in_array($project->id, $reqproject)) selected @endif>{{ $project->name }}</option>
                                    @endforeach
                                </select> 
                            </div>

                            <div class="col-md-2 pd-sm">                                      
                                 <h5>Search organizations</h5>	
                                <select class="form-control globalSelect2" multiple="true" id="organizations-select" name="organizations[]" placeholder="Select organizations">
                                    @foreach($organizations as $organization)
                                    <option value="{{ $organization->id }}" @if(in_array($organization->id, $reqorganizations)) selected @endif>{{ $organization->name }}</option>
                                    @endforeach
                                </select> 
                            </div>
                            <div class="col-md-2 pd-sm">                                
                                <h5>Search Repository</h5>	
                                <select class="form-control globalSelect2" multiple="true" id="repo_ids" name="repo_ids[]" placeholder="Select Repos">
                                    @foreach($repo_names as $repo_name)
                                    <option value="{{ $repo_name->id }}"  @if(in_array($repo_name->id, $reqrepoids)) selected @endif>{{ $repo_name->name }}</option>
                                    @endforeach
                                </select> 
                            </div>
                            <div class="col-md-2 pd-sm">                                
                                <h5>Search Build By</h5>	
                                    <select class="form-control globalSelect2" multiple="true" id="platform-Users" name="users[]" placeholder="Select Users">
                                        @foreach($users as $user)
                                        <option value="{{ $user->id }}" @if(in_array($user->id, $requsers)) selected @endif>{{ $user->name }}</option>
                                        @endforeach
                                    </select> 
                            </div>
                            <div class="col-md-2 pd-sm">                               
                                 <h5>Search Status</h5>	
                                    <select class="form-control globalSelect2" multiple="true" id="status-select" name="status[]" placeholder="Select Status">
                                        <option value="SUCCESS" @if(in_array('SUCCESS', $reqstatus)) selected @endif>success</option>
                                        <option value="FAILURE" @if(in_array('FAILURE', $reqstatus)) selected @endif>failure</option>
                                        <option value="RUNNING" @if(in_array('RUNNING', $reqstatus)) selected @endif>running</option>
                                        <option value="WAITING" @if(in_array('WAITING', $reqstatus)) selected @endif>waiting</option>
                                        <option value="UNSTABLE" @if(in_array('UNSTABLE', $reqstatus)) selected @endif>unstable</option>
                                        <option value="ABORTED" @if(in_array('ABORTED', $reqstatus)) selected @endif>aborted</option>
                                    </select> 
                            </div>
                            <div class="col-md-2 pd-sm">                               
                                 <h5>Search Branch name</h5>	
                                <input class="form-control" type="text" id="search_branch_name" placeholder="Search Branch name" name="search_branch_name" value="{{ request()->get('search_branch_name') }}">
                            </div>
                            <div class="col-md-2 pd-sm">                              
                                <h5>Search Build Number</h5>	
                                <input class="form-control" type="text" id="search_build_number" placeholder="Search Build Number" name="search_build_number" value="{{ request()->get('search_build_number') }}">
                            </div>
                            <div class="col-md-2 pd-sm">                             
                                <h5>Search Build Name</h5>	
                                <input class="form-control" type="text" id="search_build_name" placeholder="Search Build Name" name="search_build_name" value="{{ request()->get('search_build_name') }}">
                            </div>
                            <div class="col-md-2 pd-sm">                               
                                <h5>Search By keyword</h5>	
                                <input type="text" name="keyword" placeholder="keyword" class="form-control" value="{{ request()->get('keyword') }}">
                            </div>
                            <div class="col-md-2 pd-sm"><br>
                                <button type="submit" class="btn btn-image search">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                                <a href="{{ route('project.buildProcessLogs') }}" class="btn btn-image" id="">
                                    <img src="/images/resend2.png" style="cursor: nwse-resize;">
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                <br>
                <br>
                <div class="">
                    <br>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#serverenv-create"> Create Serverenv </button>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#project-create"> Create Project </button>
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

@include('project.partials.serverenv-create-modal')
@include('project.partials.project-create-modal')


<div class="tab-content">
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;" id="build-process-logs-list">
                        <tr>
                            <th width="2%">ID</th>
                            <th width="10%">Project</th>
                            <th width="10%">Organization</th>
                            <th width="10%">Repo</th>
                            <th width="10%">Branch</th>
                            <th width="10%">Build By</th>
                            <th width="10%">Build Number</th>
                            <th width="10%">Build Name</th>
                            <th width="10%">PR</th>
                            <th width="10%">Initiate From</th>
                            <th width="10%">Text</th>
                            <th width="10%">Command</th>
                            <th width="5%">Status</th>
                            <th width="5%">Date</th>
                            <th width="5%">Job Status</th>
                        </tr>
                        @foreach ($responseLogs as $key => $responseLog)
                            <tr data-id="{{ $responseLog->id }}">
                                <td>{{ $responseLog->id }}</td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $responseLog->project->name ?? '' }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $responseLog->organization->name ?? '' }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $responseLog->repository->name ?? '' }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $responseLog->github_branch_state_name }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($responseLog->usersname) > 30 ? substr($responseLog->usersname, 0, 30).'...' :  $responseLog->usersname }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $responseLog->usersname }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $responseLog->build_number }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $responseLog->build_name }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $responseLog->build_pr }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($responseLog->initiate_from) > 30 ? substr($responseLog->initiate_from, 0, 30).'...' :  $responseLog->initiate_from }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $responseLog->initiate_from }}
                                    </span>
                                </td>
                                <td style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($responseLog->text) > 10 ? substr($responseLog->text, 0, 10).'...' :  $responseLog->text }}
								       <i class="fa fa-eye show_logs show-full-text" data-full-text="{{ nl2br($responseLog->text) }}" style="color: #808080;float: right;"></i>
                                    </span>
                                </td>
                                <td style="word-break: break-all">
                                    @if($responseLog->command)
                                    <span class="td-mini-container">
                                       {{ strlen($responseLog->command) > 10 ? substr($responseLog->command, 0, 10).'...' :  $responseLog->command }}
								       <i class="fa fa-eye show_logs show-full-command" data-full-text="{{ nl2br($responseLog->command) }}" style="color: #808080;float: right;"></i>
                                    </span>
                                    @endif
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $responseLog->status }}
                                    <button type="button" class="btn btn-xs show-status-modal" title="Show Status History" data-id="{{$responseLog->id}}"><i class="fa fa-info-circle"></i></button>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $responseLog->created_at }}
                                </td>
                                <td>
                                    @if(count($responseLog->job_status) > 0)
                                        @foreach ($responseLog->job_status as $key=>$value )
                                            <strong>{{"Job: "}}</strong>{{ $key."(".$value.") "}}
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                {!! $responseLogs->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>
<div id="status-history-listing" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Status History</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">

                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Old Status</th>
                                <th width="30%">New Status</th>
                                <th width="20%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="status-history-listing-view">
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

<div class="modal" tabindex="-1" role="dialog" id="show_full_text_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Text</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="show_full_text_modal_content">
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="show_full_command_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Command</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="show_full_command_modal_content">
                        
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
    #show_full_text_modal .modal-body {
      max-height: 400px; /* Maximum height for the scrollable area */
      overflow-y: auto; /* Enable vertical scrolling when content exceeds the height */
    }

    #show_full_command_modal .modal-body {
      max-height: 400px; /* Maximum height for the scrollable area */
      overflow-y: auto; /* Enable vertical scrolling when content exceeds the height */
    }
</style>
@endsection
@section('scripts')
<script>
$('.select2').select2();

$( document ).ready(function() {
    $(document).on('click', '.expand-row', function () {
        
        $(this).find('.td-mini-container').toggleClass('hidden');
        $(this).find('.td-full-container').toggleClass('hidden');
    });

    $(document).on('click', '.show-full-text', function() {
        var fullText = $(this).data('full-text');
        $('#show_full_text_modal').modal('show');
        $('#show_full_text_modal_content').html(fullText);
    });

    $(document).on('click', '.show-full-command', function() {
        var fullCommand = $(this).data('full-text');
        $('#show_full_command_modal').modal('show');
        $('#show_full_command_modal_content').html(fullCommand);
    });


    $(document).on('click', '.show-status-modal', function() {
            var id = $(this).attr('data-id');
            $("#loading-image-preview").show();
            $.ajax({
                method: "GET",
                url: "{{ route('project.buildProcessStatusLogs')}}",
                dataType: "json",
                data: {
                    id:id,
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${v.id} </td>
                                        <td> <div class="expand-row module-text" style="width: 100%;"><div class="flex  items-center justify-left td-mini-container" title="${v.old_status}">${setStringLength(v.old_status, 50)}</div><div class="flex items-center justify-left td-full-container hidden" title="${v.old_status}">${v.old_status}</div></div> </td>
                                        <td> <div class="expand-row module-text" style="width: 100%;"><div class="flex  items-center justify-left td-mini-container" title="${v.status}">${setStringLength(v.status, 50)}</div><div class="flex items-center justify-left td-full-container hidden" title="${v.status}">${v.status}</div></div> </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#status-history-listing").find(".status-history-listing-view").html(html);
                        $("#status-history-listing").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                    $("#loading-image-preview").hide();
                }
            });
        });
});



</script>
@endsection