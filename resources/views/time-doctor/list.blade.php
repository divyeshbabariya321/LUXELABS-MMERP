@foreach($projects as $project)
    <tr>
        <td style="vertical-align:middle;">{{ $loop->iteration }}</td>        
        <td style="vertical-align:middle;">{{ $project->time_doctor_project_id }}</td>
        <td style="vertical-align:middle;">{{ $project->time_doctor_project_name }}</td>
        <td style="vertical-align:middle;">{{ $project->time_doctor_company_id }}</td>
        <td style="vertical-align:middle;">{{ $project->account_detail->time_doctor_email }}</td>
        <td style="vertical-align:middle;">{{ $project->account_detail->created_at }}</td>
        <td style="vertical-align:middle;"><button type="button" class="btn btn-secondary edit_project" data-id="{{ $project->id }}">Edit Project</button></
    </tr>
@endforeach