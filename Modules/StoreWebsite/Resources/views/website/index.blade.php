@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
<style type="text/css">
	.preview-category input.form-control {
	  width: auto;
	}
    .badge-danger {
        color: #fff;
        background-color: #dc3545;
    }
    .badge-success {
        color: #fff;
        background-color: #28a745;
    }
    .change-is_price_ovveride {
        cursor: pointer;
    }
</style>

<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
    </div>
    <br>
    <div class="col-lg-12 margin-tb">
    	<div class="row">
	    	<div class="col col-md-7">
		    	<div class="row">
	    			<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-add-action" data-toggle="modal" data-target="#colorCreateModal">
		  				<img src="/images/add.png" style="cursor: default;">
		  			</button>
		  			<button style="display: inline-block;" class="btn btn-secondary btn-add-default-store m-2" data-toggle="modal" data-target="#add-default-store">
		  				Add Default Store
		  			</button>
                    <button style="display: inline-block;" class="btn btn-secondary btn-merge-group m-2" data-toggle="modal" data-target="#merge-website-modal">
                        Merge Website
                    </button>
                    <button style="display: inline-block;" class="btn btn-secondary btn-copy-websites m-2" data-toggle="modal" data-target="#copy-websites-modal">
                        Copy Website
                    </button>
                    <button style="display: inline-block;" class="btn btn-secondary btn-push-websites m-2" data-toggle="modal" data-target="#push-websites-modal">
                        Push Website
                    </button>
                    <button style="display: inline-block;" class="btn btn-secondary btn-copy-full-websites m-2" data-toggle="modal" data-target="#copy-websites-modal-struct">
                        Copy Websites
                    </button>
                    <button style="display: inline-block;" class="btn btn-secondary btn-push-websites-logs m-2">
                        Push Logs
                    </button>
				 </div>
		    </div>
		    <div class="col col-md-5">
		    	<div class="h">
					<div class="row">
		    			<form class="form-inline message-search-handler" method="get">
                            <div class="col">
                                <div class="form-group">
                                    <label for="store_website_id">Store Websites:</label>
                                    {{ html()->select('store_website_id', $storeWebsites, request('store_website_id'))->class('form-control')->placeholder('Select Store website') }}                                </div>
                                <div class="form-group">
                                    <label for="is_finished">Finished:</label>
                                    {{ html()->select('is_finished', ['' => '-- Select--', 0 => 'No', 1 => 'Yes'], request('is_finished'))->class('form-control') }}                                </div>
                            	<div class="form-group">
								    <label for="keyword">Keyword:</label>
                                    {{ html()->text('keyword', request('keyword'))->class('form-control')->placeholder('Enter keyword') }}							  	</div>
                                
							  	<div class="form-group">
							  		<label for="button">&nbsp;</label>
							  		<button type="submit" style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
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
  	<div class="modal-dialog" role="document">
  	</div>	
</div>

<div id="add-default-store" class="modal" role="dialog">
  	<div class="modal-dialog" role="document">
		<div class="modal-content">
	      <div class="modal-header">
	        <h4 class="modal-title">Add Default Store</h4>
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	      </div>
	     <div class="modal-body">
			<div class="form-row">
                <div class="form-group col-md-12">
                    <strong>Country Code</strong>
                    {{ html()->text('country_codes')->class('form-control default-store-country-code') }}                </div>
            </div>
            <div class="form-row">
          		<div class="form-group col-md-12">
            		<strong>Store websites</strong>
                    {{ html()->select('store_website_id', $storeWebsites)->class('form-control default-store-website-select') }}          		</div>
	        </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-secondary create-default-stores">Create Store</button>
        </div>
	    </div>
  	</div>
</div>

<div id="merge-website-modal" class="modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Merge Website</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <div class="form-group">
                            <strong>Group name</strong>
                            {{ html()->text('group_name')->class('form-control move-store-group-change') }}                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group">
                            <strong>Store websites</strong>
                            {{ html()->select('store_website_id', $storeWebsites)->class('form-control move-store-website-select') }}                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary move-stores">Move Store</button>
            </div>
        </div>
    </div>
</div>

<div id="copy-websites-modal" class="modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Copy Websites</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <div class="form-group">
                            <strong>Store websites</strong>
                            {{ html()->select('store_website_id', $storeWebsites)->class('form-control copy-store-websites-select') }}                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary copy-websites">Copy Store</button>
            </div>
        </div>
    </div>
</div>

<div id="copy-website-modal" class="modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Copy Website</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" name="copy_website_id" class="copy-website" id="copy-website-field">
                    <div class="form-group">
                        <div class="form-group">
                            <strong>Store websites</strong>
                            {{ html()->select('store_website_id', $storeWebsites)->class('form-control copy-store-website-select') }}                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary copy-stores">Copy Store</button>
            </div>
        </div>
    </div>
