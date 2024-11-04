<script type="text/x-jsrender" id="template-new-duplicate">
            <form name="form-new-duplicate" method="post">
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Duplicate</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="form-group col-md-6">
                        <label for="name">Duplicate Number</label>
                        <input type="number" name="number" class="form-control" id="number" placeholder="Enter Duplicate" required>
                    </div>
                    <div class="form-group col-md-6">
                        <input type="hidden" name="id" class="store_website_id">
                    </div>
                    <div class="modal-footer">
                       <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                       <button type="button" class="btn btn-primary submit-duplicate">Save changes</button>
                    </div>
                </div>
            </form>

</script>
