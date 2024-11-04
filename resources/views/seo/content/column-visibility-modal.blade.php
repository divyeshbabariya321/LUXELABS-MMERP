<div id="seodatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('seo.content.column.update') }}" method="POST" id="seo-content-column-update">
                @csrf
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Column Name</b></td>
                            <td class="text-center"><b>hide</b></td>
                        </tr>
                        <div id="columnVisibilityControls">
                            <tr>
                                <td>#</td>
                                <td>
                                    <input type="checkbox" value="#" id="#" name="column_seo[]" @if (!empty($dynamicColumnsToShowSeo) && in_array('#', $dynamicColumnsToShowSeo)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Website</td>
                                <td>
                                    <input type="checkbox" value="Website" id="Website" name="column_seo[]" @if (!empty($dynamicColumnsToShowSeo) && in_array('Website', $dynamicColumnsToShowSeo)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Keywords</td>
                                <td>
                                    <input type="checkbox" value="Keywords" id="Keywords" name="column_seo[]" @if (!empty($dynamicColumnsToShowSeo) && in_array('Keywords', $dynamicColumnsToShowSeo)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>User</td>
                                <td>
                                    <input type="checkbox" value="User" id="User" name="column_seo[]" @if (!empty($dynamicColumnsToShowSeo) && in_array('User', $dynamicColumnsToShowSeo)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Price</td>
                                <td>
                                    <input type="checkbox" value="Price" id="Price" name="column_seo[]" @if (!empty($dynamicColumnsToShowSeo) && in_array('Price', $dynamicColumnsToShowSeo)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Document Link</td>
                                <td>
                                    <input type="checkbox" value="Document Link" id="Document Link" name="column_seo[]" @if (!empty($dynamicColumnsToShowSeo) && in_array('Document Link', $dynamicColumnsToShowSeo)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Word count</td>
                                <td>
                                    <input type="checkbox" value="Word count" id="Word count" name="column_seo[]" @if (!empty($dynamicColumnsToShowSeo) && in_array('Word count', $dynamicColumnsToShowSeo)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Suggestion</td>
                                <td>
                                    <input type="checkbox" value="Suggestion" id="Suggestion" name="column_seo[]" @if (!empty($dynamicColumnsToShowSeo) && in_array('Suggestion', $dynamicColumnsToShowSeo)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>
                                    <input type="checkbox" value="Status" id="Status" name="column_seo[]" @if (!empty($dynamicColumnsToShowSeo) && in_array('Status', $dynamicColumnsToShowSeo)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>SEO Checklist</td>
                                <td>
                                    <input type="checkbox" value="SEO Checklist" id="SEO Checklist" name="column_seo[]" @if (!empty($dynamicColumnsToShowSeo) && in_array('SEO Checklist', $dynamicColumnsToShowSeo)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Publish Checklist</td>
                                <td>
                                    <input type="checkbox" value="Publish Checklist" id="Publish Checklist" name="column_seo[]" @if (!empty($dynamicColumnsToShowSeo) && in_array('Publish Checklist', $dynamicColumnsToShowSeo)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Live Status Link</td>
                                <td>
                                    <input type="checkbox" value="Live Status Link" id="Live Status Link" name="column_seo[]" @if (!empty($dynamicColumnsToShowSeo) && in_array('Live Status Link', $dynamicColumnsToShowSeo)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Publish Date</td>
                                <td>
                                    <input type="checkbox" value="Publish Date" id="Publish Date" name="column_seo[]" @if (!empty($dynamicColumnsToShowSeo) && in_array('Publish Date', $dynamicColumnsToShowSeo)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Actions</td>
                                <td>
                                    <input type="checkbox" value="Actions" id="Actions" name="column_seo[]" @if (!empty($dynamicColumnsToShowSeo) && in_array('Actions', $dynamicColumnsToShowSeo)) checked @endif>
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