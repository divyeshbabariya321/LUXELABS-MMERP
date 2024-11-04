@extends('layouts.app')

@section('link-css')
@endsection


@section('content')
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <h2 class="page-heading">Time Doctor task creation logs</h2>
                <div class="pull-left">
                    <div class="form-group">
                        <div class="row">
                            <form class="form-inline message-search-handler" style="align-items: flex-start" method="get">
                                <div class="form-group m-1">
                                    <input name="url" id="search_url" type="text" class="form-control" placeholder="Url" value="">
                                </div>
                                <div class="form-group m-1 multi-select-box">
                                    <select name="response_code[]" id="response_code" class="form-control">
                                        @isset($responseCode)
                                            @foreach ($responseCode as $code)
                                                <option value="{{$code}}">{{$code}}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                                <div class="form-group m-1 multi-select-box">
                                    <select name="users[]" id="search_users" class="form-control">
                                        @isset($filterUsers)
                                            @foreach ($filterUsers as $key => $user)
                                                <option value="{{$key}}">{{$user}}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                                <div class="form-group m-1 multi-select-box">
                                    <select name="tasks[]" id="search_tasks" class="form-control">\
                                        @if (isset($generalTask) && $generalTask->count() > 0)
                                            <optgroup label="Select Task">
                                                @foreach ($generalTask as $task)
                                                    <option value="TASK-{{$task}}">#TASK-{{$task}}</option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                        @if (isset($developerTask) && $developerTask->count() > 0)
                                            <optgroup label="Select Task">
                                                @foreach ($developerTask as $task)
                                                    <option value="DEVTASK-{{$task}}">#DEVTASK-{{$task}}</option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group mt-2">
                                    <button type="button" style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
                                        <img src="/images/search.png" style="cursor: default;" onclick="searchHandler()">
                                    </button>
                                    <a href="/google-drive-screencast" class="btn btn-image" id="resetFilter">
                                        <img src="/images/resend2.png" style="cursor: nwse-resize;">
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.flash_messages', ['withMessageButton' => true])
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('status') }}
        </div>
    @endif
    <div class="table-responsive">
        <table class="table table-bordered" id="task-creation-logs-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>URL</th>
                    <th>Response Code</th>
                    <th>User</th>
                    <th>Task</th>
                    <th>Payload</th>
                    <th>Response</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="container-fluid" id="task-log-pagination"></div>

    <div id="loading-image"
        style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
      50% 50% no-repeat;display:none;">
    </div>

    <div id="full-text-model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg"  role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="full-text-heading"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="full-text-body">
                    
                </div>
            </div>
        </div>
    </div>

    <style>
        .select2-search--inline {
            display: contents; 
        }

        .select2-search__field:placeholder-shown {
            width: 100% !important; 
        }
        .multi-select-box .select2{
            width: 200px!important;
        }
        ul.select2-selection__rendered{
            display: block!important;
        }
    </style>
@endsection
@section('scripts')
    <!-- <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script> -->
    <script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.validate.min.js') }} "></script>
    <script type="text/javascript">
        var pagination = 1; 
        $("#response_code").select2({
            multiple: true,
            width: '100%',
            placeholder: "Select Response Code"
        })
        $("#response_code").val(null);
        $("#response_code").trigger("change");
        
        $("#search_users").select2({
            multiple: true,
            width: '100%',
            placeholder: "Select Users"
        })
        $("#search_users").val(null);
        $("#search_users").trigger("change");
        $("#search_tasks").select2({
            multiple: true,
            width: '100%',
            placeholder: "Select Tasks"
        })
        $("#search_tasks").val(null);
        $("#search_tasks").trigger("change");
        $(document).ready(function() {

            submitSearch();

            $(document).on("click", "#task-log-pagination .page-link", function (e) {
                e.preventDefault();
                const url = new URL($(this).attr('href'));
                if(url && url.searchParams) {
                    let urlParams = new URLSearchParams(url.searchParams);
                    let page = urlParams.get("page")
                    if(page && page != '' && page != null) {
                        pagination = page;
                        submitSearch();
                    }
                }
            });

        });

        function submitSearch() {
            src = "{{ route('time-doctor.task_creation_logs.records') }}"
            let search_url = $("#search_url").val();
            let response_code = $("#response_code").val();
            let search_users = $("#search_users").val();
            let search_tasks = $("#search_tasks").val();

            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    search_url,
                    response_code,
                    search_users,
                    search_tasks,
                    page: pagination
                },
                beforeSend: function () {
                    $("#loading-image").show();
                },

            }).done(function (data) {
                $("#loading-image").hide();
                $("#task-creation-logs-table tbody").empty().html(data.tbody);
                $("#task-log-pagination").empty().html(data.pagination);
                // $("#Referral_count").text(data.count);
                // if (data.links.length > 10) {
                    //     $('ul.pagination').replaceWith(data.links);
                    // } else {
                        //     $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                        // }
                        
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                $("#loading-image").hide();
                alert('No response from server');
            });

        }

        function searchHandler() {
            pagination = 1;
            submitSearch()
        }

        function showFullText(heading, el) {
            let text = $(el).closest('td').find('.full-text').html();
            $("#full-text-heading").html(heading);
            $("#full-text-body").html(text)
            $("#full-text-model").modal('show');
        }

        $("#resetFilter").click(function (e) { 
            e.preventDefault();
            $("#search_url").val("");
            $("#response_code").val(null);
            $("#response_code").trigger('change');
            $("#search_users").val(null);
            $("#search_users").trigger('change');
            $("#search_tasks").val(null);
            $("#search_tasks").trigger('change');
            pagination = 1;
            submitSearch();
        });
    </script>
@endsection
