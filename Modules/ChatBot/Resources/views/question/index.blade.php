@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Intent | Chatbot')

@section('large_content')
<style type="text/css">
	table.dataTable thead .sorting:after,
	table.dataTable thead .sorting:before,
	table.dataTable thead .sorting_asc:after,
	table.dataTable thead .sorting_asc:before,
	table.dataTable thead .sorting_asc_disabled:after,
	table.dataTable thead .sorting_asc_disabled:before,
	table.dataTable thead .sorting_desc:after,
	table.dataTable thead .sorting_desc:before,
	table.dataTable thead .sorting_desc_disabled:after,
	table.dataTable thead .sorting_desc_disabled:before {
	bottom: .5em;
	}
	.table>tbody>tr>td {
		padding:4px;
	}
	.pd-3 {
		padding: 3px;
	}
	.select2-container .select2-selection--single {
	height:33px !important;
	}
</style>
<div class="row">
	<div class="col-lg-12 margin-tb">
	    <h2 class="page-heading">Intent / entity | Chatbot</h2>
	</div>
</div>
<div class="row">
    <div class="col-lg-12 margin-tb" style="margin-bottom: 10px;padding:0px;">
    	<div class="col-md-7 pull-left">
    		<form action="" method="get">
	            <div class="row">
				    <div class="col">
				      <input type="text" name="q" value="<?php echo request('q', ''); ?>" class="form-control" placeholder="Search Entiry / Keyword">
				    </div>
				    <div class="col">
				      <select name="category_id" class="select-chatbot-category form-control"></select>
				    </div>
					<div class="col">
				      <select name="keyword_or_question" class="form-control">
					  <!-- <option value="">Select Type</option> -->
					  <option value="intent" {{request()->get('keyword_or_question') == 'intent' ? 'selected' : ''}}>Intent</option>
					  <option value="entity" {{request()->get('keyword_or_question') == 'entity' ? 'selected' : ''}}>Entity</option>
					  <option value="simple" {{request()->get('keyword_or_question') == 'simple' ? 'selected' : ''}}>Simple Text</option>
					  <option value="priority-customer" {{request()->get('keyword_or_question') == 'priority-customer' ? 'selected' : ''}}>Priority Customer</option>
					  </select>
				    </div>
					<div class="col">
				      <select name="store_website_id" class="form-control">
						<option value="">Select Website</option>
						@foreach($watson_accounts as $acc)
						<option value="{{$acc->store_website_id}}" {{request()->get('store_website_id') == $acc->store_website_id ? 'selected' : ''}}>{{$acc->storeWebsite->title}}</option>
						@endforeach
					  </select>
				    </div>
				    <div class="col">
				      <button type="submit" class="btn btn-xs text-secondary mt-2"><i class="fa fa-filter"></i></button>
				    </div>
				</div>
			</form>
        </div>
        <div class="col-md-5">
            <div class="form-inline pull-right">
                <button type="button" class="btn btn-xs btn-secondary ml-3" id="add-entity-type-btn">Entity Type</button>
                <button type="button" class="btn btn-xs btn-secondary ml-3" id="repeat-bulk-option">Repeat</button>
                <button type="button" class="btn btn-xs btn-secondary ml-3" id="create-reply-btn">Dynamic Reply</button>
                <button type="button" class="btn btn-xs btn-secondary ml-3" id="create-task-btn">Dynamic Task</button>
                <button type="button" class="btn btn-xs btn-secondary ml-3" id="create-keyword-btn">Create</button>
        	</div>
        </div>
    </div>
