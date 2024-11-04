@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Jenkins Logs({{ $monitorJenkinsBuilds->total() }})</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-12">
                    <form action="{{ route('monitor-jenkins-build.index') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-2 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control h-100" value="{{ request()->get('keyword') }}">
                            </div>
                            <div class="col-md-2 pd-sm">
                                <label> Search Projects </label>
                                {{ html()->multiselect("project_id[]", \app\Models\MonitorJenkinsBuild::pluck('project', 'id')->toArray(), request('project_id'))->class("form-control globalSelect2")->data('placeholder', "Select Website") }}
                            </div>
                            <div class="col-md-2 pd-sm">
                                <label>Search worker </label>
                                {{ html()->multiselect("worker_id[]", \app\Models\MonitorJenkinsBuild::pluck('worker', 'id')->toArray(), request('worker_id'))->class("form-control globalSelect2")->data('placeholder', "Select Website") }}
                            </div>
                            <div class="col-md-2 pd-sm">
                                <label> Sort By ID </label>
                                <select name="id_sort_by" id="id_sort_by" class="form-control globalSelect" data-placeholder="Sort By">
                                    <option  Value="">Sort By ID</option>
                                    <option  Value="asc" {{ (request('id_sort_by') == "asc") ? "selected" : "" }} >ASC</option>
                                    <option value="desc" {{ (request('id_sort_by') == "desc") ? "selected" : "" }}>DSEC</option>
                                </select>
                                </div>       
                            <div class="col-md-2 pd-sm pl-0 mt-2">
                                <label> </label>
                                 <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                               </button>
                               <a href="{{route('monitor-jenkins-build.index')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                            </div>
                        </div>
                    </form>
                    <br>
                    <div class="col-md-2 pd-sm">
                        <a href="{{route('monitor-jenkins-build.truncate')}}" class="btn btn-primary" class= "form-control" onclick="return confirm('{{ __('Are you sure you want to Truncate a Data? Note : It will Remove all the data)') }}')">Truncate Data </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<div class="tab-content ">
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;" id="quick-reply-list">
                        <tr>
                            <th width="7%">No</th>
                            <th width="15%">Build No</th>
                            <th width="30%">Project</th>
                            <th width="30%">Worker</th>
                            <th width="15%">StoreId</th>
                            <th width="20%">Clone Repository</th>
                            <th width="20%">LockBuild</th>
                            <th width="20%">Update Code</th>
                            <th width="20%">Composer Install</th>
                            <th width="20%">Make Config</th>
                            <th width="20%">Setup Upgrade</th>
                            <th width="20%">Compile Code</th>
                            <th width="20%">Static Content</th>
                            <th width="20%">Reindexes</th>
                            <th width="20%">Magento Cache Flush</th>
                            <th width="45%">Error</th>
                            <th width="20%">Build Status</th>
                            <th width="35%">Full Log</th>
                            <th width="20%">Meta Update</th>
                            <th width="20%">Date Time</th>
                            <th width="30%">Insert Code Shortcut</th>
                        </tr>
                        @foreach ($monitorJenkinsBuilds  as $key => $monitorJenkinsBuild)
                            <tr class="quick-website-task-{{ $monitorJenkinsBuild->id }}" data-id="{{ $monitorJenkinsBuild->id }}">
                                <td>{{$monitorJenkinsBuild->id }}</td>
                                <td>{{ $monitorJenkinsBuild->build_number }}</td>
                                <td>{{ $monitorJenkinsBuild->project }}</td>
                                <td>{{ $monitorJenkinsBuild->worker }}</td>
                                <td>{{ $monitorJenkinsBuild->store_id }}</td>
                                <td><span class="badge badge-pill" style ="background-color: {{ $monitorJenkinsBuild->clone_repository === 0 ? 'green' : 'orange' }}">{{ $monitorJenkinsBuild->clone_repository === 0 ? 'Success' : 'Failure' }}</span></td>
                                <td><span class="badge badge-pill" style ="background-color: {{$monitorJenkinsBuild->lock_build === 0 ? 'green' : 'orange' }}">{{ $monitorJenkinsBuild->lock_build  === 0 ? 'Success' : 'Failure' }}</span></td>
                                <td><span class="badge badge-pill" style ="background-color: {{ $monitorJenkinsBuild->update_code === 0 ? 'green' : 'orange' }}">{{ $monitorJenkinsBuild->update_code === 0 ? 'Success' : 'Failure' }}</span></td>
                                <td><span class="badge badge-pill" style ="background-color: {{ $monitorJenkinsBuild->composer_install === 0 ? 'green' : 'orange'}}">{{ $monitorJenkinsBuild->composer_install === 0 ? 'Success' : 'Failure'}}</span></td>
                                <td><span class="badge badge-pill" style ="background-color: {{ $monitorJenkinsBuild->make_config === 0 ? 'green' : 'orange'}}">{{ $monitorJenkinsBuild->make_config === 0 ? 'Success' : 'Failure'}}</span></td>
                                <td><span class="badge badge-pill" style ="background-color: {{ $monitorJenkinsBuild->setup_upgrade === 0 ? 'green' : 'orange'}}">{{ $monitorJenkinsBuild->setup_upgrade === 0 ? 'Success' : 'Failure'}}</span></td>
                                <td><span class="badge badge-pill" style ="background-color: {{ $monitorJenkinsBuild->compile_code === 0 ? 'green' : 'orange'}}">{{ $monitorJenkinsBuild->compile_code === 0 ? 'Success' : 'Failure'}}</span></td>
                                <td><span class="badge badge-pill" style ="background-color: {{ $monitorJenkinsBuild->static_content === 0 ? 'green' : 'orange'}}">{{ $monitorJenkinsBuild->static_content === 0 ? 'Success' : 'Failure'}}</span></td>
                                <td><span class="badge badge-pill" style ="background-color: {{ $monitorJenkinsBuild->reindexes === 0 ? 'green' : 'orange'}}">{{ $monitorJenkinsBuild->reindexes === 0 ? 'Success' : 'Failure'}}</span></td>
                                <td><span class="badge badge-pill" style ="background-color:{{ $monitorJenkinsBuild->magento_cache_flush === 0 ? 'green' : 'orange'}}">{{ $monitorJenkinsBuild->magento_cache_flush === 0 ? 'Success' : 'Failure'}}</span></td>
                                <td>{{ $monitorJenkinsBuild->error }}</td>
                                <td><span class="badge badge-pill" style = "background-color:{{ $monitorJenkinsBuild->build_status === 0 ? 'green' : 'orange' }}">{{ $monitorJenkinsBuild->build_status === 0 ? 'Success' : 'Failure' }}</span></td>
                                <td style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($monitorJenkinsBuild->full_log) > 10 ? substr($monitorJenkinsBuild->full_log, 0, 10).'...' :  $monitorJenkinsBuild->full_log }}
								       <i class="fa fa-eye show_logs show-full-log-text" data-full-log="{{ nl2br($monitorJenkinsBuild->full_log) }}" style="color: #808080;float: right;"></i>
                                    </span>
                                </td>
                                <td><span class="badge badge-pill" style = "background-color:{{ $monitorJenkinsBuild->meta_update === 0 ? 'green' : 'orange' }}">{{ $monitorJenkinsBuild->meta_update === 0 ? 'Success' : 'Failure' }}</span></td>
                                <td>{{ $monitorJenkinsBuild->created_at }}</td>
                                <td><button class="btn btn-success monitor-insert-code-shortcut" data-id="{{ $monitorJenkinsBuild->id }}">Insert Code</button>		
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                {!! $monitorJenkinsBuilds->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="show_full_log_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Full Log</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="show_full_log_modal_content">
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@section("styles")
<style>
    /* CSS to make specific modal body scrollable */
    #show_full_log_modal .modal-body {
      max-height: 400px; /* Maximum height for the scrollable area */
      overflow-y: auto; /* Enable vertical scrolling when content exceeds the height */
    }
</style>

@endsection

<script>
     $(document).on('click', '.show-full-log-text', function() {
        var fullLog = $(this).data('full-log');
        $('#show_full_log_modal').modal('show');
        $('#show_full_log_modal_content').html(fullLog);
    });

    $(document).on('click', '.monitor-insert-code-shortcut', function() {
		var id = $(this).data('id');
			$.ajax({
				url: '{{route('monitor-jenkins-insert-code-shortcut')}}',
				method: 'GET',
				data: {
					id: id
				},
				success: function(response) {
                    toastr['success'](response.message);
				},
				error: function(xhr, status, error) {
                    toastr['error'](response.message);
				}
			});
		});

 </script>
@endsection