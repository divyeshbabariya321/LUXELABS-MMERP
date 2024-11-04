@extends('layouts.app')

@section('styles')
    <!-- START - Purpose : Add CSS - DEVTASK-4416 -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style type="text/css">

        .select2-selection__rendered{
            width: 460px !important;
        }
        table tr td {
            overflow-wrap: break-word;
        }

        .page-note {
            font-size: 14px;
        }

        .flex {
            display: flex;
        }
        .btn-secondary1{
            background: #fff !important;
            border: 1px solid #ddd !important;
            color: #757575 !important;
            padding: 8px 5px 8px 10px;
        }
        .space-right{
        padding-right:10px;
        padding-left: 10px;
    }
tr#sid1 span.select2.select2-container.select2-container--default {
width: 100% !important;
float: left;
}
.select_table {
width: 100%;
float: left;
}
.select_table .w-25 {
width: 40% !important;
float: left;
}
.select_table .w-50-25-main {
width: 100%;
float: left;
display: -ms-flexbox;
    display: -webkit-flex;
    display: flex;
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
}
.select_table .w-50.pull-left {
width: 40% !important;
float: left;
padding:0 0 0 6px;
}
.select_table .select_table .w-25 {
width: 50% !important;
float: left;
}
.select_table .w-25.pull-left.pull_button {
    width: 20% !important;
    float: left;
    text-align:center;
}
.select_table .select2-selection__rendered{
    width:auto !important;
}
.pull-left.pull_button_inner {
    width: auto;
   float: none !important;
    padding: 0 10px;
    display: inline-block;
}
.select_table .w-50.pull-left .form-control {
    height: 32px;
    resize:none;
    overflow: hidden;
}
#Sop-User-Permission-Modal .select2-container{
    min-width: 100%;
    width: 100%;
}
.select2-selection--multiple{
    overflow: hidden !important;
    height: auto !important;
}

    </style>
    <!-- END - DEVTASK-4416 -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
