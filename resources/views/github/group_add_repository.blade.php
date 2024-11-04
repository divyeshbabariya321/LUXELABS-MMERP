@extends('layouts.app')

@section('content')
<h2 class="text-center">Add repository to <i>{{ $group->name }}</i></h2>
<div>
    {{ html()->form('POST', url('/github/groups/repositories/add'))->open() }}
    {{ html()->hidden('group_id', $group->id) }}

    <div class="form-group">
        <label for="" class="form-label">Organization</label>
        <select name="organizationId" id="organizationId" class="form-control" required>
            @foreach ($githubOrganizations as $githubOrganization)
                <option value="{{ $githubOrganization->id }}" data-repos='{{ $githubOrganization->repos }}' {{ ($githubOrganization->name == 'MMMagento' ? 'selected' : '' ) }}>{{  $githubOrganization->name }}</option>
            @endforeach
        </select>

        @if ($errors->has('organizationId'))
            <div class="alert alert-danger">{{$errors->first('organizationId')}}</div>
        @endif
    </div>

    <div class="form-group">
        <label for="" class="form-label">Repository</label>
        <select name="repoId" id="repoId" class="form-control" required>
            
        </select>
        @if ($errors->has('repoId'))
            <div class="alert alert-danger">{{$errors->first('repoId')}}</div>
        @endif
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
<script>
     $('#organizationId').change(function (){
        getRepositories();
    });

    function getRepositories(){
        var repos = $.parseJSON($('#organizationId option:selected').attr('data-repos'));

        $('#repoId').empty();

        if(repos.length > 0){
            $.each(repos, function (k, v){
                $('#repoId').append('<option value="'+v.id+'">'+v.name+'</option>');
            });
        }
    }

    $(document).ready(function() {
        getRepositories();
    });
</script>
@endsection