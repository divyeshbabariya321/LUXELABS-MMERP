@if ($getHistory->count())
    @foreach ($getHistory as $value)
        <tr>
            <td>{{ $value->id ?? '-' }}</td>
            <td>{{ $value->userName ?? '-' }}</td>
            <td>
                <div style="width: 86%; float: left;" class="expand-row-msg" data-name="lan_message"
                    data-id="{{ $value->id }}">
                    <span
                        class="show-short-lan_message-{{ $value->id }}">{{ Str::limit($value->message, 30, '...') }}</span>
                    <span style="word-break: break-all;" id="show-full-lan_message-{{ $value->id }}"
                        class="show-full-lan_message-{{ $value->id }} hidden">{{ $value->message }}</span>
                </div>
                <i class="fa fa-copy" data-text="{{ $value->message }}"></i>
            </td>
            <td>{{ $value->status_name ?? '-' }}</td>
            <td class="cls-created-date">{{ $value->created_at ?? '' }}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="6">No records found.</td>
    </tr>
@endif
