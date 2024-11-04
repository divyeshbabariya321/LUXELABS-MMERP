@extends('layouts.app')
@section('title', 'Social Posts')
@section('content')
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    @include("social.posts.history")

    <div class="col-md-12">
        <h2 class="page-heading">
            Social Posts Grid ({{ $posts->total() }})<span class="count-text"></span>

            <a target="_blank" class="btn btn-secondary mr-2" style="background: #fff;color: #757575;border: 1px solid #ccc;float: right;" href="{{route('social.all-comments')}}">All Comments</a>
        </h2>
        <div class="col-lg-12">
            <form action="" method="GET" class="form-inline align-items-start">
                <div class="row mr-3 mb-3">
                    <div class="form-group">
                        <label>Platform</label>
                        <select id="social_config" class="form-control social_config" name="social_config[]" multiple>
                            @foreach ($socialconfigs->unique('platform') as $socialconfig)
                                <option value="{{ $socialconfig->platform }}" {{ in_array($socialconfig->platform,$_GET['social_config']?? []) ? 'selected' : '' }}>{{ $socialconfig->platform }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group ml-3">
                        <label>Web site</label>

                        <select id="store_website_id" class="form-control store_website_id" name="store_website_id[]" multiple>
                            @foreach ($websites as $id => $website)
                                <option value="{{ $website->id }}" {{ in_array($website->id,$_GET['store_website_id']?? []) ? 'selected' : '' }}>{{ $website->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Add filters. DEVTASK-24791 --}}
                    <div class="form-group ml-3">
                        <label>Config Account</label>

                        <select id="config_account" class="form-control store_website_id" name="config_account[]" multiple>
                            @foreach ($socialconfigs as $socialconfig)
                                <option value="{{ $socialconfig->id }}" {{ in_array($socialconfig->id,$_GET['config_account']?? []) ? 'selected' : '' }}>{{ $socialconfig->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group ml-3">
                        <label>Post Date</label>

                        <input type="text" class="form-control" name="post_on_date" id="post_on_date" placeholder="YYYY/MM/DD - YYYY/MM/DD">
                    </div>
                    <div class="form-group ml-3">
                        <label>Search caption/hashtags</label>

                        <input type="text" class="form-control" name="caption_hashtags" placeholder="Search Captions/Hashtags...">
                    </div>
                    <button type="submit" class="btn btn-image"><img src="{{asset('images/filter.png')}}"/></button>
                </div>
            </form>
        </div>
        <div class="row" id="common-page-layout">
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
            <br>

            <div class="col-md-12 margin-tb">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout:fixed;">
                        <tr>
                            <th style="width:5%">Image</th>
                            <th style="width:5%">Account</th>
                            <th style="width:5%">Website</th>
                            <th style="width:5%">Platform</th>
                            <th style="width:15%">Caption</th>
                            <th style="width:15%">Translated Caption</th>
                            <th style="width:10%">Hashtags</th>
                            <th style="width:10%">Translated Hashtags</th>
                            <th style="width:5%">Translation Language</th>
                            <th style="width:7%">Post Translation Approved By</th>
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
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>

    <script type="text/javascript">
        $('#social_config').select2({
            placeholder: 'Select Platform',
        });
        $('#store_website_id').select2({
            placeholder: 'Select Website',
        });
        // Handle filter functions. DEVTASK-24791
        $('#config_account').select2({
            placeholder: 'Select Account',
        });

        $(function() {
            $('#post_on_date').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('#post_on_date').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
            });

            $('#post_on_date').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        });

        $(document).on("click", ".account-history", function (e) {
            e.preventDefault();
            var post_id = $(this).data("id");
            $.ajax({
                url: "{{ route('social.post.history') }}",
                type: 'POST',
                data: {"_token": "{{ csrf_token() }}", post_id: post_id},
                dataType: 'json',
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function (result) {
                    $("#loading-image").hide();

                    if (result.code == 200) {
                        var t = '';
                        $.each(result.data, function (k, v) {
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
                error: function () {
                    $("#loading-image").hide();
                }
            });
        });

        $(document).on("click", ".post-delete", function (e) {
            e.preventDefault();
            var post_id = $(this).data("id");
            if (confirm("Are you sure?")) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('social.post.postdelete') }}",
                    data: {"_token": "{{ csrf_token() }}", "post_id": post_id},
                    dataType: "json",
                    success: function (message) {
                        alert('Deleted Post');
                        location.reload(true);
                    }, error: function () {
                        alert('Something went wrong');
                    }

                });
            }
            return false;


        });

        $(document).on('click', '.create-post', function (e) {
            e.preventDefault();

            
            jQuery.ajax({

                type: "GET",
                url: $action_url,
                dataType: 'html',
                success: function (data) {
                    $("#create-modal").modal('show');
                    $("#record-content").html(data);

                },
                error: function (error) {
                },

            });
            return false;

        });

        $(document).on('submit', '#create-form1', function (e) {
            e.preventDefault();

            var form = $(this);
            var postData = new FormData(form[0]);


            $.ajax({
                url: "{{ route('social.post.store') }}",
                type: 'POST',
                data: postData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function (response) {

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

            }).fail(function (errObj) {
                //toastr['error'](errObj.responseJSON.message, 'error');
                $("#loading-image").hide();
                location.reload();
            });
        });

        $(window).scroll(function () {
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
                beforeSend: function () {
                    $loader.show();
                    $('ul.pagination').remove();
                }
            })
                .done(function (data) {
                    if ('' === data.trim())
                        return;

                    $loader.hide();

                    $('.infinite-scroll-data').append(data);

                    isLoadingProducts = false;
                })
                .fail(function (jqXHR, ajaxOptions, thrownError) {
                    console.error('something went wrong');

                    isLoadingProducts = false;
                });
        }
    </script>
@endsection
