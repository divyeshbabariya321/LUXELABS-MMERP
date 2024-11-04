@extends('layouts.app')
@section('title', 'Affiliate Marketing')
@section('styles')
    <style type="text/css">
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
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">
                {!! $provider->provider->provider_name !!} Commissions (<span
                        id="affiliate_count">{{ $providersCommissions->total() }}</span>)

            </h2>
            <div class="pull-left">
                <form action="{{route('affiliate-marketing.provider.commission.index', ['provider_account' => $provider->id])}}">
                    <div class="form-group">
                        <input type="hidden" name="provider_account" value="{!! $provider->id !!}">
                        <div class="row">
                            <div class="col-md-2">
                                <input name="amount" type="text" class="form-control"
                                       value="{{ request('amount') }}" placeholder="Search amount">
                            </div>
                            <div class="col-md-2">
                                <input name="commission_type" type="text" class="form-control"
                                       value="{{ request('commission_type') }}" placeholder="Search commission_type">
                            </div>
                            <div class="col-md-2">
                                <input name="kind" type="text" class="form-control"
                                       value="{{ request('kind') }}" placeholder="Search kind">
                            </div>
                            <div class="col-md-2">
                                <input name="currency" type="text" class="form-control"
                                       value="{{ request('currency') }}" placeholder="Search currency">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-image">
                                    <img src="/images/filter.png" />
                                </button>
                                <button type="reset"
                                        onclick="window.location='{{route('affiliate-marketing.provider.commission.index', ['provider_account' => $provider->id])}}'"
                                        class="btn btn-image" id="resetFilter">
                                    <img src="/images/resend2.png" />
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6 pl-0 float-right">
                {{ html()->form('POST', route('affiliate-marketing.provider.commission.sync', [['provider_account' => $provider->id]]))->style('display:inline')->open() }}
                <button type="submit" class="float-right mb-3 btn-secondary">Refresh Commissions
                </button>
                {{ html()->form()->close() }}
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered" id="affiliates-table">
            <thead>
            <tr>
                <th>No</th>
                <th>Commission Id</th>
                <th>Amount</th>
                <th>Currency</th>
                <th>Approved</th>
                <th>Date</th>
                <th>Commission Type</th>
                <th>Conversion sub amount</th>
                <th>Payout</th>
                <th>Commission Kind</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($providersCommissions as $key => $providersCommission)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $providersCommission->affiliate_commission_id }}</td>
                    <td>{{ $providersCommission->amount }}</td>
                    <td>{{ $providersCommission->currency }}</td>
                    <td>{{ $providersCommission->approved ? 'Yes': 'No' }}</td>
                    <td>{{ date('Y-m-d h:i:s a', strtotime($providersCommission->affiliate_commission_created_at)) }}</td>
                    <td>{{ $providersCommission->commission_type }}</td>
                    <td>{{ $providersCommission->conversion_sub_amount ?: 'N/A'}}</td>
                    <td>{{ $providersCommission->payout ?: 'N/A'}}</td>
                    <td>{{ $providersCommission->kind ?: 'N/A'}}</td>
                    <td>
                        <button type="button" data-toggle="modal" data-target="#update-commission"
                                onclick="editData('{!! $providersCommission->id !!}')"
                                class="btn btn-image"><img src="/images/edit.png"></button>
                        {{ html()->form('POST', route('affiliate-marketing.provider.commission.approveDisapprove', [[$providersCommission->id, 'provider_account' => $provider->id]]))->style('display:inline')->open() }}
                        @if($providersCommission->approved)
                            <button type="submit" class="btn btn-image">
                                <img src="/images/icons-delete.png" />
                            </button>
                        @else
                            <button type="submit" class="btn btn-image">
                                <img src="/images/icons-checkmark.png" />
                            </button>
                        @endif
                        {{ html()->form()->close() }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {!! $providersCommissions->render() !!}
    <div class="modal fade" id="update-commission" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Update commission</h2>
                    </div>
                    <form id="add-group-form" method="POST"
                          action="{{route('affiliate-marketing.provider.commission.update', ['provider_account' => $provider->id])}}">
                        @csrf
                        <input type="hidden" id="provider_id" name="affiliate_account_id" value="{!! $provider->id !!}">
                        <input type="hidden" id="commission_id" name="commission_id" value="">
                        <div class="form-group row">
                            <label for="headline1" class="col-sm-2 col-form-label">Amount</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="amount" name="amount"
                                       placeholder="Amount" value="{{ old('amount') }}">
                                @if ($errors->has('amount'))
                                    <span class="text-danger">{{$errors->first('amount')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                                    aria-label="Close">Close
                            </button>
                            <button type="submit" class="float-right custom-button btn">Update</button>
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
      function editData(id) {
        let url = "{{ route('affiliate-marketing.provider.commission.get', [":id", 'provider_account' => $provider->id]) }}";
        url = url.replace(":id", id);
        $.ajax({
          url,
          type: "GET",
          success: function(response) {
            if (!response.status) {
              toastr["error"](response.message);
              $("#update-commission").modal("hide");
            } else {
              $("#add-group-form [name=\"amount\"]").val(response.data.amount);
              $("#add-group-form [name=\"commission_id\"]").val(response.data.id);
            }
          }
        });
      }
    </script>
@endsection
