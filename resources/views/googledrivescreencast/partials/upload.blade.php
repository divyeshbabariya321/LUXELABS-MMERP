
<style type="text/css">
#uploadeScreencastModal .select2-selection__rendered{display: inline !important;}
</style>
<div id="uploadeScreencastModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Upload Screencast/File to Google Drive</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form id="upload-screencast-file-form" action="{{ route('google-drive-screencast.create') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <!-- <div class="form-group">
                        <strong>Name:</strong>
                        <input type="text" name="doc_name" value="{{ old('doc_name') }}" class="form-control input-sm" placeholder="Document Name" required>

                        @if ($errors->has('doc_name'))
                            <div class="alert alert-danger">{{$errors->first('doc_name')}}</div>
                        @endif
                    </div> -->
                    <div class="form-group custom-select2">
                        <label>Development Task
                        </label>
                        <select class="w-100 js-example-basic-multiple js-states"
                                id="id_label_task" name="task_id">
                                <option value="" class="form-control">Select Task</option>
                            </select>
                    </div>
                    <div class="form-group">
                        <strong>Upload File</strong>
                        <input type="file" name="file[]" id="fileInput" class="form-control input-sm" placeholder="Upload File" style="height: fit-content;" multiple>
                        @if ($errors->has('file'))
                            <div class="alert alert-danger">{{$errors->first('file')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>File Creation Date:</strong>
                        <input type="date" name="file_creation_date" value="{{ old('file_creation_date') }}" class="form-control input-sm" placeholder="Drive Date" required>
                    </div>
                        @if(auth()->user() && auth()->user()->isAdmin())
                    <div class="form-group custom-select2 read_user">
                        <label>Read Permission for Users</label>
                        <select class="w-100 js-example-basic-multiple js-states" id="id_label_multiple_user_read" multiple="multiple" name="file_read[]">
                            <option value="" class="form-control">Select User</option>
                        </select>
                    </div>
                    <div class="form-group custom-select2 write_user">
                        <label>Write Permission for Users
                        </label>
                        <select class="w-100 js-example-basic-multiple js-states" id="id_label_multiple_user_write" multiple="multiple" name="file_write[]">
                             <option value="" class="form-control">Select user</option>
                        </select>
                    </div>
                        @endif
                    <div class="form-group">
                            <label>Remarks:</label>
                            <textarea id="remarks" name="remarks" rows="4" cols="55" value="{{ old('remarks') }}" placeholder="Remarks"></textarea>

                            @if ($errors->has('remarks'))
                                <div class="alert alert-danger">{{$errors->first('remarks')}}</div>
                            @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Upload</button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    $('.js-example-basic-multiple').select2();
    $(document).on("submit", "#upload-screencast-file-form", function (e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('google-drive-screencast.create') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function (resp) {
                switch (resp.status) {
                    case 'success':
                        toastr.success(resp.msg);
                        break;
                }
            },
            error: function (error) {
                for (let msg of Object.values(error.responseJSON.errors)) {
                    toastr.error(msg);
                }
            }
        });
    }); 
</script>
