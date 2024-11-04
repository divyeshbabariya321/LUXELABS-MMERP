@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Scrap Links</h2>
        </div>

        <div class="col-md-12">
            <form class="form-inline" method="GET">
                <div class="form-group ml-3">
                    {{ html()->text("search", request()->get("search", ""))->class("form-control")->placeholder("Enter keyword for search") }}
                </div>
                <div class="form-group ml-3">
                    <select class="form-control" name="status">
                        <option value="">Select Status</option>
                        <option value="in stock" {{request()->get('status') == 'in stock' ? 'selected' : ''}}>in stock</option>
                        <option value="out of stock" {{request()->get('status') == 'out of stock' ? 'selected' : ''}}>out of stock</option>
                        <option value="new" {{request()->get('status') == 'new' ? 'selected' : ''}}>new</option>
                    </select>
                </div>

                <div class="form-group ml-3">
                    {{ html()->input('date', "selected_date", request()->get("selected_date", ""))->class("form-control") }}
                </div>

                <button type="submit" class="btn ml-2"><i class="fa fa-filter"></i></button>
                <a href="/scrap/scrap-links" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
            </form>
        </div>

        <div class="col-md-12">
            <div class="table-responsive mt-3 col-lg-12 margin-tb">
                <table class="table table-bordered table-striped sort-priority-scrapper">
                    <thead>
                        <tr>
                            <th width="5%">Id</th>
                            <th width="10%">Website</th>
                            <th>Link</th>
                            <th width="10%">Status</th>
                            <th width="10%">Created at</th>
                        </tr>
                    </thead>
                    <tbody class="conent">
                        @foreach ($scrap_links as $links)
                            <tr>
                                <td>{{ $links->id }}</td>
                                <td>{{ $links->website }}</td>
                                <td><a href="{{ $links->links }}" target="_blank">{{ $links->links }}</a></td>
                                <td>
                                    {{ $links->status }}
                                    <button type="button" data-id="{{ $links->id  }}" class="btn btn-image status-history-show p-0 ml-2 pull-right"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>
                                </td>
                                <td>{{ $links->created_at }}</td>
                            </tr>
                        @endforeach
                   </tbody>

                </table>
                {{$scrap_links->links()}}
            </div>
        </div>
    </div>

    <div id="scrap-links-status-histories-list" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Histories</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="30%">Status</th>
                                    <th width="30%">Created Date</th>
                                </tr>
                            </thead>
                            <tbody class="scrap-links-status-histories-list-view">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ mix('webpack-dist/css/media-card.css') }} ">
@endsection
@section('scripts')

<script>
    // Load settings value Histories
    $(document).on('click', '.status-history-show', function() {
        var id = $(this).attr('data-id');
        $.ajax({
            method: "GET",
            url: `{{ route('scrap_links.status.histories', [""]) }}/` + id,
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${v.status} </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#scrap-links-status-histories-list").find(".scrap-links-status-histories-list-view").html(html);
                    $("#scrap-links-status-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });
</script>
@endsection