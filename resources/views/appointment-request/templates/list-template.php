<script type="text/x-jsrender" id="template-result-block">
<div class="mt-3">
	<table class="table table-bordered" id="script_document_maintable">
	    <thead>
	      	<tr>
				<th width="4%">ID</th>
				<th width="8%">Date</th>
				<th width="8%">Sender</th>
				<th width="8%">Receiver</th>
				<th width="8%">Requested Time</th>
				<th width="8%">Status</th>
			</tr>
	    </thead>
    	<tbody class="pending-row-render-view infinite-scroll-pending-inner">
    		{{props data}}
				
		      	<tr>
			      	<td>{{:prop.id}}</td>

			      	<td>{{:prop.created_at_date}}</td>

			      	<td>{{:prop.user.name}}</td>

			      	<td>{{:prop.userrequest.name}}</td>

			      	<td>
			      		{{:prop.requested_time}}

			      		{{if prop.remarks != ''}}
				      		<button type="button" data-id="{{>prop.id}}" class="btn requested-remarks-view" style="padding:1px 0px;">
			        			<i class="fa fa-exclamation-circle" aria-hidden="true"></i>
			        		</button>
		        		{{/if}}
			      	</td>

			      	<td>
			      		{{if prop.request_status == 0}}
                            Requested
                        {{/if}}

                        {{if prop.request_status == 1}}
                        	Accepeted
                        {{/if}}
                        
                        {{if prop.request_status == 2}}
                        	Decline

                        	{{if prop.decline_remarks != ''}}
					      		<button type="button" data-id="{{>prop.id}}" class="btn decline_remarks-view" style="padding:1px 0px;">
				        			<i class="fa fa-exclamation-circle" aria-hidden="true"></i>
				        		</button>
			        		{{/if}}
	                    {{/if}}
			      	</td>			      	

               	</tr>
		    {{/props}}  
	    </tbody>
	</table>
	{{:pagination}}
</div>
</script>
