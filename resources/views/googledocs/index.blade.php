@extends('layouts.app')
@section('favicon' , '')

@section('title', 'Google Docs')

@section('styles')
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
        .select2-selection__rendered{overflow: inherit !important;}
    </style>

@endsection

@section('content')
    <div class="col-md-12">
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Google Docs</h2>
            <div class="">
                <div class="form-group">
                    <div class="row">
                        <div class="col-lg-12 pd-sm">
                            <form class="form-inline message-search-handler" id="searchGoogleDocForm" method="get">
                                <div class="col-lg-3 pd-sm">
                                    <b>Select File Name: </b>
                                    {{ html()->multiselect("name[]", \App\GoogleDoc::pluck('name', 'id')->toArray(), request('name'))->class("form-control globalSelect2") }}
                                </div>
                                <div class="col-lg-3 pd-sm">
                                    <b>Select Tasks: </b>
                                    <input class="form-control" type="text" id="tag-tasks" name="tasks" placeholder="Enter Task Id" style="width: 100%;" value="{{request()->get('tasks')}}">
                                </div>
                                <div class="col-lg-3 pd-sm">
                                    <b>Select Tasks Type: </b>
                                    <select class="form-control" id="task_type" name="task_type" placeholder="Select Task Type">
                                        <option value="">Select Type</option>
                                        <option value="App\Task" @if(!empty($request->input('task_type')) && ($request->input('task_type')=='App\Task')) selected @endif>TASK</option>
                                        <option value="App\DeveloperTask" @if(!empty($request->input('task_type')) && ($request->input('task_type')=='App\DeveloperTask')) selected @endif>DEVTASK</option>
                                    </select>
                                </div>
                                <div class="col-lg-3 pd-sm">
                                    <b>Select Category: </b>
                                    <select class="form-control globalSelect2" multiple="true" id="googleDocCategoryFilter" name="googleDocCategory[]" placeholder="Select Category">
                                        @foreach ($googleDocCategory as $key => $c)
                                            <option value="{{ $key }}" @if(in_array($key, $request->input('googleDocCategory', []))) selected @endif>{{ $c }}</option>
                                        @endforeach
                                    </select>
                                </div>
    				            @if(Auth::user()->isAdmin())
                                    <div class="col-lg-3 pd-sm">
                                        <b>Select User: </b>
                                        <select class="form-control globalSelect2" multiple="true" id="user_gmail" name="user_gmail[]" placeholder="Select User">
                                            @foreach($users as $val)
                                            <option value="{{ $val->gmail }}" @if(in_array($val->gmail, $request->input('user_gmail', []))) selected @endif>{{ $val->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
    				            @endif
                                <div class="col-lg-3 pd-sm">
                                    <b>Enter Search Url: </b>
                                    <input name="docid" list="docid-lists" type="text" class="form-control" placeholder="Search Url" value="{{request()->get('docid')}}" style="width: 100%;"/>
                                    <datalist id="docid-lists">
                                        @foreach ($data as $key => $val )
                                            <option value="{{$val->docId}}"></option>
                                        @endforeach
                                    </datalist>
                                </div>
                                <div class="col-lg-3 pd-sm">
                                    <b>Enter keyword for search: </b>
                                    {{ html()->text("search", request()->get("search", ""))->class("form-control")->placeholder("Enter keyword for search") }}
                                </div>
                                <div class="col-lg-1 pd-sm">
                                    <div class="form-group">
                                        <button type="submit" style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
                                            <img src="/images/search.png" style="cursor: default;">
                                        </button>
                                        <a href="/google-docs" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
	    @if(Auth::user()->isAdmin())
            <div class="pull-right">
                <button type="button" class="btn btn-secondary open-google-documents">
                    Open Documents
                </button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#updatemultipleGoogleDocPermissionModal">
                    Add Permission
                  </button>   
                  <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#GoogleDocRemovePermissionModal">
                    Remove Permission for mulitiple users
                  </button>   
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#AddGoogleDocCategoryModal">
                    Create Category
                </button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#RemoveGoogleDocPermissionModal">
                    Remove Permission
                </button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#createGoogleDocModal">
                    + Create Doc
                </button>
            </div>
	    @endif
        </div>
    </div>

    @include('partials.flash_messages')
    <div id="googleDocTableWrapper">
        @include('googledocs.partials.list-files')
    </div>

    <div id="RemoveGoogleDocPermissionModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Remove Google Doc Permission</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{ route('google-docs.permission.remove') }}" id="RemoveGoogleDocPermissionForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group custom-select2">
                            <label>Select Remove Permission for Users
                            </label>
                            <select class="w-100 js-example-basic-multiple js-states"
                                    id="remove_permission_write"  name="remove_permission">
                                @foreach(App\User::whereNotNull('gmail')->get() as $val)
                                    <option value="{{$val->gmail}}" class="form-control">{{$val->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">remove</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <div id="AddGoogleDocCategoryModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create new category</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{ route('google-docs.category.create') }}" id="AddGoogleDocCategoryForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group custom-select2">
                            <label>Category</label>
                            <input type="text" name="name" class="form-control">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Create</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <div id="viewGoogleDocPermissionModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">View Google Doc Permission</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                        <div class="modal-body">
                            <input type="hidden" name="view_file_id" id = "view_file_id">
                            <input type="hidden" name="view_id" id = "view_id">
                            <div class="form-group custom-select2">
                                <label>Read Permission for Users</label>
                                <div class="read_permission_users">

                                </div>
                            </div>
                            <div class="form-group custom-select2">
                                <label>Write Permission for Users</label>
                                <div class="write_permission_users">

                                </div>
                            </div>
                        </div>
                </div>

            </div>
        </div>


    @include('googledocs.partials.create-doc')
    @include('googledocs.partials.update-doc-permissions')
    @include('googledocs.partials.update-doc')
    </div>

    <style>
        .select2-search--inline {
            display: contents; 
        }

        .select2-search__field:placeholder-shown {
            width: 100% !important; 
        }
        .multi-select-box .select2{
            width: 200px!important;
        }
        ul.select2-selection__rendered{
            display: block!important;
        }
        .googleDocCategory-container .select2-container{
            width: 200px!important
        }
    </style>
@endsection
@section('scripts')
<script type="text/javascript">
    $("#remove_permission_write").select2();
    $("#googleDocCategoryFilter").select2({
        multiple: true,
        placeholder: "Select Category"
    });
    $("#googleDocCategoryFilter").trigger('change');
    

    $(document).on('click', '.permissionupdate', function (e) {

        $("#updateGoogleDocPermissionModal #id_label_permission_read").val("").trigger('change');
        $("#updateGoogleDocPermissionModal #id_label_permission_write").val("").trigger('change');
        
        let data_read = $(this).data('readpermission');
        let data_write = $(this).data('writepermission');
        var file_id = $(this).data('docid');
        var id = $(this).data('id');
        var permission_read = data_read.split(',');
        var permission_write = data_write.split(',');
        if(permission_read)
        {
            $("#updateGoogleDocPermissionModal #id_label_permission_read").val(permission_read).trigger('change');
        }
        if(permission_write)
        {
            $("#updateGoogleDocPermissionModal #id_label_permission_write").val(permission_write).trigger('change');
        }
        
        $('#file_id').val(file_id);
        $('#id').val(id);
        
    });

    $(document).on('click', '.permissionview', function (e) {
        var id = $(this).data('id');

        $.ajax({
            type: 'POST',
            url: '{{ route('google-docs.permission.view') }}',
            data: {id: id},
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
        }).done(response => {
            if(response.code == 200) {
                var read = response.read.split(',')
                var write = response.write.split(',');
                var data_read = [];
                var data_write = [];

                $.each(read, function (index, value) {
                    data_read += '<div class="py-2 px-4 m-1 d-inline-block" style="border: 1px solid #000; border-radius:4px; cursor: text;">' + value + '</div>';
                });

                $.each(write, function (index, value) {
                    data_write += '<div class="py-2 px-4 m-1 d-inline-block" style="border: 1px solid #000; border-radius:4px; cursor: text;">' + value + '</div>';
                });

                $(".read_permission_users").html(data_read);
                $(".write_permission_users").html(data_write);

                $("#viewGoogleDocPermissionModal").modal("show");
            }else{
                toastr['error']('Oops, something went wrong', 'error');
            }
        });
    });

    $(document).on('click', '.google-doc-update', function (e) {
		var action = $(this).data('action');
        $.ajax({
            type: "GET",
            url: action,
            data: {_token: "{{ csrf_token() }}"},
            dataType: "json",
            success: function (response) {
                // $('#updateGoogleDocModal input[name=doc_category]').val(response.data.category);
                $("#editGoogleDocCategory").val(response.data.category);
                $('#updateGoogleDocModal input[name=docId]').val(response.data.docId);
                $('#updateGoogleDocModal [name=type]').val(response.data.type);
                $('#updateGoogleDocModal input[name=name]').val(response.data.name);
                $('#updateGoogleDocModal input[name=id]').val(response.data.id);
            }
        });
	});

    $(document).on("change", ".update-category", function () {
        var doc_id = $(this).data("docs_id")
        var category_id = $(this).val()

        if(doc_id != "" && doc_id != null) {
            $.ajax({
                type: "post",
                url: "{{ route('google-docs.category.update') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    category_id, doc_id
                },
                success: function (response) {
                    if(response.status == true) {
                        toastr['success'](response.message, 'Note');
                    } else {
                        toastr['error'](response.message, 'Error');
                    }
                }, 
                error: function(error) {
                    toastr['error']("Something went wrong.", 'Error');
                }
            });
        }
        
    });

    $(document).ready(function() {
        $('#updatemultipleGoogleDocPermissionForm').on('submit', async function(e) {
            e.preventDefault();

            var selectedCheckboxes = [];
            var fileIDs = [];

            if ($('.select_all_doc').prop('checked')) {
                    $('.google_doc_check').each(function() {
                        var fileID = $(this).data('id');
                        var checkboxValue = $(this).val();

                        fileIDs.push(fileID);
                        selectedCheckboxes.push(checkboxValue);
                    });
                } else {
                    $('input[name="google_doc_check"]:checked').each(function() {
                        var fileID = $(this).data('id');
                        var checkboxValue = $(this).val();

                        fileIDs.push(fileID);
                        selectedCheckboxes.push(checkboxValue);
                    });
                }

                if (selectedCheckboxes.length === 0) {
                    alert('Please select at least one checkbox.');
                    return;
                }

            $('#add_doc_ids').val(selectedCheckboxes.join(','));

            // $(this).unbind('submit').submit()
            let request = {
                url: `${e.target.action}?ajax=1`,
                type:  e.target.method.toUpperCase(),
                data: $(this).serialize(),
                modalSelector: '#updatemultipleGoogleDocPermissionModal',
                loader: $("#loading-image"),
            }

            await sendAjaxRequest(request); 
        });

        $('#GoogleDocRemovePermissionForm').on('submit', async function(e) {
            e.preventDefault(); 
            var selectedCheckboxes = [];
            var fileIDs = [];

            if ($('.select_all_doc').prop('checked')) {
                    $('.google_doc_check').each(function() {
                        var fileID = $(this).data('id');
                        var checkboxValue = $(this).val();

                        fileIDs.push(fileID);
                        selectedCheckboxes.push(checkboxValue);
                    });
            } else {
                $('input[name="google_doc_check"]:checked').each(function() {
                    var fileID = $(this).data('id');
                    var checkboxValue = $(this).val();

                    fileIDs.push(fileID);
                    selectedCheckboxes.push(checkboxValue);
                });
            }

            if (selectedCheckboxes.length === 0) {
                alert('Please select at least one checkbox.');
                return;
            }

            $('#remove_doc_ids').val(selectedCheckboxes.join(','));

            // $(this).unbind('submit').submit()
            let request = {
                url: `${e.target.action}?ajax=1`,
                type:  e.target.method.toUpperCase(),
                data: $(this).serialize(),
                modalSelector: '#GoogleDocRemovePermissionModal',
                loader: $("#loading-image"),
            }

            await sendAjaxRequest(request); 

        });

        $('.select_all_doc').on('change', function() {
            var isChecked = $(this).prop('checked');
            $('.google_doc_check').prop('checked', isChecked);
        });
    });

    $(document).ready(function($) {
        $("#tag-tasks").autocomplete({
            source: function(request, response) {
                // Send an AJAX request to the server-side script
                $.ajax({
                    url: '{{ route('google-docs.tasks') }}',
                    dataType: 'json',
                    data: {
                        term: request.term // Pass user input as 'term' parameter
                    },
                    success: function(data) {
                        response(data); // The server returns filtered suggestions as JSON
                    }
                });
            },
            minLength: 1, // Minimum characters before showing suggestions
            select: function(event, ui) {
                // Handle the selection if needed
            }
        });
    })

    $('#AddGoogleDocCategoryForm').on('submit', async function(e) {
        e.preventDefault();
        
        let request = {
            url: `${e.target.action}?ajax=1`,
            type:  e.target.method.toUpperCase(),
            data: $(this).serialize(),
            modalSelector: '#AddGoogleDocCategoryModal',
            loader: $("#loading-image"),
        }

        await sendAjaxRequest(request);
        reloadTable(1);
    });

    $('#RemoveGoogleDocPermissionForm').on('submit', async function(e) {
        e.preventDefault();
        
        let request = {
            url: `${e.target.action}?ajax=1`,
            type:  e.target.method.toUpperCase(),
            data: $(this).serialize(),
            modalSelector: '#RemoveGoogleDocPermissionModal',
            loader: $("#loading-image"),
        }

        await sendAjaxRequest(request);
    });

    $('#createGoogleDocForm').on('submit', async function(e) {
        e.preventDefault();
        
        let request = {
            url: `${e.target.action}?ajax=1`,
            type:  e.target.method.toUpperCase(),
            data: $(this).serialize(),
            modalSelector: '#createGoogleDocModal',
            loader: $("#loading-image"),
        }

        await sendAjaxRequest(request);
        reloadTable(1);
    });

    $('#searchGoogleDocForm').on('submit', function(e) {
        e.preventDefault();
        
        let filters = $(this).serialize();
        reloadTable(1,filters);
    });
    
    $(document).on('click','#googleDocTableWrapper .pagination a', function(e) {
        e.preventDefault();
        reloadTable($(this).attr('href').split('page=')[1]);
    });

    function sendAjaxRequest({url, type, data, loader, modalSelector}) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url,
                type,
                data,
                dataType: 'json',
                beforeSend: function() {
                    if (loader) {
                        loader.show();
                    }
                },
                success: function (response) {

                    if (loader) {
                        loader.hide();
                    }

                    switch(response.status) {
                        case 'success':
                            if (response.msg) {
                                toastr.success(response.msg);
                            }
                            if (modalSelector) {
                                $(modalSelector).modal('hide');
                            }
                            resolve(response);
                            break;
                        case 'error':
                            if (response.msg) {
                                toastr.error(response.msg);
                            }
                            reject(response);
                            break;
                    }
                },
                error: function (error) {
                    if (loader) {
                        loader.hide();
                    }
                    toastr.error(error);
                    reject(response);
                }
            });
        });
    }

    async function reloadTable(page, data = false){
        let request = {
            url: "{{ route('google-docs.index') }}",
            type:  "GET",
            loader: $("#loading-image"),
        }

        data = 'page=' + page + (data ? '&' + data : '');
        Object.assign(request, {data});

        let responseData = await sendAjaxRequest(request);

        $('#googleDocTableWrapper').html('')
        $('#googleDocTableWrapper').html(responseData.data)
    }
    
</script>

<script type="text/javascript">
    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });

    $(document).on('click', '.open-google-documents', function () {
        var selectedCheckboxes = [];
        
        $('input[name="google_doc_check"]:checked').each(function() {
            var checkboxValue = $(this).val();

            var href = $('.google_document_'+checkboxValue).attr('href');
            window.open(href, '_blank');      

            selectedCheckboxes.push(checkboxValue);
        });

        if (selectedCheckboxes.length === 0) {
            alert('Please select at least one checkbox.');
            return;
        }
    });

    function Showactionbtn(id){
        $(".action-btn-tr-"+id).toggleClass('d-none')
        $("#asset_user_name").select2('destroy');
    }
</script>
@endsection
