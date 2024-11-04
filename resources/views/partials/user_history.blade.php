@foreach ($users as $user)
    <tr>
        <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}</td>
        <td>{{ $user->user_type ?? '-' }}</td>
        <td>{{ $user->old_name ?? '-' }}</td>
        <td>{{ $user->new_name ?? '-' }}</td>

        <td>{{ $user->updated_by }}</td>
    </tr>
@endforeach
