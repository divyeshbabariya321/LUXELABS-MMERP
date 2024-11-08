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
        <h5 class="modal-title">{{if data.id}} Edit Environment  {{else}}Create Environment {{/if}}</h5>
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
                <input type="hidden" name="created_by" value="<?php echo auth()->user()->id; ?>"/>
                {{/if}}
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <div class="d-flex justify-content-between">
                    <label for="name" class="mt-2 font-weight-normal">Store Website</label>
                    </div>
                    <div class="input-group">
                    {{ html()->select('store_website_id', $storeWebsites)->class('form-control select2-ele')->id('store_website_id')->placeholder('Select Store website') }}
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    
                    <div class="d-flex justify-content-between">
                    <label for="name" class="mt-2 font-weight-normal">Path</label>
                    </div>
                    <div class="input-group">
                    <input type="text" name="path" id="path-page" value="{{if data}}{{:data.path}}{{/if}}" class="form-control" placeholder="Enter Path">
                    </div>
                    
                </div>
            </div>
            {{if !data.id}}
            <div class="form-row">
                <div class="form-group col-md-12">
                    
                    <div class="d-flex justify-content-between">
                    <label for="name" class="mt-2 font-weight-normal">Value</label>
                    </div>
                    <div class="input-group">
                    <input type="text" name="value" id="value-page" value="{{if data}}{{:data.value}}{{/if}}" class="form-control" placeholder="Enter Value">
                    </div>
                    
                </div>
            </div>
            {{/if}}
            <div class="form-row">
                <div class="form-group col-md-12">
                    
                    <div class="d-flex justify-content-between">
                    <label for="name" class="mt-2 font-weight-normal">Command</label>
                    </div>
                    <div class="input-group">
                    <input type="text" name="command" id="command-page" value="{{if data}}{{>data.command}}{{/if}}" class="form-control" placeholder="Enter Command">
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-secondary btn-sm submit-store-site">Save </button>
        </div>
    </form>
</div>
</script>