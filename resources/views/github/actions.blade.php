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
        table-layout: fixed; // 
        word-wrap: break-word; // 
    }

</style>

<div class="row">
    <div class="col-lg-12 margin-tb page-heading">
        <h5 class="ml-3">Actions <span id="actionCount"></span> </h2>
        <h3 class="text-center">Github Actions</h3>
    </div>
</div>
@include('partials.flash_messages', ['extraContent' => true])

<div class="container container-grow" style="max-width: 100%;width: 100%;" id="action-workflows">
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="" class="form-label">Organization</label>
            <select name="organizationId" id="organizationId" class="form-control">
                @foreach ($githubOrganizations as $githubOrganization)
                    <option value="{{ $githubOrganization->id }}" data-repos='{{ $githubOrganization->repos }}' {{ ($githubOrganization->name == 'MMMagento' ? 'selected' : '' ) }}>{{  $githubOrganization->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="" class="form-label">Repository</label>
            <select name="repoId" id="repoId" class="form-control">
                
            </select>
        </div>

        <div class="col-md-2">
            <label for="" class="form-label">From date</label>
            <input type="date" name="fromDate" class="form-control">
        </div>
        <div class="col-md-2">
            <label for="" class="form-label">To date</label>
            <input type="date" name="toDate" class="form-control">
        </div>
    </div>
    <table class="table table-bordered action-table" style="table-layout: fixed;" id="">
        <thead>
            <tr>
                <th style="width:40% !important;">Title</th>
                <th style="width:25% !important;">Executed On</th>
                <th style="width:10% !important;">Status</th>
                <th style="width:25% !important;">Failure Reason</th>
                <!-- <th style="width:25% !important;">Action</th> -->
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot style="display: none">
            <td colspan="4">
                <h5 class="text-center text-bold">No Data Found</h5>
            </td>
        </tfoot>
    </table>
    <div class="loader-section">
        <div style="position: relative;left: 0px;top: 0px;width: 100%;height: 120px;z-index: 9999;background: url({{ url('images/pre-loader.gif')}}) 50% 50% no-repeat;"></div>
    </div>
</div>

<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>

<script>
    var currentChatParams = {};
    currentChatParams.data = {
        page: 1
        , hasMore: true
    , };
    var workingOn = null;
    
    $('#organizationId').change(function (){
        getRepositories();
    });

    async function getRepositories(){
        var repos = $.parseJSON($('#organizationId option:selected').attr('data-repos'));

        $('#repoId').empty();

        if(repos.length > 0){
            console.log(repos);

            $.each(repos, function (k, v){
                $('#repoId').append('<option value="'+v.id+'">'+v.name+'</option>');
            });

            await fetchActions({
                repoId: $(document).find("select[name=repoId]").val()
                , page: 1
            });
        }else{
            await fetchActions({
                repoId: $(document).find("select[name=repoId]").val()
                , page: 1
            });
        }
    }

    function getActionHtml(response) {
        let html = "";
        $.each(response, function(key, value) {
            html += "<tr>";
            html += `<td>
                <blockquote class="blockquote">
                <p class="mb-0">`+value.display_title+`</p>
                <footer class="blockquote-footer">Commit Pushed By : `+value.head_commit.author.name+`</footer>
                <footer class="blockquote-footer">WorkFlow: `+value.name+`</footer>
                </blockquote></td>`;
            html += "<td>" + moment(value.created_at).format('YYYY-MM-DD HH:mm:ss') + "</td>";
            html += "<td>" + value.conclusion + "</td>";
            html += "<td>" + value.failure_reason + "</td>";
            // html += `<td>  <button class="btn btn-sm btn-secondary" style="margin-top: 5px;" onclick="rerunAction('`+value.repository.id+`','`+value.id+`')">Rerun Action</button></td>`;
            html += "</tr>";
        });
        return html;
    }

    async function getActions({
        repoId
        , page
        , date = null
    }) {
        return $.ajax({
            type: "GET"
            , url: ""
            , async: true
            , data: {
                repoId: repoId
                , page: page
                , date: date
            }
            , dataType: "json"
            , success: function(response) {
                return response
            }
        });
    }

    function rerunAction(repositoryId, jobId) {
        let result = confirm("Are you sure you want to rerun this action?");
        if (result) {
            $.ajax({
                headers : {
                    'Accept' : 'application/json',
                    'Content-Type' : 'application/json',
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: '/github/repos/'+repositoryId+'/actions/jobs/'+jobId+'/rerun',
                dataType: "json",
                success: function (response) {
                    if(response.status) {
                        toastr['success']('Action has been rerun successfully!');
                        window.location.reload();
                    }else{
                        errorMessage = response.error ? response.error : 'Something went wrong.Please try again later!';
                        toastr['error'](errorMessage);
                    }
                },
                error: function () {
                    toastr['error']('Something went wrong.Please try again later!');
                }
            });
        }
    }

    let isApiCall = true;
    let pageNum = 1;
    let totalCount = 0;

    async function fetchActions(data) {
        $(document).find("#action-workflows .loader-section").show();
        $(document).find("#action-workflows table tfoot").hide();
        let actions = await getActions(data);
        totalCount = actions.data.total_count;
        $(document).find('#actionCount').html(`(${totalCount})`)
        if (actions.data.workflow_runs.length < 1 && data.page == 1) {
            $(document).find("#action-workflows table tfoot").show();
            $(document).find("#action-workflows .loader-section").hide();
        } else if (actions.data.workflow_runs.length < 1 && data.page > 1) {
            $(document).find("#action-workflows .loader-section").hide();
        }
        let htmlContent = getActionHtml(actions.data.workflow_runs);
        $(document).find("#action-workflows .loader-section").hide();
        $(document).find("#action-workflows table tbody").append(htmlContent);
    }

    $(document).on('change', "select[name=repoId], [name=fromDate], [name=toDate]", async function() {
        $(document).find("#action-workflows table tbody tr").remove();
        let fromDate = $(document).find("[name=fromDate]").val();
        let endDate = $(document).find("[name=toDate]").val();
        if (!endDate && fromDate) {
            alert("Plase select to date.")
            return false;
        }
        pageNum = 1;
        isApiCall = true;
        await fetchActions({
            repoId: $(document).find("select[name=repoId]").val()
            , page: 1
            , date: (fromDate && endDate) ? `${fromDate}..${endDate}` : null
        });
    });

    $(window).on('scroll', async function() {
        if ($(window).scrollTop() + $(window).height() >= ($(document).height() - 5)) {
            if (isApiCall) {
                isApiCall = false;
                let fromDate = $(document).find("[name=fromDate]").val();
                let endDate = $(document).find("[name=toDate]").val();

                await fetchActions({
                    repoId: $(document).find("select[name=repoId]").val()
                    , page: pageNum + 1
                    , date: (fromDate && endDate) ? `${fromDate}..${endDate}` : null
                });
                if (totalCount > 0) {
                    isApiCall = true;
                }
                pageNum = pageNum + 1;
            }
        }
    });

    $(document).ready(function() {
        getRepositories();

        // $(async function() {
        //     await fetchActions({
        //         repoId: $(document).find("select[name=repoId]").val()
        //         , page: 1
        //     });
        // });
    })
</script>


@endsection
