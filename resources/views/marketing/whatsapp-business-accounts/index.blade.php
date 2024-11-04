@extends('layouts.app')
@section('styles')
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">WhatsApp Business Accounts</h2>
        </div>
    </div>
    <div class="row ">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <form action="{{ route('whatsapp.business.account.index') }}" method="GET"
                      class="form-inline align-items-start">
                    <div class="form-group mr-2 mb-3">
                        <input name="business" type="text" class="form-control global" id="business"
                               value="{{ request('business') }}"
                               placeholder="number , provider, username" style="width:150px !important;">
                    </div>
                    <button type="submit" class="btn btn-image mt-0"><img src="/images/filter.png"/></button>
                </form>
            </div>
            <div class="pull-right" style="padding-right: 11px;">
                <button type="button" class="btn btn-secondary" data-toggle="modal"
                        data-target="#whatsapp-business-create">+
                </button>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-error">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered" id="passwords-table">
            <thead>
            <tr>
                <th style="width: 2% !important;">#</th>
                <th style="width: 18% !important;">Business Phone number</th>
                <th style="width: 20% !important;">Account Id</th>
                <th style="width: 50% !important;">Token</th>
                <th style="width: 10% !important;">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($whatsappBusinessAccounts as $key => $whatsappBusinessAccount)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $whatsappBusinessAccount->business_phone_number }}</td>
                    <td>{{ $whatsappBusinessAccount->business_account_id }}</td>
                    <td style="word-break: break-all">{{ $whatsappBusinessAccount->business_access_token }}</td>
                    <td>
                        <button type="button" data-toggle="modal" data-target="#create-provider"
                                onclick="editData('{!! $whatsappBusinessAccount->id !!}')"
                                class="btn btn-image"><img src="/images/edit.png"></button>
                        {{ html()->form('POST', route('whatsapp.business.account.delete', [[$whatsappBusinessAccount->id]]))->style('display:inline')->open() }}
                        <button type="submit" class="btn btn-image"><img src="/images/delete.png"/></button>
                        {{ html()->form()->close() }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {!! $whatsappBusinessAccounts->render() !!}

    @include('marketing.whatsapp-business-accounts.partials.create')
    @include('marketing.whatsapp-business-accounts.partials.edit')
@endsection


@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="application/javascript">
        let showPopup;
        @if(Session::get('create_popup'))
            showPopup = true;
        @endif

        if (showPopup) {
            $('#whatsapp-business-create').modal('show');
        }

        function editData(id) {
            let url = "{{ route('whatsapp.business.account.get', [":id"]) }}";
            url = url.replace(':id', id);
            $.ajax({
                url,
                type: 'GET',
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function (response) {
                    $("#loading-image").hide();
                    if (!response.status) {
                        toastr["error"](response.message);
                        $('#whatsapp-business-edit').modal('hide');
                    } else {
                        $('#whatsapp-business-edit').modal('show');
                        $('#edit_id').val(response.data.id)
                        $('#edit_business_phone_number').val(response.data.business_phone_number)
                        $('#edit_business_account_id').val(response.data.business_account_id)
                        $('#edit_business_access_token').val(response.data.business_access_token)
                        $('#edit_business_phone_number_id').val(response.data.business_phone_number_id)
                        $('#edit_email').val(response.data.email)
                        $('#edit_about').val(response.data.about)
                        $('#edit_address').val(response.data.address)
                        $('#edit_description').val(response.data.description)
                        $('#edit_websites').val(response.data.websites)
                    }
                }
            })
        }
    </script>
@endsection
