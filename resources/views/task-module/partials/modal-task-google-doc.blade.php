<div id="taskGoogleDocModal" class="modal fade" role="dialog" style="display: none;">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Google Doc</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>

            <form action="{{route('google-docs.task')}}" method="POST">
                @csrf
                <input type="hidden" id="task_id">
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Document type:</strong>

                        <select class="form-control" name="type" required id="doc-type">
                            <option value="spreadsheet">Spreadsheet</option>
                            <option value="doc">Doc</option>
                            <option value="ppt">Ppt</option>
                            <option value="xps">Xps</option>
                            <option value="txt">Txt</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <strong>Name:</strong>
                        <input type="text" name="doc_name" value="" class="form-control input-sm"
                               placeholder="Document Name" required id="doc-name">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-secondary" id="btnCreateTaskDocument">Create</button>
                </div>
            </form>
        </div>

    </div>
</div>