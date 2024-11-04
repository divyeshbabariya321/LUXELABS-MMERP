@extends('layouts.app')



@section('title', $title)

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput-typeahead.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
        
        .disabled{
            pointer-events: none;
            background: #bababa;
        }
        .glyphicon-refresh-animate {
            -animation: spin .7s infinite linear;
            -webkit-animation: spin2 .7s infinite linear;
        }

        @-webkit-keyframes spin2 {
            from { -webkit-transform: rotate(0deg);}
            to { -webkit-transform: rotate(360deg);}
        }

        @keyframes spin {
            from { transform: scale(1) rotate(0deg);}
            to { transform: scale(1) rotate(360deg);}
        }
        #CreateCheckList .bootstrap-tagsinput,#EditCheckList .bootstrap-tagsinput{
            display: block;

        }
        #CreateCheckList .modal-body strong ,#EditCheckList .modal-body strong {
            display: block;
            margin-bottom: 5px;
        }
        #CreateCheckList .bootstrap-tagsinput .tag,#EditCheckList .bootstrap-tagsinput .tag  {
            background: gray;
            color: white;
            font-size: 14px;
        }
        .dataTables_scrollHeadInner{
            width: 100% !important; 
        } 
        .dataTables_scrollHeadInner table{
            width: 100% !important; 
        }
        .dataTables_scrollBody table{
            width: 100% !important; 
        }
        .addCheckList{
            display: block;
            width: 100%;
        }
    </style>
@endsection


