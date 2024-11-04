<table class="table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Created Date</th>
            <th>Last Updated</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($history as $data)
            <tr>
                <td>{{ $data->title }}</td>
                <td>{{ $data->builder_created_date }}</td>
                <td>{{ $data->builder_last_updated }}</td>
                <td>
                    <a target="_blank" href="{{ route('uicheck.get-builder-html', $data->id) }}">
                        <i class="btn btn-xs fa fa-eye" title="View Builder HTML"></i>
                    </a>
                    <a href="{{ route('uicheck.get-builder-download-html', $data->id) }}">
                        <i class="btn btn-xs fa fa-download" title="Download Builder HTML"></i>
                    </a>
                    <i data-data-id="{{ $data->id }}" class="btn btn-xs fa fa-info-circle show-download-history" title="Download History"></i>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>