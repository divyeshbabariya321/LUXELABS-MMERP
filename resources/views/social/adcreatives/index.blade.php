@extends('layouts.app')


@section('title', 'Social  AdCreatives')

@section('content')
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


    <div class="row" id="common-page-layout">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Social AdCreatives ({{ $adcreatives->total() }})<span class="count-text"></span>
            </h2>
            <div class="pull-right mr-4">
                <a class="btn btn-secondary create-post">+</a>
            </div>
            <div class="pull-left ml-2 mb-3">
                <form class="form-inline" action="{{route('social.adcreative.index')}}" method="GET">
                    <div class="form-group mr-2">
                        <input type="date" name="dae" id="date" class="form-control" style="width:250px !important"
                               value="{{$_GET['date'] ?? '' }}">
                    </div>
                    <div class="form-group mr-2">
                        <select class="form-control globalSelect2" name="config_name[]" data-placeholder="Config Name"
                                id="" style="width:250px !important" multiple>
                            @foreach($adcreatives as $adcreative_config)
                                <option value="{{$adcreative_config->account->id}}" {{ isset($_GET['config_name']) && in_array($adcreative_config->account->id,$_GET['config_name']) ? 'selected' : '' }}>{{$adcreative_config->account->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <select class="form-control globalSelect2" name="name[]" data-placeholder="Ad Creative Name"
                                id="name" style="width:250px !important" multiple>
                            @foreach($adcreatives as $adcreative_value)
                                <option value="{{$adcreative_value->name}}" {{ isset($_GET['name']) && in_array($adcreative_value->name,$_GET['name']) ? 'selected' : '' }}>{{$adcreative_value->name}}</option>
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
        </div>

        <br>
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
                            <th style="width:5%">Date</th>
                            <th style="width:10%">Config Name</th>
                            <th style="width:10%">Website</th>
                            <th style="width:30%">Name</th>
                            <th style="width:10%">Object Story Title</th>
                            <th style="width:10%">Post ID</th>
                            <th style="width:5%">Live Status</th>
                            <th style="width:5%">Action</th>
                        </tr>
                        <tbody class="infinite-scroll-data">
                        @include("social.adcreatives.data")
                        </tbody>
                    </table>
                </div>
                {{ $adcreatives->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
              50% 50% no-repeat;display:none;">
    </div>

    @include("social.adcreatives.history")

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js">
    </script>

    <script type="text/javascript">
      $(document).on("click", ".account-history", function(e) {
        e.preventDefault();
        var post_id = $(this).data("id");
        $.ajax({
          url: "{{ route('social.adcreative.history') }}",
          type: "POST",
          data: { "_token": "{{ csrf_token() }}", post_id: post_id },
          dataType: "json",
          beforeSend: function() {
            $("#loading-image").show();
          },
          success: function(result) {
            $("#loading-image").hide();

            if (result.code == 200) {
              var t = "";
              $.each(result.data, function(k, v) {
                t += `<tr><td>` + v.post_id + `</td>`;
                t += `<td>` + v.log_title + `</td>`;
                t += `<td>` + v.log_description + `</td>`;
                t += `<td>` + v.created_at + `</td>`;
                t += `<td>` + v.updated_at + `</td></tr>`;
              });
            }
            $("#log-history-modal").find(".show-list-records").html(t);
            $("#log-history-modal").modal("show");
          },
          error: function() {
            $("#loading-image").hide();
          }
        });
      });
      $(document).on("click", ".create-post", function(e) {
        e.preventDefault();

        var $action_url = "{{ route('social.adcreative.create') }}";
        jQuery.ajax({

          type: "GET",
          url: $action_url,
          dataType: "html",
          success: function(data) {
            $("#create-modal").modal("show");

            $("#record-content").html(data);

          },
          error: function(error) {
          }

        });
        return false;

      });

      $(document).on("change", "#config_id", function() {
        $.ajax({
          url: '{{route("social.adcreative.getpost")}}',
          dataType: "json",
          data: {
            id: $(this).val()
          },
          beforeSend: function() {
            $("#loading-image").show();
          },
          success: function(result) {
            $("#loading-image").hide();
            if (result) {
              let html = `<option value="">-----Select Post-----</option>`;
              $.each(result, function(key, value) {
                html += `<option value="${value.id}" rel="${value.post_body}" >${value.post_body}</option>`;
              });
              $("#post_id").html(html);
            } else {
              $("#loading-image").hide();
            }
          }
        });
      });

      $(document).on("change", "#post_id", function() {
        if ($(this).val() != "") {
          var g_name = $("option:selected", this).attr("rel");
          $("#object_story_title").val(g_name);
        }
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
        if (!$(".pagination li.active + li a").attr("href"))
          return;

        var $loader = $(".infinite-scroll-products-loader");
        $.ajax({
          url: $(".pagination li.active + li a").attr("href"),
          type: "GET",
          beforeSend: function() {
            $loader.show();
            $("ul.pagination").remove();
          }
        })
          .done(function(data) {
            if ("" === data.trim())
              return;

            $loader.hide();

            $(".infinite-scroll-data").append(data);

            isLoadingProducts = false;
          })
          .fail(function(jqXHR, ajaxOptions, thrownError) {
            console.error("something went wrong");

            isLoadingProducts = false;
          });
      }
    </script>
@endsection
