@extends('layouts.app')

@section('title', 'Broadcast Messages')

@section('styles')

    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin:-50px 0px 0px -50px;
        }
        .select2-container{
            float:left;
            width:100%!important;
        }
    </style>
@endsection


@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Broadcast Messages</h2>
                    
                    <div class="pull-left cls_filter_box">
                        {{ html()->modelForm([], 'GET', url()->current())->class('form-inline')->open() }}
                            <div class="form-group ml-3 cls_filter_inputbox">
                                <label for="leads_email">Order By</label>
                                {{ html()->select('order', ['' => 'Select', 'asc' => 'ASC', 'desc' => 'DESC'], @$inputs['order'])->class('form-control') }}
                            </div>
                         
                            <div class="form-group ml-3 cls_filter_inputbox">
                                <label for="name">Name</label>
                                {{ html()->text('name', @$inputs['name'])->class('form-control') }}
                            </div>
                            <button type="submit" style="margin-top: 20px;padding: 5px;" class="btn btn-image"><img src="{{url('/images/filter.png')}}"/></button>
                        </form>
                    </div>
                    
              
							
							
                </div>
            </div>
        </div>

    </div>

	
    <div class="modal fade" id="messageTitle" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Message Title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" >
                    {{ html()->form('POST', route('create.marketing.message'))->class('ajax-submit')->open() }}
					<div id="messageTitleForm"></div>
					</form>
                </div>
            </div>
        </div>
    </div>

<div class="pl-4 pr-4">
    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="passwords-table"style="table-layout: fixed;">
            <thead>
            <tr>
                <th width="1%">ID</th>
                <th width="7%">Name</th>
                <th width="5%">Created Date</th>
                <th width="3%">Action</th>
            </thead>
            <tbody>
            @foreach($data as $key=>$value)
                <tr class="{{$value->id}}">
                    <td id="id">{{$key+1}}</td>
                    <td id="name">{{$value->name??''}}</td>
                    <td id="name">{{$value->created_at->format('Y-m-d')??''}}</td>
                    <td>
                        <a class="create_broadcast" data-message-id="{{ $value->id }}" href="javascript:;"><i class="fa fa-plus" aria-hidden="true" style="color: gray;"></i></a>
                        <a title="Preview Broadcast Numbers" data-message-id="{{ $value->id }}" class="btn btn-image preview_broadcast_numbers" href="javascript:;"  ><i class="fa fa-eye" aria-hidden="true"></i></a>

						<a data-route="{{route('delete.message')}}" data-id="{{$value->id}}" class="trigger-delete">  <i style="cursor: pointer;" class="fa fa-trash " aria-hidden="true"></i></a>
                        <a class=" show_massage ml-3" data-message-id="{{ $value->id }}" href="javascript:;"><i class="fa fa-comments-o" aria-hidden="true" style="color:gray;"></i></a>
                        <a class=" add_type ml-3" data-type="supplier" data-message-id="{{ $value->id }}" href="javascript:;"><i class="fa fa-user" aria-hidden="true"style="color:gray;"></i></a>
                        <a class=" add_type ml-3" data-type="vendor" data-message-id="{{ $value->id }}" href="javascript:;"><i class="fa fa-users" aria-hidden="true" style="color:gray;"></i></a>
                        <a class=" add_type ml-3" data-type="customer" data-message-id="{{ $value->id }}" href="javascript:;"><i class="fa fa-plus" aria-hidden="true" style="color:gray;"></i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(isset($data))
            {{ $data->links() }}
        @endif
    </div>
</div>
@endsection

<div id="create_broadcast" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Send Message</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form id="send_message" method="POST">
                <input type="hidden" name="id" id="bid" value="">
				<div class="modal-body">
                    <div class="form-group">
						<strong>Medium</strong>
                        <select name="is_email" class="form-control" id="is_email">
                            <option value="">Select Medium</option>
                            <option value="0">Whatsapp</option>
                            <option value="1">Email</option>
                            <option value="2">Bulk notification</option>

                        </select>
					</div>
					<div class="form-group">
						<strong>Name</strong>
						<input name="name" id="name" autocomplete="off" type="text" class="form-control"/>
					</div>
					<div class="form-group">
						<strong>Message</strong>
						<textarea name="message" id="message_to_all_field" rows="8" cols="80" class="form-control"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-secondary">Send Message</button>
				</div>
			</form>
		</div>

	</div>
</div>

