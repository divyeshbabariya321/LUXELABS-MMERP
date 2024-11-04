@extends('layouts.app')

@section('title', 'Direct Database Query Page')

@section('styles')
    <style>
        #collapse {
            overflow-y: scroll;
            height: 600px;
        }
        #collapse1 {
            overflow-y: scroll;
            height: 600px;
        }

        li {
            list-style-type: none;
        }
        .padding-left-zero {
            padding-left: 0px;
        }
        .border{
            border: 1px solid grey;
            border-radius: 4px;
        }
        .update_column{
            padding: 0;
            border-right: 1px solid #dee2e6;
        }
        .update_column h3{
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
            font-size: 16px;
            font-weight: bold;
            margin-top: 12px;
        }
        .where_column{
            padding: 0;
            border-right: 1px solid #dee2e6;
        }
        .left_bar, .right_bar{
            padding: 0 15px;
        }
        .left_bar .col-md-6, .right_bar .col-md-4 {
            width: 50%;
            border-bottom: 1px solid #ddd;
        }
        .left_bar .col-md-6 .form-group, .right_bar .col-md-4 .form-group{
            margin: 6px 0;
        }
        .left_bar .col-md-6.text-left, .right_bar .col-md-4.text-left{
            border-right: 1px solid #ddd;
            align-items: center;
            display: flex;
        }
        .left_bar .col-md-6 strong{
            border-left: 1px solid #ddd;
            height: -webkit-fill-available;
            display: flex;
            align-items: center;
            padding-left: 15px;
        }

        .right-cont{
            border-right: none !important;
        }
.select_per select.globalSelect2 + span.select2{
    width: 400px !important;
}

    </style>
