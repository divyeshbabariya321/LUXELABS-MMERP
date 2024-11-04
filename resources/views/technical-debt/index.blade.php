@extends('layouts.app')
@section('title', 'Technical debt')
@section('content')

    <div class="row" id="product-template-page">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Technical Dept ({{ $technicaldebts->total() }})</h2>
            <div class="pull-left">
                <form action="{{ route('technical-debt-lists') }}" method="get" class="search">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <input name="problem" type="text" class="form-control" value="{{ request('problem') }}"
                                    placeholder="Search problem" id="search_problem">
                            </div>
                            <div class="col-md-3">
                                <input name="description" type="text" class="form-control"
                                    value="{{ request('description') }}" placeholder="search description"
                                    id="search_description">
                            </div>
                            <div class="col-md-3">
                                <input name="estimate" type="text" class="form-control" value="{{ request('estimate') }}"
                                    placeholder="search Estimate Investigation" id="search_estimate">
                            </div>
                            <div class="col-md-3">
                                <input name="approximate" type="text" class="form-control"
                                    value="{{ request('approximate') }}" placeholder="search Approximate Estimate"
                                    id="search_approximate">
                            </div>
                            <div class="col-md-3">
                                <br>
                                <input name="status" type="text" class="form-control" value="{{ request('status') }}"
                                    placeholder="search Status" id="search_status">
                            </div>
                            <div class="col-md-3">
                                <br>
                                <select class="form-control select-multiple" id="priority" name="priority">
                                    <option value="">Select Priority</option>
                                    @for ($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>

                            </div>

                            <div class="col-md-3">
                                <h5>Search FrameWork :</h5>
                                {{ html()->multiselect('frameworks_ids[]', \App\Models\TechnicalFrameWork::pluck('name', 'id')->toArray(), request('frameworks_ids'))->class('form-control globalSelect2')->data('placeholder', 'Select frameworks_ids') }}
                            </div>
                            <div class="col-md-3">
                                <h5>Search Users :</h5>
                                {{ html()->multiselect('usernames[]', \App\User::pluck('name', 'id')->toArray(), request('usernames'))->class('form-control globalSelect2')->data('placeholder', 'Select User') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-image search">
                            <img src="{{ asset('images/search.png') }}" alt="Search">
                        </button>
                        <a href="{{ route('technical-debt-lists') }}" class="btn btn-image" id=""><img
                                src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                    </div>
                </form>
            </div>
            <div class="pull-right pr-4">
                <button type="button" class="btn btn-secondary create-framework-btn" data-toggle="modal"
                    data-target="#create-framework">+ Add FrameWork</button>
                <button type="button" class="btn btn-secondary create-technical-debt-btn" data-toggle="modal"
                    data-target="#create_technical-debt">+ Add Techical Debt</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
        @include('partials.flash_messages')

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered" id="technicaldebt_table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>FrameWork name</th>
                        <th>problem</th>
                        <th>priority</th>
                        <th>Description</th>
                        <th>Estimate Investigation</th>
                        <th>Approximate Estimate</th>
                        <th>Remarks</th>
                        <th>status</th>
                        <th>Created By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($technicaldebts as $key => $technicaldebt)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $technicaldebt->technical_framework->name }}</td>
                            <td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                    {{ strlen($technicaldebt->problem) > 30 ? substr($technicaldebt->problem, 0, 30) . '...' : $technicaldebt->problem }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $technicaldebt->problem }}
                                </span>
                            </td>
                            <td>{{ $technicaldebt->priority }}</td>
                            <td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                    {{ strlen($technicaldebt->description) > 30 ? substr($technicaldebt->description, 0, 30) . '...' : $technicaldebt->description }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $technicaldebt->description }}
                                </span>
                            </td>
                            <td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                    {{ strlen($technicaldebt->estimate_investigation) > 30 ? substr($technicaldebt->estimate_investigation, 0, 30) . '...' : $technicaldebt->estimate_investigation }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $technicaldebt->estimate_investigation }}
                                </span>
                            </td>
                            <td class="expand-row" style="word-break: break-all">
                                <span class="td-mini-container">
                                    {{ strlen($technicaldebt->approximate_estimate) > 30 ? substr($technicaldebt->approximate_estimate, 0, 30) . '...' : $technicaldebt->approximate_estimate }}
                                </span>
                                <span class="td-full-container hidden">
                                    {{ $technicaldebt->approximate_estimate }}
                                </span>
                            </td>
                            <td>
                                <div style="width: 100%;">
                                    <div class="d-flex">
                                        <input type="text" name="remark_pop"
                                            class="form-control remark_pop{{ $technicaldebt->id }}"
                                            placeholder="Please enter remarks"
                                            style="margin-bottom:5px;width:100%;display:inline;">
                                        <button type="button" class="btn btn-sm btn-image add_message pointer"
                                            title="Send message" data-technicaldebt="{{ $technicaldebt->id }}">
                                            <img src="{{ asset('images/filled-sent.png') }}">
                                        </button>
                                        <button data-technicaldebt="{{ $technicaldebt->id }}"
                                            class="btn btn-xs btn-image show-technical-debt-remark" title="Remark"><img
                                                src="{{ asset('images/chat.png') }}" alt=""></button>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $technicaldebt->status }}</td>
                            <td>{{ $technicaldebt->user_detail->name }}</td>
                        </tr>
                    @endforeach
                </tbody>


            </table>
        </div>
    </div>


    <!-- Frame Work Modal content-->
    <div id="create-framework" class="modal fade in" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add FrameWork</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form action="{{ route('frame-work-store') }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="modal-body">
                        <div class="form-group">
                            {{ html()->label('Name', 'framework_name')->class('form-control-label') }}
                            {{ html()->text('framework_name')->class('form-control')->required()->attribute('rows', 3) }}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <div class="modal fade" id="create_technical-debt" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Create Technical Dept</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" id="technical-debt-from" action="{{ route('technical-debt-store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>FrameWork</label>
                                <select class="form-control select-multiple" id="frameWork-select" name="framework_id" required>
                                    <option value="">Select FrameWork</option>
                                    @foreach ($frameworks as $framework)
                                        <option value="{{ $framework->id }}">{{ $framework->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Problem</label>
                                {{ html()->text('problem')->class('form-control problem')->required() }}
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Priority</label>
                                <select class="form-control select-multiple" id="priority" name="priority" required>
                                    <option value="">Select Priority</option>
                                    @for ($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Description</label>
                                {{ html()->textarea('description')->class('form-control title') }}
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Estimate Investigation</label>
                                {{ html()->text('estimate_investigation  ')->class('form-control estimate_investigation') }}
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Approximate Estimate</label>
                                {{ html()->text('approximate_estimate')->class('form-control approximate_estimate') }}
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Status</label>
                                {{ html()->text('status')->class('form-control status')->required() }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div id="preview-technical-debt-get-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Technical debt Remark</h4>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:1%;">ID</th>
                                    <th style=" width: 12%">Update By</th>
                                    <th style="word-break: break-all; width:12%">Remark</th>
                                    <th style="width: 11%">Created at</th>
                                    <th style="width: 11%">Action</th>
                                </tr>
                            </thead>
                            <tbody class="technical-remark-get-list-view">
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

    <script type="text/javascript">
        $(document).on('click', '.expand-row', function() {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });

        $(document).on("click", ".add_message", function(e) {
            e.preventDefault();
            var technical_id = $(this).data('technicaldebt');
            var remark = $(`.remark_pop` + technical_id).val();

            technicalDeptRemark(technical_id, remark);
        });

        $(document).on("click", ".show-technical-debt-remark", function(e) {
            e.preventDefault();
            var technical_id = $(this).data('technicaldebt');
            var remark = $(`.remark_pop` + technical_id).val();

            technicalDeptRemark(technical_id, remark);
        });


        function technicalDeptRemark(technical_id, remark) {
            $.ajax({
                type: "GET",
                url: '{{ route('technical-debt-remark') }}',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    technical_id: technical_id,
                    remark: remark,
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                if (response.code == 200) {
                    $("#loading-image").hide();
                    if (remark == '') {
                        $("#preview-technical-debt-get-modal").modal("show");
                    }
                    $(".technical-remark-get-list-view").html(response.data);
                    $(`.remark_pop` + technical_id).val("");
                    toastr['success'](response.message);
                } else {
                    $("#loading-image").hide();
                    if (remark == '') {
                        $("#preview-technical-debt-get-modal").modal("show");
                    }
                    $(".technical-remark-get-list-view").html("");
                    toastr['error'](response.message);
                }

            }).fail(function(response) {
                $("#loading-image").hide();
                $("#preview-technical-debt-get-modal").modal("show");
                $(".technical-remark-get-list-view").html("");
                toastr['error'](response.message);
            });
        }
    </script>
@endsection
