<div id="newMasterFlowChartModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="mt-0">Add new Master Flow Chart</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form style="padding:10px;" method="POST" id="add-master-flow-chart-form">
                @csrf

                <div class="form-group">
                    <input type="text" class="form-control" name="title" placeholder="Title" value="{{ old('title') }}" required>

                    @if ($errors->has('title'))
                        <div class="alert alert-danger">{{$errors->first('title')}}</div>
                    @endif
                </div>

                <button class="btn btn-secondary add-master-flow-chart-btn">Add Master Flow Chart</button>
            </form>

            <div class="form-group col-md-12">
                <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                    <thead>
                        <tr>
                            <td class="text-center"><b>Title</b></td>
                            <td class="text-center"><b>Actions</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(count($flowchart_master) > 0){
                        foreach ($flowchart_master as $vendorflowchart) { ?>
                            <tr>
                                <td>
                                    <?php echo $vendorflowchart->title; ?>
                                </td>
                                <td>
                                    <a class="btn btn-image save-master-flow-chart-btn abtn-pd" style="display: none" data-id="<?php echo $vendorflowchart->id; ?>"><img src="/images/send.png" style="cursor: nwse-resize; width: 16px;"></a>
                                    <a class="btn btn-image edit-master-flow-chart-btn abtn-pd" data-id="<?php echo $vendorflowchart->id; ?>"><img src="/images/edit.png" style="cursor: nwse-resize; width: 16px;"></a>
                                    <a class="btn delete-master-flow-chart-btn abtn-pd padding-top-action" data-id="<?php echo $vendorflowchart->id; ?>" href="#"><img src="/images/delete.png" style="cursor: nwse-resize; width: 16px;"></a>
                                </td>
                            </tr>
                        <?php }} ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>