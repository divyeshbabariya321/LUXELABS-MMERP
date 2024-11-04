@extends('layouts.app')

@section('title', 'Magento Crons')

@section('styles')
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
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
        color: #343a40!important // use your own color here
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
    display: contents; /*this will make the container disappear, making the child the one who sets the width of the element*/
}

.select2-search__field:placeholder-shown {
    width: 100% !important; /*makes the placeholder to be 100% of the width while there are no options selected*/
}
.select2-container{width: 100%;}
</style>

@endsection

@section('content')

<div class="row">
    <div class="col-12">
        <h2 class="page-heading">Magento Crons</h2>
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
    <form class="form-inline" action="/magento/magento_command/searchcron" method="GET" style=" width: 100%;">
        <div class="col-8">
            <div class="form-group" style=" width: 100%;">
                <div class="input-group" style=" width: 100%;">
                    <select name="command_name[]" class="form-control select2" id="command_name" multiple data-placeholder="Select Command Name">
                        <option></option>
                        @foreach ($allMagentoCommandListArray as $comName => $comType)
                        <option @if(!empty(request('command_name')) && in_array($comName ,request('command_name'))) selected @endif value="{{$comName}}">{{$comType}}</option>
                        @endforeach
                    </select>
                    
                </div>
            </div>
        </div>
        <div class="col-4">
            <button type="submit" class="btn btn-image"><img src="/images/filter.png"></button>
            <a href="/magento/magento_command" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
            <button type="button" class="btn custom-button float-right mr-3 openmodeladdpostman" data-toggle="modal" data-target="#addPostman">Add Command</button>
        </div>
    </form>    
</div>

@php $isPermissionCommandRun = 0; @endphp
</br>
<div class="row m-0">
    <div class="col-12" style="border: 1px solid;border-color: #dddddd;">
        <div class="table-responsive mt-2" style="overflow-x: auto !important;">
            <table class="table table-bordered text-nowrap">
                <thead>
                    <tr>
                        <th style="width: 3%;">ID</th>
                        <th style="width: 22%;overflow-wrap: anywhere;">Command name</th>
                        <th style="width: 22%;overflow-wrap: anywhere;">Frequency</th>
                        <th style="width: 22%;overflow-wrap: anywhere;">Last Execution Time</th>
                        <th style="width: 22%;overflow-wrap: anywhere;">Last Message</th>
                        <th style="width: 22%;overflow-wrap: anywhere;">Status</th>
                        <th style="width: 10%;overflow-wrap: anywhere;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($magentoCommand as $key => $magentoCom)
                    <tr>
                        <td>{{$magentoCom->id}}</td>
                        <td class="expand-row-msg" data-name="cron_name" data-id="{{$magentoCom->id}}">
                            <span class="show-short-cron_name-{{$magentoCom->id}}">{{ Str::limit($magentoCom->cron_name, 20, '..')}}</span>
                            <span style="word-break:break-all;" class="show-full-cron_name-{{$magentoCom->id}} hidden">{{$magentoCom->cron_name}}</span>
                        </td>
                        <td>{{$magentoCom->frequency}}</td>
                        <td>@if($magentoCom->last_execution_time!='0000-00-00 00:00:00') {{$magentoCom->last_execution_time}} @endif</td>
                        <td>{{$magentoCom->last_message}}</td>
                        <td>@if($magentoCom->last_execution_time!='0000-00-00 00:00:00') @if($magentoCom->cron_status==0) {{'Success'}} @else {{'Failure'}} @endif @endif </td>
                        <td>
                            <a title="Run Command" class="btn btn-image magentoCom-run-btn pd-5 btn-ht" data-id="{{ $magentoCom->id }}" href="javascript:;">
                                <i class="fa fa-paper-plane" aria-hidden="true"></i>
                            </a>

                            <a class="btn btn-image edit-magentoCom-btn" data-id="{{ $magentoCom->id }}"><img data-id="{{ $magentoCom->id }}" src="/images/edit.png" style="cursor: nwse-resize; width: 16px;"></a>

                            <a class="btn delete-magentoCom-btn" data-id="{{ $magentoCom->id }}" href="#"><img data-id="{{ $magentoCom->id }}" src="/images/delete.png" style="cursor: nwse-resize; width: 16px;"></a>

                            <a title="Preview Response" data-id="{{ $magentoCom->id }}" class="btn btn-image preview_response pd-5 btn-ht" href="javascript:;"><i class="fa fa-product-hunt" aria-hidden="true"></i></a>
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
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
    </div>
