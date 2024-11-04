@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'List | Return Exchange')

@section('styles')
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
	<style>
	.form-group-extended{
		margin-bottom: 5px !important;
		width:19.5%  !important;
	}
	.form-group-extended-fifteen{
		margin-bottom: 5px !important;
		width:17%  !important;
	}
	.form-group-extended-four{
		margin-bottom: 5px !important;
		width:2%  !important;
	}
	.form-group-extended input[type=text]{
		width:100%  !important;
	}

	.modal-dialog-wide{ 
		max-width: 100%;
		width: auto !important;
		/*display: inline-block;*/
	}
	.select2-container {
		width:100% !important;
	}.no_pd {
		padding: 0px !important;
	}
		.btn-secondary{
			border: 1px solid #ddd;
			color:#757575;
			background: #fff;
			padding: 4px 10px !important;
		}
	.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{
		padding:3px 8px;
	}
	</style>
@endsection

@section('large_content')

<div class="row" id="return-exchange-page">
	<div class="col-lg-12 margin-tb p-0">
        <h2 class="page-heading mb-3">Return Exchange <span id="total-counter"></span>
			<div class="pull-right pr-3">
				<button type="button" class="btn btn-xs btn-secondary" data-toggle="modal" data-target="#returnexchangedatatablecolumnvisibilityList">Column Visiblity</button>

				<a href="#" class="btn btn-xs btn-secondary delete-orders" id="bulk_delete">
					Delete
				</a>
				<a href="#" class="btn btn-xs update-customer btn-secondary" id="bulk_update">
					Update
				</a>
				<a href="#" class="btn btn-xs update-customer btn-secondary" id="create_status">
					Create Status
				</a>
				<button class="btn btn-xs btn-secondary" data-toggle="modal" data-target="#newStatusColor"> Status Color</button>
				<a href="#" class="btn btn-xs update-customer btn-secondary" id="create_refund">
					Create Refund
				</a>
			</div>
		</h2>
    </div>
    <br>
    <div class="col-lg-12 margin-tb pl-3">
    	<div class="row" style="margin-bottom:0px;">
	    	<div class="col-md-12">
		    	<div class="h" style="margin-bottom:0px;">
		    		<form class="form-inline return-exchange-handler" method="post">
					  <div class="row">
				  		<div class="col pr-0">
				  			<div class="form-group form-group-extended">
							    <!--<label for="from">Customer Name:</label>-->
							    {{ html()->text("customer_name", request("customer_name"))->class("form-control")->placeholder("Enter Customer Name") }}
						  	</div>
						    <div class="form-group form-group-extended">
							    <!--<label for="from">Customer Email:</label>-->
							    {{ html()->text("customer_email", request("customer_email"))->class("form-control")->placeholder("Enter Customer Email") }}
						  	</div>
							<div class="form-group form-group-extended">
							    <!--<label for="from">Customer Id:</label>-->
							    {{ html()->text("customer_id", request("customer_id"))->class("form-control")->placeholder("Enter Customer Id") }}
						  	</div>
							<div class="form-group form-group-extended">
							    <!--<label for="from">Order Id:</label>-->
							    {{ html()->text("order_id", request("order_id"))->class("form-control")->placeholder("Enter Order Id") }}
						  	</div>
						  	<div class="form-group form-group-extended">
							    <!--<label for="from">Product:</label>-->
							    {{ html()->text("product", request("product"))->class("form-control")->placeholder("Enter product sku/id/name") }}
						  	</div>
							<div class="form-group form-group-extended">
							    <!--<label for="from">Product:</label>-->
							    {{ html()->text("website", request("website"))->class("form-control")->placeholder("Website") }}
						  	</div>
				  			<div class="form-group form-group-extended">
							    <!--<label for="action">Status:</label>-->
							    <?php /*?><?php echo Form::select("status",\App\ReturnExchange::STATUS,request("limti"),[
                                    "class" => "form-control select2",
                                    "placeholder" => "-- Select Status --"
                                ]) ?><?php */ ?>
								{{ html()->select("status", \App\ReturnExchangeStatus::pluck("status_name", "id")->toArray(), request("limti"))->class("form-control")->placeholder("-- Select Status --") }}
						  	</div>
						  	<div class="form-group form-group-extended">
							    <!--<label for="action">Type:</label>-->
							    {{ html()->select("type", ["refund" => "Refund", "exchange" => "Exchange", "buyback" => "Buyback", "return" => "Return", "cancellation" => "Cancellation"], request("limti"))->class("form-control")->placeholder("-- Select Type --") }}
						  	</div>
						  	<div class="form-group form-group-extended">
							    <!--<label for="action">Number of records:</label>-->
							    {{ html()->select("limit", [10 => "10", 20 => "20", 30 => "30", 50 => "50", 100 => "100", 500 => "500", 1000 => "1000"], request("limti"))->class("form-control recCount")->placeholder("Number of records") }}
						  	</div>
							  <div class="form-group form-group-extended-fifteen">
							  <input style="width: 100%;" placeholder="Est Return / Exch. date" value="" type="text" class="form-control search-est-return-exch-date" name="est_completion_date">
						  	</div>
						  	<div class="form-group form-group-extended-four">
						  		<!--<label for="button">&nbsp;</label>-->
						  		<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
						  			<img src="/images/search.png" style="cursor: default;">
						  		</button>
						  	</div>		
				  		</div>
					  </div>	
					</form>	
		    	</div>
		    </div>
	    </div>	
		<div class="row">
			<div class="col-md-12 mb-1">
				<div class="pull-right" style="display:none;">
				  <a href="#" class="btn btn-xs btn-secondary delete-orders" id="bulk_delete">
						Delete
				  </a>
				  <a href="#" class="btn btn-xs update-customer btn-secondary" id="bulk_update">
						Update
				  </a>
				  <a href="#" class="btn btn-xs update-customer btn-secondary" id="create_status">
						Create Status
				  </a>
				  <a href="#" class="btn btn-xs update-customer btn-secondary" id="create_refund">
						Create Refund
				  </a>
				</div>
			</div>
		</div>
		<!-- a -->
		<div class="margin-tb infinite-scroll" id="page-view-result">

		</div>
	</div>
