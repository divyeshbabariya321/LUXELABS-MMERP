<script type="text/x-jsrender" id="template-website-push-logs">
<div class="modal-content">
	<div class="modal-header">
		<h4 class="modal-title">Website Push Logs</h4>
		<button type="button" class="close" data-dismiss="modal">×</button>
	</div>
	<div class="modal-body">
		<div class="table-responsive mt-3" id="website-push-log-table">
			<table class="table table-bordered">
				<thead>
				<tr>
					<th>Name</th>
					<th>Type</th>
					<th>Message</th>
				</tr>
				</thead>
				<tbody>
					{{props data}}
					<tr>
						<td>{{:prop.name}}</td>
						<td>{{:prop.type}}</td>
						<td>{{:prop.message}}</td>
					</tr>
					{{/props}}
				</tbody>
			</table>
			{{:pagination}}
		</div>
	</div>
</div>	
</script>