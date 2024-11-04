<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th width="3%">ID</th>
            <th width="10%">File</th>
            <th width="10%">Description</th>
            <th width="10%">Run Time</th>
            <th width="10%">Run Output</th>
            <th width="10%">Created Date</th>
        </tr>
    </thead>
    <tbody class="show-search-password-list" id="google_screen_cast">
        @foreach($datas as $key => $data)
        <tr>
            <td>{{ $data->script_document_id }}</td>
            <td>{{ $data->scriptDocument->file }}</td>
            <td>{{ $data->description }}</td>
            <td>{{ $data->run_time }}</td>
            <!-- <td>{{ base64_decode($data->run_output) }}</td> -->
            <td>
                <button type="button" data-id="{{ $data->script_document_id }}" class="btn script-document-last_output-header-view" style="padding:1px 0px;">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                </button>
            </td>
            <td>{{ $data->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>