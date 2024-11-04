<div id="create-d-task-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Task</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="<?php echo route('task.create.hubstaff_task'); ?>" method="post"
                      id="assign_task_form">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <input type="hidden" name="id" id="issueId" />
                        <input type="hidden" name="type" id="type" />
                        <label for="task_for_modal">Task For</label>
                        <select name="task_for_modal" class="form-control task_for_modal" style="width:100%;">
                            <option value="">Select</option>
                            <option value="hubstaff">Hubstaff</option>
                            <option value="time_doctor">Time Doctor</option>
                        </select>
                    </div>
                    <div class="form-group time_doctor_account_section_modal">
                        <label for="time_doctor_account">Task Account</label>
                        {{ html()->select("time_doctor_account", ['' => ''])->class("form-control time_doctor_account_modal globalSelect2")->style("width:100%;")->data('ajax', route('select2.time_doctor_accounts_for_task'))->data('placeholder', 'Account') }}
                    </div>
                    <div class="form-group time_doctor_project_section_modal">
                        <label for="time_doctor_project">Time Doctor Project</label>
                        {{ html()->select("time_doctor_project", ['' => ''])->class("form-control time_doctor_project globalSelect2")->style("width:100%;")->data('ajax', route('select2.time_doctor_projects'))->data('placeholder', 'Project') }}
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-default" data-task_id="">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>