<div id="synclogscolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('magento_module.sync.logs.column.update') }}" method="POST" id="synclogscolumnForm">
                <?php echo csrf_field(); ?>
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Column Name</b></td>
                            <td class="text-center"><b>hide</b></td>
                        </tr>
                        <div id="columnVisibilityControls">
                            <tr>
                                <td>Id</td>
                                <td>
                                    <input type="checkbox" value="Id" id="Id" name="synclogscolumn" @if (!empty($dynamicColumnsToShow) && in_array('Id', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Module Name</td>
                                <td>
                                    <input type="checkbox" value="Module Name" id="Module Name" name="synclogscolumn" @if (!empty($dynamicColumnsToShow) && in_array('Module Name', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Command</td>
                                <td>
                                    <input type="checkbox" value="Command" id="Command" name="synclogscolumn" @if (!empty($dynamicColumnsToShow) && in_array('Command', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Job Id</td>
                                <td>
                                    <input type="checkbox" value="Job Id" id="Job Id" name="synclogscolumn" @if (!empty($dynamicColumnsToShow) && in_array('Job Id', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Status</td>
                                <td>
                                    <input type="checkbox" value="Status" id="Status" name="synclogscolumn" @if (!empty($dynamicColumnsToShow) && in_array('Status', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Response</td>
                                <td>
                                    <input type="checkbox" value="Response" id="Response" name="synclogscolumn" @if (!empty($dynamicColumnsToShow) && in_array('Response', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Created At</td>
                                <td>
                                    <input type="checkbox" value="Created At" id="Created At" name="synclogscolumn" @if (!empty($dynamicColumnsToShow) && in_array('Created At', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>

                            <tr>
                                <td>Updated At</td>
                                <td>
                                    <input type="checkbox" value="Updated At" id="Updated At" name="synclogscolumn" @if (!empty($dynamicColumnsToShow) && in_array('Updated At', $dynamicColumnsToShow)) checked @endif>
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

<script>
    $(document).ready(function() {
        $('#synclogscolumnForm').on('submit', function(event) {
            event.preventDefault();
            
            var selectedColumns = [];
    
            $('input[name="synclogscolumn"]:checked').each(function() {
                selectedColumns.push($(this).val());
            });
                var formData = {
                columns: selectedColumns 
            };
    
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/sync-logs-column-visbility',
                data: formData,
                success: function(response) {
                    toastr["success"]("column Hide Update successfully");
                    location.reload();
                },
                error: function(error) {
                    console.error('Error:', error);
                    location.reload();
                }
            });
        });
    });
</script>