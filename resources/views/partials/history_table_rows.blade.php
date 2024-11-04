@forelse($history as $entry)
    <tr>
        <td>{{ $entry->daily_activities_id }}</td>
        <td>{{ $entry->title }}</td>
        <td>{{ $entry->description }}</td>
        <td>{{ $entry->created_at }}</td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center">No data found</td>
    </tr>
@endforelse
