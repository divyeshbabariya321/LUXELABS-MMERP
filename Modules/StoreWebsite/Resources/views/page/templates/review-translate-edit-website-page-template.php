<script type="text/x-jsrender" id="template-create-website">
<style>
  .btn-secondary{
    padding:3px 10px !important;
    margin-left:5px !important;
  }
  hr {
    margin-top: 10px !important;
    margin-bottom: 10px !important;
  }
  .table .thead-dark th {
    color: #292929;
    background-color: #eeeeee;
    border-color: #e4e4e4;
  }
</style>
<div class="modal-content">
  <div class="modal-header">
    <h5 class="modal-title">{{if data.id}} Edit Page Translation {{else}}Create Page{{/if}}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <form name="form-create-website" method="post">
    <?php echo csrf_field(); ?>
    <div class="modal-body">
      
      <div class="form-row">
        {{if data}}
        <input type="hidden" name="id" value="{{:data.id}}"/>
        <input type="hidden" name="approved_by_user_id" value="<?php echo auth()->user()->id; ?>"/>
        <input type="hidden" name="is_flagged_translation" value="0"/>
        {{/if}}
      </div>
      <div class="form-row">
        <div class="form-group col-md-4">
          <div class="form-row">
            <div class="form-group col-md-12">
              <div class="d-flex justify-content-between">
                <label for="name" class="mt-2 font-weight-normal">Title</label>
              </div>
              <div class="input-group">
                <input type="text" name="title" id="title-page" value="{{if data}}{{:data.title}}{{/if}}" class="form-control" placeholder="Enter title">
              </div>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-12 mt-3">
              <label for="meta_title" class="font-weight-normal">Meta Title</label>
              <input type="text" name="meta_title" value="{{if data}}{{:data.meta_title}}{{/if}}" class="form-control" id="meta_title" placeholder="Enter Meta title">
            </div>
          </div>
        </div>
        <div class="form-group col-md-8">
          <div class="form-row">
              <div class="form-group col-md-12">
                <div class="d-flex justify-content-between">
                  <label for="meta_keywords" class="font-weight-normal">Meta Keywords</label>
                  <span id="meta_keywords_count"></span>
                </div>
                <textarea name="meta_keywords" oninput="auto_grow(this)" id="meta_keywords" class="form-control" placeholder="Enter Keywords">{{if data}}{{:data.meta_keywords}}{{/if}}</textarea>
              </div>
          </div>
          
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-12">
					<div class="d-flex justify-content-between">
						<label for="meta_description" class="font-weight-normal">Meta Description</label>
						<span id="meta_desc_count"></span>
					</div>
          <textarea name="meta_description" class="form-control" placeholder="Enter meta description">{{if data}}{{:data.meta_description}}{{/if}}</textarea>
        </div>
      </div>
      <div class="form-row">
        
        <div class="form-group col-md-3">
          <label for="content_heading" class="font-weight-normal">Content heading</label>
          <input type="text" name="content_heading" value="{{if data}}{{:data.content_heading}}{{/if}}" class="form-control" id="content_heading" placeholder="Enter content_heading">
        </div>
        
      </div>
              <div class="form-row">
                <div class="form-group col-md-12 mb-0">
                  <label for="content" class="font-weight-normal">Content</label>
                  <textarea name="content" class="form-control content-preview" id="google_translate_element" placeholder="Enter content">{{if data}}{{:data.content}}{{/if}}</textarea>
                </div>
              </div>
              <div class="form-row hidden">
                
                
                  <div class="form-group col-md-4">
                     <label for="language" class="font-weight-normal">Language</label>
                     <select name="language" class="form-control store-website-language">
                        <option value="">-- N/A --</option>
                        <?php
                            foreach (\App\Language::pluck('name', 'name')->toArray() as $k => $l) {
                                echo "<option {{if data.language == '" . $k . "'}} selected {{/if}} value='" . $k . "'>" . $l . '</option>';
                            }
    ?>
                     </select>
                  </div>
              </div>
             
           </div>
           <div class="modal-footer">
              <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-secondary btn-sm submit-store-site">Save Changes & Approve Translate</button>
           </div>
          </form>
        </div>
</script> 