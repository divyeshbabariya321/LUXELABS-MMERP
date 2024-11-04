<script type="text/x-jsrender" id="template-test-cases">
            <form name="form-create-testcases" method="post">
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Test Cases</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="form-group col-md-12">
                        <label for="name">Test Case Website</label>
                        <select class="form-control" name="website" id="website">
                            <option value="">Select Test Case Website</option>
                            <?php foreach ($filterWebsites as  $key => $filterWebsite) { ?>
                            <option value="<?php echo $key ?>"><?php echo $filterWebsite ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="name">Assign to</label>
                        <select class="form-control" name="assign_to_test_case" id="assign_to_test_case" required>
                            <option value="">Select Assign to</option>
                            <?php foreach ($users as $key => $user) { ?>
                            <option value="<?php echo $key ?>"><?php echo $user ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="name">Bug Tracker Website</label>
                        <select class="form-control" name="bug_website" id="bug_website" required>
                            <option value=""> Select Bug Tracker Website</option>
                            <?php foreach ($filterWebsites as  $key => $filterWebsite) { ?>
                            <option value="<?php echo $key ?>"><?php echo $filterWebsite ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary submit-test-cases">Save changes</button>
                    </div>
                </div>
            </form>
</script>
