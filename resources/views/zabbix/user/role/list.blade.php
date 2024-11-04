<table class="table table-bordered overlay api-token-table">
    <thead>
    <tr>
        <th>Id</th>
        <th width="15%">Name</th>
        <th>Type</th>
        <th>Readonly</th>
        <th>Edit</th>
    </tr>
    </thead>
    <tbody>
    <?php /** @var \App\Models\Zabbix\Role $role */ ?>
    @foreach($roles as $role)
        <tr>
            <td class="td-id-{{ $role['roleid'] }}">
                {{ $role['roleid'] }}
            </td>
            <td class="td-rolename-{{ $role['roleid'] }}">
                {{ $role['name'] }}
            </td>
            <td class="td-name-{{ $role['roleid'] }}">
                {{ $role['type'] }}
            </td>
            <td class="td-surname-{{ $role['roleid'] }}">
                {{ $role['readonly'] }}
            </td>
            <td>
                <a href="#" class="btn btn-xs btn-secondary btn-edit-role td-edit-{{ $role['roleid'] }}" data-id="{{ $role['roleid'] }}" data-json='@json($role)'>Edit</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>