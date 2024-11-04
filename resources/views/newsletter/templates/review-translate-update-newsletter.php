<script type="text/x-jsrender" id="form-create-newsletters">
	<form name="form-create-landing-page" method="post">
		<?php echo csrf_field(); ?>
		<div class="modal-content">
		   <div class="modal-header">
		      <h5 class="modal-title">Edit Newsletter Translation</h5>
		      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		      <span aria-hidden="true">&times;</span>
		      </button>
		   </div>
		   <div class="modal-body">
				{{if data}}
					<input type="hidden" name="id" value="{{:data.id}}"/>
					<input type="hidden" name="is_flagged_translation" value="0"/>
					<input type="hidden" name="approved_by_user_id" value="<?php echo auth()->user()->id; ?>"/>
				{{/if}}
				<div class="form-group">
					<label for="subject">Subject</label>
					<input type="text" name="subject" value="{{if data}}{{:data.subject}}{{/if}}" class="form-control" id="subject" placeholder="Enter subject">
				</div>
	        </div>
		   <div class="modal-footer">
		      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		      <button type="button" class="btn btn-secondary submit-platform">Save Changes & Approve Translate</button>
		   </div>
		</div>
	</form>
</script>