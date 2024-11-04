<?php foreach($status as $s) {  ?>
    <tr data-id="<?php echo $s->id; ?>">
        <td><?php echo $s->id; ?></td>
        <td>
            <?php echo $s->status_name; ?>
        </td>
        <td>
            <div class="form-group"style="margin-bottom:-3px !important;">
                <textarea class="form-control text-editor-textarea" data-field="message"style="height: 34px"><?php echo $s->message; ?></textarea>
            </div>
        </td>
        <td>
            <button type="button" class="btn btn-delete-template no_pd"  data-id="<?php echo $s->id; ?>"><img width="15px" src="/images/delete.png"></button>
        </td>
    </tr>
<?php } ?>