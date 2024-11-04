<div id="scdatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('seo.company.column.update') }}" method="POST" id="seo-company-column-update">
                @csrf
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Column Name</b></td>
                            <td class="text-center"><b>hide</b></td>
                        </tr>
                        <div id="columnVisibilityControls">
                            <tr>
                                <td>#</td>
                                <td>
                                    <input type="checkbox" value="#" id="#" name="column_sc[]" @if (!empty($dynamicColumnsToShowsc) && in_array('#', $dynamicColumnsToShowsc)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Type</td>
                                <td>
                                    <input type="checkbox" value="Type" id="Type" name="column_sc[]" @if (!empty($dynamicColumnsToShowsc) && in_array('Type', $dynamicColumnsToShowsc)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Website</td>
                                <td>
                                    <input type="checkbox" value="Website" id="Website" name="column_sc[]" @if (!empty($dynamicColumnsToShowsc) && in_array('Website', $dynamicColumnsToShowsc)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>DA</td>
                                <td>
                                    <input type="checkbox" value="DA" id="DA" name="column_sc[]" @if (!empty($dynamicColumnsToShowsc) && in_array('DA', $dynamicColumnsToShowsc)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>PA</td>
                                <td>
                                    <input type="checkbox" value="PA" id="PA" name="column_sc[]" @if (!empty($dynamicColumnsToShowsc) && in_array('PA', $dynamicColumnsToShowsc)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>SS</td>
                                <td>
                                    <input type="checkbox" value="SS" id="SS" name="column_sc[]" @if (!empty($dynamicColumnsToShowsc) && in_array('SS', $dynamicColumnsToShowsc)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>User</td>
                                <td>
                                    <input type="checkbox" value="User" id="User" name="column_sc[]" @if (!empty($dynamicColumnsToShowsc) && in_array('User', $dynamicColumnsToShowsc)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Username</td>
                                <td>
                                    <input type="checkbox" value="Username" id="Username" name="column_sc[]" @if (!empty($dynamicColumnsToShowsc) && in_array('Username', $dynamicColumnsToShowsc)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Password</td>
                                <td>
                                    <input type="checkbox" value="Password" id="Password" name="column_sc[]" @if (!empty($dynamicColumnsToShowsc) && in_array('Password', $dynamicColumnsToShowsc)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Live link</td>
                                <td>
                                    <input type="checkbox" value="Live link" id="Live link" name="column_sc[]" @if (!empty($dynamicColumnsToShowsc) && in_array('Live link', $dynamicColumnsToShowsc)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Date</td>
                                <td>
                                    <input type="checkbox" value="Date" id="Date" name="column_sc[]" @if (!empty($dynamicColumnsToShowsc) && in_array('Date', $dynamicColumnsToShowsc)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>
                                    <input type="checkbox" value="Status" id="Status" name="column_sc[]" @if (!empty($dynamicColumnsToShowsc) && in_array('Status', $dynamicColumnsToShowsc)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Action</td>
                                <td>
                                    <input type="checkbox" value="Action" id="Action" name="column_sc[]" @if (!empty($dynamicColumnsToShowsc) && in_array('Action', $dynamicColumnsToShowsc)) checked @endif>
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