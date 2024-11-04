<script type="text/x-jsrender" id="template-bug-status-color">
            <form name="form-create-status" method="post">
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Bug Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="form-group col-md-12">
					
						<table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
							<tr>
								<td style="text-align:center;"><b>Status Name</b></td>
								<td style="text-align:center;"><b>Color Code</b></td>	
								<td style="text-align:center;"><b>Color</b></td>								
							</tr>
							<?php
                            foreach ($bugStatuses as $bugstatus) { ?>
							<tr>
								<td>&nbsp;&nbsp;&nbsp;<?php echo $bugstatus->name; ?></td>
								<td style="text-align:center;"><?php echo $bugstatus->bug_color; ?></td>
								<td style="text-align:center;"><input type="color" name="color_name[<?php echo $bugstatus->id; ?>]" class="form-control" data-id="<?php echo $bugstatus->id; ?>" id="color_name_<?php echo $bugstatus->id; ?>" value="<?php echo $bugstatus->bug_color; ?>" style="height:30px;padding:0px;"></td>								
							</tr>
							<?php } ?>
							
							
						</table>
						
						
                    </div>
                    <div class="modal-footer">
                       <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                       <button type="button" class="btn btn-primary submit-status-color">Save changes</button>
                    </div>
                </div>
            </form>
</script>
