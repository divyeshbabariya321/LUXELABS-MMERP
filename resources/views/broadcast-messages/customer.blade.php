@extends('layouts.app')
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
            margin: -50px 0px 0px -50px;
        }
		div#addCustomer form.ajax-submit .form-group > span.select2 {
			width: 100% !important;
			margin: 6px 0 0 0;
		}
    </style>
	<style type="text/css">
      

        /* The switch - the box around the slider */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        /* Hide default HTML checkbox */
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        /* The slider */
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        .show_select {
            display: none;
        }
    </style>

	{{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet"> --}}
@endsection

@section('content')

    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Customers</h2>
                   
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addCustomer" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Edit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
				
                {{ html()->form('POST', route('add.customer.group'))->class('ajax-submit')->open() }}
				<div class="modal-body edit-modal-body" id="edit-modal">
					<input type="hidden" id="message_group_id" name="message_group_id" value="{{$messageGroupId}}">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Customer Name</label>
                        {{ html()->select('customer_id', [])->class('form-control customer-select')->required() }}
                    </div>     
				</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>                    
                </div>
				</form>	
            </div>
        </div>
    </div>

    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="passwords-table">
            <thead>
            <tr>
                <th style="">ID</th>
                <th style="">Name</th>
                <th style="">Email</th>
                <th style="">Phone</th>
                <th style="">Whatsapp Number</th>
                <th style="">Message Sent</th>
                <th style="">Action</th>
            </thead>
            <tbody>
            @foreach($customers as $value)
                <tr class="{{$value->id}}">
                    <td id="id">{{$value->id}}</td>
                    <td id="name">{{$value->name}}</td>
                    <td id="description">{{$value->email}}</td>
                    <td >{{$value->phone}}</td>
                    <td >{{$value->whatsapp_number}}</td>
                    <td>@if(isset( $messageSentToCustomers[$value->id]) and $messageSentToCustomers[$value->id] == 1 ) Yes @else No @endif</td>
                    <td>
					<label class="switch" style="margin: 0px">
                        <input type="checkbox" class="checkbox __toggle" value="{{$value->id}}"  data-id="{{$value->id}}" @if(in_array($value->id, $customerAdded)) checked @endif>
                        <span class="slider round">
						</span>
                    </label>
					<!--<a href="#" data-route="{{route('remove.customer.group')}}" data-id="{{$value->groupCustomerId}}" class="trigger-delete"><i style="cursor: pointer;" class="fa fa-trash "   aria-hidden="true"></i>
                   </a>-->
				   </td>
                </tr>
            @endforeach
            </tbody>
        </table>
      
    </div>
@endsection


@section('scripts')

    <script>
	
	
		$('body').on('click', '.__toggle', function(e) {
			var id = $(this).attr('data-id');
			var message_group_id = $('#message_group_id').val();
			e.preventDefault();
			var option = { _token: token, _method: 'post', customer_id:id, 'message_group_id': message_group_id };
			var route = "{{route('add.customer.group')}}";
			$.ajax({
				type: 'post',
				url: route,
				data: option,
				success: function(response) {
					toastr["success"](response.message); 
					setTimeout(function(){
                          location.reload();
                    }, 1000);
				},
				error: function(data) {
					alert('An error occurred.');
				}
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

		$(document).ready(function() {
		$(".customer-select").select2({
			dropdownParent: $("#addCustomer"),
			ajax: {
				url: base_url+"fetch/customers",
				dataType: 'json',
				delay: 250,
				data: function(params) {
					return {
						q: params.term // search term
					};
				},
				processResults: function(data, params) { 
					var resData = [];
					data.forEach(function(value) {
						if (value.email.indexOf(params.term) != -1)
							resData.push(value)
					});
					return {
						results: $.map(resData, function(item) {
							return {
								text: item.email,
								id: item.id
							}
						})
					};
				},
				cache: true
			},
			minimumInputLength: 3
		})
	});
    </script>
@endsection