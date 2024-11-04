@if (isset($userAccess))
    @forelse ($userAccess as $access)
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$access->user->name}}</td>
            <td>{{$access->oldUser?->name}}</td>
            <td>{{$access->newUser?->name}}</td>
            <td>{{$access->created_at}}</td>
        </tr>
    @empty
        <tr>
            <td colspan="3">No record found</td>
        </tr>
    @endforelse
@else
    <tr>
        <td colspan="3">No record found</td>
    </tr>
@endif