<div id="uploadeTaskFileModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Upload Screencast/File to Google Drive</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('task.upload-file') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="task_id" id="upload_task_id">
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Upload File</strong>
                        <input type="file" name="file[]" id="fileInput" class="form-control input-sm"
                               placeholder="Upload File" style="height: fit-content;" multiple required>
                        @if ($errors->has('file'))
                            <div class="alert alert-danger">{{$errors->first('file')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>File Creation Date:</strong>
                        <input type="date" name="file_creation_date" value="{{ old('file_creation_date') }}"
                               class="form-control input-sm" placeholder="Drive Date" required>
                    </div>
                    <div class="form-group">
                        <label>Remarks:</label>
                        <textarea id="remarks" name="remarks" rows="4" cols="64" value="{{ old('remarks') }}"
                                  placeholder="Remarks" required class="form-control"></textarea>

                        @if ($errors->has('remarks'))
                            <div class="alert alert-danger">{{$errors->first('remarks')}}</div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-default">Upload</button>
                </div>
            </form>
        </div>

    </div>
</div>