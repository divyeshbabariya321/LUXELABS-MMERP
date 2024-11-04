@extends('layouts.app')

@section('styles')
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Customer Account ({{$total}})</h2>
            <div class="pull-left">
              <form class="form-inline" action="{{url('customers/accounts')}}" method="GET">
                    <div class="col">
                  <div class="form-group">
                    <div class='input-group'>
                        <select class="form-control name" name="name[]" multiple>
                            @foreach($customers_name as $s)
                                @php
                                    $sel='';
                                    if(isset($_GET['name']) && in_array($s->name,$_GET['name']))
                                        $sel="selected='selected'";
                                @endphp
                                <option {{ $sel}} value="{{$s->name}}">{{$s->name}} </option>
                            @endforeach
                        </select>
                    </div>
                  </div>
                </div>
                    <div class="col">
                  <div class="form-group">
                    <div class='input-group'>
                        <select class="form-control email" name="email[]" multiple>
                            @foreach($customers_email as $s)
                                @php
                                    $sel='';
                                    if(isset($_GET['email']) && in_array($s->email,$_GET['email']))
                                        $sel="selected='selected'";
                                @endphp
                                <option {{ $sel}} value="{{$s->email}}">{{$s->email}} </option>
                            @endforeach
                        </select>
                    </div>
                  </div>
                </div>
                    <div class="col">
                      <div class="form-group">
                        <div class='input-group'>
                            <select class="form-control phone" name="phone[]" multiple>
                                @foreach($customers_phone as $s)
                                    @php
                                        $sel='';
                                        if(isset($_GET['phone']) && in_array($s->phone,$_GET['phone']))
                                        $sel="selected='selected'";
                                    @endphp
                                    <option {{ $sel}} value="{{$s->phone}}">{{$s->phone}} </option>
                                @endforeach
                            </select>
                        </div>
                      </div>
                    </div>
                    <div class="col">
                      <div class="form-group">
                          <div class='input-group'>
                              <input type='date' placeholder="Start Date" class="form-control" name="from_date"  value="{{ isset($_GET['from_date'])?$_GET['from_date']:''}}"  />
                          </div>
                      </div>
                  </div>
                    <div class="col">
                      <div class="form-group">
                          <div class='input-group'>
                              <input type='date' placeholder="End Date" class="form-control" name="to_date"  value="{{ isset($_GET['to_date'])?$_GET['to_date']:''}}"  />
                          </div>
                      </div>
                  </div>
                    <div class="col">
                  <div class="form-group">
                    <div class='input-group'>
                      <select class="form-control store_website" name="store_website[]" multiple>
                              @foreach($store_website as $s)
                                @php
                                  $sel='';
                                  if(isset($_GET['store_website']) && in_array($s->id,$_GET['store_website']))
                                        $sel="selected='selected'";
                                @endphp

                              <option {{ $sel}} value="{{$s->id}}">{{$s->title}} </option>
                             @endforeach
                        </select>
                    </div>
                  </div>
                </div>
                    <div class="col">
                  <button type="submit" class="btn btn-image"><img src="{{asset('/images/filter.png')}}" /></button>
                </div>
                </form>
            </div>
            <div class="pull-right">
            
            </div>
        </div>
    </div>
    <div class="modal" tabindex="-1" role="dialog" id="reply_logs_modal">
      <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title">Name</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body">
                  <div class="row">
                      <div class="col-md-12" id="reply_logs_div">
                          <table class="table">
                              <thead>
                                  <tr>
                                  </tr>
                              </thead>
                              <tbody>
                              </tbody>
                          </table>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
          </div>
      </div>
  </div>

    @include('partials.flash_messages')


    <div class="table-responsive mt-3" style="overflow-x: auto !important;">
      <table class="table table-bordered">
        <thead>
          <tr>
           <th width="1%">ID</th>
            <th width="5%">Name</th>
            <th width="4%">Email</th>
            <th width="4%">Phone</th>
            <th width="6%">Date</th>
            <th width="6%">Whatsapp Number</th>
            <th width="12%">Address</th>
            <th width="5%">City</th>
            <th width="5%">Pincode</th>
            <th width="5%">Country</th>
            <th width="6%">Store Website</th>
            <th width="6%">Platform Id</th>
            <th width="2%">Action</th>

          </tr>
        </thead>

        <tbody class="pending-row-render-view infinite-scroll-cashflow-inner">
          @foreach ($customers_all as $c)
            <tr>
              <td>{{ $c->id }}</td>
              <td> {{ strlen($c->name) > 10 ? substr($c->name, 0, 7).'...' : $c->name }}
                <i class="fa fa-eye show_logs show-logs-icon" data-id="{{ $c->id }}" style="color: #808080;float: right;"></i></td>
              <td>{{ $c->email }}</td>
              <td>{{ $c->phone }}</td>
              <td>{{ date("d-m-Y",strtotime($c->created_at)) }}</td>
              <td>{{ $c->whatsapp_number }}</td>
              <td>{{ $c->address }}</td>
              <td>{{ $c->city }}</td>
              <td>{{ $c->pincode }}</td>
              <td>{{ $c->country }}</td>
              <td>{{ $c->title }}</td>
              <td>{{ $c->platform_id }}</td>
              <td><a href="#" onClick="openInfo({{$c}})"><i class="fa fa-edit"></i></a>
			  <a href="#" onClick="showMessagePopup({{$c->id}})"><i class="fa fa-eye"></i></a></td>
            </tr>
          @endforeach
        

        

         
        </tbody>
      </table>
    </div>

    <img class="infinite-scroll-products-loader center-block" src="{{asset('/images/loading.gif')}}" alt="Loading..." style="display: none" />

	<div id="customer_edit" class="modal fade" role="dialog">
		<div class="modal-dialog">
		<!-- Modal content-->
			<div class="modal-content">
			  <div class="modal-header">
				<h4 class="modal-title">Edit Customer</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			  </div>

			  <form action="{{ url('customer/update') }}" method="POST" enctype="multipart/form-data" class="ajax-submit" novalidate="true">
				@csrf
				<input type="hidden" name="customer_id" value="" id="customer_id">
				<div class="modal-body">
				    <div class="form-group">
					    <strong>Name</strong>
					    <input type="text" class="form-control" name="name" value="" required id="name">
				    </div>

					<div class="form-group">
					    <strong>Email</strong>
					    <input type="text" class="form-control" name="email" value="" required id="email">
				    </div>	

					<div class="form-group">
					    <strong>Phone</strong>
					    <input type="text" class="form-control" name="phone" value="" required id="phone">
				    </div>	

					<div class="form-group">
					    <strong>Address</strong>
					    <input type="text" class="form-control" name="address" value="" required id="address">
				    </div>	

					<div class="form-group">
					    <strong>City</strong>
					    <input type="text" class="form-control" name="city" value="" required id="city">
				    </div>	

					<div class="form-group">
					    <strong>Pincode</strong>
					    <input type="text" class="form-control" name="pincode" value="" required id="pincode">
				    </div>	

					<div class="form-group">
					    <strong>Country</strong>
					    <input type="text" class="form-control" name="country" value="" required id="country">
				    </div>				
					<div class="form-group">
					    <strong>Platform Id</strong>
					    <input type="text" class="form-control" name="platform_id" value="" required id="platform_id">
				    </div>				
					
				</div>
				<div class="modal-footer">
				  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				  <button type="submit" class="btn btn-secondary">Send</button>
				</div>
			  </form>
			</div>
		</div>
	</div>
	
	<div id="customer_history" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
		<!-- Modal content-->
			<div class="modal-content">
			  <div class="modal-header">
				<h4 class="modal-title">Customer History</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			  </div>
			   <table class="table table-bordered">
				<thead>
				  <tr>
				    <th>ID</th>
					<th>Name</th>
					<th>Email</th>
					<th>Phone</th>
					<th>Address</th>
					<th>City</th>
					<th>Pincode</th>
					<th>Country</th>
				  </tr>
				</thead>
				<tbody id="history">
				
				</tbody>
				</table>
			  
			</div>
		</div>
	</div>

   

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script>
    $(document).ready(function (){
        $('.store_website').select2({
            placeholder:'Select Store Website',
        });
        $('.phone').select2({
            placeholder:'Select Phone',
        });
        $('.email').select2({
            placeholder:'Select Email',
        });
        $('.name').select2({
            placeholder:'Select Name',
        });
    });

    $.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': "{{ csrf_token() }}"
		}
	});   
        var isLoading = false;
        var page = 1;
        $(document).ready(function () {
            
            $(window).scroll(function() {
                if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
                    loadMore();
                }
            });

            function loadMore() {
                if (isLoading)
                    return;
                isLoading = true;
                var $loader = $('.infinite-scroll-products-loader');
                page = page + 1;
                $.ajax({
                    url: "{{url('customers/accounts')}}?ajax=1&page="+page,
                    type: 'GET',
                    data: $('.form-search-data').serialize(),
                    beforeSend: function() {
                        $loader.show();
                    },
                    success: function (data) {
                        
                        $loader.hide();
                        if('' === data.trim())
                            return;
                        $('.infinite-scroll-cashflow-inner').append(data);
                        

                        isLoading = false;
                    },
                    error: function () {
                        $loader.hide();
                        isLoading = false;
                    }
                });
            }            
        });

        function openInfo(details) {
		   $('#customer_id').val(details.id);
		   $('#name').val(details.name);
		   $('#email').val(details.email);
		   $('#phone').val(details.phone);
		   $('#address').val(details.address);
		   $('#city').val(details.city);
		   $('#pincode').val(details.pincode);
		   $('#country').val(details.country);
		   $('#platform_id').val(details.platform_id);
		   $('#customer_edit').modal('show');
	    }
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
		
	function showMessagePopup(customerId) { 
		$.get(window.location.origin+"/customer/update/history/"+customerId, function(data){ 
			$('#history').html(data.records);
			$('#customer_history').modal('show');
		});
	}

  $(document).on('click', '.show-logs-icon', function() {
		var id = $(this).data('id');
			$.ajax({
				url: '{{route('customer.name.show')}}',
				method: 'GET',
				data: {
					id: id
				},
				success: function(response) {
					$('#reply_logs_modal').modal('show');
					$('#reply_logs_div').html(response);
				},
				error: function(xhr, status, error) {
					alert("Error occured.please try again");
				}
			});
		});

  </script>   
  
@endsection
