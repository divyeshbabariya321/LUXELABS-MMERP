@extends('layouts.app')

@section('title', 'Totem Cron Module')

@section('styles')
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
<style>
.ajax-loader{
    position: fixed;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.2);
    z-index: 1060;
}
.inner_loader {
	top: 30%;
    position: absolute;
    left: 40%;
    width: 100%;
    height: 100%;
}
.pd-5 {
  padding:5px !important;
}
.pd-3 {
  padding:3px !important;
}
.status-select-cls .multiselect {
  width:100%;
}
.btn-ht {
  height:30px;
}
.status-select-cls .btn-group {
  width:100%;
  padding: 0;
}
.table.table-bordered.order-table a{
color:black!important;
}
.fa-info-circle{
    padding-left:10px;
    cursor: pointer;
}
table tr td {
  word-wrap: break-word;
}
.fa-list-ul{
    cursor: pointer;
}

.fa-upload{
    cursor: pointer;
}
.fa-refresh{
    cursor: pointer;
    color:#000;
}
.red{
    color: red
}
#addEditTaskModal .modal-dialog{
    max-width: 1050px;
    width: 100%;
}
.uk-margin-remove th, .uk-margin-remove td{
    padding: 4px 10px;
}
.btn-default:focus{
    outline: none;
    border:1px solid #ddd;
    box-shadow: none !important;
    background: #fff;
    color: #757575;
}
.select2-container {
    width: 100% !important;
}
</style>
@endsection

@section('large_content')
    <script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.jscroll.min.js') }} "></script>

    
	<div class="ajax-loader" style="display: none;">
		<div class="inner_loader">
		<img src="{{ asset('/images/loading2.gif') }}">
		</div>
	</div>

    <div class="row">
        <div class="infinite-scroll" style="width:100%;padding: 0 8px">
            {!! $tasks->links() !!}
	        <div class="table-responsive mt-2">
                <table class="table table-bordered order-table" style="color:black;table-layout:fixed">
                    <thead>
                        <tr>
                            <th width="2%">ID</th>
                            <th width="5%">Date</th> 
                            <th width="5%">User</th> 
                            <th width="8%">Hubstaff Hours</th>
                            <th width="5%">Hours Tracked</th>
                            <th width="5%">Hours Not Tracked</th>
                            <th width="5%">Task ID</th>
                            <th width="5%">Approved Hours</th>
                            <th width="5%">Difference Hours</th>
                            <th width="5%">Total Hours</th>
                            <th width="5%">Activity Levels</th>
                            <th width="5%">Status</th>
                        </tr>    
                    </thead>
                    <tbody> 
                            @foreach($userTrack as $key => $time)
                            <tr>
                                <td>{{$time->id}}</td>
                                <td>{{date('d M Y',strtotime($time->created_at))}}</td>
                                <td>{{$time->user_name}}</td>
                                <td>{{$time->hubstaff_tracked_hours}}</td>
                                <td>{{$time->hours_tracked_with}}</td>
                                <td>{{$time->task_id}}</td>
                                <td>{{$time->approved_hours}}</td>
                                <td>{{$time->difference_hours}}</td>
                                <td>{{$time->total_hours}}</td>
                                <td>{{$time->activity_levels}}</td>
                            </tr>
                            @endforeach
                    </tbody>
                </table>
                @if(!count($tasks))
                <h5 class="text-center">No Tasks found</h5> 
                @endif
	        </div>
        </div>
    </div>

@endsection
@section('scripts')

