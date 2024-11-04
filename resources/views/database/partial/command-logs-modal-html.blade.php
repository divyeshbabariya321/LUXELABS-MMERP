<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th> No </th>
            <th>Command</th>
            <th>Response</th>
            <th>Updated by</th>
            <th>Created Date</th>
        </tr>
    </thead>
    <tbody class="show-search-password-list">
        @foreach($histories as $history)
        <tr>
            <td>{{ $history->id }}</td>
            <td class="expand-row" style="word-break: break-all">
                <span class="td-mini-container">
                   {{ strlen($history->command) > 30 ? substr($history->command, 0, 30).'...' :  $history->command }}
                </span>
                <span class="td-full-container hidden">
                    {{ $history->command }}
                </span>
            </td>
            <td class="expand-row" style="word-break: break-all">
                <span class="td-mini-container">
                   {{ strlen($history->response) > 30 ? substr($history->response, 0, 30).'...' :  $history->response }}
                </span>
                <span class="td-full-container hidden">
                    {{ $history->response }}
                </span>
            </td>
            <td class="expand-row" style="word-break: break-all">
                <span class="td-mini-container">
                   {{ strlen($history->user->name) > 30 ? substr($history->user->name, 0, 30).'...' :  $history->user->name }}
                </span>
                <span class="td-full-container hidden">
                    {{ $history->user->name }}
                </span>
            </td>
            <td>{{ $history->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="pagination-container-db-command"></div>

