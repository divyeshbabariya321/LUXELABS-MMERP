<script type="text/x-jsrender" id="template-create-group">
    <form name="form-create-group" id="form-create-group" method="post">
        <?php echo csrf_field(); ?>
        <div class="modal-content">
           <div class="modal-header">
              <h5 class="modal-title">{{if data.type == 'edit'}} Edit Website Group View {{else}}Create Website Group{{/if}}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
           </div>
           <div class="modal-body">
              <div class="form-row">
                 {{if data.type == 'edit'}}
                     <input type="hidden" name="id" value="{{:data.id}}"/>
                     <input type="hidden" name="row_id" value="{{:data.row_id}}"/>
                 {{else}}
                     <input type="hidden" name="row_id" value="{{:data.row_id}}"/>
                 {{/if}}
                 
              </div>
              <div class="form-group col-md-12 name_div">
                <label for="name">Name</label>
                <input type="text" name="name" value="{{if data.type == 'edit'}}{{:data.name}}{{/if}}" class="form-control" id="name" placeholder="Enter Name"> 
             </div> 
              <div class="form-group col-md-12">
                <label for="route_domain">Route Details</label>
                <input type="text" name="route_name" value="{{if data.type == 'edit'}}{{:data.name}}{{/if}}" class="form-control" id="route_name" placeholder="Enter route name (eg. VeraLusso_es)"> 
             </div> 
              <div class="form-group col-md-12">
                <input type="text" name="route_domain" value="" class="form-control" id="route_domain" placeholder="Enter route domain (eg. -es, -cn)"> 
             </div> 
              <div class="form-group col-md-12">
                <input type="text" name="route_url" value="" class="form-control" id="route_url" placeholder="Enter route url (eg. veralusso, upeau)"> 
             </div> 

             <div class="form-group col-md-12">
                     <label for="code" class="form-group">Agents Priorities</label> 
                <button type="button" title="Create" data-id="" class="btn btn-add-priority">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </button>
            </div>
            
            </div> 

           <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary submit-group">Save changes</button>
           </div>
        </div>
    </form>      
</script>