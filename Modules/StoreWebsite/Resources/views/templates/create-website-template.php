<script type="text/x-jsrender" id="template-create-website">
	<form name="form-create-website" id="form-create-website" class="formcreatewebsite" method="post" enctype="multipart/form-data">
   <?php echo csrf_field(); ?>
   <div class="modal-content">
      <div class="modal-header">
         <h5 class="modal-title">{{if data.id}} Edit Site {{else}}Create Site{{/if}}</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
         </button>
      </div>
      <div class="modal-body">
         <div class="row">
            {{if data}}
            <input type="hidden" name="id" id="store_website_id" value="{{:data.id}}"/>
            {{/if}}
            <div class="col-md-4">
               <div class="form-group">
                  <label for="title">Title</label>
                  <input type="text" id='swTitle' name="title" value="{{if data}}{{:data.title}}{{/if}}" class="form-control mt-0"  placeholder="Enter Title">
                  <span class="text-danger text-danger-url"></span>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="website">Website</label>
                  <input type="text" name="website" value="{{if data}}{{:data.website}}{{/if}}" class="form-control" id="website" placeholder="Enter Website">
                  <span class="text-danger text-danger-url"></span>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="website">Semrush Project Id</label>
                  <input type="text" name="semrush_project_id" value="{{if data}}{{:data.semrush_project_id}}{{/if}}" class="form-control" id="semrush_project_id" placeholder="Enter Semrush Project Id">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="country_duty">Mailing Service Id</label>
                  <select id="mailing_service_id" name="mailing_service_id" class="form-control">
                     <option disabled>-- N/A --</option>
                     <?php
                        if (isset($services)) {
                            foreach ($services as $service) {
                                $service_id = $service->id;
                                echo "<option {{if data.mailing_service_id == '".($service_id)."'}} selected {{/if}} value='".($service_id)."'>".($service->name).'</option>';
                            }
                        }
   ?>
                  </select>
               </div>
            </div>
			 <div class="col-md-4">
               <div class="form-group">
                  <label for="sale_old_products">Sale PreOwned Products</label>
                    <select id="sale_old_products" name="sale_old_products" class="form-control">
					   <option value="0" {{if data.sale_old_products==0}} SELECTED {{/if}} > No </option>
					   <option value="1" {{if data.sale_old_products==1}} SELECTED {{/if}} > Yes </option>
                    </select>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="is_debug_true">Database Log</label>
                    <select id="is_debug_true" name="is_debug_true" class="form-control">
                  <option value="0" {{if data.is_debug_true=='0'}} SELECTED {{/if}} > No </option>
                  <option value="1" {{if data.is_debug_true=='1'}} SELECTED {{/if}} > Yes </option>
                    </select>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="is_dev_website">Is Dev Website</label>
                    <select id="is_dev_website" name="is_dev_website" class="form-control">
                  <option value="0" {{if data.is_dev_website=='0'}} SELECTED {{/if}} > No </option>
                  <option value="1" {{if data.is_dev_website=='1'}} SELECTED {{/if}} > Yes </option>
                    </select>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="description">Description</label>
                  <input type="text" name="description" value="{{if data}}{{:data.description}}{{/if}}" class="form-control" id="description" placeholder="Enter Description">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="description">Send Blue Account</label>
                  <input type="text" name="send_in_blue_account" value="{{if data}}{{:data.send_in_blue_account}}{{/if}}" class="form-control" id="send_in_blue_account" placeholder="Enter Send Blue Account">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="description">Send Blue API</label>
                  <input type="text" name="send_in_blue_api" value="{{if data}}{{:data.send_in_blue_api}}{{/if}}" class="form-control" id="send_in_blue_api" placeholder="Enter Send in Blue APi">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="description">Send Blue SMTP Email API</label>
                  <input type="text" name="send_in_blue_smtp_email_api" value="{{if data}}{{:data.send_in_blue_smtp_email_api}}{{/if}}" class="form-control" id="send_in_blue_smtp_email_api" placeholder="Enter Send in Blue SMTP Mail APi">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="remote_software">Remote software</label>
                  <input type="text" name="remote_software" value="{{if data}}{{:data.remote_software}}{{/if}}" class="form-control" id="remote_software" placeholder="Enter Remotesoftware">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="magento_url">Magento Url</label>
                  <input type="text" name="magento_url" value="{{if data}}{{:data.magento_url}}{{/if}}" class="form-control" id="magento_url" placeholder="Enter magento url">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="magento_url">Magento Url (for DEV)</label>
                  <input type="text" name="dev_magento_url" value="{{if data}}{{:data.dev_magento_url}}{{/if}}" class="form-control" id="dev_magento_url" placeholder="Enter dev magento url">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="magento_url">Magento Url (for Stage)</label>
                  <input type="text" name="stage_magento_url" value="{{if data}}{{:data.stage_magento_url}}{{/if}}" class="form-control" id="stage_magento_url" placeholder="Enter stage magento url">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="magento_username">Magento username</label>
                  <input type="text" name="magento_username" value="{{if data}}{{:data.magento_username}}{{/if}}" class="form-control" id="magento_username" placeholder="Enter Username">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="magento_password">Magento Password</label>
                  <input type="text" name="magento_password" value="{{if data}}{{:data.magento_password}}{{/if}}" class="form-control" id="magento_password" placeholder="Enter Password">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="api_token">Api Token</label>
                  <input type="text" name="api_token" value="{{if data}}{{:data.api_token}}{{/if}}" class="form-control" id="api_token" placeholder="Enter Api token">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="dev_api_token">Api Token (for dev)</label>
                  <input type="text" name="dev_api_token" value="{{if data}}{{:data.dev_api_token}}{{/if}}" class="form-control" id="dev_api_token" placeholder="Enter Dev Api token">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="api_token">Api Token (for stage)</label>
                  <input type="text" name="stage_api_token" value="{{if data}}{{:data.stage_api_token}}{{/if}}" class="form-control" id="stage_api_token" placeholder="Enter Stage Api token">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="facebook">Facebook</label>
                  <input type="text" name="facebook" value="{{if data}}{{:data.facebook}}{{/if}}" class="form-control" id="facebook" placeholder="Enter facebook profle">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="facebook_remarks">Facebook Remarks</label>
                  <textarea rows="1" name="facebook_remarks" class="form-control" id="facebook_remarks" placeholder="Enter facebook remarks">{{if data}}{{:data.facebook_remarks}}{{/if}}</textarea>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="instagram">Product Markup %</label>
                  <input type="number" name="product_markup" onkeyup="this.value = fnc(this.value, 0, 100)" value="{{if data}}{{:data.product_markup}}{{/if}}" class="form-control" id="product_markup" placeholder="Enter product markup">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="instagram">Instagram</label>
                  <input type="text" name="instagram" value="{{if data}}{{:data.instagram}}{{/if}}" class="form-control" id="instagram" placeholder="Enter instagram profile">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="instagram_remarks">Instagram Remarks</label>
                  <textarea rows="1" name="instagram_remarks" class="form-control" id="instagram_remarks" placeholder="Enter instagram remarks">{{if data}}{{:data.instagram_remarks}}{{/if}}</textarea>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="cropper_color">Cropper color</label>
                  <input type="text" name="cropper_color" value="{{if data}}{{:data.cropper_color}}{{/if}}" class="form-control" id="cropper_color" placeholder="Enter cropper color">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="cropping_size">Cropping size</label>
                  <input type="text" name="cropping_size" value="{{if data}}{{:data.cropping_size}}{{/if}}" class="form-control" id="cropping_size" placeholder="Enter Cropping size">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="logo_color">Logo Color</label>
                  <input type="text" name="logo_color" value="{{if data}}{{:data.logo_color}}{{/if}}" class="form-control" id="logo_color" placeholder="Enter Logo Color">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="logo_border_color">Logo Border Color</label>
                  <input type="text" name="logo_border_color" value="{{if data}}{{:data.logo_border_color}}{{/if}}" class="form-control" id="logo_border_color" placeholder="Enter Logo Border Color">
               </div>
            </div>
             <div class="col-md-4">
               <div class="form-group">
                  <label for="text_color"> Text Color</label>
                  <input type="text" name="text_color" value="{{if data}}{{:data.text_color}}{{/if}}" class="form-control" id="text_color" placeholder="Enter Text Color">
               </div>
            </div>
             <div class="col-md-4">
               <div class="form-group">
                  <label for="border_color">Border Color</label>
                  <input type="text" name="border_color" value="{{if data}}{{:data.border_color}}{{/if}}" class="form-control" id="border_color" placeholder="Enter Border Color">
               </div>
            </div>
             <div class="col-md-4">
               <div class="form-group">
                  <label for="border_thickness">Border Thickness</label>
                  <input type="text" name="border_thickness" value="{{if data}}{{:data.border_thickness}}{{/if}}" class="form-control" id="border_thickness" placeholder="Enter Border Thickness">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="country_duty">Country Duty</label>
                  <select id="country_duty" name="country_duty" class="form-control">
                     <option value="">-- N/A --</option>
                     <?php
   foreach (\App\SimplyDutyCountry::all() as $k => $l) {
       echo "<option {{if data.country_duty == '".$l->country_code."'}} selected {{/if}} value='".$l->country_code."'>".$l->country_name.'</option>';
   }
   ?>
                  </select>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-row">
                  <div class="form-group col-md-12">
                     <label for="inputState">Is Published?</label>
                     <select name="is_published" id="is_published" class="form-control">
                     <option {{if data && data.is_published == 0}}selected{{/if}} value="0">No</option>
                     <option {{if data && data.is_published == 1}}selected{{/if}} value="1">Yes</option>
                     </select>
                  </div>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-row">
                  <div class="form-group col-md-12">
                     <label for="inputState">Disable Push?</label>
                     <select name="disable_push" id="disable_push" class="form-control">
                     <option {{if data && data.disable_push == 0}}selected{{/if}} value="0">No</option>
                     <option {{if data && data.disable_push == 1}}selected{{/if}} value="1">Yes</option>
                     </select>
                  </div>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-row">
                  <div class="form-group col-md-12">
                     <label for="website_source">Website source?</label>
                     <select name="website_source" id="website_source" class="form-control">
                     <option {{if data && data.website_source == "magento"}}selected{{/if}} value="magento">Magento</option>
                     <option {{if data && data.website_source == "shopify"}}selected{{/if}} value="shopify">Shopify</option>
                     </select>
                  </div>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="server_ip">Server IP</label>
                  <input type="text" name="server_ip" value="{{if data}}{{:data.server_ip}}{{/if}}" class="form-control" id="server_ip" placeholder="Enter Server IP">
                  <span class="text-danger text-danger-url"></span>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="country_duty">Repository</label>
                  <select id="repository_id" name="repository_id" class="form-control">
                     <option value="">-- N/A --</option>
                     <?php
   foreach (\App\Github\GithubRepository::all() as $k => $l) {
       echo "<option {{if data.repository_id == '".$l->id."'}} selected {{/if}} value='".$l->id."'>".$l->name.'</option>';
   }
   ?>
                  </select>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="country_duty">Key File Path</label>
                  <input type="file" name="key_file_path1" value="{{if data}}{{:data.key_file_path}}{{/if}}" class="form-control" id="key_file_path1" placeholder="Enter Key File Path">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="country_duty">Project ID</label>
                  <input type="text" name="project_id" value="{{if data}}{{:data.project_id}}{{/if}}" class="form-control" id="project_id" placeholder="Enter Project ID">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="store_code_id">Store Code</label>
                  <select id="store_code_id" name="store_code_id" class="form-control">
                     <option value="">Choose store code</option>
                    <?php
                       foreach ($storeCodes as $storeCode) {
                           echo "<option {{if data.store_code_id == '".(isset($storeCode['id']) ? $storeCode['id'] : '')."'}} selected {{/if}} value='".(isset($storeCode['id']) ? $storeCode['id'] : '')."'>".(isset($storeCode['code']) ? ($storeCode['code'].' ( '.(isset($storeCode['server_id']) ? $storeCode['server_id'] : 'NA').' ) ') : '').'</option>';
                       }
   ?>
                  </select>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="site_folder">Site Folder</label>
                  <select id="site_folder" name="site_folder" id="site_folder" class="form-control siteFolder">
                     <option>--Select Site Folder--</option>
                  <?php
   foreach (\App\AssetsManager::whereNotNull('ip')->get() as $k => $l) {
       echo "<option {{if data.site_folder == '".$l->folder_name."'}} selected {{/if}} value='".$l->folder_name."'>".$l->ip_name.'</option>';
   }
   /*$dataofIp = \App\AssetsManager::whereNotNull('ip')->get();
   foreach($dataofIp as $kk => $ll) {
      $arrIp = json_decode($ll->ip_name) ?? '';
      if(is_array($arrIp)){
         foreach($arrIp as $k => $l) {
            echo "<option {{if data.site_folder == '".$l."'}} selected {{/if}} value='".$l."'>".$l."</option>";
         }
      }
   }
   */
   ?>
                  </select>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="country_duty">Working Directory</label>
                  <input type="text" name="working_directory" value="{{if data}}{{:data.working_directory}}{{/if}}" class="form-control" id="working_directory" placeholder="Enter Working Directory">
                  <span class="text-danger text-danger-url"></span>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="assets_manager_id">Assets Manager</label>
                  <select name="assets_manager_id" id="assets_manager_id" class="form-control siteFolder">
                        <option>--Select Assets Manager--</option>
                        <?php
                     foreach (\App\AssetsManager::whereNotNull('ip')->get() as $k => $l) {
                         echo "<option {{if data.assets_manager_id == '".$l->id."'}} selected {{/if}} value='".$l->id."'>".$l->name.'</option>';
                     }
   ?>
                  </select>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="database_name">Database Name</label>
                  <input type="text" name="database_name" value="{{if data}}{{:data.database_name}}{{/if}}" class="form-control" id="database_name" placeholder="Enter Database Name">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="instance_number">Instance Number</label>
                  <input type="number" name="instance_number" value="{{if data}}{{:data.instance_number}}{{/if}}" class="form-control" id="instance_number" placeholder="Enter Instance Number">
                  The Instance Number is 1 or 2, based on the instance on the server 
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="website_store_project_id">Project</label>
                  <select id="website_store_project_id" name="website_store_project_id" class="form-control" onChange="getPodList()">
                     <option value="">Choose Project</option>
                    <?php
                       foreach ($projects as $project) {
                           echo "<option {{if data.website_store_project_id == '".(isset($project['id']) ? $project['id'] : '')."'}} selected {{/if}} value='".(isset($project['id']) ? $project['id'] : '')."'>".(isset($project['name']) ? ($project['name']) : '').'</option>';
                       }
   ?>
                  </select>
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="instance_number">Admin URL</label></br>
                  <div style="display: flex">
                      <a id="generated-admin-href" href ="{{if last_adminurl}}{{:last_adminurl.admin_url}}{{/if}}" target="_blank" style="display:flex; gap:5px"> 
                           <input class="fileUrl" id="generated-admin-url" type="text" value="{{if last_adminurl}}{{:last_adminurl.admin_url}}{{/if}}" />
                        </a>

                      <button type="button" data-id="" class="btn btn-sm btn-copy-admin-url" data-value="">
                          <i class="fa fa-clone" aria-hidden="true"></i>
                      </button>
                  </div>
                  <a href="javascript:void(0)" onClick="createAdminUrl()" id="generate-admin-url">Generate URL</a>
               </div>
            </div>
         </div>
         <div class="MainMagentoUser">
            {{if totaluser != 0}}
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

               <div class="table-responsive" id="request-response-{{>prop.id}}" style="display:none;">
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
                     <div class="col-sm-4">
                        <label for="username">Username</label>
                        <input type="text" name="username" value="" class="form-control userName" id="username" placeholder="Enter Username">
                     </div>
                     <div class="col-sm-4">
                        <label for="userEmail">Email</label>
                        <input type="email" name="userEmail" value="" class="form-control userEmail" id="userEmail" placeholder="Enter Email">
                     </div>
                     <div class="col-sm-4">
                        <label for="firstName">First Name</label>
                        <input type="text" name="firstName" value="" class="form-control firstName" id="firstName" placeholder="Enter First Name">
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <div class="row">
                     <div class="col-sm-4">
                        <label for="lastName">Last Name</label>
                        <input type="text" name="lastName" value="" class="form-control lastName" id="lastName" placeholder="Enter Last Name">
                     </div>
                     <div class="col-sm-4">
                        <label for="password">Password</label>
                        <input type="password" name="password" value="" class="form-control user-password" id="password" placeholder="Enter Password">
                     </div>
                     <div class="col-sm-4">
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
         <div class="row">
            <div class="col-md-4">
               <div class="form-group">
                  <label for="staging_username">Staging Username</label>
                  <input type="text" name="staging_username" value="{{if data}}{{:data.staging_username}}{{/if}}" class="form-control" id="staging_username" placeholder="Enter Staging Username">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="staging_password">Staging Password</label>
                  <input type="password" name="staging_password" value="{{if data}}{{:data.staging_password}}{{/if}}" class="form-control" id="staging_password" placeholder="Enter Staging Password">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="mysql_username">Mysql Username</label>
                  <input type="text" name="mysql_username" value="{{if data}}{{:data.mysql_username}}{{/if}}" class="form-control" id="mysql_username" placeholder="Enter Mysql Username">
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-4">
               <div class="form-group">
                  <label for="mysql_password">Mysql Password</label>
                  <input type="password" name="mysql_password" value="{{if data}}{{:data.mysql_password}}{{/if}}" class="form-control" id="mysql_password" placeholder="Enter Mysql Password">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="mysql_staging_username">Mysql Staging Username</label>
                  <input type="text" name="mysql_staging_username" value="{{if data}}{{:data.mysql_staging_username}}{{/if}}" class="form-control" id="mysql_staging_username" placeholder="Enter Mysql Staging Username">
               </div>
            </div>
            <div class="col-md-4">
               <div class="form-group">
                  <label for="mysql_staging_password">Mysql Staging Password</label>
                  <input type="password" name="mysql_staging_password" value="{{if data}}{{:data.mysql_staging_password}}{{/if}}" class="form-control" id="mysql_staging_password" placeholder="Enter Mysql Staging Password">
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-3">
               <div class="form-group">
                  <label for="push_web_key">FCM Server Key</label>
                  <input type="text" name="push_web_key" value="{{if data}}{{:data.push_web_key}}{{/if}}" class="form-control" id="push_web_key" placeholder="Enter FCM Server Key">
               </div>
            </div>
            <div class="col-md-3">
               <div class="form-group">
                  <label for="push_web_id">FCM Server Id</label>
                  <input type="text" name="push_web_id" value="{{if data}}{{:data.push_web_id}}{{/if}}" class="form-control" id="push_web_id" placeholder="Enter FCM Server Id">
               </div>
            </div>
            <div class="col-md-3">
               <div class="form-group">
                  <label for="icon">Icon</label>
                  <input type="text" name="icon" value="{{if data}}{{:data.icon}}{{/if}}" class="form-control" id="icon" placeholder="Enter Icon Url">
               </div>
            </div>
            <div class="col-md-3">
               <div class="form-row">
                  <div class="form-group col-md-12">
                     <label for="is_price_override">Price ovveride?</label>
                     <select name="is_price_override" id="is_price_override" class="form-control">
                     <option {{if data && data.is_price_override == "0"}}selected{{/if}} value="0">No</option>
                     <option {{if data && data.is_price_override == "1"}}selected{{/if}} value="1">Yes</option>
                     </select>
                  </div>
               </div>
            </div>
            <div class="col-md-3">
               <div class="form-row">
                  <div class="form-group col-md-12">
                     <label for="cloud_platform">Cloud Plateform</label>
                     <select name="cloud_platform" id="cloud_platform" class="form-control">
                     <option {{if data && data.cloud_platform == "aws"}}selected{{/if}} value="aws">AWS ECS</option>
                     <option {{if data && data.cloud_platform == "do"}}selected{{/if}} value="do">Digital Ocean Kubernates</option>
                     </select>
                  </div>
               </div>
            </div> 
            <div class="col-md-3">
               <div class="form-row">
                  <div class="form-group col-md-12">
                     <label for="ai_assistant">AI Assistant</label>
                     <select name="ai_assistant" id="ai_assistant" class="form-control">
                     <option {{if data && data.ai_assistant == "watson"}}selected{{/if}} value="watson">Watson</option>
                     <option {{if data && data.ai_assistant == "geminiai"}}selected{{/if}} value="geminiai">Gemini AI</option>
                     </select>
                  </div>
               </div>
            </div> 
            <div class="col-md-3">
               <div class="form-row">
                  <div class="form-group col-md-12">
                     <label for="aws_region">AWS region</label>
                     <input type="text" name="aws_region" class="form-control" id="aws_region" placeholder="Enter AWS Region">
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="form-row">
                  <div class="form-group col-md-12">
                     <label for="aws_cluster">AWS Cluster</label>
                     <input type="text" name="aws_cluster" value="{{if data}}{{:data.aws_cluster}}{{/if}}" class="form-control" id="aws_cluster" placeholder="Enter AWS Cluster">
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="form-row">
                  <div class="form-group col-md-12">
                     <label for="aws_api_key">AWS API Key</label>
                     <input type="text" name="aws_api_key" value="{{if data}}{{:data.aws_api_key}}{{/if}}" class="form-control" id="aws_api_key" placeholder="Enter AWS API Key">
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="form-row">
                  <div class="form-group col-md-12">
                     <label for="aws_ecs_service_id">AWS ECS Service ID</label>
                     <input type="text" name="aws_ecs_service_id" value="{{if data}}{{:data.aws_ecs_service_id}}{{/if}}" class="form-control" id="aws_ecs_service_id" placeholder="Enter AWS ECS Service ID">
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="form-row">
                  <div class="form-group col-md-12">
                     <label for="aws_api_secret">AWS Secret Key</label>
                     <input type="text" name="aws_api_secret" value="{{if data}}{{:data.aws_api_secret}}{{/if}}" class="form-control" id="aws_api_secret" placeholder="Enter AWS Secret Key">
                  </div>
               </div>
            </div>

            <div class="col-md-3">
               <div class="form-row">
                  <div class="form-group col-md-12">
                     <label for="aws_document_name">AWS Document Name</label>
                     <input type="text" name="aws_document_name" value="{{if data}}{{:data.aws_document_name}}{{/if}}" class="form-control" id="aws_document_name" placeholder="Enter AWS Document Name">
                  </div>
               </div>
            </div>
            
            <div class="col-md-3">
               <div class="form-row">
                  <div class="form-group col-md-12">
                     <label for="cluster_file">Cluster File Name</label>
                     <input type="text" readonly name="cluster_file" value="{{if data}}{{:data.cluster_file}}{{/if}}" class="form-control" id="cluster_file" placeholder="Cluster File Name">
                  </div>
               </div>
            </div>
            <div class="col-md-3">
               <div class="form-group">
                  <label for="pod_name">Pod Name</label>
                  <select id="pod_name" name="pod_name" class="form-control">
                     <option value="">Select Pod Name</option>
                     <?php
                        foreach ($podNames as $podName) {
                            echo "<option {{if data.pod_name == '".$podName."'}} selected {{/if}} value='".$podName."'>".$podName.'</option>';
                        }
   ?>
                  </select>
               </div>
            </div>

         </div>
      </div>
	   <div class="modal-footer">
	      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
         <button type="button" class="btn btn-primary test-store-site">Test Magento Token</button>
         
	      <button type="button" class="btn btn-primary submit-store-site">Save changes</button>

	   </div>
   </div>
</form>  	

</script>
