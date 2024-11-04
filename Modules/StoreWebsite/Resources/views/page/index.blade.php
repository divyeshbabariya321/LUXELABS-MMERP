@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
<style type="text/css">
    .preview-category input.form-control {
      width: auto;
    }
    .keyword-list {
        cursor: pointer;
        
    }
     .height-fix {
        height: 220px;
        /* display: inline-block; */
        overflow: auto;
        
    }
    textarea {
        overflow: hidden;
    }
</style>
<link href="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<div class="row" id="common-page-layout">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">{{$title}}<span class="count-text"></span></h2>
    </div>
    <br>
    <div class="col-lg-12 margin-tb">
        <div class="row">
            <div class="col col-md-12 d-flex">
             
                    <button style="display: inline-block;" class="btn ml-2 btn-sm btn-image btn-add-action" data-toggle="modal" data-target="#colorCreateModal">
                        <img src="/images/add.png" style="cursor: default;">
                    </button>
                    <button class="btn custom-button ml-2 push-by-store-website"  data-toggle="modal" data-target="#push-by-store-website-modal" style="width:133px;">Push Storewebsite</button> 
                    <button class="btn custom-button ml-2 pull-by-store-website"  data-toggle="modal" data-target="#pull-by-store-website-modal" style="width:133px;">Pull Storewebsite</button>
					<button type="button" title="Pull logs" data-id="" class="btn ml-2 custom-button btn-pullLogs" style="width:133px;">Pull Logs</button>
                    <button type="button" class="btn ml-2 custom-button" data-toggle="modal" data-target="#todolistStatusCreateModal">Add Status</a> </button>
                    <button type="button" class="btn ml-2 custom-button" data-toggle="modal" data-target="#statusList">List Status</a> </button>
                </div>
            </div>
        </div>
      
            <div class="col-lg-12 margin-tb">
                <div class="row">
                    <br>
                    <br>
                        <form class="form-inline message-search-handler" method="get"> 
                            <div class="col-lg-2">
                                <label> Search Store Website </label>
                               <?php 
                                    if(request('store_website_id')){   $store_search = request('store_website_id'); }
                                    else{ $store_search = []; }
                                    ?>
                                    <select name="store_website_id[]" id="store_website_id" class="form-control select2" multiple>
                                        <option value="" @if($store_search=='') selected @endif>-- Select a Language --</option>
                                        @forelse($storeWebsites as $id => $store)
                                        <option value="{{ $id }}" @if(in_array($id, $store_search)) selected @endif>{!! $store !!}</option>
                                        @empty
                                        @endforelse
                                    </select>
                            </div>
                            <div class="col-lg-2">
                                <label> Search Language </label>
                               <?php 
                                    if(request('language')){   $lang_search = request('language'); }
                                    else{ $lang_search = []; }
                                    ?>
                                    <select name="language[]" id="language" class="form-control select2" multiple>
                                        <option value="" @if($lang_search=='') selected @endif>-- Select a Language --</option>
                                        @forelse($languagesList as $id=>$lang)
                                        <option value="{{ $id }}" @if(in_array($id, $lang_search)) selected @endif>{!! $lang !!}</option>
                                        @empty
                                        @endforelse
                                    </select>
                            </div>
                            <div class="col-lg-2">
                                <label> Search Status </label>
                               <?php 
                                    if(request('status_id')){   $status_search = request('status_id'); }
                                    else{ $status_search = []; }
                                    ?>
                                    <select name="status_id[]" id="status_id" class="form-control select2" multiple>
                                        <option value="" @if($status_search=='') selected @endif>-- Select a Status --</option>
                                        @forelse($statuses as $id=>$stat)
                                        <option value="{{ $stat->id }}" @if(in_array($stat->id, $status_search)) selected @endif>{!! $stat->status_name !!}</option>
                                        @empty
                                        @endforelse
                                    </select>
                            </div>
                            <div class="col-lg-2">
                                <label> Search Translation Approved by </label>
                               <?php 
                                    if(request('user_id')){   $user_search = request('user_id'); }
                                    else{ $user_search = []; }
                                    ?>
                                    <select name="user_id[]" id="user_id" class="form-control select2" multiple>
                                        <option value="" @if($user_search=='') selected @endif>-- Select a User --</option>
                                        @forelse($users as $id=>$user)
                                        <option value="{{ $user->id }}" @if(in_array($user->id, $user_search)) selected @endif>{!! $user->name !!}</option>
                                        @empty
                                        @endforelse
                                    </select>
                            </div>
                            <div class="ml-2">
                                <div class="form-group">
                                    <select name="is_pushed" class="form-control">
                                        <option value="">Is Pushed</option>
                                        <option value="0">False</option>
                                        <option value="1">True</option>
                                   </select>    
                                </div>
                            </div>
                            <div class="ml-2">
                                <div class="form-group">
                                    <select name="version_pushed" class="form-control">
                                        <option value="">Latest Version Pushed</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                   </select>    
                                </div>
                            </div>
                            <div class="ml-2">
                                <div class="form-group">
                                    <select name="version_trans" class="form-control">
                                        <option value="">Version Translated</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                   </select>    
                                </div>
                            </div>
                            <div class="ml-2">
                                <div class="form-group">
                                    <br>
                                    <select name="revision_trans" class="form-control">
                                        <option value="">Review Translated</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                   </select>    
                                </div>
                            </div>
                            <div class="ml-2">
                                <br>
                                <input class="form-control" type="text" id="search_title" placeholder="Search Title" name="search_title" value="{{ (request('search_title') ?? "" ) }}">
                            </div>
                            <div class="ml-2">
                                <br>
                                <input class="form-control" type="text" id="search_url" placeholder="Search Url" name="search_url" value="{{ (request('search_url') ?? "" ) }}">
                            </div>
                            <div class="ml-2">
                                <br>
                                <input class="form-control" type="date" name="date" value="{{ (request('date') ?? "" )}}">
                            </div>
                            <div class="ml-2">
                                <br>
                                <div class="form-group">
                                {{ html()->text('keyword', request('keyword'))->class('form-control')->placeholder('Enter keyword') }}                                </div>
                                <div class="form-group">
                                    <button type="submit" style="display: inline-block;width: 10%;" class="btn btn-sm btn-image btn-search-action">
                                        <img src="/images/search.png" style="cursor: default;">
                                    </button>
                                    <a href="{{route('store-website.page.index')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                                </div>
                            </div>
                        </form>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success" id="alert-msg" style="display: none;">
                    <p></p>
                </div>
            </div>
        </div>
        <div class="col-12 pt-2">
            <button class="btn btn-secondary get-multi-score-btn">Get Score</button>
        </div>
        <div class="col-md-12 margin-tb" id="page-view-result">

        </div>
    </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
          50% 50% no-repeat;display:none;">
