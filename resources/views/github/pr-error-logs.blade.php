
<div class="modal-header">
    <h5 class="modal-title">Error Logs</h5>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body" >
    <input type="hidden" id="repo"/>
    <input type="hidden" id="pullNumber"/>
    <table id="repository-table" class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Log</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($githubPrErrorLogs as $githubPrErrorLog)
                <tr>
                    <td>{{$githubPrErrorLog['id']}}</td>
                    <td>{{$githubPrErrorLog['type']}}</td>
                    <td class="expand-row" style="word-break: break-all">
                        <span class="td-mini-container">
                            {{$githubPrErrorLog['short_log']}}
                        </span>
                        <span class="td-full-container hidden">
                            {{$githubPrErrorLog['log']}}
                        </span>
                    </td>
                    <td>{{$githubPrErrorLog['created_at']}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <!-- Display pagination links -->
    {{ $githubPrErrorLogs->links() }}
</div>