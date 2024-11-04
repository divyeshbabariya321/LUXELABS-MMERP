<script type="text/x-jsrender" id="product-templates-create-block">
<div class="modal fade" id="product-template-create-modal" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Create Template</h4>
        </div>
        <div class="modal-body">
          <form method="post" enctype="multipart/form-data" id="product-template-from">
             <?php echo csrf_field(); ?>
             <div class="col-sm-6">
                <div class="form-group">
                  <label for="name" class="col-form-label">Template Name</label>
                  <input type="text" name="name" class="form-control name">
                </div>
             </div>
             <div class="col-sm-6">
                <div class="form-group">
                  <label for="no_of_images" class="col-form-label">No of Images</label>
                  <input type="text" name="no_of_images" value="0" class="form-control no_of_images">
                </div>
             </div>
             <div class="form-group row pl-5">
               <div class="d-flex"  style="justify-content: space-between;">
                <label for="no_of_images" class=" col-form-label">Auto generate for products</label>
                <div class="pl-3 pt-2">
                  <input type="checkbox" name="auto_generate_product" class="auto_generate_product">
                </div>
              </div>
             </div>
             <div class="form-group  show-product-image"> </div>
             <div class="form-group row">
              <div class="col-sm-3 imgUp pl-5">
                 <div class="imagePreview"></div>
                 <label class="btn btn-primary">
                 Upload<input type="file" name="files[]" class="uploadFile img" value="Upload Photo" style="width: 0px;height: 0px;overflow: hidden;">
                 </label>
              </div>
           </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary create-product-template">Create Template</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</script>
