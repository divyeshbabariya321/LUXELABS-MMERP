<div id="send-message-text-box" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('task.send-brodcast') }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="task_id" id="hidden-task-id" value="">
                <div class="modal-header">
                    <h4 class="modal-title">Send Brodcast Message</h4>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                    @csrf
                    <div class="form-group">
                        <label for="document">Message</label>
                        <textarea class="form-control message-for-brodcast" name="message"
                                  placeholder="Enter your message"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-send-brodcast-message">Send</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>