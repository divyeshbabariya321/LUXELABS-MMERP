@extends('layouts.app')
@section('title', 'Pinterest Pins')
@section('styles')
    <style type="text/css">
        #myDiv {
            width: 100%;
            position: fixed;
            z-index: 99999;
            height: 100%;
            background: rgba(0,0,0,0.4);
        }

        #loading-image {
            position: fixed;
            top: 50%;
            right: 50%;
        }

        .btn-secondary, .btn-secondary:focus, .btn-secondary:hover {
            background: #fff;
            color: #757575;
            border: 1px solid #ddd;
            height: 32px;
            border-radius: 4px;
            padding: 5px 10px;
            font-size: 14px;
            font-weight: 100;
            line-height: 10px;
        }

        .link-button, .link-button:hover, .link-button:focus {
            text-decoration: none;
            line-height: 1.4;
        }
    </style>
@endsection
@section('content')
    <div id="myDiv" style="display:none;">
        <img id="loading-image" src="/images/pre-loader.gif"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">
                {!! $pinterestBusinessAccountMail->pinterest_account !!} Pins (<span
                        id="affiliate_count">{{ $pinterestPins->total() }}</span>)
            </h2>
            <div class="pull-left">
                <form action="{{route('pinterest.accounts.pin.index', [$pinterestBusinessAccountMail->id])}}">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <input name="name" type="text" class="form-control"
                                       value="{{ request('name') }}" placeholder="Search name">
                            </div>
                            <div class="col-md-3">
                                {{ html()->select("pinterest_board_id", ['' => 'Select board'] + $pinterestBoards, request('pinterest_board_id'))->class("form-control type-filter") }}
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-image">
                                    <img src="/images/filter.png"/>
                                </button>
                                <button type="reset"
                                        onclick="window.location='{{route('pinterest.accounts.pin.index', [$pinterestBusinessAccountMail->id])}}'"
                                        class="btn btn-image" id="resetFilter">
                                    <img src="/images/resend2.png"/>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6 pl-0 float-right">
                <button data-toggle="modal" data-target="#create-board" type="button"
                        class="float-right mb-3 mr-2 btn-secondary">New Pin
                </button>
                <a href="{!! route('pinterest.accounts.dashboard', [$pinterestBusinessAccountMail->id]) !!}"
                   type="button"
                   class="float-right mb-3 mr-2 btn-secondary link-button">Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered" id="affiliates-table">
            <thead>
            <tr>
                <th>No</th>
                <th>Title</th>
                <th>Link</th>
                <th>Board</th>
                <th>Ads Account</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($pinterestPins as $key => $pinterestPin)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $pinterestPin->title }}</td>
                    <td>{{ $pinterestPin->link }}</td>
                    <td>{{ $pinterestPin->board->name }}</td>
                    <td>{{ $pinterestPin->account->ads_account_name }}</td>
                    <td>
                        <button type="button" data-toggle="modal" data-target="#update-board"
                                onclick="editData('{!! $pinterestPin->id !!}')"
                                class="btn btn-image"><img src="/images/edit.png"></button>
                        <a class="btn-image"
                           href="{!! route('pinterest.accounts.pin.delete', [$pinterestBusinessAccountMail->id, $pinterestPin->id]) !!}"
                           title="Delete Board"><img src="/images/delete.png"/></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {!! $pinterestPins->render() !!}
    <div class="modal fade" id="create-board" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Create Pin</h2>
                    </div>
                    @include('pinterest._partials.pins-create')
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="update-board" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Update Pin</h2>
                    </div>
                    @include('pinterest._partials.pins-update')
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>

    <script type="text/javascript">
        let showPopup;
        let showEditPopup;
        @if(Session::get('create_popup'))
            showPopup = true;
        @endif
                @if(Session::get('update_popup'))
            showEditPopup = true;
        @endif

        if (showPopup) {
            $('#create-board').modal('show');
        }

        if (showEditPopup) {
            $('#update-board').modal('show');
        }

        function updateValues() {
            let file = document.getElementById('media').files[0];
            $('#media_content_type').val(file.type)
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function () {
                $('#media_data').val(reader.result.split("base64,")[1]);
            };
            reader.onerror = function (error) {
                console.log('Error: ', error);
            };
        }

        function getSections(input, isEdit) {
            let val = $(input).val();
            if (val) {
                let url = "{{ route('pinterest.accounts.pin.board.sections', [$pinterestBusinessAccountMail->id, ':id']) }}";
                url = url.replace(':id', val);
                $.ajax({
                    url,
                    type: 'GET',
                    beforeSend: function () {
                        $("#myDiv").show();
                    },
                    success: function (response) {
                        $("#myDiv").hide();
                        if (!response.status) {
                            toastr["error"](response.message);
                        } else {
                            let html = '<option value="">Select</option>';
                            response.data.forEach(item => {
                                html += `<option value="${item.id}">${item.name}</option>`
                            })
                            if (isEdit) {
                                $('#edit_pinterest_board_section_id').html(html);
                            } else {
                                $('#pinterest_board_section_id').html(html);
                            }
                        }
                    }
                })
            }
        }

        function editData(id) {
            let url = "{{ route('pinterest.accounts.pin.get', [$pinterestBusinessAccountMail->id, ':id']) }}";
            url = url.replace(':id', id);
            $.ajax({
                url,
                type: 'GET',
                beforeSend: function () {
                    $("#myDiv").show();
                },
                success: function (response) {
                    $("#myDiv").hide();
                    if (!response.status) {
                        toastr["error"](response.message);
                        $('#update-board').modal('hide');
                    } else {
                        let {pin, sections} = response.data;
                        let html = '<option value="">Select</option>';
                        sections.forEach(item => {
                            html += `<option value="${item.id}">${item.name}</option>`
                        })
                        $('#edit_pinterest_board_section_id').html(html);
                        $('#edit_pin_id').val(id);
                        $('#edit_title').val(pin.title);
                        $('#edit_description').val(pin.description);
                        $('#edit_alt_text').val(pin.alt_text);
                        $('#edit_link').val(pin.link);
                        $('#edit_pinterest_board_id').val(pin.pinterest_board_id);
                        $('#edit_pinterest_board_section_id').val(pin.pinterest_board_section_id || '');
                    }
                }
            })
        }
    </script>
@endsection
