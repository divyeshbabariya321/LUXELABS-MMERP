@extends('layouts.app')

@section('content')
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<script>
    var currentChatParams = {};
    currentChatParams.data = {
        page: 1
        , hasMore: true
    , };
    var workingOn = null;

    function getActionHtml(response) {
        let html = "";
        $.each(response, function(key, value) {
            html += "<tr>";
            html += "<td class='Website-task'>" + value.repository.name + "</td>";
            html += "<td class='Website-task'>" + value.head_branch + "</td>";
            html += "<td class='Website-task'>" + value.name + "</td>";
            html += "<td class='Website-task'>" + value.actor.login + "</td>";
            html += "<td class='Website-task'>" + moment(value.created_at).format('YYYY-MM-DD HH:mm:ss') + "</td>";
            html += "<td class='Website-task'>" + value.event +"</td>";
            html += "<td class='Website-task'>" + value.run_number +"</td>";
            html += "<td class='Website-task'>" + value.run_attempt +"</td>";
            html += "<td class='Website-task'>" + value.run_started_at +"</td>";
            html += "<td class='Website-task'>" + value.status +"</td>";
            html += "<td class='Website-task'>";
                if(value.job_status){
                    jQuery.each(value.job_status, function(index, item) {
                        html += "<strong>Job:</strong>"+item['name']+"( "+item['status']+") ";
                    });
                }
            html + "</td>";
            html += "<td class='Website-task'>" + value.conclusion + "</td>";
            html += "<td class='Website-task'>" + value.failure_reason + "</td>";
            html += "</tr>";
        });
        return html;
    }

    let isApiCall = true;
    let pageNum = 1;
    function getMoreActions(params) {
        var AllMessages = [];
        if(isApiCall) {
            workingOn = $.ajax({
                type: "GET", 
                url: '/github/repos/{!! $selectedRepositoryId !!}/github-actions', 
                data: {
                    'page': pageNum + 1
                }, 
                beforeSend: function() {
                    isApiCall = false;
                    // let loaderImg = `{{url('images/pre-loader.gif')}}`;
                    // let loadingIcon = `<div id="loading-image" style="position: relative;left: 0px;top: 0px;width: 100%;height: 120px;z-index: 9999;background: url(${loaderImg}) 50% 50% no-repeat;"></div>`;
                    // $(document).find("#action-workflows").append(loadingIcon);
                    $(document).find('#action-workflows .loader-section').show();
                    pageNum = pageNum + 1;
                }
            }).done(function(response) {
                workingOn = null;
                if (response.workflow_runs.length > 0) {
    
                    var li = getActionHtml(response.workflow_runs);
                    $("#action-workflows table tbody").append(li);
                    $(document).find('#action-workflows .loader-section').hide();
                    isApiCall = true;
    
                    // $("#action-workflows").find("#loading-image").remove();
                    // var searchterm = $('.search_chat_pop').val();
                    // if(searchterm && searchterm != '') {
                    //     var value = searchterm.toLowerCase();
                    //     $(".filter-message").each(function () {
                    //         if ($(this).text().search(new RegExp(value, "i")) < 0) {
                    //             $(this).hide();
                    //         } else {
                    //             $(this).show()
                    //         }
                    //     });
                    // }
                } else {
                    $("#action-workflows").find("#loading-image").remove();
                    currentChatParams.data.hasMore = false;
                }
    
    
            }).fail(function(response) {
                workingOn = null;
            });
        }
    };
    $(document).ready(function() {
        $('#action-workflow-table').DataTable({
            "paging": false, 
            "ordering": true, 
            "info": false,
            "searching": true
        });
        $(document).find('#action-workflows .loader-section').hide();

        $('#organizationId').change(function (){
            getRepositories();
        });
        getRepositories();
        function getRepositories(){
            var repos = $.parseJSON($('#organizationId option:selected').attr('data-repos'));

            $('#repoId').empty();

            if(repos.length > 0){
                $.each(repos, function (k, v){
                    if(v.id == {!! $selectedRepositoryId !!})
                        $('#repoId').append('<option value="'+v.id+'" selected>'+v.name+'</option>');
                    else 
                        $('#repoId').append('<option value="'+v.id+'">'+v.name+'</option>');
                });
            }else{
            }
        }

        $("#repoId").on('change', function(e) {
            getBranches();
        });

        function getBranches() {
            var url = "{{ route('project.getGithubBranches') }}";
            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: "GET",
                url: url,
                data: {
                    build_repository: jQuery('#repoId').val(),
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
            }).done(function(response) {
                jQuery('#branchName').html(response.data);
                $("#loading-image-preview").hide();
            }).fail(function(response) {});
        }

        $('.select2').select2();
        $("#job-name-create-organization").on('change', function(e) {
            var url = "{{ route('project.getGithubRepo') }}";
            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: "GET",
                url: url,
                data: {
                    build_organization: jQuery('#job-name-create-organization').val(),
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
            }).done(function(response) {
                jQuery('#job-name-create-repository').html(response.data);
                $("#loading-image-preview").hide();
            }).fail(function(response) {});
        });

        $(document).on('submit', 'form#job-name-create-form', function(e){
            e.preventDefault();
            var self = $(this);
            let formData = new FormData(document.getElementById("job-name-create-form"));
            var button = $(this).find('[type="submit"]');
            $.ajax({
                url: '{{ route("github.job-name.store") }}',
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
                        $('#job-name-create-modal').modal('hide');
                        // reload page
                        location.reload();
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

        // Click event for the "view-jobs" icon
        $(document).on('click', '.view-jobs', function(e) {
            e.preventDefault();

            // Get the action ID from the data attribute
            var actionId = $(this).data('action-id');
            var selectedRepositoryId = {!! $selectedRepositoryId !!};

            // Make the AJAX request to fetch the jobs for the selected action
            $.ajax({
                url: "{{ route('github.get-jobs') }}",
                type: 'GET',
                dataType: 'html',
                data: { action_id: actionId, selectedRepositoryId: selectedRepositoryId }, // Send the action ID as a query parameter
                beforeSend: function() {
                    $("#loading-image-preview").show();
                },
                success: function(response) {
                    $("#loading-image-preview").hide();
                    // Update the modal content with the retrieved jobs
                    $('#jobsModalContent').html(response);

                    // Show the modal
                    $('#jobsModal').modal('show');
                },
                error: function(xhr, status, error) {
                    $("#loading-image-preview").hide();
                    // Handle the error, if any
                    console.error(error);
                }
            });
        });
    });

    // Laravel pagination is using, So below code not need
    // $(window).on('scroll', function() {
    //     if ($(window).scrollTop() + $(window).height() >= ($(document).height() - 5)) {
    //         getMoreActions(currentChatParams);
    //     }
    // })

    // $(window).scroll(function() {
    //     console.log(getMoreActions(currentChatParams));
    //     //  if($(window).scrollTop() == $(document).height() - $(window).height()) {
    //     // console.log(currentChatParams.data);
    //     // console.log(currentChatParams.data.hasMore);

    //     //     if(currentChatParams.data && currentChatParams.data.hasMore && workingOn == null) {
    //     //         workingOn = true;
    //     //         // currentChatParams.data.page++;
    //     //         getMoreActions(currentChatParams);
    //     //     }
    //     }

    // });

</script>
<style>
    #action-workflow-table_filter {
        text-align: right;
    }

    table {
        margin: 0 auto;
        width: 100%;
        clear: both;
        border-collapse: collapse;
        table-layout: fixed; // ***********add this
        word-wrap: break-word; // ***********and this
    }

    .scrollable-steps {
        height: 75px; /* Adjust the height as per your preference */
        overflow-y: auto;
    }

</style>

<div class="row">
    <div class="col-lg-12">
        <h2 class="page-heading">Github Actions ({{ $githubActionRuns['total_count'] }})</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-12">
                    <form action="{{ url('/github/repos/'.$repositoryId.'/actions') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="" class="form-label">Organization</label>
                                <select name="organizationId" id="organizationId" class="form-control select2">
                                    <option value="">-- Select organisation --</option>
                                    @foreach ($githubOrganizations as $githubOrganization)
                                        <option value="{{ $githubOrganization->id }}" data-repos='{{ $githubOrganization->repos }}' @if($githubOrganization->id == $selectedOrganizationID) selected @endif>{{  $githubOrganization->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                    
                            <div class="col-md-3">
                                <label for="" class="form-label">Repository</label>
                                <select name="repoId" id="repoId" class="form-control select2">
                                    
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="" class="form-label">Branch Name</label>
                                <select name="branchName" id="branchName" class="form-control select2">
                                    <option value="">-- Select branch --</option>
                                    @foreach ($selectedRepoBranches as $selectedRepoBranch)
                                        <option value="{{ $selectedRepoBranch->branch_name }}" @if($selectedRepoBranch->branch_name == $branchName) selected @endif>{{  $selectedRepoBranch->branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <?php 
                                    if(request('status')){   $status = request('status'); }
                                    else{ $status = ''; }
                                ?>
                                <label for="" class="form-label">Status</label>
                                <select name="status" id="status" class="form-control select2">
                                    <option value="" @if($status=='') selected @endif>-- Select a status --</option>
                                    <option value="completed" @if($status=="completed") selected @endif>completed</option>
                                    <option value="action_required" @if($status=="action_required") selected @endif>action_required</option>
                                    <option value="cancelled" @if($status=="cancelled") selected @endif>cancelled</option>
                                    <option value="failure" @if($status=="failure") selected @endif>failure</option>
                                    <option value="neutral" @if($status=="neutral") selected @endif>neutral</option>
                                    <option value="skipped" @if($status=="skipped") selected @endif>skipped</option>
                                    <option value="stale" @if($status=="stale") selected @endif>stale</option>
                                    <option value="success" @if($status=="success") selected @endif>success</option>
                                    <option value="timed_out" @if($status=="timed_out") selected @endif>timed_out</option>
                                    <option value="in_progress" @if($status=="in_progress") selected @endif>in_progress</option>
                                    <option value="queued" @if($status=="queued") selected @endif>queued</option>
                                    <option value="requested" @if($status=="requested") selected @endif>requested</option>
                                    <option value="waiting" @if($status=="waiting") selected @endif>waiting</option>
                                    <option value="pending" @if($status=="pending") selected @endif>pending</option>
                                </select>
                            </div>
                            <div class="col-md-2 pd-sm pl-0 mt-2">
                                 <button type="submit" class="btn btn-image search">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                                <a href="{{ url('/github/repos/'.$repositoryId.'/actions') }}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                                {{-- <a class="btn btn-primary" href="{{ route('github.sync-actions', [$repositoryId]) }}">Sync Actions</a> --}}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@include('partials.flash_messages', ['extraContent' => true])
<div class="row" id="action-workflows"  style="margin:10px;">
    <div class="col-12">
        <div class="table-responsive" style="overflow-x: auto!important">
            <table id="action-workflow-table" class="table table-bordered action-table" style="width: 135%;max-width:unset">
                <thead>
                    <tr>
                        <th style="width: auto">Repository</th>
                        <th style="width: auto">Branch</th>
                        <th style="width: auto">Name</th>
                        <th style="width: auto">Actor</th>
                        <th style="width: auto">Executed On</th>
                        <th style="width: auto">Event</th>
                        <th style="width: auto">Run Number</th>
                        <th style="width: auto">Run Attempt</th>
                        <th style="width: auto">Run Started At</th>
                        <th style="width: auto">Conclusion</th>
                        <th style="width: auto">Failure Reason</th>
                        <th style="width: auto">Status</th>
                        
                        <th style="width: auto">Action</th>
                       
                    </tr>
                </thead>
                <tbody>
                    @foreach($githubActionRuns['workflow_runs'] as $runs)
                    @switch(strtolower($runs->conclusion))
                        @case('failure')
                        @case('cancelled')
                        @case('timed-out')
                            @php $class = 'text-danger' @endphp    
                        @break
                        @case('success')
                        @case('completed')
                            @php $class = 'text-success' @endphp    
                        @break
                        @case('pending')
                        @case('action_required')
                        @case('startup_failure')
                            @php $class = 'text-warning' @endphp    
                        @break
                        
                        @default
                            @php $class = 'text-primary' @endphp
                    @endswitch
                        
                    <tr>
                        <td class="Website-task">{{$runs->repository->name}}</td>
                        <td class="Website-task">{{$runs->head_branch}}</td>
                        <td class="Website-task">{{$runs->name}}</td>
                        <td class="Website-task">{{$runs->actor->login}}</td>
                        <td class="Website-task">{{date('Y-m-d H:i:s', strtotime($runs->created_at))}}</td>
                        <td class="Website-task">{{$runs->event}}</td>
                        <td class="Website-task">{{$runs->run_number}}</td>
                        <td class="Website-task">{{$runs->run_attempt}}</td>
                        <td class="Website-task">{{$runs->run_started_at}}</td>
                        <td class="Website-task {{$class}}">{{$runs->conclusion}}</td>
                        <td class="Website-task">{{$runs->failure_reason}}</td>
                        <td class="Website-task">{{$runs->status}}</td>
                        
                        <td class="Website-task">
                            <a href="#" class="view-jobs" data-action-id="{{ $runs->id }}" title="View Jobs">
                                <i class="fa fa-eye" aria-hidden="true" style="color:grey;"></i>
                            </a>
                        </td>
                       
                    </tr>
                    @endforeach
                </tbody>
            </table>
             <!-- Display pagination links -->
            {{ $githubActionRuns->links() }}
        </div>
        <div class="loader-section">
            <div style="position: relative;left: 0px;top: 0px;width: 100%;height: 120px;z-index: 9999;background: url({{ url('images/pre-loader.gif')}}) 50% 50% no-repeat;"></div>
        </div>
    </div>
</div>

<div id="job-name-create-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Job Name</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <form id="job-name-create-form">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <strong>Organizations:</strong>
                                        <select name="organization" id="job-name-create-organization" class="form-control select2" style="width: 100%!important">
                                            <option value="" selected disabled>-- Select a Organizations --</option>
                                            @forelse($githubOrganizations as $organization)
                                                <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <strong>Repository:</strong>
                                        <select name="repository" id="job-name-create-repository" class="form-control select2" style="width: 100%!important">
                                            <option value="" selected disabled>-- Select a Repository --</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <strong>Job Name :</strong>
                                        {{ html()->text('job_name')->placeholder('Job Name')->id('job_name')->class('form-control')->required() }}
                                    </div>
                                </div>                            
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button data-id=""class="btn btn-secondary create-job-name">Create</button>
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

<!-- Modal markup -->
<div class="modal" id="jobsModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Jobs for Action</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="jobsModalContent">
                <!-- AJAX content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection
