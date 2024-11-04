@extends('layouts.app')
@section('favicon', 'task.png')

@section('title', 'Site Asset')

@section('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
    <style type="text/css">
        .preview-category input.form-control {
            width: auto;
        }

        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }

        .dis-none {
            display: none;
        }

        .pd-5 {
            padding: 3px;
        }

        .toggle.btn {
            min-height: 25px;
        }

        .toggle-group .btn {
            padding: 2px 12px;
        }

        .latest-remarks-list-view tr td {
            padding: 3px !important;
        }

        #latest-remarks-modal .modal-dialog {
            max-width: 1100px;
            width: 100%;
        }

        .btn-secondary {
            border: 1px solid #ddd;
            color: #757575;
            background-color: #fff !important;
        }

        .modal {
            overflow-y: auto;
        }

        body.overflow-hidden {
            overflow: hidden;
        }

        span.user_point_none button,
        span.admin_point_none button {
            pointer-events: none;
            cursor: not-allowed;
        }

        table tr:last-child td {
            border-bottom: 1px solid #ddd !important;
        }

        select.globalSelect2+span.select2 {
            width: calc(100% - 26px) !important;
        }

        table-same-button {
            width: 20px;
            height: 29px;
            border: 1px black solid;
        }

        table {
            display: inline-block;
            overflow-x: auto;
            white-space: nowrap;
            width: 1000px;
        }

        .table-bordered>tbody>tr>td:last-child,
        .table-bordered>tbody>tr>th:last-child,
        .table-bordered>tfoot>tr>td:last-child,
        .table-bordered>tfoot>tr>th:last-child,
        .table-bordered>thead>tr>td:last-child,
        .table-bordered>thead>tr>th:last-child {
            border-right: 1px #dedede solid !important;
        }

        .break-text {
            word-wrap: break-word;
        }

    </style>
@endsection

@section('large_content')

    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>

    <div class="row" id="common-page-layout">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Site Assets</h2>
        </div>
        <br>
        <div class="col-lg-12 margin-tb">
            <form id="filter_data" action="{{ route('site-check-list') }}" method="get">
                <div class="row">
                    <div class="col-md-3">
                        {{ html()->multiselect('store_webs[]', $all_store_websites, $search_website)->class('form-control  globalSelect22')->placeholder('-- All Website --') }}
                    </div>

                    <div class="col-md-3">
                        {{-- <select name="categories[]"  class="form-control globalSelect22" multiple>
                            <option value="">-- Select a categories --</option>
                            @forelse($categories as $ct)
                                <option value="{{ $ct->id }}" @if (in_array($ct->id, $search_category) && is_array($search_category)) selected @endif>
                                    {{ $ct->title }}</option>
                            @empty
                            @endforelse
                        </select> --}}
                        {{ html()->multiselect('categories[]', $categories, $search_category)->class('form-control  globalSelect2') }}
                    </div>

                    <div class="col-md-3">
                        {{ html()->multiselect('site_development_status_id[]', $allStatus, $site_development_status_id)->class('form-control globalSelect2')->placeholder('--- Select Status---') }}
                    </div>

                    <div class="col-md-3">
                        <button type="submit" class="btn btn-secondary">Search</button>
                        <a href="{{ route('site-check-list') }}" class="btn btn-secondary">Reset</a>
                        <button type="button" class="btn btn-secondary download_check_list_data">Download</button>
                    </div>

                   

                </div>
            </form>
        </div>
    </div>
    @if (Session::has('message'))
        {{ Session::get('message') }}
    @endif
    <br />
    <div class="row mt-2">
        <div class="col-md-12 margin-tb infinite-scroll">
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered" id="documents-table">
                        <thead>
                            <tr>
                                <th width="20px">Categories</th>
                                @foreach ($store_websites as $sw)
                                    <th> <span class="break-text">{{ $sw->title }} </span></th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="infinite-scroll-pending-inner">
                            @include(
                                'storewebsite::site-check-list.partials.data'
                            )
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="create-quick-task" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('task.create.task.shortcut') }}" method="post">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h4 class="modal-title">Create Task</h4>
                    </div>
                    <div class="modal-body">

                        <input class="form-control" type="hidden" name="category_id" id="category_id" />
                        <input class="form-control" type="hidden" name="site_id" id="site_id" />
                        <div class="form-group">
                            <label for="">Subject</label>
                            <input class="form-control" type="text" id="hidden-task-subject" name="task_subject" />
                        </div>
                        <div class="form-group">
                            <label for="">Task Type</label>
                            <br />
                            <select class="form-control" style="width:100%;" name="task_type" tabindex="-1"
                                aria-hidden="true">
                                <option value="0">Other Task</option>
                                <option value="4">Developer Task</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="repository_id">Repository:</label>
                            <br>
                            <select style="width:100%" class="form-control 	" id="repository_id" name="repository_id">
                                <option value="">-- select repository --</option>
                                @foreach (\App\Github\GithubRepository::all() as $repository)
                                    <option value="{{ $repository->id }}">{{ $repository->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Details</label>
                            <input class="form-control" type="text" name="task_detail" />
                        </div>

                        <div class="form-group">
                            <label for="">Cost</label>
                            <input class="form-control" type="text" name="cost" />
                        </div>

                        <div class="form-group">
                            <label for="">Assign to</label>
                            <select name="task_asssigned_to" class="form-control select2">
                                @foreach ($allUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
						<label for="">Create Review Task?</label>
						<div class="form-group">
								<input type="checkbox" name="need_review_task" value="1" />
						</div>
					</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-default create-task">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="dev_task_statistics" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Dev Task statistics</h2>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body" id="dev_task_statistics_content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>Task type</th>
                                    <th>Task Id</th>
                                    <th>Assigned to</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="remark-area-list" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">

                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="45%">Remark</th>
                                    <th width="25%">BY</th>
                                    <th width="25%">Date</th>
                                </tr>
                            </thead>
                            <tbody class="remark-action-list-view">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @include(
        'storewebsite::site-check-list.partials.status-history-modal'
    )
    @include(
        'storewebsite::site-check-list.partials.upload-document'
    )
    @include(
        'storewebsite::site-check-list.partials.download-modal'
    )

    @include('partials.plain-modal')
@endsection


@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
    <script type="text/javascript">
        $(document).on('click', '.create-quick-task', function() {
            var $this = $(this);
            site = $(this).data("id");
            title = $(this).data("title");
            category_id = $(this).data("category_id");
            if (!title || title == '') {
                toastr["error"]("Please add title first");
                return;
            }

            $("#create-quick-task").modal("show");

            $("#hidden-task-subject").val(title);
            $('#site_id').val(site);
            $('#category_id').val(49);
        });

        function saveRemarks(rowId) {
            var siteId = $("#remark_" + rowId).data("siteid");
            var cat_id = $("#remark_" + rowId).data("catid");
            var website_id = $("#remark_" + rowId).data("websiteid");

            var val = $("#remark_" + rowId).val();
            var data = {
                remark: val,
                cat_id: cat_id,
                website_id: website_id
            };
            $.ajax({
                url: '/site-development/' + siteId + '/remarks',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: data,
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                $("#remark-field").val("");
                toastr["success"]("Remarks fetched successfully");
                var html = "";
                $.each(response.data, function(k, v) {
                    html += "<tr>";
                    html += "<td>" + v.id + "</td>";
                    html += "<td>" + v.remarks + "</td>";
                    html += "<td>" + v.created_by + "</td>";
                    html += "<td>" + v.created_at + "</td>";
                    html += "</tr>";
                });
                $("#remark-area-list").find(".remark-action-list-view").html(html);

            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                toastr["error"]("Oops,something went wrong");
                $("#loading-image").hide();
            });
        }


        $(document).on("click", ".create-task", function(e) {
            e.preventDefault();
            var form = $(this).closest("form");
            $.ajax({
                url: form.attr("action"),
                type: 'POST',
                data: form.serialize(),
                beforeSend: function() {
                    $(this).text('Loading...');
                },
                success: function(response) {
                    if (response.code == 200) {
                        form[0].reset();
                        toastr['success'](response.message);
                        $("#create-quick-task").modal("hide");
                    } else {
                        toastr['error'](response.message);
                    }
                }
            }).fail(function(response) {
                toastr['error'](response.responseJSON.message);
            });
        });

        $(document).on("click", ".btn-store-development-remark", function(e) {
            var id = $(this).data("site-id");
            var cat_id = $(this).data("site-category-id");
            var website_id = $(this).data("store-website-id");
            $.ajax({
                url: '/site-development/' + id + '/remarks',
                type: 'GET',
                data: {
                    cat_id: cat_id,
                    website_id: website_id
                },
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                toastr["success"]("Remarks fetched successfully");

                var html = "";
                const shorter = (a, b) => a.id > b.id ? -1 : 1;
                response.data.flat().sort(shorter)

                $.each(response.data.flat().sort(shorter), function(k, v) {
                    html += "<tr>";
                    html += "<td>" + v.id + "</td>";
                    html += "<td>" + v.remarks + "</td>";
                    html += "<td>" + v.created_by + "</td>";
                    html += "<td>" + v.created_at + "</td>";
                    html += "</tr>";
                });

                $("#remark-area-list").find("#remark-field").data("id", id);
                $("#remark-area-list").find("#remark_cat_id").val(cat_id);
                $("#remark-area-list").find("#remark_website_id").val(website_id);
                $("#remark-area-list").find(".remark-action-list-view").html(html);
                $("#remark-area-list").modal("show").css('z-index', 1051);
                //$this.closest("tr").remove();
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                toastr["error"]("Oops,something went wrong");
                $("#loading-image").hide();
            });
        });


        $(document).on("click", ".count-dev-customer-tasks", function() {
            var $this = $(this);
            var site_id = $(this).data("id");
            $.ajax({
                type: 'get',
                url: '/site-development/countdevtask/' + site_id,
                dataType: "json",
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(data) {
                    $("#dev_task_statistics").modal("show");
                    var table = `<div class="table-responsive">
				<table class="table table-bordered table-striped">
					<tr>
						<th width="4%">Tsk Typ</th>
						<th width="4%">Tsk Id</th>
						<th width="7%">Asg to</th>
						<th width="12%">Desc</th>
						<th width="12%">Sts</th>
						<th width="33%">Communicate</th>
						<th width="10%">Action</th>
					</tr>`;
                    for (var i = 0; i < data.taskStatistics.length; i++) {
                        var str = data.taskStatistics[i].subject;
                        var res = str.substr(0, 100);
                        var status = data.taskStatistics[i].status;
                        if (typeof status == 'undefined' || typeof status == '' || typeof status ==
                            '0') {
                            status = 'In progress'
                        };
                        table = table + '<tr><td>' + data.taskStatistics[i].task_type + '</td><td>#' +
                            data.taskStatistics[i].id +
                            '</td><td class="expand-row-msg" data-name="asgTo" data-id="' + data
                            .taskStatistics[i].id + '"><span class="show-short-asgTo-' + data
                            .taskStatistics[i].id + '">' + data.taskStatistics[i].assigned_to_name
                            .replace(/(.{6})..+/, "$1..") +
                            '</span><span style="word-break:break-all;" class="show-full-asgTo-' + data
                            .taskStatistics[i].id + ' hidden">' + data.taskStatistics[i]
                            .assigned_to_name +
                            '</span></td><td class="expand-row-msg" data-name="res" data-id="' + data
                            .taskStatistics[i].id + '"><span class="show-short-res-' + data
                            .taskStatistics[i].id + '">' + res.replace(/(.{7})..+/, "$1..") +
                            '</span><span style="word-break:break-all;" class="show-full-res-' + data
                            .taskStatistics[i].id + ' hidden">' + res + '</span></td><td>' + status +
                            '</td><td><div class="col-md-10 pl-0 pr-1"><textarea rows="1" style="width: 100%; float: left;" class="form-control quick-message-field input-sm" name="message" placeholder="Message"></textarea></div><div class="p-0"><button class="btn btn-sm btn-xs send-message" title="Send message" data-taskid="' +
                            data.taskStatistics[i].id +
                            '"><i class="fa fa-paper-plane"></i></button></div></td><td><button type="button" class="btn btn-xs load-communication-modal load-body-class" data-object="' +
                            data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i]
                            .id +
                            '" title="Load messages" data-dismiss="modal"><i class="fa fa-comments"></i></button>';
                        table = table + '<a href="javascript:void(0);" data-task-type="' + data
                            .taskStatistics[i].task_type + '" data-id="' + data.taskStatistics[i].id +
                            '" class="delete-dev-task-btn btn btn-xs"><i class="fa fa-trash"></i></a>';
                        table = table +
                            '<button type="button" class="btn btn-xs  preview-img pd-5" data-object="' +
                            data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i]
                            .id + '" data-dismiss="modal"><i class="fa fa-list"></i></button></td>';
                        table = table + '</tr>';
                    }
                    table = table + '</table></div>';
                    $("#loading-image").hide();
                    $(".modal").css("overflow-x", "hidden");
                    $(".modal").css("overflow-y", "auto");
                    $("#dev_task_statistics_content").html(table);
                },
                error: function(error) {
                    $("#loading-image").hide();
                }
            });
        });
        
        function get_query(){
            var url = document.location.href;
            var qs = url.substring(url.indexOf('?') + 1).split('&');
            for(var i = 0, result = {}; i < qs.length; i++){
                qs[i] = qs[i].split('=');
                result[qs[i][0]] = decodeURIComponent(qs[i][1]);
            }
            return result;
        }
        $(".select2").select2();

        var selectedValues = [{{$search_website_string}}];
        $(document).ready(function() {
            $('.globalSelect22').select2({
                multiple: true,
            });
            let resu = get_query();
            if(!resu['store_webs%5B%5D'] && resu['store_webs%5B%5D'] !=''){
                $('.globalSelect22').val(selectedValues).trigger('change');
            }
        });


        $("#checkAll").click(function() {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });

        // For Open File Upload Modal 
        $(document).on("click", ".upload-document-btn", function() {
            var sdcid = $(this).data("sdcid");
            var swid = $(this).data("swid");
            var sdid = $(this).data("sdid");
            $("#upload-document-modal").find("#site_development_category_id	").val(sdcid);
            $("#upload-document-modal").find("#store_website_id").val(swid);
            $("#upload-document-modal").find("#site_development_id").val(sdid); // 
            $("#upload-document-modal").modal("show");
        });

        // For Document Upload ajax
        $(document).on("submit", "#upload-task-documents", function(e) {
            e.preventDefault();
            var form = $(this);
            var postData = new FormData(form[0]);
            $.ajax({
                method: "post",
                url: `{{ route('site-check-list.upload-document') }}`,
                data: postData,
                processData: false,
                contentType: false,
                dataType: "json",
                beforeSend: function() {
                    $("#loading-image").show();
                },
                complete: function() {
                    $("#loading-image").hide();
                },
                success: function(response) {
                    if (response.code == 200) {
                        toastr["success"]("Document Uploaded!", "Message")
                        $("#upload-document-modal").modal("hide");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                    $("#loading-image").hide();
                }
            });
        });

        // get Uploaded Document data`
    $(document).on("click", ".list-document-btn", function() {
        var sdcid = $(this).data("sdcid");
        var swid = $(this).data("swid");
        var sdid = $(this).data("sdid");
        $.ajax({
            method: "GET",
            url: `{{ route('site-check-list.get-document') }}`,
            data: {
                site_development_category_id: sdcid,
                store_website_id: swid,
                site_development_id: sdid,
            },
            dataType: "json",
            beforeSend: function() {
                $("#loading-image").show();
            },

            success: function(response) {
                if (response.code == 200) {
                    $("#blank-modal").find(".modal-title").html("Document List");
                    $("#blank-modal").find(".modal-body").html(response.data);
                    $("#blank-modal").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }

                $("#loading-image").hide();
            },
            error: function(error) {
                toastr["error"]('Unauthorized permission development-get-document', "Message")
                $("#loading-image").hide();
            }
        });
    });

    // Open Modal for download model
    $(document).on("click", ".download_check_list_data", function() {
        $("#download_site_check_list_modal").modal('show');
    });

    // Update Site status 
    $(document).on("change", ".save-item-select", function() {
        category = $(this).data("category");
        type = $(this).data("type");
        site = $(this).data("site");
        websiteId = $(this).data("swid");
        var text = $(this).val();

        $.ajax({
                url: `{{ route('site-development.save') }}`,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        websiteId: websiteId,
                        category: category,
                        type: type,
                        text: text,
                        site: site
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                    },
                })
                .done(function(data) {
                    toastr["success"]("Successful");
                    $("#loading-image").hide();
                })
                .fail(function(data) {
                    $("#loading-image").hide();
                });
        });

        $(document).on('click', '.btn-status-histories-get', function(e) {
            e.preventDefault();
            id = $(this).data('site-id');
            if (!id) return;
            $.ajax({
                url: "/site-development/status-history/" + id,
                type: 'GET',
                beforeSend: function() {
                    $("#loading-image").show();
                },
                dataType: "json",
                success: function(response) {
                    var tr = '';
                    $.each(response.data, function(k, v) {
                        tr = tr + '<tr><td>' + v.id + '</td><td>' + v.created_at +
                            '</td><td> ' + v.status_name + '</td><td>' + v.user_name +
                            '</td></tr>';
                    });
                    $("#status-history-modal").modal("show");
                    $(".status-history-list-view").html(tr);
                    $("#loading-image").hide();
                },
                error: function() {
                    $("#loading-image").hide();
                }
            });
        });

        $(document).on('click', '.send-message', function() {
            var thiss = $(this);
            var data = new FormData();
            var task_id = $(this).data('taskid');
            var message = $(this).closest('tr').find('.quick-message-field').val();
            var mesArr = $(this).closest('tr').find('.quick-message-field');
            $.each(mesArr, function(index, value) {
                if ($(value).val()) {
                    message = $(value).val();
                }
            });

            data.append("task_id", task_id);
            data.append("message", message);
            data.append("status", 1);

            if (message.length > 0) {
                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        url: '/whatsapp/sendMessage/task',
                        type: 'POST',
                        "dataType": 'json', // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function() {
                            $(thiss).attr('disabled', true);
                        }
                    }).done(function(response) {
                        thiss.closest('tr').find('.quick-message-field').val('');

                        toastr["success"]("Message successfully send!", "Message")
                        // $.post( "/whatsapp/approve/customer", { messageId: response.message.id })
                        //   .done(function( data ) {
                        //
                        //   }).fail(function(response) {
                        //     alert(response.responseJSON.message);
                        //   });

                        $(thiss).attr('disabled', false);
                    }).fail(function(errObj) {
                        $(thiss).attr('disabled', false);

                        alert("Could not send message");
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });
    </script>

@endsection