</div>
<div class="tab-pane">
	<div class="row">
	    <div class="col-lg-12 margin-tb">
	        <table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%" style="table-layout:fixed;">
			  <thead>
			    <tr>
					<th class="th-sm" style="width:2%">#</th>
					<th class="th-sm" style="width:5%">Id</th>
					<th class="th-sm" style="width:10%">Intent / entity</th>
					<th class="th-sm" style="width:5%">Type</th>
					<th class="th-sm" style="width:15%">User Intent / entity</th>
					<th class="th-sm" style="width:7%">Erp/Watson</th>
					<th class="th-sm" style="width:10%">Sugg Resp</th>
					<th class="th-sm" style="width:9%">Category</th>
					<th class="th-sm" style="width:9%">W-Status</th>
					<th class="th-sm" style="width:9%">G-Status</th>
					<th class="th-sm" style="width:9%">Created</th>
					<th class="th-sm" style="width:8%">Action</th>
			    </tr>
			  </thead>
			  <tbody>
			    <?php foreach ($chatQuestions as $chatQuestion) { ?>
				    <tr>
						<td>
							<input type="checkbox" value="{{ $chatQuestion->id }}" class="chatbotQuestion_chkbox">
						</td>
				      	<td>
							<?php echo $chatQuestion->id; ?>
						</td>
						<td class="expand-row-msg" data-name="value" data-id="{{$chatQuestion->id}}">
							<span class="show-short-value-{{$chatQuestion->id}}">{{ Str::limit($chatQuestion->value, 15, '..')}}</span>
							<span style="word-break:break-all;" class="show-full-value-{{$chatQuestion->id}} hidden">{{$chatQuestion->value	}}</span>
						</td>
						<td>
							<?php echo $chatQuestion->keyword_or_question; ?>
						</td>
						<?php
                            $listOfQuestions = explode(',', $chatQuestion->questions);
			        ?>
						<td class="expand-row-msg" data-name="listOfQuestions" data-id="{{$chatQuestion->id}}">
							<?php
			            //echo implode("</br>",$listOfQuestions);
			            if (count($listOfQuestions) > 0) {
			                $listOfQuestion = $listOfQuestions[0];
			                ?>
								<span class="show-short-listOfQuestions-{{$chatQuestion->id}}">{{ Str::limit($listOfQuestion, 18, '..')}}</span>
                  				<span style="word-break:break-all;" class="show-full-listOfQuestions-{{$chatQuestion->id}} hidden">{{$listOfQuestion}}</span>
								<button type="button" class="btn btn-xs text-secondary" data-toggle="modal" data-target="#listOfQuestion_{{$chatQuestion->id}}"><i class="fa fa-info-circle"></i></button>
								<div class="modal fade" id="listOfQuestion_{{$chatQuestion->id}}" tabindex="-1" role="dialog" aria-labelledby="listOfQuestion_{{$chatQuestion->id}}" aria-hidden="true">
									<div class="modal-dialog modal-lg" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="listOfQuestion_{{$chatQuestion->id}}">User Intent / entity</h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body">
												<ul class="list-group">
												<?php
			                                //echo implode("</br>",$listOfQuestions);
			                                foreach ($chatQuestion->chatbotQuestionExamples as $key => $value) {
			                                    ?>
													<li class="list-group-item d-flex justify-content-between align-items-center">
													<?php echo $value->question; ?>
													<a data-id="<?php echo $value->id; ?>" href="<?php echo route('chatbot.question-example.delete', [$chatQuestion->id, $value->id]); ?>" class="btn btn-xs text-secondary"><i class="fa fa-trash"></i></a>
													</li>
												<?php
			                                }
			                ?>
												</ul>
											</div>
										</div>
									</div>
								</div>
								<?php
			            } else {
			                $listOfQuestion = '';
			            }

			        ?>
						</td>
						<td>
							<?php echo $chatQuestion->erp_or_watson; ?>
						</td>
						<td>
							<i class="text-secondary fa fa-comments show-response-by-website" data-value="{{$chatQuestion->value}}"></i>
							@if(request('store_website_id'))
									<?php
			                $reply = \App\ChatbotQuestionReply::where('store_website_id', request('store_website_id'))->where('chatbot_question_id', $chatQuestion->id)->first();
			        if ($reply) {
			            $r = $reply->suggested_reply;
			        } else {
			            $r = '';
			        }
			        ?>
							{{$r}}
							@endif
						</td>
						<td>
							<select name="category_id" id="" class="form-control question-category" data-id="{{$chatQuestion->id}}">
								<option value="">Select</option>
								@foreach($allCategoryList as $cat)
								<option {{$cat['id'] == $chatQuestion->category_id ? 'selected' : ''}} value="{{$cat['id']}}">{{$cat['text']}}</option>
								@endforeach
							</select>
						</td>
						<td class="{{ ( $chatQuestion->watson_status == 1 ) ? 'success' : null }}">{{ ( $chatQuestion->watson_status == '1' ) ? 'Success' : null }} {{ ( $chatQuestion->watson_status == 0 ) ? 'Error' : null }} {{ ( $chatQuestion->watson_status != 1 && $chatQuestion->watson_status != 0 ) ? $chatQuestion->watson_status : null }}</td>
						<td class="{{ ( $chatQuestion->google_status == 1 ) ? 'success' : null }}">{{ ( $chatQuestion->google_status == '1' ) ? 'Success' : null }} {{ ( $chatQuestion->google_status == 0 ) ? 'Error' : null }} {{ ( $chatQuestion->google_status != 1 && $chatQuestion->google_status != 0 ) ? $chatQuestion->google_status : null }}</td>
						<td title="{{ $chatQuestion->created_at }}">{{ date("Y-m-d",strtotime($chatQuestion->created_at))}}</td>
						<td>
							<a title="Edit" class="btn btn-xs text-secondary edit-button pd-3" data-id="<?php echo $chatQuestion->id; ?>" href="<?php echo route('chatbot.question.edit', [$chatQuestion->id]); ?>"><i class="fa fa-edit"></i></a>
							<a title="Delete" class="btn btn-xs text-secondary delete-button pd-3" data-id="<?php echo $chatQuestion->id; ?>" href="<?php echo route('chatbot.question.delete', [$chatQuestion->id]); ?>"><i class="fa fa-trash"></i></a>
							<a title="Show" class="btn btn-xs text-secondary show-button pd-3" data-id="<?php echo $chatQuestion->id; ?>" href="javascript:void(0);"><i class="fa fa-clipboard"></i></a>
							<a title="Repeat" class="btn btn-xs text-secondary repeat-button pd-3" data-id="<?php echo $chatQuestion->id; ?>" href="javascript:void(0);"><i class="fa fa-repeat"></i></a>
							<a title="Sync watson" class="btn btn-xs text-secondary sync-watson-button pd-3" data-id="<?php echo $chatQuestion->id; ?>" href="javascript:void(0);">WSync</a>
							<a title="Sync google" class="btn btn-xs text-secondary sync-google-button pd-3" data-id="<?php echo $chatQuestion->id; ?>" href="javascript:void(0);">GSync</a>
						</td>
					</tr>
				<?php } ?>
			  </tbody>
			</table>
	    </div>
	    <div class="col-lg-12 margin-tb">
	    	<?php echo $chatQuestions->links(); ?>
	    </div>
	</div>
