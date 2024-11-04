<script type="text/x-jsrender" id="template-result-block">
	<div class="row">
		<table class="table table-bordered"style="table-layout:fixed;">
		    <thead>
		      <tr>
		      	<th width="5%"><?php echo '#'; ?></th>
		      	<th width="5%">Id</th>
		      	<th width="20%">StoreWebsite</th>
				<th width="25%">Original Subject</th>
				<th width="25%">Subject</th>
		      	<th width="10%">Updated by</th>
		      	<th width="10%">Action</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      	<tr>
						<td><input type="checkbox" value="{{:prop.id}}" name="check-product" class="check-product"></td>
						<td>{{:prop.id}}</td>
						<td>{{:prop.store_websiteName}}</td>
						<td class="Website-task">{{:prop.original_newsletter.subject}}</td>
						<td class="Website-task">{{:prop.subject}}</td>
						<td>{{:prop.updated_by_name}}</td>
						<td>
							<div>
								<button type="button" data-id="{{>prop.id}}" class="btn p-1 btn-edit-template" >
									<img width="15px" title="Edit" src="/images/edit.png">
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