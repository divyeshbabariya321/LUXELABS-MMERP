@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Build process error logs</h2>
    </div>
    <div class="mt-3 col-md-12">
		<form action="{{route('project.buildProcessErrorLogs')}}" method="get" class="search">
			<div class="col-md-2 pd-sm">
            <h5> Search Projects </h5> 
                <?php 
                if(request('project_search')){   $project_search = request('project_search'); }
                else{ $project_search = []; }
            ?>
            <select name="project_search[]" id="project_search" class="form-control select2" multiple>
                    <option value="" @if($project_search=='') selected @endif>-- Select a  projects --</option>
                    @forelse($projects as $project)
                    <option value="{{ $project->id }}" @if(in_array($project->id, $project_search)) selected @endif>{!! $project->name !!}</option>
                    @empty
                    @endforelse
            </select>
			</div>
            <div class="col-md-2 pd-sm">
                <h5> Search Organizations </h5> 
              <?php
                    if (request('orgs')) {$selectedOrgs = request('orgs'); } else {
                        $selectedOrgs = [];} ?>
                <select name="orgs[]" id="orgs" class="form-control select2" multiple>
                    <option value="">Select organizations</option>
                    @foreach ($organizations as $org)
                        <option value="{{$org->id}}" @if(in_array($org->id, $selectedOrgs)) selected @endif>{{$org->name}}</option>
                    @endforeach
                </select>
             </div>
            <div class="col-md-2 pd-sm">
                <h5> Search repositories </h5> 
                <?php 
                    if(request('repos')){   $repos = request('repos'); }
                    else{ $repos = []; }
                ?>
                <select name="repos[]" id="repos" class="form-control select2" multiple>
                    <option  Value="">Select repositories</option>
                    @foreach ($repositories as $repository)
                    <option  Value="{{$repository->id}}"  @if(in_array($repository->id, $repos)) selected @endif>{!! $repository->name !!}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 pd-sm">
                <h5> Search Users </h5> 
                <?php 
                    if(request('usersIds')){  $usersIds = request('usersIds'); }
                    else{ $usersIds = []; }
                ?>
                <select name="usersIds[]" id="usersIds" class="form-control select2" multiple>
                    <option  Value="">Select Users</option>
                    @foreach ($users as $user)
                    <option  Value="{{$user->id}}"  @if(in_array($user->id, $usersIds)) selected @endif>{!! $user->name !!}</option>
                    @endforeach
                </select>
             </div>
			<div class="col-lg-2">
                <h5> Search branch </h5> 
				<input class="form-control" type="text" id="search_error" placeholder="Search branch" name="search_branch" value="{{ (request('search_branch') ?? "" )}}">
			</div>
			<div class="col-lg-2">
                <h5> Search Error Code </h5> 
				<input class="form-control" type="text" id="error_code" placeholder="Search Error Code" name="error_code" value="{{ (request('error_code') ?? "" )}}">
			</div>
            <div class="col-lg-2"><br>
				<input class="form-control" type="text" id="error_msg" placeholder="Search Error Message" name="error_msg" value="{{ (request('error_msg') ?? "" )}}">
			</div>
			<div class="col-lg-2"><br>
				<input class="form-control" type="date" name="date" value="{{ (request('date') ?? "" )}}">
			</div>
			<div class="col-lg-2"><br>
				<button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
				   <img src="{{ asset('images/search.png') }}" alt="Search">
			   </button>
			   <a href="{{route('project.buildProcessErrorLogs')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
			</div>
		</form>
	</div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<div class="tab-content">
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;" id="build-process-error-logs-list">
                        <tr>
                            <th width="2%">ID</th>
                            <th width="10%">Project</th>
                            <th width="10%">Organization</th>
                            <th width="10%">Repo</th>
                            <th width="10%">Branch</th>
                            <th width="10%">Error Message</th>
                            <th width="10%">Error Code</th>
                            <th width="10%">Created By</th>
                            <th width="10%">Created At</th>
                        </tr>
                        @foreach ($buildProcessErrorLogs as $key => $buildProcessErrorLog)
                            <tr data-id="{{ $buildProcessErrorLog->id }}">
                                <td>{{ $buildProcessErrorLog->id }}</td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $buildProcessErrorLog->project->name ?? '' }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $buildProcessErrorLog->organization->name ?? '' }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $buildProcessErrorLog->repository->name ?? '' }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $buildProcessErrorLog->github_branch_state_name }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($buildProcessErrorLog->error_message) > 30 ? substr($buildProcessErrorLog->error_message, 0, 30).'...' :  $buildProcessErrorLog->error_message }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $buildProcessErrorLog->error_message }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $buildProcessErrorLog->error_code }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $buildProcessErrorLog->user?->name }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $buildProcessErrorLog->created_at }}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                {!! $buildProcessErrorLogs->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>

$('.select2').select2();

$(document).on('click', '.expand-row', function () {
    var selection = window.getSelection();
    if (selection.toString().length === 0) {
        $(this).find('.td-mini-container').toggleClass('hidden');
        $(this).find('.td-full-container').toggleClass('hidden');
    }
});

$( document ).ready(function() {

});
</script>
@endsection