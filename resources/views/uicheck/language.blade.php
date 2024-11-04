@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Ui Check')

@section('styles')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<style type="text/css">
	.preview-category input.form-control {
		width: auto;
	}

	#loading-image {
		position: fixed;
		top: 50%;
		left: 50%;
		margin: -50px 0px 0px -50px;
	}

	.dis-none {
		display: none;
	}

	.pd-5 {
		padding: 3px;
	}

	.toggle.btn {
		min-height: 25px;
	}

	.toggle-group .btn {
		padding: 2px 12px;
	}

	.latest-remarks-list-view tr td {
		padding: 3px !important;
	}

	#latest-remarks-modal .modal-dialog {
		max-width: 1100px;
		width: 100%;
	}

	.btn-secondary {
		border: 1px solid #ddd;
		color: #757575;
		background-color: #fff !important;
	}

	.modal {
		overflow-y: auto;
	}

	body.overflow-hidden {
		overflow: hidden;
	}

	span.user_point_none button,
	span.admin_point_none button {
		pointer-events: none;
		cursor: not-allowed;
	}

	table tr:last-child td {
		border-bottom: 1px solid #ddd !important;
	}

	select.globalSelect2+span.select2 {
		width: calc(100% - 26px) !important;
	}
	.table {
		width: 3000px;
		max-width: 3000px;
		margin-bottom: 20px;
	}
</style>
@endsection

@section('large_content')

<div id="myDiv">
	<img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
</div>

<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
		<h2 class="page-heading">Ui Check Languages ({{$uiLanguages->total()}})</h2>
	</div>

</div>