@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>


    <div class="row ">
        <div class="col-lg-12 ">
            <h2 class="page-heading">{{ $title }}
            </h2>

            <form method="POST" action="#" id="dateform">

                <div class="row m-4">
                    <div class="col-xs-3 col-sm-3">
                        <div class="form-group">
                            {{ html()->text('category_name')->placeholder('Category Name')->class('form-control')->attribute('autocomplete', 'off') }}
                        </div>
                    </div>
                    <div class="col-xs-3 col-sm-3">
                        <div class="form-group">
                            {{ html()->text('sub_category_name')->placeholder('Sub Category name')->class('form-control')->attribute('autocomplete', 'off') }}
                        </div>
                    </div>
                    <div class="col-xs-3 col-sm-3">
                        <div class="form-group">
                            {{ html()->text('subjects')->placeholder('Subject Name')->class('form-control') }}
                        </div>
                    </div>
                    
                    <div class="col-xs-2 col-sm-1 pt-2 ">
                        <div class="d-flex" >
                            <div class="form-group pull-left ">
                                <button type="submit" class="btn btn-image search">
                                    <img src="/images/search.png" alt="Search" style="cursor: inherit;">
                                </button>
                            </div>
                            <div class="form-group pull-left ">
                                <button type="submit" id="searchReset" class="btn btn-image search ml-3">
                                    <img src="/images/resend2.png" alt="Search" style="cursor: inherit;">
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group addCheckList ml-3 mt-3">
                        <button type="button" class="btn btn-secondary " data-toggle="modal" data-target="#CreateCheckList"> Check List Create </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
        <div class="checklist_data">
            <table class="table table-bordered " id="checklist_table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Category Name</th>
                        <th>Sub Category Name</th>
                        <th>Subjects</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        
    </div>

    {{--checkListCreateModal --}} {{-- checkListEditModal --}}
    @include('checklist.models.add_checklist')

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).on('click', '#searchReset', function(e) {
            //alert('success');
            $('#dateform').trigger("reset");
            e.preventDefault();
            oTable.draw();
        });

        $('#dateform').on('submit', function(e) {
            e.preventDefault();
            oTable.draw();

            return false;
        });
        // START Print Table Using datatable
        var oTable;
        $(document).ready(function() {
            oTable = $('#checklist_table').DataTable({
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                responsive: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                sScrollX:true,
                searching: false,
                order: [
                    [0, 'desc']
                ],
                targets: 'no-sort',
                bSort: false,
                ajax: {
                    "url": "{{ route('checklist.index') }}",
                    data: function(d) {
                        d.category_name = $('input[name=category_name]').val();
                        d.sub_category_name = $('input[name=sub_category_name]').val();
                        d.subjects = $('input[name=subjects]').val();
                    },
                },
                columnDefs: [{
                    targets: [],
                    orderable: false,
                    searchable: false
                }],
                columns: [{
                        data: null,
                        width : "5%",
                        render: function (data, type, full, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'category_name',
                        width : "15%",
                        name: 'checklist.category_name',
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        data: 'sub_category_name',
                        width : "15%",
                        name: 'checklist.sub_category_name',
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },{
                        data: 'subjects',
                        width : "50%",
                        name: 'checklist.subjects',
                        render: function(data, type, row, meta) {
                            if(data !== null && data !== '') {
                                var subjects = data;
                                var subject_html = "";
                                for(var i=0; i<subjects.length; i++){
                                    subject_html += "<span class='badge badge-primary mr-2'>"+subjects[i].title+"</span>";
                                }
                                return subject_html;
                            }
                            // return data;
                        }
                    },
                    {
                        data: 'id',
                        width : "15%",
                        name: 'magento_modules.id',
                        // visible:false,
                        render: function(data, type, row, meta) {
                            var show_data = actionShowButtonWithClass('show-details', row['id']);
                            var edit_data = actionEditButtonWithClass('edit-checklist', JSON.stringify(row));
                            var del_data = actionDeleteButton(row['id']);
                            var view_route = '{{ route('checklist.view',':id') }}'; 
                            view_route = view_route.replace(':id', row['id']);
                            var view_data = actionShowButton(view_route);
                            return `<div class="flex justify-left items-center">  ${edit_data} ${del_data} ${view_data}</div>`;
                            // return data;
                        }
                    },
                ],
            });
        });
        // END Print Table Using datatable
// Show details page button
function actionShowButtonWithClass(cls, id) {
  return `<button type="button" title="Details Page"  class="btn btn-image padding-10-3 ${cls}" data-id="${id}"><img src="/images/view.png" /></button>`;
}
// Edit Button
function actionEditButtonWithClass(cls, data) {
  return `<button type='button' title='Edit'  class='btn btn-image padding-10-3 ${cls}' data-row='${data}'><img src='/images/edit.png' /></button>`;
}
// delete button
function actionDeleteButton(id, deleteclass = "clsdelete") {
  return `<a  class="btn btn-image padding-10-3 ${deleteclass}" data-id="${id}"><img src="/images/delete.png" /></a>`;
}
// Show details page button
function actionShowButton(url) {
  return `<a href="${url}" title="Details Page" class="btn btn-image padding-10-3"><img src="/images/view.png" /></a>`;
}
        $(document).on('submit', '#checklist_form', function(e){
        e.preventDefault();

        var self = $(this);
        let formData = new FormData(document.getElementById("checklist_form"));
        var button = $(this).find('[type="submit"]');
        $.ajax({
            url: '{{ route("checklist.store") }}',
            type: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                button.html(spinner_html);
                button.prop('disabled', true);
                button.addClass('disabled');
            },
            complete: function() {
                $('#CreateCheckList').modal('hide');
                button.html('Add');
                button.prop('disabled', false);
                button.removeClass('disabled');
            },
            success: function(response) {
                $('#CreateCheckList').modal('hide');
                $('#CreateCheckList #checklist_form').trigger('reset');
                $('#CreateCheckList #checklist_form').find('.error-help-block').remove();
                $('#CreateCheckList #checklist_form').find('.invalid-feedback').remove();
                $('#CreateCheckList #checklist_form').find('.alert').remove();
                
                oTable.draw();
                toastr["success"](response.message);
            },
            error: function(xhr, status, error) { // if error occured
                if(xhr.status == 422){
                    var errors = JSON.parse(xhr.responseText).errors;
                    customFnErrors(self, errors);
                }
                else{
                    Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                }
            },
        });
    });
    $('#CreateCheckList').on('shown.bs.modal', function() { 
        $('#subjects').tagsinput('removeAll');

    });
    $(document).on('click', '.edit-checklist', function() {
        var checklistData = $(this).data('row');
        $('#edit_checklist_form #checklist_id').val(checklistData.id);
        $('#edit_checklist_form #category_name').val(checklistData.category_name);
        $('#edit_checklist_form #sub_category_name').val(checklistData.sub_category_name);
        $('#edit_checklist_form #subjects').tagsinput('removeAll');
        var tags = "";
        for(i=0; i<checklistData.subjects.length; i++){
            tags += checklistData.subjects[i].title+", ";
        }
        $('#edit_checklist_form #subjects').tagsinput('add', tags);
        $('#EditCheckList').modal('show');
    });
    
    $(document).on('submit', '#edit_checklist_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("edit_checklist_form"));
        var checklist_id = $('#edit_checklist_form #checklist_id').val();
        var button = $(this).find('[type="submit"]');
        
        $.ajax({
            url: '{{ route("checklist.update", '') }}/' + checklist_id,
            type: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                button.html(spinner_html);
                button.prop('disabled', true);
                button.addClass('disabled');
            },
            complete: function() {
                button.html('Update');
                button.prop('disabled', false);
                button.removeClass('disabled');
            },
            success: function(response) {
                
                $('#EditCheckList #edit_checklist_form').find('.error-help-block').remove();
                $('#EditCheckList #edit_checklist_form').find('.invalid-feedback').remove();
                $('#EditCheckList #edit_checklist_form').find('.alert').remove();
                $('.close_modal').click();
                oTable.draw();
                toastr["success"](response.message);
            },
            error: function(xhr, status, error) { // if error occured
                if(xhr.status == 422){
                    var errors = JSON.parse(xhr.responseText).errors;
                    customFnErrors(self, errors);
                }
                else{
                    Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                }
            },
        });
    });

    // Delete Checklist
    $(document).on('click', '.clsdelete', function() {
            var id = $(this).attr('data-id');
            var e = $(this).parent().parent();
            var url = `{{ url('/') }}/checklist/` + id;
            tableDeleteRow(url, oTable);
        });
    </script>

@endsection
