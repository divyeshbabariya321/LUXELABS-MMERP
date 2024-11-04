@extends('layouts.app')


@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Update New Referral Program</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-secondary" href="{{ route('referralprograms.list') }}"> Back</a>
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



{{ html()->form('POST', route('referralprograms.update'))->open() }}
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Program name:</strong>
            {{ html()->text('name', $ReferralProgram->name ? $ReferralProgram->name : old('name'))->placeholder('Name')->class('form-control') }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Program uri:</strong>
            <select name="uri" class="form-control">
                <option value="">Select Website</option>
                @foreach($StoreWebsite as $website)
                <option value="{{$website->website}}" {{ ($website->id == $ReferralProgram->store_website_id)?'selected':(($website->website == old('uri'))?'selected':'')}}>{{$website->website}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Program credit:</strong>
            {{ html()->text('credit', $ReferralProgram->credit ? $ReferralProgram->credit : old('credit'))->placeholder('credit')->class('form-control') }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Program currency:</strong>
            {{ html()->text('currency', $ReferralProgram->currency ? $ReferralProgram->currency : old('currency'))->placeholder('currency')->class('form-control') }}
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Program lifetime minutes:</strong>
            {{ html()->text('lifetime_minutes', $ReferralProgram->lifetime_minutes ? $ReferralProgram->lifetime_minutes : old('lifetime_minutes'))->placeholder('Program lifetime minutes')->class('form-control') }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
        <input type="hidden" name="id" value="{{$ReferralProgram->id}}">
        <button type="submit" class="btn btn-secondary">+</button>
    </div>
    
</div>
{{ html()->form()->close() }}


@endsection