@extends('layouts.app')

@section('title', 'Magento Command')

@section('styles')
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <style>
        .multiselect {
            width: 100%;
        }

        .multiselect-container li a {
            line-height: 3;
        }

        /* Pagination style */
        .pagination>li>a,
        .pagination>li>span {
            color: #343a40 !important // use your own color here
        }

        .pagination>.active>a,
        .pagination>.active>a:focus,
        .pagination>.active>a:hover,
        .pagination>.active>span,
        .pagination>.active>span:focus,
        .pagination>.active>span:hover {
            background-color: #343a40 !important;
            border-color: #343a40 !important;
            color: white !important
        }

        .select2-search--inline {
            display: contents;
            /*this will make the container disappear, making the child the one who sets the width of the element*/
        }

        .select2-search__field:placeholder-shown {
            width: 100% !important;
            /*makes the placeholder to be 100% of the width while there are no options selected*/
        }
    </style>

@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <h2 class="page-heading">Magento Commands</h2>
        </div>

        <div class="col-12 mb-3">
            <div class="pull-left">
            </div>
            <div class="pull-right">
                <!-- <a title="add new domain" class="btn btn-secondary add-new-btn">+</a> -->
            </div>
        </div>
    </div>
    <div class=" row ">
        <form class="form-inline" action="/magento/command/search" method="GET">
            <div class="col">
                <div class="form-group">
                    <div class="input-group">
                        <select name="website[]" class="form-control select2" data-placeholder="Select Websites"
                            id="website" multiple>
                            <option></option>
                            <option value="ERP" @if (!empty(request('website')) && in_array('ERP', request('website'))) selected @endif>ERP</option>
                            @php
                                $ops = 'id';
                            @endphp
                            @foreach ($websites as $website)
                                <option @if (!empty(request('website')) && in_array($website->id, request('website'))) selected @endif value="{{ $website->id }}">
                                    {{ $website->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <div class="input-group">
                        <select name="command_name[]" class="form-control select2" id="command_name" multiple
                            data-placeholder="Select Command Name">
                            <option></option>
                            @foreach ($magentoCommandListArray as $comName => $comType)
                                <option @if (!empty(request('command_name')) && in_array($comName, request('command_name'))) selected @endif value="{{ $comName }}">
                                    {{ $comName }}</option>
                            @endforeach
                        </select>

                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <div class="input-group">
                        <select name="user_id[]" class="form-control select2" id="user_id" multiple
                            data-placeholder="Select User Name">
                            <option></option>
                            @foreach ($users as $key => $user)
                                <option @if (!empty(request('user_id')) && in_array($user->id, request('user_id'))) selected @endif value="{{ $user->id }}">
                                    {{ $user->name }}</option>
                            @endforeach
                        </select>
                        {{-- <input type="text" placeholder="Request Name" class="form-control" name="request_name" value="{{request('request_name')}}"> --}}
                    </div>
                </div>
            </div>
            <div class="col">
                <button type="submit" class="btn btn-image"><img src="/images/filter.png"></button>
                <a href="/magento/command" class="btn btn-image" id=""><img src="/images/resend2.png"
                        style="cursor: nwse-resize;"></a>
            </div>
        </form>
        <button type="button" class="btn custom-button float-right mr-3 openmodeladdpostman" data-toggle="modal"
            data-target="#addPostman">Add Command</button>
        <button type="button" class="btn custom-button float-right mr-3" data-toggle="modal"
            data-target="#rcmw_runCommand">Run Command</button>
        <button type="button" class="btn custom-button float-right mr-3" data-toggle="modal"
            data-target="#run_ms_query">Run MySql Query</button>
        <a target="_blank" href="/magento/command/run-mysql-command-logs" class="btn custom-button float-right mr-3">MySql
            Query Logs</a>
        <a target="_blank" href="/magento/command/run-mulitiple-command-logs"
            class="btn custom-button float-right mr-3">View Mulitple Command Lists</a>

    </div>

    @php $isPermissionCommandRun = 0; @endphp

    @if (auth()->user()->isAdmin())
        @php $isPermissionCommandRun = 1; @endphp

        <div class="col-12">
            <h3>Assign Permission to User</h3>
            <form class="form-inline" id="update_user_permission" action="/magento/command/permission/user" method="POST">
                @csrf
                <div class="form-group">
                    <div class="input-group">
                        <select name="persmission_website" class="form-control" id="persmission_website" required>
                            <option value="">--select website for Permission--</option>
                            @foreach ($websites as $website)
                                <option @if ($website->id == request('website')) selected @endif value="{{ $website->id }}">
                                    {{ $website->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div> &nbsp;&nbsp;&nbsp;
                <div class="form-group">
                    <div class="input-group">
                        <select name="persmission_user" class="form-control" id="persmission_user" required>
                            <option value="">--select user for Permission--</option>
                            @foreach ($users as $key => $user)
                                <option @if ($user->id == request('user_id')) selected @endif value="{{ $user->id }}">
                                    {{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div> &nbsp;&nbsp;
                <button type="submit" class="btn custom-button update-userpermission">Update User Permission</button>
            </form>
        </div>
    @endif

    </br>
    <div class="row m-0">
        <div class="col-12" style="border: 1px solid;border-color: #dddddd;">
            <div class="table-responsive mt-2" style="overflow-x: auto !important;">
                <table class="table table-bordered text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 3%;">ID</th>
                            <th style="width: 5%;overflow-wrap: anywhere;">User Name</th>
                            <th style="width: 22%;overflow-wrap: anywhere;">Websites</th>
                            <th style="width: 22%;overflow-wrap: anywhere;">Command name</th>
                            <th style="width: 22%;overflow-wrap: anywhere;">Command</th>
                            <th style="width: 22%;overflow-wrap: anywhere;">Created Date</th>
                            <th style="width: 10%;overflow-wrap: anywhere;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($magentoCommand as $key => $magentoCom)
                            <tr>
                                <td>{{ $magentoCom->id }}</td>
                                <td class="expand-row-msg" data-name="userName" data-id="{{ $magentoCom->id }}">
                                    @if ($magentoCom->user)
                                        <span
                                            class="show-short-userName-{{ $magentoCom->id }}">{{ Str::limit($magentoCom->user->name, 5, '..') }}</span>
                                        <span style="word-break:break-all;"
                                            class="show-full-userName-{{ $magentoCom->id }} hidden">{{ $magentoCom->user->name }}</span>
                                    @else
                                        <span class="show-short-userName-{{ $magentoCom->id }}">-NA-</span>
                                    @endif
                                </td>
                                <td class="expand-row-msg" data-name="websites" data-id="{{ $magentoCom->id }}">
                                    @if ($magentoCom->website_ids == 'ERP')
                                        <span class="show-short-websites-{{ $magentoCom->id }}">ERP</span>
                                        <span style="word-break:break-all;"
                                            class="show-full-websites-{{ $magentoCom->id }} hidden">ERP</span>
                                    @else
                                        <span class="show-short-websites-{{ $magentoCom->id }}">
                                            @if ($magentoCom->website)
                                                {{ Str::limit($magentoCom->website->title, 20, '..') }}
                                            @endif
                                        </span>
                                        <span style="word-break:break-all;"
                                            class="show-full-websites-{{ $magentoCom->id }} hidden">
                                            @if ($magentoCom->website)
                                                {{ $magentoCom->website->title }}
                                            @endif
                                        </span>
                                    @endif
                                </td>
                                <td class="expand-row-msg" data-name="command_name" data-id="{{ $magentoCom->id }}">
                                    <span
                                        class="show-short-command_name-{{ $magentoCom->id }}">{{ Str::limit($magentoCom->command_name, 20, '..') }}</span>
                                    <span style="word-break:break-all;"
                                        class="show-full-command_name-{{ $magentoCom->id }} hidden">{{ $magentoCom->command_name }}</span>
                                </td>
                                <td>{{ $magentoCom->command_type }}</td>
                                <td>{{ $magentoCom->created_at }}</td>
                                <td>
                                    @php
                                        $isPerCommandRunCheck = $isPermissionCommandRun;

                                        if ($isPerCommandRunCheck == 0 && !empty($magentoCom->user_permission)) {
                                            $userPermissions = explode(',', $magentoCom->user_permission);

                                            if (in_array(auth()->user()->id, $userPermissions)) {
                                                $isPerCommandRunCheck = 1;
                                            }
                                        }
                                    @endphp

                                    @if ($isPerCommandRunCheck == 1)
                                        <a title="Run Command" class="btn btn-image magentoCom-run-btn pd-5 btn-ht"
                                            data-id="{{ $magentoCom->id }}" href="javascript:;">
                                            <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                        </a>
                                    @endif

                                    <a class="btn btn-image edit-magentoCom-btn" data-id="{{ $magentoCom->id }}"><img
                                            data-id="{{ $magentoCom->id }}" src="/images/edit.png"
                                            style="cursor: nwse-resize; width: 16px;"></a>
                                    <a class="btn delete-magentoCom-btn" data-id="{{ $magentoCom->id }}"
                                        href="#"><img data-id="{{ $magentoCom->id }}" src="/images/delete.png"
                                            style="cursor: nwse-resize; width: 16px;"></a>
                                    <a title="Preview Response" data-id="{{ $magentoCom->id }}"
                                        class="btn btn-image preview_response pd-5 btn-ht" href="javascript:;"><i
                                            class="fa fa-product-hunt" aria-hidden="true"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="text-center">
                    {!! $magentoCommand->appends(Request::except('page'))->links() !!}
                </div>
            </div>
        </div>
        <div id="loading-image"
            style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
        </div>
    </div>
@endsection
<div id="commandHistory" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content ">
            <div id="add-mail-content">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Command History</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                <tr>
                                    <th style="width: 3%;">ID</th>
                                    <th style="width: 5%;overflow-wrap: anywhere;">User Name</th>
                                    <th style="width: 5%;overflow-wrap: anywhere;">Websites</th>
                                    <th style="width: 4%;overflow-wrap: anywhere;">Command name</th>
                                    <th style="width: 5%;overflow-wrap: anywhere;">Response</th>
                                    <th style="width: 22%;overflow-wrap: anywhere;">Action</th>
                                </tr>
                                </tr>
                            </thead>
                            <tbody class="tbodaycommandHistory">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="commandResponseHistoryModel" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 100%;width: 90% !important;">
        <!-- Modal content-->
        <div class="modal-content ">
            <div id="add-mail-content">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Command Response History</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 3%;">ID</th>
                                    <th style="width: 5%;overflow-wrap: anywhere;">User Name</th>
                                    <th style="width: 5%;overflow-wrap: anywhere;">Command Name</th>
                                    <th style="width: 5%;overflow-wrap: anywhere;">Status</th>
                                    <th style="width: 5%;overflow-wrap: anywhere;">Response</th>
                                    <th style="width: 5%;overflow-wrap: anywhere;">Request</th>
                                    <th style="width: 5%;overflow-wrap: anywhere;">Job ID</th>
                                    <th style="width: 4%;overflow-wrap: anywhere;">Date</th>
                                </tr>
                            </thead>
                            <tbody class="tbodayCommandResponseHistory">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="addPostman" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content ">
            <div id="add-mail-content">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><span id="titleUpdate">Add</span> Command</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="magentoForm" method="post">
                            @csrf

                            <div class="form-row">
                                <input type="hidden" id="command_id" name="id" value="" />

                                @if (auth()->user()->isAdmin())
                                    <div class="form-group col-md-12">
                                        <label for="title">User Name</label>
                                        <select name="user_permission[]" multiple class="form-control dropdown-mul-1"
                                            style="width: 100%" id="user_permission" required>
                                            <option>--Users--</option>
                                            @foreach ($users as $key => $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <div class="form-group col-md-12">
                                    <label for="title">Website</label>
                                    <div class="dropdown-sin-1">
                                        @php $websites = \App\StoreWebsite::get(); @endphp
                                        <select name="websites_ids[]" class="websites_ids form-control dropdown-mul-1"
                                            style="width: 100%;" id="websites_ids" required>
                                            <option>--Website--</option>
                                            <option value="ERP">ERP</option>

                                            @foreach ($websites as $website)
                                                <option value="{{ $website->id }}"
                                                    data-website="{{ $website->website }}">
                                                    {{ $website->title }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="assets_manager_id">Assets Manager <span
                                            id="am-client-id"></span></label>
                                    <div class="dropdown-sin-1">
                                        <select name="assets_manager_id"
                                            class="assets_manager_id form-control dropdown-mul-1" style="width: 100%;"
                                            id="assets_manager_id" required>
                                            <option value="">--Assets Manager--</option>

                                            @foreach ($assetsmanager as $am)
                                                <option value="{{ $am->id }}"
                                                    data-client_id="{{ $am->client_id }}">
                                                    {{ $am->name }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="command_name">Command Name</label>
                                    {{-- <input type="text" name="command_name" value="" class="form-control" id="command_name_search" placeholder="Enter Command name"> --}}
                                    <select name="command_name" class="form-control" id="command_name_search"
                                        style="width: 100%" required>
                                        <option value="">--Select Command Name--</option>
                                        @foreach ($magentoCommandListArray as $comName => $comType)
                                            <option @if ($comName == request('command_name')) selected @endif
                                                value="{{ $comName }}">{{ $comName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="command_type">Command</label>
                                    {{-- <input type="text" name="command_type" value="" class="form-control" id="command_type" placeholder="Enter request type"> --}}
                                    <select name="command_type" class="form-control" id="command_type"
                                        style="width: 100%" required>
                                        <option value="">--Select Command Name--</option>
                                        @foreach ($magentoCommandListArray as $comName => $comType)
                                            <option @if ($comType == request('command_type')) selected @endif
                                                value="{{ $comType }}">{{ $comType }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="working_directory">Working Directory</label>
                                    <input type="text" name="working_directory" value=""
                                        class="form-control" id="working_directory"
                                        placeholder="Enter the working directory" required>

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-secondary submit-form">Save</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div id="rcmw_runCommand" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content ">
            <div id="add-mail-content">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Run Command</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="title">Websites</label>
                                <div class="dropdown-sin-1">
                                    @php $websites = \App\StoreWebsite::get(); @endphp
                                    <select name="rcmw_websites_ids[]"
                                        class="rcmw_websites_ids form-control dropdown-mul-1" style="width: 100%;"
                                        id="rcmw_websites_ids" multiple>
                                        <option>--Websites--</option>
                                        <option value="ERP">ERP</option>
                                        @foreach ($websites as $website)
                                            <option value="{{ $website->id }}"
                                                data-website="{{ $website->website }}">
                                                {{ $website->title }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="rcmw_command_id">Command</label>
                                <select name="rcmw_command_id" class="form-control" id="rcmw_command_id"
                                    style="width: 100%" required>
                                    <option value="">--Select Command--</option>
                                    @foreach ($allMagentoCommandListArray as $id => $comType)
                                        <option value="{{ $id }}">{{ $comType }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="rcmw-run-command-btn" class="btn btn-secondary">Run
                            Command</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="run_ms_query" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content ">
            <div id="add-mail-content">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Run MySql Query</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="title">Websites</label>
                                <div class="dropdown-sin-1">
                                    @php $websites = \App\StoreWebsite::get(); @endphp
                                    <select name="run_msq_websites_ids[]"
                                        class="run_msq_websites_ids form-control dropdown-mul-1" style="width: 100%;"
                                        id="run_msq_websites_ids" multiple>
                                        <option>--Websites--</option>
                                        <option value="ERP">ERP</option>
                                        @foreach ($websites as $website)
                                            <option value="{{ $website->id }}"
                                                data-website="{{ $website->website }}">
                                                {{ $website->title }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="rcmw_command_id">MySql Query</label>
                                <input type="text" id="run_msq_command" class="form-control"
                                    name="run_msq_command" value="">
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="run_msq_command-btn" class="btn btn-secondary">Run</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.dropdown.min.css') }}">
{{-- <link rel="stylesheet" type="text/css" href="{{asset('css/jquery.dropdown.css')}}"> --}}
@section('scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-multiselect.min.js') }} "></script>

    <script type="text/javascript" src="{{ mix('webpack-dist/js/mock.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.dropdown.min.js') }} "></script>
    {{-- <script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.dropdown.js') }} "></script> --}}


    <script>
        var Random = Mock.Random;
        var json1 = Mock.mock({
            "data|10-50": [{
                name: function() {
                    //return Random.name(true)
                },
                "id|+1": 1,
                "disabled|1-2": true,
                groupName: 'Group Name',
                "groupId|1-4": 1,
                "selected": true
            }]
        });
        //   $('.dropdown-mul-1').dropdown({
        //   //data: json1.data,
        //   limitCount: 40,
        //   multipleMode: 'label',
        //   choice: function () {
        //     // console.log(arguments,this);
        //   }
        // });

        //  $('.dropdown-sin-11').dropdown({
        //   readOnly: true,
        //   input: '<input type="text" maxLength="25" placeholder="Search">'
        // });
    </script>
    </div>

    <script type="text/javascript">
        // $('ul.pagination').hide();
        //   $('.infinite-scroll').jscroll({
        //     autoTrigger: true,
        //     // debug: true,
        //     loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
        //     padding: 0,
        //     nextSelector: '.pagination li.active + li a',
        //     contentSelector: 'div.infinite-scroll',
        //     callback: function () {
        //       $('ul.pagination').first().remove();
        //       $('ul.pagination').hide();
        //     }
        // });

        $('.multiselect').multiselect({
            enableClickableOptGroups: true
        });
        $(document).on("click", ".openmodeladdpostman", function(e) {
            $('#titleUpdate').html("Add");
            $('#postmanform').find("input[type=text], textarea").val("");
        });

        $(document).on("click", "#see_users", function(e) {
            e.preventDefault();
            //debugger;
            var $this = $(this);
            var id = $this.data('user_details');
            $('.postmanUserDetailsModelBody').html(id);
        });

        $(document).on("change", ".assets_manager_id", function(e) {
            e.preventDefault();
            var clientID = $(this).find(':selected').attr('data-client_id');
            if (clientID != "" && clientID !== undefined) {
                $('#am-client-id').html("").html("(Client ID: " + clientID + ")");
            } else {
                $('#am-client-id').html("").html("(Client ID: NO CLIENT ID)");
            }
            //debugger;
        });

        $(document).on("change", ".folder_name", function(e) {
            e.preventDefault();
            var folder_name = $(this).find(':selected').attr('data-folder_name');
            //debugger;
            $('#folder_real_name').val(folder_name);
        });

        $(document).on("click", ".delete-magentoCom-btn", function(e) {
            e.preventDefault();
            if (confirm("Are you sure?")) {
                var $this = $(this);
                var id = $this.data('id');
                $.ajax({
                    url: "/magento/command/delete",
                    type: "delete",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: id
                    },
                    beforeSend: function() {
                        $('#loading-image').show();
                    },
                }).done(function(response) {
                    if (response.code = '200') {
                        toastr['success']('Command deleted successfully!!!', 'success');
                        location.reload();
                    } else {
                        toastr['error'](response.message, 'error');
                    }
                }).fail(function(errObj) {
                    $('#loading-image').hide();
                    $("#addPostman").hide();
                    toastr['error'](errObj.message, 'error');
                });
            }
        });
        $(document).on("click", ".submit-form", function(e) {
            e.preventDefault();
            var $this = $(this);

            <?php if (auth()->user()->isAdmin()) { ?>
            if ($("#user_permission").val().length == 0) {
                toastr['error']('Please Select User Permission', 'error');
                return '';
            }
            <?php } ?>

            if ($("#websites_ids").val() == '--Website--') {
                toastr['error']('Please Select Website', 'error');
                return '';
            }
            if ($("#websites_ids").val() == '--Website--') {
                toastr['error']('Please Select Website', 'error');
                return '';
            }
            if ($("#assets_manager_id").val() == '') {
                toastr['error']('Please Select Assets Manager', 'error');
                return '';
            }
            if ($("#command_name_search").val() == '') {
                toastr['error']('Please Select Command Name', 'error');
                return '';
            }
            if ($("#command_type").val() == '') {
                toastr['error']('Please Select Command', 'error');
                return '';
            }
            if ($("#working_directory").val() == '') {
                toastr['error']('Please Enter Command Working Directory', 'error');
                return '';
            }

            if ($('#titleUpdate').text() == 'Add')
                $("#command_id").val("");
            $.ajax({
                url: "/magento/command/add",
                type: "post",
                data: $('#magentoForm').serialize(),
                beforeSend: function() {
                    $('#loading-image').show();
                },
            }).done(function(response) {
                if (response.code == '200') {
                    $('#loading-image').hide();
                    $('#addCommand').modal('hide');
                    toastr['success']('Command added successfully!!!', 'success');
                    location.reload();
                } else {
                    toastr['error'](response.message, 'error');
                }
            }).fail(function(errObj) {
                $('#loading-image').hide();
                //$("#addMail").hide();
                toastr['error'](errObj.message, 'error');
            });
        });

        $(document).on("click", ".edit-magentoCom-btn", function(e) {
            e.preventDefault();
            $('#titleUpdate').html("Update");
            var $this = $(this);
            var id = $this.data('id');
            $.ajax({
                url: "/magento/command/edit",
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id
                },
                beforeSend: function() {
                    $('#loading-image').show();
                },
            }).done(function(response) {
                if (response.code = '200') {
                    form = $('#magentoForm');
                    $.each(response.data, function(key, v) {

                        if (key == "website_ids") {
                            var Values = new Array();
                            if (v !== null && v !== undefined) {
                                $.each(v.split(","), function(i, e) {
                                    console.log(e);
                                    //$(".websites_ids option[value='" + e + "']").prop("selected", true);
                                    Values.push(e);
                                });
                            }
                            $('.websites_ids').val(Values).trigger('change');
                        }
                        if (form.find('[name="' + key + '"]').length) {
                            form.find('[name="' + key + '"]').val(v);
                            if (key == 'command_name') {
                                $('#command_name_search').val(v).trigger('change');
                            }
                            if (key == 'command_type') {
                                $('#command_type').val(v).trigger('change');
                            }
                            if (key == 'assets_manager_id') {
                                $('#assets_manager_id').val(v).trigger('change');
                            }

                        } else if (form.find('[name="' + key + '[]"]').length) {
                            //form.find('[name="'+key+'[]"]').val(response.ops);
                            //debugger;


                            // $.each(v.split(","), function(i, e) {
                            //     console.log(e);
                            //     // $("#websites_ids option[value='" + e + "']").prop("selected", true);
                            // });
                        }

                        <?php if (auth()->user()->isAdmin()) { ?>
                        if (key == "user_permission" && v !== null && v !== undefined) {
                            $('#user_permission').val(v.split(',')).trigger('change');
                        }
                        <?php } ?>
                    });
                    $('#addPostman').modal('show');
                    toastr['success']('Command Listed successfully!!!', 'success');

                } else {
                    toastr['error'](response.message, 'error');
                }
                $('#loading-image').hide();
            }).fail(function(errObj) {
                $('#loading-image').hide();
                $("#addPostman").hide();
                toastr['error'](errObj.message, 'error');
            });
        });

        $(document).on("click", ".magento-history-btn", function(e) {
            e.preventDefault();
            var $this = $(this);
            var id = $this.data('id');
            $.ajax({
                url: "/postman/history/",
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id
                },
                beforeSend: function() {
                    $('#loading-image').show();
                },
            }).done(function(response) {
                if (response.code = '200') {
                    
                    $(".tbodayPostmanHistory").html(response.html);
                    $('#postmanHistory').modal('show');
                    toastr['success']('Command added successfully!!!', 'success');

                } else {
                    toastr['error'](response.message, 'error');
                }
                $('#loading-image').hide();
            }).fail(function(errObj) {
                $('#loading-image').hide();
                $("#postmanHistory").hide();
                toastr['error'](errObj.message, 'error');
            });
        });

        $(document).on("click", "#rcmw-run-command-btn", function(e) {
            e.preventDefault();
            var websites_ids = $("#rcmw_websites_ids").val();
            var command_id = $("#rcmw_command_id").val();
            if (command_id == '') {
                toastr['error']('Please select Command', 'error');
                return;
            }
            if (typeof websites_ids == 'undefined' || websites_ids.length == 0) {
                toastr['error']('Please select Website', 'error');
                return;
            }

            $.ajax({
                url: "/magento/command/run-on-multiple-website",
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    command_id: command_id,
                    websites_ids: websites_ids
                },
                beforeSend: function() {
                    $('#loading-image').show();
                },
            }).done(function(response) {
                if (response.code = '200') {
                    toastr['success'](response.message, 'success');
                } else {
                    toastr['error'](response.message, 'error');
                }
                $('#loading-image').hide();
                $("#rcmw_command_id").val('');
                $("#rcmw_websites_ids").val('').trigger('change')
                $("#rcmw_runCommand").modal('hide');

            }).fail(function(errObj) {
                $('#loading-image').hide();
                if (errObj?.responseJSON?.message) {
                    toastr['error'](errObj.responseJSON.message, 'error');
                    return;
                }
                toastr['error'](errObj.message, 'error');
                $("#rcmw_command_id").val('');
                $("#rcmw_websites_ids").val('').trigger('change')
                $("#rcmw_runCommand").modal('hide');
            });

        });
        $(document).on("click", "#run_msq_command-btn", function(e) {
            e.preventDefault();
            var websites_ids = $("#run_msq_websites_ids").val();
            var command = $("#run_msq_command").val();
            if (command == '') {
                toastr['error']('Please Enter MySql Query', 'error');
                return;
            }
            if (typeof websites_ids == 'undefined' || websites_ids.length == 0) {
                toastr['error']('Please select Website', 'error');
                return;
            }

            $.ajax({
                url: "/magento/command/run-mysql-command",
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    command: command,
                    websites_ids: websites_ids
                },
                beforeSend: function() {
                    $('#loading-image').show();
                },
            }).done(function(response) {
                if (response.code == '200') {
                    toastr['success'](response.message, 'success');
                } else {
                    toastr['error'](response.message, 'error');
                }
                $('#loading-image').hide();
                $("#run_msq_command").val('');
                $("#run_msq_websites_ids").val('').trigger('change')
                $("#run_ms_query").modal('hide');

            }).fail(function(errObj) {
                $('#loading-image').hide();
                if (errObj?.responseJSON?.message) {
                    toastr['error'](errObj.responseJSON.message, 'error');
                    return;
                }
                toastr['error'](errObj.message, 'error');
                $("#run_msq_command").val('');
                $("#run_msq_websites_ids").val('').trigger('change')
                $("#run_ms_query").modal('hide');
            });

        });

        $(document).on("click", ".magentoCom-run-btn", function(e) {
            e.preventDefault();
            var $this = $(this);
            var id = $this.data('id');
            $.ajax({
                url: "/magento/command/run",
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id
                },
                beforeSend: function() {
                    $('#loading-image').show();
                },
            }).done(function(response) {
                if (response.code == '200') {
                    toastr['success']('Command Run successfully!!!', 'success');
                } else {
                    toastr['error'](response.message, 'error');
                }
                $('#loading-image').hide();
            }).fail(function(errObj) {
                $('#loading-image').hide();
                if (errObj?.responseJSON?.message) {
                    toastr['error'](errObj.responseJSON.message, 'error');
                    return;
                }
                toastr['error'](errObj.message, 'error');
            });
        });

        $(document).on("click", ".preview_response", function(e) {
            e.preventDefault();
            var $this = $(this);
            var id = $this.data('id');
            $.ajax({
                url: "/magento/command/history/",
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id
                },
                beforeSend: function() {
                    $('#loading-image').show();
                },
            }).done(function(response) {
                if (response.code = '200') {
                    
                    $(".tbodayCommandResponseHistory").html(response.html);
                    $('#commandResponseHistoryModel').modal('show');
                    toastr['success']('Command response listed successfully!!!', 'success');

                } else {
                    toastr['error'](response.message, 'error');
                }
                $('#loading-image').hide();
            }).fail(function(errObj) {
                $('#loading-image').hide();
                $("#commandResponseHistoryModel").hide();
                toastr['error'](errObj.message, 'error');
            });
        });


        $(document).on('click', '.expand-row-msg', function() {
            var name = $(this).data('name');
            var id = $(this).data('id');
            var full = '.expand-row-msg .show-short-' + name + '-' + id;
            var mini = '.expand-row-msg .show-full-' + name + '-' + id;
            $(full).toggleClass('hidden');
            $(mini).toggleClass('hidden');
        });
        $(document).ready(function() {
            $('.select2').select2({
                // placeholder: "Select a state",

            });
            $("#command_name_search").select2({
                tags: true
            });
            $("#command_type").select2({
                tags: true
            });
            $(".dropdown-mul-1").select2({});
        });

        $('#update_user_permission').submit(function(e) {
            e.preventDefault();
            var persmission_website = $('#persmission_website').val();
            var persmission_user = $('#persmission_user').val();

            if (persmission_website && persmission_user) {
                $.ajax({
                    url: "{{ route('magento.command.user.permission') }}",
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        persmission_website: persmission_website,
                        persmission_user: persmission_user
                    }

                }).done(function(response) {
                    $('#loading-image').hide();
                    if (response.code = '200') {
                        toastr['success'](response.message, 'success');
                        location.reload();
                    } else {
                        toastr['error'](response.message, 'error');
                    }
                }).fail(function(errObj) {
                    $('#loading-image').hide();
                    toastr['error'](errObj.message, 'error');
                });
            } else {
                if (persmission_website.length > 0) {
                    $('#persmission_website').addClass("alert alert-danger");

                    toastr['error']("Please Select Required fileds", 'error');
                } else if (persmission_user.length > 0) {
                    $('#persmission_user').addClass("alert alert-danger");

                    toastr['error']("Please Select Required fileds", 'error');
                }
            }
        });
    </script>
@endsection
