@php
    $enum = [
        "App\Uicheck" => "UICHECK-",
        "App\UiDevice" => "UIDEVICE-",
        "App\Task" => "TASK-"
    ];
@endphp

@foreach ($data as $key => $file)
    <tr>
        @if(Auth::user()->isAdmin())
         <td><input type="checkbox" name="fileCheckbox" class="fileCheckbox" value="{{ $file->id }}" data-file="{{ $file->google_drive_file_id }}" data-id="{{ $file->id }}" data-select="true"></td>
        @endif
        <td>{{ ++$i }}</td>
        <td style="max-width: 150px">
            <div data-message="{{$file->file_name}}" data-title="File name" style="cursor: pointer" class="showFullMessage">
                {{ show_short_message($file->file_name, 25) }}
            </div>
        </td>
        <td>
            @if (isset($file->developer_task_id))
                #DEVTASK-{{ $file->developer_task_id }}
            @endif
            @if ($file->bug_id)
                #BUG-{{ $file->bug_id }}
            @endif
            @if (!isset($file->developer_task_id) && !isset($file->bug_id))
                {{$enum[$file->belongable_type] ?? ""}}{{$file->belongable_id}}
            @endif
        </td>
        <td>{{ $file->file_creation_date }}</td>
        <td>
            <a target="_blank" href="{{config('settings.google_drive_file_url').$file->google_drive_file_id}}/view?usp=share_link">Open Document</a>
            <button class="copy-button btn btn-xs text-dark" data-message="{{config('settings.google_drive_file_url').$file->google_drive_file_id}}/view?usp=share_link" title="Copy document URL"><i class="fa fa-copy"></i></button>
        </td>   
        <td  style="max-width: 200px">
            <div data-message="{{$file->remarks}}" data-title="Remark" style="cursor: pointer" class="showFullMessage">
                {{ show_short_message($file->remarks) }}
            </div>
        </td> 
        <td>
            @isset($file->user)
                {{$file->user->name}}
            @endisset    
        </td> 
        <td>{{ $file->created_at }}</td>
        <td>
                {{-- <a class="btn btn-image" href="{{config('settings.google_drive_file_url').$file->google_drive_file_id}}/view?usp=share_link" target="_blank">
                    <img src="{{asset('images/docs.png')}}" />
                    Open</a> --}}
                    @if (Auth::user()->isAdmin())
                        {{ html()->form('DELETE', route('google-drive-screencast.destroy', [$file->google_drive_file_id]))->style('display:inline')->open() }}
                        <button type="submit" class="btn btn-image" onclick="return confirm('{{ __('Are you sure you want to delete?') }}')"><img src="/images/delete.png" /></button>
                        {{ html()->form()->close() }}
                        <button style="padding:3px;" type="button" class="btn btn-image filepermissionupdate d-inline border-0" data-toggle="modal" data-readpermission="{{ $file->read }}" data-writepermission="{{ $file->write}}" data-fileid="{{ $file->google_drive_file_id}}" data-target="#updateGoogleFilePermissionModal" data-id="{{ $file->id }}" title="Update permission"><img width="2px;" src="/images/edit.png"/></button>
                        <button style="padding:3px;font-size: 20px;line-height: 20px;color:black" type="button" class="btn btn-image filedetailupdate d-inline border-0" data-toggle="modal" data-file_remark="{{html_entity_decode($file->remarks)}}" data-file_name="{{html_entity_decode($file->file_name)}}" data-readpermission="{{ $file->read }}" data-writepermission="{{ $file->write}}" data-fileid="{{ $file->google_drive_file_id}}" data-target="#updateUploadedFileDetailModal" data-id="{{ $file->id }}" title="Update detail">
                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                        </button>

                    @else
                        -
                    @endif
        </td>
    </tr>
@endforeach