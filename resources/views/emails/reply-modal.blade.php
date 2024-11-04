    <style>
        .btn-gray {
            background-color: #c5c5c5;
            color: black;
        }
    </style>

    <div class="modal-body">
        <input type="hidden" id="reply_email_id_1" name="reply_email_id" value="{{ $email['id'] }}" />

        <button type="button" class="btn btn-gray btn-sm pull-right mb-2" data-reply-add-receiver-btn>Add Receiver</button>

        <div class="form-group">
            <input type="text" id="reply_receiver_email" name="receiver_email" value="{{ $email->to }}" style="width: 100%;" readonly>
        </div>

        <div class="form-group">
            <input type="text" id="reply_subject" name="reply_subject" value="{{ $email->subject }}" style="width: 100%;">
        </div>

        <div class="form-group" style="margin-bottom:10px;margin-right:5px;">
            <select class="form-control" name="" id="reply-type" onchange="displayReplyCategory(this);">
                <option value="">Select Reply Type</option>
                <option value="reply">Reply List</option>
                <option value="quick-reply">Quick Reply List</option>
            </select>
        </div>

        <div class="form-group" id="reply-category-div" style="display:none;">
            <select class="form-control" name="" id="reply-category" onchange="displayReplayList(this);">
                <option value="">Select Reply Category</option>
                @foreach ($replyCategories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group" id="store-website-div" style="display:none;">
            <select class="form-control" name="" id="storeWebsite">
                <option value="">Select Store Website</option>
                @foreach ($storeWebsites as $website)
                    <option value="{{ $website->id }}">{{ $website->website }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group" id="parent-category-div" style="display:none;">
            <select class="form-control globalSelect2" style="width:100%" name="parent_category_id" data-placeholder="Select Parent Category" id="parentCategory">
                @if ($parentCategory)
                <option value="">Select Parent Category</option>
                    @foreach($parentCategory as $key => $parentCategory)
                        <option value="{{ $parentCategory->id }}">{{ $parentCategory->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="form-group" id="category-div" style="display:none;">
            <select class="form-control globalSelect2" style="width:100%" name="category_id" data-placeholder="Select Category" id="categoryDrpDwn">
                @if ($categories)
                    <option value="">Select Category</option>
                    @foreach($categories as $key => $cat)
                        <option value="{{ $key }}">{{ $cat }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="form-group" id="sub-category-div" style="display:none;">
            <select class="form-control globalSelect2" style="width:100%" name="sub_category_id" data-placeholder="Select Sub Category" onchange="displayReplayListFromQuickReply(this);">
                @if ($subCategory)
                    <option value="">Select Sub Category</option>
                    @foreach($subCategory as $key => $subCategory)
                    <option value="{{ $key }}">{{ $subCategory }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="form-group reply-div" style="display:none;">

        </div>

        <div class="form-group">
            <textarea id="reply-message-1" name="message" class="form-control reply-message-textarea" rows="3" placeholder="Reply..."></textarea>
            <div class="message-to-reply">
                <blockquote style="margin:15px 0px 0px 0.8ex;border-left:1px solid rgb(204,204,204);padding-left:1ex">
                    <iframe src="{{url('/email/email-frame', [$email['id']])}}" id="replyFrame" scrolling="no" style="width:100%;" frameborder="0" onload="autoIframe('replyFrame');"></iframe>
                </blockquote>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-default submit-reply">Reply</button>
    </div>