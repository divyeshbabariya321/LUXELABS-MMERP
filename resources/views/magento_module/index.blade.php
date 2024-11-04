@extends('layouts.app')



@section('title', $title)

@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <style>
        .general-remarks {
            display: flex;
            align-items: center;
            gap: 5px;
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
            padding: 5px 5px !important;
        }
        table.dataTable tbody th, table.dataTable tbody td {
            padding: 5px 5px !important;
        }
        .copy_remark{
            cursor: pointer;
        }
        .multiselect-native-select .btn-group{
            width: 100%;
            margin: 0px;
            padding: 0;
        }
        .multiselect-native-select .checkbox input{
            margin-top: -5px !important;
        }
        .multiselect-native-select .btn-group button.multiselect{
            width: 100%;
        
        }

    </style>

    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

    
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
        @media(max-width:1200px) {
            .action_button{
                display: block;
                width: 100%;
            }
        }
        .table  select.form-control{
            width: 130px !important;
            padding: 5px;
        }
       .table input.form-control{
            width: 90px !important;
        }

        .general-remarks input.remark-input {
            width: 130px !important;
        }
    </style>
@endsection


@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;z-index: 9999;" />
    </div>

    @php
    $message_send_to = [
        'to_master' => 'Send To Master Developer',
        'to_developer' => 'Send To Developer',
        'to_team_lead' => 'Send To Team Lead',
        'to_tester' => 'Send To Tester',
    ];
    @endphp

    <div class="row ">
        <div class="col-lg-12 ">
            <h2 class="page-heading">
                {{ $title }}
                (<span id="total-count-magento-modules"></span>)
            </h2>
            <form method="POST" action="#" id="dateform">

                <div class="row m-4">
                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {{ html()->select('module', $moduleNames)->placeholder('Module Name')->class('form-control filter-module') }}
                        </div>
                    </div>

                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {{ html()->select('module_type', $magento_module_types)->placeholder('Select Module Type')->class('form-control filter-module_type') }}
                        </div>
                    </div>

                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {{ html()->select('module_category_id', $module_categories)->placeholder('Select Module Category')->class('form-control filter-module_category_id') }}
                        </div>
                    </div>

                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {{ html()->select('magneto_location_id', $module_locations)->placeholder('Select Module Location')->class('form-control filter-magneto_location_id') }}
                        </div>
                    </div>
                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {{ html()->select('return_type_name', $module_return_type_statuserrors)->placeholder('Select Module Return type Error')->class('form-control filter-return_type_name') }}
                        </div>
                    </div>
                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {{ html()->select('is_customized', ['No', 'Yes'])->placeholder('Customized')->class('form-control filter-is_customized') }}
                        </div>
                    </div>
                    <?php /*
                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {{ html()->select('store_website_id', $store_websites)->placeholder('Store Website')->class('form-control') }}
                        </div>
                    </div>
                    */ ?>
                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {{ html()->select('site_impact', ['No', 'Yes'])->id('site_impact')->placeholder('Select Site Impact')->class('form-control filter-site_impact') }}
                        </div>
                    </div>
                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {{ html()->select('modules_status', ['Disabled', 'Enable'])->placeholder('Select Status')->class('form-control')->id("modules_status") }}
                        </div>
                    </div>
                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {{ html()->multiselect('dev_verified_by[]', $users)->class('form-control multiselect-dev') }}
                        </div>
                    </div>
                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {{ html()->multiselect('lead_verified_by[]', $users)->class('form-control multiselect-lead') }}
                        </div>
                    </div>
                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {{ html()->multiselect('dev_verified_status_id[]', $verified_status_array)->class('form-control multiselect-dev-status') }}
                        </div>
                    </div>
                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {{ html()->multiselect('lead_verified_status_id[]', $verified_status_array)->class('form-control multiselect-lead-status') }}
                        </div>
                    </div>
                    <div class="col-xs-3 col-sm-2">
                        <div class="form-group">
                            {{ html()->multiselect('m2_error_status_id[]', $m2_error_status_array)->class('form-control multiselect-m2-error-status') }}
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

                    <div class="action_button form-group pull-right ml-3 mt-3">
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#moduleTypeCreateModal"> Module Type Create </button>
                    
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#moduleCategoryCreateModal"> Module Category Create </button>

                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#moduleCreateModal"> Magento Module Create </button>

                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#magentoModuleVerifiedStatus"> Add Verified Status </button>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#magentoModuleVerifiedStatusList"> List Verified Status </button>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#moduleLocationCreateModal"> Module Location Create  </button>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#moduleReturnTypeCreateModal"> Module Return Type Error Create  </button>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#m2ErrorStatusCreateModal">M2 Error Status Create</button>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#unitTestStatusCreateModal">Unit test Status Create</button>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#columnvisibilityList">Column Visiblity</button>
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

        @php
        $dynamicColumnsToShow = json_decode($hideColumns, true);

        if ($dynamicColumnsToShow !== null) {
            $dynamicColumnsToShow = array_map('intval', $dynamicColumnsToShow);
        } else {
            $dynamicColumnsToShow = []; // Set to an empty array or handle as needed
        }  
        @endphp
       
       <script>
            var dynamicColumnsToShow = @json($dynamicColumnsToShow); // Convert the PHP array to a JSON array
        </script>
        <div class="erp_table_data">
            <table class="table table-bordered" id="erp_table">
                <thead>
                    <tr>
                        <th> Id </th>
                        <th> Remark </th>
                        <th> Category </th>
                        <th> Description </th>
                        <th> Name </th>
                        <th> Location </th>
                        <th> Used At </th>
                        <th> API </th>
                        <th> Cron </th>
                        <th> Version </th>
                        <th> Type </th>
                        <th> Payment Status</th>
                        <th> Status </th>
                        <th> Dev Verified By </th>
                        <th> Dev Verified Status </th>
                        <th> Lead Verified By </th>
                        <th> Lead Verified Status </th>
                        <th> Developer Name </th>
                        <th> Customized </th>
                        <th> js/css </th>
                        <th> 3rd Party Js </th>
                        <th> Sql </th>
                        <th> 3rd Party plugin </th>
                        <th> Site Impact </th>
                        <th> Review Standard </th>
                        <th> Return Type Error </th>
                        <th> Return Type Error Status </th>
                        <th> M2 Error Status </th>
                        <th> M2 Error Remark </th>
                        <th> M2 Error Assignee </th>
                        <th> Unit Test status </th>
                        <th> Unit Test Remarks </th>
                        <th> Unit test User </th>
                        <th> Dependancies </th>
                        <th> Action </th>
    
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        </div>

    {{-- #blank-modal --}}
    @include('partials.plain-modal')
    {{-- #remark-area-list --}}
    @include('magento_module.partials.remark_list')
    {{-- #api-value-histories-list --}}
    @include('magento_module.partials.api_value_histories_list')
    {{-- #verified-status-histories-list --}}
    @include('magento_module.partials.verified_status_histories_list')
    {{-- moduleTypeCreateModal --}} {{-- moduleTypeEditModal --}}
    @include('magento_module_type.partials.form_modals')
    {{-- moduleCategoryCreateModal --}} {{-- moduleCategoryEditModal --}}
    @include('magento_module_category.partials.form_modals')
    {{-- moduleCreateModal --}} {{-- moduleEditModal --}}
    @include('magento_module.partials.form_modals')
    {{-- magentoModuleVerifiedStatus --}} {{-- magentoModuleVerifiedStatus --}}
    @include('magento_module.partials.mm_verified_status_form_modals')
    {{-- magentoModuleVerifiedStatusList --}} {{-- magentoModuleVerifiedStatusList --}}
    @include('magento_module.partials.mm_verified_status_list_modals')
    {{-- apiDataAddModal --}}
    @include('magento_module.partials.api_form_modals')
    {{-- cronJobDataAddModal --}}
    @include('magento_module.partials.cron_form_modals')
    {{-- apiDataShowModal --}}
    @include('magento_module.partials.api_data_show_modals')
    {{-- cronJobDataShowModal --}}
    @include('magento_module.partials.cron_data_show_modals')
    {{-- JsRequireDataAddModal --}}
    @include('magento_module.partials.js_require_form_modals')
    {{-- JsRequireDataShowModal --}}
    @include('magento_module.partials.js_require_show_modals')
    {{-- isCustomizedDataAddModal --}}
    @include('magento_module.partials.is_customized_form_modals')
    {{-- isCustomizedDataShowModal --}}
    @include('magento_module.partials.is_customized_show_modals')
    {{-- magentoModuleHistoryShowModal --}}
    @include('magento_module.partials.show_history_modals')
    {{-- magentoModuleverifiedShowModal --}}
    @include('magento_module.verified_by_list')
     {{-- magentoreviewdShowModal --}}
    @include('magento_module.review-standard-list')
   
     {{-- moduleLocationCreateModal --}} {{-- moduleLocationEditModal --}}
    @include('magneto_module_location.partials.form_modal')
    {{-- moduleLocationnListodal --}} 
    @include('magento_module.location-listing')
    {{-- Description History --}} 
    @include('magento_module.description-history-listing')
    {{-- Used At History --}} 
    @include('magento_module.used-at-history-listing')
    {{-- moduleReturnTypeCreateModal --}}
    @include('magento-return-type-status.form_modal')
    {{-- m2ErrorStatusCreateModal --}}
    @include('magento-m2-error-status.form_modal')
    @include('magento-m2-error-status.m2-error-status-history')
    @include('magento_module.magento-m2-error-assignee-list')
     {{-- moduleReturnTypeHistoryModal --}}
    @include('magento-return-type-status.return-type-history')
     {{-- moduleDependcyModal --}}
     @include('magento_module.partials.dependency_list')
    {{-- moduleM2ErrorRemark --}}
     @include('magento_module.magento-m2-error-remark-list')
    {{-- Unit Test Status CreateModal --}}
    @include('magento_module.magento-unit-test-status.unit-test-status-create-modal')
    {{-- moduleTestUser --}}
    @include('magento_module.magneto-unit-test-user-list')
    {{-- moduleTestRemark --}}
    @include('magento_module.magento-unit-test-remark-list')
    {{-- moduleTestStatus --}}
    @include('magento_module.magneto-unit-test-status-list')

    @include('magento_module.partials.column-visibility-modal')




@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js">
    </script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    {{-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> --}}
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    {{-- <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap.min.js"></script> --}}
    <script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-multiselect.min.js') }} "></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.colVis.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".filter-module").multiselect({
                enableFiltering: true,
                nonSelectedText: 'Select Module',
            });
            $(".multiselect-dev").multiselect({
                allSelectedText: 'All',
                includeSelectAllOption: true,
                enableFiltering: true,
                nonSelectedText: 'Select Dev Verified By',
            });
            $(".multiselect-lead").multiselect({
                allSelectedText: 'All',
                includeSelectAllOption: true,
                enableFiltering: true,
                nonSelectedText: 'Select Lead Verified By',
            });
            $(".multiselect-dev-status").multiselect({
                allSelectedText: 'All',
                includeSelectAllOption: true,
                //enableFiltering: true,
                nonSelectedText: 'Select Dev Verified Status',
            });
            $(".multiselect-lead-status").multiselect({
                allSelectedText: 'All',
                includeSelectAllOption: true,
               // enableFiltering: true,
                nonSelectedText: 'Select Lead Verified Status',
            });
            $(".multiselect-m2-error-status").multiselect({
                allSelectedText: 'All',
                includeSelectAllOption: true,
               // enableFiltering: true,
                nonSelectedText: 'Select M2 error  Status',
            });
        });
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

        $('#extraSearch').on('click', function(e) {
            e.preventDefault();
            oTable.draw();
        });

        // START Print Table Using datatable
        var oTable;
        $(document).ready(function() {
            oTable = $('#erp_table').DataTable({
                pageLength: 50,
                responsive: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
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
                    "url": "{{ route('magento_module.index-post') }}",
                    "type": "POST", // Use POST method
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    data: function(d) {
                        d.module = $('.filter-module').val();
                        d.module_type = $('.filter-module_type').val();
                        d.is_customized = $('.filter-is_customized').val();
                        d.module_category_id = $('.filter-module_category_id').val();   
                        d.magneto_location_id = $('.filter-magneto_location_id').val();
                        d.task_status = $('.filter-task_status').val();
                        d.store_website_id = $('.filter-store_website_id').val();
                        d.site_impact = $('.filter-site_impact').val();
                        d.modules_status = $('#modules_status').val();
                        d.dev_verified_by = $('.multiselect-dev').val();
                        d.dev_verified_status_id = $('.multiselect-dev-status').val();
                        d.lead_verified_by = $('.multiselect-lead').val();
                        d.lead_verified_status_id = $('.multiselect-lead-status').val();
                        d.return_type_error_status = $('.filter-return_type_name').val();
                        d.m2_error_status_id = $('.multiselect-m2-error-status').val();
                        
                        
                        
                        // d.view_all = $('input[name=view_all]:checked').val(); // for Check box
                    },
                },
                columnDefs: [
                        {
                            targets: dynamicColumnsToShow,
                            visible: false,
                        },
                ],
                columns: [{
                        data: 'id',
                        name: 'magento_modules.id',
                        render: function(data, type, row, meta) {
                            var html = '<input type="hidden" name="mm_id" class="data_id" value="'+data+'">';
                            return html + data;
                        }
                    },
                    {
                        data: 'last_message',
                        name: 'magento_modules.last_message',
                        render: function(data, type, row, meta) {
                            
                            let message = `<input type="text" id="remark_${row['id']}" name="remark" class="form-control remark-input" placeholder="Remark" />`;

                            let remark_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-module-remark ml-2" data-type="general" data-id="${row['id']}" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;

                            let remark_send_button =
                                `<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" id="submit_message"  data-id="${row['id']}" onclick="saveRemarks(${row['id']})"><img src="/images/filled-sent.png"></button>`;
                                // data = (data == null) ? '' : `<div class="flex items-center justify-left" title="${data}">${setStringLength(data, 15)}</div>`;
                                data = (data == null) ? '' : '';
                            let retun_data = `${data} <div class="general-remarks"> ${message} ${remark_send_button} ${remark_history_button} </div>`;
                            
                            return retun_data;
                        }
                    },
                    {
                        data: 'category_name',
                        name: 'magento_module_categories.category_name',
                        render: function(data, type, row, meta) {
                            var m_types = row['categories'];
                            var m_types =  m_types.replace(/&quot;/g, '"');
                            if(m_types && m_types != "" ){
                                var m_types = JSON.parse(m_types);
                                var m_types_html = '<select id="module_category_id" class="form-control edit_mm" required="required" name="module_category_id"><option selected="selected" value="">Select Module Category</option>';
                                m_types.forEach(function(m_type){
                                    if(m_type.category_name == data){
                                        m_types_html += `<option value="${m_type.id}" selected>${m_type.category_name}</option>`;
                                    }else{
                                        m_types_html += `<option value="${m_type.id}" >${m_type.category_name}</option>`;
                                    }
                                    
                                });
                                m_types_html += '</select>';
                                return m_types_html;
                            }else{
                                return `<div class="flex items-center justify-left">${data}</div>`;
                            }
                            
                        }
                        
                    },
                    {
                        data: 'module_description',
                        name: 'magento_modules.module_description',
                        render: function(data, type, row, meta) {
                            var status_array = ['Disabled', 'Enable'];
                            data=(data == null) ? '' : `<div class="flex items-center gap-5"><div class="expand-row module-text"><div class="flex  items-center justify-left td-mini-container" title="${data}">${setStringLength(data, 15)}</div><div class="flex items-center justify-left td-full-container hidden" title="${data}">${data}</div></div><button type="button" class="btn btn-xs show-description-modal" title="Show Description History" data-id="${row['id']}"><i class="fa fa-info-circle"></i></button></div>`;
                            return data;
                        }
                    },
                    {
                        data: 'module',
                        name: 'magento_modules.module',
                        render: function(data, type, row, meta) {
                            var status_array = ['Disabled', 'Enable'];
                            data=(data == null) ? '' : `<div class="expand-row module-text" style="word-break: break-all"><div class="flex  items-center justify-left td-mini-container" title="${data}">${setStringLength(data, 5)}</div><div class="flex items-center justify-left td-full-container hidden" title="${data}">${data}</div></div>`;
                            return data;
                        }
                    },
                    {
                        data: 'magento_module_locations',
                        name: 'magento_module_locations.magento_module_locations',
                        render: function(data, type, row, meta) {
                            var m_types = row['locations'];
                            var m_types =  m_types.replace(/&quot;/g, '"');
                            if(m_types && m_types != "" ){
                                var m_types = JSON.parse(m_types);
                                var m_types_html = '<select id="magneto_location_id" class="form-control edit_mm" required="required" name="magneto_location_id"><option selected="selected" value="">Select Module location</option>';
                                m_types.forEach(function(m_type){
                                    if(m_type.magento_module_locations == data){
                                        m_types_html += `<option value="${m_type.id}" selected>${m_type.magento_module_locations}</option>`;
                                    }else{
                                        m_types_html += `<option value="${m_type.id}" >${m_type.magento_module_locations}</option>`;
                                    }
                                    
                                });
                                m_types_html += '</select>';
                                let remark_history_button =
                                `  <button type="button" class="btn btn-xs btn-image load-location-history ml-2" data-type="dev" data-id="${row['id']}" title="Dev User Histories" style="cursor: default;"> <i class="fa fa-info-circle"> </button>`;

                                return `<div class="flex items-center gap-5">${m_types_html} ${remark_history_button}</div>`;
                            
                            }else{
                                return `<div class="flex items-center justify-left">${data}</div>`;
                            }
                            
                        }
                        
                    },
                    {
                        data: 'used_at',
                        name: 'magento_modules.used_at',
                        render: function(data, type, row, meta) {
                            var status_array = ['Disabled', 'Enable'];
                            data=(data == null) ? '' : `<div class="flex items-center gap-5"><div class="expand-row module-text"><div class="flex  items-center justify-left td-mini-container" title="${data}">${setStringLength(data, 15)}</div><div class="flex items-center justify-left td-full-container hidden" title="${data}">${data}</div></div><button type="button" class="btn btn-xs show-usedat-modal" title="Show Used At History" data-id="${row['id']}"><i class="fa fa-info-circle"></i></button></div>`;
                            return data;
                        }
                    },
                    {
                        data: 'api',
                        name: 'magento_modules.api',
                        render: function(data, type, row, meta) {
                            var html = '<select id="api" class="form-control edit_mm" name="api"><option selected="selected" value="">Select API</option>';
                                html += '<option value="0" '+(data == '0' ? 'selected' : '')+'>No</option><option value="1" '+(data == '1' ? 'selected' : '')+'>Yes</option><option value="2" '+(data == '2' ? 'selected' : '')+'>API Error</option><option value="3" '+(data == '3' ? 'selected' : '')+'>API Error Resolve</option>';
                            html +='</select>';
                            let add_button = `<button type="button" class="btn btn-xs add-api-data-modal" title="Add Api Details" data-id="${row['id']}"><i class="fa fa-plus"></i></button>`;
                            let show_button = `<button type="button" class="btn btn-xs show-api-modal" title="Show Api Detail History" data-id="${row['id']}"><i class="fa fa-info-circle"></i></button>`;
                            let value_history_button = `<button type="button" class="btn btn-xs load-api-value-history" title="Show Api value History" data-id="${row['id']}"><i class="fa fa-history"></i></button>`;
                            let html_data = ``;
                            
                            if(data == 1){
                                html_data = `<div class="flex items-center gap-5"> ${html}  ${add_button} ${show_button}  ${value_history_button}</div>`;
                            }else{
                                html_data = `<div class="flex items-center gap-5"> ${html}  ${show_button}  ${value_history_button}</div>`;
                            }
                            return html_data;
                        }
                    },
                    {
                        data: 'cron_job',
                        name: 'magento_modules.cron_job',
                        render: function(data, type, row, meta) {
                            
                            var html = '<select id="cron_job" class="form-control edit_mm" name="cron_job"><option selected="selected" value="">Select Cron Job</option>';
                                html += '<option value="1" '+(data == '1' ? 'selected' : '')+'>Yes</option><option value="0" '+(data == '0' ? 'selected' : '')+'>No</option>';
                            html +='</select>';

                            
                            let add_button = `<button type="button" class="btn btn-xs add-cron_job-modal" title="Add Cron Details" data-id="${row['id']}"><i class="fa fa-plus"></i></button>`;
                            let show_button = `<button type="button" class="btn btn-xs show-cron_job-modal" title="Show Cron History" data-id="${row['id']}"><i class="fa fa-info-circle"></i></button>`;
                            
                            if(data == 1){
                                html_data = `<div class="flex items-center gap-5"> ${html}  ${add_button} ${show_button} </div>`;
                            }else{
                                html_data = `<div class="flex items-center gap-5"> ${html}  ${show_button} </div>`;
                            }
                            return  html_data;
                        }
                    },
                    
                    {
                        data: 'current_version',
                        name: 'magento_modules.current_version',
                    },
                    {
                        data: 'magento_module_type',
                        name: 'magento_module_types.magento_module_type',
                        render: function(data, type, row, meta) {
                            var m_types = row['m_types'];
                            var m_types =  m_types.replace(/&quot;/g, '"');
                            if(m_types && m_types != "" ){
                                var m_types = JSON.parse(m_types);
                                var m_types_html = '<select id="module_type" class="form-control edit_mm" name="module_type"><option selected="selected" value="">Select Module Type</option>';
                                m_types.forEach(function(m_type){
                                    if(m_type.magento_module_type == data){
                                        m_types_html += `<option value="${m_type.id}" selected>${m_type.magento_module_type}</option>`;
                                    }else{
                                        m_types_html += `<option value="${m_type.id}" >${m_type.magento_module_type}</option>`;
                                    }
                                    
                                });
                                m_types_html += '</select>';
                                return m_types_html;
                            }else{
                                return `<div class="flex items-center justify-left">${data}</div>`;
                            }
                            
                        }
                    },
                    {
                        data: 'payment_status',
                        name: 'magento_modules.payment_status',
                        render: function(data, type, row, meta) {
                            
                            var html = '<select id="payment_status" class="form-control edit_mm" name="payment_status"><option selected="selected" value="">Select Payment Status</option>';
                                html += '<option value="Free" '+(data == 'Free' ? 'selected' : '')+'>Free</option><option value="Paid" '+(data == 'Paid' ? 'selected' : '')+'>Paid</option>';
                            html +='</select>';
                            return  html;
                        }
                    },
                    {
                        data: 'status',
                        name: 'magento_modules.status',
                        render: function(data, type, row, meta) {
                            var status_array = ['Disabled', 'Enable'];
                            return status_array[data];
                        }
                    },
                    {
                        data: 'dev_verified_by',
                        name: 'dev_verified_by',
                        render: function(data, type, row, meta) {
                            
                            var dev_list = row['developer_list'];
                            var dev_list =  dev_list.replace(/&quot;/g, '"');
                            if(dev_list && dev_list != "" ){
                                var dev_html = '<select id="dev_verified_by" class="form-control edit_mm" name="dev_verified_by"><option selected="selected" value="">Select user </option>';
                                var dev_list = JSON.parse(dev_list);
                                dev_list.forEach(function(dev){
                                    dev_html += `<option value="${dev.id}" `+(dev.id == data ? 'selected' :'') +`>${dev.name}</option>`;
                                });
                                dev_html +="</select>";
                            }
                            let remark_history_button =
                                `<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" id="add-remark-module-open" data-type="dev" data-id="${row['id']}" title="Add New Dev Remark" ><img src="/images/add.png"></button>
                                <button type="button" class="btn btn-xs btn-image load-module-remark ml-2" data-type="dev" data-id="${row['id']}" title="Dev Remark History"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                <button type="button" class="btn btn-xs btn-image load-user-dev-history ml-2" data-type="dev" data-id="${row['id']}" title="Dev User Histories" style="cursor: default;"> <i class="fa fa-info-circle"> </button>`;

                            return `<div class="flex items-center gap-5">${dev_html} ${remark_history_button}</div>`;
                        }
                    },
                    {
                        data: 'dev_verified_status_id',
                        name: 'dev_verified_status_id',
                        render: function(data, type, row, meta) {
                            
                            var dev_list = row['verified_status'];
                            var dev_list =  dev_list.replace(/&quot;/g, '"');
                            if(dev_list && dev_list != "" ){
                                var dev_html = '<select id="dev_verified_status_id" class="form-control edit_mm" name="dev_verified_status_id"><option selected="selected" value="">Select Status </option>';
                                var dev_list = JSON.parse(dev_list);
                                dev_list.forEach(function(dev){
                                    dev_html += `<option value="${dev.id}" `+(dev.id == data ? 'selected' :'') +`>${dev.name}</option>`;
                                });
                                dev_html +="</select>";
                            }

                            let dev_status_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-status-history ml-2" data-type="dev" data-id="${row['id']}" title="Load status histories"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;
                                
                            return `<div class="flex items-center gap-5">${dev_html} ${dev_status_history_button}</div>`;
                        }
                    },
                    /*{
                        data: 'dev_last_remark',
                        name: 'magento_modules.dev_last_remark',
                        render: function(data, type, row, meta) {
                            let message = `<input type="text" id="dev_last_remark_${row['id']}" name="dev_last_remark" class="form-control" placeholder="Dev Remark" />`;

                            let remark_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-module-remark ml-2" data-type="dev" data-id="${row['id']}" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;

                            let remark_send_button =
                                `<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" id="submit_message"  data-id="${row['id']}" onclick="saveRemarks(${row['id']}, 'dev', 'dev_last_remark')"><img src="/images/filled-sent.png"></button>`;
                                data = (data == null) ? '' : `<div class="flex items-center justify-left" title="${data}">${setStringLength(data, 15)}</div>`;
                            let retun_data = `${data} <div class=""> ${message} ${remark_send_button} ${remark_history_button} </div>`;
                            
                            return retun_data;
                        }
                    },*/
                    {
                        data: 'lead_verified_by',
                        name: 'lead_verified_by',
                        render: function(data, type, row, meta) {
                            var dev_list = row['developer_list'];
                            var dev_list =  dev_list.replace(/&quot;/g, '"');
                            if(dev_list && dev_list != "" ){
                                var dev_html = '<select id="lead_verified_by" class="form-control edit_mm" name="lead_verified_by"><option selected="selected" value="">Select user </option>';
                                var dev_list = JSON.parse(dev_list);
                                dev_list.forEach(function(dev){
                                    dev_html += `<option value="${dev.id}" `+(dev.id == data ? 'selected' :'') +`>${dev.name}</option>`;
                                });
                                dev_html +="</select>";
                            }
                            let remark_history_button =
                                `<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" id="add-remark-module-open" data-type="lead" data-id="${row['id']}" title="Add New Lead Remark" ><img src="/images/add.png"></button>
                                <button type="button" class="btn btn-xs btn-image load-module-remark ml-2" data-type="lead" data-id="${row['id']}" title="Lead Remark History"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>
                                <button type="button" class="btn btn-xs btn-image load-user-dev-history ml-2" data-type="lead" data-id="${row['id']}" title="Lead-user-dev-history" style="cursor: default;"> <i class="fa fa-info-circle"> </button>`;

                            return `<div class="flex items-center gap-5">${dev_html} ${remark_history_button}</div>`;
                            
                        }
                    },
                    {
                        data: 'lead_verified_status_id',
                        name: 'lead_verified_status_id',
                        render: function(data, type, row, meta) {
                            
                            var dev_list = row['verified_status'];
                            var dev_list =  dev_list.replace(/&quot;/g, '"');
                            if(dev_list && dev_list != "" ){
                                var dev_html = '<select id="lead_verified_status_id" class="form-control edit_mm" name="lead_verified_status_id"><option selected="selected" value="">Select Status </option>';
                                var dev_list = JSON.parse(dev_list);
                                dev_list.forEach(function(dev){
                                    dev_html += `<option value="${dev.id}" `+(dev.id == data ? 'selected' :'') +`>${dev.name}</option>`;
                                });
                                dev_html +="</select>";
                            }

                            let lead_status_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-status-history ml-2" data-type="lead" data-id="${row['id']}" title="Load status histories"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;

                            return `<div class="flex items-center gap-5">${dev_html} ${lead_status_history_button}</div>`;
                        }
                    },
                    /*{
                        data: 'lead_last_remark',
                        name: 'magento_modules.lead_last_remark',
                        render: function(data, type, row, meta) {
                            let message = `<input type="text" id="lead_last_remark_${row['id']}" name="lead_last_remark" class="form-control" placeholder="Lead Remark" />`;

                            let remark_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-module-remark ml-2" data-type="lead" data-id="${row['id']}" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;

                            let remark_send_button =
                                `<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" id="submit_message"  data-id="${row['id']}" onclick="saveRemarks(${row['id']}, 'lead', 'lead_last_remark')"><img src="/images/filled-sent.png"></button>`;
                                data = (data == null) ? '' : `<div class="flex items-center justify-left" title="${data}">${setStringLength(data, 15)}</div>`;
                            let retun_data = `${data} <div class=""> ${message} ${remark_send_button} ${remark_history_button} </div>`;
                            
                            return retun_data;
                        }
                    },*/
                    {
                        data: 'developer_id',
                        name: 'users.name',
                        render: function(data, type, row, meta) {
                            
                            var dev_list = row['developer_list'];
                            var dev_list =  dev_list.replace(/&quot;/g, '"');
                            if(dev_list && dev_list != "" ){
                                var dev_html = '<select id="developer_name" class="form-control edit_mm" name="developer_name"><option selected="selected" value="">Select developer name</option>';
                                var dev_list = JSON.parse(dev_list);
                                dev_list.forEach(function(dev){
                                    dev_html += `<option value="${dev.id}" `+(dev.id == data ? 'selected' :'') +`>${dev.name}</option>`;
                                });
                                dev_html +="</select>";
                            }
                            return `<div class="flex items-center justify-left">${dev_html}</div>`;
                        }
                    },
                    {
                        data: 'is_customized',
                        name: 'magento_modules.is_customized', 
                        render: function(data, type, row, meta) {
                            
                            var html = '<select id="is_customized" class="form-control edit_mm"  name="is_customized"><option selected="selected" value="">Customized</option>';
                                html += '<option value="1" '+(data == '1' ? 'selected' : '')+'>Yes</option><option value="0" '+(data == '0' ? 'selected' : '')+'>No</option>';
                            html +='</select>';
                            
                            let add_button = `<button type="button" class="btn btn-xs add-is_customized-modal" title="Add 3rd party JS Details" data-id="${row['id']}"><i class="fa fa-plus"></i></button>`;
                            let show_button = `<button type="button" class="btn btn-xs show-is_customized-modal" title="Show 3rd party JS History" data-id="${row['id']}"><i class="fa fa-info-circle"></i></button>`;
                            
                            if(data == 1){
                                html_data = `<div class="flex items-center gap-5"> ${html}  ${add_button} ${show_button} </div>`;
                            }else{
                                html_data = `<div class="flex items-center gap-5"> ${html}  ${show_button} </div>`;
                            }
                            return html_data;
                        }
                    },
                    {
                        data: 'is_js_css',
                        name: 'magento_modules.is_js_css',
                        render: function(data, type, row, meta) {
                             
                            var html = '<select id="is_js_css" class="form-control edit_mm"  name="is_js_css"><option selected="selected" value="">Select Javascript/css Require</option>';
                                html += '<option value="1" '+(data == '1' ? 'selected' : '')+'>Yes</option><option value="0" '+(data == '0' ? 'selected' : '')+'>No</option>';
                            html +='</select>';

                            return html;
                        }
                    },
                    {
                        data: 'is_third_party_js',
                        name: 'magento_modules.is_third_party_js',
                        render: function(data, type, row, meta) {

                            var html = '<select id="is_third_party_js" class="form-control edit_mm"  name="is_third_party_js"><option selected="selected" value="">Select Javascript/css Require</option>';
                                html += '<option value="1" '+(data == '1' ? 'selected' : '')+'>Yes</option><option value="0" '+(data == '0' ? 'selected' : '')+'>No</option>';
                            html +='</select>';

                            let add_button = `<button type="button" class="btn btn-xs add-third_party_js-modal" title="Add Customized Details" data-id="${row['id']}"><i class="fa fa-plus"></i></button>`;
                            let show_button = `<button type="button" class="btn btn-xs show-third_party_js-modal" title="Show Customized History" data-id="${row['id']}"><i class="fa fa-info-circle"></i></button>`;
                            
                            
                            if(data == 1){
                                html_data = `<div class="flex items-center gap-5"> ${html} ${add_button} ${show_button} </div>`;
                            }else{
                                html_data = `<div class="flex items-center gap-5"> ${html} ${show_button} </div>`;
                            }
                            return html_data;
                        }
                    },
                    {
                        data: 'is_sql',
                        name: 'magento_modules.is_sql',
                        render: function(data, type, row, meta) {
                            var html = '<select id="is_sql" class="form-control edit_mm"  name="is_sql"><option selected="selected" value="">Select Sql Query Status</option>';
                                html += '<option value="1" '+(data == '1' ? 'selected' : '')+'>Yes</option><option value="0" '+(data == '0' ? 'selected' : '')+'>No</option>';
                            html +='</select>';

                            return html;
                        }
                    },
                    {
                        data: 'is_third_party_plugin',
                        name: 'magento_modules.is_third_party_plugin',
                        render: function(data, type, row, meta) {
                            var html = '<select id="is_third_party_plugin" class="form-control edit_mm"  name="is_third_party_plugin"><option selected="selected" value="">Select Third Party Plugin</option>';
                                html += '<option value="1" '+(data == '1' ? 'selected' : '')+'>Yes</option><option value="0" '+(data == '0' ? 'selected' : '')+'>No</option>';
                            html +='</select>';

                            return html;
                        }
                    },
                    {
                        data: 'site_impact',
                        name: 'magento_modules.site_impact',
                        render: function(data, type, row, meta) {
                            var html = '<select id="site_impact" class="form-control edit_mm"  name="site_impact"><option selected="selected" value="">Site Impact</option>';
                                html += '<option value="1" '+(data == '1' ? 'selected' : '')+'>Yes</option><option value="0" '+(data == '0' ? 'selected' : '')+'>No</option>';
                            html +='</select>';

                            return html;
                        }
                    },
                    {
                        data: 'module_review_standard',
                        name: 'magento_modules.module_review_standard',
                        render: function(data, type, row, meta) {
                            var html = '<select id="module_review_standard" class="form-control edit_mm" name="module_review_standard">';
                            html += '<option value="">Review Standard</option>';
                            html += '<option value="1" ' + (data == '1' ? 'selected' : '') + '>Yes</option>';
                            html += '<option value="0" ' + (data == '0' ? 'selected' : '') + '>No</option>';
                            html += '</select>';

                            if (data === null) {
                                html = '<select id="module_review_standard" class="form-control edit_mm" name="module_review_standard">';
                                html += '<option value="">No</option>';
                                html += '<option value="1">Yes</option>';
                                html += '</select>';
                            }

                            let remark_history_button =
                                `  <button type="button" class="btn btn-xs btn-image load-review-standard-history ml-2"  data-id="${row['id']}" title="Review standard Histories" style="cursor: default;"> <i class="fa fa-info-circle"> </button>`;
                            
                            return `<div class="flex items-center gap-5">${html} ${remark_history_button}</div>`;
   
                        }
                    },
                    {
                        data: 'return_type_error',
                        name: 'magento_modules.return_type_error',
                        render: function(data, type, row, meta) {
                            let message = `<input type="text" id="return_type_error_${row['id']}" name="return_type_error" class="form-control return_type_error_input" placeholder="Return Type Error" />`;

                            let return_type_error_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-module-remark ml-2" data-type="return_type_error" data-id="${row['id']}" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;

                            let return_type_error_send_button =
                                `<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" id="submit_message"  data-id="${row['id']}" onclick="saveRemarks(${row['id']}, 'return_type_error', 'return_type_error')"><img src="/images/filled-sent.png"></button>`;
                                // data = (data == null) ? '' : `<div class="flex items-center justify-left" title="${data}">${setStringLength(data, 15)}</div>`;
                                data = (data == null) ? '' : '';
                            let retun_data = `${data} <div class="general-remarks"> ${message} ${return_type_error_send_button} ${return_type_error_history_button} </div>`;
                            
                            return retun_data;
                        }
                    },

                    {
                        data: 'return_type_name',
                        name: 'magento_modules.return_type_error_status',
                        render: function(data, type, row, meta) {
                            console.log(data);
                            var m_types = row['module_return_type_statuserrors'];
                            var m_types =  m_types.replace(/&quot;/g, '"');
                            if(m_types && m_types != "" ){
                                var m_types = JSON.parse(m_types);
                                var m_types_html = '<select id="return_type_error_status" class="form-control edit_mm" required="required" name="return_type_error_status"><option selected="selected" value="">Select Return type Error Status</option>';
                                m_types.forEach(function(m_type){
                                    if(m_type.return_type_name == data){
                                        m_types_html += `<option value="${m_type.id}" selected>${m_type.return_type_name}</option>`;
                                    }else{
                                        m_types_html += `<option value="${m_type.id}" >${m_type.return_type_name}</option>`;
                                    }
                                    
                                });
                                m_types_html += '</select>';
                                let remark_history_button =
                                `  <button type="button" class="btn btn-xs btn-image load-error-type-history ml-2" data-type="dev" data-id="${row['id']}" title="Dev User Histories" style="cursor: default;"> <i class="fa fa-info-circle"> </button>`;

                                return `<div class="flex items-center gap-5">${m_types_html} ${remark_history_button}</div>`;
                            
                            }else{
                                return `<div class="flex items-center justify-left">${data}</div>`;
                            }
                            
                        }
                    },
                    {
                        data: 'm2_error_status_id',
                        name: 'magento_modules.m2_error_status_id',
                        render: function(data, type, row, meta) {
                            
                            var m2ErrorStatuses = row['m2_error_status'];
                            var m2ErrorStatuses =  m2ErrorStatuses.replace(/&quot;/g, '"');
                            if(m2ErrorStatuses && m2ErrorStatuses != "" ){
                                var dev_html = '<select id="m2_error_status_id" class="form-control edit_mm" name="m2_error_status_id"><option selected="selected" value="">Select Status </option>';
                                var m2ErrorStatuses = JSON.parse(m2ErrorStatuses);
                                m2ErrorStatuses.forEach(function(dev){
                                    dev_html += `<option value="${dev.id}" `+(dev.id == data ? 'selected' :'') +`>${dev.m2_error_status_name}</option>`;
                                });
                                dev_html +="</select>";
                            }

                            let history_button =
                                `<button type="button" class="btn btn-xs btn-image load-m2-error-status-history ml-2" data-id="${row['id']}" title="Load histories"> <i class="fa fa-info-circle"> </button>`;

                            return `<div class="flex items-center gap-5">${dev_html} ${history_button}</div>`;
                        }
                    },
                    {
                        data: 'm2_error_remark',
                        name: 'magento_modules.m2_error_remark',
                        render: function(data, type, row, meta) {
                            
                            let message = `<input type="text" id="m2_error_remark_${row['id']}" name="m2_error_remark" class="form-control m2_error_remark_input" placeholder="M2 Error Remark" />`;

                            let remark_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-module-unit-m2-remark ml-2" data-id="${row['id']}" title="Unit Test Remark History"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;

                            let remark_send_button =
                                `<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" id="submit_message"  data-id="${row['id']}" onclick="saveM2Remarks(${row['id']})"><img src="/images/filled-sent.png"></button>`;

                            let retun_data = `<div class="general-remarks"> ${message} ${remark_send_button} ${remark_history_button} </div>`;
                            
                            return retun_data;
                        }
                    },
                    {
                        data: 'm2_error_assignee',
                        name: 'm2_error_assignee',
                        render: function(data, type, row, meta) {
                            
                            var dev_list = row['developer_list'];
                            var dev_list =  dev_list.replace(/&quot;/g, '"');
                            if(dev_list && dev_list != "" ){
                                var dev_html = '<select id="m2_error_assignee" class="form-control edit_mm" name="m2_error_assignee"><option selected="selected" value="">Select user </option>';
                                var dev_list = JSON.parse(dev_list);
                                dev_list.forEach(function(dev){
                                    dev_html += `<option value="${dev.id}" `+(dev.id == data ? 'selected' :'') +`>${dev.name}</option>`;
                                });
                                dev_html +="</select>";
                            }
                            let history_button =
                                `<button type="button" class="btn btn-xs btn-image load-m2-error-assignee-history ml-2" data-id="${row['id']}" title="Assignee Histories" style="cursor: default;"> <i class="fa fa-info-circle"> </button>`;

                            return `<div class="flex items-center gap-5">${dev_html} ${history_button}</div>`;
                        }
                    },
                    {
                        data: 'unit_test_status_id',
                        name: 'magento_modules.unit_test_status_id',
                        render: function(data, type, row, meta) {
                            
                            var unitTestStatuses= row['unit_test_status'];
                            var unitTestStatuses =  unitTestStatuses.replace(/&quot;/g, '"');
                            if(unitTestStatuses && unitTestStatuses != "" ){
                                var dev_html = '<select id="unit_test_status_id" class="form-control edit_mm" name="unit_test_status_id"><option selected="selected" value="">Select unit Test Status </option>';
                                var unitTestStatuses = JSON.parse(unitTestStatuses);
                                unitTestStatuses.forEach(function(dev){
                                    dev_html += `<option value="${dev.id}" `+(dev.id == data ? 'selected' :'') +`>${dev.unit_test_status_name}</option>`;
                                });
                                dev_html +="</select>";
                            }

                            let history_button =
                                `<button type="button" class="btn btn-xs btn-image load-unit-test-status-history ml-2" data-id="${row['id']}" title="Load histories"> <i class="fa fa-info-circle"> </button>`;

                            return `<div class="flex items-center gap-5">${dev_html} ${history_button}</div>`;
                        }
                    },
                    {
                        data: 'unit_test_remark',
                        name: 'magento_modules.unit_test_remark',
                        render: function(data, type, row, meta) {
                            
                            let message = `<input type="text" id="unit_test_remark_${row['id']}" name="unit_test_remark" class="form-control unit_test_remark_input" placeholder="Unit Test Remark" />`;

                            let remark_history_button =
                                `<button type="button" class="btn btn-xs btn-image load-module-unit-test-remark ml-2" data-id="${row['id']}" title="Unit Test Remark History"> <img src="/images/chat.png" alt="" style="cursor: default;"> </button>`;

                            let remark_send_button =
                                `<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" type="submit" id="submit_message"  data-id="${row['id']}" onclick="saveUnitTestRemarks(${row['id']})"><img src="/images/filled-sent.png"></button>`;

                            let retun_data = `<div class="general-remarks"> ${message} ${remark_send_button} ${remark_history_button} </div>`;
                            
                            return retun_data;
                        }
                    },
                    {
                        data: 'unit_test_user_id',
                        name: 'unit_test_user_id',
                        render: function(data, type, row, meta) {
                            
                            var dev_list = row['developer_list'];
                            var dev_list =  dev_list.replace(/&quot;/g, '"');
                            if(dev_list && dev_list != "" ){
                                var dev_html = '<select id="unit_test_user_id" class="form-control edit_mm" name="unit_test_user_id"><option selected="selected" value="">Select test user </option>';
                                var dev_list = JSON.parse(dev_list);
                                dev_list.forEach(function(dev){
                                    dev_html += `<option value="${dev.id}" `+(dev.id == data ? 'selected' :'') +`>${dev.name}</option>`;
                                });
                                dev_html +="</select>";
                            }
                            let history_button =
                                `<button type="button" class="btn btn-xs btn-image load-test-user-history ml-2" data-id="${row['id']}" title="Assignee Histories" style="cursor: default;"> <i class="fa fa-info-circle"> </button>`;

                            return `<div class="flex items-center gap-5">${dev_html} ${history_button}</div>`;
                        }
                    },
                     {
                        data: 'dependency',
                        name: 'magento_modules.dependency',
                        render: function(data, type, row, meta) {
                            var status_array = ['Disabled', 'Enable'];
                            data=(data == null) ? '' : `<div class="flex items-center gap-5"><div class="expand-row module-text"><div class="flex  items-center justify-left td-mini-container" title="${data}">${setStringLength(data, 15)}</div><div class="flex items-center justify-left td-full-container hidden" title="${data}">${data}</div></div><button style="display: inline-block;width: 10%" class="btn btn-sm btn-image" id="add-dependancies-module-open"  data-id="${row['id']}" title="Add New Dependancies Remarks " ><img src="/images/add.png"></button>
                                <button type="button" class="btn btn-xs btn-image load-dependancies-remark ml-2" data-type="general" data-id="${row['id']}" title="Load messages"> <img src="/images/chat.png" alt="" style="cursor: default;">  </button></div>`;
                            return data;
                        }
                        
                    },
                    {
                        data: 'id',
                        name: 'magento_modules.id',
                        // visible:false,
                        render: function(data, type, row, meta) {
                            row["m_types"] = "";
                            row["developer_list"] = "";
                            row["categories"] = "";
                            row["website_list"] = "";
                            row["verified_status"] = "";
                            var listing_route = '{{ route("magento_module_listing") }}?module_name=' + row['module']; 
                            var list_data = actionShowButtonWithTitle(listing_route, "Listing page");

                            var show_data = actionShowButtonWithClass('show-details', row['id']);
                            var edit_data = actionEditButtonWithClass('edit-magento-module', row['id']);
                            let history_button = `<button type="button" class="btn btn-xs show-magenato_module_history-modal" title="Show History" data-id="${row['id']}"><i class="fa fa-info-circle"></i></button>`;
                            var del_data = "";
                            <?php if (auth()->user() && auth()->user()->isAdmin()) { ?>
                            del_data = actionDeleteButton(row['id']);
                            <?php } ?>
                            return `<div class="flex justify-left items-center">${list_data} ${show_data} ${history_button} ${edit_data} ${del_data} </div>`;
                        }
                    },
                ],

                    drawCallback: function(settings) {
                    var api = this.api();
                    var recordsTotal = api.page.info().recordsTotal;
                    var recordsFiltered = api.page.info().recordsFiltered;
                    $('#total-count-magento-modules').text(recordsTotal);
                },
            });


        });
        // END Print Table Using datatable

        // Delete Module 
        $(document).on('click', '.clsdelete', function() {
            var id = $(this).attr('data-id');
            var e = $(this).parent().parent();
            var url = `{{ url('/') }}/magento_modules/` + id;
            tableDeleteRow(url, oTable);
        });

        //Status Update 
        $(document).on('click', '.clsstatus', function() {
            var id = $(this).attr('data-id');
            var status = $(this).attr('data-status');
            var url = `{{ url('/') }}/magento_modules/status/` + id + `/` + status;
            tableChnageStatus(url, oTable);
        });

        // Load All Module Details
        $(document).on("click", ".show-details", function() {

            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: `{{ url('/') }}/magento_modules/` + id,
                data: {
                    id: id
                },
                dataType: "json",
                success: function(response) {
                    if (response.code == 200) {
                        $("#blank-modal").find(".modal-title").html(response.title);
                        $("#blank-modal").find(".modal-body").html(response.data);
                        $("#blank-modal").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });
        
        // Store Reark
        function saveRemarks(row_id, type = 'general', selector = 'remark') {
            var remark = $("#"+selector+"_" + row_id).val();
            // var send_to = $("#send_to_" + row_id).val();

            var val = $("#"+selector+"_" + row_id).val();

            $.ajax({
                url: `{{ route('magento_module_remark.store') }}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    remark: remark,
                    // send_to: send_to,
                    magento_module_id: row_id,
                    type: type
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                if (response.status) {
                    $("#"+selector+"_" + row_id).val('');
                    $("#send_to_" + row_id).val('');
                    toastr["success"](response.message);
                    oTable.draw();
                } else {
                    toastr["error"](response.message);
                }
                $("#loading-image").hide();
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                if (jqXHR.responseJSON.errors !== undefined) {
                    $.each(jqXHR.responseJSON.errors, function(key, value) {
                        // $('#validation-errors').append('<div class="alert alert-danger">' + value + '</div');
                        toastr["warning"](value);
                    });
                } else {
                    toastr["error"]("Oops,something went wrong");
                }
                $("#loading-image").hide();
            });
        }


         // Store M2 Remark
         function saveM2Remarks(row_id, selector = 'm2_error_remark') {
            var remark = $("#"+selector+"_" + row_id).val();
            var val = $("#"+selector+"_" + row_id).val();

            $.ajax({
                url: `{{ route('magento_module_m2_remark.store') }}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    remark: remark,
                    magento_module_id: row_id,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                if (response.status) {
                    $("#"+selector+"_" + row_id).val('');
                    toastr["success"](response.message);
                    oTable.draw();
                } else {
                    toastr["error"](response.message);
                }
                $("#loading-image").hide();
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                if (jqXHR.responseJSON.errors !== undefined) {
                    $.each(jqXHR.responseJSON.errors, function(key, value) {
                        // $('#validation-errors').append('<div class="alert alert-danger">' + value + '</div');
                        toastr["warning"](value);
                    });
                } else {
                    toastr["error"]("Oops,something went wrong");
                }
                $("#loading-image").hide();
            });
        }


         // Store M2 Remark
         function saveUnitTestRemarks(row_id, selector = 'unit_test_remark') {
            var remark = $("#"+selector+"_" + row_id).val();
            var val = $("#"+selector+"_" + row_id).val();

            $.ajax({
                url: `{{ route('magento_module_unit_test_remark.store') }}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    remark: remark,
                    magento_module_id: row_id,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                if (response.status) {
                    $("#"+selector+"_" + row_id).val('');
                    toastr["success"](response.message);
                    oTable.draw();
                } else {
                    toastr["error"](response.message);
                }
                $("#loading-image").hide();
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                if (jqXHR.responseJSON.errors !== undefined) {
                    $.each(jqXHR.responseJSON.errors, function(key, value) {
                        // $('#validation-errors').append('<div class="alert alert-danger">' + value + '</div');
                        toastr["warning"](value);
                    });
                } else {
                    toastr["error"]("Oops,something went wrong");
                }
                $("#loading-image").hide();
            });
        }

        $(document).on("click", ".add-api-data-modal", function() {
            let magento_module_id = $(this).data('id');
            $("#apiDataAddModal").find('[name="magento_module_id"]').val(magento_module_id);
            $('#apiDataAddModal').modal('show');
        });

        $(document).on("click", ".add-cron_job-modal", function() {
            let magento_module_id = $(this).data('id');
            $("#cronJobDataAddModal").find('[name="magento_module_id"]').val(magento_module_id);
            $('#cronJobDataAddModal').modal('show');
        });

        $(document).on("click", ".add-third_party_js-modal", function() {
            let magento_module_id = $(this).data('id');
            $("#JsRequireDataAddModal").find('[name="magento_module_id"]').val(magento_module_id);
            $('#JsRequireDataAddModal').modal('show');
        });

        $(document).on("click", ".add-is_customized-modal", function() {
            let magento_module_id = $(this).data('id');
            $("#isCustomizedDataAddModal").find('[name="magento_module_id"]').val(magento_module_id);
            $('#isCustomizedDataAddModal').modal('show');
        });
        
        // Load Remark
        $(document).on('click', '.btn-mmanr-save-remark', function() {
            var magento_module_id=$("#mmanr-magento_module_id").val();
            var type=$("#mmanr-type ").val();
            var remark=$("#mmanr-remark").val();
            var frontend_issues=$("#mmanr-frontend_issues").val();
            var backend_issues=$("#mmanr-backend_issues").val();
            var security_issues=$("#mmanr-security_issues").val();
            var api_issues=$("#mmanr-api_issues").val();
            var performance_issues=$("#mmanr-performance_issues").val();
            var best_practices=$("#mmanr-best_practices").val();
            var conclusion=$("#mmanr-conclusion").val();
            var other=$("#mmanr-other").val();

            $.ajax({
                url: `{{ route('magento_module_remark.store') }}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    remark: remark,
                    magento_module_id: magento_module_id,
                    type: type,
                    frontend_issues: frontend_issues,
                    backend_issues: backend_issues,
                    security_issues: security_issues,
                    api_issues: api_issues,
                    performance_issues: performance_issues,
                    best_practices: best_practices,
                    conclusion: conclusion,
                    other: other,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                if (response.status) {
                    toastr["success"](response.message);
                    $("#modal-add-new-remark").modal("hide");
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
        });
        $(document).on('click', '#add-remark-module-open', function() {
            var id = $(this).attr('data-id');
            var type = $(this).attr('data-type');
            $("#modal-add-new-remark #mmanr-magento_module_id").val(id);
            $("#modal-add-new-remark #mmanr-type").val(type);
            $("#modal-add-new-remark").modal("show");
        });

         $(document).on('click', '#add-dependancies-module-open', function() {
            var id = $(this).attr('data-id');
            $("#modal-add-new-dependency #mmdepency_magento_module_id").val(id);
            $("#modal-add-new-dependency").modal("show");
        });

        $(document).on('click', '.btn-depency-save', function() {
            var magento_module_id=$("#mmdepency_magento_module_id").val();

            var remark=$("#depency_remark").val();
            var module_issues=$("#depency_module_issues").val();
            var api_issues=$("#depency_api_issues").val();
            var theme_issues=$("#depency_theme_issues").val();

            $.ajax({
                url: `{{ route('magento_module_dependency.store') }}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    remark: remark,
                    magento_module_id: magento_module_id,
                    module_issues: module_issues,
                    api_issues: api_issues,
                    theme_issues: theme_issues,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                if (response.status) {
                    toastr["success"](response.message);
                    $("#modal-add-new-dependency").modal("hide");
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
        });

        $(document).on('click', '.load-dependancies-remark ', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: `{{ route('magento_module_dependency.remarks', ['', '']) }}/` + id,
                dataType: "json",
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            remarkText=v.depency_remark;
                            if(v.depency_module_issues!='' && v.depency_module_issues!=null){
                                remarkText+="<br><br><b>Module Issues:</b><br>"+v.depency_module_issues;
                            }
                            if(v.depency_theme_issues!='' && v.depency_theme_issues!=null){
                                remarkText+="<br><br><b>ThemeIssues:</b><br>"+v.depency_theme_issues;
                            }
                            if(v.depency_api_issues!='' && v.depency_api_issues!=null){
                                remarkText+="<br><br><b>Api Issues:</b><br>"+v.depency_api_issues;
                            }
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> 
                                            ${remarkText}
                                        </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                        <td><i class='fa fa-copy copy_remark' data-remark_text='${remarkText}'></i></td>
                                    </tr>`;
                        });
                        $("#dependency-area-list").find(".dependency-action-list-view").html(html);
                        $("#dependency-area-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                    $("#loading-image").hide();
                }
            });
        });

        $(document).on('click', '.load-module-remark', function() {
            var id = $(this).attr('data-id');
            var type = $(this).attr('data-type');
            $.ajax({
                method: "GET",
                url: `{{ route('magento_module_remark.get_remarks', ['', '']) }}/` + id + '/' + type,
                dataType: "json",
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            remarkText=v.remark;
                            if(v.frontend_issues!='' && v.frontend_issues!=null){
                                remarkText+="<br><br><b>Frontend Issues:</b><br>"+v.frontend_issues;
                            }
                            if(v.backend_issues!='' && v.backend_issues!=null){
                                remarkText+="<br><br><b>Backend Issues:</b><br>"+v.backend_issues;
                            }
                            if(v.security_issues!='' && v.security_issues!=null){
                                remarkText+="<br><br><b>Security Issues:</b><br>"+v.security_issues;
                            }
                            if(v.api_issues!='' && v.api_issues!=null){
                                remarkText+="<br><br><b>API Issues:</b><br>"+v.api_issues;
                            }
                            if(v.performance_issues!='' && v.performance_issues!=null){
                                remarkText+="<br><br><b>Performance Issues:</b><br>"+v.performance_issues;
                            }
                            if(v.best_practices!='' && v.best_practices!=null){
                                remarkText+="<br><br><b>Best Practices:</b><br>"+v.best_practices;
                            }
                            if(v.conclusion!='' && v.conclusion!=null){
                                remarkText+="<br><br><b>Conclusion:</b><br>"+v.conclusion;
                            }
                            if(v.other!='' && v.other!=null){
                                remarkText+="<br><br><b>Other:</b><br>"+v.other;
                            }
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> 
                                            ${remarkText}
                                        </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                        <td><i class='fa fa-copy copy_remark' data-remark_text='${remarkText}'></i></td>
                                    </tr>`;
                        });
                        $("#remark-area-list").find(".remark-action-list-view").html(html);
                        $("#remark-area-list").find(".modal-type").html(prepareTitle(type));
                        // $("#blank-modal").find(".modal-title").html(response.title);
                        // $("#blank-modal").find(".modal-body").html(response.data);
                        $("#remark-area-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                    $("#loading-image").hide();
                }
            });
        });

        function prepareTitle(str) {
            let frags = str.split('_');
            for (let i = 0; i < frags.length; i++) {
                frags[i] = frags[i].charAt(0).toUpperCase() + frags[i].slice(1);
            }
            return frags.join(' ');
        }

        // Load status history
        $(document).on('click', '.load-status-history', function() {
            var id = $(this).attr('data-id');
            var type = $(this).attr('data-type');

            $.ajax({
                method: "GET",
                url: `{{ route('magento_module.get-verified-status-histories', ['', '']) }}/` + id + '/' + type,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.old_status ? v.old_status.name : ''} </td>
                                        <td> ${v.new_status ? v.new_status.name : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#verified-status-histories-list").find(".verified-status-histories-list-view").html(html);
                        $("#verified-status-histories-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });
        $(document).on('click', '.load-api-value-history', function() {
            var id = $(this).attr('data-id');
            
            $.ajax({
                method: "GET",
                url: `{{ route('magento_module.get-api-value-histories', ['']) }}/` + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            var oldValue="";
                            if(v.old_value==0){oldValue="NO";}
                            if(v.old_value==1){oldValue="YES";}
                            if(v.old_value==2){oldValue="API Error";}
                            if(v.old_value==3){oldValue="API Error Resolve";}
                            var newValue="";
                            if(v.new_value==0){newValue="NO";}
                            if(v.new_value==1){newValue="YES";}
                            if(v.new_value==2){newValue="API Error";}
                            if(v.new_value==3){newValue="API Error Resolve";}
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${oldValue} </td>
                                        <td> ${newValue} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#api-value-histories-list").find(".api-value-histories-list-view").html(html);
                        $("#api-value-histories-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

         // Load status history
         $(document).on('click', '.load-user-dev-history', function() {
            var id = $(this).attr('data-id');
            var type = $(this).attr('data-type');
            $.ajax({
                method: "GET",
                url: "{{ route('magento_module.verified.User')}}",
                dataType: "json",
                data: {
                    id:id,
                    type:type,
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.old_verified_by ? v.old_verified_by.name : ''} </td>
                                        <td> ${v.new_verified_by ? v.new_verified_by.name : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#verified_by_list").find(".verified-by-histories-list-view").html(html);
                        $("#verified_by_list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        $(document).on('click', '.load-m2-error-assignee-history', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: "{{ route('magento_module.m2-error-assignee-history')}}",
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
                                        <td> ${v.old_assignee ? v.old_assignee.name : ''} </td>
                                        <td> ${v.new_assignee ? v.new_assignee.name : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#magento-m2-error-assignee-list").find(".magento-m2-error-assignee-list-view").html(html);
                        $("#magento-m2-error-assignee-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        $(document).on('click', '.load-test-user-history', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: "{{ route('magento_module.unit-test-user-history')}}",
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
                                        <td> ${v.old_test_user ? v.old_test_user.name : ''} </td>
                                        <td> ${v.new_test_user ? v.new_test_user.name : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${new Date(v.created_at).toISOString().slice(0, 10)} </td>
                                    </tr>`;
                        });
                        $("#magento-unit-test-user-list").find(".magento-unit-test-user-list-view").html(html);
                        $("#magento-unit-test-user-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        $(document).on('click', '.load-module-unit-test-remark', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: "{{ route('magento_module.unit-test-remark-history')}}",
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
                                        <td> ${v.old_unit_test_remark ? v.old_unit_test_remark : ''} </td>
                                        <td> ${v.new_unit_test_remark ? v.new_unit_test_remark : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${new Date(v.created_at).toISOString().slice(0, 10)} </td>
                                    </tr>`;
                        });
                        $("#magento-unit-test-remark-list").find(".magento-unit-test-remark-list-view").html(html);
                        $("#magento-unit-test-remark-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        $(document).on('click', '.load-unit-test-status-history', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: "{{ route('magento_module.unit-status-history')}}",
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
                                        <td> ${v.old_test_status ? v.old_test_status.unit_test_status_name : ''} </td>
                                        <td> ${v.new_test_status ? v.new_test_status.unit_test_status_name : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${new Date(v.created_at).toISOString().slice(0, 10)} </td>
                                    </tr>`;
                        });
                        $("#magento-unit-test-status-list").find(".magento-unit-test-status-list-view").html(html);
                        $("#magento-unit-test-status-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });


        $(document).on('click', '.load-module-unit-m2-remark', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: "{{ route('magento_module.m2-error-remark-history')}}",
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
                                        <td> ${v.old_m2_error_remark ? v.old_m2_error_remark : ''} </td>
                                        <td> ${v.new_m2_error_remark ? v.new_m2_error_remark : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${new Date(v.created_at).toISOString().slice(0, 10)} </td>
                                    </tr>`;
                        });
                        $("#magento-m2-error-remark-list").find(".magento-m2-error-remark-list-view").html(html);
                        $("#magento-m2-error-remark-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        // Load Api Modal
        $(document).on('click', '.show-api-modal', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: `{{ route('magento_module_api_histories.show', '') }}/` + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${v.id } </td>
                                        <td> ${v.resources } </td>
                                        <td> ${v.frequency } </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${getDateByFormat(v.created_at) } </td>
                                    </tr>`;
                        });
                        $("#apiDataShowModal").find(".api-details-data-view").html(html);
                        // $("#blank-modal").find(".modal-title").html(response.title);
                        // $("#blank-modal").find(".modal-body").html(response.data);
                        $("#apiDataShowModal").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        // Load cron job Modal
        $(document).on('click', '.show-cron_job-modal', function() {
            var id = $(this).attr('data-id');
            
            $.ajax({
                method: "GET",
                url: `{{ route('magento_module_cron_job_histories.show', '') }}/` + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${v.id } </td>
                                        <td> ${v.cron_time } </td>
                                        <td> ${v.frequency } </td>
                                        <td> ${v.cpu_memory } </td>
                                        <td> ${v.comments } </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${getDateByFormat(v.created_at) } </td>
                                    </tr>`;
                        });
                        $("#cronJobDataShowModal").find(".cron-job-details-data-view").html(html);
                        // $("#blank-modal").find(".modal-title").html(response.title);
                        // $("#blank-modal").find(".modal-body").html(response.data);
                        $("#cronJobDataShowModal").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });
        
        // Load Js Require Modal
        $(document).on('click', '.show-third_party_js-modal', function() {
            var id = $(this).attr('data-id');

            $.ajax({
                method: "GET",
                url: `{{ route('magento_module_js_require_histories.show', '') }}/` + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${v.id } </td>
                                        <td> ${(v.files_include  == 1)? 'Yes': 'No'  } </td>
                                        <td> ${(v.native_functionality  == 1)? 'Yes': 'No'  } </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${getDateByFormat(v.created_at) } </td>
                                    </tr>`;
                        });
                        $("#JsRequireDataShowModal").find(".js-require-details-data-view").html(html);
                        // $("#blank-modal").find(".modal-title").html(response.title);
                        // $("#blank-modal").find(".modal-body").html(response.data);
                        $("#JsRequireDataShowModal").modal("show");
                        
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        // Load Js Require Modal
        $(document).on('click', '.show-is_customized-modal', function() {
            var id = $(this).attr('data-id');

            $.ajax({
                method: "GET",
                url: `{{ route('magento_module_customized_histories.show', '') }}/` + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${v.id } </td>
                                        <td> ${(v.magento_standards == 1)? 'Yes': 'No' } </td>
                                        <td> ${v.remark } </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${getDateByFormat(v.created_at) } </td>
                                    </tr>`;
                        });
                        $("#isCustomizedDataShowModal").find(".is-customized-details-data-view").html(html);
                        // $("#blank-modal").find(".modal-title").html(response.title);
                        // $("#blank-modal").find(".modal-body").html(response.data);
                        $("#isCustomizedDataShowModal").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        $(document).on('click', '.load-review-standard-history', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: "{{ route('magento_module.review.standard.histories')}}",
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
                                        <td> ${v.review_standard === 0 ? 'No' : v.review_standard === 1 ? 'Yes' : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#review_list").find(".review_histories-list-view").html(html);
                        $("#review_list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });
        
        $(document).on('click', '.load-location-history', function() {
            var id = $(this).attr('data-id');
            var type = $(this).attr('data-type');
            $.ajax({
                method: "GET",
                url: "{{ route('magento_module.location.history')}}",
                dataType: "json",
                data: {
                    id:id,
                    type:type,
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.old_location ? v.old_location.magento_module_locations : ''} </td>
                                        <td> ${v.new_location ? v.new_location.magento_module_locations : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#location-listing").find(".location-listing-view").html(html);
                        $("#location-listing").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        $(document).on('click', '.load-error-type-history', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: "{{ route('magento_module.return_type.history')}}",
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
                                        <td> ${v.old_location ? v.old_location.return_type_name : ''} </td>
                                        <td> ${v.new_location ? v.new_location.return_type_name : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#return-type-listing").find(".return-type-listing-view").html(html);
                        $("#return-type-listing").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        $(document).on('click', '.load-m2-error-status-history', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: "{{ route('magento_module.get-m2-error-status-histories', '')}}/" + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.old_m2_error_status ? v.old_m2_error_status.m2_error_status_name : ''} </td>
                                        <td> ${v.new_m2_error_status ? v.new_m2_error_status.m2_error_status_name : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#m2-error-status-listing").find(".m2-error-status-listing-view").html(html);
                        $("#m2-error-status-listing").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        $(document).on('click', '.show-description-modal', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: "{{ route('magento_module.description.history')}}",
                dataType: "json",
                data: {
                    id:id,
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${v.id} </td>
                                        <td> <div class="expand-row module-text" style="width: 100%;"><div class="flex  items-center justify-left td-mini-container" title="${v.module_description}">${setStringLength(v.module_description, 50)}</div><div class="flex items-center justify-left td-full-container hidden" title="${v.module_description}">${v.module_description}</div></div> </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#description-history-listing").find(".description-history-listing-view").html(html);
                        $("#description-history-listing").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });
        $(document).on('click', '.show-usedat-modal', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: "{{ route('magento_module.usedat.history')}}",
                dataType: "json",
                data: {
                    id:id,
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${v.id} </td>
                                        <td> <div class="expand-row module-text" style="width: 100%;"><div class="flex  items-center justify-left td-mini-container" title="${v.used_at}">${setStringLength(v.used_at, 50)}</div><div class="flex items-center justify-left td-full-container hidden" title="${v.used_at}">${v.used_at}</div></div> </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#description-history-listing").find(".description-history-listing-view").html(html);
                        $("#description-history-listing").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        // Show History
        $(document).on('click', '.show-magenato_module_history-modal', function() {
            var id = $(this).attr('data-id');

            $.ajax({
                method: "GET",
                url: `{{ route('magento_module_histories.show', '') }}/` + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> <span title="" > ${v.id } </span> </td>
                                        <td> <span title="${(v.module_category !== null) ? v.module_category.category_name : ' - ' }" > ${(v.module_category !== null) ? v.module_category.category_name : ' - ' } </span> </td>
                                        <td> <span title="${(v.store_website !== null) ? v.store_website.website : ' - ' }" > ${(v.store_website !== null) ? setStringLength(v.store_website.website) : ' - ' } </span> </td>
                                        <td> <span title="${ v.module }" > ${ v.module } </span> </td>
                                        <td> <span title="${ v.module_description }" > ${ setStringLength(v.module_description) } </span> </td>
                                        <td> <span title="${ v.current_version }" > ${ v.current_version } </span> </td>
                                        <td> <span title="${(v.module_type_data !== null) ? v.module_type_data.magento_module_type : ' - ' }" > ${(v.module_type_data !== null) ? v.module_type_data.magento_module_type : ' - ' } </span> </td>
                                        <td> <span title="${(v.task_status_data !== null) ? v.task_status_data.name : ' - ' }" > ${(v.task_status_data !== null) ? v.task_status_data.name : ' - ' } </span> </td>
                                        <td> <span title="${(v.is_sql == 1)? 'Yes': 'No' }" > ${(v.is_sql == 1)? 'Yes': 'No' } </span> </td>
                                        <td> <span title="${(v.api == 1)? 'Yes': 'No' }" > ${(v.api == 1)? 'Yes': 'No' } </span> </td>
                                        <td> <span title="${(v.cron_job == 1)? 'Yes': 'No' }" > ${(v.cron_job == 1)? 'Yes': 'No' } </span> </td>
                                        <td> <span title="${(v.is_third_party_plugin == 1)? 'Yes': 'No' }" > ${(v.is_third_party_plugin == 1)? 'Yes': 'No' } </span> </td>
                                        <td> <span title="${(v.is_third_party_js == 1)? 'Yes': 'No' }" > ${(v.is_third_party_js == 1)? 'Yes': 'No' } </span> </td>
                                        <td> <span title="${(v.is_customized == 1)? 'Yes': 'No' }" > ${(v.is_customized == 1)? 'Yes': 'No' } </span> </td>
                                        <td> <span title="${(v.is_js_css == 1)? 'Yes': 'No' }" > ${(v.is_js_css == 1)? 'Yes': 'No' } </span> </td>
                                        <td> <span title="${v.payment_status }" > ${v.payment_status } </span> </td>
                                        <td> <span title="${(v.developer_name_data !== null) ? v.developer_name_data.name : ' - ' }" > ${(v.developer_name_data !== null) ? v.developer_name_data.name : ' - ' } </span> </td>
                                        <td> <span title="${(v.user !== null) ? v.user.name : ' - ' }" > ${(v.user !== null) ? v.user.name : ' - ' } </span> </td>
                                        <td> <span title="" > ${getDateByFormat(v.created_at) } </span> </td>
                                    </tr>`;
                        });
                        $("#magentoModuleHistoryShowModal").find(".js-magento-module-history-data-view").html(html);
                        // $("#blank-modal").find(".modal-title").html(response.title);
                        // $("#blank-modal").find(".modal-body").html(response.data);
                        $("#magentoModuleHistoryShowModal").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });
        $(document).on('click', '.set-remark', function() {
            var id = $(this).attr('data-mm_id');
            $.ajax({
                type: "POST",
                url: "{{route('task.create.get.remark')}}",
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    task_id : task_id,
                    remark : remark,
                    type : "TASK",
                },
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function (response) {
                if(response.code == 200) {
                    $("#loading-image").hide();
                    $("#preview-task-create-get-modal").modal("show");
                    $(".task-create-get-list-view").html(response.data);
                    $('.remark_pop').val("");
                    toastr['success'](response.message);
                }else{
                    $("#loading-image").hide();
                    $("#preview-task-create-get-modal").modal("show");
                    $(".task-create-get-list-view").html("");
                    toastr['error'](response.message);
                }
                
            }).fail(function (response) {
                $("#loading-image").hide();
                $("#preview-task-create-get-modal").modal("show");
                $(".task-create-get-list-view").html("");
                toastr['error'](response.message);
            });
        });
        $(document).on("click",".copy_remark",function(e) {
            var thiss = $(this);
            var remark_text = thiss.data('remark_text');
            copyToClipboard(remark_text);
            /* Alert the copied text */
            toastr['success']("Copied the text: " + remark_text);
            
        });
        function copyToClipboard(text) {
            var sampleTextarea = document.createElement("textarea");
            document.body.appendChild(sampleTextarea);
            sampleTextarea.value = text; //save main text in it
            sampleTextarea.select(); //select textarea contenrs
            document.execCommand("copy");
            document.body.removeChild(sampleTextarea);
        }
        $(document).on('change', '.edit_mm', function() {
            var  column = $(this).attr('name');
            var value = $(this).val();
            var data_id = $(this).parents('tr').find('.data_id').val();
            
            $.ajax({
                type: "POST",
                url: "{{route('magento_module.update.option')}}",
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
                     oTable.draw();
                    toastr['success'](response.message);
                }else{
                    $("#loading-image").hide();
                    oTable.draw();
                    toastr['error'](response.message);
                }
                
            }).fail(function (response) {
                $("#loading-image").hide();
                oTable.draw();
                toastr['error'](response.message);
            });
        });
        $( document ).ready(function() {
            $(document).on('click', '.expand-row', function () {
                var selection = window.getSelection();
                if (selection.toString().length === 0) {
                    $(this).find('.td-mini-container').toggleClass('hidden');
                    $(this).find('.td-full-container').toggleClass('hidden');
                }
            });
		});
    </script>

@endsection
