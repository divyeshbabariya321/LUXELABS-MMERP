<div id="quick-create-task" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Task / Dev Task</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="<?php echo route('task.create.task.shortcut'); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="quick_task" value="1">
                    <div class="form-group">
                        <label for="task_type">Task Type</label>
                        {{ html()->select("task_type", \App\Task::TASK_TYPES)->class("form-control select2-vendor type-on-change")->style("width:100%;") }}
                    </div>
                    <div class="form-group normal-subject">
                        <label for="task_subject">Task Subject</label>
                        <input type="text" name="task_subject" id="task_subject" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="task_detail">Task Detail</label>
                        <input type="text" name="task_detail" id="task_detail" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="task_asssigned_to">Assigned to</label>
                        {{ html()->select("task_asssigned_to", ['' => ''])->class("form-control  task_asssigned_to globalSelect2")->style("width:100%;")->data('ajax', route('select2.user'))->data('placeholder', 'Assign to') }}
                    </div>
                    <div class="form-group">
                        <label for="task_category">Task category</label>
                        {{ html()->select("category", ['' => ''])->class("form-control  category_ globalSelect2")->style("width:100%;")->data('ajax', route('select2.taskcategories'))->data('placeholder', 'Select Category') }}
                    </div>
                    <div class="form-group">
                        <label for="task_for">Task For</label>
                        <select name="task_for" class="form-control task_for" style="width:100%;">
                            <option value="">Select</option>
                            <option value="hubstaff">Hubstaff</option>
                            <option value="time_doctor">Time Doctor</option>
                        </select>
                    </div>
                    <div class="form-group time_doctor_project_section">
                        <label for="time_doctor_project">Time Doctor Project</label>
                        {{ html()->select("time_doctor_project", ['' => ''])->class("form-control time_doctor_project globalSelect2")->style("width:100%;")->data('ajax', route('select2.time_doctor_projects'))->data('placeholder', 'Project') }}
                    </div>
                    <div class="form-group time_doctor_project_section">
                        <label for="time_doctor_project">Time Doctor Account</label>
                        {{ html()->select("time_doctor_account", ['' => ''])->class("form-control time_doctor_account globalSelect2")->style("width:100%;")->data('ajax', route('select2.time_doctor_accounts_for_task'))->data('placeholder', 'Account') }}
                    </div>
                    <div class="form-group">
                        <button class="btn btn-secondary save-task-window">Save</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>