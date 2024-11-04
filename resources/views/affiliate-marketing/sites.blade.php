@extends('layouts.app')
@section('title', 'Affiliate Marketing Sites')
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
                Affiliates Providers Sites (<span id="affiliate_count">{!! $providerAccounts->total() !!}</span>)
            </h2>
            <div class="pull-left">
                <form action="{{route('affiliate-marketing.providerAccounts')}}">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <input name="provider_name" type="text" class="form-control"
                                       value="{{ request('provider_name') }}" placeholder="Search providers">
                            </div>
                            <div class="col-md-4">
                                <input name="site" type="text" class="form-control"
                                       value="{{ request('site') }}" placeholder="Search site">
                            </div>
                            <div class="col-md-4">
                                {{ html()->select("status", ["" => "Select status", "active" => "Active", "inactive" => "Inactive"], request('status'))->class("form-control type-filter") }}
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-image">
                                    <img src="/images/filter.png"/>
                                </button>
                                <button type="reset"
                                        onclick="window.location='{{route('affiliate-marketing.providerAccounts')}}'"
                                        class="btn btn-image" id="resetFilter">
                                    <img src="/images/resend2.png"/>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-2 pl-0 float-right">
                <button data-toggle="modal" data-target="#add-site-provider" type="button"
                        class="float-right mb-3 btn-secondary">New Provider account
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
                <th>Store Website</th>
                <th>Url</th>
                <th>Provider</th>
                <th>API Key</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($providerAccounts as $key => $value)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $value->storeWebsite->title }}</td>
                    <td>{{ $value->storeWebsite->website }}</td>
                    <td>{{ $value->provider->provider_name}}</td>
                    <td>{{ $value->api_key }}</td>
                    <td>{{ $value->status ? 'Active': 'Inactive' }}</td>
                    <td>
                        <a href="{{route('affiliate-marketing.provider.index', ['provider_account' => $value->id])}}"
                           class="btn btn-sm btn-default">View Dashboard</a>
                        <button type="button" data-toggle="modal" data-target="#add-site-provider"
                                onclick="editData('{!! $value->id !!}')"
                                class="btn btn-image"><img src="/images/edit.png"></button>
                        {{ html()->form('POST', route('affiliate-marketing.deleteProviderAccount', ))->style('display:inline')->open() }}
                        <input type="hidden" value="{{$value->id}}" name="id">
                        <button type="submit" class="btn btn-image"><img src="/images/delete.png"/></button>
                        {{ html()->form()->close() }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {!! $providerAccounts->links() !!}

    <div class="modal fade" id="add-site-provider" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Add Site to Provider</h2>
                    </div>
                    <form id="add-site-provider-form" method="POST"
                          action="{{route('affiliate-marketing.createProviderAccount')}}">
                        @csrf
                        <div class="form-group row">
                            <label for="headline1" class="col-sm-2 col-form-label">Select Provider</label>
                            <div class="col-sm-10">
                                <select class="browser-default custom-select" id="status" name="affiliates_provider_id"
                                        style="height: auto">
                                    <option value="">Select provider from list</option>
                                    @foreach ($providers as $key => $affiliate)
                                        <option value="{{$affiliate->id}}">{{$affiliate->provider_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="headline1" class="col-sm-2 col-form-label">Select Site</label>
                            <div class="col-sm-10">
                                <select class="browser-default custom-select" id="status" name="store_website_id"
                                        style="height: auto">
                                    <option value="">Select site from list</option>
                                    @foreach ($storeWebsites as $key => $site)
                                        <option value="{{$site->id}}">{{$site->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="headline1" class="col-sm-2 col-form-label">API Key</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="api_key" name="api_key"
                                       placeholder="API Key" value="{{ old('api_key') }}">
                                @if ($errors->has('api_key'))
                                    <span class="text-danger">{{$errors->first('api_key')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="status" class="col-sm-2 col-form-label">Status</label>
                            <div class="col-sm-10">
                                <select class="browser-default custom-select" id="status" name="status"
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
            $('#add-site-provider').modal('show');
        }

        $('#add-site-provider').on('show.bs.modal', function () {
            $('#add-site-provider-form').attr('action', "{{ route('affiliate-marketing.createProviderAccount') }}");
            $('#add-site-provider-form button[type="submit"]').text('Create');
        })

        function editData(id) {
            let url = "{{ route('affiliate-marketing.getProviderAccount', [":id"]) }}";
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
                        $('#add-site-provider').modal('hide');
                    } else {
                        let url = "{{ route('affiliate-marketing.updateProviderAccount', [":id"]) }}";
                        url = url.replace(':id', id);
                        $('#add-site-provider-form').attr('action', url);
                        $('#add-site-provider-form button[type="submit"]').text('Update');
                        $('#add-site-provider-form [name="api_key"]').val(response.data.api_key);
                        $('#add-site-provider-form [name="affiliates_provider_id"]').val(response.data.affiliates_provider_id);
                        $('#add-site-provider-form [name="store_website_id"]').val(response.data.store_website_id);
                        $('#add-site-provider-form [name="status"]').val(response.data.status ? 'true' : 'false');
                    }
                }
            })
        }
    </script>
@endsection
