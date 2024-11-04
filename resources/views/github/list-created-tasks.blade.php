
<div class="modal-header">
    <h5 class="modal-title">Tasks List</h5>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body" >
    <table id="list-created-tasks-table" class="table table-bordered">
        <thead>
            <tr>
                <th>TaskID</th>
                <th>Date</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Assign To</th>
                <th>PullNumbers</th>
            </tr>
        </thead>
        <tbody>
            @if ($githubTaskPullRequests->count() > 0)
            @foreach($githubTaskPullRequests as $githubTaskPullRequest)
                @if(isset($githubTaskPullRequest['task']) && !@empty($githubTaskPullRequest['task']))
                <tr>
                    
                    <td>{{$githubTaskPullRequest['task']['id']}}</td>
                    <td>{{ Carbon\Carbon::parse($githubTaskPullRequest['task']['created_at'])->format('d-m-y H:i') }}</td>
                    <td class="expand-row" style="word-break: break-all">
                        <span class="td-mini-container">
                            {{ strlen($githubTaskPullRequest['task']['task_subject']) > 12 ? substr($githubTaskPullRequest['task']['task_subject'], 0, 12).'...' :  $githubTaskPullRequest['task']['task_subject'] }}
                        </span>
                        <span class="td-full-container hidden">
                            {{$githubTaskPullRequest['task']['task_subject']}}
                        </span>
                    </td>
                    <td class="expand-row" style="word-break: break-all">
                        <span class="td-mini-container">
                            {{ strlen($githubTaskPullRequest['task']['task_details']) > 12 ? substr($githubTaskPullRequest['task']['task_details'], 0, 12).'...' :  $githubTaskPullRequest['task']['task_details'] }}
                        </span>
                        <span class="td-full-container hidden">
                            {{$githubTaskPullRequest['task']['task_details']}}
                        </span>
                    </td>
                    <td>{{$githubTaskPullRequest['task']['assignedTo']['name']}}</td>
                    <td>{{$githubTaskPullRequest['pull_number_concatenated']}}</td>
                   
                </tr>
                @endif
            @endforeach
            @else
            <tr><td colspan="6"> No data found </td></tr>
            @endif
        </tbody>
    </table>
    <!-- Display pagination links -->
    {{ $githubTaskPullRequests->links() }}
</div>