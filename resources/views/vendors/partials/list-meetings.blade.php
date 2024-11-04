@php $i=0; 
$base_url = config('env.APP_URL');
@endphp
@foreach ($meetings as $key => $metting)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ $metting->file_name }}</td>
        <td><textarea name="description" class="form-control description" placeholder="Description" style="height: 90px;width:70%;">{{ $metting->description }}</textarea>
        <button class="btn btn-secondary btn-xs update_description" data-id="{{ $metting->id }}">Update</button>
        </td>
        <td><button class="btn btn-secondary btn-xs"><a href="{{ url($metting->file_path) }}" target="_blank" style="color:#ffff;">Preview</a></button>
        <button class="btn btn-secondary btn-xs"><a href="{{ url($metting->file_path) }}" style="color:#ffff;" download>Download</a></button>
        </td>

    </tr>
@endforeach