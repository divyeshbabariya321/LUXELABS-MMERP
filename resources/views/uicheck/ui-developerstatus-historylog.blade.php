@foreach ($adminStatusLog as $adminStatus) {
    <td>{{ $adminStatus->id }}</td>
    <td>{{ $adminStatus->userName }}</td>
    <td>{{ $adminStatus->old_name }}</td>
    <td>{{ $adminStatus->dev_status }}</td>
    <td>{{ $adminStatus->created_at }}</td>
}
@endforeach