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

        legend {
            display: block;
            width: auto;
            max-width: 100%;
            padding: 0;
            margin-bottom: 0;
            font-size: 1.5rem;
            line-height: inherit;
            color: inherit;
            white-space: normal;
            border-bottom: none !important;
        }

        fieldset {
            padding: 10px 10px;
            margin: 0 2px;
            border: 1px solid #c0c0c07a;
            border-radius: 4px;
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
                {!! $provider->provider->provider_name !!} Affiliates (<span
                        id="affiliate_count">{{ $providersAffiliates->total() }}</span>)
            </h2>
            <div class="pull-left">
                <form action="{{route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $provider->id])}}">
                    <input type="hidden" name="provider_account" value="{!! $provider->id !!}">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <input name="name" type="text" class="form-control"
                                       value="{{ request('name') }}" placeholder="Search affiliate">
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-image">
                                    <img src="/images/filter.png"/>
                                </button>
                                <button type="reset"
                                        onclick="window.location='{{route('affiliate-marketing.provider.affiliate.index', ['provider_account' => $provider->id])}}'"
                                        class="btn btn-image" id="resetFilter">
                                    <img src="/images/resend2.png"/>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6 pl-0 float-right">
                {{ html()->form('POST', route('affiliate-marketing.provider.affiliate.sync', [['provider_account' => $provider->id]]))->style('display:inline')->open() }}
                <button type="submit" class="float-right mb-3 btn-secondary">Refresh affiliates
                </button>
                {{ html()->form()->close() }}
                <button data-toggle="modal" data-target="#create-affiliate" type="button"
                        class="float-right mb-3 btn-secondary">New Affiliate
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
                <th>Email</th>
                <th>Company Name</th>
                <th>Group</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($providersAffiliates as $key => $providersAffiliate)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $providersAffiliate->firstname .' '. $providersAffiliate->lastname }}</td>
                    <td>{{ $providersAffiliate->email ?: 'N/A' }}</td>
                    <td>{{ $providersAffiliate->company_name ?: 'N/A' }}</td>
                    <td>{{ $providersAffiliate->group ? $providersAffiliate->group->title: 'N/A' }}</td>
                    <td>
                        {{ html()->form('POST', route('affiliate-marketing.provider.affiliate.delete', [[$providersAffiliate->id, 'provider_account' => $provider->id]]))->style('display:inline')->open() }}
                        <button type="submit" onclick="return affiliateDeleteConfirm()" class="btn btn-image"><img src="/images/delete.png"/></button>
                        {{ html()->form()->close() }}
                        <button type="button" data-toggle="modal" data-target="#update-payout"
                                onclick="editPayout('{!! $providersAffiliate->id !!}')"
                                class="btn btn-image"><img src="/images/price.png"/></button>
                        @if (!$providersAffiliate->referral_link)
                            <button type="button" data-toggle="modal" data-target="#update-programme"
                                    class="btn btn-image"
                                    onclick="$('#affiliate_id').val('{!! $providersAffiliate->id !!}')">Add to Programme
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {!! $providersAffiliates->render() !!}
    <div class="modal fade" id="create-affiliate" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Create Affiliate Group</h2>
                    </div>
                    @include('affiliate-marketing.providers.partials.affiliate-create')
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="update-payout" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Update Payout methods</h2>
                    </div>
                    <form id="add-payout-form" method="POST"
                          action="{{route('affiliate-marketing.provider.affiliate.create', ['provider_account' => $provider->id])}}">
                        @csrf
                        <input type="hidden" id="provider_id" name="affiliate_account_id" value="{!! $provider->id !!}">
                        <div class="form-group row">
                            <label for="headline1" class="col-sm-2 col-form-label">Payout method</label>
                            <select name="payout_id" id="payout_id" class="form-control">
                                <option value="">Select</option>
                            </select>
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
    <div class="modal fade" id="update-programme" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Add Affiliate To Programme</h2>
                    </div>
                    <form id="add-payout-form" method="POST"
                          action="{{route('affiliate-marketing.provider.affiliate.addToProgramme', ['provider_account' => $provider->id])}}">
                        @csrf
                        <input type="hidden" id="provider_id" name="provider_account" value="{!! $provider->id !!}">
                        <input type="hidden" id="affiliate_id" name="affiliate_id" value="">
                        <div class="form-group row">
                            <label for="headline1" class="col-sm-2 col-form-label">Programme</label>
                            <select name="programme_id" id="programme_id" class="form-control"
                                    style="width: 50% !important;">
                                <option value="">Select</option>
                                @foreach($affiliateProgrammes as $programme)
                                    <option value="{!! $programme->id !!}">{!! $programme->title !!}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group row">
                            <label for="headline1" class="col-sm-2 col-form-label">Coupon</label>
                            <div class="col-sm-10 p-0">
                                <input type="text" class="form-control" id="coupon" name="coupon" style="width: 50%"
                                       placeholder="Coupon" value="{{ old('coupon') }}">
                                @if ($errors->has('coupon'))
                                    <span class="text-danger">{{$errors->first('coupon')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="headline1" class="col-sm-2 col-form-label">Status</label>
                            <select name="approved" id="approved" class="form-control" style="width: 50% !important;">
                                <option value="">Select</option>
                                <option value="true">Approved</option>
                                <option value="false">Disapproved</option>
                            </select>
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
        let showPopup;
        @if(Session::get('create_popup'))
            showPopup = true;
        @endif

        if (showPopup) {
            $('#create-affiliate').modal('show');
        }

        function editPayout(id) {
            let url = "{!! route('affiliate-marketing.provider.affiliate.payoutMethods', [':id']) !!}";
            url = url.replace(':id', id);
            $.ajax({
                url: url + '?provider_account=' + '{!! $provider->id !!}',
                type: 'GET',
                success: function (response) {
                    if (!response.status) {
                        toastr["error"](response.message);
                        $('#update-payout').modal('hide');
                    } else {
                        if (response.data.length <= 0) {
                            toastr["success"]('No Payout methods found');
                            $('#update-payout').modal('hide');
                        } else {
                            let optionsHtml = '<option value="">Select</option>';
                            response.data.forEach(item => {
                                optionsHtml += '<option value=' + item.id + '>' + item.title + '</option>'
                            })
                            $('#payout_id').html(optionsHtml);
                        }
                    }
                }
            })
        }

        function validateCreateAffiliate() {
            var err = false;
            var focus = false;
            var firstName = $('#firstName').val();
            var lastName = $('#lastName').val();
            var email = $('#affiliateEmail').val();
            var group = $('#affiliateGroup').val();
            var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

            if(firstName == ''){
                $('#firstNameErr').text('The first name field is required.');
                err = true;
                if(!focus) {
                    $('#firstName').focus();
                    focus = true;
                }
            }
            if(lastName == ''){
                $('#lastNameErr').text('The last name field is required.');
                err = true;
                if(!focus) {
                    $('#lastName').focus();
                    focus = true;
                }
            }
            if(email == ''){
                $('#affiliateEmailErr').text('The email field is required.');
                err = true;
                if(!focus) {
                    $('#affiliateEmail').focus();
                    focus = true;
                }
            }else if(!email.match(validRegex)){
                $('#affiliateEmailErr').text('Please enter valid email.');
                err = true;
                if(!focus) {
                    $('#affiliateEmail').focus();
                    focus = true;
                }
            }
            if(group == ''){
                $('#affiliateGroupErr').text('The affiliate group id field is required.');
                err = true;
            }
            if(!err) {
                return true;
            }
            return false;
        }

        function affiliateDeleteConfirm() {
            return confirm("Are sure you want to delete affiliate?");
        }
    </script>
@endsection
