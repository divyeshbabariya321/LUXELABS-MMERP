<table class="table table-bordered table-striped" style="table-layout:fixed;">
    <tr>
        <th style="width:5%;">ID</th>
        <th style="width:8%;">Module</th>
        <th style="width:12%;">Subject</th>
        <th style="width:22%;">Communication </th>
        <th style="width:7%;">Est Completion Time</th>
        <th style="width:10%;">Est Completion Date</th>
        <th style="width:5%;">Tracked Time</th>
        <th style="width:15%;">Developers</th>
        <th style="width:12%;">Status</th>
        <th style="width:6%;">Cost</th>
        <th style="width:8%;">Milestone</th>
        <th style="width:8%;">Actons</th>
    </tr>
    @foreach ($issues as $key => $issue)
        @include($issue->view)
    @endforeach
</table>
 {{ $issues->appends(request()->except('page'))->links(); }}