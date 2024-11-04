@extends('layouts.app')

@section('content')
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<style>
    #migration-error-logs-table_filter {
        text-align: right;
    }

    .d-n{
        display: none;
    }
</style>
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Github Deploy Branch Migration Error (<span id="migration_error_log_html_id"></span>)</h2>
    </div>
</div>

<div class="container container-grow" style="max-width: 100%;width: 100%;">
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
    </div>

    <table id="migration-error-logs-table" class="table table-bordered">
        <thead>
            <tr>
                <th width="5%">Id</th>
                <th width="15%">Branch Name</th>
                <th width="70%">Error</th>
            </tr>
        </thead>
        <tbody>
           
        </tbody>
    </table>
    <div class="loader-section d-n">
        <div style="position: relative;left: 0px;top: 0px;width: 100%;height: 120px;z-index: 9999;background: url({{ url('images/pre-loader.gif')}}) 50% 50% no-repeat;"></div>
    </div>
</div>
@endsection
@section('scripts')

<script>
    $('#migration-error-logs-table').DataTable({
        "ordering": true,
        "info": false
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

            getMigrationErrorLogs();
        }else{
            getMigrationErrorLogs();
        }
    }

    $('#repoId').change(function (){
        getMigrationErrorLogs();
    });

    function getMigrationErrorLogs(){
        var repoId = $('#repoId').val();

        $('.loader-section').removeClass('d-n');

        $.ajax({
            type: "GET",
            url: "",
            async:true,
            data: {
                repoId: repoId,
            },
            dataType: "json",
            success: function (result) {
                $('#migration-error-logs-table').DataTable().clear().destroy();

                $('#migration_error_log_html_id').html(result.count);;
                $('#migration-error-logs-table tbody').empty().html(result.tbody);

                $('#migration-error-logs-table').DataTable({
                    "ordering": true,
                    "info": false
                });

                $('.loader-section').addClass('d-n');
            }
        });
    }

    $(document).ready(function() {
        getRepositories();
    })

   //Expand Row
    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });
</script>
@endsection