</div>

<div id="update-status-message-tpl" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content ">
			<div class="modal-header ml-4 mr-4">
				<h4 class="modal-title">Change Status</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form action="" id="update-status-message-tpl-frm" method="POST">
				@csrf
				<input type="hidden" name="order_id" id="order-id-status-tpl" value="">
				<input type="hidden" name="order_status_id" id="order-status-id-status-tpl" value="">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-2">
								<strong>Message:</strong>
							</div>
							<div class="col-md-5">
								<div class="form-group">
									<textarea cols="45" class="form-control" id="order-template-status-tpl" name="message"style="height: 35px;"></textarea>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group d-flex">
									<div class="checkbox">
										<label><input class="msg_platform" onclick="loadpreview(this);"
												type="checkbox" value="email">Email</label>
									</div>
									<div class="checkbox mt-3 ml-2">

										<label><input class="msg_platform" type="checkbox" value="sms">SMS</label>
									</div>
								</div>
							</div>
							<div class="col-md-8">
								<div id="preview" style="display:none">

								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer pb-0">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" class="btn custom-button update-status-with-message">Submit</button>
						<!-- <button type="button" class="btn btn-secondary update-status-with-message">With Message</button> -->
						<!-- <button type="button" class="btn btn-secondary update-status-without-message">Without Message</button> -->
					</div>
				</div>
			</form>
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
@include("return-exchange.column-visibility-modal")
@include("return-exchange.templates.list-template")
@include("return-exchange.templates.modal-emailToCustomer")
@include("return-exchange.templates.modal-createstatus")
@include("return-exchange.templates.modal-productDetails")
@include("return-exchange.templates.create-refund")
@include("return-exchange.templates.update-refund-modal")
@include("return-exchange.partial.modal-status-color")
@endsection

