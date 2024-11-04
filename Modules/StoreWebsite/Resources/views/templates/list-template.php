<script type="text/x-jsrender" id="template-result-block">
	<div class="col-md-12 pl-5 pr-5">
	<div class="table-responsive">
		<table class="table table-bordered"style="table-layout: fixed;">
		    <thead>
		      <tr>
		      	<th width="5%" class="Website-task"title="Id">Id</th>
		        <th width="8%" class="Website-task"title="Title">Title</th>
		        <th width="12%"class="Website-task"title="Website">Website</th>
		        <th width="8%" class="Website-task"title="Country Duty">Country Duty</th>
		        <th width="12%" class="Website-task"title="Description">Description</th>
		        <th width="6%" class="Website-task"title="Service id">Service id</th>
		        <th width="6%" class="Website-task-warp"title="Remote software">Remote software</th>
		        <th width="6%" class="Website-task"title="Facebook">Facebook</th>
		        <th width="6%" class="Website-task"title="Instagram">Instagram</th>
		        <th width="6%" class="Website-task"title="Published">Published</th>
				<th width="8%" class="Website-task"title="FCM Key">FCM Key</th>
				<th width="8%" class="Website-task"title="FCM Id">FCM Id</th>
				<th width="6%" class="Website-task"title="Icon">Icon</th>
				<th width="4%" class="Website-task"title="Action">Action</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td>{{:prop.id}}</td>
			        <td class="Website-task-warp" title="{{:prop.title}}">{{:prop.title}}</td>
			        <td class="Website-task-warp">{{:prop.website}}</td>
			        <td class="Website-task">{{:prop.country_duty}}</td>
			        <td class="Website-task-warp">{{:prop.description}}</td>
			        <td>{{:prop.mailing_service_id}}</td>
			        <td>{{:prop.store_code_name}}</td>
			        <td>{{:prop.remote_software}}</td>
			        <td>
			        	{{:prop.facebook}}
			        	<i class="fa fa-comment show-facebook-remarks" data-id="{{>prop.id}}" data-value="{{:prop.facebook_remarks}}"></i>
			        </td>
			        <td>{{:prop.instagram}}
			        	<i class="fa fa-comment show-instagram-remarks" data-id="{{>prop.id}}" data-value="{{:prop.instagram_remarks}}"></i>
			        </td>
					<td>{{if prop.is_published == 1}}Yes{{else}}No{{/if}}</td>
					<td class="Website-task-warp">{{if prop.push_web_key !=null}} {{:prop.push_web_key.substring(0, 10)+'..'}} {{else}} {{/if}} </td>
					<td class="Website-task-warp">{{if prop.push_web_id !=null}} {{:prop.push_web_id.substring(0, 10)+'..'}} {{else}} {{/if}}
					{{if prop.icon !=null}} <img width="25px" height="25px" alt="" src="{{:prop.icon}}""> {{else}} {{/if}} </td>
					<td>
					    <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{>prop.id}}')"><i class="fa fa-arrow-down"></i></button>
					</td>
			      </tr>
			      <tr class="action-btn-tr-{{>prop.id}} d-none">
                  <td class="font-weight-bold text-left" title="Action">Action</td>
			      <td colspan="12" p-0>
			        	<button type="button" data-id="{{>prop.id}}" class="btn btn-edit-template" style="padding:1px 0px;">
			        		<i class="fa fa-edit" aria-hidden="true"></i>
			        	</button>

			        	<button type="button" data-id="{{>prop.id}}" class="btn btn-delete-template"style="padding:1px 0px;">
			        		<i class="fa fa-trash" aria-hidden="true"></i>
			        	</button>

			        	<button type="button" data-id="{{>prop.id}}" class="btn btn-attach-category"style="padding:1px 0px;">
			        		<i class="fa fa-paperclip" aria-hidden="true"></i>
			        	</button>

			        	<button type="button" data-id="{{>prop.id}}" class="btn btn-attach-brands"style="padding:1px 0px;">
			        		<img width="15px" title="Attach Brands" src="/images/purchase.png">
			        	</button>

			        	<button type="button" data-id="{{>prop.id}}" class="btn"style="padding:1px 0px;">
                            <a href="/site-development/{{>prop.id}}">
                                    <img width="15px" title="Site Development" src="/images/project.png">
                            </a>
                        </button>

                            <button type="button" data-id="{{>prop.id}}" class="btn"style="padding:1px 0px;"><a href="/store-website/{{>prop.id}}/goal"><i class="fa fa-bullseye"style="color:gray;"></i></a></button>

                            <button title="Social media strategy" type="button"  class="btn"style="padding:1px 0px;"><a href="/store-website/{{>prop.id}}/social-strategy" style="color:gray;"><i class="fa fa-fa"></i></a></button>

                            <button title="Seo Format" data-id="{{>prop.id}}" type="button"  class="btn btn-seo-format"style="padding:1px 0px;"><a href="javascript:;"><i class="fa fa-external-link"style="color:gray;"></i></a></button>

                            <button title="store User history" data-id="{{>prop.id}}" type="button"  class="btn open-store-user-histoty"style="padding:1px 0px;">
                                    <a href="javascript:;"style="color:gray;"><i class="fa fa-info-circle"></i></a>
                            </button>

                            <button title="Store Reindexing" data-id="{{>prop.id}}" type="button"  class="btn open-store-reindex-history"style="padding:1px 0px;">
                                    <a href="javascript:;"style="color:gray;"><i class="fa fa-history"></i></a>
                            </button>

                            <button title="Build Process" data-id="{{>prop.id}}" type="button"  class="btn open-build-process-template"style="padding:1px 0px;">
                                <a href="javascript:void(0);"style="color:gray;"><i class="fa fa-simplybuilt"></i></a>
                            </button>
                            <button title="Build Process History" data-id="{{>prop.id}}" type="button"  class="btn open-build-process-history"style="padding:1px 0px;">
                                <a href="javascript:void(0);"style="color:gray;"><i class="fa fa-folder"></i></a>
                            </button>
                            <button title="Sync Stage To Master" data-id="{{>prop.id}}" type="button"  class="btn sync_stage_to_master"style="padding:1px 0px;">
                                <a href="javascript:void(0);"style="color:gray;"><i class="fa fa-refresh"></i></a>
                            </button>
                            <button title="Response History" data-id="{{>prop.id}}" type="button"  class="btn response_history"style="padding:1px 0px;">
                                <a href="javascript:void(0);"style="color:gray;"><i class="fa fa-history"></i></a>
                            </button>
                           <button type="button" class="btn btn-xs btn-image  load-duplicate-modal mt-1" data-target="#newDuplicate"  data-id="{{>prop.id}}" title="Duplicate store website"><i class="fa fa-copy"></i> </button>

                           <button type="button" class="btn btn-xs btn-image  load-tag-modal mt-1" data-toggle="modal" data-target="#store-attach-tag"  data-id="{{>prop.id}}" title="Create Tag"><i class="fa fa-link"></i> </button>

						   	<button type="button" class="btn btn-xs btn-image  btn-download-db-env mt-1"   data-id="{{>prop.id}}" data-type="db" title="Download Database">
								<img src="/images/database-download-icon.png" style="color:gray;"> 
								<span class="loader" style="display: none;">
									<!-- Add your loading spinner icon here -->
									<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>
								</span>
							</button>

						   	<button type="button" class="btn btn-xs btn-image  btn-download-db-env mt-1"   data-id="{{>prop.id}}" data-type="env" title="Download Env File">
								<i class="fa fa-download"></i> 
							</button>

							<button type="button" class="btn btn-xs btn-image  btn-download-db-env-logs mt-1"   data-id="{{>prop.id}}" data-type="db" title="Download Database/Env Logs" style="color:gray;">
								<i class="fa fa-info-circle"></i>
							</button>

                            {{if prop.is_dev_website == 1 }}
                                <a style="padding:1px;" class="btn d-inline btn-image execute-bash-command-select-folder"  data-folder_name="{{>prop.site_folder}}" href="#" data-id="{{>prop.id}}" title="Execute Bash Command">
                                    <img src="/images/send.png" style="color:gray; cursor: nwse-resize; width: 0px;">
                                </a>
                                <button title="Response History" data-id="{{>prop.id}}" type="button"  class="btn execute_bash_command_response_history"style="padding:1px 0px;">
                                    <a href="javascript:void(0);"style="color:gray;"><i class="fa fa-history"></i></a>
                                </button>

                            {{/if}}
							<button title="Run File Permissions" data-id="{{>prop.id}}" type="button"  class="btn run_file_permissions" style="padding:1px 0px;">
                                    <a href="javascript:void(0);"style="color:gray;"><i class="fa fa-file"></i></a>
							</button>
							<button title="Clear Cloudflare Caches" data-id="{{>prop.id}}" type="button"  class="btn btn-xs btn-image mt-1 clear_cloudflare_caches" style="padding:1px 0px;">
								<img src="/images/clear_caches.png" style="color:gray;width: 20px !important;">
							</button>
			        </td>
			        </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</div>
