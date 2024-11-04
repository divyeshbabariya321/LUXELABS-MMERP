@extends('layouts.app')
@section('favicon' , 'productstats.png')
@section('title', 'Twilio Message Tones')
@section('content')
    <?php $base_url = URL::to('/');?>
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Twilio Manage Tones</h2>
        </div>
        <div class="mt-3 col-md-12">
            <form action="{{ route('twilio.view_tone') }}" method="get" class="search">
                <div class="form-group col-md-2">
					<h5>Search websites</h5>
                    {{ html()->multiselect("website_ids[]", \App\StoreWebsite::pluck('website', 'id')->toArray(), request('website_ids'))->class("form-control globalSelect2")->data('placeholder', "Select Website") }}
                </div>
                <div class="form-group col-md-2"><br><br>
                    <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                        <img src="{{ asset('images/search.png') }}" alt="Search">
                    </button>
                    <a href="{{ route('twilio.view_tone') }}" class="btn btn-image" id="">
                        <img src="/images/resend2.png" style="cursor: nwse-resize;">
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row pl-5 pr-5 twilio-manage-tones">
        @if ($twilioMessageTones->isEmpty())
            <p>No message tones found.</p>
        @else
			<div class="col-md-12">
				<div class="col-md-1 p-2  border-top border-bottom border-right border-left"><strong>#</strong></div>
				<div class="col-md-2 p-2 border-top border-bottom border-right border-left"><strong>Store Website</strong></div>
				<div class="col-md-2 p-2 border-top border-bottom border-right border-left"><strong>End work ring</strong></div>
				<div class="col-md-2 p-2 border-top border-bottom border-right border-left"><strong>Intro ring</strong></div>
				<div class="col-md-2 p-2  border-top border-bottom border-right border-left"><strong>Busy ring</strong></div>
				<div class="col-md-2 p-2  border-top border-bottom border-right border-left"><strong>Wait URL ring</strong></div>
				<div class="col-md-1 p-2 border-top border-bottom border-right border-left"><strong>Action</strong></div>
			</div> 
            @foreach($twilioMessageTones as $i => $twilioMessageTone)
			@if (empty($websiteIds) || in_array($twilioMessageTone->websiteId, $websiteIds))
			<div class="col-md-12 ">
            {{ html()->form('POST', url('twilio/save-message-tone'))->acceptsFiles()->class('ajax-submit')->open() }}
						<div class="col-md-1 p-2  border-top border-bottom border-right border-left" style="height: 46px !important;">{{ $i+1 }} {{ html()->hidden('store_website_id', $twilioMessageTone['websiteId']) }}</div>

						<div class="col-md-2 Website-task p-2  border-top border-bottom border-right border-left" style="height: 46px !important;">{{ $twilioMessageTone->website }}</div>

						<div class="col-md-2 p-2  border-top border-bottom border-right border-left"><input type="file" name="end_work_ring" class="w-100">
							@if($twilioMessageTone->end_work_ring != null)
								<div class="d-flex">
									<audio src="{{url('twilio/'.rawurlencode($twilioMessageTone->end_work_ring))}}" controls="" preload="metadata">
									</audio>
								</div>
							@endif
						</div>
						<div class="col-md-2 p-2  border-top border-bottom border-right border-left"><input type="file" name="intro_ring" class="w-100">
							@if($twilioMessageTone->intro_ring != null)
								<div class="d-flex">
									<audio src="{{url('twilio/'.rawurlencode($twilioMessageTone->intro_ring))}}" controls="" preload="metadata">
									</audio>
								</div>
							@endif
						</div>
						<div class="col-md-2 p-2  border-top border-bottom border-right border-left"><input type="file" name="busy_ring" class="w-100">
							@if($twilioMessageTone->busy_ring != null)
								<div class="d-flex ">
									<audio src="{{url('twilio/'.rawurlencode($twilioMessageTone->busy_ring))}}" controls="" preload="metadata">
									</audio>
								</div>
							@endif
						</div>
						<div class="col-md-2 p-2  border-top border-bottom border-right border-left"><input type="file" name="wait_url_ring" class="w-100">
							@if($twilioMessageTone->wait_url_ring != null)
								<div class="d-flex ">
									<audio src="{{url('twilio/'.$twilioMessageTone->wait_url_ring)}}" controls="" preload="metadata">
									</audio>
								</div>
							@endif
						</div>
						<div class="col-md-1 p-2  border-top border-bottom border-right border-left"><button class="btn btn-secondary" type="submit" >Save</button></div>
					</form>
                    </div>
                @endif
            @endforeach
        @endif
    </div>
@endsection

@section('scripts')
    <script>
    		$('.ajax-submit').on('submit', function(e) { 
			e.preventDefault(); 
			$.ajax({
                type: $(this).attr('method'),
				url: $(this).attr('action'),
				data: new FormData(this),
				processData: false,
				contentType: false,
				success: function(data) { console.log(data);
					if(data.statusCode == 400) { 
					$.each(data.errors, function( index, value ) {
						toastr["error"](value);
					});
						
					} else {
						toastr["success"](data.message);
						/*setTimeout(function(){
                          location.reload();
                        }, 1000);*/
 
					}
				},
				done:function(data) {
					console.log('success '+data);
				}
            });
		});
    </script>
@endsection
