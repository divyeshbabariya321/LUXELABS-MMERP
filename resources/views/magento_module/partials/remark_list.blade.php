<div id="remark-area-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 95%;width: 100%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><span class="modal-type">Remark</span> History</h4>
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
                        <tbody class="remark-action-list-view">
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
<div id="modal-add-new-remark" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 80%;width: 100%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Remark</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="mmanr-magento_module_id" value="0" name="mmanr-magento_module_id">
                <input type="hidden" id="mmanr-type" value="" name="mmanr-type">
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <strong>Remark:</strong>
                        {{ html()->textarea('mmanr-remark')->id('mmanr-remark')->placeholder('Remark')->class('form-control')->rows(4)->cols(40) }}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <strong>Frontend Issues:</strong>
                        {{ html()->textarea('mmanr-frontend_issues')->id('mmanr-frontend_issues')->placeholder('Frontend Issues')->class('form-control')->rows(4)->cols(40) }}
                    </div>
                </div> 
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <strong>Backend Issues:</strong>
                        {{ html()->textarea('mmanr-backend_issues')->id('mmanr-backend_issues')->placeholder('Backend Issues')->class('form-control')->rows(4)->cols(40) }}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <strong>Security Issues:</strong>
                        {{ html()->textarea('mmanr-security_issues')->id('mmanr-security_issues')->placeholder('Security Issues')->class('form-control')->rows(4)->cols(40) }}
                    </div>
                </div>             
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <strong>API Issues:</strong>
                        {{ html()->textarea('mmanr-api_issues')->id('mmanr-api_issues')->placeholder('API Issues')->class('form-control')->rows(4)->cols(40) }}
                    </div>
                </div>             
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <strong>Performance Issues:</strong>
                        {{ html()->textarea('mmanr-performance_issues')->id('mmanr-performance_issues')->placeholder('Performance Issues')->class('form-control')->rows(4)->cols(40) }}
                    </div>
                </div>             
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <strong>Best Practices:</strong>
                        {{ html()->textarea('mmanr-best_practices')->id('mmanr-best_practices')->placeholder('Best Practices')->class('form-control')->rows(4)->cols(40) }}
                    </div>
                </div>             
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <strong>Conclusion:</strong>
                        {{ html()->textarea('mmanr-conclusion')->id('mmanr-conclusion')->placeholder('Conclusion')->class('form-control')->rows(4)->cols(40) }}
                    </div>
                </div>             
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <strong>Other Detail:</strong>
                        {{ html()->textarea('mmanr-other')->id('mmanr-other')->placeholder('Other Detail')->class('form-control')->rows(4)->cols(40) }}
                    </div>
                </div>             
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-default btn-mmanr-save-remark">Save</button>
            </div>
        </div>
    </div>
</div>