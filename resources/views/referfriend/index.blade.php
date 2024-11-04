@extends('layouts.app')
@section('favicon', 'referral-management.png')

@section('title', 'Referral Info')

@section('styles')

    <!-- ... -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }

    </style>
@endsection
@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Friend Referrals Listing (<span id="Referral_count">{{ $data->total() }}</span>)
            </h2>
            <div class="pull-left">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Search All Columns</label>
                            <input name="term" type="text" class="form-control" value="{{ isset($term) ? $term : '' }}"
                                placeholder="search all fields" id="term">
                        </div>
                        <div class="col-md-6">
                            <label for="for_date">Created at:</label>
                            <input class="form-control datepicker-block" placeholder="Enter date" name="for_date" type="text"
                                id="for_date">
                        </div>

                        <div class="col-md-2">
                            <button type="button" class="btn btn-image" onclick="submitSearch()"><img
                                    src="/images/filter.png" /></button>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img
                                    src="/images/resend2.png" /></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered" id="Referrals-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Referrer Name</th>
                    <th>Referrer Email</th>
                    <th>Referrer Phone</th>
                    <th>Referee Name</th>
                    <th>Referee Email</th>
                    <th>Referee Phone</th>
                    <th>Website</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody class="infinite-scroll-pending-inner">
                @include('referfriend.partials.list-referral')
            </tbody>
        </table>
    </div>
    <img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..."
        style="display: none" />

    <div id="view_error" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content" style="min-width: 707px !important;">
                <div class="modal-header">
                    <h4 class="modal-title">View Logs</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Index</th>
                            <th>Time</th>
                            <th>Log</th>
                            <th>Message</th>
                        </tr>
                        <tbody class="content">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default close-setting" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js">
    </script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script type="text/javascript">
        $('.select-multiple').select2({
            width: '100%'
        });

        function submitSearch() {
            src = "{{ route('referfriend.list') }}"
            term = $('#term').val()
            for_date = $('#for_date').val()
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    term: term,
                    for_date: for_date,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },

            }).done(function(data) {
                $("#loading-image").hide();
                $("#Referrals-table tbody").empty().html(data.tbody);
                $("#Referral_count").text(data.count);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }

            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });

        }

        function resetSearch() {
            src = "{{ route('referfriend.list') }}"
            blank = ''
            $.ajax({
                url: src,
                dataType: "json",
                data: {

                    blank: blank,

                },
                beforeSend: function() {
                    $("#loading-image").show();
                },

            }).done(function(data) {
                $("#loading-image").hide();
                $('#term').val('')
                $('#Referral-select').val('')
                $("#Referrals-table tbody").empty().html(data.tbody);
                $("#Referral_count").text(data.count);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }

            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        }

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
                    url: "/referfriend/list?page=" + page,
                    type: 'GET',
                    beforeSend: function() {
                        $loader.show();
                    },
                    success: function(data) {
                        $loader.hide();
                        console.log(page);
                        $('.infinite-scroll-pending-inner').append($.trim(data['tbody']));
                        isLoading = false;
                    },
                    error: function() {
                        $loader.hide();
                        isLoading = false;
                    }
                });
            }
        });

        $(document).on('click', '.view_error', function(event) {
            event.preventDefault();
            $.ajax({
                url: '{{ route('referfriend.logAjax') }}',
                dataType: "json",
                data: {
                    id: $(this).data('id')
                },
                beforeSend: function() {
                    $("#loading-image").show();
                },
            }).done(function(data) {
                $("#loading-image").hide();
                var $html = '';
                if (data.data.length > 0) {
                    $.each(data.data, function(i, item) {
                        $html += '<tr>';
                        $html += '<td>' + parseInt(i + 1) + '</td>';
                        $html += '<td>' + item.created_at + '</td>';
                        $html += '<td>' + item.log + '</td>';
                        $html += '<td>' + wordWrap(item.message, 50) + '</td>';
                        $html += '</tr>';
                    });
                }
                $('#view_error table tbody.content').html($html);
                $('#view_error').modal('show');
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                $("#loading-image").hide();
            });

        });

        function wordWrap(str, maxWidth) {
            var newLineStr = "\n";
            done = false;
            res = '';
            while (str.length > maxWidth) {
                found = false;
                // Inserts new line at first whitespace of the line
                for (i = maxWidth - 1; i >= 0; i--) {
                    if (testWhite(str.charAt(i))) {
                        res = res + [str.slice(0, i), newLineStr].join('');
                        str = str.slice(i + 1);
                        found = true;
                        break;
                    }
                }
                // Inserts new line at maxWidth position, the word is too long to wrap
                if (!found) {
                    res += [str.slice(0, maxWidth), newLineStr].join('');
                    str = str.slice(maxWidth);
                }
            }
            return res + str;
        }

        function testWhite(x) {
            var white = new RegExp(/^\s$/);
            return white.test(x.charAt(0));
        };

        $(".datepicker-block").datetimepicker({
            format: 'YYYY-MM-DD'
        });
    </script>

@endsection
