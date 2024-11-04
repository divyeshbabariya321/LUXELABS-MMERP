@include('partials.loader')
@if (auth()->user())
    <div id="menu-sop-search-model" class="modal fade" role="dialog">
    </div>

    <!-- user-search Modal-->
    <div id="menu-user-search-model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">User Search</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-flex" id="search-bar">
                                <input type="text" value="" name="search" id="menu_user_search"
                                    class="form-control" placeholder="Search Here.." style="width: 30%;">
                                <a title="User Search" type="button" id="menu-user-search-btn"
                                    class="menu-user-search-btn btn btn-sm btn-image " style="padding: 10px"><span>
                                        <img src="{{ asset('images/search.png') }}" alt="Search"></span></a>
                                <span class="processing-txt d-none">{{ __('Loading...') }}</span>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="table table-bordered table-responsive mt-3">
                                <table class="table table-bordered page-notes"
                                    style="font-size:13.8px;border:0px !important; table-layout:fixed"
                                    id="NameTable-app-layout">
                                    <thead>
                                        <tr>
                                            <th width="10%">ID</th>
                                            <th width="30%">Name</th>
                                            <th width="30%">Email</th>
                                            <th width="30%">Phone</th>
                                        </tr>
                                    </thead>
                                    <tbody class="user_search_global_result">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- sop-add Modal-->
    <div class="modal fade" id="exampleModalAppLayout" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="FormModalAppLayout">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name-app-layout" name="name"
                                required />
                        </div>
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select name="category[]" id="categorySelect-app-layout"
                                class="globalSelect2 form-control"
                                data-ajax="{{ route('select2.sop-categories') }}" data-minimuminputlength="1"
                                multiple>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="content">Content</label>
                            <input type="text" class="form-control" id="content-app-layout" required />
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary btnsave" id="btnsave">Submit</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- email-search Modal-->
    <div id="menu-email-search-model" class="modal fade" role="dialog">
    </div>

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

    <div id="menu-sopupdate" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Data</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('updateName') }}" id="menu_sop_edit_form">
                        <input type="text" hidden name="id" id="sop_edit_id">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="hidden" class="form-control sop_old_name" name="sop_old_name"
                                id="sop_old_name" value="">
                            <input type="text" class="form-control sopname" name="name"
                                id="sop_edit_name">
                        </div>
                        <div class="form-group">
                            <label for="name">Category</label>
                            <input type="hidden" class="form-control sop_old_category" name="sop_old_category"
                                id="sop_old_category" value="">
                            <select class="form-control sopcategory" id="sop_edit_category" name="category[]" multiple>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="content">Content</label>
                            <textarea class="form-control sop_edit_class" name="content" id="sop_edit_content"></textarea>
                        </div>

                        <button type="submit" class="btn btn-secondary ml-3 updatesopnotes">Update</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="decline-remarks" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Remarks</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('appointment-request.declien.remarks') }}" id="menu_sop_edit_form">
                        <input type="text" hidden name="appointment_requests_id" id="appointment_requests_id">
                        @csrf
                        <div class="form-group">
                            <label for="content">Remarks</label>
                            <textarea class="form-control sop_edit_class" name="appointment_requests_remarks" id="appointment_requests_remarks"></textarea>
                            <span class="text-danger"></span>
                        </div>

                        <button type="button" class="btn btn-secondary ml-3 updatedeclienremarks">Update</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @auth
        
        
    @endauth

    <!-- sop-search Modal-->
    <div id="commandResponseHistoryModelHeader" class="modal fade" role="dialog" style="z-index:2000">
        <div class="modal-dialog modal-lg" style="max-width: 100%;width: 90% !important;">
            <div class="modal-content ">
                <div id="add-mail-content">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Command Response History</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 3%;">ID</th>
                                        <th style="width: 5%;overflow-wrap: anywhere;">User Name</th>
                                        <th style="width: 5%;overflow-wrap: anywhere;">Command Name</th>
                                        <th style="width: 5%;overflow-wrap: anywhere;">Status</th>
                                        <th style="width: 5%;overflow-wrap: anywhere;">Response</th>
                                        <th style="width: 5%;overflow-wrap: anywhere;">Request</th>
                                        <th style="width: 5%;overflow-wrap: anywhere;">Job ID</th>
                                        <th style="width: 4%;overflow-wrap: anywhere;">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="tbodayCommandResponseHistory">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="instructionAlertModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Instruction Reminder</h3>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <a href="" id="instructionAlertUrl" class="btn btn-secondary mx-auto">OK</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="developerAlertModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Developer Task Reminder</h3>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <a href="" id="developerAlertUrl" class="btn btn-secondary mx-auto">OK</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="masterControlAlertModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Master Control Alert</h3>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <a href="" id="masterControlAlertUrl" class="btn btn-secondary mx-auto">OK</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="instructionAlertModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Instruction Reminder</h3>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <a href="" id="instructionAlertUrl" class="btn btn-secondary mx-auto">OK</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="developerAlertModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Developer Task Reminder</h3>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <a href="" id="developerAlertUrl" class="btn btn-secondary mx-auto">OK</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="masterControlAlertModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Master Control Alert</h3>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <a href="" id="masterControlAlertUrl" class="btn btn-secondary mx-auto">OK</a>
                </div>
            </div>
        </div>
    </div>
    
    <div id="showLatestEstimateTime" class="modal fade" role="dialog">
    </div>

    <div id="todolist-request-model" class="modal fade" role="dialog">
        <div class="modal-content modal-dialog modal-md">
            <form action="{{ route('todolist.store') }}" method="POST" onsubmit="return false;">
                @csrf

                <div class="modal-header">
                    <h4 class="modal-title">Create Todo List</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body show-list-records" id="todolist-request">
                    <div class="form-group">
                        <strong>Title:</strong>
                        <input type="text" name="title" class="form-control add_todo_title"
                            value="{{ old('title') }}" required="">
                        <span class="text-danger"></span>
                    </div>
                    <div class="form-group">
                        <strong>Subject:</strong>
                        <input type="text" name="subject" class="form-control add_todo_subject"
                            value="{{ old('subject') }}" required="">
                        <span class="text-danger"></span>
                    </div>

                    <div class="form-group">
                        <strong>Category:</strong>
                        
                        <select name="todo_category_id" class="form-control add_todo_category">
                        </select>
                        <span class="text-danger"></span>
                    </div>

                    <div class="form-group othercat" style="display: none;">
                        <strong>Add New Category:</strong>
                        <input type="text" name="other" class="form-control add_todo_other"
                            value="{{ old('other') }}">
                        <span class="text-danger"></span>
                    </div>


                    <div class="form-group">
                        <strong>Status:</strong>
                        
                        <select name="status" class="form-control add_todo_status">
                            {{-- @foreach ($statuses as $status)
                                <option value="{{ $status['id'] }}"
                                    @if (old('status') == $status['id']) selected @endif>{{ $status['name'] }}
                                </option>
                            @endforeach --}}
                        </select>
                        <span class="text-danger"></span>
                    </div>
                    <div class="form-group" style="margin-bottom: 0px;">
                        <strong>Date:</strong>

                        <div class='input-group date' id='todo-date' required="">
                            <input type="text" class="form-control global add_todo_date" name="todo_date"
                                placeholder="Date" value="{{ old('todo_date') }}">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <span class="text-danger text-danger-date"></span>

                    <div class="form-group" style="margin-top: 15px;">
                        <strong>Remark:</strong>
                        <input type="text" name="remark" class="form-control add_todo_remark"
                            value="{{ old('remark') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary submit-todolist-button">Store</button>
                </div>
            </form>
        </div>
    </div>

    <div id="menu-todolist-get-model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Todo List</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form id="database-form">
                                @csrf
                                <div class="row">
                                    <div class="col-12 pb-3">
                                        <div class="row">
                                            <div class="col-4 pr-0">
                                                <label for="todolist_search">Search Keyword:</label>
                                                <input type="text" name="todolist_search"
                                                    class="dev-todolist-table" class="form-control"
                                                    placeholder="Search Keyword" style=" width: 100%;">
                                            </div>
                                            <div class="col-3 pr-0">
                                                <div class="form-group">
                                                    <label for="start_date">Start Date:</label>
                                                    <input type="date" class="form-control"
                                                        id="todolist_start_date" name="start_date">
                                                </div>
                                            </div>
                                            <div class="col-3 pr-0">
                                                <div class="form-group">
                                                    <label for="end_date">End Date:</label>
                                                    <input type="date" class="form-control"
                                                        id="todolist_end_date" name="end_date">
                                                </div>
                                            </div>
                                            <div class="col-2 pr-0">
                                                <div class="form-group">
                                                    <label for="button" style=" width: 100%;">&nbsp;</label>
                                                    <button type="button"
                                                        class="btn btn-secondary btn-todolist-search-menu"><i
                                                            class="fa fa-search"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Title</th>
                                                    <th>Subject</th>
                                                    <th>Category</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody class="show-search-todolist-list">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="menu_user_history_modal" class="modal fade" tabindex="-1" role="dialog" style="z-index: 99999;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User history</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12" id="user_history_div">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>User type</th>
                                        <th>Previous user</th>
                                        <th>New User</th>
                                        <th>Updated by</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="menu_confirmMessageModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

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
                            <input checked="checked" name="send_message_recepients[]"
                                class="send_message_recepients" type="checkbox" value="assign_by">Assign By
                            <input checked="checked" name="send_message_recepients[]"
                                class="send_message_recepients" type="checkbox" value="assigned_to">Assign To
                            <input checked="checked" name="send_message_recepients[]"
                                class="send_message_recepients" type="checkbox" value="master_user_id">Lead 1
                            <input checked="checked" name="send_message_recepients[]"
                                class="send_message_recepients" type="checkbox"
                                value="second_master_user_id">Lead 2
                            <input checked="checked" name="send_message_recepients[]"
                                class="send_message_recepients" type="checkbox" value="contacts">Contacts
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary menu-confirm-messge-button">Send</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <div id="menu-upload-document-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Upload Document</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="menu-upload-task-documents">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" id="hidden-identifier" name="developer_task_id" value="">
                        <div class="row">
                            <div class="col-md-10 col-md-offset-1">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Subject</label>
                                            {{ html()->text('subject')->class('form-control')->placeholder('Enter subject') }}
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Description</label>
                                            {{ html()->textarea('description')->class('form-control')->placeholder('Enter Description') }}
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Documents</label>
                                            <input type="file" name="files[]" id="filecount"
                                                multiple="multiple">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-default">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="menu-blank-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="menu-file-upload-area-section" class="modal fade" role="dialog" style="z-index: 99999;">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('task.save-documents') }}" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="task_id" id="hidden-task-id" value="">
                    <div class="modal-header">
                        <h4 class="modal-title">Upload File(s)</h4>
                    </div>
                    <div class="modal-body" style="background-color: #999999;">
                        @csrf
                        <div class="form-group">
                            <label for="document">Documents</label>
                            <input type="file" name="document" class="needsclick" id="document-dropzone"
                                multiple>
                            

                            
                        </div>
                        <div class="form-group add-task-list">

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default menu-btn-save-documents">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="menu-preview-task-image" class="modal fade" role="dialog" style="z-index: 99999;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:1%;">No</th>
                                    <th style=" width: 30%">Files</th>
                                    <th style="word-break: break-all; width:12%">Send to</th>
                                    <th style="width: 1%;">User</th>
                                    <th style="width: 11%">Created at</th>
                                    <th style="width: 6%">Action</th>
                                </tr>
                            </thead>
                            <tbody class="menu-task-image-list-view">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="create-manual-payment" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content" id="create-manual-payment-content">

            </div>
        </div>
    </div>

    @if ($isAdmin)
        <div id="emailAlertModal" class="modal fade mymodal" role="dialog">
            <div class="modal-dialog modal-lg">

                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="emailAlert-modal-subject">Subject</h4>
                        <button class="close modalMinimize"> <i class='fa fa-minus'></i> </button>
                        <button type="button" class=" btn" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p id="emailAlert-modal-body">
                            <iframe style="width: 100%;border:none;height:70vh;"
                                id="emailAlert-modal-body-myframe" frameborder="0"></iframe>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button id="emailAlert-reply" type="button" class="btn btn-default"
                            data-dismiss="modal">Reply
                        </button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="minmaxCon"></div>
    @endif

@endif

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                dropdownAutoWidth: true,
            });
        });

            // To set the iframe height based on content
        if (autoIframe == undefined || (autoIframe !== undefined && typeof autoIframe !== 'function')) {
            function autoIframe(frameId) {
                try {
                    frame = document.getElementById(frameId);
                    innerDoc = (frame.contentDocument) ?
                        frame.contentDocument : frame.contentWindow.document;
                    objToResize = (frame.style) ? frame.style : frame;
                    objToResize.height = innerDoc.body.scrollHeight + 10 + 'px';
                }
                catch (err) {
                    window.status = err.message;
                }
            }
        }
    </script>
@endsection
