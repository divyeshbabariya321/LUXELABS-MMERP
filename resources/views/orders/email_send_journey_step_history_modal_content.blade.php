@foreach($stepHistoryData as $historyItem)
    <tr>
        <td>{{ $historyItem->steps }}</td>
        <td>{{ $historyItem->created_at }}</td>
        <!-- Add other columns if needed -->
    </tr>
@endforeach