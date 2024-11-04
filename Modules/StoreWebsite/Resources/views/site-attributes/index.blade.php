@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
<style type="text/css">
.preview-category input.form-control {
    width: auto;
}

</style>

<div class="row" id="common-page-layout">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
    </div>
    <br>
    <div class="col-lg-12 margin-tb">

        <button class="btn btn-sm btn-image btn-add-action float-right attrbtnPage"
            data-toggle="modal" data-target="#colorCreateModal">
            <img src="/images/add.png" style="cursor: default;">
        </button>
    </div>
</div>
<div class="col">
    <div class="h" style="margin-bottom:10px;">
        <div class="row">
            <form class="form-inline message-search-handler" method="get">
                <div class="col">
                    <div class="form-group ml-4">
                        <label for="keyword">Store Website Title:</label>
                        {{ html()->text("keyword", request("keyword"))->class("form-control")->placeholder("Enter keyword") }}
                    </div>
                    <div class="form-group ml-4">
                        <label for="attributeKey">Attribute Key:</label>
                        {{ html()->text("attribute_key", request("attribute_key"))->class("form-control")->placeholder("Attribute Key") }}
                    </div>
                    <div class="form-group ml-4">
                        <label for="attributeVal">Attribute Value : </label>
                       {{ html()->text("attribute_val", request("attribute_val"))->class("form-control")->placeholder("Attribute Value") }}
                    </div>
                    <div class="form-group ml-4">
                        <label for="storeWebsiteId">Store Website Id:</label>
                       {{ html()->text("store_website_id", request("store_website_id"))->class("form-control")->placeholder("store webiste") }}
                    </div>

                    <div class="form-group">
                        <label for="button">&nbsp;</label>
                        <button type="submit"
                            class="btn btn-sm btn-image btn-search-action btnsearchSubmit">
                            <img src="/images/search.png" style="cursor: default;">
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-success d-none" id="alert-msg">
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
    <div class="modal-dialog" role="document">
    </div>
</div>

@include("storewebsite::site-attributes.templates.list-template")
@include("storewebsite::site-attributes.templates.create-website-template")
<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/site-attributes.js') }}"></script>

<script type="text/javascript">
page.init({
    bodyView: $("#common-page-layout"),
    baseUrl: "<?php echo url("/"); ?>"
});
</script>
@endsection
