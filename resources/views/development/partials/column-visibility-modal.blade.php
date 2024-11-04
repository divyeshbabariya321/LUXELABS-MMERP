<div id="dscolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('ds.column.update') }}" method="POST" id="postman-column-update">
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
                                    <input type="checkbox" value="ID" id="ID" name="column_ds[]" @if (!empty($dynamicColumnsToShowDs) && in_array('ID', $dynamicColumnsToShowDs)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>MODULE</td>
                                <td>
                                    <input type="checkbox" value="MODULE" id="MODULE" name="column_ds[]" @if (!empty($dynamicColumnsToShowDs) && in_array('MODULE', $dynamicColumnsToShowDs)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Assigned To</td>
                                <td>
                                    <input type="checkbox" value="Assigned To" id="Assigned_To" name="column_ds[]" @if (!empty($dynamicColumnsToShowDs) && in_array('Assigned To', $dynamicColumnsToShowDs)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Lead</td>
                                <td>
                                    <input type="checkbox" value="Lead" id="Lead" name="column_ds[]" @if (!empty($dynamicColumnsToShowDs) && in_array('Lead', $dynamicColumnsToShowDs)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Communication</td>
                                <td>
                                    <input type="checkbox" value="Communication" id="Communication" name="column_ds[]" @if (!empty($dynamicColumnsToShowDs) && in_array('Communication', $dynamicColumnsToShowDs)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Send To</td>
                                <td>
                                    <input type="checkbox" value="Send To" id="Send_To" name="column_ds[]" @if (!empty($dynamicColumnsToShowDs) && in_array('Send To', $dynamicColumnsToShowDs)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>
                                    <input type="checkbox" value="Status" id="Status" name="column_ds[]" @if (!empty($dynamicColumnsToShowDs) && in_array('Status', $dynamicColumnsToShowDs)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Estimated Time</td>
                                <td>
                                    <input type="checkbox" value="Estimated Time" id="Estimated_Time" name="column_ds[]" @if (!empty($dynamicColumnsToShowDs) && in_array('Estimated Time', $dynamicColumnsToShowDs)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Estimated Datetime</td>
                                <td>
                                    <input type="checkbox" value="Estimated Start Datetime" id="Estimated_Start_Datetime" name="column_ds[]" @if (!empty($dynamicColumnsToShowDs) && in_array('Estimated Start Datetime', $dynamicColumnsToShowDs)) checked @endif>
                                </td>
                            </tr>
                            <!-- <tr>
                                <td>Estimated End Datetime</td>
                                <td>
                                    <input type="checkbox" value="Estimated End Datetime" id="Estimated_End_Datetime" name="column_ds[]" @if (!empty($dynamicColumnsToShowDs) && in_array('Estimated End Datetime', $dynamicColumnsToShowDs)) checked @endif>
                                </td>
                            </tr> -->
                            <tr>
                                <td>Shortcuts</td>
                                <td>
                                    <input type="checkbox" value="Shortcuts" id="Shortcuts" name="column_ds[]" @if (!empty($dynamicColumnsToShowDs) && in_array('Shortcuts', $dynamicColumnsToShowDs)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Actions</td>
                                <td>
                                    <input type="checkbox" value="Actions" id="Actions" name="column_ds[]" @if (!empty($dynamicColumnsToShowDs) && in_array('Actions', $dynamicColumnsToShowDs)) checked @endif>
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