@foreach ($data as $shortnote)
    <tr>
        <td>{{ $shortnote->id }}</td>
        <td>{{ $shortnote->platform ? $shortnote->platform->name : '-' }}</td>
        <td>
            <button type="button" data-id="{{ $shortnote->id }}" data-type="title" class="btn list-code-shortcut-title-view" style="padding:1px 0px;">
                <i class="fa fa-eye" aria-hidden="true"></i>
            </button>
        </td>
        <td>
            <button type="button" data-id="{{ $shortnote->id }}" data-type="code" class="btn list-code-shortcut-title-view" style="padding:1px 0px;">
                <i class="fa fa-eye" aria-hidden="true"></i>
            </button>
        </td>
        <td>
            <button type="button" data-id="{{ $shortnote->id }}" data-type="description" class="btn list-code-shortcut-title-view" style="padding:1px 0px;">
                <i class="fa fa-eye" aria-hidden="true"></i>
            </button>
        </td>
        <td>
            <button type="button" data-id="{{ $shortnote->id }}" data-type="solution" class="btn list-code-shortcut-title-view" style="padding:1px 0px;">
                <i class="fa fa-eye" aria-hidden="true"></i>
            </button>
        </td>
        <td>{{ $shortnote->user_detail->name }}</td>
        <td>{{ $shortnote->supplier_detail ? $shortnote->supplier_detail->supplier : '-' }}</td>
        <td>
            @if ($shortnote->filename)
                <img src="{{ asset('codeshortcut-image/' . $shortnote->filename) }}" height="50" width="50">
            @else
                -
            @endif
        </td>
        <td>{{ $shortnote->created_at }}</td>
    </tr>
@endforeach
