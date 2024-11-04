<table id="classTable" class="table table-bordered">
    <thead>
        <tr>
            <th>Subject</th>
            <th>Info</th>
            <th>Creator</th>
            <th>Documents</th>
            <th>created_at</th>
        </tr>
    </thead>
    <tbody>
        @if (!$devDocuments->isEmpty())
            @foreach ($devDocuments as $documents)
                <tr>
                    <td> {{ $documents->subject }}</td>
                    <td> {{ $documents->description }}</td>
                    <td> {{ $documents->creator ? $documents->creator->name : 'N/A' }}</td>
                    <td>
                        @if ($documents->getMedia(config('constants.media_tags'))->first())
                            @foreach ($documents->getMedia(config('constants.media_tags')) as $i => $file)
                                <a href="{{ getMediaUrl($file) }}" target="_blank" class="d-inline-block">
                                    {{ 'Document : ' . ($i + 1) }}
                                </a>
                                <br />
                            @endforeach
                        @endif
                    </td>
                    <td>{{ $documents->created_at ? $documents->created_at->format('Y-m-d H:i:s') : '-' }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
