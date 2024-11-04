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

            <form method="POST" action="" id="dateform">

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
                        <button type="button" class="btn btn-secondary " data-toggle="modal" data-target="#CreateCheckList"> Task List Create </button>
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
    @include('Taskcategories.models.add_checklist')

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        function DeleteRow(url, oTable) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You want be able to delete this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function() {
            return new Promise(function(resolve) {
                $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: url,
                        type: 'DELETE',
                        dataType: 'json'
                    })
                    .done(function(response) {
                       
                        
                        oTable.draw().ajax.reload();
                       
                        Swal.fire('Deleted!', response.message, 'success');
                    })
                    .fail(function(response) {
                        console.log(response);
                        console.log(url);
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
}
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
                    "url": "{{ route('taskcategories.index') }}",
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
                        render: function (data, type, full, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'task_category.name',
                       
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },
                    {
                        data: 'name',
                      
                        render: function(data, type, row, meta) {
                            return data;
                        }
                    },{
                        data: 'task_subject',
                       
                        render: function(data, type, row, meta) {
                           
                            var subject = "";
                            for(i=0; i<data.length; i++){
                                subject += data[i].name+", ";
                            }
                          
                            return subject;
                        }
                    },
                    {
                        data: 'id',
                        name: 'magento_modules.id',
                        // visible:false,
                        render: function(data, type, row, meta) {
                            // var show_data = actionShowButtonWithClass('show-details', row['id']);
                            var edit_data = actionEditButtonWithClass('edit-checklist', JSON.stringify(row));
                            var del_data = actionDeleteButton(row['task_category_id']);
                            return `<div class="flex justify-left items-center">  ${edit_data} ${del_data} </div>`;
                        }
                    },
                ],
            });
           
          
        
        });
        // END Print Table Using datatable
        $(document).ready(function(){ 
            var global =0;
            var imagesPreview = function(input) {
                var html = '<div class="custom-element"><div class="form-group col-md-4"><label for="inputEmail4">Subject Name</label><input class="form-control" type="text" name="subjectname[]" id ="subjectname"></div><div class="form-group col-md-6"><label for="inputEmail4">Description</label><textarea class="form-control" name="subject[]" id ="subjects"/></div><div class="form-group col-md-2"><label for="inputEmail4">&nbsp;</label><button type="button" class="btn btn-primary form-control deletesubject" id="deletesubject0" value="1">Delete</button></div></div>';
                $('.subjects').append(html);
            };
            var Preview = function(input) {
                var html = '<div class="custom-element"><div class="form-group col-md-4"><label for="inputEmail4">Subject Name</label><input class="form-control" type="text" name="subject1[]" id ="subject"></div><div class="form-group col-md-6"><label for="inputEmail4">Description</label><textarea class="form-control" name="description1[]" id ="description"/></div><div class="form-group col-md-2"><label for="inputEmail4">&nbsp;</label><button type="button" class="btn btn-primary form-control deletesubject" id="deletesubject0" value="1">Delete</button></div></div>';
                $('.subjects').append(html);
            };
            $('#showtask').on('click', function() {
                imagesPreview(this);  
            });
           $('#showsubject').on('click', function(){
            Preview(this);
           });
        });

        var global =0;
        
        
        $(document).on('click', '.deletesubject', function(e){
            $(this).parents('.custom-element').remove();
        });

        $(document).on('submit', '#task_form', function(e){
        e.preventDefault();

        var self = $(this);
        let formData = new FormData(document.getElementById("task_form"));
        var button = $(this).find('[type="submit"]');
        $.ajax({
            url: '{{ route("taskcategories.store") }}',
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
                $('#CreateCheckList #task_form').trigger('reset');
                $('#CreateCheckList #task_form').find('.error-help-block').remove();
                $('#CreateCheckList #task_form').find('.invalid-feedback').remove();
                $('#CreateCheckList #task_form').find('.alert').remove();
                
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
    // $('#CreateCheckList').on('shown.bs.modal', function() { 
    //     $('#subjects').tagsinput('removeAll');

    // });
    $(document).on('click', '.edit-checklist', function() {
        var checklistData = $(this).data('row');
        
        $('#edit_task_form #checklist_id').val(checklistData.task_category_id);
        $('#edit_task_form #category_name').val(checklistData.task_category.name);
        $('#edit_task_form #sub_category_name').val(checklistData.name);
        var length = checklistData.task_subject.length;
      
        $('#edit_task_form #subjects').tagsinput('removeAll');
        $('#EditCheckList .subjects').html("");
        for(i=0; i<length; i++){          
            var html = '<div class="custom-element"><div class="form-group col-md-4"><label for="inputEmail4">Subject Name</label><input class="form-control" type="text" name="subject[]" id ="subject'+i+'"></div><div class="form-group col-md-6"><label for="inputEmail4">Description</label><textarea class="form-control" name="description[]" id ="description'+i+'"/></div><div class="form-group col-md-2"><label for="inputEmail4">&nbsp;</label><button type="button" class="btn btn-primary form-control deletesubject-edit" id="deletesubject'+i+'" value="1">Delete</button></div></div>';
            $('#EditCheckList .subjects').append(html);            
            $('#edit_task_form #subject'+i+'').val(checklistData.task_subject[i].name);
            $('#edit_task_form #description'+i+'').val(checklistData.task_subject[i].description);
            $('#edit_task_form #deletesubject'+i+'').val(checklistData.task_subject[i].id);
        }     
        $('#EditCheckList').modal('show');
    });
    
    $(document).on('submit', '#edit_task_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("edit_task_form"));
        var checklist_id = $('#edit_task_form #checklist_id').val();
        var button = $(this).find('[type="submit"]');
        
        $.ajax({
            url: '{{ route("taskcategories.update", '') }}/' + checklist_id,
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
                
                $('#EditCheckList #edit_task_form').find('.error-help-block').remove();
                $('#EditCheckList #edit_task_form').find('.invalid-feedback').remove();
                $('#EditCheckList #edit_task_form').find('.alert').remove();
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
        $('.close_modal').click(function(){
            $(".hello").remove(); 
        });
        $('#close').click(function(){
            $(".hello").remove(); 
        });

    // Delete Checklist
    $(document).on('click', '.clsdelete', function() {
            var id = $(this).attr('data-id');
            var e = $(this).parent().parent();
            var url = `{{ url('/') }}/tasklist/` + id;
          
            tableDeleteRow(url, oTable);
        });
        $(document).on('click', '.deletesubject-edit', function() {
            var id = $(this).val();
            
            
            var url = `{{ url('/') }}/tasksubject/` + id;
          
            DeleteRow(url, oTable);
          
           
            
        });
        //deletesubject-edit
    </script>

@endsection
