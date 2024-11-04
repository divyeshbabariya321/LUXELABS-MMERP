@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
<style type="text/css">
    
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

    .message-search-handler label {display: inline-block; width: 100%;; }
    .message-search-handler .form-group {width: 100% !important}
</style>
<link href="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<div class="row" id="common-page-layout">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">
            {{$title}}<span class="count-text"></span>

            <div style="float: right;">
                <button class="btn btn-secondary btn-add-action" data-toggle="modal" data-target="#colorCreateModal">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#environmentHistoryStatusCreate"> Create Status </button>
                <button class="btn btn-secondary" data-toggle="modal" data-target="#environmentHistoryStatusList"> List Status</button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#sedatatablecolumnvisibilityList">Column Visiblity</button>
            </div>
        </h2>
    </div>
    <br>
    <div class="col-lg-12 margin-tb">
        <div class="row">
            <div class="col col-md-12 ">
				<form class="form-inline message-search-handler" method="get">
					<div class="ml-2 col-md-5">
						<div class="form-group">
                            <label>Select Store Website :</label>
                            {{ html()->select('store_websites[]', $storeWebsites, request('store_websites'))->class('form-control select2-ele')->multiple() }}						</div>
					</div>
					<div class="ml-2 col-md-5">
						<div class="form-group">
                            <label>Select Environment path :</label>
                            {{ html()->select('paths[]', $paths, request('paths'))->class('form-control select2-ele')->multiple() }}							<?php //echo Form::select("paths",$paths,request("paths"),["class"=> "form-control select2-ele","placeholder" => "Select Environment path"]) ?>
						</div>
					</div>
					<div class="ml-2 col-md-1">
						<div class="form-group">
							<label for="button">&nbsp;</label>
							<button type="submit" style="width: 10%;" class="btn btn-sm btn-image btn-search-action1">
								<img src="/images/search.png">
							</button>

                            <a href="{{route('store-website.environment.matrix')}}" class="btn btn-sm btn-image"><img src="/images/resend2.png"></a>
						</div>
					</div>
				</form>
			</div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success" id="alert-msg" style="display: none;">
                    <p></p>
                </div>
            </div>
        </div>
        <div class="col-md-12 margin-tb" >
            <div class="row table-horizontal-scroll mt-3">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            @if(!empty($dynamicColumnsToShowse))
                                @if (!in_array('Env Path', $dynamicColumnsToShowse))
                                    <th>Env Path</th>
                                @endif

                                @foreach($env_store_websites as $id => $title)
                                    @if (!in_array($id, $dynamicColumnsToShowse))
                                        <th class="expand-row">
                                            <span class="td-mini-container">
                                                {{ strlen($title) > 10 ? substr($title, 0, 10).'...' :  $title }}
                                            </span>
                                            <span class="td-full-container hidden">
                                                {{$title}}
                                            </span>

                                        </td>
                                    @endif
                                @endforeach
                            @else 
                                <th>Env Path</th>
                                @foreach($env_store_websites as $id => $title)
                                    <th class="expand-row">
                                        <span class="td-mini-container">
                                            {{ strlen($title) > 10 ? substr($title, 0, 10).'...' :  $title }}
                                        </span>
                                        <span class="td-full-container hidden">
                                            {{$title}}
                                        </span>

                                    </td>
                                @endforeach
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($env_paths as $id => $paths)
                            <tr>
                                @if(!empty($dynamicColumnsToShowse))
                                    @if (!in_array('Env Path', $dynamicColumnsToShowse))
                                        <td class="expand-row">
                                            <span class="td-mini-container">
                                                {{ strlen($paths->path) > 15 ? substr($paths->path, 0, 15).'...' :  $paths->path }}
                                            </span>
                                            <span class="td-full-container hidden">
                                                {{$paths->path}}
                                            </span>
                                        </td>
                                    @endif

                                    @foreach($env_store_websites as $id => $title)
                                        @if (!in_array($id, $dynamicColumnsToShowse))
                                            <?php 
                                                $key = array_search($paths->path, array_column($environments[$id], 'path'));
                                            ?>
                                            @if($key !== false)
                                            <td class="expand-row" style="background-color: {{$environments[$id][$key]['status_color']}}">
                                                <span class="td-mini-container">
                                                    {{ strlen($environments[$id][$key]['value']) > 15 ? substr($environments[$id][$key]['value'], 0, 15).'...' :  $environments[$id][$key]['value'] }}
                                                </span>
                                                <span class="td-full-container hidden">
                                                    {{$environments[$id][$key]['value']}}
                                                </span>
                                                
                                                <br>
                                                <button type="button" title="Edit" data-id="{{$environments[$id][$key]['id']}}" class="btn btn-edit-template" style="padding: 0px 5px !important;">
                                                    <i class="fa fa-edit" aria-hidden="true"></i>
                                                </button>
                                                <button type="button" title="Update Value" data-id="{{$environments[$id][$key]['id']}}" class="btn btn-update-value" style="padding: 0px 5px !important;">
                                                    <i class="fa fa-upload" aria-hidden="true"></i>
                                                </button>
                                                <button type="button" title="History" data-id="{{$environments[$id][$key]['id']}}" class="btn btn-history" style="padding: 0px 5px !important;">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                            @else
                                            <td></td>
                                            @endif
                                        @endif
                                    @endforeach
                                @else
                                    <td class="expand-row">
                                        <span class="td-mini-container">
                                            {{ strlen($paths->path) > 15 ? substr($paths->path, 0, 15).'...' :  $paths->path }}
                                        </span>
                                        <span class="td-full-container hidden">
                                            {{$paths->path}}
                                        </span>
                                    </td>
                                    @foreach($env_store_websites as $id => $title)
                                        <?php 
                                            $key = array_search($paths->path, array_column($environments[$id], 'path'));
                                        ?>
                                        @if($key !== false)
                                            <td class="expand-row" style="background-color: {{$environments[$id][$key]['status_color']}}">
                                            <span class="td-mini-container">
                                                {{ strlen($environments[$id][$key]['value']) > 15 ? substr($environments[$id][$key]['value'], 0, 15).'...' :  $environments[$id][$key]['value'] }}
                                            </span>
                                            <span class="td-full-container hidden">
                                                {{$environments[$id][$key]['value']}}
                                            </span>
                                            
                                            <br>
                                            <button type="button" title="Edit" data-id="{{$environments[$id][$key]['id']}}" class="btn btn-edit-template" style="padding: 0px 5px !important;">
                                                <i class="fa fa-edit" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" title="Update Value" data-id="{{$environments[$id][$key]['id']}}" class="btn btn-update-value" style="padding: 0px 5px !important;">
                                                <i class="fa fa-upload" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" title="History" data-id="{{$environments[$id][$key]['id']}}" class="btn btn-history" style="padding: 0px 5px !important;">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                        @else
                                            <td></td>
                                        @endif
                                    @endforeach
                                @endif
                            </tr>
                        @endforeach
                        
                    </tbody>
                </table>
                {!! $env_paths->appends(Request::except('page'))->links() !!}
            </div>
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
    <div class="modal-dialog modal-lg" role="document" style="width: 100%;max-width: 95%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Update Environment History</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Path</th>
								<th>Value</th>
								<th>Command</th>
								<th>Job Id</th>
								<th>Status</th>
								<th>Response</th>
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

