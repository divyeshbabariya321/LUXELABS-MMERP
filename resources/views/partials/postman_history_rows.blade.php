@forelse($postHis as $history)
    <tr>
        <td>{{ $history->id }}</td>
        <td>{{ $history->userName }}</td>
        <td>{{ $history->created_at }}</td>
    </tr>
@empty
    <tr>
        <td colspan="3" class="text-center">No history found</td>
    </tr>
@endforelse
