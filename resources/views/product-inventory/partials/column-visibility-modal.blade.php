<div id="pidatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="" method="POST">
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
                                    <input type="checkbox" value="Checkbox" id="Checkbox" name="column_pi[]" @if (!empty($dynamicColumnsToShowPi) && in_array('Checkbox', $dynamicColumnsToShowPi)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>ID</td>
                                <td>
                                    <input type="checkbox" value="ID" id="ID" name="column_pi[]" @if (!empty($dynamicColumnsToShowPi) && in_array('ID', $dynamicColumnsToShowPi)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Sku</td>
                                <td>
                                    <input type="checkbox" value="Sku" id="Sku" name="column_pi[]" @if (!empty($dynamicColumnsToShowPi) && in_array('ID', $dynamicColumnsToShowPi)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Supplier count</td>
                                <td>
                                    <input type="checkbox" value="Supplier count" id="Supplier count" name="column_pi[]" @if (!empty($dynamicColumnsToShowPi) && in_array('Supplier count', $dynamicColumnsToShowPi)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Name</td>
                                <td>
                                    <input type="checkbox" value="Name" id="Name" name="column_pi[]" @if (!empty($dynamicColumnsToShowPi) && in_array('Name', $dynamicColumnsToShowPi)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Category</td>
                                <td>
                                    <input type="checkbox" value="Category" id="Category" name="column_pi[]" @if (!empty($dynamicColumnsToShowPi) && in_array('Category', $dynamicColumnsToShowPi)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Brand</td>
                                <td>
                                    <input type="checkbox" value="Brand" id="Brand" name="column_pi[]" @if (!empty($dynamicColumnsToShowPi) && in_array('Brand', $dynamicColumnsToShowPi)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Price</td>
                                <td>
                                    <input type="checkbox" value="Price" id="Price" name="column_pi[]" @if (!empty($dynamicColumnsToShowPi) && in_array('Price', $dynamicColumnsToShowPi)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Discount %</td>
                                <td>
                                    <input type="checkbox" value="Discount" id="Discount" name="column_pi[]" @if (!empty($dynamicColumnsToShowPi) && in_array('Discount', $dynamicColumnsToShowPi)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Supplier</td>
                                <td>
                                    <input type="checkbox" value="Supplier" id="Supplier" name="column_pi[]" @if (!empty($dynamicColumnsToShowPi) && in_array('Supplier', $dynamicColumnsToShowPi)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Color</td>
                                <td>
                                    <input type="checkbox" value="Color" id="Color" name="column_pi[]" @if (!empty($dynamicColumnsToShowPi) && in_array('Color', $dynamicColumnsToShowPi)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Composition</td>
                                <td>
                                    <input type="checkbox" value="Composition" id="Composition" name="column_pi[]" @if (!empty($dynamicColumnsToShowPi) && in_array('Composition', $dynamicColumnsToShowPi)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Size system</td>
                                <td>
                                    <input type="checkbox" value="Size system" id="Size system" name="column_pi[]" @if (!empty($dynamicColumnsToShowPi) && in_array('Size system', $dynamicColumnsToShowPi)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Size</td>
                                <td>
                                    <input type="checkbox" value="Size" id="Size" name="column_pi[]" @if (!empty($dynamicColumnsToShowPi) && in_array('Size', $dynamicColumnsToShowPi)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Size(IT)</td>
                                <td>
                                    <input type="checkbox" value="SizeIT" id="SizeIT" name="column_pi[]" @if (!empty($dynamicColumnsToShowPi) && in_array('SizeIT', $dynamicColumnsToShowPi)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>
                                    <input type="checkbox" value="Status" id="Status" name="column_pi[]" @if (!empty($dynamicColumnsToShowPi) && in_array('Status', $dynamicColumnsToShowPi)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Sub Status</td>
                                <td>
                                    <input type="checkbox" value="Sub Status" id="Sub Status" name="column_pi[]" @if (!empty($dynamicColumnsToShowPi) && in_array('Sub Status', $dynamicColumnsToShowPi)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Created Date</td>
                                <td>
                                    <input type="checkbox" value="Created Date" id="Created Date" name="column_pi[]" @if (!empty($dynamicColumnsToShowPi) && in_array('Created Date', $dynamicColumnsToShowPi)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Actions</td>
                                <td>
                                    <input type="checkbox" value="Actions" id="Actions" name="column_pi[]" @if (!empty($dynamicColumnsToShowPi) && in_array('Actions', $dynamicColumnsToShowPi)) checked @endif>
                                </td>
                            </tr>
                        </div>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button id="submitPidatatablecolumnvisibilityList" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
