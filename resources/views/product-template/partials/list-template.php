<script type="text/x-jsrender" id="product-templates-result-block">
	<div class="row col-md-12">
		<table class="table table-bordered"style="display:table;table-layout:fixed;">
		    <thead>
		      <tr>
		      	<th width="2%">Id</th>
		        <th width="5%">Tem no</th>
		        <th width="6%">Pro Title</th>
		        <th width="5%">Brand</th>
		        <th width="5%">Currency</th>
		        <th width="4%">Price</th>
		        <th width="6%">Dis price</th>
		        <th width="5%">Product</th>
		        <th width="4%">Text</th>
		        <th width="6%">Fon Style</th>
		        <th width="6%">Font size</th>
		        <th width="9%">Back color</th>
		        <th width="4%">status</th>
		        <th width="9%">Website</th>
		        <th width="11%">Crea at</th>
		        <th width="6%">Action</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props result.data}}
			      <tr>
			      	<td>{{>prop.id}}</td>
			        <td>{{>prop.template_no}}</td>
			        <td>{{>prop.product_title}}</td>
			        <td class="Website-task"title="{{>prop.brand_name}}">{{>prop.brand_name}}</td>
			        <td>{{>prop.currency}}</td>
			        <td>{{>prop.price}}</td>
			        <td>{{>prop.discounted_price}}</td>
			        <td>{{>prop.product_id}}</td>
			        <td>{{>prop.text}}</td>
			        <td>{{>prop.font_style}}</td>
			        <td>{{>prop.font_size}}</td>
			        <td>{{>prop.background_color}}</td>
			        <td>{{>prop.template_status}}</td>
			        <td>{{>prop.website_name}}</td>
			        <td>{{>prop.created_at}}</td>
			        <td><button type="button" data-id="{{>prop.id}}" class="pr-1 pl-1 btn btn-delete-template"><img width="15px" src="/images/delete.png"></button>
			        	<button type="button" data-id="{{>prop.id}}" data-image="{{>prop.image_url}}" onClick="bigImg('{{>prop.image_url}}')" class="pr-1 pl-1 border-0 bg-transparent btn-sm show-image"><i class="fa fa-picture-o"></i></button>
			        	<button type="button" data-id="{{>prop.id}}" data-uid="{{>prop.uid}}" class="pr-1 pl-1 border-0 bg-transparent btn-sm reload-image" title="Reload image"><i class="fa fa-refresh"></i></button>
			        </td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>
