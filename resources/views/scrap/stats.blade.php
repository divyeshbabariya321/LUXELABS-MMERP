@extends('layouts.app')

@section('favicon' , 'supplierstats.png')

@section('title', 'Scrape Statistics')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <style type="text/css">
        .dis-none {
            display: none;
        }

        #remark-list li {
            width: 100%;
            float: left;
        }

        .fixed_header {
            table-layout: fixed;
            border-collapse: collapse;
        }

        .fixed_header tbody {
            width: 100%;
            overflow: auto;
            height: 250px;
        }

        /* Purpose :  Comment code -  DEVTASK-4219*/
        /* .fixed_header thead {
            background: black;
            color: #fff;
        } */
        .modal-lg{
            max-width: 1500px !important; 
        }

        .remark-width{
            white-space: nowrap;
            overflow-x: auto;
            max-width: 20px;
        }

        .status .select2 .select2-selection{
            width:80px;
        }

        #remark-list tr td{
            word-break : break-all !important;
        }

        table tr td{
            word-wrap: break-word;
        }
        div#chat-list-history.modal {
            z-index: 99999999;
        }

        .select2-container--default, .select2-container--default .selection, .select2-selection--single{display: block !important;}
    </style>
@endsection

@include('partials.loader')
@include('partials.modals.latest-remarks')
@include('partials.modals.remarks',['type' => 'scrap'])

