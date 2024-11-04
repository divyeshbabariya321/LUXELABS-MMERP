@if (isset($googleDoc))
    @forelse ($googleDoc as $file)
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$file->name}}</td>
            <td>{{$file->created_at}}</td>
            <td>
                @if($file->type === 'spreadsheet')
                    <a href ="{{config('settings.google_excel_file_url').$file->docId.'/edit' }}" target="_blank"><input class="fileUrl" type="text" value="{{config('settings.google_excel_file_url').$file->docId.'/edit' }}" />
                    <button class="copy-button btn btn-secondary" data-message="{{config('settings.google_excel_file_url').$file->docId.'/edit' }}">Copy</button>
                @endif
                @if($file->type === 'doc')
                    <a href ="{{config('settings.google_doc_file_url').$file->docId.'/edit'}}" target="_blank"><input class="fileUrl" type="text" value="{{config('settings.google_doc_file_url').$file->docId.'/edit'}}"/></a>
                    <button class="copy-button btn btn-secondary"  data-message="{{config('settings.google_doc_file_url').$file->docId.'/edit'}}">Copy</button>
                @endif
                @if($file->type === 'ppt')
                <a href ="{{config('settings.google_slides_file_url').$file->docId.'/edit'}}" target="_blank"><input class="fileUrl" type="text" value="{{config('settings.google_slides_file_url').$file->docId.'/edit'}}" /></a>
                    <button class="copy-button btn btn-secondary" data-message="{{config('settings.google_slides_file_url').$file->docId.'/edit'}}">Copy</button>
                @endif
                @if($file->type === 'xps')
                <a href ="{{config('settings.google_doc_file_url').$file->docId.'/edit'}}" target="_blank"><input class="fileUrl" type="text" value="{{config('settings.google_doc_file_url').$file->docId.'/edit'}}" /></a>
                    <button class="copy-button btn btn-secondary" data-message="{{config('settings.google_doc_file_url').$file->docId.'/edit'}}">Copy</button>
                @endif
                @if($file->type === 'txt')
                <a href ="{{config('settings.google_doc_file_url').$file->docId.'/edit'}}" target="_blank"> <input class="fileUrl" type="text" value="{{config('settings.google_doc_file_url').$file->docId.'/edit'}}"/></a>
                <button class="copy-button btn btn-secondary" data-message="{{config('settings.google_doc_file_url').$file->docId.'/edit'}}">Copy</button>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="2">No record found</td></tr>
    @endforelse
@else
    <tr><td colspan="2">No record found</td></tr>    
@endif