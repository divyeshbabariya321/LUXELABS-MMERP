@extends('layouts.app')

@section('styles')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@endsection

@section('large_content')
    <?php $base_url = URL::to('/'); ?>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Routes Data</h2>
        </div>
    </div>
    @if(Session::has('message'))
        <p class="alert alert-info">{{ Session::get('message') }}</p>
    @endif

    <div class="row">
        <div class="col-lg-6 margin-tb">
            <div class="pull-left cls_filter_box">
                <div class="form-group ml-3 cls_filter_inputbox">
                    <select class="form-control" id="route-search-box"></select>
                    <button class="btn btn-secondary search-btn">Search</button>
                </div>
            </div>
        </div>
        <div class="col-lg-6 margin-tb">
            @if(auth()->user()->isAdmin())
                <div class="pull-right mt-3 ml-3">
                    <button data-action="enable" class="btn btn-default switchEmailAlertRouteAll">Enable Email Alerts (All)</button>
                    <button data-action="disable" class="btn btn-default switchEmailAlertRouteAll">Disable Email Alerts (All)</button>
                </div>
            @endif
            <div class="pull-right mt-3">
                <a class="btn btn-default" href="{{ route('routes.sync') }}">Route Sync</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="panel-group" style="margin-bottom: 5px;">
                <div class="panel mt-3 panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            Routes
                        </h4>
                    </div>
					<div class="panel-body">
                        @include('routes.ajax.index_ajax', compact('routesData'))
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
    $(document).on("change", ".status-change", function () {
        var _this = jQuery(this);
        $.ajax({
            headers: {
            "X-CSRF-TOKEN": jQuery("meta[name=\"csrf-token\"]").attr("content")
            },
            url: jQuery(_this).data("url"),
            type: "POST",
            data: { status: jQuery(_this).val() },
            dataType: "JSON",
            success: function (resp) {
            console.log(resp);
            if (resp.status == "ok") {
                $("body").append(resp.html);
                $("#newTaskModal").modal("show");
            }
            }
        });
    });

    $(document).on('click','.switchEnableEmailAlertRoute',function(e){
        $(this).closest('.switchAN').find('.switchEnableEmailAlertRouteText').removeClass('textLeft');
        $(this).closest('.switchAN').find('.switchEnableEmailAlertRouteText').removeClass('textRight');

        let routeId = $(this).attr('data-id');
        var isChecked = $(this).prop('checked');
        var availabilityText = isChecked ? 'On' : 'Off';
        var alignmentText = isChecked ? 'textLeft' : 'textRight';

        $(this).closest('.switchAN').find('.switchEnableEmailAlertRouteText').text(availabilityText);
        $(this).closest('.switchAN').find('.switchEnableEmailAlertRouteText').addClass(alignmentText);
        var url = "{{ route('routes.update.email-alert') }}";

        $.ajax({
            type: 'POST',
            url: url,
            beforeSend: function () {
                $("#loading-image").show();
            },
            data: {
                _token: "{{ csrf_token() }}",
                email_alert : isChecked,
                id: routeId,
                type: 'single',
            },
            dataType: "json",
        }).done(function (response) {
            $("#loading-image").hide();
            toastr['success'](response.message, 'success');
        }).fail(function (response) {
            $("#loading-image").hide();
            toastr['error']('Error', 'error');
        });
    });

    $(document).on('click','.switchEmailAlertRouteAll',function(e){
        let action = $(this).attr('data-action');
        var url = "{{ route('routes.update.email-alert') }}";

        $.ajax({
            type: 'POST',
            url: url,
            beforeSend: function () {
                $("#loading-image").show();
            },
            data: {
                _token: "{{ csrf_token() }}",
                email_alert : (action == 'enable') ? true : false,
                type: 'all'
            },
            dataType: "json",
        }).done(function (response) {
            $("#loading-image").hide();
            toastr['success'](response.message, 'success');
            location.reload();
        }).fail(function (response) {
            $("#loading-image").hide();
            toastr['error']('Error', 'error');
        });
    });

    $(document).ready(function () {
        $('#route-search-box').select2({
            width: "200px",
            multiple: true,
            ajax: {
                url: "{{ route('routes.index') }}?route-suggestions=1",
                data: function (params) { 
                    let query = {
                        search: params.term,
                    }

                    return query;
                },
                processResults: function (data) {
                    const results = [];

                    for (let i = 0; i < data.length; i++) {
                        results.push({
                            id: data[i].url,
                            text: "{{ $base_url }}" + data[i].url,
                        });
                    }

                    return {
                        results: results,
                    }
                }       
            },
        });
    });

    function fetchAndReplaceTableData(page=1) {
        $("#loading-image").show();
        $.ajax({
            url: "{{ route('routes.index') }}",
            data: {
                page: page,
                search: $('#route-search-box').val(),
            },
            success: function (data) {
                $("#loading-image").hide();
                $('.panel-body').html(data);
            },
            error: function (error) {
                $("#loading-image").hide();
            },
        });
    }

    $(document).on('click', '.search-btn', function (event) {
        event.preventDefault();
        fetchAndReplaceTableData();
    });

    $(document).ready(function(){
        $(document).on('click', '.pagination a', function(event){
            event.preventDefault(); 
            let page = $(this).attr('href').split('page=')[1];
            fetchAndReplaceTableData(page=page);
        });
    });
    </script>
@endsection
