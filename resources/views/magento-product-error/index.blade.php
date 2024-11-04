@extends('layouts.app')
@section('favicon', 'task.png')

@section('title', $title)

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <style type="text/css">
        .preview-category input.form-control {
            width: auto;
        }

        td {
            word-wrap: break-word;
        }
        .btn-primary {
            background: #fff;
            border: 1px solid #ddd;
            color: #757575;

        }
        select.globalSelect2 + span.select2{
            width: 150px !important;
            flex-grow: 1;
        }
        .modal-lg{
            width: auto;
            max-width: inherit;
        }
        @media (min-width: 768px){
            .modal-dialog {
                width: 900px;
                max-width: 900px;
            }
        }
        .multiselect-native-select .multiselect {
            width: 200px;
        }
        .multiselect-container li a {
            line-height: 1.8;
        }
    </style>

    <div class="row m-0" id="common-page-layout">
        <div class="col-lg-12 margin-tb p-0">
            <h2 class="page-heading">{{ $title }} <span class="count-text"></span>
            <div class="pull-right pr-2">
                <a href="{{ route('magento_product_today_common_err') }}" class="btn btn-sm btn-primary mr-2">
                    Export Today Common Errors Report
                </a>

                <!-- START - Purpose : Add button - DEVTASK-20123  -->
                <a href="#" class="btn btn-sm btn-primary view_today_common_errors_report">
                    Today Common Errors Report
                </a>
            </div>
            </h2>
        </div>
        <br>
        <div class="col-lg-12 margin-tb p-0">
            <div class="row m-0">

                        <!-- Purpose : Rename from Today Common Errors Report to Export Today Common Errors Report - DEVTASK-20123  -->

                        <!-- END - DEVTASK-20123  -->

                <div class="col pl-3">
                    <div class="h" style="margin-bottom:0px;">
                        <form class="form-inline message-search-handler" method="post">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        
                                        {{ html()->text('keyword', request('keyword'))->class('form-control')->placeholder('Enter keyword') }}
                                    </div>
                                    <div class="form-group">
                                        
                                        <div class="col-md-2 pd-sm">
                                            <input placeholder="Date" type="text" class="form-control estimate-date_picker"
                                                id="estimate_date_picker" name="log_date">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        

                                        <select class="form-control globalSelect2" name="website" id="website">
                                            <option value="all" selected>All</option>
                                            @foreach ($websites as $website)
                                                <option value="{{ $website->id }}">{{ $website->title }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="form-group">
                                        {{ html()->select("response_status", ["" => "All", "error" => "Error", "success" => "success", "php" => "PHP", "translation_not_found" => "Translation not found", "magento" => "Magento"], request('response_status'))->class("form-control") }}
                                    </div>
                                    <div class="form-group">
                                        
                                        <button style="display: inline-block;width: 10%"
                                            class="btn btn-sm btn-image btn-secondary btn-search-action">
                                            <img src="/images/search.png" style="cursor: default;">
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="tab-content ">
                <!-- Pending task div start -->
                <div class="tab-pane active" id="1">
                    <div class="row" style="margin:0 10px;">
                        <div class="col-12 p-0">
                            <div class="margin-tb" id="page-view-result">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
              50% 50% no-repeat;display:none;">
        </div>

        <div class="common-modal modal" role="dialog">

            <div class="modal-dialog" role="document" style="width: 1000px; max-width: 1000px;word-break: break-all;">
            </div>
        </div>

        <!-- START - Purpose : modal for liting error - DEVTASK-20123  -->
        <!-- Modal -->
        <div class="modal fade" id="today_common_error_report_modal" tabindex="-1" role="dialog" aria-labelledby=""
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-lg">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Today Common Errors Report</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <strong>Date Range  </strong>
                                <input type="hidden" class="range_start_filter" value="<?php echo date('Y-m-d'); ?>" name="range_start" />
                                <input type="hidden" class="range_end_filter" value="<?php echo date('Y-m-d'); ?>" name="range_end" />
                                <div id="filter_date_range_" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ddd; width: 100%;border-radius:4px;">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span></span> <i class="fa fa-caret-down"></i>
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <strong style="padding-left: 12px;">Websites</strong><br/>
                                <select class="form-control multiselect" name="filter_by_website_ids[]" id="filter_by_website_ids" multiple>
                                    @foreach ($websites as $website)
                                        <option value="{{ $website->id }}">{{ $website->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <table id="" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%"
                            style="table-layout:fixed;">
                            <thead>
                                <tr>
                                    <th class="th-sm" style="width:20%">Count</th>
                                    <th class="th-sm" style="width:20%">Status</th>
                                    <th class="th-sm" style="width:60%">Message</th>
                                </tr>
                            </thead>
                            <tbody class="table_data">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div id="log_history_modal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Status History</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="row">
                        <div class="col-md-12" id="log_history_div">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Old Status</th>
                                        <th>New Status</th>
                                        <th>Updated by</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
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

        <div id="show-product-information" class="modal fade" role="dialog" style="margin: 150px;">
            <div class="modal-dialog modal-lg" style="margin: 0px;">
              <div class="modal-content" style="width: 1500px">
                <div class="modal-header">
                  <h4 class="modal-title">Product information</h4>
                </div>
                  <div class="modal-body">
                      <table class="table table-bordered table-hover" style="table-layout:fixed;">
                        <thead>
                          <th width="10%">SKU</th>
                          <th width="15%">Description</th>
                          <th>Name</th>
                          <th>Price</th>
                          <th>Composition</th>
                          <th>Material</th>
                          <th>Manufracturer.</th>
                          <th>Brand</th>
                          <th>Sizes</th>
                          <th>Dimensions</th>
                          <th width="5%">Stock</th>
                          <th width="5%">Min day</th>
                          <th width="5%">Max day</th>
                        </thead>
                        <tbody class="product-information-data">

                        </tbody>
                      </table>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                  </div>
              </div>
            </div>
          </div>


        @include("magento-product-error.templates.list-template")
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <script type="text/javascript" src="{{ asset('/js/jsrender.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('/js/jquery.validate.min.js') }}"></script>
        <script src="{{ asset('/js/jquery-ui.js') }}"></script>
        <script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
        <script type="text/javascript" src="{{ asset('/js/magento-product-error.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script type="text/javascript">
            function get_today_log_report_by_date_website() {
                var getSelectedWebsiteIds = $("#filter_by_website_ids").val();
                var setWebsites = "";
                if(getSelectedWebsiteIds){
                    setWebsites = "&websites="+getSelectedWebsiteIds;
                }
                
                $.ajax({
                    type: 'GET',
                    url: "{{ route('magento_product_today_common_err_report') }}" + '?startDate=' + jQuery('input[name="range_start"]').val()+ '&&endDate='+ jQuery('input[name="range_end"]').val()+setWebsites ,
                    dataType: "json",
                    beforeSend : function() {
                        $("#loading-image").show();
                    },
                    success: function(response) {
                        $("#loading-image").hide();
                        if (response.code == 200) {
                            var html_content = '';
                            $.each(response.data, function(key, value) {
                                html_content += '<tr>';
                                html_content +=  '<td>' +value.count + '</td>';
                                html_content +=  '<td>' +value.status + '</td>';
                                html_content +=  '<td class="data">';
                                var myContent = value.message;
                                html_content +=   myContent.replace(/(<([^>]+)>)/ig,"");
                                html_content += '</td>';
                                html_content += '</tr>';
                            });

                            $('.table_data').html(html_content);
                            $('#today_common_error_report_modal').modal('show');
                        }
                    },
                    error: function() {
                        $("#loading-image").hide();
                        toastr['error']('Could not change module!');
                    }
                });
            }
            
             $(document).ready(function() {
                setTimeout(() => {
                    page.init({
                        bodyView: $("#common-page-layout"),
                        baseUrl: "<?php echo url('/'); ?>"
                    });
                }, 2000);
            });
            
                let r_s = "";
                let r_e = "";
        
                let start = r_s ? moment(r_s,'YYYY-MM-DD') : moment().subtract(0, 'days');
                let end =   r_e ? moment(r_e,'YYYY-MM-DD') : moment();
        
                jQuery('input[name="range_start"]').val();
                jQuery('input[name="range_end"]').val();
        
                function cb(start, end) {
                    $('#filter_date_range_ span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                }
        
                $('#filter_date_range_').daterangepicker({
                    startDate: start,
                    maxYear: 1,
                    endDate: end,
                    //parentEl: '#filter_date_range_',
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                }, cb);
        
                cb(start, end);
        
                $('#filter_date_range_').on('apply.daterangepicker', function(ev, picker) {
                    let startDate=   jQuery('input[name="range_start"]').val(picker.startDate.format('YYYY-MM-DD'));
                    let endDate =    jQuery('input[name="range_end"]').val(picker.endDate.format('YYYY-MM-DD'));
                    get_today_log_report_by_date_website();
                });

            //START - Purpose : Get data - DEVTASK-20123
            $(document).on('click', '.view_today_common_errors_report', function(e) {

                $.ajax({
                    type: 'GET',
                    url: "{{ route('magento_product_today_common_err_report') }}",
                    dataType: "json",
                    beforeSend : function() {
                        $("#loading-image").show();
                    },
                    success: function(response) {
                        $("#loading-image").hide();
                        if (response.code == 200) {
                            var html_content = '';
                            $.each(response.data, function(key, value) {
                                html_content += '<tr>';
                                html_content +=  '<td>' +value.count + '</td>';
                                html_content +=  '<td>' +value.status + '</td>';
                                html_content +=  '<td class="data">';
                                var myContent = value.message;
                                html_content +=   myContent.replace(/(<([^>]+)>)/ig,"");
                                html_content += '</td>';
                                html_content += '</tr>';
                            });

                            $('.table_data').html(html_content);

                            $('#today_common_error_report_modal').modal('show');
                        }

                    },
                    error: function() {
                        $("#loading-image").hide();
                        toastr['error']('Could not change module!');
                    }
                });
            });
            //END - DEVTASK-20123

            $('#estimate_date_picker').datepicker({
                dateformat: 'yyyy-mm-dd'
            });

            $(document).on('click', '.show-logs-history', function() {
                $('#log_history_modal table tbody').html('');
                $.ajax({
                    url: '/log_history/list/' + $(this).data('id'),
                    method: 'post',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        if (data != 'error') {
                            $.each(data, function(i, item) {

                                $('#log_history_modal table tbody').append(
                                    '<tr>\
                                            <td>' + moment(item['created_at']).format('DD/MM/YYYY') + '</td>\
                                                <td>' + ((item['old_value'] != null) ? item['old_value'] : '-') + '</td>\
                                                <td>' + item['new_value'] + '</td>\
                                                <td>' + item['name'] + '</td>\
                                            </tr>'
                                );
                            });
                        }
                    }
                });
                $('#log_history_modal').modal('show');
            });
            $(document).on('change', '#error_status', function() {

                $.ajax({
                    url: '/log_status/change/' + $(this).data('log_id'),
                    method: 'post',
                    data: {
                        type: $(this).val(),
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend : function() {
                        $("#loading-image").show();
                    },
                    success: function() {
                        $("#loading-image").hide();
                        toastr['success']('Status Change Successfully');
                        
                        setTimeout(function(){ location.reload(); }, 2000);
                    },
                    error: function(response) {
                        $("#loading-image").hide();
                    }
                });
            });

            $(document).on("click",".show-product-information",function (e) {
                e.preventDefault();
                var id  = $(this).data("id");
                $.ajax({
                  method: "GET",
                  url: "/logging/list-magento/product-information",
                  data : {
                     product_id : id
                  }
                })
                .done(function(response) {
                    $(".product-information-data").html(response);
                    $("#show-product-information").modal("show");
                });
              });
              
              $(document).on("change","#filter_by_website_ids",function(){
                  get_today_log_report_by_date_website();
              });
        </script>

    @endsection
