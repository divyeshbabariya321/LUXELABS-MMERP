@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">{{$group->name}}</h2>
    </div>
</div>

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#repositories">Repositories</a></li>
    <li><a data-toggle="tab" href="#members">Members</a></li>
</ul>
<div class="tab-content">
    <div id="repositories" class="tab-pane fade in active">
        <div class="text-right">
            <a style="margin: 10px 0px" class="btn btn-sm btn-primary" href="{{ url('/github/groups/'.$group->id.'/repositories/add') }}">Add Repository</a>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Permission</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($repositories as $repository)
                <tr>
                    <td>{{$repository->id}}</td>
                    <td>{{$repository->name}}</td>
                    <td>{{$repository->pivot->rights}}</td>
                    <td>
                        <a class="btn btn-secondary btn-sm" href="/github/groups/{{$group->id}}/repos/{{$repository->id}}/remove">Remove</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div id="members" class="tab-pane fade">
        <div class="text-right">
            <a style="margin: 10px 0px" class="btn btn-sm btn-primary" href="{{ url('/github/groups/'.$group->id.'/users/add') }}">Add User</a>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{$user->id}}</td>
                    <td>{{$user->username}}</td>
                    <td>
                        <button type="button" class="btn btn-primary" data-github-groups-memeber-remove group-id="{{ $group->id }}" user-id="{{ $user->id }}">Remove</button>
                        <!-- <a class="btn btn-secondary btn-sm" href="/github/groups/{{$group->id}}/users/{{$user->id}}/remove">Remove</a> -->
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="viewOrganizationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 999999;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Select Organization</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <form action="#" method="POST" id="githubOrganizationForm">
        <input type="hidden" name="github_group_id" id="github_group_id">
        <input type="hidden" name="github_user_id" id="github_user_id">

        <div class="modal-body">
            <div class="form-group">
                <label for="recipient-name" class="col-form-label">Organization</label>
                <select class="form-control" id="organization_id" name="organization_id" required>
                    @foreach($githubOrganizations as $githubOrganization)
                        <option value="{{ $githubOrganization->id }}">{{ $githubOrganization->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
       <form>
    </div>
  </div>
</div>
<script>
    $('#githubOrganizationForm').submit(function(e){
      e.preventDefault();

      var groupId = $('#github_group_id').val();
      var userId = $('#github_user_id').val();
      var organizationId = $('#organization_id').val();
      
      window.location.href = '/github/groups/'+groupId+'/users/'+userId+'/organization/'+organizationId+'/remove';
    });

    $('[data-github-groups-memeber-remove]').click(function (){
        var groupId = $(this).attr('group-id');
        var userId = $(this).attr('user-id');

        $('#github_group_id').val(groupId);
        $('#github_user_id').val(userId);
        
        $('#viewOrganizationModal').modal('show');
    });
</script>

@endsection