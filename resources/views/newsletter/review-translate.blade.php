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
			<div class="col">
				<div class="h">
					<form class="form-inline message-search-handler mb-5 fr" method="post">
						<div class="row">
							<div class="form-group" style="margin-left:20px">
								<label for="button" style="justify-content: left;">Select Language</label>
								{{ html()->select("language", $languagesList, request("language"))->class("form-control select-language newsletwrp") }}
							</div>
						</div>
						
					</form>
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
@include("newsletter.templates.review-translate-list-template")
@include("newsletter.templates.review-translate-update-newsletter")
@include("newsletter.templates.update-time")
@include('newsletter.partials.add-status-modals')
<script type="text/javascript" src="{{ mix('webpack-dist/js/jsrender.min.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.validate.min.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery-ui.js') }} "></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="{{ asset('js/common-helper.js') }} "></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/newsletters.js') }} "></script>

<script type="text/javascript">

	page.init({
		bodyView : $("#common-page-layout"),
		baseUrl : "<?php echo url('/'); ?>"
	});

	$(document).ready(function(){
		$('#store_id').on("click", function(){
			$('.btn-push-icon').attr('data-attr', $(this).val());
		});
		$(document).on('change', '.select-language', function () {
				var lan=$(this).val()
				window.location.href = '<?php echo url('/'); ?>'+"/newsletters/review-translate/"+lan;
		});
	});
</script>

@endsection

