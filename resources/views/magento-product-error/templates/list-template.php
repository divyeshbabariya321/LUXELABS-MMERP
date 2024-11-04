<script type="text/x-jsrender" id="template-result-block">
	<div class="table-responsive mt-3">
		<table class="table table-bordered"style="table-layout:fixed;">
		    <thead>
		      <tr>
		      	<th width="7%">Product ID</th>
		        <th width="10%">Date</th>
		        <th width="12%">Website</th>
		        <th width="16%">Message</th>
		        <th width="8%">Request data</th>
		        <th width="8%">Response Data</th>
		        <th width="15%">Condition Checked</th>
		        <th width="12%">Status</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td style="color:black; vertical-align: middle;padding:4px;">{{:prop.product_id}}</td>
			      	<td style="vertical-align: middle;padding: 4px;">{{:prop.updated_at}}</td>
			      	<td style="vertical-align: middle;padding: 4px;">{{:prop.store_website}}</td>
			      	<td class="message_load_data Website-task" style="vertical-align: middle;">{{:prop.message}}</td>
			      	<td class="request_message_load_data" style="vertical-align: middle;">{{:prop.request_data}}</td>
			      	<td class="response_message_load_data" style="vertical-align: middle;">{{:prop.response_data}}</td>
					<td style="vertical-align: middle; vertical-align: middle;">{{:prop.condition_id}}</td>
			      	<td style="vertical-align: middle; vertical-align: middle;">{{:prop.response_status}}  </td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
	</div>
	{{:pagination}}
</script>


<script type="text/x-jsrender" id="template-load-data">
	<div class="modal-content">
	   <div class="modal-header">
	      
	      <h5 class="modal-title"></h5>
	      
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	      <span aria-hidden="true">&times;</span>
	      </button>
	   </div>
	   <div class="modal-body">
			{{:data}}
		</div>
	</div>


</script>
<script>

    $(document).ready(function() {
        $(".globalSelect2").select2();
    });

</script>