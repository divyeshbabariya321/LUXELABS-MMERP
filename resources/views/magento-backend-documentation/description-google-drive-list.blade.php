
@if (isset($result))
    @forelse ($result as $file);
        <tr>
            <td>{{$file['file_name']}}</td>
            <td>{{$file['created_at']}}</td>
            <td>
                <a href ="{{config('settings.google_drive_file_url').$file['new_value']}}/view?usp=share_link" target="_blank"><input class="fileUrl" type="text" value="{{config('settings.google_drive_file_url').$file['new_value']}}/view?usp=share_link" /></a>
                <button class="copy-button btn btn-secondary" data-message="{{config('settings.google_drive_file_url').$file['new_value']}}/view?usp=share_link">Copy</button>
            </td>
        </tr>
    @empty
        <tr><td colspan="4">No record found</td></tr>
    @endforelse
@else
    <tr><td colspan="4">No record found</td></tr>
@endif
