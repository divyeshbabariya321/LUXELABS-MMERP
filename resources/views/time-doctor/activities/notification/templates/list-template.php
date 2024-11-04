<script type="text/x-jsrender" id="template-result-block">
	<div class="row">
		<table class="table table-bordered">
		    <thead>
		      <tr>
			  <th width="2%"></th>
		      	<th width=13%">User</th>
		        <th width="10%">Start Date</th>
		        <th width="5%">Daily Availble hr</th>
		        <th width="5%">Total Working hr</th>
		        <th width="8%">Different</th>
		        <th width="2%">Min Percentage</th>
		        <th width="2%">Actual Percentage</th>
		        <th width="10%">Reason</th>
		        <th width="8%">Status</th>
		        <th width="25%">Communnication</th>
		        <th width="10%">Action</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td>
			      		<input type="checkbox" class="activity-notification-ckbx" name="activity_notification[]" value="{{:prop.user_id}}"/>
			      	</td>
			      	<td>{{:prop.user_name}}</td>
			        <td>{{:prop.start_date}}</td>
			        <td>{{:prop.daily_working_hour}}</td>
			        <td>{{:prop.total_working_hour}}</td>
			        <td>{{:prop.different}}</td>
			        <td>{{:prop.min_percentage}}</td>
			        <td>{{:prop.actual_percentage}}</td>
			        <td>{{:prop.reason}}</td>
			        <td>{{if prop.status == 1}} Approved {{else}} Pending {{/if}}</td>
			        <td>
						<div style="display:flex;" class="time_doctor_chat_message">
							<textarea rows="1" class="form-control quick-message-field cls_quick_message" id="messageid_{{:prop.user_id}}" name="message" placeholder="Message" style="width:calc(100% - 45px)"></textarea>
							<div style="width:45px;display:flex;">
								<button class="btn btn-sm btn-image send-message1 pt-0 pb-0 pl-2 pr-0" data-timedoctorid="{{:prop.user_id}}"><img src="/images/filled-sent.png"/></button>
								<button type="button" class="btn  btn-image load-communication-modal pt-0 pb-0 pl-2 pr-0" data-object="timedoctor" data-is_admin="{{:prop.is_admin}}" data-is_hod_crm="{{:prop.is_hod_crm}}"  data-id="{{:prop.user_id}}" data-load-type="text" data-all="1" title="Load messages"><img src="/images/chat.png" alt=""></button>
							</div>
						</div>
						<span class="td-mini-container message-chat-txt" id="message-chat-txt-{{:prop.user_id}}">{{:prop.latest_message}}</span>
					</td>
			        <td>
					<button type="button" data-id="{{>prop.id}}" class="btn btn-xs btn-edit-reason">
			        	<i class="fa fa-comment"></i>
			        </button>
					<button type="button" data-id="{{>prop.id}}" class="btn btn-xs btn-change-status">
			        	<i class="fa fa-edit"></i>
			        </button>
					</td>
			      </tr>
			    {{/props}}



			   {{if user_id > "0" }} () 
			    <tr>
			        <td></td>
			        <td></td>
			        <td></td>
			        <td></td>
			        <td>{{:sum}}</td>
			        <td></td>
			        <td></td>
			        <td></td>
			        <td></td>
			        <td></td>
			        <td></td>
			        <td></td>
			      </tr>
			   {{/if}}   


		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>

<script type="text/x-jsrender" id="template-edit-reason">
<div class="modal-content">
   <div class="modal-header">
      <h5 class="modal-title">Add Reason</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      	<span aria-hidden="true">&times;</span>
      </button>
   </div>
   <div class="modal-body">
		<div class="row">
			<div class="col-lg-12">
				<form>
					<?php echo csrf_field(); ?>
					<input type="hidden" name="id" value="{{:id}}">
					<div class="row">
				  		<div class="col-md-12">
				    		<div class="form-group">
							{{ 
								html()->textarea('reason')
									->class('form-control')
							}}
					         </div>
				        </div> 
				        <div class="col-md-12">
					    	<div class="form-group">
					      		<button class="btn btn-secondary store-reason-btn">Save</button>
					    	</div>
				    	</div>
				  	</div>
				</form>
			</div>
		</div>
	</div>
</div>
</script>


<script type="text/x-jsrender" id="template-change-status">
<div class="modal-content">
   <div class="modal-header">
      <h5 class="modal-title">Change Status</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      	<span aria-hidden="true">&times;</span>
      </button>
   </div>
   <div class="modal-body">
		<div class="row">
			<div class="col-lg-12">
				<form>
					<?php echo csrf_field(); ?>
					<input type="hidden" name="id" value="{{:id}}">
					<div class="row">
				  		<div class="col-md-12">
				    		<div class="form-group">
							{{ 
								html()->select('status', ['0' => 'Pending', '1' => 'Approved'])
									->class('form-control')
							}}
					         </div>
				        </div> 
				        <div class="col-md-12">
					    	<div class="form-group">
					      		<button class="btn btn-secondary submit-change-status">Change</button>
					    	</div>
				    	</div>
				  	</div>
				</form>
			</div>
		</div>
	</div>
</div>
</script>