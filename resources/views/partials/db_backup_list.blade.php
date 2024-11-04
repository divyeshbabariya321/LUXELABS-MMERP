@foreach ($dbLists as $index => $dberrorlist)
    @php
        $sNo = $index + 1 + (($dbLists->currentPage() - 1) * $dbLists->perPage());
        $errorSnippet = strlen($dberrorlist->error) > 15 ? substr($dberrorlist->error, 0, 15) . '...' : $dberrorlist->error;
    @endphp
    <tr>
        <td>{{ $sNo }}</td>
        <td>{{ $dberrorlist->server_name ?? '' }}</td>
        <td>{{ $dberrorlist->instance ?? '' }}</td>
        <td>{{ $dberrorlist->database_name ?? '' }}</td>
        <td class="expand-row-dblist" style="word-break: break-all">
            @if ($dberrorlist->error)
                <span class="td-mini-container">{{ $errorSnippet }}</span>
                <span class="td-full-container hidden">{{ $dberrorlist->error }}</span>
            @endif
        </td>
        <td>
            <input type="checkbox" name="is_resolved" value="1" data-id="{{ $dberrorlist->id }}" onchange="updateIsResolved(this)">
        </td>
        <td>{{ $dberrorlist->date ?? '' }}</td>
        <td>{{ $dberrorlist->dbStatusColour->name ?? '' }}</td>
    </tr>
@endforeach
