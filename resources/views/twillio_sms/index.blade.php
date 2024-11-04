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
                    <h2 class="page-heading">Managing Group</h2>
                    
                  &nbsp;  <button type="button" class="btn btn-secondary float-right" data-toggle="modal"
                            data-target="#messageGroup"> Add Messaging Group </button>  &nbsp;
							&nbsp;<button type="button" class="btn btn-secondary float-right" data-toggle="modal"
                            data-target="#serviceModal"> Add Service </button>&nbsp;
							
							<div class="pull-left cls_filter_box">
								{{ html()->modelForm($inputs, 'GET', url()->current())->class('form-inline')->open() }}
									<div class="form-group ml-3 cls_filter_inputbox">
										<label for="leads_email">Status</label>
										{{ html()->select('status', ['' => 'Select', 'pending' => 'Pending', 'scheduled' => 'Scheduled', 'done' => 'Done'])->class('form-control') }}
									</div>
									<div class="form-group ml-3 cls_filter_inputbox">
										<label for="leads_email">Website</label>
										{{ html()->select('webiste', $websites)->class('form-control') }}
									</div>
									<div class="form-group ml-3 cls_filter_inputbox">
										<label for="leads_email">Title</label>
										{{ html()->text('title')->class('form-control') }}
									</div>
									<button type="submit" style="margin-top: 20px;padding: 5px;" class="btn btn-image"><img src="{{url('/images/filter.png')}}"/></button>
								</form>
							</div>
                </div>
            </div>
        </div>

    </div>
	
	<div class="modal fade" id="serviceModal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Service</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('create.message.service')}}"  method="POST" class="ajax-submit">
                        @csrf
                        <div class="form-group">
                            <label >Name</label>
                            <input required name="name" type="text" class="form-control name" placeholder="Enter name">
                        </div>
                       
                        <button id="btn" type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="messageGroup" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Create Group</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('create.message.group')}}"  method="POST" class="ajax-submit">
                        @csrf
                        <div class="form-group">
                            <label >Name</label>
                            <input required name="name" type="text" class="form-control name" placeholder="Enter name">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Store Website</label>
							{{ html()->select('store_website_id', $websites)->class('form-control')->required() }}
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Select Service</label>
                           {{ html()->select('service_id', $services)->class('form-control')->required() }}
                        </div>
                        <button id="btn" type="submit" class="btn btn-primary">Submit</button>
                    </form>
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


    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="passwords-table">
            <thead>
            <tr>
                <th style="">ID</th>
                <th style="">Name</th>
                <th style="">Store Website</th>
                <th style="">Service</th>
                <th style="">Message Title</th>
                <th style="">Scheduled at</th>
                <th style="">Is sent</th>
                <th style="">Customers Added</th>
                <th style="">Action</th>
            </thead>
            <tbody>
            @foreach($data as $key=>$value)
			@php $customerCount = \App\Helpers\TwilioHelper::getMessageGroupCountByMessageGroupId($value->id) @endphp
                <tr class="{{$value->id}}">
                    <td id="id">{{$key+1}}</td>
                    <td id="name">{{$value->name}}</td>
                    <td id="description">{{$value->website}}</td>
                    <td id="description">{{$value->service}}</td>
                    <td id="description">{{$value->title}}</td>
                    <td id="description">{{$value->scheduled_at}}</td>
                    <td id="description">@if($value->scheduled_at == null)Pending @elseif($value->is_sent == 1) Done @else Scheduled @endif</td>
                    <td id="description">{{$customerCount}}</td>
                    <td>
					    <a href="{{route('customer.group', ['groupId'=>$value->id])}}"><i class="fa fa-user-plus change" title="Add user" aria-hidden="true" ></i></a>
					    <a href="{{route('process.twillio.sms', ['groupId'=>$value->id])}}"><i class="fa fa-envelope change" title="Send Message Manually" aria-hidden="true" ></i></a>
					    <a  href="javascript:void(0);" onclick="showMessageTitleModal('{{$value->id}}');"><i class="fa fa-plus change" title="Add Marketing Message" aria-hidden="true" ></i></a>
						<a data-route="{{route('delete.message.group')}}" data-id="{{$value->id}}" class="trigger-delete">  <i style="cursor: pointer;" class="fa fa-trash " aria-hidden="true"></i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(isset($data))
            {{ $data->links() }}
        @endif
    </div>
@endsection


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

		function showMessageTitleModal(groupId){
			$('#message_group_id').val(groupId);
			$.get(base_url+"twillio/marketing/message/"+groupId, function(data, status){ 
				$('#messageTitleForm').html(data);
			});
			$('#messageTitle').modal('show');			
		}
    </script>
@endsection