<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th width="5%"> No</th>
            <th width="15%">Name</th>
            <th width="12%">Qualifier</th>
            <th width="20%">Visibility</th>
            <th width="25%">LastAnalysis Date</th>
        </tr>
    </thead>
    <tbody class="show-search-password-list">
        @foreach($projects['components'] as $key=>$project)
        <tr>
            <td>{{ $key+1}}</td>
            <td>{{ $project['name'] }}</td>
            <td>{{ $project['qualifier'] }}</td>
            <td>{{ $project['visibility'] }}</td>
            <td class="expand-row" style="word-break: break-all">
                @if(isset($project['lastAnalysisDate']) && $project['lastAnalysisDate'])
                    {{ \Carbon\Carbon::parse($project['lastAnalysisDate'])->format('m-d F') }}
                @else
                    -
                @endif
            </td>   
        </tr>
        @endforeach
    </tbody>
</table>
