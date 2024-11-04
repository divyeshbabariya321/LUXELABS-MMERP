@foreach ($usersystemips as $v)
    <tr>
        <td>{{ $v->index_txt }}</td>
        <td>{{ $v->ip }}</td>
        <td>{{ $v->user ? $v->user->name : $v->other_user_name }}</td>
        <td>{{ $v->source }}</td>
        <td>{{ $v->notes }}</td>
        <td>{{ $v->command }}</td>
        <td>{{ $v->status }}</td>
        <td>{{ $v->message }}</td>
        <td>
            <button class="btn-warning btn deleteIp" data-usersystemid="{{ $v->id }}">Delete</button>
        </td>
    </tr>
@endforeach
