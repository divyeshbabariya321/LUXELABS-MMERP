<div id="dlcolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('dl.column.update') }}" method="POST" id="postman-column-update">
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
                                    <input type="checkbox" value="ID" id="ID" name="column_dl[]" @if (!empty($dynamicColumnsToShowDl) && in_array('ID', $dynamicColumnsToShowDl)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Module</td>
                                <td>
                                    <input type="checkbox" value="Module" id="Module" name="column_dl[]" @if (!empty($dynamicColumnsToShowDl) && in_array('Module', $dynamicColumnsToShowDl)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Date</td>
                                <td>
                                    <input type="checkbox" value="Date" id="Date" name="column_dl[]" @if (!empty($dynamicColumnsToShowDl) && in_array('Date', $dynamicColumnsToShowDl)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Subject</td>
                                <td>
                                    <input type="checkbox" value="Subject" id="Subject" name="column_dl[]" @if (!empty($dynamicColumnsToShowDl) && in_array('Subject', $dynamicColumnsToShowDl)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Communication</td>
                                <td>
                                    <input type="checkbox" value="Communication" id="Communication" name="column_dl[]" @if (!empty($dynamicColumnsToShowDl) && in_array('Communication', $dynamicColumnsToShowDl)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Est Completion Time</td>
                                <td>
                                    <input type="checkbox" value="Est Completion Time" id="Est_Completion_Time" name="column_dl[]" @if (!empty($dynamicColumnsToShowDl) && in_array('Est Completion Time', $dynamicColumnsToShowDl)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Est Completion Date</td>
                                <td>
                                    <input type="checkbox" value="Est Completion Date" id="Est_Completion_Date" name="column_dl[]" @if (!empty($dynamicColumnsToShowDl) && in_array('Est Completion Date', $dynamicColumnsToShowDl)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Tracked Time</td>
                                <td>
                                    <input type="checkbox" value="Tracked Time" id="Tracked_Time" name="column_dl[]" @if (!empty($dynamicColumnsToShowDl) && in_array('Tracked Time', $dynamicColumnsToShowDl)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Developers</td>
                                <td>
                                    <input type="checkbox" value="Developers" id="Developers" name="column_dl[]" @if (!empty($dynamicColumnsToShowDl) && in_array('Developers', $dynamicColumnsToShowDl)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>
                                    <input type="checkbox" value="Status" id="Status" name="column_dl[]" @if (!empty($dynamicColumnsToShowDl) && in_array('Status', $dynamicColumnsToShowDl)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Cost</td>
                                <td>
                                    <input type="checkbox" value="Cost" id="Cost" name="column_dl[]" @if (!empty($dynamicColumnsToShowDl) && in_array('Cost', $dynamicColumnsToShowDl)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Milestone</td>
                                <td>
                                    <input type="checkbox" value="Milestone" id="Milestone" name="column_dl[]" @if (!empty($dynamicColumnsToShowDl) && in_array('Milestone', $dynamicColumnsToShowDl)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Estimated Time</td>
                                <td>
                                    <input type="checkbox" value="Estimated Time" id="Estimated_Time" name="column_dl[]" @if (!empty($dynamicColumnsToShowDl) && in_array('Estimated Time', $dynamicColumnsToShowDl)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Estimated Datetime</td>
                                <td>
                                    <input type="checkbox" value="Estimated Start Datetime" id="Estimated_Start_Datetime" name="column_dl[]" @if (!empty($dynamicColumnsToShowDl) && in_array('Estimated Start Datetime', $dynamicColumnsToShowDl)) checked @endif>
                                </td>
                            </tr>
                            <!-- <tr>
                                <td>Estimated End Datetime</td>
                                <td>
                                    <input type="checkbox" value="Estimated End Datetime" id="Estimated_End_Datetime" name="column_dl[]" @if (!empty($dynamicColumnsToShowDl) && in_array('Estimated End Datetime', $dynamicColumnsToShowDl)) checked @endif>
                                </td>
                            </tr> -->
                            <tr>
                                <td>Shortcuts</td>
                                <td>
                                    <input type="checkbox" value="Shortcuts" id="Shortcuts" name="column_dl[]" @if (!empty($dynamicColumnsToShowDl) && in_array('Shortcuts', $dynamicColumnsToShowDl)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Actions</td>
                                <td>
                                    <input type="checkbox" value="Actions" id="Actions" name="column_dl[]" @if (!empty($dynamicColumnsToShowDl) && in_array('Actions', $dynamicColumnsToShowDl)) checked @endif>
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