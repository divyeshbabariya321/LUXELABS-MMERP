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
                {!! $provider->provider->provider_name !!} Conversions (<span
                        id="affiliate_count">{{ $providersConversions->total() }}</span>)
            </h2>
            <div class="pull-left">
                <form action="{{route('affiliate-marketing.provider.conversion.index', ['provider_account' => $provider->id])}}">
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
                                        onclick="window.location='{{route('affiliate-marketing.provider.conversion.index', ['provider_account' => $provider->id])}}'"
                                        class="btn btn-image" id="resetFilter">
                                    <img src="/images/resend2.png"/>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6 pl-0 float-right">
                {{ html()->form('POST', route('affiliate-marketing.provider.conversion.sync', [['provider_account' => $provider->id]]))->style('display:inline')->open() }}
                <button type="submit" class="float-right mb-3 btn-secondary">Refresh Data
                </button>
                {{ html()->form()->close() }}
                <button data-toggle="modal" data-target="#create-conversion" type="button"
                        class="float-right mb-3 btn-secondary">New Conversion
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
                <th>Amount</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($providersConversions as $key => $providersConversion)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $providersConversion->affiliate->firstname .' '.$providersConversion->affiliate->lastname }}</td>
                    <td>{{ $providersConversion->amount }}</td>
                    <td>
                        <button type="button" data-toggle="modal" data-target="#update-conversion"
                                onclick="editData('{!! $providersConversion->id !!}', '{!! $providersConversion->amount !!}')"
                                class="btn btn-image"><img src="/images/edit.png"></button>
                        {{ html()->form('POST', route('affiliate-marketing.provider.conversion.delete', [[$providersConversion->id, 'provider_account' => $provider->id]]))->style('display:inline')->open() }}
                        <button type="submit" onclick="return conversionDeleteConfirm()" class="btn btn-image"><img src="/images/delete.png"/></button>
                        {{ html()->form()->close() }}
                        <button type="button" data-toggle="modal" data-target="#add-commission" onclick="addCommission('{!! $providersConversion->id !!}')"
                                class="btn btn-image"><img src="/images/price.png"/></button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {!! $providersConversions->render() !!}
    <div class="modal fade" id="create-conversion" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Create Conversion</h2>
                    </div>
                    @include('affiliate-marketing.providers.partials.conversion-create')
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="update-conversion" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Update Conversion amount</h2>
                    </div>
                    @include('affiliate-marketing.providers.partials.conversion-update')
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add-commission" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Add commission</h2>
                    </div>
                    @include('affiliate-marketing.providers.partials.conversion-commission')
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
            $('#create-conversion').modal('show');
        }

        function editData(id, amount) {
            $('#edit-amount').val(amount);
            $('#conversion_id').val(id);
        }

        function addCommission(id) {
            $('#add_conversion_id').val(id);
        }

        function validateConversion(){
            var err = false;
            $('.err').text('');
            var amount = $('#amount').val();
            var asset = $('#asset_id').val();
            var customer = $('#customer_id').val();

            if(amount == ''){
                $('#amountErr').text('Please enter amount.');
                err = true;
            }
            if(asset == ''){
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

        $('#program').change(function(){
            var providerID = "{{ $_GET['provider_account'] }}";
            var program = $(this).val();
            //get commision type
            $.ajax({
                type: "GET",
                url: "{{ route('affiliate-marketing.provider.program.commissionType')}}?provider_account="+providerID+'&program='+program,
                dataType : "json",
                success: function (response) {
                    var html = '<option value="">Select</option>';
                    $.each(response, function(index, row) {
                        html += '<option value="'+row.identifier+'">'+row.title+'</option>';
                    });
                    $('#commission_type').html(html);
                }
            });
        });

        function conversionDeleteConfirm() {
            return confirm("Are sure you want to delete conversion?");
        }
    </script>
@endsection
