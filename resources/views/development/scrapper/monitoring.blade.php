@extends('layouts.app')

@section('title', 'Scrapper | Monitoring')

@section('styles')
    <style>
        .select2-container {
            width: 100% !important;
        }

        .form-input-error {
            color: red;
        }

        .select2-form-group {
            min-width: 150px;
        }
    </style>
@endsection

@section('content')
    @include('partials.loader')

    <x-container-fluid>

        <x-page-heading heading="Scapper Monitoring" />

        @include('development.scrapper.partials.search-and-filters')

        @include('development.scrapper.partials.action-buttons')

        <x-row>
            <div class="col-md-12" id="tableDataWrapper">
                @include('development.scrapper.partials.table-data')'
            </div>
        </x-row>
    </x-container-fluid>

    @include('development.scrapper.partials.create-monitoring-modal')
@endsection

@section('scripts')
    <script>
        let currentPageIndex = 0;
        $(document).on('click', '#createMonitoring', function(event) {
            event.preventDefault();
            $('#createMonitoringModal').modal('show');
        });

        $(".select2").select2({});

        $(document).on('click', '#createMonitoringModal #submitForm', function(event) {
            event.preventDefault();
            $("#loading-image").show();
            clearFormErrors();

            let options = {
                url: "{{ route('development.scrap.monitoring.store') }}",
                data: $('#createMonitoringModalForm').serialize(),
                type: 'POST',
                success: function(response) {
                    $("#loading-image").hide();
                    console.log(response);
                    switch (response.status) {
                        case 'success':
                            toastr.success(response.message);
                            $('#createMonitoringModal').modal('hide');
                            fetchData(currentPageIndex);
                            $("#createMonitoringModalForm")[0].reset();
                            $('.select2').val(null).trigger('change');
                            break;

                        case 'error':
                            toastr.error(response.message);
                            break;
                    }
                },
                error: function(error) {
                    $("#loading-image").hide();

                    if (error?.status === 422) {
                        var data = JSON.parse(error.responseText);
                        toastr.error(data.message);
                        displayFormErrorsByName(data.errors);

                    } else {
                        toastr.error("Error while submitting data");
                    }
                }
            };

            $.ajax(options);
        });

        $(document).on('click', '.pagination a', function(event) {
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            fetchData(page);
        });

        function fetchData(page, filters = false) {
            currentPageIndex = page;
            $("#loading-image").show();
            let url = "{{ route('development.scrap.monitoring') }}" + "?page=" + page;
            if (filters) url += "&filter=" + filters;

            let options = {
                url: url,
                success: function(data) {
                    $("#loading-image").hide();
                    $('#tableDataWrapper').html(data);
                },
                error: function(error) {
                    $("#loading-image").hide();
                    toastr.error("Error while fetching data");
                }
            };
            $.ajax(options);
        }

        function displayFormErrorsByName(errors) {
            Object.keys(errors).map(function(inputName) {
                $(`[name="${inputName}"]`).after($(
                    `<span class='form-input-error'>${errors[inputName][0]}</span>`
                ));
            });
        }

        function clearFormErrors() {
            $('.form-input-error').remove();
        }

        $(document).on('submit', '#searchScrapDevelopmentMonitoring', function(event) {
            event.preventDefault();
            fetchData(1, $('#searchScrapDevelopmentMonitoring').serialize());
        });
    </script>
@endsection