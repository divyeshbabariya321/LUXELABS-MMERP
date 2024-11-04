@foreach ($data as $key => $file)
<tr>
    <td>{{ ++$i }}</td>
    <td>{{ $file->name }}</td>
    <td>{{ $file->updated_at }}</td>
    <td>{{ $file->created_at }}</td>
    <td>
        @if($file->download_status== 1)
        {{-- <a class="btn btn-image" href="{{ route('googlefiletranslator.download',$file->name) }}">Download File</a> --}}
        <a class="btn btn-image" href="{{ route('store-website.download.csv', ['id' => $file->id, 'type' => 'googletranslate']) }}" target="_blank">Download CSV</a>

        @else 
        <button class="btn btn-image" onclick="showPermissionAlert()">Download CSV</button>
        @endif
        {{ html()->form('DELETE', route('googlefiletranslator.destroy', [$file->id]))->style('display:inline')->open() }}
        <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
        {{ html()->form()->close() }}
        <a class="btn btn-image" href="{{ route('googlefiletranslator.list-page.view', ['id' => $file->id, 'type' => 'googletranslate']) }}" target="_blank">View File</a>
    </td>
</tr>
@endforeach


<script>
    function showPermissionAlert() {
        alert('Permission required to download this file.');
    }
</script>