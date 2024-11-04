@extends('layouts.app')

@section('large_content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Twilio Delivery Logs</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form action="{{route('twilio.twilio_delivery_logs')}}" method="get" class="search">
                <div class="form-group col-md-2">
                    <h5>Search Email ID</h5>
                    <select class="form-control globalSelect2" multiple="true" id="twilicustomer_email" name="twilicustomer_email[]" placeholder="Twilio AccountEmails">
                        @foreach($twiliCoustomerEmails as $twiliCoustomerEmail)
                        <option value="{{ $twiliCoustomerEmail->id}}" 
                        @if(is_array(request('twilicustomer_email')) && in_array($twiliCoustomerEmail->id, request('twilicustomer_email')))
                            selected
                        @endif >{{ $twiliCoustomerEmail->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <h5>Message ID</h5>	
                    <input class="form-control" type="text" id="message_id" placeholder="Search Message ID" name="message_id" value="{{ (request('message_id') ?? "" )}}">
                </div>
                <div class="form-group col-md-2">
                    <h5>Search To User</h5>	
                    <input class="form-control" type="text" id="user_to" placeholder="Search To Users" name="user_to" value="{{ (request('user_to') ?? "" )}}">
                </div>
                <div class="form-group col-md-2">
                    <h5>Search From User</h5>	
                    <input class="form-control" type="text" id="user_from" placeholder="Search From Users" name="user_from" value="{{ (request('user_from') ?? "" )}}">
                </div>
                <div class="form-group col-md-2">
                    <h5>Delivery Status	</h5>	
                    <input class="form-control" type="text" id="deliver_status" placeholder="Search Delivery Status" name="deliver_status" value="{{ (request('deliver_status') ?? "" )}}">
                </div>
                <div class="form-group col-md-2">
                    <h5>Search Created At</h5>	
                    <input class="form-control" type="date" name="date" value="{{ (request('date') ?? "" )}}">
                </div>
                <div class="form-group col-md-2">
                    <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                        <img src="{{ asset('images/search.png') }}" alt="Search">
                    </button>
                    <a href="{{route('twilio.twilio_delivery_logs')}}" class="btn btn-image" id="">
                        <img src="/images/resend2.png" style="cursor: nwse-resize;">
                    </a>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-responsive table-bordered">
                <tr>
                    <th style="width: 20%">Message Id</th>
                    <th style="width: 20%">Customer Email</th>
                    <th style="width: 10%">To</th>
                    <th style="width: 10%">From</th>
                    <th style="width: 10%">Delivery Status</th>
                    <th style="width: 15%">Created At</th>
                </tr>
                @foreach ($twilioDeliveryLogs as $val)
                    <tr>
                        <td>{{ $val->message_sid }}</td>
                        <td>{{ $val->customers ? $val->customers->email : '' }}</td>
                        <td>{{ $val->to }}</td>
                        <td>{{ $val->from }}</td>
                        <td>{{ $val->delivery_status }}</td>
                        <td>{{ $val->created_at }}</td>
                    </tr>
                @endforeach
            </table>
            {{ $twilioDeliveryLogs->links() }}
        </div>
    </div>
@endsection
