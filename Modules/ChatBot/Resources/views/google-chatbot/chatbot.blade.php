@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Dialog | Chatbot')

@section('content')
    <link rel="stylesheet" type="text/css" href="/css/chat-bot.css">
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
          50% 50% no-repeat;display:none;">
    </div>
    <div class="container-fluid">
        <div class="row my-3">
            <div class="col-md-2">
                <div class="form-group">
                    <select name="google_account_id" id="google_account_id" class="form-control selectpicker"
                            placholder="Select google account" data-live-search="true" data-none-selected-text>
                        <option value="">Select account</option>
                        @foreach($google_accounts as $k => $value)
                            <option value="{{$value->id}}"> {{$value->id}} - {{$value->storeWebsite->title}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="chat-bot">
                    <div class="chat-bot-body">

                    </div>

                    <div class="chat-bot-footer">
                        <div class="form-group position-relative">
                            <input type="text" id="bot-message" placeholder="Type your message" class="form-control">
                            <i class="fa fa-paper-plane" aria-hidden="true" onclick="sendQuestion()"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#bot-message').on('keyup', function (event) {
            if (event.keyCode === 13) {
                sendQuestion();
            }
        })

        function sendQuestion() {
            let googleAccountId = $('#google_account_id').val();
            if (!googleAccountId) {
                toastr["error"]('Please select a google account');
                return;
            }
            let question = $('#bot-message').val();
            $('.chat-bot-body').append(`<div class="right-chat chat-message"><p>${question}</p></div>`);
            $('.chat-bot-body').animate({scrollTop: $('.chat-bot-body').height()}, 1000);
            $('#bot-message').val('');
            $("#loading-image").show();
            $.ajax({
                url: "{{ route('chatbot-api.reply') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    question,
                    googleAccount: googleAccountId
                },
                success: function (response) {
                    $("#loading-image").hide();
                    if (response.code === 400) {
                        toastr["error"](response.data);
                        return;
                    }
                    $('.chat-bot-body').append(`<div class="left-chat chat-message"><p>${response.data}</p></div>`);
                    $('.chat-bot-body').animate({scrollTop: $('.chat-bot-body').height()}, 1000);
                },
                error: function (error) {
                    $("#loading-image").hide();
                    toastr["error"](error.message);
                    return;
                }
            })
        }
    </script>
@endsection
