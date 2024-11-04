@extends('layouts.app')


@section('title', 'Social  Ads')

@section('content')
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <div class="row" id="common-page-layout">

        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Social Ads ({{ $ads->total() }})<span class="count-text"></span></h2>
            <div class="pull-right mr-4">
                <a class="btn btn-secondary create-post">+</a>
            </div>

            <div class="pull-left ml-2 mb-3">
                <form class="form-inline" action="" method="GET">
                    <div class="form-group mr-2">
                        <input type="date" name="date" id="date" class="form-control" style="width:250px !important"
                               value="{{$_GET['date'] ?? '' }}">
                    </div>
                    <div class="form-group mr-2">
                        <select class="form-control" name="name" id="name" style="width:250px !important">
                            <option value="">Ads Name</option>
                            .
                            @foreach($ads_data as $ads_val)
                                <option value="{{$ads_val->name}}" {{ isset($_GET['name']) && !empty($ads_val->name == $_GET['name']) ? 'selected' : '' }}>{{$ads_val->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <select class="form-control globalSelect2" name="config_name[]" data-placeholder="Config Name"
                                id="" style="width:250px !important" multiple>
                                
                            @foreach($ads_data as $ads_config)
                              @if(!empty($ads_config->account))
                                <option value="{{$ads_config->account->id}}" {{ isset($_GET['config_name']) && in_array($ads_config->account->id,$_GET['config_name']) ? 'selected' : '' }}>{{$ads_config->account->name}}</option>
                              @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mr-2">
                        <select class="form-control globalSelect2" name="adset_name[]" data-placeholder="Adset Name.."
                                id="" style="width:250px !important" multiple>
                            @foreach($ads_data as $ads_name)
                                <option value="{{$ads_name->ad_set_name}}" {{ isset($_GET['adset_name']) && in_array($ads_name->ad_set_name,$_GET['adset_name']) ? 'selected' : '' }}>{{$ads_name->ad_set_name}}</option>
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
                            <th style="width:7%"> Name</th>
                            <th style="width:7%">Config Name</th>
                            <th style="width:7%">Website</th>
                            <th style="width:7%">Adset Name</th>
                            <!-- <th style="width:10%">Image</th> -->
                            <th style="width:17%">Creation Name</th>
                            <th style="width:5%">Status</th>
                            <th style="width:5%">Live Status</th>
                            <th style="width:5%">Action</th>
                        </tr>
                        <tbody class="infinite-scroll-data">
                        @include("social.ads.data")
                        </tbody>
                    </table>
                </div>
                {{ $ads->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
              50% 50% no-repeat;display:none;">
    </div>
    @include("social.ads.history")
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
          url: "{{ route('social.ad.history') }}",
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
        const $action_url = "{{ route('social.ads.create') }}";
        jQuery.ajax({
          type: "GET",
          url: $action_url,
          dataType: "html",
          success: function(data) {
            $("#create-modal").modal("show");
            $("#record-content").html(data);

          }
        });
        return false;

      });

      $(document).on("change", "#adset_id", function() {
        var g_name = $("option:selected", this).attr("rel");
        $("#ad_set_name").val(g_name);
      });
      $(document).on("change", "#adcreative_id", function() {
        var g_name = $("option:selected", this).attr("rel");
        $("#ad_creative_name").val(g_name);
      });
      $(document).on("change", "#config_id", function() {
        if ($(this).val() != "") {
          $.ajax({
            url: '{{route("social.ad.getAdsets")}}',
            dataType: "json",
            data: {
              id: $(this).val()
            },
            success: function(result) {
              if (result.adsets) {
                $("#loading-image").hide();
                let html = `<option value="">-----Select Adsets-----</option>`;
                $.each(result.adsets, function(key, value) {
                  html += `<option value="${value.id}" rel="${value.name}" >${value.name}</option>`;
                });
                $("#adset_id").html(html);
                let c_html = `<option value="">-----Select AdCreative-----</option>`;
                if (result.adcreatives) {
                  $.each(result.adcreatives, function(key, value) {
                    c_html += `<option value="${value.id}" rel="${value.object_story_title}" >${value.object_story_title}</option>`;
                  });
                }
                $("#adcreative_id").html(c_html);

              } else {
                $("#loading-image").hide();
              }
            }
          });
        }
      });

      $(document).on("change", "#post_id", function() {
        alert($(this).val());
        if ($(this).val() != "") {
          var object_story_title = $(this).attr("rel");
          $("#object_story_title").val(object_story_title);
        }
      });


      $(window).scroll(function() {
        if (($(window).scrollTop() + $(window).outerHeight()) >= ($(document).height() - 2500)) {
          loadMore();
        }
      });

      let isLoadingProducts;

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
