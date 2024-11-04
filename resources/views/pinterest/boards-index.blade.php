@extends('layouts.app')
@section('title', 'Pinterest Boards')
@section('styles')
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
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
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">
                {!! $pinterestBusinessAccountMail->pinterest_account !!} Boards (<span
                        id="affiliate_count">{{ $pinterestBoards->total() }}</span>)
            </h2>
            <div class="pull-left">
                <form action="{{route('pinterest.accounts.board.index', [$pinterestBusinessAccountMail->id])}}">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <input name="name" type="text" class="form-control"
                                       value="{{ request('name') }}" placeholder="Search name">
                            </div>
                            <div class="col-md-3">
                                {{ html()->select("account_id", ['' => 'Select ads account'] + $pinterestAdsAccount, request('account_id'))->class("form-control type-filter") }}
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-image">
                                    <img src="/images/filter.png"/>
                                </button>
                                <button type="reset"
                                        onclick="window.location='{{route('pinterest.accounts.board.index', [$pinterestBusinessAccountMail->id])}}'"
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
                        class="float-right mb-3 mr-2 btn-secondary">New Board
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
                <th>Name</th>
                <th>Ads Account</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($pinterestBoards as $key => $pinterestBoard)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $pinterestBoard->name }}</td>
                    <td>{{ $pinterestBoard->account->ads_account_name }}</td>
                    <td>
                        <button type="button" data-toggle="modal" data-target="#update-board"
                                onclick="editData('{!! $pinterestBoard->id !!}')"
                                class="btn btn-image"><img src="/images/edit.png"></button>
                        <a class="btn-image"
                           href="{!! route('pinterest.accounts.board.delete', [$pinterestBusinessAccountMail->id, $pinterestBoard->id]) !!}"
                           title="Delete Board"><img src="/images/delete.png"/></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {!! $pinterestBoards->render() !!}
    <div class="modal fade" id="create-board" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Create Board</h2>
                    </div>
                    @include('pinterest._partials.board-create')
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="update-board" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="col-md-12">
                    <div class="page-header" style="width: 69%">
                        <h2>Update Board</h2>
                    </div>
                    @include('pinterest._partials.board-update')
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

        function editData(id) {
            let url = "{{ route('pinterest.accounts.board.get', [$pinterestBusinessAccountMail->id, ':id']) }}";
            url = url.replace(':id', id);
            $.ajax({
                url,
                type: 'GET',
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function (response) {
                    $("#loading-image").hide();
                    if (!response.status) {
                        toastr["error"](response.message);
                        $('#update-board').modal('hide');
                    } else {
                        $('#edit_board_id').val(id);
                        $('#edit_pinterest_ads_account_id').val(response.data.pinterest_ads_account_id);
                        $('#edit_name').val(response.data.name);
                        $('#edit_description').val(response.data.description);
                    }
                }
            })
        }
    </script>
@endsection
