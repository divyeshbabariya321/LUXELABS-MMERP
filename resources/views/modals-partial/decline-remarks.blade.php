<div id="decline-remarks" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Remarks</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('appointment-request.declien.remarks') }}" id="menu_sop_edit_form">
                        <input type="text" hidden name="appointment_requests_id" id="appointment_requests_id">
                        @csrf
                        <div class="form-group">
                            <label for="content">Remarks</label>
                            <textarea class="form-control sop_edit_class" name="appointment_requests_remarks" id="appointment_requests_remarks"></textarea>
                            <span class="text-danger"></span>
                        </div>

                        <button type="button" class="btn btn-secondary ml-3 updatedeclienremarks">Update</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>