<div id="confirmMessageModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Confirm Message</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form action="{{ route('task_category.store') }}" method="POST" onsubmit="return false;">
        @csrf

        <div class="modal-body">


          <div class="form-group">
            <div id="message_confirm_text"></div>
            <input name="task_id" id="confirm_task_id" type="hidden" />
            <input name="message" id="confirm_message" type="hidden" />
            <input name="status" id="confirm_status" type="hidden" />
          </div>
          <div class="form-group">
            <p>Send to Following</p>
            <input checked="checked" name="send_message_recepients[]" class="send_message_recepients" type="checkbox" value="assign_by">Assign By
            <input checked="checked" name="send_message_recepients[]" class="send_message_recepients" type="checkbox" value="assigned_to">Assign To
            <input checked="checked" name="send_message_recepients[]" class="send_message_recepients" type="checkbox" value="master_user_id">Lead 1
            <input checked="checked" name="send_message_recepients[]" class="send_message_recepients" type="checkbox" value="second_master_user_id">Lead 2
            <input checked="checked" name="send_message_recepients[]" class="send_message_recepients" type="checkbox" value="contacts">Contacts
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary confirm-messge-button">Send</button>
        </div>
      </form>
    </div>

  </div>
</div>