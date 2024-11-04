@foreach ($data as $value)
    <tr>
        @if(!empty($value) && !empty($value->brand))
            <td>{{ $value->brand->name }}</td>
        @endif
        <td>{{ $value->color_name }}</td>
        <td><input type="text" class="update-color-code" data-id="{{ $value->id }}" value="{{ $value->color_code }}" style="width: 100%;"/></td>
    </tr>
@endforeach