<script type="text/x-jsrender" id="template-result-block">
	<div class="table-responsive mt-3">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th>Id</th>
				<th>Website</th>
				<th>Amount Ordered</th>
				<th>Amount Paid</th>
				<th>Amount Refunded</th>
				<th>Amount Cancelled</th>
				<th>Amount Authorized</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td>{{:prop.id}}</td>
			      	<td>{{:prop.website.title}}</td>
					<td>{{:prop.amount_ordered}}</td>
			        <td>{{:prop.amount_paid}}</td>
					<td>{{:prop.amount_refunded}}</td>
					<td>{{:prop.amount_canceled}}</td>
					<td>{{:prop.amount_authorized}}</td>
			      </tr>
			    {{/props}}
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>