<div class="modal fade" role="dialog" id="statusListModal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Status <a href="javascript:void(0)" data-url="{{ route('seo.content-status.create') }}" class="addStatusBtn" style=" font-weight: bold;   text-decoration: underline;">Add</a></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-control">
                            <option value="">-- SELECT --</option>
                            <option value="seo_approval">SEO</option>
                            <option value="publish">Publish</option>
                        </select>
                    </div>
                    <div class="col-md-2 mt-5">
                        <button class="btn btn-image search ui-autocomplete-input searchBtn" autocomplete="off">
                            <img src="{{ asset('images/search.png') }}" alt="Search" style="cursor: default;">
                        </button>
                    </div>
                </div>
                <div class="">
                    <table class="table table-bordered" id="statusTable">
                        <thead>
                            <th>#</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Action</th>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-notification" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Status Form Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="statusFormModal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-notification" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary saveBtn">Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- Checklist Form Modal -->
<div class="modal fade" role="dialog" id="checklistModal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-notification" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary saveBtn">Submit</button>
            </div>
        </div>
    </div>
</div>


<!-- Checklist History Form Modal -->
<div class="modal fade" role="dialog" id="checklistHistoryModal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Checklist history</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-notification" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Checklist History Form Modal -->
<div class="modal fade" role="dialog" id="statusHistoryModal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Status History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-notification" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>