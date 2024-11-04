<div id="file-upload-area-section" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('task.save-documents') }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="task_id" id="hidden-task-id" value="">
                <div class="modal-header">
                    <h4 class="modal-title">Upload File(s)</h4>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                    @csrf
                    <div class="form-group">
                        <label for="document">Documents</label>
                        <div class="needsclick dropzone" id="document-dropzone">

                        </div>
                    </div>
                    <div class="form-group add-task-list">

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-save-documents">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>