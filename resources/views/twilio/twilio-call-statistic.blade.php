@extends('layouts.app')

@section('styles')
  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
@endsection
@section('large_content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Twilio Call Statistic </h2>
    </div>
</div>
<div class="mt-3 col-md-12">
    {{ html()->modelForm($input, 'GET', route('twilio.call.statistic'))->open() }}
    @csrf
    <div class="row">
      <div class="col-md-2 pd-sm">
        <h5>Account sid</h5>
        {{ html()->text('search_account_sid')->class('form-control') }}
      </div>
      <div class="col-lg-2">
        <h5>Twilio Number</h5>
        {{ html()->text('search_twilio_number')->class('form-control') }}
      </div>
      <div class="col-lg-2">
        <h5>Customer Number</h5>
        {{ html()->text('search_customer_number')->class('form-control') }}
      </div>
      <div class="col-lg-2">
        <h5>Select customer</h5>
        <select class="form-control globalSelect2" multiple="true" id="customer_names" name="customer_names[]" placeholder="Select Customers">
          <option value="">Select customer name</option>
          @foreach($customers as $customer)
          <option value="{{ $customer->id }}" @if(is_array($reqcustomerNames) && in_array($customer->id, $reqcustomerNames)) selected @endif >{{ $customer->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-lg-2">
        <h5>Twilio Credential</h5>
        <select class="form-control globalSelect2" multiple="true" id="twilicondition_email" name="twilicondition_email[]" placeholder="Twilio Credential">
          <option value="">Select Twilio Credential</option>
          @foreach($twiliconditionsemails as $twiliconditionsemail)
          <option value="{{ $twiliconditionsemail->id }}" @if(is_array($reqtwiliconditionEmail) && in_array($twiliconditionsemail->id, $reqtwiliconditionEmail)) selected @endif>{{ $twiliconditionsemail->twilio_email }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-lg-2">
        <h5>Customer Website</h5>
        <select class="form-control globalSelect2" multiple="true" id="customer_websites" name="customer_websites[]" placeholder="Select Customer Website">
          <option value="">Select customer Website</option>
          @foreach($storeWebsites as $storeWebsite)
          <option value="{{ $storeWebsite->id }}" @if(is_array($reqCustomerWebsites) && in_array($storeWebsite->id, $reqCustomerWebsites)) selected @endif>{{ $storeWebsite->website }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-lg-2">
        <h5>Twilio Number Website</h5>
        <select class="form-control globalSelect2" multiple="true" id="twilio_websites" name="twilio_websites[]" placeholder="Select Twilio Number Website">
          <option value="">Select Twilio Number Website</option>
          @foreach($storeWebsites as $storeWebsite)
          <option value="{{ $storeWebsite->id }}" @if(is_array($reqTwilioWebsites) && in_array($storeWebsite->id, $reqTwilioWebsites)) selected @endif>{{ $storeWebsite->website }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-lg-2">
        <br><br>
        <button type='submit' class="btn btn-default">Search</button>
        <a href="{{route('twilio.call.statistic')}}" class="btn btn-default">Clear</a>
      </div>
    </div>
    {{ html()->closeModelForm() }}
  </div>
  
    <div class="row">
        <div class="col-lg-12 margin-tb">
			<div class="panel-group" style="margin-bottom: 5px;">
                <div class="panel mt-3 panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
							Twilio Call Statistic
                        </h4>
                    </div>
					<div class="panel-body">
						<table class="table table-bordered table-hover" style="table-layout:fixed;">
							<thead>
								<tr>
									<th style="width:5%"><input type="checkbox" id="callIdAll"/></th>
									<th style="width:5%">ID</th>
									<th style="width:10%">Account sid</th>
									<th style="width:10%">Customer Number</th>
									<th style="width:10%">Twilio Number</th>
									<th style="width:10%">Customer </th>
									<th style="width:10%">Twilio Credential</th>
									<th style="width:10%">Customer Website</th>
									<th style="width:10%">Twilio Number Website</th>
									<th style="width:10%">Date</th>
								</tr>
							</thead>
							<tbody>
							@foreach ($twilioCallStatistic as $val )                            
								<tr id = "row_{{$val->id}}">
									<td style="width:5%"><Input type="checkbox" id="callId[]" name="callId[]" value="{{$val->id}}"/></td>
									<td style="width:5%">{{$val->id}}</td>
									<td style="width:10%;overflow-wrap: break-word">{{$val->account_sid}}</td>
									<td style="width:10%;overflow-wrap: break-word">{{$val->customer_number}}</td>
									<td style="width:10%;overflow-wrap: break-word">{{$val->twilio_number}}</td>
									<td style="width:10%;overflow-wrap: break-word">{{$val->customerName}}</td>
									<td style="width:10%;overflow-wrap: break-word">{{$val->twilio_email}}</td>
									<td style="width:10%;overflow-wrap: break-word">{!! $val->customerWebsite !!}</td> 
									<td style="width:10%;overflow-wrap: break-word">{!! $val->twWebsite !!}</td>
									<td style="width:10%;overflow-wrap: break-word">{{date('d-M-Y', strtotime($val->created_at))}}</td>
								</tr>
							@endforeach
							</tbody>
						</table>
						{{ $twilioCallStatistic ->appends($input)->links() }}
                    </div>
                </div>
            </div>
		</div>
	</div>	
	<script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-multiselect.min.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.jscroll.min.js') }} "></script>
    <script>
        $(function(){
            $('#callIdAll').click(function(){
                var idchecked = $('input:checkbox').not(this).prop('checked', this.checked);
                if($("#sugIdAll").prop('checked') == true){
                    //alert('Yes');
                }
            });
        });
            
        $('.delete-all-record').on('click', function(e) {
            var val = [];
            $('input[name="callId[]"]:checkbox:checked').each(function(i, elem) {
                val[i] = $(this).val();
            });

            if(val.length == 0) {
                alert("Please select any one row you want to delete record!!!");
            } else {
                if(confirm('Are you sure really want to Delete records?')) {
                    e.preventDefault();
                    var ids = val.toString();
                    $.ajax({
                        url: '{{route("twilio.call.statistic.delete")}}',
                        type:"get",
                        data: { 
                                "_token": $('meta[name="csrf-token"]').attr('content'),
                                ids : ids
                                },
                        dataType: 'json',
                    }).done(function (response) {
                        if(response.code == 200) {
                            toastr['success'](response.message);
                            location.reload();
                        }else{
                            errorMessage = response.message ? response.message : 'Record not found!';
                            toastr['error'](errorMessage);
                        }        
                    }).fail(function (response) {
                        toastr['error'](response.message);
                    });
                }
            }
        });
	</script>
@endsection
