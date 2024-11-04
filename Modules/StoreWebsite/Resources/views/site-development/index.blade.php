@extends('layouts.app')
@section('favicon', 'task.png')

@section('title', 'Site Development')

@section('styles')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
<style type="text/css">
    .select2-search__field {
        width: 80% !important;
    }

    .select2-selection__rendered {
        width: 80% !important;
    }

    .preview-category input.form-control {
        width: auto;
    }

    #loading-image {
        position: fixed;
        top: 50%;
        left: 50%;
        margin: -50px 0px 0px -50px;
    }

    .dis-none {
        display: none;
    }

    .pd-5 {
        padding: 3px;
    }

    .toggle.btn {
        min-height: 25px;
    }

    .toggle-group .btn {
        padding: 2px 12px;
    }

    .latest-remarks-list-view tr td {
        padding: 3px !important;
    }

    #latest-remarks-modal .modal-dialog {
        max-width: 1100px;
        width: 100%;
    }

    .btn-secondary {
        border: 1px solid #ddd;
        color: #757575;
        background-color: #fff !important;
    }

    .modal {
        overflow-y: auto;
    }

    body.overflow-hidden {
        overflow: hidden;
    }

    span.user_point_none button,
    span.admin_point_none button {
        pointer-events: none;
        cursor: not-allowed;
    }

    table tr:last-child td {
        border-bottom: 1px solid #ddd !important;
    }

    select.globalSelect2+span.select2 {
        width: calc(100% - 26px) !important;
    }
</style>
@endsection

@section('large_content')

<div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
</div>

<input type='hidden' id='site-development-category-id' />

