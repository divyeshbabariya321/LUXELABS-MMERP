<div id="dependency-area-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 95%;width: 100%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><span class="modal-type">Dependency</span> History</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">

                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="50%"><span class="modal-type">Remark</span></th>
                                <th width="20%">Updated BY</th>
                                <th width="20%">Created Date</th>
                                <th width="20%">Action</th>
                            </tr>
                        </thead>
                        <tbody class="dependency-action-list-view">
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
<div id="modal-add-new-dependency" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 80%;width: 100%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Dependency Remark</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="mmdepency_magento_module_id" value="0" name="mmdepency_magento_module_id">

                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <strong>Remark:</strong>
                        {{ html()->textarea('depency_remark')->id('depency_remark')->placeholder('Remark')->class('form-control')->rows(4)->cols(40)->required() }}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <strong>Magento Modules issues</strong>
                        {{ html()->textarea('depency_module_issues')->id('depency_module_issues')->placeholder('Magento Modules issues')->class('form-control')->rows(4)->cols(40) }}
                    </div>
                </div> 
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <strong>APIs issues</strong>
                        {{ html()->textarea('depency_api_issues')->id('depency_api_issues')->placeholder('APIs issues')->class('form-control')->rows(4)->cols(40) }}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <strong>Theme issues</strong>
                        {{ html()->textarea('depency_theme_issues')->id('depency_theme_issues')->placeholder('Theme issues')->class('form-control')->rows(4)->cols(40) }}
                    </div>
                </div>                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-default btn-depency-save">Save</button>
            </div>
        </div>
    </div>
</div>