@if (Session::has('message'))
{{ Session::get('message') }}
@endif
<br />
<div class="col-lg-12 margin-tb">
	<div class="row">
		<div class="col-md-12">
			<form>
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<input type="text" name="id" id="id" class="form-control" value="{{request('id')}}" placeholder="Please Enter Uicheck Id" />
						</div>
					</div>
					
					<div class="col-md-2">
						<div class="form-group">
							<?php
                                if (request('categories')) {
                                    $categoriesArr = request('categories');
                                } else {
                                    $categoriesArr = '';
                                }
							?>
							<select name="categories" id="store-categories" class="form-control select2">
								<option value="" @if($categoriesArr=='') selected @endif>-- Select a categories --</option>
								@forelse($site_development_categories as $ctId => $ctName)
								<option value="{{ $ctId }}" @if($categoriesArr==$ctId) selected @endif>{!! $ctName !!}</option>
								@empty
								@endforelse
							</select>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<?php
							  if (request('user_name')) {
							      $userNameArr = request('user_name');
							  } else {
							      $userNameArr = [];
							  }
							?>
							<select name="user_name[]" id="user_name" class="form-control select2" multiple>
								<option value="" @if($userNameArr=='') selected @endif>-- Select a User --</option>
								@forelse($allUsers as $uId => $uName)
								<option value="{{ $uName->id }}" @if(in_array($uName->id, $userNameArr)) selected @endif>{!! $uName->name !!}</option>
								@empty
								@endforelse
							</select>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<?php
							    if (request('status')) {
							        $statusArr = request('status');
							    } else {
							        $statusArr = '';
							    }
							?>
							<select name="status" id="status" class="form-control select2">
								<option value="" @if($statusArr=='') selected @endif>-- Status --</option>
								@forelse($allStatus as $key => $as)
								<option value="{{ $key }}" @if($statusArr==$key) selected @endif>{{ $as }}</option>
								@empty
								@endforelse
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<button type="submit" class="btn btn btn-image custom-filter"><img src="/images/filter.png" style="cursor: nwse-resize;"></button>
						<a href="{{route('uicheck.translation')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="row mt-2">
	<div class="table-responsive mt-2">
		<div class=" mt-2" style="overflow-x: scroll !important;">
			<table class="table table-bordered text-nowrap" id="uicheck_table1">
				<thead>
					<tr>					
						<th style="width:100px;overflow-wrap: anywhere;">Ui Check ID</th>
						<th style="width:100px;overflow-wrap: anywhere;">Categories</th>
						<th style="width:100px;overflow-wrap: anywhere;">User Name</th>
						<th style="width:100px;overflow-wrap: anywhere;">Website</th>
						@foreach ($languages as $language)
							<th  style="width:155px;overflow-wrap: anywhere;">{{$language->name}}</th>
						@endforeach
						<th style="width:150px;">Change Status</th>
						
					</tr>
				</thead>
				<tbody>
					
					@foreach ($uiLanguages as $uiLanguage)
						<tr>
							<td>{{$uiLanguage->uicheck_id}}</td>
							<td class="expand-row-msg" data-name="title" data-id="{{$uiLanguage->id}}">
								<span class="show-short-title-{{$uiLanguage->id}}">@if($uiLanguage->title != '') {{ Str::limit($uiLanguage->title, 5, '..')}} @else   @endif</span>
								<span style="word-break:break-all;" class="show-full-title-{{$uiLanguage->id}} hidden">@if($uiLanguage->title != '') {{$uiLanguage->title}} @else   @endif</span>
							</td>
							<td class="expand-row-msg" data-name="username" data-id="{{$uiLanguage->id}}">
								<span class="show-short-username-{{$uiLanguage->id}}">@if($uiLanguage->username != '') {{ Str::limit($uiLanguage->username, 5, '..')}} @else   @endif</span>
								<span style="word-break:break-all;" class="show-full-username-{{$uiLanguage->id}} hidden">@if($uiLanguage->username != '') {{$uiLanguage->username}} @else   @endif</span>
							</td>
							<td class="expand-row-msg" data-name="website" data-id="{{$uiLanguage->id}}">
								<span class="show-short-website-{{$uiLanguage->id}}">@if($uiLanguage->website != '') {{ Str::limit($uiLanguage->website, 5, '..')}} @else   @endif</span>
								<span style="word-break:break-all;" class="show-full-website-{{$uiLanguage->id}} hidden">@if($uiLanguage->website != '') {{$uiLanguage->website}} @else   @endif</span>
							</td>
							
							@foreach ($languages as $language)
								<td>
									<input type="text" name="uilanmessage{{$language->id.$uiLanguage->uicheck_id}}" class="uilanmessage{{$language->id.$uiLanguage->uicheck_id}}" style="margin-top: 0px;width:70% !important;float: left;"/>
									<button class="btn pr-0 btn-xs btn-image message-language" onclick="funLanUpdate('{{$language->id}}', '{{$uiLanguage->uicheck_id}}');"><img src="/images/filled-sent.png" style="cursor: nwse-resize; width: 0px;"></button><i class="btn btn-xs fa fa-info-circle languageHistorty" onclick="funGetLanHistory('{{$language->id}}', '{{$uiLanguage->uicheck_id}}');"></i>
								</td>
							@endforeach
							<?php
							  $status = '';
							$lanid = '';
							$uiLan = \App\Helpers\DevelopmentHelper::getFirstUiLanguageByUicheckId($uiLanguage->uicheck_id);
							$languages_id = $uiLan->languages_id ?? '';
							$lanid = ($lanid) ? $lanid : $uiLan->id ?? '';
							$status = ($status) ? $status : '';
							if ($languages_id == 2) {
							    $status = $uiLan->status;
							} ?>
							<td data-id="{{$lanid }}" data-uicheck_id="{{$uiLanguage->uicheck_id }}" data-language_id="2" data-old_status="{{$status}}" >
								{{ html()->select("statuschanges", ["" => "-- None --"] + $allStatus, $status)->class("form-control statuschanges statusVal" . $uiLanguage->uicheck_id)->style("width:70% !important;float:left;") }}
								<button type="button" class="btn btn-xs btn-status-history" title="Show Status History" data-id="{{$uiLanguage->id}}" data-uicheck_id="{{$uiLanguage->uicheck_id}}" data-old_status="{{$uiLanguage->status}}" ><i class="fa fa-info-circle "></i></button>
							</td>
							
						</tr>
					@endforeach
				</tbody>
			</table>
			<div class="text-center">
				{!! $uiLanguages->appends(Request::except('page'))->links() !!}
			  </div>
		</div>
	</div>
