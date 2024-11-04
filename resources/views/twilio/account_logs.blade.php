@extends('layouts.app')

@section('large_content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Twilio Account Logs</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form action="{{route('twilio.account_logs')}}" method="get" class="search">
                <div class="form-group col-md-2">
                    <h5>Search Email ID</h5>
                    <select class="form-control globalSelect2" multiple="true" id="twiliAccount_email" name="twiliAccount_email[]" placeholder="Twilio AccountEmails">
                        @foreach($twiliAccountemails as $twiliAccountemail)
                        <option value="{{ $twiliAccountemail}}" 
                        @if(is_array(request('twiliAccount_email')) && in_array($twiliAccountemail, request('twiliAccount_email')))
                            selected
                        @endif >{{ $twiliAccountemail }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <h5>Search SID</h5>	
                    <input class="form-control" type="text" id="sid" placeholder="Search SID" name="sid" value="{{ (request('sid') ?? "" )}}">
                </div>
                <div class="form-group col-md-2">
                    <h5>Search Log</h5>	
                    <input class="form-control" type="text" id="log" placeholder="Search Log" name="log" value="{{ (request('log') ?? "" )}}">
                </div>
                <div class="form-group col-md-2">
                    <h5>Search Log</h5>	
                    <input class="form-control" type="date" name="date" value="{{ (request('date') ?? "" )}}">
                </div>
                <div class="form-group col-md-2"><br><br>
                    <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                        <img src="{{ asset('images/search.png') }}" alt="Search">
                    </button>
                    <a href="{{route('twilio.account_logs')}}" class="btn btn-image" id="">
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
                    <th style="width: 10%">Email</th>
                    <th style="width: 15%">SID</th>
                    <th style="width: 50%">Log</th>
                    <th style="width: 10%">Created At</th>
                </tr>
                @foreach ($accountLogs as $val)
                    <tr>
                        <td>{{ $val->email }}</td>
                        <td>{{ $val->sid }}</td>
                        <td>{{ $val->log }}</td>
                        <td>{{ $val->created_at }}</td>
                    </tr>
                @endforeach
            </table>
            {{ $accountLogs->links() }}
        </div>
    </div>
@endsection
