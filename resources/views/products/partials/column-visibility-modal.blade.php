<div id="pdatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('products.column.update') }}" method="POST" id="postman-column-update">
                @csrf
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Column Name</b></td>
                            <td class="text-center"><b>hide</b></td>
                        </tr>
                        <div id="columnVisibilityControls">
                            <tr>
                                <td>Date</td>
                                <td>
                                    <input type="checkbox" value="Date" id="Date" name="column_p[]" @if (!empty($dynamicColumnsToShowp) && in_array('Date', $dynamicColumnsToShowp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Product ID</td>
                                <td>
                                    <input type="checkbox" value="Product ID" id="Product ID" name="column_p[]" @if (!empty($dynamicColumnsToShowp) && in_array('Product ID', $dynamicColumnsToShowp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Suppliers</td>
                                <td>
                                    <input type="checkbox" value="Suppliers" id="Suppliers" name="column_p[]" @if (!empty($dynamicColumnsToShowp) && in_array('Suppliers', $dynamicColumnsToShowp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Scrape</td>
                                <td>
                                    <input type="checkbox" value="Scrape" id="Scrape" name="column_p[]" @if (!empty($dynamicColumnsToShowp) && in_array('Scrape', $dynamicColumnsToShowp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Auto crop</td>
                                <td>
                                    <input type="checkbox" value="Auto crop" id="Auto crop" name="column_p[]" @if (!empty($dynamicColumnsToShowp) && in_array('Auto crop', $dynamicColumnsToShowp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Final approval</td>
                                <td>
                                    <input type="checkbox" value="Final approval" id="Final approval" name="column_p[]" @if (!empty($dynamicColumnsToShowp) && in_array('Final approval', $dynamicColumnsToShowp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Is being cropped</td>
                                <td>
                                    <input type="checkbox" value="Is being cropped" id="Is being cropped" name="column_p[]" @if (!empty($dynamicColumnsToShowp) && in_array('Is being cropped', $dynamicColumnsToShowp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Is being scraped</td>
                                <td>
                                    <input type="checkbox" value="Is being scraped" id="Is being scraped" name="column_p[]" @if (!empty($dynamicColumnsToShowp) && in_array('Is being scraped', $dynamicColumnsToShowp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Pending products without category</td>
                                <td>
                                    <input type="checkbox" value="Pending products without category" id="Pending products without category" name="column_p[]" @if (!empty($dynamicColumnsToShowp) && in_array('Pending products without category', $dynamicColumnsToShowp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Request For external Scraper</td>
                                <td>
                                    <input type="checkbox" value="Request For external Scraper" id="Request For external Scraper" name="column_p[]" @if (!empty($dynamicColumnsToShowp) && in_array('Request For external Scraper', $dynamicColumnsToShowp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Send external Scraper</td>
                                <td>
                                    <input type="checkbox" value="Send external Scraper" id="Send external Scraper" name="column_p[]" @if (!empty($dynamicColumnsToShowp) && in_array('Send external Scraper', $dynamicColumnsToShowp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Finished external Scraper</td>
                                <td>
                                    <input type="checkbox" value="Finished external Scraper" id="Finished external Scraper" name="column_p[]" @if (!empty($dynamicColumnsToShowp) && in_array('Finished external Scraper', $dynamicColumnsToShowp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Unknown Color</td>
                                <td>
                                    <input type="checkbox" value="Unknown Color" id="Unknown Color" name="column_p[]" @if (!empty($dynamicColumnsToShowp) && in_array('Unknown Color', $dynamicColumnsToShowp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Unknown Size</td>
                                <td>
                                    <input type="checkbox" value="Unknown Size" id="Unknown Size" name="column_p[]" @if (!empty($dynamicColumnsToShowp) && in_array('Unknown Size', $dynamicColumnsToShowp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Unknown Composition</td>
                                <td>
                                    <input type="checkbox" value="Unknown Composition" id="Unknown Composition" name="column_p[]" @if (!empty($dynamicColumnsToShowp) && in_array('Unknown Composition', $dynamicColumnsToShowp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Unknown Measurement</td>
                                <td>
                                    <input type="checkbox" value="Unknown Measurement" id="Unknown Measurement" name="column_p[]" @if (!empty($dynamicColumnsToShowp) && in_array('Unknown Measurement', $dynamicColumnsToShowp)) checked @endif>
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