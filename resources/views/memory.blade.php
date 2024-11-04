@extends('layouts.app')

@section('content')

    <h2 class="page-heading flex" style="padding: 8px 5px 8px 10px;border-bottom: 1px solid #ddd;line-height: 32px;">
        Memory Usage
        <div class="margin-tb" style="flex-grow: 1;">
            <div class="pull-right ">
                <div class="d-flex justify-content-between  mx-3">
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#thresold-limit-popup">
                        Update Threshold limit
                    </button>
                </div>
            </div>
        </div>
    </h2>

    {{-- modal for updated thresold limit --}}
    <div class="modal fade" id="thresold-limit-popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Update thresold limit</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">


                    <form method="POST" action="{{ route('update.thresold-limit') }}" id="thresold-limit-form">
                        @csrf
                        <div class="form-group mb-0">
                            <input type="number" class="form-control" id="thresold-limit" name="limit"
                                placeholder="Enter thresold limit (%)" min="0" max="100" value="{{$thresold_limit}}" required>

                        </div>

                        <div class="form-group d-flex justify-content-between mt-3">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button class="btn btn-secondary">Update</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>


    <div class="col-md-12">
        <div class="infinite-scroll">
            {!! $memoryUses->appends(request()->query())->links() !!}
            <div class="table-responsive">


                <table class="table table-bordered" style="table-layout:fixed;">
                    <tr>
                        <th>Total(MB)</th>
                        <th>Used(MB)</th>
                        <th>Free(MB)</th>
                        <th>Buff & Cache(MB)</th>
                        <th>Available(MB)</th>
                        <th>Created At</th>
                    </tr>
                    @foreach ($memoryUses as $memory)
                        <tr>
                            <td>{{ $memory->total }}</td>
                            <td>{{ $memory->used }}</td>
                            <td>{{ $memory->free }}</td>
                            <td>{{ $memory->buff_cache }}</td>
                            <td>{{ $memory->available }}</td>
                            <td>{{ \Carbon\Carbon::parse($memory->created_at)->format(' F j, Y') }}</td>
                        </tr>
                    @endforeach



                </table>

            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
    <script type="text/javascript">
        $('ul.pagination').hide();
        $(function() {
            $('.infinite-scroll').jscroll({
                autoTrigger: true,
                loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
                padding: 2500,
                nextSelector: '.pagination li.active + li a',
                contentSelector: 'div.infinite-scroll',
                callback: function() {

                    $('ul.pagination').remove();
                    $(".select-multiple").select2();
                    initialize_select2();
                }
            });
        });


        $(document).on('submit', '#thresold-limit-form', function(e) {
            e.preventDefault()
            $this = $(this)

            $.ajax({

                type: 'POST',
                url: "{{ route('update.thresold-limit') }}",
                dataType: 'json',
                beforeSend: function() {
                    $("#loading-image").show();



                },
                data: $(this).serialize()
            }).done(function(response) {


                if (response.code == 200) {
                    $("#loading-image").hide();
                    toastr['success'](response.message, 'success');
                    $('#thresold-limit-popup').modal('hide')

                }

            }).fail(function(response) {
                $("#loading-image").hide();
                alert('fail')
            })


        })
    </script>


@endsection
