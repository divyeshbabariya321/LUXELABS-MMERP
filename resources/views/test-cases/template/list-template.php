<script type="text/x-jsrender" id="template-result-block">
	<div class="mt-3">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th width="4%"><input type="checkbox" id="chkTaskDeleteCommon" name="chkTaskDeleteCommon" class="chkTaskDeleteCommon"> ID</th>
                <th width="6%">Date</th>
                <th width="7%">Name</th>
                <th width="6%">Suite</th>
                <th width="7%">Created By</th>
                <th width="6%">Module</th>
                <th width="7%">Precondition</th>
                <th width="8%">Assign to</th>
                <th width="5%">Step To Reproduce</th>
                <th width="7%">Expected Result</th>
                <th width="8%">Status</th>
                <th width="15%">Communication</th>
                <th width="7%">Website</th>
                <th width="7%">Action</th>
		      </tr>
		    </thead>
		    <tbody class="pending-row-render-view infinite-scroll-pending-inner">
		    	{{props data}}
			      <tr>
                    <td class='break'><input type="checkbox" id="chkTaskChange{{>prop.id}}" name="chkTaskNameChange[]" class="chkTaskNameClsChange"  value="{{>prop.id}}"></br>{{:prop.id}}</td>
			      	<td class='break'>{{:prop.created_at}}</td>
			        <td class='break' >{{:prop.name}}</td>
			        <td class='break'>{{:prop.suite}}</td>
			        <td class='break'>{{:prop.created_by}}</td>
			        <td class='break'>{{:prop.module_id}}</td>
			        <td class='break'>{{:prop.precondition}}</td>
			        <td class='break'>
			            <select class='form-control assign_to'  data-id="{{>prop.id}}" data-token=<?php echo csrf_token(); ?> >
			                <?php
                                foreach ($users as $key => $user) {
                                    echo "<option {{if prop.assign_to == '" . $key . "'}} selected {{/if}} value='" . $key . "'>" . $user . '</option>';
                                }
			            ?>
			            </select>
			          <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-user-test-history" title="Show History" data-id="{{:prop.id}}"><i class="fa fa-info-circle"></i></button>
			        </td>
			        <td class='break expand-row-msg' data-name="step_to_reproduce" data-id="{{:prop.id}}"><span class="show-short-Steps to reproduce-{{:prop.id}}">{{:prop.step_to_reproduce_short}}</span>
                        <span class="show-full-step_to_reproduce-{{:prop.id}} hidden" >{{:prop.step_to_reproduce}}</span>
                    </td>
			        <td class='break'>{{:prop.expected_result}}</td>
			        <td class='break'>
			            <select class='form-control test_case_status_id'  data-id="{{>prop.id}}" data-token=<?php echo csrf_token(); ?>>
			                <?php
			                foreach ($testCaseStatuses as $testCaseStatus) {
			                    echo "<option {{if prop.test_status_id == '" . $testCaseStatus->id . "'}} selected {{/if}} value='" . $testCaseStatus->id . "'>" . $testCaseStatus->name . '</option>';
			                }
			            ?>
			            </select>
			          <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-user-teststatus-history" title="Show History" data-id="{{:prop.id}}"><i class="fa fa-info-circle"></i></button>
			        </td>

			        <td class='break'>
			         <div style="margin-bottom:10px;width: 100%;">
                    <div class="d-flex">
                       <input type="text" style="width: 100%;" class="form-control quick-message-field input-sm" id="getMsg{{>prop.id}}" name="message" placeholder="Message" value=""><div style="max-width: 30px;">
                       <button class="btn btn-sm btn-image send-message" title="Send message" data-id="{{>prop.id}}"><img src="images/filled-sent.png" /></button> </div>
                        <div style="max-width: 30px;">
                        <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='testcase' data-id="{{:prop.id}}" title="Load messages"><img src="images/chat.png" alt=""></button>
                         </div>
                        </div>
                    </div>
			        </td>
			        <td class='break'>{{:prop.website}}</td>
			        <td>
			        <div class="d-flex">
			        	<button type="button" title="Edit" data-id="{{>prop.id}}" data-action="edit" class="btn btn-edit-template openTestCasesModal">
			        		<i class="fa fa-edit" aria-hidden="true"></i>
			        	</button>
			        	<button type="button" title="Push"  data-id="{{:prop.id}}" class="btn btn-push">
			        	<i class="fa fa-eye" aria-hidden="true"></i>
			        	</button>

			        	<button type="button" title="Delete" data-id="{{>prop.id}}" class="btn btn-delete-template">
			        		<i class="fa fa-trash" aria-hidden="true"></i>
			        	</button>
			        </div>
			        </td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>

</script>