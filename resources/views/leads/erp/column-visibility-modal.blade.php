<div id="eldatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('erp-leads.column.update') }}" method="POST" id="erp-leads-column-update">
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
                                    <input type="checkbox" value="Checkbox" id="Checkbox" name="column_el[]" @if (!empty($dynamicColumnsToShowel) && in_array('Checkbox', $dynamicColumnsToShowel)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>ID</td>
                                <td>
                                    <input type="checkbox" value="ID" id="ID" name="column_el[]" @if (!empty($dynamicColumnsToShowel) && in_array('ID', $dynamicColumnsToShowel)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Date</td>
                                <td>
                                    <input type="checkbox" value="Date" id="Date" name="column_el[]" @if (!empty($dynamicColumnsToShowel) && in_array('Date', $dynamicColumnsToShowel)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>
                                    <input type="checkbox" value="Status" id="Status" name="column_el[]" @if (!empty($dynamicColumnsToShowel) && in_array('Status', $dynamicColumnsToShowel)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Cust</td>
                                <td>
                                    <input type="checkbox" value="Cust" id="Cust" name="column_el[]" @if (!empty($dynamicColumnsToShowel) && in_array('Cust', $dynamicColumnsToShowel)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>C Email</td>
                                <td>
                                    <input type="checkbox" value="C Email" id="C Email" name="column_el[]" @if (!empty($dynamicColumnsToShowel) && in_array('C Email', $dynamicColumnsToShowel)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>C WApp</td>
                                <td>
                                    <input type="checkbox" value="C WApp" id="C WApp" name="column_el[]" @if (!empty($dynamicColumnsToShowel) && in_array('C WApp', $dynamicColumnsToShowel)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Store</td>
                                <td>
                                    <input type="checkbox" value="Store" id="Store" name="column_el[]" @if (!empty($dynamicColumnsToShowel) && in_array('Store', $dynamicColumnsToShowel)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Image</td>
                                <td>
                                    <input type="checkbox" value="Image" id="Image" name="column_el[]" @if (!empty($dynamicColumnsToShowel) && in_array('Image', $dynamicColumnsToShowel)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Pro ID</td>
                                <td>
                                    <input type="checkbox" value="Pro ID" id="Pro ID" name="column_el[]" @if (!empty($dynamicColumnsToShowel) && in_array('Pro ID', $dynamicColumnsToShowel)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Sku</td>
                                <td>
                                    <input type="checkbox" value="Sku" id="Sku" name="column_el[]" @if (!empty($dynamicColumnsToShowel) && in_array('Sku', $dynamicColumnsToShowel)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Pro name</td>
                                <td>
                                    <input type="checkbox" value="Pro name" id="Pro name" name="column_el[]" @if (!empty($dynamicColumnsToShowel) && in_array('Pro name', $dynamicColumnsToShowel)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Brand</td>
                                <td>
                                    <input type="checkbox" value="Brand" id="Brand" name="column_el[]" @if (!empty($dynamicColumnsToShowel) && in_array('Brand', $dynamicColumnsToShowel)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>B Sgmt</td>
                                <td>
                                    <input type="checkbox" value="B Sgmt" id="B Sgmt" name="column_el[]" @if (!empty($dynamicColumnsToShowel) && in_array('B Sgmt', $dynamicColumnsToShowel)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Category</td>
                                <td>
                                    <input type="checkbox" value="Category" id="Category" name="column_el[]" @if (!empty($dynamicColumnsToShowel) && in_array('Category', $dynamicColumnsToShowel)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Color</td>
                                <td>
                                    <input type="checkbox" value="Color" id="Color" name="column_el[]" @if (!empty($dynamicColumnsToShowel) && in_array('Color', $dynamicColumnsToShowel)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Size</td>
                                <td>
                                    <input type="checkbox" value="Size" id="Size" name="column_el[]" @if (!empty($dynamicColumnsToShowel) && in_array('Size', $dynamicColumnsToShowel)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Type</td>
                                <td>
                                    <input type="checkbox" value="Type" id="Type" name="column_el[]" @if (!empty($dynamicColumnsToShowel) && in_array('Type', $dynamicColumnsToShowel)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Communication</td>
                                <td>
                                    <input type="checkbox" value="Communication" id="Communication" name="column_el[]" @if (!empty($dynamicColumnsToShowel) && in_array('Communication', $dynamicColumnsToShowel)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Action</td>
                                <td>
                                    <input type="checkbox" value="Action" id="Action" name="column_el[]" @if (!empty($dynamicColumnsToShowel) && in_array('Action', $dynamicColumnsToShowel)) checked @endif>
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