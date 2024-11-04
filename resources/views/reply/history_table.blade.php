@forelse ($history as $entry)
    <tr>
        <td>{{ $entry->id }}</td>
        <td>{{ $entry->lang }}</td>
        <td>{{ $entry->translate_text }}</td>
        <td>{{ $entry->status }}</td>
        <td>{{ $entry->updater }}</td>
        <td>{{ $entry->approver }}</td>
        <td>{{ $entry->created_at }}</td>
    </tr>
@empty
    <tr>
        <td colspan="7">No translation history found.</td>
    </tr>
@endforelse