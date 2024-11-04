<div id="github-task-create" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="github-task-create-form" action="<?php echo route('github.github-task.store'); ?>" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Create Github Task</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                        <?php echo csrf_field(); ?>
                        <div class="form-group normal-subject">
                            <label for="task_name">Task Name<span class="text-danger">*</span></label>
                            <input type="text" name="task_name" id="task_name" class="form-control" value="Pull Request Task" readonly/>
                        </div>
                        <div class="form-group">
                            <label for="assign_to">Assigned to<span class="text-danger">*</span></label>
                            {{ html()->select("assign_to", ['' => ''])->class("form-control assign_to globalSelect2")->style("width:100%;")->data('ajax', route('select2.user'))->data('placeholder', 'Assign to') }}
                        </div>
                        <div class="form-group normal-subject">
                            <label for="task_details">Task Details<span class="text-danger">*</span></label>
                            <textarea name="task_details" id="task_details" class="form-control"></textarea>
                        </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary save-github-task-window">Save</butto>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>