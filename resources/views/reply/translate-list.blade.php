@extends('layouts.app')
<style>
    .editbtn_model {
        position: unset !important;
    }

    .chat-msg{
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        word-break: break-word;
    }
    .chat-msg:hover {
        white-space: normal;
        overflow: visible;
    }
</style>
@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Quick Replies Translate List</h2>
        
        <div class="col-md-12 ml-sm-6">         
            <div class="col-md-8 ml-sm-6">     
                <form action="{{ route('reply.replyTranslateList') }}" method="get" class="search">
                    <div class="row">
                        <div class="col-md-3 pd-sm">
                            {{ html()->select("store_website_id", ["" => "-- Select Website --"] + $store_websites, request('store_website_id'))->class("form-control") }}
                        </div>
                        <div class="col-md-2 pd-sm">
                            <select name="lang" id="lang" class="form-control globalSelect" data-placeholder="Sort By">
                                <option  Value="">Select lang</option>
                                @foreach ($getLangs as $r)
                                <option  Value="{{$r}}"  {{ (request('lang') == $r ? "selected" : "") }} >{{$r}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 pd-sm">
                            <input type="text" name="keyword" placeholder="keyword" class="form-control" value="{{ request()->get('keyword') }}">
                        </div>

                        <div class="col-md-2 pd-sm">
                            <select name="status" class="form-control" placeholder="Search Status">
                                <option value="">Search Status</option>
                                <option value="approved" @if(request()->get('status')=='approved') selected @endif>Approved</option>
                                <option value="rejected" @if(request()->get('status')=='rejected') selected @endif>Rejected</option>
                                <option value="pending" @if(request()->get('status')=='pending') selected @endif>Pending</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2 pd-sm" style="padding-top: 10px;">
                            <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                                <img src="{{ asset('images/search.png') }}" alt="Search">
                            </button>

                            <a href="{{ route('reply.replyTranslateList') }}" class="btn btn-image" id="">
                                <img src="/images/resend2.png" style="cursor: nwse-resize;">
                            </a>
                        </div>                             
                    </div>
                </form>
            </div>
            <div class="col-md-4 float-right">
                <button class="btn btn-secondary text-white" data-toggle="modal" data-target="#statusColor"> Status Color</button>

                <button class="btn btn-secondary text-white" data-toggle="modal" data-target="#Translatestatus"> Replies Status</button>
                @if(auth()->user()->hasRole(['Lead Translator', 'Admin']))
                    <a class="btn btn-secondary text-white btn_select_user" data-toggle="modal" data-target="#remove_permissions_model">Remove Permission</a>
                @endif
                @if(auth()->user()->hasRole(['Lead Translator', 'Admin']))
                    <a class="btn btn-secondary text-white btn_select_user" data-toggle="modal" data-target="#permissions_model">Permission</a>
                @endif
            </div>  
        </div>
        
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<div class="tab-content ">
    <!-- Pending task div start -->
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;"> 
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;" id="quick-reply-list">
                        <tr>
                            <th class="chat-msg" width="5%">ID</th>
                            <th class="chat-msg" width="10%">Store website</th>
                            <th class="chat-msg" width="10%">Category</th>
                            <th class="chat-msg" width="7%">Translate From</th>
                            <th class="chat-msg" width="10%">Original Reply</th>
                            @foreach ($lang as $r)
                                <th class="chat-msg" width="9%">{{$r}}</th>
                            @endforeach
                            <th class="chat-msg" width="9%">Created On</th>
                            <th class="chat-msg" width="9%">Updated On</th>
                            <th class="chat-msg" width="4%">Action</th>
                        </tr>
                        @foreach (json_decode($replies) as $key => $reply)
                            <tr>
                                <td id="reply_id">{{ $reply->id }}</td>
                                <td class="expand-row table-hover-cell" style="word-break: break-all;" id="reply-store-website">
                                    <div class="td-mini-container">
                                        {!! strlen($reply->website) > 10 ? substr($reply->website, 0, 10).'...' : $reply->website !!}
                                    </div>
                                    <div class="td-full-container hidden">
                                        {{ $reply->website }}
                                    </div>
                                </td>
                                <td>
                                    <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                        <div class="td-mini-container">
                                            {!! strlen($reply->category_name) > 10 ? substr($reply->category_name, 0, 10).'...' : $reply->category_name !!}
                                        </div>
                                        <div class="td-full-container hidden">
                                            {{ $reply->category_name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="expand-row" id="reply_model">{{ $reply->translate_from }}</td>
                                <td style="cursor:pointer;" id="reply_text" class="expand-row change-reply-text" data-id="{{ $reply->id }}" data-message="{{ $reply->original_text }}">
                                    <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                        <div class="td-mini-container">
                                            {!! strlen($reply->original_text) > 20 ? substr($reply->original_text, 0, 20).'...' : $reply->original_text !!}
                                        </div>
                                        <div class="td-full-container hidden">
                                            {{ $reply->original_text }}
                                        </div>
                                    </div>
                                </td>
                                @foreach ($lang as $l)
                                    @php
                                        $text = null;
                                        $re_lang = null;
                                        $id = null;
                                        $status = null;
                                        $status_color = null;
                                        $text_score = null;

                                        if(!empty($reply->transalates->$l->translate_text)){
                                            $text = $reply->transalates->$l->translate_text;
                                        }

                                        if(!empty($reply->transalates->$l->translate_id)){
                                            $id = $reply->transalates->$l->translate_id;
                                        }

                                        if(!empty($reply->transalates->$l->translate_lang)){
                                            $re_lang = $reply->transalates->$l->translate_lang;
                                        }

                                        if(!empty($reply->transalates->$l->translate_status)){
                                            $status = $reply->transalates->$l->translate_status;
                                        }

                                        if(!empty($reply->transalates->$l->translate_status_color)){
                                            $status_color = $reply->transalates->$l->translate_status_color;
                                        }

                                        if(!empty($reply->transalates->$l->translate_text_score)){
                                            $text_score = $reply->transalates->$l->translate_text_score;
                                        }
                                    @endphp
                                    @if($text)
                                        <td style="cursor:pointer; background-color: {{$status_color}}!important;" id="reply_text_translate" data-id="{{$id}}" data-message="{{ $text }}">
                                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                                <div class="td-mini-container">
                                                    {!! strlen($text) > 10 ? substr($text, 0, 10).'..' : $text !!}
                                                    {!! ($text_score) ? "<br><b>Score:</b> ".$text_score : "" !!}
                                                </div>
                                                <div class="td-full-container hidden">
                                                    {{ $text }}
                                                    {!! ($text_score) ? "<br><b>Score:</b> ".$text_score : "" !!}
                                                </div>
                                            </div>
                                            @if(auth()->user()->hasRole(['Lead Translator', 'Admin']))
                                                <a href="#" class="history_model float-right" data-lang="{{$re_lang}}" data-id="{{$id}}" data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>
                                                @if(!empty($status) && $status == "new")
                                                    <div>
                                                        <input type="radio" class="float-left mt-4" data-id="{{$id}}" id="radio1" data-lang="{{$re_lang}}" name="radio1" value="checked">
                                                        <label style="margin-top: 6px; margin-left: 5px;">Accept</label>
                                                        <input type="radio" class="float-left mt-2" data-id="{{$id}}" data-lang="{{$re_lang}}" id="radio2" name="radio1" value="unchecked">
                                                        <label style="margin-top: 12px; margin-left: 5px;">Reject</label>
                                                        <div>
                                                            @endif
                                                            @php
                                                                $check_edit_permission = $check_permission->where('lang_id', $re_lang)->where('action', 'edit')->first();
                                                                $check_view_permission = $check_permission->where('lang_id', $re_lang)->where('action', 'view')->first();
                                                            @endphp
                                                            @if(isset($check_edit_permission))
                                                                <a href="#" class="editbtn_model" data-value="{{$text}}" data-lang="{{$re_lang}}" data-user="{{auth()->user()->id}}" data-id="{{$id}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                            @endif
                                                            @if(isset($check_view_permission))
                                                                <a href="#" class="history_model" data-value="{{$text}}" data-lang="{{$re_lang}}" data-id="{{$id}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                            @endif
                                                            @else
                                                                @php
                                                                    $check_edit_permission = $check_permission->where('lang_id', $re_lang)->where('action', 'edit')->first();
                                                                    $check_view_permission = $check_permission->where('lang_id', $re_lang)->where('action', 'view')->first();
                                                                @endphp
                                                                @if(isset($check_edit_permission))
                                                                    <a href="#" class="editbtn_model" data-value="{{$text}}" data-lang="{{$re_lang}}" data-user="{{auth()->user()->id}}" data-id="{{$id}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                                @endif
                                                                @if(isset($check_view_permission))
                                                                    <a href="#" class="history_model" data-value="{{$text}}" data-lang="{{$re_lang}}" data-id="{{$id}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                @endif
                                            @endif
                                        </td>
                                    @else
                                        <td></td>
                                    @endif
                                @endforeach

                                <td>
                                    <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                        <div class="td-mini-container">
                                            {!! strlen($reply->created_at) > 10 ? substr($reply->created_at, 0, 10).'...' : $reply->created_at !!}
                                        </div>
                                        <div class="td-full-container hidden">
                                            {{ date('Y-m-d H:i',strtotime($reply->created_at)) }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($reply->updated_at != '' && $reply->updated_at != null) {
                                        ?>
                                    <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                        <div class="td-mini-container">
                                            {!! strlen($reply->updated_at) > 10 ? substr($reply->updated_at, 0, 10).'...' : $reply->updated_at !!}
                                        </div>
                                        <div class="td-full-container hidden">
                                            {{ date('Y-m-d H:i',strtotime($reply->updated_at)) }}
                                        </div>
                                    </div>
                                <?php
                                    } else {
                                        echo '-';
                                    } ?>
                                </td>
                                <td>
                                    <button type="button" title="get Translared Text Score" data-id="{{ $key }}" class="btn btn-get-translate-score">
                                        <i class="fa fa-dashboard" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                <!-- Custom pagination -->
                @if ($totalItems > 0)
                    <nav aria-label="Pagination">
                        <ul class="pagination justify-content-center">
                            @if ($currentPage > 1)
                                <li class="page-item">
                                    <a class="page-link" href="?page={{ $currentPage - 1 }}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            @endif

                            @for ($i = 1; $i <= $totalPages; $i++)
                                <li class="page-item {{ ($i == $currentPage) ? 'active' : '' }}">
                                    <a class="page-link" href="?page={{ $i }}">{{ $i }}</a>
                                </li>
                            @endfor

                            @if ($currentPage < $totalPages)
                                <li class="page-item">
                                    <a class="page-link" href="?page={{ $currentPage + 1 }}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </nav>
                @else
                    <p>No data available.</p>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="reply-update-form-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
             <form method="POST" action="{{route('reply.replyUpdate')}}" id="reply-update-form" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Reply</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" name="id" id="reply-update-model-text-id">
                            <textarea id="reply-update-model-text-reply" class="form-control" name="reply"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                   <!-- <button type="submit" id="create-camp-btn" class="btn btn-secondary">Update</button> -->
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="reply_history_modal">
    <div class="modal-dialog" role="document"style="width: 60%; max-width: 100%;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tracked time history</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="reply_history_div">
                        <table class="table" style="table-layout:fixed;">
                            <thead>
                                <tr>
                                    <th width="11%">User Name</th>
                                    <th width="60%">Last Message</th>
                                    <th width="17%">Updated Time</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="reply_logs_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Watson push Logs</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="reply_logs_div">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Request</th>
                                    <th>Response</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="remove_permissions_model" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title position-absolute">Edit Permission</h4>
            </div>
            <div class="modal-body">
                <form action="{{route('remove.permissions')}}" class="permission_form" method="post">
                    @csrf
                    <input type="hidden" class="form-control" name="type" value="remove_permission">
                    <input type="hidden" class="form-control" name="user_permission_id" id="user_permission_id" value="">
                    <div class="form-group">
                        <label>Select User :</label>
                        <select class="form-control" id="selectuserid" name="selectusername">
                            <option>Select</option>
                            @foreach ($active_users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label>View Permission</label>
                        <div class="row">
                            <div class="col-12 d-flex">
                            @foreach ($lang as $reply)
                                <div class="form-group p-3">
                                    <label for="lang_name">{{$reply}}</label>
                                    <input type="checkbox" name="view_lang_name[]" id="view_lang_name_{{$reply}}" value="{{$reply}}" style="height: 13px">
                                </div>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label>Edit Permission</label>
                        <div class="row">
                            <div class="col-12 d-flex">
                                @foreach ($lang as $reply)
                                    <div class="form-group p-3">
                                        <label for="lang_name">{{$reply}}</label>
                                        <input type="checkbox" name="edit_lang_name[]" id="edit_lang_name_{{$reply}}" value="{{$reply}}" style="height: 13px">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="d-none alert alert-class">

                    </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">Remove
                        Permission</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="history_permissions_model" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title position-absolute">History</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered text-wrap w-auto min-w-100" style="overflow-x:auto; display: block">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Keyword</th>
                        @foreach ($lang as $reply)
                            <th width="5%">{{$reply}}</th>
                        @endforeach
                        <th>Updator</th>
                        <th>Approver</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody class="all_data_history">
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="permissions_model" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title position-absolute">Edit Permission</h4>
            </div>
            <div class="modal-body">
                <form class="permission_form">
                    <div class="form-group">
                        <label>Select User :</label>
                        <select class="form-control" id="selectUserId" name="user">
                            <option>Select</option>
                            @foreach ($active_users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Select Lanuage</label>
                        <select class="form-control" name="lang" id="selectLangId">
                            <option>Select</option>
                            @foreach ($lang as $reply)
                                <option value="{{$reply}}">{{$reply}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Select Action</label>
                        <select class="form-control" name="action" id="actionId">
                            <option>Select</option>
                            <option value="view">view</option>
                            <option value="edit">edit</option>
                        </select>
                    </div>
                    <div class="d-none alert alert-class">

                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-submit-form">Add
                    Permission</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="history" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title position-absolute">History</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered text-wrap w-auto min-w-100">
                    <thead>
                    <tr>
                        <th>Id</th>                        
                        <th>Lang</th>
                        <th>Text</th>
                        <th>Status</th>
                        <th>Updator</th>
                        <th>Approver</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody class="data_history">
                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="edit_model" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="form-update" action="{{route('reply.replyTranslateupdate')}}">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title position-absolute">Update Value</h4>
                </div>
                <div class="modal-body edit_model_body">
                    @csrf
                    <input type="text" name="update_record" class="form-control update_record" />
                    <div class="d-none add_hidden_data"></div>


                </div>
                <div class="modal-footer">
                    <input type="submit" value="update" name="update" class="btn btn-secondary" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="Translatestatus" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Replies Status</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="form-group col-md-12">
                <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                    <tr>
                        <th>Language</th>
                        <th>Uncheck</th>
                        <th>New</th>
                        <th>Approve</th>
                        <th>Reject</th>
                    </tr>
                    <?php
                    foreach ($StatusArray as $totalValue) { ?>
                        <tr>
                            <td>{{$totalValue['language']}}</td>
                            <td>{{$totalValue['uncheck']}}</td>
                            <td>{{(!empty($totalValue['new'])) ? $totalValue['new'] :0}}</td>
                            <td>{{(!empty($totalValue['approve'])) ? $totalValue['approve'] :0}}</td>
                            <td>{{(!empty($totalValue['rejected'])) ? $totalValue['rejected'] :0}}</td>
                        </tr>
                    <?php
                    } ?>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="statusColor" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Status Color</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('reply.statuscolor') }}" method="POST">
                <?php echo csrf_field(); ?>
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Status Name</b></td>
                            <td class="text-center"><b>Color Code</b></td>
                            <td class="text-center"><b>Color</b></td>
                        </tr>
                        <?php
                        foreach ($replyTranslatorStatuses as $replyTranslatorStatus) { ?>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;<?php echo $replyTranslatorStatus->name; ?></td>
                            <td class="text-center"><?php echo $replyTranslatorStatus->color; ?></td>
                            <td class="text-center"><input type="color" name="color_name[<?php echo $replyTranslatorStatus->id; ?>]" class="form-control" data-id="<?php echo $replyTranslatorStatus->id; ?>" id="color_name_<?php echo $replyTranslatorStatus->id; ?>" value="<?php echo $replyTranslatorStatus->color; ?>" style="height:30px;padding:0px;"></td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submit-status-color">Save changes</button>
                </div>
            </form>
        </div>

    </div>
</div>

<script type="text/javascript">

$(document).on('click', '.expand-row', function() {
    var selection = window.getSelection();
    if (selection.toString().length === 0) {
        $(this).find('.td-mini-container').toggleClass('hidden');
        $(this).find('.td-full-container').toggleClass('hidden');
    }
});

$(document).on("click",".fa-trash-bin-record",function() {
    var $this = $(this);
    $.ajax({
        url: "{{ url('reply-list/delete') }}",
        type: 'POST',
        data: {
          _token: "{{ csrf_token() }}",
          id: $this.data("id")
        },
        beforeSend: function() {
            $("#loading-image").show();
        }
      }).done( function(response) {
            $("#loading-image").hide();
            if(response.code == 200) {
                toastr["success"](response.message);
                location.reload();
            }else{
               toastr["error"]('Record is unable to delete!');
            }
      }).fail(function(errObj) {
            $("#loading-image").hide();
      });
});

$(document).on("click",".push_to_watson",function() {
    var $this = $(this);
    $.ajax({
        url: "{{ url('push-reply-to-watson') }}",
        type: 'POST',
        data: {
          _token: "{{ csrf_token() }}",
          id: $this.data("id")
        },
        beforeSend: function() {
            $("#loading-image").show();
        }
      }).done( function(response) {
            $("#loading-image").hide();
            if(response.code == 200) {
                toastr["success"](response.message);
                //location.reload();
            }else{
               toastr["error"]('Unable to push!');
            }
      }).fail(function(errObj) {
            $("#loading-image").hide();
      });
});

$(document).on("click",".change-reply-text",function(e) {
    e.preventDefault();
    var $this = $(this);
    $("#reply-update-model-text-id").val($this.data("id"));
    $("#reply-update-model-text-reply").val($this.data("message"));
    $("#reply-update-form-modal").modal("show");
});

$(document).on('click', '.show-reply-history', function() {
    var issueId = $(this).data('id');
    $('#reply_history_div table tbody').html('');
    $.ajax({
        url: "{{ route('reply.replyhistory') }}",
        data: {id: issueId},
        success: function (data) {
            if(data != 'error') {
                $.each(data.histories, function(i, item) {
                    $('#reply_history_div table tbody').append(
                        '<tr>\
                        <td class="Website-task">'+ ((item['name'] != null) ? item['name'] : '') +'</td>\
                        <td class="Website-task">'+ ((item['last_message'] != null) ? item['last_message'] : '') +'</td>\
                        <td>'+ ((item['created_at'] != null) ? item['created_at'] : '') +'</td>\
                        </tr>'
                        );
                });
            }
        }
    });
    $('#reply_history_modal').modal('show');
});

$(document).on('click', '.show_logs', function() {
    var issueId = $(this).data('id');
    $('#reply_logs_div table tbody').html('');
    $.ajax({
        url: "{{ route('reply.replylogs') }}",
        data: {id: issueId},
        success: function (data) {
            if(data != 'error') {
                $.each(data.logs, function(i, item) {
                    $('#reply_logs_div table tbody').append(
                        '<tr>\
                        <td>'+ ((item['created_at'] != null) ? item['created_at'] : '') +'</td>\
                        <td>'+ ((item['request'] != null) ? item['request'] : '') +'</td>\
                        <td>'+ ((item['response'] != null) ? item['response'] : '') +'</td>\
                        </tr>'
                        );
                });
            }
        }
    });
    $('#reply_logs_modal').modal('show');
});

$(document).on('click', ".editbtn_model", function() {
    setTimeout(function() {
        $("#Show_message_display").modal('hide');
    }, 1);
    var id = $(this).data('id');
    var formValue = $(this).data('value');
    var userId = $(this).data('user');
    var langId = $(this).data('lang');
    $(".update_record").val(formValue);
    let html = `<input type="hidden" name="update_by_user_id" value='`+userId+`'>
        <input type="hidden" name="lang_id" value='`+langId+`'>
        <input type="hidden" name="record_id" value='`+id+`'>`;
    $(".add_hidden_data").html(html);

});

$(document).on('click','.history_model',function(){
    setTimeout(function() {
        $("#Show_message_display").modal('hide');
    }, 0.1);
    var id = $(this).data('id');
    var key = $(this).data('key');
    var language = $(this).data('lang');

    $.ajax({
        url:"{{ route('reply.replyTranslatehistory') }}",
        method:'POST',
        data:{'id':id,"key":key,"language":language,'_token':"{{csrf_token()}}"},
        success:function(response){
            $("#Show_message_display").modal('hide');
            $(".data_history").html('');
            if(response.data.length == 0){
                $(".data_history").html('<tr colspan="12"><td class="text-center">No Data Found</td></tr>');
            }else{
                $(".data_history").html(response.data);
            }
        }
    })
});

$(document).on('click','.btn_history_permissions',function(){
    setTimeout(function() {
        $("#Show_message_display").modal('hide');
    }, 0.1);
    var id = $(this).data('id');
    var key = $(this).data('key');
    var language = $(this).data('lang');

    $.ajax({
        url:"{{ route('reply.replyTranslatehistory') }}",
        method:'POST',
        data:{'type':'all_view','_token':"{{csrf_token()}}"},
        success:function(response){
            $("#Show_message_display").modal('hide');
            $(".all_data_history").html('');
            if(response.data.length == 0){
                $(".all_data_history").html('<tr colspan="12"><td class="text-center">No Data Found</td></tr>');
            }else{
                $(".all_data_history").html(response.data);
            }
        }
    })
});

$(document).on('click', ".btn-submit-form", function() {
    var userId = $("#selectUserId").val();
    var langId = $("#selectLangId").val();
    var actionId = $("#actionId").val();
    $(".alert-class").text('');
    $.ajax({
        url:'{{route('reply.permissions')}}',
        method:'POST',
        data:{'user_id':userId,'lang_id':langId,'action':actionId,'_token':"{{csrf_token()}}"},
        success:function(response){
            if(response.status === 200){
                $(".alert-class").text("Successfully added");
                $(".alert-class").addClass("alert-success");
                $(".alert-class").removeClass("alert-danger");
                $(".alert-class").removeClass("d-none");
                window.location.reload();
            }else{
                $("#selectUserId").val('');
                $("#selectLangId").val('');
                $("#actionId").val('');
                $(".alert-class").removeClass("alert-success");
                $(".alert-class").text("Permission already exist");
                $(".alert-class").removeClass("d-none");
                $(".alert-class").addClass("alert-danger");
            }
        }
    })
});

$(document).on("change",'input:radio[name="radio1"]',function(){
    var id,language,status;
    if($(this).val() == 'checked'){
        id = $(this).data('id');
        language = $(this).data('lang');
        status = "approved";
    }else if($(this).val() == 'unchecked'){
        id = $(this).data('id');
        language = $(this).data('lang');
        status = "rejected";
    }
    $.ajax({
        url:'{{route('reply.approved_by_admin')}}',
        method:"POST",
        data:{"id":id,"lang":language,"status":status,"_token":"{{csrf_token()}}"},
        success:function(response){
            if (response.status == 200)
            {
                toastr["success"]('Data Updated Successully');
                location.reload();
            }else {
                toastr["error"]('Unable to update this record!');
            }
        }
    })
});

$('#selectuserid').on("change", function(){
    var user_id = $('#selectuserid').val();
    $('#user_permission_id').val(user_id);

    $.ajax({
        url:'{{route('remove.permissions')}}',
        method:"POST",
        data:{"id":user_id,"_token":"{{csrf_token()}}"},
        success:function(response){
            if (response.status == 200)
            {
                $.each(response.edit_lang, function( index, value ) {
                    $('#edit_lang_name_'+value).attr('checked', true);
                });

                $.each(response.view_lang, function( index, value ) {
                    $('#view_lang_name_'+value).attr('checked', true);
                });
                toastr["success"]('Data Updated Successully');
            }else {
                toastr["error"]('Unable to update this record!');
            }
        }
    })
});

$(document).on('click', '.btn-get-translate-score', function() {
    var id = $(this).data('id');
    var obj = $(this);
    if (id != "") {
        $.ajax({
            url: "/reply-translate-list/get-translated-text-score",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                replies_id: id
            },
            beforeSend: function() {
                $("#loading-image").show();
                obj.prop('disabled', true);
            }
        }).done( function(response) {
            $("#loading-image").hide();
            obj.prop('disabled', false);
            if (response.code == 200) {
                toastr["success"](response.message);
                location.reload();
            } else {
               toastr["error"]('Something went wrong');
            }
        }).fail(function(errObj) {
            $("#loading-image").hide();
            obj.prop('disabled', false);
            toastr["error"]('Something went wrong');
        });
    }
});


function updateTranslateReply(ele) {
    let btn = jQuery(ele);
    let reply_id = btn.data('reply_id');
    let is_flagged = btn.data('is_flagged');
    
    //alert(jQuery(ele).is(':checked'));
    
    //alert(is_flagged)

    if (confirm(btn.data('is_flagged') == 1 ? 'Are you sure? Do you want unflagged this ?' : 'Are you sure want flagged this ?')) {
        jQuery.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('reply.replytranslate') }}",
            type: 'POST',
            data: {
                reply_id: reply_id,
                is_flagged: is_flagged,
            },
            dataType: 'json',
            beforeSend: function () {
                jQuery("#loading-image").show();
            },
            success: function (res) {
                toastr["success"](res.message);
                jQuery("#loading-image").hide();
                btn.find('.fa').removeClass('fa-toggle-on fa-toggle-off');
                if (is_task_planned == 1) {
                    btn.find('.fa').addClass('fa-toggle-off');
                }
                else {
                    btn.find('.fa').addClass('fa-toggle-on');
                }
                btn.data('is_task_planned', is_task_planned == 1 ? 0 : 1);
            },
            error: function (res) {
                if (res.responseJSON != undefined) {
                    toastr["error"](res.responseJSON.message);
                }
                jQuery("#loading-image").hide();
            }
        });
    }
}
</script>
@endsection

