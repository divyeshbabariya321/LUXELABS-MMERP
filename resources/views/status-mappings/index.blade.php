@extends('layouts.app')

@section('title', 'Status Mappings')

@section('styles')
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }

        .status-mappings ul {
            list-style-type: none;
        }
    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Status Mappings</h2>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-3 status-mappings">
                        <h3>Order Status</h3>
                        <ul>
                            @foreach ($orderStatuses as $orderStatusId => $orderStatus)
                                <li class="right" style="margin-right: 10px; margin-bottom: 5px">
                                    <a class="btn btn-secondary create-mapping" data-order-status-id="{{ $orderStatusId }}"
                                        href="javascript:void(0)">+</a> {{ $orderStatus }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h3>Purchase Status</h3>
                        <ul>
                            @foreach ($purchaseStatuses as $purchaseStatus)
                                <li class="right" style="margin-right: 10px; margin-bottom: 5px">
                                    {{ $purchaseStatus }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h3>Shipping Status</h3>
                        <ul>
                            @foreach ($shippingStatuses as $shippingStatus)
                                <li class="right" style="margin-right: 10px; margin-bottom: 5px">
                                    {{ $shippingStatus }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <h3>Return Exchange Status</h3>
                        <ul>
                            @foreach ($returnExchangeStatuses as $returnExchangeStatus)
                                <li class="right" style="margin-right: 10px; margin-bottom: 5px">
                                    {{ $returnExchangeStatus }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered" id="users-table">
            <thead>
                <tr>
                    <th>Order Status</th>
                    <th>Purchase Status</th>
                    <th>Shipping Status</th>
                    <th>Return Exchange Status</th>
                    <th>Updated By</th>
                    <th width="280px">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($statusMappings as $key => $statusMapping)
                    <tr>
                        <td>{{ $statusMapping->orderStatus->status }}</td>
                        <td class="number">
                            <select class="form-control ui-autocomplete-input purchase-status"
                                data-row-id="{{ $statusMapping->id }}">
                                <option value="0">-- Select --</option>
                                @foreach ($purchaseStatuses as $pkey => $purchaseStatus)
                                    <option value="{{ $pkey }}"
                                        @if ($statusMapping->purchase_status_id == $pkey) selected=selected @endif>
                                        {{ $purchaseStatus }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="number">
                            <select class="form-control ui-autocomplete-input shipping-status"
                                data-row-id="{{ $statusMapping->id }}">
                                <option value="0">-- Select --</option>
                                @foreach ($shippingStatuses as $skey => $shippingStatus)
                                    <option value="{{ $skey }}"
                                        @if ($statusMapping->shipping_status_id == $skey) selected=selected @endif>
                                        {{ $shippingStatus }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="number">
                            <select class="form-control ui-autocomplete-input return-exchange-status"
                                data-row-id="{{ $statusMapping->id }}">
                                <option value="0">-- Select --</option>
                                @foreach ($returnExchangeStatuses as $rkey => $returnExchangeStatus)
                                    <option value="{{ $rkey }}"
                                        @if ($statusMapping->return_exchange_status_id == $rkey) selected=selected @endif>
                                        {{ $returnExchangeStatus }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="updated-by">
                            {{ isset($statusMapping->statusMappingHistories[0]) ? $statusMapping->statusMappingHistories[0]->user->name : '' }}
                        </td>
                        <td>
                            <a class="btn btn-image padding-10-3 delete-mapping" data-row-id="{{ $statusMapping->id }}"
                                href="javascript:void(0)"><img src="/images/delete.png" /></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js">
    </script>

    <script type="text/javascript">
        $(document).on('click', '.create-mapping', function() {
            var orderStatusId = $(this).attr('data-order-status-id');

            if (confirm("Are you sure you want to create mapping with this status ?")) {
                $.ajax({
                    url: '{{ route('status-mapping.store') }}',
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    data: {
                        orderStatusId: orderStatusId
                    },
                    async: false,
                    success: function(response) {
                        if (response.status) {
                            toastr['success'](response.message);
                        } else {
                            toastr['error']('Something went wrong with ajax !');
                        }

                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    },
                    error: function(xhr, status, error) { // if error occured
                        if (xhr.status == 422) {
                            var errors = JSON.parse(xhr.responseText).errors;
                            customFnErrors(self, errors);
                        } else if (xhr.status == 500) {
                            toastr['error'](xhr.responseJSON.message);
                        } else {
                            toastr['error']('Something went wrong with ajax !');
                        }
                    },
                });
            }
        });

        $(document).on('change', '.purchase-status', function() {
            var rowId = $(this).attr("data-row-id");
            var purchaseStatusId = $(this).val();
            var updatedByTd = $(this).closest('tr').find('td.updated-by');

            $.ajax({
                url: '{{ route('status-mapping.update', '') }}/' + rowId,
                type: "PUT",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                data: {
                    statusType: "Purchase",
                    purchaseStatusId: purchaseStatusId
                },
                async: false,
                success: function(response) {
                    if (response.status) {
                        toastr['success'](response.message);
                        updatedByTd.text(response.data.lastUpdatedUser);
                    } else {
                        toastr['error']('Something went wrong with ajax !');
                    }
                },
                error: function(xhr, status, error) { // if error occured
                    if (xhr.status == 422) {
                        var errors = JSON.parse(xhr.responseText).errors;
                        customFnErrors(self, errors);
                    } else if (xhr.status == 500) {
                        toastr['error'](xhr.responseJSON.message);
                    } else {
                        toastr['error']('Something went wrong with ajax !');
                    }
                },
            });
        });

        $(document).on('change', '.shipping-status', function() {
            var rowId = $(this).attr("data-row-id");
            var shippingStatusId = $(this).val();
            var updatedByTd = $(this).closest('tr').find('td.updated-by');

            $.ajax({
                url: '{{ route('status-mapping.update', '') }}/' + rowId,
                type: "PUT",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                data: {
                    statusType: "Shipping",
                    shippingStatusId: shippingStatusId
                },
                async: false,
                success: function(response) {
                    if (response.status) {
                        toastr['success'](response.message);
                        updatedByTd.text(response.data.lastUpdatedUser);
                    } else {
                        toastr['error']('Something went wrong with ajax !');
                    }
                },
                error: function(xhr, status, error) { // if error occured
                    if (xhr.status == 422) {
                        var errors = JSON.parse(xhr.responseText).errors;
                        customFnErrors(self, errors);
                    } else if (xhr.status == 500) {
                        toastr['error'](xhr.responseJSON.message);
                    } else {
                        toastr['error']('Something went wrong with ajax !');
                    }
                },
            });
        });

        $(document).on('change', '.return-exchange-status', function() {
            var rowId = $(this).attr("data-row-id");
            var returnExchangeStatusId = $(this).val();
            var updatedByTd = $(this).closest('tr').find('td.updated-by');

            $.ajax({
                url: '{{ route('status-mapping.update', '') }}/' + rowId,
                type: "PUT",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                data: {
                    statusType: "Return Exchange",
                    returnExchangeStatusId: returnExchangeStatusId
                },
                async: false,
                success: function(response) {
                    if (response.status) {
                        toastr['success'](response.message);
                        updatedByTd.text(response.data.lastUpdatedUser);
                    } else {
                        toastr['error']('Something went wrong with ajax !');
                    }
                },
                error: function(xhr, status, error) { // if error occured
                    if (xhr.status == 422) {
                        var errors = JSON.parse(xhr.responseText).errors;
                        customFnErrors(self, errors);
                    } else if (xhr.status == 500) {
                        toastr['error'](xhr.responseJSON.message);
                    } else {
                        toastr['error']('Something went wrong with ajax !');
                    }
                },
            });
        });

        $(document).on('click', '.delete-mapping', function() {
            let $this = $(this)
            var result = window.confirm('Are you sure want to delete this mapping?');
            var updatedByTd = $(this).closest('tr').find('td.updated-by');

            if (result == true) {
                var rowId = $(this).data("row-id");
                var token = $("meta[name='csrf-token']").attr("content");

                $.ajax({
                    url: '{{ route('status-mapping.destroy', '') }}/' + rowId,
                    type: 'DELETE',
                    data: {
                        "_token": token,
                    },
                    success: function(response) {
                        $this.closest('tr').find('select').each(function(i, selected) {
                            $(this).val($(this).find('option:first').val());
                        });
                        updatedByTd.text(response.data.lastUpdatedUser);
                        toastr["success"](response.message, "Message")
                    }
                });
            }
        });
    </script>

@endsection