</div>
@include('chatbot::partial.question_log')
@include('chatbot::partial.create_question')
@include('chatbot::partial.create_dynamic_task')
@include('chatbot::partial.create_dynamic_reply')
@include('chatbot::partial.autoreply-create-modal')
@include('partials.chat-history')
@include('chatbot::partial.form.create_entity_type')
<div class="modal" id="create-chatbot-reply" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

<script type="text/javascript">
	$(document).on('click', '.expand-row-msg', function () {
      var name = $(this).data('name');
      var id = $(this).data('id');
      var full = '.expand-row-msg .show-short-'+name+'-'+id;
      var mini ='.expand-row-msg .show-full-'+name+'-'+id;
      $(full).toggleClass('hidden');
      $(mini).toggleClass('hidden');
    });
	$("#create-keyword-btn").on("click",function() {
		$("#create-question").modal("show");
	});
	$("#create-task-btn").on("click",function() {
		$("#create-dynamic-task").modal("show");
	});
	$("#create-reply-btn").on("click",function() {
		$("#create-dynamic-reply").modal("show");
	});

	$("#add-entity-type-btn").on("click",function() {
		$("#add-entity-type").modal("show");
	});

	function checkType() {
		var selectElement = document.getElementById("keyword_or_question");
		var selectedOption = selectElement.options[selectElement.selectedIndex].value;
		let divElement = $('#google_account');
		if (selectedOption == 'simple' || selectedOption == 'priority-customer') {
			divElement.css("display", "none");
		} else {
			divElement.css("display", "");
		}
	}
	$('.show-button').on('click',function(e){


		//$('.spinner-border').show();
		$.ajax({
			type: "POST",
            url: "{{ route('chatbot.question.error_log')}}",
            data: {
				id : $(this).attr('data-id'),
				_token: "{{csrf_token()}}",
			},
            dataType : "json",
            success: function (response) {
               //location.reload();
               if(response.code == 200) {
				//$('.spinner-border').hide();
				$('#question_log_table_body').html('');
				$.each(response.data, function (key, value)
				{
					let action = "";
					if(value.response_type == "error"){
						action = '<a class="btn btn-image edit-data-button" data-id="'+value.id+'"><img src="/images/edit.png" style="cursor: nwse-resize;"></a>';
					}

					let id = key+1;
				   $('#question_log_table_body').append('<tr><td>'+id+'</td> <td>' + value.response + '</td>  <td class="'+value.response_type+'">' + value.response_type + '</td><td>'+value.type+'</td><td>'+action+'</td></tr>');
				})
				$('#question-log-dialog').modal("show");
               }else{

				errorMessage = response.error ? response.error.value : 'data is not found!';
               	toastr['error'](errorMessage);
               }
            },
            error: function (error) {
               toastr['error']('Something Went Wrong!');
            }
        });
	});

	$(".form-save-btn").on("click",function(e) {
		e.preventDefault();
		var form = $(this).closest("form");
		$.ajax({
			type: form.attr("method"),
            url: form.attr("action"),
            data: form.serialize(),
            dataType : "json",
            success: function (response) {
               //location.reload();
               if(response.code == 200) {
               	  toastr['success']('data updated successfully!');
               	  window.location.replace(response.redirect);
               }else{

				errorMessage = response.error ? response.error.value : 'data is not correct or duplicate!';
               	toastr['error'](errorMessage);
               }
            },
            error: function (error) {
               toastr['error']('Could not send this request please refresh browser if the page is idel for while!');
            }
        });
	});

	$(".add-entity-type-btn").on("click", function (e) {
		e.preventDefault();
		var form = $(this).closest("form");
		$.ajax({
			type: form.attr("method"),
			url: form.attr("action"),
			data: form.serialize(),
			dataType : "json",
			success: function (response) {
				//location.reload();
				if(response.code == 200) {
					toastr['success']('data updated successfully!');
					window.location.replace(response.redirect);
				}else{

					errorMessage = response.error ? response.error.value : 'data is not correct or duplicate!';
					toastr['error'](errorMessage);
				}
			},
			error: function (error) {
				toastr['error']('Could not send this request please refresh browser if the page is idel for while!');
			}
		});
	})

	$("#repeat-bulk-option").on("click",function(e) {
		e.preventDefault();

		var allVals = [];
		$(".chatbotQuestion_chkbox:checked").each(function () {
			allVals.push($(this).val());
		});

		if (allVals.length <= 0) {
			toastr['error']('Please select row.');
			return false;
		}

		$.ajax({
			type: 'POST',
            url: 'question/repeat-watson',
            data: { id : allVals, _token: "{{csrf_token()}}" },
            dataType : "json",
            success: function (response) {
				if(response.code == 200) {
					toastr['success'](response.message);
				}else{
					toastr['error'](response.message);
				}
            },
            error: function () {
               toastr['error']('Could not change module!');
            }
        });
	});

	$(".repeat-button").on("click",function(e) {
		e.preventDefault();
		var allVals = [];
		allVals.push( $(this).data("id") );

		$.ajax({
			type: 'POST',
            url: 'question/repeat-watson',
            data: { id : allVals, _token: "{{csrf_token()}}" },
            dataType : "json",
            success: function (response) {
				if(response.code == 200) {
					toastr['success'](response.message);
				}else{
					toastr['error'](response.message);
				}
            },
            error: function () {
               toastr['error']('Could not change module!');
            }
        });
	});

	$(".sync-watson-button").on("click",function(e) {
		let chatBotQuestionId = $(this).data("id");

		$.ajax({
			type: 'GET',
			url: `question/${chatBotQuestionId}/sync-watson`,
			success: function (response) {
				if(response.code == 200) {
					toastr['success'](response.message);
				}else{
					toastr['error'](response.message);
				}
			},
			error: function () {
				toastr['error']('Could not change module!');
			}
		});
	});

	$(".sync-google-button").on("click",function(e) {
		let chatBotQuestionId = $(this).data("id");

		$.ajax({
			type: 'GET',
			url: `<?php echo route('chatbot.question.list'); ?>/${chatBotQuestionId}/sync-google`,
			success: function (response) {
				if(response.code == 200) {
					toastr['success'](response.message);
				}else{
					toastr['error'](response.message);
				}
			},
			error: function () {
				toastr['error']('Could not change module!');
			}
		});
	});

	$(".form-task-btn").on("click",function(e) {
		e.preventDefault();
		var form = $(this).closest("form");
		$.ajax({
			type: form.attr("method"),
            url: form.attr("action"),
            data: form.serialize(),
            dataType : "json",
            success: function (response) {
               if(response.code == 200) {
               	  toastr['success']('data updated successfully!');
               location.reload();

               }else{
				errorMessage = 'Please fill all filed';
               	toastr['error'](errorMessage);
               }
            },
            error: function (error) {
               toastr['error'](error.responseJSON.message);
            }
        });
	});

    $(document).on("click",".show-response-by-website",function() {
        var $this = $(this);
        $.ajax({
            type: "GET",
            url: "<?php echo route('chatbot.question.suggested.response'); ?>",
            data: {value:$this.data("value")},
            success: function (response) {
                $("#create-chatbot-reply").find(".modal-content").html(response);
                $("#create-chatbot-reply").modal("show");
            },
            error: function (error) {
               toastr['error'](error.responseJSON.message);
            }
        });
    });

	$(".select-chatbot-category").select2({
            placeholder: "Enter category name",
            width: "100%",
            tags: true,
            allowClear: true,
            ajax: {
                url: '/chatbot/question/search-category',
                dataType: 'json',
                processResults: function(data) {
                    return {
                        results: data.items
                    };
                }
            }
        });

		$(document).on('change', '.question-category', function () {
            var id = $(this).data("id");
            var category_id = $(this).val();
            $.ajax({
                url: "/chatbot/question/change-category",
                type: 'POST',
                data: {
                    id: id,
                    _token: "{{csrf_token()}}",
                    category_id: category_id
                },
                success: function () {
                    toastr['success']('Category Changed successfully!')
                },
                error: function (error) {
                    toastr["error"](error.responseJSON.message);
                }
            });
        });
		// $('#intent_details').hide();
		$('#entity_details').hide();
		$('#erp_details').hide();
		$(document).on('change', '.view_details_div', function () {
            var type = $(this).val();
			if(type =='intent') {
				$('#intent_details').show();
				$('#entity_details').hide();
				$('#erp_details').hide();
			}
			else if(type =='entity') {
				$('#intent_details').hide();
				$('#entity_details').show();
				$('#erp_details').hide();
			}
			else if(type =='simple' || type =='priority-customer') {
				$('#intent_details').hide();
				$('#entity_details').hide();
				$('#erp_details').show();
			}
			else {
				$('#intent_details').show();
				$('#entity_details').hide();
				$('#erp_details').hide();
			}
        });
		$('#repo-details').hide();
		$(document).on('change', '.change-task-type', function () {
            var type = $(this).val();
			if(type =='task') {
				$('#repo-details').hide();
			}
			else if(type =='devtask') {
				$('#repo-details').show();
			}
        });

		var intentValue = 1;
		$(".add-more-intent-condition-btn").on("click", function(e){
			intentValue++;
		var removeBtnId = '#intentValue_'+(intentValue-1);
		$(removeBtnId).append('<input type="button" value="-" class="btn btn-secondary" onclick="remove(this)"/>');
		    $("<div style='margin-bottom:5px;' class='row align-items-end' id='intentValue_"+intentValue+"' ><div class='col-md-9'><input type='text' name='question[]' class='form-control' placeholder='Enter User Intent'/></div><div class='col-md-2' id='add-intent-value-btn'></div></div>").insertBefore(removeBtnId)
	});

		function remove(ele) {
		$(ele).parents('div.row').remove()
	}

	var idValue=1;
	$(".add-more-condition-btn").on("click", function(e){
		idValue++;
		var removeBtnId = '#typeValue_'+(idValue-1);
		var selectedType = $(this).closest("form").find("select[name = 'types']").val();
		if ( selectedType == "synonyms" || idValue<=5 ){
			$(removeBtnId).append('<input type="button" value="-" class="btn btn-secondary" onclick="remove_entity(this)"/>');
		    $("<div class='form-group col-md-4' ><div class='row align-items-end' id='typeValue_"+idValue+"' ><div class='col-md-9'><label for='type'>&nbsp</label><input type='text' name='type[]' class='form-control' placeholder='Enter value' maxLength = 64/><div/></div></div>").insertBefore('#add-type-value-btn')
		} else {
			toastr["error"]("maximum pattern value limit reached : 5", "error");
			
			idValue--;
		}
	});

	var idValue2=1;
	$(".add-more-condition-btn2").on("click", function(e){
		idValue2++;
		var removeBtnId = '#typeValue2_'+(idValue2-1);
		var selectedType = $(this).closest("form").find("select[name = 'entity_type']").val();
		if ( selectedType == "synonyms" || idValue2<=5 ){
			$(removeBtnId).append('<input type="button" value="-" class="btn btn-secondary" onclick="remove_entity2(this)"/>');
		    $("<div class='form-group col-md-4' ><div class='row align-items-end' id='typeValue2_"+idValue2+"' ><div class='col-md-9'><label for='type'>&nbsp</label><input type='text' name='entity_types[]' class='form-control' placeholder='Enter value' maxLength = 64/><div/></div></div>").insertBefore('#add-type-value-btn2')
		} else {
			toastr["error"]("maximum pattern value limit reached : 5", "error");
			idValue2--;
		}
	});
	$("#types").on("change", function(e) {
		var typeValueCount = $(this).closest("form").find("input[name = 'type[]']").length;
		if(e.target.value == 'patterns' && typeValueCount>5) {
			alert('You are changing a synonym value to a pattern value. You currently have '+ typeValueCount+ ' synonyms associated with this value, but patterns may only have 5');
			$(this).closest("form").find("select[name = 'types']").val('synonyms').change()
			e.preventDefault();
		}
	});
	function remove_entity(ele) {
		$(ele).parents('div.row').remove()
	}
	function remove_entity2(ele) {
		$(ele).parents('div.row').remove()
	}

</script>
@endsection
