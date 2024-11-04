<tr>
    <td>entered_in_product_push</td>
    <td> @if(in_array('entered_in_product_push', $pushJourney)) YES @else NO @endif</td>
</tr>
@foreach($conditions as $condition)
<tr>
    <td>{{$condition}}</td>
    <td>@if(in_array($condition, $pushJourney)) YES @else NO @endif</td>
</tr>
@endforeach
