<script type="text/x-jsrender" id="product-templates-result-block">
	<div class="row pl-4 pr-4 pt-3">
		<table class="table table-bordered" style="table-layout: fixed;">
		    <thead>
		      <tr>
		      	<th width="2%">Id</th>
		        <th width="15%">Name</th>
		        <th width="6%">Image</th>
		        <th width="5%">No Of Images</th>
		        <th width="7%">UID</th>
		        <th width="5%">Created At</th>
		        <th width="2%">Action</th>
		      </tr>
		    </thead>
		    <tbody>
		    
		    	{{props result.data}}
			      <tr>
			      	<td style="vertical-align: middle;">{{>prop.id}}</td>
			      	<td style="vertical-align: middle;">{{>prop.name}}</td>
			      	<td style="vertical-align: middle;"><img src="{{>prop.image}}" width="80px" height="69px" onclick="bigImg('{{>prop.image}}')"></td>
			      	<td style="vertical-align: middle;">{{>prop.no_of_images}}</td>
			      	<td style="vertical-align: middle;">{{>prop.uid}}</td>
			        <td style="vertical-align: middle;">{{>prop.created_at}}</td>
			        <td style="vertical-align: middle;"><button type="button" class="btn btn-delete" onclick="editTemplate('{{>prop.id}}','{{>prop.name}}','{{>prop.image}}','{{>prop.no_of_images}}','{{>prop.auto_generate_product}}','{{>prop.uid}}')"style="padding: 0px 1px !important;"><img width="15px" src="/images/edit.png"></button>
			        <button type="button" data-uid="{{>prop.uid}}" data-id="{{>prop.id}}" class="btn btn-delete-template"style="padding: 0px 1px !important;"><img width="15px" src="/images/delete.png"></button>
					</td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>
