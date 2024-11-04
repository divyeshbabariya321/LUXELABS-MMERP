<div id="addGoogleDocPermission" class="modal fade" role="dialog" style="display: none;">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add document permission</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <form action="{{route('google-docs.assign-user-permission')}}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id">
                    <input type="hidden" name="task_id">
                    <input type="hidden" name="task_type">
                    <label for="">Search document</label>
                    <div>
                        <select name="document_id" id="assignDocumentList" class="form-control"></select>
                    </div>
                    <div class="text-right">
                        <button class="btn btn-secondary mt-4">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>