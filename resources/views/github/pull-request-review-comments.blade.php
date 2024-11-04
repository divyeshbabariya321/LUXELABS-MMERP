
<div class="modal-header">
    <h5 class="modal-title">Pull Request Review Comments (<span>{{ $totalCount }}</span>)</h5>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body" >
    <input type="hidden" id="repo"/>
    <input type="hidden" id="pullNumber"/>
    <table id="repository-table" class="table table-bordered">
        <thead>
            <tr>
                <th>Comment ID</th>
                <th>User</th>
                <th>Comment Body</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($comments as $comment)
                <tr>
                    <td>{{$comment['id']}}</td>
                    <td>{{$comment['user']['login']}}</td>
                    <td>{{$comment['body']}}</td>
                    <td>{{$comment['created_at']}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <!-- Display pagination links -->
    {{ $comments->links() }}
</div>