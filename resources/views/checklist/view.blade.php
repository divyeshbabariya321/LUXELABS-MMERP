@extends('layouts.app')



@section('title', $title)

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput-typeahead.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<style type="text/css">
    #loading-image {
        position: fixed;
        top: 50%;
        left: 50%;
        margin: -50px 0px 0px -50px;
    }

    .disabled {
        pointer-events: none;
        background: #bababa;
    }

    .glyphicon-refresh-animate {
        -animation: spin .7s infinite linear;
        -webkit-animation: spin2 .7s infinite linear;
    }

    @-webkit-keyframes spin2 {
        from {
            -webkit-transform: rotate(0deg);
        }

        to {
            -webkit-transform: rotate(360deg);
        }
    }

    @keyframes spin {
        from {
            transform: scale(1) rotate(0deg);
        }

        to {
            transform: scale(1) rotate(360deg);
        }
    }

    #CreateCheckList .bootstrap-tagsinput,
    #EditCheckList .bootstrap-tagsinput {
        display: block;

    }

    #CreateCheckList .modal-body strong,
    #EditCheckList .modal-body strong {
        display: block;
        margin-bottom: 5px;
    }

    #CreateCheckList .bootstrap-tagsinput .tag,
    #EditCheckList .bootstrap-tagsinput .tag {
        background: gray;
        color: white;
        font-size: 14px;
    }

    .dataTables_scrollHeadInner {
        width: 100% !important;
    }

    .dataTables_scrollHeadInner table {
        width: 100% !important;
    }

    .dataTables_scrollBody table {
        width: 100% !important;
    }

    .addCheckList {
        display: block;
        width: 100%;
    }
</style>
@endsection


@section('content')
<div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
</div>
<input type="hidden" value="{{ $id }}" name="checklist_id">
<input type="hidden" value="{{ \Auth::id() }}" name="user_id">
<div class="table-responsive mt-3 pr-2 pl-2">
    @if ($message = Session::get('success'))
    <div class="col-lg-12">
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    </div>
    @endif

    @if ($errors->any())
    <div class="col-lg-12">
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <div class="form-row align-items-center">
        <div class="col-auto">
            <input type='text' class="form-control" name="record_date" id="record_date" required />
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-primary mb-2 add-record">Add</button>
        </div>
    </div>
    <div class="checklist_data">
        <table class="table table-bordered text-nowrap" id="assign_checklist_table">

        </table>
    </div>

</div>


<div id="remarkHistoryModel" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content ">
        <div id="add-mail-content">
          <div class="modal-content">
            <div class="modal-header">
              <h3 class="modal-title">Remark History</h3>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Added by</th>
                    <th>Remark</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody class="remarkHistoryTboday">
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-datepicker.min.js') }} "></script>

