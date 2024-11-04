<div id="eventColor" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Event Colors</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('email.event.color') }}" method="POST">
                <?php echo csrf_field(); ?>
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Event Name</b></td>
                            <td class="text-center"><b>Color Code</b></td>
                            <td class="text-center"><b>Color</b></td>
                        </tr>
                        <?php
                        foreach ($eventColors as $eventColor) { ?>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;<?php echo $eventColor->name; ?></td>
                            <td class="text-center"><?php echo $eventColor->color; ?></td>
                            <td class="text-center"><input type="color" name="color_name[<?php echo $eventColor->id; ?>]" class="form-control" data-id="<?php echo $eventColor->id; ?>" id="color_name_<?php echo $eventColor->id; ?>" value="<?php echo $eventColor->color; ?>" style="height:30px;padding:0px;"></td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submit-status-color">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
