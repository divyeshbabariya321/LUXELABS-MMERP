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
                    <h2 class="page-heading">Scheduled Messages</h2>
				    <div class="pull-left cls_filter_box">
                        {{ html()->modelForm([], 'GET', url()->current())->class('form-inline')->open() }}
                            <div class="form-group ml-3 cls_filter_inputbox">
                                <label for="leads_email">Message</label>
                                {{ html()->text('message', $message)->class('form-control') }}
                            </div>
                            <div class="form-group ml-3 cls_filter_inputbox">
                                <label for="leads_email">Message Type</label>
                                {{ html()->select('message_application_id', $types, $type)->class('form-control') }}
                            </div>
                            <div class="form-group ml-3 cls_filter_inputbox">
                                <label for="leads_email">Is Queue</label>
                                {{ html()->select('is_queue', $is_queues, $type)->class('form-control') }}
                            </div>
                            <button type="submit" style="margin-top: 20px;padding: 5px;" class="btn btn-image"><img src="{{url('/images/filter.png')}}"/></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
	
   
    @include('partials.flash_messages')

    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="passwords-table">
            <thead>
            <tr>
                <th style="">ID</th>
                <th style="">Task</th>
                <th style="">Customer</th>
                <th style="">Message</th>
                <th style="">Status</th>
                <th style="">Is Queue</th>
                <th style="">Approved</th>
                <th style="">User ID</th>
                <th style="">Message Type</th>
                <th style="">Scheduled At</th>
                <th style="">Action</th>
            </thead>
            <tbody>
			@php $i=1; @endphp	
            @foreach($messages as $key=>$value)
                <tr class="{{$value->id}}">
                    <td id="id">{{$i}}</td>
                    <td id="task">@if($value->task_id != 0) {{$value->task_id}} @elseif($value->developer_task_id != 0) DEVTASK-{{$value->developer_task_id}} @endif</td>
                    <td id="name">{{$value->customer_name}}</td>
                    <td id="name">{{$value->message}}</td>
                    <td id="name">{{$value->status}}</td>
                    <td id="name">
                        @if($value->is_queue == 0) {{-- message has been sent --}}
                            No
                        @else {{-- message has not been sent --}}
                            Yes
                        @endif
                    </td>
                    
                    <td id="name">
                        @if($value->approved == 1) {{-- message has been approved --}}
                            Yes
                        @else {{-- message has not been approved --}}
                            No
                        @endif
                    </td>
                    <td id="name">{{$value->user_name}}</td>
                    <td id="name">{{($value->message_application_id==3)?'SMS':'Whatsapp'}}</td>
                    <td id="name">{{$value->scheduled_at}}</td>
                    <td>
                    <button type="button" class="btn btn-image edit-email-addresses d-inline"  data-toggle="modal" data-target="#emailAddressEditModal" data-email-addresses="{{ json_encode($value) }}"><img src="/images/edit.png" /></button>
						<a data-route="{{route('flow.delete-message')}}" data-id="{{$value->id}}" class="trigger-delete">  <i style="cursor: pointer;" class="fa fa-trash " aria-hidden="true"></i></a>
                    </td>
                </tr>
				@php $i++; @endphp
            @endforeach
            </tbody>
        </table>
        @if(isset($messages))
            {{ $messages->links() }}
        @endif
    </div>

    <div id="emailAddressEditModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="" method="POST" enctype="multipart/form-data" >
        @csrf
        @method('POST')
        <input type="hidden" name="id" class="form-control" value="">
        <div class="modal-header">
          <h4 class="modal-title">Update Data</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <strong>Message:</strong>
            <input type="text" name="message" class="form-control" value="{{ old('message') }}" required>

            @if ($errors->has('message'))
              <div class="alert alert-danger">{{$errors->first('message')}}</div>
            @endif
          </div>
		  
		   <div class="form-group">
            <strong>Approve:</strong>
			{{ html()->select('approved', [1 => 'Yes', 0 => 'No'])->class('form-control')->id('approved') }}
          </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Update</button>
        </div>
      </form>
    </div>

  </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
         50% 50% no-repeat;display:none;">
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
      <script type="text/javascript">
    $(document).on('click', '.edit-email-addresses', function() {
      var data = $(this).data('email-addresses');
      var url = "{{ route('flow.update-message') }}";

      $('#emailAddressEditModal form').attr('action', url);
      $('#emailAddressEditModal').find('input[name="id"]').val(data.id);
      //$('#emailAddressEditModal').find('input[name="approved"]').val(data.approved);
	  var approved = 1;
	  if(data.approved == false) {
		  approved = 0;
	  }
	   $('#approved').val(approved); console.log(data.approved);
	   $('#emailAddressEditModal').find('input[name="message"]').val(data.message);
      
    });
</script>
@endsection