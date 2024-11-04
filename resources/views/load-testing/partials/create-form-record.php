
<script type="text/x-jsrender" id="product-templates-create-block">
<div class="modal fade" id="product-template-create-modal" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Create Load Testing Data</h4>
        </div>
        <div class="modal-body">
          <form method="post" enctype="multipart/form-data" id="product-template-from">
             <?php echo csrf_field(); ?>
             <div class="form-group row">
               <label for="currency" class="col-sm-3 col-form-label">No of Virtual User</label>
               <div class="col-sm-6">
                  <input type='text' name="no_of_virtual_user" class="form-control">
               </div>
            </div>
            <div class="form-group row">
               <label for="currency" class="col-sm-3 col-form-label">Ramp Time</label>
               <div class="col-sm-6">
                  <input type='text' name="ramp_time" class="form-control">
               </div>
            </div>
            <div class="form-group row">
               <label for="currency" class="col-sm-3 col-form-label">Duration</label>
               <div class="col-sm-6">
                  <input type='text' name="duration" class="form-control">
               </div>
            </div>
            <div class="form-group row">
               <label for="currency" class="col-sm-3 col-form-label">Delay(In Milliseconds)</label>
               <div class="col-sm-6">
                  <input type='text' name="delay" class="form-control">
               </div>
            </div>
            <div class="form-group row">
               <label for="currency" class="col-sm-3 col-form-label">Loop Count</label>
               <div class="col-sm-6">
                  <input type='text' name="loop_count" class="form-control">
               </div>
            </div>
            <div class="form-group row">
               <label for="currency" class="col-sm-3 col-form-label">Domain Name</label>
               <div class="col-sm-6">
                  <input type='text' name="domain_name" class="form-control">
               </div>
            </div>
            <div class="form-group row">
               <label for="currency" class="col-sm-3 col-form-label">Protocols</label>
               <div class="form-check form-check-inline">
                  <input class="form-check-input" name="protocols" type="radio" id="protocols_https" value="Https">
                  <label class="form-check-label" for="protocols_https">Https</label>
               </div>
               <div class="form-check form-check-inline">
                  <input class="form-check-input" name="protocols" type="radio" id="protocols_http" value="Http">
                  <label class="form-check-label" for="protocols_http">Http</label>
               </div>
            </div>
            <div class="form-group row">
               <label for="currency" class="col-sm-3 col-form-label">Path</label>
               <div class="col-sm-6">
                  <input type='text' name="path" class="form-control">
               </div>
            </div>
             <div class="form-group row">
                <label  class="col-sm-3 col-form-label">Request Method</label>
                <div class="col-sm-6">
                   <select class="form-control" name="request_method" id="request_method_input" aria-invalid="false">
                        <option value="get">Get</option>
                        <option value="post">Post</option>
                        <option value="put">Put</option>
                        <option value="delete">Delete</option>
                    </select>
                </div>
             </div>
             
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary create-product-template">Create Request</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</script>
