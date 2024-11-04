<div class="table-responsive mt-3">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Date</th>
          <th>Updated By User</th>
          <th>Updated Category</th>
          <th>User</th>
          <th>Old Category</th>
        </tr>
      </thead>
      <tbody id="emailCategoryLogs">
        @foreach ($data as $logs)
            <tr>
                <td>{{ $logs->created_at->format('Y-m-d') }}</td>
                <td>{{ $logs->updatedByUser->name }}</td>
                <td>{{ $logs->category->category_name }}</td>
                <td>{{ isset($logs->user->name) ? $logs->user->name : '-' }}</td>
                <td>{{ isset($logs->oldCategory->category_name) ? $logs->oldCategory->category_name : '-' }}</td>
            </tr>
        @endforeach
      </tbody>
    </table>
  </div>