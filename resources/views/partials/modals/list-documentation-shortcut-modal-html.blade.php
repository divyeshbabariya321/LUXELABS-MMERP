@php
    $enum = [
        "App\Uicheck" => "UICHECK-",
        "App\UiDevice" => "UIDEVICE-",
        "App\Task" => "TASK-"
    ];
@endphp

<table class="table table-sm table-bordered" id="document_list_table">
    <thead>
        <tr>
            <th>Date</th>
                <th>User</th>
                <th>Department</th>
                <th>Document Type</th>
                <th>Category</th>
                <th>Filename</th>
                <th>Actions</th>
        </tr>
    </thead>
    <tbody class="show-search-password-list" id="document_list">
        @foreach($datas as $key => $document)
        <tr>
            <td>{{ $document->updated_at->format('d.m-Y') }}</td>
            <td>@if(isset($document->user->name)){{ $document->user->name }}@endif</td>
            <td>@if(isset($document->user->agent_role)){{ $document->user->agent_role  }}@endif</td>
            <td>@if(isset($document->name)){{ $document->name}}@endif</td>
            <td>@if(isset($document->documentCategory->name)){{ $document->documentCategory->name }} @endif</td>
            <td>{{ $document->filename }}</td>
            <td>
                <a href="{{ route('document.download', $document->id) }}" class="btn btn-xs btn-secondary">Download</a>
                {{ html()->form('DELETE', route('document.destroy', [$document->id]))->style('display:inline')->open() }}
                <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                {{ html()->form()->close() }}
                V: {{ $document->version }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

