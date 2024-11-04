<?php 
foreach($data as $prop) {?>

	<tr>
	   	<td><?php echo $prop->id;  ?></td>

	   	<td><?php echo $prop->created_at_date;  ?></td>

	   	<td><?php echo $prop->script_type;  ?></td>

	   	<td><?php echo $prop->file;  ?></td>

	   	<!-- <td class='break expand-row-msg' data-name="url" data-id="<?php echo $prop->id;  ?>">
			<button class="btn btn-sm upload-script_documents-files-button" type="button" title="Uploaded Files" data-script_document_id="<?php echo $prop->id;  ?>">
				<i class="fa fa-cloud-upload" aria-hidden="true"></i>
			</button>
			<button class="btn btn-sm view-script_documents-files-button" type="button" title="View Uploaded Files" data-script_document_id="<?php echo $prop->id;  ?>">
				<img src="/images/google-drive.png" style="cursor: nwse-resize; width: 12px;">
			</button>
	 	</td> -->

	 	<td><?php echo $prop->description; ?></td>

	   	<td><?php echo $prop->usage_parameter; ?></td>

	   	<td><?php echo $prop->category; ?></td>

	   	<td>
	      	<button type="button" data-id="<?php echo $prop->id;  ?>" class="btn script-document-comment-view" style="padding:1px 0px;">
    			<i class="fa fa-eye" aria-hidden="true"></i>
    		</button>
		</td>

	   	<td><?php echo $prop->author; ?></td>

	   	<td><?php echo $prop->location; ?></td>

	   	<td><?php echo $prop->last_run; ?></td>

	   	<td>
	      	<button type="button" data-id="<?php echo $prop->id;  ?>" class="btn script-document-last_output-view" style="padding:1px 0px;">
    			<i class="fa fa-eye" aria-hidden="true"></i>
    		</button>
		</td>

	   	<td><?php echo $prop->status; ?></td>

	   	<td>
            <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn(<?php echo $prop->id;  ?>)"><i class="fa fa-arrow-down"></i></button>
        </td>

	     <!-- <td>		
		 	<div  class="d-flex">
				<button type="button" title="Edit" data-id="<?php echo $prop->id;  ?>" class="btn btn-edit-template">
	     			<i class="fa fa-edit" aria-hidden="true"></i>
				</button>
				
				<button type="button" title="Delete" data-id="<?php echo $prop->id;  ?>" class="btn btn-delete-template 11">
					<i class="fa fa-trash" aria-hidden="true"></i>
				</button>

				<button type="button" data-id="<?php echo $prop->id;  ?>" class="btn script-document-history" style="padding:1px 0px;">
        			<i class="fa fa-info-circle" aria-hidden="true"></i>
        		</button>
		 	</div>
	     </td> -->
	</tr>

	<tr class="action-btn-tr-<?php echo $prop->id;  ?> d-none">
        <td>Action</td>
        <td colspan="13">
        	<div  class="d-flex">
				<button type="button" title="Edit" data-id="<?php echo $prop->id;  ?>" class="btn btn-edit-template">
        		<i class="fa fa-edit" aria-hidden="true"></i>
				</button>
				
				<button type="button" title="Delete" data-id="<?php echo $prop->id;  ?>" class="btn btn-delete-template">
					<i class="fa fa-trash" aria-hidden="true"></i>
				</button>

				<button type="button" data-id="<?php echo $prop->id;  ?>" class="btn script-document-history">
        			<i class="fa fa-info-circle" aria-hidden="true"></i>
        		</button>

        		<button title="create quick task" type="button" class="btn create-quick-task " data-id="<?php echo $prop->id;  ?>"  data-category_title="Script Document Page" data-title="<?php echo $prop->file;  ?> - <?php echo $prop->id;  ?>"><i class="fa fa-plus" aria-hidden="true"></i></button>

                <button type="button" class="btn count-dev-customer-tasks" title="Show task history" data-id="<?php echo $prop->id;  ?>" data-category="<?php echo $prop->id;  ?>"><i class="fa fa-list"></i></button>
		 	</div>
        </td>
       </tr>
<?php 
} ?>