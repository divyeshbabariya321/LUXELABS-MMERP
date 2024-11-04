@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Tables | Database')

@section('content')

<div class="row">
  <div class="col-lg-12 margin-tb">
      <h2 class="page-heading">
        Tables | Database 
        <button type="button" class="btn btn-secondary truncate-tables-btn" style=" float: right;">
            Truncate Table
        </button> 
      </h2>
  </div>
</div>
<div class="row">
    <div class="col-md-12 mb-1">
        <form method="get" action="{{ route('database.tables-list') }}">
        <div class="row">
            <div class="col-md-3">
                <select class="form-control" name="table_name">
                    <option value="">Select Table</option>
                    @foreach ($tables as $table)
                        <option value="{{ $table->table_name }}" <?php if ($table->table_name == Request::get('table_name')) {
                            echo 'selected';
                        } ?>>{{ $table->table_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" style="display: inline-block;width: 10%" class="btn btn-sm btn-image">
                    <img src="/images/search.png" style="cursor: default;">
                </button>
                <a href="{{route('database.tables-list')}}" type="button" class="btn btn-image" id=""><img src="/images/resend2.png"></a>    
            </div>
        </div>
        </form>
    </div>
    
  <div class="col-md-12">
    <div class="table-responsive-lg" id="page-view-result">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th style="width: 5%;">#</th>
            <th>Table Name</th>
            <th style="width: 10%;">Table Size</th>
            <th style="width: 5%;">Action</th>
          </tr>
        </thead>

        <tbody>
        <?php
        if (! empty($tables)) {
            foreach ($tables as $table) {

                if (! empty(Request::get('table_name'))) {
                    if ($table->table_name == Request::get('table_name')) {
                        ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="tables_check" class="tables_check" value="{{ $table->table_name }}" data-id="{{ $table->table_name }}">
                            </td>
                          <td>{{ $table->table_name }}</td>
                          <td>{{ $table->size_kb . " KB" }}</td>
                          <td><button type="button" data-table-name="{{$table->table_name}}" class="btn btn-image truncate-history-show" title="Status Histories"><i class="fa fa-info-circle"></i></button></td>
                        </tr>
                    <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="tables_check" class="tables_check" value="{{ $table->table_name }}" data-id="{{ $table->table_name }}">
                        </td>
                      <td>{{ $table->table_name }}</td>
                      <td>{{ $table->size_kb . " KB" }}</td>
                      <td><button type="button" data-table-name="{{$table->table_name}}" class="btn btn-image truncate-history-show" title="Status Histories"><i class="fa fa-info-circle"></i></button></td>
                    </tr>
                <?php
                } ?>
                
              <?php }?>
          <?php }?>
        </tbody>
        </table>
    </div>
  </div>
</div>

<div id="truncate-table-histories-list" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Truncate Table Histories</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="20%">Truncate BY</th>
                                    <th width="30%">Truncate Date</th>
                                </tr>
                            </thead>
                            <tbody class="truncate-table-histories-list-view">
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


<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
  50% 50% no-repeat;display:none;">
</div>
<script type="text/javascript">
    $(document).on("click",".truncate-tables-btn",function() {
        var selectedCheckboxes = [];
        var fileIDs = [];

        $('input[name="tables_check"]:checked').each(function() {
            var fileID = $(this).data('id');
            var checkboxValue = $(this).val();

            fileIDs.push(fileID);
            selectedCheckboxes.push(checkboxValue);
        });

        if (selectedCheckboxes.length === 0) {
            alert('Please select at least one checkbox.');
            return;
        }  

        var formData = {
            ids: selectedCheckboxes 
        };

        if (confirm('Are you sure you want to truncate the selected tables?')) {

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '{{ route('truncate-tables') }}',
                data: formData,
                success: function(response) {
                    toastr["success"]("Your selected batabase tables has been truncate successfully");
                    //location.reload();
                },
                error: function(error) {
                    console.error('Error:', error);
                    //location.reload();
                }
            });      

        }
    });

     $(document).on('click', '.truncate-history-show', function() {
        var table_name = $(this).attr('data-table-name');

        $.ajax({
            url: "{{route('truncate.tables.histories')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'table_name' :table_name,
            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#truncate-table-histories-list").find(".truncate-table-histories-list-view").html(html);
                    $("#truncate-table-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });
</script>
@endsection