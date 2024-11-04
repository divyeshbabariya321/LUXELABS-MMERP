<?php
//adding in multidimentional if in future need to add anything else in condition
$columns_array = [
    [
        'id' => 'Order_Id',
        'name' => 'Order Id'
    ],
    [
        'id' => 'Supplier',
        'name' => 'Supplier'
    ],
    [
        'id' => 'MRP',
        'name' => 'MRP'
    ],
    [
        'id' => 'Dis_Prc',
        'name' => 'Dis Prc'
    ],
    [
        'id' => 'Spc_Prc',
        'name' => 'Spc Prc'
    ],
    [
        'id' => 'Invoice_No',
        'name' => 'Invoice No'
    ],
    [
        'id' => 'Paym_Details',
        'name' => 'Paym Details'
    ],
    [
        'id' => 'Cost_Details',
        'name' => 'Cost Details'
    ],
    [
        'id' => 'Land_Cost',
        'name' => 'Land Cost'
    ],
    [
        'id' => 'Status',
        'name' => 'Status'
    ],
    [
        'id' => 'Purchase_Status',
        'name' => 'Purchase Status'
    ],
    [
        'id' => 'Create_Date',
        'name' => 'Create Date'
    ],
];

?>
<div id="purchaseproductorderscolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('purchaseproductorders.column.update') }}" method="POST" id="postman-column-update">
                @csrf
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Column Name</b></td>
                            <td class="text-center"><b>hide</b></td>
                        </tr>
                        <div id="columnVisibilityControls">
                            @foreach($columns_array as $k=>$v)
                            <tr>
                                <td>{{$v['name']}}</td>
                                <td>
                                    <input type="checkbox" value="{{$v['name']}}" id="{{$v['id']}}" name="column_purchaseproductorders[]" @if (!empty($dynamicColumnsToShowPurchaseproductorders) && in_array($v['name'], $dynamicColumnsToShowPurchaseproductorders)) checked @endif>
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