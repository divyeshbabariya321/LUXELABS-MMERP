<script type="text/x-jsrender" id="template-test-status">
            <form name="form-create-status" method="post">
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Test Case Status</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="name">Status</label>
                        <input type="text" name="name" class="form-control" id="name" placeholder="Enter Status">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary submit-status">Save changes</button>
                    </div>
                </div>
            </form>
</script>