</script>

<script type="text/x-jsrender" id="template-attached-category">
	<div class="modal-content">
	   <div class="modal-header">
	      <h5 class="modal-title">{{if data.id}} Edit Site {{else}}Create Site{{/if}}</h5>
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	      <span aria-hidden="true">&times;</span>
	      </button>
	   </div>
	   <div class="modal-body">
			<div class="row">
				<div class="col-lg-12">
					<form>
						<?php echo csrf_field(); ?>
						<input type="hidden" name="store_website_id" value="{{:store_website_id}}">
					  	<div class="row">
					    	<div class="col">
					      		{{:scdropdown}}
					      	</div>
					    	<div class="col">
					      		<input type="text" class="form-control" name="remote_id" placeholder="Remote Id">
					    	</div>
					    	<div class="col">
					      		<button class="btn btn-secondary add-attached-category">ADD</button>
					    	</div>
					  	</div>
					</form>
				</div>
			</div>
			<div class="row mt-5 preview-category">
			</div>	
			<div class="row mt-5">		
				<div class="col-lg-12">
					<table class="table table-bordered">
					    <thead>
					      <tr>
					      	<th>No</th>
					        <th>Name</th>
					        <th>Remote id</th>
					        <th>Created At</th>
					        <th>Action</th>
					      </tr>
					    </thead>
					    <tbody>
					    	{{props data}}
						      <tr>
						      	<td>{{:prop.id}}</td>
						        <td>{{:prop.title}}</td>
						        <td>{{:prop.remote_id}}</td>
						        <td>{{:prop.created_at}}</td>
						        <td>
						        	<button type="button" data-store-website-id="{{>prop.store_website_id}}" data-id="{{>prop.id}}" class="btn btn-delete-store-website-category"><i class="fa fa-trash" aria-hidden="true"></i></button>
						        </td>
						      </tr>
						    {{/props}}  
					    </tbody>
					</table>
					{{:pagination}}
				</div>	
			</div>
		</div>
	</div>