<script>
    $('#record_date').datepicker({
        format: 'yyyy-mm-dd' //2022-06-29
    });
    var addRecord = "{{ route('checklist.add') }}";
    var addRemarkRecord = '{{ route('checklist.add.remark') }}';
    var addRemarkHistory = '{{ route('checklist.remark.list') }}';
    var subjects = [];
    var oTable;
    var columns = [{
            data: null,
            title: "No",
            width: "5%",
            render: function(data, type, full, meta) {
                return meta.row + 1;
            }
        },
        {
            data: 'subjects',
            title: "Subject",
            width: "50%",
            name: 'checklist.subjects',
            render: function(data, type, row, meta) {
                if (data !== null && data !== '') {
                    subjects.push(row.id);
                    return row.title;
                }
            }
        },
        {
            data: null,
            title : "Remark",
            
            name: 'subjects.remark',
            class : "expand-row-msg",
            attr : "data-name='remark'",
            render: function(data, type, row, meta) {
                if(typeof row.checklistsubject_remark[0]  !== 'undefined') {
                    var text_remark_lim = '';
                    if(row.checklistsubject_remark[0].remark){
                        var trtext = row.checklistsubject_remark[0].remark;
                        text_remark_lim = trtext.substring(0, 5)+'...';
                    }
                    var text_remark = '<div><span class="show-short-remark-' + row.id + '">' + text_remark_lim + '</span>    <span style="word-break:break-all;" class="show-full-remark-' + row.id + ' hidden">' + row.checklistsubject_remark[0].remark + '</span></div>';
                    return "<div style='width:200px !important;height: 35px;'><input type='text' data-subect_id='"+row.id+"' value='' class='form-control remark"+row.id+"' style='margin-top: 0px;width:70% !important;float:left;'  > <button class='btn pr-0 btn-xs btn-image add-remark' data-id='"+row.id+"' style='float:left;' onclick='saveRemarks("+row.id+")'><img src='/images/filled-sent.png' style='cursor: nwse-resize;'></button> <button style='padding-left: 0;padding-right:0px;' type='button' class='btn btn-image d-inline remark_history' title='Show remark history' data-subect_id='"+row.id+"'><i class='fa fa-info-circle'></i></button></div>"+text_remark;                                
                } else {
                    return "<div style='width:200px !important;float:left;'><input type='text' data-subect_id='"+row.id+"' value='' class='form-control remark"+row.id+"' style='margin-top: 0px;width:70% !important;float:left;'  > <button class='btn pr-0 btn-xs btn-image add-remark' data-id='"+row.id+"' style='float:left;' onclick='saveRemarks("+row.id+")'><img src='/images/filled-sent.png' style='cursor: nwse-resize;'></button></div>";
                }                  
            }
        }
    ];




    // START Print Table Using datatable

    $(document).ready(function() {

        $(document).on('change', '.checkbox', function(e) {
            var checklistSubjectID = $(this).attr("data-id");
            if ($(this).is(":checked")) {
                var is_checked = 1;
            } else {
                var is_checked = 0;
            }
            $.ajax({
                type: 'POST',
                url: "{{ route('checklist.update.c') }}",
                data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    "id": checklistSubjectID,
                    "is_checked": is_checked
                },
                success: function(data) {
                    oTable.draw();
                    toastr["success"](data.message);
                }
            });
        });

        $(document).on('click', '.add-record', function(e) {
            if($("#record_date").val() == ''){
                siteErrorAlert('Please select date.');
                return false;
            }
            $.ajax({
                type: 'POST',
                url: addRecord,
                data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    "date": $("#record_date").val(),
                    "id": $('input[name=checklist_id]').val(),
                    "subjects": subjects
                },
                success: function(data) {
                    columns.push({
                        data: 'checklistsubject',
                        width: "10%",
                        title: $("#record_date").val(),
                        name: 'checklistsubject',
                        render: function(data, type, row, meta) {
                            // alert(data.length);
                            var title = $('#assign_checklist_table').DataTable().columns(meta.col).header();
                            var columnName = $(title).html();
                            if (row.checklistsubject != null && row.checklistsubject !== '') {
                                for (var j = 0; j < row.checklistsubject.length; j++) {
                                    // console.log(row.checklistsubject[i])    
                                    if ((row.checklistsubject[j].date && row.checklistsubject[j].date === columnName) && (row.checklistsubject[j].checklist_id && row.checklistsubject[j].checklist_id === row.checklist_id) && (row.checklistsubject[j].subject_id && row.checklistsubject[j].subject_id === row.id) && (row.checklistsubject[j].user_id && row.checklistsubject[j].user_id === $('input[name=user_id]').val())) {
                                        if (row.checklistsubject[j].is_checked == 0) {
                                            return "<input type='checkbox' data-id='" + row.checklistsubject[j].id + "' value='0' class='checkbox' >";
                                        } else {
                                            return "<input type='checkbox' data-id='" + row.checklistsubject[j].id + "' value='1' checked class='checkbox'>";
                                        }
                                    }
                                }
                            }
                        }
                    });
                    toastr["success"](data.message);
                    oTable.destroy();
                    $('#assign_checklist_table').empty();
                    oTable = $('#assign_checklist_table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: "{{ route('checklist.subjects') }}",
                            type: "POST",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: function(d) {
                                d.id = $('input[name=checklist_id]').val();
                                d.type = "datatable";
                            }
                        },
                        bSort: false,
                        columns: columns,
                    });
                }
            });
        });

        $(document).on('click', '.add-remark', function(e) {
            var subjectID = $(this).attr("data-id");
            var remark = $(".remark"+subjectID).val();
            $.ajax({
                type:'POST',
                url: addRemarkRecord,
                data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    "checklist_id" : $('input[name=checklist_id]').val(),
                    "subject_id" : subjectID,
                    "remark" : remark
                }, 
                success:function(data) {                        
                    toastr["success"](data.message);
                    init();
                }
            });     
        }); 
        
        $(document).on('click', '.remark_history', function(e) {
                var subjectID = $(this).attr("data-subect_id");
                $.ajax({
                    type:'POST',
                    url: addRemarkHistory,
                    data: {
                        "_token": $('meta[name="csrf-token"]').attr('content'),
                        "subject_id" : subjectID,
                    }, 
                    success:function(response) {                        
                        if (response.code = '200') {
                            var t = '';
                            $.each(response.data, function(key, v) {
                            t += '<tr><td>' + v.id + '</td>';
                            t += '<td>' + v.username + '</td>';
                            t += '<td>' + v.remark + '</td>';
                            t += '<td>' + v.created_at + '</td></tr>';
                            });
                            $(".remarkHistoryTboday").html(t);
                            $('#remarkHistoryModel').modal('show');
                            toastr['success']('History Listed successfully!!!', 'success');

                        } else {
                            toastr['error'](response.message, 'error');
                        }
                    }
                });     
            }); 

            $(document).on('click', '.expand-row-msg', function() {
                var name = $(this).data('name');
                var id = $(this).data('id');
                var full = '.expand-row-msg .show-short-' + name + '-' + id;
                var mini = '.expand-row-msg .show-full-' + name + '-' + id;
                $(full).toggleClass('hidden');
                $(mini).toggleClass('hidden');
            });
        $.ajax({
            type: 'POST',
            url: "{{ route('checklist.subjects') }}",
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "id": $('input[name=checklist_id]').val(),
                "type": "normal"
            },
            success: function(d) {
                if (d !== null && d !== '') {
                    var checklistsubject = JSON.parse(d);
                    for (var i = 0; i < checklistsubject.length; i++) {
                        if (checklistsubject[i]) {
                            columns.push({
                                data: 'checklistsubject',
                                width: "10%",
                                title: checklistsubject[i].date,
                                name: 'checklistsubject',
                                render: function(data, type, row, meta) {
                                    var title = $('#assign_checklist_table').DataTable().columns(meta.col).header();
                                    var columnName = $(title).html();
                                    if (row.checklistsubject != null && row.checklistsubject !== '') {
                                        for (var j = 0; j < row.checklistsubject.length; j++) {
                                            if ((row.checklistsubject[j].date && row.checklistsubject[j].date === columnName) && (row.checklistsubject[j].checklist_id && row.checklistsubject[j].checklist_id === row.checklist_id) && (row.checklistsubject[j].subject_id && row.checklistsubject[j].subject_id === row.id) && (row.checklistsubject[j].user_id && row.checklistsubject[j].user_id === $('input[name=user_id]').val())) {
                                                if (row.checklistsubject[j].is_checked == 0) {
                                                    return "<input type='checkbox' data-id='" + row.checklistsubject[j].id + "' value='0' class='checkbox' >";
                                                } else {
                                                    return "<input type='checkbox' data-id='" + row.checklistsubject[j].id + "' value='1' checked class='checkbox'>";
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    }
                }
                init();
            }
        });
    });

    function init() {
        oTable = $('#assign_checklist_table').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: {
                url: "{{ route('checklist.subjects') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    d.id = $('input[name=checklist_id]').val();
                    d.type = "datatable";
                }
            },
            bSort: false,
            columns: columns,
            columnDefs: [
                            {
                                'targets': 2,
                                'createdCell':  function (td, cellData, rowData, row, col) {
                                $(td).attr('data-name', 'remark'); 
                                $(td).attr('data-id', cellData.id); 
                                
                                }
                            }
                        ]
        });
    }
    // END Print Table Using datatable
</script>

<style>
    .dataTables_wrapper{
        overflow-x: scroll;
    }
</style>
@endsection
