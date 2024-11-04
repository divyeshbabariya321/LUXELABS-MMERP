<script type="text/x-jsrender" id="template-bug-environment">
            <form name="form-create-environment" method="post">
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Bug Environment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="form-group col-md-6">
                        <label for="name">Environment</label>
                        <input type="text" name="name" class="form-control" id="name" placeholder="Enter Environment">
                    </div>
                    <div class="modal-footer">
                       <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                       <button type="button" class="btn btn-primary submit-environment">Save changes</button>
                    </div>
                </div>


            </form>

</script>
