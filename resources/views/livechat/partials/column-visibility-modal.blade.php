<div id="ltdatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('livechat.column.update') }}" method="POST" id="livechat-column-update">
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
                                    <input type="checkbox" value="Checkbox" id="Checkbox" name="column_lt[]" @if (!empty($dynamicColumnsToShowLt) && in_array('Checkbox', $dynamicColumnsToShowLt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Id</td>
                                <td>
                                    <input type="checkbox" value="Id" id="Id" name="column_lt[]" @if (!empty($dynamicColumnsToShowLt) && in_array('Id', $dynamicColumnsToShowLt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Source</td>
                                <td>
                                    <input type="checkbox" value="Source" id="Source" name="column_lt[]" @if (!empty($dynamicColumnsToShowLt) && in_array('Source', $dynamicColumnsToShowLt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Name</td>
                                <td>
                                    <input type="checkbox" value="Name" id="Name" name="column_lt[]" @if (!empty($dynamicColumnsToShowLt) && in_array('Name', $dynamicColumnsToShowLt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>
                                    <input type="checkbox" value="Email" id="Email" name="column_lt[]" @if (!empty($dynamicColumnsToShowLt) && in_array('Email', $dynamicColumnsToShowLt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Subject</td>
                                <td>
                                    <input type="checkbox" value="Subject" id="Subject" name="column_lt[]" @if (!empty($dynamicColumnsToShowLt) && in_array('Subject', $dynamicColumnsToShowLt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Message</td>
                                <td>
                                    <input type="checkbox" value="Message" id="Message" name="column_lt[]" @if (!empty($dynamicColumnsToShowLt) && in_array('Message', $dynamicColumnsToShowLt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Asg name</td>
                                <td>
                                    <input type="checkbox" value="Asg name" id="Asg name" name="column_lt[]" @if (!empty($dynamicColumnsToShowLt) && in_array('Asg name', $dynamicColumnsToShowLt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Brand</td>
                                <td>
                                    <input type="checkbox" value="Brand" id="Brand" name="column_lt[]" @if (!empty($dynamicColumnsToShowLt) && in_array('Brand', $dynamicColumnsToShowLt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Country</td>
                                <td>
                                    <input type="checkbox" value="Country" id="Country" name="column_lt[]" @if (!empty($dynamicColumnsToShowLt) && in_array('Country', $dynamicColumnsToShowLt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Ord no</td>
                                <td>
                                    <input type="checkbox" value="Ord no" id="Ord no" name="column_lt[]" @if (!empty($dynamicColumnsToShowLt) && in_array('Ord no', $dynamicColumnsToShowLt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Ph no</td>
                                <td>
                                    <input type="checkbox" value="Ph no" id="Ph no" name="column_lt[]" @if (!empty($dynamicColumnsToShowLt) && in_array('Ph no', $dynamicColumnsToShowLt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Msg Box</td>
                                <td>
                                    <input type="checkbox" value="Msg Box" id="Msg Box" name="column_lt[]" @if (!empty($dynamicColumnsToShowLt) && in_array('Msg Box', $dynamicColumnsToShowLt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Images</td>
                                <td>
                                    <input type="checkbox" value="Images" id="Images" name="column_lt[]" @if (!empty($dynamicColumnsToShowLt) && in_array('Images', $dynamicColumnsToShowLt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Resolution Date</td>
                                <td>
                                    <input type="checkbox" value="Resolution Date" id="Resolution Date" name="column_lt[]" @if (!empty($dynamicColumnsToShowLt) && in_array('Resolution Date', $dynamicColumnsToShowLt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>
                                    <input type="checkbox" value="Status" id="Status" name="column_lt[]" @if (!empty($dynamicColumnsToShowLt) && in_array('Status', $dynamicColumnsToShowLt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Created</td>
                                <td>
                                    <input type="checkbox" value="Created" id="Created" name="column_lt[]" @if (!empty($dynamicColumnsToShowLt) && in_array('Created', $dynamicColumnsToShowLt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Shortcuts</td>
                                <td>
                                    <input type="checkbox" value="Shortcuts" id="Shortcuts" name="column_lt[]" @if (!empty($dynamicColumnsToShowLt) && in_array('Shortcuts', $dynamicColumnsToShowLt)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Action</td>
                                <td>
                                    <input type="checkbox" value="Action" id="Action" name="column_lt[]" @if (!empty($dynamicColumnsToShowLt) && in_array('v', $dynamicColumnsToShowLt)) checked @endif>
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