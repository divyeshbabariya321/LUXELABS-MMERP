@foreach($redisJob as $rjData)
    <tr>
        <td>
            <a class="show-product-information text-dark" data-id="{{ $rjData->id }}" href="#">{{ $item->id }}</a>
        </td>
        <td class="expand-row-msg" data-name="{{$rjData->name}}" data-id="{{$rjData->id}}">
            {{$rjData->name}}
        </td>
        <td class="expand-row-msg" data-name="{{$rjData->type}}" data-id="{{$item->id}}">
            {{$rjData->type}}
        </td>
    </tr>
@endforeach()