</div>

<div id="push-websites-modal" class="modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Push Website</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <div class="form-group">
                            <strong>Store websites</strong>
                            {{ html()->select('store_website_id', $storeWebsites)->class('form-control push-website-store-id') }}                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary push-stores">Push Store(s)</button>
            </div>
        </div>
    </div>
</div>

<div id="copy-websites-modal-struct" class="modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Copy Website</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <div class="form-group">
                            <strong>Store websites</strong>
                            {{ html()->select('store_website_id', $storeWebsites)->class('form-control copy-website-id') }}                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group">
                            <strong>To Store websites</strong>
                            {{ html()->select('to_store_website_id', $storeWebsites)->class('form-control to-copy-website-id') }}                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-secondary copy-website-struct">Copy Website(s)</button>
            </div>
        </div>
    </div>
</div>

<div id="push-websites-logs-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Push website logs</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="store_website_id">Websites:</label>
                            {{ html()->select('website_id', $websites, request('website_id'))->class('form-control website_id')->placeholder('Select website') }}                        </div>
                    </div>
                    <div class="col-lg-12">
                        <table id="push-websites-logs-table" class="table">
                            <thead>
                                <tr>
                                    <th width="20%" style="word-break: break-all;">Name</th>
                                    <th width="15%" style="word-break: break-all;">Type</th>
                                    <th width="30%" style="word-break: break-all;">Message</th>
                                    <th width="15%" style="word-break: break-all;">Created at</th>
                                </tr>
                            </thead>
                            <tbody id="push-websites-logs-table-tbody">
                                <!-- Table rows will be dynamically populated here -->
                            </tbody>
                        </table>
                        <!-- Pagination links -->
                        <div id="push-websites-logs-table-paginationLinks">
                            <!-- Pagination links will be dynamically populated here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@include("storewebsite::website.templates.list-template")
@include("storewebsite::website.templates.create-website-template")
@include("storewebsite::website.templates.website-push-logs-template")

<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/website.js') }}"></script>

<script type="text/javascript">
	page.init({
		bodyView : $("#common-page-layout"),
		baseUrl : "<?php echo url('/'); ?>"
	});

    $(document).on("change", ".website_id", function (e) { 
        getWebsitesLogs(1, $(this).val());
    });

    $(document).on('click','.btn-push-websites-logs',function(e){
        e.preventDefault();
        $('#push-websites-logs-modal').modal('show');
        getWebsitesLogs(1);
    });

    function getWebsitesLogs(page, storeWebsiteId) {
        if (typeof storeWebsiteId != 'undefined') {
            var url = "/store-website/websites/push-all-logs?page=" + page + "&website_id=" + storeWebsiteId
        } else {
            var url = "/store-website/websites/push-all-logs?page=" + page
        }

        $.ajax({
            type: "GET",
            url: url,
            dataType:"json",
            beforeSend:function(data){
                $('.ajax-loader').show();
            }
        }).done(function (response) {
            $('.ajax-loader').hide();
            var tableBody = $('#push-websites-logs-table-tbody');
            tableBody.empty(); // Clear the table body
            // Loop through the data and populate the table rows
            $.each(response.data, function(index, item) {
                var row = $('<tr>');
                row.append($('<td>').text(item.name));
                row.append($('<td>').text(item.type));
                row.append($('<td>').text(item.message));
                row.append($('<td>').text(item.created_at));
                // Add more table data cells as needed

                tableBody.append(row);
            });

            var paginationLinks = $('#push-websites-logs-table-paginationLinks');
            paginationLinks.empty(); // Clear the pagination links

            // Generate the pagination links manually
            var links = response.links;
            var currentPage = response.current_page;
            var lastPage = response.last_page;

            var pagination = $('<ul class="pagination"></ul>');

            // Previous page link
            if (currentPage > 1) {
                pagination.append('<li class="page-item"><a href="#" class="page-link" data-page="' + (currentPage - 1) + '">Previous</a></li>');
            }

            // Individual page links
            for (var i = 1; i <= lastPage; i++) {
                var activeClass = (i === currentPage) ? 'active' : '';
                pagination.append('<li class="page-item ' + activeClass + '"><a href="#" class="page-link" data-page="' + i + '">' + i + '</a></li>');
            }

            // Next page link
            if (currentPage < lastPage) {
                pagination.append('<li class="page-item"><a href="#" class="page-link" data-page="' + (currentPage + 1) + '">Next</a></li>');
            }

            paginationLinks.append(pagination);

            // Handle pagination link clicks
            paginationLinks.find('a').on('click', function(event) {
                event.preventDefault();
                var page = $(this).data('page');
                getWebsitesLogs(page, storeWebsiteId);
            });
        }).fail(function (response) {
            $('.ajax-loader').hide();
        });
    }
</script>
@endsection