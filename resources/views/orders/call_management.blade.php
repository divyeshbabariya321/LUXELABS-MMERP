@extends('layouts.app')
@section('title', 'Call Management')
@section('content')

    <style>
        td audio {
            height: 30px;
        }

        td {
            padding: 5px 8px 0 !important;

        }

        #customer_order_details {
            padding: 10px 0 !important;
        }

    </style>

    <h2 class="page-heading flex" style="padding: 8px 5px 8px 10px;border-bottom: 1px solid #ddd;line-height: 32px;">
        Call Management
        <div class="margin-tb" style="flex-grow: 1;">
            <div class="pull-right ">
                <div class="d-flex align-items-center justify-content-between mx-3">
                    <a href="javascript:void(0)" style="font-size:13px;text-decoration:underline;margin-right:16px"
                        class="worker-activity-toggle ml-3"></a>
                    <div class="alert py-1 px-4 text-sm mb-0 worker-activity" style="font-size:13px;">Status Loading...</div>
                </div>
            </div>
        </div>
    </h2>




    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif





    <div class="col-md-6">
        <div class="col-md-12">
            <h3>All waiting calls</h3>
            <div class="table-responsive">
                <table id="show-ordres-table" class="table table-bordered table-hover" style="table-layout:fixed;">
                    <thead class="reserved-calls">
                        <tr>
                            <th>Customer Name</th>
                            <th>Customer Email</th>
                            <th>Store Website</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Call Time</th>
                        </tr>
                    </thead>
                    <tbody id="waiting-calls-table-body">

                        @foreach ($reservedCalls as $reservedCall)
                            <tr>
                                <td>
                                    {{ $reservedCall->name }}
                                </td>
                                <td>
                                    {{ $reservedCall->email }}
                                </td>
                                <td>
                                    {{ $reservedCall->storeWebsite->website ?? '' }}
                                </td>
                                <td>
                                    {{ $reservedCall->from }}
                                </td>
                                <td>
                                    {{ $reservedCall->to }}
                                </td>
                                <td>{{ $reservedCall->created_at }}</td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>


        <div class="col-md-12">
            <h3>Ticket</h3>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="2%">#</th>
                            <th width="2%">Subject</th>
                            <th width="2%">Message</th>
                            <th width="2%">Status</th>
                            <th width="2%">Created At</th>
                        </tr>
                    </thead>

                    <tbody class="current_call_ticket_data">
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-12">
            <h3>Credit</h3>
            <p>Remaining Credit : <strong class="remaining_credit">0</strong> </p>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="2%">#</th>
                            <th width="2%">Credit</th>
                            <th width="2%">Add/Deduction From</th>
                            <th width="2%">Transaction Type</th>
                            <th width="2%">Created At</th>
                        </tr>
                    </thead>

                    <tbody class="current_call_credit_data">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <h3>Current call information</h3>
        <ul class="d-none customer-call-information" style="margin-left: -1.5rem !important;">
            <li class="mx-4">Name : <b class="customer-call-name"></b> </li>
            <li class="mx-4">Email : <b class="customer-call-mail"></b> </li>
            <li class="mx-4">Phone : <b class="customer-call-number"></b> <button class="btn btn-xs btn-image load-customer-chat-button"><img src="/images/chat.png" alt=""></button> </li>
        </ul>
        <div class="table-responsive">
            <div class="col-md-12">
                <h3>Orders</h3>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>

                            <tr>
                                <th style="width: 10%">Customer Name</th>
                                <th style="width: 8%">Customer Email</th>
                                <th style="width: 10%">Order ID</th>
                                <th style="width: 10%">Client Name</th>
                                <th style="width: 10%">Store Website</th>
                                <th style="width: 10%">Order Status</th>
                                <th style="width: 8%">Order Created Date</th>
                            </tr>
                        </thead>

                        <tbody class="current_call_orders">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-12">
                <h3>Leads</h3>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="2%">ID</th>
                                <th width="4%">Lead ID</th>
                                <th width="2%">Customer Name</th>
                                <th width="2%">Color</th>
                                <th width="2%">Size</th>
                                <th width="2%">Min Price</th>
                                <th width="2%">Max Price</th>
                                <th width="2%">Brand Segment</th>
                                <th width="2%">Gender</th>
                                <th width="2%">Quantity</th>
                                <th width="2%">Product Name</th>
                                <th width="2%">Category</th>
                                <th width="2%">Brand</th>
                                <th width="2%">Status</th>
                            </tr>
                        </thead>
                        <tbody class="current_call_all_leads">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-12">
                <h3>Return and Exchange</h3>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="2%">ID</th>
                                <th width="2%">Customer Name</th>
                                <th width="2%">Product Name</th>
                                <th width="2%">Type</th>
                                <th width="2%">Refund Amount</th>
                                <th width="2%">Reason for Refund</th>
                                <th width="2%">Status</th>
                                <th width="2%">Pickup Address</th>
                                <th width="2%">Remarks</th>
                                <th width="2%">Created At</th>
                            </tr>
                        </thead>

                        <tbody class="current_call_return_and_exchange">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="customer-call-chat-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Chat History</h4>
                </div>
                <div class="modal-body" style="background-color: #999999;">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        setInterval(() => {
            $.ajax({
                url: '/twilio/get-waiting-call-list',
                type: 'POST',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name=csrf-token]').val()
                },
                success: function(res) {
                    let table = $("#waiting-calls-table-body");
                    table.empty()
                    if (res.calls.length > 0) {
                        let tableAppend = '';
                        res.calls.forEach((value) => {
                            tableAppend += `<tr>
                    <td>${value.name || ''}</td>
                    <td>${value.email || ''}</td>
                    <td>${(value.store_website) ? value.store_website.website : ''}</td>
                    <td>${value.from || ''}</td>
                    <td>${value.to || ''}</td>
                    <td>${value.created_at || ''}</td>
                    </tr>`;
                        })

                        table.append(tableAppend)
                    }
                }
            })
        }, 2000);
    </script>
@endsection
