<div id="odatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('orders.journey.column.update') }}" method="POST" id="orders-journey-column-update">
                @csrf
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Column Name</b></td>
                            <td class="text-center"><b>hide</b></td>
                        </tr>
                        <div id="columnVisibilityControls">
                            <tr>
                                <td>Order ID</td>
                                <td>
                                    <input type="checkbox" value="Order ID" id="Order ID" name="column_oj[]" @if (!empty($dynamicColumnsToShowoj) && in_array('Order ID', $dynamicColumnsToShowoj)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Products</td>
                                <td>
                                    <input type="checkbox" value="Products" id="Products" name="column_oj[]" @if (!empty($dynamicColumnsToShowoj) && in_array('Products', $dynamicColumnsToShowoj)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Customer</td>
                                <td>
                                    <input type="checkbox" value="Customer" id="Customer" name="column_oj[]" @if (!empty($dynamicColumnsToShowoj) && in_array('Customer', $dynamicColumnsToShowoj)) checked @endif>
                                </td>
                            </tr>
                            @foreach ($orderStatusList as $orderStatus)
                                <tr>
                                    <td>{{ $orderStatus }}</td>
                                    <td>
                                        <input type="checkbox" value="{{ $orderStatus }}" id="{{ $orderStatus }}" name="column_oj[]" @if (!empty($dynamicColumnsToShowoj) && in_array($orderStatus, $dynamicColumnsToShowoj)) checked @endif>
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