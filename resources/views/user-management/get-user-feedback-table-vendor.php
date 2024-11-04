@extends('layouts.app')

@section('favicon' , 'task.png')

@section('title', 'Users Feedback')

@section('content')
    <style type="text/css">
        .feedback_model .modal-dialog{
           max-width:1024px;
           width:100%;
        }
        .quick_feedback, #feedback-status{
            border: 1px solid #ddd;
            border-radius: 4px;
            height: 35px;
            outline: none;
        }
        .quick_feedback:focus, #feedback-status:focus{
            outline: none;
        }
        .communication-td input{
            width: calc(100% - 25px) !important;
        }
        .communication-td button{
            width:20px;
        }

    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <div class="col-md-12">
        <div class="row" id="common-page-layout">
            <input type="hidden" name="page_no" class="page_no" />
            <div class="col-lg-12 margin-tb">
                <h2 class="page-heading">Users Feedback</h2>
                <div class="row">
                    <div class="col">
                        <div class="" style="margin-bottom:10px;">
                            <div class="row">
                                <form class="form-inline message-search-handler" method="get">
                                    <div class="col">
                                        <div class="form-group">
                                            <select name="user_id" class="form-control  select-multiple">
                                                <option>-select-</option>
                                                <?php foreach ($users as $key => $user) {
                                                    $selectedUser = '';
                                                    if ($user->id == $request->user_id) {
                                                        $selectedUser = 'selected="selected"';
                                                    }
                                                    echo '<option value="' . $user->id . '" ' . $selectedUser . '>' . $user->name . '</option>';
                                                }?>
                                            </select>
                                        </div>
                                        {{-- <div class="form-group">
                                            <select name="is_active" class="form-control" placholder="Active:">
                                                <option value="0" {{ request('is_active') == 0 ? 'selected' : '' }}>All</option>
                                                <option value="1" {{ request('is_active') == 1 ? 'selected' : '' }}>Active
                                                </option>
                                                <option value="2" {{ request('is_active') == 2 ? 'selected' : '' }}>In active
                                                </option>
                                            </select>
                                        </div> --}}
                                        <div class="form-group pl-3">
                                            <label for="button">&nbsp;</label>
                                            <button style="display: inline-block;width: 10%;margin-top: -26px; padding-left: 0px"
                                                class="btn btn-sm btn-image btn-search-action">
                                                <img src="{{asset('/images/search.png')}}" style="cursor: default;">
                                            </button>
                                        </div>
                                    </div>
                                </form>

                        </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 margin-tb" id="page-view-result">

                </div>
            </div>
        </div>
        <div class="table-responsive">
        <table class="table table-bordered" style="margin-top: 25px">
            <tr>
                <th width="17%">Category</th>
                <th width="15%">SOP</th>
                <th width="15%">Admin Response</th>
                <th width="15%">User Response</th>
                <th width="15%">Status</th>
                <th width="10%">History</th>
            </tr>
            @if (Auth::user()->isAdmin())
            <tr>
                <td>
                    <input type="text" style="width:calc(100% - 41px)" class="quick_feedback" id="addcategory" name="category">
                    <button style="width: 20px" type="button" class="btn btn-image add-feedback" id="btn-save"><img src="{{asset('/images/add.png')}}" style="cursor: nwse-resize; width: 0px;"></button>
                </td>
                <td></td>
                <td></td>
                <td><input type="textbox" style="width:calc(100% - 41px)" id="feedback-status">
                    <button style="width: 20px" type="button" class="btn btn-image user-feedback-status"><img src="{{asset('/images/add.png')}}" style="cursor: nwse-resize; width: 0px;"></button></td>
                <td></td>
            </tr>
            @endif
            <?php $sopOps = ''; ?>
            @foreach ($sops as $sop)
                <?php $sopOps .= '<option value="' . $sop->id . '">' . $sop->name . '</option>' ?>
            @endforeach
            @foreach ($category as $cat)
                @php
                    if($user_id !=''){
                        $cat->user_id = $user_id;
                    }
                    $latest_messages = App\ChatMessage::select('message')->where('user_feedback_id', $cat->user_id)->where('user_feedback_category_id', $cat->id)->orderBy('id','DESC')->first();
                    if ($latest_messages) {
                        $latest_msg = $latest_messages->message;
                        if (strlen($latest_msg) > 20) {
                            $latest_msg = substr($latest_messages->message,0,20).'...';
                        }
                    }
                    $feedback_status = App\UserFeedbackStatusUpdate::select('user_feedback_status_id')->where('user_id', $cat->user_id)->where('user_feedback_category_id', $cat->id)->first();
                    $status_id = 0;
                    if ($feedback_status) {
                        $status_id = $feedback_status->user_feedback_status_id;
                    }
                    $latest_comment = App\UserFeedbackCategorySopHistoryComment::select('comment', 'id')
                                ->where('sop_history_id', $cat->sop_id)->whereNotNull('sop_history_id')
                                ->orderBy('id','DESC')->first();
                    $comment = '';
                    if(isset($latest_comment->comment))
                        $comment = $latest_comment->comment.'...';
                    $commentId = '';
                    if(isset($latest_comment->comment))
                        $commentId = $latest_comment->id;
                @endphp
                <tr data-cat_id="{{ $cat->id }}" data-user_id="{{ $cat->user_id }}">
                    <td>{{ $cat->category }}</td>
                    <td class="communication-td">
                        @if(\Auth::user()->isAdmin() == true)
                            {{-- <input type="text" class="form-control" data-id="{{$cat->id}}" id="sop_{{$cat->id}}" name="sop_{{$cat->id}}" placeholder="Enter SOP name...."  style="margin-bottom:5px;width:77%;display:inline;"/> --}}
                            <select class="form-control" data-id="{{$cat->id}}" id="sop_{{$cat->id}}" name="sop_{{$cat->id}}" style="margin-bottom:5px;width:77%;display:inline;">
                                <option value="">-Select sop-</option>
                                @foreach ($sops as $sop)
                                    <?php echo '<option value="' . $sop->id . '">' . $sop->name . '</option>'; ?>
                                @endforeach
                            </select>
                            <div class="row">
                                <div class="col-4 pr-0">
                                    <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image user-sop-save" data-sop="sop_{{$cat->id}}" data-feedback_cat_id="{{$cat->id}}" type="submit" id="submit_message"  data-id="{{$cat->id}}" ><img src="{{asset('/images/filled-sent.png')}}"/></button>
                                    @if(Auth::user()->isAdmin())
                                        <button type="button" class="btn btn-secondary1 mr-2" data-toggle="modal" title="Add Sop Name and category" data-target="#exampleModal"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                    @endif
                                </div>
                                <div class="sop-text-{{$cat->id}}" style="float: left;">
                                    <div class="expand-row-msg" data-name="name" data-id="{{$cat->id}}"  style="float: left;">
                                        <span class="show-short-name-{{$cat->id}}">{{ Str::limit($cat->sop, 18, '..')}}</span>
                                        <span style="word-break:break-all;" class="show-full-name-{{$cat->id}} hidden">{{$cat->sop}}</span>
                                    </div> 
                                    <div  style="float: left;">&nbsp;
                                        <img class='sop-history' src='{{asset('/images/chat.png')}}' data-cat_id="{{$cat->id}}" data-sop_id="{{$cat->sop_id}}" alt='history' style='width:17px;cursor: nwse-resize;'>
                                    </div>
                                </div>
                            </div>
                        @else
{{--                            <div id="comment_div_{{$cat->id}}">--}}
{{--                                <input type="radio" name="accept_reject_{{$cat->id}}" id="accept_reject_{{$cat->id}}" value="Yes" style="width: 12px !important;height: 12px !important;"> Yes &nbsp;--}}
{{--                                <input type="radio" name="accept_reject_{{$cat->id}}" id="accept_rejectN_{{$cat->id}}" value="No" style="width: 12px !important;height: 12px !important;"> No--}}
{{--                                <input type="text" class="form-control " data-id="{{$cat->id}}" id="comment_{{$cat->id}}" name="comment_{{$cat->id}}" placeholder="Enter comment ...."  style="margin-bottom:5px;width:77%;display:inline;"/>--}}
{{--                                <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image user-sop-comment-save" data-sop_id="{{$cat->sop_id}}" data-id="{{$cat->id}}" data-comment="comment_{{$cat->id}}" data-feedback_cat_id="{{$cat->id}}"  type="submit" id="submit_message" ><img src="/images/filled-sent.png"/></button>--}}
{{--                                <div class="sop-comment-text-{{$cat->id}}">--}}
{{--                                    <div style='width:50%;'>{{$comment}}</div> <img class='sop-comment-history' src='/images/chat.png' data-id="{{$cat->id}}" data-sop_history_id="{{$cat->sop_id}}" data-sop_comment_id="{{$commentId}}" alt='history' style='width:17px;cursor: nwse-resize;'>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            {{$cat->sop}}
                        @endif                        
                        
                    </td>
                    <td class="communication-td">
                         <input type="text" class="form-control send-message-textbox" data-id="{{$cat->user_id}}" id="send_message_{{$cat->user_id}}" name="send_message_{{$cat->user_id}}" placeholder="Enter Message...." style="margin-bottom:5px;width:77%;display:inline;" @if (!Auth::user()->isAdmin()) {{ "readonly" }} @endif/>
{{--                        <select class="form-control send-message-textbox" data-id="{{$cat->user_id}}" id="send_message_{{$cat->user_id}}" name="send_message_{{$cat->user_id}}" style="margin-bottom:5px;width:77%;display:inline;" @if (!Auth::user()->isAdmin()) {{ "readonly" }} @endif>--}}
{{--                            <?php echo $sopOps; ?></?php>--}}
{{--                        </select>--}}
                        <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image send-message-open" data-feedback_cat_id="{{$cat->id}}" type="submit" id="submit_message"  data-id="{{$cat->user_id}}" ><img src="{{asset('/images/filled-sent.png')}}"/></button>
                        @if ($latest_messages && $latest_messages->user_feedback_category_id == $cat->id)
                            <span class="latest_message">@if ($latest_messages->send_by) {{ $latest_msg }} @endif</span>
                        @else
                            <span class="latest_message"></span>
                        @endif
                    </td>
                    <td class="communication-td">
{{--                        <input type="text" class="form-control send-message-textbox" data-id="{{$cat->user_id}}" id="send_message_{{$cat->user_id}}" name="send_message_{{$cat->user_id}}" placeholder="Enter Message...." style="margin-bottom:5px;width:77%;display:inline;" @if (Auth::user()->isAdmin()) {{ "readonly" }} @endif/>--}}
{{--                        <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image send-message-open" data-feedback_cat_id="{{$cat->id}}" type="submit" id="submit_message"  data-id="{{$cat->user_id}}" ><img src="/images/filled-sent.png"/></button></button>--}}
{{--                        @if ($latest_messages && $latest_messages->user_feedback_category_id == $cat->id)--}}
{{--                            <span class="latest_message">@if (!$latest_messages->send_by) {{ $latest_msg }} @endif</span>--}}
{{--                        @else--}}
{{--                            <span class="latest_message"></span>--}}
{{--                        @endif--}}
                        <div id="comment_div_{{$cat->id}}">
                            {{-- <input type="radio" name="accept_reject_{{$cat->id}}" id="accept_reject_{{$cat->id}}" value="Yes" style="width: 12px !important;height: 12px !important;"> Yes &nbsp;
                            <input type="radio" name="accept_reject_{{$cat->id}}" id="accept_rejectN_{{$cat->id}}" value="No" style="width: 12px !important;height: 12px !important;"> No --}}
                            <input type="text" class="form-control " data-id="{{$cat->id}}" id="comment_{{$cat->id}}" name="comment_{{$cat->id}}" placeholder="Enter comment ...."  style="margin-bottom:5px;width:77%;display:inline;"  @if (Auth::user()->isAdmin()) {{ "readonly" }} @endif/>
                            @if (Auth::user()->isAdmin())

                            @else
                                <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image user-sop-comment-save" data-sop_id="{{$cat->sop_id}}" data-id="{{$cat->id}}" data-comment="comment_{{$cat->id}}" data-feedback_cat_id="{{$cat->id}}"  type="submit" id="submit_message" ><img src="/images/filled-sent.png"/></button>
                            @endif

                            <div class="sop-comment-text-{{$cat->id}}">
                                <div style='width:50%;'>{{$comment}}</div> <img class='sop-comment-history' src='{{asset('/images/chat.png')}}' data-id="{{$cat->id}}" data-sop_history_id="{{$cat->sop_id}}" data-sop_comment_id="{{$commentId}}" alt='history' style='width:17px;cursor: nwse-resize;'>
                            </div>
                        </div>
                    </td>
                    <td>
                        <select class="form-control user_feedback_status">
                            <option value="">Select</option>
                            @foreach ($status as $st)
                                <option value="{{$st->id}}" @if ($st->id == $status_id) {{ "selected" }} @endif>{{ $st->status }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-xs btn-image load-communication-modal" data-feedback_cat_id="{{$cat->id}}" data-object='user-feedback' data-id="{{$cat->user_id}}" style="mmargin-top: -0%;margin-left: -2%;" title="Load messages"><img src="{{asset('/images/chat.png')}}" alt=""></button>
                        <button type="button" class="btn btn-secondary1 hrTicket" data-toggle="modal"  data-feedback_cat_id="{{$cat->id}}" data-id="{{$cat->user_id}}" data-cat_name="{{$cat->category}}" title="Add Ticket" data-target="#hrTicketModal" id="hrTicket"><i class="fa fa-plus" aria-hidden="true"></i></button>
                        <button style="padding-left: 0px;padding-right:0px;margin-top:2px;" type="button" class="btn pt-1 btn-image d-inline count-dev-customer-tasks"  title="Show task history" data-id="{{$cat->id}}" data-user_id="{{$cat->user_id}}"><i class="fa fa-info-circle"></i></button>
                        @if (auth()->user()->isAdmin())
                            <button style="padding-left: 10px;padding-right:0px;margin-top:2px;" type="button" class="btn pt-1 btn-image d-inline delete-category"  title="Delete Category with all data" data-id="{{$cat->id}}" ><i class="fa fa-trash"></i></button>
                        @endif
                    </td>
                </tr>
            @endforeach
    </table>
    {{ $category->links() }}
</div>
    </div>
<!--------------------------------------------------- Add Data Modal ------------------------------------------------------->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add Sop Name and Category</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="FormModal">
                @csrf
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required />
                </div>

                <div class="form-group">
                    <label for="name">Category</label>
                    <input type="text" class="form-control" id="category" name="category" required />
                </div>

                <div class="form-group">
                    <label for="content">Content</label>
                    <input type="text" class="form-control" id="content" required />
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


<div class="modal fade" id="hrTicketModal" tabindex="-1" role="dialog" aria-labelledby="hrTicketModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            {{-- users.feedback.task.create --}}
            <form action="<?php echo route('task.create.task.shortcut'); ?>" method="post">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h4 class="modal-title">Create Task</h4>
                </div>
                <div class="modal-body">
                    <input class="form-control" value="{{\Auth::user()->id}}" type="hidden" name="user_id">
                    <input class="form-control" value="4" type="hidden" name="category_id">
                    <input class="form-control" type="hidden" name="user_feedback_cat_id" id="user_feedback_cat_id" value="">
                    <div class="form-group">
                        <label for="">Subject</label>
                        <input class="form-control" type="text" id="hidden-task-subject" name="task_subject"  />
                    </div>
                    <div class="form-group">
                        <select class="form-control" style="width:100%;" name="task_type" tabindex="-1"
                            aria-hidden="true">
                            <option value="0">Other Task</option>
                            <option value="4">Developer Task</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="repository_id">Repository:</label>
                        <br>
                        <select style="width:100%" class="form-control  " id="repository_id" name="repository_id">
                            <option value="">-- select repository --</option>
                            @foreach (\App\Github\GithubRepository::all() as $repository)
                                <option value="{{ $repository->id }}">{{ $repository->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Details</label>
                        <input class="form-control text-task-development" type="text" name="task_detail" />
                    </div>

                    <div class="form-group">
                        <label for="">Cost</label>
                        <input class="form-control" type="text" name="cost" />
                    </div>

                    <div class="form-group">
                        <label for="">Assign to</label>
                        <select name="task_asssigned_to" id="task_asssigned_to" class="form-control assign-to select2">
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-default create-task">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="hr_task_statistics" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2>HR Task </h2>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body" id="hr_task_statistics_content">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Task type</th>
                                <th>Task Id</th>
                                <th>Assigned to</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="dev_task_statistics" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Dev Task statistics</h2>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body" id="dev_task_statistics_content">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Task type</th>
                                <th>Task Id</th>
                                <th>Assigned to</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="chat-list-history" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Communication</h4>
                <input type="hidden" id="chat_obj_type" name="chat_obj_type">
                <input type="hidden" id="chat_obj_id" name="chat_obj_id">
                <button type="submit" class="btn btn-default downloadChatMessages">Download</button>
            </div>
            <div class="modal-body" style="background-color: #999999;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div id="sop-history" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 1200px; width:1200px;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sop history</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Sop Name</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody class="show-sop-history-records">
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
<div id="sop-comment-history" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sop comment history</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Comment</th>
                                    <th>User Name</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody class="show-sop-comment-history-records">
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
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script src="https://cdn.ckeditor.com/4.11.4/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('content');
    $('#FormModal').submit(function(e) {
            e.preventDefault();
            let name = $("#name").val();
            let category = $("#category").val();
            let content = CKEDITOR.instances['content'].getData(); //$('#cke_content').html();//$("#content").val();

            let _token = $("input[name=_token]").val();

            $.ajax({
                url: "{{ route('sop.store') }}",
                type: "POST",
                data: {
                    name: name,
                    category: category,
                    content: content,

                    _token: _token
                },
                success: function(response) {
                    if (response) {
                        if(response.success==false){
                            toastr["error"](response.message, "Message");
                            return false;
                        }
                        var content_class = response.sop.content.length < 270 ? '' : 'expand-row';
                        var content = response.sop.content.length < 270 ? response.sop.content : response.sop.content.substr(0, 270) + '.....';
                        $("#FormModal")[0].reset();
                        $('.cke_editable p').text(' ');
                        CKEDITOR.instances['content'].setData('');
                        $("#exampleModal").modal('hide');
                        toastr["success"]("Data Inserted Successfully!", "Message");
                        location.reload();
                        
                    }
                }

            });
        });
    $('.select-multiple').select2();
    $("#user_id").select2({
        ajax: {
            url: '{{ route('user-search') }}',
            dataType: 'json',
            data: function(params) {
                return {
                    q: params.term, // search term
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
        },
        placeholder: "Select User",
        allowClear: true,
        minimumInputLength: 2,
        width: '100%',
    });

    $(document).on("click", ".create-task", function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        $.ajax({
            url: form.attr("action"),
            type: 'POST',
            data: form.serialize(),
            beforeSend: function() {
                $(this).text('Loading...');
            },
            success: function(response) {
                if (response.code == 200) {
                    form[0].reset();
                    toastr['success'](response.message);
                    $("#hrTicketModal").modal('hide');
                } else {
                    toastr['error'](response.message);
                }
            }
        }).fail(function(response) {
            toastr['error'](response.responseJSON.message);
        });
    });

    $(".hrTicket").bind("click", function() { 
        var feedback_cat_id = $(this).data("feedback_cat_id");
        var cat_name = $(this).data("cat_name");
        $("#user_feedback_cat_id").val(feedback_cat_id);
        let selecUserVal = $('.select-multiple').val();
        $("#task_asssigned_to").val(selecUserVal);
        $("#hidden-task-subject").val(cat_name);
        
    });

    $(document).on("click", ".tasks-list", function() {

        var $this = $(this);
        var user_id = $(this).data("user_id");
        var feedback_cat_id = $(this).data("id");
        $.ajax({
            type: 'get',
            url: "/instagram/users/feedback/get/hr_ticket?id="+feedback_cat_id,
            dataType: "json",
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(data) {
                $("#hr_task_statistics").modal("show");
                var table = `<div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th width="4%">Tsk Typ</th>
                        <th width="4%">Tsk Id</th>
                        <th width="7%">Asg to</th>
                        <th width="12%">Desc</th>
                        <th width="12%">Sts</th>
                        <th width="33%">Communicate</th>
                        <th width="10%">Action</th>
                    </tr>`;
                for (var i = 0; i < data.data.length; i++) {
                    var str = data.data[i].subject;
                    var res = str.substr(0, 100);
                    var status = data.data[i].status;
                    if (typeof status == 'undefined' || typeof status == '' || typeof status =='In progress') {
                        status = 'In progress'
                    };
                    if(data.data[i].task_type == 0){
                        task_type = 'Other Task';
                    }else if(data.data[i].task_type == 4){
                        task_type = 'Developer Task';
                    }
                    table = table + '<tr><td>' + task_type + '</td><td>#' +
                        data.data[i].id +
                        '</td><td class="expand-row-msg" data-name="asgTo" data-id="' + data
                        .data[i].id + '"><span class="show-short-asgTo-' + data
                        .data[i].id + '">' + data.data[i].assigned_to_name
                        .replace(/(.{6})..+/, "$1..") +
                        '</span><span style="word-break:break-all;" class="show-full-asgTo-' + data
                        .data[i].id + ' hidden">' + data.data[i]
                        .assigned_to_name +
                        '</span></td><td class="expand-row-msg" data-name="res" data-id="' + data
                        .data[i].id + '"><span class="show-short-res-' + data
                        .data[i].id + '">' + res.replace(/(.{7})..+/, "$1..") +
                        '</span><span style="word-break:break-all;" class="show-full-res-' + data
                        .data[i].id + ' hidden">' + res + '</span></td><td>' + status +
                        '</td><td class="communication-hr-ticket-td"><div class="col-md-10 pl-0 pr-1"><textarea rows="1" style="width: 100%; float: left;" class="form-control quick-message-field input-sm" name="message" placeholder="Message" data-user_id="'+user_id+'"  data-feedback_cat_id="'+feedback_cat_id+'" data-id="'+data.data[i].id+'"></textarea></div><div class="p-0"><button class="btn btn-sm btn-xs send-message-open-hr_ticket" title="Send message" data-taskid="' +
                        data.data[i].id +
                        '"><i class="fa fa-paper-plane"></i></button></div></td><td><button type="button" class="btn btn-xs load-communication-modal load-body-class" data-object="user-feedback-hrTicket" data-id="'+user_id+'" data-feedback_cat_id="' + data.data[i]
                        .id +
                        '" title="Load messages" data-dismiss="modal"><i class="fa fa-comments"></i></button>';
                    table = table + '<a href="javascript:void(0);" data-task-type="' + data
                        .data[i].task_type + '" data-id="' + data.data[i].id +
                        '" class="delete-dev-task-btn btn btn-xs"><i class="fa fa-trash"></i></a>';
                    table = table+'</td>';
                    table = table + '</tr>';
                }
                table = table + '</table></div>';
                $("#loading-image").hide();
                $(".modal").css("overflow-x", "hidden");
                $(".modal").css("overflow-y", "auto");
                $("#hr_task_statistics_content").html(table);
            },
            error: function(error) {
                console.log(error);
                $("#loading-image").hide();
            }
        });


    });
    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
        return false;
    };

    $(document).on("click", ".count-dev-customer-tasks", function() {
    
    var $this = $(this);
    var user_feedback = $(this).data("id");
    var isAvaible = getUrlParameter('user_id');
    var is_set = "";
    if(isAvaible)
        is_set = $(this).data("user_id");
    else
        is_set = "";
    var user_id = $(this).data("user_id");
    var url = "{{route('hr-ticket.countdevtask',[':user_feedback',':user_id'])}}";
    var url1 = url.replace(':user_feedback',user_feedback);
    var url2 = url1.replace(':user_id',user_id);

    $.ajax({
        type: 'get',
        url: url2,
        dataType: "json",
        beforeSend: function() {
            $("#loading-image").show();
        },
        success: function(data) {
            $("#dev_task_statistics").modal("show");
            var table = `<div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th width="4%">Tsk Typ</th>
                        <th width="4%">Tsk Id</th>
                        <th width="7%">Asg to</th>
                        <th width="12%">Desc</th>
                        <th width="12%">Sts</th>
                        <th width="33%">Communicate</th>
                        <th width="10%">Action</th>
                    </tr>`;
                for (var i = 0; i < data.taskStatistics.length; i++) {
                    var str = data.taskStatistics[i].subject;
                    var res = str.substr(0, 100);
                    var status = data.taskStatistics[i].status;
                    if(typeof status=='undefined' || typeof status=='' || typeof status=='0' ){ status = 'In progress'};
                    table = table + '<tr><td>' + data.taskStatistics[i].task_type + '</td><td>#' + data.taskStatistics[i].id + '</td><td class="expand-row-msg" data-name="asgTo" data-id="'+data.taskStatistics[i].id+'"><span class="show-short-asgTo-'+data.taskStatistics[i].id+'">'+data.taskStatistics[i].assigned_to_name.replace(/(.{6})..+/, "$1..")+'</span><span style="word-break:break-all;" class="show-full-asgTo-'+data.taskStatistics[i].id+' hidden">'+data.taskStatistics[i].assigned_to_name+'</span></td><td class="expand-row-msg" data-name="res" data-id="'+data.taskStatistics[i].id+'"><span class="show-short-res-'+data.taskStatistics[i].id+'">'+res.replace(/(.{7})..+/, "$1..")+'</span><span style="word-break:break-all;" class="show-full-res-'+data.taskStatistics[i].id+' hidden">'+res+'</span></td><td>' + status + '</td><td><div class="col-md-10 pl-0 pr-1"><textarea rows="1" style="width: 100%; float: left;" class="form-control quick-message-field input-sm" name="message" placeholder="Message"></textarea></div><div class="p-0"><button class="btn btn-sm btn-xs send-message" title="Send message" data-taskid="'+ data.taskStatistics[i].id +'"><i class="fa fa-paper-plane"></i></button></div></td><td><button type="button" class="btn btn-xs load-communication-modal load-body-class" data-object="' + data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i].id + '" title="Load messages" data-dismiss="modal"><i class="fa fa-comments"></i></button>';
                    table = table + '<a href="javascript:void(0);" data-task-type="'+data.taskStatistics[i].task_type +'" data-id="' + data.taskStatistics[i].id + '" class="delete-dev-task-btn btn btn-xs"><i class="fa fa-trash"></i></a>';
                    table = table + '<button type="button" class="btn btn-xs  preview-img pd-5" data-object="' + data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i].id + '" data-dismiss="modal"><i class="fa fa-list"></i></button></td>';
                    table = table + '</tr>';
                }
                table = table + '</table></div>';
                $("#loading-image").hide();
                $(".modal").css("overflow-x", "hidden");
                $(".modal").css("overflow-y", "auto");
                $("#dev_task_statistics_content").html(table);
            },
            error: function(error) {
                console.log(error);
                $("#loading-image").hide();
            }
        });
    });

    $(document).on("click", "#btn-save",function(e){
            // $('#btn-save').attr("disabled", "disabled");
            e.preventDefault();
             var category = $('#addcategory').val();
             var user_id = $(this).data('user_id');
            if(category!=""){
                 $.ajax({
                    url:"{{ route('user.feedback-category') }}",
                    type:"get",
                    data:{
                        category:category,
                        user_id:user_id,
                    },
                    cashe:false,
                    success:function(response){
                        if (response.message) {
                            toastr.error(response.message);
                        }else{
                            $('#addcategory').val('');
                            $(document).find('.user-feedback-data').append(response);
                        }
                    }
                });
            }else{
               alert("error");
            }
         });

         $(document).on('click','.user-feedback-status',function(){
            var status = $('#feedback-status').val();
            $('.user_feedback_status').text('');

            $.ajax({
                type: "get",
                url: '{{ route("user.feedback-status") }}',
                data: {'status':status},
                success:function(response){
                    if (response.status == true) {
                        $('#feedback-status').val('');
                        var all_status = response.feedback_status;
                        var Select = '<option value="">Select</option>'
                        $('.user_feedback_status').append(Select);

                        for (let i = 0; i < all_status.length; i++) {
                            var html = '<option value="' + all_status[i].id+'">'+all_status[i].status+'</option>'; 
                            $('.user_feedback_status').append(html);
                        }
                    }
                }
            });
        });
        $(document).on('click','.sop-history',function(){
            var id = $(this).data('id');
            var catId = $(this).data("cat_id");
            $.ajax({
                type: "get",
                url: '{{ route("user.get.sop.data") }}',
                data: {
                        'cat_id': catId,
                        },
                success:function(response){
                    if (response.code == 200) {
                        toastr["success"](response.message);
                        // var t = '';
                        // $.each(response.data, function(k, v) {
                        //     t += `<tr><td>` + v.id + `</td>`;
                        //     t += `<td>` + v.sop + `</td>`;
                        //     t += `<td>` + v.created_at + `</td></tr>`;
                        // });
                        // if (t == '') {
                        //     t = '<tr><td colspan="4" class="text-center">No data found</td></tr>';
                        // }
                        $("#sop-history").find(".show-sop-history-records").html(response.data);
                        $("#sop-history").modal("show");

                    } else {
                        toastr["error"](response.message);
                    }
                }
            });
        });
        $(document).on('dblclick', '.expand-row-msg', function() {
            var name = $(this).data('name');
            var id = $(this).data('id');
            var full = '.expand-row-msg .sop-short-' + name + '-' + id;
            var mini = '.expand-row-msg .sop-full-' + name + '-' + id;
            $(full).toggleClass('hidden');
            $(mini).toggleClass('hidden');
        });

        $(document).on('click','.sop-comment-history',function(){
            var id = $(this).data('id');
            var sopId = $(this).data("sop_history_id");
            $.ajax({
                type: "get",
                url: '{{ route("user.get.sop-comment.data") }}',
                data: {
                        'sop_history_id': sopId,
                        },
                success:function(response){
                    if (response.code == 200) {
                        toastr["success"](response.message);
                        
                        var t = '';
                        $.each(response.data, function(k, v) {
                            t += `<tr><td>` + v.id + `</td>`;
                            t += `<td>` + v.comment + `</td>`;
                            t += `<td>` + v.username + `</td>`;
                            t += `<td>` + v.created_at + `</td></tr>`;
                        });
                        if (t == '') {
                            t = '<tr><td colspan="4" class="text-center">No data found</td></tr>';
                        }
                        $("#sop-comment-history").find(".show-sop-comment-history-records").html(t);
                        $("#sop-comment-history").modal("show");

                    } else {
                        toastr["error"](response.message);
                    }
                }
            });
        });

        
        $(document).on('click','.user-sop-save',function(){
            var id = $(this).data('id');
            var sop_id = $(this).data('sop');
            var sop = $("#"+sop_id+ " option:selected").text();
            //debugger;
            var sops_id = $("#"+sop_id).val();
            if(sops_id == ''){
                toastr["error"]('Please Select Sop');
                return false
            }
            var cat = $(this).data("feedback_cat_id");
            
            $("#send_message_"+$(this).data('id')).val('');

            $.ajax({
                type: "get",
                url: '{{ route("user.save.sop") }}',
                data: {
                        'cat_id': cat,
                        'sops_id': sops_id,
                        'sop_text': sop
                        },
                success:function(response){
                    if (response.code == 200) {
                        $("#"+sop_id).val('');
                        var resSop = response.data.sop;
                        var resSopshort = '<span style="float:left;width: 41px;overflow: hidden;height: 23px;">'+response.data.sop+'</span>';
                        var resSopId = response.data.id;
                        toastr["success"](response.message);
                        $(".sop-text-"+id).html("<div class='expand-row-msg' data-name='name' data-id='"+id+"' style='float: left;'><span class='show-short-name-"+id+"'>"+resSopshort+"</span><span style='word-break:break-all;' class='show-full-name-"+id+" hidden'>"+resSop+"</span>...</div><div  style='float: left;'>&nbsp; <img class='sop-history' data-cat_id='"+id+"' data-sop_id='"+resSopId+"' src='{{asset('/images/chat.png')}}' alt='history' style='width:17px;cursor: nwse-resize;'></div>");
                    } else {
                        toastr["error"](response.message);
                    }
                }
            });
        });
        $(document).on('click','.user-sop-comment-save',function(){
            var id = $(this).data('id');
            var comment = $("#comment_"+id).val();
            var acceptReject = $('input[name="accept_reject_'+id+'"]:checked').val();
            var sopHistoryId = $(this).data("sop_id");
           
            $("#send_message_"+$(this).data('id')).val('');

            $.ajax({
                type: "get",
                url: '{{ route("user.save.sop.comment") }}',
                data: {'sop_history_id': sopHistoryId,
                        'comment': comment,
                        'accept_reject': acceptReject
                        },
                success:function(response){
                    if (response.code == 200) {
                        $('#comment_'+id).val('');
                        var resSopComm = response.data.comment;
                        var resSopCommId = response.data.id;
                        var resSopId = response.data.sop_history_id;
                        toastr["success"](response.message);
                        $(".sop-comment-text-"+id).html("<div style='width:50%;'>"+resSopComm+"...</div> <img class='sop-comment-history' data-id='"+id+"' data-sop_history_id='"+resSopId+"' data-sop_comment_id='"+resSopCommasset()+Id+"' src='{{asset('/images/chat.png')}}' alt='history' style='width:17px;cursor: nwse-resize;'>");
                    } else {
                        toastr["error"](response.message);
                    }
                }
            });
        });
        $(document).on('click', '.send-message-open', function (event) {
            var feedback_status_id = $(this).parents('tr').find('.user_feedback_status').val();
            var textBox = $(this).closest(".communication-td").find(".send-message-textbox");
            let user_id = textBox.attr('data-id');
            let message = textBox.val();
            var feedback_cat_id = $(this).data('feedback_cat_id');
            var $this = $(this);
            if (message == '') {
                return;
            }

            let self = textBox;

            $.ajax({
                url: "{{action([\App\Http\Controllers\WhatsAppController::class, 'sendMessage'], 'user-feedback')}}",
                type: 'POST',
                data: {
                    "feedback_status_id": feedback_status_id,
                    "feedback_cat_id": feedback_cat_id,
                    "user_id": user_id,
                    "message": message,
                    "_token": "{{csrf_token()}}",
                   "status": 2
                },
                dataType: "json",
                success: function (response) {
                    toastr["success"]("Message sent successfully!", "Message");
                    $('#message_list_' + user_id).append('<li>' + response.message.created_at + " : " + response.message.message + '</li>');
                    $(self).removeAttr('disabled');
                    $(self).val('');
                    var msg = response.message.message;
                    if (msg.length > 20) {
                        var msg = msg.substring(1,20)+'...';
                        $this.siblings('.latest_message').text(msg);
                    }else{
                        $this.siblings('.latest_message').text(msg);
                    }
                },
                beforeSend: function () {
                    $(self).attr('disabled', true);
                },
                error: function () {
                    alert('There was an error sending the message...');
                    $(self).removeAttr('disabled', true);
                }
            });
        });

        $(document).on('click', '.send-message-open-hr_ticket', function (event) {
            var feedback_status_id = '1';
            var textBox = $(this).closest(".communication-hr-ticket-td").find(".quick-message-field");
            let user_id = textBox.attr('data-user_id');
            let message = textBox.val();
            var feedback_cat_id = textBox.attr('data-id');
            var $this = $(this);
            if (message == '') {
                return;
            }

            let self = textBox;

            $.ajax({
                url: "{{action([\App\Http\Controllers\WhatsAppController::class, 'sendMessage'], 'user-feedback-hrTicket')}}",
                type: 'POST',
                data: {
                    "feedback_status_id": feedback_status_id,
                    "feedback_cat_id": feedback_cat_id,
                    "task_id" : feedback_cat_id,
                    "user_id": user_id,
                    "message": message,
                    "_token": "{{csrf_token()}}",
                   "status": 2
                },
                dataType: "json",
                success: function (response) {
                    $(self).val('');
                    $(self).attr('disabled', false);
                    toastr["success"]("Message sent successfully!", "Message");
                },
                beforeSend: function () {
                    $(self).attr('disabled', true);
                },
                error: function () {
                    alert('There was an error sending the message...');
                    $(self).removeAttr('disabled', true);
                }
            });
        });

    $(document).on('click', '.expand-row-msg', function () {
        var name = $(this).data('name');
        var id = $(this).data('id');
        var full = '.expand-row-msg .show-short-'+name+'-'+id;
        var mini ='.expand-row-msg .show-full-'+name+'-'+id;
        $(full).toggleClass('hidden');
        $(mini).toggleClass('hidden');
    });

    $(document).on("click", ".delete-category",function(e){
            // $('#btn-save').attr("disabled", "disabled");
            e.preventDefault();
            let _token = $("input[name=_token]").val();
            let category_id =  $(this).data('id');
            if(category_id!=""){
                if(confirm("Are you sure you want to delete record?")) {
                    debugger;
                    $.ajax({
                        url:"{{ route('delete.user.feedback-category') }}",
                        type:"post",
                        data:{
                            id:category_id,
                            _token: _token
                        },
                        cashe:false,
                        success:function(response){
                            if (response.message) {
                                toastr["success"](response.message, "Message");
                                location.reload();
                            }else{
                                toastr.error(response.message);
                            }
                        }
                    });
                } else {

                }
            }else{
                toastr.error("Please realod and try again");
            }
         });
</script>
@endsection 