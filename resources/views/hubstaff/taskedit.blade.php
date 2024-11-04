@extends('layouts.app')

@section('content')
<h2 class="text-center">{{ isset($isNew)? 'Add task' : 'Edit Task' }}</h2>
<div>

    {{
        isset($isNew)
        ? Form::open(array('url' => '/hubstaff/tasks/addData'))
        : Form::model($task, array('url' => '/hubstaff/tasks/editData', 'method' => 'PUT'))
    }}
    {{ html()->hidden('id', Input::old('id')) }}
    {{ html()->hidden('lock_version', Input::old('lock_version')) }}
    <div class="form-group">
        {{ html()->label('Summary', 'summary') }}
        {{ html()->text('summary', Input::old('summary'))->class('form-control') }}
    </div>
    <div class="form-group">
        {{ html()->label('Project', 'project_id') }}
        {{ 
            isset($isNew)
            ? Form::select('project_id', $projects, null , array('class' => 'form-control')) 
            : Form::select('project_id', $projects, $task['project_id'], array('class' => 'form-control')) 
        }}
    </div>
    
    @if(isset($isNew))
    <div class="form-group">
        {{ html()->label('Assignee', 'assignee_id') }}
        {{ html()->select('assignee_id', $users)->class('form-control') }}
    </div>
    @endif

    {{ html()->submit('Save')->class('btn btn-primary') }}

    {{ html()->form()->close() }}

</div>
@endsection