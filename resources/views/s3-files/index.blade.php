@extends('layouts.app')

@section('title', 'S3 | Files')

@section('styles')
    <style>
        .records-not-found {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
            background-color: #f0f0f0;
            color: #333;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
    </style>
@endsection

@section('content')
    <div id="loading-image"
        style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
               50% 50% no-repeat;display:none;">
    </div>

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">S3 Files</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div type="text/x-jsrender" class="p-5 files-table-container">
                @include('s3-files.partials.file-list')
            </div>
        </div>
    </div>

    @include('s3-files.partials.view-media-modal')
    
    <x-jquery-confirm />
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let currentPageIndex = 0;

            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                fetchData(page);
            });

            function fetchData(page) {
                currentPageIndex = page;
                $("#loading-image").show();

                $.ajax({
                    url: "{{ route('s3-files.index') }}" + "?page=" + page,
                    success: function(data) {
                        $("#loading-image").hide();
                        $('.files-table-container').html(data);
                    },
                    error: function(error) {
                        $("#loading-image").hide();
                        toastr.error("Error while fetching files");
                    }
                });
            }

            $(document).on('click', '.file-delete-btn', function(event) {
                event.preventDefault();
                const mediaId = $(this).attr('data-id');

                confirmDialog({
                    title: 'Confirm!',
                    content: 'Do you want to delete file',
                    confirm: function() {
                        $("#loading-image").show();

                        $.ajax({
                            url: "{{ route('s3-files.destroy') }}",
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: "{{ csrf_token() }}",
                                mediaId: mediaId,
                            },
                            success: function(data) {
                                $("#loading-image").hide();
                                switch (data.status) {
                                    case 'success':
                                        toastr.success(data.message);
                                        fetchData(currentPageIndex);
                                        break;
                                    case 'error':
                                        toastr.error(data.message);
                                        break;
                                }
                            }
                        });
                    },
                    cancel: function() {
                        toastr.warning('Canceled');
                    }
                });
            });

            $(document).on('click', '.file-move-btn', function(event) {
                event.preventDefault();
                const mediaId = $(this).attr('data-id');

                confirmDialog({
                    title: 'Confirm!',
                    content: 'Do you want to move file to Glacier',
                    confirm: function() {
                        $("#loading-image").show();

                        $.ajax({
                            url: "{{ route('s3-files.move-to-glacier') }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                mediaId: mediaId,
                            },
                            success: function(data) {
                                $("#loading-image").hide();
                                switch (data.status) {
                                    case 'success':
                                        toastr.success(data.message);
                                        fetchData(currentPageIndex);
                                        break;
                                    case 'warning':
                                        toastr.warning(data.message);
                                        break;
                                    case 'error':
                                        toastr.error(data.message);
                                        break;
                                }
                            }
                        });
                    },
                    cancel: function() {
                        toastr.warning('Canceled');
                    }
                });
            });

            $(document).on('click', '.view-s3-file', async function(event) {
                event.preventDefault();
                const mediaId = $(this).attr('data-id');
                const presigned = $(this).attr('data-presigned');

                $('#viewMediaModalContent').attr('src', presigned);
                $('#viewMediaModal').modal('show');
            });

        });
    </script>
@endsection
