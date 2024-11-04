<div id="listmagentodatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('list.magento.column.update') }}" method="POST" id="listmagento-column-update">
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
                                    <input type="checkbox" value="ID" id="ID" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('ID', $dynamicColumnsToShowListmagento)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>SKU</td>
                                <td>
                                    <input type="checkbox" value="SKU" id="SKU" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('SKU', $dynamicColumnsToShowListmagento)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Brand</td>
                                <td>
                                    <input type="checkbox" value="Brand" id="Brand" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('Brand', $dynamicColumnsToShowListmagento)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Category</td>
                                <td>
                                    <input type="checkbox" value="Category" id="Category" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('Category', $dynamicColumnsToShowListmagento)) checked @endif>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>Price</td>
                                <td>
                                    <input type="checkbox" value="Price" id="Price" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('Price', $dynamicColumnsToShowListmagento)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Message</td>
                                <td>
                                    <input type="checkbox" value="Message" id="Message" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('Message', $dynamicColumnsToShowListmagento)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>D&T</td>
                                <td>
                                    <input type="checkbox" value="D&T" id="D_T" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('D&T', $dynamicColumnsToShowListmagento)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Website</td>
                                <td>
                                    <input type="checkbox" value="Website" id="Website" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('Website', $dynamicColumnsToShowListmagento)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Status</td>
                                <td>
                                    <input type="checkbox" value="Status" id="Status" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('Status', $dynamicColumnsToShowListmagento)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Lang Id</td>
                                <td>
                                    <input type="checkbox" value="Lang Id" id="Lang_Id" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('Lang Id', $dynamicColumnsToShowListmagento)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Sync Sts</td>
                                <td>
                                    <input type="checkbox" value="Sync Sts" id="Sync_Sts" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('Sync Sts', $dynamicColumnsToShowListmagento)) checked @endif>
                                </td>
                            </tr>
                
                            <tr>
                                <td>Job Start</td>
                                <td>
                                    <input type="checkbox" value="Job Start" id="Job_Start" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('Job Start', $dynamicColumnsToShowListmagento)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Job End</td>
                                <td>
                                    <input type="checkbox" value="Job End" id="Job_End" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('Job End', $dynamicColumnsToShowListmagento)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Total</td>
                                <td>
                                    <input type="checkbox" value="Total" id="Total" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('Total', $dynamicColumnsToShowListmagento)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Success</td>
                                <td>
                                    <input type="checkbox" value="Success" id="Success" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('Success', $dynamicColumnsToShowListmagento)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Failure</td>
                                <td>
                                    <input type="checkbox" value="Failure" id="Failure" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('Failure', $dynamicColumnsToShowListmagento)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>User</td>
                                <td>
                                    <input type="checkbox" value="User" id="User" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('User', $dynamicColumnsToShowListmagento)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Time</td>
                                <td>
                                    <input type="checkbox" value="Time" id="Time" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('Time', $dynamicColumnsToShowListmagento)) checked @endif>
                                </td>
                            </tr>
              
                            <tr>
                                <td>Size</td>
                                <td>
                                    <input type="checkbox" value="Size" id="Size" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('Size', $dynamicColumnsToShowListmagento)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Queue</td>
                                <td>
                                    <input type="checkbox" value="Queue" id="Queue" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('Queue', $dynamicColumnsToShowListmagento)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Try</td>
                                <td>
                                    <input type="checkbox" value="Try" id="Try" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('Try', $dynamicColumnsToShowListmagento)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Action</td>
                                <td>
                                    <input type="checkbox" value="Action" id="Action" name="column_listmagento[]" @if (!empty($dynamicColumnsToShowListmagento) && in_array('Action', $dynamicColumnsToShowListmagento)) checked @endif>
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