@extends('layouts.app')
@section('title', 'Affiliate Marketing')
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
                {!! $provider->provider->provider_name !!} Customers (<span
                        id="affiliate_count">{{ $providersCustomers->total() }}</span>)
            </h2>
            <div class="pull-left">
                <form action="{{route('affiliate-marketing.provider.customer.index', ['provider_account' => $provider->id])}}">
                    <input type="hidden" name="provider_account" value="{!! $provider->id !!}">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <input name="name" type="text" class="form-control"
                                       value="{{ request('name') }}" placeholder="Search name">
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-image">
                                    <img src="/images/filter.png"/>
                                </button>
                                <button type="reset"
                                        onclick="window.location='{{route('affiliate-marketing.provider.customer.index', ['provider_account' => $provider->id])}}'"
                                        class="btn btn-image" id="resetFilter">
                                    <img src="/images/resend2.png"/>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6 pl-0 float-right">
                {{ html()->form('POST', route('affiliate-marketing.provider.customer.sync', [['provider_account' => $provider->id]]))->style('display:inline')->open() }}
                <button type="submit" class="float-right mb-3 btn-secondary">Refresh Data
                </button>
                {{ html()->form()->close() }}
                <button data-toggle="modal" data-target="#create-customer" type="button"
                        class="float-right mb-3 btn-secondary">New Customer
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
                <th>Affiliate Name</th>
                <th>Customer Id</th>
                <th>Programme</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($providersCustomers as $key => $providersCustomer)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $providersCustomer->affiliate->firstname .' '.$providersCustomer->affiliate->lastname }}</td>
                    <td>{{ $providersCustomer->customer_id }}</td>
                    <td>{{ $providersCustomer->programme->title }}</td>
                    <td>{{ $providersCustomer->status }}</td>
                    <td>
                        {{ html()->form('POST', route('affiliate-marketing.provider.customer.delete', [[$providersCustomer->id, 'provider_account' => $provider->id]]))->style('display:inline')->open() }}
                        <button type="submit" onclick="return customerDeleteConfirm()" class="btn btn-image"><img src="/images/delete.png"/></button>
                        {{ html()->form()->close() }}
                        {{ html()->form('POST', route('affiliate-marketing.provider.customer.cancelUncancel', [[$providersCustomer->id, 'provider_account' => $provider->id]]))->style('display:inline')->open() }}
                        @if($providersCustomer->status != 'stopped')
                            <button type="submit" onclick="return customerCancelConfirm()" class="btn btn-image" title="Cancel Customer">
                                <img src="/images/icons-delete.png"/>
                            </button>
                        @else
                            <button type="submit" onclick="return customerUnCancelConfirm()" class="btn btn-image" title="Un-Cancel Customer">
                                <img src="/images/icons-checkmark.png"/>
                            </button>
                        @endif
                        {{ html()->form()->close() }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {!! $providersCustomers->render() !!}
    <div class="modal fade" id="create-customer" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Create Customer</h2>
                    </div>
                    @include('affiliate-marketing.providers.partials.customer-create')
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
            $('#create-customer').modal('show');
        }

         function validateCreateCustomer() {
            var err = false;
            $('.err').text('');
            var affiliate = $('#asset_id').val();
            var customer = $('#customer_id').val();

            if(affiliate == ''){
                $('#assetErr').text('Please select affiliate.');
                err = true;
            }
            if(customer == ''){
                $('#customerErr').text('Please select customer.');
                err = true;
            }
            if(!err) {
                return true;
            }
            return false;
        }

        function customerDeleteConfirm() {
            return confirm("Are sure you want to delete customer?");
        }
        function customerCancelConfirm() {
            return confirm("Are sure you want to cancel customer?");
        }
        function customerUnCancelConfirm() {
            return confirm("Are sure you want to uncancel customer?");
        }
    </script>
@endsection