</div>
<div class="common-modal modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
    </div>  
</div>

<div class="preview-history-modal modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th >Id</th>
                                <th>Content</th>
								<th>URL</th>
								<th>Result</th>
								<th>Result Type</th>
                                <th>Updated By</th>
                                <th>Updated At</th>
                            </tr>
                        </thead>
                        <tbody id="preview-history-tbody">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-logs-modal modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Store Website</th>
								<th>Content</th>
								<th>URL</th>
								<th>Result Type</th>
                                <th>Updated At</th>
                            </tr>
                        </thead>
                        <tbody id="page-logs-tbody">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="todolistStatusCreateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form action="{{ route('store_website_page.status') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h4 class="modal-title">Create Status</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Name:</strong>
                        <input type="text" name="status_name" class="form-control" value="{{ old('status_name') }}">

                        @if ($errors->has('status_name'))
                            <div class="alert alert-danger">{{ $errors->first('status_name') }}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>Color:</strong>
                        <input type="color" name="status_color" class="form-control" value="{{ old('status_color') }}">
                    </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Store</button>
                </div>
            </form>
        </div>

    </div>
</div>
</div>

<div id="statusList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">List Status</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('todolist-color-update') }}" method="POST">
                <?php echo csrf_field(); ?>
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Status Name</b></td>
                            <td class="text-center"><b>Color Code</b></td>
                            <td class="text-center"><b>Color</b></td>
                        </tr>
                        <?php
                        foreach ($statuses as $status) { ?>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;<?php echo $status['status_name'] ?></td>
                            <td class="text-center"><?php echo $status['color']; ?></td>
                            <td class="text-center"><input type="color" name="color_name[<?php echo $status['id'] ?>]" class="form-control" data-id="<?php echo $status['id']; ?>" id="color_name_<?php echo  $status['id']; ?>" value="<?php echo $status['color']; ?>" style="height:30px;padding:0px;"></td>
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

<div class="preview-activities-modal modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Description</th>
                                <th>Updated By</th>
                                <th>Updated At</th>
                            </tr>
                        </thead>
                        <tbody id="preview-activities-tbody">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="push-by-store-website-modal modal" id="push-by-store-website-modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form>
                    <div class="form-row col-md-12">
                        <div class="form-group">
                            <strong>Store websites</strong>
                            {{ html()->select('store_website_id', $storeWebsites)->class('form-control push-website-store-id') }}                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary push-pages-store-wise">Push Store(s)</button>
            </div>
        </div>
    </div>
</div>

<div class="pull-by-store-website-modal modal" id="pull-by-store-website-modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form>
                    <div class="form-row col-md-12">
                        <div class="form-group">
                            <strong>Store websites</strong>
                            {{ html()->select('store_website_id', $storeWebsites)->class('form-control pull-website-store-id') }}                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary pull-pages-store-wise">Pull Store(s)</button>
            </div>
        </div>
    </div>
</div>

<div id="store-website-status-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 95%;width: 100%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Status History</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">

                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="25%">Old Status</th>
                                <th width="25%">New Status</th>
                                <th width="25%">Updated BY</th>
                                <th width="25%">Updated Date</th>
                            </tr>
                        </thead>
                        <tbody class="store-website-status-view">
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

@include("storewebsite::page.templates.list-template")
@include("storewebsite::page.templates.create-website-template")
<div class="modal-iframe">
    <div id="iframe">
        <button type="button" class="btn-close close-iframe-btn" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <iframe
                id="inlineFrameExample"
                title="Inline Frame Example">
        </iframe>
    </div>
</div>
<script src="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/store-website-page.js') }}"></script>
<script type="text/javascript">
    page.init({
        bodyView : $("#common-page-layout"),
        baseUrl : "<?php echo url("/"); ?>"
    });
    function save_platform_id(page_id) {
        var platform_id = $(this.event.target).val();
        $.ajax({
            url: '{{ route('store_website_page.store_platform_id') }}',
            method: 'PUT',
            data: {
                _token: "{{ csrf_token() }}",
                'page_id': page_id,
                'platform_id': platform_id
            }
        });
    }
	
	function openUrl(url) {
		if (url && !url.match(/^http([s]?):\/\/.*/)) {
			var urlToOpen = 'http://'+url;
		} else {
			var urlToOpen = url;
		}
		window.open(urlToOpen, '_blank');
	}

        $(document).ready(function() {
            $(".select2").select2();
            $('.common-modal').on('show.bs.modal', function () {
                $(".select-searchable").select2();
        	});
            

            $(document).on('change','.status-update', function() {
                var selectedStatusId = $(this).val(); 
                var dataId = $(this).data('id'); // Get the data-id attribute value
                $.ajax({
                    url: "{{route('store_website_status-update')}}",
                    type: 'GET', // Adjust the HTTP method as needed
                    data: {
                        statusId: selectedStatusId,
                        dataId: dataId // Include data-id in the AJAX request
                    },
                    success: function(response) { 
                        if (response.code == 200) { 
                            toastr["success"](response.message);
                            location.reload();
                        } else {
                            toastr["error"](response.message);
                        }
                    },
                    error: function(error) {
                        // Handle errors if the AJAX request fails
                        console.error(error);
                    }
                });
            });
        });

        $(document).on('click', '.status-history', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: `{{ route('store_website-status-history-list') }}`,
                dataType: "json",
                data: {
                    id:id,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.oldstatus ? v.oldstatus.status_name : ''} </td>
                                        <td> ${v.newstatus ? v.newstatus.status_name : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${new Date(v.created_at).toISOString().slice(0, 10)} </td>
                                    </tr>`;
                        });
                        $("#store-website-status-list").find(".store-website-status-view").html(html);
                        $("#store-website-status-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                    $("#loading-image").hide();
                }
            });
        });

	
</script>
@endsection 