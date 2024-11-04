<div id="plfdatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('products.plfcolumn.update') }}" method="POST" id="products-column-update">
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
                                    <input type="checkbox" value="Checkbox" id="Checkbox" name="column_plf[]" @if (!empty($dynamicColumnsToShowPlf) && in_array('Checkbox', $dynamicColumnsToShowPlf)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Product ID</td>
                                <td>
                                    <input type="checkbox" value="Product ID" id="Product_ID" name="column_plf[]" @if (!empty($dynamicColumnsToShowPlf) && in_array('Product ID', $dynamicColumnsToShowPlf)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Image</td>
                                <td>
                                    <input type="checkbox" value="Image" id="Image" name="column_plf[]" @if (!empty($dynamicColumnsToShowPlf) && in_array('Image', $dynamicColumnsToShowPlf)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Brand</td>
                                <td>
                                    <input type="checkbox" value="Brand" id="Brand" name="column_plf[]" @if (!empty($dynamicColumnsToShowPlf) && in_array('Brand', $dynamicColumnsToShowPlf)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Category</td>
                                <td>
                                    <input type="checkbox" value="Category" id="Category" name="column_plf[]" @if (!empty($dynamicColumnsToShowPlf) && in_array('Category', $dynamicColumnsToShowPlf)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Title</td>
                                <td>
                                    <input type="checkbox" value="Title" id="Title" name="column_plf[]" @if (!empty($dynamicColumnsToShowPlf) && in_array('Title', $dynamicColumnsToShowPlf)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Description</td>
                                <td>
                                    <input type="checkbox" value="Description" id="Description" name="column_plf[]" @if (!empty($dynamicColumnsToShowPlf) && in_array('Description', $dynamicColumnsToShowPlf)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Composition</td>
                                <td>
                                    <input type="checkbox" value="Composition" id="Composition" name="column_plf[]" @if (!empty($dynamicColumnsToShowPlf) && in_array('Composition', $dynamicColumnsToShowPlf)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Color</td>
                                <td>
                                    <input type="checkbox" value="Color" id="Color" name="column_plf[]" @if (!empty($dynamicColumnsToShowPlf) && in_array('Color', $dynamicColumnsToShowPlf)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Dimension</td>
                                <td>
                                    <input type="checkbox" value="Dimension" id="Dimension" name="column_plf[]" @if (!empty($dynamicColumnsToShowPlf) && in_array('Dimension', $dynamicColumnsToShowPlf)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Sizes</td>
                                <td>
                                    <input type="checkbox" value="Sizes" id="Sizes" name="column_plf[]" @if (!empty($dynamicColumnsToShowPlf) && in_array('Sizes', $dynamicColumnsToShowPlf)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Price</td>
                                <td>
                                    <input type="checkbox" value="Price" id="Price" name="column_plf[]" @if (!empty($dynamicColumnsToShowPlf) && in_array('Price', $dynamicColumnsToShowPlf)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>
                                    <input type="checkbox" value="Status" id="Status" name="column_plf[]" @if (!empty($dynamicColumnsToShowPlf) && in_array('Status', $dynamicColumnsToShowPlf)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>User</td>
                                <td>
                                    <input type="checkbox" value="User" id="User" name="column_plf[]" @if (!empty($dynamicColumnsToShowPlf) && in_array('User', $dynamicColumnsToShowPlf)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Stock</td>
                                <td>
                                    <input type="checkbox" value="Stock" id="Stock" name="column_plf[]" @if (!empty($dynamicColumnsToShowPlf) && in_array('Stock', $dynamicColumnsToShowPlf)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Action</td>
                                <td>
                                    <input type="checkbox" value="Action" id="Action" name="column_plf[]" @if (!empty($dynamicColumnsToShowPlf) && in_array('Action', $dynamicColumnsToShowPlf)) checked @endif>
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