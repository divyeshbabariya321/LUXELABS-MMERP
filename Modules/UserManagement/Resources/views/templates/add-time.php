<script type="text/x-jsrender" id="template-add-time">
    <form  method="post">
    <?php echo csrf_field(); ?>
		<div class="modal-content">
		   <div class="modal-header">
		      <h5 class="modal-title">{{if data.id}} Edit Avaibility {{else}}Add Avaibility{{/if}}</h5>
		      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		      <span aria-hidden="true">&times;</span>
		      </button>
		   </div>
		   <div class="modal-body">
           
           <input type="text" id="time_user_id" name="user_id" class="form-control" 
           value="{{if data.user_id}}{{:data.user_id}}{{/if}}">
           <div class="form-group">
               <strong>Day:</strong></br>
               <a>
                  <input value='monday' type="checkbox" name="day[]" {{if data.weekday_0 }} checked {{/if}}>
                  <strong>Monday</strong>
               </a>
               <a>
                  <input value='tuesday' type="checkbox" name="day[]" {{if data.weekday_1 }} checked {{/if}}>
                  <strong>Tuesday</strong>
               </a>
               <a>
                  <input value='wednesday' type="checkbox" name="day[]" {{if data.weekday_2 }} checked {{/if}} >
                  <strong>Wednesday</strong>
               </a>
               <a>
                  <input value='thursday' type="checkbox" name="day[]" {{if data.weekday_3 }} checked {{/if}}>
                  <strong>Thursday</strong>
               </a>
               <a>
                  <input value='friday' type="checkbox" name="day[]" {{if data.weekday_4 }} checked {{/if}}>
                  <strong>Friday</strong>
               </a>
               <a>
                  <input value='saturday' type="checkbox" name="day[]" {{if data.weekday_5 }} checked {{/if}}>
                  <strong>Saturday</strong>
               </a>
            </div>
           
         <div class="form-group">
            <strong>Available Day (eg. 6) <small> From Week </small> </strong>
            <input type="number"  class="form-control" name="availableDay" 
            value={{if data.day}}{{:data.day }}{{/if}}>
			</div>
         <div class="form-group">
            <strong>Available Hour (eg. 2) </strong>
            <input type="time" step="3600000" class="form-control" name="availableHour" 
            value={{if data.minute}}{{:data.minute}}{{/if}}>
			</div>
         <div class="form-group">
            <strong>Start Time </strong>
            <input type="time" step="3600000" class="form-control" name="startTime" 
            value={{if data.start_time}}{{:data.start_time}}{{/if}}>
			</div>
         <div class="form-group">
            <strong>End Time </strong>
            <input type="time" step="3600000" class="form-control" name="endTime" 
            value={{if data.end_time}}{{:data.end_time}}{{/if}}>
			</div>
         <div class="form-group">
            <strong>Lunch Time </strong>
            <input type="time" step="3600000"  class="form-control" name="lunchTime" 
            value={{if data.lunch_time}}{{:data.lunch_time}}{{/if}}>
			</div>
         <div class="form-group">
               <strong>Available From (eg. 10) <small>24 Hours format</small> </strong>
               <input type="date" step=0.1 class="form-control" name="from" value={{if data.from}}{{:data.from}}{{/if}}>
         </div>
            <div class="form-group">
                  <strong>Available To (eg. 18) <small>24 Hours format</small></strong>
                  <input type="date" step=0.1 class="form-control" name="to" value={{if data.to}}{{:data.to}}{{/if}}>
			</div>
            <div class="form-group">
                  <strong>Status</strong>
                  <select class="form-control" name="status">
                    <option value="1" {{if data.status == 1}} selected {{/if}}>Available</option>
                    <option value="0" {{if data.status == 0}} selected {{/if}}>Not Available</option>
                 </select>
			</div>
         <div class="form-group">
            <strong>Note</strong>
            <textarea class="form-control" name="note" id="" rows="3">{{if data.note}}{{:data.note}}{{/if}}</textarea>
         </div>
		   <div class="modal-footer">
		      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		      <button type="button"  class="btn btn-secondary submit-time" data-dismiss="modal">Submit</button>
		   </div>
		</div>
	</form> 
   
</script>
<script type="text/x-jsrender" id="template-view-time">
   <style>
   .display {
      display: flex;
      justify-content: space-evenly;
   }

   </style>

   <div class="modal-content">
      <div class="modal-header">
         <h5 class="modal-title">User Availibility Details</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
         </button>
      </div>
      <div class="modal-body">
         <div class="task_hours_section">
            {{if data.user_id }}
               <p><strong>User Id:</strong> <span>{{:data.user_id}} </span></><br>
            {{/if}}
            {{if data.weekday }}
               <p><strong>Day:</strong> <span>{{:data.weekday}} </span></><br>
            {{/if}}
            {{if data.start_time }}
               <p><strong>Start Time:</strong> <span>{{:data.start_time}} </span></><br>
            {{/if}}
            {{if data.end_time }}
               <p><strong>End Time:</strong> <span>{{:data.end_time}} </span></><br>
            {{/if}}
            {{if data.lunch_time }}
               <p><strong>Lunch Time:</strong> <span>{{:data.lunch_time}} </span></><br>
            {{/if}}
            {{if data.day }}
               <p><strong>Available Day:</strong> <span>{{:data.day}} </span></><br>
            {{/if}}
            {{if data.minute }}
               <p><strong>Available Hour:</strong> <span>{{:data.minute}} </span></><br>
            {{/if}}
            {{if data.date_from }}
               <p><strong>Available From:</strong> <span>{{:data.date_from}} </span></><br>
            {{/if}}
            {{if data.date_to }}
               <p><strong>Available To:</strong> <span>{{:data.date_to}} </span></><br>
            {{/if}}

            {{if data.status }}
               <p><strong>Status:</strong> <span>{{if data.status == 1}}
                     Available
                  {{/if}}
                  {{if data.status == 0}}
                     Not Available
                  {{/if}} </span></><br>
            {{/if}}
            {{if data.note }}
               <p><strong>Notes:</strong> <span>{{:data.note}} </span></><br>
            {{/if}}
            <p><strong>HubStaff Total Minute::</strong> <span> {{:data.userhubstafftotal}}</span></><br>
         </div>
      </div>
      <div class="modal-footer">
         <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
   </div>
</script>