@extends('layouts.app')

@section('title','Product pricing')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
<link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>.hidden {
    display:none;
}
.btn-secondary, .btn-secondary:hover, .btn-secondary:focus{
        background: #fff;
        color: #757575;
        border: 1px solid #ddd;
        outline: none;
        box-shadow: none;
    }
  .shortTable{
    cursor: pointer;
  }
</style>
<div class = "row m-0">
    <div class="col-lg-12 margin-tb p-0">
        <h2 class="page-heading">Product pricing({{$magentoStatusCount}})</h2>
    </div>
</div>
<div class = "row m-0">
    <div class="pl-3 pr-3 margin-tb">
        <div class="pull-left cls_filter_box">
            <form class="form-inline filter_form" action="" method="GET">
                <div class="form-group mr-3">
                    <select class="form-control globalSelect2" data-placeholder="Select Websites" name="website" id="magentowebsite">
                         <option value="">Select Websites</option>
                    @php
                    $selectcate ='';
                    if(isset($_GET['website'])){
                      $selectcate =$_GET['website'];
                    }
                    @endphp
                        @if ($magentoCronWebsites)
                            @foreach($magentoCronWebsites as $id => $web)
                                <option value="{{ $web }}" @if($selectcate == $web) selected @endif  >{{ $web }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group mr-3">
                    <select class="form-control globalSelect2" data-placeholder="Select Job Code" name="job_code" id="magentojobcode">
                         <option value="">Select Job Code</option>
                    @php
                    $select_job_code ='';
                    if(isset($_GET['job_code'])){
                      $select_job_code =$_GET['job_code'];
                    }
                    @endphp
                        @if ($magentoCronJobCodes)
                            @foreach($magentoCronJobCodes as $id => $code)
                                <option value="{{ $code }}" @if($select_job_code == $code) selected @endif  >{{ $code }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group mr-3">
                    <select class="form-control globalSelect2" data-placeholder="Select Status" name="status" id="magentowebsitestatus">
                        <option value="">Select Status</option>
                    @php
                    $selectcate ='';
                    if(isset($_GET['status'])){
                      $selectcate =$_GET['status'];
                    }
                    @endphp
                        @if ($status)
                            @foreach($status as $sat)
                                <option value="{{ $sat->name }}" @if($selectcate == $sat->name) selected @endif  >{{ $sat->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group mr-3">
                    <input name="create_at" type="text" class="form-control" value="" placeholder="Select Date Range" id="term">
                </div>
                <div class="form-group mr-3">
                    <input type="text" name="jobcode" class="form-control" placeholder="Search by jobcode">
                </div>
                <div class="form-group mr-3">
                <select name="sort_by" id="sort_by" class="form-control globalSelect" data-placeholder="Sort By">
                    <option  Value="created_at">Created At sort By</option>
                    <option value="scheduled_at">Scheduled At sort By</option>
                    <option value="executed_at">Executed At sort By</option>
                    <option value="finished_at">Finished At sort By</option>
                    </select>
                </div>

                <div class="form-group mr-3">
                    <button type="submit" class="btn btn-secondary">Search</button>
                    <a href="/show-magento-cron-data" class="btn btn-secondary" id="">
                        Reset
                    </a>
                 </div>
            </form> 
            <div class="form-inline mr-3">
                <button class="btn btn-secondary my-3" data-toggle="modal" data-target="#cronStatusColor"> Status Color</button>
            </div>
        </div>
    </div>
</div>

<div class="row m-0">
    <div class="col-lg-12"> 
        <div class="panel-group" style="margin-bottom: 5px;">
            <div class="table-responsive">
                   <table class="table table-bordered table-striped" id="product-price" style="table-layout: fixed">
                       <thead>
                       <tr>
                           <th style="width: 7%">website
                            <!-- <i class="fa fa-arrow-up shortTable cursor-pointer" data-input="category" data-order="asc" aria-hidden="true"></i>
                            <i class="fa fa-arrow-down shortTable cursor-pointer"data-input="category" data-order="desc" aria-hidden="true"></i> -->
                           </th>
                           <th style="width: 7%">Cron ID 
                            <!-- <i class="fa fa-arrow-up shortTable cursor-pointer" data-input="website" data-order="asc" aria-hidden="true"></i>
                            <i class="fa fa-arrow-down shortTable cursor-pointer" data-input="website" data-order="desc" aria-hidden="true"></i> -->
                           </th>
                           <th style="width: 7%">Job Code
                            <!--  <i class="fa fa-arrow-up shortTable cursor-pointer" data-input="bsegment" data-order="asc" aria-hidden="true"></i>
                            <i class="fa fa-arrow-down shortTable cursor-pointer" data-input="bsegment" data-order="desc" aria-hidden="true"></i> -->
                           </th>
                           <th style="width: 4%;word-break: break-all">Cron Message</th>
                           <th style="width: 5%">Cron Status
                            <!-- <i class="fa fa-arrow-up shortTable cursor-pointer" data-input="csegment" data-order="asc" aria-hidden="true"></i>
                            <i class="fa fa-arrow-down shortTable cursor-pointer" data-input="csegment" data-order="desc" aria-hidden="true"></i> -->
                           </th>
                           <th style="width: 5%">Created at</th>
                        
                           <th style="width: 5%">Scheduled at</th>
                           <th style="width: 5%">Executed at</th>
                           <th style="width: 5%">Finished at</th>
                           <th style="width: 5%">Actions</th>
                       </tr>
                       </thead>
                       <tbody>
                       @php $i=1; @endphp
                       @foreach ($data as $dat) 
                           <tr  style="background-color: {{$dat?->cronStatus?->color}}!important;" data-id="{{$i}}" class="tr_{{$i++}}">
                               <td class="expand-row" style="word-break: break-all">
                                   <span class="td-mini-container">
                                      {{ strlen( $dat['website']) > 22 ? substr( $dat['website'], 0, 22).'...' :  $dat['website'] }}
                                   </span>

                                   <span class="td-full-container hidden">
                                       {{ $dat['website'] }}
                                   </span>
                                </td>

                                <td class="expand-row" style="word-break: break-all">
                                   <span class="td-mini-container">
                                      {{ strlen( $dat['cron_id']) > 9 ? substr( $dat['cron_id'], 0, 8).'...' :  $dat['cron_id'] }}
                                   </span>

                                   <span class="td-full-container hidden">
                                       {{ $dat['cron_id'] }}
                                   </span>
                                </td>

                               <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                                {{ strlen( $dat['job_code']) > 18 ? substr( $dat['job_code'], 0, 18).'...' :  $dat['job_code'] }}
                                     </span>

                                   <span class="td-full-container hidden">
                                               {{ $dat['job_code'] }}
                                            </span>
                               </td>
                               <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                                {{ strlen( $dat['cron_message']) > 15 ? substr( $dat['cron_message'], 0, 15).'...' :  $dat['cron_message'] }}
                                     </span>

                                   <span class="td-full-container hidden">
                                               {{ $dat['cron_message'] }}
                                            </span>
                               </td>
                            
                               <td>{{ $dat['cronstatus'] }}</td>

                               <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                                {{ strlen( $dat['cron_created_at']) > 15 ? substr( $dat['cron_created_at'], 0, 15).'...' :  $dat['cron_created_at'] }}
                                     </span>

                                   <span class="td-full-container hidden">
                                               {{ $dat['cron_created_at'] }}
                                            </span>
                               </td>

                               <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                                {{ strlen( $dat['cron_scheduled_at']) > 15 ? substr( $dat['cron_scheduled_at'], 0, 15).'...' :  $dat['cron_scheduled_at'] }}
                                     </span>

                                   <span class="td-full-container hidden">
                                               {{ $dat['cron_scheduled_at'] }}
                                            </span>
                               </td>

                               <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                                {{ strlen( $dat['cron_executed_at']) > 15 ? substr( $dat['cron_executed_at'], 0, 15).'...' :  $dat['cron_executed_at'] }}
                                     </span>

                                   <span class="td-full-container hidden">
                                               {{ $dat['cron_executed_at'] }}
                                            </span>
                               </td>

                               <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                                {{ strlen( $dat['cron_finished_at']) > 15 ? substr( $dat['cron_finished_at'], 0, 15).'...' :  $dat['cron_finished_at'] }}
                                     </span>

                                   <span class="td-full-container hidden">
                                               {{ $dat['cron_finished_at'] }}
                                            </span>
                               </td>
                               <td class="expand-row" style="word-break: break-all">
                                    <a title="Run Cron" class="btn btn-image magentoCom-run-btn pd-5     btn-ht" data-id="{{ $dat['id']}}" href="javascript:;">
                                        <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                    </a>
                                    <a title="Preview Response" data-id="{{ $dat['id']}}" class="btn btn-image preview_response pd-5 btn-ht" href="javascript:;">
                                        <i class="fa fa-product-hunt" aria-hidden="true"></i>
                                    </a>
                               </td>

                           </tr> 
                       @endforeach
                       </tbody>
                   </table>
                   <img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..." style="display: none" />
              </div>
        </div>
    </div>
</div>
@include("magento_cron_data.partials.modal-status-color")

<div id="commandResponseHistoryModel" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 100%;max-width: 95%;">
        <!-- Modal content-->
        <div class="modal-content ">
            <div id="add-mail-content">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Cron Command Response History</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">ID</th>
                                    <th style="width: 5%;overflow-wrap: anywhere;">Command Id</th>
                                    <th style="width: 10%;overflow-wrap: anywhere;">User Name</th>
                                    <th style="width: 10%;overflow-wrap: anywhere;">Website</th>
                                    <th style="width: 10%;overflow-wrap: anywhere;">Working Directory</th>
                                    <th style="width: 20%;overflow-wrap: anywhere;">Command Name</th>
                                    <th style="width: 20%;overflow-wrap: anywhere;">Response</th>
                                    <th style="width: 5%;overflow-wrap: anywhere;">Date</th>
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
@endsection
@section('scripts')
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(function() {
        $('input[name="create_at"]').daterangepicker({
            opens: 'left'
        }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });
        $('input[name="create_at"]').val("");
        $('input[name="create_at"]').on('cancel.daterangepicker', function(ev, picker) {
            picker.setStartDate({});
            picker.setEndDate({});
            $(this).val('');
        });
    });
    $(document).ready(function () {
        $(document).on('click', '.expand-row-msg', function() {
        var name = $(this).data('name');
        var id = $(this).data('id');
        var full = '.expand-row-msg .show-short-' + name + '-' + id;
        var mini = '.expand-row-msg .show-full-' + name + '-' + id;
        $(full).toggleClass('hidden');
        $(mini).toggleClass('hidden');
    });
        $(document).on("click", ".preview_response", function(e) {
        e.preventDefault();
        var $this = $(this);
        var id = $this.data('id');
        $.ajax({
            url: "/show-magento-cron-data/history"
            , type: "post"
            , headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , data: {
                id: id
            },
            beforeSend: function() {
                $('#loading-image-preview').show();
            },
        }).done(function(response) {
            if (response.code == '200') {
                var t = '';
                $.each(response.data, function(key, v) {
                    var responseString = '';
                    if (v.response)
                        responseString = v.response.substring(0, 10);
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
                    t += '<td>' + v.command_id + '</td>';
                    t += '<td>' + v.userName + '</td>';
                    t += '<td>' + v.website + '</td>';
                    t += '<td>' + v.working_directory + '</td>';
                    t += '<td  class="expand-row-msg" data-name="command" data-id="' + v.id + '" ><span class="show-short-command-' + v.id + '">' + commandString + '...</span>    <span style="word-break:break-all;" class="show-full-command-' + v.id + ' hidden">' + v.command_name + '</span></td>';
                    t += '<td  class="expand-row-msg" data-name="response" data-id="' + v.id + '" ><span class="show-short-response-' + v.id + '">' + responseString + '...</span>    <span style="word-break:break-all;" class="show-full-response-' + v.id + ' hidden">' + v.response + '</span></td>';
                    //t += '<td>'+v.response_code+'</td>';
                    //t += '<td  class="expand-row-msg" data-name="request_url" data-id="'+v.id+'" ><span class="show-short-request_url-'+v.id+'">'+request_url_val+'...</span>    <span style="word-break:break-all;" class="show-full-request_url-'+v.id+' hidden">'+v.request_url+'</span></td>';
                    //t += '<td  class="expand-row-msg" data-name="request_data" data-id="'+v.id+'" ><span class="show-short-request_data-'+v.id+'">'+request_data_val+'...</span>    <span style="word-break:break-all;" class="show-full-request_data-'+v.id+' hidden">'+v.request_data+'</span></td>';
                    t += '<td>' + v.created_at + '</td></tr>';
                });
                $(".tbodayCommandResponseHistory").html(t);
                $('#commandResponseHistoryModel').modal('show');
                

            } else {
                toastr['error'](response.message, 'error');
            }
            $('#loading-image-preview').hide();
        }).fail(function(errObj) {
            $('#loading-image-preview').hide();
            $("#commandResponseHistoryModel").hide();
            toastr['error'](errObj.message, 'error');
        });
    });

        $(document).on("click", ".magentoCom-run-btn", function(e) {
            e.preventDefault();
            var $this = $(this);
            var id = $this.data('id');
            $.ajax({
                url: "/show-magento-cron-data/run-magento-cron"
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
                if (response.code == '200') {
                    toastr['success'](response.message, 'success');
                } else {
                    toastr['error'](response.message, 'error');
                }
                $('#loading-image').hide();
            }).fail(function(errObj) {
                $('#loading-image').hide();
                if (errObj ?.responseJSON ?.message) {
                    toastr['error'](errObj.responseJSON.message, 'error');
                    return;
                }
                toastr['error'](errObj.message, 'error');
            });
        });    
        

      $(".filter_form").submit(function (event) {
        page = 0; // Every time form submit, Reset page from 1.
        event.preventDefault();
        let data = $('.filter_form').serialize();

        page = page + 1;
        $('#loading-image-preview').show();
        $.ajax({
        url: "{{url('/show-magento-cron-data')}}?page="+ page + '&count=' + {{$i}} + '&' + data,
        type: 'GET',
        data: $('.filter_form').serialize(),
        success: function (data) {
            console.log(data);
            $('#loading-image-preview').hide();
            // $loader.hide();
             $('#product-price tbody').html($.trim(data['html']));
            // isLoading = false;
        },
        error: function () {
            $('#loading-image-preview').hide();
            // $loader.hide();
            // isLoading = false;
        }
    });

      });
    });


    var isLoading = false;
    var page = 1;
    $(document).ready(function () {
        
        $(window).scroll(function() {
            if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
              loadMore();
            }
        });

        let data = $('.filter_form').serialize();

        function loadMore() {
            if (isLoading)
                return;
            isLoading = true;
            var $loader = $('.infinite-scroll-products-loader');
            page = page + 1;

            var url  = new URL(window.location.href);
            var search_params = url.searchParams;
          // add "topic" parameter
            search_params.set('page', page);
            search_params.set('count',{{$i}});
            url.search = search_params.toString();
            var new_url = url.toString();

            $.ajax({
                url: new_url + '&' + data,
                type: 'GET',
                data: $('.filter_form').serialize(),
                beforeSend: function() {
                    $loader.show();
                },
                success: function (data) {
                    $loader.hide();
                    $('#product-price tbody').append($.trim(data['html']));
                    isLoading = false;
                },
                error: function () {
                    $loader.hide();
                    isLoading = false;
                }
            });
        }  

    $(document).on('click', '.shortTable',function(){
      var $loader = $('#loading-image-preview');
      $loader.show();
      var order = $(this).data('order');
      var input = $(this).data('input');

      var url  = new URL(window.location.href);
      var search_params = url.searchParams;

      search_params.set('order', order);
      search_params.set('input', input);

      url.search = search_params.toString();
      var new_url = url.toString();

      window.history.pushState("", "Title", new_url);
      $.ajax({
        url: new_url,
        type: 'GET',
        data: {},
        // beforeSend: function() {
        //   $loader.show();
        // },
        success: function (data) {
            $loader.hide();
            $('#tbody').html($.trim(data['html']));
            isLoading = false;
        },
        error: function () {
            $loader.hide();
            isLoading = false;
        } 
      });

    });


    });

$(document).on('click', '.expand-row', function () {
  var selection = window.getSelection();
  if (selection.toString().length === 0) {
    $(this).find('.td-mini-container').toggleClass('hidden');
    $(this).find('.td-full-container').toggleClass('hidden');
  }
});


</script>

@endsection