@section('large_content')
        @php
            $user = auth()->user();
            $isAdmin = $user->isAdmin();
            $hod = $user->hasRole('HOD of CRM');
        @endphp

    <div class="row">
        <div class="col-lg-12 margin-tb p-0">
            <!-- START - Purpose : Comment code and get total scrapper - DEVTASK-4219 -->
            <!-- <h2 class="page-heading">Supplier Scrapping Info <span class="total-info"></span></h2> -->
            <h2 class="page-heading">Supplier Scrapping Info ({{$scrapper_total}})</h2>
            <!-- END - DEVTASK-4219 -->
        </div>
    </div>


    @include('partials.flash_messages')
    <?php $status = request()->get('status', ''); ?>
    <?php $excelOnly = request()->get('excelOnly', ''); ?>
    <form id="statistics-filter-page" action="/scrap/statistics">
        <div class="row">
            <div class="form-group mb-3 col-md-2">
                <label>Supplier Name</label>
                <input name="term" type="text" class="form-control" id="product-search" value="{{ request()->get('term','') }}" placeholder="Enter Supplier name">
            </div>
            <div class="form-group mb-3 col-md-2">
                <label>Made by</label>
                @php

                     $madeByArray = ['' => ''];

                     if($selectedMadeBy){
                        $madeByArray[$selectedMadeBy->id] = $selectedMadeBy->name;
                     }

                @endphp
                {{ html()->select("scraper_made_by", $madeByArray, request("scraper_made_by"))->class("form-control globalSelect2")->data('ajax', route('select2.user'))->data('placeholder', 'Made by') }}
            </div>
            <div class="form-group mb-3 col-md-2">
                <label>Select Type</label>
                {{ html()->select("scraper_type", ['' => '-- Select Type --'] + \App\Helpers\DevelopmentHelper::scrapTypes(), request("scraper_type"))->class("form-control select22") }}
            </div>
            <div class="form-group mb-3 col-md-1">
                <label>All scrapers</label>
                <select name="excelOnly" class="form-control form-group select22">
                    <option <?php echo $excelOnly == '' ? 'selected=selected' : '' ?> value="">All scrapers</option>
                    <option <?php echo $excelOnly == -1 ? 'selected=selected' : '' ?> value="-1">Without Excel</option>
                    <option <?php echo $excelOnly == 1 ? 'selected=selected' : '' ?> value="1">Excel only</option>
                </select>
            </div>

            <div class="form-group mb-3 col-md-2">
                <label>Select User</label>
                {{ html()->select("task_assigned_to", ["" => "Select User"] + $users, request('task_assigned_to'))->class("form-control select22") }}
            </div>

            <div class="form-group mb-3 col-md-1">
                <label>Scrapers Status</label>
                <select name="scrapers_status" class="form-control form-group">
                    @foreach($scrapersStatus as $k => $v)
                        <option <?php echo request()->get('scrapers_status', '') == $k ? 'selected=selected' : '' ?> value="<?php echo $k; ?>"><?php echo $v; ?></option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-3 col-md-2">
                <label style="width:100%">&nbsp;</label>
                <button type="submit" class="btn btn-image"><img src="{{asset('/images/filter.png')}}"></button>
            </div>
        </div>
    </form>

    <?php $totalCountedUrl = 0; ?>
    <div id="status-count-container">
        @include('scrap.ajax.status', compact('allStatus', 'allStatusCounts'))
    </div>

    <div class="row">
        <div class="col col-md-12">
            <div class="row">
                <div class="col-md-12 mt-1">
                    <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#addChildScraper">
                      <span class="glyphicon glyphicon-th-plus"></span> Add Child Scraper
                    </button>
                
                    <button type="button" class="btn btn-default btn-sm add-remark" data-toggle="modal" data-target="#addRemarkModal">
                      <span class="glyphicon glyphicon-th-plus"></span> Add Note
                    </button>
                
                    <a href="{{ route('scrap.auto-restart') }}?status=on">
                        <button type="button" class="btn btn-default btn-sm auto-restart-all">
                            <span class="glyphicon glyphicon-th-list"></span> Auto Restart On
                        </button>
                    </a>
                
                    <button type="button" class="btn btn-default btn-sm get-latest-remark">
                      <span class="glyphicon glyphicon-th-list"></span> Latest Remarks
                    </button>
                
                    <a href="{{ route('scrap.latest-remark') }}?download=true">
                        <button type="button" class="btn btn-default btn-sm download-latest-remark">
                          <span class="glyphicon glyphicon-th-list"></span> Download Latest Remarks
                        </button>
                    </a>
                
                    <a href="{{ route('scrap.auto-restart') }}?status=off">
                        <button type="button" class="btn btn-default btn-sm auto-restart-all">
                            <span class="glyphicon glyphicon-th-list"></span> Auto Restart Off
                        </button>
                    </a>
                
                    <a href="javascript:void(0)">
                        <button type="button" class="btn btn-default btn-sm position-all">
                            <span class="glyphicon glyphicon-th-list"></span> Download Scraper position
                        </button>
                    </a>

                    <!-- START - Purpose : Add Button - DEVTASK-20102-->
                    <a href="javascript:void(0)">
                        <button type="button" class="btn btn-default btn-sm scrapper_process_btn">
                            <span class=""></span>Scraper Process
                        </button>
                    </a>

                    <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#scrapdatatablecolumnvisibilityList">Column Visiblity</button>

                    <button type="button" class="btn btn-default btn-sm multiple-scrap-btn">Multiple Scrap</button> 

                    <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#status-create">Add Status</button>
                    <!-- END - DEVTASK-20102-->
                </div>
            </div>
        </div>   
    </div>

    @include('scrap.partials.column-visibility-modal', compact('dynamicColumnsToShows'))

    <div class="row mt-3">
        <div class="col-md-12">
            <table class="table table-bordered table-striped sort-priority-scrapper">
                <thead>
                <tr>
                    <th>Scraper History</th>
                    <?php for ($i = 0; $i < 7; $i++) {

                        if (! isset($date)) {
                            $date = date('Y-m-d');
                        }
                        echo '<th>'.$date.' <button style="padding-right:0px;" type="button" class="btn btn-xs show-scraper-history" title="Show Scraper server history"  data-date="'.$date.'"><i class="fa fa-info-circle"></i></button>
                        <button style="padding-right:0px;" type="button" class="btn btn-xs show-scraper-process" title="Show Scraper process history"  data-date="'.$date.'"><i class="fa fa-gear"></i></button></th>';
                        $date = date('Y-m-d', strtotime('-1 day', strtotime($date)));
                        // code...
                    } ?>
                </tr>
                </thead>
            </table>
        </div>    
    </div>

    <div id="table-data-container">
        @include('scrap.ajax.stats', compact('allStatus', 'allStatusCounts', 'activeSuppliers', 'serverIds', 'scrapeData', 'users', 'allScrapperName', 'timeDropDown', 'lastRunAt', 'allScrapper', 'getLatestOptimization', 'scrapper_total', 'dynamicColumnsToShows'))
    </div>

    <div id="addRemarkModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Note</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{ route('scrap/add/note') }}" method="POST" enctype="multipart/form-data" id="add-note-form">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label>Scraper Name</label>
                            <select name="scraper_name" class="form-control select22" required>
                                @forelse ($allScrapper as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Note</label>
                            <textarea rows="2" name="remark" class="form-control" placeholder="Note" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Screenshot</label>
                            <input type="file" class="form-control" name="image">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-default">Submit</button>
                    </div>
                </form>
            </div>
        </div>
      </div>



      <div id="remarkHistory" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Remark History</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                    <div class="modal-body" id="remark-history-content">
                      
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
            </div>
        </div>
      </div>


    <div id="addChildScraper" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Child Scraper</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{ route('save.childrenScraper') }}" method="POST" id="add-child-scraper-form">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label>Select Scraper</label>
                            <select name="scraper_name" class="form-control select22" required>
                                @forelse ($allScrapper as $k => $item)
                                    <option value="{{ $item }}#{{$k}}">{{ $item }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Scraper Name</label>
                            <input type="integer" name="name" class="form-control">
                        </div>
                        <div class="form-group">
                            <strong>Run Gap:</strong>
                            <input type="integer" name="run_gap" class="form-control">
                        </div>
                        <div class="form-group">
                            <strong>Start Time:</strong>
                            <div class="input-group">
                                {{ html()->select("start_time", ['' => "--Time--"] + $timeDropDown, '')->class("form-control start_time select22")->style("width:100%;") }}
                            </div>
                        </div>
                        <div class="form-group">
                            <strong>Made By:</strong>
                            <div class="form-group">
                                {{ html()->select("scraper_made_by", ["" => "N/A"], '')->class("form-control scraper_made_by globalSelect2")->style("width:100%;")->data('ajax', route('select2.user'))->data('placeholder', 'Made by') }}
                            </div>
                        </div>
                        <div class="form-group">
                            <strong>Server Id:</strong>
                            <div class="form-group">
                                {{ html()->text("server_id", '')->class("form-control server-id-update") }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-default">Submit</button>
                    </div>
                </form>
            </div>
        </div>
      </div>  
      <div id="show-content-model" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </div>
      </div>
      <div id="show-content-model-table" class="modal fade scrp-task-list" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                       
                    </div>
                </div>
            </div>
      </div>
      <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                    <input type="text" name="search_chat_pop"  class="form-control search_chat_pop" placeholder="Search Message" style="width: 200px;">
                    <input type="hidden" id="chat_obj_type" name="chat_obj_type">
                    <input type="hidden" id="chat_obj_id" name="chat_obj_id">
                    <button type="submit" class="btn btn-default downloadChatMessages">Download</button>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div id="remark-confirmation-box" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Note</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="?" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label>Note</label>
                            <textarea id="confirmation-remark-note" rows="2" name="remark" class="form-control" placeholder="Note" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-default btn-confirm-remark">Submit</button>
                    </div>
                </form>
            </div>
        </div>
      </div>
    </div>

      <!-- Modal -->
    <div class="modal fade" id="scrapper_process_log" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">Scraper Process List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 610px;overflow: auto;">
                <table class="table table-bordered table-striped scraper-process" style="table-layout: fixed;">
                    <thead>
                        <tr>
                            <th style="width:15%">No.</th>
                            <th style="width:40%">Scraper Name</th>
                            <th style="width:45%">Status</th>
                            <th style="width:45%">Assign To </th>
                        </tr>
                    </thead>
                    <tbody class="ScraperProcess">
                        
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
    </div>

    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
               50% 50% no-repeat;display:none;">
    </div>

    <div id="status-create" class="modal fade in" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Stauts</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form  method="POST" id="status-create-form">
                    @csrf
                    @method('POST')
                    <div class="modal-body">
                        <div class="form-group">
                            {{ html()->label('Name', 'status_name')->class('form-control-label') }}
                            {{ html()->text('status_name')->class('form-control')->required()->attribute('rows', 3) }}
                        </div>
                        <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary status-save-btn">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('scripts')

    <script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-datepicker.min.js') }} "></script>
    <!-- <script type="text/javascript" src="{{ mix('webpack-dist/js/jquery-ui.js') }} "></script> -->
    @include('partials.script_developer_task')

    <script type="text/javascript">
    
        function saveAssignedTo(fieldId, scrapperId) {
            var assignedTo = $('#'+fieldId).val();
            if(assignedTo == '') {
                alert('Please select user.');
                return false;
            }
            $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{route('scrap.assign')}}",
                    method: "POST",
                    data: {scrapper_id: scrapperId, assigned_to: assignedTo},
                    success: function (response) {
                        toastr['success']('Scrapper assigned.');
                    },
                });
        }

        $(".total-info").html("({{$totalCountedUrl}})");

         $(document).on("change", ".quickComments", function (e) {
            var message = $(this).val();
            var select = $(this);

            if ($.isNumeric(message) == false) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{url('scrap/statistics/reply/add')}}",
                    dataType: "json",
                    method: "POST",
                    data: {reply: message}
                }).done(function (data) {
                    var vendors_id =$(select).find("option[value='']").data("vendorid");
                    var message_re = data.data.reply;
                    $("textarea#messageid_"+vendors_id).val(message_re);

                    console.log(data)
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });
            }
            //$(this).closest("td").find(".quick-message-field").val($(this).find("option:selected").text());
            var vendors_id =$(select).find("option[value='']").data("vendorid");
            var message_re = $(this).find("option:selected").html();

            $("textarea#messageid_"+vendors_id).val($.trim(message_re));

        });

        

        //$(".scrapers_status").select2();

        $(document).on("click", ".toggle-class", function () {
            $(".hidden_row_" + $(this).data("id")).toggleClass("dis-none");
        });

        $(document).on("keyup",".table-full-search",function() {
            var input, filter, table, tr, td, i, txtValue;
              input = document.getElementById("table-full-search");
              filter = input.value.toUpperCase();
              table = document.getElementById("latest-remark-records");
              tr = table.getElementsByTagName("tr");

              // Loop through all table rows, and hide those who don't match the search query
              for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0];
                if (td) {
                  txtValue = td.textContent || td.innerText;
                  if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                  } else {
                    tr[i].style.display = "none";
                  }
                }
              }
        });

        $(document).on("click",".get-latest-remark",function(e) {
            $.ajax({
                type: 'GET',
                url: '{{ route('scrap.latest-remark') }}',
                dataType:"json"
            }).done(response => {
                var html = '';
                var no = 1;
                if(response.code == 200) {
                    $.each(response.data, function (index, value) {
                        //Purpose : Add Index - DEVTASK-4219
                        var i = index + 1;
                        html += '<tr><td>' + i + '</td><td>' + value.scraper_name + '</td><td class="remark_created_at">' + moment(value.created_at).format('DD-M H:mm') + '</td><td>'+ value.inventory+'</td><td>'+(value.last_date?  moment(value.last_date).format('D-MMM-YYYY'):'-')  +'</td><td>'+ (value.log_messages?  value.log_messages.substr(0,19)+ (value.log_messages.length>19 ?   '...' : '  '):'-')+'</td><td class="remark_posted_by" >' + value.user_name + '</td><td class="remark_text">' + value.remark + '</td>';

                        //START - Purpose : Send Remark - DEVTASK-4219
                        if(value.user_name != '')
                        {
                            html += '<td>';
                            html += '<textarea rows="1" cols="25" class="form-control remark_text" name="remark" placeholder="Remark"></textarea>';
                            html += '<button class="btn btn-sm btn-image latestremarks_sendbtn" data-name="'+value.scraper_name+'"><img src="/images/filled-sent.png"></button>';
                            html += '<button style="padding:3px;" type="button" class="btn btn-image make-remark d-inline" data-toggle="modal" data-target="#makeRemarkModal" data-name="'+value.scraper_name+'"><img width="2px;" src="/images/remark.png"/></button>';
                            html += '</td>';
                        }else{
                            html += '<td></td>';
                        }

                        html += '</tr>';
                        //END - DEVTASK-4219

                        no++;
                    });
                    $("#latestRemark").find('.show-list-records').html(html);
                    $("#latestRemark").modal("show");
                }else{
                    toastr['error']('Oops, something went wrong', 'error');
                }
            });
        });

        //START - Purpose : Hide modal - DEVTASK-4219
        $("#makeRemarkModal").on('hide.bs.modal', function(){
            
           var remark_modal = $("#latestRemark").attr('class');
           if(remark_modal == 'modal fade in')
           {
               $(".modal-open .modal").css({"overflow-x": "auto","overflow-y": "auto"});
           }
        });
        //END - DEVTASK-4219

        $(document).on('click', '.make-remark', function (e) {
            e.preventDefault();

            var name = $(this).data('name');

            $('#add-remark input[name="id"]').val(name);

            $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('scrap.getremark') }}',
                data: {
                    name: name
                },
            }).done(response => {
                var html = '';
                var no = 1;
                $.each(response, function (index, value) {
                    /*html += '<li><span class="float-left">' + value.remark + '</span><span class="float-right"><small>' + value.user_name + ' updated on ' + moment(value.created_at).format('DD-M H:mm') + ' </small></span></li>';
                    html + "<hr>";*/
                    html += '<tr><td>' + value.remark + '</td><td>' + value.user_name + '</td><td>' + moment(value.created_at).format('DD-M H:mm') + '</td></tr>';
                    no++;
                });
                $("#makeRemarkModal").find('#remark-list').html(html);
            });
        });

        $(document).on('click', '.filter-auto-remark', function (e) {
            var name = $('#add-remark input[name="id"]').val();
            var auto = $(this).is(":checked");
            $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('scrap.getremark') }}',
                data: {
                    name: name,
                    auto : auto
                },
            }).done(response => {
                var html = '';
                var no = 1;
                $.each(response, function (index, value) {
                    /*html += '<li><span class="float-left">' + value.remark + '</span><span class="float-right"><small>' + value.user_name + ' updated on ' + moment(value.created_at).format('DD-M H:mm') + ' </small></span></li>';
                    html + "<hr>";*/
                    html += '<tr><td>' + value.remark + '</td><td>' + value.user_name + '</td><td>' + moment(value.created_at).format('DD-M H:mm') + '</td></tr>';
                    no++;
                });
                $("#makeRemarkModal").find('#remark-list').html(html);
            });
        });

        $('#scrapAddRemarkbutton').on('click', function () {
            var id = $('#add-remark input[name="id"]').val();
            var remark = $('#add-remark').find('textarea[name="remark"]').val();

            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('scrap.addRemark') }}',
                data: {
                    id: id,
                    remark: remark,
                    need_to_send: ($(".need_to_send").is(":checked")) ? 1 : 0,
                    inlcude_made_by: ($(".inlcude_made_by").is(":checked")) ? 1 : 0
                },
            }).done(response => {
                $('#add-remark').find('textarea[name="remark"]').val('');

                /*var html = '<li><span class="float-left">' + remark + '</span><span class="float-right">You updated on ' + moment().format('DD-M H:mm') + ' </span></li>';
                html + "<hr>";
*/
                var no = $("#remark-list").find("tr").length + 1;
                html = '<tr><td>' + remark + '</td><td>You</td><td>' + moment().format('DD-M H:mm') + '</td></tr>';
                $("#makeRemarkModal").find('#remark-list').append(html);
            }).fail(function (response) {
                alert('Could not fetch remarks');
            });

        });

        //START - Purpose : Send Remark - DEVTASK-4219
        $(document).on('click', '.latestremarks_sendbtn', function (e) {
            
            var id = $(this).data("name");
            var remark = $(this).siblings('.remark_text').val();

            if(remark == ''){
                toastr['error']('Remark field is required');
                return false;
            }
            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('scrap.addRemark') }}',
                data: {
                    id: id,
                    remark: remark,
                    need_to_send:  1 ,
                    inlcude_made_by: 1
                },
            }).done(response => {
                $(this).siblings('.remark_text').val('');
                toastr['success']('Remark Added Successfully', 'success');
                if(response.last_record != '')
                {
                    var data =  response.last_record;
                    // console.log(data);

                    $(this).closest('tr').children('.remark_created_at').html(moment(data.created_at).format('DD-M H:mm'));
                    $(this).closest('tr').children('.remark_posted_by').html(data.user_name);
                    $(this).closest('tr').children('.remark_text').html(data.remark);
                }
            }).fail(function (response) {
                alert('Could not fetch remarks');
            });
        });
        //END - DEVTASK-4219

        /*$(".sort-priority-scrapper").sortable({
            items: $(".sort-priority-scrapper").find(".history-item-scrap"),
            start: function (event, ui) {
                //console.log(ui.item);
            },
            update: function (e, ui) {

                var itemMoving = ui.item;
                var itemEle = itemMoving.data("id");
                var needToMove = $(".hidden_row_" + itemEle);
                needToMove.detach().insertAfter(itemMoving);

                var lis = $(".sort-priority-scrapper tbody tr");
                var ids = lis.map(function (i, el) {
                    return {id: el.dataset.id}
                }).get();
                $.ajax({
                    url: '/scrap/statistics/update-priority',
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    method: 'POST',
                    data: {
                        ids: ids,
                    }
                }).done(response => {
                    toastr['success']('Priority updated Successfully', 'success');
                }).fail(function (response) {
                });
            }
        });*/

        $(document).on("click", ".btn-set-priorities", function () {

        });

        $(document).on("change", ".start_time", function () {
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $.ajax({
                type: 'GET',
                url: '{{url("scrap/statistics/update-field")}}',
                data: {
                    search: id,
                    field: "scraper_start_time",
                    field_value: $(this).val()
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {

            });
        });

        $(document).on("change", ".scraper_field_change", function () {
            // var tr = $(this).closest("tr");
            var id = $(this).data("id");
            var field = $(this).data("field");
            var value = $(this).val();
            if(!value || value == '') {
                return;
            }
            $.ajax({
                type: 'GET',
                url: '{{url("scrap/statistics/update-scrap-field")}}',
                data: {
                    search: id,
                    field: field,
                    field_value: value
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {
                toastr['error']('Data not updated', 'error');
            });
        });

        
        $(document).on("click", ".show-history", function () {
            var id = $(this).data("id");
            var field = $(this).data("field");
            $.ajax({
                type: 'GET',
                url: '{{url("scrap/statistics/show-history")}}',
                data: {
                    search: id,
                    field: field
                },
            }).done(function (response) {
                $("#remarkHistory").modal("show");
                var table = '';
                if( field == "update-restart-time") {
                    table = table + '<table class="table table-bordered table-striped" ><tr><th>Date</th></tr>';
                }else{
                    table = table + '<table class="table table-bordered table-striped" ><tr><th>From</th><th>To</th><th>Date</th><th>By</th></tr>';
                }

                for(var i=0;i<response.length;i++) {
                    if( field == "update-restart-time") {
                        table = table + '<tr><td>'+response[i].new_value+'</td></tr>';
                    }else{
                        table = table + '<tr><td>'+response[i].old_value+'</td><td>'+response[i].new_value+'</td></td><td>'+response[i].created_at+'</td><td>'+response[i].user_name+'</td></tr>';
                    }    
                }
                table = table + '</table>';

                $("#remark-history-content").html(table);
            }).fail(function (response) {
            });
        });
        

        $(document).on("click", ".submit-logic", function () {
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $.ajax({
                type: 'GET',
                url: '{{url("scrap/statistics/update-field")}}',
                data: {
                    search: id,
                    field: "scraper_logic",
                    field_value: tr.find(".scraper_logic").val()
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {

            });
        });


        $(document).on("change", ".scraper_type", function () {
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $.ajax({
                type: 'GET',
                url: '{{url("scrap/statistics/update-field")}}',
                data: {
                    search: id,
                    field: "scraper_type",
                    field_value: tr.find(".scraper_type").val()
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {

            });
        });

        $(document).on("change", ".scraper_made_by", function () {
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $.ajax({
                type: 'GET',
                url: '{{url("scrap/statistics/update-field")}}',
                data: {
                    search: id,
                    field: "scraper_made_by",
                    field_value: tr.find(".scraper_made_by").val()
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {

            });
        });

        $(document).on("change", ".next_step_in_product_flow", function () {
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $.ajax({
                type: 'GET',
                url: '{{url("scrap/statistics/update-field")}}',
                data: {
                    search: id,
                    field: "next_step_in_product_flow",
                    field_value: tr.find(".next_step_in_product_flow").val()
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {

            });
        });

        $(document).on("change", ".scrapers_status", function (e) {
            e.preventDefault();
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $("#remark-confirmation-box").modal("show").on("click",".btn-confirm-remark",function(e) {
                e.preventDefault();
                 var remark =  $("#confirmation-remark-note").val();
                 if($.trim(remark) == "") {
                    alert("Please Enter remark");
                    return false;
                 }
                 $.ajax({
                    type: 'GET',
                    url: '{{url("scrap/statistics/update-field")}}',
                    data: {
                        search: id,
                        field: "status",
                        field_value: tr.find(".scrapers_status").val(),
                        remark : remark    
                    },
                }).done(function (response) {
                    toastr['success']('Data updated Successfully', 'success');
                    $("#remark-confirmation-box").modal("hide");
                }).fail(function (response) {
                });
            });

            return false;
        });

        $(document).on("change", ".full_scrape", function () {
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $.ajax({
                type: 'GET',
                url: '{{url("scrap/statistics/update-field")}}',
                data: {
                    search: id,
                    field: "full_scrape",
                    field_value: tr.find(".full_scrape").val()
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {

            });
        });

        $(document).on("change", ".auto_restart", function () {
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $.ajax({
                type: 'GET',
                url: '{{url("scrap/statistics/update-field")}}',
                data: {
                    search: id,
                    field: "auto_restart",
                    field_value: tr.find(".auto_restart").val()
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {

            });
        });

        $(document).on("change", ".parent_supplier_id", function () {
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $.ajax({
                type: 'GET',
                url: '{{url("scrap/statistics/update-field")}}',
                data: {
                    search: id,
                    field: "parent_supplier_id",
                    field_value: tr.find(".parent_supplier_id").val()
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {

            });
        });

        $(document).on("click",".server-id-update-btn",function() {
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $.ajax({
                type: 'GET',
                url: '{{url("scrap/statistics/update-field")}}',
                data: {
                    search: id,
                    field: "server_id",
                    field_value: tr.find(".server-id-update").val()
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {

            });
        });

        function restartScript(name,server_id) {
            var x = confirm("Are you sure you want to restart script?");
            if (x)
                  $.ajax({
                    url: '{{url("api/node/restart-script")}}',
                    type: 'POST',
                    dataType: 'json',
                    data: {name: name ,server_id : server_id, "_token": "{{ csrf_token() }}"},
                })
                .done(function(response) {
                    if(response.code == 200){
                        alert('Script Restarted Successfully')
                    }else{
                        alert('Please check if server is running')
                    }
                })
                .error(function() {
                    alert('Please check if server is running')
                });
            else
                return false;    
            
        }

        function updateScript(name,server_id, data_id) {
            var data_id = data_id;
            var x = confirm("Are you sure you want to update script?");
            if (x)
                  $.ajax({
                    url: '{{url("api/node/update-script")}}',
                    type: 'POST',
                    dataType: 'json',
                    data: {name: name ,server_id : server_id, "_token": "{{ csrf_token() }}"},
                })
                .done(function(response) {
                    if(response.code == 200){
                        alert('Script updated Successfully');
                        if(response.duration !== null){
                            $(`tr[data-id='${data_id}'] td:last-child .duration_display`).html(`${response.duration}`);
                            $(`tr[data-id='${data_id}'] td:last-child .duration_display`).css('display', 'inherit');
                            $(`tr[data-id='${data_id}'] td:last-child .duration_display`).removeClass('d-none'); 
                        }
                    }else{
                        alert('Please check if server is running')
                    }
                }) ;
            else
                return false;    
            
        }

        function killScript(name,server_id) {
            var x = confirm("Are you sure you want to kill script?");
            if (x)
                  $.ajax({
                    url: '{{url("api/node/kill-script")}}',
                    type: 'POST',
                    dataType: 'json',
                    data: {name: name ,server_id : server_id, "_token": "{{ csrf_token() }}"},
                })
                .done(function(response) {
                    if(response.code == 200){
                        alert('Script killed Successfully')
                    }else{
                        alert('Please check if server is running')
                    }
                }) ;
            else
                return false;    
            
        }


        function getRunningStatus(name,server_id) {
            var x = confirm("Are you sure you want to restart script?");
            if (x)
                  $.ajax({
                    url: '{{url("api/node/get-status")}}',
                    type: 'POST',
                    dataType: 'json',
                    data: {name: name ,server_id : server_id, "_token": "{{ csrf_token() }}"},
                })
                .done(function(response) {
                    if(response.code == 200){
                        alert(response.message)
                    }else{
                        alert('Please check if server is running')
                    }
                })
                .error(function() {
                    alert('Please check if server is running')
                });
            else
                return false;    
            
        }


        function showHidden(name) {
            $("."+name).toggle();
        }


        $(".select2").select2();

        $(document).on("click",".show-scraper-detail",function (e){
            e.preventDefault();
            var startime = $(this).data("start-time");
            var endtime = $(this).data("end-time");

            var model  = $("#show-content-model");
            var html = `<div class="row">
                <div class="col-md-12">
                    <p>Star Time : `+startime+`</p>
                    <p>End Time : `+endtime+`</p>
                </div>
            </div>`;
            model.find(".modal-title").html("Scraper Start time details");
            model.find(".modal-body").html(html);
            model.modal("show");
        });

        $(document).on("click",".get-screenshot",function (e){
            e.preventDefault();
            var id = $(this).data("id");
            $.ajax({
                url: '{{url("scrap/screenshot")}}',
                type: 'GET',
                data: {id: id},
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Scraper screenshots");
                model.find(".modal-body").html(response);
                model.modal("show");
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });

        $(document).on("click",".get-last-errors",function (e){
            e.preventDefault();
            var id = $(this).data("id");
            $.ajax({
                url: '{{url("scrap/get-last-errors")}}',
                type: 'GET',
                data: {id: id},
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Last Errors");
                model.find(".modal-body").html(response);
                model.modal("show");
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });

        $(document).on("click",".show-scraper-history",function (e){
            e.preventDefault();
            var date = $(this).data("date");
            $.ajax({
                url: '{{url("scrap/server-status-history")}}',
                type: 'GET',
                data: {date: date},
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Server status history");
                model.find(".modal-body").html(response);
                model.modal("show");
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });

        $(document).on("click",".show-scraper-process",function (e){
            e.preventDefault();
            var date = $(this).data("date");
            $.ajax({
                url: '{{url("scrap/server-status-process")}}',
                type: 'GET',
                data: {date: date},
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Server process history");
                model.find(".modal-body").html(response);
                model.modal("show");
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });

        

        
        $(document).on("click",".get-tasks-killed",function (e){
            e.preventDefault();
            var id = $(this).data("id");
            $.ajax({
                url: '{{url("scrap/killed-list")}}',
                type: 'GET',
                data: {id: id},
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Scraper Killed Histories List");
                model.find(".modal-body").html(response);
                model.modal("show");
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });


        $(document).on("click",".get-position-history",function (e){
            e.preventDefault();
            var id = $(this).data("id");
            $.ajax({
                url: '{{url("scrap/position-history")}}',
                type: 'GET',
                data: {id: id},
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Scraper Position History");
                model.find(".modal-body").html(response);
                model.modal("show");
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });

        $(document).on("click",".get-scraper-server-timing",function (e){
            e.preventDefault();
            var scraper_name = $(this).data("name");
            $.ajax({
                url: '{{url("scrap/get-server-scraper-timing")}}',
                type: 'GET',
                data: {scraper_name: scraper_name},
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Scraper Server History");
                model.find(".modal-body").html(response);
                model.modal("show");
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });



        
        $(document).on("click",".flag-scraper",function (e){
            e.preventDefault();
            var flag = $(this).data("flag");
            var id = $(this).data("id");
            var $this =  $(this);
            $.ajax({
                url: "{{url('scrap/statistics/update-field')}}",
                type: 'GET',
                data: {
                    search: id,
                    field: "flag",
                    field_value: flag
                },
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                 $("#loading-image").hide();
                 if(response.data.flag == 1) {
                    $this.closest(".flag-scraper-div").append('<button type="button" style="padding:1px;" class="btn btn-image flag-scraper" data-flag="0" data-id="'+response.data.supplier_id+'"><img src="/images/flagged.png" /></button>');
                 }else{
                    $this.closest(".flag-scraper-div").append('<button type="button" style="padding:1px;" class="btn btn-image flag-scraper" data-flag="1" data-id="'+response.data.supplier_id+'"><img src="/images/unflagged.png" /></button>');
                 }
                 $this.remove();
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });

        $(document).on("click",".flag-scraper-developer",function (e){
            e.preventDefault();
            var flag = $(this).data("flag");
            var id = $(this).data("id");
            var $this =  $(this);
            $.ajax({
                url: "{{url('scrap/statistics/update-field')}}",
                type: 'GET',
                data: {
                    search: id,
                    field: "developer_flag",
                    field_value: flag
                },
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                 $("#loading-image").hide();
                 if(response.data.developer_flag == 1) {
                    $this.closest(".flag-scraper-developer-div").append('<button type="button" style="padding:1px;" class="btn btn-image flag-scraper-developer" data-flag="0" data-id="'+response.data.supplier_id+'"><img src="/images/flagged-green.png" /></button>');
                 }else{
                    $this.closest(".flag-scraper-developer-div").append('<button type="button" style="padding:1px;" class="btn btn-image flag-scraper-developer" data-flag="1" data-id="'+response.data.supplier_id+'"><img src="/images/flagged-yellow.png" /></button>');
                 }
                 $this.remove();
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });


       
        $(document).on("click",".toggle-title-box",function(ele) {
            var $this = $(this);
            if($this.hasClass("has-small")){
                $this.html($this.data("full-title"));
                $this.removeClass("has-small")
            }else{
                $this.addClass("has-small")
                $this.html($this.data("small-title"));
            }
        });

        //STRAT - Purpose : Download  Position History - DEVTASK-4086
        $(document).on("click",".downloadPositionHistory",function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            $.ajax({
                url: "{{url('scrap/position-history-download')}}",
                type: 'POST',
                "dataType": 'json',           // what to expect back from the PHP script, if anything
                data: {
                    id: id,
                    "_token": "{{ csrf_token() }}"
                },
                beforeSend: function () {
                   
                }
            }).done(function (response) {
                
                if(response.downloadUrl){
                    var form = $("<form/>", 
                            { action:"{{url('chat-messages/downloadChatMessages')}}",
                                method:"POST",
                                target:'_blank',
                                id:"chatHiddenForm",
                                }
                        );
                    form.append( 
                        $("<input>", 
                            { type:'hidden',  
                            name:'filename', 
                            value:response.downloadUrl }
                        )
                    );
                    form.append( 
                        $("<input>", 
                            { type:'hidden',  
                            name:'_token', 
                            value:$('meta[name="csrf-token"]').attr('content') }
                        )
                    );
                    $("body").append(form);
                    $('#chatHiddenForm').submit();
                }else{
                    console.log('no message found !')
                }
               
            }).fail(function (errObj) {
                
            });
        });
        //END - DEVTASK-4086


        $(document).on("click",".position-all",function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{url('scrap/position-all')}}",
                type: 'POST',
                "dataType": 'json',           // what to expect back from the PHP script, if anything
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                beforeSend: function () {
                   
                }
            }).done(function (response) {
                
                if(response.downloadUrl){
                    var form = $("<form/>", 
                            { action:"{{url('chat-messages/downloadChatMessages')}}",
                                method:"POST",
                                target:'_blank',
                                id:"chatHiddenForm",
                                }
                        );
                    form.append( 
                        $("<input>", 
                            { type:'hidden',  
                            name:'filename', 
                            value:response.downloadUrl }
                        )
                    );
                    form.append( 
                        $("<input>", 
                            { type:'hidden',  
                            name:'_token', 
                            value:$('meta[name="csrf-token"]').attr('content') }
                        )
                    );
                    $("body").append(form);
                    $('#chatHiddenForm').submit();
                }else{
                    console.log('no message found !')
                }
               
            }).fail(function (errObj) {
                
            });
        });

        //START - Purpose : Add get data for scrappers - DEVTASK-20102
        $(document).on("click",".scrapper_process_btn",function(e) {
            

            $.ajax({
                type: 'GET',
                url: '{{url("scrap/logdata/view_scrappers_data")}}',
                // data: {
                //     search: id,
                //     field: "scraper_made_by",
                //     field_value: tr.find(".scraper_made_by").val()
                // },
            }).done(function (response) {
                // toastr['success']('Data updated Successfully', 'success');
                $('#scrapper_process_log').modal('show');
                $('.ScraperProcess').append(response);
            }).fail(function (response) {

            });
        });

        $(document).on("click",".multiple-scrap-btn",function() {
            var selectedCheckboxes = [];
            var fileIDs = [];

            $('input[name="scrap_check"]:checked').each(function() {
                var fileID = $(this).data('id');
                var checkboxValue = $(this).val();

                fileIDs.push(fileID);
                selectedCheckboxes.push(checkboxValue);
            });

            if (selectedCheckboxes.length === 0) {
                alert('Please select at least one checkbox.');
                return;
            }  

            var formData = {
                ids: selectedCheckboxes 
            };

            var x = confirm("Are you sure you want to full scrape on the selected records.");
            if (x){
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: '{{ route('scrap.multiple.update.field') }}',
                    data: formData,
                    success: function(response) {
                        toastr["success"]("Full Scrap status has been updated successfully");
                        location.reload();
                    },
                    error: function(error) {
                        console.error('Error:', error);
                        location.reload();
                    }
                });  
            }    
        });


        function reloadStatuses(statuses) {
            let options = ``;

            for (let [key, value] of Object.entries(statuses)) {
                options += `<option value="${key}">${value}</option>`;
            }

            $("[name=scrapers_status]").html(options);
        }


        $(document).on("click", ".status-save-btn", function(e) {
            e.preventDefault();
            var $this = $(this);
            $.ajax({
                url: "{{route('scrap.status.create')}}",
                type: "post",
                data: $('#status-create-form').serialize()
            }).done(function(response) {
                if (response.code = '200') {
                    $('#loading-image').hide();
                    $('#addPostman').modal('hide');
                    reloadStatuses(response.data);
                    $('#status-create').modal('hide');
                    toastr['success']('Status  Created successfully!!!', 'success');
                } else {
                    toastr['error'](response.message, 'error');
                }
            }).fail(function(errObj) {
                $('#loading-image').hide();
                toastr['error'](errObj.message, 'error');
            });
        });

        $(document).on('click', '.expand-row-msgg', function () {
            var name = $(this).data('name');
            var id = $(this).data('id');
            var full = '.expand-row-msgg .show-short-'+name+'-'+id;
            var mini ='.expand-row-msgg .show-full-'+name+'-'+id;
            $(full).toggleClass('hidden');
            $(mini).toggleClass('hidden');
        });
        //END - DEVTASK-20102


        /**
        * @param {string} url  - url string with or without page index (ex :- url?page=1)
        * @param {string} data - query string
        * @return null
        */
        function loadScrapperTableData(url, data) {
            $.ajax({
                url: url,
                method: "GET",
                data: data,
                beforeSend: function () {
                    $('#loading-image').show();
                },
                success: function (resp) {
                    $('#table-data-container').html(resp);
                    $('#loading-image').hide();
                },
                error: function (error) {
                    $('#loading-image').hide();
                }
            });
        }


        /**
        * @param {string} url  - url string with or without page index (ex :- url?page=1)
        * @param {string} data - query string
        * @return null
        */
        function loadStatusCounts(url, data) {
            $.ajax({
                url: url,
                method: "GET",
                data: data != '' ? data + '&load-status=1' : 'load-status=1',
                beforeSend: function () {
                    $('#loading-image').show();
                },
                success: function (resp) {
                    $('#status-count-container').html(resp);
                    $('#loading-image').hide();
                },
                error: function (error) {
                    $('#loading-image').hide();
                }
            });
        }


        $(document).on("submit", "#statistics-filter-page", function (e) {
            e.preventDefault();

            loadScrapperTableData(
                "/scrap/statistics",
                $(this).serialize()
            );

            loadStatusCounts(
                "/scrap/statistics",
                $(this).serialize()
            );
        });


        $(document).on('click', '.pagination a', function(event){
            event.preventDefault(); 
            let page = $(this).attr('href').split('page=')[1];

            loadScrapperTableData(
                "/scrap/statistics?page=" + page, 
                '',
            );

            loadStatusCounts(
                "/scrap/statistics",
                ''
            );
        });


        $(document).on("submit", "#add-child-scraper-form", function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('save.childrenScraper') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function (resp) {
                    switch(resp.status) {
                    case "success":
                        toastr.success(resp.msg);
                        $('#addChildScraper').modal('hide');
                        break;
                    case "error":
                        toastr.error(resp.msg);
                        break;
                    }
                },
                error: function (error) {
                    for (let msg of Object.values(error.responseJSON.errors)) {
                        toastr.error(msg);
                    }
                },
            });
        });


        $(document).on("submit", "#add-note-form", function (e) {
            e.preventDefault();
            
            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('scrap/add/note') }}",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                processData: false,
                contentType: false, 
                data: formData,
                success: function (resp) {
                    switch(resp.status) {
                    case "success":
                        toastr.success(resp.msg);
                        $('#addRemarkModal').modal('hide');
                        break;
                    case "error":
                        toastr.error(resp.msg);
                        break;
                    }
                },
                error: function (error) {
                    toastr.error(error.msg);
                },
            });
        });

        $(document).on('submit', '#scrap-column-update', function (e) {
            e.preventDefault();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('scrap.column.update') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function (resp) {
                    switch(resp.status) {
                    case "success":
                        loadScrapperTableData(
                            "/scrap/statistics",
                            $('#statistics-filter-page').serialize(),
                        );

                        loadStatusCounts(
                            "/scrap/statistics",
                            $('#statistics-filter-page').serialize(),
                        );

                        toastr.success(resp.msg);
                        $('#scrapdatatablecolumnvisibilityList').modal('hide');

                        break;
                    case "error":
                        toastr.error(resp.msg);
                        break;
                    }
                },
                error: function (error) {
                    toastr.error(error.message);
                },
            });
        });

    </script>
@endsection
