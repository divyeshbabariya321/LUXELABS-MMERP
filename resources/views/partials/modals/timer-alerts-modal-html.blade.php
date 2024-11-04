
<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th width="5%">S.No</th>
            <th width="15%">URL</th>
            <th width="10%">PayLoad</th>
            <th width="15%">Response</th>
            <th width="10%">user</th>
            <th width="8%">Response code</th>
        </tr>
    </thead>
    <tbody class="show-search-password-list">
        @foreach($currentLogs as $key => $currentLog)
        <tr>
            <td>{{ $key +1}}</td>
            <td>{{ strlen($currentLog->url) > 10 ? substr($currentLog->url, 0, 20).'...' : $currentLog->url }}</td>
            <td>{{ $currentLog->payload}}</td>
            <td>{{ strlen($currentLog->response) > 10 ? substr($currentLog->response, 0, 20).'...' : $currentLog->response }}</td>
            <td>{{ $currentLog->user->name}}</td>
            <td>{{ $currentLog->response_code}}</td>
        </tr>
        @endforeach
    </tbody>
</table>