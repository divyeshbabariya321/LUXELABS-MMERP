@if (Auth::user()->isAdmin())
        <div id="view-quick-email" class="modal" tabindex="-1" role="dialog" style="z-index: 99999;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">View Email</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @include('emails.shortcuts')
                        <p><strong>Subject : </strong><input type="text" id="quickemailSubject" name="subject"
                                class="form-control"></p>
                        <p><strong>Body : </strong>
                            <textarea id="reply-message" name="message" class="form-control reply-email-message"></textarea>
                        </p>
                        </br>
                        <span class="pull-right"><label>History : <input type="checkbox" name="pass_history"
                                    id="pass_history" value="1" style=" height: 13px;"></label></span>
                        <span><strong>Message Body : </strong> - <span id="quickemailDate"></span></span><br>
                        <span><strong>From : </strong> - <span id="quickemailFrom"></span></span><br>
                        <span><strong>To : </strong> - <span id="quickemailTo"></span></span><br>
                        <span><strong>CC : </strong> - <span id="quickemailCC"></span></span><br>
                        <span><strong>BCC : </strong> - <span id="quickemailBCC"></span></span>

                        <input type="hidden" id="receiver_email">
                        <input type="hidden" id="sender_email_address">
                        <input type="hidden" id="reply_email_id">
                        <div id="formattedContent" class="mt-5"></div>

                        <div class="col-md-12">
                            <iframe src="" id="eFrame" scrolling="no" style="width:100%;"
                                frameborder="0" onload="autoIframe('eFrame');"></iframe>
                        </div>
                    </div>
                    <div class="modal-footer" style=" width: 100%; display: inline-block;">
                        <label style=" float: left;"><span>Unread :</span> <input type="checkbox" id="unreadEmail"
                                value="" style=" height: 13px;"></label>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-default submit-reply-email">Reply</button>
                    </div>
                </div>
            </div>
        </div>
    @endif