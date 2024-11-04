@extends('layouts.app')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endsection


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-heading">Pr Activites({{ $prActivities->total() }})</h2>
        </div>
    </div>
    <div class="mt-3 col-md-12">
        <form action="{{ url('/github/new-pr-activities') }}" method="get" class="search">
            <div class="col-lg-2">
                <label> Search Organizations </label>
                <?php 
                if(request('org')){   $org_search = request('org'); }
                else{ $org_search = []; }
                ?>
                <select name="org[]" id="org" class="form-control select2" multiple>
                    <option value="" @if($org_search=='') selected @endif>-- Select a Organizations --</option>
                    @forelse($orgs as $id=>$org)
                    <option value="{{ $id }}" @if(in_array($id, $org_search)) selected @endif>{!! $org !!}</option>
                    @empty
                    @endforelse
                </select>
            </div>
            <div class="col-lg-2">
                <label> Search Events </label>
               <?php 
                    if(request('event')){   $event_search = request('event'); }
                    else{ $event_search = []; }
                    ?>
                    <select name="event[]" id="event" class="form-control select2" multiple>
                        <option value="" @if($event_search=='') selected @endif>-- Select a Events --</option>
                        @forelse($events as $id=>$event)
                        <option value="{{ $event }}" @if(in_array($event, $event_search)) selected @endif>{!! $event !!}</option>
                        @empty
                        @endforelse
                    </select>
            </div>
            <div class="col-lg-2">
                <label> Search Event header </label>
                 <?php 
                    if(request('event_header')){   $event_head_search = request('event_header'); }
                    else{ $event_head_search = []; }
                    ?>
                    <select name="event_header[]" id="event_header" class="form-control select2" multiple>
                        <option value="" @if($event_head_search=='') selected @endif>-- Select a Event header --</option>
                        @forelse($eventHeaders as $id=>$eventHead)
                        <option value="{{ $eventHead }}" @if(in_array($eventHead, $event_head_search)) selected @endif>{!! $eventHead !!}</option>
                        @empty
                        @endforelse
                    </select>
            </div>
            <div class="col-lg-2">
                <label> Search Repository </label>
                <?php 
                        if(request('repo')){ $repo_search = request('repo'); }
                        else{ $repo_search = []; }
                    ?>
                    <select name="repo[]" id="repo" class="form-control select2" multiple>
                        <option value="" @if($repo_search=='') selected @endif>-- Select a Repository --</option>
                        @forelse($repos as $id=>$repo)
                        <option value="{{ $id}}" @if(in_array($id, $repo_search)) selected @endif>{!! $repo!!}</option>
                        @empty
                        @endforelse
                    </select>
            </div>
            <div class="col-lg-2">
                <label> Search User </label>
                 <?php 
                        if(request('user')){ $user_search = request('user'); }
                        else{ $user_search = []; }
                    ?>
                    <select name="user[]" id="user" class="form-control select2" multiple>
                        <option value="" @if($user_search=='') selected @endif>-- Select a User --</option>
                        @forelse($users as $id=>$user)
                        <option value="{{ $user}}" @if(in_array($user, $user_search)) selected @endif>{!! $user!!}</option>
                        @empty
                        @endforelse
                    </select>
            </div>
            <div class="col-lg-2">
                <label> Search Pull Numbers </label>
                 <?php 
                        if(request('pull_number')){ $pull_num_search = request('pull_number'); }
                        else{ $pull_num_search = []; }
                    ?>
                    <select name="pull_number[]" id="pull_number" class="form-control select2" multiple>
                        <option value="" @if($pull_num_search=='') selected @endif>-- Select a Pull Number --</option>
                        @forelse($pullNumbers as $id=>$pullNum)
                        <option value="{{ $pullNum}}" @if(in_array($pullNum, $pull_num_search)) selected @endif>{!! $pullNum!!}</option>
                        @empty
                        @endforelse
                    </select>
            </div>
            <div class="col-lg-2">
                <label> Search labelNames </label>
                 <?php 
                        if(request('label_name')){ $label_search = request('label_name'); }
                        else{ $label_search = []; }
                    ?>
                    <select name="label_name[]" id="label_name" class="form-control select2" multiple>
                        <option value="" @if($label_search=='') selected @endif>-- Select a labelNames --</option>
                        @forelse($labelNames as $id=>$label)
                        <option value="{{ $label}}" @if(in_array($label, $label_search)) selected @endif>{!! $label!!}</option>
                        @empty
                        @endforelse
                    </select>
            </div>
            <div class="col-lg-2">
                <label> Search Description</label>
                <input class="form-control" type="text" id="description" placeholder="Search Description" name="description"
                        value="{{ request('description') ?? '' }}">
            </div>
            <div class="col-lg-2">
                <label> Search body</label>
             <input class="form-control" type="text" id="body" placeholder="Search body" name="body"
                        value="{{ request('body') ?? '' }}">
            </div>
            <div class="col-lg-2">
                <label> Search Activity Date</label>
             <input class="form-control" type="date" name="activity_date" value="{{ request('activity_date') ?? '' }}">
            </div>
            <div class="col-lg-2">
                <label> Search Created At</label>
                    <input class="form-control" type="date" name="date" value="{{ request('date') ?? '' }}">
            </div>
    
            <div class="col-lg-2"><br>
                <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                    <img src="{{ asset('images/search.png') }}" alt="Search">
                </button>
                <a href="{{ url('/github/new-pr-activities') }}" class="btn btn-image" id=""><img
                        src="/images/resend2.png" style="cursor: nwse-resize;"></a>
            </div>
        </form>
    </div>
    <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped" id="log-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pull Number</th>
                    <th>Repo organization</th>
                    <th>Repo Name</th>
                    <th>Event</th>
                    <th>Event Header</th>
                    <th>Body</th>
                    <th>Description</th>
                    <th>Lablel Name</th>
                    <th>user</th>
                    <th>Activity Created At</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            <tbody>
                @foreach ($prActivities as $prAct)
                    <tr>
                        <td>{{ $prAct->id }}</td>
                        <td>{{ $prAct->pull_number }}</td>
                        <td>{{ $prAct->githubOrganization?->name }}</td>
                        <td>{{ $prAct->githubRepository?->name }}</td>
                        <td>{{ $prAct->event }}</td>
                        <td>{{ $prAct->event_header }}</td>
                        <td style="cursor:pointer;" id="reply_text" class="expand-row error-text-modal"
                            data-id="{{ $prAct->id }}" data-message="{{ $prAct->body }}">
                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                <div class="td-mini-container">
                                    {!! strlen($prAct->body) > 20
                                        ? substr($prAct->body, 0, 45) . '...'
                                        : $prAct->body !!}
                                </div>
                                <div class="td-full-container hidden">
                                    {{ $prAct->body }}
                                </div>
                            </div>
                        </td>
                        <td style="cursor:pointer;" id="reply_text" class="expand-row error-text-modal"
                            data-id="{{ $prAct->id }}" data-message="{{ $prAct->description }}">
                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                <div class="td-mini-container">
                                    {!! strlen($prAct->description) > 20
                                        ? substr($prAct->description, 0, 45) . '...'
                                        : $prAct->description !!}
                                </div>
                                <div class="td-full-container hidden">
                                    {{ $prAct->description }}
                                </div>
                            </div>
                        </td>
                        <td>{{ $prAct->label_name }}</td>
                        <td>{{ $prAct->user }}</td>
                        <td>{{ \Carbon\Carbon::parse($prAct->activity_created_at)->format('Y-m-d H:i:s') }}</td>
                        <td>{{ $prAct->created_at?->format('Y-m-d') }}</td>
                        <td>
                            @if($prAct->label_name == "Ready to Merge")
                                <button title="Build Process" data-repo ="{{ $prAct->github_repository_id}}" data-id="{{ $prAct->github_organization_id }}" type="button" class="btn open-build-process-template" style="padding:1px 0px;">
                                    <a href="javascript:void(0);" style="color:gray;"><i class="fa fa-simplybuilt"></i></a>
                                </button> 
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            </thead>
        </table>
        {!! $prActivities->appends(Request::except('page'))->links() !!}
    </div>
    <div id="loading-image"
        style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
    </div>

   <div id="build-process-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Build Process</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <form id="build-process">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <strong>projects:</strong>
                                        <select name="project" id="project" class="form-control select2" style="width: 100%!important">
                                            <option value="" selected disabled>-- Select a Project --</option>
                                            @forelse($projects as $project)
                                            <option value="{{ $project->id }}">
                                                {{ $project->name }}
                                            </option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <strong>Job Name :</strong>
                                        {{ html()->text('job_name')->placeholder('Job Name')->id('job_name')->class('form-control')->required() }}
                                    </div>
                                    <div class="form-group">
                                        <strong>Organizations:</strong>
                                        <select name="organization" id="build_organization" class="form-control select2" style="width: 100%!important">
                                            <option value="" selected disabled>-- Select a Organizations --</option>
                                            @forelse($organizations as $organization)
                                            <option value="{{ $organization->id }}">
                                                {{ $organization->name }}
                                            </option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <strong>Repository:</strong>
                                        <select name="repository" id="build_repository" class="form-control select2" style="width: 100%!important">
                                            <option value="" selected disabled>-- Select a Repository --</option>
                                            @forelse($repositories as $repo)
                                            <option value="{{ $repo->id }}">
                                                {{ $repo->name }}
                                            </option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                </div>                            
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <strong>Branch Name:</strong>
                                        <select name="branch_name" id="build_branch_name" class="form-control select2" style="width: 100%!important">
                                            <option value="" selected disabled>-- Select a Branch --</option>
                                            @forelse($branches as $branch)
                                            <option value="{{ $branch->branch_name }}">
                                                {{ $branch->branch_name }}
                                            </option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                </div>                        
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="hidden" name="initiate_from" value="Project Page - Build Process">
                                        <button data-id=""class="btn btn-secondary update-build-process">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="modal fade" id="magento-error-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Pr Activitiy Body</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <textarea id="magento-error-body-text" class="form-control" name="reply"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        $('.select2').select2();

        $(document).on("click", ".error-text-modal", function(e) {
            e.preventDefault();
            var $this = $(this);
            $("#magento-error-body-text").val($this.data("message"));
            $("#magento-error-modal").modal("show");
        });

        
        $(document).ready(function() {
                $(document).on("click",".open-build-process-template",function(e) {
                e.preventDefault();
                var id=$(this).attr("data-id");
                var repo=$(this).attr("data-repo");

                $("#build_organization").val(id);
                $("#build_organization").trigger("change");

                $("#build_repository").val(repo);
                $("#build_repository").trigger("change");

                $('#build-process-modal').modal('show'); 
            });
        });

    $(document).on('submit', 'form#build-process', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("build-process"));
        var button = $(this).find('[type="submit"]');
        $.ajax({
            url: '{{ route("project.buildProcess") }}',
            type: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                $("#loading-image-preview").show();
            },
            complete: function() {
                $("#loading-image-preview").hide();
            },
            success: function(response) {
                if(response.code=='200'){
                    toastr["success"](response.message);
                    $('#build-process-modal').modal('hide');
                }else{
                    toastr["error"](response.message);
                }
                $("#loading-image-preview").hide();
            },
            error: function(xhr, status, error) { // if error occured
                $("#loading-image-preview").hide();
            },
        });
    });
    </script>
@endsection