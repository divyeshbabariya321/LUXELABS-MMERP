<div id="runRequestUrl" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Run Request URL</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST" id="postman-run-request-url">
                @csrf
                </br>
                <div class="col-12">
                    <div class="form-group">
                        <div class="input-group">
                            <select name="run_request_folder_name" class="form-control" id="run_request_folder_name" required>
                                <option value="">--Select Folder--</option>
                                <?php
                                foreach ($folders as $folder) {
                                    echo '<option value="' . $folder->id . '">' . $folder->name . '</option>';
                                }?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="run-request-save-btn">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>