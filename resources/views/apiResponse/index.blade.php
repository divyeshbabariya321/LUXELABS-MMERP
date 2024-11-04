@extends('layouts.app')

@section('link-css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.jqueryui.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/scroller/2.0.1/css/scroller.jqueryui.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" /> --}}
    <style>


        /* */


        .panel-default > .panel-heading {
            color: #333;
            background-color: #fff;
            border-color: #e4e5e7;
            padding: 0;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .panel-default > .panel-heading a {
            display: block;
            padding: 10px 15px;
        }

        .panel-default > .panel-heading a:after {
            content: "";
            position: relative;
            top: 1px;
            display: inline-block;
            font-family: 'Glyphicons Halflings';
            font-style: normal;
            font-weight: 400;
            line-height: 1;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            float: right;
            transition: transform .25s linear;
            -webkit-transition: -webkit-transform .25s linear;
        }

        .panel-default > .panel-heading a[aria-expanded="true"] {
            background-color: #eee;
        }

        .panel-default > .panel-heading a[aria-expanded="true"]:after {
            content: "\2212";
            -webkit-transform: rotate(180deg);
            transform: rotate(180deg);
        }

        .panel-default > .panel-heading a[aria-expanded="false"]:after {
            content: "\002b";
            -webkit-transform: rotate(90deg);
            transform: rotate(90deg);
        }

        .full-rep {
            padding-bottom: 15px;
            width: 100%;
            display: inline-block;
        }

        form label.required:after {
            color: red;
            content: ' *';
        }

        /*PRELOADING------------ */
        #overlayer {
            width: 100%;
            height: 100%;
            position: absolute;
            z-index: 1;
            background: #4a4a4a33;
        }

        .loader {
            display: inline-block;
            width: 30px;
            height: 30px;
            position: absolute;
            z-index: 3;
            border: 4px solid #Fff;
            top: 50%;
            animation: loader 2s infinite ease;
            margin-left: 50%;
        }

        .loader-inner {
            vertical-align: top;
            display: inline-block;
            width: 100%;
            background-color: #fff;
            animation: loader-inner 2s infinite ease-in;
        }

        @keyframes loader {
            0% {
                transform: rotate(0deg);
            }

            25% {
                transform: rotate(180deg);
            }

            50% {
                transform: rotate(180deg);
            }

            75% {
                transform: rotate(360deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes loader-inner {
            0% {
                height: 0%;
            }

            25% {
                height: 0%;
            }

            50% {
                height: 100%;
            }

            75% {
                height: 100%;
            }

            100% {
                height: 0%;
            }
        }
    </style>
@endsection
@section('content')

    <!-- <div id="overlayer"></div>
<span class="loader">
  <span class="loader-inner"></span>
</span> -->
    <div class="m-auto row">
        <div class="col-lg-12 margin-tb p-0 w-100">
            <h2 class="page-heading">Api Response Management</h2>
        </div>
    </div>
    <!-- Hidden content used to generate dynamic elements (start) -->
    <div id="response-alert" style="display:none;" class="alert alert-success">
        <span>You should check in on some of those fields below.</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <!-- Hidden content used to generate dynamic elements (end) -->
    <div id="response-alert-container"></div>
    <form class="form-search-data">
        <div class="m-auto row">
            <div class="col-xs-6 col-md-2 pd-2">
                <div class="form-group">
                    <select class="form-control select select2 required" name="store_website_id"
                            placeholder="Select Store Website">
                        <option value="">Please select Website</option>
                        @foreach($store_websites as $web)
                            @php $sel = (isset($_GET['store_website_id']) && $_GET['store_website_id']==$web->id) ? " selected='selected' " : ""; @endphp
                            <option value="{{ $web->id }}" {{ $sel }} >{{ $web->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group ml-3 cls_filter_inputbox">
                <input name="api_key" type="text" class="form-control" placeholder="Search Key" id="api-key">
            </div>
            <div class="form-group ml-5 cls_filter_inputbox">
                <input name="api_value" type="text" class="form-control" placeholder="Value" id="api-value"
                       placeholder="Search Value">
            </div>
            <button type="button" onclick="$('.form-search-data').submit();" class="btn btn-image btn-call-data"><img
                        src="{{asset('/images/filter.png')}}" style="margin-top:-9px;"></button>
            <div class="pr-4" style="text-align: right; margin-bottom: 10px;">
                <button type="button" class="btn btn-primary custom-button " onclick="openAddModal();"
                        style="padding-top:-1;">
                    Add Response Message
                </button>
            </div>
        </div>
    </form>
    <!-- COUPON DETAIL MODAL -->
    <div class="modal fade" id="responseModal" tabindex="-1" role="dialog" aria-labelledby="responseModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <!-- <form id="coupon-form" method="POST" onsubmit="return executeCouponOperation();"> -->
            <form id="response-form" method="POST" action="{{ route('api-response-message.store') }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="responseModalLabel">New Api Response</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <div class="form-group ">
                            <div class="col-md-4">
                                <select class="form-control select select2 required" name="store_website_id">
                                    <option value="">Please select</option>
                                    @foreach($store_websites as $web)
                                        <option value="{{ $web->id }}">{{ $web->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-4" style="margin-top: -14px;">
                                <input type="text" class="form-control required" name="res_key" placeholder="Key"
                                       value="" id="key" />
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-md-4" style="margin-top: -14px;">
                                <input type="text" class="form-control required" name="res_value" placeholder="Value"
                                       value="" id="message" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn custom-button" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn custom-button save-button">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Edit MODAL -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <!-- <form id="coupon-form" method="POST" onsubmit="return executeCouponOperation();"> -->
            <form id="edit-form" method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Api Response</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body edit-body">
                        @csrf
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary update-button">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="apiResponseMessageTranslations" tabindex="-1" role="dialog"
         aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Api Response Message Translations</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body edit-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered p-0 w-100" style=" table-layout:fixed;"
                               id="api_response_table">
                            <thead class="p-0">
                            <tr>
                                <th width="5%">ID</th>
                                <th width="20%">Store Website</th>
                                <th width="20%">Lang Code</th>
                                <th width="20%">Key</th>
                                <th width="20%">Value</th>
                                <th width="15%">Approved By</th>
                            </tr>
                            </thead>
                            <tbody id="armt_tbody" class="p-0 pending-row-render-view infinite-scroll-api-inner">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
    @if(Session::has('message'))
        <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
    @endif
    <div class="row">
        <div class="col-md-12 pl-5 pr-5">
            <div class="m-auto row">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered p-0 w-100" style=" table-layout:fixed;"
                           id="api_response_table">
                        <thead class="p-0">
                        <tr>
                            <th width="4%">ID</th>
                            <th width="25%">Store Website</th>
                            <th width="25%">Key</th>
                            <th width="25%">Value</th>
                            <th width="7%">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="p-0 pending-row-render-view infinite-scroll-api-inner">
                        @php $i = 1; @endphp
                        @foreach($api_response as $res)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ isset($res->storeWebsite->title) ? $res->storeWebsite->title : '' }}</td>
                                <td>{{ $res->key }}</td>
                                <td>{{ $res->value }}</td>
                                <td>
                                    <div class="d-flex" style="justify-content: space-between;">
                                        <a onclick="editModal({{ $res->id}});" href="javascript:void(0);">
                                            <i class="fa fa-pencil" aria-hidden="true" style="color:grey;"></i>
                                        </a>
                                        <a href="{{ route('api-response-message.responseDelete',['id' => $res->id]) }}">
                                            <i class="fa fa-trash-o" aria-hidden="true" style="color:grey;"></i>
                                        </a>
                                        <a data-id="{{ $res->id}}" class="view-message-translation"
                                           title="View Message Translation" href="#">
                                            <i class="fa fa-eye" aria-hidden="true" style="color:grey;"></i>
                                        </a>
                                        <a onclick="apiResponseMessageTranslate(this)" data-id="{{ $res->id}}"
                                           title="Translate API Response Message" href="#">
                                            <i class="fa fa-language" aria-hidden="true" style="color:grey;"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @php $i = $i+1; @endphp
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <img class="infinite-scroll-products-loader center-block" src="{{asset('/images/loading.gif')}}" alt="Loading..."
         style="display: none" />
@endsection
@section('scripts')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.js"></script>
    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
    <script type="text/javascript">
      /* beautify preserve:end */
      $(document).ready(function() {
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });

        //  $('#api_response_table').dataTable();
        $(document).on("click", ".view-message-translation", function(event) {
          event.preventDefault()
          var id = $(this).attr('data-id');
          $("#loading-image-preview").show();
          $.ajax({
            url: "{{ route('api-response-message.lodeTranslation') }}",
            data: {
              id: id
            },
            type: "POST",
            success: function(response) {
              if (response.type == "success") {
                $('#armt_tbody').html('');
                $('#armt_tbody').html(response.data);
                $('#apiResponseMessageTranslations').modal('show');
              }
              $("#loading-image-preview").hide();
            },
            error: function(response) {
              $("#loading-image-preview").hide();
            }
          });
          $('#apiResponseMessageTranslations').show();
        });
      });
      $('#response-form').validate();

      function openAddModal() {
        $('#responseModal').modal('show');
      }

      function editModal(id) {
        $.ajax({
          url: "{{ route('getEditModal') }}",
          data: {
            id: id
          },
          type: "POST",
          success: function(response) {
            if (response.type == "success") {
              $('.edit-body').html('');
              $('.edit-body').append(response.data);
              $('#editModal').modal('show');
            }
          },
          error: function(response) {

          }
        });
        $('#editModal').show();
      }

      $('.update-button').on('click', function() {
        if ($(document).find('#edit-form').valid()) {
          $.ajax({
            url: "{{ route('api-response-message.updateResponse') }}",
            data: {
              id: $(document).find('#id').val(),
              key: $(document).find('#edit_key').val(),
              value: $(document).find('#edit_value').val(),
              store_website_id: $(document).find('#edit_store_website_id').val(),
            },
            type: "POST",
            success: function(response) {
              location.reload();
            },
            error: function(response) {

            }
          });
        }
      })

      function apiResponseMessageTranslate(ele) {
        let btn = jQuery(ele);
        let api_response_message_id = btn.data('id');

        if (confirm('Are you sure you want translate this message ?')) {
          jQuery.ajax({
            headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('api-response-message.messageTranslate') }}",
            type: 'POST',
            data: {
              api_response_message_id: api_response_message_id,
            },
            dataType: 'json',
            beforeSend: function() {
              jQuery("#loading-image-preview").show();
            },
            success: function(res) {
              if (res.code == 200) {
                toastr["success"](res.message);
              } else {
                toastr["error"](res.message);
              }
              jQuery("#loading-image-preview").hide();
            },
            error: function(res) {
              if (res.responseJSON != undefined) {
                toastr["error"](res.responseJSON.message);
              }
              jQuery("#loading-image-preview").hide();
            }
          });
        }
      }
    </script>
    <script>

      var isLoading = false;
      var page = 1;
      $(document).ready(function() {
        $(window).scroll(function() {
          if (($(window).scrollTop() + $(window).outerHeight()) >= ($(document).height() - 2500)) {
            loadMore();
          }
        });

        function loadMore() {
          if (isLoading)
            return;
          isLoading = true;
          var $loader = $('.infinite-scroll-products-loader');
          page = page + 1;
          $.ajax({
            url: "{{url('api-response')}}?ajax=1&page="+page,
                    type: 'GET',
                    data: $('.form-search-data').serialize(),
                    beforeSend: function() {
                        $loader.show();
                    },
                    success: function (data) {
                        
                        $loader.hide();
                        if('' === data.trim())
                            return;
                        $('.infinite-scroll-api-inner').append(data);
                        

                        isLoading = false;
                    },
                    error: function () {
                        $loader.hide();
                        isLoading = false;
                    }
                });
            }            
        });

       

  </script>      

@endsection