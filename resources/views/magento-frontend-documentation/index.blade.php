@extends('layouts.app')



@section('title', 'magento-frontent-documentation')

@section('styles')
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <style>
        .general-remarks {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .gap-5 {
            gap: 5px;
        }

        .module-text {
            width: 80px;
        }

        .users {
            display: none;
        }

        table.dataTable thead th {
            padding: 5px 7px !important;
            white-space: nowrap;
        }

        table.dataTable tbody th,
        table.dataTable tbody td {
            padding: 5px 5px !important;
        }

        .copy_remark {
            cursor: pointer;
        }

        .multiselect-native-select .btn-group {
            width: 100%;
            margin: 0px;
            padding: 0;
        }

        .multiselect-native-select .checkbox input {
            margin-top: -5px !important;
        }

        .multiselect-native-select .btn-group button.multiselect {
            width: 100%;

        }
        /* CSS for positioning the eye and copy icons in the corner */
        .file-info-container {
            /* position: relative; */
        }

        /* .action-buttons-container {
            position: absolute;
            top: 0;
            right: 0;
        } */

        .flex-center-block{
            align-items: center;
            gap: 10px;
            justify-content: space-between;
        }
    </style>

    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.jqueryui.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
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

        @media(max-width:1200px) {
            .action_button {
                display: block;
                width: 100%;
            }
        }

        .table select.form-control {
            width: 130px !important;
            padding: 5px;
        }

    </style>
@endsection


@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;z-index: 9999;" />
    </div>

    <div class="row ">
        <div class="col-lg-12 ">
            <h2 class="page-heading">
                Magento FrontEnd Documentation<span id="total-count"></span>
            </h2>
            <form method="POST" action="#" id="dateform">

                <div class="row m-4">
                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            <h5>Search Category</h5>
                            <select class="form-control globalSelect2 category_name" multiple="true" id="category-select" name="magento_docs_category_id[]">
                                @php
                                 $storecategories = \App\SiteDevelopmentCategory::select('title', 'id')->wherenotNull('title')->get();
                                 @endphp
     
                                 <option value="">Select Category</option>
                                 @foreach ($storecategories as $storecategory)
                                     <option value="{{ $storecategory->id }}">{{ $storecategory->title }}</option>
                                 @endforeach
                             </select>
                        </div>
                    </div>

                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            <h5>Search locations</h5>
                            <select class="form-control  globalSelect2 location_name" multiple="true" id="location_select" name="location_name[]">
                                @php
                                 $locations = \App\Models\MagentoFrontendDocumentation::select('location', 'id')->get();
                                 @endphp
     
                                 <option value="">Select locations</option>
                                 @foreach ($locations as $location)
                                     <option value="{{ $location->location }}">{{ $location->location }}</option>
                                 @endforeach
                             </select>
                        </div>
                    </div>

                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            <h5>Search Admin config</h5>
                            <input name="search_admin_config" type="text" class="form-control search_admin_config" value="{{ request('status') }}"
                                    placeholder="search Admin Config" id="search_admin_config">
                        </div>
                    </div>

                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            <h5>Search Frontend config</h5>
                            <input name="search_frontend_confid" type="text" class="form-control search_frontend_config" value="{{ request('status') }}"
                            placeholder="search Frontend Config" id="search_frontend_config">                     
                          </div>
                    </div>

                    <div class="col-xs-2 col-sm-1 pt-2 "><br>
                        <div class="d-flex">
                            <div class="form-group pull-left ">
                                <button type="submit" class="btn btn-image search">
                                    <img src="/images/search.png" alt="Search" style="cursor: inherit;">
                                </button>
                            </div>
                            <div class="form-group pull-left ">
                                <a href="{{route('magento_frontend_listing')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                            </div>
                        </div>
                    </div>

                    <div class="pull-right pr-5"><br>
                        <h5></h5>
                        <button type="button" class="btn btn-secondary" data-toggle="modal"
                            data-target="#create-magento-frontend-docs"> Create Magento FrontEnd Documentation </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive mt-3 pr-2 pl-2">

        <div class="erp_table_data">
            <table class="table table-bordered" id="magento_frontend_docs_table">
                <thead>
                    <tr>
                        <th> Id </th>
                        <th> Category </th>
                        <th> Parent folder </th>
                        <th> child folder </th>
                        <th> Remark </th>
                        <th> Location </th>
                        <th> Admin Configuration </th>
                        <th> Frontend configuration </th>    
                        <th width="10%"> File Name </th>   
                        <th> Updated by </th>   
                        <th> Created At </th>   
                        <th> Action </th>              
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

    </div>


    <div id="moduleEditModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <form id="magento_module_edit_form" method="POST">
                    @csrf
                    {{ html()->hidden('id')->id('id') }}
                    <div class="modal-header">
                        <h4 class="modal-title">Update Magneto Frontend Documentation</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        @include('magento-frontend-documentation.partials.edit-form')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="parentImageAddModal" tabindex="-1" role="dialog" aria-labelledby="parentImageAddModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <form id="magento_frontend_parent_image_form" class="form mb-15" enctype="multipart/form-data">
            @csrf
            {{ html()->hidden('magento_frontend_id')->id('magento_frontend_id') }}  
            <div class="modal-header">
              <h5 class="modal-title" id="parentImageModalLabel">Parent Image Create</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="row ml-2 mr-2">
                    <div class="col-xs-6 col-sm-6">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input type="file" name="parent_folder_image[]" id="parent_folder_image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Add</button>
            </div>
        </form>
          </div>
        </div>
      </div>

      <div id="magnetobackendFileUpload" class="modal fade" role="dialog">
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
                                </tr>
                            </thead>
                            <tbody id="magnetoFileUpload">

                            </tbody>
                        </table>
                    </div>
                 </div>


            </div>

        </div>
    </div>

    @include('magento-frontend-documentation.upload-file-listing')
    @include('magento-frontend-documentation.partials.magento-fronent-create')
    @include('magento-frontend-documentation.remark_list')
    @include('magento-frontend-documentation.location-list')
    @include('magento-frontend-documentation.front-list-history')
    @include('magento-frontend-documentation.admin-list-history')
    @include('magento-frontend-documentation.magento-frontend-history')
    @include('magento-frontend-documentation.partials.magento-frontend-category-history')
    @include('magento-frontend-documentation.partials.magento-frontend-parent-folder-history')
    @include('magento-frontend-documentation.partials.child-folder-image')
    @include('magento-frontend-documentation.partials.magento-frontend-child-folder-history')



    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js">
    </script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-multiselect.min.js') }} "></script>
    <script>

        $("#id_label_file_permission_read").select2();
        $("#id_label_file_permission_write").select2();
        // START Print Table Using datatable
        var magentofrontendTable;
        $(document).ready(function() {
            magentofrontendTable = $('#magento_frontend_docs_table').DataTable({
                pageLength: 25,
                responsive: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                searching: false,
                sScrollX: true,
                order: [
                    [0, 'desc']
                ],
                targets: 'no-sort',
                bSort: false,

                oLanguage: {
                    sLengthMenu: "Show _MENU_",
                },
                createdRow: function(row, data, dataIndex) {
                    // Set the data-status attribute, and add a class
                    $(row).attr('role', 'row');
                    $(row).find("td").last().addClass('text-danger');
                    if (data["row_bg_colour"] != "") {
                        $(row).css("background-color", data["row_bg_colour"]);
                    }
                },
                ajax: {
                    "url": "{{ route('magento_frontend_listing') }}",
                    data: function(d) {
                        d.categoryname = $('.category_name').val();
                        d.frontend_configuration = $('.search_frontend_config').val();
                        d.admin_configuration = $('.search_admin_config').val();
                        d.location = $('.location_name').val();   
                    },
                },
                columnDefs: [{
                    targets: [],
                    orderable: false,
                    searchable: true,
                    className: 'mdl-data-table__cell--non-numeric'
                }],
                columns: [{
                        data: 'id',
                        name: 'magento_frontend_docs.id',
                        render: function(data, type, row, meta) {
                            var html = '<input type="hidden" name="mm_id" class="data_id" value="' +
                                data + '">';
                            return html + data;
                        }
                    },

                    {
                        data: 'store_website_categories.category_name',
                        name: 'store_website_categories.category_name',
                        render: function(data, type, row, meta) {
                            var categories = JSON.parse(row['categories']);
                            if (!categories || categories.length === 0) {
                                return '<div class="flex items-center justify-left">' + data + '</div>';
                            }

                            var selectedCategoryId = row['store_website_category_id'];
                            var categoriesHtml = '<select id="store_website_category_id" class="form-control edit_mm" required="required" name="store_website_category_id">';
                            categoriesHtml += '<option value="" selected>Select Module Category</option>'; // Add default option

                            categories.forEach(function(category) {
                                if (category.id === selectedCategoryId) {
                                    categoriesHtml += '<option value="' + category.id + '" selected>' + category.title + '</option>';
                                } else {
                                    categoriesHtml += '<option value="' + category.id + '">' + category.title + '</option>';
                                }
                            });

                            categoriesHtml += '</select>';

                            let category_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-category-history"  data-id="${row['id']}" title="Load messages">
                                    <i class="fa fa-info-circle"></i>
                                </button>`;

                            return `<div class="flex flex-center-block" style="position: relative;">
                                        ${categoriesHtml} ${category_history_button}
                                    </div>`;
                        }
                    },
                    {
                        render: function(data, type, row, meta) {

                            let message =
                                `<input type="text" id="parent_folder_${row['id']}" name="parent_folder" class="form-control parent_folder-input" placeholder="parent folder" />`;

                            let remark_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-module-parent-folder p-0"  data-id="${row['id']}" title="Parent Folder History"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;

                            let Upload_button =  `<button style="display: inline-block;" class="btn btn-sm upload-parent-folder-modal  p-0" type="submit" id="submit_message" data-type="parentFolder" data-id="${row['id']}" data-toggle="modal" data-target="#parentImageAddModal"> <i class="fa fa-upload" aria-hidden="true"></i></button>`;
                            
                            let remark_send_button =
                                `<button style="display: inline-block;" class="btn btn-sm btn-image p-0" type="submit" id="submit_message"  data-id="${row['id']}" onclick="saveparentFolder(${row['id']})"><img src="/images/filled-sent.png"></button>`;
                            data = (data == null) ? '' : '';  

                            let ViewFiles = `
                            <button class="btn btn-image view-upload-parent-files-button ml-2" type="button" title="View Uploaded Files" data-id="${row['id']}" data-type="description">
                                                            <img src="/images/google-drive.png" style="cursor: nwse-resize; width: 10px;">
                                                        </button>`;                    
                            let retun_data =
                                `${data} <div class="general-remarks"> ${message} ${remark_send_button} ${Upload_button} ${remark_history_button} ${ViewFiles} </div>`;

                            return retun_data;
                        }
                    },
                    {
                        render: function(data, type, row, meta) {

                            let message =
                                `<input type="text" id="child_folder_${row['id']}" name="child_folder" class="form-control child_folder-input" placeholder="child folder" />`;

                            let Upload_button =  `<button style="display: inline-block;" class="btn btn-sm upload-child-folder-image-modal p-0" type="submit" id="submit_message"  data-target="#childImageAddModal" data-id="${row['id']}"> <i class="fa fa-upload" aria-hidden="true"></i></button>`;
                            
                            let remark_history_button =
                            `<button type="button" class="btn btn-xs btn-image load-module-child-folder p-0"  data-id="${row['id']}" title="Child Folder History"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;

                            let remark_send_button =
                                `<button style="display: inline-block;" class="btn btn-sm btn-image p-0" type="submit" id="submit_message"  data-type="childFolder" data-id="${row['id']}" onclick="saveChildFolder(${row['id']})"><img src="/images/filled-sent.png"></button>`;
                            data = (data == null) ? '' : '';

                            let ViewFiles = `
                            <button class="btn btn-image view-upload-files-button ml-2" type="button" title="View Uploaded Files" data-id="${row['id']}" data-type="description">
                                                            <img src="/images/google-drive.png" style="cursor: nwse-resize; width: 10px;">
                                                        </button>`;

                            let retun_data =
                                `${data} <div class="general-remarks"> ${message} ${remark_send_button} ${Upload_button} ${remark_history_button} ${ViewFiles}</div>`;

                            return retun_data;
                        }
                    },
                    {
                        render: function(data, type, row, meta) {

                            let message =
                                `<input type="text" id="remark_${row['id']}" name="remark" class="form-control remark-input" placeholder="Remark" />`;

                            let remark_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-module-remark" data-type="general" data-id="${row['id']}" title="Remark History"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;

                            let remark_send_button =
                                `<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" id="submit_message"  data-id="${row['id']}" onclick="saveRemarks(${row['id']})"><img src="/images/filled-sent.png"></button>`;
                            data = (data == null) ? '' : '';
                            let retun_data =
                                `${data} <div class="general-remarks"> ${message} ${remark_send_button} ${remark_history_button} </div>`;

                            return retun_data;
                        }
                    },
                    {
                        data: 'location',
                        name: 'magento_frontend_docs.location',
                        render: function(data, type, row, meta) {
                            let remark_history_button = `<button type="button" class="btn btn-xs btn-image load-location-remark" data-type="location" data-id="${row['id']}" title="Location history"> <img src="/images/chat.png" alt=""> </button>`;

                            let datas =
                                `<div class="data-content">
                                        ${data == null ? '' : `<div class="expand-row module-text"><div class="flex items-center justify-left td-mini-container">${setStringLength(data, 9)}</div><div class="flex items-center justify-left td-full-container hidden">${data}</div></div>`}
                                </div>`;

                                return `<div class="flex flex-center-block" style="position: relative;">
                                                            ${datas} ${remark_history_button}
                                        </div>`;
                        }
                    },
                    {
                        data: 'admin_configuration',
                        name: 'magento_frontend_docs.admin_configuration',
                        render: function(data, type, row, meta) {
                            let remark_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-admin-remark" data-type="AdminConfig" data-id="${row['id']}" title="Admin Config History"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;
                           
                                let datas =
                                `<div class="data-content">
                                        ${data == null ? '' : `<div class="expand-row module-text"><div class="flex items-center justify-left td-mini-container">${setStringLength(data, 15)}</div><div class="flex items-center justify-left td-full-container hidden">${data}</div></div>`}
                                </div>`;

                            return `<div class="flex flex-center-block" style="position: relative;">
                                                        ${datas} ${remark_history_button}
                                    </div>`;
                        }
                    },
                    {
                        data: 'frontend_configuration',
                        name: 'magento_frontend_docs.frontend_configuration',
                        render: function(data, type, row, meta) {
                            let remark_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-frontnend-remark" data-type="FrontEndConfig" data-id="${row['id']}" title="Frontend Config History"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;
                           
                                let datas =
                                `<div class="data-content">
                                        ${data == null ? '' : `<div class="expand-row module-text"><div class="flex items-center justify-left td-mini-container">${setStringLength(data, 18)}</div><div class="flex items-center justify-left td-full-container hidden">${data}</div></div>`}
                                </div>`;

                            return `<div class="flex flex-center-block" style="position: relative;">
                                                        ${datas} ${remark_history_button}
                                    </div>`;
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row, meta) {
                           // Extract file_name and google_drive_file_id from the row data
                            let file_name = data.file_name;
                            let fullFimeName = data.file_name;;
                            if (file_name !== null) {
                                file_name = file_name.length > 10 ? file_name.substring(0, 10) + '...' : file_name;
                            }
                            let google_drive_file_id = data.google_drive_file_id;

                            let file_name_html = (file_name == null) ? '' : `
                            <div class="expand-row">
                                        <span class="td-mini-container">${file_name}</span>
                                        <span class="td-full-container hidden">${fullFimeName}</span>
                                </div>`;

                            let action_buttons = '';
                            if (google_drive_file_id) {
                                let documentUrl = `https://drive.google.com/file/d/${google_drive_file_id}/view?usp=sharing`;
                                action_buttons = `
                                    <a target="_blank" href="${documentUrl}" class="btn btn-image padding-10-3 show-details">
                                        <img src="/images/view.png" style="cursor: default;">
                                    </a>
                                    <button class="copy-button btn btn-xs text-dark" data-message="${documentUrl}" title="Copy document URL">
                                        <i class="fa fa-copy"></i>
                                    </button>`;
                            }

                            // Combine both file_name_html and action_buttons in the same TD
                            return `
                                <div class="file-info-container flex flex-center-block">
                                    ${file_name_html}
                                    <div class="action-buttons-container">
                                        ${action_buttons}
                                    </div>
                                </div>`;
                        }
                    },
                    {
                        data: 'user.name',
                        name: 'magento_frontend_docs.user_id',
                        render: function(data, type, row, meta) {
                            var userName = '';
                            if (data !== undefined && data !== null) {
                                userName = data.length > 8 ? data.substring(0, 8) + '...' : data;
                            }

                            return `<td class="expand-row">
                                <div class="expand-row">
                                        <span class="td-mini-container">${userName}</span>
                                        <span class="td-full-container hidden">${data}</span>
                                </div>
                                    </td>`;
                        }
                     },
                    {
                        data: 'created_at',
                        name: 'magento_frontend_docs.created_at',
                        render: function(data, type, row, meta) {
                            var formattedDate = '';
                            
                            if (data !== null) {
                                var dateObject = new Date(data);  // Assuming 'data' is in a valid date format
                                var year = dateObject.getFullYear();
                                var month = String(dateObject.getMonth() + 1).padStart(2, '0');  // Months are zero-based
                                var day = String(dateObject.getDate()).padStart(2, '0');
                                
                                formattedDate = `${year}-${month}-${day}`;
                            }
                            return `<td class="expand-row" >
                                <div class="expand-row">
                                    <span class="td-mini-container">${formattedDate}</span>
                                    <span class="td-full-container hidden">${formattedDate}</span>
                                </div>
                            </td>`;
                        }
                    },

                    {
                        render: function(data, type, row, meta) {

                            let edit_button =
                                `<button type="button" class="btn btn-xs btn-image edit-module" data-type="general" data-id="${row['id']}" title="Edit messages"> <img src="/images/edit.png" alt="" style="cursor: default;"> </button>`;

                            var del_data = "";
                            <?php if (auth()->user() && auth()->user()->isAdmin()) { ?>
                            del_data =
                                `<button type="button" class="btn btn-xs btn-image load-frontend-delete" data-type="general" data-id="${row['id']}" title="delete"> <img src="/images/delete.png" alt="" style="cursor: default;"> </button>`;
                            <?php } ?>

                            let remark_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-frontend-history" data-type="general" data-id="${row['id']}" title="View History"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;
                               
                            return `<div class="flex gap-5">${edit_button} ${del_data} ${remark_history_button} </div>`;
        
                        }
                    },


                ],
                drawCallback: function(settings) {
                    var api = this.api();
                    var recordsTotal = api.page.info().recordsTotal;
                    var recordsFiltered = api.page.info().recordsFiltered;
                    $('#total-count').text(recordsTotal);
                },
            });

        });

        $('#dateform').on('submit', function(e) {
            e.preventDefault();
            magentofrontendTable.draw();

            return false;
        });

        $(document).on('click', '#searchReset', function(e) {
            $('#dateform').trigger("reset");
            e.preventDefault();
            magentofrontendTable.draw();
        });


         // Store Reark
         function saveRemarks(row_id, selector = 'remark') {
            var remark = $("#"+selector+"_" + row_id).val();
            var val = $("#"+selector+"_" + row_id).val();

            $.ajax({
                url: `{{ route('magento-frontend-remark-store') }}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    remark: remark,
                    magento_front_end_id: row_id,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                if (response.status) {
                    $("#"+selector+"_" + row_id).val('');
                    $("#send_to_" + row_id).val('');
                    toastr["success"](response.message);
                    magentofrontendTable.draw();
                } else {
                    toastr["error"](response.message);
                }
                $("#loading-image").hide();
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                if (jqXHR.responseJSON.errors !== undefined) {
                    $.each(jqXHR.responseJSON.errors, function(key, value) {
                        toastr["warning"](value);
                    });
                } else {
                    toastr["error"]("Oops,something went wrong");
                }
                $("#loading-image").hide();
            });
        }


        $(document).on("click", ".view-upload-parent-files-button", function (e) {
                e.preventDefault();
                let id = $(this).data("id");
                let type = $(this).data("type");
                $.ajax({
                    type: "get",
                    url: "{{route('magento-frontend.files.record')}}",
                    data: {
                        id,
                        type,
                    },
                    success: function (response) {
                        if(typeof response.data != 'undefined') {
                            $("#magnetoFileUpload").html(response.data);
                        } else {
                            $("#magnetoFileUpload").html(response);
                        }
                        
                        $("#magnetobackendFileUpload").modal("show")
                    },
                    error: function (response) {
                        toastr['error']("Something went wrong!");
                    }
                });
        });
         $(document).on("click", ".view-upload-files-button", function (e) {
            e.preventDefault();
            let id = $(this).data("id");
            let type = $(this).data("type");            
                $.ajax({
                    type: "get",
                    url: "{{route('magento-frontend.files.record')}}",
                    data: {
                        id,
                        type,
                    },
                    success: function (response) {
                        if(typeof response.data != 'undefined') {
                            $("#magnetoFileUpload").html(response.data);
                        } else {
                            $("#magnetoFileUpload").html(response);
                        }
                        
                        $("#magnetobackendFileUpload").modal("show")
                    },
                    error: function (response) {
                        toastr['error']("Something went wrong!");
                    }
                });
        });

        $(document).on('click', '.load-location-remark', function() {
            var id = $(this).attr('data-id');
            var location = $(this).attr('data-type');

            $.ajax({
                method: "GET",
                url: `{{ route('magento-location-list') }}`,
                dataType: "json",
                data: {
                    id:id,
                    location:location,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            old_location = v.old_location;
                            new_loaction= v.location;

                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> 
                                            ${old_location}
                                        </td>
                                        <td> 
                                            ${new_loaction}
                                        </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${new Date(v.created_at).toISOString().slice(0, 10)} </td>
                                        <td><i class='fa fa-copy copy_remark' data-remark_text='${new_loaction}'></i></td>
                                    </tr>`;
                        });
                        $("#location-magneto-frontend-list").find(".location-magnetolist-view").html(html);
                        $("#location-magneto-frontend-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                    $("#loading-image").hide();
                }
            });
        });

        $(document).on('click', '.load-admin-remark', function() {
            var id = $(this).attr('data-id');
            var admin = $(this).attr('data-type');

            $.ajax({
                method: "GET",
                url: `{{ route('magento-admin-list') }}`,
                dataType: "json",
                data: {
                    id:id,
                    admin:admin,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            new_admin=v.admin_configuration;
                            old_admin = v.old_admin_configuration;
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> 
                                            ${old_admin}
                                        </td>
                                        <td> 
                                            ${new_admin}
                                        </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${new Date(v.created_at).toISOString().slice(0, 10)} </td>
                                        <td><i class='fa fa-copy copy_remark' data-remark_text='${new_admin}'></i></td>
                                    </tr>`;
                        });
                        $("#admin-magneto-frontend-list").find(".admin-magnetolist-view").html(html);
                        $("#admin-magneto-frontend-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                    $("#loading-image").hide();
                }
            });
        });

        $(document).on('click', '.load-frontnend-remark', function() {
            var id = $(this).attr('data-id');
            var admin = $(this).attr('data-type');

            $.ajax({
                method: "GET",
                url: `{{ route('magento-frontend-list') }}`,
                dataType: "json",
                data: {
                    id:id,
                    admin:admin,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            old_frontend=v.old_frontend_configuration;
                            new_frontend = v.frontend_configuration;
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> 
                                            ${old_frontend}
                                        </td>
                                        <td> 
                                            ${new_frontend}
                                        </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${new Date(v.created_at).toISOString().slice(0, 10)} </td>
                                        <td><i class='fa fa-copy copy_remark' data-remark_text='${new_frontend}'></i></td>
                                    </tr>`;
                        });
                        $("#frontend-magneto-frontend-list").find(".frontend-magnetolist-view").html(html);
                        $("#frontend-magneto-frontend-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                    $("#loading-image").hide();
                }
            });
        });


        $(document).on('click', '.load-module-remark', function() {
            var id = $(this).attr('data-id');

            $.ajax({
                method: "GET",
                url: `{{ route('magento-frontend-get-remarks') }}`,
                dataType: "json",
                data: {
                    id:id,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            remarkText=v.remark;
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> 
                                            ${remarkText}
                                        </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${new Date(v.created_at).toISOString().slice(0, 10)} </td>
                                        <td><i class='fa fa-copy copy_remark' data-remark_text='${remarkText}'></i></td>
                                    </tr>`;
                        });
                        $("#remark-magneto-frontend-list").find(".remark-magnetolist-view").html(html);
                        $("#remark-magneto-frontend-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                    $("#loading-image").hide();
                }
            });
        });
                
        $(document).on('click', '.expand-row', function () {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });

     //edit module
     $(document).on('click', '.edit-module', function() {
        var moduleId = $(this).data("id");
        var url = "{{ route('magento_frontend_edit', ['id' => ':id']) }}";
        url = url.replace(':id', moduleId);    
        jQuery.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type: "GET",
            url: url,
        }).done(function(response) {
            $("#magento_module_edit_form #id").val(response.data.id);
            $("#magento_module_edit_form #location").val(response.data.location);
            $("#magento_module_edit_form #admin_configuration").val(response.data.admin_configuration);
            $("#magento_module_edit_form #frontend_configuration").val(response.data.frontend_configuration);
            $("#magento_module_edit_form #parent_folder").val(response.data.parent_folder);
            $("#magento_module_edit_form #child_folder").val(response.data.child_folder);
            $("#magento_module_edit_form #filename").val(response.data.child_folder_image);
			var image = "/magentofrontend-child-image/" + response.data.child_folder_image; 
			$('#magento_module_edit_form #filename').attr('src', image);
            $("#moduleEditModal").modal("show");
        }).fail(function (response) {
            $("#loading-image-preview").hide();
            console.log("Sorry, something went wrong");
        });
    });


    $(document).on('change', '.edit_mm', function() {
            var  column = $(this).attr('name');
            var value = $(this).val();
            var data_id = $(this).parents('tr').find('.data_id').val();
            
            $.ajax({
                type: "POST",
                url: "{{route('magento_frontend.update.option')}}",
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    columnName : column,
                    data : value,
                    id : data_id
                },
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function (response) {
                if(response.code == 200) {
                    $("#loading-image").hide();
                    toastr['success'](response.message);
                }else{
                    $("#loading-image").hide();
                    toastr['error'](response.message);
                }
                
            }).fail(function (response) {
                $("#loading-image").hide();
                console.log("failed");
                toastr['error'](response.message);
            });
        });


        $(document).on('submit', '#magento_module_edit_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("magento_module_edit_form"));
        var magento_module_id = $('#magento_module_edit_form #id').val();
        var button = $(this).find('[type="submit"]');
        $.ajax({
            url: '{{ route("magento_frontend.update", '') }}/' + magento_module_id,
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
                $('#moduleCreateModal #magento_module_edit_form').find('.error-help-block').remove();
                $('#moduleCreateModal #magento_module_edit_form').find('.invalid-feedback').remove();
                $('#moduleCreateModal #magento_module_edit_form').find('.alert').remove();
                toastr["success"](response.message);
                $("#moduleEditModal").modal("hide");
                location.reload();
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



    $(document).on('click', '.load-frontend-history', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: '{{ route("magentofrontend_histories.show", '') }}/' + id,

                dataType: "json",
                data: {
                    id:id,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#magneto-frontend-historylist").modal("show");

                    if (response) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.location} </td>
                                        <td> ${v.admin_configuration} </td>
                                        <td> ${v.frontend_configuration} </td>
                                        <td> ${v.user.name} </td>
                                    </tr>`;
                        });
                        $("#magneto-frontend-historylist").find(".magneto-historylist-view").html(html);
                        $("#magneto-frontend-historylist").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                    $("#loading-image").hide();
                }
            });
        });

        $(document).on('click', '.load-category-history', function() {
            var id = $(this).attr('data-id');

            $.ajax({
                url: '{{ route("magentofrontend_category.histories.show", '') }}/' + id,
                dataType: "json",
                data: {
                    id:id,

                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.new_category ? v.new_category.title : ''} </td>
                                        <td> ${v.old_category ? v.old_category.title : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${new Date(v.created_at).toISOString().slice(0, 10)} </td>
                                    </tr>`;
                        });
                        $("#category-listing").find(".category-listing-view").html(html);
                        $("#category-listing").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        //Store Parent folder
        function saveparentFolder(row_id, selector = 'parent_folder') {
            var folderName = $("#"+selector+"_" + row_id).val();
            var val = $("#"+selector+"_" + row_id).val();
            $.ajax({
                url: `{{ route('magento-frontend-parent-folder-store') }}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    folderName: folderName,
                    magento_front_end_id: row_id,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                if (response.status) {
                    $("#"+selector+"_" + row_id).val('');
                    $("#send_to_" + row_id).val('');
                    toastr["success"](response.message);
                    magentofrontendTable.draw();
                } else {
                    toastr["error"](response.message);
                }
                $("#loading-image").hide();
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                if (jqXHR.responseJSON.errors !== undefined) {
                    $.each(jqXHR.responseJSON.errors, function(key, value) {
                        toastr["warning"](value);
                    });
                } else {
                    toastr["error"]("Oops,something went wrong");
                }
                $("#loading-image").hide();
            });
        }
        
        function saveChildFolder(row_id, selector = 'child_folder') {
            var childFolderName = $("#"+selector+"_" + row_id).val();
            $.ajax({
                url: `{{ route('magento-frontend-child-folder-store') }}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    folderName: childFolderName,
                    magento_front_end_id: row_id,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                console.log(response);
                if (response.status) {
                    
                    toastr["success"](response.message);
                    magentofrontendTable.draw();
                } else {
                    toastr["error"](response.message);
                }
                $("#loading-image").hide();
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                if (jqXHR.responseJSON.errors !== undefined) {
                    $.each(jqXHR.responseJSON.errors, function(key, value) {
                        toastr["warning"](value);
                    });
                } else {
                    toastr["error"]("Oops,something went wrong");
                }
                $("#loading-image").hide();
            });
        }

        $(document).on("click", ".upload-child-folder-image-modal", function() {
            let magento_frontend_id = $(this).data('id');
            $("#childImageAddModal").find('[name="magento_frontend_id"]').val(magento_frontend_id);
            $('#childImageAddModal').modal('show');
        });

        $(document).on('click', '.load-module-parent-folder', function() {
            var id = $(this).attr('data-id');

            $.ajax({
                method: "GET",
                url: `{{ route('magento-frontend-get-parent-folder') }}`,
                dataType: "json",
                data: {
                    id:id,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            folderName = v.parent_folder_name;
                            var imageTag = '';
                            if (v.parent_image && v.parent_image.trim() !== '') {
                                imageTag = `<img src="/magentofrontend-parent-image/${v.parent_image}" alt="Image" "height="50" width="50">`;
                            }
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> 
                                            ${folderName}
                                        </td>
                                        <td> 
                                            ${imageTag}
                                        </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${new Date(v.created_at).toISOString().slice(0, 10)} </td>
                                        <td><i class='fa fa-copy copy_remark' data-remark_text='${folderName}'></i></td>
                                    </tr>`;
                        });
                        $("#magneto-frontend-parent-folder-list").find(".magneto-frontend-parent-view").html(html);
                        $("#magneto-frontend-parent-folder-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                    $("#loading-image").hide();
                }
            });
        });

        $(document).on('click', '.load-module-child-folder', function() {
            var id = $(this).attr('data-id');

            $.ajax({
                method: "GET",
                url: `{{ route('magento-frontend-get-child-folder-history') }}`,
                dataType: "json",
                data: {
                    id:id,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            folderName = v.child_folder_name;
                            var imageTag = '';
                            if (v.child_image && v.child_image.trim() !== '') {
                                imageTag = `<img src="/magentofrontend-child-image//${v.child_image}" alt="Image" "height="50" width="50">`;
                            }
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> 
                                            ${folderName}
                                        </td>
                                        <td> 
                                            ${imageTag}
                                        </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${new Date(v.created_at).toISOString().slice(0, 10)} </td>
                                        <td><i class='fa fa-copy copy_remark' data-remark_text='${folderName}'></i></td>
                                    </tr>`;
                        });
                        $("#magneto-frontend-parent-folder-list").find(".magneto-frontend-parent-view").html(html);
                        $("#magneto-frontend-parent-folder-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                    $("#loading-image").hide();
                }
            });
        });

        $(document).on('click', '.upload-parent-folder-modal', function() {
            let magento_frontend_id = $(this).data('id');
            $("#parentImageAddModal").find('[name="magento_frontend_id"]').val(magento_frontend_id);
        });

        $(document).on('submit', '#magento_frontend_parent_image_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("magento_frontend_parent_image_form"));
        var button = $(this).find('[type="submit"]');
        console.log(button);
        $.ajax({
            url: '{{ route("magento-frontend-parent-folder-image.store") }}',
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
                button.html('Add');
                button.prop('disabled', false);
                button.removeClass('disabled');
            },
            success: function(response) {
                $('#apiDataAddModal #magento_frontend_parent_image_form').trigger('reset');
                magentofrontendTable.draw();
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


    $(document).on('click', '.load-frontend-delete', function () {
        var id = $(this).attr('data-id');
        if (confirm('Are you sure you want to delete this item?')) {
                    $.ajax({
                        url: '/magento-frontend/child-folder/' + id, // Add a slash before id
                        type: 'DELETE',
                        headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                     },
                        dataType: 'json',
                        success: function(response) {
                            location.reload();
                            toastr["success"](response.message);
                        },
                        error: function(xhr) {
                            alert('Error: ' + xhr.responseText);
                        }
                    });
                }
    });

    </script>

@endsection
