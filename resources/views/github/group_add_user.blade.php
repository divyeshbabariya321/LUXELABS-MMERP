@extends('layouts.app')

@section('content')
<h2 class="text-center">Add user to <i>{{$group->name}}</i></h2>
<div>
    {{ html()->form('POST', url('/github/groups/users/add'))->open() }}
    {{ html()->hidden('group_id', $group->id) }}

    <div class="form-group">
        <label for="" class="form-label">Organization</label>
        <select name="organizationId" id="organizationId" class="form-control" required>
            @foreach ($githubOrganizations as $githubOrganization)
                <option value="{{ $githubOrganization->id }}" {{ ($githubOrganization->name == 'MMMagento' ? 'selected' : '' ) }}>{{  $githubOrganization->name }}</option>
            @endforeach
        </select>

        @if ($errors->has('organizationId'))
            <div class="alert alert-danger">{{$errors->first('organizationId')}}</div>
        @endif
    </div>

    <div class="form-group">
        {{ html()->label('User', 'username') }}
        {{ html()->select('username', $users)->class('form-control') }}
    </div>
    <div class="form-group">
        {{ html()->label('Role', 'role') }}
        <select name="role" class="form-control">
            <option value="member">Member</option>
            <option value="maintainer">Maintainer</option>
        </select>
    </div>
    {{ html()->submit('Add')->class('btn btn-primary') }}
    {{ html()->form()->close() }}
</div>
@endsection