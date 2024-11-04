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
                    <h2 class="page-heading">Customer Reviews</h2>

							<div class="pull-left cls_filter_box">
								{{ html()->modelForm([], 'GET', url()->current())->class('form-inline')->open() }}

									<div class="form-group ml-3 cls_filter_inputbox">
										<label for="leads_email">Email</label>
										{{ html()->text('email', $email)->class('form-control') }}
									</div>
                                    <div class="form-group ml-3 cls_filter_inputbox">
										<label for="leads_email">Name</label>
										{{ html()->text('name', $name)->class('form-control') }}
									</div>
                                    <div class="form-group ml-3 cls_filter_inputbox">
										<label for="leads_email">Store Website</label>
										{{ html()->text('store', $store)->class('form-control') }}
									</div>
									<button type="submit" style="margin-top: 20px;padding: 5px;" class="btn btn-image"><img src="{{url('/images/filter.png')}}"/></button>
								</form>
							</div>
                </div>
            </div>
        </div>

    </div>


    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="passwords-table">
            <thead>
            <tr>
                <th style="">ID</th>
                <th style="">Email</th>
                <th style="">Name</th>
                <th style="">Store Website</th>
                <th style="">Platform Id</th>
                <th style="">Star Rating</th>
                <th style="">Comment</th>
                <th style="">Status</th>
                <th style="">Action</th>
            </thead>
            <tbody>
            @foreach($reviews as $key=>$value)
                <tr class="{{$value->id}}">
                    <td id="id">{{$key+1}}</td>
                    <td id="name">{{$value->email??'N/A'}}</td>
                    <td id="name">{{$value->name??'N/A'}}</td>
                    <td id="name">{{$value->storeWebsite->website??'N/A'}}</td>
                    <td id="description">{{$value->platform_id??'N/A'}}</td>
                    <td id="description">{{$value->stars??'N/A'}}</td>
                    <td id="description">{{$value->comment??'N/A'}}</td>
                    @if($value->status)
                    <td id="description"><button class="btn btn-success">Approved</button></td>
                    @else
                    <td id="description"><button class="btn btn-danger click-approve" data-route="{{route('product.click-approve')}}" data-platform="{{$value->platform_id}}" data-url="{{$value->storeWebsite->magento_url}}" data-token="{{$value->storeWebsite->api_token}}">Click to approve</button></td>
                    @endif

                    <td>
						<a data-route="{{route('product.delete-review')}}" data-id="{{$value->id}}" class="trigger-delete">  <i style="cursor: pointer;" class="fa fa-trash " aria-hidden="true"></i></a>
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

        $('.click-approve').on('click', function(e) {
            e.preventDefault();
            var platform_id = $(this).attr('data-platform');
			var base_url = $(this).attr('data-url');
			var token = $(this).attr('data-token');
            var route = $(this).attr('data-route');
			var option = { _token: "{{ csrf_token() }}", platform_id:platform_id, base_url:base_url,token:token };
			$("#loading-image").show();
			$.ajax({
				type: 'post',
				url: route,
				data: option,
				success: function(response) {
					$("#loading-image").hide();
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