</div>
<div id="status_history_model" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h2>Status History</h2>
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>
			<div class="modal-body" id="">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>ID</th>
								<th>User Name</th>
								<th>Old Status</th>
								<th>Status</th>
								<th>Date</th>

							</tr>
						</thead>
						<tbody class="status_history_tboday">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modalGetMessageHistory" class="modal fade" role="dialog" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Ui Language Message History fghgfhfg <i class="fa fa-copy"  data-text="Ui Language Message History">ghghghg</i></h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="col-md-12">
					<table class="table table-bordered" style="width: 100%;">
						<thead>
							<tr>
								<th width="5%">ID</th>
								<th width="8%">Update By</th>
								<th width="25%" style="word-break: break-all;">Message</th>
								<th width="15%" style="word-break: break-all;">Status</th>
								<th width="15%">Created at</th>
							</tr>
						</thead>
						<tbody>
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


@if (Auth::user()->hasRole('Admin'))
<input type="hidden" id="user-type" value="Admin">
@else
<input type="hidden" id="user-type" value="Not Admin">
@endif

@endsection


@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
	var urlUicheckGet = "{{ route('uicheck.get') }}";
	var urlUicheckHistoryDates = "{{ route('uicheck.history.dates') }}";
	var isAdmin = "{{ Auth::user()->hasRole('Admin') ? 1 : 0 }}";

	$(document).on("change", ".statuschanges", function(e) {
		e.preventDefault();
		var id = $(this).parent().data('id');
		var uicheck_id = $(this).parent().data('uicheck_id');
		var ui_language_id = $(this).parent().data('ui_language_id');
		var language_id = $(this).parent().data('language_id');
		var old_status = $(this).parent().data('old_status');

		var status = $(this).val();

		$.ajax({
			url: "{{route('uicheck.translator.status')}}",
			type: 'POST',
			data: {
				id: id,
				uicheck_id: uicheck_id,
				ui_language_id:ui_language_id,
				language_id:language_id,
				old_status : old_status,
				status: status,
				"_token": "{{ csrf_token() }}",
			},
			beforeSend: function() {
				
			},
			success: function(response) {
				if (response.code == 200) {
					//$(".statuschanges").val("");
					toastr['success'](response.message);
				} else {
					toastr['error'](response.message);
				}
			}
		}).fail(function(response) {
			toastr['error'](response.message);
		});
	});	

	function funLanUpdate(id, uicheckId) {
		siteLoader(true);
		var uicheckId = uicheckId;
		let uilanmessage = jQuery('.uilanmessage'+id+uicheckId).val();
		let uilanstatus = jQuery('.statusVal'+uicheckId).val();
		jQuery.ajax({
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			url: "/uicheck/set/language",
			type: 'POST',
			data: {
				id: id,
				uicheck_id : uicheckId,
				message : uilanmessage,
				uilanstatus : uilanstatus
			},
			beforeSend: function() {
				//jQuery("#loading-image").show();
			}
		}).done(function(response) {
			siteLoader(false);
			toastr["success"]("Message saved successfully!!!");
			//mdl.find('tbody').html(response.html);
			//mdl.modal("show");
		}).fail(function(errObj) {
			siteErrorAlert(errObj);
			toastr["error"](errObj);
			//siteLoader(false);
		});
	}

	function funGetLanHistory(id,uicheckId) {
		//siteLoader(true);
		let mdl = jQuery('#modalGetMessageHistory');
		var uicheckId = uicheckId;
		
		jQuery.ajax({
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			url: "/uicheck/get/message/history/language",
			type: 'POST',
			data: {
				id: id,
				uicheck_id : uicheckId,
			},
			beforeSend: function() {
				//jQuery("#loading-image").show();
			}
		}).done(function(response) {
			//siteLoader(false);
			//siteSuccessAlert("Listed successfully!!!");
			//$("#modalCreateLanguage").modal("hide");
			
			mdl.find('tbody').html(response.html);
			mdl.modal("show");
		}).fail(function(errObj) {
			//siteErrorAlert(errObj);
			//siteLoader(false);
			toastr["error"](errObj);
		});
	}


	$(document).on("click",".btn-status-history",function(e) {
        e.preventDefault();
        var $this = $(this);
        var id = $(this).parent().data('id');
		var language_id = $(this).parent().data('language_id');
		var uicheck_id = $(this).parent().data('uicheck_id');
		
        $.ajax({
          	url: '/uicheck/get/translator/status/history',
          	type: 'POST',
        	headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
			data: {
				id: id,
				language_id : language_id,
				uicheck_id:uicheck_id
			},
			dataType:"json",
          	beforeSend: function() {
            	$("#loading-image").show();
			}
		}).done(function (response) {
          $("#loading-image").hide();
          var html = "";
			if(response.code == 200){
				
				$.each(response.data,function(k,v){
					html += "<tr>";
					html += "<td>"+v.id+"</td>";
					html += "<td>"+v.username+"</td>";
					html += "<td><div class='form-row'>"+v.oldstatusname+"</div></td>";
					html += "<td><div class='form-row'>"+v.statusname+"</div></td>";
					html += "<td><div class='form-row'>"+v.created_at+"</div></td>";
					html += "</tr>";
				});
				$(".status_history_tboday").html(html);
				$("#status_history_model").modal("show");
			} else {
				toastr["error"](response.message);	
			}
        }).fail(function (jqXHR, ajaxOptions, thrownError) {
			console.log(jqXHR);
			toastr["error"](jqXHR.responseJSON.message);
          $("#loading-image").hide();
        });
      });

	  $(document).on("click",".link-delete-document",function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        var $this = $(this);
        if(confirm("Are you sure you want to delete records ?")) {
          $.ajax({
            url: '/uicheck/delete/attachment',
            type: 'POST',
            headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
              },
              dataType:"json",
            data: { id : id},
            beforeSend: function() {
              $("#loading-image").show();
                  }
          }).done(function (data) {
            $("#loading-image").hide();
            toastr["success"]("Document deleted successfully");
            $this.closest("tr").remove();
          }).fail(function (jqXHR, ajaxOptions, thrownError) {
            toastr["error"](jqXHR.responseJSON.message);
            $("#loading-image").hide();
          });
        }
      });

	jQuery(document).ready(function() {
		applyDateTimePicker(jQuery('.cls-start-due-date'));
	});

	$(document).on('click', '.expand-row-msg', function() {
		var name = $(this).data('name');
		var id = $(this).data('id');
		var full = '.expand-row-msg .show-short-' + name + '-' + id;
		var mini = '.expand-row-msg .show-full-' + name + '-' + id;
		$(full).toggleClass('hidden');
		$(mini).toggleClass('hidden');
	});
	$('.select2').select2();

	function copyToClipboard(text) {
		var sampleTextarea = document.createElement("textarea");
		document.body.appendChild(sampleTextarea);
		sampleTextarea.value = text; //save main text in it
		sampleTextarea.select(); //select textarea contenrs
		document.execCommand("copy");
		document.body.removeChild(sampleTextarea);
	}
	$(document).on("click", ".fa-copy", function() {
		console.log("asdasdasd");		
		var id = $(this).data("text");
		copyToClipboard(id);
		toastr['success']("Text copy successfully");
	});
</script>


@endsection