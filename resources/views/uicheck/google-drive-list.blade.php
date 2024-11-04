@if (isset($result))
    @forelse ($result as $file)
        <tr>
            <td>{{$file['file_name']}}</td>
            <td>{{$file['file_creation_date']}}</td>
            <td>
                <a href ="{{config('settings.google_drive_file_url').$file['google_drive_file_id']}}/view?usp=share_link" target="_blank"><input class="fileUrl" type="text" value="{{config('settings.google_drive_file_url').$file['google_drive_file_id']}}/view?usp=share_link" />
                <button class="copy-button btn btn-secondary" data-message="{{config('settings.google_drive_file_url').$file['google_drive_file_id']}}/view?usp=share_link">Copy</button>
            </td>
            <td>{{$file['remarks']}}</td>
        </tr>

    @empty
        <tr><td colspan="4">No record found</td></tr>
    @endforelse
@else
    <tr><td colspan="4">No record found</td></tr>
@endif
