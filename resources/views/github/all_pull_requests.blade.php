@extends('layouts.app')

@section('content')
<style>
    #pull-request-table_filter {
        text-align: right;
    }
	table{
        margin: 0 auto;
        width: 100%;
        clear: both;
        border-collapse: collapse;
        table-layout: fixed; // ***********add this
        word-wrap:break-word; // ***********and this
    }
    .d-n{
        display: none !important;
    }

    .scrollable-steps {
        height: 75px; /* Adjust the height as per your preference */
        overflow-y: auto;
    }

    .spinner-parent-container {
        align-items: center;
        justify-content: left;
        display: flex;
    }

    .spinner-border {
        display: inline-block;
        width: 3rem;
        height: 3rem;
        vertical-align: text-bottom;
        border: 0.25em solid currentColor;
        border-right: 0.25em solid transparent;
        border-radius: 50%;
        animation: spinner-border 0.75s linear infinite;
    }

    @keyframes spinner-border {
        to {
            transform: rotate(360deg);
        }
    }
</style>

@include('github.repo_details')
@include('github.include.github-task-create')

<div class="row">
    <div class="col-lg-12 margin-tb page-heading">
        <h2 class="page-heading">Pull Requests (<span id="pull_request_html_id"></span>)</h2>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 margin-tb page-heading">
    @include('partials.flash_messages', ['extraContent' => true])
    </div>
</div>

