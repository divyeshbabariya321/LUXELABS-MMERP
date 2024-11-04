@extends('layouts.app')

@section('favicon' , 'task.png')

@section('title', 'Tasks')

@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
{{-- <link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet"/>
<style>
    #message-wrapper {
        height: 450px;
        overflow-y: scroll;
    }

    .dis-none {
        display: none;
    }

    .pd-5 {
        padding: 3px;
    }

    .cls_task_detailstextarea {
        height: 30px !important;
    }

    .cls_remove_allpadding {
        padding-right: 0px !important;
        padding-left: 0px !important;
    }

    .cls_right_allpadding {
        padding-right: 0px !important;
    }

    .cls_left_allpadding {
        padding-left: 0px !important;
    }

    #addNoteButton {
        margin-top: 2px;
    }

    #saveNewNotes {
        margin-top: 2px;
    }

    table tr:last-child td {
        border-bottom: 1px solid #dee2e6 !important;
    }

    .col-xs-12.col-md-2 {
        padding-left: 5px !important;
        padding-right: 5px !important;
        height: 38px;
    }

    .cls_task_subject {
        padding-left: 9px;
    }

    #recurring-task .col-xs-12.col-md-6 {
        padding-left: 5px !important;
        padding-right: 5px !important;
    }

    #appointment-container .col-xs-12.col-md-6 {
        padding-left: 5px !important;
        padding-right: 5px !important;
    }

    #taskCreateForm .form-group {
        margin-bottom: 0px;
    }

    .cls_action_box .btn-image img {
        width: 12px !important;
    }

    .cls_action_box .btn.btn-image {
        padding: 2px;
    }

    .btn.btn-image {
        padding: 5px 3px;
    }

    .td-mini-container {
        margin-top: 9px;
    }

    .td-full-container {
        margin-top: 9px;
    }

    .cls_textbox_notes {
        width: 100% !important;
    }

    .cls_multi_contact .btn-image img {
        width: 12px !important;
    }

    .cls_multi_contact {
        width: 100%;
    }

    .cls_multi_contact_first {
        width: 80%;
        display: inline-block;
    }

    .cls_multi_contact_second {
        width: 7%;
        display: inline-block;
    }

    .cls_categoryfilter_box .btn-image img {
        width: 12px !important;
    }

    .cls_categoryfilter_box {
        width: 100%;
    }

    .cls_categoryfilter_first {
        width: 80%;
        display: inline-block;
    }

    .cls_categoryfilter_second {
        width: 7%;
        display: inline-block;
    }

    .cls_comm_btn {
        margin-left: 3px;
        padding: 4px 8px;
    }

    .btn.btn-image.btn-call-data {
        margin-top: -9px;
    }

    .dis-none {
        display: none;
    }

    .no-due-date {
        background-color: #f1f1f1 !important;
    }

    .over-due-date {
        background-color: #777 !important;
        color: white;
    }

    .over-due-date .btn {
        background-color: #777 !important;
    }

    .over-due-date .btn .fa {
        color: black !important;
    }

    .no-due-date .btn {
        background-color: #f1f1f1 !important;
    }

    @media (max-width: 991px) {
        .padding-right-md {
            padding-right: 13px;
        }
    }

    @media (max-width: 767px) {
        .padding-right-sm {
            padding-right: 13px;
        }
    }

    .pd-2 {
        padding: 2px;
    }

    .down-icon {
        margin-right: 10px;
    }

    .padding-right {
        padding-right: 13px;
    }

    .cls_task_subject {
        padding-left: 13px;
    }

    .zoom-img:hover {
        -ms-transform: scale(1.5); /* IE 9 */
        -webkit-transform: scale(1.5); /* Safari 3-8 */
        transform: scale(1.5);
    }

    .loading {
        z-index: 20;
        position: absolute;
        top: 0;
        left:-5px;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.4);
    }
    .loading-content {
        position: absolute;
        border: 16px solid #f3f3f3;
        border-top: 16px solid #3498db;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        top: 40%;
        left:50%;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endsection

@section('large_content')

<div class="row">
    <div class="col-lg-12 text-center">
        <h2 class="page-heading">{{$title}}</h2>
    </div>
</div>
<!--- Pre Loader -->
<img src="/images/pre-loader.gif" id="Preloader" style="display:none;"/>

@include('partials.flash_messages')

<div class="row">
    <div class="col-xs-12">
        <form class="form-search-data" id="search_keyword_form">
            <input type="hidden" name="daily_activity_date" value="">
            <input type="hidden" name="type" id="tasktype" value="pending">
            <div class="row">
                <div class="col-xs-12 col-lg-3 col-sm-6 pd-2">
                    <div class="form-group cls_task_subject padding-right-sm" id="keyword_search_type">
                        <input type="text" name="term" placeholder="Search Keyword" id="keyword_search"
                               class="form-control input-sm" value="">
                    </div>
                </div>
                <!-- Location for which we are searching keyword for  -->
                <div class="col-xs-12 col-lg-3 col-sm-6 pd-2">
                    <div class="form-group cls_task_subject padding-right-md">
                        <select name="keyword_location" id="keyword_location" class="form-control input-sm">
                            <option value="">Search by Location</option>
                            @foreach ($locations as $location)
                            <option value="{{ $location['code'] }}">{{ $location['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- Language Selection -->
                <div class="col-xs-12 col-lg-3 col-sm-6 pd-2">
                    <div class="form-group cls_task_subject padding-right-sm">
                        <select name="keyword_language" id="keyword_language" class="form-control input-sm">
                            <option value="">Search by Language</option>
                            @foreach ($languages as $language)
                            <option value="{{ $language['criterion_id'] }}">{{ $language['language_name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- View Selection -->
                <div class="col-xs-12 col-lg-2 col-sm-6 pd-2">
                    <div class="form-group cls_task_subject padding-right">
                        <select name="view_type" id="view_type" class="form-control input-sm">
                            <option value="">select View</option>
                            <option value="keyword_view">Keyword view</option>
                            <option value="grouped_view">Grouped view</option>
                        </select>
                    </div>
                </div>

                <div class="col-xs-12 col-lg-1 col-sm-6 pd-2 ml-auto">
                    <div class="padding-right pl-4 pl-sm-0 text-lg-left text-sm-right">
                        <button class="btn btn-primary" type="button" name="keyword_search_btn" id="keyword_search_btn">search</button>
                    </div>
                </div>
                <!-- Search Network -->
               
            </div>
            <!-- FILTERS -->
           
        </form>
    </div>
</div>

@php
    $isAdmin = (\App\Helpers::getadminorsupervisor() && !empty($selected_user))?true:false;
@endphp

@if(auth()->user()->isAdmin())
<div class="row">
    <div class="col-md-12">
        <div class="collapse" id="openFilterCount">
            <div class="card card-body">
            </div>
        </div>
    </div>
</div>
@endif

<div id="exTab2" style="overflow: auto">
    <ul class="nav nav-tabs">
        <!-- <li class="active"><a href="#1" data-toggle="tab" class="btn-call-data" data-type="pending">Pending Task</a></li>
        <li><a href="#2" data-toggle="tab" class="btn-call-data" data-type="statutory_not_completed">Statutory Activity</a></li>
        <li><a href="#3" data-toggle="tab" class="btn-call-data" data-type="completed">Completed Task</a></li>
        <li><a href="#unassigned-tab" data-toggle="tab">Unassigned Messages</a></li>
        <li><button type="button" class="btn btn-xs btn-secondary my-3" id="view_tasks_button" data-selected="0">View Tasks</button></li>&nbsp;
        <li><button type="button" class="btn btn-xs btn-secondary my-3" id="view_categories_button">Categories</button></li>&nbsp;
        <li><button type="button" class="btn btn-xs btn-secondary my-3" id="make_complete_button">Complete Tasks</button></li>&nbsp;
        <li><button type="button" class="btn btn-xs btn-secondary my-3" id="make_delete_button">Delete Tasks</button></li>&nbsp; -->
    </ul>
    <div class="tab-content ">
        <!-- Pending task div start -->
        <div class="tab-pane active" id="1">
            <div class="row" style="margin:0px;">
                <!-- <h4>List Of Pending Tasks</h4> -->
                <div class="col-12">
                    <table class="table table-sm table-bordered" id="keyword_table">
                        <thead id="keyword_table_header">
                        <tr>
                            <th>Kewords</th>
                            <th>Avg. monthly searches</th>
                            <th>Competition</th>
                            <th>Top of page bid (low range)</th>
                            <th>Top of page bid (high range)</th>
                        </tr>
                        </thead>
                        <tbody class="pending-row-render-view infinite-scroll-pending-inner" id="keyword_table_data">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
              50% 50% no-repeat;display:none;">
    </div>
    @include("development.partials.time-history-modal")
    @include("task-module.partials.tracked-time-history")
    @endsection

    @section('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script> --}}
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#search_keyword_form').submit(e => e.preventDefault());
            var keywordSearch = null;
            $(document).on('click', '#keyword_search_btn', function () {

                $(this).append('<i class="fa fa-spinner ml-3"></i>');
                $(this).attr("disabled", 'disabled');

                if ($('#keyword_search').val() == '' || $('#keyword_search').val() == null) {
                    // error
                    toastr['error']('Keyword not be empty!!', 'Error');
                    $(this).find(".fa-spinner").remove();
                    $(this).removeAttr("disabled");
                    return false;
                } else {
                    /*  var networkCheck = true;
                     if ( $('#google_search').is(":checked") ) {
                         networkCheck = false;
                     }
                     if ( $('#search_network').is(":checked") ) {
                         networkCheck = false;
                     }
                     if ( $('#content_network').is(":checked") ) {
                         networkCheck = false;
                     }
                     if ( $('#partner_search_network').is(":checked") ) {
                         networkCheck = false;
                     }
                     if (networkCheck == true) {
                         toastr['error']('Atleast one network option should be selected!!', 'Error');
                         return false;
                     } */
                    keywordSearch = $.ajax({
                        url: "{{route('google-keyword-search-v2')}}",
                        type: 'GET',
                        data: {
                            keyword: $('#keyword_search').val(),
                            location: $('#keyword_location').val(),
                            language: $('#keyword_language').val(),
                            viewType: $('#view_type').val()

                            /* network:$('#keyword_network').val(),
                            gender:$('#filter_by_gender').val(),
                            google_search:($('#google_search').is(":checked"))? true : false,
                            search_network:($('#search_network').is(":checked"))? true : false,
                            content_network:($('#content_network').is(":checked"))? true : false,
                            partner_search_network:($('#partner_search_network').is(":checked"))? true : false, */
                        },
                        beforeSend: function () {
                            if (keywordSearch != null) {
                                keywordSearch.abort();
                                // $("#keyword_search_btn").find(".fa-spinner").remove();
                            }
                        },
                        success: function (response) {
                            let tableData = '';
                            $('#keyword_table_data').html(tableData);
                            if (response.status == 'success') {
                                response = response.data;
                                if ($('#view_type').val() === 'grouped_view') {
                                    const keyword_arr = [];
                                    $('#keyword_table_header').hide();
                                    $.each(response, function (index, value) {
                                        keyword_arr.push(index);
                                    });

                                    $.each(keyword_arr, function (index_key, value) {
                                        tableData += `<tr onclick="openSubDiv(${index_key})" style="cursor: pointer">
                                                    <td colspan="5" style="text-transform: capitalize">
                                                        <i class="down-icon fa fa-angle-down" aria-hidden="true" id="angle-change-${index_key}"></i>
                                                        ${value} (${response[value].length})</td>
                                                </tr>
                                    <tr class="sub-table-div-class hidden" id="sub-table-div-${index_key}" >
                                    <td colspan="5" style="padding: 15px">
                                    <table width="100%">
                                                        <th>Keywords</th>
                                                        <th>Avg. monthly searches</th>
                                                        <th>Competition</th>
                                                        <th>Top of page bid (low range)</th>
                                                        <th>Top of page bid (high range)</th>
                                                    </tr>
                                            </thead>
                                    <tbody>`;
                                        $.each(response[value], function (index, data) {
                                            tableData += `<tr>
                                                            <td>${data.keyword}</td>
                                                            <td>${data.avg_monthly_searches}</td>
                                                            <td>${data.competition}</td>
                                                            <td>${data.low_top}</td>
                                                            <td>${data.high_top}</td>
                                                            </tr>`;
                                        });

                                        tableData += `</tbody>
                                    </table>
                                    </td>
                                    </tr>`;
                                    });
                                } else {
                                    $('#keyword_table_header').show();
                                    $.each(response, function (index, data) {
                                        tableData += `<tr>
                                    <td>${data.keyword}</td>
                                    <td>${data.avg_monthly_searches}</td>
                                    <td>${data.competition}</td>
                                    <td>${data.low_top}</td>
                                    <td>${data.high_top}</td>
                                </tr>`;
                                    });
                                }
                                $('#keyword_table_data').html(tableData);
                                $("#keyword_search_btn").find(".fa-spinner").remove();
                                $("#keyword_search_btn").removeAttr("disabled");
                            } else {
                                toastr['error'](response.message, 'Error');
                            }
                            // toastr['success']('Priority successfully update!!', 'success');
                            // $('#priority_model').modal('hide');
                        },
                        error: function (response) {
                            $("#keyword_search_btn").find(".fa-spinner").remove();
                            toastr['error'](response.message, 'Error');
                        }
                    });
                }
            });
        });

        /*show sub keyword for group view*/
        function openSubDiv(index) {
            $('#sub-table-div-' + index).toggleClass('hidden');
            if ($("#sub-table-div-" + index).hasClass('hidden')) {
                $("#angle-change-" + index).addClass('fa-angle-down');
                $("#angle-change-" + index).removeClass('fa-angle-up');
            } else {
                $("#angle-change-" + index).removeClass('fa-angle-down');
                $("#angle-change-" + index).addClass('fa-angle-up');
            }
        }
    </script>
    @endsection