<div id="add_type" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"></h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<form id="send_type" method="POST">
                <input type="hidden" name="id" id="tid" value="">
                <input type="hidden" name="type" id="type" value="">
				<div class="modal-body">
					<div class="form-group" id="suppliers">
						<select name="suppliers" class="form-control" multiple id="supp">
                            @if($suppliers)
                                @foreach($suppliers as $_supplier)
                                <option value="{{$_supplier->id}}">{{$_supplier->supplier}}</option>
                                @endforeach
                            @endif
                        </select>
					</div>
                    <div class="form-group" id="vendors">
						<select name="vendors" multiple id="ven">
                            @if($vendors)
                                @foreach($vendors as $_vendor)
                                <option value="{{$_vendor->id}}">{{$_vendor->name}}</option>
                                @endforeach
                            @endif
                        </select>
					</div>
                    <div class="form-group" id="customers">
						<select name="customers" multiple id="cust">
                            @if($customers)
                                @foreach($customers as $_customer)
                                <option value="{{$_customer->id}}">{{$_customer->name}}</option>
                                @endforeach
                            @endif
                        </select>
					</div>
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-secondary">Submit</button>
				</div>
			</form>
		
            <div class="table-responsive" style="margin-top:20px;overflow-x: auto !important; padding: 0 10px">
                <table class="table table-bordered text-nowrap" id="massage-table">
                    <thead>
                        <tr>
                            <th>ID </th> 
                            <th>Name</th>
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="group-items-list">
                    </tbody>
                </table>
            </div>
        </div>
	</div>
</div>

<div id="ShowMassageModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
       <!-- Modal content-->
       <div class="modal-content "  >
          
            <div class="modal-header">
                <h4 class="modal-title">Show Broadcast Message</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" >
                <div class="table-responsive" style="margin-top:20px;">
                    <table class="table table-bordered text-nowrap" id="massage-table">
                        <thead>
                            <tr>
                                <th>Date </th> 
                                <th>Name</th>
                                <th>Massage</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="massage-items-list">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                   <button type="button" style="margin-top: 5px;" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
       </div>
    </div>
 </div>


<div id="previewSendMailsModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
       <!-- Modal content-->
       <div class="modal-content "  >
          
             <div class="modal-header">
                <h4 class="modal-title">Broadcast Message Numbers</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
             </div>
 
 
              <div class="modal-body" >
             <div class="table-responsive" style="margin-top:20px;">
               <table class="table table-bordered text-nowrap" id="email-table">
                 <thead>
                   <tr>
                   <th>Date </th> 
                   <th>Message Id</th>
                   <th>Type Id</th>
                   <th>Type</th>
                   <th>Action</th>
                   </tr>
                 </thead>
                 <tbody class="product-items-list">
                 
                 </tbody>
                 </table>
             </div>
           </div>
 
             
             <div class="modal-footer">
                <div class="row">
                   <button type="button" style="margin-top: 5px;" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
             </div>
          
       </div>
    </div>
 </div>

