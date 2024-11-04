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
                    <h2 class="page-heading">Twilio Errors</h2>
                    <div class="mt-3 col-md-12">
                        <form action="{{route('twilio.errors')}}" method="get" class="search">
                            <div class="col-md-2 pd-sm">
                                <input class="form-control" type="text" id="sid" placeholder="Search SID" name="sid" value="{{ (request('sid') ?? "" )}}">
                            </div>
                            <div class="col-lg-2">
                                <input class="form-control" type="text" id="account_sid" placeholder="Search Account SID" name="account_sid" value="{{ (request('account_sid') ?? "" )}}">
                            </div>
                            <div class="col-lg-2">
                                <input class="form-control" type="text" id="call_sid" placeholder="Search Call SID" name="call_sid" value="{{ (request('call_sid') ?? "" )}}">
                            </div>
                            <div class="col-lg-2">
                                <input class="form-control" type="text" id="error_code" placeholder="Search Error Code	" name="error_code" value="{{ (request('error_code') ?? "" )}}">
                            </div>
                            <div class="col-lg-2">
                                <input class="form-control" type="text" id="message" placeholder="Search Message" name="message" value="{{ (request('message') ?? "" )}}">
                            </div>
                            <div class="col-lg-2">
                                <input class="form-control" type="date" name="date" value="{{ (request('date') ?? "" )}}">
                            </div>
                            <div class="col-lg-2"><br>
                                <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                                   <img src="{{ asset('images/search.png') }}" alt="Search">
                               </button>
                               <a href="{{route('twilio.errors')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                            </div>
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
                <th style="">SID</th>
                <th style="">Account SID</th>
                <th style="">Call SID</th>
                <th style="">Error Code</th>
                <th style="">Message Text</th>
                <th style="">Message Date</th>
                <th style="">Action</th>
            </thead>
            <tbody>
            @foreach($data as $key=>$value)
                <tr class="{{$value->id}}">
                    <td id="id">{{$key+1}}</td>
                    <td id="name">{{$value->sid}}</td>
                    <td id="description">{{$value->account_sid}}</td>
                    <td id="description">{{$value->call_sid}}</td>
                    <td id="description">{{$value->error_code}}</td>
                    <td id="description">{{urldecode($value->message_text)}}</td>
                    <td id="description">{{$value->message_date}}</td>
                    <td>
						<a data-route="{{route('delete.twilio.error')}}" data-id="{{$value->id}}" class="trigger-delete">  <i style="cursor: pointer;" class="fa fa-trash " aria-hidden="true"></i></a>
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
