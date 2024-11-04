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
        #devoopslist_table td .justify-left {display: inline-block !important;width: 100%;}
        #devoopslist_table td .justify-left .edit-checklist, #devoopslist_table td .justify-left .clsdelete, #devoopslist_table td .justify-left .sub_edit-checklist, #devoopslist_table td .justify-left .clssubdelete {float: right;}
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
                    <div class="col-xs-2 col-sm-2 p-0">
                        <select class="form-control" name="category_name[]" id="category" multiple="multiple">
                            <?php
                            foreach ($devoops_main_categories as $row_cate) { ?>
                                <option value="<?php echo $row_cate->id; ?>" ><?php echo $row_cate->name; ?></option>
                            <?php }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-2 col-sm-2">
                        <select class="form-control" name="sub_category_name[]" id="sub_category" multiple="multiple">
                            <?php
                            foreach ($devoops_sub_categories as $row_cate) { ?>
                                <option value="<?php echo $row_cate->id; ?>" ><?php echo $row_cate->name; ?></option>
                            <?php }
                            ?>
                        </select>
                    </div>

                    <div class="col-xs-2 col-sm-2">
                        <div class="form-group">
                            {{ html()->text('remarks')->placeholder('Remarks')->class('form-control')->attribute('autocomplete', 'off') }}
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

                    <div class="col-xs-4 col-sm-4 pt-2">
                        <button type="button" class="btn custom-button float-left mr-3" data-toggle="modal" data-target="#CreateCheckList"> Create Category</button>
                    
                        <button type="button" class="btn custom-button float-left mr-3" data-toggle="modal" data-target="#CreateSubCheckList"> Create Sub Category</button>
                    
                        <button type="button" class="btn custom-button float-left mr-3" data-toggle="modal" data-target="#status-create">Add Status</button>

                        <button class="btn custom-button mr-3" data-toggle="modal" data-target="#newStatusColor"> Status Color</button>
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
            <table class="table table-bordered " id="devoopslist_table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">Category Name</th>
                        <th width="10%">Sub Category Name</th>
                        <th width="20%">Remarks</th>
                        <th width="10%">Status</th>
                        <th width="12%">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        
    </div>

    @include('dev-oops.models.add_checklist')

    <div id="devoops-remarks-histories-list" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Remarks Histories</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="30%">Remarks</th>
                                    <th width="20%">Updated BY</th>
                                    <th width="30%">Created Date</th>
                                </tr>
                            </thead>
                            <tbody class="devoops-remarks-histories-list-view">
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

    <div id="devoops-status-histories-list" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Status Histories</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="30%">Old Status</th>
                                    <th width="30%">New Status</th>
                                    <th width="20%">Updated BY</th>
                                    <th width="30%">Created Date</th>
                                </tr>
                            </thead>
                            <tbody class="devoops-status-histories-list-view">
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

    <div id="create-quick-task" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="<?php echo route('task.create.multiple.task.shortcutdevoops'); ?>" method="post">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title">Create Task</h4>
                    </div>
                    <div class="modal-body">

                        <input class="form-control" value="60" type="hidden" name="category_id" />
                        <input class="form-control" value="" type="hidden" name="category_title" id="category_title" />
                        <input class="form-control" type="hidden" name="site_id" id="site_id" />
                        <div class="form-group">
                            <label for="">Subject</label>
                            <input class="form-control" type="text" id="hidden-task-subject" name="task_subject" />
                        </div>
                        <div class="form-group">
                            <select class="form-control" style="width:100%;" name="task_type" tabindex="-1" aria-hidden="true">
                                <option value="0">Other Task</option>
                                <option value="4">Developer Task</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="repository_id">Repository:</label>
                            <br>
                            <select style="width:100%" class="form-control  " id="repository_id" name="repository_id">
                                <option value="">-- select repository --</option>
                                @foreach ($github_repository as $repository)
                                <option value="{{ $repository->id }}">{{ $repository->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Details</label>
                            <input class="form-control text-task-development" type="text" name="task_detail" />
                        </div>

                        <div class="form-group">
                            <label for="">Cost</label>
                            <input class="form-control" type="text" name="cost" />
                        </div>

                        <div class="form-group">
                            <label for="">Assign to</label>
                            <select name="task_asssigned_to" class="form-control assign-to select2">
                                @foreach ($allUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Create Review Task?</label>
                            <div class="form-group">
                                <input type="checkbox" name="need_review_task" value="1" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-default create-task">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="dev_task_statistics" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Dev Task statistics</h2>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body" id="dev_task_statistics_content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>Task type</th>
                                    <th>Task Id</th>
                                    <th>Assigned to</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="preview-task-image" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered" style="table-layout: fixed">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">Sl no</th>
                                    <th style=" width: 30%">Files</th>
                                    <th style="word-break: break-all; width: 40%">Send to</th>
                                    <th style="width: 10%">User</th>
                                    <th style="width: 10%">Created at</th>
                                    <th style="width: 15%">Action</th>
                                </tr>
                            </thead>
                            <tbody class="task-image-list-view">
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

    <div id="status-create" class="modal fade in" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Status</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form  method="POST" id="status-create-form">
                    @csrf
                    @method('POST')
                    <div class="modal-body">
                        <div class="form-group">
                            {{ html()->label('Name', 'status_name')->class('form-control-label') }}
                            {{ html()->text('status_name')->class('form-control')->required()->attribute('rows', 3) }}
                        </div>
                      <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary status-save-btn">Save</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <div id="status-update" class="modal fade in" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Status</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form  method="POST" id="status-update-form">
                    @csrf
                    @method('POST')

                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="form-group">
                            {{ html()->label('Select Status', 'status_name')->class('form-control-label')->id('status_name') }}
                            {{ html()->select('status_name', ['' => 'Select Status'] + $status->pluck('status_name', 'id')->toArray())->class('form-control')->required() }}
                        </div>
                      <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary status-update-btn">Save</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <div id="newStatusColor" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Dev Ooops Status Color</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{ route('devoops.statuscolor') }}" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="form-group col-md-12">
                        <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                            <tr>
                                <td class="text-center"><b>Status Name</b></td>
                                <td class="text-center"><b>Color Code</b></td>
                                <td class="text-center"><b>Color</b></td>
                            </tr>
                            <?php
                            foreach ($status as $status_data) { ?>
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;<?php echo $status_data->status_name; ?></td>
                                <td style="text-align:center;"><?php echo $status_data->status_color; ?></td>
                                <td style="text-align:center;"><input type="color" name="color_name[<?php echo $status_data->id; ?>]" class="form-control" data-id="<?php echo $status_data->id; ?>" id="color_name_<?php echo $status_data->id; ?>" value="<?php echo $status_data->status_color; ?>" style="height:30px;padding:0px;"></td>                              
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary submit-status-color">Save changes</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <div id="uploadeTaskFileModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Upload Screencast/File to Google Drive</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{ route('devoopssublist.upload-file') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="task_id" id="upload_task_id">
                    <div class="modal-body">                        
                        <div class="form-group">
                            <strong>Upload File</strong>
                            <input type="file" name="file[]" id="fileInput" class="form-control input-sm" placeholder="Upload File" style="height: fit-content;" multiple required>
                            @if ($errors->has('file'))
                                <div class="alert alert-danger">{{$errors->first('file')}}</div>
                            @endif
                        </div>
                        <div class="form-group">
                            <strong>File Creation Date:</strong>
                            <input type="date" name="file_creation_date" value="{{ old('file_creation_date') }}" class="form-control input-sm" placeholder="Drive Date" required>
                        </div>
                        <div class="form-group">
                                <label>Remarks:</label>
                                <textarea id="remarks" name="remarks" rows="4" cols="64" value="{{ old('remarks') }}" placeholder="Remarks" required class="form-control"></textarea>

                                @if ($errors->has('remarks'))
                                    <div class="alert alert-danger">{{$errors->first('remarks')}}</div>
                                @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-default">Upload</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <div id="displayTaskFileUpload" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Google Drive Uploaded files</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Filename</th>
                                    <th>File Creation Date</th>
                                    <th>URL</th>
                                    <th>Remarks</th>
                                    <th>Created by</th>
                                </tr>
                            </thead>
                            <tbody id="taskFileUploadedData">
                                
                            </tbody>
                        </table>
                    </div>
                 </div>


            </div>

        </div>
    </div>


    <!-- Modal -->
    <div id="upload-document-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Upload Document</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="upload-task-documents">
                    <div class="modal-body">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" id="hidden-identifier" name="devoops_task_id" value="">
                        <div class="row">
                            <div class="col-md-10 col-md-offset-1">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Subject</label>
                                            {{ html()->text("subject")->class("form-control")->placeholder("Enter subject") }}
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Description</label>
                                            {{ html()->textarea("description")->class("form-control")->placeholder("Enter Description") }}
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Documents</label>
                                            <input type="file" name="files[]" id="filecount" multiple="multiple">
                                        </div>
                                    </div>  
                                </div>  
                            </div>  
                        </div>  
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-default">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="blank-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>

    $(document).on("click", ".list-document-btn", function() {
        var id = $(this).data("id");
        $.ajax({
            method: "GET",
            url: "{{action([\App\Http\Controllers\DevOppsController::class, 'getDocument'])}}",
            data: {
                id: id
            },
            dataType: "json",
            success: function(response) {
                if (response.code == 200) {
                    $("#blank-modal").find(".modal-title").html("Document List");
                    $("#blank-modal").find(".modal-body").html(response.data);
                    $("#blank-modal").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            },
            error: function(error) {
                toastr["error"]('Unauthorized permission development-get-document', "Message")

            }
        });
    });

    $(document).on("submit", "#upload-task-documents", function(e) {
        e.preventDefault();
        var form = $(this);
        var postData = new FormData(form[0]);
        $.ajax({
            method: "post",
            url: "{{action([\App\Http\Controllers\DevOppsController::class, 'uploadDocument'])}}",
            data: postData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                if (response.code == 200) {
                    toastr["success"]("Status updated!", "Message")
                    $("#upload-document-modal").modal("hide");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

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
        oTable = $('#devoopslist_table').DataTable({
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
                "url": "{{ route('devoops.index') }}",
                data: function(d) {
                    d.category_name = $('#category').val();
                    d.sub_category_name = $('#sub_category').val();
                    d.remarks = $('input[name=remarks]').val();
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
                    data: 'devoops_category.name',
                    render: function(data, type, row, meta) {
                        var edit_data = actionEditButtonWithClass('edit-checklist', JSON.stringify(row));
                        /*var del_data = actionDeleteButton(row['devoops_category_id'], 'clsdelete');*/
                        return `<div class="flex justify-left items-center">`+data+`  ${edit_data} </div>`;
                    }
                },
                {
                    data: 'name',
                    render: function(data, type, row, meta) {
                        var sub_edit_data = actionEditButtonWithClass('sub_edit-checklist', JSON.stringify(row));
                        var sub_del_data = actionDeleteButton(row['id'], 'clssubdelete');
                        return `<div class="flex justify-left items-center">`+data+` ${sub_edit_data} ${sub_del_data} </div>`;
                    }
                },
                {
                    data: 'id',
                    name: 'magento_modules.id',
                    // visible:false,
                    render: function(data, type, row, meta) {
                        return `<div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                    <input style="margin-top: 0px;width:80% !important;" type="text" class="form-control " name="message" placeholder="Remarks" value="" id="remark_`+row['devoops_category_id']+`_`+row['id']+`" data-catid="`+row['devoops_category_id']+`" data-subcatid="`+row['id']+`">
                                    <div style="margin-top: 0px;" class="d-flex p-0">
                                        <button class="btn pr-0 btn-xs btn-image " onclick="saveRemarks(`+row['devoops_category_id']+`, `+row['id']+`)"><img src="/images/filled-sent.png"></button>
                                        <button type="button" data-catid="`+row['devoops_category_id']+`" data-subcatid="`+row['id']+`" class="btn btn-image remarks-history-show p-0 ml-2" title="Status Histories"><i class="fa fa-info-circle"></i></button>
                                    </div>
                                </div>`;
                    }
                },
                {
                    data: 'status.status_name',
                    render: function(data, type, row, meta) {                        

                        return `<div class="flex justify-left items-center">`+data+`<button type="button" class="btn pr-0 btn-xs btn-image status-update pull-right" data-toggle="modal" data-target="#status-update" data-id="`+row['id']+`" data-statusid="`+row['status_id']+`"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
                            <button type="button" data-id="`+row['id']+`" class="btn btn-image status-history-show p-0 mr-2 pull-right" title="Status Histories"><i class="fa fa-info-circle"></i></button>`;
                    }
                },
                {
                    data: 'id',
                    name: 'magento_modules.id',
                    // visible:false,
                    render: function(data, type, row, meta) {
                      return `<button title="create quick task" type="button" class="btn btn-image d-inline create-quick-task " data-id="`+row['id']+`"  data-category_title="Dev Oops Page" data-title="Dev Oops Page - `+row['id']+`"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            <button type="button" class="btn btn-image d-inline count-dev-customer-tasks" title="Show task history" data-id="`+row['id']+`" data-category="`+row['id']+`"><i class="fa fa-info-circle"></i></button>
                            <button class="btn btn-image d-inline upload-task-files-button" type="button" title="Uploaded Files" data-task_id="`+row['id']+`">
                                <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                            </button>
                            <button class="btn btn-image d-inline view-task-files-button" type="button" title="View Uploaded Files" data-task_id="`+row['id']+`">
                                <img src="/images/google-drive.png" style="cursor: nwse-resize; width: 10px;">
                            </button>
                            <a href="javascript:;" data-id="`+row['id']+`" class="btn btn-image d-inline upload-document-btn"><img width="15px" src="/images/attach.png" alt="" style="cursor: default;"></a>
                            <a href="javascript:;" data-id="`+row['id']+`" class="btn btn-image d-inline list-document-btn"><img width="15px" src="/images/archive.png" alt="" style="cursor: default;"></a>`;
                    }
                },
            ],
            rowCallback: function(row, data, index) {
                // Example: Change background color for rows where status is 'Completed'
                if(data.status){
                    $(row).css('background-color', data.status.status_color);
                }
            },
        });

        // added this function to update sub category on category change
        $('#category').on('change', function () {
            var category_id = $(this).val();

            if (Object.values(category_id).length) {
                $.ajax({
                    url: "{{ url('/api/devoops/subcategory/') }}/"+category_id,
                    type:"GET",
                    dataType:"json",
                    success:function(data) {
                        var d =$('#sub_category').empty();
                        $.each(data, function(key, value){
                            $('#sub_category').append('<option value="'+ value.id +'">' + value.name + '</option>');
                        });
                    },
                });
            } else {
                $.ajax({
                    url: "{{ url('/api/devoops/subcategory/') }}/",
                    type:"GET",
                    dataType:"json",
                    success:function(data) {
                        var d =$('#sub_category').empty();
                        $.each(data, function(key, value){
                            $('#sub_category').append('<option value="'+ value.id +'">' + value.name + '</option>');
                        });
                    },
                });
            }
        });
    });

    $(document).on('submit', '#task_form', function(e){
        e.preventDefault();

        var self = $(this);
        let formData = new FormData(document.getElementById("task_form"));
        var button = $(this).find('[type="submit"]');
        $.ajax({
            url: '{{ route("devoops.store") }}',
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
   
    $(document).on('click', '.edit-checklist', function() {
        var checklistData = $(this).data('row');
        
        $('#edit_task_form #checklist_id').val(checklistData.devoops_category_id);
        $('#edit_task_form #category_name').val(checklistData.devoops_category.name);
        $('#edit_task_form #sub_category_name').val(checklistData.name);
     
        $('#EditCheckList').modal('show');
    });
    
    $(document).on('submit', '#edit_task_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("edit_task_form"));
        var checklist_id = $('#edit_task_form #checklist_id').val();
        var button = $(this).find('[type="submit"]');
        
        $.ajax({
            url: '{{ route("devoops.update", '') }}/' + checklist_id,
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

    $(document).on('submit', '#sub_task_form', function(e){
        e.preventDefault();

        var self = $(this);
        let formData = new FormData(document.getElementById("sub_task_form"));
        var button = $(this).find('[type="submit"]');
        $.ajax({
            url: '{{ route("devoops.store") }}',
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
                $('#CreateSubCheckList').modal('hide');
                button.html('Add');
                button.prop('disabled', false);
                button.removeClass('disabled');
            },
            success: function(response) {
                $('#CreateSubCheckList').modal('hide');
                $('#CreateSubCheckList #sub_task_form').trigger('reset');
                $('#CreateSubCheckList #sub_task_form').find('.error-help-block').remove();
                $('#CreateSubCheckList #sub_task_form').find('.invalid-feedback').remove();
                $('#CreateSubCheckList #sub_task_form').find('.alert').remove();
                
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

    $(document).on('click', '.sub_edit-checklist', function(e){
        e.preventDefault();
        var checklistData = $(this).data('row');
            
                
        $('#EditSubCheckList #devoops_category_id option[value="' + checklistData.devoops_category_id + '"]').prop('selected', true);

        $('#EditSubCheckList #checklist_id').val(checklistData.id);
        $('#EditSubCheckList #sub_category_name').val(checklistData.name);
     
        $('#EditSubCheckList').modal('show');
    });
    
    $(document).on('submit', '#sub_edit_task_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("sub_edit_task_form"));
        var checklist_id = $('#sub_edit_task_form #checklist_id').val();
        var button = $(this).find('[type="submit"]');
        
        $.ajax({
            url: '{{ route("devoops.update", '') }}/' + checklist_id,
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
                
                $('#EditSubCheckList #sub_edit_task_form').find('.error-help-block').remove();
                $('#EditSubCheckList #sub_edit_task_form').find('.invalid-feedback').remove();
                $('#EditSubCheckList #sub_edit_task_form').find('.alert').remove();
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
        var url = `{{ url('/') }}/devoopslist/` + id;
      
        tableDeleteRow(url, oTable);
    });

    $(document).on('click', '.clssubdelete', function() {
        var id = $(this).attr('data-id');
        var e = $(this).parent().parent();
        var url = `{{ url('/') }}/devoopssublist/` + id;
      
        tableDeleteRow(url, oTable);
    });
    //deletesubject-edit

    function saveRemarks(main_category_id, sub_category_id){

        var remarks = $("#remark_"+main_category_id+"_"+sub_category_id).val();

        if(remarks==''){
            alert('Please enter remarks.');
            return false;
        }

        $.ajax({
            url: "{{route('devoopssublist.saveremarks')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'main_category_id' :main_category_id,
                'sub_category_id' :sub_category_id,
                'remarks' :remarks,
            },
            beforeSend: function() {
                $(this).text('Loading...');
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();

                
            }
        }).fail(function(response) {
            $("#loading-image").hide();
            toastr['error'](response.responseJSON.message);
        });
    }

    $(document).on('click', '.remarks-history-show', function() {
        var main_category_id = $(this).attr('data-catid');
        var sub_category_id = $(this).attr('data-subcatid');
        $.ajax({
            url: "{{route('devoopssublist.getremarks')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'main_category_id' :main_category_id,
                'sub_category_id' :sub_category_id,
            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${(v.remarks != null) ? v.remarks : ' - ' } </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#devoops-remarks-histories-list").find(".devoops-remarks-histories-list-view").html(html);
                    $("#devoops-remarks-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    $(document).on('click', '.create-quick-task', function() {
        var $this = $(this);
        site = $(this).data("id");
        title = $(this).data("title");
        cat_title = $(this).data("category_title");
        development = $(this).data("development");
        if (!title || title == '') {
            toastr["error"]("Please add title first");
            return;
        }

        $("#create-quick-task").modal("show");

        var selValue = $(".save-item-select").val();
        if (selValue != "") {
            $("#create-quick-task").find(".assign-to option[value=" + selValue + "]").attr('selected',
                'selected')
            $('.assign-to.select2').select2({
                width: "100%"
            });
        }

        $("#hidden-task-subject").val(title);
        $(".text-task-development").val(development);
        $('#site_id').val(site);
    });

    $(document).on("click", ".create-task", function(e) {

        $("#loading-image").show();

        e.preventDefault();
        var form = $(this).closest("form");
        $.ajax({
            url: form.attr("action"),
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: form.serialize(),
            beforeSend: function() {
                $(this).text('Loading...');
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    form[0].reset();
                    toastr['success'](response.message);
                    $("#create-quick-task").modal("hide");
                } else {
                    toastr['error'](response.message);
                }
            }
        }).fail(function(response) {
            toastr['error'](response.responseJSON.message);
        });
    });

    $(document).on("click", ".count-dev-customer-tasks", function() {

        $("#loading-image").show();

        var $this = $(this);
        // var user_id = $(this).closest("tr").find(".ucfuid").val();
        var site_id = $(this).data("id");
        var category_id = $(this).data("category");
        $("#site-development-category-id").val(category_id);
        $.ajax({
            type: 'get',
            url: 'devoops/countdevtask/' + site_id,
            dataType: "json",
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(data) {
                $("#dev_task_statistics").modal("show");
                var table = `<div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th width="4%">Tsk Typ</th>
                            <th width="4%">Tsk Id</th>
                            <th width="7%">Asg to</th>
                            <th width="12%">Desc</th>
                            <th width="12%">Sts</th>
                            <th width="33%">Communicate</th>
                            <th width="10%">Action</th>
                        </tr>`;
                for (var i = 0; i < data.taskStatistics.length; i++) {
                    var str = data.taskStatistics[i].subject;
                    var res = str.substr(0, 100);
                    var status = data.taskStatistics[i].status;
                    if (typeof status == 'undefined' || typeof status == '' || typeof status ==
                        '0') {
                        status = 'In progress'
                    };
                    table = table + '<tr><td>' + data.taskStatistics[i].task_type + '</td><td>#' +
                        data.taskStatistics[i].id +
                        '</td><td class="expand-row-msg" data-name="asgTo" data-id="' + data
                        .taskStatistics[i].id + '"><span class="show-short-asgTo-' + data
                        .taskStatistics[i].id + '">' + data.taskStatistics[i].assigned_to_name
                        .replace(/(.{6})..+/, "$1..") +
                        '</span><span style="word-break:break-all;" class="show-full-asgTo-' + data
                        .taskStatistics[i].id + ' hidden">' + data.taskStatistics[i]
                        .assigned_to_name +
                        '</span></td><td class="expand-row-msg" data-name="res" data-id="' + data
                        .taskStatistics[i].id + '"><span class="show-short-res-' + data
                        .taskStatistics[i].id + '">' + res.replace(/(.{7})..+/, "$1..") +
                        '</span><span style="word-break:break-all;" class="show-full-res-' + data
                        .taskStatistics[i].id + ' hidden">' + res + '</span></td><td>' + status +
                        '</td><td><div class="col-md-10 pl-0 pr-1"><textarea rows="1" style="width: 100%; float: left;" class="form-control quick-message-field input-sm" name="message" placeholder="Message"></textarea></div><div class="p-0"><button class="btn btn-sm btn-xs send-message" title="Send message" data-taskid="' +
                        data.taskStatistics[i].id +
                        '"><i class="fa fa-paper-plane"></i></button></div></td><td><button type="button" class="btn btn-xs load-communication-modal load-body-class" data-object="' +
                        data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i]
                        .id +
                        '" title="Load messages" data-dismiss="modal"><i class="fa fa-comments"></i></button>';
                    table = table + '<a href="javascript:void(0);" data-task-type="' + data
                        .taskStatistics[i].task_type + '" data-id="' + data.taskStatistics[i].id +
                        '" class="delete-dev-task-btn btn btn-xs"><i class="fa fa-trash"></i></a>';
                    table = table +
                        '<button type="button" class="btn btn-xs  preview-img pd-5" data-object="' +
                        data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i]
                        .id + '" data-dismiss="modal"><i class="fa fa-list"></i></button></td>';
                    table = table + '</tr>';
                }
                table = table + '</table></div>';
                $("#loading-image").hide();
                $(".modal").css("overflow-x", "hidden");
                $(".modal").css("overflow-y", "auto");
                $("#dev_task_statistics_content").html(table);
            },
            error: function(error) {
                console.log(error);
                $("#loading-image").hide();
            }
        });
    

    });

    $(document).on('click', '.send-message', function() {

        $("#loading-image").show();

        var thiss = $(this);
        var data = new FormData();
        var task_id = $(this).data('taskid');
        var message = $(this).closest('tr').find('.quick-message-field').val();
        var mesArr = $(this).closest('tr').find('.quick-message-field');
        $.each(mesArr, function(index, value) {
            if ($(value).val()) {
                message = $(value).val();
            }
        });

        data.append("task_id", task_id);
        data.append("message", message);
        data.append("status", 1);

        if (message.length > 0) {
            if (!$(thiss).is(':disabled')) {
                $.ajax({
                    url: '/whatsapp/sendMessage/task',
                    type: 'POST',
                    "dataType": 'json', // what to expect back from the PHP script, if anything
                    "cache": false,
                    "contentType": false,
                    "processData": false,
                    "data": data,
                    beforeSend: function() {
                        $(thiss).attr('disabled', true);
                        $("#loading-image").show();
                    }
                }).done(function(response) {
                    $("#loading-image").hide();
                    thiss.closest('tr').find('.quick-message-field').val('');

                    toastr["success"]("Message successfully send!", "Message")
                    // $.post( "/whatsapp/approve/customer", { messageId: response.message.id })
                    //   .done(function( data ) {
                    //
                    //   }).fail(function(response) {
                    //     console.log(response);
                    //     alert(response.responseJSON.message);
                    //   });

                    $(thiss).attr('disabled', false);
                }).fail(function(errObj) {
                    $(thiss).attr('disabled', false);

                    alert("Could not send message");
                    console.log(errObj);
                });
            }
        } else {
            alert('Please enter a message first');
        }
    });

    $(document).on("click", ".delete-dev-task-btn", function() {

        $("#loading-image").show();

        var x = window.confirm("Are you sure you want to delete this ?");
        if (!x) {
            return;
        }
        var $this = $(this);
        var taskId = $this.data("id");
        var tasktype = $this.data("task-type");
        if (taskId > 0) {
            $.ajax({
                beforeSend: function() {
                    $("#loading-image").show();
                },
                type: 'get',
                url: "/site-development/deletedevtask",
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: taskId,
                    tasktype: tasktype
                },
                dataType: "json"
            }).done(function(response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    $this.closest("tr").remove();
                }
            }).fail(function(response) {
                $("#loading-image").hide();
                alert('Could not update!!');
            });
        }

    });

    $(document).on('click', '.expand-row-msg', function() {
        var name = $(this).data('name');
        var id = $(this).data('id');
        console.log(name);
        var full = '.expand-row-msg .show-short-' + name + '-' + id;
        var mini = '.expand-row-msg .show-full-' + name + '-' + id;
        $(full).toggleClass('hidden');
        $(mini).toggleClass('hidden');
    });

    $(document).on('click', '.preview-img', function(e) {
        e.preventDefault();
        id = $(this).data('id');
        if (!id) {
            alert("No data found");
            return;
        }
        $.ajax({
            url: "/task/preview-img-task/" + id,
            type: 'GET',
            success: function(response) {
                $("#preview-task-image").modal("show");
                $(".task-image-list-view").html(response);
                initialize_select2()
            },
            error: function() {}
        });
    });

    $(document).on("click", ".send-to-sop-page", function() {

        $("#loading-image").show();

        var id = $(this).data("id");
        var task_id = $(this).data("media-id");

        $.ajax({
            url: '/task/send-sop',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType: "json",
            data: {
                id: id,
                task_id: task_id
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                if (response.success) {
                    toastr["success"](response.message);
                } else {
                    toastr["error"](response.message);
                }

            },
            error: function(error) {
                toastr["error"];
            }

        });
    });

    $(document).on('click', '.previewDoc', function() {
        $('#previewDocSource').attr('src', '');
        var docUrl = $(this).data('docurl');
        var type = $(this).data('type');
        var type = jQuery.trim(type);
        if (type == "image") {
            $('#previewDocSource').attr('src', docUrl);
        } else {
            $('#previewDocSource').attr('src', "https://docs.google.com/gview?url=" + docUrl +
                "&embedded=true");
        }
        $('#previewDoc').modal('show');
    });

    $(document).on("click", ".status-save-btn", function(e) {
        e.preventDefault();
        var $this = $(this);
        $.ajax({
            url: "{{route('devoops.status.create')}}",
            type: "post",
            data: $('#status-create-form').serialize()
        }).done(function(response) {
            if (response.code = '200') {
                $('#loading-image').hide();
                toastr['success']('Status  Created successfully!!!', 'success');
                location.reload();
            } else {
                toastr['error'](response.message, 'error');
            }
        }).fail(function(errObj) {
            $('#loading-image').hide();
            toastr['error'](errObj.message, 'error');
        });
    });

    $(document).on("click", ".status-update", function(e) {
        e.preventDefault();
        var $this = $(this);
        var id = $(this).attr('data-id');
        var statusid = $(this).attr('data-statusid');

        $('#status-update-form #status_name option[value="'+statusid+'"]').attr("selected", "selected");

        $("#status-update #id").val(id);
    });

    $(document).on("click", ".status-update-btn", function(e) {
        e.preventDefault();
        var $this = $(this);
        $.ajax({
            url: "{{route('devoops.status.update')}}",
            type: "post",
            data: $('#status-update-form').serialize()
        }).done(function(response) {
            if (response.code = '200') {
                $('#loading-image').hide();
                toastr['success']('Status  Created successfully!!!', 'success');
                location.reload();
            } else {
                toastr['error'](response.message, 'error');
            }
        }).fail(function(errObj) {
            $('#loading-image').hide();
            toastr['error'](errObj.message, 'error');
        });
    });

    $(document).on('click', '.status-history-show', function() {
        var id = $(this).attr('data-id');
        $.ajax({
            url: "{{route('devoopssublist.getstatus')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'id' :id,
            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                            <td> ${k + 1} </td>
                            <td> ${(v.old_value != null) ? v.old_value.status_name : ' - ' } </td>
                            <td> ${(v.new_value != null) ? v.new_value.status_name : ' - ' } </td>
                            <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                            <td> ${v.created_at} </td>
                        </tr>`;
                    });
                    $("#devoops-status-histories-list").find(".devoops-status-histories-list-view").html(html);
                    $("#devoops-status-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    $(document).on("click", ".upload-task-files-button", function (e) {
        e.preventDefault();
        let task_id = $(this).data("task_id");
        $("#uploadeTaskFileModal #upload_task_id").val(task_id || 0);
        $("#uploadeTaskFileModal").modal("show")
    });

    $(document).on("click", ".view-task-files-button", function (e) {
        e.preventDefault();
        let dev_oops_id = $(this).data("task_id");
        $.ajax({
            type: "get",
            url: "{{route('devoopssublist.files.record')}}",
            data: {
                dev_oops_id
            },
            success: function (response) {
                if(typeof response.data != 'undefined') {
                    $("#taskFileUploadedData").html(response.data);
                } else {
                    // display unauthorized permission message
                    $("#taskFileUploadedData").html(response);
                }
                
                $("#displayTaskFileUpload").modal("show")
            },
            error: function (response) {
                toastr['error']("Something went wrong!");
            }
        });
    });

    $(document).on("click", ".upload-document-btn", function() {
        var id = $(this).data("id");
        $("#upload-document-modal").find("#hidden-identifier").val(id);
        $("#upload-document-modal").modal("show");
    });

    $("#category").select2({
        tags: true,
        placeholder: "Select a category"
    });

    $("#sub_category").select2({
        tags: true,
        placeholder: "Select a sub category"
    });

</script>
@endsection