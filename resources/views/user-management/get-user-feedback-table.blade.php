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

        table select.form-control {
            min-width: 140px;
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <div class="col-md-12 p-0">
        <div class="row" id="common-page-layout">
            <input type="hidden" name="page_no" class="page_no" />
            <div class="col-lg-12 margin-tb">
                <h2 class="page-heading">Users Feedback <button type="button" class="btn btn-secondary btn-xs float-right mr-3" data-toggle="modal" data-target="#status-create">Add Status</button>
                    <button class="btn btn-secondary btn-xs float-right mr-3" data-toggle="modal" data-target="#newStatusColor"> Status Color</button>

                    <button type="button" class="btn btn-secondary btn-xs float-right mr-3" data-toggle="modal" data-target="#feedbackdatatablecolumnvisibilityList">Column Visiblity</button>
                </h2>
                <div class="" style="margin-bottom:10px;">
                    <form class="form-inline message-search-handler" method="get">
                        <div class="col-md-12">
                            <div class="form-group col-md-3 cls_filter_inputbox p-0 mr-2">
                                <select name="term" type="text" class="form-control" placeholder="Search Name" id="vendor-search" data-allow-clear="true">
                                    <?php
                                    if (request()->get('term')) {
                                        echo '<option value="'.request()->get('term').'" selected>'.request()->get('term').'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group pl-3">
                                <label for="button">&nbsp;</label>
                                <button style="display: inline-block;width: 10%;margin-top: -26px; padding-left: 0px"
                                    class="btn btn-sm btn-image btn-search-action">
                                    <img src="{{asset('/images/search.png')}}" style="cursor: default;">
                                </button>
                            </div>

                            @if (Auth::user()->isAdmin())
                            <div class="form-group pl-3">
                                <input type="text" style="width:calc(100% - 41px)" class="quick_feedback" id="addcategory" name="category" placeholder="Create Category">
                                <button style="width: 20px" type="button" class="btn btn-image add-feedback" id="btn-save"><img src="{{asset('/images/add.png')}}" style="cursor: nwse-resize; width: 0px;"></button>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="infinite-scroll" style="overflow-y: auto">
            
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            @if(!empty($dynamicColumnsToShowVendorsFeeback))
                                @if (!in_array('Vendor', $dynamicColumnsToShowVendorsFeeback))
                                    <th width="17%">Vendor</th>
                                @endif

                                @if($category)
                                    @foreach($category as $cat)
                                        @if (!in_array($cat->id, $dynamicColumnsToShowVendorsFeeback))
                                            <th width="15%">{{$cat->category}}</th>
                                        @endif
                                    @endforeach
                                @endif
                            @else
                                <th width="17%">Vendor</th>
                                @foreach ($category as $cat)
                                    <th width="15%">
                                        {{ $cat->category }}

                                        @if (auth()->user()->isAdmin())
                                            <button style="padding-left: 10px;padding-right:0px;margin-top:2px;" type="button" class="btn pt-1 btn-image d-inline delete-category" title="Delete Category with all data" data-id="{{$cat->id}}" ><i class="fa fa-trash"></i></button>
                                        @endif
                                    </th>
                                @endforeach
                            @endif
                        </tr>
                    </thead>
                    <tbody class="infinite-scroll-data">
                    <?php $sopOps = ''; ?>
                    @foreach ($sops as $sop)
                        <?php $sopOps .= '<option value="'.$sop->id.'">'.$sop->name.'</option>' ?>
                    @endforeach
                    @foreach ($vendors as $vendor)
                        @if(!empty($dynamicColumnsToShowVendorsFeeback))
                            <tr>
                                @if (!in_array('ID', $dynamicColumnsToShowVendorsFeeback))
                                    <td>{{$vendor->name}}</td>
                                @endif

                                @foreach ($category as $cat)

                                    @if (!in_array($cat->id, $dynamicColumnsToShowVendorsFeeback))
                                        @php
                                            if($user_id !=''){
                                                $cat->user_id = $user_id;
                                            }
                                            $latest_messages = \App\Helpers\MessageHelper::getLastMessageByUserFeedbakAndCategoryId($cat->user_id, $cat->id); 
                                            if ($latest_messages) {
                                                $latest_msg = $latest_messages->message;
                                                if (strlen($latest_msg) > 20) {
                                                    $latest_msg = substr($latest_messages->message,0,20).'...';
                                                }
                                            }
                                            $feedback_status = \App\Helpers\StatusHelper::getLastUserFeedbackStatusByUserFeedbakAndCategoryId($cat->id, $vendor->id);
                                            $status_id = 0;
                                            $status_color = new stdClass();
                                            if ($feedback_status) {
                                                $status_id = $feedback_status->user_feedback_status_id;

                                                $status_color = \App\Helpers\StatusHelper::getUserFeedbackStatusById($status_id);
                                            }
                                            $latest_comment = getLastUserFeedbackSopHistoryCommentBySopHistoryId($cat->sop_id);
                                            $comment = '';
                                            if(isset($latest_comment->comment))
                                                $comment = $latest_comment->comment.'...';
                                            $commentId = '';
                                            if(isset($latest_comment->comment))
                                                $commentId = $latest_comment->id;
                                        @endphp
                                        <td style="background-color: {{$status_color->status_color ?? ""}}!important;">
                                            @if(\Auth::user()->isAdmin() == true)                                                
                                                <div class="sop-div">
                                                    <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                                        <select class="form-control" data-id="{{$cat->id}}" id="sop_{{$cat->id}}" name="sop_{{$cat->id}}" style="margin-bottom:5px;width:77%;display:inline;">
                                                            <option value="">-Select sop-</option>
                                                            @foreach ($sops as $sop)
                                                                <?php echo '<option value="'.$sop->id.'">'.$sop->name.'</option>'; ?>
                                                            @endforeach
                                                        </select>
                                                        <div style="margin-top: 0px;" class="d-flex p-0">
                                                            <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image user-sop-save" data-sop="sop_{{$cat->id}}" data-feedback_cat_id="{{$cat->id}}" type="submit" id="submit_message"  data-id="{{$cat->id}}" data-vendorid="{{$vendor->id}}" ><img src="{{asset('/images/filled-sent.png')}}"/></button>

                                                            <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image sop-history" data-cat_id="{{$cat->id}}" data-sop_id="{{$cat->sop_id}}" data-vendorid="{{$vendor->id}}" title='history'><i class="fa fa-info-circle" aria-hidden="true"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                {{$cat->sop}}
                                            @endif                        
                                            
                                            <div class="status-div">
                                                <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                                    <select class="form-control" data-id="{{$cat->id}}" id="status_{{$cat->id}}" name="status_{{$cat->id}}" style="margin-bottom:5px;width:77%;display:inline;">
                                                        <option value="">-Select status-</option>
                                                        @foreach ($status as $st)
                                                            <option value="{{$st->id}}">{{ $st->status }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div style="margin-top: 0px;" class="d-flex p-0">
                                                        <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image user-status-save" data-status="status_{{$cat->id}}" data-feedback_cat_id="{{$cat->id}}" type="submit" id="submit_message"  data-id="{{$cat->id}}" data-vendorid="{{$vendor->id}}" ><img src="{{asset('/images/filled-sent.png')}}"/></button>

                                                        <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image status-history" data-cat_id="{{$cat->id}}" data-status_id="{{$cat->status_id}}" data-vendorid="{{$vendor->id}}" title='history'><i class="fa fa-info-circle" aria-hidden="true"></i></button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                                <input style="margin-top: 0px;width:80% !important;" type="text" class="form-control " name="message" placeholder="Remarks" value="" id="remark_{{$cat->id}}_{{$vendor->id}}" data-catid="{{$cat->id}}" data-vendorid="{{$vendor->id}}">
                                                <div style="margin-top: 0px;" class="d-flex p-0">
                                                    <button class="btn pr-0 btn-xs btn-image " onclick="saveRemarks({{$cat->id}}, {{$vendor->id}})"><img src="/images/filled-sent.png"></button>
                                                    <button type="button" data-catid="{{$cat->id}}" data-vendorid="{{$vendor->id}}" class="btn btn-image remarks-history-show p-0 ml-2" title="Status Histories"><i class="fa fa-info-circle"></i></button>
                                                </div>
                                            </div>

                                            <div class="history-div">
                                                <button style="padding-left: 0px;padding-right:0px;margin-top:2px;" type="button" class="btn pt-1 btn-image d-inline hrTicket" data-toggle="modal"  data-feedback_cat_id="{{$cat->id}}" data-vendorid="{{$vendor->id}}" data-id="{{$cat->user_id}}" data-cat_name="{{$cat->category}}" title="Add Ticket" data-target="#hrTicketModal" id="hrTicket"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                                <button style="padding-left: 0px;padding-right:0px;margin-top:2px;" type="button" class="btn pt-1 btn-image d-inline count-dev-customer-tasks" data-vendorid="{{$vendor->id}}" title="Show task history" data-id="{{$cat->id}}" data-user_id="{{$cat->user_id}}"><i class="fa fa-info-circle"></i></button>
                                                
                                            </div>
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                        @else
                            <tr>
                                <td>{{$vendor->name}}</td>

                                @foreach ($category as $cat)
                                    @php
                                        if($user_id !=''){
                                            $cat->user_id = $user_id;
                                        }
                                        $latest_messages = \App\Helpers\MessageHelper::getLastMessageByUserFeedbakAndCategoryId($cat->user_id, $cat->id); 
                                        if ($latest_messages) {
                                            $latest_msg = $latest_messages->message;
                                            if (strlen($latest_msg) > 20) {
                                                $latest_msg = substr($latest_messages->message,0,20).'...';
                                            }
                                        }
                                        
                                        $feedback_status = \App\Helpers\StatusHelper::getLastUserFeedbackStatusByUserFeedbakAndCategoryId($cat->id, $vendor->id);
                                        $status_id = 0;
                                        $status_color = new stdClass();
                                        if ($feedback_status) {
                                            $status_id = $feedback_status->user_feedback_status_id;

                                            $status_color = \App\Helpers\StatusHelper::getUserFeedbackStatusById($status_id);
                                        }
                                        $latest_comment = getLastUserFeedbackSopHistoryCommentBySopHistoryId($cat->sop_id);
                                        $comment = '';
                                        if(isset($latest_comment->comment))
                                            $comment = $latest_comment->comment.'...';
                                        $commentId = '';
                                        if(isset($latest_comment->comment))
                                            $commentId = $latest_comment->id;
                                    @endphp
                                    <td style="background-color: {{$status_color->status_color ?? ""}}!important;">
                                        @if(\Auth::user()->isAdmin() == true)                                                
                                            <div class="sop-div">
                                                <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                                    <select class="form-control" data-id="{{$cat->id}}" id="sop_{{$cat->id}}" name="sop_{{$cat->id}}" style="margin-bottom:5px;width:77%;display:inline;">
                                                        <option value="">-Select sop-</option>
                                                        @foreach ($sops as $sop)
                                                            <?php echo '<option value="'.$sop->id.'">'.$sop->name.'</option>'; ?>
                                                        @endforeach
                                                    </select>
                                                    <div style="margin-top: 0px;" class="d-flex p-0">
                                                        <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image user-sop-save" data-sop="sop_{{$cat->id}}" data-feedback_cat_id="{{$cat->id}}" type="submit" id="submit_message"  data-id="{{$cat->id}}" data-vendorid="{{$vendor->id}}" ><img src="{{asset('/images/filled-sent.png')}}"/></button>

                                                        <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image sop-history" data-cat_id="{{$cat->id}}" data-sop_id="{{$cat->sop_id}}" data-vendorid="{{$vendor->id}}" title='history'><i class="fa fa-info-circle" aria-hidden="true"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            {{$cat->sop}}
                                        @endif                        
                                        
                                        <div class="status-div">
                                            <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                                <select class="form-control" data-id="{{$cat->id}}" id="status_{{$cat->id}}" name="status_{{$cat->id}}" style="margin-bottom:5px;width:77%;display:inline;">
                                                    <option value="">-Select status-</option>
                                                    @foreach ($status as $st)
                                                        <option value="{{$st->id}}">{{ $st->status }}</option>
                                                    @endforeach
                                                </select>
                                                <div style="margin-top: 0px;" class="d-flex p-0">
                                                    <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image user-status-save" data-status="status_{{$cat->id}}" data-feedback_cat_id="{{$cat->id}}" type="submit" id="submit_message"  data-id="{{$cat->id}}" data-vendorid="{{$vendor->id}}" ><img src="{{asset('/images/filled-sent.png')}}"/></button>

                                                    <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image status-history" data-cat_id="{{$cat->id}}" data-status_id="{{$cat->status_id}}" data-vendorid="{{$vendor->id}}" title='history'><i class="fa fa-info-circle" aria-hidden="true"></i></button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                            <input style="margin-top: 0px;width:80% !important;" type="text" class="form-control " name="message" placeholder="Remarks" value="" id="remark_{{$cat->id}}_{{$vendor->id}}" data-catid="{{$cat->id}}" data-vendorid="{{$vendor->id}}">
                                            <div style="margin-top: 0px;" class="d-flex p-0">
                                                <button class="btn pr-0 btn-xs btn-image " onclick="saveRemarks({{$cat->id}}, {{$vendor->id}})"><img src="/images/filled-sent.png"></button>
                                                <button type="button" data-catid="{{$cat->id}}" data-vendorid="{{$vendor->id}}" class="btn btn-image remarks-history-show p-0 ml-2" title="Status Histories"><i class="fa fa-info-circle"></i></button>
                                            </div>
                                        </div>

                                        <div class="history-div">
                                            <button style="padding-left: 0px;padding-right:0px;margin-top:2px;" type="button" class="btn pt-1 btn-image d-inline hrTicket" data-toggle="modal"  data-feedback_cat_id="{{$cat->id}}" data-vendorid="{{$vendor->id}}" data-id="{{$cat->user_id}}" data-cat_name="{{$cat->category}}" title="Add Ticket" data-target="#hrTicketModal" id="hrTicket"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                            <button style="padding-left: 0px;padding-right:0px;margin-top:2px;" type="button" class="btn pt-1 btn-image d-inline count-dev-customer-tasks" data-vendorid="{{$vendor->id}}" title="Show task history" data-id="{{$cat->id}}" data-user_id="{{$cat->user_id}}"><i class="fa fa-info-circle"></i></button>
                                            
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        
    </div>
</div>
<div id="newStatusColor" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Status Color</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('feedback.statuscolor') }}" method="POST">
                <?php echo csrf_field(); ?>                
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Status Name</b></td>
                            <td class="text-center"><b>Color Code</b></td>
                            <td class="text-center"><b>Color</b></td>
                        </tr>
                        <?php
                        foreach ($status as $status_data) { ?>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;<?php echo $status_data->status; ?></td>
                            <td style="text-align:center;"><?php echo $status_data->status_color; ?></td>
                            <td style="text-align:center;"><input type="color" name="color_name[<?php echo $status_data->id; ?>]" class="form-control" data-id="<?php echo $status_data->id; ?>" id="color_name_<?php echo $status_data->id; ?>" value="<?php echo $status_data->status_color; ?>" style="height:30px;padding:0px;"></td>                              
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

<div id="status-create" class="modal fade in" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
      <h4 class="modal-title">Add Stauts</h4>
      <button type="button" class="close" data-dismiss="modal">×</button>
      </div>
      <form  method="POST" id="status-create-form">
        @csrf
        @method('POST')
          <div class="modal-body">
            <div class="form-group">
              {{ html()->label('Name', 'status_name')->class('form-control-label') }}
              {{ html()->text('status_name')->class('form-control')->required()->attribute('rows', 3) }}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary status-save-btn">Save</button>
          </div>
        </div>
      </form>
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
                        <input class="form-control" type="hidden" id="hidden-vendor_id" name="user_feedback_vendor_id"  />
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
                        <select style="width:100%" class="form-control 	" id="repository_id" name="repository_id">
                            <option value="">-- select repository --</option>
                            @foreach ($gitRepository as $repository)
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
 <div id="feedback-remarks-histories-list" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Remarks Histories</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="30%">Remarks</th>
                                    <th width="20%">Updated BY</th>
                                    <th width="30%">Created Date</th>
                                </tr>
                            </thead>
                            <tbody class="feedback-remarks-histories-list-view">
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
<div id="status-history" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm" style="max-width: 600px; width:600px;">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Status history</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Status Name</th>
                                </tr>
                            </thead>
                            <tbody class="show-status-history-records">
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
@include("vendors.partials.column-visibility-modal-feedback")
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
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
        var vendor_id = $(this).data("vendorid");
        var cat_name = $(this).data("cat_name");
        $("#user_feedback_cat_id").val(feedback_cat_id);
        let selecUserVal = $('.select-multiple').val();
        $("#task_asssigned_to").val(selecUserVal);
        $("#hidden-task-subject").val(cat_name);
        $("#hidden-vendor_id").val(vendor_id);
        
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
    var vendor_id = $(this).data("vendorid");
    var isAvaible = getUrlParameter('user_id');
    var is_set = "";
    if(isAvaible)
        is_set = $(this).data("user_id");
    else
        is_set = "";
    var user_id = $(this).data("user_id");
    var url = "{{route('hr-ticket.countdevtask',[':user_feedback',':user_id',':vendor_id'])}}";
    var url1 = url.replace(':user_feedback',user_feedback);
    var url2 = url1.replace(':user_id',user_id);
    var url3 = url2.replace(':vendor_id',vendor_id);

	$.ajax({
		type: 'get',
		url: url3,
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
                    toastr["success"]('Caetgory successfully added.');
                    location.reload();
                }
            });
        }else{
           alert("error");
        }
    });

         
    $(document).on('click','.sop-history',function(){
        var id = $(this).data('id');
        var catId = $(this).data("cat_id");
        var vendor_id = $(this).data('vendorid');
        $.ajax({
            type: "get",
            url: '{{ route("user.get.sop.data") }}',
            data: {
                    'cat_id': catId,
                    'vendor_id': vendor_id,
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

    $(document).on('click','.status-history',function(){
        var id = $(this).data('id');
        var catId = $(this).data("cat_id");
        var vendor_id = $(this).data('vendorid');
        $.ajax({
            type: "get",
            url: '{{ route("user.get.status.data") }}',
            data: {
                    'cat_id': catId,
                    'vendor_id': vendor_id,
                    },
            success:function(response){
                if (response.code == 200) {
                    toastr["success"](response.message);
                    $("#status-history").find(".show-status-history-records").html(response.data);
                    $("#status-history").modal("show");
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

    $(document).on('click','.user-sop-save',function(){
        var id = $(this).data('id');
        var vendor_id = $(this).data('vendorid');
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
                    'sop_text': sop,
                    'vendor_id': vendor_id
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

    $(document).on('click','.user-status-save',function(){
        var id = $(this).data('id');
        var vendor_id = $(this).data('vendorid');
        var sop_id = $(this).data('status');
        var status_id = $("#"+sop_id).val();
        if(status_id == ''){
            toastr["error"]('Please Select Status');
            return false
        }
        var cat = $(this).data("feedback_cat_id");

        $.ajax({
            type: "get",
            url: '{{ route("user.save.status") }}',
            data: {
                'cat_id': cat,
                'status_id': status_id,
                'vendor_id': vendor_id
            },
            success:function(response){
                if (response.code == 200) {
                    toastr["success"](response.message);
                } else {
                    toastr["error"](response.message);
                }
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

    $(document).on("click", ".status-save-btn", function(e) {
    e.preventDefault();
    var $this = $(this);
    $.ajax({
      url: "{{route('feedback.status.create')}}",
      type: "post",
      data: $('#status-create-form').serialize()
    }).done(function(response) {
      if (response.code = '200') {
        $('#loading-image').hide();
        toastr['success']('Status  Created successfully!!!', 'success');
        location.reload();
      } else {
        toastr['error'](response.message, 'error');
      }
    }).fail(function(errObj) {
      $('#loading-image').hide();
      toastr['error'](errObj.message, 'error');
    });
  });

    function saveRemarks(user_feedback_category_id, user_feedback_vendor_id){

        var remarks = $("#remark_"+user_feedback_category_id+"_"+user_feedback_vendor_id).val();

        if(remarks==''){
            alert('Please enter remarks.');
            return false;
        }

        $.ajax({
            url: "{{route('feedback.saveremarks')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'user_feedback_category_id' :user_feedback_category_id,
                'user_feedback_vendor_id' :user_feedback_vendor_id,
                'remarks' :remarks,
            },
            beforeSend: function() {
                $(this).text('Loading...');
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                toastr['success']('Remarks successfully added.');
            }
        }).fail(function(response) {
            $("#loading-image").hide();
            toastr['error'](response.responseJSON.message);
        });
    }

    $(document).on('click', '.remarks-history-show', function() {
        var user_feedback_category_id = $(this).attr('data-catid');
        var user_feedback_vendor_id = $(this).attr('data-vendorid');
        $.ajax({
            url: "{{route('feedback.getremarks')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'user_feedback_category_id' :user_feedback_category_id,
                'user_feedback_vendor_id' :user_feedback_vendor_id,
            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${(v.remarks != null) ? v.remarks : ' - ' } </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#feedback-remarks-histories-list").find(".feedback-remarks-histories-list-view").html(html);
                    $("#feedback-remarks-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    $('#vendor-search').select2({
        tags: true,
        width: '100%',
        ajax: {
            url: BASE_URL + '/vendor-search',
            dataType: 'json',
            delay: 750,
            data: function(params) {
                return {
                    q: params.term, // search term
                };
            },
            processResults: function(data, params) {
                for (var i in data) {
                    data[i].id = data[i].name ? data[i].name : data[i].text;
                }
                params.page = params.page || 1;

                return {
                    results: data,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
        },
        placeholder: 'Search Vendor',
        escapeMarkup: function(markup) {
            return markup;
        },
        minimumInputLength: 1,
        templateResult: function(customer) {

            if (customer.name) {
                //return "<p> <b>Id:</b> " + customer.id + (customer.name ? " <b>Name:</b> " + customer.name : "") + (customer.phone ? " <b>Phone:</b> " + customer.phone : "") + "</p>";
                return "<p>" + customer.name + "</p>";
            }
        },
        templateSelection: (customer) => customer.text || customer.name,

    });
</script>
@endsection 