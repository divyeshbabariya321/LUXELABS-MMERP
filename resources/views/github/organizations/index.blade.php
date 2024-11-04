@extends('layouts.app')

@section('content')
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<script>
    $(document).ready(function() {
        $('#organizations-table').DataTable({
            "paging": true,
            "ordering": true,
            "info": false
        });
    });
</script>
<style>
    #organizations-table_filter {
        text-align: right;
    }
</style>
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Github Organizations ({{ count($githubOrganizations) }})</h2>
    </div>
</div>
<div class="container container-grow">
    <button class="btn btn-primary btn-sm mb-3 pull-right" data-upsert-organization-btn data-mode="ADD"><i class="fa fa-plus"></i> Add Organization</button>
    <form method="get" action="{{url('github/organizations/')}}">

        <div class="form-group">
            <div class="row">
                <div class="col-md-2">
                    <input name="query_string" type="text" class="form-control" value="{{$request->name??''}}"  placeholder="Enter Name or Username" id="name">
                </div>

                <div class="col-md-1 d-flex justify-content-between">
                    <button type="submit" class="btn btn-image" ><img src="/images/filter.png"></button>
                    <button type="button" onclick="resetForm(this)" class="btn btn-image" id=""><img src="/images/resend2.png"></button>
                </div>
            </div>
        </div>
    </form>
    <table id="repository-table" class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Token</th>
                <th>Added by</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($githubOrganizations as $githubOrganization)
            <tr>
                <td>{{ $githubOrganization->name }}</td>
                <td>{{ $githubOrganization->username }}</td>
                <td>{{ $githubOrganization->token }} 
                    @if (!empty($githubOrganization->token))
                    <div class="btn btn-default delete-token" data-url="{{ url('/github/repos/delete-token/'.$githubOrganization->id) }}" href="return:void(0)" title="Delete token">
                        <i class="fa fa-trash"></i>
                    </div>   
                    @endif
                </td>
                <td>{{ $githubOrganization->user->name ?? '' }}</td>
                
                <td>
                    <button class="btn btn-default"  title="Edit Organization" data-upsert-organization-btn data-mode="EDIT" data-id="{{ $githubOrganization->id }}" data-name='{{ $githubOrganization->name }}' data-username='{{ $githubOrganization->username }}' data-token='{{ $githubOrganization->token }}'>
                        <i class="fa fa-edit"></i>
                    </button>
                    <a class="btn btn-default" href="{{ url('/github/repos/'.$githubOrganization->id) }}" title="Repositories">
                        <i class="fa fa-eye"></i>
                    </a>
                    <div class="btn btn-default delete-organization" data-url="{{ url('/github/organization/delete/'.$githubOrganization->id) }}" href="return:void(0)" title="Delete token">
                        <i class="fa fa-trash"></i>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
<div class="modal fade" id="upsertGithubOrganizationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><span id="form_mode_html_id">Add</span> Organization</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ url('github/organizations') }}" method="POST">
        @csrf

        <div class="modal-body">
            <input type="hidden" id="organization_id" name="organization_id" value="{{ old('organization_id') }}">

            <div class="form-group">
                <strong>Name:</strong>
                <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="{{ old('name') }}" required/>
                @if ($errors->has('name'))
                <div class="alert alert-danger">{{$errors->first('name')}}</div>
                @endif
            </div>

            <div class="form-group">
                <strong>Username:</strong>
                <input type="text" class="form-control" name="username" id="username" placeholder="Username" value="{{ old('username') }}" required/>
                @if ($errors->has('username'))
                <div class="alert alert-danger">{{$errors->first('username')}}</div>
                @endif
            </div>
    
            <div class="form-group">
                <strong>Token:</strong>
                <input type="text" class="form-control" name="token" id="token" placeholder="Token" value="{{ old('token') }}" required/>
                @if ($errors->has('token'))
                <div class="alert alert-danger">{{$errors->first('token')}}</div>
                @endif
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script>
    $('[data-upsert-organization-btn]').click(function (){
        var mode = $(this).attr('data-mode');

        $('#organization_id').val('');
        $('#name').val('');
        $('#username').val('');
        $('#token').val('');

        if(mode == 'EDIT'){
            $('#organization_id').val($(this).attr('data-id'));
            $('#name').val($(this).attr('data-name'));
            $('#username').val($(this).attr('data-username'));
            $('#token').val($(this).attr('data-token'));

            $('#form_mode_html_id').html('Edit');
        }else{
            $('#form_mode_html_id').html('Add');
        }

        $('#upsertGithubOrganizationModal').modal('show');
    });

    $(document).on('click','.delete-token',function(){
        if(confirm("Are you sure you want to delete this token?")){
            var api_url = $(this).data('url')
            $.ajax({
                method: "GET",
                url: api_url,
                data: [],
                dataType: "json",
                success: function(response){
                    if (response.status == true) {
                        toastr.success('Token Deleted Successfully');
                        location.reload();
                    } else {
                        toastr.error(response.message);
                    }
                    
                },
                beforeSend: function() {
                },
                error: function() {
                    alert("There was an error sending the message...");
                }
                
            });
        }

        return false;
    });

    $(document).on('click','.delete-organization',function(){
        if(confirm("Are you sure you want to delete this organization?")){
            var api_url = $(this).data('url')
            $.ajax({
                method: "get",
                url: api_url,
                data: [],
                dataType: "json",
                success: function(response){
                    if (response.status == true) {
                        toastr.success('Deleted Successfully');
                        location.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
                beforeSend: function() {
                },
                error: function() {
                    alert("There was an error sending the message...");
                }
                
            });
        }

        return false;
    });


    function resetForm(selector)
    {
        window.location.href = "{{ url('github/organizations/') }}";
    }

</script>
@endsection