</script>

<script type="text/x-jsrender" id="template-attached-brands">
	<div class="modal-content">
	   <div class="modal-header">
	      <h5 class="modal-title">{{if data.id}} Edit Site {{else}}Create Site{{/if}}</h5>
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	      	<span aria-hidden="true">&times;</span>
	      </button>
	   </div>
	   <div class="modal-body">
			<div class="row">
				<div class="col-lg-12">
					<form>
						<?php echo csrf_field(); ?>
						<input type="hidden" name="store_website_id" value="{{:store_website_id}}">
					  	<div class="row">
					    	<div class="col">
					    		<select class="form-control" name="brand_id">
					    			{{props brands}}
					    				<option value="{{>key}}">{{>prop}}</option>
					    			{{/props}}		
					    		</select>
					      	</div>
					    	<div class="col">
					      		<input type="text" class="form-control" name="markup" placeholder="Mark up percentage">
					    	</div>
					    	<div class="col">
					      		<button class="btn btn-secondary add-attached-brands">ADD</button>
					    	</div>
					  	</div>
					</form>
				</div>
			</div>	
			<div class="row mt-5">		
				<div class="col-lg-12">
					<table class="table table-bordered">
					    <thead>
					      <tr>
					      	<th>No</th>
					        <th>Name</th>
					        <th>Markup</th>
					        <th>Created At</th>
					        <th>Action</th>
					      </tr>
					    </thead>
					    <tbody>
					    	{{props data}}
						      <tr>
						      	<td>{{:prop.id}}</td>
						        <td>{{:prop.name}}</td>
						        <td>{{:prop.markup}}</td>
						        <td>{{:prop.created_at}}</td>
						        <td>
						        	<button type="button" data-store-website-id="{{>prop.store_website_id}}" data-id="{{>prop.id}}" class="btn btn-delete-store-website-brand"><i class="fa fa-trash" aria-hidden="true"></i></button>
						        </td>
						      </tr>
						    {{/props}}  
					    </tbody>
					</table>
					{{:pagination}}
				</div>	
			</div>
		</div>
	</div>			
