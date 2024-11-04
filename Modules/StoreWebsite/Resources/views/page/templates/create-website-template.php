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
  #iframe {
    z-index: 99999;
    display: none;
    position: fixed;
    width: 100%;
    height: 100%;
    margin: auto;
    /* Center the element vertically */
    top: 0;
    bottom: 0;
    /* Center the element horizontally */
    left: 0;
    right: 0;
    background: rgba(0,0,0,0.825);
}
#iframe > iframe {
    width: 90%;
    height: 80%;
    overflow-y: scroll;
    margin: auto;
    position: absolute;
    top: 0; left: 0; bottom: 0; right: 0;
    border: none;
    outline: none;
}
.close-iframe-btn {
    position: absolute;
    top: 0; right: 0;
    margin: 15px;
    width: 40px;
    height: 40px;
    border: none;
    outline: none;
    background: rgba(200,200,200, 1);
}
.close-iframe-btn span {
    font-size: 29px;
}
</style>
<div class="modal-content">
  <div class="modal-header">
    <h5 class="modal-title">{{if data.id}} Edit Page {{else}}Create Page{{/if}}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <form name="form-create-website" method="post">
    <?php echo csrf_field(); ?>
    <div class="modal-body">
      {{if data}}
      <div class="form-row">
        <div class="form-group col-md-3">
          <select multiple class="form-control globalSelect2 select-searchable" id="website-page-copy-to" name="copy_to[]">
          <option value="">Copy To Page</option>
            <?php foreach ($pages as $k => $page) { ?>
              <option value="<?php echo $k; ?>"><?php echo $page; ?></option>
            <?php } ?>
          </select>
				</div>
			  <div class="form-group col-md-9 pt-2">
          <input type="checkbox" name="cttitle" id="cttitle" class="font-weight-normal"> <label for="cttitle" class="font-weight-normal"> Meta title </label>
          <input type="checkbox" name="ctkeyword" id="ctkeyword" class="font-weight-normal"> <label for="ctkeyword" class="font-weight-normal"> Meta Keywords </label>
          <input type="checkbox" name="ctdesc" id="ctdesc"> <label for="ctdesc" class="font-weight-normal">Meta Description</label>
          <input type="checkbox" name="site_url" id="site_urls"> <label for="site_urls" class="font-weight-normal"> Entire site urls </label>
					<button class="btn btn-sm copy-to-btn text-dark" title="Copy to" type="button"><i class="fa fa-clone" ></i> Copy</button>
        </div>
      </div>
			<hr>
			{{/if}}
      <div class="form-row">
        <div class="form-group col-md-3">
          <select name="page" class="form-control website-page-change select-searchable" id="website-page-change">
            <option value="">Copy From Page</option>
              <?php foreach ($pages as $k => $page) { ?>
                <option value="<?php echo $k; ?>"><?php echo $page; ?></option>
              <?php } ?>
          </select>
        </div>
        <div class="form-group col-md-3">
            <select name="language" class="form-control website-language-change">
              <option value="">Language</option>
                <?php foreach ($languages as $k => $language) { ?>
                  <option value="<?php echo $language; ?>"><?php echo $language; ?></option>
                <?php } ?>
            </select> 
        </div>
        <div class="form-group col-md-6 pt-2">
          <input type="checkbox" name="ctitle" id="ctitle"> <label for="ctitle" class="font-weight-normal"> Meta title </label>
          <input type="checkbox" name="ckeyword" id="ckeyword"> <label for="ckeyword" class="font-weight-normal"> Meta Keywords </label>
          <input type="checkbox" name="cdesc" id="cdesc"> <label for="cdesc" class="font-weight-normal">Meta Description</label>
          <button class="btn btn-sm reload-page-data text-dark" title="Reload page data" type="button"><i class="fa fa-refresh" ></i></button>
        </div>
      </div>
      <div class="form-row">
        {{if data}}
        <input type="hidden" name="id" value="{{:data.id}}"/>
        {{/if}}
      </div>
      <div class="form-row">
        <div class="form-group col-md-4">
          <div class="form-row">
            <div class="form-group col-md-12">
              <div class="d-flex justify-content-between">
                <label for="name" class="mt-2 font-weight-normal">Title</label>
                <button type="button" class="btn btn-sm mb-2 text-dark" id="keyword-search-btn"> <l class="fa fa-search"></i> </button>
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
          <div class="form-row">
              <div class="form-group col-md-12">
                <div class="d-flex justify-content-between">
                  <label for="meta_keyword_avg_monthly" class="font-weight-normal">Meta Keywords avg.monthly</label>
                </div>
                <textarea name="meta_keyword_avg_monthly" oninput="auto_grow(this)" class="form-control" id="meta_keyword_avg_monthly" readOnly>{{if data}}{{:data.meta_keyword_avg_monthly}}{{/if}}</textarea>
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
        <div class="form-group col-md-6 mb-0 mt-4">
          <div class="form-group">
            <div class="justify-content-end pt-2 input-group">
              <input type="text" value="" class="hide form-control  w-75" id="extra-keyword-search">
              <div class="input-group-append">
                <button type="button" class="hide btn text-dark" id="extra-keyword-search-btn" style="margin: 0px !important; height:35px;"> <l class="fa fa-search"></i> </button>
              </div>
            </div>
            <div class="pt-3 suggestList" style="display:none">
              <table class="table table-bordered">
                <thead class="thead-dark">
                  <tr>
                    <th>Keywords </th>
                    <th>Avg. monthly</th>
                    <th>Competition</th>
                    <th>Translation</th>
                  </tr>
                </thead>
                <tbody class="suggestList-table"></tbody>
              </table>
            </div>
            </div>
        </div>
        <div class="form-group col-md-3">
          <label for="content_heading" class="font-weight-normal">Content heading</label>
          <input type="text" name="content_heading" value="{{if data}}{{:data.content_heading}}{{/if}}" class="form-control" id="content_heading" placeholder="Enter content_heading">
        </div>
        <div class="form-group col-md-3">
          <label for="platform_id" class="font-weight-normal">Platform ID</label>
          <input type="text" name="platform_id" value="{{if data}}{{:data.platform_id}}{{/if}}" class="form-control" id="website_platform_id" placeholder="Enter Platform ID">
        </div>
      </div>
      <button type="button" class="btn btn-secondary btn-sm" id="btn-edit-cms-page-select" data-website-url="{{:data.website_url}}">Open In Page Builder</button>
              <div class="form-row">
                <div class="form-group col-md-12 mb-0">
                  <label for="content" class="font-weight-normal">Content</label>
                  <textarea name="content" class="form-control content-preview" id="google_translate_element" placeholder="Enter content">{{if data}}{{:data.content}}{{/if}}</textarea>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label for="layout" class="font-weight-normal">layout</label>
                  <input type="text" name="layout" value="{{if data}}{{:data.layout}}{{/if}}" class="form-control" id="layout" placeholder="Enter layout">
                </div>
                <div class="form-group col-md-4">
                     <label for="active" class="font-weight-normal">Active</label>
                     <select name="active" class="form-control store-website-change">
                        <option value="">-- N/A --</option>
                        <?php
                        foreach ([0 => 'No', 1 => 'Yes'] as $k => $l) {
                            echo "<option {{if data.active == '".$k."'}} selected {{/if}} value='".$k."'>".$l.'</option>';
                        }
    ?>
                     </select>
                  </div>
                  <div class="form-group col-md-4">
                     <label for="language" class="font-weight-normal">Language</label>
                     <select name="language" class="form-control store-website-language">
                        <option value="">-- N/A --</option>
                        <?php
        foreach (\App\Language::pluck('name', 'name')->toArray() as $k => $l) {
            echo "<option {{if data.language == '".$k."'}} selected {{/if}} value='".$k."'>".$l.'</option>';
        }
    ?>
                     </select>
                  </div>
              </div>
              <div class="form-row">
                {{if data && data.id}}
                    <input type="text" name="stores_str" value="{{if data}}{{:data.stores}}{{/if}}" class="form-control" placeholder="Enter Stores comma seperate">
                {{else}}
                  <div class="form-group col-md-6">
                     <label for="store_website_id" class="font-weight-normal">Store website</label>
                     <select name="store_website_id" class="form-control store-website-change select-searchable">
                        <option value="">-- N/A --</option>
                        <?php
        foreach ($storeWebsites as $k => $l) {
            echo "<option {{if data.store_website_id == '".$k."'}} selected {{/if}} value='".$k."'>".$l.'</option>';
        }
    ?>
                     </select>
                  </div>
                  <div class="form-group col-md-6">
                     <label for="stores" class="font-weight-normal">Stores</label>
                     <select name="stores[]" class="form-control store-selection" multiple="multiple">
                     </select>
                  </div>
                {{/if}}
              </div>
           </div>
           <div class="modal-footer">
              <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-secondary btn-sm submit-store-site">Save changes</button>
           </div>
          </form>
        </div>
    <script>
        $(document).on("click", ".open-iframe-btn", function (e) {
            e.preventDefault();
            $('#iframe').show();
        });

        $(document).on("click", ".close-iframe-btn", function (e) {
            e.preventDefault();
            $('#iframe').hide();
        });

        $(document).ready(function() {
            $(document).on("click", "#btn-edit-cms-page-select", function(e) {
                var platformId = $("#website_platform_id").val();
                var websiteUrl = $(this).data('website-url');
                if(typeof platformId !="undefined" && platformId != '' && platformId != null && typeof websiteUrl != "undefined" && websiteUrl !="") {
                  $('#iframe').show();
                  $("#iframe > iframe").attr("src", websiteUrl + 'admin/cms/page/edit/page_id/' + platformId);
                } else {
                  toastr["error"]("Store Website & Platform ID field is required to access page builder.");
                }
            });
        });
    </script>
</script> 