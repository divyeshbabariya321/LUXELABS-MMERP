@extends('layouts.app')
@section('title', 'Pinterest')
@section('styles')
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
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
    </style>
@endsection
@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">
                Pinterest accounts (<span id="affiliate_count">{{ $pinterestBusinessAccounts->total() }}</span>)
            </h2>
            <div class="pull-left">
                <form action="{{route('pinterest.accounts')}}">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <input name="name" type="text" class="form-control"
                                       value="{{ request('name') }}" placeholder="Search accounts">
                            </div>
                            <div class="col-md-4">
                                {{ html()->select("is_active", ["" => "Select status", "active" => "Active", "inactive" => "Inactive"], request('status'))->class("form-control type-filter") }}
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-image">
                                    <img src="/images/filter.png"/>
                                </button>
                                <button type="reset"
                                        onclick="window.location='{{route('pinterest.accounts')}}'"
                                        class="btn btn-image" id="resetFilter">
                                    <img src="/images/resend2.png"/>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-2 pl-0 float-right">
                <button data-toggle="modal" data-target="#create-account" type="button"
                        class="float-right mb-3 btn-secondary">New pinterest account
                </button>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered" id="affiliates-table">
            <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Client Id</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($pinterestBusinessAccounts as $key => $pinterestBusinessAccount)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $pinterestBusinessAccount->pinterest_application_name }}</td>
                    <td>{{ $pinterestBusinessAccount->pinterest_client_id }}</td>
                    <td>{{ $pinterestBusinessAccount->is_active ? 'Active': 'Inactive' }}</td>
                    <td>
                        <button type="button" data-toggle="modal" data-target="#create-provider"
                                onclick="editData('{!! $pinterestBusinessAccount->id !!}')"
                                class="btn btn-image"><img src="/images/edit.png"></button>
                        {{ html()->form('POST', route('pinterest.accounts.delete', [[$pinterestBusinessAccount->id]]))->style('display:inline')->open() }}
                        <button type="submit" class="btn btn-image"><img src="/images/delete.png"/></button>
                        {{ html()->form()->close() }}
                        @if ($pinterestBusinessAccount->is_active)
                            <a href="{!! route('pinterest.accounts.connect', [$pinterestBusinessAccount->id]) !!}">Connect</a>
                        @endif
                    </td>
                </tr>
                @if(count($pinterestBusinessAccount->accounts) > 0)
                    <tr>
                        <td colspan="5">
                            <div>
                                <h5>Connected Accounts</h5>
                                <table style="width: 95%;margin: 0 auto;">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($pinterestBusinessAccount->accounts as $accountKey => $account)
                                        <tr>
                                            <td>{!! $accountKey + 1 !!}</td>
                                            <td>{!! $account->pinterest_account !!}</td>
                                            <td>
                                                <a class="btn-image" href="{!! route('pinterest.accounts.refresh', [$account->id]) !!}"
                                                   title="Refresh"><img src="/images/resend2.png"/></a>
                                                <a class="btn-image" href="{!! route('pinterest.accounts.disconnect', [$account->id]) !!}"
                                                   title="Disconnect"><img src="/images/delete.png"/></a>
                                                <a href="{!! route('pinterest.accounts.dashboard', [$account->id]) !!}">Dashboard</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
    {!! $pinterestBusinessAccounts->render() !!}

    <div class="modal fade" id="create-account" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Create Pinterest Account</h2>
                    </div>
                    <form id="create-account-form" method="POST"
                          action="{{route('pinterest.accounts.create')}}">
                        @csrf
                        <div class="form-group row">
                            <label for="headline1" class="col-sm-2 col-form-label">Pinterest Application name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="pinterest_application_name"
                                       name="pinterest_application_name"
                                       placeholder="Pinterest Application name"
                                       value="{{ old('pinterest_application_name') }}">
                                @if ($errors->has('pinterest_application_name'))
                                    <span class="text-danger">{{$errors->first('pinterest_application_name')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="headline1" class="col-sm-2 col-form-label">Pinterest Client Id</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="pinterest_client_id"
                                       name="pinterest_client_id"
                                       placeholder="Pinterest Client Id" value="{{ old('pinterest_client_id') }}">
                                @if ($errors->has('pinterest_client_id'))
                                    <span class="text-danger">{{$errors->first('pinterest_client_id')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="headline1" class="col-sm-2 col-form-label">Pinterest Client Secret</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="pinterest_client_secret"
                                       name="pinterest_client_secret"
                                       placeholder="Pinterest Client Secret"
                                       value="{{ old('pinterest_client_secret') }}">
                                @if ($errors->has('pinterest_client_secret'))
                                    <span class="text-danger">{{$errors->first('pinterest_client_secret')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="status" class="col-sm-2 col-form-label">Status</label>
                            <div class="col-sm-10">
                                <select class="browser-default custom-select" id="is_active" name="is_active"
                                        style="height: auto">
                                    <option value="true" selected>Active</option>
                                    <option value="false">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                                    aria-label="Close">Close
                            </button>
                            <button type="submit" class="float-right custom-button btn">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>

    <script type="text/javascript">
        let showPopup;
        @if(Session::get('create_popup'))
            showPopup = true;
        @endif

        if (showPopup) {
            $('#create-account').modal('show');
        }

        $('#create-account').on('show.bs.modal', function () {
            $('#create-account .page-header h2').text('Create Pinterest account');
            $('#create-account-form').attr('action', "{{ route('pinterest.accounts.create') }}");
            $('#create-account-form button[type="submit"]').text('Create');
        })

        $('#create-account').on('hidden.bs.modal', function () {
            $('#create-account-form').get(0).reset();
        })

        function editData(id) {
            let url = "{{ route('pinterest.accounts.get', [":id"]) }}";
            url = url.replace(':id', id);
            $.ajax({
                url,
                type: 'GET',
                params: {id},
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function (response) {
                    $("#loading-image").hide();
                    if (!response.status) {
                        toastr["error"](response.message);
                        $('#create-account').modal('hide');
                    } else {
                        $('#create-account').modal('show');
                        let url = "{{ route('pinterest.accounts.update', [":id"]) }}";
                        url = url.replace(':id', id);
                        $('#create-account-form').attr('action', url);
                        $('#create-account-form button[type="submit"]').text('Update');
                        $('#create-account .page-header h2').text('Update Pinterest Account');
                        $('#create-account-form [name="pinterest_application_name"]').val(response.data.pinterest_application_name);
                        $('#create-account-form [name="pinterest_client_id"]').val(response.data.pinterest_client_id);
                        $('#create-account-form [name="pinterest_client_secret"]').val(response.data.pinterest_client_secret);
                        $('#create-account-form [name="is_active"]').val(response.data.is_active ? 'true' : 'false');
                    }
                }
            })
        }
    </script>
@endsection
