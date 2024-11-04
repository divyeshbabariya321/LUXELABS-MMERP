@extends('layouts.app')

@section('title', 'Blogger Email')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Blogger Email</h2>
            {{ html()->modelForm($template, 'PUT', route('blogger.email.template.update', [$template->id]))->open() }}
            <div class="row">
                <!-- subject -->
                <div class="col-md-12 col-lg-12 @if($errors->has('subject')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                    <div class="form-group">
                        {{ html()->label(__('Subject'), 'subject')->class('form-control-label') }}
                        {{ html()->text('subject')->class('form-control ' . ($errors->has('subject') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : '')))->required() }}
                        @if($errors->has('subject'))
                            <div class="form-control-feedback">{{$errors->first('subject')}}</div>
                        @endif
                    </div>
                </div>
                <!-- message -->
                <div class="col-md-12 col-lg-12 @if($errors->has('message')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                    <div class="form-group">
                        {{ html()->label(__('Message'), 'message')->class('form-control-label') }}
                        {{ html()->textarea('message')->class('form-control ' . ($errors->has('message') ? 'form-control-danger' : (count($errors->all()) > 0 ? ' form-control-success' : '')))->required() }}
                        @if($errors->has('message'))
                            <div class="form-control-feedback">{{$errors->first('message')}}</div>
                        @endif
                    </div>
                </div>
            </div>
            <button class="btn btn-primary" type="submit">Update</button>
            {{ html()->closeModelForm() }}
        </div>
    </div>

    @include('partials.flash_messages')
@endsection