@endsection
@section('content')


    <div class="row" style="margin:0%">
        <div class="col-md-12 margin-tb p-0">
            <h2 class="page-heading"> ListingApproved - SOP ({{$usersop->count()}})

                <div class="pull-right">
                    <button class="btn btn-Secondary1" data-toggle="modal" data-target="#Sop-Permission-Modal">Sop Permissions</button>
                </div>
                <div class="pull-right">
                    <button type="button" class="btn btn-secondary1 mr-2 addnotesop" data-toggle="modal" data-target="#exampleModal">Add Notes</button>
                </div>
                <div class="pull-right">
                    <button type="button" class="btn btn-secondary1 mr-2" data-toggle="modal" data-target="#categoryModal">Add / Delete Category</button>
                </div>
                <div class="pull-right">
                    <button type="button" class="btn btn-secondary1 mr-2 send-multi-email-btn" id="send_mail"  data-object="Sop" data-type="multi_user">Send Mail</button>
                </div>
            </h2>
        </div>

        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <div class="form-group" style="margin-bottom: 0px;">
                    <div class="row">
                        <form method="get" action="{{ route('sop.index') }}">
                            <div class="flex">
                                <div class="col" id="search-bar">
                                    <input type="text" value="{{ request('search') ?? '' }}" name="search" class="form-control"
                                        placeholder="Search Keyword Here.." style="margin-left: -5px;">                                    
                                </div>
                                <div class="col" id="category-bar">
                                    <select class="form-control" id="categoryFilter" name="category[]" multiple>
                                        @if(isset($category_result) && $category_result!='')
                                            @foreach($category_result as $category_value)
                                                <option value="{{$category_value->id}}" {{in_array($category_value->id, $request["category"] ?? []) ? "selected" : ""}}>{{$category_value->category_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-xs search-button">
                                    <i class="fa fa-search"></i>
                                </button>

                                <a href="{{ route('sop.store') }}" type="button" class="btn btn-xs mt-3 ml-2" id="">
                                    <i class="fa fa-undo"></i>
                                </a>


                            </div>
                        </form>
                    </div>

                </div>
            </div>


        </div>
    </div>

    @include('partials.flash_messages',['extraDiv' => true, 'colMargin' => true])

    <!-- Sop Perission Modal -->
    <div id="Sop-Permission-Modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Sop Permissions</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form class="sop-data-permission-form" id="sopDataPermissionForm">
                <input type="text" name="user_id" hidden>
                <div class="modal-body">
                    <select class="form-control select2-for-user">
                        <option value="">Select User</option>
                        @foreach ($users as $user)
                            @if (!$user->isAdmin())
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    <div class="pt-3">
                        <table class="table table-bordered sop-data-table" style="display:none">
                            <thead>
                                <tr>
                                    <th width="7%"></th>
                                    <th width="93%">Sop</th>
                                </tr>
                            </thead>
                            <tbody>  
                                @foreach ($usersop as $sop)
                                    <tr>
                                        <td><input type="checkbox" name="sop[]" value="{{ $sop->id }}"></td>
                                        <td>{{ $sop->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default sop-data-save" style="display:none">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>

        </div>
    </div>

    <!-- Sop-User Perission Modal -->
    <div id="Sop-User-Permission-Modal" class="modal fade" role="dialog">
        <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Sop Users Permissions</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <select class="sop-permission-user" name="states[]" multiple="multiple">
                    
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default sop-permission-submit">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

        </div>
    </div>

    <!-- Button trigger modal -->

    <!--------------------------------------------------- Add Data Modal ------------------------------------------------------->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Data</h5>
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
                            <label for="category">Category</label>
                            
                            <select class="form-control" id="categorySelect" name="category[]" multiple>
                                @if(isset($category_result) && $category_result!='')
                                @foreach($category_result as $category_value)
                                <option value="{{$category_value->id}}">{{$category_value->category_name}}</option>
                                @endforeach
                                @endif
                            </select>
                            
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
    <!-- category add modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add / Delete Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="FormCategoryModal">
                        @csrf
                        <div class="form-group">
                            <label for="name">Category Name</label>
                            <input type="text" class="form-control" id="name" name="name" required />
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary btnsave" id="btnsave">Submit</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                    <div>
                        <label for=""><strong>Categories</strong></label>
                        <table class="table">
                            <thead>
                                <tr>
                                    <td>#</td>
                                    <td>Name</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($category_result as $category_value)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$category_value->category_name}}</td>
                                        <td>
                                            <a href="javascript:void(0)" class="edit_sop_category btn btn-xs p-2" data-id="{{$category_value->id}}" data-category="{{$category_value->category_name}}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form style="display: inline-block" method="POST" action="{{route('sop.category.delete')}}">
                                                @csrf
                                                @method('delete')
                                                <input type="hidden" name="id" value="{{$category_value->id}}" />
                                                <button class="btn btn-image deleteSopCategories p-2 text-secondary" data-id="{{$category_value->id}}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="" method="post" action="{{route('sop.category.update')}}">
                        @csrf
                        <input type="hidden" name="id" id="edit_category_id">
                        <div class="form-group">
                            <label for="name">Category Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required />
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary btnupdate" id="btnupdate">Update</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <!-- categoy add modal end -->
    <!--------------------------------------------------- end Add Data Modal ------------------------------------------------------->

        <div class="col-md-12">
            <div class="table-responsive mt-3">
                <table class="table table-bordered page-notes" style="font-size:13.8px;border:0px !important; table-layout:fixed" id="NameTable">
                    <thead>
                        <tr>
                            <th width="2%"><input type="checkbox" class="check-all" style="height: auto"></th>
                            <th width="2%">ID</th>
                            <th width="13%">Name</th>
                            <th width="10%">Category</th>
                            <th width="10%">Content</th>
                            <th width="12%">Communication</th>
                            <th width="6%">Created at</th>
                            <th width="7%">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($usersop as $key => $value)
                            <tr id="sid{{ $value->id }}" class="parent_tr" data-id="{{ $value->id }}">
                                <td> <input type="checkbox" class="checkbox" name="select_user" data-id="{{ $value->id }}"  data-toemail="@if ($value->user){{$value->user->email}}@endif" value="" style="height:auto;"></td>
                                <td class="sop_table_id">
                                    {{ $value->id }}
                                </td>
                                <td class="expand-row-msg" data-name="name" data-id="{{$value->id}}">
                                    <span class="show-short-name-{{$value->id}}">{{ Str::limit($value->name, 17, '..')}}</span>
                                    <span style="word-break:break-all;" class="show-full-name-{{$value->id}} hidden">{{$value->name}}</span>
                                </td>
                                <td class="expand-row-msg" data-name="category" data-id="{{$value->id}}">
                                    
                                    Old Value: 
                                    <span class="show-short-category-{{$value->id}}">{{ Str::limit($value->category, 17, '..')}}</span>
                                    <span style="word-break:break-all;" class="show-full-category-{{$value->id}} hidden">{{$value->category}}</span>
                                        
                                    {{-- NEW CONCEPT --}}
                                    <select data-row_id="{{$value->id}}" class="form-control sop_list_category_select" name="category[]" multiple>
                                        @if(isset($category_result) && $category_result!='')
                                        @foreach($category_result as $category_value)
                                        <option value="{{$category_value->id}}" {{!empty($value->selected_category_ids) && in_array($category_value->id, $value->selected_category_ids) ? 'selected' : ''}}>{{$category_value->category_name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </td>
                                <td class="expand-row-msg" data-name="content" data-id="{{$value->id}}">
                                    <span class="show-content" id="show_content_{{$value->id}}" data-content="{!! $value->content !!}" data-id="{{$value->id}}">{{ strip_tags(Str::limit($value->content, 50, '..')) }}</span>

                                </td>
                                <td class="table-hover-cell p-1">
                                    <div class="select_table">
                                        <div class="w-50-25-main">
                                            <div class="w-100">
                                                <select name="sop_user_id" class="form-control select2-for-user" id="user_{{$value->id}}">
                                                    <option value="">Select User</option>
                                                    @foreach ($users as $user)
                                                        @if (!$user->isAdmin())
                                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="w-50 pull-left" style="display:none;">
                                                <textarea rows="1" class="form-control" id="messageid_{{ $value->id }}" name="message" placeholder="Message">{!! strip_tags($value->content) !!}</textarea>
                                            </div>
                                            <div class="w-25 pull-left pull_button">
                                                <div class=" pull_button_inner d-flex">
                                                    <button class="btn btn-xs send-message-open pull-left" data-user_id="{{ $value->user_id }}" data-id="{{ $value->id }}">
                                                        <i class="fa fa-paper-plane"></i>
                                                    </button>
                                                     <button type="button"
                                                            class="btn btn-image btn-xs load-communication-modal pull-left"
                                                            data-id="{{$value->user_id}}" title="Load messages"
                                                            data-object="SOP">
                                                            <img src="/images/chat.png" alt="" style="cursor: nwse-resize;">                                                            
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                   </div>
                                </td>
                                <td>{{ date('yy-m-d', strtotime($value->created_at)) }}</td>
                                <td class="p-1">
                                    <a href="javascript:;" data-id="{{ $value->id }}" title="Edit sop" class="editor_edit btn btn-xs p-2" >
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    <a class="btn btn-image deleteRecord p-2 text-secondary" title="Delete sop" data-id="{{ $value->id }}">
                                        <i class="fa fa-trash" ></i>
                                    </a>
                                    <a class="btn btn-xs view_log p-2 text-secondary" title="status-log"
                                        data-name="{{ $value->purchaseProductOrderLogs ? $value->purchaseProductOrderLogs->header_name : '' }}"
                                        data-id="{{ $value->id }}" data-toggle="modal" data-target="#ViewlogModal">
                                        <i class="fa fa-info-circle"></i>
                                    </a>
                                    <a title="Download Invoice" class="btn btn-xs p-2" href="{{ route('sop.download',$value->id) }}">
                                            <i class="fa fa-download downloadpdf"></i>
                                    </a>
                                    <button type="button" class="btn send-email-common-btn p-2" title="send email" data-content="{{$value->content}}" data-toemail="@if ($value->user){{$value->user->email}} @endif" data-object="Sop" data-id="{{$value->user_id}}">
                                        <i class="fa fa-envelope-square"></i>
                                    </button>
                                    <button data-target="#Sop-User-Permission-Modal" data-toggle="modal" class="btn btn-secondaryssss sop-user-list p-2" title="Sop User" data-sop_id="{{ $value->id }}">
                                        <i class="fa fa-user-o"></i>
                                    </button>
                                    <a href="javascript:;" data-id="{{ $value->id }}" data-content="{{$value->content}}" title="copy" class="menu_editor_copy btn btn-xs p-2" >
                                        <i class="fa fa-copy"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $usersop->appends(request()->input())->links() }}
            </div>

        </div>


    {{-- ------------------ View Log ----------------------- --}}

    <div class="modal fade log_modal" id="ViewlogModal" tabindex="-1" role="dialog" aria-labelledby="log_modal"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">History Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive mt-2">
                        <table class="table table-bordered log-table"
                            style="border: 1px solid #ddd !important; color:black;table-layout:fixed">
                            <thead>
                                <tr>
                                    <th width="30%">Created By</th>
                                    <th width="30%">Updated By</th>

                                    <th width="40%">Updated At</th>

                                </tr>
                            </thead>

                            <tbody class="log_data" id="log_data">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>

    {{-- -------------------------- end view log --------------------------- --}}


    {{-- --------------------------------------------- Update Data start----------------------------------------- --}}

    <div id="sopupdate" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Data</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo route('updateName'); ?>" id="sop_edit_form">
                        <input type="text" hidden name="id" id="sop_edit_id">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="hidden" class="form-control sop_old_name" name="sop_old_name" id="sop_old_name"
                                value="">
                            <input type="text" class="form-control sopname" name="name" id="sop_edit_name">
                        </div>
                        <div class="form-group">
                            <label for="name">Category</label>
                            <input type="hidden" class="form-control sop_old_category" name="sop_old_category" id="sop_old_category"
                                value="">                            
                            <select class="form-control" id="sop_edit_category_select" name="category[]" multiple>
                                @if(isset($category_result) && $category_result!='')
                                @foreach($category_result as $category_value)
                                <option value="{{$category_value->id}}">{{$category_value->category_name}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="content">Content</label>
                            <textarea class="form-control sop_edit_class" name="content" id="sop_edit_content2"></textarea>
                        </div>

                        <button type="submit" class="btn btn-secondary ml-3 updatesopnotes">Update</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- -------------------------- end Update Data start-------------------------- --}}


    <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                    <input type="text" name="search_chat_pop"  class="form-control search_chat_pop" placeholder="Search Message" style="width: 200px;">
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
{{-- Content POP-up --}}
<div id="logMessageModel" class="modal fade" role="dialog">
     <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                  <h4 class="modal-title">Content description</h4>
            </div>
            <div class="modal-body">
               <p style="word-break: break-word;" ></p>
            </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
             </div>
         </div>
    </div>
</div>
{{-- End Content POP-up --}}


 <!-- Send Email Modal-->
<div id="commonEmailModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Send Email</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="" method="POST" enctype="multipart/form-data" id="resetdata">

                <input type="hidden" name="id" id="id">
                <input type="hidden" name="datatype" id="datatype">
                <input type="hidden" name="object" id="object">
                <input type="hidden" name="action" class="action" value="{{route('common.getmailtemplate')}}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Send To</strong>
                        <input type="text" name="sendto" class="form-control sendto" id="sendto">
                    </div>

                    <div class="form-group">
                        <strong>From Mail</strong>
                        <select class="form-control" name="from_mail" id="from_mail">
                          <?php $emailAddressArr = getEmailAddress(); ?>
                          @foreach ($emailAddressArr as $emailAddress)
                            <option value="{{ $emailAddress->from_address }}">{{ $emailAddress->from_name }} - {{ $emailAddress->from_address }} </option>
                          @endforeach
                        </select>
                    </div>

                    <div class="form-group text-right">
                        <a class="add-cc mr-3" href="#">Cc</a>
                        <a class="add-bcc" href="#">Bcc</a>
                    </div>

                    <div id="cc-label" class="form-group" style="display:none;">
                        <strong class="mr-3">Cc</strong>
                        <a href="#" class="add-cc">+</a>
                    </div>

                    <div id="cc-list" class="form-group">

                    </div>

                    <div id="bcc-label" class="form-group" style="display:none;">
                        <strong class="mr-3">Bcc</strong>
                        <a href="#" class="add-bcc">+</a>
                    </div>

                    <div id="bcc-list" class="form-group">

                    </div>

                    <div class="form-group">
                        <strong>Subject *</strong>
                        <input type="text" class="form-control subject" name="subject" id="subject" required>
                        <span class="error" id="subject-error" for="subject" style="color:red;display:none;font-size: 10px;">This field is required</span>
                    </div>

                    <div class="form-group">
                        <strong>Message *</strong>
                        <textarea class="form-control mail_message" name="message"  id="message_sop" rows="8" cols="80" required></textarea>
                        {{-- message --}}
                        <span class="error" id="message-error" for="message" style="color:red;display:none;font-size: 10px;">This field is required</span>
                    </div>

                    <div class="form-group">
                        <strong>Files</strong>
                        <input type="file" name="file[]" value="" multiple>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-secondary sop-mail-send">Send</button>
                </div>
            </form>
        </div>

    </div>
</div>
 <!-- End Send Email Modal-->

 <!-- Show Content Model -->

    <div class="modal fade" id="showContentModal"  role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="content_message_id" id="content_message_id">
                    <div class="modal-header">
                        <h3>Content</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="content_message" name="content_message"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--End Show Content Model -->

    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('{{asset('/images/pre-loader.gif')}}')
                                                                  50% 50% no-repeat;display:none;">


@endsection


@section('scripts')
<script src="https://phpcoder.tech/multiselect/js/jquery.multiselect.js"></script>


    <script src="https://cdn.ckeditor.com/4.11.4/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('content');
        CKEDITOR.replace('sop_edit_content2');
        CKEDITOR.replace('message_sop');
    </script>
    <script>
        
        // jQuery('#categorySelect').multiselect({
        //     columns: 1,
        //     placeholder: 'Select Category',
        //     search: true,
        //     refresh: true
        // });
        $('#categorySelect, #sop_edit_category_select').select2({
            width: "100%",
            multiple: true,
            placeholder: "Select sop category"
        });
        $('#categoryFilter').select2({
            multiple: true,
            placeholder: "Select sop category"
        });
        $('.sop_list_category_select').select2({
            width: "100%",
            multiple: true,
            placeholder: "Select sop category"
        });
        $('.sop_list_category_select').on('select2:selecting', function(e) {
            var rowId = $(this).data("row_id");
            var updateCategoryId = e.params.args.data.id;
            $.ajax({
                url: "{{ route('sop.update-sop-category') }}",
                method: 'POST',
                data: {
                    "id": rowId,
                    "updateCategoryId": updateCategoryId,
                    "type": "attach",
                    "_token": "{{csrf_token()}}",
                },
                dataType: "json",
                success: function(response) {
                    // Handle the AJAX response
                    toastr["success"]("Category updated successfully", "Message");
                    // console.log('Selection AJAX success:', response);
                },
                error: function(xhr, status, error) {
                    // Handle AJAX errors
                    toastr["error"]("Category update failed", "Message");
                    // console.error('Selection AJAX error:', error);
                }
            });
        });

        $('.sop_list_category_select').on('select2:unselecting', function(e) {
            var rowId = $(this).data("row_id");
            var updateCategoryId = e.params.args.data.id;
            $.ajax({
                url: "{{ route('sop.update-sop-category') }}",
                method: 'POST',
                data: {
                    "id": rowId,
                    "updateCategoryId": updateCategoryId,
                    "type": "detach",
                    "_token": "{{csrf_token()}}",
                },
                dataType: "json",
                success: function(response) {
                    // Handle the AJAX response
                    console.log('Selection AJAX success:', response);
                    toastr["success"]("Category updated successfully", "Message");
                },
                error: function(xhr, status, error) {
                    // Handle AJAX errors
                    console.error('Selection AJAX error:', error);
                    toastr["error"]("Category update failed", "Message");
                }
            });
        });
    </script>
    <script>
        // cc

        $(document).on('click', '.add-cc', function (e) {
            e.preventDefault();

            if ($('#cc-label').is(':hidden')) {
                $('#cc-label').fadeIn();
            }

            var el = `<div class="row cc-input">
            <div class="col-md-10">
                <input type="text" name="cc[]" class="form-control mb-3">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-image cc-delete-button"><img src="/images/delete.png"></button>
            </div>
        </div>`;

            $('#cc-list').append(el);
        });

        $(document).on('click', '.cc-delete-button', function (e) {
            e.preventDefault();
            var parent = $(this).parent().parent();

            parent.hide(300, function () {
                parent.remove();
                var n = 0;

                $('.cc-input').each(function () {
                    n++;
                });

                if (n == 0) {
                    $('#cc-label').fadeOut();
                }
            });
        });

        // bcc

        $(document).on('click', '.add-bcc', function (e) {
            e.preventDefault();

            if ($('#bcc-label').is(':hidden')) {
                $('#bcc-label').fadeIn();
            }

            var el = `<div class="row bcc-input">
            <div class="col-md-10">
                <input type="text" name="bcc[]" class="form-control mb-3">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-image bcc-delete-button"><img src="/images/delete.png"></button>
            </div>
        </div>`;

            $('#bcc-list').append(el);
        });

        $(document).on('click', '.bcc-delete-button', function (e) {
            e.preventDefault();
            var parent = $(this).parent().parent();

            parent.hide(300, function () {
                parent.remove();
                var n = 0;

                $('.bcc-input').each(function () {
                    n++;
                });

                if (n == 0) {
                    $('#bcc-label').fadeOut();
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

    $(document).on('click','.send-email-common-btn',function(e){
        e.preventDefault();
        document.getElementById("message").innerHTML = '';
        var ele = $(this).parentsUntil('form').parent();
        var mailtype = $(this).data('object');
        var id = $(this).data('id');
        var content =$(this).data('content');
        var toemail = $(this).data('toemail');
        $('#commonEmailModal').find('form').find('input[name="id"]').val(id);
        $('#commonEmailModal').find('form').find('input[name="sendto"]').val(toemail);
        $('#commonEmailModal').find('form').find('input[name="object"]').val(mailtype);
        // $('#commonEmailModal').find('form').find('textarea[name="message"]').val(content);
        CKEDITOR.instances['message_sop'].setData(content)

        $('#commonEmailModal').modal("show");
    });

        $(document).on('click','.send-multi-email-btn',function(){
            var id = [];
            var toemail = [];
            $('input[name=select_user]:checked').each(function (i) {
                id[i] = $(this).data('id');
            });

            $('input[name=select_user]:checked').each(function (i) {
                toemail[i] = $(this).data('toemail');
            });

            if (id == ''){
                toastr["error"]("Please Select User!", "Message");
            }else {
                var ele = $(this).parentsUntil('form').parent();
                var mailtype = $(this).data('object');
                var data = $(this).data('type');
                console.log(content)
                $('#commonEmailModal').find('form').find('input[name="id"]').val(id);
                $('#commonEmailModal').find('form').find('input[name="datatype"]').val(data);
                $('#commonEmailModal').find('form').find('input[name="sendto"]').val(toemail);
                $('#commonEmailModal').find('form').find('input[name="object"]').val(mailtype);
                $('#commonEmailModal').modal("show");
            }
        });

        $(document).on('click','.show-content',function(e){
            e.preventDefault();
            document.getElementById("content_message").innerHTML = '';

            var ele = $(this).parentsUntil('form').parent();
            var id = $(this).data('id');
            var content = $('#show_content_'+ id).data('content');
            $('#showContentModal').find('form').find('input[name="content_message_id"]').val(id);
            document.getElementById("content_message").innerHTML = content;
            $('#showContentModal').modal("show");
        });

    $(document).on('keyup','#subject', function() {
        if ($(this).val()) {
            $('#subject-error').hide();
        }
    })
    $(document).on('keyup','#message_sop', function() {
        if ($(this).val()) {
            $('#message-error').hide();
        }
    })

    $(document).on('click','.sop-mail-send',function(e){
        e.preventDefault();

        let id = $("#id").val();
        let datatype = $("#datatype").val();
        let sendto = $("#sendto").val();
        let from_mail = $("#from_mail").val();
        let object = $("#object").val();
        let subject = $("#subject").val();
        // let message = $(".mail_message").val();
        let message = CKEDITOR.instances.message_sop.getData();
        
        $('#message-error, #subject-error').hide();
        if (!message || !subject) {
            if (!message) {
                $('#message-error').show();
            }
            if (!subject) {
                $('#subject-error').show();
            }
            return;
        }

        $.ajax({
            url: "{{ route('common.send.email') }}",
            type: 'POST',
            data: {
                "id": id,
                "datatype": datatype,
                "sendto": sendto,
                "from_mail": from_mail,
                "object": object,
                "subject": subject,
                "message": message,
                "from": 'sop',
                "_token": "{{csrf_token()}}",
            },
            dataType: "json",
                beforeSend: function() {
                    $("#loading-image").show();
                },
            success: function (response) {
                $("#loading-image").hide();
                $("#resetdata")[0].reset();
                $('#commonEmailModal').modal('hide');

                toastr["success"]("Your Mail sent successfully!", "Message");


            },
            error: function (response) {
                $("#loading-image").hide();
                toastr["error"]("There was an error sending the Mail...", "Message");

            }
        });

        });


    $(document).on("click", ".check-all", function() {
        $(".checkbox").trigger("click");
    });

</script>

<script>
var selectedUser = null;
$(document).on('click', '.send-message-open', function (event) {
            var thiss = $(this);
            var $this = $(this);
            var data = new FormData();
            var sop_user_id = $(this).data('user_id');
            var id = $(this).data('id');
            var sop_user_id = $(this).closest(".select_table").find(".select2-for-user").val();
            selectedUser = $(this).closest(".select_table").find(".select2-for-user");
            var message = $(this).parents('td').find("#messageid_"+id).val();
            if(sop_user_id == null || sop_user_id == "" || typeof sop_user_id === 'undefined'){
                toastr["error"]("Select user first", "Message");
                return;
            }
            if (message.length > 0) {

            //  let self = textBox;
            siteLoader(true);
            $.ajax({

                url: "{{route('whatsapp.send', 'SOP-Data')}}",
                type: 'POST',
                data: {
                    "sop_user_id": sop_user_id,
                    "message": message,
                    "_token": "{{csrf_token()}}",
                   "status": 2,

                },
                dataType: "json",
                success: function (response) {
                    siteLoader(false);
                    $this.parents('td').find("#messageid_"+sop_user_id).val('');
                    toastr["success"]("Message sent successfully!", "Message");
                    if(selectedUser.length > 0) {
                        $(selectedUser).val(null).trigger('change')
                    }
                },
                error: function (response) {
                    siteLoader(false);
                    toastr["error"]("There was an error sending the message...", "Message");

                }
            });
        } else {
                alert('Please enter a message first');
            }
        });
    </script>

    <script>
 $(document).on('click', '.expand-row', function () {
    $('#logMessageModel .modal-body p').html($(this).attr('data-subject'));
    $('#logMessageModel').modal("show");
    var selection = window.getSelection();
    // if (selection.toString().length === 0) {

    //     $(this).find('.td-mini-container').toggleClass('hidden');
    //     $(this).find('.td-full-container').toggleClass('hidden');
    // }
});
</script>
    <script>
        $(document).on("click", ".view_log", function(e) {

            var id = $(this).data('id');

            var purchase_order_products_id = $(this).data('data-id');
            var header_name = $(this).attr('data-name');

            $.ajax({
                type: "GET",
                url: "{{ route('sopname.logs') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    purchase_order_products_id: purchase_order_products_id,
                    header_name: header_name,
                },
                dataType: "json",
                success: function(response) {

                    var html_content = ''
                    $.each(response.log_data, function(key, value) {
                        html_content += '<tr>';
                        html_content += '<td>' + value.sop.user.name + '</td>';
                        html_content += '<td>' + value.updated_by.name + '</td>';
                        html_content += '<td>' + value.created_at + '</td>';
                        html_content += '</tr>';
                        console.log(html_content, 132);
                    });

                    $("#log_data").html(html_content);
                    $('#log_modal').modal('show');
                },
                error: function() {
                    toastr['error']('Message not sent successfully!');
                }
            });
        });
    </script>

    <script>


        // category submit form start
        $('#FormCategoryModal').submit(function(e) {
            e.preventDefault();
            
            let category_input = $(this).find("input[name=name]");
            category_name = category_input.val();
            let _token = $(this).find("input[name=_token]").val();
            console.log('category',name);
            $.ajax({
                url: "{{ route('sop.category') }}",
                type: "POST",
                data: {
                    category_name: category_name,
                    _token: _token
                },
                success: function(response) {
                    console.log(response);
                    if(response.success==false){
                        toastr["error"](response.message, "Message");
                        return false;
                    }
                    $("#categoryModal").modal('hide');
                    category_input.val('');
                    var lilength = $('.ms-options ul').find('li').length;
                    $('.ms-options ul').append('<li class=""><label for="ms-opt-1" style="padding-left: 15px;"><input type="checkbox" value="'+response.data.category_name+'" title="testing category" id="ms-opt-'+lilength+'">'+response.data.category_name+'</label></li>');
                    $('#categorySelect').append('<option value="'+response.data.category_name+'">'+response.data.category_name+'</option>');
                    
                    $('#categorySelect').multiselect('rebuild');
                    $('#categorySelect').multiselect('refresh');
                    toastr["success"]("Category Inserted Successfully!", "Message");
                    window.location.reload()
                }
            })
        })

        
        // category submit form end

        $('#FormModal').submit(function(e) {
            e.preventDefault();
            let name = $("#name").val();
            let category = $("#categorySelect").val();
            if(category.length==0){
                toastr["error"]('Select Category', "Message");
                return false;
            }
            let content = CKEDITOR.instances['content'].getData(); //$('#cke_content').html();//$("#content").val();
            if(content==''){
                toastr["error"]('Content not', "Message");
                return false;
            }
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
                        $("#categorySelect").val('');
                        $('.ms-options-wrap button').text('Select Category');
                        var content_class = response.sop.content.length < 270 ? '' : 'expand-row';
                        var content = response.sop.content.length < 270 ? response.sop.content : response.sop.content.substr(0, 270) + '.....';

                        if(response.appendData) {
                            $("#NameTable tbody").prepend(response.appendData);
                        }
                        // $("#NameTable tbody").prepend(`
                        // <tr id="sid`+response.sop.id+`" data-id="`+response.sop.id+`" class="parent_tr">
                        //         <td class="sop_table_id">`+response.sop.id+`</td>
                        //         <td class="expand-row-msg" data-name="name" data-id="`+response.sop.id+`">
                        //             <span class="show-short-name-`+response.sop.id+`">`+response.sop.name.replace(/(.{17})..+/, "$1..")+`</span>
                        //             <span style="word-break:break-all;" class="show-full-name-`+response.sop.id+` hidden">`+response.sop.name+`</span>
                        //         </td>
                        //         <td class="expand-row-msg" data-name="category" data-id="`+response.sop.id+`">
                        //             <span class="show-short-category-`+response.sop.id+`">`+response.sop.category.replace(/(.{17})..+/, "$1..")+`</span>
                        //             <span style="word-break:break-all;" class="show-full-category-`+response.sop.id+` hidden">`+response.sop.category+`</span>
                        //         </td>
                        //         <td class="expand-row-msg" data-name="content" data-id="`+response.sop.id+`">
                        //             <span class="show-short-content-`+response.sop.id+`">`+response.sop.content.replace(/(.{50})..+/, "$1..")+`</span>
                        //             <span style="word-break:break-all;" class="show-full-content-`+response.sop.id+` hidden">`+response.sop.content+`</span>
                        //         </td>
                        //         <td class="table-hover-cell p-1">
                        //             <div>
                        //                 <div class="w-75 pull-left">
                        //                     <textarea rows="1" class="form-control" id="messageid_`+response.sop.user_id+`" name="message" placeholder="Message"></textarea>
                        //                 </div>
                        //                 <div class="w-25 pull-left">
                        //                     <button class="btn btn-xs send-message-open pull-left" data-user_id="`+response.sop.user_id+`">
                        //                         <i class="fa fa-paper-plane"></i>
                        //                     </button>
                        //                      <button type="button"
                        //                             class="btn btn-xs load-communication-modal pull-left"
                        //                             data-id="`+response.sop.user_id+`" title="Load messages"
                        //                             data-object="SOP">
                        //                             <i class="fa fa-comments"></i>
                        //                     </button>
                        //                 </div>
                        //            </div>
                        //         </td>
                        //         <td>`+response.only_date+`</td>
                        //         <td class="p-1">
                        //             <a href="javascript:;" data-id="`+response.sop.id+`" class="editor_edit btn btn-xs p-2" >
                        //                 <i class="fa fa-edit"></i>
                        //             </a>
                        //             <a class="btn btn-image deleteRecord p-2 text-secondary" data-id="`+response.sop.id+`">
                        //                 <i class="fa fa-trash" ></i>
                        //             </a>
                        //             <a class="btn btn-xs view_log p-2 text-secondary" title="status-log"
                        //                 data-name="`+response.params.header_name+`"
                        //                 data-id="`+response.sop.id+`" data-toggle="modal" data-target="#ViewlogModal">
                        //                 <i class="fa fa-info-circle"></i>
                        //             </a>
                        //             <a title="Download Invoice" class="btn btn-xs p-2" href="sop/DownloadData/`+response.sop.id+`">
                        //                     <i class="fa fa-download downloadpdf"></i>
                        //             </a>
                        //             <button type="button" class="btn send-email-common-btn p-2" data-toemail="`+response.user_email[0].email+`" data-object="Sop" data-id="`+response.sop.user_id+`">
                        //                 <i class="fa fa-envelope-square"></i>
                        //             </button>
                        //             <button data-target="#Sop-User-Permission-Modal" data-toggle="modal" class="btn btn-secondaryssss sop-user-list  p-2" title="Sop User" data-sop_id="`+response.sop.user_id+`">
                        //                 <i class="fa fa-user-o"></i>
                        //             </button>
                        //         </td>
                        // </tr>
                        // `);

                        $("#FormModal")[0].reset();
                        $('.cke_editable p').text(' ');
                        CKEDITOR.instances['content'].setData('');
                        $("#exampleModal").modal('hide');
                        toastr["success"]("Data Inserted Successfully!", "Message");
                        $(document).on('click', '.expand-row-msg', function () {
                            var name = $(this).data('name');
                            var id = $(this).data('id');
                            var full = '.expand-row-msg .show-short-'+name+'-'+id;
                            var mini ='.expand-row-msg .show-full-'+name+'-'+id;
                            $(full).toggleClass('hidden');
                            $(mini).toggleClass('hidden');
                        });
                    }
                }

            });
        });
    </script>


    <script>
        $(document).on('click', '.deleteRecord', function() {

            let $this = $(this)
            console.log($this)
            var result = window.confirm('Are You Sure Want To Delete This Records?');
            if (result == true) {
                // alert('Are You Sure Want To Delete This Records?');
                var id = $(this).data("id");
                var token = $("meta[name='csrf-token']").attr("content");

                $.ajax({
                    url: "/sop/" + id,
                    type: 'DELETE',
                    data: {
                        "id": id,
                        "_token": token,
                    },
                    success: function(response) {

                        $this.closest('.parent_tr').remove()
                        toastr["success"](response.message, "Message")

                    }
                });
            }
        });
    </script>

    <script>
        $(document).on('click', '.editor_edit', function() {

            var $this = $(this);

            $.ajax({
                type: "GET",
                data: {
                    id: $this.data("id")

                },
                url: "{{ route('editName') }}"
            }).done(function(data) {

                console.log(data.sopedit);

                $('#sop_edit_form #sop_edit_id').val(data.sopedit.id)
                $('#sop_edit_form #sop_edit_name').val(data.sopedit.name)
                $('#sop_edit_form #sop_edit_category_select').val(data.sopedit.sopCategory),
                $('#sop_edit_form #sop_edit_category_select').trigger("change");
                $('#sop_edit_form #sop_old_name').val(data.sopedit.name)
                $('#sop_edit_form #sop_old_category').val(data.sopedit.category)
                $('#sop_edit_form #sop_edit_content2').val(data.sopedit.content)

                if (!CKEDITOR.instances['sop_edit_content2']) {
                    CKEDITOR.instances['sop_edit_content2'].setData(data.sopedit.content)
                }

                $("#sopupdate #sop_edit_form").attr('data-id', $($this).attr('data-id'));
                $("#sopupdate").modal("show");

            }).fail(function(data) {
                console.log(data);
            });
        });
    </script>

    <script>
        $(document).on('submit', '#sop_edit_form', function(e) {
            e.preventDefault();
            const $this = $(this)
            $(this).attr('data-id', );

            $.ajax({
                type: "POST",
                data: $(this).serialize(),
                url: "{{ route('updateName') }}",
                datatype: "json"
            }).done(function(data) {

                if(data.success==false){
                    toastr["error"](data.message, "Message");
                    return false;
                }
                $("#sopupdate").modal("hide");
                toastr["success"]("Data Updated Successfully!", "Message")
                window.location.reload();

            }).fail(function(data) {
                console.log(data);
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.select2-for-user').select2();
            $('.deleteSopCategories').click(function (e) { 
                if(!confirm("Do you want to delete")) {
                    e.preventDefault();
                }
            });

            $('.edit_sop_category').click(function (e) { 
                let category_id = $(this).data('id');
                let category_name = $(this).data('category');
                $('#edit_name').val(category_name);
                $('#edit_category_id').val(category_id);
                $('#editCategoryModal').modal('show');
            });
            
        });

        $(document).on('change', '.select2-for-user', function() {
            var user_id = $(this).val();
            var $this = $(this);
            $('#sopDataPermissionForm')[0].reset();

            $(this).parents('.sop-data-permission-form').find('input[type="checkbox"]').removeAttr('checked');
            $(this).parents('.sop-data-permission-form').find('input[name="user_id"]').attr('value',user_id);
            if (!user_id) {
                $('.sop-data-table').hide();
                $('.sop-data-save').hide();
                return;
            }
            $.ajax({
                type: "GET",
                url: "{{ route('sop.permission-data') }}",
                data:{user_id:user_id},
                success: function(response){
                    $('.sop-data-table').show();
                    $('.sop-data-save').show();
                    var data = response.permissions;
                    for (let i = 0; i < data.length; i++) {
                        $this.parents('.modal-body').find('input[value="'+data[i].sop_id+'"]').attr('checked','checked');
                    }
                }
            })
        })

        $(document).on('click','.sop-data-save', function(){
            var formdata = $(this).parents('.sop-data-permission-form').serialize();

            $.ajax({
                type: "GET",
                url: "{{ route('sop.permission-list') }}",
                data:formdata,
                success: function(response){
                   toastr.success(response.message);
                }
            })
        })
        $("#Sop-Permission-Modal").on("hidden.bs.modal", function (e) {
            $(".select2-selection__rendered").text('Select User');
            $('.sop-data-table').hide();
            $('#sopDataPermissionForm')[0].reset();
        });

        $("#Sop-User-Permission-Modal").on("hidden.bs.modal", function (e) {
            $(this).find('.sop-permission-user').text('');
        });

        $(document).on('click','.sop-user-list',function(){
            var sop_id = $(this).data('sop_id');
            $('#Sop-User-Permission-Modal').find('.sop-permission-submit').attr('data-sop_id',sop_id);

            $.ajax({
                type: 'get',
                url: "{{ route('sop.permission.user-list') }}",
                data: {sop_id:sop_id},
                success: function(response){

                    $('#Sop-User-Permission-Modal').find('.modal-title').text(response.sop.name+' Permission');

                    for (let j = 0; j < response.user.length; j++) {
                        var html = '<option value="'+response.user[j].id+'">'+response.user[j].name+'</option>';
                            $('#Sop-User-Permission-Modal').find('.sop-permission-user').append(html);
                    }

                    var vals = response.user_list;

                    let s2 = $('.sop-permission-user').select2()

                    // vals.forEach(function(e){
                    //     if(!s2.find('option:contains(' + e + ')').length) {
                    //             console.log('aaaaaa')
                    //     }
                    // });

                    s2.val(vals).trigger("change");
                }
            })
        })

        $(document).on('click','.sop-permission-submit',function(){
            var sop_id = $(this).attr('data-sop_id');
            var user_id = $('#Sop-User-Permission-Modal').find('.sop-permission-user').val();
            $.ajax({
                type: 'get',
                url: "{{ route('sop.remove.permission') }}",
                data: {sop_id:sop_id,user_id:user_id},
                success: function(response){
                    toastr.success(response.message);
                    $('#Sop-User-Permission-Modal').modal('hide');
                }
            })
        })
        $(document).ready(function() {
            $('.sop-permission-user').select2();
        });
    </script>

@endsection
