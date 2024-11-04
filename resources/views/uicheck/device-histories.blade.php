@extends('layouts.app')
@section('favicon', '')

@section('title', 'Ui Device Histories')

@section('styles')
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
    </style>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">UI Device Estimated Time Histories</h2>
            <div class="pull-left">
                <div class="form-group">
                    <div class="row">
                        <form class="form-inline message-search-handler" method="get">
                            <div class="form-group m-1">
                                <?php 
									if(request('category')){   $categoriesArr = request('category'); }
									else{ $categoriesArr = ''; }
								?>
								<select name="category" id="store-categories" class="form-control select2">
									<option value="" @if($categoriesArr=='') selected @endif>-- Select a categories --</option>
									@forelse($siteDevelopmentCategories as $sdcId => $siteDevelopmentCategory)
									<option value="{{ $sdcId }}" @if($categoriesArr==$sdcId) selected @endif>{!! $siteDevelopmentCategory !!}</option>
									@empty
									@endforelse
								</select>
                            </div>
                            @if (Auth::user()->isAdmin())
							<div class="form-group m-1">
                                <?php 
									if(request('user_name')){   $userNameArr = request('user_name'); }
									else{ $userNameArr = []; }
								?>
								<select name="user_name[]" id="user_name" class="form-control select2" multiple>
									<option value="" @if($userNameArr=='') selected @endif>-- Select a User --</option>
									@forelse($allUsers as $uId => $uName)
									<option value="{{ $uName->id }}" @if(in_array($uName->id, $userNameArr)) selected @endif>{!! $uName->name !!}</option>
									@empty
									@endforelse
								</select>
                            </div>
                            @endif
                            <div class="form-group">
                                <label for="button">&nbsp;</label>
                                <button type="submit" style="display: inline-block;width: 10%"
                                    class="btn btn-sm btn-image btn-search-action">
                                    <img src="/images/search.png" style="cursor: default;">
                                </button>
                                <a href="/uicheck/device-histories" class="btn btn-image" id="">
									<img src="/images/resend2.png" style="cursor: nwse-resize;">
								</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th style="max-width: 150px">Categories</th>
                    <th>Website</th>
					@if (Auth::user()->isAdmin())
                    <th>User Name</th>
					@endif
                    <th>Device No</th>
                    <th>Message</th>
                    <th>Estimated Time</th>
                    <th>Expected Completion Time</th>
                    <th>Status</th>
                    <th>Is Time Approved</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($uiDeviceHistories as $key => $uiDeviceHistory)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td style="max-width: 150px">
                            <div data-message="{{ $uiDeviceHistory->title }}" data-title="Title" style="cursor: pointer"
                                class="showFullMessage">
                                {{ show_short_message($uiDeviceHistory->title, 25) }}
                            </div>
                        </td>
                        <td>
                            {{ $uiDeviceHistory->website }}
                        </td>
						@if (Auth::user()->isAdmin())
                        <td>{{ $uiDeviceHistory->name }}</td>
						@endif
                        <td>{{ $uiDeviceHistory->uiDevice->device_no }}</td>
                        <td style="max-width: 150px">
                            <div data-message="{{ $uiDeviceHistory->message }}" data-title="Message" style="cursor: pointer"
                                class="showFullMessage">
                                {{ show_short_message($uiDeviceHistory->message, 25) }}
                            </div>
                        </td>
                        <td>{{ $uiDeviceHistory->estimated_time }} @if ($uiDeviceHistory->estimated_time) Mins @endif</td>
                        <td>{{ $uiDeviceHistory->expected_completion_time }}</td>
                        <td>
                            <div class="select">
                                <select class="form-control historystatus" name="status" id="status" data-id="{{$uiDeviceHistory->id}}">
                                    <option value="">Select</option>
                                    @forelse($siteDevelopmentStatuses as $sID => $siteDevelopmentStatus)
									<option value="{{ $sID }}" {{$uiDeviceHistory->status == $sID ? 'selected' : ''}}>{!! $siteDevelopmentStatus !!}</option>
									@empty
									@endforelse
                                </select>
                           </div>
                        </td>
                        <td>
                            <div class="select">
                                <select class="form-control is_time_approve" name="is_time_approve" id="is_time_approve" data-id="{{$uiDeviceHistory->id}}">
                                    <option value="0">Select</option>
                                    <option {{$uiDeviceHistory->is_estimated_time_approved == 1 ? 'selected' : ''}} value="1">Approve</option>
                                </select>
                           </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {!! $uiDeviceHistories->appends(request()->except('page'))->links() !!}

    <div id="showFullMessageModel" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script type="text/javascript">
		$('.select2').select2();
        $("#search_user").select2();
        $('#search_task').select2({
            minimumInputLength: 3 // only start searching when the user has input 3 or more characters
        });

        $(document).on("click", ".showFullMessage", function() {
            let title = $(this).data('title');
            let message = $(this).data('message');

            $("#showFullMessageModel .modal-body").html(message);
            $("#showFullMessageModel .modal-title").html(title);
            $("#showFullMessageModel").modal("show");
        });

        $(document).on("change", ".historystatus", function(e) {
            var id = $(this).data("id");
            var status_id = $(this).val();
            $.ajax({
                url: "{{route('uicheck.device.status')}}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                dataType:"json",
                data: { id : id, status_id:status_id},
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function (response) {
                $("#loading-image").hide();
                toastr["success"](response.message);
            }).fail(function (jqXHR, ajaxOptions, thrownError) {      
                toastr["error"](jqXHR.responseJSON.message);
                $("#loading-image").hide();
            });
        });

        $(".is_time_approve").change(function(){
            var id = $(this).data('id');
            var isEstimatedTimeApproved = $(this).val();

            $.ajax({
                type: "POST",
                url: "{{ route('uicheck.device-history.time-approve') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    isEstimatedTimeApproved : isEstimatedTimeApproved,
                    id: id,
                },
                dataType : "json",
                success: function (response) {
                    if(response.code == 200) {
                        toastr['success'](response.messages);
                    }else{
                        toastr['error'](response.messages);
                    }
                },
                error: function () {
                    toastr['error']('Message not sent successfully!');
                }
            });
        });

		$(function() {
			$('input[name="daterange"]').daterangepicker({
				opens: 'left'
			}, function(start, end, label) {
				console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
			});
		});
    </script>
@endsection
