@extends('layouts.app')

@section('content')
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<script>
    $(document).ready(function() {
        $('#repository-table').DataTable({
            "paging": true,
            "ordering": true,
            "info": false
        });
    });
</script>
<style>
    #repository-table_filter {
        text-align: right;
    }
</style>
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Github Repositories (<span id="repository_row_html_id">{{ count($repositories) }}</span>)</h2>
    </div>
</div>

<div class="container container-grow">
    @if(strlen($organizationId) == 0)
        <form action="" method="GET" id="filterRepositoryForm">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Organization</label>
                    <select class="form-control" id="organization" name="organization">
                        @foreach($githubOrganizations as $githubOrganization)
                            <option value="{{ $githubOrganization->id }}" {{ ($githubOrganization->name == 'MMMagento' ? 'selected' : '') }}>{{ $githubOrganization->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-default" style="margin-top: 25px;"><i class="fa fa-filter"></i> </button>
                </div>
            </div>
        </form>
        <div class="clearfix"></div><br />
    @endif
    <div class="ml-2">
    <form method="get" action="{{url('github/repos/'.$organizationId)}}">
        <div class="form-group">
            <div class="row">
                <div class="col-md-2">
                    <input name="query_string" type="text" class="form-control" value="{{$request->name??''}}"  placeholder="Enter Name" id="name">
                </div>

                <div class="col-md-1 d-flex justify-content-between">
                    <button type="submit" class="btn btn-image" ><img src="/images/filter.png"></button>
                    <button type="button" onclick="resetForm('{{ Request::url() }}')" class="btn btn-image" id=""><img src="/images/resend2.png"></button>
                    <button class="btn btn-default sync-data-button" data-organization_id="{{$organizationId}}" title="Sync Repositories">
                        <span class="fa fa-repeat"></span>
                    </button>
                </div>
            </div>
        </div>
    </form>
    <div class="clearfix"></div><br />
    </div>
    <table id="repository-table" class="table table-bordered">
        <thead>
            <tr>
                <th style="width: auto">Serial Number</th>
                <th style="width: auto">Organization</th>
                <th style="width: auto">Name</th>
                <th style="width: auto">Expire Date</th>
                <th style="width: auto">Last Update </th>
                <th style="width: 25%">Actions</th>
            </tr>
        </thead>
        <tbody>
            @include('github.include.repository-list')
        </tbody>
    </table>

    <div class="modal fade" id="label-modal" tabindex="-1" role="dialog" aria-labelledby="label-modal-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="label-modal-title">Labels for Repository</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Label Name</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody id="label-modal-body">
                            <!-- Labels and messages will be dynamically added here using JavaScript -->
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </div>

    <div class="modal fade" id="generate-token-modal" tabindex="-1" role="dialog" aria-labelledby="generate-token-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Add/Update Token</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">                
                    <div class="row">
                        <div class="col-lg-12">
                            <form method="post" id="addTokenForm">
                                <input type="hidden" name="github_repositories_id" id="github_repositories_id">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <div class="form-group">
                                                <label>Github Type</label>
                                                <select class="form-control" id="github_type" name="github_type">
                                                    <option value="ssh">SSH</option>
                                                    <option value="git">GIT</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <div class="form-group">
                                                <label>Token or rsa Key</label>
                                                <input class="form-control" type="text" name="token_key" id="token_key">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <div class="form-group">
                                                <label>Expiry Date</label>
                                                <input class="form-control" type="date" name="expiry_date" id="expiry_date">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-secondary submit_create_tag float-right float-lg-right">Add</button>
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
</div>

@include('github-token-history')
@endsection

@push('scripts')
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<script>
    $("#filterRepositoryForm").submit(function(e){
        e.preventDefault();

        var organizationId = $("#organization").val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url : '{{ url('github/repos') }}/'+organizationId,
            type : 'GET',
            success : function(result){
                $('#repository-table').DataTable().clear().destroy();

                $('#repository_row_html_id').html(result.count);;
                $('#repository-table tbody').empty().html(result.tbody);

                $('#repository-table').DataTable();
            }
        });
    });

    $(document).on('submit', '#addTokenForm', function (e) {
        e.preventDefault();

        var github_repositories_id = $("#github_repositories_id").val();
        var github_type = $("#github_type").val();
        var token_key = $("#token_key").val();
        var expiry_date = $("#expiry_date").val();
        
        $.ajax({
            url: "{{route('github.addtoken')}}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                github_repositories_id: github_repositories_id,
                github_type: github_type,
                token_key: token_key,
                expiry_date: expiry_date,
            },
            success: function (response) {
                if (response.code == 200) {
                    toastr['success'](response.message);
                    $('#github-task-create').modal('hide');
                } else {
                    toastr['error'](response.message);
                }

                window.location.reload();
            },
            error: function () {
                alert('There was error loading priority task list data');
            }
        });
    });

    $(document).on('click','.token-list-button',function(){
        github_repositories_id = $(this).data('id');
        $.ajax({
            method: "GET",
            url: `{{ route('github.token.histories', [""]) }}/` + github_repositories_id,
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += "<tr>";
                        html += "<td>" + (k + 1) + "</td>";
                        /*html += "<td>" + v.githubrepository.id + "</td>";
                        html += "<td>" + v.githubrepository.name + "</td>";*/
                        html += "<td>" + v.github_type + "</td>";
                        html += "<td>" + v.token_key + "</td>";
                        html += "<td>" + v.user.name + "</td>";
                        html += "<td>" + v.details + "</td>";
                        html += "<td>" + v.created_at + "</td>";
                        html += "</tr>";
                    });
                    $("#github-token-histories-list").find(".github-token-list-view").html(html);
                    $("#github-token-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    $(document).ready(function() {
        // sync-labels-button
        $(document).on('click', '.sync-labels-button', function () {
            var repo_id = $(this).data('repo_id');
            $("#loading-image-preview").show();

            $.ajax({
                url: '{{ route("github.sync-repo-labels") }}',
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: 'json',
                data: {
                    repo_id: repo_id, // Include the repository in the data
                },
                success: function (data) {
                    $("#loading-image-preview").hide();
                    toastr['success']('Labels synced successfully!');
                },
                error: function (error) {
                    $("#loading-image-preview").hide();
                    console.error(error);
                }
            });
        });

        $(document).on('click', '.sync-data-button', function() {
            var organization_id = $(this).data('organization_id');
            if (organization_id) {
                $("#loading-image-preview").show();
                $.ajax({
                    url: '/github/repos/'+organization_id+'/sync',
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType: 'json',
                    data: {},
                    success: function (data) {
                        $("#loading-image-preview").hide();
                        toastr['success']('Repositories synced successfully!');
                        window.location.reload();
                    },
                    error: function (error) {
                        $("#loading-image-preview").hide();
                        console.error(error);
                    }
                });
            }
        });

        // Function to fetch labels and messages and populate the modal
        function fetchLabelsAndMessages(repo_id) {
            $("#loading-image-preview").show();
            
            $.ajax({
                url: '{{ route("github.list-repo-labels") }}',
                type: 'GET',
                data: {
                    repo_id: repo_id
                },
                dataType: 'json',
                success: function (data) {
                    $("#loading-image-preview").hide();

                    var modalBody = $('#label-modal-body');
                    modalBody.empty();

                    $.each(data, function (index, label) {
                        var messageInput = '<input type="text" name="message" class="form-control message-input" data-label-id="' + label.id + '" value="' + (label.message ? label.message : '') + '">';
                        var labelRow = '<tr>' +
                            '<td>' + label.label_name + '</td>' +
                            '<td>' + messageInput +
                            '</td>' +
                            '</tr>';

                        modalBody.append(labelRow);
                    });

                    // Show the modal
                    $('#label-modal').modal('show');
                },
                error: function (error) {
                    $("#loading-image-preview").hide();
                    console.error(error);
                }
            });
        }

        // AJAX request to list labels and messages in the modal
        $(document).on('click', '.show-labels-button', function () {
            var repo_id = $(this).data('repo_id');
            fetchLabelsAndMessages(repo_id);
        });

        $(document).on('click', '.generate-token-labels-button', function () {
            var repo_id = $(this).data('repo_id');
            $("#github_repositories_id").val(repo_id);

            $('#addTokenForm')[0].reset();

            $.ajax({
                url: '{{ route("github.repo-data") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    repo_id: repo_id
                },
                dataType: 'json',
                success: function (data) {
                    if(data.data.expiry_date!=''){
                        $("#expiry_date").val(data.data.expiry_date);
                    }
                    if(data.data.token_key!=''){
                        $("#token_key").val(data.data.token_key);
                    }
                    if(data.data.github_type!=''){
                        $("#github_type").val(data.data.github_type).trigger('change');
                    }
                },
                error: function (error) {
                    console.error(error);
                    toastr['error']('Label message update failed!');
                }
            });
            $('#generate-token-modal').modal('show');
        });

        // Function to fetch labels and messages and populate the modal
        function generateTokenGithub(repo_id) {
            $("#loading-image-preview").show();
            
            
        }

        // AJAX request to update label message
        $(document).on('change', '.message-input', function () {
            var labelId = $(this).data('label-id');
            var message = $(this).val();

            $.ajax({
                url: '{{ route("github.update-repo-label-message") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    label_id: labelId,
                    message: message
                },
                dataType: 'json',
                success: function (data) {
                    // Show success message or handle the response as needed
                    toastr['success']('Label message updated successfully!');
                },
                error: function (error) {
                    console.error(error);
                    toastr['error']('Label message update failed!');
                }
            });
        });
    });

    function resetForm(url)
    {
        window.location.href = url;
    }
</script>
@endpush