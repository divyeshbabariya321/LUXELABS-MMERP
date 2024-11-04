@foreach($datas as $index => $history)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $history->old_value ? $history->old_value->status_name : ' - ' }}</td>
        <td>{{ $history->new_value ? $history->new_value->status_name : ' - ' }}</td>
        <td>{{ $history->user ? $history->user->name : ' - ' }}</td>
        <td>{{ $history->created_at }}</td>
    </tr>
@endforeach
