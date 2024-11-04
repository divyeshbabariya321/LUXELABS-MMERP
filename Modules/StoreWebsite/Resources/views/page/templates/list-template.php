<script type="text/x-jsrender" id="template-result-block">
{{if pageUrl == "store-website.page.keywords"}}
 <div class="table-responsive mt-3 keywordRecords">
        <table class="table table-bordered">
            <thead>
              <tr>
                <th width="5%">Title</th>
                <th width="5%">Title Score</th>
                <th width="10%">Url</th>
                <th width="5%">Store View</th>
                <th width="5%">Site</th>
                <th width="5%">Meta Title</th>
                <th width="5%">Meta Keyword</th>
                <th width="10%">Meta Description</th>
                <th width="30%">Keywords</th>
                <th width="25%">Actions</th>
              </tr>
            </thead>
            <tbody>
                {{props data}} 
                  <tr>
                    <td>{{:prop.title}} </td>
                    <td>{{:prop.title_score}} </td>
                    <td>{{:prop.url_key}}</td>
                    <td title="{{:prop.stores}}">{{:prop.stores_small}}</td>
                    <td>{{:prop.store_website_name}}</td>
                    <td>{{:prop.meta_title}}</td>
                    <td id="keyword_{{:prop.id}}">{{:prop.meta_keywords}}</td>
                    <td>{{:prop.meta_description}}</td>
                    <td id="col_{{:prop.id}}" data-id="{{:prop.id}}" class="row_keywords" data-title="{{:prop.title}}"></td>
                    <td>
                        <button type="button" title="Edit" data-id="{{>prop.id}}" class="btn btn-edit-template">
                            <i class="fa fa-edit" aria-hidden="true"></i>
                        </button>
                        <button type="button" title="Push" data-id="{{>prop.id}}" class="btn btn-push">
                            <i class="fa fa-upload" aria-hidden="true"></i>
                        </button>
						<button type="button" title="Pull" data-id="{{>prop.id}}" class="btn btn-pull">
			        		<i class="fa fa-download" aria-hidden="true"></i>
			        	</button>
                        <button type="button" title="Delete" data-id="{{>prop.id}}" class="btn btn-delete-template">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                        <button type="button" title="History" data-id="{{>prop.id}}" class="btn btn-find-history">
                            <i class="fa fa-globe" aria-hidden="true"></i>
                        </button>
                        <button type="button" title="Language" data-id="{{>prop.id}}" class="btn btn-translate-for-other-language">
                            <i class="fa fa-language" aria-hidden="true"></i>
                        </button>
                        <button type="button" title="Activities" data-id="{{>prop.id}}" class="btn btn-activities">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                        </button>
                        {{if prop.translated_from}}
                        <button type="button" title="get Translared Text Score" data-id="{{>prop.id}}" class="btn btn-get-translate-score">
                            <i class="fa fa-dashboard" aria-hidden="true"></i>
                        </button>
                        {{/if}}
                    </td>
                  </tr>
                {{/props}}
            </tbody>
        </table>
        {{:pagination}}
    </div>
	{{else}}
    <div class="table-responsive mt-3" {{:pageUrl}}>
        <table class="table table-bordered"style="table-layout:fixed;">
            <thead>
              <tr>
                <th width="2%"></th>
                <th width="3%">Id</th>
                <th width="5%">Platform ID</th>
                <th width="10%">Title</th>
                <th width="10%">Title Score</th>
                <th width="6%">Url</th>
                <th width="6%">Store View</th>
                <th width="10%">Site</th>
                <th width="6%">Language</th>
                <th width="3%">Active</th>
                <th width="4%">Pushed</th>
                <th width="8%">Status</th>
                <th width="4%">Is Latest Version Pushed</th>
                <th width="4%">Is Latest Version Translated</th>
                <th width="5%">Is Pending Review Translations</th>
                <th width="5%">Translation Approved by</th>
                <th width="8%">Created at</th>
                <th width="9%">Actions</th>
              </tr>
            </thead>
            <tbody>
                {{props data}} 
                  <tr style="background-color: {{:prop.colorcode}};">
                    <td><input type="checkbox" class="groups" name="groups[]" value="{{:prop.id}}"></td>
                    <td>&nbsp;{{:prop.id}}</td>
                    <td></td>
                    <td class="Website-task" title="{{:prop.title}}">{{:prop.title}} </td>
                    <td class="Website-task" title="{{:prop.title_score}}">{{:prop.title_score}} </td>
                    <td class="Website-task" title="{{:prop.url_key}}">{{:prop.url_key}}</td>
                    <td class="Website-task" title="{{:prop.stores}}">{{:prop.stores_small}}</td>
                    <td class="Website-task" title="{{:prop.store_website_name}}">{{:prop.store_website_name}}</td>
                    <td class="Website-task" title="{{:prop.language}}">{{:prop.language}}</td>
                    <td>{{if prop.active == "1"}}Yes{{else}}NO{{/if}}</td>
                    <td>{{if prop.is_pushed == "1"}}Yes{{else}}NO{{/if}}</td>
                    <td>
                        <div style="display: flex; justify-content: space-between; align-items: center; position: relative;">
                            <select class="form-control select2 status-update" data-id="{{>prop.id}}" data-placeholder="Select Status" name="status_id">
                                <option value="">Select Status</option>
                                <?php $status = \App\Models\StoreWebsiteStatus::pluck('status_name', 'id')->toArray(); ?> 
                                <?php foreach ($status as $id => $name) { ?>
                                    <option value="<?php echo $id; ?>" {{if prop.website_store_views_status_id == <?php echo $id; ?>}}selected{{/if}}><?php echo $name; ?></option>                 
                                <?php } ?>
                            </select>
                            <button type="button" title="Status history" data-id="{{>prop.id}}" class="btn btn- status-history" style="padding: 0px 1px !important; margin-left: 10px;">
                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                            </button>
                        </div>
                    </td>
                    <td>{{if prop.is_latest_version_pushed == "1"}}Yes{{else}}NO{{/if}}</td>
                    <td>{{if prop.is_latest_version_translated == "1"}}Yes{{else}}NO{{/if}}</td>
                    <td>{{if prop.is_flagged_translation == "1"}}Yes{{else}}NO{{/if}}</td>
                    <td>{{if prop.is_flagged_translation != "1"}} {{:prop.approved_by}} {{/if}}</td>
                    <td>{{:prop.created_at}}</td>
                    <td>
                        <button type="button" title="Edit" data-id="{{>prop.id}}" class="btn btn-edit-template" style="padding: 0px 1px !important;">
                            <i class="fa fa-edit" aria-hidden="true"></i>
                        </button>
                        <button type="button" title="Push" data-id="{{>prop.id}}" class="btn btn-push"style="padding: 0px 1px !important;">
                            <i class="fa fa-upload" aria-hidden="true"></i>
                        </button>
						<button type="button" title="Pull" data-id="{{>prop.id}}" class="btn btn-pull"style="padding: 0px 1px !important;">
			        		<i class="fa fa-download" aria-hidden="true"></i>
			        	</button>
                        <button type="button" title="Delete" data-id="{{>prop.id}}" class="btn btn-delete-template"style="padding: 0px 1px !important;">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                        <button type="button" title="History" data-id="{{>prop.id}}" class="btn btn-find-history"style="padding: 0px 1px !important;">
                            <i class="fa fa-globe" aria-hidden="true"></i>
                        </button>
                        {{if !prop.translated_from}}
                        <button type="button" title="Language" data-id="{{>prop.id}}" class="btn btn-translate-for-other-language"style="padding: 0px 1px !important;">
                            <i class="fa fa-language" aria-hidden="true"></i>
                        </button>
                        {{/if}}
                        <button type="button" title="Activities" data-id="{{>prop.id}}" class="btn btn-activities"style="padding: 0px 1px !important;">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                        </button>
						 <button type="button" title="Pull logs" data-id="{{>prop.id}}" class="btn btn-pullLogs"style="padding: 0px 1px !important;">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </button>
                        {{if prop.translated_from}}
                        <button type="button" title="get Translared Text Score" data-id="{{>prop.id}}" class="btn btn-get-translate-score" style="padding: 0px 1px !important;">
                            <i class="fa fa-dashboard" aria-hidden="true"></i>
                        </button>
                        {{/if}}
                    </td>
                  </tr>
                {{/props}}
            </tbody>
        </table>
        {{:pagination}}
    </div>
	{{/if}}
</script> 