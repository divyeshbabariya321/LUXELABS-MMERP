@foreach ($users as $user)
    @php
        $userEmail = $user->email ? "<span class='copy_me'>{$user->email}</span> <a href='javascript:void(0)' class='copy_the_text'><i class='fa fa-copy' aria-hidden='true'></i></a>" : "";
        $userName = $user->name ? "<span class='copy_me'>{$user->name}</span> <a href='javascript:void(0)' class='copy_the_text'><i class='fa fa-copy' aria-hidden='true'></i></a>" : "";
        $userPhone = $user->phone ? "<span class='copy_me'>{$user->phone}</span> <a href='javascript:void(0)' class='copy_the_text'><i class='fa fa-copy' aria-hidden='true'></i></a>" : "";
    @endphp
    <tr>
        <td>{{ $user->id }}</td>
        <td>{!! $userName !!}</td>
        <td>{!! $userEmail !!}</td>
        <td>{!! $userPhone !!}</td>
    </tr>
@endforeach
