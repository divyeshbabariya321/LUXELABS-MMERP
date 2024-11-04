<div class="table-responsive mt-3">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Date</th>
          <th>Updated By User</th>
          <th>Updated Status</th>
          <th>User</th>
          <th>Old Status</th>
        </tr>
      </thead>
      <tbody id="emailCategoryLogs">
        @foreach ($data as $logs)
            <tr>
                <td>{{ $logs->created_at->format('Y-m-d') }}</td>
                <td>{{ $logs->updatedByUser->name }}</td>
                <td>{{ $logs->status->email_status }}</td>
                <td>{{ isset($logs->user->name) ? $logs->user->name : '-' }}</td>
                <td>{{ isset($logs->oldStatus->email_status) ? $logs->oldStatus->email_status : '-' }}</td>
            </tr>
        @endforeach
      </tbody>
    </table>
  </div>