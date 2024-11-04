@foreach ($usersop as $key => $value)
    <tr id="sid{{ $value->id }}" class="parent_tr"
        data-id="{{ $value->id }}">
        <td class="sop_table_id">{{ $value->id }}</td>
        <td class="expand-row-msg" data-name="name"
            data-id="{{ $value->id }}">
            <span
                class="show-short-name-{{ $value->id }}">{{ Str::limit($value->name, 17, '..') }}</span>
            <span style="word-break:break-all;"
                class="show-full-name-{{ $value->id }} hidden">{{ $value->name }}</span>
        </td>
        <td class="expand-row-msg Website-task " data-name="content"
            data-id="{{ $value->id }}">
            <span
                class="show-short-content-{{ $value->id }}">{{ Str::limit($value->content, 50, '..') }}</span>
            <span style="word-break:break-all;"
                class="show-full-content-{{ $value->id }} hidden">{{ $value->content }}</span>
        </td>
        <td class="p-1">
            <a href="javascript:;" data-id="{{ $value->id }}"
                class="menu_editor_edit btn btn-xs p-2">
                <i class="fa fa-edit"></i>
            </a>
            <a href="javascript:;" data-id="{{ $value->id }}"
                data-content="{{ $value->content }}"
                class="menu_editor_copy btn btn-xs p-2">
                <i class="fa fa-copy"></i>
            </a>
        </td>
    </tr>
@endforeach