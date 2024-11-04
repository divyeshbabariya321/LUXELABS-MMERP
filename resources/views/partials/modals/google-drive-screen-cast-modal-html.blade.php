@php
    $enum = [
        "App\Uicheck" => "UICHECK-",
        "App\UiDevice" => "UIDEVICE-",
        "App\Task" => "TASK-"
    ];
@endphp

<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th width="3%">S.NO</th>
            <th width="10%">File Name</th>
            <th width="10%">Dev Task</th>
            <th width="8%">File creation Date</th>
            <th width="10%">URl</th>
            <th width="8%">Users</th>
            <th width="8%">Remarks</th>
            <th width="8%">File uploaded At </th>
            <th width="10%">Action</th>
        </tr>
    </thead>
    <tbody class="show-search-password-list" id="google_screen_cast">
        @foreach($datas as $key => $data)
        <tr>
            <td>{{ $key+1 }}</td>
            <td> {{ strlen($data->file_name) > 10 ? substr($data->file_name, 0, 10).'...' : $data->file_name }}</td>
            <td> @if (isset($data->developer_task_id))
                #DEVTASK-{{ $data->developer_task_id }}
            @endif
            @if ($data->bug_id)
                #BUG-{{ $data->bug_id }}
            @endif
            @if (!isset($data->developer_task_id) && !isset($data->bug_id))
                {{$enum[$data->belongable_type] ?? ""}}{{$data->belongable_id}}
            @endif</td>
            <td>{{ $data->file_creation_date}}</td>
            <td><a target="_blank" href="{{config('settings.google_drive_file_url').$data->google_drive_file_id}}/view?usp=share_link">Open Document</a>
                <button class="copy-button btn btn-xs text-dark" data-message="{{config('settings.google_drive_file_url').$data->google_drive_file_id}}/view?usp=share_link" title="Copy document URL"><i class="fa fa-copy"></i></button></td>
            <td> @isset($data->user)
                {{$data->user->name}}
            @endisset </td> 

            <td>{{ $data->remarks}}</td>
            <td>{{ $data->created_at }}</td>
            <td> @if (Auth::user()->isAdmin())
                {{ html()->form('DELETE', route('google-drive-screencast.destroy', [$data->google_drive_file_id]))->style('display:inline')->open() }}
                <button type="submit" class="btn btn-image" onclick="return confirm('{{ __('Are you sure you want to delete?') }}')"><img src="/images/delete.png" /></button>
                {{ html()->form()->close() }}
                <button style="padding:3px;" type="button" class="btn btn-image filepermissionupdate d-inline border-0" data-toggle="modal" data-readpermission="{{ $data->read }}" data-writepermission="{{ $data->write}}" data-fileid="{{ $data->google_drive_file_id}}" data-target="#updateGoogleFilePermissionModal" data-id="{{ $data->id }}" title="Update permission"><img width="2px;" src="/images/edit.png"/></button>
                <button style="padding:3px;font-size: 20px;line-height: 20px;color:black" type="button" class="btn btn-image filedetailupdate d-inline border-0" data-toggle="modal" data-file_remark="{{html_entity_decode($data->remarks)}}" data-file_name="{{html_entity_decode($data->file_name)}}" data-readpermission="{{ $data->read }}" data-writepermission="{{ $data->write}}" data-fileid="{{ $data->google_drive_file_id}}" data-target="#updateUploadedFileDetailModal" data-id="{{ $data->id }}" title="Update detail">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                </button>
            @else
                -
            @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>