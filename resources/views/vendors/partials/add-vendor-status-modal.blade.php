<div id="add-vendor-status-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="add-vendor-info-form" action="{{ route('vendors.edit-vendor') }}" method="POST">
                <div class="modal-header">
                    <h2>Status </h2>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <label for="status">Status</label>
                        <input type="text" name="status" id="status" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="hourly_rate">Hourly Rate</label>
                        <input type="text" name="hourly_rate" id="hourly_rate" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="available_hour">Avaialble hours</label>
                        <input type="text" name="available_hour" id="available_hour" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="experience_level">Experience Level</label>
                        <select class="form-control" name="experience_level" id="experience_level">
                            <option value="Excellent">Excellent</option>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="communication_skill">Communication skills</label>
                        <select class="form-control" name="communication_skill" id="communication_skill">
                            <option value="Excellent">Excellent</option>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="agency">Agency </label>
                        <select class="form-control" name="agency" id="agency">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="remark">Remarks </label>
                        <textarea class="form-control" name="remark" id="remark_ven"></textarea>
                    </div>
                    <input type="hidden" id='hidden_vendor_id' name="vendor_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary btn-submit-status">Submit</button>

                </div>

            </form>
        </div>
    </div>
</div>
<div id="status_detail_history_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Status History</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="row">
                <div class="col-md-12" id="status_detail_history_div">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Status</th>
                            <th>Hourly Rate</th>
                            <th>Avaialble hours</th>
                            <th>Experience Level</th>
                            <th>Communication skills</th>
                            <th>Agency</th>
                            <th>Remarks</th>
                            <th>Updated by</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>