@extends('layouts.app')

@section('content')
<style>
    #action-workflow-table_filter {
        text-align: right;
    }

    table {
        margin: 0 auto;
        width: 100%;
        clear: both;
        border-collapse: collapse;
        table-layout: fixed;
        word-wrap: break-word;
    }
    .d-n{
        display: none;
    }

    .dataTables_wrapper.dt-bootstrap4 .row div.col-sm-12.col-md-6:empty{ display: none }

</style>

<div class="row">
    <div class="col-lg-12 margin-tb page-heading">
        <h5 class="ml-5">Branches (<span id="branches_row_html_id"></span>)</h5>
        <h3 class="text-center">Github Branches</h3>
    </div>
</div>
@include('partials.flash_messages', ['extraContent' => true])
<div class="container container-grow" style="max-width: 100%;width: 100%;" id="branch-section">
    <div class="row mb-3">

        <div class="col-md-2">
            <!-- Single button -->
            <label for="" class="form-label">Action on selected Item</label>
            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle jq_selected_item" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    0 Items Selected <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="#"  onclick="confirmDelete()">Delete All</a></li>
                </ul>
            </div>
        </div>

        <div class="col-md-3">
            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#deleteBranchesModal">
                Enter Number To Delete Branches</span>
            </button>
        </div>

        <div class="col-md-3">
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

        <div class="col-md-2">
            <label for="" class="form-label">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="">All</option>
                <option value="open">Open</option>
                <option value="closed">Closed</option>
            </select>
        </div>

    </div>
    <table class="table table-bordered action-table" style="table-layout: fixed;" id="branches-table">
        <thead>
            <tr>

                <th style="width:10% !important;"><input type="checkbox" name="select_all" value="1" id="action-select-all"></th>
                <th style="width:25% !important;">Name</th>
                <th style="width:10% !important;">Status</th>
                <th style="width:10% !important;">Behind By</th>
                <th style="width:10% !important;">Ahead By</th>
                <th style="width:15% !important;">Last Commit By</th>
                <th style="width:15% !important;">Last Commit At</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    <div class="loader-section d-n">
        <div style="position: relative;left: 0px;top: 0px;width: 100%;height: 120px;z-index: 9999;background: url({{ url('images/pre-loader.gif')}}) 50% 50% no-repeat;"></div>
    </div>

    <div id="deleteBranchesModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Delete Branches</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form>
              @csrf
              <div class="modal-body">
                <div class="form-group">
                  <input type="number" name="number_of_branches" value="{{ old('number_of_branches') }}" id="number_of_branches" class="form-control" placeholder="Enter Number Of Branches To Delete" min="0" required>
                </div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-secondary" onclick="deleteBranches()">Delete</button>
                </div>
            </form>
            </div>
          </div>
        </div>
      </div>
</div>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<script>
    $("#branches-table").DataTable({
        "bPaginate": false,
        "search":false,
    });

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

            getBranches();
        }else{
            getBranches();
        }
    }

    $('#repoId').change(function (){
        getBranches();
    });

    function getBranches(){
        var repoId = $('#repoId').val();

        $('.loader-section').removeClass('d-n');

        $.ajax({
            type: "GET",
            url: "",
            async:true,
            data: {
                repoId: repoId,
                status: $("#status").val(),
            },
            dataType: "json",
            success: function (response) {
                var branchHtml = getBranchHtml(response.data);

                $('#branches-table').DataTable().clear().destroy();

                $('#branches_row_html_id').html(response.data.length);
                $('#branches-table tbody').empty().html(branchHtml);

                $("#branches-table").DataTable({
                    "bPaginate": false,
                    "search":false,
                });

                $('.loader-section').addClass('d-n');
            }
        });
    }

    function getBranchHtml(response) {
        let html = "";
        $.each(response, function(key, value) {
            html += "<tr>";
            html += `<td><input type="checkbox" class="action" name="action[]" data-repository-id="`+value.repository_id+`" value="` + value.branch_name+ `"></td>`;
            html += "<td>" + value.branch_name + "</td>";
            html += "<td>" + value.status + "</td>";
            html += "<td>" + value.behind_by + "</td>";
            html += "<td>" + value.ahead_by + "</td>";
            html += "<td>" + value.last_commit_author_username + "</td>";
            html += "<td>" + value.last_commit_time + "</td>";
            // html += `<td style="width:10%;">
            //     <div style="margin-top: 5px;">
            //         <button class="btn btn-sm btn-secondary" style="margin-top: 5px;" onclick="confirmDelete('`+value.repository_id+`','`+value.branch_name+`')">
            //             Delete Branch
            //         </button>
            //     </div>
            // </td>`;
            html += "</tr>";
        });
        return html;
    }

    $(document).ready(function() {
        getRepositories();
    });

    function confirmDelete() {
        $length = $('input:checkbox[name="action[]"]:checked').length;
        if($length == 0){
            toastr['error']("Please select item to delete the branch");
        }else{
            let result = confirm("Are you sure you want to delete these branches?");
            if (result) {
                $('input:checkbox[name="action[]"]:checked').each(function(){
                    let repositoryId = $(this).data('repository-id');
                    let branchName = $(this).val();
                    if(repositoryId && branchName ){
                        $.ajax({
                            headers : {
                                'Accept' : 'application/json',
                                'Content-Type' : 'application/json',
                                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                            },
                            type: "post",
                            url: '/github/repos/'+repositoryId+'/branch?branch_name='+branchName,
                            dataType: "json",
                            success: function (response) {
                                if(response.status) {
                                    toastr['success']('Branch has been deleted successfully!');

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

                });
                window.location.reload();
            }
        }
    }

    $(document).on('click','#action-select-all', function(){
      if ($("#action-select-all").is(':checked')) {
          $('input[name="action[]"]').prop('checked', true);
        }else{
          $('input[name="action[]"]').prop('checked', false);
      }
      $length = $('input[name="action[]"]:checked').length;
      $(".jq_selected_item").html($length+" Items Selected");
   });

    $(document).on('click','.action', function(){
      $length = $('input[name="action[]"]:checked').length;
      $(".jq_selected_item").html($length+" Items Selected");
    });

    function resetActionButoonAndCheckbox(){
        $(".jq_selected_item").html("0 Items Selected");
        $("#action-select-all").prop('checked', false);
    }

    $(document).on('change', "select[name=repoId]", function() {
        resetActionButoonAndCheckbox();
        getBranches();
    })

    $(document).on('change', "#status", function() {
        resetActionButoonAndCheckbox();
        getBranches();
    })

    function deleteBranches(){
        var repoId = $('#repoId').val();
        var numberOfBranches = $('#number_of_branches').val();

        if(repoId.length == ''){
            toastr['error']('Please select repository.');
            return;
        }

        if(numberOfBranches.length == '' || numberOfBranches < 1){
            toastr['error']('Please enter number of branches to delete.');
            return;
        }

        $.ajax({
            headers : {
                'Accept' : 'application/json',
                'Content-Type' : 'application/json',
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            url: '/github/repos/'+repoId+'?number_of_branches='+numberOfBranches,
            dataType: "json",
            success: function (response) {
                toastr['success']('Branches has been deleted successfully!');
            },
            error: function () {
                toastr['error']('Could not change module!');
            }
        });

        window.location.reload();
    }
</script>
@endsection
