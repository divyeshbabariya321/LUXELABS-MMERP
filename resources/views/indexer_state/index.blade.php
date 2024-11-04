@extends('layouts.app')

@section('title')
Indexer State
@endsection

@section('content')
<style>
    .index-invalidate {
        background-color: red;
        color: white;
        letter-spacing: 2px;
    font-weight: bold;
    font-size: 15px;
    text-align: center;
    }

    .index-valid {
        background-color: #00ff00;
        color: white;
        letter-spacing: 2px;
    font-weight: bold;
    font-size: 15px;
    text-align: center;
    }

    .index-running {
        background-color: gray;
        color: white;
        letter-spacing: 2px;
    font-weight: bold;
    font-size: 15px;
    text-align: center;
    }

    .index-partial_invalid {
        background-color: #00bfbf;
        color: white;
        letter-spacing: 2px;
        font-weight: bold;
        font-size: 15px;
        text-align: center;
    }
</style>
    <div class="row" id="common-page-layout">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Indexer State <span class="count-text"></span> <button class="btn btn-primary btn-refresh-listing"><span class="glyphicon glyphicon-refresh"></span> Refresh</button></h2>
        </div>
        <br>
        <div class="col-lg-12 pl-5 pr-5">
            <div id="elasticConnection">

            </div>
            <form action="/store-website/generate-api-token" method="post">
                <?php echo csrf_field(); ?>

                <div class="col-md-12">
                    <div class="table-responsive mt-3" id="reindex-table">
                        @include('indexer_state.list')
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="indexer-settings" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Indexer Settings</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form action="" method="post">
                                <?php echo csrf_field(); ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive mt-3">
                                            <input hidden type="text" class="form-control" name="id"
                                                   placeholder="Enter id" id="indexer-id">
                                            <div class="form-group">
                                                <label>Cycles</label>
                                                <input type="text" class="form-control" name="cycles"
                                                       placeholder="Enter count" id="indexer-cycles" value="10000">
                                                <small>Specify the number of cycles, for 1 cycle saves 5000 records from the database to elastic. Default value "500".</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button type="submit"
                                                    class="btn btn-secondary submit_save_reindex float-right float-lg-right">
                                                Submit
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="indexer-logs" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Reindex logs</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <ul class="logs">

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

        $(document).on("click", ".submit_save_reindex", function (e) {
            e.preventDefault();
            var url = "{{ route('indexer-state.save') }}";
            var formData = $(this).closest('form').serialize();

            $('#loading-image-preview').show();
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                success: function (resp) {
                    $('#loading-image-preview').hide();
                    if (resp.code == 200) {
                        toastr["success"](resp.message);
                        $.ajax({
                            url: "{{ route('indexer-state.index') }}",
                            method: 'GET',
                            success: function (resp) {
                                let reindexTable = $("#reindex-table");
                                reindexTable.empty();
                                reindexTable.html(resp.tpl);
                            }
                        })
                    } else {
                        toastr["error"](resp.message);
                    }
                },
                error: function (err) {
                    $('#loading-image-preview').hide();
                    toastr["error"](err.responseJSON.message);
                }
            })
        });

        $(document).on('click', 'a.btn-edit-indexer', function(e) {
            e.preventDefault();
            $('#indexer-settings').modal('show');

            restoreForm();

            $('#indexer-id').val($(this).attr('data-id'));

            let data = JSON.parse($(this).attr('data-json'));

            let settings = JSON.parse(data.settings);

            $('#indexer-cycles').val(settings.cycles);
            $('#indexer-id').val(data.id);
        });

        $(document).on('click', 'a.btn-reindex-logs', function(e) {
            e.preventDefault();
            $('#indexer-logs').modal('show');

            let indexerStateId = $(this).attr('data-id');
            let url = "{{ route('indexer-state.logs') }}/" + indexerStateId;

            $.ajax({
                url: url,
                method: 'GET',
                success: function (resp) {
                    let data = resp.data.reverse();

                    $(".logs").empty();

                    for (let i = 0;i<data.length;i++) {
                        $(".logs").append("<li>" + data[i] + "</li>");
                    }
                },
                error: function (err) {
                    toastr["error"](err.responseJSON.data);
                }
            })
        });

        var restoreForm = function() {
            $('#indexer-cycles').val('');
            $('#indexer-id').val('');
        }

        $(document).ready(function() {
            var url = "{{ route('indexer-state.elastic-conn') }}";

            let elastic = $("#elasticConnection");
            $.ajax({
                url: url,
                method: 'GET',
                success: function (resp) {
                    if (resp.code == 200) {
                        elastic.html('<div class="alert alert-success" role="alert">'+resp.message+'</div>');
                    } else {
                        elastic.html('<div class="alert alert-danger" role="alert">'+resp.message+'</div>');
                    }
                },
                error: function (err) {
                    toastr["error"](err.responseJSON.message);
                    elastic.html('<div class="alert alert-danger" role="alert">'+err.responseJSON.message+'</div>');
                }
            })
        });

        $(document).on('click', 'a.btn-reindex-indexer', function (e) {
            e.preventDefault();

            let indexerStateId = $(this).attr('data-id');
            console.log($(this).attr('stop-reindex'));
            let stopReindex = $(this).attr('stop-reindex') == 1 ? true : false;
            console.log(stopReindex);

            var url = "{{ route('indexer-state.reindex') }}?id=" + indexerStateId;

            if (stopReindex === true) {
                url = url + '&stop_reindex=1';
            }

            console.log(url);

            let reindexTable = $("#reindex-table");
            $('#loading-image-preview').show();
            $.ajax({
                url: url,
                method: 'GET',
                success: function (resp) {
                    toastr["success"](resp.message);
                },
                error: function (err) {
                    toastr["error"](err.responseJSON.message);
                }
            })
            setTimeout(function() {
                $.ajax({
                    url: "{{ route('indexer-state.index') }}",
                    method: 'GET',
                    success: function (resp) {
                        let reindexTable = $("#reindex-table");
                        reindexTable.empty();
                        reindexTable.html(resp.tpl);
                    }
                })
            }, 2000)
            $('#loading-image-preview').hide();
        })

        $(document).on('click', '.btn-refresh-listing', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('indexer-state.index') }}",
                method: 'GET',
                success: function (resp) {
                    let reindexTable = $("#reindex-table");
                    reindexTable.empty();
                    reindexTable.html(resp.tpl);
                    toastr["success"]('Content refreshed.');
                }
            })
        })
    </script>
@endsection