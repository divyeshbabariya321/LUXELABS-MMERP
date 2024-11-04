<div id="ordersdatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('orders.column.update') }}" method="POST" id="orders-column-update">
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
                                    <input type="checkbox" value="ID" id="ID" name="column_orders[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('ID', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>Date</td>
                                <td>
                                    <input type="checkbox" value="Date" id="Date" name="column_orders[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Date', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>Client</td>
                                <td>
                                    <input type="checkbox" value="Client" id="Client" name="column_orders[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Client', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>Site Name</td>
                                <td>
                                    <input type="checkbox" value="Site Name" id="Site Name" name="column_orders[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Site Name', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                                
                            <tr>
                                <td>Products</td>
                                <td>
                                    <input type="checkbox" value="Products" id="Products" name="column_orders[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Products', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>Eta</td>
                                <td>
                                    <input type="checkbox" value="Eta" id="Eta" name="column_orders[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Eta', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                              
                            <tr>
                                <td>Brands</td>
                                <td>
                                    <input type="checkbox" value="Brands" id="Brands" name="column_orders[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Brands', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                               
                            <tr>
                                <td>Order Status</td>
                                <td>
                                    <input type="checkbox" value="Order Status" id="Order Status" name="column_orders[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Order Status', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>Order Product Status</td>
                                <td>
                                    <input type="checkbox" value="Order Product Status" id="Order Product Status" name="column_orders[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Order Product Status', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                              
                            <tr>
                                <td>Product Status</td>
                                <td>
                                    <input type="checkbox" value="Product Status" id="Product Status" name="column_orders[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Product Status', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                             
                            <tr>
                                <td>Advance</td>
                                <td>
                                    <input type="checkbox" value="Advance" id="Advance" name="column_orders[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Advance', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                             
                            <tr>
                                <td>Balance</td>
                                <td>
                                    <input type="checkbox" value="Balance" id="Balance" name="column_orders[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Balance', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                             
                            <tr>
                                <td>Waybill</td>
                                <td>
                                    <input type="checkbox" value="Waybill" id="Waybill" name="column_orders[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Waybill', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                               
                            <tr>
                                <td>Price</td>
                                <td>
                                    <input type="checkbox" value="Price" id="Price" name="column_orders[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Price', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                                
                            <tr>
                                <td>Shipping</td>
                                <td>
                                    <input type="checkbox" value="Shipping" id="Shipping" name="column_orders[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Shipping', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>Duty</td>
                                <td>
                                    <input type="checkbox" value="Duty" id="Duty" name="column_orders[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Duty', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>Action</td>
                                <td>
                                    <input type="checkbox" value="Action" id="Action" name="column_orders[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Action', $dynamicColumnsToShowPostman)) checked @endif>
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