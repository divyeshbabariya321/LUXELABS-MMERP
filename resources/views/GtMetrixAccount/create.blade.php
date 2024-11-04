@extends('layouts.app')


@section('content')
<div class="row ">
    <div class="col-lg-12 margin-tb pl-5 pr-5">
        <div class="pull-left">
            <h2>Create New GT Metrix Account</h2>
        </div>
        <div class="pull-right mt-4 ">
            <a class="btn custom-button" href="{{ route('GtMetrixAccount.index') }}"> Back</a>
        </div>
    </div>
</div>


@if (count($errors) > 0)
<div class="alert alert-danger">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif



{{ html()->form('POST', route('gtmetrix.account.store'))->open() }}
<div class="row m-4">
    <div class="col-xs-12 col-sm-12 col-md-3">
        <div class="form-group">
            {{ html()->text('email')->placeholder('Email')->class('form-control') }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3">
        <div class="form-group">
            {{ html()->text('password')->placeholder('Password')->class('form-control') }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-3">
        <div class="form-group">
            {{ html()->text('account_id')->placeholder('Api Key')->class('form-control') }}
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-2">
        <div class="form-group">
            {{ html()->select('status', ["active" => "Active", "error" => "Error", "in-active" => "In-Active"], request('status'))->class('form-control') }}
        </div>
    </div>
    
    <div class="col-xs-12 col-sm-12 col-md-1 text-center">
        <button type="submit" class="btn custom-button">+</button>
    </div>
    
</div>
{{ html()->form()->close() }}


@endsection