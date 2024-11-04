@extends('layouts.app')
@section('title', 'Gemini AI accounts')
@section('large_content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Gemini AI Accounts</h2>
        </div>
    </div>
    @include('partials.flash_messages')
    <div class="row">
        <div class="col-md-12">
            <button class="btn btn-secondary pull-right" data-target="#addAccount" data-toggle="modal">+</button>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">

            <div class="table-responsive">
                <table class="table table-bordered table-hover" style="border: 1px solid #ddd;table-layout:fixed;">
                    <thead>
                    <tr>
                        <th style="width:5%;" class="text-center">Sno</th>
                        <th style="width:15%;" class="text-center">Website</th>
                        <th style="width:20%;" class="text-center">API Key</th>
                        <th style="width:20%;" class="text-center">API Url</th>
                        <th style="width:20%;" class="text-center">Fallback Message</th>
                        <th style="width:20%;" class="text-center">Prompt</th>
                        <th style="width:5%;" class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    @foreach($accounts as $key => $account)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{ $account->storeWebsite->title }}</td>
                            <td class="expand-row-msg" data-name="api_key" data-id="{{$account->id}}">
                                <span class="show-short-api_key-{{$account->id}}">{{ Str::limit($account->api_key, 30, '...')}}</span>
                                <span style="word-break:break-all;" class="show-full-api_key-{{$account->id}} hidden">{{ $account->api_key }}</span>
                            </td>
                            <td class="expand-row-msg" data-name="api_url" data-id="{{$account->id}}">
                                <span class="show-short-url-{{$account->id}}">{{ Str::limit($account->api_url, 30, '...')}}</span>
                                <span style="word-break:break-all;" class="show-full-url-{{$account->id}} hidden">{{ $account->api_url }}</span>
                            </td>
                            <td class="expand-row-msg" data-name="fallback_message" data-id="{{$account->id}}">
                                <span class="show-short-fallback_message-{{$account->id}}">{{ Str::limit($account->fallback_message, 30, '...')}}</span>
                                <span style="word-break:break-all;" class="show-full-fallback_message-{{$account->id}} hidden">{{ $account->fallback_message }}</span>
                            </td>
                            <td class="expand-row-msg" data-name="prompt" data-id="{{$account->id}}">
                                <span class="show-short-prompt-{{$account->id}}">{{ Str::limit($account->prompt, 30, '...')}}</span>
                                <span style="word-break:break-all;" class="show-full-prompt-{{$account->id}} hidden">{{ $account->prompt }}</span>
                            </td>
                            <td>
                               <div class="d-flex">
                               <a data-id="{{ $account->id }}" class="btn btn-sm edit_account" style="padding:3px;">
                                    <i class="fa fa-edit" aria-hidden="true"></i>
                                </a>
                                <a href="{{ route('geminiai-accounts.delete', $account->id) }}" data-id="1"
                                   class="btn btn-delete-template"
                                   onclick="return confirm('Are you sure you want to delete this account ?');" style="padding:3px;">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!--Add Account Modal -->
        <div class="modal fade" id="addAccount" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">Add Gemini AI Account</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" id="submit-geminiai-account" action="">
                        @csrf
                        <div class="modal-body mb-2">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="mt-3">Website</label>
                                    <select name="store_website_id" id="" class="form-control" required>
                                        <option value="">Select</option>
                                        @foreach($store_websites as $website)
                                            <option value="{{$website->id}}">{{$website->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="mt-3">API Key</label>
                                    <input type="text" class="form-control" name="api_key" required/>
                                </div>
                                <div class="col-md-12">
                                    <label class="mt-3">API Url</label>
                                    <input type="text" class="form-control" name="api_url" required/>
                                </div>
                                <div class="col-md-12">
                                    <label class="mt-3">Fallback Message</label>
                                    <input type="text" class="form-control" name="fallback_message" required/>
                                </div>
                                <div class="col-md-12">
                                    <label class="mt-3">Prompt</label>
                                    <input type="text" class="form-control" name="prompt" required/>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="modal-footer mt-3">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-secondary save-account">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!--Update Account Modal -->
        <div class="modal fade" id="updateAccount" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Update Gemini AI Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="" id="edit-geminiai-account">
                        @csrf
                        <input type="hidden" id="account_id">
                        <div class="modal-body mb-2">
                            <div class="row">
                                <div class="col-md-12 mt-3">
                                    <label>Website</label>
                                    <select name="store_website_id" id="store_website_id" class="form-control" required>
                                    </select>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label>API Key</label>
                                    <input type="text" class="form-control" id="api_key" name="api_key" required/>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label>API Url</label>
                                    <input type="text" class="form-control" id="api_url" name="api_url" required/>
                                </div>
                                <div class="col-md-12">
                                    <label class="mt-3">Fallback Message</label>
                                    <input type="text" class="form-control" id="fallback_message" name="fallback_message" required/>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label>Prompt</label>
                                    <input type="text" class="form-control" id="prompt" name="prompt" required/>
                                </div>
                            </div>
                        </div>

                        <br>
                        <div class="modal-footer mt-3">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-secondary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <script>
        $(document).on("submit", "#submit-geminiai-account", function (e) {
            e.preventDefault();
            var postData = $(this).serialize();
            $.ajax({
                method: "post",
                url: "{{action([\App\Http\Controllers\GeminiAIController::class, 'store'])}}",
                data: postData,
                dataType: "json",
                success: function (response) {
                    if (response.code == 200) {
                        toastr["success"]("Status updated!", "Message")
                        $("#addAccount").modal("hide");
                        $("#submit-geminai-account").trigger('reset');
                        location.reload();
                    } else {
                        toastr["error"](response.message, "Message");
                    }
                },
                error: function (error) {
                    toastr["error"](error.responseJSON.message, "Message");
                }
            });
        });

        $(document).on("click", ".edit_account", function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            $.ajax({
                method: "GET",
                url: "/geminiai/account/" + id,
                dataType: "json",
                success: function (response) {
                    var option = '<option value="" >Select</option>';
                    $.each(response.store_websites, function (i, item) {
                        if (item['id'] == response.account.store_website_id) {
                            var selected = 'selected';
                        } else {
                            var selected = '';
                        }
                        option = option + '<option value="' + item['id'] + '" ' + selected + ' >' + item['title'] + '</option>';
                    });
                    $('#account_id').val(response.account.id);
                    $('#store_website_id').html(option);
                    $('#api_key').val(response.account.api_key);
                    $('#api_url').val(response.account.api_url);
                    $('#fallback_message').val(response.account.fallback_message);
                    $('#prompt').val(response.account.prompt);
                    $('#updateAccount').modal('show');
                },
                error: function (error) {
                    toastr["error"](error.responseJSON.message, "Message");
                }
            });
        });

        $(document).on("submit", "#edit-geminiai-account", function (e) {
            e.preventDefault();
            var postData = $(this).serialize();
            var id = $('#account_id').val();
            $.ajax({
                method: "post",
                url: "/geminiai/account/" + id,
                data: postData,
                dataType: "json",
                success: function (response) {
                    if (response.code == 200) {
                        toastr["success"]("Status updated!", "Message")
                        $("#updateAccount").modal("hide");
                    } else {
                        toastr["error"](response.message, "Message");
                    }
                },
                error: function (error) {
                    toastr["error"](error.responseJSON.message, "Message");
                }
            });
        });

        $(document).on('click', '.expand-row-msg', function () {
            var name = $(this).data('name');
			var id = $(this).data('id');
            var full = '.expand-row-msg .show-short-'+name+'-'+id;
            var mini ='.expand-row-msg .show-full-'+name+'-'+id;
            $(full).toggleClass('hidden');
            $(mini).toggleClass('hidden');
        });
    </script>
@endsection
