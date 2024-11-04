@extends('layouts.app')

@section('content')
	<div class="row mt-5">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Add {{$title}}</h2>
            </div>
            <div class="pull-right mr-5">
                <a class="btn btn-primary" href="{{ route('magento_modules.index') }}"> Back</a>
               
            </div>
        </div>
    </div>
       @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
   		 @endif

      {{ html()->form('POST', route('magento_modules.store'))->class('form mb-15')->acceptsFiles()->open() }}
        @include('magento_module.form')
      {{ html()->form()->close() }}
@endsection