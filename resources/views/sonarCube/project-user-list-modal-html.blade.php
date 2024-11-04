<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th width="5%"> No</th>
            <th width="20%">Name</th>
            <th width="12%">Type</th>
            <th width="20%">Created At</th>
            <th width="20%">Last Connection Date</th>
        </tr>
    </thead>
    <tbody class="show-search-password-list">
        @foreach($projects['userTokens'] as $key=>$project)
        <tr>
            <td>{{ $key+1}}</td>
            <td>{{ $project['name'] }}</td>
            <td>{{ $project['type'] }}</td>
            <td class="expand-row" style="word-break: break-all">
                @if(isset($project['createdAt']) && $project['createdAt'])
                    {{ \Carbon\Carbon::parse($project['createdAt'])->format('m-d F') }}
                @else
                    -
                @endif
            </td>   
            <td class="expand-row" style="word-break: break-all">
                @if(isset($project['lastConnectionDate']) && $project['lastConnectionDate'])
                    {{ \Carbon\Carbon::parse($project['lastConnectionDate'])->format('m-d F') }}
                @else
                    -
                @endif
            </td>   
        </tr>
        @endforeach
    </tbody>
</table>
