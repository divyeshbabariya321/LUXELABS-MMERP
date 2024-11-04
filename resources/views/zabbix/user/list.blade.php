<table class="table table-bordered overlay api-token-table">
    <thead>
    <tr>
        <th>Id</th>
        <th width="15%">Username</th>
        <th width="20%">Name</th>
        <th width="20%">Surname</th>
        <th>Role ID</th>
        <th>Delete</th>
        <th>Edit</th>
    </tr>
    </thead>
    <tbody>
    <?php /** @var \App\Models\Zabbix\User $user */ ?>
    @foreach($users as $user)
        <tr>
            <td class="td-id-{{ $user->getId() }}">
                {{ $user->getId() }}
            </td>
            <td class="td-username-{{ $user->getId() }}">
                {{ $user->getUsername() }}
            </td>
            <td class="td-name-{{ $user->getId() }}">
                {{ $user->getName() }}
            </td>
            <td class="td-surname-{{ $user->getId() }}">
                {{ $user->getSurname() }}
            </td>
            <td class="td-role-id-{{ $user->getId() }}">
                {{ $roles[$user->getRoleId()]['name'] }}
            </td>
            <td class="td-delete-{{ $user->getId() }}">
                <a href="#" class="btn btn-xs btn-danger submit_delete_user td-edit-{{ $user->getId() }}" data-id="{{ $user->getId() }}" data-json='@json($user)'>Delete</a>
            </td>
            <td>
                <a href="#" class="btn btn-xs btn-secondary btn-edit-user td-edit-{{ $user->getId() }}" data-id="{{ $user->getId() }}" data-json='@json($user)'>Edit</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>