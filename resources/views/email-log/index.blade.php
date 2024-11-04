@extends('layouts.app')

@section('title', 'Email Logs')

@section('content')
    @include('partials.loader')

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 margin-tb">
                <h2 class="page-heading">Email Logs</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-default truncate-email-alert-logs">Truncate</button>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12" id="email-alert-logs-table-wrapper">
                @include('email-log.partials.email-alert-logs')
            </div>
        </div>
    </div>

    <x-jquery-confirm />
@endsection

@section('scripts')
    <script>
        $(document).on('click', '.show-log-info', function(e) {
            $(this).find('.log-message-full').toggleClass('hidden');
            $(this).find('.log-message-limit').toggleClass('hidden');
        })

        $(document).on('click', '.truncate-email-alert-logs', function(e) {
            confirmDialog({
                title: 'Confirm!',
                content: 'Do you want to truncate email alert logs?',
                confirm: function() {
                    $.ajax({
                        url: "{{ route('email-alert-log.truncate') }}",
                        method: 'DELETE',
                        dataType: 'json',
                        beforeSend: function() {
                            $('#loading-image').show();
                        },
                        success: function(response) {
                            $('#loading-image').hide();

                            if (response.code == 200) {
                                toastr.success(response.message);
                                getEmailAlertLogs();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            $('#loading-image').hide();
                            toastr.error('Something went wrong');
                        }
                    });
                },
                cancel: function() {
                    toastr.warning('Canceled');
                }
            });
        })

        function getEmailAlertLogs() {
            $.ajax({
                url: "{{ route('email-alert-log.index') }}",
                method: 'GET',
                beforeSend: function() {
                    $('#loading-image').show();
                },
                success: function(response) {
                    $('#loading-image').hide();
                    $('#email-alert-logs-table-wrapper').html(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#loading-image').hide();
                    toastr.error('Something went wrong');
                }
            });
        }
    </script>
@endsection
