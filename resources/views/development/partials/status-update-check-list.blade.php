<div id="status_update_checklist" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Check List
                <span id="status_checklist"></span></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="" id="statusUpdateChecklistForm" method="POST">
                @csrf
                <input type="hidden" name="issue_id" id="checklist_issue_id">
                <input type="hidden" name="is_resolved" id="checklist_is_resolved">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th>Subject</th>
                                    <th>Remark</th>
                                </tr>
                                <tbody class="show_checklist">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                     <button type="submit" class="btn btn-default">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $.ajax({
        url: "{{action([\App\Http\Controllers\DevelopmentController::class, 'resolveIssue'])}}",
        data: {
            issue_id: id,
            is_resolved: status,
        },
        success: function() {
            toastr["success"]("Status updated!", "Message")
        },
        error: function(error) {
            toastr["error"](error.responseJSON.message);
        }
    });
</script>