<script type="text/x-jsrender" id="template-result-block">
	<div class="row">
		<table class="table table-bordered"style="table-layout:fixed;">
		    <thead>
		      <tr>
		      	<th width="2%"><?php echo '#'; ?></th>
		      	<th width="2%">Id</th>
		      	<th width="10%">Subject</th>
		      	<th width="10%">Subject Score</th>
		      	<th width="12%">StoreWebsite</th>
		      	<th width="9%">Send On</th>
		      	<th width="9%">Send At</th>
		      	<th width="10%">Mail List</th>
				<th width="10%">Mailing list Templates</th>
		      	<th width="10%">Products</th>
		      	<th width="9%">Updated by</th>
		      	<th width="9%">Language</th>
		        <th width="8%">Action</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td><input type="checkbox" value="{{:prop.id}}" name="check-product" class="check-product"></td>
			      	<td>{{:prop.id}}</td>
			      	<td class="Website-task">{{:prop.subject}}</td>
			      	<td class="Website-task">{{:prop.subject_score}}</td>
			      	<td>{{:prop.store_websiteName}}</td>
			      	<td>{{:prop.sent_on}}</td>
			      	<td>{{:prop.sent_at}}</td>
			      	<td>{{:prop.mailinglist_name}}</td>
					  <td>{{:prop.mailinglist_template_name}}</td>
			      	<td ><a href="javascript:;" class="show-more-image" style="color: #333;"data-attr="section_p_{{:prop.id}}">View All</a></td>
			      	<td>{{:prop.updated_by_name}}</td>
					<td>{{:prop.language}}</td>
			      	<td>
                        <div>
                            <button type="button" data-id="{{>prop.id}}" class="btn p-1 btn-edit-template" >
                            	<img width="15px" title="Edit" src="/images/edit.png">
                            </button>
                            <button type="button" data-id="{{>prop.id}}" class="btn p-1 btn-delete-template" title="Delete">
                            	<i class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                            <a href="newsletters/{{>prop.id}}/preview" class="p-1" title="Preview" style="color: gray;">
                            	<i class="fa fa-globe " aria-hidden="true"></i>
                            </a>
							{{if !prop.translated_from}}
								<a href="newsletters/{{>prop.id}}/translate" data-id="{{>prop.id}}" class="p-1 btn-translate" title="Translate to other Language" style="color: gray;">
									<i class="fa fa-language" aria-hidden="true"></i>
								</a>
							{{/if}}
							{{if !prop.subject_score && prop.translated_from}}
								<button type="button" title="get Translared Text Score" data-id="{{>prop.id}}" class="btn p-1 btn-get-translate-score">
									<i class="fa fa-dashboard" aria-hidden="true"></i>
								</button>
							{{/if}}
                        </div>
			        </td>
			      </tr>
			       <tr class="hideall" id="section_p_{{:prop.id}}" style="display:none;">
			       	<td colspan="9">
			      		 {{props prop.product_images}}
					        <div data-id="{{:prop.id}}" data-productid="{{:prop.product_id}}" class="l-container">
								<img height=60 width=60 src="{{:prop.url}}" class="l-image"/>
								<div class="l-middle btn-delete-image" data-id="{{:prop.id}}" data-productid="{{:prop.product_id}}">
									<div class="l-text"><i class="fa fa-trash"></i></div>
								</div>
		  					</div>
				        {{/props}}
				    </td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>