<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.jscroll.min.js') }} "></script>
<script type="text/javascript"> 
    $('ul.pagination').hide();
    $(function() {
        $('.infinite-scroll').jscroll({
            autoTrigger: true,
            loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
            padding: 2500,
            nextSelector: '.pagination li.active + li a',
            contentSelector: 'div.infinite-scroll',
            callback: function() {
                $('ul.pagination').hide();
                setTimeout(function(){
                    $('ul.pagination').first().remove();
                }, 2000);
                $(".select-multiple").select2();
                initialize_select2();
            }
        });
    });

    $('#command').select2({
        dropdownParent: $('#addEditTaskModal')
    });

    var freq = 0;
    $('#addEditTaskModal').on('hidden.bs.modal', function (e) {
        $('.error').remove();
        $(this).attr('data-id', '');
        $('#addEditTaskModal .modal-title').html('Create task');
        $('.freq').html('<tr><td class="default_td">No Frequencies Found</td></tr>');
    });

    $('.infinite-scroll').jscroll({
            autoTrigger: true,
            loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
            padding: 2500,
            nextSelector: '.pagination li.active + li a',
            contentSelector: 'div.infinite-scroll',
            callback: function() {
                $('ul.pagination').first().remove();
                $(".select-multiple").select2();
            }
    });

    $(document).on("click",".view-task",function(e) {
        let expression = $(this).attr('data-expression');
        $.ajax({
            type: "GET",
            url: "/totem/tasks/"+$(this).data('id'), 
            dataType : "json",
            success: function (response) {
                var html_content = '';
                html_content += '<tr class="supplier-10">';      
                html_content += '<td>' + response.task.description.substring(0, 80) + '</td>';
                html_content += '<td>' + response.task.command + '</td>';
                var parameters = response.task.parameters != null ? response.task.parameters : 'N/A';
                html_content += '<td>' + parameters + '</td>';
                html_content += '<td>' + expression + '</td>'; 
                html_content += '<td>' + response.task.timezone + '</td>';
                html_content += '<td>' + response.task.created_at + '</td>';
                html_content += '<td>' + response.task.updated_at + '</td>';
                var notification_email_address = response.task.notification_email_address == null ? 'N/A' : response.task.notification_email_address;
                html_content += '<td>' + notification_email_address + '</td>';
                var notification_phone_number = response.task.notification_phone_number == null ? 'N/A' : response.task.notification_phone_number;
                html_content += '<td>' + notification_phone_number + '</td>';
                var notification_slack_webhook = response.task.notification_slack_webhook == null ? 'N/A' : response.task.notification_slack_webhook;
                html_content += '<td>' + notification_slack_webhook + '</td>';
                html_content += '<td>' + response.results + ' seconds' + '</td>';
                html_content += '<td>' + response.task.upcoming + '</td>';
                html_content += '</tr>';

                if(response.task.dont_overlap || response.task.run_in_maintenance || response.task.run_on_one_server){
                    $("#view_task_modal .notes").removeClass('d-none');                   
                }

                if(response.task.dont_overlap){
                    $("#view_task_modal .dont_overlap").removeClass('d-none');                   
                }

                if(response.task.run_in_maintenance){
                    $("#view_task_modal .run_in_maintenance").removeClass('d-none');                   
                }

                if(response.task.run_on_one_server){
                    $("#view_task_modal .run_on_one_server").removeClass('d-none');                   
                }
                $("#view_task_modal tbody").html(html_content);                   
                $('#view_task_modal').modal('show');   
            },
            error: function () {
                toastr['error']('Something went wrong!');
            }
        });
    }); 

    $(document).on("click",".execution-history",function(e) {
        let results = JSON.parse($(this).attr('data-results'));
        var html_content = '';
        for(let i=0; i< results.length; i++){ 
            html_content += '<tr>'; 
            html_content += '<td>' + results[i].ran_at + '</td>';
            html_content += '<td>' + (results[i].duration / 1000 , 2).toFixed(2) + ' seconds</td>';
            html_content += `<td id="show-result" data-output="${results[i].result}"><i class="fa fa-info-circle"></i></td>`;
            html_content += '</tr>';
        }
        if(results.length == 0){
            html_content += '<tr class="text-center"><td colspan="3"><h5>' + 'Not executed yet.' + '</h5></td></tr>';
        }
        $("#view_execution_history tbody").html(html_content);                   
        $('#view_execution_history').modal('show'); 
    });

    $(document).on("click","#show-result",function(e) {
        let results = $(this).attr('data-output'); 
        $("#showResultModal .modal-body h5").html(results);                   
        $('#showResultModal').modal('show'); 
    });

    $(document).on("click",".execute-task",function(e) {
        thiss = $(this);
        thiss.html(`<img src="/images/loading_new.gif" style="cursor: pointer; width: 0px;">`);
        $.ajax({
            type: "GET",
            url: "/totem/tasks/"+$(this).data('id')+"/execute", 
            dataType : "json",
            success: function (response) {
                toastr['success']('Task executed successfully!');
                thiss.html(`<img src="/images/send.png" style="cursor: pointer; width: 0px;">`);
            },
            error: function (response) {
                if(response.status == 200){
                    toastr['success']('Task executed successfully!');
                }else{
                    toastr['error']('Something went wrong!');
                }
                thiss.html(`<img src="/images/send.png" style="cursor: pointer; width: 0px;">`);
            }
        });
    }); 

    $(document).on("click",".delete-tasks",function(e) {
        if(confirm('Do you really want to delete this task?')){
            $.ajax({
            type: "POST",
            url: "/totem/tasks/"+$(this).data('id')+"/delete", 
            data: {
				_token: "{{ csrf_token() }}", 
            }, 
            dataType : "json",
            success: function (response) {
                if(response.status){
                    toastr['success'](response.message);
                }else{
                    toastr['error'](response.message);
                }
                setTimeout(function(){
                    window.location.reload(1);
                }, 1000);
            },
            error: function () {
                toastr['error']('Something went wrong!');
            }
        });
        }
    }); 

    $(document).on("click",".active-task",function(e) {
        let active = $(this).attr('data-active');
        $.ajax({
            type: "POST",
            url: "/totem/tasks/"+$(this).data('id')+"/status", 
            data: {
                active: active,
				_token: "{{ csrf_token() }}", 
            }, 
            dataType : "json",
            success: function (response) {
                if(response.status){
                    toastr['success'](response.message);
                }else{
                    toastr['error'](response.message);
                }
                setTimeout(function(){
                    window.location.reload(1);
                }, 1000);
            },
            error: function () {
                toastr['error']('Something went wrong!');
            }
        });
    });   

    $('#frequency').change(function(){
        $('.added_params').remove();
        let params = JSON.parse($(this).val());
        if(params.parameters){
            let html = '';
            for(let i=0; i<params.parameters.length; i++){
                html += `
                <div class="form-group added_params">
                    <input type="text" value="" name="${params.parameters[i].name}" placeholder="${params.parameters[i].label}" class="form-control">
                </div> 
                `;
            }
            $(this).closest('.modal-body').append(html);
        }
    }); 

    $('input[name="type"]').change(function(){
        if($(this).val() == 'expression'){
            $('.cron_expression').removeClass('d-none');
            $('.frequencies').addClass('d-none');
        }else{
            $('.frequencies').removeClass('d-none');
            $('.cron_expression').addClass('d-none');
        }
    }); 

    $(document).on('click', '.remove_td', function(){
        $(this).closest('tr').remove();
    });

    $('.add_freq').click(function(){
        $('.default_td').remove();
        let freq_type = JSON.parse($('#addFrequencyModal').find('select').val());
        let input_fields = $('#addFrequencyModal').find('input');
        let tr = `
                    <tr> 
                    <td data-id="${freq}">${freq_type.label}
                        <input type="hidden" name="frequencies[${freq}][interval]" value="${freq_type.interval}">
                        <input type="hidden" name="frequencies[${freq}][label]" value="${freq_type.label}">
                    </td>
                    <td data-id="${freq}">
                    `;
        for(let i=0; i<input_fields.length; i++){
            tr += `${i!=0 ? ',' : ''} ${$(input_fields[i]).val()}
                        <input type="hidden" name="frequencies[${freq}][parameters][${i}][name]" value="${$(input_fields[i]).attr('name')}">
                        <input type="hidden" name="frequencies[${freq}][parameters][${i}][value]" value="${$(input_fields[i]).val()}">
                    `;
        }
        if(input_fields.length == 0){
            tr += `No Parameters`; 
        }
        tr += ` </td>
                <td>
                    <a class="remove_td">
                        <i class="fa fa-window-close"></i>
                    </a>
                </td>
                </tr>`;
        $('.freq').append(tr);
        freq++;
        $('#addFrequencyModal').modal('hide');   
    }); 

    $('.submit_btn').click(function(){
        
        $('.error').remove();
        let url = $('#addEditTaskModal').attr('data-id') == '' ? '/totem/tasks/create' : `/totem/tasks/${$('#addEditTaskModal').attr('data-id')}/edit`
        var form_data =  $('.taskForm').serialize();
        $.ajax({
            type: "POST",
            url: url,  
            data: form_data,
            dataType : "json",
            success: function (response) {
                if(response.task){
                    toastr['success']('Task Updated Successfully.');
                }else{
                    // toastr['error']('Something went wrong!');
                }
                setTimeout(function(){
                    window.location.reload(1);
                }, 1000);
            },
            error: function (response) {
                if(response.status == 200){
                    toastr['success']('Task Created Successfully.');
                    setTimeout(function(){
                        window.location.reload(1);
                    }, 1000);
                }else{
                    debugger;
                    let errors = response.responseJSON.errors;
                    let error = '';
                    for (var key in errors) {
                        if($(`input[name="${key}"]`).length == 0){
                             error = `<p class="error" style="color:red;margin-top:-15px">${errors[key][0]}</p>`;
                            $(`select[name="${key}"]`).parent().after(error);
                        }else{
                            error = `<p class="error" style="color:red">${errors[key][0]}</p>`;
                            $(`input[name="${key}"]`).after(error);
                        }
                        if(key == 'frequencies'){
                            error = `<p class="error" style="color:red;margin-top:-15px">${errors[key][0]}</p>`;
                            $('.frequencies').after(error);
                        }
                    }
                    toastr['error'](error);
                }
                toastr['error'](errors.message);
            }
        });

    });

    $(document).on("click",".task-history",function() {
        $.ajax({
            type: "GET",
            url: "/totem/tasks/"+$(this).data('id')+"/development-task",  
            beforeSend : function() {
                $(".ajax-loader").show();
            },
            success: function (response) {
                $(".ajax-loader").hide();
                $("#show-development-history").find(".modal-body").html(response);
                $("#show-development-history").modal("show");
            },
            error: function (response) { 
                $(".ajax-loader").hide();
                if(response.status != 200){      
                    toastr['error']('Something went wrong!');
                }
            }
        });
    });

    $('.edit-task').click(function(){
        freq = 0;
        $('#addEditTaskModal').attr('data-id', $(this).data('id'));
        $('#addEditTaskModal .modal-title').html('Edit task');
        $.ajax({
            type: "GET",
            url: "/totem/tasks/"+$(this).data('id'),  
            dataType : "json",
            success: function (response) {
                let task_fields = response  .task;
                for (var key in task_fields) {
                    if($(`input[name="${key}"]`).length != 0){
                        $(`input[name="${key}"]`).val(task_fields[key]);
                    }else if($(`select[name="${key}"]`).length != 0){
                        $(`select[name="${key}"]`).val(task_fields[key]);
                    }
                    if(key == 'frequencies'){
                        if(task_fields[key].length){
                            $('.default_td').remove();
                        }
                        for(let i=0; i<task_fields[key].length; i++){
                            let interval = task_fields[key][i].interval; 
                            let label = task_fields[key][i].label; 
                            let parameters = task_fields[key][i].parameters; 
                            let tr = `
                                        <tr> 
                                        <td data-id="${freq}">${label}
                                            <input type="hidden" name="frequencies[${freq}][interval]" value="${interval}">
                                            <input type="hidden" name="frequencies[${freq}][label]" value="${label}">
                                        </td>
                                        <td data-id="${freq}">
                                        `;
                            for(let j=0; j<task_fields[key][i].parameters; j++){
                                tr += `${j!=0 ? ',' : ''} ${$(task_fields[i]).val()}
                                            <input type="hidden" name="frequencies[${freq}][parameters][${j}][name]" value="${$(task_fields[j]).attr('name')}">
                                            <input type="hidden" name="frequencies[${freq}][parameters][${j}][value]" value="${$(task_fields[j]).val()}">
                                        `;
                            }
                            if(task_fields[key][i].parameters.length == 0){
                                tr += `No Parameters`; 
                            }
                            tr += ` </td>
                                    <td>
                                        <a class="remove_td">
                                            <i class="fa fa-window-close"></i>
                                        </a>
                                    </td>
                                    </tr>`;
                            $('.freq').append(tr);
                            freq++;
                        }
                    }
                    $('#addEditTaskModal').modal('show');  
                }
            },
            error: function (response) { 
                if(response.status != 200){      
                    toastr['error']('Something went wrong!');
                }
            }
        });
    });

</script>
@endsection