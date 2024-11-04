<div id="cropdatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('crop_references.column.update') }}" method="POST" id="crop_references-column-update">
                @csrf
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td><b>Column Name</b></td>
                            <td class="text-center"><b>hide</b></td>
                        </tr>
                        <div id="columnVisibilityControls">
                            <tr>
                                <td>ID</td>
                                <td>
                                    <input type="checkbox" value="ID" id="ID" name="column_crop[]" @if (!empty($dynamicColumnsToShowCrop) && in_array('ID', $dynamicColumnsToShowCrop)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Pro. Id</td>
                                <td>
                                    <input type="checkbox" value="Pro. Id" id="ProId" name="column_crop[]" @if (!empty($dynamicColumnsToShowCrop) && in_array('Pro. Id', $dynamicColumnsToShowCrop)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Category</td>
                                <td>
                                    <input type="checkbox" value="Category" id="Category" name="column_crop[]" @if (!empty($dynamicColumnsToShowCrop) && in_array('Category', $dynamicColumnsToShowCrop)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Supplier</td>
                                <td>
                                    <input type="checkbox" value="Supplier" id="Supplier" name="column_crop[]" @if (!empty($dynamicColumnsToShowCrop) && in_array('Supplier', $dynamicColumnsToShowCrop)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Brand</td>
                                <td>
                                    <input type="checkbox" value="Brand" id="Brand" name="column_crop[]" @if (!empty($dynamicColumnsToShowCrop) && in_array('Brand', $dynamicColumnsToShowCrop)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Store Website</td>
                                <td>
                                    <input type="checkbox" value="Store Website" id="Store_Website" name="column_crop[]" @if (!empty($dynamicColumnsToShowCrop) && in_array('Store Website', $dynamicColumnsToShowCrop)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Original Image</td>
                                <td>
                                    <input type="checkbox" value="Original Image" id="Original_Image" name="column_crop[]" @if (!empty($dynamicColumnsToShowCrop) && in_array('Original Image', $dynamicColumnsToShowCrop)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Cropped Image</td>
                                <td>
                                    <input type="checkbox" value="Cropped Image" id="Cropped_Image" name="column_crop[]" @if (!empty($dynamicColumnsToShowCrop) && in_array('Cropped Image', $dynamicColumnsToShowCrop)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Time</td>
                                <td>
                                    <input type="checkbox" value="Cropped Image" id="Time" name="column_crop[]" @if (!empty($dynamicColumnsToShowCrop) && in_array('Cropped Image', $dynamicColumnsToShowCrop)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Date</td>
                                <td>
                                    <input type="checkbox" value="Date" id="Date" name="column_crop[]" @if (!empty($dynamicColumnsToShowCrop) && in_array('Date', $dynamicColumnsToShowCrop)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Action</td>
                                <td>
                                    <input type="checkbox" value="Action" id="Action" name="column_crop[]" @if (!empty($dynamicColumnsToShowCrop) && in_array('Action', $dynamicColumnsToShowCrop)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Issue</td>
                                <td>
                                    <input type="checkbox" value="Issue" id="Issue" name="column_crop[]" @if (!empty($dynamicColumnsToShowCrop) && in_array('Issue', $dynamicColumnsToShowCrop)) checked @endif>
                                </td>
                            </tr>
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