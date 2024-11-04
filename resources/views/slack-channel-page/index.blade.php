@extends('layouts.app')


@section('title', 'Slack Channel')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
@section('content')

    <style>
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0 0 -50px;
            z-index: 60
        }
    </style>

    <div id="myDiv">
        <img id="loading-image" src="{{ asset('/images/pre-loader.gif') }}" style="display:none;" />
    </div>
    <div class="col-md-12 p-0">
        <h2 class="page-heading">Slack channel page</h2>
    </div>
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{!! $message !!}</p>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger">
            <p>{{ $message }}</p>
        </div>
    @endif
    <div class="col-md-12">
        <div class="row">
            <div class="col-lg-12 margin-tb">

                <!-- <div class="row"> -->

                <form action="{{ url()->current() }}" method="GET" id="searchForm" class="form-inline align-items-start">
                    <div class="form-group col-md-1 mr-3s mb-3 no-pd">
                        <input name="term" type="text" class="form-control" value="{{ request('term') }}"
                            placeholder="Search.." style="width:100%;">
                    </div>

                    <div class="col-md-1 no-pd">
                        <button type="submit" class="btn mt-0 btn-image image-filter-btn"><img
                                src="{{ asset('/images/filter.png') }}" /></button>
                        <a href="{{ route('slack.channel.index') }}" class="btn btn-image" id=""><img
                                src="{{ asset('/images/resend2.png') }}" style="cursor: nwse-resize;"></a>
                    </div>

                    <div class="col-md-4">
                        <div class="align-right mb-">
                            <button type="button" class="btn btn-secondary new-channel" data-toggle="modal"
                                data-target="#myModal">New channel</button>
                        </div>
                    </div>
                    <!-- </div> -->
                </form>
            </div>

        </div>
    </div>
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered" id="store_website-analytics-table"style="table-layout: fixed;">
                <thead>
                    <tr>
                        <th width="10%">#ID</th>
                        <th width="20%">Channel ID</th>
                        <th width="20%">Channel Name</th>
                        <th width="30%">Description</th>
                        <th width="10%">status</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>
                <tbody class="searchable">
                    @foreach ($channelList as $key => $record)
                        <tr>
                            <td style="vertical-align:middle">{{ $record->id }}</td>
                            <td style="vertical-align:middle">{{ $record->channel_id }}</td>
                            <td style="vertical-align:middle">{{ $record->channel_name }}</td>
                            <td class="Website-task" style="vertical-align:middle">{{ $record->description }}</td>
                            <td>
                                <select class="form-control channel-status" name="channel-status">
                                    <option value="">Please Select status</option>
                                    <option value="active" {{ $record->status == 'active' ? 'selected' : '' }}
                                        data-id ="{{ $record->id }}">active</option>
                                    <option value="inactive" {{ $record->status == 'inactive' ? 'selected' : '' }}
                                        data-id ="{{ $record->id }}">inactive</option>
                                </select>
                            </td>
                            <td class="actions-main"style="vertical-align:middle">
                                <button type="button" class="btn mt-0 btn-secondary edit-channel"
                                    data-id="{{ $record->id }}"><i class="fa fa-edit"></i></button>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- The Modal -->

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModal">ChannelS</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="channeladd" action="{{ route('slack.channel.store') }}">
                    <div class="modal-body">
                        <div class="container-fluid">
                            @csrf
                            <div class="row subject-field">
                                <input type="hidden" id="channel_edit_id" name="channel_edit_id" value="0">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Channel Name</label>
                                        <input type="text" class="form-control" name="channel_name" id="channel_name"
                                            required="required" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Status:</label>
                                        <select class="form-control" name="channel_status" required>
                                            <option value="active">active</option>
                                            <option value="inactive">inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-form-label">Description:</label>
                                        <textarea class="form-control" name="description"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

<script>
    //change code to solve bug
    $(document).ready(function() {
        $(document).on('click', '.edit-channel', function(event) {
            $("#channel_name").prop('required', true);
            $("#channel_name").prop('required', true);
            $("#channel_status").prop('required', true);

            $('#channeladd')[0].reset();
            var id = $(this).data('id');
            $('#channel_edit_id').val('');

            $('#channel_edit_id').val(id)

            $.ajax({
                url: "{{ route('slack.channel.edit') }}",
                data: {
                    id: id
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(data) {
                $("#loading-image").hide();
                if (data.code == 200) {
                    $('input[name="channel_edit_id"]').val(data.object.id);
                    $('input[name="channel_name"]').val(data.object.channel_name);

                    $('select[name="channel_status"]').val(data.object.status).change();
                    $('textarea[name="description"]').val(data.object.description);


                    $('#myModal').modal('toggle');
                } else {
                    toastr['error']("Opps! Something went wrong, Please try again.");
                }
            }).fail(function(jqXHR, ajaxOptions, thrownError) {
                toastr['error']("Opps! Something went wrong, Please try again.");
            });

        });


        (function($) {
            $('#filter').keyup(function() {
                var rex = new RegExp($(this).val(), 'i');
                $('.searchable tr').hide();
                $('.searchable tr').filter(function() {
                    return rex.test($(this).text());
                }).show();
            })

        }(jQuery));
        
        $(document).on('change', '.channel-status', function(e) {
            if ($(this).val() != "" && ($('option:selected', this).attr('data-id') != "" || $('option:selected',
                    this).attr('data-id') != undefined)) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: "{{ route('slack.channel.status.update') }}",
                    data: {
                        status: $('option:selected', this).val(),
                        channel_id: $('option:selected', this).attr('data-id')
                    },
                    success: function(response) {
                        location.reload();
                        toastr['success'](response.message, 'success');
                    },
                    error: function(response) {
                        toastr['error']("An error occurred");
                    }
                })
            }
        });
    });

</script>
