@extends('layouts.app')

@section('content')
<h2 class="text-center">Edit project</h2>
<div>

    {{ html()->modelForm($project, 'PUT', url('/hubstaff/projects/edit'))->open() }}
    {{ html()->hidden('id', Input::old('id')) }}
    {{ html()->hidden('hubstaff_project_id', Input::old('hubstaff_project_id')) }}
    <div class="form-group">
        {{ html()->label('Name', 'name') }}
        {{ html()->text('name', Input::old('name'))->class('form-control') }}
    </div>
    <div class="form-group">
        {{ html()->label('Description', 'description') }}
        {{ html()->text('description', Input::old('description'))->class('form-control') }}
    </div>
    
    {{ html()->submit('Save')->class('btn btn-primary') }}

    {{ html()->closeModelForm() }}

</div>
@endsection