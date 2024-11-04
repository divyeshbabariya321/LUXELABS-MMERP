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
				<form class="form-inline message-search-handler" method="get">
					   
					<div class="ml-3">
						<div class="form-group">
                            <label>Select Store Website :</label>
                        {{ html()->select('store_website_id[]', $storeWebsites, request('store_website_id'))->class('form-control select2-ele')->multiple() }}						
                    </div>
					</div>
					<div class="ml-3">
						<div class="form-group">
                            <label>Select Environment path :</label>
                        {{ html()->select('paths[]', $paths, request('paths'))->class('form-control select2-ele')->multiple() }}						
                    </div>
					</div>
					<div class="ml-2">
						<div class="form-group">
							<label for="button">&nbsp;</label>
							<button type="submit" style="display: inline-block;width: 10%; margin-top: -22px;" class="btn btn-sm btn-image btn-search-action">
								<img src="/images/search.png">
							</button>
							
						</div>
						<div class="form-group">
							<a href="{{route('store-website.environment.index')}}" style="; margin-top:0px;" class="btn btn-sm btn-image"><img src="/images/resend2.png"></a>
							
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
</script>
@endsection 