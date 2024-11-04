@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
	<div class="row" id="common-page-layout">
		<div class="col-lg-12 margin-tb">
			<h2 class="page-heading">{{$title}} <span>({{$records_count}})</span></h2>
		</div>
		<br>
		<!-- <div class="col-lg-12 margin-tb">
			<div class="col col-md-4">

				<form class="form-inline message-search-handler" method="get">
					<div class="form-group" style="width: 300px;margin-bottom: 0px;">
						<input name="keyword" type="text" class="form-control" placeholder="Search Keywords" id="keyword" data-allow-clear="true" style=" width: 100%;"/>
					</div>

					<div class="form-group" >
						<button type="submit" style="display: inline-block;width: 10%;margin-top: 1px;" class="btn btn-sm btn-image btn-search-action">
							<img src="/images/search.png" style="cursor: default;">
						</button>
						<a href="/appointment-request" class="btn btn-image" style="margin-top: 1px;">
							<img src="/images/resend2.png" style="cursor: nwse-resize;">
						</a>
					</div>
				</form>
			</div>
		</div> -->
		<div class="col-lg-12 margin-tb">
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

	<div id="requested-remarks-list" class="modal fade" role="dialog">
	    <div class="modal-dialog modal-xl">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h4 class="modal-title">Appointment Requested Remarks</h4>
	                <button type="button" class="close" data-dismiss="modal">×</button>
	            </div>
	            <div class="modal-body">
	                <div class="col-md-12">
	                    <div class="requested-remarks-view"></div>
	                </div>
	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	            </div>
	        </div>
	    </div>
	</div>

	<div id="requested-decline_remarks-list" class="modal fade" role="dialog">
	    <div class="modal-dialog modal-xl">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h4 class="modal-title">Appointment Decline Remarks</h4>
	                <button type="button" class="close" data-dismiss="modal">×</button>
	            </div>
	            <div class="modal-body">
	                <div class="col-md-12">
	                    <div class="requested-decline_remarks-view"></div>
	                </div>
	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	            </div>
	        </div>
	    </div>
	</div>

	@include("appointment-request.templates.list-template")
    
	<script>
		@if($errors->any())
			@php
				$error = $errors->all()
			@endphp
			toastr["error"]("{{$error[0] ?? 'Something went wrong.'}}");
		@endif
			
		@if ($message = Session::get('success'))
			toastr["success"]("{{$message}}");
		@endif
		@if ($message = Session::get('error'))
			toastr["error"]("{{$message}}");
		@endif
	
		var page_script_document = 0; 
		var total_limit_script_document = 19;
		var action_script_document = 'inactive';

	</script>
	<script type="text/javascript" src="{{ mix('webpack-dist/js/jsrender.min.js') }} "></script>
	<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.validate.min.js') }} "></script>
	<script type="text/javascript" src="{{ mix('webpack-dist/js/jquery-ui.js') }} "></script>
	<script type="text/javascript" src="{{ asset('js/common-helper.js') }} "></script>
	<script type="text/javascript" src="{{ mix('webpack-dist/js/appointment-request.js') }} "></script>
	
	<script type="text/javascript">
		page.init({
			bodyView: $("#common-page-layout"),
			baseUrl: "{{ url("/") }}"
		});
	    $(document).ready(function () {
	        $('.js-example-basic-multiple').select2();
	  	})
	</script>
	<script type="text/javascript">	
		$(document).ready(function() {
			$("body").tooltip({ selector: '[data-toggle=tooltip]' });
		});

		$('.change_assign_to_top').select2({
				width: "150px",
				placeholder: 'Select Assign To'
		});
		
		$(document).on('click', '.expand-row-msg-chat', function() {
			var id = $(this).data('id');
			var full = '.expand-row-msg-chat .td-full-container-' + id;
			var mini = '.expand-row-msg-chat .td-mini-container-' + id;
			$(full).toggleClass('hidden');
			$(mini).toggleClass('hidden');
		});

		$( document ).ready(function() {
			$(document).on('click', '.expand-row,.expand-row-msg', function () {
				var selection = window.getSelection();
				if (selection.toString().length === 0) {
					$(this).find('.td-mini-container').toggleClass('hidden');
					$(this).find('.td-full-container').toggleClass('hidden');
				}
			});
		});
	
	
		// Script Document  ajax starts
		function GetParameterValues(param) {  
			var url = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');  
			for (var i = 0; i < url.length; i++) {  
				var urlparam = url[i].split('=');  
				if (urlparam[0] == param) {  
					return urlparam[1];  
				}  
			}  
		}
		
		$(window).scroll(function(){
			let urlString_script_document = window.location.href;
			let paramString_script_document = urlString_script_document.split('?')[1];
			let queryString_script_document = new URLSearchParams(paramString_script_document);
			var arr = 0;
			for(let pair of queryString_script_document.entries()) {
				arr = 1;
			}
			
			if(arr==0) {
				if($(window).scrollTop() + $(window).height() > $("#page-view-result").height() && action_script_document == 'inactive'){
					
					action_script_document = 'active';
					page_script_document++;				   
					setTimeout(function(){						
						load_more(page_script_document);
					}, 1000);
				}
			}
		});
		
		function load_more(page_script_document){	
			$.ajax({
				url: "/appointment-request/record-appointment-request-ajax?page="+page_script_document+"&"+$(".message-search-handler").serialize(),
				type: "get",
				datatype: "html",			  
				beforeSend: function()
				{
					$('#loading-image-preview').css("display","block");
				}
			})
			.done(function(data)
			{
				$('#loading-image-preview').css("display","none");
				
				if(data.length == 0){
					//notify user if nothing to load
					action_script_document = "inactive";
					//$('.ajax-loading').html("No more records!");
					page_script_document = 0;
					return;
				}
				$('.loading-image-preview').hide(); //hide loading animation once data is received
				$('#loading-image-preview').css("display","none");			  
				$('#script_document_maintable > tbody:last').append(data); 
				action_script_document = "inactive";			   
		   })
		   .fail(function(jqXHR, ajaxOptions, thrownError)
		   {
			  alert('No response from server');
		   });
		}

		$(window).on('load', function() {
			$( "th" ).resizable();
		});
		
		var uriv = window.location.href.toString();
		if (uriv.indexOf("?") > 0) {
			var clean_uri = uriv.substring(0, uriv.indexOf("?"));
			$('#script_document-id-search').val("");
			window.history.replaceState({}, document.title, clean_uri);
		}

	    $(document).on('input', '#searchInput', function(e) {
	        e.preventDefault();
	        const searchInput = $(this).val();
	        const urlInputs = $(".urlInput");

	        urlInputs.each(function() {
	            const urlInput = $(this);
	            const url = urlInput.val();
	            if (url.includes(searchInput)) {
	                urlInput.removeClass("hidden");
	            } else {
	                urlInput.addClass("hidden");
	            }
	        });
	    });

	    $(document).on('click','.requested-remarks-view',function(){
	        id = $(this).data('id');
			$.ajax({
	            method: "GET",
	            url: `{{ route('appointment-request.remarks', [""]) }}/` + id,
	            dataType: "json",
	            success: function(response) {
                    $("#requested-remarks-list").find(".requested-remarks-view").html(response.data.remarks);
                    $("#requested-remarks-list").modal("show");
	            }
	        });
		});

		$(document).on('click','.decline_remarks-view',function(){
	        id = $(this).data('id');
			$.ajax({
	            method: "GET",
	            url: `{{ route('appointment-request.remarks', [""]) }}/` + id,
	            dataType: "json",
	            success: function(response) {
                    $("#requested-decline_remarks-list").find(".requested-decline_remarks-view").html(response.data.decline_remarks);
                    $("#requested-decline_remarks-list").modal("show");
	            }
	        });
		});
	</script>
@endsection