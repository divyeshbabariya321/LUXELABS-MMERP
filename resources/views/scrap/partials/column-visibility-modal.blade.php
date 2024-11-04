<div id="scrapdatatablecolumnvisibilityList" class="modal" role="dialog" aria-labelledby="" >
    <div class="modal-dialog" role="document">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('scrap.column.update') }}" method="POST" id="scrap-column-update">
                    @csrf
                    <div class="form-group col-md-12">
                        <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                            <tr>
                                <td class="text-center"><b>Column Name</b></td>
                                <td class="text-center"><b>hide</b></td>
                            </tr>
                            <div id="columnVisibilityControls">
                                <tr>
                                    <td>Checkbox</td>
                                    <td>
                                        <input type="checkbox" value="Checkbox" id="Checkbox" name="column_s[]" @if (!empty($dynamicColumnsToShows) && in_array('Checkbox', $dynamicColumnsToShows)) checked @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#</td>
                                    <td>
                                        <input type="checkbox" value="#" id="#" name="column_s[]" @if (!empty($dynamicColumnsToShows) && in_array('#', $dynamicColumnsToShows)) checked @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Supplier</td>
                                    <td>
                                        <input type="checkbox" value="Supplier" id="Supplier" name="column_s[]" @if (!empty($dynamicColumnsToShows) && in_array('Supplier', $dynamicColumnsToShows)) checked @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Server ID</td>
                                    <td>
                                        <input type="checkbox" value="Server ID" id="Server ID" name="column_s[]" @if (!empty($dynamicColumnsToShows) && in_array('Server ID', $dynamicColumnsToShows)) checked @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Auto Restart</td>
                                    <td>
                                        <input type="checkbox" value="Auto Restart" id="Auto Restart" name="column_s[]" @if (!empty($dynamicColumnsToShows) && in_array('Auto Restart', $dynamicColumnsToShows)) checked @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Run Time</td>
                                    <td>
                                        <input type="checkbox" value="Run Time" id="Run Time" name="column_s[]" @if (!empty($dynamicColumnsToShows) && in_array('Run Time', $dynamicColumnsToShows)) checked @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Start Scrap</td>
                                    <td>
                                        <input type="checkbox" value="Start Scrap" id="Start Scrap" name="column_s[]" @if (!empty($dynamicColumnsToShows) && in_array('Start Scrap', $dynamicColumnsToShows)) checked @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Stock</td>
                                    <td>
                                        <input type="checkbox" value="Stock" id="Stock" name="column_s[]" @if (!empty($dynamicColumnsToShows) && in_array('Stock', $dynamicColumnsToShows)) checked @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>URL Count</td>
                                    <td>
                                        <input type="checkbox" value="URL Count" id="URL Count" name="column_s[]" @if (!empty($dynamicColumnsToShows) && in_array('URL Count', $dynamicColumnsToShows)) checked @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>YDay New</td>
                                    <td>
                                        <input type="checkbox" value="YDay New" id="YDay New" name="column_s[]" @if (!empty($dynamicColumnsToShows) && in_array('YDay New', $dynamicColumnsToShows)) checked @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>URL Count Scrap</td>
                                    <td>
                                        <input type="checkbox" value="URL Count Scrap" id="URL Count Scrap" name="column_s[]" @if (!empty($dynamicColumnsToShows) && in_array('URL Count Scrap', $dynamicColumnsToShows)) checked @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>URLs</td>
                                    <td>
                                        <input type="checkbox" value="URLs" id="URLs" name="column_s[]" @if (!empty($dynamicColumnsToShows) && in_array('URLs', $dynamicColumnsToShows)) checked @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td>
                                        <input type="checkbox" value="Status" id="Status" name="column_s[]" @if (!empty($dynamicColumnsToShows) && in_array('Status', $dynamicColumnsToShows)) checked @endif>
                                    </td>
                                </tr>
                                <!-- <tr>
                                    <td>Remarks</td>
                                    <td>
                                        <input type="checkbox" value="Remarks" id="Remarks" name="column_s[]" @if (!empty($dynamicColumnsToShows) && in_array('Remarks', $dynamicColumnsToShows)) checked @endif>
                                    </td>
                                </tr> -->
                                <tr>
                                    <td>Full scrap</td>
                                    <td>
                                        <input type="checkbox" value="Full scrap" id="Full scrap" name="column_s[]" @if (!empty($dynamicColumnsToShows) && in_array('Full scrap', $dynamicColumnsToShows)) checked @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Scraper Duration</td>
                                    <td>
                                        <input type="checkbox" value="Scraper Duration" id="Scraper Duration" name="column_s[]" @if (!empty($dynamicColumnsToShows) && in_array('Scraper Duration', $dynamicColumnsToShows)) checked @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Suppiier Inventory</td>
                                    <td>
                                        <input type="checkbox" value="Suppiier Inventory" id="Suppiier Inventory" name="column_s[]" @if (!empty($dynamicColumnsToShows) && in_array('Suppiier Inventory', $dynamicColumnsToShows)) checked @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Date Last Product Added</td>
                                    <td>
                                        <input type="checkbox" value="Date Last Product Added" id="Date Last Product Added" name="column_s[]" @if (!empty($dynamicColumnsToShows) && in_array('Date Last Product Added', $dynamicColumnsToShows)) checked @endif>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Functions</td>
                                    <td>
                                        <input type="checkbox" value="Functions" id="Functions" name="column_s[]" @if (!empty($dynamicColumnsToShows) && in_array('Functions', $dynamicColumnsToShows)) checked @endif>
                                    </td>
                                </tr>
                            </div>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
