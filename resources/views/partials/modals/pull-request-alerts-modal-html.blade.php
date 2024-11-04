<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th width="15%">Title</th>
            <th width="10%">User</th> 
            <th width="10%">deploy</th> 
            <th width="10%">Action</th> 
        </tr>
    </thead>
    <tbody class="show-search-password-list">
        @foreach($pullRequests as $pullRequest)
        <tr>
            <td>{{ $pullRequest['title']}}</td>
            <td>{{ $pullRequest['username']}}</td> 
            <td><a class="btn btn-sm btn-secondary" href="{{ url('/github/repos/'.$repo->id.'/deploy?branch='.urlencode($pullRequest['source'])) }}">Deploy</a></td>
            <td>
                <div>
                    <a class="btn btn-sm btn-secondary" href="{{url('/github/repos/'.$repo->id.'/branch/merge?source=master&destination='.urlencode($pullRequest['source']))}}">
                        Merge from master
                    </a>
                </div>
                <div style="margin-top: 5px;">
                    <button class="btn btn-sm btn-secondary" onclick="confirmMergeToMaster('{{$pullRequest["source"]}}','{{url('/github/repos/'.$repo->id.'/branch/merge?destination=master&source='.urlencode($pullRequest['source']))}}')">
                        Merge into master
                    </button>
                </div>
            </td>

        </tr>
        @endforeach
    </tbody>
</table>