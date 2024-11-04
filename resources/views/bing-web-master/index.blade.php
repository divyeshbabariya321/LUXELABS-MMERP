@extends('layouts.app')

@section('title', 'Bing Web Master')

@section('large_content')

    <div id="accounts" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" style="max-width: 100%;width: 90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Bing Client Apps</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table fixed_header" id="latest-remark-records">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">BING CLIENT ID</th>
                            <th scope="col">BING CLIENT APPLICATION NAME</th>
                            <th scope="col">Action</th>
                        </tr>
                        </thead>
                        <tbody class="show-list-records">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="new_account" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Bing App</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form class="addAccount" method="post" action="{{route('bingwebmaster.account.add')}}">
                        @csrf
                        <input name="bing_client_id" type="text" class="form-control m-3"
                               placeholder="Bing Client ID (required)">
                        <input name="bing_client_secret" type="text" class="form-control m-3"
                               placeholder="Bing Client Secret (required)">
                        <input name="bing_client_key" type="text" class="form-control m-3"
                               placeholder="Bing Client Key">
                        <input name="bing_client_application_name" type="text" class="form-control m-3"
                               placeholder="Bing Client Application Name">
                        <button type="submit" class="btn btn-secondary m-3 float-right">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Bing Web Master</h2>
        </div>
    </div>

    @include('partials.flash_messages')

    <div style="float:right">
        <button class="btn btn-secondary accounts">Show Accounts</button>
        <button class="btn btn-secondary new_account">Add Account</button>
    </div>

    <div class="col-md-12">
        <div id="exTab2">
            <ul class="nav nav-tabs">
                <li class="{{ request('logs_per_page') || request('crawls_per_page') || request('webmaster_logs_per_page') || request('history_per_page')  ? '' : 'active' }}">
                    <a href="#search_analytics" data-toggle="tab">Search Analytics</a>
                </li>
                <li class="{{ request('logs_per_page') ? 'active' : '' }}">
                    <a href="#sites_logs" data-toggle="tab">Sites Logs</a>
                </li>
                <li class="{{ request('crawls_per_page') ? 'active' : '' }}">
                    <a href="#site_crawls" data-toggle="tab">Site crawls</a>
                </li>
                <li class="{{ request('webmaster_logs_per_page') ? 'active' : '' }}">
                    <a href="#webmaster_logs" data-toggle="tab">Auth Logs</a>
                </li>
            </ul>
        </div>
    </div>


    <div class="tab-content">
        <div class="tab-pane {{ request('logs_per_page') || request('crawls_per_page') || request('webmaster_logs_per_page') || request('history_per_page') ? '' : 'active' }}"
             id="search_analytics">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="page-heading">Bing Search Analytics</h2>
                </div>

                <div class="col-12">
                    <div class="pull-left"></div>

                    <div class="pull-right">
                        <div class="form-group">
                            &nbsp;
                        </div>
                    </div>
                </div>
            </div>

            <form method="get" action="{{route('bingwebmaster.index')}}">

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-2">
                            <select class="form-control select-multiple" id="web-select" tabindex="-1"
                                    aria-hidden="true" name="site" onchange="//showStores(this)">
                                <option value="">Select Site</option>
                                @foreach($sites as $site)
                                    @if(isset($request->site) && $site->id==$request->site)
                                        <option value="{{$site->id}}" selected="selected">{{$site->site_url}}</option>
                                    @else
                                        <option value="{{$site->id}}">{{$site->site_url}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input name="start_date" type="date" class="form-control"
                                   value="{{$request->start_date??''}}" placeholder="Start Date" id="search">
                        </div>

                        <div class="col-md-2">
                            <input name="end_date" type="date" class="form-control" value="{{$request->end_date??''}}"
                                   placeholder="End Date" id="search">
                        </div>
                        <div class="col-md-1 d-flex justify-content-between">
                            <button type="submit" class="btn btn-image"><img src="/images/filter.png"></button>
                            <button type="button" onclick="resetForm(this)" class="btn btn-image" id=""><img
                                        src="/images/resend2.png"></button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col-md-12">
                    <table id="table" class="table table-striped table-bordered">
                        <thead>
                        @php
                            $currentQueries = $request->query();
                        @endphp
                        <tr>
                            <th>S.N</th>
                            <th>Site URL</th>
                            <th>Query</th>
                            <th>Page</th>
                            @php
                                $clickType=$ctrType=$positionType=$impressionsType='asc';

                                if(isset($request->clicks) && $request->clicks=='asc'):
                                $clickType='desc';
                                endif;

                                if(isset($request->ctr) && $request->ctr=='asc'):
                                $ctrType='desc';
                                endif;

                                if(isset($request->position) && $request->position=='asc'):
                                $positionType='desc';
                                endif;

                                if(isset($request->impression) && $request->impression=='asc'):
                                $impressionsType='desc';
                                endif;
                                $allQueries=array_merge($currentQueries,['clicks'=>$clickType,]);

                                $ctrURL=$request->fullUrlWithQuery(array_merge($currentQueries,['ctr'=>$ctrType]));

                                $positionURL=$request->fullUrlWithQuery(array_merge($currentQueries,['position'=>$positionType]));

                                $impressionsURL=$request->fullUrlWithQuery(array_merge($currentQueries,['impression'=>$impressionsType]));

                                $clicksURL=$request->fullUrlWithQuery($allQueries);

                            @endphp
                            <th style="text-align:center;">Clicks
                                <a style="color:black;" href="{{$clicksURL}}">
                                    @if($clickType=='asc')

                                        <i class="fa fa-angle-down"></i>
                                    @else
                                        <i class="fa fa-angle-up"></i>
                                    @endif
                                </a>
                            </th>
                            <th style="text-align:center;">Ctr
                                <a style="color:black;" href="{{$ctrURL}}">
                                    @if($ctrType=='asc')

                                        <i class="fa fa-angle-down"></i>
                                    @else
                                        <i class="fa fa-angle-up"></i>
                                    @endif
                                </a>
                            </th>
                            <th style="text-align:center;">Position
                                <a style="color:black;" href="{{$positionURL}}">
                                    @if($positionType=='asc')

                                        <i class="fa fa-angle-down"></i>
                                    @else
                                        <i class="fa fa-angle-up"></i>
                                    @endif
                                </a>
                            </th>
                            <th style="text-align:center;">Impression
                                <a style="color:black;" href="{{$impressionsURL}}">
                                    @if($impressionsType=='asc')
                                        <i class="fa fa-angle-down"></i>
                                    @else
                                        <i class="fa fa-angle-up"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Crawl requests</th>
                            <th>Crawl errors</th>
                            <th>Indexed pages</th>
                            <th>Keywords</th>
                            <th>Pages</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbody style="word-break: break-all">
                        @foreach ($sitesData as $key=> $row )
                            <tr>
                                <td>{{$row->id}}</td>

                                <td>{{$row->site->site_url}}</td>
                                <td>{{$row->query}}</td>
                                <td>{{$row->page}}</td>
                                <td>{{$row->clicks}}</td>
                                <td>{{$row->ctr}}</td>
                                <td>{{$row->position}}</td>
                                <td>{{$row->impression}}</td>
                                <td>{{$row->crawl_requests}}</td>
                                <td>{{$row->crawl_errors}}</td>
                                <td>{{$row->index_pages}}</td>
                                <td>{{$row->keywords}}</td>
                                <td>{{$row->pages}}</td>
                                <td>{{isset($row->date) ? $row->date : date('Y-m-d', strtotime($row->created_at))}}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="12">
                                {{ $sitesData->appends(request()->except("page"))->links() }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane {{ request('crawls_per_page') ? 'active' : '' }}" id="site_crawls">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="page-heading">Site crawls</h2>
                </div>
                <div class="col-12">
                    <div class="pull-left"></div>

                    <div class="pull-right">
                        <div class="form-group">
                            &nbsp;
                        </div>
                    </div>
                </div>
            </div>
            <table id="table" class="table table-striped table-bordered">
                <thead>

                <span><a class="btn btn-secondary pull-right m-2" href="{{route('bingwebmaster.get.records')}}"> Refresh Record</a></span>
                <tr>
                    <th>S.N</th>
                    <th>Site URL</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($getSites as $key=> $site )
                    <tr>
                        <td>{{$site->id}}</td>
                        <td>{{$site->site_url}}</td>
                        <td class="delete_site cursor-pointer" data-id="{{$site->id}}">Delete</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $getSites->links() }}
        </div>
        <div class="tab-pane {{ request('logs_per_page') ? 'active' : '' }}" id="sites_logs">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="page-heading">Sites Logs</h2>
                </div>
                <div class="col-12">
                    <div class="pull-left"></div>

                    <div class="pull-right">
                        <div class="form-group">
                            &nbsp;
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">

                    <table id="table" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>S.N</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Created At</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($logs as $key=> $log )
                            <tr>
                                <td>{{$log->id}}</td>
                                <td>{{$log->log_name}}</td>
                                <td>{{$log->description}}</td>
                                <td>{{$log->created_at}}</td>

                            </tr>

                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $logs->links() }}
            </div>
        </div>

        <div class="tab-pane {{ request('webmaster_logs_per_page') ? 'active' : '' }}" id="webmaster_logs">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="page-heading">Auth Logs</h2>
                </div>
                <div class="col-12">
                    <div class="pull-left"></div>

                    <div class="pull-right">
                        <div class="form-group">
                            &nbsp;
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">

                    <table id="table" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>S.N</th>
                            <th>User Name</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Description</th>
                            <th>Created At</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($webmaster_logs as $key=> $log )
                            <tr>
                                <td>{{$log->id}}</td>
                                <td>{{$log->user_name}}</td>
                                <td>{{$log->name}}</td>
                                <td>{{$log->status}}</td>
                                <td>{{$log->message}}</td>
                                <td>{{$log->created_at}}</td>

                            </tr>

                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $webmaster_logs->links() }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script type="text/javascript">


        function resetForm(selector) {

            $(selector).closest('form').find('input,select').val('');

            $(selector).closest('form').submit();
        }


        $(document).on("click", ".new_account", function (e) {
            $("#new_account").modal("show");
        });

        $(document).on("submit", ".addAccount", function (e) {
            if ($('input[name="bing_client_id"]').val() == '') {
                toastr['error']('Bing Client ID is required', 'Error');
                return false;
            }
            if ($('input[name="bing_client_secret"]').val() == '') {
                toastr['error']('Bing Client Secret is required', 'Error');
                return false;
            }
            if ($('input[name="bing_client_application_name"]').val() == '') {
                toastr['error']('Bing Client Application name is required', 'Error');
                return false;
            }
        });

        $(document).on("click", ".accounts", function (e) {
            var btn = $(this);
            $.ajax({
                url: '/bing-webmaster/get-accounts',
                type: 'GET',
                dataType: 'json',
                beforeSend: function () {
                    btn.prop('disabled', true);
                },
                success: function (result) {
                    console.log(result);
                    if (result.code == 200) {
                        var t = '';
                        $.each(result.data, function (k, v) {
                            t += `<tr><td>` + v.id + `</td>`;
                            t += `<td>` + v.bing_client_id + `</td>`;
                            t += `<td>` + v.bing_client_application_name + `</td>`;
                            t += `<td><span><a href="/bing-webmaster/accounts/connect/${v.id}">Connect</a></span></td></tr>`;
                            t += `<tr class="font-weight-bold"><td colspan="4">Connected Bing Accounts</td></tr>`
                            $.each(v.mails, function (kk, vv) {
                                t += `<tr>`;
                                t += `<td colspan="3">${vv.bing_account}</td>`;
                                t += `<td><a href="/bing-webmaster/accounts/disconnect/${vv.id}">Disconnect</a></td>`;
                                t += `</tr>`;
                            })
                        });
                        if (t == '') {
                            t = '<tr><td colspan="4" class="text-center">No data found</td></tr>';
                        }
                    }
                    $("#accounts").find(".show-list-records").html(t);
                    $("#accounts").modal("show");
                    btn.prop('disabled', false);
                },
                error: function () {
                    btn.prop('disabled', false);
                    toastr['error']('Something went wrong', 'Error');
                }
            });
        });

        $(document).on('click', '.delete_site', function () {
            var id = $(this).data('id');
            var $this = $(this);
            $.ajax({
                method: "POST",
                url: "{{ route('bingwebmaster.delete.site.webmaster') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    id: id
                },
                success: function (response) {
                    if (response.status == true) {
                        $this.closest('tr').remove();
                        toastr.success('Site Deleted Successfully')
                    }
                    if (response.code == 200) {
                        toastr.success(response.message)
                        setTimeout(function () {
                            location.reload();
                        }, 1000);

                    }
                }
            });
        });
    </script>

@endsection

