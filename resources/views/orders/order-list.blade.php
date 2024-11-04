@if (!empty($dynamicColumnsToShowPostman))
    <tr style="background-color: {{ $order->status?->color }}";
        class="{{ \App\Helpers::statusClass($order->assign_status) }}">

        <td class="text-center">
            <span class="td-mini-container">
                <input type="checkbox" class="selectedOrder" name="selectedOrder" value="{{ $order->id }}">
            </span>
        </td>

        @if (!in_array('ID', $dynamicColumnsToShowPostman))
            <td class="table-hover-cell">
                <div class="form-inline ">
                    @if ($order->is_priority == 1)
                        <strong class="text-danger mr-1">!!!</strong>
                    @endif
                    <span class="td-mini-container">
                        <span style="font-size:14px;" class="toggle-title-box has-small"
                            data-small-title="<?php echo $order->order_id ? substr($order->order_id, 0, 15) : ''; ?>" data-full-title="<?php echo $order->order_id ? $order->order_id : ''; ?>">
                            <?php echo strlen($order->order_id) > 15 ? substr($order->order_id, 0, 15) . '..' : $order->order_id;
                            ?>
                        </span>
                    </span>
                </div>
            </td>
        @endif

        @if (!in_array('Date', $dynamicColumnsToShowPostman))
            <td class="Website-task" title="{{ Carbon\Carbon::parse($order->order_date)->format('d-m') }}">
                {{ Carbon\Carbon::parse($order->order_date)->format('d-m') }}</td>
        @endif

        @if (!in_array('Client', $dynamicColumnsToShowPostman))
            <td class="expand-row table-hover-cell Website-task" style="color:grey;">
                @if ($order->customer)
                    <span class="td-mini-container">
                        <a style="color: #6c757d;"
                            href="{{ route('customer.show', $order->customer->id) }}">{{ strlen($order->customer->name) > 15 ? substr($order->customer->name, 0, 13) . '...' : $order->customer->name }}</a>
                    </span>
                    <span class="td-full-container hidden">
                        <a style="color: #6c757d;"
                            href="{{ route('customer.show', $order->customer->id) }}">{{ $order->customer->name }}</a>
                    </span>
                @endif
            </td>
        @endif

        @if (!in_array('Site Name', $dynamicColumnsToShowPostman))
            <td class="expand-row table-hover-cell">
                @if ($order->storeWebsiteOrder)
                    @if ($order->storeWebsiteOrder->storeWebsite)
                        @php
                            $storeWebsite = $order->storeWebsiteOrder->storeWebsite;
                        @endphp
                        <span class="td-mini-container">
                            <a style="color: #6c757d;" href="{{ $storeWebsite->website }}"
                                target="_blank">{{ strlen($storeWebsite->website) > 15 ? substr($storeWebsite->website, 0, 13) . '...' : $storeWebsite->website }}</a>
                        </span>
                        <span class="td-full-container hidden">
                            <a style="color: #6c757d;" href="{{ $storeWebsite->website }}"
                                target="_blank">{{ $storeWebsite->website }}</a>
                        </span>
                    @endif
                @endif
            </td>
        @endif

        @if (!in_array('Products', $dynamicColumnsToShowPostman))
            <td class="expand-row table-hover-cell">
                @php $count = 0; @endphp
                <div class="d-flex">
                    <div class="">
                        @foreach ($order->order_product as $order_product)
                            @if ($order_product->product)
                                @if ($order_product->product->hasMedia($attach_image_tag) && $order_product->product->id == $items->product_id)
                                    <span class="td-mini-container">
                                        @if ($count == 0)
                                            <?php 
                                                foreach($order_product->product->getMedia($attach_image_tag) as $media) { ?>
                                            <a data-fancybox="gallery"
                                                href="{{ getMediaUrl($media) }}">#{{ $order_product->product->id }}<i
                                                    class="fa fa-eye"></i></a>
                                            <a class="view-supplier-details" data-id="{{ $order_product->id }}"
                                                href="javascript:;"><i class="fa fa-shopping-cart"></i></a>
                                            <br />
                                            <?php break; } ?>
                                            @php ++$count; @endphp
                                        @endif
                                    </span>
                                    <span class="td-full-container hidden">
                                        @if ($count >= 1)
                                            <?php foreach($order_product->product->getMedia($attach_image_tag) as $media) { ?>
                                            <a data-fancybox="gallery" href="{{ getMediaUrl($media) }}">VIEW
                                                <?php break; } ?>
                                                #{{ $order_product->product->id }}</a>
                                            @php $count++; @endphp
                                        @endif
                                    </span>
                                @endif
                            @endif
                        @endforeach
                    </div>
            </td>
        @endif

        @if (!in_array('Eta', $dynamicColumnsToShowPostman))
            <td>
                <div style="display:inline;">
                    {{ $order->estimated_delivery_date ? $order->estimated_delivery_date : '---' }}</div>

                <i style="color:#6c757d;" class="fa fa-pencil-square-o show-est-del-date" data-id="{{ $order->id }}"
                    data-new-est="{{ $order->estimated_delivery_date ? $order->estimated_delivery_date : '' }}"
                    aria-hidden="true"></i>
                <i style="color:#6c757d;" class="fa fa-info-circle est-del-date-history" data-id="{{ $order->id }}"
                    aria-hidden="true"></i>

            </td>
        @endif

        @if (!in_array('Brands', $dynamicColumnsToShowPostman))
            <td class="Website-task">
                <?php
                $totalBrands = explode(',', $order->brand_name_list);
                if (count($totalBrands) > 1) {
                    $str = 'Multi';
                } else {
                    $str = $order->brand_name_list;
                }
                ?>
                <span style="font-size:14px;">{{ $str }}</span>
            </td>
        @endif

        @if (!in_array('Order Status', $dynamicColumnsToShowPostman))
            <td class="expand-row table-hover-cell">
                <div class="form-group" style="margin-bottom:0px;">
                    <select data-placeholder="Order Status"
                        class="form-control order-status-select order-status-select-{{ $order->id }}" id="supplier"
                        data-id={{ $order->id }}>
                        <optgroup label="Order Status">
                            <option value="">Select Order Status</option>
                            @foreach ($order_status_list as $id => $status)
                                <option value="{{ $id }}"
                                    {{ $order->order_status_id == $id ? 'selected' : '' }}>{{ $status }}
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
            </td>
        @endif

        @if (!in_array('Order Product Status', $dynamicColumnsToShowPostman))
            <td class="expand-row table-hover-cell">
                {{-- <div class="form-group" style="margin-bottom:0px;">
                    <select data-placeholder="Order Product Status" class="form-control order-product-status-select"
                        data-order_product_id={{ $items->id }}>
                        <optgroup label="Order Product Status">
                            <option value="">Select Order Product Status</option>
                            @foreach ($order_status_list as $id => $status)
                                <option value="{{ $id }}"
                                    {{ $items->order_product_status_id == $id ? 'selected' : '' }}>{{ $status }}
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                </div> --}}
            </td>
        @endif

        @if (!in_array('Product Status', $dynamicColumnsToShowPostman))
            <td class="expand-row table-hover-cell">
                {{-- <div class="form-group" style="margin-bottom:0px;">
                    <select data-placeholder="Product Status" class="form-control product_order_status_delivery"
                        id="product_delivery_status" data-id={{ $order->id }}
                        data-order_product_item_id={{ $items->id }}>
                        <optgroup label="Product Status">
                            <option value="">Select product Status</option>
                            @foreach ($order_status_list as $id => $status)
                                <option value="{{ $id }}"
                                    {{ $items->delivery_status == $id ? 'selected' : '' }}>{{ $status }}
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                </div> --}}
            </td>
        @endif

        @if (!in_array('Advance', $dynamicColumnsToShowPostman))
            <td>{{ $order->advance_detail }}</td>
        @endif

        @if (!in_array('Balance', $dynamicColumnsToShowPostman))
            <td>{{ $order->balance_amount }}</td>
        @endif




        @if (!in_array('Waybill', $dynamicColumnsToShowPostman))
            <td>
                @if ($order->waybill)
                    {{ $order->waybill->awb }}
                @else
                    -
                @endif
            </td>
        @endif

        @if (!in_array('Price', $dynamicColumnsToShowPostman))
            <td>{{ $orderProductPrice * $productQty }}</td>
        @endif

        @if (!in_array('Shipping', $dynamicColumnsToShowPostman))
            <td>{{ $duty_shipping[$order->id]['shipping'] }}</td>
        @endif

        @if (!in_array('Duty', $dynamicColumnsToShowPostman))
            <td>{{ $duty_shipping[$order->id]['duty'] }}</td>
        @endif

        @if (!in_array('Action', $dynamicColumnsToShowPostman))
            <td>
                {{-- <button type="button" class="btn btn-secondary btn-sm mt-2"
                    onclick="Showactionbtn('{{ $items->id }}')"><i class="fa fa-arrow-down"></i></button> --}}
            </td>
        @endif
    </tr>

    {{-- <tr class="action-btn-tr-{{ $items->id }} d-none">
        <td>Action</td>
        <td colspan="16">
            <div class="align-items-center">
                <a type="button" title="Payment history"
                    class="btn btn-image pd-5 btn-ht cancel-transaction-btn pull-left" data-id="{{ $order->id }}">
                    <i class="fa fa-close"></i>
                </a>
                <a type="button" title="Payment history"
                    class="btn btn-image pd-5 btn-ht payment-history-btn pull-left" data-id="{{ $order->id }}">
                    <i class="fa fa-history"></i>
                </a>
                <a class="btn btn-image pd-5 btn-ht"
                    href="{{ route('purchase.grid') }}?order_id={{ $order->id }}">
                    <img title="Purchase Grid" style="display: inline; width: 15px;"
                        src="{{ asset('images/customer-order.png') }}" alt="">
                </a>
                <a class="btn btn-image pd-5 btn-ht" href="{{ route('order.show', $order->id) }}"><img
                        title="View order" src="{{ asset('images/view.png') }}" /></a>
                <a class="btn btn-image send-invoice-btn pd-5 btn-ht" data-id="{{ $order->id }}"
                    href="{{ route('order.show', $order->id) }}">
                    <img title="Send Invoice" src="{{ asset('images/purchase.png') }}" />
                </a>
                <a title="Preview Order" class="btn btn-image preview-invoice-btn pd-5 btn-ht"
                    href="{{ route('order.perview.invoice', $order->id) }}">
                    <i class="fa fa-hourglass"></i>
                </a>
                @if ($order->waybill)
                    <a title="Download Package Slip pd-5 btn-ht"
                        href="{{ route('order.download.package-slip', $order->waybill->id) }}" class="btn btn-image"
                        href="javascript:;">
                        <i class="fa fa-download" aria-hidden="true"></i>
                    </a>
                    <a title="Track Package Slip pd-5 btn-ht" href="javascript:;"
                        data-id="{{ $order->waybill->id }}" data-awb="{{ $order->waybill->awb }}"
                        class="btn btn-image track-package-slip">
                        <i class="fa fa fa-globe" aria-hidden="true"></i>
                    </a>
                @endif
                <a title="Generate AWB" data-order-id="<?php echo $order->id; ?>" data-items='@json($extraProducts)'
                    data-customer='<?php echo $order->customer ? json_encode($order->customer) : json_encode([]); ?>' class="btn btn-image generate-awb pd-5 btn-ht"
                    href="javascript:;">
                    <i class="fa fa-truck" aria-hidden="true"></i>
                </a>

                <a title="Preview Sent Mails" data-order-id="<?php echo $order->id; ?>"
                    class="btn btn-image preview_sent_mails pd-5 btn-ht" href="javascript:;"><i class="fa fa-eye"
                        aria-hidden="true"></i></a>

                <a title="View customer address" data-order-id="<?php echo $order->id; ?>"
                    class="btn btn-image customer-address-view pd-5 btn-ht" href="javascript:;">
                    <i class="fa fa-address-card" aria-hidden="true"></i>
                </a>


                {{ html()->form('DELETE', route('order.destroy', [$order->id]))->style('display:inline;margin-bottom:0px;height:30px;')->open() }}
                <button type="submit" class="btn btn-image pd-5 btn-ht"><img title="Archive Order"
                        src="{{ asset('images/archive.png') }}" /></button>
                {{ html()->form()->close() }}
                <?php
                if ($order->auto_emailed) {
                    $title_msg = 'Resend Email';
                } else {
                    $title_msg = 'Send Email';
                }
                ?>
                <a title="<?php echo $title_msg; ?>" class="btn btn-image send-order-email-btn pd-5 btn-ht"
                    data-id="{{ $order->id }}" href="javascript:;">
                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                </a>
                @if (auth()->user()->checkPermission('order-delete'))
                    {{ html()->form('DELETE', route('order.permanentDelete', [$order->id]))->style('display:inline;margin-bottom:0px;height:30px;')->open() }}
                    <button type="submit" class="btn btn-image pd-5 btn-ht"><img title="Delete Order"
                            src="{{ asset('images/delete.png') }}" /></button>
                    {{ html()->form()->close() }}
                @endif
                @if (!$order->invoice_id)
                    <a title="Add invoice" class="btn btn-image add-invoice-btn pd-5 btn-ht"
                        data-id='{{ $order->id }}'>
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </a>
                @endif
                <a title="Return / Exchange" data-id="{{ $order->id }}"
                    class="btn btn-image quick_return_exchange pd-5 btn-ht">
                    <i class="fa fa-product-hunt" aria-hidden="true"></i>
                </a>
                <button type="button" class="btn btn-image pd-5 btn-ht send-email-common-btn" title="Send Mail"
                    data-toemail="{{ $order->cust_email }}" data-object="order"
                    data-id="{{ $order->customer_id }}"><i class="fa fa-envelope-square"></i></button>
                <button type="button" class="btn btn-xs btn-image pd-5 btn load-communication-modal"
                    data-is_admin="{{ Auth::user()->hasRole('Admin') }}"
                    data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="order"
                    data-id="{{ $order->id }}" data-load-type="text" data-all="1" title="Load messages"><img
                        src="{{ asset('images/chat.png') }}" alt=""></button>
                @if ($order->cust_email)
                    <a class="btn btn-image pd-5 btn-ht" title="Order Mail PDF"
                        href="{{ route('order.generate.order-mail.pdf', ['order_id' => $order->id]) }}">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                    </a>
                @endif

                @if ($order->invoice_id)
                    <a title="Download Invoice" class="btn btn-image"
                        href="{{ route('order.download.invoice', $order->invoice_id) }}">
                        <i class="fa fa-download"></i>
                    </a>
                @endif
                <button type="button" class="btn btn-xs btn-image load-log-modal"
                    data-is_admin="{{ Auth::user()->hasRole('Admin') }}"
                    data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="order"
                    data-id="{{ $order->id }}" data-load-type="text" data-all="1" title="Show Error Log"><img
                        src="{{ asset('images/chat.png') }}" alt=""></button>
                <button type="button" title="Payment history"
                    class="btn btn-image pd-5 btn-ht magento-log-btn btn-xs pull-left" data-id="{{ $order->id }}">
                    <i class="fa fa-eye"></i>
                </button>
                <button type="button" title="Order Email send error"
                    class="btn  btn-xs btn-image pd-5 email_exception_list" data-id="{{ $order->id }}">
                    <i style="color:#6c757d;" class="fa fa-info-circle" data-id="{{ $order->id }}"
                        aria-hidden="true"></i>
                </button>
                <button type="button" title="Order Email Send Log"
                    class="btn  btn-xs btn-image pd-5 order_email_send_log" data-id="{{ $order->id }}">
                    <img src="{{ asset('/images/chat.png') }}" alt="">
                </button>
                <button type="button" title="Order SMS Send Log"
                    class="btn  btn-xs btn-image pd-5 order_sms_send_log" data-id="{{ $order->id }}">
                    <img src="{{ asset('/images/chat.png') }}" alt="">
                </button>
                <button type="button" title="Order return true" class="btn  btn-xs btn-image pd-5 order_return"
                    data-status="1" data-id="{{ $order->id }}">
                    <i style="color:#6c757d;" class="fa fa-check" data-id="{{ $order->id }}"
                        aria-hidden="true"></i>
                </button>
                <a title="Order return False" data-id="{{ $order->id }}" data-status="0"
                    class="btn btn-image order_return pd-5 btn-ht">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </a>
                <button type="button" data-id="{{ $order->id }}"
                    data-order_product_item_id="{{ $items->id }}"
                    class="btn btn-xs btn-image pd-5 order-status-change-history" style="padding:1px 0px;">
                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                </button>
            </div>
        </td>
    </tr> --}}
