<?php
foreach ($modelColors as $bugstatus) { ?>
<tr>
    <td>&nbsp;&nbsp;&nbsp;<?php echo $bugstatus->model_name; ?></td>
    <td class="text-center"><?php echo $bugstatus->color_code; ?></td>
    <td class="text-center"><input type="color" name="color_name[<?php echo $bugstatus->id; ?>]" class="form-control" data-id="<?php echo $bugstatus->id; ?>" id="color_name_<?php echo $bugstatus->id; ?>" value="<?php echo $bugstatus->color_code; ?>" style="height:30px;padding:0px;"></td>
</tr>
<?php } ?>