<div class="container container-grow" style="max-width: 100%;width: 100%;">
    <div class="row mb-3">
        <div class="col-md-2">
            <label for="" class="form-label">Organization</label>
            <select name="organizationId" id="organizationId" class="form-control">
                @foreach ($githubOrganizations as $githubOrganization)
                    <option value="{{ $githubOrganization->id }}" data-repos='{{ $githubOrganization->repos }}' {{ ($githubOrganization->name == 'MMMagento' ? 'selected' : '' ) }}>{{  $githubOrganization->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label for="" class="form-label">Repository</label>
            <select name="repoId" id="repoId" class="form-control">
                
            </select>
        </div>

        <div class="col-md-1 spinner-parent-container">
            <div class="loader-section d-n spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        <div class="col-md-7">
            <div class="text-right">
                <button type="button" class="btn btn-sm btn-secondary list-created-tasks" title="List Task">List Task</button>
                <button class="btn btn-sm btn-secondary" onclick="createPRTask()"> Create PR Task </button>
                <button class="btn btn-sm btn-secondary" onclick="updateLabelActivities()"> Update Label Activities </button>
                <a class="btn btn-sm btn-secondary" id="repo_select">Select Repository</a>
                <a class="btn btn-sm btn-secondary" href="/github/repos/231925646/deploy?branch=master&pull_only=1">Deploy ERP Master</a>
                <a class="btn btn-sm btn-secondary" href="/github/repos/231925646/deploy?branch=master&composer=true&pull_only=1">Deploy ERP Master + Composer</a>
            </div>
        </div>
    </div>
    

    <table id="pull-request-table" class="table table-bordered" style="table-layout: fixed;">
        <thead>
            <tr>
                <th style="width:4% !important;"><input type="checkbox" name="select_all" class="select_all"></th>
                <th style="width:7% !important;">Repository</th>
                <th style="width:7% !important;">Number</th>
                <th style="width:13% !important;">Title</th>
                <th style="width:10% !important;">Branch</th>
                <th style="width:10% !important;">User</th>
                <th style="width:10% !important;">Updated At</th>
                <th style="width:13% !important;">Latest Activity</th>
                <th style="width:10% !important;">Deploy</th>
                <th style="width:10% !important;">Actions</th>
            </tr>
        </thead>
        <tbody>
          
        </tbody>
    </table>
    
</div>
<!-- Modal markup -->
<div class="modal" id="pr-review-comments-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="pr-review-comments-modal-content">
            <!-- AJAX content will be loaded here -->
        </div>
    </div>
</div>
<!-- Modal markup -->
<div class="modal" id="pr-activities-modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" id="pr-activities-modal-content">
            <!-- AJAX content will be loaded here -->
        </div>
    </div>
</div>
<!-- Modal markup -->
<div class="modal" id="pr-error-logs-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="pr-error-logs-modal-content">
            <!-- AJAX content will be loaded here -->
        </div>
    </div>
</div>
<!-- Modal markup -->
<div class="modal" id="list-created-tasks-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="list-created-tasks-modal-content">
            <!-- AJAX content will be loaded here -->
        </div>
    </div>
</div>
<div class="modal" id="actionsJobsModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Jobs for Action</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="actionsJobsModalContent">
                <!-- AJAX content will be loaded here -->
            </div>
        </div>
    </div>
</div>
<div id="build-process-modal" class="modal fade" role="dialog">
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
                        <form id="build-process">
                            <?php echo csrf_field(); ?>
                            
                            <div class="row">
                                
                                <div class="col-md-12">
                                    
                                    <div class="form-group">
                                        <strong>Please Select Projects:</strong>
                                        <select name="projects[]" id="build_projects" class="form-control" style="width: 100%!important" multiple>
                                            @forelse($projects as $project)
                                                <option value="{{ $project->id }}" selected>{{ $project->name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                    
                                </div>                            
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="hidden" class="build_pr" name="build_pr" value="">
                                        <input type="hidden" class="build_process_repository" name="build_process_repository" value="">
                                        <input type="hidden" class="build_process_branch" name="build_process_branch" value="">
                                        <button data-id=""class="btn btn-secondary update-build-process">Update</button>
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
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<script>
    $('#pull-request-table').DataTable({
        "paging": false,
        "ordering": true,
        "info": false
    });

    function confirmMergeToMaster(branchName, url) {
        let result = confirm("Are you sure you want to merge " + branchName + " to master?");
        if (result) {
            window.location.href = url;
        }
    }

    function confirmClosePR(repositoryId, pullRequestNumber) {
        let result = confirm("Are you sure you want to close this PR : "+ pullRequestNumber+ "?");
        if (result) {
            $.ajax({
                headers : {
                    'Accept' : 'application/json',
                    'Content-Type' : 'application/json',
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: '/github/repos/'+repositoryId+'/pull-request/'+pullRequestNumber+'/close',
                dataType: "json",
                success: function (response) {
                    if(response.status) {
                        toastr['success']('Pull request has been closed successfully!');
                        window.location.reload();
                    }else{
                        errorMessage = response.error ? response.error : 'Something went wrong please try again later!';
                        toastr['error'](errorMessage);
                    }
                },
                error: function () {
                    toastr['error']('Could not change module!');
                }
            });
        }
    }

    $('#organizationId').change(function (){
        getRepositories();
    });

    function getRepositories(){
        var repos = $.parseJSON($('#organizationId option:selected').attr('data-repos'));

        $('#repoId').empty();

        if(repos.length > 0){
            $.each(repos, function (k, v){
                $('#repoId').append('<option value="'+v.id+'">'+v.name+'</option>');
            });

            getPullRequests();
        }else{
            getPullRequests();
        }
    }

    $('#repoId').change(function (){
        getPullRequests();
    });
    var xhr = null;
    function getPullRequests(){
        var repoId = $('#repoId').val();

        $('.loader-section').removeClass('d-n');
        
        xhr = $.ajax({
            type: "GET",
            url: "",
            async:true,
            data: {
                repoId: repoId,
            },
            beforeSend: function () {
                console.log("xhr-"+xhr);
                if (xhr != undefined && xhr !== null) {
                    xhr.abort();
                }
            },
            dataType: "json",
            success: function (result) {
                $('#pull-request-table').DataTable().clear().destroy();

                $('#pull_request_html_id').html(result.count);;
                $('#pull-request-table tbody').empty().html(result.tbody);

                $('#pull-request-table').DataTable({
                    "paging": false,
                    "ordering": true,
                    "info": false
                });

                $('.loader-section').addClass('d-n');
            }
        });
    }

    $(document).ready(function() {
        getRepositories();
    });

    $(document).ready(function() {
        var currentPage = 1;
        var currentPageActivity = 1;
        var currentPageErrorLogs = 1;
        var currentPageForListTasks = 1;

        $(document).on('click', '.show-pr-review-comments', function(e) {
            e.preventDefault();
            var repo = $(this).data("repo");
            var pullNumber = $(this).data("pull-number");

            // Make the AJAX request
            loadComments(currentPage, repo, pullNumber);
        });

        // Load comments for the given page number
        function loadComments(page, repo, pullNumber) {
            $("#loading-image-preview").show();
            $.ajax({
                url: "{{ url('/github/pull-request-review-comments') }}/" + repo + "/" + pullNumber + "?page=" + page,
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    $("#loading-image-preview").hide();
                    // Update the modal content with the retrieved comments
                    $('#pr-review-comments-modal-content').html(response);
                    $('#repo').val(repo);
                    $('#pullNumber').val(pullNumber);

                    // Show the modal
                    $('#pr-review-comments-modal').modal('show');
                },
                error: function(xhr, status, error) {
                    $("#loading-image-preview").hide();
                    // Handle the error, if any
                    console.error(error);
                }
            });
        }

        // Pagination click event
        $(document).on('click', '#pr-review-comments-modal .pagination a', function(e) {
            e.preventDefault();

            // Get the page number from the clicked link
            var page = $(this).attr('href').split('page=')[1];
            var repo = $("#pr-review-comments-modal #repo").val();
            var pullNumber = $("#pr-review-comments-modal #pullNumber").val();
            // Update the current page and load comments for the new page
            currentPage = page;
            loadComments(currentPage, repo, pullNumber);
        });

        $(document).on('click', '.show-pr-activities', function(e) {
            e.preventDefault();
            var repo = $(this).data("repo");
            var pullNumber = $(this).data("pull-number");

            // Make the AJAX request
            loadActivities(currentPageActivity, repo, pullNumber);
        });

        // Load activities for the given page number
        function loadActivities(page, repo, pullNumber) {
            $("#loading-image-preview").show();
            $.ajax({
                url: "{{ url('/github/pull-request-activities') }}/" + repo + "/" + pullNumber + "?page=" + page,
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    $("#loading-image-preview").hide();
                    // Update the modal content with the retrieved comments
                    $('#pr-activities-modal-content').html(response);
                    $('#pr-activities-modal #repo').val(repo);
                    $('#pr-activities-modal #pullNumber').val(pullNumber);

                    // Show the modal
                    $('#pr-activities-modal').modal('show');
                },
                error: function(xhr, status, error) {
                    $("#loading-image-preview").hide();
                    // Handle the error, if any
                    console.error(error);
                }
            });
        }

        // Pagination click event
        $(document).on('click', '#pr-activities-modal .pagination a', function(e) {
            e.preventDefault();

            // Get the page number from the clicked link
            var page = $(this).attr('href').split('page=')[1];
            var repo = $("#pr-activities-modal #repo").val();
            var pullNumber = $("#pr-activities-modal #pullNumber").val();
            // Update the current page and load comments for the new page
            currentPage = page;
            loadActivities(currentPage, repo, pullNumber);
        });
        
        // 
        $(document).on('click', '.show-pr-error-logs', function(e) {
            e.preventDefault();
            var repo_id = $(this).attr('data-repo');
            var pull_number = $(this).attr('data-pull-number');

            // Make the AJAX request
            loadErrorLogs(currentPageErrorLogs, repo_id, pull_number);
        });

        // Load activities for the given page number
        function loadErrorLogs(page, repo, pullNumber) {
            $("#loading-image-preview").show();
            $.ajax({
                url: "{{ url('/github/pr-error-logs') }}/" + repo + "/" + pullNumber + "?page=" + page,
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    $("#loading-image-preview").hide();
                    // Update the modal content with the retrieved comments
                    $('#pr-error-logs-modal-content').html(response);
                    $('#pr-error-logs-modal #repo').val(repo);
                    $('#pr-error-logs-modal #pullNumber').val(pullNumber);

                    // Show the modal
                    $('#pr-error-logs-modal').modal('show');
                },
                error: function(xhr, status, error) {
                    $("#loading-image-preview").hide();
                    // Handle the error, if any
                    console.error(error);
                }
            });
        }

        // Pagination click event
        $(document).on('click', '#pr-error-logs-modal .pagination a', function(e) {
            e.preventDefault();

            // Get the page number from the clicked link
            var page = $(this).attr('href').split('page=')[1];
            var repo = $("#pr-error-logs-modal #repo").val();
            var pullNumber = $("#pr-error-logs-modal #pullNumber").val();
            // Update the current page and load comments for the new page
            currentPage = page;
            loadErrorLogs(currentPage, repo, pullNumber);
        });

        $(document).on('click', '.expand-row', function () {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });

        // List created tasks
        $(document).on('click', '.list-created-tasks', function(e) {
            e.preventDefault();

            // Make the AJAX request
            loadCreatedTasks(currentPageForListTasks);
        });

        // Load activities for the given page number
        function loadCreatedTasks(page) {
            $("#loading-image-preview").show();
            $.ajax({
                url: "{{ url('/github/list-created-tasks') }}?page=" + page,
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    $("#loading-image-preview").hide();
                    // Update the modal content with the retrieved comments
                    $('#list-created-tasks-modal-content').html(response);
                    // Show the modal
                    $('#list-created-tasks-modal').modal('show');
                },
                error: function(xhr, status, error) {
                    $("#loading-image-preview").hide();
                    // Handle the error, if any
                    console.error(error);
                }
            });
        }

        // Pagination click event
        $(document).on('click', '#list-created-tasks-modal .pagination a', function(e) {
            e.preventDefault();

            // Get the page number from the clicked link
            var page = $(this).attr('href').split('page=')[1];
            // Update the current page and load comments for the new page
            currentPage = page;
            loadCreatedTasks(currentPage);
        });
    });

    $(document).on('click', '#repo_select', function(event) {
       $('#create-repo-modal').modal('show');

    });
    
    $(document).ready(function() {
        $('.repostatus').on('change', function() {
            $('.repostatus').not(this).prop('checked', false);

            var selectedRepos = [];
            $('input[name="repostatus"]:checked').each(function() {
                selectedRepos.push($(this).val());
            });
            var repoId = $(this).data('repo_id');
            $.ajax({
                type: "GET",
                url: "{{route('github.repoStatusCheck')}}",
                data: {
                    _token: "{{ csrf_token() }}",
                    repoId: repoId
                },
                success: function(response) {
                    $('.ajax-loader').hide();
                    if (response.message) {
                        toastr['success'](response.message, 'Success');
                    } else {
                        toastr['error'](response.message, 'Error');
                    }
                },
                error: function(response) {
                    $('.ajax-loader').hide();
                    console.log(response);
                }
            });
        });
        $("#build_projects").select2({});
        $(document).on("click",".open-build-process-template",function(e) {
            e.preventDefault();
            var repository=$(this).attr("data-id");
            var branch=$(this).attr("data-branch");
            var build_pr=$(this).attr("data-build_pr");
            $(".build_process_repository").val(repository);
            $(".build_process_branch").val(branch);
            $(".build_pr").val(build_pr);
            $('#build-process-modal').modal('show'); 
        });
        $(document).on('submit', 'form#build-process', function(e){
            e.preventDefault();
            var self = $(this);
            let formData = new FormData(document.getElementById("build-process"));
            var button = $(this).find('[type="submit"]');
            $.ajax({
                url: '{{ route("project.pullRequests.buildProcess") }}',
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
                        $('#build-process-modal').modal('hide');
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

        // Click event for the "view-actions-jobs" icon
        $(document).on('click', '.view-actions-jobs', function(e) {
            e.preventDefault();

            // Get the action ID from the data attribute
            var branch = $(this).data('branch');
            var repo = $(this).data('repo');

            // Make the AJAX request to fetch the jobs for the selected action
            $.ajax({
                url: "{{ route('github.get-actions-jobs') }}",
                type: 'GET',
                dataType: 'html',
                data: { selectedBranchName: branch, selectedRepositoryId: repo }, // Send the action ID as a query parameter
                beforeSend: function() {
                    $("#loading-image-preview").show();
                },
                success: function(response) {
                    $("#loading-image-preview").hide();
                    // Update the modal content with the retrieved jobs
                    $('#actionsJobsModalContent').html(response);

                    // Show the modal
                    $('#actionsJobsModal').modal('show');
                },
                error: function(xhr, status, error) {
                    $("#loading-image-preview").hide();
                    // Handle the error, if any
                    console.error(error);
                }
            });
        });

        $('.select_all').on('change', function() {
            var isChecked = $(this).prop('checked');
            $('.bulk_select_pull_request').prop('checked', isChecked);
        });
    });

    function updateLabelActivities()
    {
        event.preventDefault();
        var selected_rows = [];
        var selected_repo_id = "";

        $(".bulk_select_pull_request").each(function () {
            if ($(this).prop("checked") == true) {
                selected_rows.push($(this).val());
                selected_repo_id = $(this).data("repo");
            }
        });

        if (selected_rows.length == 0) {
            alert('Please select any row');
            return false;
        }

        if(confirm('Are you sure you want to perform this action?')==false)
        {
            console.log(selected_rows, selected_repo_id);
            return false;
        }

        $.ajax({
            type: "post",
            url: "{{ route('github.pull-request-activities.update') }}",
            data: {
                _token: "{{ csrf_token() }}",
                prIds: selected_rows,
                repoId: selected_repo_id
            },
            beforeSend: function() {
                $(this).attr('disabled', true);
                $("#loading-image-preview").show();
            }
        }).done(function(data) {
            $("#loading-image-preview").hide();
            toastr["success"]("Updated successfully, Please wait for updating the content!", "Message");
            getPullRequests();
        }).fail(function(response) {
            $("#loading-image-preview").hide();
            toastr["error"](error.responseJSON.message);
        });
    }

    var selected_rows_for_pr_task = [];
    var selected_repo_id_for_pr_task_ = "";

    function createPRTask()
    {
        event.preventDefault();

        selected_rows_for_pr_task = [];
        selected_repo_id_for_pr_task_ = "";
        $(".bulk_select_pull_request").each(function () {
            if ($(this).prop("checked") == true) {
                selected_rows_for_pr_task.push($(this).val());
                selected_repo_id_for_pr_task_ = $(this).data("repo");
            }
        });

        if (selected_rows_for_pr_task.length == 0) {
            alert('Please select any row');
            return false;
        }
        $('#github-task-create #task_details').val("Please check PR numbers " + selected_rows_for_pr_task);
        $('#github-task-create').modal('show');
    }

    $(document).on('submit', '#github-task-create-form', function (e) {
        e.preventDefault();
        var data = $(this).serializeArray();
        data.push({name: 'selected_rows', value: selected_rows_for_pr_task});
        data.push({name: 'selected_repo_id', value: selected_repo_id_for_pr_task_});
        $.ajax({
            url: "{{route('github.github-task.store')}}",
            type: 'POST',
            data: data,
            success: function (response) {
                if (response.code == 200) {
                    toastr['success'](response.message);
                    $('#github-task-create').modal('hide');
                } else {
                    toastr['error'](response.message);
                }
            },
            error: function () {
                alert('There was error loading priority task list data');
            }
        });
    });
</script>
@endsection