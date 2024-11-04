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
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">
                {!! $provider->provider->provider_name !!} Payments (<span
                        id="affiliate_count">{{ $providersPayments->total() }}</span>)
            </h2>
            <div class="pull-left">
                <form action="{{route('affiliate-marketing.provider.payments.index', ['provider_account' => $provider->id])}}">
                    <input type="hidden" name="provider_account" value="{!! $provider->id !!}">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <input name="name" type="text" class="form-control"
                                       value="{{ request('name') }}" placeholder="Search name">
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-image">
                                    <img src="/images/filter.png" />
                                </button>
                                <button type="reset"
                                        onclick="window.location='{{route('affiliate-marketing.provider.payments.index', ['provider_account' => $provider->id])}}'"
                                        class="btn btn-image" id="resetFilter">
                                    <img src="/images/resend2.png" />
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6 pl-0 float-right">
                {{ html()->form('POST', route('affiliate-marketing.provider.payments.sync', [['provider_account' => $provider->id]]))->style('display:inline')->open() }}
                <button type="submit" class="float-right mb-3 btn-secondary">Refresh Data
                </button>
                {{ html()->form()->close() }}
                <button data-toggle="modal" data-target="#create-payment" type="button"
                        class="float-right mb-3 btn-secondary">New payment
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
                <th>Payment Id</th>
                <th>Amount</th>
                <th>Currency</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($providersPayments as $key => $providersPayment)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $providersPayment->affiliate->firstname . ' ' . $providersPayment->affiliate->lastname }}</td>
                    <td>{{ $providersPayment->payment_id }}</td>
                    <td>{{ $providersPayment->amount }}</td>
                    <td>{{ $providersPayment->currency }}</td>
                    <td>
                        {{ html()->form('POST', route('affiliate-marketing.provider.payments.cancel', [[$providersPayment->id, 'provider_account' => $provider->id]]))->style('display:inline')->open() }}
                        <button type="submit" class="btn btn-image">
                            <img src="/images/icons-delete.png" />
                        </button>
                        {{ html()->form()->close() }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {!! $providersPayments->render() !!}
    <div class="modal fade" id="create-payment" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Create Affiliate Payment</h2>
                    </div>
                    <form id="add-group-form" method="POST"
                          action="{{route('affiliate-marketing.provider.payments.create', ['provider_account' => $provider->id])}}">
                        @csrf
                        <input type="hidden" id="provider_id" name="provider_account" value="{!! $provider->id !!}">
                        <div class="form-group row">
                            <label for="headline1" class="col-sm-2 col-form-label">Affiliate</label>
                            <select name="affiliate_id" id="affiliate_id" class="form-control"
                                    style="width: 50% !important;">
                                <option value="">Select</option>
                                @foreach($affiliates as $affiliate)
                                    <option value="{!! $affiliate->id !!}">{!! $affiliate->firstname .' '.$affiliate->lastname !!}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group row">
                            <label for="headline1" class="col-sm-2 col-form-label">Amount</label>
                            <div class="col-sm-10 p-0">
                                <input type="number" class="form-control" id="amount" name="amount" style="width: 50%"
                                       placeholder="Amount" value="{{ old('amount') }}">
                                @if ($errors->has('amount'))
                                    <span class="text-danger">{{$errors->first('amount')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="headline1" class="col-sm-2 col-form-label">Currency</label>
                            <div class="col-sm-10 p-0">
                                <input type="text" class="form-control" id="currency" name="currency" style="width: 50%"
                                       placeholder="Currency" value="{{ old('currency') }}">
                                @if ($errors->has('currency'))
                                    <span class="text-danger">{{$errors->first('currency')}}</span>
                                @endif
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
        $("#create-payment").modal("show");
      }
    </script>
@endsection
