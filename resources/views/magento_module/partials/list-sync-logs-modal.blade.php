<div id="sync-logs-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Magento Modules Logs</h5>
                <br>

                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#synclogscolumnvisibilityList" style="margin-left: 20px;">Column Visiblity</button>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <form method="get" id="screen_cast_search" style="margin-left:auto">
                            <div class="row">
                                <div class="col-lg-4">
                                    <label style=" width: 100%;">Module Name</label>
                                    {{ html()->select('module_name_sync', $allMagentoModules, request()->get('module_name_sync'))->placeholder('Module Name')->class('form-control')->id('module_name_sync') }} 
                                </div>    

                                <div class="col-lg-4">
                                    <label style=" width: 100%;">Date</label>
                                    <input type="date" name="selected_date" style="width: 100%;" id="selected_date">   
                                </div>    

                                <div class="col-lg-4">
                                    <button type="button" class="btn btn-image search" style="margin-top: 22px;" onclick="changeMagnetoSyncLogs(1)">
                                        <img src="{{ asset('images/search.png') }}" alt="Search">
                                    </button>

                                    <a href="{{ route('magento_module_listing') }}" class="btn btn-image" id="" style="margin-top: 22px;">
                                        <img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                </br>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-12" id="list-sync-logs-modal-html">
                                <table class="table table-sm table-bordered" id="sync_logs_list_table">
                                    <thead>
                                        <tr>
                                            @if(!empty($dynamicColumnsToShow))
                                                @if (!in_array('Id', $dynamicColumnsToShow))
                                                    <th>Id</th>
                                                @endif

                                                @if (!in_array('Module Name', $dynamicColumnsToShow))
                                                    <th>Module Name</th>
                                                @endif

                                                @if (!in_array('Command', $dynamicColumnsToShow))
                                                    <th>Command</th>
                                                @endif

                                                @if (!in_array('Job Id', $dynamicColumnsToShow))
                                                    <th>Job Id</th>
                                                @endif

                                                @if (!in_array('Status', $dynamicColumnsToShow))
                                                    <th>Status</th>
                                                @endif

                                                @if (!in_array('Response', $dynamicColumnsToShow))
                                                    <th>Response</th>
                                                @endif

                                                @if (!in_array('Created At', $dynamicColumnsToShow))
                                                    <th>Created At</th>
                                                @endif

                                                @if (!in_array('Updated At', $dynamicColumnsToShow))
                                                    <th>Updated At</th>
                                                @endif
                                            @else
                                                <th>Id</th>
                                                <th>Module Name</th>
                                                <th>Command</th>
                                                <th>Job Id</th>
                                                <th>Status</th>
                                                <th>Response</th>
                                                <th>Created At</th>
                                                <th>Updated At</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody id="sync_logs_list_table_data">
                                       
                                    </tbody>
                                </table>
                                <!-- Pagination links -->
                                <div class="pagination-container-sync"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>