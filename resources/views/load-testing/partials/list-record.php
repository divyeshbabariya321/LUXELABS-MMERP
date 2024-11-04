<script type="text/x-jsrender" id="product-templates-result-block">
	<div class="row col-md-12">
		<table class="table table-bordered"style="display:table;table-layout:fixed;" id="task_Tables">
		    <thead>
		      <tr>
		      	<th width="5%">Id</th>
		        <th width="10%">No of Virtual User</th>
		        <th width="5%">Ramp Time</th>
		        <th width="5%">Duration</th>
		        <th width="5%">Delay</th>
		        <th width="5%">Loop Count</th>
		        <th width="10%">Domain Name</th>
		        <th width="5%">Protocols</th>
		        <th width="10%">Path</th>
		        <th width="5%">Request Method</th>
		        <th width="10%">Request</th>
		        <th width="5%">Response</th>
				<th width="5%">Created At</th>
				<th width="5%">Action</th>
		      </tr>
		    </thead>
		    <tbody>
			{{if result.data.length === 0}}
		      		<tr>
						<td colspan="12">No result found</td>
					</tr>
		    	{{else}}
		    	{{props result.data}}
			      <tr>
			      	<td>{{>prop.id}}</td>
			        <td>{{>prop.no_of_virtual_user}}</td>
			        <td>{{>prop.ramp_time}}</td>
			        <td>{{>prop.duration}}</td>
			        <td>{{>prop.delay}}</td>
			        <td>{{>prop.loop_count}}</td>
			        <td>{{>prop.domain_name}}</td>
			        <td>{{>prop.protocols}}</td>
			        <td>{{>prop.path}}</td>
			        <td>{{>prop.request_method}}</td>
					<td>-</td>
					<td>{{>prop.jmeter_api_response == '1'? 'Success': (prop.jmeter_api_response == '0 ' ? 'Fail' : '')}}</td>
			        <td>{{>prop.created_at}}</td>
					<td><button type="button" class="btn btn-secondary btn-sm" onclick="Showactionbtn('{{>prop.id}}')"><i
                                class="fa fa-arrow-down"></i></button></td>
			      </tr>
				  	<tr class="action-btn-tr-{{>prop.id}} d-none">
						<td class="font-weight-bold">Action</td>
						<td colspan="11">
							{{if prop.status === 0}}
								<a class="btn btn-default" onclick="submitJmeterRequest('{{>prop.id}}')">
								Load Test</a>
							{{else prop.status === 1}}
								<a class="btn btn-default">In Process</a>
							{{else prop.status === 2}}
								<a class="btn btn-default" onclick="submitJmeterRequest('{{>prop.id}}')">Load Test Regenerate</a>
							{{else prop.status === 3}}
								<a class="btn btn-default" href="/load-testing/view-result/{{>prop.id}}">View Result</a>
							{{else prop.status === 4}}
								<a class="btn btn-default" href="/load-testing/view-result/{{>prop.id}}">View Result</a>
							{{/if}}
						</td>
					</tr>
			    {{/props}}  
				{{/if}}
		    </tbody>
		</table>
		{{:pagination}}
	</div>
	<div id="showResponse" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
				<div class="modal-head">
					Response
				</div>
                <div class="modal-body">
                    <div class="form-group mr-3" id="responseBody">
                        
                    </div>
                </div>
                <div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
	<div id="loading-image-preview" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')50% 50% no-repeat;display:none;">
    </div>
</script>
<script lang="javascript">
	function showResponse(id) {
		var data = id;
		$.ajax({
            method : "get",
			url: "/load-testing/get-jmter-response/"+id,
			data : [],
			beforeSend: function () {
                    $("#loading-image").show();
                },
			success: function (response) {
				$("#loading-image").hide();
				$("#showResponse").modal("show");
				$("#responseBody").html(response.response);		
			},
			error: function (xhr, status, error) {
				toastr['error']("Invalid JSON response", 'error');
			},
			
        });
	}
</script>