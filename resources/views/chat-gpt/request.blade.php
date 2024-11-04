@extends('layouts.app')
@section('title', 'Chat GPT')
@section('styles')
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 9999;
        }

        .btn-secondary, .btn-secondary:focus, .btn-secondary:hover {
            background: #fff;
            color: #757575;
            border: 1px solid #ddd;
            height: 32px;
            border-radius: 4px;
            padding: 5px 10px;
            font-size: 14px;
            font-weight: 100;
            line-height: 10px;
        }

        .note {
            font-size: 12px;
        }
    </style>
@endsection
@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">
                New Chat GPT Request
            </h2>
        </div>
    </div>
    @include('partials.flash_messages')
    <div class="row col-md-12">
        <div class="col-12 d-flex justify-content-end pe-5">
            <button class="response-type-btn float-right custom-button btn mb-2" disabled hidden></button>
        </div>
        <div class="col-md-6 question">
            @include('chat-gpt._partials.form')
        </div>
        <div class="col-md-6 response" style="word-break: break-word;"></div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>

    <script type="text/javascript">
        let typeField = $('#type');
        let isImageUpload = false;
        let divs = ['models', 'completions', 'edits', 'image_generate', 'image_edit', 'image_variation', 'moderations'];

        function getFormValues() {
            isImageUpload = false;
            let type = $('#add-group-form [name="type"]').val();
            let values = {};
            if (type === 'models') {
                values = {
                    type,
                    regenerate: $("#regenerate").prop('checked'),
                    _token: '{!! csrf_token() !!}'
                };
            }

            if (type === 'completions') {
                values = {
                    type,
                    model: $(`#add-group-form [name="${type}_model"]`).val(),
                    prompt: $(`#add-group-form [name="${type}_prompt"]`).val(),
                    suffix: $(`#add-group-form [name="${type}_suffix"]`).val(),
                    max_tokens: $(`#add-group-form [name="${type}_max_tokens"]`).val(),
                    temperature: $(`#add-group-form [name="${type}_temperature"]`).val(),
                    top_p: $(`#add-group-form [name="${type}_top_p"]`).val(),
                    n: $(`#add-group-form [name="${type}_n"]`).val(),
                    regenerate: $("#regenerate").prop('checked'),
                    _token: '{!! csrf_token() !!}'
                };
            }
            if (type === 'edits') {
                values = {
                    type,
                    model: $(`#add-group-form [name="${type}_model"]`).val(),
                    input: $(`#add-group-form [name="${type}_input"]`).val(),
                    instruction: $(`#add-group-form [name="${type}_instruction"]`).val(),
                    temperature: $(`#add-group-form [name="${type}_temperature"]`).val(),
                    top_p: $(`#add-group-form [name="${type}_top_p"]`).val(),
                    n: $(`#add-group-form [name="${type}_n"]`).val(),
                    regenerate: $("#regenerate").prop('checked'),
                    _token: '{!! csrf_token() !!}'
                };
            }
            if (type === 'image_generate') {
                values = {
                    type,
                    prompt: $(`#add-group-form [name="${type}_prompt"]`).val(),
                    n: $(`#add-group-form [name="${type}_n"]`).val(),
                    size: $(`#add-group-form [name="${type}_size"]`).val(),
                    regenerate: $("#regenerate").prop('checked'),
                    _token: '{!! csrf_token() !!}'
                };
            }
            if (type === 'image_edit') {
                isImageUpload = true;
                values = new FormData();
                values.set('image', $(`#add-group-form [name="${type}_image"]`).prop('files')[0]);
                values.set('mask', $(`#add-group-form [name="${type}_mask"]`).prop('files')[0]);
                values.set('prompt', $(`#add-group-form [name="${type}_prompt"]`).val());
                values.set('n', $(`#add-group-form [name="${type}_n"]`).val());
                values.set('size', $(`#add-group-form [name="${type}_size"]`).val());
                values.set('_token', '{!! csrf_token() !!}');
                values.set('regenerate', $("#regenerate").prop('checked'));
                values.set('type', type);
            }
            if (type === 'image_variation') {
                isImageUpload = true;
                values = new FormData();
                values.set('image', $(`#add-group-form [name="${type}_image"]`).prop('files')[0]);
                values.set('n', $(`#add-group-form [name="${type}_n"]`).val());
                values.set('size', $(`#add-group-form [name="${type}_size"]`).val());
                values.set('_token', '{!! csrf_token() !!}');
                values.set('regenerate', $("#regenerate").prop('checked'));
                values.set('type', type);
            }
            if (type === 'moderations') {
                values = {
                    type,
                    model: $(`#add-group-form [name="${type}_model"]`).val(),
                    input: $(`#add-group-form [name="${type}_input"]`).val(),
                    regenerate: $("#regenerate").prop('checked'),
                    _token: '{!! csrf_token() !!}'
                };
            }
            return values;
        }

        function getResponse() {
            let data = getFormValues();
            $.ajax({
                url: "{{ route('chatgpt.response') }}",
                type: 'POST',
                data: data,
                ...(isImageUpload ? {
                    processData: false,
                    contentType: false,
                }: {}),
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function (response) {
                    $("#loading-image").hide();
                    if (!response.status) {
                        toastr["error"](response.message);
                    } else {
                        let responseStr = '';
                        $('.response-type-btn').removeAttr('hidden');
                        $('.response-type-btn').html(response.data.getting_type);
                        if (!response.data.type) {
                            Object.entries(response.data.response).forEach(entry => {
                                const [key, value] = entry;
                                responseStr += `<p><b>${key}</b>&nbsp;${value}</p>`;
                            });
                        } else {
                            if (response.data.type == 'moderations') {
                            } else {
                                $('.response').empty();
                                for (const key in response.data.response) {
                                    if (response.data.response.hasOwnProperty(key)) {
                                        responseStr += `<div style="max-width: 50%; margin: 5px auto;">
                                                            <img src="${response.data.response[key]}" style="width: 100%;"></div>`;
                                    }
                                }
                            }
                        }
                        $('.response').html(responseStr);
                        // $('.response').html(Array.isArray(response.data.response) ? JSON.stringify(response.data.response) : response.data.response);
                    }
                },
            })
        }

        function updateFields() {
            if (typeField.val().trim()) {
                showSpecific(typeField.val().trim());
            } else {
                hideAll();
            }
        }

        function hideAll() {
            divs.forEach(t => $(`#${t}`).hide());
        }

        function showSpecific(divId) {
            divs.forEach(t => {
                if (t === divId) {
                    $(`#${t}`).show()
                } else {
                    $(`#${t}`).hide()
                }
            });
        }
    </script>
@endsection
