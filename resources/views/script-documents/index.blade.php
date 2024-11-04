@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
	<div class="row" id="common-page-layout">
		<div class="col-lg-12 margin-tb">
			<h2 class="page-heading">{{$title}} <span>{{$records_count}}</span></h2>
		</div>
		<br>
		<div class="col-lg-12 margin-tb">
			<div class="row">
				<div class="col col-md-4">
					<div class="row">
						<button style="display: inline-block;width: 10%;margin-left:10px;" class="btn btn-sm btn-image btn-add-action"
								data-toggle="modal" data-target="#scriptdocumentsCreateModal">
							<img src="/images/add.png" style="cursor: default;">
						</button>

						<form class="form-inline message-search-handler" method="get">
							<div class="form-group" style="width: 300px;margin-bottom: 0px;">
								<input name="keyword" type="text" class="form-control" placeholder="Search Keywords" id="keyword" data-allow-clear="true" style=" width: 100%;"/>
							</div>

							<div class="form-group" >
								<button type="submit" style="display: inline-block;width: 10%;margin-top: 1px;" class="btn btn-sm btn-image btn-search-action">
									<img src="/images/search.png" style="cursor: default;">
								</button>
								<a href="/script-documents" class="btn btn-image" style="margin-top: 1px;">
									<img src="/images/resend2.png" style="cursor: nwse-resize;">
								</a>
							</div>
						</form>
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

	@include("script-documents.templates.list-template")
    @include("script-documents.create")
    @include("script-documents.edit")
    @include('script-documents.history')
    @include('script-documents.comment')
    @include('script-documents.last-output')
	
	<div id="uploadeScriptDocumentsScreencastModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
	
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Upload Screencast/File to Google Drive</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
	
				<form action="{{ route('script-documents.upload-file') }}" method="POST" enctype="multipart/form-data">
					@csrf
					<input type="hidden" name="script_document_id" id="script_document_id">
					<div class="modal-body">						
						<div class="form-group">
							<strong>Upload File</strong>
							<input type="file" name="images[]" id="fileInput" class="form-control input-sm" placeholder="Upload File" style="height: fit-content;" multiple="multiple" required>
							@if ($errors->has('images'))
								<div class="alert alert-danger">{{$errors->first('images')}}</div>
							@endif
						</div>
						<div class="form-group">
							<strong>File Creation Date:</strong>
							<input type="date" name="file_creation_date" value="{{ old('file_creation_date') }}" class="form-control input-sm" placeholder="Drive Date" required>
						</div>						
						<div class="form-group">
								<label>Remarks:</label>
								<textarea id="remarks" name="remarks" rows="4" cols="64" value="{{ old('remarks') }}" placeholder="Remarks" required class="form-control"></textarea>
	
								@if ($errors->has('remarks'))
									<div class="alert alert-danger">{{$errors->first('remarks')}}</div>
								@endif
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-secondary">Upload</button>
					</div>
				</form>
			</div>
	
		</div>
	</div>

	<div id="displayScriptDocumentsUpload" class="modal fade" role="dialog">
		<div class="modal-dialog modal-xl">
	
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Google Drive Script Document files</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
	
				<div class="modal-body">
					<div class="table-responsive mt-3">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Filename</th>
									<th>File Creation Date</th>
									<th>URL</th>
									<th>Remarks</th>
								</tr>
							</thead>
							<tbody id="googleDriveScriptDocumentData">
								
							</tbody>
						</table>
					</div>
				 </div>
			</div>
		</div>
	</div>

	<div id="script_documentShowFullTextModel" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content ">
				<div id="add-mail-content">
					<div class="modal-content">
						<div class="modal-header">
							<h3 class="modal-title">Full text view</h3>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body script_documentmanShowFullTextBody">

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
		
	<div id="create-quick-task" class="modal fade" role="dialog">
	    <div class="modal-dialog modal-lg">
	        <div class="modal-content">
	            <form action="<?php echo route('task.create.multiple.task.shortscriptdocument'); ?>" method="post">
	                @csrf
	                <div class="modal-header">
	                    <h4 class="modal-title">Create Task</h4>
	                </div>
	                <div class="modal-body">

	                    <input class="form-control" value="56" type="hidden" name="category_id" />
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
	                        <select style="width:100%" class="form-control  " id="repository_id" name="repository_id">
	                            <option value="">-- select repository --</option>
	                            @foreach ($githubRepository as $repository)
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
	                    <!-- <div class="form-group">
	                        <label for="">Websites</label>
	                        <div class="form-group website-list row">
	                           
	                        </div>
	                    </div> -->
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
	<script type="text/javascript" src="{{ asset('/js/jsrender.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/jquery.validate.min.js') }}"></script>
	<script src="{{ asset('/js/jquery-ui.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/script-documents.js') }} "></script>
	
	<script type="text/javascript">
		$(document).ready(function() {
			setTimeout(() => {
				page.init({
					bodyView: $("#common-page-layout"),
					baseUrl: "<?php echo url('/'); ?>"
				});
			}, 2000);
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
				console.log("Key is:" + pair[0]);
				console.log("Value is:" + pair[1]);
				arr = 1;
			}
			
			if(arr==0) {
				if($(window).scrollTop() + $(window).height() > $("#page-view-result").height() && action_script_document == 'inactive'){
					
					action_script_document = 'active';
					page_script_document++;				   
					setTimeout(function(){
						console.log("coming");						
						load_more(page_script_document);
					}, 1000);
					console.log("act="+action_script_document);
				}
			}
		});
		
		function load_more(page_script_document){	
			$.ajax({
				url: "/script-documents/record-script-document-ajax?page="+page_script_document+"&"+$(".message-search-handler").serialize(),
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
					console.log("len="+data.length);
					//notify user if nothing to load
					action_script_document = "inactive";
					//$('.ajax-loading').html("No more records!");
					page_script_document = 0;
					console.log("if="+action_script_document);
					return;
				}
				$('.loading-image-preview').hide(); //hide loading animation once data is received
				$('#loading-image-preview').css("display","none");			  
				$('#script_document_maintable > tbody:last').append(data); 
				action_script_document = "inactive";
				console.log("in success="+action_script_document);			   
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

		$(document).ready(function () {
			$(document).on("click", ".upload-script_documents-files-button", function (e) {
				e.preventDefault();
				let script_document_id = $(this).data("script_document_id");
				$("#uploadeScriptDocumentsScreencastModal #script_document_id").val(script_document_id || 0);
				$("#uploadeScriptDocumentsScreencastModal").modal("show")
			});

			$(document).on("click", ".view-script_documents-files-button", function (e) {
				e.preventDefault();
				let script_document_id = $(this).data("script_document_id");
				$.ajax({
					type: "get",
					url: "{{route('script-documents.files.record')}}",
					data: {
						script_document_id
					},
					success: function (response) {
						$("#googleDriveScriptDocumentData").html(response.data);
						$("#displayScriptDocumentsUpload").modal("show")
					},
					error: function (response) {

					}
				});
			});
		});

		$(document).on('click','.script-document-history',function(){
	        id = $(this).data('id');
			$.ajax({
	            method: "GET",
	            url: `{{ route('script-documents.histories', [""]) }}/` + id,
	            dataType: "json",
	            success: function(response) {
                    var html = "";
                    $.each(response.data, function(k, v) {
						html += "<tr>";
					
						if(v.description!=null){
							html += "<td>" + v.description + "</td>";
						} else {
							html += "<td></td>";
						}

						if(v.run_time!=null){
							html += "<td>" + v.run_time + "</td>";
						} else {
							html += "<td></td>";
						}

						if(v.last_output_text!=null){
							//html += "<td>" + v.last_output_text + "</td>";

							html += '<td><button type="button" data-id="'+v.id+'" class="btn script-document-history-last_output-view" style="padding:1px 0px;"><i class="fa fa-eye" aria-hidden="true"></i></button></td>';

						} else {
							html += "<td></td>";
						}

						if(v.run_status!=null){
							html += "<td>" + v.run_status + "</td>";
						} else {
							html += "<td></td>";
						}

						if(v.created_at!=null){
							html += "<td>" + v.created_at + "</td>";
						} else {
							html += "<td></td>";
						}

						html += "</tr>";
                    });

                    $("#script-document-histories-list").find(".script-document-list-view").html(html);
                    $("#script-document-histories-list").modal("show");	                
	            }
	        });
		});

		$(document).on('click','.script-document-comment-view',function(){
	        id = $(this).data('id');
			$.ajax({
	            method: "GET",
	            url: `{{ route('script-documents.comment', [""]) }}/` + id,
	            dataType: "json",
	            success: function(response) {
	               
                    $("#script-document-comment-list").find(".script-document-comment-view").html(response.data.comments);
                    $("#script-document-comment-list").modal("show");
	         
	            }
	        });
		});

		$(document).on('click','.script-document-last_output-view',function(){
	        id = $(this).data('id');
			$.ajax({
	            method: "GET",
	            url: `{{ route('script-documents.comment', [""]) }}/` + id,
	            dataType: "json",
	            success: function(response) {
	               
                    $("#script-document-last-output-list").find(".script-document-last-output-view").html(response.last_output);
                    $("#script-document-last-output-list").modal("show");
	         
	            }
	        });
		});

		$(document).on('click','.script-document-history-last_output-view',function(){
	        id = $(this).data('id');
			$.ajax({
	            method: "GET",
	            url: `{{ route('script-documents.histroy_comment', [""]) }}/` + id,
	            dataType: "json",
	            success: function(response) {
	               
                    $("#script-document-last-output-list").find(".script-document-last-output-view").html(response.last_output);
                    $("#script-document-last-output-list").modal("show");
	         
	            }
	        });
		});

		$(document).on('click', '.expand-row-msg', function () {
			var name = $(this).data('name');
			var id = $(this).data('id');
			var full = '.expand-row-msg .show-short-'+name+'-'+id;
			var mini ='.expand-row-msg .show-full-'+name+'-'+id;
			$(full).toggleClass('hidden');
			$(mini).toggleClass('hidden');
		});

		function Showactionbtn(id){
	      $(".action-btn-tr-"+id).toggleClass('d-none')
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
	    });

	    $(document).on("click", ".create-task", function(e) {
	        e.preventDefault();
	        var form = $(this).closest("form");
	        $.ajax({
	            url: form.attr("action"),
	            type: 'POST',
	            headers: {
	              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            },
	            data: form.serialize(),
	            beforeSend: function() {
	                $(this).text('Loading...');
	                $("#loading-image").show();
	            },
	            success: function(response) {
	                $("#loading-image").hide();
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

	    $(document).on("click", ".count-dev-customer-tasks", function() {

	        var $this = $(this);
	        // var user_id = $(this).closest("tr").find(".ucfuid").val();
	        var site_id = $(this).data("id");
	        var category_id = $(this).data("category");
	        $("#site-development-category-id").val(category_id);
	        $.ajax({
	            type: 'get',
	            url: 'script-documents/countdevtask/' + site_id,
	            dataType: "json",
	            headers: {
	              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	            },
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
	                console.log(error);
	                $("#loading-image").hide();
	            }
	        });
	    

	    });

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
	                        $("#loading-image").show();
	                    }
	                }).done(function(response) {
	                    $("#loading-image").hide();
	                    thiss.closest('tr').find('.quick-message-field').val('');

	                    toastr["success"]("Message successfully send!", "Message")
	                    // $.post( "/whatsapp/approve/customer", { messageId: response.message.id })
	                    //   .done(function( data ) {
	                    //
	                    //   }).fail(function(response) {
	                    //     console.log(response);
	                    //     alert(response.responseJSON.message);
	                    //   });

	                    $(thiss).attr('disabled', false);
	                }).fail(function(errObj) {
	                    $(thiss).attr('disabled', false);

	                    alert("Could not send message");
	                    console.log(errObj);
	                });
	            }
	        } else {
	            alert('Please enter a message first');
	        }
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

	    $(document).on('click', '.expand-row-msg', function() {
	        var name = $(this).data('name');
	        var id = $(this).data('id');
	        console.log(name);
	        var full = '.expand-row-msg .show-short-' + name + '-' + id;
	        var mini = '.expand-row-msg .show-full-' + name + '-' + id;
	        $(full).toggleClass('hidden');
	        $(mini).toggleClass('hidden');
	    });

	    $(document).on('click', '.preview-img', function(e) {
	        e.preventDefault();
	        id = $(this).data('id');
	        if (!id) {
	            alert("No data found");
	            return;
	        }
	        $.ajax({
	            url: "/task/preview-img-task/" + id,
	            type: 'GET',
	            success: function(response) {
	                $("#preview-task-image").modal("show");
	                $(".task-image-list-view").html(response);
	                initialize_select2()
	            },
	            error: function() {}
	        });
	    });

	    $(document).on("click", ".send-to-sop-page", function() {
	        var id = $(this).data("id");
	        var task_id = $(this).data("media-id");

	        $.ajax({
	            url: '/task/send-sop',
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

	    $(document).on("click", ".btn-show-request-url", function () {
	        $(".add_more_urls_div").toggle();
	    });

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
	</script>
@endsection