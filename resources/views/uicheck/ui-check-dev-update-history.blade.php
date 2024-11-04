@if ($getHistory->count())
            @foreach ($getHistory as $value)
                <tr>
                    <td>{{ $value->id ?? '-' }}</td>
                    <td>{{ $value->userName ?? '-' }}</td>
                    <td>
                        <div style="width: 86%; float: left;" class="expand-row-msg" data-name="dev_message" data-id="{{ $value->id }}">
                            <span class="show-short-dev_message-{{ $value->id }}">{{ Str::limit($value->message, 30, '...') }}<i class="fa-solid fa-copy"></i></span>
                            <span style="word-break: break-all;" class="show-full-dev_message-{{ $value->id }} hidden">{{ $value->message }}<i class="fa-solid fa-copy"></i></span>
                        </div>
                        <i class="fa fa-copy" data-text="{{ $value->message }}"></i>
                    </td>
                    <td>{{ $value->expected_start_time ?? '-' }}</td>
                    <td>{{ $value->expected_completion_time ?? '-' }}</td>
                    <td>{{ $value->estimated_time ?? '-' }}</td>
                    <td class="cls-created-date">{{ $value->created_at ?? '' }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="7">No records found.</td>
            </tr>
        @endif