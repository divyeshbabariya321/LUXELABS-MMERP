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
                {!! $provider->provider->provider_name !!} Programmes (<span
                        id="affiliate_count">{{ $providersProgrammes->total() }}</span>)
            </h2>
            <div class="pull-left">
                <form action="{{route('affiliate-marketing.provider.program.index', ['provider_account' => $provider->id])}}">
                    <div class="form-group">
                        <input type="hidden" name="provider_account" value="{!! $provider->id !!}">
                        <div class="row">
                            <div class="col-md-6">
                                <input name="title" type="text" class="form-control"
                                       value="{{ request('title') }}" placeholder="Search programme">
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-image">
                                    <img src="/images/filter.png"/>
                                </button>
                                <button type="reset"
                                        onclick="window.location='{{route('affiliate-marketing.provider.program.index', ['provider_account' => $provider->id])}}'"
                                        class="btn btn-image" id="resetFilter">
                                    <img src="/images/resend2.png"/>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6 pl-0 float-right">
                {{ html()->form('POST', route('affiliate-marketing.provider.program.sync', [['provider_account' => $provider->id]]))->style('display:inline')->open() }}
                <button type="submit" class="float-right mb-3 btn-secondary">Refresh Programmes
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
                <th>Name</th>
                <th>Programme Id</th>
                <th>Currency</th>
                <th>Cookie Time</th>
                <th>Default Landing page url</th>
                <th>Recurring</th>
                <th>Recurring Cap</th>
                <th>Recurring Period Days</th>
                <th>Category Identifier</th>
                <th>Category Title</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($providersProgrammes as $key => $providersProgramme)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $providersProgramme->title }}</td>
                    <td>{{ $providersProgramme->affiliate_program_id }}</td>
                    <td>{{ $providersProgramme->currency }}</td>
                    <td>{{ $providersProgramme->cookie_time }}</td>
                    <td>{{ $providersProgramme->default_landing_page_url }}</td>
                    <td>{{ $providersProgramme->recurring ? 'Yes' : 'No' }}</td>
                    <td>{{ $providersProgramme->recurring_cap ?: 'N/A'}}</td>
                    <td>{{ $providersProgramme->recurring_period_days ?: 'N/A'}}</td>
                    <td>{{ $providersProgramme->program_category_identifier ?: 'N/A'}}</td>
                    <td>{{ $providersProgramme->program_category_title ?: 'N/A'}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {!! $providersProgrammes->render() !!}
@endsection

@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
@endsection