</script>
<script type="text/x-jsrender" id="template-category-list">
	<div class="col-lg-12">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th><input type="checkbox" class="select-all-preview-category"></th>
		      	<th>Title</th>
		        <th>Action</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr id="preview-category-{{:prop.id}}">
			      	<td><input class="preview-checkbox" type="checkbox" name="push_category" value="{{:prop.id}}"></td>
			      	<td>{{:prop.title}}</td>
			        <td>
			        	<button type="button" data-category-id="{{:prop.id}}" class="btn btn-delete-preview-category">
			        		<i class="fa fa-trash" aria-hidden="true"></i>
			        	</button>
			        </td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
	</div>
	<div class="col-lg-12">
		<button class="btn btn-secondary save-preview-categories"><i class="fa fa-save"></i> Save</button>
	</div>			
</script>

<script type="text/x-jsrender" id="template-update-remarks">
	<div class="modal-content">
	   <div class="modal-header">
	      <h5 class="modal-title">Edit Remarks</h5>
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	      	<span aria-hidden="true">&times;</span>
	      </button>
	   </div>
	   <div class="modal-body">
			<div class="row">
				<div class="col-lg-12">
					<form>
						<?php echo csrf_field(); ?>
						<input type="hidden" class="frm_store_website_id" name="store_website_id" value="{{:id}}">
					  	<div class="row">
					  		<div class="col-md-12">
					    		<div class="form-group">
						         	<label for="{{:field}}">{{if field == "facebook_remarks"}}Facebook{{else}}Instagram{{/if}} Remarks</label>
						         	<textarea name="{{:field}}" class="form-control" id="facebook_remarks" placeholder="Enter {{:field}}">{{if field}}{{:remarks}}{{/if}}</textarea>
						         </div>
					        </div> 
					        <div class="col-md-12">
						    	<div class="form-group">
						      		<button class="btn btn-secondary update-remark-btn">Update</button>
						    	</div>
					    	</div>
					  	</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</script>

<script type="text/x-jsrender" id="template-edit-seo">
	<div class="modal-content">
	   <div class="modal-header">
	      <h5 class="modal-title">Edit Seo Format</h5>
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	      	<span aria-hidden="true">&times;</span>
	      </button>
	   </div>
	   <div class="modal-body">
			<div class="row">
				<div class="col-lg-12">
					<form>
						<?php echo csrf_field(); ?>
						<input type="hidden" class="frm_store_website_id" name="store_website_id" value="{{:data.store_website_id}}">
					  	<div class="row">
					  		<div class="col-md-12">
					    		<div class="form-group">
						         	<label for="meta_title">Meta Title</label>
						         	<textarea name="meta_title" class="form-control" id="meta_title" placeholder="Enter Meta Title">{{if data}}{{:data.meta_title}}{{/if}}</textarea>
						         </div>
					        </div> 
					        <div class="col-md-12">
					    		<div class="form-group">
						         	<label for="meta_description">Meta Description</label>
						         	<textarea name="meta_description" class="form-control" id="meta_description" placeholder="Enter Meta Description">{{if data}}{{:data.meta_description}}{{/if}}</textarea>
						         </div>
					        </div> 
					        <div class="col-md-12">
					    		<div class="form-group">
						         	<label for="meta_keyword">Meta Keywords</label>
						         	<textarea name="meta_keyword" class="form-control" id="meta_keyword" placeholder="Enter Meta Keywords">{{if data}}{{:data.meta_keyword}}{{/if}}</textarea>
						         </div>
					        </div> 
					        <div class="col-md-12">
						    	<div class="form-group">
						      		<button data-id="{{:data.store_website_id}}" class="btn btn-secondary update-seo-format">Update</button>
						    	</div>
					    	</div>
					  	</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</script>
