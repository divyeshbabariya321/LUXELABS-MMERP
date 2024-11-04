@extends('layouts.app')


@section('content')
<div class="row m-4">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2><strong>Update Account Data</strong></h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('GtMetrixAccount.index') }}"> Back</a>
        </div>
    </div>
</div>

@if(Session::has('success'))
<div class="alert alert-success">
      <p>{{ Session::get('success') }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
  </div> <!-- end .flash-message -->
@endif

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

{{ html()->form('POST', route('account.update'))->open() }}
<div class="row m-4">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Email:</strong>
            {{ html()->text('email', $account->email ? $account->email : old('email'))->placeholder('Email')->class('form-control') }}
        </div>
    </div>
    
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Password:</strong>
            {{ html()->text('password', $account->password ? $account->password : old('password'))->placeholder('Password')->class('form-control') }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Api Key:</strong>
            {{ html()->text('account_id', $account->account_id ? $account->account_id : old('account_id'))->placeholder('Api Key')->class('form-control') }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Status:</strong>
            {{ html()->select('status', ["active" => "Active", "error" => "Error", "in-active" => "In-Active"], $account->status ? $account->status : old('status'))->class('form-control') }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
        <input type="hidden" name="id" value="{{$account->id}}">
        <button type="submit" class="btn btn-success">+</button>
    </div>
    
</div>
{{ html()->form()->close() }}


@endsection