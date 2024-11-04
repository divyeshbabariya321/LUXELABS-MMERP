@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Message List  | Chatbot')

@section('content')
    @php
    $isAdmin = Auth::user()->hasRole('Admin');
    $isHod  = Auth::user()->hasRole('HOD of CRM');

@endphp
    <div class="row m-0">
        <div class="col-lg-12 margin-tb p-0">
            <h2 class="page-heading">Chatbot Message List | Chatbot</h2>
        </div>
    </div>
<div class="table-responsive">
    <table class="table table-bordered chatbot page-template">
        <thead>
        <tr>
            <th width="2%">Message</th>
            <th width="2%">Sender name</th>
            <th width="2%">Timestamp</th>
        </tr>
        </thead>
        <tbody>
        @if(!empty($message_list))
            @foreach($message_list as $message)
                <tr>
                <td>{{ $message['message'] }}</td>
                <td>Send by {{ $message['send_by_simulator'] ? 'simulator' : 'manually'}}</td>
                <td>From {{ $message['sendTo'] }} to {{ $message['sendBy'] }} on {{ $message['datetime'] }}</td>
                </tr>
            @endforeach
        @endif
        </tbody>
        <tfoot>
        <tr>
            <td class="p-0" colspan="9"></td>
        </tr>
        </tfoot>
    </table>

</div>
@endsection

