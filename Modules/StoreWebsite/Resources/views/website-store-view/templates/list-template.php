<script type="text/x-jsrender" id="template-result-block">
	<div class="table-responsive mt-3">
		<table class="table table-bordered">
			<thead>
				<tr>
				    <th><input class="form-check-input" id="selectAllStoreViews" type="checkbox" style="height: 15px;">&nbsp;</th>
					<th>Id</th>
					<th>Website Name</th>
					<th>Name</th>
					<th>Code</th>
					<th>Sort Order</th>
					<th>Status</th>
					<th>Store Name</th>
					<th>Platform id</th>
					<th>Chat Group Store</th>
					<th>Actions</th>
				</tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			        <td><input class="form-check-input selected-website-views" type="checkbox" value="{{:prop.id}}"
			        name="selected_website_views[]"></td>
			      	<td>{{:prop.id}}</td>
			        <td class="name_div">
                        {{if prop.website_store !== null && prop.website_store.website !== null && prop.website_store.website.store_website !== null}}
                            {{:prop.website_store.website.store_website.title}}
                        {{/if}}
                    </td>
			        <td>{{:prop.name}}</td>
			        <td class="code_div">{{:prop.code}}</td>
			        {{if prop.status == 1}} 
			        	<td>Active</td>
			        {{else}}
			        	<td>In Active</td>
			        {{/if}}
			        <td>{{:prop.sort_order}}</td>
			        <td>{{:prop.website_store_name}}</td>
			        <td>{{:prop.platform_id}}</td>
			        <td>
								{{if prop.store_group_id == null}}
										<button type="button" title="Create" data-id="{{>prop.id}}" class="btn btn-create-group">
											<i class="fa fa-plus" aria-hidden="true"></i>
										</button>
								{{else}}
										<button type="button" title="Edit" data-id="{{>prop.id}}" data-store_group_id="{{>prop.store_group_id}}" class="btn btn-edit-group">
											<i class="fa fa-edit" aria-hidden="true"></i>
										</button>
										<button type="button" title="Delete" data-id="{{>prop.id}}" data-store_group_id="{{>prop.store_group_id}}" class="btn btn-delete-group">
											<i class="fa fa-trash" aria-hidden="true"></i>
										</button>
								{{/if}}
			        </td>
			        <td>
			        	<button type="button" title="Edit" data-id="{{>prop.id}}" class="btn btn-edit-template">
			        		<i class="fa fa-edit" aria-hidden="true"></i>
			        	</button>
			        	<button type="button" title="Push" data-id="{{>prop.id}}" class="btn btn-push">
			        		<i class="fa fa-upload" aria-hidden="true"></i>
			        	</button>
			        	<button type="button" title="Delete" data-id="{{>prop.id}}" class="btn btn-delete-template">
			        		<i class="fa fa-trash" aria-hidden="true"></i>
			        	</button>
			        </td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>
