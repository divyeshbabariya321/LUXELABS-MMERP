@extends('layouts.app')


@section('title', 'Social  Campaigns')

@section('content')
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    @include("social.campaigns.history")

    <div class="row" id="common-page-layout">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Social  Campaigns ({{ $campaigns->total() }})<span class="count-text"></span></h2>
            <div class="pull-left ml-2 mb-3">
                <form class="form-inline" action="{{route('social.campaign.index')}}" method="GET">
                    <div class="form-group mr-2">
                        <input type="date" name="date" id="date" class="form-control" style="width:250px !important">
                    </div>
                    <div class="form-group mr-2">
                        <select class="form-control globalSelect2" name="config_name[]" data-placeholder="Config Name" id="" style="width:250px !important" multiple>
                            @foreach($campaigns as $campaign)
                                <option value="{{$campaign?->account?->id}}" {{ isset($_GET['config_name']) && in_array($campaign?->account?->id,$_GET['config_name']) ? 'selected' : '' }}
                                >{{$campaign?->account?->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <select class="form-control globalSelect2" name="campaign_name[]"  data-placeholder="Campaign Name" id="campaign_name" style="width:250px !important" multiple>
                            @foreach($campaigns as $campaign)
                                <option value="{{$campaign->name}}" {{ isset($_GET['campaign_name']) && in_array($campaign->name,$_GET['campaign_name']) ? 'selected' : '' }}>{{$campaign->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <select class="form-control globalSelect2" name="objective[]" data-placeholder="Objective"  id="objective" style="width:250px !important" multiple>
                            <option value="">Objective</option>
                            @foreach($campaigns as $campaign)
                                <option value="{{$campaign->objective_name}}" {{ isset($_GET['objective']) && in_array($campaign->objective_name,$_GET['objective']) ? 'selected' : '' }}>{{$campaign->objective_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <select class="form-control" name="type" id="type" style="width:250px !important">
                            <option value="">Type</option>.
                            @foreach($campaigns as $campaign)
                                <option value="{{$campaign->buying_type}}" {{ isset($_GET['type']) && !empty($campaign->buying_type == $_GET['type']) ? 'selected' : '' }}>{{$campaign->buying_type}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <select class="form-control" name="status" id="status" style="width:250px !important">
                            <option value="">Status</option>
                            @foreach($campaigns as $campaign)
                                <option value="{{$campaign->status}}" {{ isset($_GET['status']) && !empty($campaign->status == $_GET['status']) ? 'selected' : '' }}>{{$campaign->status}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <button type="submit" class="btn btn-image3 btn-sm text-dark">
                            <i class="fa fa-filter"></i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="pull-right mr-2">
                <a class="btn btn-secondary create-post">+</a>
            </div>
        </div>

        <div class="row ml-4 mb-2">
            @include("social.header_menu")
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="col-lg-12 margin-tb">
            <div class="col-md-12 margin-tb">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout:fixed;">
                        <tr>
                            <th>Date</th>
                            <th>Config Name</th>
                            <th>Website</th>
                            <th>Campaign Name</th>
                            <th style="width:15%">Objective</th>
                            <th>Buying Type</th>
                            <th>Daily Budget</th>
                            <th>Special Ad Categories</th>
                            <th style="width:5%">Status</th>
                            <th style="width:5%">Live Status</th>
                            <th style="width:5%">Action</th>
                        </tr>
                        <tbody class="infinite-scroll-data">
                            @include("social.campaigns.data")
                        </tbody>
                    </table>
                </div>
                {{ $campaigns->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
              50% 50% no-repeat;display:none;">
    </div>
    <div id="create-modal" class="modal" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="record-content">

            </div>
        </div>
    </div>


    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>

    <script type="text/javascript">
        $(document).on("click",".account-history",function(e) {
        e.preventDefault();
            var post_id = $(this).data("id");
            $.ajax({
                url: "{{ route('social.campaign.history') }}",
                type: 'POST',
                data : { "_token": "{{ csrf_token() }}", post_id : post_id },
                dataType: 'json',
                beforeSend: function () {
                  $("#loading-image").show();
                },
                success: function(result){
                    $("#loading-image").hide();

                    if(result.code == 200) {
                       var t = '';
                       $.each(result.data,function(k,v) {
                          t += `<tr><td>`+v.post_id+`</td>`;
                          t += `<td>`+v.log_title+`</td>`;
                          t += `<td>`+v.log_description+`</td>`;
                          t += `<td>`+v.created_at+`</td>`;
                          t += `<td>`+v.updated_at+`</td></tr>`;
                       });
                    }
                    $("#log-history-modal").find(".show-list-records").html(t);
                    $("#log-history-modal").modal("show");
                },
                error: function (){
                    $("#loading-image").hide();
                }
            });
       });

        $(document).on('click', '.create-post', function(e) {
             e.preventDefault();

            var $action_url = "{{ route('social.campaign.create') }}";
            jQuery.ajax({

                type: "GET",
                url: $action_url,
                dataType: 'html',
                success: function(data) {
                    $("#create-modal").modal('show');
                    $("#record-content").html(data);

                },
                error: function(error) {},

            });
            return false;

        });

        $(document).on('submit', '#create-form1', function(e) {
            e.preventDefault();

            var form = $(this);
            var postData = new FormData(form[0]);


            $.ajax({
                url:  "{{ route('social.campaign.store') }}",
                type: 'POST',
                data: postData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(response) {

                if (response.code == 200) {
                    $("#loading-image").hide();
                    toastr['success'](response.message, 'Success');
                    $('#create-modal').modal('hide');
                    location.reload();
                } else {
                    $("#loading-image").hide();
                  //  toastr['error'](response.message, 'error');
                    location.reload();
                }

            }).fail(function(errObj) {
                //toastr['error'](errObj.responseJSON.message, 'error');
                $("#loading-image").hide();
                location.reload();
            });
        });



        $(window).scroll(function() {
            if (($(window).scrollTop() + $(window).outerHeight()) >= ($(document).height() - 2500)) {
                loadMore();
            }
        });

        var isLoadingProducts;

        function loadMore() {
            if (isLoadingProducts)
                return;
            isLoadingProducts = true;
            if (!$('.pagination li.active + li a').attr('href'))
                return;

            var $loader = $('.infinite-scroll-products-loader');
            $.ajax({
                    url: $('.pagination li.active + li a').attr('href'),
                    type: 'GET',
                    beforeSend: function() {
                        $loader.show();
                        $('ul.pagination').remove();
                    }
                })
                .done(function(data) {
                    if ('' === data.trim())
                        return;

                    $loader.hide();

                    $('.infinite-scroll-data').append(data);

                    isLoadingProducts = false;
                })
                .fail(function(jqXHR, ajaxOptions, thrownError) {
                    console.error('something went wrong');

                    isLoadingProducts = false;
                });
        }
    </script>
@endsection
