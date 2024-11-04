<div class="row mt-2">
    <table class="table table-bordered">
        <thead>
          <tr>
            <th width="2%">Id</th>
            <th width="30%">Subject</th>
            <th width="10%">Task</th>
            <th width="10%">Status</th>
            <th width="10%">Assigned to</th>
            <th width="10%">Created</th>
          </tr>
        </thead>
        <tbody>
            <?php if(!empty($findTasks)) { ?> 
                <?php foreach($findTasks as $findTask) { ?>
                    <tr>
                        <td><?php echo $findTask->id; ?></td>
                        <td><?php echo $findTask->subject; ?></td>
                        <td><?php echo $findTask->task; ?></td>
                        <td><?php echo $findTask->status; ?></td>
                        <td><?php echo ($findTask->assignedUser) ? $findTask->assignedUser->name : "-"; ?></td>
                        <td><?php echo $findTask->created_at; ?></td>
                      </tr>
                <?php } ?>
            <?php } ?>    
        </tbody>
    </table>
</div>