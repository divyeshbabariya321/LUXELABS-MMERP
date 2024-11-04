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
                <form class="form-inline message-search-handler" method="get">
                    <div class="ml-2">
                        <div class="form-group">
                            <label for="button">Select Language</label>
                            {{ html()->select('language', $languagesList, request('language'))->class('form-control select-language') }}                        </div>
                    </div> 
                    <div class="ml-4">
                        <div class="form-group">
                        {{ html()->select('store_website_id', $storeWebsites, request('store_website_id'))->class('form-control selectbox')->placeholder('Select Store website') }}                        </div>
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
                        {{ html()->text('keyword', request('keyword'))->class('form-control')->placeholder('Enter keyword') }}                        </div>
                        <div class="form-group">
                            <label for="button">&nbsp;</label>
                            <button type="submit" style="display: inline-block;width: 10%; margin-top: -22px;" class="btn btn-sm btn-image btn-search-action">
                                <img src="/images/search.png" style="cursor: default;">
                            </button>
                            
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

@include("storewebsite::page.templates.review-translate-list-template")
@include("storewebsite::page.templates.review-translate-edit-website-page-template")
<script src="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/store-website-page-review-traslate.js') }}"></script>
<script type="text/javascript">
    page.init({
        bodyView : $("#common-page-layout"),
        baseUrl : "<?php echo url("/"); ?>"
    });
    $(document).on('change', '.select-language', function () {
        var lan=$(this).val()
        window.location.href = '<?php echo url("/"); ?>'+"/store-website/page/review-translate/"+lan;
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
    $( document ).ready(function() {
        $(document).on('click', '.open-page-content-modal', function () {
            var id=$(this).attr("data-id");
            $("#page-content-modal-"+id).modal('show');
        });
    });
</script>
@endsection 