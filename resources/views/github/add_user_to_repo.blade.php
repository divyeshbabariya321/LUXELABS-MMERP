@extends('layouts.app')

@section('content')
<h2 class="text-center">Add user to repository</h2>
<div>
    {{ html()->form('POST', url('/github/add_user_to_repo'))->open() }}
    {{ html()->hidden('repoId', $repoId) }}
    <div class="form-group">
        {{ html()->label('Users', 'username') }}
        {{ html()->select('username', $users)->class('form-control') }}
    </div>
    <div class="form-group">
        {{ html()->label('Permission', 'permission') }}
        <select name="permission" class="form-control">
            <option value="pull">Pull</option>
            <option value="push">Push</option>
            <option value="admin">Admin</option>
        </select>
    </div>
    {{ html()->submit('Add')->class('btn btn-primary') }}
    {{ html()->form()->close() }}
</div>
@endsection