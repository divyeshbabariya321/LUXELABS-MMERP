@extends('layouts.app')
@section('title', 'Social Posts')
@push('styles')
    <style>
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
        .carousel-inner.maincarousel img {
            margin-top: 20px;
        }
    </style>
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush
@section('content')
    @include("social.posts.history")
    @include("social.posts.translation-approve")
    <div class="row p-lg-4 p-md-0" id="common-page-layout">
        <div class="col-lg-12">
            <h2 class="page-heading">Social Posts ({{ $posts->total() }})<span class="count-text"></span></h2>
            <div class="pull-right">
                <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#postCreateModal">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>
        @include("social.header_menu")
        @include('partials.flash_messages')
        <br>
        <div class="col-md-12 margin-tb">
            <div class="table-responsive">
                <table class="table table-bordered" style="table-layout:fixed;">
                    <tr>
                        <th style="width:5%">Image</th>
                        <th style="width:5%">Account</th>
                        <th style="width:9%">Website</th>
                        <th style="width:6%">Platform</th>
                        <th style="width:15%">Caption</th>
                        <th style="width:15%">Translated Caption</th>
                        <th style="width:7%">Translated Caption Score</th>
                        <th style="width:10%">Hashtags</th>
                        <th style="width:10%">Translated Hashtags</th>
                        {{-- <th style="width:7%">Translated Hashtags Score</th> --}}
                        <th style="width:7%">Translation Language</th>
                        <th style="width:7%">Translation Approved & Post By</th>
                        <th style="width:7%">Posted on</th>
                        <th style="width:6%">Status</th>
                        <th style="width:5%">Action</th>
                    </tr>
                    <tbody class="infinite-scroll-data">
                    @include("social.posts.data")
                    </tbody>
                </table>
            </div>
            {{ $posts->appends(request()->except('page'))->links() }}
        </div>
    </div>
    <div id="loading-image" style="position: fixed;left: 0;top: 0;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
              50% 50% no-repeat;display:none;">
    </div>
    <div id="create-modal" class="modal" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="record-content">

            </div>
        </div>
    </div>

    <div id="show-image-modal" class="modal" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="show-image-modal-data">
            </div>
        </div>
    </div>
    @include('social.posts.modals.create')
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js">
    </script>
    <script type="text/javascript">
      $(document).on("click", ".account-history", function(e) {
        e.preventDefault();
        var post_id = $(this).data("id");

        console.log(post_id,'postif')
        $.ajax({
          url: "{{ route('social.post.history') }}",
          type: "POST",
          data: { "_token": "{{ csrf_token() }}", post_id: post_id },
          dataType: "json",
          beforeSend: function() {
            $("#loading-image").show();
          },
          success: function(result) {
            $("#loading-image").hide();
            if (result.code === 200) {
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

      $(document).on("click", ".translation-approval", function(e) {
        e.preventDefault();
        var post_id = $(this).data("id");
        $.ajax({
          url: "{{ route('social.post.translationapproval') }}",
          type: "POST",
          data: { "_token": "{{ csrf_token() }}", post_id: post_id },
          dataType: "json",
          beforeSend: function() {
            $("#loading-image").show();
          },
          success: function(result) {
            console.log(result);
            $("#loading-image").hide();

            document.getElementById("caption").value = result.data.caption;
            document.getElementById("post_id").value = result.data.post_id;
            document.getElementById("caption_trans").value = result.data.caption_trans;
            document.getElementById("hashtag").value = result.data.hashtag;
            document.getElementById("hashtag_trans").value = result.data.hashtag_trans;

            $("#TranslationApproval").modal("show");
          },
          error: function() {
            $("#loading-image").hide();
          }
        });
      });


      $(document).on("click", ".post-delete", function(e) {
        e.preventDefault();
        const post_id = $(this).data("id");
        if (confirm("Are you sure?")) {
          $.ajax({
            type: "POST",
            url: "{{ route('social.post.postdelete') }}",
            data: { "_token": "{{ csrf_token() }}", "post_id": post_id },
            dataType: "json",
            success: function(message) {
              alert("Deleted Post");
              location.reload(true);
            }, error: function() {
              alert("Something went wrong");
            }

          });
        }
        return false;


      });

      $(document).on("submit", "#create-form1", function(e) {
        e.preventDefault();
        const form = $(this);
        const postData = new FormData(form[0]);
        $.ajax({
          url: "{{ route('social.post.store') }}",
          type: "POST",
          data: postData,
          processData: false,
          contentType: false,
          dataType: "json",
          beforeSend: function() {
            $("#loading-image").show();
          }
        }).done(function(response) {

          if (response.code == 200) {
            $("#loading-image").hide();
            toastr["success"](response.message, "Success");
            $("#create-modal").modal("hide");
            location.reload();
          } else {
            $("#loading-image").hide();
            location.reload();
          }

        }).fail(function(errObj) {
          $("#loading-image").hide();
          location.reload();
        });
      });

        $(document).on("click", ".btn-get-translate-score", function(e) {
            var postId = $(this).data('id');
            if (postId) {
                $.ajax({
                    url: "/social/post/" +  postId + "/get-translated-text-score",
                    type: "GET",
                    beforeSend: function() {
                        $("#loading-image").show();
                    }
                })
                .done(function(data) {
                    $("#loading-image").hide();
                    location.reload();
                })
                .fail(function(jqXHR, ajaxOptions, thrownError) {
                    $("#loading-image").hide();
                    location.reload();
                });
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
