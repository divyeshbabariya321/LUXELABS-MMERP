<div id="emaildatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('email.column.update') }}" method="POST" id="email-column-update">
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
                                    <input type="checkbox" value="ID" id="ID" name="column_emails[]" @if (!empty($dynamicColumnsToShowEmails) && in_array('ID', $dynamicColumnsToShowEmails)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Date</td>
                                <td>
                                    <input type="checkbox" value="Date" id="Date" name="column_emails[]" @if (!empty($dynamicColumnsToShowEmails) && in_array('Date', $dynamicColumnsToShowEmails)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Sender</td>
                                <td>
                                    <input type="checkbox" value="Sender" id="Sender" name="column_emails[]" @if (!empty($dynamicColumnsToShowEmails) && in_array('Sender', $dynamicColumnsToShowEmails)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Receiver</td>
                                <td>
                                    <input type="checkbox" value="Receiver" id="Receiver" name="column_emails[]" @if (!empty($dynamicColumnsToShowEmails) && in_array('Receiver', $dynamicColumnsToShowEmails)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Model Type</td>
                                <td>
                                    <input type="checkbox" value="Model Type" id="Model_Type" name="column_emails[]" @if (!empty($dynamicColumnsToShowEmails) && in_array('Model Type', $dynamicColumnsToShowEmails)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Mail Type</td>
                                <td>
                                    <input type="checkbox" value="Mail Type" id="Mail_Type" name="column_emails[]" @if (!empty($dynamicColumnsToShowEmails) && in_array('Mail Type', $dynamicColumnsToShowEmails)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Subject & Body</td>
                                <td>
                                    <input type="checkbox" value="Subject & Body" id="Subject_Body" name="column_emails[]" @if (!empty($dynamicColumnsToShowEmails) && in_array('Subject & Body', $dynamicColumnsToShowEmails)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>
                                    <input type="checkbox" value="Status" id="Status" name="column_emails[]" @if (!empty($dynamicColumnsToShowEmails) && in_array('Status', $dynamicColumnsToShowEmails)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Draft</td>
                                <td>
                                    <input type="checkbox" value="Draft" id="Draft" name="column_emails[]" @if (!empty($dynamicColumnsToShowEmails) && in_array('Draft', $dynamicColumnsToShowEmails)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Error Message</td>
                                <td>
                                    <input type="checkbox" value="Error Message" id="Error_Message" name="column_emails[]" @if (!empty($dynamicColumnsToShowEmails) && in_array('Error Message', $dynamicColumnsToShowEmails)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Category</td>
                                <td>
                                    <input type="checkbox" value="Category" id="Category" name="column_emails[]" @if (!empty($dynamicColumnsToShowEmails) && in_array('Category', $dynamicColumnsToShowEmails)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Action</td>
                                <td>
                                    <input type="checkbox" value="Action" id="Action" name="column_emails[]" @if (!empty($dynamicColumnsToShowEmails) && in_array('Action', $dynamicColumnsToShowEmails)) checked @endif>
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