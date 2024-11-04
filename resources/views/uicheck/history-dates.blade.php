@if ($data->count())
    @foreach ($data as $value)
        <tr>
            <td>{{ $value->id ?? '-' }}</td>
            <td>{{ $value->type ?? '-' }}</td>
            <td>{{ $value->old_val ?? '-' }}</td>
            <td>{{ $value->new_val ?? '-' }}</td>
            <td>{{ $value->updatedByName() ?? '-' }}</td>
            <td class="cls-created-date">{{ $value->created_at ?? '-' }}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="6">No records found.</td>
    </tr>
@endif
