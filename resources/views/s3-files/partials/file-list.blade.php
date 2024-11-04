<table class="table table-bordered files-table" style="table-layout: fixed;">
    <thead>
        <tr>
            <th width="">Filename</th>
            <th width="">Location</th>
            <th width="">Actions</th>
        </tr>
    </thead>
    <tbody>
        @if (!empty($files))
            @foreach ($files as $file)
                @php
                    $presigned = getMediaUrl($file);
                    $viewableImageExtensions = [
                        'jpg',
                        'jpeg',
                        'png',
                        'gif',
                        'bmp',
                        'webp',
                        'tiff',
                        'svg',
                        'ico',
                        'jfif',
                        'pjpeg',
                        'pjp',
                    ];
                    $isViewEnabled = in_array(strtolower($file->extension), $viewableImageExtensions);
                @endphp
                <tr>
                    <td style="vertical-align: middle;">{{ $file->filename }}</td>
                    <td style="vertical-align: middle;">{{ $file->directory }}
                        @if ($file->glacier_archive_id)
                            &nbsp;&nbsp; <span class="badge">Archived</span>
                        @endif
                    </td>
                    <td style="vertical-align: middle;">
                        @if (!$file->glacier_archive_id)
                            <button class="btn view-s3-file" data-id="{{ $file->id }}"
                                {{ $isViewEnabled ? '' : 'disabled' }} data-presigned="{{ $presigned }}"
                                title="View file"><i class="fa fa-eye" aria-hidden="true"></i></button>
                            <a href="{{ $presigned }}" download title="Download file">
                                <i class="fa fa-download" aria-hidden="true"></i>
                            </a>
                            <button class="file-delete-btn btn" data-id="{{ $file->id }}" title="Delete file"><i
                                    class="fa fa-trash" aria-hidden="true"></i></button>
                            <button class="file-move-btn btn" data-id="{{ $file->id }}" title="Move to Glacier"><i
                                    class="fa fa-exchange" aria-hidden="true"></i></button>
                        @endif
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="3" class="text-center records-not-found">No records found</td>
            </tr>
        @endif
    </tbody>
</table>

{{ $files->links() }}