@section('scripts')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<script type="text/javascript" src="{{ asset('/js/jsrender.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/jquery.validate.min.js') }}"></script>
	<script src="{{ asset('/js/jquery-ui.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/return-exchange.js') }} "></script>
	<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
	<script type="text/javascript">

		function Showactionbtn(id){
	      	$(".action-btn-tr-"+id).toggleClass('d-none')
	      	$("#asset_user_name").select2('destroy');
	    }

		msQueue.init({
			bodyView : $("#return-exchange-page"),
			baseUrl : "<?php echo url('/'); ?>"
		});

		$('#date_of_request').datetimepicker({
		format: 'YYYY-MM-DD HH:mm'
		});

		$('#date_of_dispatched').datetimepicker({
			format: 'YYYY-MM-DD HH:mm'
		});

		$('.search-est-return-exch-date').datetimepicker({
			format: 'YYYY-MM-DD'
		});
		
		$(document).ready(function () {
		// $(".estimate-date").each(function() {
        //         $(this).datetimepicker({
		// 			format: 'YYYY-MM-DD'
		// 		});
		// });

		$('body').on('focus',".estimate-date", function(){
        		$(this).datetimepicker({
					format: 'YYYY-MM-DD'
				});
    	});
	});

	$(document).on("click",".update-status-with-message",function(e) {
          e.preventDefault();
          console.log($("#email_from_mail").val());
          console.log($("#email_to_mail").val());
          var selected_array = [];
          console.log(selected_array);
          $('.msg_platform:checkbox:checked').each(function() {
            selected_array.push($(this).val());
          });
          
          if(selected_array.length == 0){
            alert('Please at least select one option');
            return;
          }else{
            $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '{{route("return-exchange.status-send-email")}}',
            type: "post",
            async : false,
            data : {
              id : $("#order-id-status-tpl").val(),
              status : $("#order-status-id-status-tpl").val(),
              sendmessage:'1',
              message:$("#order-template-status-tpl").val(),
              custom_email_content:$("#customEmailContent").val(),
              from_mail:$("#email_from_mail").val(),
              to_mail:$("#email_to_mail").val(),
              order_via: selected_array,
            }
            }).done( function(response) {
				toastr['success'](response.message);
              $("#update-status-message-tpl").modal("hide");
            }).fail(function(errObj) {
              toastr['error'](errObj.responseText);
           });
          }
          
      });

	    $(document).on('click', '.expand-row', function () {
			var id = $(this).data('id');
			console.log(id);
            var full = '.expand-row .td-full-container-'+id;
            var mini ='.expand-row .td-mini-container-'+id;
            $(full).toggleClass('hidden');
            $(mini).toggleClass('hidden');
        });


		$(document).on('click', '#dispatch_date', function (e) {
		if ($(this).prop('checked')) {
			$('#additional-fields').show();
		} else {
			$('#additional-fields').hide();
		}
    	});
		$(document).on('click', '.send-email-to-customer', function () {
            $('#emailToCustomerModal').find('form').find('input[name="customer_id"]').val($(this).data('id'));
            $('#emailToCustomerModal').modal("show");
        });
				
		$(document).on('change', '.recCountproduct', function () {
            $(this).parent().closest("form").submit();
        });
		
		$(document).on('click', '.re-generate-coupon', function (e) {
			e.preventDefault();

			console.log( $(this).data() );
			var id = $(this).data('id');
			$.ajax({
				url: "/return-exchange/"+id+"/re-generate-coupon",
				type: 'GET',
				success: function (response) {
					if (response.code == 200) {
						toastr['success'](response.message, 'success');
					} else {
						toastr['error'](response.message, 'Error');
					}
				},
				error: function () {
					alert('There was error loading priority task list data');
				}
			});
		});

		$(document).on('submit', '#customerUpdateForm', function (e) {
			e.preventDefault();
			var data = $(this).serializeArray();
			$.ajax({
				url: "{{route('return-exchange.updateCusromer')}}",
				type: 'POST',
				data: data,
				success: function (response) {
					toastr['success']('Successful', 'success');
					$('#emailToCustomerModal').modal('hide');
					$("#customerUpdateForm").trigger("reset");
					$("tr").find('.select-id-input').each(function () {
					  if ($(this).prop("checked") == true) {
						$(this).prop("checked", false);
					  }
					});
					window.location.reload();
				},
				error: function () {
					alert('There was error loading priority task list data');
				}
			});
		});


		$(document).on('submit', '#createRefundForm', function (e) {
			e.preventDefault();
			var data = $(this).serializeArray();
			$.ajax({
				url: "{{route('return-exchange.createRefund')}}",
				type: 'POST',
				data: data,
				success: function (response) {
					toastr['success'](response.message, 'success');
					$('#createRefundModal').modal('hide');
					$("#createRefundForm").trigger("reset");
					$("tr").find('.select-id-input').each(function () {
					  if ($(this).prop("checked") == true) {
						$(this).prop("checked", false);
					  }
					});
					window.location.reload();
				},
				error: function (error) {
					toastr['error'](error.responseJSON.message, 'error');
				}
			});
		});

		CKEDITOR.replace('editableFile');
		$(document).on('submit', '#updateRefundForm', function (e) {
			e.preventDefault();
			var data = $(this).serializeArray();
			$.ajax({
				url: "{{route('return-exchange.updateRefund')}}",
				type: 'POST',
				data: data,
				success: function (response) {
					alert('fgf');
					toastr['success'](response.message, 'success');
					$('#updateRefundModal').modal('hide');
					$("#updateRefundForm").trigger("reset");
					$("tr").find('.select-id-input').each(function () {
					  if ($(this).prop("checked") == true) {
						$(this).prop("checked", false);

					  }
					});
					// window.location.reload();
				},
				error: function (error) {
					toastr['error'](error.responseJSON.message, 'error');
				}
			});
		});
		
		$(document).on('submit', '#createStatusForm', function (e) {
			e.preventDefault();
			var data = $(this).serializeArray();
			$.ajax({
				url: "{{route('return-exchange.createStatus')}}",
				type: 'POST',
				data: data,
				success: function (response) {
					toastr['success']('Successful', 'success');
					$('#createstatusModal').modal('hide');
					window.location.reload();
				},
				error: function () {
					alert('There was error loading priority task list data');
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
		
		$(document).on('click', '.estimate-date-submit', function () {
			let exchange_id = $(this).data('id');
			let estimate_date = $("#estimate_date_" + exchange_id).val();
            $.ajax({
				url: "{{route('return-exchange.update-estimated-date')}}",
				type: 'POST',
                data: {
                    estimate_date : estimate_date,
					exchange_id: exchange_id,
					_token: "{{csrf_token()}}"
                },
                success: function (response) {
                    toastr["success"]("Estimated Date updated successfully!", "Message");
                }
            });

        });
		$(document).on('click','.addnewreplybtn',function(e){
          e.preventDefault();
          var replybox  = $(this).parentsUntil('#customerUpdateForm').find('.addnewreply');
          var selectreplybox = $(this).parentsUntil('#customerUpdateForm').find('.quickreply');
          var reply = $(this).parentsUntil('#customerUpdateForm').find('.addnewreply').val();
          if(!reply){
            alert('please add reply to input box !');
            return false;
          }
          $.ajax({
                type: "POST",
                url: "{{route('returnexchange.addNewReply')}}",
                data: {reply:reply},
                headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:"json",
                beforeSend:function(data){
                  $('.ajax-loader').show();
                }
            }).done(function (response) {
              $('.ajax-loader').hide();
              if(response.status==200){
                replybox.val('');
                selectreplybox.html(response.html);
              }
            }).fail(function (response) {
              $('.ajax-loader').hide();
                console.log(response);
            });
          
        })
        $('.quickreply').on('change',function(){
			var reply = $(this).find('option:selected').text();
			var replyval = $(this).find('option:selected').attr('value');
			if(replyval!=''){
				$(this).parentsUntil('#customerUpdateForm').find('textarea[name="customer_message"]').val(reply);
			}else{
				$(this).parentsUntil('#customerUpdateForm').find('textarea[name="customer_message"]').val('');
			}
        });
		
		$(document).on("change", '.return_exchange_status', function(event) { 
   			//$('.return_exchange_status').on('change',function(){
			
        	var statusVal = $(this).val();
			let exchange_id = $(this).data('id');
			var oldStatusText = $(this).data('old_status_name');
			var oldStatusId = $(this).data('old_status_id');
			$.ajax({
                type: "POST",
                url: "{{route('returnexchange.update-status')}}",
                data: {
					return_exchange_status: statusVal,
					id: exchange_id,
					status_name: oldStatusText,
					status_id: oldStatusId
				},
                headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:"json",
                beforeSend:function(data){
                  $('.ajax-loader').show();
                }
            }).done(function (response) {
              $('.ajax-loader').hide();
				if (response.code == 200) {
					toastr['success']("Status updated successfully!", 'success');
					$("#order-id-status-tpl").val(exchange_id);
					$("#preview").html(response.data.preview);
					CKEDITOR.replace( 'editableFile' );
					$("#order-status-id-status-tpl").val(statusVal);
					$("#order-template-status-tpl").val(response.data.template);
					$(".msg_platform").prop('checked', false);
					$("#update-status-message-tpl").modal("show");
				} else {
					toastr['error'](response.message, 'Error');
				}
			}).fail(function (response) {
              $('.ajax-loader').hide();
			  toastr['error'](response.message, 'Error');
            });
        });

		function loadpreview(t)
        {
          $("#preview").hide();
          if (t.checked == true){
            $("#preview").show();
          }
        }


		$(document).on("change", '.return_exchange_status_info', function(event) { 
   			let exchange_id = $(this).data('id');
			$.ajax({
                type: "POST",
                url: "{{route('returnexchange.update_status_log')}}",
                data: {
					id: exchange_id,
				},
                headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:"json",
                beforeSend:function(data){
                  $('.ajax-loader').show();
                }
            }).done(function (response) {
              $('.ajax-loader').hide();
				if (response.code == 200) {
					$('#returnExchangeStatusInfotr')	
					toastr['success']("Status Log listed successfully!", 'success');
				} else {
					toastr['error'](response.message, 'Error');
				}
			}).fail(function (response) {
              $('.ajax-loader').hide();
			  toastr['error'](response.message, 'Error');
            });
        });
	</script>
@endsection


