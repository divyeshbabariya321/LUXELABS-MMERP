<div id="chatbotmessagesdatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('chatbot.messages.column.update') }}" method="POST" id="chatbot-messages-column-update">
                @csrf
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Column Name</b></td>
                            <td class="text-center"><b>hide</b></td>
                        </tr>
                        <div id="columnVisibilityControls">
                            <tr>
                                <td>Name</td>
                                <td>
                                    <input type="checkbox" value="Name" id="Name" name="column_chatbox[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Name', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Website</td>
                                <td>
                                    <input type="checkbox" value="Website" id="Website" name="column_chatbox[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Website', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Message Type</td>
                                <td>
                                    <input type="checkbox" value="Message Type" id="Message_Type" name="column_chatbox[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Message Type', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>User input</td>
                                <td>
                                    <input type="checkbox" value="User input" id="User_input" name="column_chatbox[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('User input', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>
                           
                            <tr>
                                <td>Bot Replied</td>
                                <td>
                                    <input type="checkbox" value="Bot Replied" id="Bot_Replied" name="column_chatbox[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Bot Replied', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Bot Suggested Reply</td>
                                <td>
                                    <input type="checkbox" value="Bot Suggested Reply" id="Bot_Suggested_Reply" name="column_chatbox[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Bot Suggested Reply', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Message Box</td>
                                <td>
                                    <input type="checkbox" value="Message Box" id="Message_Box" name="column_chatbox[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Message Box', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>From</td>
                                <td>
                                    <input type="checkbox" value="From" id="From" name="column_chatbox[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('From', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Shortcuts</td>
                                <td>
                                    <input type="checkbox" value="Shortcuts" id="Shortcuts" name="column_chatbox[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Shortcuts', $dynamicColumnsToShowPostman)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Action</td>
                                <td>
                                    <input type="checkbox" value="Action" id="Action" name="column_chatbox[]" @if (!empty($dynamicColumnsToShowPostman) && in_array('Action', $dynamicColumnsToShowPostman)) checked @endif>
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