@foreach ($orders as $order)
    <tr>
        <td> {{ $order->order_id }} </td>
        <?php $orderStatusHistories = $order->orderStatusHistories->pluck('new_status')->toArray(); ?>
        @foreach ($orderStatusList as $key => $orderStatus)
            <td>
                @if (in_array($key, $orderStatusHistories))
                    <i class="fa fa-check-circle-o text-secondary fa-lg" aria-hidden="true"></i>
                @else
                    <i class="fa fa-times-circle text-dark fa-lg" aria-hidden="true"></i>
                @endif
            </td>
        @endforeach()
    </tr>
@endforeach()