@else
    <tr style="background-color: {{ $order->status?->color }}";
        class="{{ \App\Helpers::statusClass($order->assign_status) }}">
        <td class="text-center"><span class="td-mini-container">
                <input type="checkbox" class="selectedOrder" name="selectedOrder" value="{{ $order->id }}">
            </span>
        </td>
        <td class="table-hover-cell">
            <div class="form-inline ">
                @if ($order->is_priority == 1)
                    <strong class="text-danger mr-1">!!!</strong>
                @endif
                <span class="td-mini-container">
                    <span style="font-size:14px;" class="toggle-title-box has-small"
                        data-small-title="<?php echo $order->order_id ? substr($order->order_id, 0, 15) : ''; ?>" data-full-title="<?php echo $order->order_id ? $order->order_id : ''; ?>">
                        <?php echo strlen($order->order_id) > 15 ? substr($order->order_id, 0, 15) . '..' : $order->order_id;
                        ?>
                    </span>
                </span>
            </div>
        </td>
        <td class="Website-task" title="{{ Carbon\Carbon::parse($order->order_date)->format('d-m') }}">
            {{ Carbon\Carbon::parse($order->order_date)->format('d-m') }}</td>
        <td class="expand-row table-hover-cell Website-task" style="color:grey;">
            @if ($order->customer)
                <span class="td-mini-container">
                    <a style="color: #6c757d;"
                        href="{{ route('customer.show', $order->customer->id) }}">{{ strlen($order->customer->name) > 15 ? substr($order->customer->name, 0, 13) . '...' : $order->customer->name }}</a>
                </span>
                <span class="td-full-container hidden">
                    <a style="color: #6c757d;"
                        href="{{ route('customer.show', $order->customer->id) }}">{{ $order->customer->name }}</a>
                </span>
            @endif
        </td>
        <td class="expand-row table-hover-cell">
            @if ($order->storeWebsiteOrder)
                @if ($order->storeWebsiteOrder->storeWebsite)
                    @php
                        $storeWebsite = $order->storeWebsiteOrder->storeWebsite;
                    @endphp
                    <span class="td-mini-container">
                        <a style="color: #6c757d;" href="{{ $storeWebsite->website }}"
                            target="_blank">{{ strlen($storeWebsite->website) > 15 ? substr($storeWebsite->website, 0, 13) . '...' : $storeWebsite->website }}</a>
                    </span>
                    <span class="td-full-container hidden">
                        <a style="color: #6c757d;" href="{{ $storeWebsite->website }}"
                            target="_blank">{{ $storeWebsite->website }}</a>
                    </span>
                @endif
            @endif
        </td>

        <td class="expand-row table-hover-cell">
            @php $count = 0; @endphp
            <div class="d-flex">
                <div class="">
                    @foreach ($order->order_product as $order_product)
                        @if ($order_product->product)
                            @if ($order_product->product->hasMedia($attach_image_tag) && $order_product->product->id == $items->product_id)
                                <span class="td-mini-container">
                                    @if ($count == 0)
                                        <?php foreach($order_product->product->getMedia($attach_image_tag) as $media) { ?>
                                        <a data-fancybox="gallery"
                                            href="{{ getMediaUrl($media) }}">#{{ $order_product->product->id }}<i
                                                class="fa fa-eye"></i></a>
                                        <a class="view-supplier-details" data-id="{{ $order_product->id }}"
                                            href="javascript:;"><i class="fa fa-shopping-cart"></i></a>
                                        <br />
                                        <?php break; } ?>
                                        @php ++$count; @endphp
                                    @endif
                                </span>
                                <span class="td-full-container hidden">
                                    @if ($count >= 1)
                                        <?php foreach($order_product->product->getMedia($attach_image_tag) as $media) { ?>
                                        <a data-fancybox="gallery" href="{{ getMediaUrl($media) }}">VIEW
                                            <?php break; } ?>
                                            #{{ $order_product->product->id }}</a>
                                        @php $count++; @endphp
                                    @endif
                                </span>
                            @endif
                        @endif
                    @endforeach
                </div>
            </div>
        </td>
        <td>
            <div style="display:inline;">
                {{ $order->estimated_delivery_date ? $order->estimated_delivery_date : '---' }}</div>

            <i style="color:#6c757d;" class="fa fa-pencil-square-o show-est-del-date" data-id="{{ $order->id }}"
                data-new-est="{{ $order->estimated_delivery_date ? $order->estimated_delivery_date : '' }}"
                aria-hidden="true"></i>
            <i style="color:#6c757d;" class="fa fa-info-circle est-del-date-history" data-id="{{ $order->id }}"
                aria-hidden="true"></i>

        </td>
        <td class="Website-task">
            <?php
            $totalBrands = explode(',', $order->brand_name_list);
            if (count($totalBrands) > 1) {
                $str = 'Multi';
            } else {
                $str = $order->brand_name_list;
            }
            ?>
            <span style="font-size:14px;">{{ $str }}</span>
        </td>
        <td class="expand-row table-hover-cell">
            <div class="form-group" style="margin-bottom:0px;">
                <select data-placeholder="Order Status"
                    class="form-control order-status-select order-status-select-{{ $order->id }}" id="supplier"
                    data-id={{ $order->id }}>
                    <optgroup label="Order Status">
                        <option value="">Select Order Status</option>
                        @foreach ($order_status_list as $id => $status)
                            <option value="{{ $id }}"
                                {{ $order->order_status_id == $id ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </optgroup>
                </select>
            </div>
        </td>
        <td class="expand-row table-hover-cell">
            {{-- <div class="form-group" style="margin-bottom:0px;">
                <select data-placeholder="Order Product Status" class="form-control order-product-status-select"
                    data-order_product_id={{ $items->id }}>
                    <optgroup label="Order Product Status">
                        <option value="">Select Order Product Status</option>
                        @foreach ($order_status_list as $id => $status)
                            <option value="{{ $id }}"
                                {{ $items->order_product_status_id == $id ? 'selected' : '' }}>{{ $status }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
            </div> --}}
        </td>
        <td class="expand-row table-hover-cell">
            {{-- <div class="form-group" style="margin-bottom:0px;">
                <select data-placeholder="Product Status" class="form-control product_order_status_delivery"
                    id="product_delivery_status" data-id={{ $order->id }}
                    data-order_product_item_id={{ $items->id }}>
                    <optgroup label="Product Status">
                        <option value="">Select product Status</option>
                        @foreach ($order_status_list as $id => $status)
                            <option value="{{ $id }}"
                                {{ $items->delivery_status == $id ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </optgroup>
                </select>
            </div> --}}
        </td>
        <td>{{ $order->advance_detail }}</td>
        <td>{{ $order->balance_amount }}</td>
        <td>
            @if ($order->waybill)
                {{ $order->waybill->awb }}
            @else
                -
            @endif
        </td>
        <td>{{ $orderProductPrice * $productQty }}</td>
        <td>{{ $duty_shipping[$order->id]['shipping'] }}</td>
        <td>{{ $duty_shipping[$order->id]['duty'] }}</td>
        <td>
            {{-- <button type="button" class="btn btn-secondary btn-sm mt-2"
                onclick="Showactionbtn('{{ $items->id }}')"><i class="fa fa-arrow-down"></i></button> --}}
        </td>
    </tr>
    {{-- <tr class="action-btn-tr-{{ $items->id }} d-none">
        <td>Action</td>
        <td colspan="16">
            <div class="align-items-center">
                <a type="button" title="Payment history"
                    class="btn btn-image pd-5 btn-ht cancel-transaction-btn pull-left"
                    data-id="{{ $order->id }}">
                    <i class="fa fa-close"></i>
                </a>
                <a type="button" title="Payment history"
                    class="btn btn-image pd-5 btn-ht payment-history-btn pull-left" data-id="{{ $order->id }}">
                    <i class="fa fa-history"></i>
                </a>
                <a class="btn btn-image pd-5 btn-ht"
                    href="{{ route('purchase.grid') }}?order_id={{ $order->id }}">
                    <img title="Purchase Grid" style="display: inline; width: 15px;"
                        src="{{ asset('images/customer-order.png') }}" alt="">
                </a>
                <a class="btn btn-image pd-5 btn-ht" href="{{ route('order.show', $order->id) }}"><img
                        title="View order" src="{{ asset('images/view.png') }}" /></a>
                <a class="btn btn-image send-invoice-btn pd-5 btn-ht" data-id="{{ $order->id }}"
                    href="{{ route('order.show', $order->id) }}">
                    <img title="Send Invoice" src="{{ asset('images/purchase.png') }}" />
                </a>
                <a title="Preview Order" class="btn btn-image preview-invoice-btn pd-5 btn-ht"
                    href="{{ route('order.perview.invoice', $order->id) }}">
                    <i class="fa fa-hourglass"></i>
                </a>
                @if ($order->waybill)
                    <a title="Download Package Slip pd-5 btn-ht"
                        href="{{ route('order.download.package-slip', $order->waybill->id) }}" class="btn btn-image"
                        href="javascript:;">
                        <i class="fa fa-download" aria-hidden="true"></i>
                    </a>
                    <a title="Track Package Slip pd-5 btn-ht" href="javascript:;"
                        data-id="{{ $order->waybill->id }}" data-awb="{{ $order->waybill->awb }}"
                        class="btn btn-image track-package-slip">
                        <i class="fa fa fa-globe" aria-hidden="true"></i>
                    </a>
                @endif
                <a title="Generate AWB" data-order-id="<?php echo $order->id; ?>"
                    data-items='@json($extraProducts)' data-customer='<?php echo $order->customer ? json_encode($order->customer) : json_encode([]); ?>'
                    class="btn btn-image generate-awb pd-5 btn-ht" href="javascript:;">
                    <i class="fa fa-truck" aria-hidden="true"></i>
                </a>

                <a title="Preview Sent Mails" data-order-id="<?php echo $order->id; ?>"
                    class="btn btn-image preview_sent_mails pd-5 btn-ht" href="javascript:;"><i class="fa fa-eye"
                        aria-hidden="true"></i></a>

                <a title="View customer address" data-order-id="<?php echo $order->id; ?>"
                    class="btn btn-image customer-address-view pd-5 btn-ht" href="javascript:;">
                    <i class="fa fa-address-card" aria-hidden="true"></i>
                </a>


                {{ html()->form('DELETE', route('order.destroy', [$order->id]))->style('display:inline;margin-bottom:0px;height:30px;')->open() }}
                <button type="submit" class="btn btn-image pd-5 btn-ht"><img title="Archive Order"
                        src="{{ asset('images/archive.png') }}" /></button>
                {{ html()->form()->close() }}
                <?php
                if ($order->auto_emailed) {
                    $title_msg = 'Resend Email';
                } else {
                    $title_msg = 'Send Email';
                }
                ?>
                <a title="<?php echo $title_msg; ?>" class="btn btn-image send-order-email-btn pd-5 btn-ht"
                    data-id="{{ $order->id }}" href="javascript:;">
                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                </a>
                @if (auth()->user()->checkPermission('order-delete'))
                    {{ html()->form('DELETE', route('order.permanentDelete', [$order->id]))->style('display:inline;margin-bottom:0px;height:30px;')->open() }}
                    <button type="submit" class="btn btn-image pd-5 btn-ht"><img title="Delete Order"
                            src="{{ asset('images/delete.png') }}" /></button>
                    {{ html()->form()->close() }}
                @endif
                @if (!$order->invoice_id)
                    <a title="Add invoice" class="btn btn-image add-invoice-btn pd-5 btn-ht"
                        data-id='{{ $order->id }}'>
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </a>
                @endif
                <a title="Return / Exchange" data-id="{{ $order->id }}"
                    class="btn btn-image quick_return_exchange pd-5 btn-ht">
                    <i class="fa fa-product-hunt" aria-hidden="true"></i>
                </a>
                <button type="button" class="btn btn-image pd-5 btn-ht send-email-common-btn" title="Send Mail"
                    data-toemail="{{ $order->cust_email }}" data-object="order"
                    data-id="{{ $order->customer_id }}"><i class="fa fa-envelope-square"></i></button>
                <button type="button" class="btn btn-xs btn-image pd-5 btn load-communication-modal"
                    data-is_admin="{{ Auth::user()->hasRole('Admin') }}"
                    data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="order"
                    data-id="{{ $order->id }}" data-load-type="text" data-all="1" title="Load messages"><img
                        src="{{ asset('images/chat.png') }}" alt=""></button>
                @if ($order->cust_email)
                    <a class="btn btn-image pd-5 btn-ht" title="Order Mail PDF"
                        href="{{ route('order.generate.order-mail.pdf', ['order_id' => $order->id]) }}">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                    </a>
                @endif

                @if ($order->invoice_id)
                    <a title="Download Invoice" class="btn btn-image"
                        href="{{ route('order.download.invoice', $order->invoice_id) }}">
                        <i class="fa fa-download"></i>
                    </a>
                @endif
                <button type="button" class="btn btn-xs btn-image load-log-modal"
                    data-is_admin="{{ Auth::user()->hasRole('Admin') }}"
                    data-is_hod_crm="{{ Auth::user()->hasRole('HOD of CRM') }}" data-object="order"
                    data-id="{{ $order->id }}" data-load-type="text" data-all="1" title="Show Error Log"><img
                        src="{{ asset('images/chat.png') }}" alt=""></button>
                <button type="button" title="Payment history"
                    class="btn btn-image pd-5 btn-ht magento-log-btn btn-xs pull-left"
                    data-id="{{ $order->id }}">
                    <i class="fa fa-eye"></i>
                </button>
                <button type="button" title="Order Email send error"
                    class="btn  btn-xs btn-image pd-5 email_exception_list" data-id="{{ $order->id }}">
                    <i style="color:#6c757d;" class="fa fa-info-circle" data-id="{{ $order->id }}"
                        aria-hidden="true"></i>
                </button>
                <button type="button" title="Order Email Send Log"
                    class="btn  btn-xs btn-image pd-5 order_email_send_log" data-id="{{ $order->id }}">
                    <img src="{{ asset('/images/chat.png') }}" alt="">
                </button>
                <button type="button" title="Order SMS Send Log"
                    class="btn  btn-xs btn-image pd-5 order_sms_send_log" data-id="{{ $order->id }}">
                    <img src="{{ asset('/images/chat.png') }}" alt="">
                </button>
                <button type="button" title="Order return true" class="btn  btn-xs btn-image pd-5 order_return"
                    data-status="1" data-id="{{ $order->id }}">
                    <i style="color:#6c757d;" class="fa fa-check" data-id="{{ $order->id }}"
                        aria-hidden="true"></i>
                </button>
                <a title="Order return False" data-id="{{ $order->id }}" data-status="0"
                    class="btn btn-image order_return pd-5 btn-ht">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </a>
                <button type="button" data-id="{{ $order->id }}"
                    data-order_product_item_id="{{ $items->id }}"
                    class="btn btn-xs btn-image pd-5 order-status-change-history" style="padding:1px 0px;">
                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                </button>
            </div>
        </td>
    </tr> --}}
@endif
