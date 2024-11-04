@foreach ($data as $key => $val)
    <tr data-id="{{ $val->id }}">
        <td>{{ ++$key }}</td>
        <td>{{ $val->website_id }}</td>
        <td>{{ $val->sku }}</td>
        <td>{{ $val->website }}</td>
        <td>{{ $val->category_names }}</td>
        <td>{{ $val->size }}</td>
        <td>{{ $val->brands }}</td>
        <td>{{ $val->size_chart_url }}</td>
        <td>{{ $val->dimensions }}</td>
        <td>{{ $val->composition }}</td>
        <td>
        @if ($val->images)
            <img src="{{ $val->images}}" style="height:100px;">
        @endif
        </td>
        <td>{{ $val->english }}</td>
        <td>{{ $val->arabic }}</td>
        <td>{{ $val->german }}</td>
        <td>{{ $val->spanish }}</td>
        <td>{{ $val->french }}</td>
        <td>{{ $val->italian }}</td>
        <td>{{ $val->japanese }}</td>
        <td>{{ $val->korean }}</td>
        <td>{{ $val->russian }}</td>
        <td>{{ $val->chinese }}</td>
        <td>{{ $val->status }}</td>
        <td><button class="btn btn-image delete_api_search_history" data-id="{{ $val->id }}"><i class="fa fa-trash"></i></button></td>
    </tr>
@endforeach