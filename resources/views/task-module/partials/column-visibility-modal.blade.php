<div id="taskcolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('task.column.update') }}" method="POST" id="postman-column-update">
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
                                    <input type="checkbox" value="ID" id="ID" name="column_task[]" @if (!empty($dynamicColumnsToShowTask) && in_array('ID', $dynamicColumnsToShowTask)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Date</td>
                                <td>
                                    <input type="checkbox" value="Date" id="Date" name="column_task[]" @if (!empty($dynamicColumnsToShowTask) && in_array('Date', $dynamicColumnsToShowTask)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Category</td>
                                <td>
                                    <input type="checkbox" value="Category" id="Category" name="column_task[]" @if (!empty($dynamicColumnsToShowTask) && in_array('Category', $dynamicColumnsToShowTask)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Task Subject</td>
                                <td>
                                    <input type="checkbox" value="Task Subject" id="Task_Subject" name="column_task[]" @if (!empty($dynamicColumnsToShowTask) && in_array('Task Subject', $dynamicColumnsToShowTask)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Assign To</td>
                                <td>
                                    <input type="checkbox" value="Assign To" id="Assign_To" name="column_task[]" @if (!empty($dynamicColumnsToShowTask) && in_array('Assign To', $dynamicColumnsToShowTask)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>
                                    <input type="checkbox" value="Status" id="Status" name="column_task[]" @if (!empty($dynamicColumnsToShowTask) && in_array('Status', $dynamicColumnsToShowTask)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Tracked time</td>
                                <td>
                                    <input type="checkbox" value="Tracked time" id="Tracked_time" name="column_task[]" @if (!empty($dynamicColumnsToShowTask) && in_array('Tracked time', $dynamicColumnsToShowTask)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Communication</td>
                                <td>
                                    <input type="checkbox" value="Communication" id="Communication" name="column_task[]" @if (!empty($dynamicColumnsToShowTask) && in_array('Communication', $dynamicColumnsToShowTask)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Estimated Time</td>
                                <td>
                                    <input type="checkbox" value="Estimated Time" id="Estimated_Time" name="column_task[]" @if (!empty($dynamicColumnsToShowTask) && in_array('Estimated Time', $dynamicColumnsToShowTask)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Estimated Datetime</td>
                                <td>
                                    <input type="checkbox" value="Estimated Start Datetime" id="Estimated_Start_Datetime" name="column_task[]" @if (!empty($dynamicColumnsToShowTask) && in_array('Estimated Start Datetime', $dynamicColumnsToShowTask)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Shortcuts</td>
                                <td>
                                    <input type="checkbox" value="Shortcuts" id="Shortcuts" name="column_task[]" @if (!empty($dynamicColumnsToShowTask) && in_array('Shortcuts', $dynamicColumnsToShowTask)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>ICON &nbsp; Finished</td>
                                <td>
                                    <input type="checkbox" value="ICON" id="ICON" name="column_task[]" @if (!empty($dynamicColumnsToShowTask) && in_array('ICON', $dynamicColumnsToShowTask)) checked @endif>
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