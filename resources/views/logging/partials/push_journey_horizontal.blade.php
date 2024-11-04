

<tr>
    <td>SKU</td>
    <td>entered_in_product_push</td>
    @foreach($conditions as $condition)
        <td>{{$condition->condition}}</td>
    @endforeach
</tr>

<tr>
    <td>{{$productSku}}</td>
    <td> @if(in_array('entered_in_product_push', $pushJourney)) <i class="fa fa-check-circle-o text-success fa-lg" aria-hidden="true"></i> @else <i class="fa fa-times-circle text-danger fa-lg" aria-hidden="true"></i> @endif</td>
    @foreach($conditions as $condition)
        
        <td>
            @if($condition->status == 0)
                <i class="fa fa-ban text-danger fa-lg" aria-hidden="true"></i>
            @elseif(in_array($condition->condition, $pushJourney))
                <i class="fa fa-check-circle-o text-success fa-lg" aria-hidden="true"></i>
            @else
                <i class="fa fa-times-circle text-danger fa-lg" aria-hidden="true"></i>
            @endif
        </td>
    @endforeach
</tr>