<div id="vendorsdatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('vendors.column.update') }}" method="POST" id="vendors-column-update">
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
                                    <input type="checkbox" value="ID" id="ID" name="column_vendors[]" @if (!empty($dynamicColumnsToShowVendors) && in_array('ID', $dynamicColumnsToShowVendors)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>WhatsApp</td>
                                <td>
                                    <input type="checkbox" value="WhatsApp" id="WhatsApp" name="column_vendors[]" @if (!empty($dynamicColumnsToShowVendors) && in_array('WhatsApp', $dynamicColumnsToShowVendors)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Category</td>
                                <td>
                                    <input type="checkbox" value="Category" id="Category" name="column_vendors[]" @if (!empty($dynamicColumnsToShowVendors) && in_array('Category', $dynamicColumnsToShowVendors)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>
                                    <input type="checkbox" value="Status" id="Status" name="column_vendors[]" @if (!empty($dynamicColumnsToShowVendors) && in_array('Status', $dynamicColumnsToShowVendors)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Name</td>
                                <td>
                                    <input type="checkbox" value="Name" id="Name" name="column_vendors[]" @if (!empty($dynamicColumnsToShowVendors) && in_array('Name', $dynamicColumnsToShowVendors)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Phone</td>
                                <td>
                                    <input type="checkbox" value="Phone" id="Phone" name="column_vendors[]" @if (!empty($dynamicColumnsToShowVendors) && in_array('Phone', $dynamicColumnsToShowVendors)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>
                                    <input type="checkbox" value="Email" id="Email" name="column_vendors[]" @if (!empty($dynamicColumnsToShowVendors) && in_array('Email', $dynamicColumnsToShowVendors)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Communication</td>
                                <td>
                                    <input type="checkbox" value="Communication" id="Communication" name="column_vendors[]" @if (!empty($dynamicColumnsToShowVendors) && in_array('Communication', $dynamicColumnsToShowVendors)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Remarks</td>
                                <td>
                                    <input type="checkbox" value="Remarks" id="Remarks" name="column_vendors[]" @if (!empty($dynamicColumnsToShowVendors) && in_array('Remarks', $dynamicColumnsToShowVendors)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Type</td>
                                <td>
                                    <input type="checkbox" value="Type" id="Type" name="column_vendors[]" @if (!empty($dynamicColumnsToShowVendors) && in_array('Type', $dynamicColumnsToShowVendors)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Framework</td>
                                <td>
                                    <input type="checkbox" value="Framework" id="Framework" name="column_vendors[]" @if (!empty($dynamicColumnsToShowVendors) && in_array('Framework', $dynamicColumnsToShowVendors)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Created Date</td>
                                <td>
                                    <input type="checkbox" value="Created Date" id="Created_Date" name="column_vendors[]" @if (!empty($dynamicColumnsToShowVendors) && in_array('Created Date', $dynamicColumnsToShowVendors)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Price</td>
                                <td>
                                    <input type="checkbox" value="Price" id="Price" name="column_vendors[]" @if (!empty($dynamicColumnsToShowVendors) && in_array('Price', $dynamicColumnsToShowVendors)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Action</td>
                                <td>
                                    <input type="checkbox" value="Action" id="Action" name="column_vendors[]" @if (!empty($dynamicColumnsToShowVendors) && in_array('Action', $dynamicColumnsToShowVendors)) checked @endif>
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