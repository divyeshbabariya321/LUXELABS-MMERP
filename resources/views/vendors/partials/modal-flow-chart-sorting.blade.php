<div id="setFlowChartSorting" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Set Flow Chart Sorting</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('vendors.flowchart-sort-order') }}" method="POST">
                <?php echo csrf_field(); ?>
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Flow Chart</b></td>
                            <td class="text-center"><b>Sorting</b></td>
                        </tr>
                        <?php
                        foreach ($vendor_flow_charts as $vendorflowchart) { ?>
                        <tr>
                            <td><?php echo $vendorflowchart->name; ?></td>
                            
                            <td style="text-align:center;">
                                <input type="number" name="sorting[<?php echo $vendorflowchart->id; ?>]" class="form-control" value="<?php echo $vendorflowchart->sorting; ?>">
                            </td>                              
                        </tr>
                        <?php } ?>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>

    </div>
</div>
