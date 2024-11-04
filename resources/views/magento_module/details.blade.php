@extends('layouts.app')

@section('content')
    <div class="row mt-5">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Add {{ $title }}</h2>
            </div>
            <div class="pull-right mr-5">
                <a class="btn btn-primary" href="{{ route('magento_modules.index') }}"> Back</a>

            </div>
        </div>
    </div>

    @include('magento_modules.partials.data')
@endsection
