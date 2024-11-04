<div id="ccrdatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('coupons.column.update') }}" method="POST" id="coupons-column-update">
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
                                    <input type="checkbox" value="ID" id="ID" name="column_ccr[]" @if (!empty($dynamicColumnsToShowccr) && in_array('ID', $dynamicColumnsToShowccr)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Rule</td>
                                <td>
                                    <input type="checkbox" value="Rule" id="Rule" name="column_ccr[]" @if (!empty($dynamicColumnsToShowccr) && in_array('Rule', $dynamicColumnsToShowccr)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Description</td>
                                <td>
                                    <input type="checkbox" value="Description" id="Description" name="column_ccr[]" @if (!empty($dynamicColumnsToShowccr) && in_array('Description', $dynamicColumnsToShowccr)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Coupon Code</td>
                                <td>
                                    <input type="checkbox" value="Coupon Code" id="Coupon Code" name="column_ccr[]" @if (!empty($dynamicColumnsToShowccr) && in_array('Coupon Code', $dynamicColumnsToShowccr)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Websites</td>
                                <td>
                                    <input type="checkbox" value="Websites" id="Websites" name="column_ccr[]" @if (!empty($dynamicColumnsToShowccr) && in_array('Websites', $dynamicColumnsToShowccr)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Start</td>
                                <td>
                                    <input type="checkbox" value="Start" id="Start" name="column_ccr[]" @if (!empty($dynamicColumnsToShowccr) && in_array('Start', $dynamicColumnsToShowccr)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>End</td>
                                <td>
                                    <input type="checkbox" value="End" id="End" name="column_ccr[]" @if (!empty($dynamicColumnsToShowccr) && in_array('End', $dynamicColumnsToShowccr)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Created By</td>
                                <td>
                                    <input type="checkbox" value="Created By" id="Created By" name="column_ccr[]" @if (!empty($dynamicColumnsToShowccr) && in_array('Created By', $dynamicColumnsToShowccr)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>
                                    <input type="checkbox" value="Status" id="Status" name="column_ccr[]" @if (!empty($dynamicColumnsToShowccr) && in_array('Status', $dynamicColumnsToShowccr)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Remarks</td>
                                <td>
                                    <input type="checkbox" value="Remarks" id="Remarks" name="column_ccr[]" @if (!empty($dynamicColumnsToShowccr) && in_array('Remarks', $dynamicColumnsToShowccr)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Action</td>
                                <td>
                                    <input type="checkbox" value="Action" id="Action" name="column_ccr[]" @if (!empty($dynamicColumnsToShowccr) && in_array('Action', $dynamicColumnsToShowccr)) checked @endif>
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