<script type="text/x-jsrender" id="template-generate-file">
	<div class="modal-content">
	   <div class="modal-header">
	      <h5 class="modal-title">Generate PEM file</h5>
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	      	<span aria-hidden="true">&times;</span>
	      </button>
	   </div>
	   <div class="modal-body">
			<div class="row">
				<div class="col-lg-12">
					<form action="/store-website/generate-file-store" method="post">
						<?php echo csrf_field(); ?>
						<div class="row">
					  		<div class="col-md-12">
					    		<div class="form-group">
						         	<label for="meta_title">Meta Title</label>
						         	<select class="form-control select2" name="for_server">
						         		<option value="Erp-Server">Erp-Server</option>
						         		<option value="s01">Scrap-Server-s01</option>
						         		<option value="s02">Scrap-Server-s02</option>
						         		<option value="s03">Scrap-Server-s03</option>
						         		<option value="s04">Scrap-Server-s04</option>
						         		<option value="s05">Scrap-Server-s05</option>
						         		<option value="s06">Scrap-Server-s06</option>
						         		<option value="s07">Scrap-Server-s07</option>
						         		<option value="s08">Scrap-Server-s08</option>
						         		<option value="s09">Scrap-Server-s09</option>
						         		<option value="s10">Scrap-Server-s10</option>
						         		<option value="s11">Scrap-Server-s11</option>
						         		<option value="s12">Scrap-Server-s12</option>
						         		<option value="s13">Scrap-Server-s13</option>
						         		<option value="s14">Scrap-Server-s14</option>
						         		<option value="s15">Scrap-Server-s15</option>
						         		<option value="Cropper-Server">Cropper-Server</option>
						         		<option value="BRANDS">BRANDS</option>
						         		<option value="AVOIRCHIC">AVOIRCHIC</option>
						         		<option value="OLABELS">OLABELS</option>
						         		<option value="SOLOLUXURY">SOLOLUXURY</option>
						         		<option value="SUVANDNAT">SUVANDNAT</option>
						         		<option value="THEFITEDIT">THEFITEDIT</option>
						         		<option value="THESHADESSHOP">THESHADESSHOP</option>
						         		<option value="UPEAU">UPEAU</option>
						         		<option value="VERALUSSO">VERALUSSO</option>
						         	</select>
						         </div>
					        </div> 
					        <div class="col-md-12">
						    	<div class="form-group">
						      		<button type="submit" class="btn btn-secondary submit-generete-file-btn">Generate</button>
						    	</div>
					    	</div>
					  	</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</script>


<script type="text/x-jsrender" id="template-magento-user-lising">
	<div class="modal-content">
	   <div class="modal-header">
	      <h5 class="modal-title">User Listing</h5>
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	      	<span aria-hidden="true">&times;</span>
	      </button>
	   </div>
	   <div class="modal-body">
			<div class="row mt-5">		
				<div class="col-lg-12">
					<table class="table table-bordered">
					    <thead>
					      <tr>
					      	<th>Stote Website Id</th>
					        <th>Website Mode</th>
					        <th>Username</th>
					        <th>Email</th>
					        <th>First Name</th>
					        <th>Last Name</th>
					        <th>Password</th>
					        <th>Action</th>
					      </tr>
					    </thead>
					    <tbody>
					    	{{props data}}
						      <tr class='subMagentoUser'>

						      	<td>
						      		{{:prop.store_website_id}}
						      		<input type="hidden" class="store_website_id"  value="{{:prop.store_website_id}}"/>

						      	</td>
						        <td>
						        	<select name="website_mode" id="website_mode" class="form-control websiteMode">
					               
								       <option {{if prop.website_mode == 'production' }}selected {{/if}} value="production">Production</option>
								       
								       <option {{if prop.website_mode == 'staging' }} selected {{/if}} value="staging">Staging</option>
									</select>
						        </td>
						        
						        <td>
						        	<input type="text" name="username" value="{{if prop}}{{:prop.username}}{{/if}}" class="form-control userName" id="username" placeholder="Enter Username" readonly>
						        </td>
						        
						        <td>
						        	<input type="email" name="userEmail" value="{{if prop}}{{:prop.email}}{{/if}}" class="form-control userEmail" id="userEmail" placeholder="Enter Email">
						        </td>
						        
						        <td>
								    <input type="text" name="firstName" value="{{if prop}}{{:prop.first_name}}{{/if}}" class="form-control firstName" id="firstName" placeholder="Enter First Name">
						        </td>
						        
						        <td>
						        	<input type="text" name="lastName" value="{{if prop}}{{:prop.last_name}}{{/if}}" class="form-control lastName" id="lastName" placeholder="Enter Last Name">
						        </td>
						        
						        <td>
						        	<input type="password" name="password" value="{{if prop}}{{:prop.password}}{{/if}}" class="form-control user-password" id="password">
						        </td>

						        <td>
						        
					 	<button type="button" data-id="" class="btn btn-show-password btn-sm" style="border:1px solid">
							<i class="fa fa-eye" aria-hidden="true"></i>
						</button>

					    <button type="button" data-id="" class="btn btn-copy-password btn-sm" style="border:1px solid">
							<i class="fa fa-clone" aria-hidden="true"></i>
						</button>

					    <button type="button" data-id="{{>prop.id}}" class="btn btn-edit-magento-user btn-sm" style="border:1px solid">
							<i class="fa fa-check" aria-hidden="true"></i>
						</button>
						
						<button type="button" data-id="{{>prop.id}}" class="btn btn-delete-magento-user btn-sm" style="border:1px solid">
							<i class="fa fa-trash" aria-hidden="true"></i>
						</button>
						        </td>
						      </tr>
						    {{/props}}  
					    </tbody>
					</table>
				</div>	
			</div>
		</div>
	</div>	
