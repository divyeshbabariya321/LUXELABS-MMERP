@extends('layouts.app')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@endsection


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Ip Logs ({{ $logs->total() }})</h2>
        </div>
    </div>
    <div class="mt-3 col-md-12">
        <form action="{{ route('get.ip.logs') }}" method="get" class="search">
            <div class="col-1">
                <b>Search</b>
            </div>
            <div class="col-md-2 pd-sm">
                {{ html()->multiselect('email_ids[]', \App\IpLog::pluck('email', 'email')->toArray(), request('email_ids'))->class('form-control globalSelect2')->data('placeholder', 'Select Emails') }}
            </div>
            <div class="col-lg-2">
                <input class="form-control" type="text" id="search_message" placeholder="Search Message"
                    name="search_message">
            </div>
            <div class="col-lg-2">
                <input class="form-control" type="text" id="search_ip" placeholder="Search Ip" name="search_ip">
            </div>
            <div class="col-lg-2">
                <input class="form-control" type="date" name="date">
            </div>

            <div class="col-lg-2">
                <button type="submit" class="btn btn-image search"
                    onclick="document.getElementById('download').value = 1;">
                    <img src="{{ asset('images/search.png') }}" alt="Search">
                </button>
                <a href="{{ route('get.ip.logs') }}" class="btn btn-image" id=""><img src="/images/resend2.png"
                        style="cursor: nwse-resize;"></a>
            </div>
        </form>
    </div>
    <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped" id="log-table">
            <thead>
                <tr>
                    <th width="3%">ID</th>
                    <th width="3%">Email</th>
                    <th width="30%">Ip</th>
                    <th width="30%">Server Name</th>
                    <th width="10%">Status</th>
                    <th width="10%">message</th>
                    <th width="10%">Date</th>
                    <th width="10%">Action</th>
                </tr>
            <tbody>
                @foreach ($logs as $data)
                    <tr>
                        <td>{{ $data->id }}</td>
                        <td>{{ $data->email }}</td>
                        <td>{{ $data->ip }}</td>
                        <td>{{ $data->server_name }}</td>
                        <td>{{ $data->status }}</td>
                        <td>{{ $data->message }}</td>
                        <td>{{ $data->created_at }}</td>
                        <td><button type="button" id="ip_log" class="btn btn-secondary" data-ip="{{ $data->ip }}"
                                data-comment="ipLogs server" data-servername="{{ $data->server_name }}"
                                onclick="return confirm('{{ __('Are you Want to execute') }}')"> <i
                                    class="fa fa-paper-plane " aria-hidden="true"></i></button></td>
                    </tr>
                @endforeach
            </tbody>
            </thead>
        </table>
        {!! $logs->appends(Request::except('page'))->links() !!}
    </div>
    <div id="loading-image"
        style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
    </div>


    <script type="text/javascript">
        $(document).on("click", "#ip_log", function(e) {
            e.preventDefault();
            var ipLogId = $(this).data('ip');
            var comment = $(this).data('comment');
            var serverName = $(this).data('servername');

            $.ajax({
                url: '{{ route('whitelist-ip') }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    ip_address: ipLogId,
                    comment: comment,
                    server_name: serverName,
                },
                beforeSend: function() {
                    $('#loading-image').show();
                },
                success: function(response) {
                    if (response.code == 200) {
                        toastr['success'](response.message, 'Success');
                    } else {
                        toastr['error'](response.message, 'Error');
                    }
                },
                error: function(xhr, status, error) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;

                        if (errors.server_name) {
                            toastr['error'](errors.server_name[0], 'Error');
                        }

                        if (errors.ip_address) {
                            toastr['error'](errors.ip_address[0], 'Error');
                        }
                    } else {
                        toastr['error']('An error occurred', 'Error');
                    }
                },
                complete: function() {
                    $('#loading-image').hide();
                }
            });
        });
    </script>
@endsection