<div class="row" id="common-page-layout">
    <div class="col-lg-12 margin-tb p-0">
        <input type="hidden" name="website_id_data" id="website_id_data" value="{{ isset($website) ? $website->id : 0 }}" />
        <h2 class="page-heading">Site Development 2 {{ isset($website) ? '- ( ' . $website->website . ' )' : ' ' }} <span class="count-text"></span>
            <div class="pull-right pr-2 d-flex">
                @if(Auth::user()->isAdmin())
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#createTaskUserModal" id="">Create Task Users</button>
                @endif

                {{ html()->select('select_website', ['' => 'All Website'] + $store_websites)->class('form-control select2')->id('copy_from_website') }}                <button type="button" class="btn btn-secondary" onClick="copyTasksFromWebsite()">Copy Tasks from
                    website</button>
 

                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#createTasksModal" id="">Create Tasks</button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#masterCategoryModal" id="">Add Category</button>
                <a style="color: #757575" href="{{ route('site-development-status.index') }}" target="__blank">
                    <button style=" color: #757575" class="btn btn-secondary btn-image ml-2">
                        + Add Status
                    </button>
                </a>
                <button style="margin-right:5px;" class="btn btn-secondary latest-remarks-btn ml-2">
                    Remarks
                </button>
                <a class="btn btn-secondary" data-toggle="collapse" href="#statusFilterCount" role="button" aria-expanded="false" aria-controls="statusFilterCount">
                    Status Count
                </a>
                <select name="order" id="order_query" class="form-control ml-2">
                    <option value="title" @if (isset($input['order']) and $input['order']=='title' ) selected @endif>Title</option>
                    <option value="communication" @if (isset($input['order']) and $input['order']=='communication' ) selected @endif>Communication
                    </option>
                </select>
            </div>
        </h2>
    </div>
    <br>
    <div class="col-lg-12 margin-tb pl-3 pr-3">
        <div class="row">
            <div class="col col-md-12">
                <div class="row mb-3">
                    <div class="col-md-2">
                        <form class="form-inline message-search-handler" onsubmit="event.preventDefault(); saveCategory();">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <?php /* <label for="keyword">Add Category:</label> */ ?>
                                        {{ html()->text('keyword', request('keyword'))->class('form-control')->placeholder('Add Category')->id('add-category') }}                                    </div>
                                    <div class="form-group">
                                        <?php /* <label for="button">&nbsp;</label> */ ?>
                                        <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
                                            <img src="/images/send.png" style="cursor: default;">
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="masterCategory" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Attach Master Category</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group col-md-12" style="margin-bottom: 15px;">

                                                <label class="justify-content-start" for="select_website_id_data">Select
                                                    Website</label>
                                                    {{ html()->select('select_website_id_data', $store_websites, isset($website) ? $website->id : null)->class('form-control select2')->id('select_website_id_data') }}
                                            </div>
                                            <div class="form-group col-md-12" style="margin-bottom: 15px;">

                                                <label class="justify-content-start" for="site_development_master_category_id">Select Master
                                                    Category</label>
                                                {{ html()->select('site_development_master_category_id', ['' => '- Select-'] + $masterCategories)->class('select2')->id('master_category_id') }}

                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <button style="display: inline-block;width: 10%" class="btn btn-default btn-image btn-search-action">
                                                Save
                                            </button>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-10">
                        <form class="handle-search">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select class="form-control select2" name="assignto[]" multiple style="width: 100% !important;">
                                            <option value="">Select...</option>
                                            @foreach ($users_all as $value)
                                                @if (isset(request()->assignto) and in_array($value->id, request()->assignto))
                                                    <option selected value="{{ $value->id }}">{{ $value->name }}</option>
                                                @else
                                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select class="form-control select2" name="assignto[]" multiple style="width: 100% !important;">
                                            <option value="">Select...</option>
                                            @foreach ($users_all as $value)
                                                @if (isset(request()->assignto) and in_array($value->id, request()->assignto))
                                                    <option selected value="{{ $value->id }}">{{ $value->name }}</option>
                                                @else
                                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select class="form-control select2" name="k[]" id="k" multiple>
                                            @foreach ($filter_category as $key => $all_cat)
                                            @if (isset(request()->k) and in_array($all_cat, request()->k))
                                            <option selected value="{{ $all_cat }}">{{ $all_cat }}</option>
                                            @else
                                            <option value="{{ $all_cat }}">{{ $all_cat }}</option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                    {{ html()->select('status', ['' => 'All Status'] + $allStatus, request('status'))->class('form-control select2')->id('enter-status') }}                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                    {{ html()->select(
                                                    'websites[]',
                                                    ['all' => 'All Website'] + $store_websites,
                                                    isset(request()->websites) ? request()->websites : $website->id,
                                                )->class('form-control select2')->attribute('multiple', 'multiple')->id('change_website1') }}                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-sm btn-image btn-search-keyword">
                                            <img src="{{ config('app.url') }}/images/send.png" style="cursor: default;width: 16px;">
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <div class="row m-0">
            <div class="col-md-12">
                <div class="collapse" id="statusFilterCount">
                    <div class="card card-body">
                        <?php if (! empty($statusCount)) { ?>
                            <div class="row col-md-12">
                                <?php foreach ($statusCount as $sC) { ?>
                                    <div class="col-md-2">
                                        <div class="card">
                                            <div class="card-header">
                                                <?php echo $sC->name; ?>
                                            </div>
                                            <div class="card-body">
                                                <?php echo $sC->total; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else {
                            echo 'Sorry , No data available';
                        } ?>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12 margin-tb infinite-scroll">
                <div class="table-responsive mt-2">
                    <table class="table table-bordered text-nowrap" id="documents-table">
                        <thead>
                            <tr>
                                <th width="4%">S No</th>
                                <th width="15%"></th>
                                <th width="7%" style="word-break: break-all;">Website</th>
                                <th width="7%">Master Category</th>
                                <th width="7%">Remarks</th>
                                <th width="7%">Assign To</th>
                                <th style="display:none;">Title</th>
                                <th style="display:none;">Message</th>
                                <th width="25%">Communication</th>
                                <th width="15%">status</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody class="infinite-scroll-pending-inner">
                            <?php if ($categories->count()) { ?>
                                @include('storewebsite::site-development.partials.data')
                            <?php } else { ?>
                                <tr>
                                    <td colspan="11">No records found.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-12 margin-tb text-center">
                {!! $categories->appends(request()->capture()->except('page', 'pagination') + ['pagination' => true])->render() !!}
            </div>
        </div>
    </div>
</div>

<div id="chat-list-history" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Communication</h4>
                <input type="text" name="search_chat_pop" class="form-control search_chat_pop" placeholder="Search Message" style="width: 200px;">
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
<div id="file-upload-area-section" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('site-development.upload-documents') }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="store_website_id" id="hidden-store-website-id" value="">
                <input type="hidden" name="id" id="hidden-site-id" value="">
                <input type="hidden" name="site_development_category_id" id="hidden-site-category-id" value="">
                <div class="modal-header">
                    <h4 class="modal-title">Upload File(s)</h4>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                    @csrf
                    <div class="form-group">
                        <label for="document">Documents</label>
                        <div class="needsclick dropzone" id="document-dropzone">

                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-save-documents">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="file-upload-area-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="45%">Link</th>
                            <th width="25%">Send To</th>
                            <th width="25%">Action</th>
                        </tr>
                    </thead>
                    <tbody class="display-document-list">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="remark-area-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="col-md-8" style="padding-bottom: 10px;">
                        <textarea class="form-control" col="5" name="remarks" data-id="" id="remark-field"></textarea>
                        <input type="hidden" name="remark_cat_id" data-cat_id="" id="remark_cat_id" />
                        <input type="hidden" name="remark_website_id" data-website_id="" id="remark_website_id" />
                    </div>
                    <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-remark-field">
                        <img src="/images/send.png" style="cursor: default;">
                    </button>
                </div>
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="45%">Remark</th>
                                <th width="25%">BY</th>
                                <th width="25%">Date</th>
                            </tr>
                        </thead>
                        <tbody class="remark-action-list-view">
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



<div id="create-quick-task" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?php echo route('task.create.multiple.task.shortcut'); ?>" method="post">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h4 class="modal-title">Create Task</h4>
                </div>
                <div class="modal-body">

                    <input class="form-control" value="49" type="hidden" name="category_id" />
                    <input class="form-control" value="" type="hidden" name="category_title" id="category_title" />
                    <input class="form-control" type="hidden" name="site_id" id="site_id" />
                    <div class="form-group">
                        <label for="">Subject</label>
                        <input class="form-control" type="text" id="hidden-task-subject" name="task_subject" />
                    </div>
                    <div class="form-group">
                        <select class="form-control" style="width:100%;" name="task_type" tabindex="-1" aria-hidden="true">
                            <option value="0">Other Task</option>
                            <option value="4">Developer Task</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="repository_id">Repository:</label>
                        <br>
                        <select style="width:100%" class="form-control 	" id="repository_id" name="repository_id">
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
                        <select name="task_asssigned_to" class="form-control assign-to select2">
                            @foreach ($allUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Create Review Task?</label>
                        <div class="form-group">
                            <input type="checkbox" name="need_review_task" value="1" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Websites</label>
                        <div class="form-group website-list row">
                           
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-default create-task">Submit</button>
                </div>
            </form>
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
<div id="preview-website-image" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sl no</th>
                                <th>Image</th>
                            </tr>
                        </thead>
                        <tbody class="website-image-list-view">
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

<div id="latest-remarks-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-12 pl-0">
                    <div class="col-md-2">
                        <select name="SearchStatus" class="form-control SearchStatus">
                            <option value="">--Select--</option>
                            @foreach ($allStatus as $key => $status)
                            <option value="{{ $key }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 pl-0">
                        <button class="btn btn-secondarys latest-remarks-btn">
                            <img src="/images/filter.png" style="cursor: nwse-resize;width:16px;">
                        </button>
                    </div>
                </div>
                <div class="col-md-12 pt-3">
                    <table class="table table-bordered pt-3" style="table-layout:fixed;">
                        <thead>
                            <tr>
                                <th style="width:3%;"></th>
                                <th style="width:4%;">S no</th>
                                <th style="width:13%;">Category</th>
                                <th style="width:12%;">Status</th>
                                <th style="width:10%;">By</th>
                                <th style="width:30%;">Remarks</th>
                                <th style="width:25%;">Communication</th>
                                <th style="width:3%;"></th>
                            </tr>
                        </thead>
                        <tbody class="latest-remarks-list-view">
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

<div id="artwork-history-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sl no</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Username</th>
                            </tr>
                        </thead>
                        <tbody class="artwork-history-list-view">
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

<div id="preview-task-image" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered" style="table-layout: fixed">
                        <thead>
                            <tr>
                                <th style="width: 5%;">Sl no</th>
                                <th style=" width: 30%">Files</th>
                                <th style="word-break: break-all; width: 40%">Send to</th>
                                <th style="width: 10%">User</th>
                                <th style="width: 10%">Created at</th>
                                <th style="width: 15%">Action</th>
                            </tr>
                        </thead>
                        <tbody class="task-image-list-view">
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

<div id="status-history-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sl no</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Username</th>
                            </tr>
                        </thead>
                        <tbody class="status-history-list-view">
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
<div id="masterCategoryModal" class="modal fade" role="dialog" style="display: none;">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Master Category</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>Master Category Title</label>
                    <div class="input-group">
                        <input type="text" class="form-control input-sm" name="text" id="masterCategorySingle" required="">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-secondary" onClick="saveMasterCategory()">Create</button>
            </div>
        </div>
    </div>
</div>

<div id="createTaskUserModal" class="modal fade" role="dialog" style="display: none;">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form action="{{ route('site-development.create_task_users') }}" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h4 class="modal-title">Create Master Category</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="select_website_for_task_add_id">Select User</label>

                        <select name="user_ids[]" id="user_ids" class="form-control" aria-placeholder="Select create Task users" multiple="multiple">
                            @foreach ($allUsers as $user)
                            <option value="{{ $user->id }}" @if(in_array($user->id,$user_ids)) {{'selected'}} @endif>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="createTasksModal" class="modal fade" role="dialog" style="display: none;">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Tasks</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label for="select_website_for_task_add_id">Select Website</label>
                    <div class="input-group">
                        {{ html()->select('select_website_for_task_add_id', $store_websites, isset($website) ? $website->id : null)->class('form-control globalSelect2')->id('select_website_for_task_add_id') }}
                    </div>
                </div>
                <div class="form-group">
                    <label>Select Task Category</label>
                    <div class="input-group">
                        {{ html()->select('task_category', ['Design' => 'Design', 'Development' => 'Functionality'])->class('form-control')->id('task_category') }}
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-secondary" onClick="createTasks()">Create</button>
            </div>
        </div>
    </div>
</div>

<div id="previewDoc" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-12">
                    <iframe src="" id="previewDocSource" width='700' height='550' allowfullscreen webkitallowfullscreen></iframe>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div id="confirmMessageModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <form>
            <?php echo csrf_field(); ?>
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Choose Assignee</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>



                <div class="modal-body">
                    <div class="form-group">

                        <label for="select_website_for_task_assign_id">Select Website</label>
                        {{ html()->select('select_website_for_task_assign_id', $store_websites, isset($website) ? $website->id : null)->class('form-control globalSelect2')->id('select_website_for_task_assign_id') }}
                    </div>
                    <div class="form-group">

                        <label for="task_asssigned_to">Assigned to</label>
                        <select name="task_asssigned_to" id="task_asssigned_to" class="form-control" aria-placeholder="Select Assigned" style="float: left">
                            @foreach ($allUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-secondary confirm-messge-button1">Send</button>
                </div>
        </form>
    </div>

</div>
</div>

<div id="save-document-in-site-asset" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-6">
                    Select the type where you want to store document
                </div>
                <div class="col-md-6">
                    <input type='hidden' id="site_asset_media_id">
                    <select id='media_type' class="form-control">
                        <option value="">Select type</option>
                        <option value="PSDD">PSD - DESKTOP</option>
                        <option value="PSDM">PSD - MOBILE</option>
                        <option value="PSDA">PSD - APP</option>
                        <option value="FIGMA">FIGMA</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-default save-document-site-asset-btn">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        applySelect2(jQuery('.select2'));
    });
    $(document).on('change', '.assign-user', function() {
        let id = $(this).attr('data-id');
        let userId = $(this).val();
        if (userId == '') {
            return;
        }
        $.ajax({
            url: "{{ route('task.AssignTaskToUser') }}",
            data: {
                user_id: userId,
                issue_id: id
            },
            success: function() {
                toastr["success"]("User assigned successfully!", "Message")
            },
            error: function(error) {
                toastr["error"](error.responseJSON.message, "Message")

            }
        });

    });
    $("#user_id").select2({
        ajax: {
            url: "{{ route('user-search') }}",
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
    $(document).on('click', '.confirm-messge-button1', function(e) {
        e.preventDefault();
        if ($("#task_asssigned_to").val() != "") {
            var copy_from_website = $('#copy_from_website').val();
            var copy_to_website = $('#select_website_for_task_assign_id').val();
            $("#confirmMessageModal").modal("hide");
            $.ajax({
                url: "{{ route('site-development.copy.task') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    copy_from_website: copy_from_website,
                    copy_to_website: copy_to_website,
                    "_token": "{{ csrf_token() }}",
                    task_asssigned_to: $("#task_asssigned_to").val(),
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
            }).done(function(data) {
                $("#loading-image").hide();
                toastr["success"](data.messages);
                setTimeout(function() {
                    refreshPage();
                }, 2000);
            }).fail(function(data) {
                $('#masterCategorySingle').val('')
            });
        }

        //}
    });

    function createTasks() {
        var text = $('#task_category').val()
        var select_website_for_task_add_id = $('#select_website_for_task_add_id').val()
        if (text === '') {
            alert('Please Enter Master Category');
        } else {
            $.ajax({
                url: "{{ route('site-development.create.task') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    task_category: text,
                    websiteId: select_website_for_task_add_id,
                    "_token": "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
            }).done(function(data) {
                $("#loading-image").hide();
                toastr["success"](data.messages);
                setTimeout(function() {
                    refreshPage();
                }, 2000);
            }).fail(function(data) {
                $('#masterCategorySingle').val('')
            });
        }
    }

    function copyTasksFromWebsite() {
        var copy_from_website = $('#copy_from_website').val();
        if (copy_from_website === '') {
            alert('Please select website');
        } else {
            $("#confirmMessageModal").modal("show");

        }
    }

    $("#change_website").change(function() {
        var websiteUrl = '';
        websiteUrl = "{{ route('site-development.index') }}/" + $(this).val() + '?k=' + $('#k').val();
        window.location = websiteUrl;
    });
    $('.assign-to.select2').select2({
        width: "100%"
    });


    $('#latest-remarks-modal').on('shown.bs.modal', function(e) {
        $("body").addClass("overflow-hidden");
    });
    $('#latest-remarks-modal').on('hidden.bs.modal', function(e) {
        $("body").removeClass("overflow-hidden");
    });
    // $('#latest-remarks-modal').on('hidden.bs.modal', function () {
    // 	document.body.classList.remove('thisClass');
    //
    // })

    // $('.infinite-scroll').jscroll({
    //         autoTrigger: true,
    //         loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
    //         padding: 20,
    //         nextSelector: '.pagination li.active + li a',
    //         contentSelector: 'div.infinite-scroll',
    //         callback: function () {
    //             $('ul.pagination').first().remove();
    //         }
    //     });

    function saveCategory() {
        // var websiteId = $('#website_id_data').val();//$('#website_id').val()
        var websiteId = $('#select_website_id_data').val(); //$('#website_id').val()

        var text = $('#add-category').val();
        var masterCategoryId = $('#master_category_id').val();
        if (masterCategoryId == null || masterCategoryId == '') {
            $('#masterCategory').modal('show');
            return false;
        }
        if (text === '') {
            alert('Please Enter Text');
        } else {
            $.ajax({
                    url: "{{ route('site-development.category.save') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        websiteId: websiteId,
                        text: text,
                        master_category_id: masterCategoryId,
                        "_token": "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                    },
                })
                .done(function(data) {
                    $('#add-category').val('');
                    $('#master_category_id').val('');
                    $("#loading-image").hide();
                    toastr["success"](data.messages);
                    // refreshPage()
                    setTimeout(function() {
                        refreshPage();
                    }, 2000);
                    $('#masterCategory').modal('hide');
                })
                .fail(function(data) {
                    $('#add-category').val('')
                });

        }
    }

    function saveMasterCategory() {
        var text = $('#masterCategorySingle').val()
        if (text === '') {
            alert('Please Enter Master Category');
        } else {
            $.ajax({
                    url: "{{ route('site-development.master_category.save') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        text: text,
                        "_token": "{{ csrf_token() }}"
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                    },
                })
                .done(function(data) {
                    $('#masterCategorySingle').val('');
                    $("#loading-image").hide();
                    toastr["success"](data.messages);
                    // refreshPage()
                    setTimeout(function() {
                        refreshPage();
                    }, 2000);
                })
                .fail(function(data) {
                    $('#masterCategorySingle').val('')
                });
        }
    }

    $(function() {
        $(document).on("focusout", ".save-item", function() {
            websiteId = $('#website_id').val()
            category = $(this).data("category")
            type = $(this).data("type")
            site = $(this).data("site")
            var text = $(this).val();
            $.ajax({
                    url: "{{ route('site-development.save') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        websiteId: websiteId,
                        "_token": "{{ csrf_token() }}",
                        category: category,
                        type: type,
                        text: text,
                        site: site
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                    },
                })
                .done(function(data) {
                    $("#loading-image").hide();
                })
                .fail(function(data) {
                    $("#loading-image").hide();
                });
        });

        $(document).on("click", ".save-artwork-status", function() {
            websiteId = $('#website_id').val()
            category = $(this).data("category")
            type = $(this).data("type")
            site = $(this).data("site")
            var text = $(this).val();
            $.ajax({
                    url: "{{ route('site-development.save') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        websiteId: websiteId,
                        "_token": "{{ csrf_token() }}",
                        category: category,
                        type: type,
                        text: text,
                        site: site
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                    },
                })
                .done(function(data) {
                    $("#loading-image").hide();
                })
                .fail(function(data) {
                    $("#loading-image").hide();
                });
        });

        $(document).on("change", ".save-item-select ", function() {
            websiteId = $('#website_id').val()
            category = $(this).data("category")
            type = $(this).data("type")
            site = $(this).data("site")
            var text = $(this).val();
            $.ajax({
                    url: "{{ route('site-development.save') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        websiteId: websiteId,
                        "_token": "{{ csrf_token() }}",
                        category: category,
                        type: type,
                        text: text,
                        site: site
                    },
                })
                .done(function(data) {
                    toastr["success"]("Successful");
                })
                .fail(function(data) {
                });
        });

        $(document).on("click", ".save-status", function() {
            websiteId = $('#website_id').val()
            category = $(this).data("category")
            type = $(this).data("type")
            site = $(this).data("site")
            var text = $(this).data("text");
            var elem = $(this);
            $.ajax({
                    url: "{{ route('site-development.save') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        websiteId: websiteId,
                        "_token": "{{ csrf_token() }}",
                        category: category,
                        type: type,
                        text: text,
                        site: site
                    },
                })
                .done(function(data) {
                    toastr["success"]("Successful");
                    if (typeof data.html !== 'undefined' || data.html !== '') {
                        elem.parent('span').html(data.html);
                    }
                })
                .fail(function(data) {
                });
        });


        $(document).on("click", ".btn-remark-field", function() {

            var id = $("#remark-field").data("id");
            var cat_id = $("#remark_cat_id").val();
            var website_id = $("#remark_website_id").val();

            var val = $("#remark-field").val();
            $.ajax({
                url: 'site-development/' + id + '/remarks',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    remark: val,
                    cat_id: cat_id,
                    website_id: website_id
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                $("#remark-field").val("");
                toastr["success"]("Remarks fetched successfully");
                var html = "";
                $.each(response.data, function(k, v) {
                    html += "<tr>";
                    html += "<td>" + v.id + "</td>";
                    html += "<td>" + v.remarks + "</td>";
                    html += "<td>" + v.created_by + "</td>";
                    html += "<td>" + v.created_at + "</td>";
                    html += "</tr>";
                });
                $("#remark-area-list").find(".remark-action-list-view").html(html);
                //$("#remark-area-list").modal("show");
                //$this.closest("tr").remove();
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                toastr["error"]("Oops,something went wrong");
                $("#loading-image").hide();
            });
        });
    });

    function saveRemarks(rowId) {
        debugger;
        var siteId = $("#remark_" + rowId).data("siteid");
        var cat_id = $("#remark_" + rowId).data("catid");
        var website_id = $("#remark_" + rowId).data("websiteid");

        var val = $("#remark_" + rowId).val();
        var data = {
            remark: val,
            cat_id: cat_id,
            website_id: website_id
        };
        $.ajax({
            url: '/site-development/' + siteId + '/remarks',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: data,
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            $("#remark_" + rowId).val("");
            toastr["success"]("Remarks fetched successfully");
            var html = "";
            $.each(response.data, function(k, v) {
                html += "<tr>";
                html += "<td>" + v.id + "</td>";
                html += "<td>" + v.remarks + "</td>";
                html += "<td>" + v.created_by + "</td>";
                html += "<td>" + v.created_at + "</td>";
                html += "</tr>";
            });
            $("#remark-area-list").find(".remark-action-list-view").html(html);

        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            toastr["error"]("Oops,something went wrong");
            $("#loading-image").hide();
        });
    }

    function editCategory(id) {
        $('#editCategory' + id).modal('show');
    }

    function submitCategoryChange(id) {
        category = $('#category-name' + id).val()
        categoryId = id
        $.ajax({
                url: "{{ route('site-development.category.edit') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    category: category,
                    "_token": "{{ csrf_token() }}",
                    categoryId: categoryId
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
            })
            .done(function(data) {
                refreshPage();
                $("#loading-image").hide();
                $('#editCategory' + id).modal('hide');
            })
            .fail(function(data) {
                refreshPage()
            });
    }


    function refreshPage() {
        $.ajax({
            url: window.location.href,
            dataType: "json",
            data: {},
        }).done(function(data) {
            $("#documents-table tbody").empty().html(data.tbody);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
    }

    $(document).on('click', '.create-quick-task', function() {
        var $this = $(this);
        site = $(this).data("id");
        title = $(this).data("title");
        cat_title = $(this).data("category_title");
        development = $(this).data("development");
        if (!title || title == '') {
            toastr["error"]("Please add title first");
            return;
        }
        //debugger;
        let val = $("#change_website1").select2("val");
        $.ajax({
            url: '/task/get/websitelist',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: {
                id: val,
                cat_title:cat_title
            },
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            //$this.siblings('input').val("");
            $('.website-list').html(response.data);
            //toastr["success"]("Remarks fetched successfully");
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            toastr["error"]("Oops,something went wrong");
            $("#loading-image").hide();
        });

        $("#create-quick-task").modal("show");

        var selValue = $(".save-item-select").val();
        if (selValue != "") {
            $("#create-quick-task").find(".assign-to option[value=" + selValue + "]").attr('selected',
                'selected')
            $('.assign-to.select2').select2({
                width: "100%"
            });
        }

        $("#hidden-task-subject").val(title);
        $(".text-task-development").val(development);
        $('#site_id').val(site);

        // $.ajax({
        // 		url: '/site-development/get-user-involved/'+site,
        // 		dataType: "json",
        // 		type: 'GET',
        // 	}).done(function (response) {
        // 		var option = '<option value="" > Select user </option>';
        // 		$.each(response.data,function(k,v){
        // 			option = option + '<option value="'+v.id+'" > '+v.name+' </option>';
        // 		});

        // 	}).fail(function (jqXHR, ajaxOptions, thrownError) {
        // 	    toastr["error"](jqXHR.responseJSON.message);
        // });
    });

    $(document).on('click', '.send-message-site-quick', function() {
        $this = $(this);
        var id = $(this).data("id");
        var val = $(this).siblings('input').val();

        $.ajax({
            url: '/site-development/' + id + '/remarks',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: {
                remark: val
            },
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            $this.siblings('input').val("");
            // $('#latest-remarks-modal').modal('hide');
            toastr["success"]("Remarks fetched successfully");
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            toastr["error"]("Oops,something went wrong");
            $("#loading-image").hide();
        });
    });

    $(document).on('click', '.send-message-site', function() {
        var $this = $(this);
        site = $(this).data("id");
        category = $(this).data("category");
        message = $('#message-' + site).val();
        userId = $('#user-' + site + ' option:selected').val();
        prefix = $this.data("prefix");
        var users = [];

        var hidden_row_class = 'hidden_row_' + category;

        if ($this.closest("tr").find("input[name='developer']:checked").length > 0) {
            var value = $this.closest("tr").find("select[name='developer_id']").val();
            if (value != "") {
                users.push(value);
            }
        }
        if ($this.closest("tr").find("input[name='designer']:checked").length > 0) {
            var value = $this.closest("tr").find("select[name='designer_id']").val();
            if (value != "") {
                users.push(value);
            }
        }
        if ($this.closest("tr").find("input[name='html']:checked").length > 0) {
            var value = $this.closest("tr").find("select[name='html_designer']").val();
            if (value != "") {
                users.push(value);
            }
        }
        if ($this.closest("tr").find("input[name='tester']:checked").length > 0) {
            var value = $this.closest("tr").find("select[name='tester_id']").val();
            if (value != "") {
                users.push(value);
            }
        }

        if (site) {
            $.ajax({
                url: '/whatsapp/sendMessage/site_development',
                dataType: "json",
                type: 'POST',
                data: {
                    'site_development_id': site,
                    'message': prefix + ' => ' + message,
                    'users': users,
                    "_token": "{{ csrf_token() }}",
                    'status': 2
                },
                beforeSend: function() {
                    $('#message-' + site).attr('disabled', true);
                }
            }).done(function(data) {
                toastr["success"](
                    "Message Sent successfully"); //Purpose : Display success message - DEVATSK-4361
                $('#message-' + site).attr('disabled', false);
                $('#message-' + site).val('');
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        } else {
            alert('Site is not saved please enter value or select User');
        }
    });

    $(document).on("click", ".fa-ignore-category", function() {
        var $this = $(this);
        var msg = "disallow";
        var status = $this.data("status");
        if (status) {
            msg = "allow";
        }
        if (confirm("Are you sure want to " + msg + " this category?")) {
            var store_website_id = $this.data("site-id");
            var category = $this.data("category-id");
            $.ajax({
                url: '/site-development/disallow-category',
                dataType: "json",
                type: 'POST',
                data: {
                    'store_website_id': store_website_id,
                    'category': category,
                    "_token": "{{ csrf_token() }}",
                    status: status
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(data) {
                $("#loading-image").hide();
                toastr["success"]("Category removed successfully");
                $this.closest("tr").remove();
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                toastr["error"]("Oops,something went wrong");
                $("#loading-image").hide();
            });
        }
    });

    $(document).on("click", ".btn-file-upload", function() {
        var $this = $(this);
        $("#file-upload-area-section").modal("show");
        $("#hidden-store-website-id").val($this.data("store-website-id"));
        $("#hidden-site-id").val($this.data("site-id"));
        $("#hidden-site-category-id").val($this.data("site-category-id"));
    });

    $(document).on("click", ".btn-file-list", function(e) {
        e.preventDefault();
        var $this = $(this);
        var id = $(this).data("site-id");
        $.ajax({
            url: '/site-development/' + id + '/list-documents',
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType: "json",
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            var html = "";
            $.each(response.data, function(k, v) {
                html += "<tr>";
                html += "<td>" + v.id + "</td>";
                html += "<td>" + v.url + "</td>";
                html += "<td><div class='form-row'>" + v.user_list + "</div></td>";
                html += '<td><a class="btn-secondary" href="' + v.url + '" data-site-id="' + v
                    .site_id +
                    '" target="__blank"><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;<a class="btn-secondary link-delete-document" data-site-id="' +
                    v.site_id + '" data-id=' + v.id +
                    ' href="_blank"><i class="fa fa-trash" aria-hidden="true"></i></a>&nbsp;<a class="btn-secondary link-send-document" data-site-id="' +
                    v.site_id + '" data-id=' + v.id +
                    ' href="_blank"><i class="fa fa-comment" aria-hidden="true"></i></a></td>';
                html += "</tr>";
            });
            $(".display-document-list").html(html);
            $("#file-upload-area-list").modal("show");
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            toastr["error"]("Oops,something went wrong");
            $("#loading-image").hide();
        });
    });

    $(document).on("click", ".btn-save-documents", function(e) {
        e.preventDefault();
        var $this = $(this);
        var formData = new FormData($this.closest("form")[0]);
        $.ajax({
            url: '/site-development/save-documents',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType: "json",
            data: $this.closest("form").serialize(),
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(data) {
            $("#loading-image").hide();
            toastr["success"]("Document uploaded successfully");
            location.reload();
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            toastr["error"](jqXHR.responseJSON.message);
            $("#loading-image").hide();
        });
    });


    $(document).on("click", ".link-send-document", function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        var site_id = $(this).data("site-id");
        var user_id = $(this).closest("tr").find(".send-message-to-id").val();
        $.ajax({
            url: '/site-development/send-document',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType: "json",
            data: {
                id: id,
                site_id: site_id,
                user_id: user_id
            },
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(data) {
            $("#loading-image").hide();
            toastr["success"]("Document sent successfully");
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            toastr["error"]("Oops,something went wrong");
            $("#loading-image").hide();
        });

    });

    $(document).on("click", ".link-delete-document", function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        var $this = $(this);
        if (confirm("Are you sure you want to delete records ?")) {
            $.ajax({
                url: '/site-development/delete-document',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                dataType: "json",
                data: {
                    id: id
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(data) {
                $("#loading-image").hide();
                toastr["success"]("Document deleted successfully");
                $this.closest("tr").remove();
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                toastr["error"]("Oops,something went wrong");
                $("#loading-image").hide();
            });
        }
    });

    $(document).on("click", ".link-send-task", function(e) {

        var id = $(this).data("id");
        var task_id = $(this).data("media-id");
        var taskdata = $(this).parent().find("#selector_id").val();

        var type = $(this).parent().find('#selector_id option[value="' + taskdata + '"]').html().includes(
            'DEVTASK') ? 'DEVTASK' : 'TASK';

        if ($(this).parent().find("#selector_id").val() == '') {
            toastr["error"]('Please Select Task Or DevTask', "Message")
            return false;
        }

        // $(this).parent().find("#selector_id").val(' ').change();
        // $(this).parent().find("#selector_id").html(' ').change();

        $.ajax({
            url: '/site-development/send',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType: "json",
            data: {
                id: id,
                task_id: task_id,
                taskdata: taskdata,
                type: type
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                toastr["success"]("File sent successfully");
            },
            error: function(error) {
                toastr["error"];
            }

        });

    });

    $(document).on("click", ".send-to-sop-page", function() {
        var id = $(this).data("id");
        var task_id = $(this).data("media-id");

        $.ajax({
            url: '/site-development/send-sop',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType: "json",
            data: {
                id: id,
                task_id: task_id
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                if (response.success) {
                    toastr["success"](response.message);
                } else {
                    toastr["error"](response.message);
                }

            },
            error: function(error) {
                toastr["error"];
            }

        });
    });

    $(document).on('click', '.previewDoc', function() {
        $('#previewDocSource').attr('src', '');
        var docUrl = $(this).data('docurl');
        var type = $(this).data('type');
        var type = jQuery.trim(type);
        if (type == "image") {
            $('#previewDocSource').attr('src', docUrl);
        } else {
            $('#previewDocSource').attr('src', "https://docs.google.com/gview?url=" + docUrl +
                "&embedded=true");
        }
        $('#previewDoc').modal('show');
    });

    $("#previewDoc").on("hidden", function() {
        $('#previewDocSource').attr('src', '');
    });

    $(document).on("click", ".btn-store-development-remark", function(e) {
        var id = $(this).data("site-id");
        var cat_id = $(this).data("site-category-id");
        var website_id = $(this).data("store-website-id");
        $.ajax({
            url: '/site-development/' + id + '/remarks',
            type: 'GET',
            data: {
                cat_id: cat_id,
                website_id: website_id
            },
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            beforeSend: function() {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            toastr["success"]("Remarks fetched successfully");

            var html = "";
            const shorter = (a, b) => a.id > b.id ? -1 : 1;
            response.data.flat().sort(shorter)

            $.each(response.data.flat().sort(shorter), function(k, v) {
                html += "<tr>";
                html += "<td>" + v.id + "</td>";
                html += "<td>" + v.remarks + "</td>";
                html += "<td>" + v.created_by + "</td>";
                html += "<td>" + v.created_at + "</td>";
                html += "</tr>";
            });

            $("#remark-area-list").find("#remark-field").data("id", id);
            $("#remark-area-list").find("#remark_cat_id").val(cat_id);
            $("#remark-area-list").find("#remark_website_id").val(website_id);
            $("#remark-area-list").find(".remark-action-list-view").html(html);
            $("#remark-area-list").modal("show").css('z-index', 1051);
            //$this.closest("tr").remove();
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            toastr["error"]("Oops,something went wrong");
            $("#loading-image").hide();
        });
    });

    var uploadedDocumentMap = {}
    Dropzone.options.documentDropzone = {
        url: "{{ route('site-development.upload-documents') }}",
        maxFilesize: 20, // MB
        addRemoveLinks: true,
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        success: function(file, response) {
            $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">')
            uploadedDocumentMap[file.name] = response.name
        },
        removedfile: function(file) {
            file.previewElement.remove()
            var name = ''
            if (typeof file.file_name !== 'undefined') {
                name = file.file_name
            } else {
                name = uploadedDocumentMap[file.name]
            }
            $('form').find('input[name="document[]"][value="' + name + '"]').remove()
        },
        init: function() {

        }
    }

    $(document).on('click', '.preview-img-btn', function(e) {
        e.preventDefault();
        id = $(this).data('id');
        if (!id) {
            toastr["error"]("No data found", "error");
            return;
        }
        $.ajax({
            url: "/site-development/preview-img/" + id,
            type: 'GET',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(response) {
                var tr = '';
                for (var i = 1; i <= response.data.length; i++) {
                    tr = tr + '<tr><td>' + i + '</td><td><img style="height:100px;" src="' +
                        response.data[i - 1].url + '"></td></tr>';
                }
                $("#preview-website-image").modal("show");
                $(".website-image-list-view").html(tr);
                $("#loading-image").hide();
            },
            error: function() {
                $("#loading-image").hide();
            }
        });
    });

    $('#latest-remarks-modal').on('shown.bs.modal', function() {
        $(this).find('.SearchStatus').val('');
    });

    $(document).on('click', '.latest-remarks-btn', function(e) {
        websiteId = $('#website_id').val();
        websiteId = $.trim(websiteId);
        var searchStatus = $(this).parents('.modal-body').find('.SearchStatus').val();
        $.ajax({
            url: "/site-development/latest-reamrks/" + websiteId,
            type: 'GET',
            data: {
                status: searchStatus
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(response) {
                var tr = '';

                for (var i = 1; i <= response.data.length; i++) {
                    var status = response.data[i - 1].status;
                    var siteId = response.data[i - 1].site_id;
                    var cateogryId = response.data[i - 1].category_id;
                    var user_id = response.data[i - 1].user_id;
                    var storeWebsite = response.data[i - 1].sw_website;
                    var storeDev = response.data[i - 1].sd_title;
                    var user_id = response.data[i - 1].user_id;
                    var user_flag = response.data[i - 1].user_flagged;
                    var admin_flag = response.data[i - 1].admin_flagged;
                    var id = response.data[i - 1].id;
                    let option_data = `<option>--select--</option>`;
                    for (var j = 0; j < response.status.length; j++) {
                        option_data +=
                            `<option value="${response.status[j].id}" ${response.status[j].id == status ? 'selected' : ''}>${response.status[j].name}</option>`
                    }
                    <?php if (Auth::user()->isAdmin()) { ?>
                        var admin_permission = 'admin_changable';
                        var user_permission = 'user_point_none';
                    <?php } else { ?>
                        var admin_permission = 'admin_point_none';
                        var user_permission = 'user_changable';
                    <?php } ?>


                    tr += '<tr><td>';
                    if (user_flag == 1) {
                        tr += '<span title="user priority" class="' + admin_permission +
                            '"><button data-id = ' + id +
                            ' type="button" class="btn btn-image remark-user-flag pd-5"><img height="14" src="/images/flagged.png"></button></span>';
                    } else {
                        tr += '<span title="user priority" class="' + admin_permission +
                            '"><button data-id = ' + id +
                            ' type="button" class="btn btn-image remark-user-flag pd-5"><img height="14" src="/images/unflagged.png"></button></span>';
                    }

                    tr += '</td><td>' + i + '</td><td>' + response.data[i - 1].title + '</td><td>' +
                        '<select class="form-control select-site-status" name="status" data-site_id="' +
                        siteId + '">' + option_data + '</select>' +
                        '</td><td style="word-break: break-all">' + response.username[i - 1] +
                        '</td><td style="word-break: break-all">' + response.data[i - 1].remarks +
                        '<button type="button" data-site-id="' + response.data[i - 1].site_id +
                        '" class="btn btn-store-development-remark pd-5"><i class="fa fa-comment" aria-hidden="true"></i></button></td><td><div class="d-flex"><input type="text" class="form-control quick-message-field" name="message" placeholder="Message" value="" id="message-' +
                        siteId +
                        '"><button style="padding: 2px;" class="btn btn-sm btn-image send-message-site-quick" data-prefix="# ' +
                        storeWebsite + ' ' + storeDev + '" data-user="' + user_id +
                        '" data-category="' + cateogryId + '" data-id="' + siteId +
                        '"><img src="/images/filled-sent.png"/></button></div></td><td>';
                    if (admin_flag == 1) {
                        tr += '<span title="admin priority" class="' + admin_permission +
                            '"><button data-id = ' + id +
                            ' type="button" class="btn btn-image remark-admin-flag pd-5"><img height="14" src="/images/flagged.png"></button></span>';
                    } else {
                        tr += '<span title="admin priority" class="' + admin_permission +
                            '"><button data-id = ' + id +
                            ' type="button" class="btn btn-image remark-admin-flag pd-5"><img height="14" src="/images/unflagged.png"></button></span>';
                    }
                    tr += '</td></tr>'
                    // tr = tr + '<tr><td>' + if(response.data[i - 1].admin_flagged === 1){ + '<button type="button" class="btn btn-image remark-task pd-5"><img height="14" src="/images/unflagged.png"></button>' + } + '</td><td>' + i + '</td><td>' + response.data[i - 1].title + '</td><td>' + response.username[i-1] + '</td><td>' + response.data[i - 1].remarks + '<button type="button" data-site-id="' + response.data[i - 1].site_id + '" class="btn btn-store-development-remark pd-5"><i class="fa fa-comment" aria-hidden="true"></i></button></td><td><div class="d-flex"><input type="text" class="form-control quick-message-field" name="message" placeholder="Message" value="" id="message-' + siteId + '"><button style="padding: 2px;" class="btn btn-sm btn-image send-message-site-quick" data-prefix="# ' + storeWebsite + ' ' + storeDev + '" data-user="' + user_id + '" data-category="' + cateogryId + '" data-id="' + siteId + '"><img src="/images/filled-sent.png"/></button></div></td><td><img height="14" src="/images/unflagged.png"></td></tr>';
                }
                $("#latest-remarks-modal").modal("show");
                $(".latest-remarks-list-view").html(tr);
                $("#loading-image").hide();
            },
            error: function() {
                $("#loading-image").hide();
            }
        });
    });

    $(document).on('change', '.select-site-status', function() {
        var status = $(this).val();
        var site_id = $(this).data('site_id');
        $.ajax({
            type: "get",
            url: "{{ route('site_devlopment.status.update') }}",
            data: {
                status: status,
                site_id: site_id
            },
            success: function(response) {
                toastr.success(response.message);
                var site = response.site;
                let option_data = `<option>--select--</option>`;
                for (var j = 0; j < response.status.length; j++) {
                    option_data +=
                        `<option value="${response.status[j].id}" ${response.status[j].id == site.status ? 'selected' : ''}>${response.status[j].name}</option>`
                }

                $('.save-item-select[ data-site = ' + site.id + ']').html(option_data);

            }
        })
    })

    $(document).on('click', '.remark-admin-flag', function() {
        var remark_id = $(this).data('id');
        var thiss = $(this);

        $.ajax({
            type: "POST",
            url: "{{ route('remark.flag.admin') }}",
            data: {
                _token: "{{ csrf_token() }}",
                remark_id: remark_id
            },
            beforeSend: function() {
                $(thiss).text('Flagging...');
            }
        }).done(function(response) {
            if (response.admin_flagged == 1) {
                $(thiss).html('<img src="/images/flagged.png" />');
            } else {
                $(thiss).html('<img src="/images/unflagged.png" />');
            }
        }).fail(function(response) {
            $(thiss).html('<img src="/images/unflagged.png" />');

            alert('Could not flag task!');
        });
    });


    $(document).on('click', '.remark-user-flag', function() {
        var remark_id = $(this).data('id');
        var thiss = $(this);

        $.ajax({
            type: "POST",
            url: "{{ route('remark.flag.user') }}",
            data: {
                _token: "{{ csrf_token() }}",
                remark_id: remark_id
            },
            beforeSend: function() {
                $(thiss).text('Flagging...');
            }
        }).done(function(response) {
            if (response.user_flagged == 1) {
                $(thiss).html('<img src="/images/flagged.png" />');
            } else {
                $(thiss).html('<img src="/images/unflagged.png" />');
            }
        }).fail(function(response) {
            $(thiss).html('<img src="/images/unflagged.png" />');

            alert('Could not flag task!');
        });
    });

    $(document).on('click', '.artwork-history-btn', function(e) {
        id = $(this).data('id');
        if (!id) return;
        $.ajax({
            url: "/site-development/artwork-history/" + id,
            type: 'GET',
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(response) {
                var tr = '';
                for (var i = 1; i <= response.data.length; i++) {
                    tr = tr + '<tr><td>' + i + '</td><td>' + response.data[i - 1].date +
                        '</td><td> Status changed from ' + response.data[i - 1].from_status +
                        ' to ' + response.data[i - 1].to_status + '</td><td>' + response.data[i - 1]
                        .username + '</td></tr>';
                }
                $("#artwork-history-modal").modal("show");
                $(".artwork-history-list-view").html(tr);
                $("#loading-image").hide();
            },
            error: function() {
                $("#loading-image").hide();
            }
        });
    });

    $(document).on('click', '.btn-status-histories-get', function(e) {
        e.preventDefault();
        id = $(this).data('site-id');
        if (!id) return;
        $.ajax({
            url: "/site-development/status-history/" + id,
            type: 'GET',
            beforeSend: function() {
                $("#loading-image").show();
            },
            dataType: "json",
            success: function(response) {
                var tr = '';
                $.each(response.data, function(k, v) {
                    tr = tr + '<tr><td>' + v.id + '</td><td>' + v.created_at +
                        '</td><td> ' + v.status_name + '</td><td>' + v.user_name +
                        '</td></tr>';
                });
                $("#status-history-modal").modal("show");
                $(".status-history-list-view").html(tr);
                $("#loading-image").hide();
            },
            error: function() {
                $("#loading-image").hide();
            }
        });
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
                    $("#create-quick-task").modal("hide");
                } else {
                    toastr['error'](response.message);
                }
            }
        }).fail(function(response) {
            toastr['error'](response.responseJSON.message);
        });
    });

    $(document).on("click", ".toggle-class", function() {
        $(".hidden_row_" + $(this).data("id")).toggleClass("dis-none");
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $(".developer").change(function() {
            $(this).closest("tr").find("input[name='developer']").prop('checked', true)
        });

        $(".designer").change(function() {
            $(this).closest("tr").find("input[name='designer']").prop('checked', true)
        });

        $(".html").change(function() {
            $(this).closest("tr").find("input[name='html']").prop('checked', true)
        });

    });
</script>
<script>
    $(document).on("click", ".tasks-relation", function() {
        alert('called');
        var $this = $(this);
        var site_id = $(this).data("id");
        $.ajax({
            type: 'get',
            url: 'task/relation/' + site_id,
            dataType: "json",
            beforeSend: function() {
                $("#loading-image").show();
            },
            success: function(data) {
                $("#dev_task_statistics").modal("show");
                var table = '<div class="table-responsive">' +
                    '<table class="table table-bordered table-striped">' +
                    '<tr><th width="4%">Task Id</th><th width="4%">Parent Task</th></tr>';
                for (var i = 0; i < data.othertask.length; i++) {
                    table = table + '<tr><td>' + data.othertask[i].id + '</td><td>#' + data
                        .othertask[i].parent_task_id + '</td></tr>';
                }
                table = table + '</table></div>';
                $("#loading-image").hide();
                $(".modal").css("overflow-x", "hidden");
                $(".modal").css("overflow-y", "auto");
                $("#dev_task_statistics_content").html(table);
            },
            error: function(error) {
                $("#loading-image").hide();
            }
        });
    });

    $(document).on("click", ".count-dev-customer-tasks", function() {

        var $this = $(this);
        // var user_id = $(this).closest("tr").find(".ucfuid").val();
        var site_id = $(this).data("id");
        var category_id = $(this).data("category");
        $("#site-development-category-id").val(category_id);
        $.ajax({
            type: 'get',
            url: 'countdevtask/' + site_id,
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
                    if (typeof status == 'undefined' || typeof status == '' || typeof status ==
                        '0') {
                        status = 'In progress'
                    };
                    table = table + '<tr><td>' + data.taskStatistics[i].task_type + '</td><td>#' +
                        data.taskStatistics[i].id +
                        '</td><td class="expand-row-msg" data-name="asgTo" data-id="' + data
                        .taskStatistics[i].id + '"><span class="show-short-asgTo-' + data
                        .taskStatistics[i].id + '">' + data.taskStatistics[i].assigned_to_name
                        .replace(/(.{6})..+/, "$1..") +
                        '</span><span style="word-break:break-all;" class="show-full-asgTo-' + data
                        .taskStatistics[i].id + ' hidden">' + data.taskStatistics[i]
                        .assigned_to_name +
                        '</span></td><td class="expand-row-msg" data-name="res" data-id="' + data
                        .taskStatistics[i].id + '"><span class="show-short-res-' + data
                        .taskStatistics[i].id + '">' + res.replace(/(.{7})..+/, "$1..") +
                        '</span><span style="word-break:break-all;" class="show-full-res-' + data
                        .taskStatistics[i].id + ' hidden">' + res + '</span></td><td>' + status +
                        '</td><td><div class="col-md-10 pl-0 pr-1"><textarea rows="1" style="width: 100%; float: left;" class="form-control quick-message-field input-sm" name="message" placeholder="Message"></textarea></div><div class="p-0"><button class="btn btn-sm btn-xs send-message" title="Send message" data-taskid="' +
                        data.taskStatistics[i].id +
                        '"><i class="fa fa-paper-plane"></i></button></div></td><td><button type="button" class="btn btn-xs load-communication-modal load-body-class" data-object="' +
                        data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i]
                        .id +
                        '" title="Load messages" data-dismiss="modal"><i class="fa fa-comments"></i></button>';
                    table = table + '<a href="javascript:void(0);" data-task-type="' + data
                        .taskStatistics[i].task_type + '" data-id="' + data.taskStatistics[i].id +
                        '" class="delete-dev-task-btn btn btn-xs"><i class="fa fa-trash"></i></a>';
                    table = table +
                        '<button type="button" class="btn btn-xs  preview-img pd-5" data-object="' +
                        data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i]
                        .id + '" data-dismiss="modal"><i class="fa fa-list"></i></button></td>';
                    table = table + '</tr>';
                }
                table = table + '</table></div>';
                $("#loading-image").hide();
                $(".modal").css("overflow-x", "hidden");
                $(".modal").css("overflow-y", "auto");
                $("#dev_task_statistics_content").html(table);
            },
            error: function(error) {
                $("#loading-image").hide();
            }
        });


    });

    $(document).on('click', '.expand-row-msg', function() {
        var name = $(this).data('name');
        var id = $(this).data('id');
        var full = '.expand-row-msg .show-short-' + name + '-' + id;
        var mini = '.expand-row-msg .show-full-' + name + '-' + id;
        $(full).toggleClass('hidden');
        $(mini).toggleClass('hidden');
    });
    $(document).on('click', '.assign', function() {
        //debugger;
        var id = $(this).data('id');
        var full = '.assign .comm-' + id;
        var mini = '.assign .comm-' + id;
        $(full).toggleClass('show');
        $(mini).toggleClass('hidden');
        $(this, "table tr:nth-child(1)").addClass('test') //css("background-color", "yellow");

    });
    $(document).on('click', '.show_moreCls', function(event) {

        var id = $(this).data('id');
        if ($('.comm-' + id).hasClass("hidden")) {
            //$(this).text('Show Less');
            $('.comm-' + id).removeClass('hidden');
        } else {
            $('.comm-' + id).addClass('hidden');
            //$(this).text('Show More');
        }


    });

    function getmessageData(item, index) {
        text += index + ": " + item + "<br>";
    }

    $(document).on('click', '.send-message', function() {
        var thiss = $(this);
        var data = new FormData();
        var task_id = $(this).data('taskid');
        var message = $(this).closest('tr').find('.quick-message-field').val();
        var mesArr = $(this).closest('tr').find('.quick-message-field');
        $.each(mesArr, function(index, value) {
            if ($(value).val()) {
                message = $(value).val();
            }
        });

        data.append("task_id", task_id);
        data.append("message", message);
        data.append("status", 1);

        if (message.length > 0) {
            if (!$(thiss).is(':disabled')) {
                $.ajax({
                    url: '/whatsapp/sendMessage/task',
                    type: 'POST',
                    "dataType": 'json', // what to expect back from the PHP script, if anything
                    "cache": false,
                    "contentType": false,
                    "processData": false,
                    "data": data,
                    beforeSend: function() {
                        $(thiss).attr('disabled', true);
                    }
                }).done(function(response) {
                    thiss.closest('tr').find('.quick-message-field').val('');

                    toastr["success"]("Message successfully send!", "Message")
                    // $.post( "/whatsapp/approve/customer", { messageId: response.message.id })
                    //   .done(function( data ) {
                    //
                    //   }).fail(function(response) {
                    //     alert(response.responseJSON.message);
                    //   });

                    $(thiss).attr('disabled', false);
                }).fail(function(errObj) {
                    $(thiss).attr('disabled', false);
                    toastr["error"]("Could not send message", "error");
                });
            }
        } else {
            alert('Please enter a message first');
            toastr["error"]("Please enter a message first", "error");
        }
    });

    $(document).on('click', '.preview-img', function(e) {
        e.preventDefault();
        id = $(this).data('id');
        if (!id) {
            toastr["error"]("No data found", "error");
            return; 
        }
        $.ajax({
            url: "/site-development/preview-img-task/" + id,
            type: 'GET',
            success: function(response) {
                $("#preview-task-image").modal("show");
                $(".task-image-list-view").html(response);
                initialize_select2()
            },
            error: function() {}
        });
    });

    $(document).on("click", ".delete-dev-task-btn", function() {
        var x = window.confirm("Are you sure you want to delete this ?");
        if (!x) {
            return;
        }
        var $this = $(this);
        var taskId = $this.data("id");
        var tasktype = $this.data("task-type");
        if (taskId > 0) {
            $.ajax({
                beforeSend: function() {
                    $("#loading-image").show();
                },
                type: 'get',
                url: "/site-development/deletedevtask",
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: taskId,
                    tasktype: tasktype
                },
                dataType: "json"
            }).done(function(response) {
                $("#loading-image").hide();
                if (response.code == 200) {
                    $this.closest("tr").remove();
                }
            }).fail(function(response) {
                $("#loading-image").hide();
                alert('Could not update!!');
            });
        }

    });


    //START - Purpose : Show / Hide Chat & Remarks - #DEVTASK-19918
    $(document).on('click', '.expand-row-msg', function() {
        var id = $(this).data('id');
        var full = '.expand-row-msg .td-full-container-' + id;
        var mini = '.expand-row-msg .td-mini-container-' + id;
        $(full).toggleClass('hidden');
        $(mini).toggleClass('hidden');
    });

    $(document).on('click', '.expand-row-msg-chat', function() {
        var id = $(this).data('id');
        var full = '.expand-row-msg-chat .td-full-chat-container-' + id;
        var mini = '.expand-row-msg-chat .td-mini-chat-container-' + id;
        $(full).toggleClass('hidden');
        $(mini).toggleClass('hidden');
    });
    //END - #DEVTASK-19918
    //START - Load More functionality
    var isLoading = false;
    var page = 1;
    $(document).ready(function() {

        $(window).scroll(function() {
            if (($(window).scrollTop() + $(window).outerHeight()) >= ($(document).height() - 2500)) {
                // loadMore();
            }
        });

        function loadMore() {
            if (isLoading)
                return;
            isLoading = true;
            status = $("#enter-status").val();
            keyword = $("#enter-keyword").val();
            var $loader = $('.infinite-scroll-products-loader');
            page = page + 1;
            $.ajax({
                url: "/site-development/{{ isset($website) ? $website->id : 'all' }}?page=" +
                    page + "&k=" + keyword + "&status=" + status,
                type: 'GET',
                data: $('.handle-search').serialize(),
                beforeSend: function() {
                    $loader.show();
                },
                success: function(data) {
                    $loader.hide();

                    $('.infinite-scroll-pending-inner').append(data.tbody);
                    isLoading = false;
                    if (data.tbody == "") {
                        isLoading = true;
                    }
                },
                error: function() {
                    $loader.hide();
                    isLoading = false;
                }
            });
        }
    });
    //End load more functionality

    $("#order_query").change(function() {
        var url = window.location.href;
        if (url.indexOf('?order=') != -1) {
            var new_url = removeParam('order', url);
            window.location = new_url + '?order=' + $(this).val();
        } else if (url.indexOf('&order=') != -1) {
            var new_url = removeParam('order', url);
            window.location = new_url + '&order=' + $(this).val();
        } else {
            if (url.indexOf('?') != -1) {
                window.location = window.location.href + '&order=' + $(this).val();
            } else {
                window.location = window.location.href + '?order=' + $(this).val();
            }

        }

    });

    function removeParam(key, sourceURL) {
        var rtn = sourceURL.split("?")[0],
            param,
            params_arr = [],
            queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
        if (queryString !== "") {
            params_arr = queryString.split("&");
            for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                param = params_arr[i].split("=")[0];
                if (param === key) {
                    params_arr.splice(i, 1);
                }
            }
            if (params_arr.length) rtn = rtn + "?" + params_arr.join("&");
        }
        return rtn;
    }

    function checkAsset(category_id, site_development_id) {
        category_modal = 'site-asset-modal-' + category_id;
        category_modal_body = 'site-asset-body-' + category_id;
        $.ajax({
            url: "{{ route('site-development.check-site-asset') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                siteDevelopmentId: site_development_id,
                "_token": "{{ csrf_token() }}",
                categoryId: category_id
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
        }).done(function(data) {
            $("#loading-image").hide();
            if (data.code == 200) {
                body_data = '';
                if (data.status == 0) {
                    body_data += "<div class='alert alert-danger' role='alert'>Site Asset is not set.</div>";
                } else {
                    body_data += "<div class='alert alert-success' role='alert'>Site Asset is set.</div>";
                }
                body_data +=
                    "<div class='row'><div class='col-md-4'> Create Site Asset </div><div class='col-md-8'><select class='form-control' id='is_site_asset_" +
                    category_id + "'><option value='0' ";
                if (data.status == 0) {
                    body_data += " selected ";
                }
                body_data += "> No </option><option value='1'  ";
                if (data.status == 1) {
                    body_data += " selected ";
                }
                body_data += "> Yes </option></select></div></div>";
                $("#" + category_modal_body).html(body_data);
                $("#" + category_modal).modal('show');
            } else {
                alert(data.status);
            }

        }).fail(function(data) {
            $("#loading-image").hide();
        });
    }

    function checkList(category_id, site_development_id) {
        category_modal = 'site-check-modal-' + category_id;
        category_modal_body = 'site-check-body-' + category_id;
        $.ajax({
            url: "{{ route('site-development.check-site-list') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                siteDevelopmentId: site_development_id,
                "_token": "{{ csrf_token() }}",
                categoryId: category_id
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
        }).done(function(data) {
            $("#loading-image").hide();
            if (data.code == 200) {
                body_data = '';
                if (data.status == 0) {
                    body_data +=
                        "<div class='alert alert-danger' role='alert'>Site check list is not set.</div>";
                } else {
                    body_data += "<div class='alert alert-success' role='alert'>Site check list is set.</div>";
                }
                body_data +=
                    "<div class='row'><div class='col-md-4'> Create Site checklist </div><div class='col-md-8'><select class='form-control' id='is_site_check_" +
                    category_id + "'><option value='0' ";
                if (data.status == 0) {
                    body_data += " selected ";
                }
                body_data += "> No </option><option value='1'  ";
                if (data.status == 1) {
                    body_data += " selected ";
                }
                body_data += "> Yes </option></select></div></div>";
                $("#" + category_modal_body).html(body_data);
                $("#" + category_modal).modal('show');
            } else {
                alert(data.status);
            }

        }).fail(function(data) {
            $("#loading-image").hide();
        });
    }

    function checkUi(category_id, site_development_id) {
        //debugger;
        category_modal = 'ui-check-modal-' + category_id;
        category_modal_body = 'ui-check-body-' + category_id;
        $.ajax({
            url: "{{ route('site-development.check-ui') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                siteDevelopmentId: site_development_id,
                "_token": "{{ csrf_token() }}",
                categoryId: category_id
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
        }).done(function(data) {
            $("#loading-image").hide();
            if (data.code == 200) {
                body_data = '';
                if (data.status == 0) {
                    body_data += "<div class='alert alert-danger' role='alert'>Site  Check Ui is not set.</div>";
                } else {
                    body_data += "<div class='alert alert-success' role='alert'>Site Check Ui is set.</div>";
                }
                body_data +=
                    "<div class='row'><div class='col-md-4'> Create Site Ui Check </div><div class='col-md-8'><select class='form-control' id='is_ui_" +
                    category_id + "'><option value='0' ";
                if (data.status == 0) {
                    body_data += " selected ";
                }
                body_data += "> No </option><option value='1'  ";
                if (data.status == 1) {
                    body_data += " selected ";
                }
                body_data += "> Yes </option></select></div></div>";
                $("#" + category_modal_body).html(body_data);
                $("#" + category_modal).modal('show');
            } else {
                alert(data.status);
            }

        }).fail(function(data) {
            $("#loading-image").hide();
        });
    }

    function setCheckUi(category_id, site_development_id) {
        //debugger;
        category_modal = 'ui-check-modal-' + category_id;
        is_ui = $('#is_ui_' + category_id).val();
        if (is_ui == '') {
            alert("Select Yes or No");
            return;
        }
        $.ajax({
            url: "{{ route('site-development.set-check-ui') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                siteDevelopmentId: site_development_id,
                "_token": "{{ csrf_token() }}",
                categoryId: category_id,
                status: is_ui
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
        }).done(function(data) {
            $("#loading-image").hide();
            if (data.code == 200) {
                $("#" + category_modal).modal('hide');
                alert(data.status);
            }

        }).fail(function(data) {
            $("#loading-image").hide();
        });
    }

    function setSiteAsset(category_id, site_development_id) {
        category_modal = 'site-asset-modal-' + category_id;
        site_asset = $('#is_site_asset_' + category_id).val();
        if (site_asset == '') {
            alert("Select Yes or No");
            return;
        }
        $.ajax({
            url: "{{ route('site-development.set-site-asset') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                siteDevelopmentId: site_development_id,
                "_token": "{{ csrf_token() }}",
                categoryId: category_id,
                status: site_asset
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
        }).done(function(data) {
            $("#loading-image").hide();
            if (data.code == 200) {
                $("#" + category_modal).modal('hide');
                alert(data.status);
            }

        }).fail(function(data) {
            $("#loading-image").hide();
        });
    }

    function setSiteList(category_id, site_development_id) {
        category_modal = 'site-ist-modal-' + category_id;
        site_list = $('#is_site_check_' + category_id).val();
        if (site_list == '') {
            alert("Select Yes or No");
            return;
        }
        $.ajax({
            url: "{{ route('site-development.set-site-list') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                siteDevelopmentId: site_development_id,
                "_token": "{{ csrf_token() }}",
                categoryId: category_id,
                status: site_list
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
        }).done(function(data) {
            $("#loading-image").hide();
            if (data.code == 200) {
                $("#" + category_modal).modal('hide');
                alert(data.status);
            }

        }).fail(function(data) {
            $("#loading-image").hide();
        });
    }

    $(document).on('click', '.save-document-as', function(e) {
        e.preventDefault();
        id = $(this).data('id');
        if (!id) {
            alert("No Media data found");
            return;
        }
        $("#site_asset_media_id").val(id);
        $("#save-document-in-site-asset").modal("show");
    });


    $(document).on('click', '.save-document-site-asset-btn', function(e) {
        e.preventDefault();
        $(".save-document-site-asset-btn").prop("disabled", true);
        media_id = $("#site_asset_media_id").val();
        category_id = $("#site-development-category-id").val();
        site_id = $("#website_id_data").val();
        if (!media_id) {
            alert("No Media data found");
            return;
        }
        media_type = $("#media_type").val();
        if (!media_type) {
            alert("Please select media type");
            return;
        }
        $.ajax({
            url: "{{ route('site-development.save-site-asset-data') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                siteDevelopmentId: site_id,
                "_token": "{{ csrf_token() }}",
                categoryId: category_id,
                media_id: media_id,
                media_type: media_type
            },
            beforeSend: function() {
                $("#loading-image").show();
            },
        }).done(function(data) {
            $("#loading-image").hide();
            if (data.code == 200) {
                $("#save-document-in-site-asset").modal("hide");
            }
            alert(data.status);
            $(".save-document-site-asset-btn").prop("disabled", false);
        }).fail(function(data) {
            $("#loading-image").hide();
            $(".save-document-site-asset-btn").prop("disabled", false);
        });
    });
</script>
@endsection