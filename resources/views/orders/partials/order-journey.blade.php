@foreach ($orders as $order)
    @if(!empty($dynamicColumnsToShowoj))
        <tr>
            @if (!in_array('Order ID', $dynamicColumnsToShowoj))
                <td> {{ $order->order_id }} </td>
            @endif

            @if (!in_array('Products', $dynamicColumnsToShowoj))
                <td><a href="javascript:void(0)" data-id="{{ $order->id }}" id="order-products"><i class="fa fa-eye" aria-hidden="true"></i></a></td>
            @endif

            @if (!in_array('Customer', $dynamicColumnsToShowoj))
                <td>@if(!empty($order->customer->name)) {{$order->customer->name}} @else {{'-'}} @endif</td>
            @endif
            
            <?php $orderStatusHistories = $order->orderStatusHistories->pluck('created_at', 'new_status')->toArray(); ?>
            @foreach ($orderStatusList as $key => $orderStatus)
                <td>
                    @if (!in_array($orderStatus, $dynamicColumnsToShowoj))
                        @if (array_key_exists($key, $orderStatusHistories))
                            {{ $orderStatusHistories[$key] }}
                        @endif
                    @endif
                </td>
            @endforeach()
        </tr>
    @else 
        <tr>
            <td> {{ $order->order_id }} </td>
            <td><a href="javascript:void(0)" data-id="{{ $order->id }}" id="order-products"><i class="fa fa-eye" aria-hidden="true"></i></a></td>
            <td>@if(!empty($order->customer->name)) {{$order->customer->name}} @else {{'-'}} @endif</td>
            <?php $orderStatusHistories = $order->orderStatusHistories->pluck('created_at', 'new_status')->toArray(); ?>
            @foreach ($orderStatusList as $key => $orderStatus)
                <td>
                    @if (array_key_exists($key, $orderStatusHistories))
                        {{ $orderStatusHistories[$key] }}
                    @endif
                </td>
            @endforeach()
        </tr>
    @endif
@endforeach()
