<div class="modal-body">
        <input type="hidden" id="reply_all_email_id" name="reply_all_email_id" value="{{ $email['id'] }}"/>

        <button type="button" class="btn btn-primary btn-sm pull-right mb-2" data-reply-all-add-receiver-btn>Add Receiver</button>

        <div class="form-group">
            <input type="text" id="reply_all_receiver_email" name="receiver_all_email" value="{{ $email->to }}" style="width: 100%;" readonly>
        </div>

        <div class="form-group">
            <input type="text" id="reply_all_subject" name="reply_all_subject" value="{{ $email->subject }}" style="width: 100%;">
        </div>

        <div class="form-group">
            <textarea id="reply-all-message" name="message" class="form-control reply-message-textarea" rows="3" placeholder="Reply..."></textarea>
            <div class="message-to-reply">
                <blockquote style="margin:15px 0px 0px 0.8ex;border-left:1px solid rgb(204,204,204);padding-left:1ex">
                    <iframe src="{{url('/email/email-frame', [$email['id']])}}" id="replyAllFrame" scrolling="no" style="width:100%;" frameborder="0" onload="autoIframe('replyFrame');"></iframe>
                </blockquote>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-default submit-reply-all">Reply</button>
    </div>