</script>


<script type="text/x-jsrender" id="template-history-store-magento-user">
	<div class="modal-content">
	   <div class="modal-header">
	      <h5 class="modal-title">Magento Store User History</h5>
	      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	      	<span aria-hidden="true">&times;</span>
	      </button>
	   </div>
	   <div class="modal-body">
			<div class="row">		
				<div class="col-lg-12">
					<table class="table table-bordered">
					    <thead>
					      <tr>
					      	<th>Date</th>
					      	<th>Website_mode</th>
					        <th>Username</th>
					        <th>First name</th>
					        <th>Last name</th>
					        <th>Action</th>
					      </tr>
					    </thead>
					    <tbody>
					    	{{props data}}
						      <tr>
						      	<td>{{if prop}}{{:prop.date}}{{/if}}</td>
						      	<td>{{if prop}}{{:prop.website_mode}}{{/if}}</td>
						      	<td>{{if prop}}{{:prop.username}}{{/if}}</td>
						      	<td>{{if prop}}{{:prop.first_name}}{{/if}}</td>
						      	<td>{{if prop}}{{:prop.last_name}}{{/if}}</td>
						      	<td>{{if prop}}{{:prop.action}}{{/if}}</td>
						      </tr>
						    {{/props}}  
					    </tbody>
					</table>
				</div>	
			</div>
		</div>
	</div>	
</script>

<script type="text/x-jsrender" id="template-store-reindex-history">
    <div class="modal-content">
        <div class="modal-header">
           <h5 class="modal-title">Store Reindex History</h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
             <span aria-hidden="true">&times;</span>
           </button>
        </div>
        <div class="modal-body">
            <div class="row">		
                <div class="col-lg-12">
                    <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th>Date</th>
                            <th>Servername</th>
                            <th>Username</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                            {{props data}}
                                  <tr>
                                    <td>{{if prop}}{{:prop.date}}{{/if}}</td>
                                    <td>{{if prop}}{{:prop.server_name}}{{/if}}</td>
                                    <td>{{if prop}}{{:prop.username}}{{/if}}</td>
                                    <td>{{if prop}}{{:prop.action}}{{/if}}</td>
                                  </tr>
                            {{/props}}
                        </tbody>
                    </table>
                </div>	
            </div>
         </div>
    </div>	
</script>
<script type="text/x-jsrender" id="template-build-process">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Build Process</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-12">
                    <form class="build-process">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" class="frm_store_website_id" name="store_website_id" value="{{:data.id}}">
                        <div class="row">
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="build_repository">Repository</label>
                                    <input type="text" name="repository" id="build_repository" value="{{:data.repository}}" placeholder="Enter Repository Name" class="form-control mt-0" />
                                </div>
                            </div>                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="build_reference">Reference</label>
                                    <input type="text" name="reference" id="build_reference" value="{{:data.reference}}" placeholder="Enter Repository Name" class="form-control mt-0" />
                                </div> 
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button data-id="{{:data.id}}"class="btn btn-secondary update-build-process">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</script>
<script type="text/x-jsrender" id="add-website-company-address">
	<div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Add Address</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-12">
                    <form class="website-address">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" class="frm_store_website_id" name="store_website_id" value="{{:data.id}}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="company_address">Address</label>
                                    <textarea name="website_address" id="company_address" placeholder="Enter Address Name" class="form-control mt-0">{{:data.website_address}}</textarea> </textarea>
                                </div> 
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button data-id="{{:data.id}}"class="btn btn-secondary update-company-wesite-address">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</script>
