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

<div class="row">
    <div class="mt-3 col-md-12">
        <form class="form-inline" action="" method="GET" id="magento-command-date-form">
            @csrf
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

                    </div>
                </div>
            </div>
            <div class="col">
                <button type="submit" class="btn btn-image"><img src="{{ asset('images/filter.png') }}"></button>
            </div>
        </form>
    </div>
</div>
@php $isPermissionCommandRun = 0; @endphp

@if (auth()->user()->isAdmin())
    @php $isPermissionCommandRun = 1; @endphp

    <div class="col-12" style="display: none;">
        <h3>Assign Permission to User</h3>
        <form class="form-inline" id="update_user_permission" action="{{ route('magento.command.user.permission') }}"
            method="POST">
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
                                    <a title="Run Command" class="btn btn-image magentoCom-run-btn_header pd-5 btn-ht"
                                        data-id="{{ $magentoCom->id }}" href="javascript:;">
                                        <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                    </a>
                                @endif

                                <a class="btn btn-image edit-magentoCom-btn_header"
                                    data-id="{{ $magentoCom->id }}"><img data-id="{{ $magentoCom->id }}"

                                        src="{{ asset('images/edit.png') }}"
                                        style="cursor: nwse-resize; width: 16px;"></a>
                                <a class="btn delete-magentoCom-btn_header" data-id="{{ $magentoCom->id }}"
                                    href="#"><img data-id="{{ $magentoCom->id }}"
                                        src="{{ asset('images/delete.png') }}"
                                        style="cursor: nwse-resize; width: 16px;"></a>
                                <a title="Preview Response" data-id="{{ $magentoCom->id }}"
                                    class="btn btn-image preview_response_header pd-5 btn-ht" href="javascript:;"><i
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
        style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('{{ asset('images/pre-loader.gif') }}') 50% 50% no-repeat;display:none;">
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
                                                data-website="{{ $website->website }}">{{ $website->title }}</option>
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
                                                data-website="{{ $website->website }}">{{ $website->title }}</option>
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
<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.dropdown.css') }}">


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

</script>


<script type="text/javascript">
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

    $(".delete-magentoCom-btn_header").click(function(e) {
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
                $("#addPostman_header").hide();
                toastr['error'](errObj.message, 'error');
            });
        }
    });
    $(document).ready(function() {
        $(".submit-form").click(function(e) {
                e.preventDefault();
                var $this = $(this);

                @auth
                @if (auth()->user()->isAdmin())
                    if ($("#user_permission").val().length == 0) {
                        toastr['error']('Please Select User Permission', 'error');
                        return;
                    }
                @endif
            @endauth

            if ($("#websites_ids").val() == '--Website--') {
                toastr['error']('Please Select Website', 'error');
                return;
            }
            if ($("#assets_manager_id").val() == '') {
                toastr['error']('Please Select Assets Manager', 'error');
                return;
            }
            if ($("#command_name_search").val() == '') {
                toastr['error']('Please Select Command Name', 'error');
                return;
            }
            if ($("#command_type").val() == '') {
                toastr['error']('Please Select Command', 'error');
                return;
            }

            if ($('#titleUpdate').text() == 'Add') {
                $("#command_id").val("");
            }

            $.ajax({
                url: "{{ route('command.add') }}", // Use Laravel route helper
                type: "POST",
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
                toastr['error'](errObj.message, 'error');
            });
        });
    });

    $(".edit-magentoCom-btn_header").click(function(e) {

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

                    @if (auth()->user()->isAdmin())
                        if (key == "user_permission" && v !== null && v !== undefined) {
                            $('#user_permission').val(v.split(',')).trigger('change');
                        }
                    @endif
                });
                $("#modal-container").load("/addPostman_header", function() {
                    $("#addPostman_header").modal("show");
                });

                toastr['success']('Command Listed successfully!!!', 'success');

            } else {
                toastr['error'](response.message, 'error');
            }
            $('#loading-image').hide();
        }).fail(function(errObj) {
            $('#loading-image').hide();
            $("#addPostman_header").hide();
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
            if (response.code === 200) {
                // Set the pre-rendered HTML from the server
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

    $(document).on("click", ".magentoCom-run-btn_header", function(e) {
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

    $(document).on("click", ".preview_response_header", function(e) {
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
            if (response.code === 200) {

                $(".tbodayCommandResponseHistory").html(response.html);
                $("#modal-container").load("/commandResponseHistoryModelHeader", function() {
                    $("#commandResponseHistoryModelHeader").modal("show");
                });
                toastr['success']('Command response listed successfully!!!', 'success');
            } else {
                toastr['error'](response.message, 'error');
            }
            $('#loading-image').hide();
        }).fail(function(errObj) {
            $('#loading-image').hide();
            $("#commandResponseHistoryModelHeader").hide();
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
        $('#magento-commands-modal .select2,#addPostman_header .select2').select2({
            // placeholder: "Select a state",

        });
        $("#magento-commands-modal #command_name_search,#addPostman_header #command_name_search").select2({
            tags: true
        });
        $("#magento-commands-modal #command_type,#addPostman_header #command_type").select2({
            tags: true
        });
        $("#magento-commands-modal .dropdown-mul-1,#addPostman_header .dropdown-mul-1").select2({});


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