</div>
@endsection

<div id="commandResponseHistoryModel" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width: 100%;width: 90% !important;">
        <!-- Modal content-->
        <div class="modal-content ">
            <div id="add-mail-content">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Cron Response History</h3>
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
                                    <th style="width: 5%;overflow-wrap: anywhere;">Cron Name</th>
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

<div id="executeCommand" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content ">
            <div id="add-mail-content">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><span>Execute Command</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="magentoExecutionForm" method="post">
                            @csrf

                            <div class="form-row">
                                <input type="hidden" id="command_id" name="id" value="" />

                                <div class="form-group col-md-12">
                                    <label for="cron_name">Cron Name</label>
                                    <input name="cron_name" type="text" value="" class="form-control" readonly>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="frequency">Frequency</label>
                                    <input name="frequency" type="text" value="" class="form-control" readonly>
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="title">Website</label>
                                    <div class="dropdown-sin-1">
                                        <?php $websites = \App\StoreWebsite::get(); ?>
                                        <select name="websites_ids[]" class="websites_ids form-control dropdown-mul-1" style="width: 100%;" id="websites_ids" required multiple>
                                            <?php
                                            foreach ($websites as $website) {
                                                echo '<option value="'.$website->id.'" data-website="'.$website->website.'">'.$website->title.'</option>';
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-secondary submit-execute-form">Save</button>
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

                                <div class="form-group col-md-12">
                                    <label for="cron_name">Cron Name</label>
                                    <input type="text" name="cron_name" value="" class="form-control" id="cron_name" placeholder="Enter cron name">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="frequency">Frequency</label>
                                    <input type="text" name="frequency" value="" class="form-control" id="frequency" placeholder="Enter the frequency" required>
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
<link rel="stylesheet" type="text/css" href="{{asset('css/jquery.dropdown.min.css')}}">
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
            }
            , "id|+1": 1
            , "disabled|1-2": true
            , groupName: 'Group Name'
            , "groupId|1-4": 1
            , "selected": true
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

    $(document).on("click", ".delete-magentoCom-btn", function(e) {
        e.preventDefault();
        if (confirm("Are you sure?")) {
            var $this = $(this);
            var id = $this.data('id');
            $.ajax({
                url: "/magento/command/deletecommand"
                , type: "delete"
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , data: {
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
        
        if($("#cron_name").val()==''){
            toastr['error']('Please Enter Cron Name', 'error');
            return '';
        }
        if($("#frequency").val()==''){
            toastr['error']('Please Enter Frequency', 'error');
            return '';
        }

        if ($('#titleUpdate').text() == 'Add')
            $("#command_id").val("");
        $.ajax({
            url: "/magento/command/addcommand"
            , type: "post"
            , data: $('#magentoForm').serialize(),
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
            url: "/magento/command/editcommand"
            , type: "post"
            , headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , data: {
                id: id
            },
            beforeSend: function() {
                $('#loading-image').show();
            },
        }).done(function(response) {
            if (response.code = '200') {
                form = $('#magentoForm');
                $.each(response.data, function(key, v) {

                    if (form.find('[name="' + key + '"]').length) {
                        form.find('[name="' + key + '"]').val(v);
                    } 
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
            url: "/postman/history/"
            , type: "post"
            , headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , data: {
                id: id
            },
            beforeSend: function() {
                $('#loading-image').show();
            },
        }).done(function(response) {
            if (response.code = '200') {
                var t = '';
                $.each(response.data, function(key, v) {
                    t += '<tr><td>' + v.id + '</td>';
                    t += '<td>' + v.userName + '</td>';
                    t += '<td>' + v.created_at + '</td></tr>';
                });
                $(".tbodayPostmanHistory").html(t);
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

    $(document).on("click", ".magentoCom-run-btn", function(e) {
        e.preventDefault();

        var $this = $(this);
        var id = $this.data('id');

        $.ajax({
            url: "/magento/command/editcommand"
            , type: "post"
            , headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , data: {
                id: id
            },
            beforeSend: function() {
                $('#loading-image').show();
            },
        }).done(function(response) {
            if (response.code = '200') {
                form = $('#magentoExecutionForm');
                $.each(response.data, function(key, v) {

                    if (form.find('[name="' + key + '"]').length) {
                        form.find('[name="' + key + '"]').val(v);
                    } 

                });
                $('#executeCommand').modal('show');

            } else {
                toastr['error'](response.message, 'error');
            }
            $('#loading-image').hide();
        }).fail(function(errObj) {
            $('#loading-image').hide();
            $("#executeCommand").hide();
            toastr['error'](errObj.message, 'error');
        });
    });

    $(document).on("click", ".submit-execute-form", function(e) {
        e.preventDefault();
        var $this = $(this);

        if($("#websites_ids").val()==''){
            toastr['error']('Please Select Website', 'error');
            return '';
        }

        $.ajax({
            url: "/magento/command/runmagentocommand"
            , type: "post"
            , data: $('#magentoExecutionForm').serialize(),
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

    $(document).on("click", ".preview_response", function(e) {
        e.preventDefault();
        var $this = $(this);
        var id = $this.data('id');
        $.ajax({
            url: "/magento/cron/history/"
            , type: "post"
            , headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , data: {
                id: id
            },
            beforeSend: function() {
                $('#loading-image').show();
            },
        }).done(function(response) {
            if (response.code = '200') {
                var t = '';
                $.each(response.data, function(key, v) {
                    var responseString = '';
                    if (v.response)
                        responseString = v.response.substring(0, 10);
                    var requestString = '';
                    if (v.request)
                        requestString = v.request.substring(0, 10);
                    var request_data_val = '';
                    if (v.request_data)
                        request_data_val = v.request_data.substring(0, 10);
                    var request_url_val = '';
                    if (v.request_data)
                        request_url_val = v.request_url.substring(0, 10)
                    var commandString = '';
                    if (v.command_name)
                        commandString = v.command_name.substring(0, 10);


                    t += '<tr><td>' + v.id + '</td>';
                    t += '<td>' + v.userName + '</td>';
                    t += '<td  class="expand-row-msg" data-name="command" data-id="' + v.id + '" ><span class="show-short-command-' + v.id + '">' + commandString + '...</span>    <span style="word-break:break-all;" class="show-full-command-' + v.id + ' hidden">' + v.command_name + '</span></td>';
                    t += '<td>' + v.status + '</td>';
                    t += '<td  class="expand-row-msg" data-name="response" data-id="' + v.id + '" ><span class="show-short-response-' + v.id + '">' + responseString + '...</span>    <span style="word-break:break-all;" class="show-full-response-' + v.id + ' hidden">' + v.response + '</span></td>';
                    t += '<td  class="expand-row-msg" data-name="response" data-id="' + v.id + '" ><span class="show-short-response-' + v.id + '">' + requestString + '...</span>    <span style="word-break:break-all;" class="show-full-response-' + v.id + ' hidden">' + v.request + '</span></td>';
                    t += '<td>' + v.job_id + '</td>';
                    //t += '<td>'+v.response_code+'</td>';
                    //t += '<td  class="expand-row-msg" data-name="request_url" data-id="'+v.id+'" ><span class="show-short-request_url-'+v.id+'">'+request_url_val+'...</span>    <span style="word-break:break-all;" class="show-full-request_url-'+v.id+' hidden">'+v.request_url+'</span></td>';
                    //t += '<td  class="expand-row-msg" data-name="request_data" data-id="'+v.id+'" ><span class="show-short-request_data-'+v.id+'">'+request_data_val+'...</span>    <span style="word-break:break-all;" class="show-full-request_data-'+v.id+' hidden">'+v.request_data+'</span></td>';
                    t += '<td>' + v.created_at + '</td></tr>';
                });
                $(".tbodayCommandResponseHistory").html(t);
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

</script>
@endsection
