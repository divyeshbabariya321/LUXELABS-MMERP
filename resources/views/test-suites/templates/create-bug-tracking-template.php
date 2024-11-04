<script type="text/x-jsrender" id="template-create-website">
	<form name="form-create-website" method="post">
		<?php echo csrf_field(); ?>
		<div class="modal-content">
		   <div class="modal-header">
		      <h5 class="modal-title">{{if data.id}} Edit Website Store {{else}}Create Bug Tracker{{/if}}</h5>
		      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		      	<span aria-hidden="true">&times;</span>
		      </button>
		   </div>
		   <div class="modal-body">
		      <div class="form-row">
		         {{if data}}
		         	<input type="hidden" name="id" value="{{:data.id}}"/>
		         {{/if}}
		         
		      </div>
		      <div class="form-group col-md-6">
	            <label for="name">Name</label>
	            <input type="text" name="name" value="{{if data}}{{:data.name}}{{/if}}" class="form-control" id="name" placeholder="Enter name">
	         </div>
			 <div class="form-group col-md-6">
	            <label for="name">Test Cases</label>
	            <input type="text" name="test_cases" value="{{if data}}{{:data.test_cases}}{{/if}}" class="form-control" id="name" placeholder="Enter Test Cases">
	         </div>
	           <div class="form-group col-md-6">
	            <label for="name">Step To Reproduce</label>
	            <input type="text" name="step_to_reproduce" value="{{if data}}{{:data.step_to_reproduce}}{{/if}}" class="form-control" id="name" placeholder="Enter Step To Reproduce">
	         </div>
              <div class="form-group col-md-6">
	            <label for="name">ScreenShot/ Video Url</label>
	            <input type="text" name="url" value="{{if data}}{{:data.url}}{{/if}}" class="form-control" id="name" placeholder="Enter ScreenShot/ Video Url">
	         </div>
		     
		      <div class="form-group col-md-6">
		         <label for="bug_environment_id">Environment</label>
		         <select name="bug_environment_id" class="form-control">
	            	<option value="">-- N/A --</option>
		            <?php
            foreach ($bugEnvironments as  $bugEnvironment) {
                echo "<option {{if data.bug_environment_id == '" . $bugEnvironment->id . "'}} selected {{/if}} value='" . $bugEnvironment->id . "'>" . $bugEnvironment->name . '</option>';
            }
		?>
		         </select>
		      </div>
		       <div class="form-group col-md-6">
		         <label for="assign_to">Assign To</label>
		         <select name="assign_to" class="form-control">
	            	<option value="">-- N/A --</option>
		            <?php
		    foreach ($users as  $user) {
		        echo "<option {{if data.assign_to == '" . $user->id . "'}} selected {{/if}} value='" . $user->id . "'>" . $user->name . '</option>';
		    }
		?>
		         </select>
		      </div>
		       
		        <div class="form-group col-md-6">
		         <label for="bug_status_id">Status</label>
		         <select name="bug_status_id" class="form-control">
	            	<option value="">-- N/A --</option>
		            <?php
		foreach ($bugStatuses as  $bugStatus) {
		    echo "<option {{if data.bug_status_id == '" . $bugStatus->id . "'}} selected {{/if}} value='" . $bugStatus->id . "'>" . $bugStatus->name . '</option>';
		}
		?>
		         </select>
		      </div>
		      <div class="form-group col-md-6">
		         <label for="module_id">Module/Feature</label>
		         <select name="module_id" class="form-control">
	            	<option value="">-- N/A --</option>
		            <?php
		    foreach ($filterCategories as  $filterCategory) {
		        echo "<option {{if data.module_id == '" . $filterCategory . "'}} selected {{/if}} value='" . $filterCategory . "'>" . $filterCategory . '</option>';
		    }
		?>
		         </select>
		      </div>
               <div class="form-group col-md-6">
		         <label for="website">Website</label>
		         <select name="website" class="form-control">
	            	<option value="">-- N/A --</option>
		            <?php
		    foreach ($filterWebsites as  $filterWebsite) {
		        echo "<option {{if data.website == '" . $filterWebsite . "'}} selected {{/if}} value='" . $filterWebsite . "'>" . $filterWebsite . '</option>';
		    }
		?>
		         </select>
		      </div>
              <div class="form-group col-md-6">
	            <label for="name">Remark</label>
	            <textarea name="remark" value="" class="form-control" id="remark" > {{if data}}{{:data.remark}}{{/if}}</textarea>
	         </div>
		   </div>
		   <div class="modal-footer">
		      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		      <button type="button" class="btn btn-primary submit-store-site">Save changes</button>
		   </div>
		</div>
	</form>  	
</script>