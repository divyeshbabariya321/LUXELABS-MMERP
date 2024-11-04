<div id="scrapperdatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('scrapper.column.update') }}" method="POST" id="scrapper-column-update">
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
                                    <input type="checkbox" value="ID" id="ID" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('ID', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Task Id</td>
                                <td>
                                    <input type="checkbox" value="Task Id" id="Task_Id" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Task Id', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Scrapper</td>
                                <td>
                                    <input type="checkbox" value="Scrapper" id="Scrapper" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Scrapper', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Title</td>
                                <td>
                                    <input type="checkbox" value="Title" id="Title" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Title', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Website</td>
                                <td>
                                    <input type="checkbox" value="Website" id="Website" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Website', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Sku</td>
                                <td>
                                    <input type="checkbox" value="Sku" id="Sku" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Sku', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Url</td>
                                <td>
                                    <input type="checkbox" value="Url" id="Url" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Url', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Images</td>
                                <td>
                                    <input type="checkbox" value="Images" id="Images" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Images', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Description</td>
                                <td>
                                    <input type="checkbox" value="Description" id="Description" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Description', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Dimension</td>
                                <td>
                                    <input type="checkbox" value="Dimension" id="Dimension" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Dimension', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Sizes</td>
                                <td>
                                    <input type="checkbox" value="Sizes" id="Sizes" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Sizes', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Material Used</td>
                                <td>
                                    <input type="checkbox" value="Material Used" id="Material_Used" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Material Used', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Category</td>
                                <td>
                                    <input type="checkbox" value="Category" id="Category" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Category', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Color</td>
                                <td>
                                    <input type="checkbox" value="Color" id="Color" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Color', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Country</td>
                                <td>
                                    <input type="checkbox" value="Country" id="Country" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Country', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Currency</td>
                                <td>
                                    <input type="checkbox" value="Currency" id="Currency" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Currency', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Size System</td>
                                <td>
                                    <input type="checkbox" value="Size System" id="Size_System" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Size System', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Price</td>
                                <td>
                                    <input type="checkbox" value="Price" id="Price" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Price', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Discounted Price</td>
                                <td>
                                    <input type="checkbox" value="Discounted Price" id="Discounted_Price" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Discounted Price', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Discounted Percentage</td>
                                <td>
                                    <input type="checkbox" value="Discounted Percentage" id="Discounted_Percentage" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Discounted Percentage', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>B2b Price</td>
                                <td>
                                    <input type="checkbox" value="B2b Price" id="B2b_Price" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('B2b Price', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Brand</td>
                                <td>
                                    <input type="checkbox" value="Brand" id="Brand" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Brand', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Is Sale</td>
                                <td>
                                    <input type="checkbox" value="Is Sale" id="Is_Sale" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Is Sale', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Date</td>
                                <td>
                                    <input type="checkbox" value="Date" id="Date" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Date', $dynamicColumnsToShowscrapper)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Action</td>
                                <td>
                                    <input type="checkbox" value="Action" id="Action" name="column_scrapper[]" @if (!empty($dynamicColumnsToShowscrapper) && in_array('Action', $dynamicColumnsToShowscrapper)) checked @endif>
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