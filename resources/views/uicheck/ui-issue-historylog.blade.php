@foreach ($getIssueLog as $issueLog)
    <tr>
        <td>{{ $issueLog->id }}</td>
        <td>{{ $issueLog->userName }}</td>
        <td>{{ $issueLog->old_issue }}</td>
        <td>{{ $issueLog->issue }}</td>
        <td>{{ $issueLog->created_at }}</td>

    </tr>
@endforeach
