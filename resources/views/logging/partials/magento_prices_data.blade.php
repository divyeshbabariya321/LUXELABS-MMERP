@foreach($prices as $log)
<tr>
    <td>
    @if($log->product_id)
        {{$log->product_id}}
    @endif
    </td>
    <td>
        {{$log->default_price}}
    </td>
    <td>
        {{$log->discounted_percentage}}
    </td>
    <td>
        {{$log->segment_discount}}
    </td>
    <td>
        @if($log->segment_discount > 0)
        {{round($log->segment_discount * 100 / $log->default_price, 2).'%'}}
        @endif
    </td>
    <td>
        {{$log->duty_price}}
    </td>
    <td>
        <?php $ivaPercentage = \App\Product::IVA_PERCENTAGE;?>
        {{ $ivaPrice = $log->default_price + ($log->default_price * $ivaPercentage / 100) - $log->segment_discount}}
    </td>
    <td>
        {{$log->override_price}}
    </td>
    <td>
        {{round((($ivaPrice - $log->override_price) / $ivaPrice) * 100, 2).'%'}}
    </td>
    
    <td>
    @if($log->status)
        {{$log->status}}
    @endif
    </td>
    <td>
    @if($log->web_store_id)
        {{$log->web_store_name}}
    @endif
    </td>
    <td>
    @if($log->store_website_id)
        {{$log->store_website->title}}
    @endif
    </td>
   
</tr>
@endforeach
