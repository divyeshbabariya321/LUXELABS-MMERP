<div id="lldatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('logging.magento.column.update') }}" method="POST" id="listmagento-column-update">
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
                                    <input type="checkbox" value="ID" id="ID" name="column_ll[]" @if (!empty($dynamicColumnsTologging) && in_array('ID', $dynamicColumnsTologging)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>SKU</td>
                                <td>
                                    <input type="checkbox" value="SKU" id="SKU" name="column_ll[]" @if (!empty($dynamicColumnsTologging) && in_array('SKU', $dynamicColumnsTologging)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Brand</td>
                                <td>
                                    <input type="checkbox" value="Brand" id="Brand" name="column_ll[]" @if (!empty($dynamicColumnsTologging) && in_array('Brand', $dynamicColumnsTologging)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Category</td>
                                <td>
                                    <input type="checkbox" value="Category" id="Category" name="column_ll[]" @if (!empty($dynamicColumnsTologging) && in_array('Category', $dynamicColumnsTologging)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Price</td>
                                <td>
                                    <input type="checkbox" value="Price" id="Price" name="column_ll[]" @if (!empty($dynamicColumnsTologging) && in_array('Price', $dynamicColumnsTologging)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>entered_in_product_push</td>
                                <td>
                                    <input type="checkbox" value="entered_in_product_push" id="entered_in_product_push" name="column_ll[]" @if (!empty($dynamicColumnsTologging) && in_array('entered_in_product_push', $dynamicColumnsTologging)) checked @endif>
                                </td>
                            </tr>

                            @foreach($conditions as $condition)
                                <tr>
                                    <td>{{$condition->condition}}</td>
                                    <td>
                                        <input type="checkbox" value="{{$condition->condition}}" id="{{$condition->condition}}" name="column_ll[]" @if (!empty($dynamicColumnsTologging) && in_array($condition->condition, $dynamicColumnsTologging)) checked @endif>
                                    </td>
                                </tr>
                            @endforeach
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