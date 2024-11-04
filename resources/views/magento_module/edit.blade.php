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
    <div class="row ml-5 mr-5">
        <div class="col-lg-12">
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        </div>
    </div>
    @endif
      
      {{ html()->modelForm($magento_module, 'PATCH', route('magento_modules.update', [$magento_module->id]))->class('form mb-15')->acceptsFiles()->open() }}

      {{ html()->hidden('id', $magento_module->id) }}

        @include('magento_module.form')

      {{ html()->closeModelForm() }}
@endsection