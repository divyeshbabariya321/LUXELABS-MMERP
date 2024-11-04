<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th> No</th>
            <th width="8%">Status</th>
            <th width="12%">Command</th>
            <th width="20%">Error</th>
            <th width="25%">OutPut</th>
            <th width="20%">Updated by</th>
            <th width="25%">Created Date</th>
        </tr>
    </thead>
    <tbody class="show-search-password-list">
        @foreach($domainHistories as $domainHistory)
        <tr>
            <td>{{ $domainHistory->id }}</td>
            <td>{{ $domainHistory->status }}</td>
            <td>{{ $domainHistory->command}}</td>
            <td class="expand-row" style="word-break: break-all">
                <span class="td-mini-container">
                   {{ strlen($domainHistory->error) > 30 ? substr($domainHistory->error, 0, 30).'...' :  $domainHistory->error }}
                </span>
                <span class="td-full-container hidden">
                    {{ $domainHistory->error }}
                </span>
            </td>
            <td class="expand-row" style="word-break: break-all">
                <span class="td-mini-container">
                   {{ strlen($domainHistory->output) > 30 ? substr($domainHistory->output, 0, 30).'...' :  $domainHistory->output }}
                </span>
                <span class="td-full-container hidden">
                    {!! nl2br(e($domainHistory->output)) !!}
                </span>
            </td>
            <td class="expand-row" style="word-break: break-all">
                <span class="td-mini-container">
                   {{ strlen($domainHistory->user->name) > 30 ? substr($domainHistory->user->name, 0, 30).'...' :  $domainHistory->user->name }}
                </span>
                <span class="td-full-container hidden">
                    {{ $domainHistory->user->name }}
                </span>
            </td>
            <td>{{ $domainHistory->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="pagination-container-domain"></div>