<div id="environmentHistoryStatusCreate" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="environment_history_status_create_form" class="form mb-15" >
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Create Status</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Status Name :</strong>
                        {{ html()->text('name')->placeholder('Status Name')->id('name')->class('form-control')->required() }}
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Status Color :</strong>
                        <input type="color" name="color" class="form-control"  id="color" value="" style="height:30px;padding:0px;">
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

<div id="environmentHistoryStatusList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">List Status</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('store-website.environment.updateEnvironmentHistoryStatus') }}" method="POST">
                <?php echo csrf_field(); ?>
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Status Name</b></td>
                            <td class="text-center"><b>Color Code</b></td>
                            <td class="text-center"><b>Color</b></td>
                        </tr>
                        <?php
                        foreach ($historyStatuses as $historyStatus) { ?>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;<?php echo $historyStatus->name; ?></td>
                            <td class="text-center"><?php echo $historyStatus->color; ?></td>
                            <td class="text-center"><input type="color" name="color_name[<?php echo $historyStatus->id; ?>]" class="form-control" data-id="<?php echo $historyStatus->id; ?>" id="color_name_<?php echo $historyStatus->id; ?>" value="<?php echo $historyStatus->color; ?>" style="height:30px;padding:0px;"></td>
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
@include("storewebsite::environment.templates.column-visibility-modal")
@include("storewebsite::environment.templates.list-template")
@include("storewebsite::environment.templates.create-website-template")
@include("storewebsite::environment.templates.change-value-template")
<script src="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/store-website-environment.js') }}"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$(".select2-ele").select2();
	});
    page.init({
        bodyView : $("#common-page-layout"),
        baseUrl : "<?php echo url("/"); ?>"
    });

    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });

    $(document).on('submit', '#environment_history_status_create_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("environment_history_status_create_form"));
        var button = $(this).find('[type="submit"]');
        $.ajax({
            url: '{{ route("store-website.environment.storeEnvironmentHistoryStatus") }}',
            type: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                button.html(spinner_html);
                button.prop('disabled', true);
                button.addClass('disabled');
            },
            complete: function() {
                button.html('Add');
                button.prop('disabled', false);
                button.removeClass('disabled');
            },
            success: function(response) {
                $('#environmentHistoryStatusCreate #environment_history_status_create_form').trigger('reset');
                $('#environmentHistoryStatusCreate #environment_history_status_create_form').find('.error-help-block').remove();
                $('#environmentHistoryStatusCreate #environment_history_status_create_form').find('.invalid-feedback').remove();
                $('#environmentHistoryStatusCreate #environment_history_status_create_form').find('.alert').remove();
                toastr["success"](response.message);
                location.reload();
            },
            error: function(xhr, status, error) { // if error occured
                if(xhr.status == 422){
                    var errors = JSON.parse(xhr.responseText).errors;
                    customFnErrors(self, errors);
                }
                else{
                    Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                }
            },
        });
    });
</script>
@endsection 