<div id="bdatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('blog.column.update') }}" method="POST" id="blog-column-update">
                @csrf
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Column Name</b></td>
                            <td class="text-center"><b>hide</b></td>
                        </tr>
                        <div id="columnVisibilityControls">
                            <tr>
                                <td>userName</td>
                                <td>
                                    <input type="checkbox" value="userName" id="userName" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('userName', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Idea</td>
                                <td>
                                    <input type="checkbox" value="Idea" id="Idea" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Idea', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Keyword</td>
                                <td>
                                    <input type="checkbox" value="Keyword" id="Keyword" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Keyword', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Website</td>
                                <td>
                                    <input type="checkbox" value="Website" id="Website" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Website', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Canonical URL</td>
                                <td>
                                    <input type="checkbox" value="Canonical URL" id="Canonical URL" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Canonical URL', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>CheckMobile Friendliness</td>
                                <td>
                                    <input type="checkbox" value="CheckMobile Friendliness" id="CheckMobile Friendliness" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('CheckMobile Friendliness', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Content</td>
                                <td>
                                    <input type="checkbox" value="Content" id="Content" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Content', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>No Follow</td>
                                <td>
                                    <input type="checkbox" value="No Follow" id="No Follow" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('No Follow', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>No Index</td>
                                <td>
                                    <input type="checkbox" value="No Index" id="No Index" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('No Index', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Meta Desc</td>
                                <td>
                                    <input type="checkbox" value="Meta Desc" id="Meta Desc" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Meta Desc', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Plaglarism</td>
                                <td>
                                    <input type="checkbox" value="Plaglarism" id="Plaglarism" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Plaglarism', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Internal Link</td>
                                <td>
                                    <input type="checkbox" value="Internal Link" id="Internal Link" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Internal Link', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>External Link</td>
                                <td>
                                    <input type="checkbox" value="External Link" id="External Link" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('External Link', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Url Structure</td>
                                <td>
                                    <input type="checkbox" value="Url Structure" id="Url Structure" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Url Structure', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Url XML</td>
                                <td>
                                    <input type="checkbox" value="Url XML" id="Url XML" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Url XML', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Header Tag</td>
                                <td>
                                    <input type="checkbox" value="Header Tag" id="Header Tag" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Header Tag', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Strong Tag</td>
                                <td>
                                    <input type="checkbox" value="Strong Tag" id="Strong Tag" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Strong Tag', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Title Tag</td>
                                <td>
                                    <input type="checkbox" value="Title Tag" id="Title Tag" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Title Tag', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Italic Tag</td>
                                <td>
                                    <input type="checkbox" value="Italic Tag" id="Italic Tag" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Italic Tag', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Facebook</td>
                                <td>
                                    <input type="checkbox" value="Facebook" id="Facebook" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Facebook', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Facebook Date</td>
                                <td>
                                    <input type="checkbox" value="Facebook Date" id="Facebook Date" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Facebook Date', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Google</td>
                                <td>
                                    <input type="checkbox" value="Google" id="Google" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Google', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Google Date</td>
                                <td>
                                    <input type="checkbox" value="Google Date" id="Google Date" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Google Date', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Twitter</td>
                                <td>
                                    <input type="checkbox" value="Twitter" id="Twitter" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Twitter', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Twitter Date</td>
                                <td>
                                    <input type="checkbox" value="Twitter Date" id="Twitter Date" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Twitter Date', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Bing</td>
                                <td>
                                    <input type="checkbox" value="Bing" id="Bing" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Bing', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Bing Date</td>
                                <td>
                                    <input type="checkbox" value="Bing Date" id="Bing Date" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Bing Date', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Instagram</td>
                                <td>
                                    <input type="checkbox" value="Instagram" id="Instagram" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Instagram', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Instagram Date</td>
                                <td>
                                    <input type="checkbox" value="Instagram Date" id="Instagram Date" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Instagram Date', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Publish Date</td>
                                <td>
                                    <input type="checkbox" value="Publish Date" id="Publish Date" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Publish Date', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Created Date</td>
                                <td>
                                    <input type="checkbox" value="Created Date" id="Created Date" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Created Date', $dynamicColumnsToShowb)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Action</td>
                                <td>
                                    <input type="checkbox" value="Action" id="Action" name="column_blogs[]" @if (!empty($dynamicColumnsToShowb) && in_array('Action', $dynamicColumnsToShowb)) checked @endif>
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