@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
@section('link-css')
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
	<style type="text/css">
		.preview-category input.form-control {
			width: auto;
		}
		.daterangepicker .ranges li.active {
			background-color : #08c !important;
		}
	</style>
@endsection

<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
		<h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
	</div>
	<br>
	@include('partials.flash_messages', ['extraDiv' => true])
	<div class="col-lg-12 p-5 margin-tb"style="margin-top: -16px;">
		<div class="row">
			<div class="col col-md-1">
				<div class="row">
					<a href="/attachImages/newsletters">
						<button style="display: inline-block;width: 10%;  margin-top: 0px;" class="btn btn-sm btn-image">
							<img src="/images/attach.png" style="cursor: default;">
						</button>
					</a>
				</div>
			</div>
			<div class="col">
				<div class="h">
					<form class="form-inline message-search-handler mb-5 fr" method="post">
						<div class="row">
							<div class="form-group" style="margin-left:20px">
								
								{{ html()->text("keyword", request("keyword"))->class("form-control newsletwrp")->placeholder("Enter Product Id ") }}
							</div>
							<div class="form-group" style="margin-left: 20px">
								
								{{ html()->input('date', "date_from", request("date_from"))->class("form-control newsletwrp")->placeholder("From date") }}
							</div>
							<div class="form-group" style="margin-left: 20px">
								{{ html()->input('date', "date_to", request("date_to"))->class("form-control newsletwrp")->placeholder("To date") }}
							</div>
							<div class="form-group" style="margin-left: 20px">
								
								{{ html()->input('date', "send_at", request("send_at"))->class("form-control newsletwrp")->placeholder("Send at") }}
							</div>
							<div class="form-group" style="margin-left:20px">
								
								<button style="display: inline-block;width:10%; margin-top: 1px;" class="btn btn-sm btn-image btn-search-action">
									<img src="/images/search.png" style="cursor: default;">
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="col-md-12 pt-2 pb-2">
				<button class="btn btn-secondary get-multi-score-btn">Get Score</button>
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
@include("newsletter.templates.list-template")
@include("newsletter.templates.create-website-template")
@include("newsletter.templates.update-time")
@include('newsletter.partials.add-status-modals')
<script type="text/javascript" src="{{ asset('/js/jsrender.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('/js/jquery-ui.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/newsletters.js') }} "></script>

<script type="text/javascript">

	$(document).ready(function() {
		setTimeout(() => {
			page.init({
				bodyView: $("#common-page-layout"),
				baseUrl: "<?php echo url('/'); ?>"
			});
		}, 2000);
	});

	$(document).ready(function(){
		$('#store_id').on("click", function(){
			$('.btn-push-icon').attr('data-attr', $(this).val());
		});
	});
</script>

@endsection