@endsection
@section('content')


    <h2 class="page-heading flex" style="padding: 8px 5px 8px 10px;border-bottom: 1px solid #ddd;line-height: 32px;">Direct Database Query Page
    </h2>

    <div class="alert alert-success commnd_reponse_div" role="alert" style="display:none;" >
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <p class="commnd_reponse"></p>
       
    </div>

    <div class="row" style="margin: 0 1px">

       <div class="col-md-4">
    <div class="col-xs-6 pl-3 pr-0">
            <div class="form-group">
                <select name="table" class="form-control table_class" id="table_id">
                    <option value>Select Table</option>
                    @foreach($table_array as $tab)
                    <option value="{{$tab}}" >{{$tab}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-xs-6 pl-3 text-left save_class d-none">
            <button type="submit" class="btn btn-secondary save_change_btn mr-2">Update</button>
            <button type="submit" class="btn btn-secondary delete_btn">Delete</button>
        </div>
    </div>

    <!-- START - Purpose : Command List - DEVTASK-19941 -->
    <div class="col-md-8 select_per" style="display:flex;justify-content:flex-end;">
       <div class=" pl-3 pr-0 " >
            <div class="row">
                <div class="col">
                    <input type="text" class="form-control manual_command" style="width:350px;" placeholder="Command">
                </div>
                <div class="col">
                    <select name="artisan_command" class="form-control artisan_command globalSelect2" id="artisan_command" style="max-width:400px;">
                        <option value>Select Command</option>
                        @foreach($command_list_arr as $key => $val)
                            <option value="{{$val['Name']}}" >{{$val['Name']}}  ==> {{$val['Description']}} </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- <div class="form-group">
                <select name="artisan_command" class="form-control artisan_command globalSelect2" id="artisan_command" style="max-width:400px;">
                    <option value>Select Command</option>
                    @foreach($command_list_arr as $key => $val)
                        <option value="{{$val['Name']}}" >{{$val['Name']}}  ==> {{$val['Description']}} </option>
                    @endforeach
                </select>
            </div> -->
        </div>

        <div class="pl-3 text-left">
            <button type="submit" class="btn btn-secondary execute_command mr-2">Execute Command</button>
            <a href="{{route('admin.command_execution_history')}}" class="btn btn-secondary">Command History</a>
        </div>
    </div>
    <!-- END - DEVTASK-19941 -->


    </div>

    <div class="container_" style="margin: 0 28px">
    <form class="db_query">
        <input type="hidden" name="table_name" class="table_name" value="">
        <div class="row border d-none" >
            <div class="col-md-6 update_column">
                <h3 class="text-center d-none mb-0">Update Columns</h3>
                <div class="row left_bar"> 
                </div>
            </div>
            <div class="col-md-6 update_column right-cont   ">
                <h3 class="text-center d-none mb-0">Where Query</h3>
                <div class="row right_bar">   
                </div>
            </div>
        </div>
    </form>    
    </div>

    <!-- START - Purpose : Add Loader - DEVTASK-19941 -->
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
  50% 50% no-repeat;display:none;">
    </div>
    <!-- END - DEVTASK-19941 -->
@endsection

@section('scripts')

<script>  
$(document).ready(function(){
    $('.column-operator').select2();
});

$('.table_class').change(function(){
    let table_name = this.value;
    if(table_name != ''){
        $.ajax({
            url: '{{route('admin.databse.menu.direct.dbquery.columns')}}',
            data: table_name,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            success: function(response){
                $('.container_ .row .left_bar').html('');
                $('.container_ .row .right_bar').html('');

                let cols = response.data;
                $.each(cols, function(index, value){
                    let input_type = value.Type;
                    let html = `
                                <div class="col-xs-6 col-sm-6 col-md-6 text-left">
                                    <input name="columns[${value.Field}]" type="checkbox" value="${value.Field}">
                                    <strong class="ml-3">${value.Field}</strong>
                                </div> 
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <input placeholder="${value.Field}" class="form-control" name="update_${value.Field}" type="${input_type}">
                                    </div>
                                </div>
                                `;

                    if(value.Field !== 'id') {
                        $('.container_ .row .left_bar').append(html);
                    }

                    html = `
                                <div class="col-xs-4 col-sm-4 col-md-4 text-left">
                                    <strong>${value.Field}</strong>
                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-4 text-left">
                                    <select class="column-operator" id="ColumnOperator[]" name="criteriaColumnOperators['${value.Field}']">
                                    <option value="">Select Operator</option><option value="=">=</option><option value=">">&gt;</option><option value=">=">&gt;=</option><option value="<">&lt;</option><option value="<=">&lt;=</option><option value="!=">!=</option><option value="LIKE">LIKE</option><option value="LIKE %...%">LIKE %...%</option><option value="NOT LIKE">NOT LIKE</option><option value="IN (...)">IN (...)</option><option value="NOT IN (...)">NOT IN (...)</option><option value="BETWEEN">BETWEEN</option><option value="NOT BETWEEN">NOT BETWEEN</option><option value="IS NULL">IS NULL</option><option value="IS NOT NULL">IS NOT NULL</option>
                                    </select>
                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-4">
                                    <div class="form-group">
                                        <input placeholder="${value.Field}" class="form-control" name="where_${value.Field}" type="text">
                                    </div>
                                </div>
                                `;

                    $('.container_ .row .right_bar').append(html);
                });
                $('.column-operator').select2();
            }
        });
        $('.save_class').removeClass('d-none');
        $('.container_ h3').removeClass('d-none');
        $('.container_ .row').removeClass('d-none');
        $('.table_name').val(table_name);
    }else{
        $('.save_class').addClass('d-none');
        $('.container_ h3').addClass('d-none');
        $('.container_ .row').addClass('d-none');
        $('.table_name').val('table_name');
    }
   
});

$('.save_change_btn').click(function(){
    let is_checkbox_empty = 1;
    $("input:checkbox:checked").each(function(){
        is_checkbox_empty = 0;
    });

    if(is_checkbox_empty){
        toastr["error"]('Please check at least one field !');
        return false;
    }

    let form_data = $('.db_query').serialize();
    $.ajax({
        url: '{{route('admin.databse.menu.direct.dbquery.confirm')}}',
        data: form_data,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        success: function(response){
            if(confirm('Do You really want to run the following query? \n' + response.sql)){
                $.ajax({
                    url: '{{route('admin.databse.menu.direct.dbquery.update')}}',
                    data: {
                        sql: response.sql
                    },
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    success: function(response){
                        if(response.error == ''){
                            toastr["success"]('Database updated successfully !');
                        }else{
                            toastr["error"](response.error.errorInfo[2]);
                        }
                    }
                });
                
            }
        }
    });
});



$('.delete_btn').click(function(){
    let is_input_empty = 1;
    $(".right_bar input").each(function(){
        if($(this).val() != '') is_input_empty = 0;
    });
    // if(is_input_empty){
    //     if(!confirm('You are about to DESTROY a complete table! Do you really want to drop table ?')){
    //         return false;
    //     };
    // }

    let form_data = $('.db_query').serialize();
    $.ajax({
        url: '{{route('admin.databse.menu.direct.dbquery.delete.confirm')}}',
        data: form_data,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        success: function(response){
            if(confirm('Do You really want to run the following query? \n' + response.sql)){
                $.ajax({
                    url: '{{route('admin.databse.menu.direct.dbquery.delete')}}',
                    data: {
                        sql: response.sql
                    },
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    success: function(response){
                        if(response.error == ''){
                            toastr["success"]('Database updated successfully !');
                        }else{
                            toastr["error"](response.error.errorInfo[2]);
                        }
                    }
                });
                
            }
        }
    });
});

    // START - Purpose : Command Execute - DEVTASK-19941 START - Purpose : Command List - DEVTASK-19941 
    $(document).on("click",".execute_command",function() {

        var manual_command_name = $('.manual_command').val();
    
        var command_name = $('#artisan_command').find(":selected").val();

        $.ajax({
            url: "{{route('admin.command_execution')}}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                command_name: command_name,
                manual_command_name: manual_command_name,
            },
            dataType: 'json',
            beforeSend: function() {
                toastr["success"]('Command Execution Started');
                // $("#loading-image").show();
                // $('.execute_command').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
            },
            success: function (response) {
                // if(response.code == 200) {
                //     $(".commnd_reponse_div").css("display", "block");
                //     $('.commnd_reponse').html(response.data);
                // }
               
                // $("#loading-image").hide();
                // $('.execute_command').prop('disabled', false).html('Execute Command');
            },
            error: function (response) {
                if(response.status == 200){
                    toastr["success"]('Command Execution Completed');
                }else{
                    toastr["success"]('Command Execution Failed');
                }
                // $("#loading-image").hide();
                // $('.execute_command').prop('disabled', false).html('Execute Command');
            }
        });
    });
    // END - DEVTASK-19941 
</script>

@endsection