@section('scripts')
    <script>
		var base_url = window.location.origin+'/';
	    $('.ajax-submit').on('submit', function(e) { 
			e.preventDefault(); 
			$.ajax({
                type: $(this).attr('method'),
				url: $(this).attr('action'),
				data: new FormData(this),
				processData: false,
				contentType: false,
				success: function(data) { 
					if(data.statusCode == 500) { 
						toastr["error"](data.message);
					} else {
						toastr["success"](data.message);
						setTimeout(function(){
                          location.reload();
                        }, 1000);
					}
				},
				done:function(data) {
					console.log('success '+data);
				}
            });
		}); 

        

        $(document).on('click', '.resend_massage', function () {
            var id = $(this).data("message-id");
            $.ajax({
                type: "POST",
                url: "broadcast-messages/resend/message",
                data: {
                    _token: "{{ csrf_token() }}",
                    id:id,
                    is_last:1
                }
            }).done(function () {
                //window.location.reload();
            }).fail(function (response) {
                $(thiss).text('No');

                alert('Could not say No!');
                console.log(response);
            });
        });

        $(document).on('click', '.create_broadcast', function () {
            var id = $(this).data("message-id");
            $("#bid").val(id);
            $("#create_broadcast").modal("show");
        }); 

        $("#suppliers").hide();
        $("#vendors").hide();
        $("#customers").hide();

        $("#supp").select2();
        $("#ven").select2();
        $("#cust").select2();

        $(document).on('click', '.show_massage', function () {
            var id = $(this).data("message-id");

            $.ajax({
                type: "POST",
                url: "broadcast-messages/show/message",
                data: {
                    _token: "{{ csrf_token() }}",
                    id:id,
                },
                beforeSend: function() {
                    $("loading-image").show();
                }
            }).done( function(response) {
                $("loading-image").hide();
                $("#ShowMassageModal").find(".massage-items-list").html("");
                if(response.code == 200) {
                    var items = response.data;
                    if(items.length > 0) {
                        var itemsHtml = '';
                        $.each(items, function(k,v) {
                            itemsHtml += `<tr class="in-background filter-message" id="tr`+v.id+`">
                                <td >`+v.created_at+`</td>
                                <td >`+v.name+`</td>
                                <td >`+v.message+`</td>
                                <td ><a data-route="" title="Resend Massage" data-broadcast-message-id="`+v.broadcast_message_id+`" data-name="`+v.name+`" data-message="`+v.message+`"  class="singal-resend-massage">  <i style="cursor: pointer;" class="fa fa-repeat" aria-hidden="true"></i></a></td>
                            </tr>`;
                        });
                        
                        $("#ShowMassageModal").find(".massage-items-list").html(itemsHtml);
                    }
                    $("#ShowMassageModal").modal("show");
                }
            });
        });

        

        $(document).on('click', '.singal-resend-massage', function () {
            var id = $(this).data("broadcast-message-id");
            var name = $(this).data("name");
            var message = $(this).data("message");

            $.ajax({
                type: "POST",
                url: "broadcast-messages/send/message",
                data: {
                    _token: "{{ csrf_token() }}",
                    message: message,
                    name: name,
                    id:id,
                }
            }).done(function () {
                //window.location.reload();
            }).fail(function (response) {
                $(thiss).text('No');

                alert('Could not say No!');
                console.log(response);
            });
        });


        $(document).on('click', '.add_type', function () {
            
            var id = $(this).data("message-id");
            var type = $(this).data("type");
            $("#tid").val(id);
            $("#type").val(type);

            $.ajax({
                type: "POST",
                url: '{{route("get-send-message-group")}}',
                data: {
                    _token: "{{ csrf_token() }}",
                    id:id,
                    type:type,
                }
            }).done(function (response) {

                if(response.code == 200) {
                    
                    var items = response.data;
                    $('.group-items-list').html('<tr><td colspan="4">No data found</td></tr>');
                    if(items.length > 0) {
                        var itemsHtml = '';
                        $.each(items, function(k,v) {
                            itemsHtml += `<tr class="in-background filter-message" id="tr`+v.id+`">
                                <td >`+v.id+`</td>
                                <td >`+v.typeName+`</td>
                                <td >`+v.type+`</td>
                                <td ><a data-route="" data-id="`+v.id+`" onclick="deleteType(`+v.id+`,event)" class="trigger-type-delete">  <i style="cursor: pointer;" class="fa fa-trash " aria-hidden="true"></i></a></td>
                            </tr>`;
                        });
                        
                        $('.group-items-list').html(itemsHtml);
                    }
                }
            }).fail(function (response) {
                $(thiss).text('No');

                alert('Could not say No!');
                console.log(response);
            });


            
            if(type=='supplier'){
                $("#add_type .modal-title").text('Select Suppliers');
                $("#suppliers").show();
                $("#customers").hide();
                $("#vendors").hide();
            }else if(type=='vendor'){
                $("#add_type .modal-title").text('Select Vendors');
                $("#vendors").show();
                $("#customers").hide();
                $("#suppliers").hide();
            }else{
                $("#add_type .modal-title").text('Select Customers');
                $("#customers").show();
                $("#suppliers").hide();
                $("#vendors").hide();
            }
            $("#add_type").modal("show");
        }); 
        
        $("#send_message").submit(function (e) {
            e.preventDefault();
            
            if ($("#send_message").find("#is_email").val() == "") {
                alert('Please select Medium ');
                return false;
            }

            if ($("#send_message").find("#name").val() == "") {
                alert('Please type name ');
                return false;
            }

            if ($("#send_message").find("#message_to_all_field").val() == "") {
                alert('Please type message ');
                return false;
            }

            $.ajax({
                type: "POST",
                url: "broadcast-messages/send/message",
                data: {
                    _token: "{{ csrf_token() }}",
                    message: $("#send_message").find("#message_to_all_field").val(),
                    name: $("#send_message").find("#name").val(),
                    id:$("#send_message").find("#bid").val(),
                    is_email:$("#send_message").find("#is_email").val(),
                }
            }).done(function () {
                //window.location.reload();
            }).fail(function (response) {
                $(thiss).text('No');

                alert('Could not say No!');
                console.log(response);
            });
        });

        $("#send_type").submit(function (e) {
            e.preventDefault();
            var type = $("#send_type").find("#type").val();
            if(type=='supplier'){
                if($('#supp').val()==''){
                    alert('Please select supplier');
                    return false;
                }
                var allvalues = $('#supp').val();
            }else if(type=='vendor'){
                if($('#ven').val()==''){
                    alert('Please select vendor');
                    return false;
                }
                var allvalues = $('#ven').val();
            }else{
                if($('#cust').val()==''){
                    alert('Please select customer');
                    return false;
                }
                var allvalues = $('#cust').val();
            }

            $.ajax({
                type: "POST",
                url: "broadcast-messages/send/type",
                data: {
                    _token: "{{ csrf_token() }}",
                    values: allvalues,
                    type: type,
                    id:$("#send_type").find("#tid").val(),
                }
            }).done(function () {
                window.location.reload();
            }).fail(function (response) {
                $(this).text('No');

                alert('Could not say No!');
                console.log(response);
            });
        });

      $(document).on("click",".preview_broadcast_numbers",function() {

        var id = $(this).data("message-id");

        $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "broadcast-messages/preview-broadcast-numbers",
        type: "post",
        data : {
            id: id,
        },
        beforeSend: function() {

            $("loading-image").show();
        }
        }).done( function(response) {

        $("loading-image").hide();

        if(response.code == 200) {
        var items = response.data;
        if(items.length > 0) {
            var itemsHtml = '';
            $.each(items, function(k,v) {
                itemsHtml += `<tr class="in-background filter-message" id="tr`+v.id+`">
                    <td >`+v.created_at+`</td>
                    <td >`+v.broadcast_message_id+`</td>
                    <td >`+v.typeName+`</td>
                    <td >`+v.type+`</td>
                    <td ><a data-route="" data-id="`+v.id+`" onclick="deleteType(`+v.id+`,event)" class="trigger-type-delete">  <i style="cursor: pointer;" class="fa fa-trash " aria-hidden="true"></i></a></td>
                </tr>`;

            });
            
            $("#previewSendMailsModal").find(".product-items-list").html(itemsHtml);
        }

        $("#previewSendMailsModal").modal("show");

        }
        
        }).fail(function(errObj) {
            alert("Could not change status");
        });




        });

	    $('.trigger-delete').on('click', function(e) {
			var id = $(this).attr('data-id');
			e.preventDefault(); 
			var option = { _token: "{{ csrf_token() }}", id:id };
			var route = $(this).attr('data-route');
			$("#loading-image").show();
			$.ajax({
				type: 'post',
				url: route,
				data: option,
				success: function(response) {
					$("#loading-image").hide();
					if(response.code == 200) {
						$(this).closest('tr').remove();
                        toastr["success"](response.message); 
                    }else if(response.statusCode == 500){
                        toastr["error"](response.message);
                    }
					setTimeout(function(){
                          location.reload();
                        }, 1000);
				},
				error: function(data) {
					$("#loading-image").hide();
					alert('An error occurred.');
				}
			}); 
		});	

        function deleteType(id,event){
			var option = { _token: "{{ csrf_token() }}", id:id };
			var route = $(this).attr('data-route');
			$("#loading-image").show();
            $.ajax({
				type: 'post',
				url: "broadcast-messages/delete/type",
				data: option,
                success: function(response) {
					$("#loading-image").hide();
					if(response.code == 200) {
						document.getElementById("tr"+id).remove();
                        toastr["success"](response.message); 
                    }else if(response.statusCode == 500){
                        toastr["error"](response.message);
                    }
					setTimeout(function(){
                          //location.reload();
                        }, 1000);
				},
				error: function(data) {
					$("#loading-image").hide();
					alert('An error occurred.');
				}
			}).done(function () {
                //window.location.reload();
            }).fail(function (response) {
                $(thiss).text('No');
                alert('Could not say No!');
                console.log(response);
            }); 
        }

		function showMessageTitleModal(groupId){
			$('#message_group_id').val(groupId);
			$.get(base_url+"twillio/marketing/message/"+groupId, function(data, status){ 
				$('#messageTitleForm').html(data);
			});
			$('#messageTitle').modal('show');			
		}
    </script>
@endsection