<script type="text/x-jsrender" id="template-create-website-password">
	<form name="form-create-website-password" id="form-create-website-password" method="post" enctype="multipart/form-data">
   <?php echo csrf_field(); ?>
   <div class="modal-content">
      <div class="modal-header">
         <h5 class="modal-title">{{if data.id}} Add/Edit Admin Password {{else}} Add/Edit Admin Password {{/if}}</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
         </button>
      </div>
      <div class="modal-body">
         <div class="row">
            {{if data.id}}
                <input type="hidden" name="id" id="store_website_id" value="{{:data.id}}"/>
                <div class="col-md-4">
                   <div class="form-group">
                      <label for="title">Title</label>
                      <input type="text" id='swTitle' name="title" value="{{if data}}{{:data.title}}{{/if}}" class="form-control mt-0"  placeholder="Enter Title" readonly>
                   </div>
                </div>
            {{else}}
                <div class="col-md-4">
                    <div class="form-group">
                        <select name="id" id="store_website_id" class="form-control">                                
                           <option value="">-- Select a website--</option>
                           <?php
                            if (isset($storeWebsites)) {
                                foreach ($storeWebsites as $storeWebsite) {
                                    $storeWebsite_id = $storeWebsite->id;
                                    echo "<option value='" . ($storeWebsite_id) . "'>" . ($storeWebsite->title) . '</option>';
                                }
                            } ?>
                            {{props storeWebsites}}
                                <option value="{{:prop.id}}">{{:prop.title}}</option>
                            {{/props}}
                        </select>
                    </div>
                </div>
            {{/if}}
            <input type="hidden" name="adminpassword" id="adminpassword" value="1"/>
            
         </div>
         <div class="MainMagentoUser">
            {{if data.id}}
            {{props userdata}}
               {{if prop.is_deleted}}
                  <div class="subMagentoUser " style="border:1px solid #ccc;padding: 15px;margin-bottom:5px;height: 76px;overflow: hidden; ">  
                  <button type="button" data-id="" class="btn btn_expand_inactive btn-sm" style="border:1px solid">
                     <i class="fa fa-ban" aria-hidden="true"></i>
                  </button>
               {{else}}
                  <div class="subMagentoUser" style="border:1px solid #ccc;padding: 15px;margin-bottom:5px">            
               {{/if}}
            
               <div class="form-group">
                  <div class="row">
                     <div class="col-sm-6">
                        <label for="username">Username</label>
                        <input type="text" name="username" value="{{if prop}}{{:prop.username}}{{/if}}" class="form-control userName" id="username" placeholder="Enter Username" readonly>
                     </div>
                     <div class="col-sm-6">
                        <label for="userEmail">Email</label>
                        <input type="email" name="userEmail" value="{{if prop}}{{:prop.email}}{{/if}}" class="form-control userEmail" id="userEmail" placeholder="Enter Email">
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <div class="row">
                     <div class="col-sm-6">
                        <label for="firstName">First Name</label>
                        <input type="text" name="firstName" value="{{if prop}}{{:prop.first_name}}{{/if}}" class="form-control firstName" id="firstName" placeholder="Enter First Name">
                     </div>
                     <div class="col-sm-6">
                        <label for="lastName">Last Name</label>
                        <input type="text" name="lastName" value="{{if prop}}{{:prop.last_name}}{{/if}}" class="form-control lastName" id="lastName" placeholder="Enter Last Name">
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <div class="row">
                     <div class="col-sm-6">
                        <label for="password">Password</label>
                        <input type="password" name="password" value="{{if prop}}{{:prop.password}}{{/if}}" class="form-control user-password" id="password" placeholder="Enter Password">
                     </div>
                     <div class="col-sm-6">
                        <label for="website_mode">Website Mode</label>
                        <select name="website_mode" id="website_mode" class="form-control websiteMode">
                        <option {{if prop.website_mode == 'production' }}selected {{/if}} value="production">Production</option>
                        <option {{if prop.website_mode == 'staging' }} selected {{/if}} value="staging">Staging</option>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <div class="row">
                     <div class="col-sm-5">
                        <button type="button" data-id="" class="btn btn-show-password btn-sm" style="border:1px solid">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                        </button>
                        <button type="button" data-id="" class="btn btn-copy-password btn-sm" style="border:1px solid">
                        <i class="fa fa-clone" aria-hidden="true"></i>
                        </button>
                        {{if prop.is_deleted}}
                        In Active
                        {{else}}
                        <button type="button" data-id="{{>prop.id}}" class="btn btn-edit-magento-user btn-sm" style="border:1px solid">
                        <i class="fa fa-check" aria-hidden="true"></i>
                        </button>
                        <button type="button" data-id="{{>prop.id}}" class="btn btn-delete-magento-user btn-sm" style="border:1px solid">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                        {{/if}}
                        <a href="<?php echo url('/store-website/log-website-users/'); ?>/{{>prop.store_website_id}}" type="button" title="Website user history" class="btn btn-sm" style="border:1px solid">
                          <i class="fa fa-history aria-hidden="true""></i>
                        </a>
                        <button type="button" data-id="{{>prop.id}}" class="btn btn btn-sm btn-magento-user-request" style="border:1px solid"><i class="fa fa-retweet" aria-hidden="true"></i></button>  
                     </div>
                  </div>
               </div>

               <div class="table-responsive" id="request-response-{{>prop.id}}"  style="display:none;">
               <table class="table">
                 <tr>
                   <th>Request :</th>
                   <td>{{>prop.request_data}}</td>
                 </tr>
                 <tr>                   
                   <th>Response :</th>
                   <td>{{>prop.response_data}}</td>
                 </tr>
               </table>
            </div>
            </div>
            {{/props}}
            {{else}}
            <div class="subMagentoUser" style="border:1px solid #ccc;padding: 15px;margin-bottom:5px">
               <div class="form-group">
                  <div class="row">
                     <div class="col-sm-6">
                        <label for="username">Username</label>
                        <input type="text" name="username" value="" class="form-control userName" id="username" placeholder="Enter Username">
                     </div>
                     <div class="col-sm-6">
                        <label for="userEmail">Email</label>
                        <input type="email" name="userEmail" value="" class="form-control userEmail" id="userEmail" placeholder="Enter Email">
                     </div>
                     
                  </div>
               </div>
               <div class="form-group">
                  <div class="row">
                    <div class="col-sm-6">
                        <label for="firstName">First Name</label>
                        <input type="text" name="firstName" value="" class="form-control firstName" id="firstName" placeholder="Enter First Name">
                     </div>
                     <div class="col-sm-6">
                        <label for="lastName">Last Name</label>
                        <input type="text" name="lastName" value="" class="form-control lastName" id="lastName" placeholder="Enter Last Name">
                     </div>
             </div>
               </div>
               <div class="form-group">
                  <div class="row">
                     <div class="col-sm-6">
                        <label for="password">Password</label>
                        <input type="password" name="password" value="" class="form-control user-password" id="password" placeholder="Enter Password">
                     </div>
                     <div class="col-sm-6">
                        <label for="website_mode">Website Mode</label>
                        <select name="website_mode" id="website_mode" class="form-control websiteMode">
                           <option value="production">Production</option>
                           <option value="staging">Staging</option>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <div class="row">
                     <div class="col-sm-5">
                        <button type="button" data-id="" class="btn btn-show-password btn-sm" style="border:1px solid">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                        </button>
                        <button type="button" data-id="" class="btn btn-copy-password btn-sm" style="border:1px solid">
                        <i class="fa fa-clone" aria-hidden="true"></i>
                        </button>
                        <button type="button" data-id="" class="btn btn-edit-magento-user btn-sm" style="border:1px solid">
                        <i class="fa fa-check" aria-hidden="true"></i>
                        </button>
                        <button type="button" data-id="" class="btn btn-delete-magento-user btn-sm" style="border:1px solid">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                        <a href="<?php echo url('/store-website/log-website-users/'); ?>/{{:data.id}}" type="button" title="Website user history" class="btn btn-sm" style="border:1px solid">
                          <i class="fa fa-history aria-hidden="true""></i>
                        </a>
                     </div>
                  </div>
               </div>
            </div>
            {{/if}}   
         </div>
         <div class="form-group" style="text-align:right">
            <button type="button" data-id="" class="btn btn-add-magento-user" style="border:1px solid">
            <i class="fa fa-plus" aria-hidden="true"></i>
            </button>
         </div>
         
      </div>
   </div>
</form>  	

</script>
