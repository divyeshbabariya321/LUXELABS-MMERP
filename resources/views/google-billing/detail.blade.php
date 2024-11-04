@extends('layouts.app')
@section('title', 'Google Billing Acount Detail')
@section('large_content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Google Billing Accounts Detail</h2>
        </div>
    </div>
    @include('partials.flash_messages')
    <div class="row">
        <div class="col-md-12">
            <a href="{{ route('google.billing.index') }}" class="text-white"><button class="btn btn-secondary pull-right">Back</button></a>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">

            <div class="table-responsive">
                <table class="table table-bordered table-hover" style="border: 1px solid #ddd;">
                    <thead>
                    <tr>
                        <th style="width:5%;" class="text-center">Sl no</th>
                        <th style="width:15%;" class="text-center">Service</th>
                        <th style="width:15%;" class="text-center">Project Id</th>
                        <th style="width:15%;" class="text-center">Email</th>
                        <th style="width:15%;" class="text-center">Billing Amount</th>
                        <th style="width:5%;" class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody class="text-center" style="word-wrap: break-word;">
                    @foreach($googleBillingAccounts as $key => $googleBillingAccount)
                        <tr>
                            <td>#</td>
                            <td>{{ config('constants.GOOGLE_SERVICE_ACCOUNTS.'.$googleBillingAccount['service_type']) }}</td>
                            <td>{{ $googleBillingAccount['project_id'] }}</td>
                            <td>{{ $googleBillingAccount['email'] }}</td>
                            <td>{{ $googleBillingAccount['amount'] }}</td>
                            <td>
                                <div class="d-flex">
                                    
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
