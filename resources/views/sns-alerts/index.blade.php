@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">SNS Alerts List</h2>
        
        <div class="col-md-12 ml-sm-6">         
            <div class="col-md-8 ml-sm-6">     
                <form action="{{ route('snsAlerts.list') }}" method="get" class="search">
                    <div class="row">
                        <div class="col-md-3 pd-sm">
                            <input type="text" name="keyword" placeholder="keyword" class="form-control" value="{{ request()->get('keyword') }}">
                        </div>

                        <div class="col-md-2 pd-sm">
                            <select name="type" class="form-control" placeholder="Search Type">
                                <option value="">Search Type</option>
                                @foreach($alertsType as $key => $value)
                                    <option value="{{ $value->type }}" @if(request()->get('type')==$value->type) selected @endif>{{ $value->type }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-2 pd-sm" style="padding-top: 10px;">
                            <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                                <img src="{{ asset('images/search.png') }}" alt="Search">
                            </button>

                            <a href="{{ route('snsAlerts.list') }}" class="btn btn-image" id="">
                                <img src="/images/resend2.png" style="cursor: nwse-resize;">
                            </a>
                        </div>                             
                    </div>
                </form>
            </div>
        </div>
        
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<div class="tab-content ">
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;"> 
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;" id="quick-reply-list">
                        <thead>
                            <tr>
                                <th class="chat-msg">ID</th>
                                <th class="chat-msg">Type</th>
                                <th class="chat-msg">Subject</th>
                                <th class="chat-msg">Message</th>
                                <th class="chat-msg">DateTime</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alertsRecord  as $alerts)
                                <tr>
                                    <td id="alerts_id">{{ $alerts->id }}</td>
                                    <td id="alerts_type">{{ $alerts->type }}</td>
                                    <td id="alerts_subject">{{ $alerts->subject }}</td>
                                    <td id="alerts_message">{{ $alerts->message }}</td>
                                    <td id="alerts_timestamp">{{ date('d-m-Y G:i:s', strtotime($alerts->timestamp)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $alertsRecord->appends(request()->except('page'))->links()  }}
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
</script>
@endsection

