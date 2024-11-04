<div id="asdatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('assetsmanager.column.update') }}" method="POST" id="assetsmanager-column-update">
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
                                    <input type="checkbox" value="ID" id="ID" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('ID', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Name</td>
                                <td>
                                    <input type="checkbox" value="Name" id="Name" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('Name', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Capacity</td>
                                <td>
                                    <input type="checkbox" value="Capacity" id="Capacity" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('Capacity', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>User Name</td>
                                <td>
                                    <input type="checkbox" value="User Name" id="User_Name" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('User Name', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Pwd</td>
                                <td>
                                    <input type="checkbox" value="Pwd" id="Pwd" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('Pwd', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Ast Type</td>
                                <td>
                                    <input type="checkbox" value="Ast Type" id="Ast_Type" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('Ast Type', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Cat</td>
                                <td>
                                    <input type="checkbox" value="Cat" id="Cat" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('Cat', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Pro Name</td>
                                <td>
                                    <input type="checkbox" value="Pro Name" id="Pro_Name" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('Pro Name', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Pur Type</td>
                                <td>
                                    <input type="checkbox" value="Pur Type" id="Pur_Type" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('Pur Type', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Pymt Cycle</td>
                                <td>
                                    <input type="checkbox" value="Pymt Cycle" id="Pymt_Cycle" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('Pymt Cycle', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Due Date</td>
                                <td>
                                    <input type="checkbox" value="Due Date" id="Due_Date" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('Due Date', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Amount</td>
                                <td>
                                    <input type="checkbox" value="Amount" id="Amount" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('Amount', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Currency</td>
                                <td>
                                    <input type="checkbox" value="Currency" id="Currency" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('Currency', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Location</td>
                                <td>
                                    <input type="checkbox" value="Location" id="Location" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('Location', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Usage</td>
                                <td>
                                    <input type="checkbox" value="Usage" id="Usage" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('Usage', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Link</td>
                                <td>
                                    <input type="checkbox" value="Link" id="Link" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('Link', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>IP</td>
                                <td>
                                    <input type="checkbox" value="IP" id="IP" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('IP', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>IP Name</td>
                                <td>
                                    <input type="checkbox" value="IP Name" id="IP_Name" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('IP Name', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Account Name</td>
                                <td>
                                    <input type="checkbox" value="Account Name" id="Account_Name" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('Account Name', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Account password</td>
                                <td>
                                    <input type="checkbox" value="Account password" id="Account_password" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('Account password', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Monit Api URL</td>
                                <td>
                                    <input type="checkbox" value="Monit Api URL" id="Monit_Api_URL" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('Monit Api URL', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Monit Api Username</td>
                                <td>
                                    <input type="checkbox" value="Monit Api Username" id="Monit_Api_Username" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('Monit Api Username', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Monit Api Password</td>
                                <td>
                                    <input type="checkbox" value="Monit Api Password" id="Monit_Api_Password" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('Monit Api Password', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>VNC Ip</td>
                                <td>
                                    <input type="checkbox" value="VNC Ip" id="VNC_Ip" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('VNC Ip', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>VNC Port</td>
                                <td>
                                    <input type="checkbox" value="VNC Port" id="VNC_Port" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('VNC Port', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>VNC Password</td>
                                <td>
                                    <input type="checkbox" value="VNC Password" id="VNC_Password" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('VNC Password', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>Created By</td>
                                <td>
                                    <input type="checkbox" value="Created By" id="Created_By" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('Created By', $dynamicColumnsToShowAM)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Action</td>
                                <td>
                                    <input type="checkbox" value="Action" id="Action" name="column_assetsmanager[]" @if (!empty($dynamicColumnsToShowAM) && in_array('Action', $dynamicColumnsToShowAM)) checked @endif>
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