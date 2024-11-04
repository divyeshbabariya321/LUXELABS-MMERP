<div id="returnexchangedatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('returnexchange.column.update') }}" method="POST" id="returnexchange-column-update">
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
                                    <input type="checkbox" value="ID" id="ID" name="column_returnexchange[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('ID', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Customer</td>
                                <td>
                                    <input type="checkbox" value="Customer" id="Customer" name="column_returnexchange[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Customer', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Product</td>
                                <td>
                                    <input type="checkbox" value="Product" id="Product" name="column_returnexchange[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Product', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>Website</td>
                                <td>
                                    <input type="checkbox" value="Website" id="Website" name="column_returnexchange[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Website', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>Type</td>
                                <td>
                                    <input type="checkbox" value="Type" id="Type" name="column_returnexchange[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Type', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>Refund</td>
                                <td>
                                    <input type="checkbox" value="Refund" id="Refund" name="column_returnexchange[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Refund', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>Refund Reason</td>
                                <td>
                                    <input type="checkbox" value="Refund Reason" id="Refund Reason" name="column_returnexchange[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Refund Reason', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                               
                            <tr>
                                <td>Status</td>
                                <td>
                                    <input type="checkbox" value="Status" id="Status" name="column_returnexchange[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Status', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Pickup Address</td>
                                <td>
                                    <input type="checkbox" value="Pickup Address" id="Pickup Address" name="column_returnexchange[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Pickup Address', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>DOR</td>
                                <td>
                                    <input type="checkbox" value="DOR" id="DOR" name="column_returnexchange[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('DOR', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>DOI</td>
                                <td>
                                    <input type="checkbox" value="DOI" id="DOI" name="column_returnexchange[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('DOI', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>DOD</td>
                                <td>
                                    <input type="checkbox" value="DOD" id="DOD" name="column_returnexchange[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('DOD', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>Credited</td>
                                <td>
                                    <input type="checkbox" value="Credited" id="Credited" name="column_returnexchange[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Credited', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>Est Refund / Ex. date</td>
                                <td>
                                    <input type="checkbox" value="Est Refund / Ex. date" id="Est Refund / Ex. date" name="column_returnexchange[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Est Refund / Ex. date', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Remarks</td>
                                <td>
                                    <input type="checkbox" value="Remarks" id="Remarks" name="column_returnexchange[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Remarks', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr> 

                            <tr>
                                <td>Created At</td>
                                <td>
                                    <input type="checkbox" value="Created At" id="Created At" name="column_returnexchange[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Created At', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Action</td>
                                <td>
                                    <input type="checkbox" value="Action" id="Action" name="column_returnexchange[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Action', $dynamicColumnsToShowPostman)) checked @endif>
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