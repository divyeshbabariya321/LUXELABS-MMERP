<div id="bugdatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('bug-tracking.column.update') }}" method="POST" id="postman-column-update">
                @csrf
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Column Name</b></td>
                            <td class="text-center"><b>hide</b></td>
                        </tr>
                        <div id="columnVisibilityControls">
                            <tr>
                                <td>ID</td>
                                <td>
                                    <input type="checkbox" value="ID" id="ID" name="column_bt[]" @if (!empty($dynamicColumnsToShowbt) && in_array('ID', $dynamicColumnsToShowbt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Date</td>
                                <td>
                                    <input type="checkbox" value="Date" id="Date" name="column_bt[]" @if (!empty($dynamicColumnsToShowbt) && in_array('Date', $dynamicColumnsToShowbt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Summary</td>
                                <td>
                                    <input type="checkbox" value="Summary" id="Summary" name="column_bt[]" @if (!empty($dynamicColumnsToShowbt) && in_array('Summary', $dynamicColumnsToShowbt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Type</td>
                                <td>
                                    <input type="checkbox" value="Type" id="Type" name="column_bt[]" @if (!empty($dynamicColumnsToShowbt) && in_array('Type', $dynamicColumnsToShowbt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Steps to reproduce</td>
                                <td>
                                    <input type="checkbox" value="Steps to reproduce" id="Steps to reproduce" name="column_bt[]" @if (!empty($dynamicColumnsToShowbt) && in_array('Steps to reproduce', $dynamicColumnsToShowbt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Environment</td>
                                <td>
                                    <input type="checkbox" value="Environment" id="Environment" name="column_bt[]" @if (!empty($dynamicColumnsToShowbt) && in_array('Environment', $dynamicColumnsToShowbt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Expected Result</td>
                                <td>
                                    <input type="checkbox" value="Expected Result" id="Expected Result" name="column_bt[]" @if (!empty($dynamicColumnsToShowbt) && in_array('Expected Result', $dynamicColumnsToShowbt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Screenshot / Video url</td>
                                <td>
                                    <input type="checkbox" value="Screenshot / Video url" id="Screenshot / Video url" name="column_bt[]" @if (!empty($dynamicColumnsToShowbt) && in_array('Screenshot / Video url', $dynamicColumnsToShowbt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Created By</td>
                                <td>
                                    <input type="checkbox" value="Created By" id="Created By" name="column_bt[]" @if (!empty($dynamicColumnsToShowbt) && in_array('Created By', $dynamicColumnsToShowbt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Assign to</td>
                                <td>
                                    <input type="checkbox" value="Assign to" id="Assign to" name="column_bt[]" @if (!empty($dynamicColumnsToShowbt) && in_array('Assign to', $dynamicColumnsToShowbt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Severity</td>
                                <td>
                                    <input type="checkbox" value="Severity" id="Severity" name="column_bt[]" @if (!empty($dynamicColumnsToShowbt) && in_array('Severity', $dynamicColumnsToShowbt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>
                                    <input type="checkbox" value="Status" id="Status" name="column_bt[]" @if (!empty($dynamicColumnsToShowbt) && in_array('Status', $dynamicColumnsToShowbt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Module</td>
                                <td>
                                    <input type="checkbox" value="Module" id="Module" name="column_bt[]" @if (!empty($dynamicColumnsToShowbt) && in_array('Module', $dynamicColumnsToShowbt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Communicaton</td>
                                <td>
                                    <input type="checkbox" value="Communicaton" id="Communicaton" name="column_bt[]" @if (!empty($dynamicColumnsToShowbt) && in_array('Communicaton', $dynamicColumnsToShowbt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Website</td>
                                <td>
                                    <input type="checkbox" value="Website" id="Website" name="column_bt[]" @if (!empty($dynamicColumnsToShowbt) && in_array('Website', $dynamicColumnsToShowbt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Action</td>
                                <td>
                                    <input type="checkbox" value="Action" id="Action" name="column_bt[]" @if (!empty($dynamicColumnsToShowbt) && in_array('Action', $dynamicColumnsToShowbt)) checked @endif>
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