@extends('layouts.app')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endsection


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Git Actions({{ $gitactions->total() }})</h2>
        </div>
    </div>
    <div class="mt-3 col-md-12">
        <form action="{{ route('git-action-lists') }}" method="get" class="search">
            @csrf
            <div class="col-1">
                <b>Search</b>
            </div>
            <div class="col-md-2 pd-sm">
                <select name="api_url" id="api_url" class="form-control globalSelect" data-placeholder="Sort By">
                    <option  Value="">Select Api Url</option>
                    @foreach ($apiUrls as $apiUrl)
                    <option  Value="{{$apiUrl}}"  {{ (request('api_url') == $apiUrl ? "selected" : "") }} >{{$apiUrl}}</option>
                    @endforeach
                </select>        
            </div>
            <div class="col-md-2 pd-sm">
                <select name="ref_url" id="ref_url" class="form-control globalSelect" data-placeholder="Sort By">
                    <option  Value="">Select Ref Url</option>
                    @foreach ($refUrls as $refUrl)
                    <option  Value="{{$refUrl}}"  {{ (request('ref_url') == $refUrl ? "selected" : "") }} >{{$refUrl}}</option>
                    @endforeach
                </select>        
            </div>
            <div class="col-md-2 pd-sm">
                <select name="repo" id="repo" class="form-control globalSelect" data-placeholder="Sort By">
                    <option  Value="">Select Repos</option>
                    @foreach ($repos as $repo)
                    <option  Value="{{$repo}}"  {{ (request('repo') == $repo ? "selected" : "") }} >{{$repo}}</option>
                    @endforeach
                </select>        
            </div>
            <div class="col-lg-2">
                <input class="form-control" type="text" id="search_actor" placeholder="Search git Actor" name="search_actor"
                value="{{ request('search_actor') ?? '' }}">
            </div>
            <div class="col-lg-2">
                <input class="form-control" type="text" id="search_event" placeholder="Search Event name" name="search_event"
                    value="{{ request('search_event') ?? '' }}">
            </div>
            <div class="col-lg-2">
                <input class="form-control" type="text" id="search_job" placeholder="Search Job" name="search_job"
                    value="{{ request('search_job') ?? '' }}">
            </div>
            <div class="col-lg-2"><br>
                <input class="form-control" type="text" id="search_ref_name" placeholder="Search Git Ref Name" name="search_ref_name"
                    value="{{ request('search_ref_name') ?? '' }}">
            </div>
            <div class="col-lg-2"><br>
                <input class="form-control" type="text" id="search_ref_type" placeholder="Search type" name="search_ref_type"
                value="{{ request('search_ref_type') ?? '' }}">
            </div>
            <div class="col-lg-2"><br>
                <input class="form-control" type="text" id="search_runner" placeholder="Search Runner" name="search_runner"
                value="{{ request('search_runner') ?? '' }}">
            </div>
            <div class="col-lg-2"><br>
                <input class="form-control" type="date" name="date" value="{{ request('date') ?? '' }}">
            </div>

            <div class="col-lg-2"><br>
                <button type="submit" class="btn btn-image search"
                    onclick="document.getElementById('download').value = 1;">
                    <img src="{{ asset('images/search.png') }}" alt="Search">
                </button>
                <a href="{{ route('git-action-lists') }}" class="btn btn-image" id=""><img
                        src="/images/resend2.png" style="cursor: nwse-resize;"></a>
            </div>
        </form>
    </div>
    <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped" id="log-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Github Actor</th>
                    <th>Github Api Url</th>
                    <th>Github Base Ref</th>
                    <th>Event Name</th>
                    <th>Github Job</th>
                    <th>Github Ref</th>
                    <th>Github Ref Name</th>
                    <th>Github Ref Type</th>
                    <th>Githubg Repository</th>
                    <th>Run attempt</th>
                    <th>Github Work Flow</th>
                    <th>Runner Name</th>
                    <th>Created At</th>
                </tr>
            <tbody>
                @foreach ($gitactions as $gitaction)
                    <tr>
                        <td>{{ $gitaction->id }}</td>
                        <td>{{ $gitaction->github_actor }}</td>
                        <td style="cursor:pointer;" id="reply_text" class="expand-row change-reply-text"
                            data-id="{{ $gitaction->id }}" data-message="{{ $gitaction->github_api_url }}">
                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                <div class="td-mini-container">
                                    {!! strlen($gitaction->github_api_url) > 20
                                        ? substr($gitaction->github_api_url, 0, 20) . '...'
                                        : $gitaction->github_api_url !!}
                                </div>
                                <div class="td-full-container hidden">
                                    {{ $gitaction->github_api_url }}
                                </div>
                            </div>
                        </td>
                        <td>{{ $gitaction->github_base_ref }}</td>
                        <td>{{ $gitaction->github_event_name }}</td>
                        <td>{{ $gitaction->github_job }}</td>
                        <td style="cursor:pointer;" id="reply_text" class="expand-row change-reply-text"
                            data-id="{{ $gitaction->id }}" data-message="{{ $gitaction->github_ref }}">
                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                <div class="td-mini-container">
                                    {!! strlen($gitaction->github_ref) > 20 ? substr($gitaction->github_ref, 0, 20) . '...' : $gitaction->github_ref !!}
                                </div>
                                <div class="td-full-container hidden">
                                    {{ $gitaction->github_ref }}
                                </div>
                            </div>
                        </td>
                        <td>{{ $gitaction->github_ref_name }}</td>
                        <td>{{ $gitaction->github_ref_type }}</td>
                        <td style="cursor:pointer;" id="reply_text" class="expand-row change-reply-text"
                            data-id="{{ $gitaction->id }}" data-message="{{ $gitaction->github_repository }}">
                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                <div class="td-mini-container">
                                    {!! strlen($gitaction->github_repository) > 20
                                        ? substr($gitaction->github_repository, 0, 20) . '...'
                                        : $gitaction->github_repository !!}
                                </div>
                                <div class="td-full-container hidden">
                                    {{ $gitaction->github_repository }}
                                </div>
                            </div>
                        </td>
                        <td>{{ $gitaction->github_run_attempt }}</td>
                        <td>{{ $gitaction->github_workflow }}</td>
                        <td>{{ $gitaction->runner_name }}</td>
                        <td>{{ $gitaction->created_at->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </tbody>
            </thead>
        </table>
        {!! $gitactions->appends(Request::except('page'))->links() !!}
    </div>
    <div id="loading-image"
        style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
    </div>
    <div class="modal fade" id="git-action-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Github Actions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <textarea id="git-action-reply-text-reply" class="form-control" name="reply"></textarea>
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
        $(document).on('click', '.show-logs-icon', function() {
            var id = $(this).gitaction('id');
            $.ajax({
                url: '{{ route('website.error.show') }}',
                method: 'GET',
                gitaction: {
                    id: id
                },
                success: function(response) {
                    $('#reply_logs_modal').modal('show');
                    $('#reply_logs_div').html(response);
                },
                error: function(xhr, status, error) {
                    alert("Error occured.please try again");
                }
            });
        });

        $(document).on("click", ".change-reply-text", function(e) {
            e.preventDefault();
            var $this = $(this);
            $("#git-action-reply-text-reply").val($this.data("message"));
            $("#git-action-modal").modal("show");
        });
    </script>
@endsection
