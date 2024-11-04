@extends('layouts.app')


@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Create New Referral Program</h2>
        </div>
        <div class="pull-right pr-4">
            <a class="btn btn-secondary mt-4" href="{{ route('referralprograms.list') }}"> Back</a>
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



{{ html()->form('POST', route('referralprograms.store'))->open() }}
<div class="row">
    <div class="col-xs-3 col-sm-3 col-md-3 pl-5">
        <div class="form-group">
            {{ html()->text('name')->placeholder('Name')->class('form-control') }}
        </div>
    </div>
    <div class="col-xs-3 col-sm-3 col-md-3">
        <div class="form-group">
            <select name="uri" class="form-control" placeholder="Program uri:">
                <option value="">Select Website</option>
                @foreach($StoreWebsite as $website)
                <option value="{{$website->website}}" {{ ($website->website == old('uri'))?'selected':''}}>{{$website->website}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-xs-3 col-sm-3 col-md-3 ">
        <div class="form-group">
            {{ html()->text('credit')->placeholder('credit')->class('form-control') }}
        </div>
    </div>
    <div class="col-xs-3 col-sm-3 col-md-3 pr-5">
        <div class="form-group">
            {{ html()->text('currency')->placeholder('currency')->class('form-control') }}
        </div>
    </div>

    <div class="col-xs-3 col-sm-3 col-md-3 pl-5">
        <div class="form-group">
            {{ html()->text('lifetime_minutes')->placeholder('Program lifetime minutes')->class('form-control') }}
        </div>
    </div>
    <div class="col-xs-1 col-sm-1 col-md-1 text-center">
        <button type="submit" class="btn mt-2 btn-secondary">+</button>
    </div>
    
</div>
{{ html()->form()->close() }}


@endsection