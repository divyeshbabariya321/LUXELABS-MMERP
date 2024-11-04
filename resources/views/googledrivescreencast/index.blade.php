@extends('layouts.app')
@section('favicon' , '')

@section('title', 'Google Drive Screencasts')

@section('styles')
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Google Drive Screencasts/Files</h2>
            <div class="pull-left">
                <div class="form-group">
                    <div class="row">
                        <form class="form-inline message-search-handler" method="get">
                                    <div class="form-group m-1">
                                        <input name="name" list="name-lists" type="text" class="form-control" placeholder="Search file" value="{{request()->get('name')}}" />
                                        <datalist id="name-lists">
                                            @foreach ($data as $key => $val )
                                                <option value="{{$val->file_name}}">
                                            @endforeach
                                        </datalist>
                                    </div>
                                    <div class="form-group sm-1">
                                        <input name="docid" list="docid-lists" type="text" class="form-control" placeholder="Search Url" value="{{request()->get('docid')}}" />
                                        <datalist id="docid-lists">
                                            @foreach ($data as $key => $val )
                                                <option value="{{$val->google_drive_file_id	}}">
                                            @endforeach
                                        </datalist>
                                    </div>
                                    <div class="form-group m-1">
                                        <select name="task_id" id="search_task" class="form-control" placeholder="Search Dev Task">
                                        <option value="">Search Tasks</option>
                                            @foreach ($tasks as $key => $task )
                                                <option value="DEV-{{$task->id}}" @if(request()->get('task_id') == "DEV-".$task->id) selected @endif>#DEVTASK-{{$task->id}}</option>
                                            @endforeach
                                            @if (isset($generalTask) && !empty($generalTask))
                                            @foreach($generalTask as $task)
                                                <option value="TASK-{{$task->id}}" @if(request()->get('task_id') == "TASK-".$task->id) selected @endif class="form-control">#TASK-{{$task->id}}</option>
                                            @endforeach
                                        @endif
                                        </select>
                                    </div>
                                    @if(Auth::user()->isAdmin())
                                    <div class="form-group m-1">
                                        <select name="user_id" id="search_user" class="form-control" placeholder="Search User">
                                        <option value="">Search User</option>
                                            @foreach ($users as $key => $val )
                                                <option value="{{$val->id}}" @if(request()->get('user_id')==$val->id) selected @endif>{{$val->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
				                    @endif
                                    <div class="form-group m-1">
                                        <input class="form-control" type="text" id="remark" placeholder="Search Remark" name="remark" value="{{ request('remark') ?? '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="button">&nbsp;</label>
                                        <button type="submit" style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
                                            <img src="/images/search.png" style="cursor: default;">
                                        </button>
                                        <a href="/google-drive-screencast" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                                    </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                @if(Auth::user()->isAdmin())
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#updateMulitipleGoogleFilePermissionModal">
                  Add Permission
                </button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#GoogleFileRemovePermissionModal">
                   Remove Permission
                </button>
                @endif
                <button type="button" class="btn btn-secondary" onclick="uploadeScreencastModal()">
                    + Upload Screencast/File
                </button>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
            <tr>
                @if(Auth::user()->isAdmin())
                <th><input type="checkbox" name="select_all" class="select_all"></th>
                @endif
                <th>No</th>
                <th style="max-width: 150px">File Name</th>
                <th>Dev Task</th>
                <th>File Creation Date</th>
                <th>URL</th>
                <th style="max-width: 200px">Remarks</th>
                <th>User</th>
                <th>File Uploaded AT</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @include('googledrivescreencast.partials.list')
            @include('googledrivescreencast.partials.update-file-permissions')
            </tbody>
        </table>
        {{ $data->appends(request()->except('page'))->links() }}
    </div>

<div id="showFullMessageModel" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

            </div>
        </div>

    </div>
</div>
{{-- @include('googledrivescreencast.partials.upload') --}}
@endsection
@section('scripts')
<script>
$(document).ready(function() {
        $('#updateMulitipleGoogleFilePermissionModal').on('submit', function(e) {
            e.preventDefault();
            var selectedCheckboxes = [];
            var fileIDs = [];
            if ($('.select_all').prop('checked')) {
                $('.fileCheckbox').each(function() {
                    var fileID = $(this).data('id');
                    var checkboxValue = $(this).val();
                    fileIDs.push(fileID);
                    selectedCheckboxes.push(checkboxValue);
                });
            } else {
                $('input[name="fileCheckbox"]:checked').each(function() {
                    var fileID = $(this).data('id');
                    var checkboxValue = $(this).val();
                    fileIDs.push(fileID);
                    selectedCheckboxes.push(checkboxValue);
                });
            }
            if (selectedCheckboxes.length === 0) {
                alert('Please select at least one checkbox.');
                return;
            }
            $('#multiple_file_id').val(selectedCheckboxes.join(','));
            $(this).unbind('submit').submit();
        });
        $('#GoogleFileRemovePermissionModal').on('submit', function(e) {
            e.preventDefault();
            var selectedCheckboxes = [];
            var fileIDs = [];
            if ($('.select_all').prop('checked')) {
                $('.fileCheckbox').each(function() {
                    var fileID = $(this).data('id');
                    var checkboxValue = $(this).val();
                    fileIDs.push(fileID);
                    selectedCheckboxes.push(checkboxValue);
                });
            } else {
                $('input[name="fileCheckbox"]:checked').each(function() {
                    var fileID = $(this).data('id');
                    var checkboxValue = $(this).val();
                    fileIDs.push(fileID);
                    selectedCheckboxes.push(checkboxValue);
                });
            }
            if (selectedCheckboxes.length === 0) {
                alert('Please select at least one checkbox.');
                return;
            }
            $('#remove_file_ids').val(selectedCheckboxes.join(','));
            $(this).unbind('submit').submit();
        });
        $('.select_all').on('change', function() {
            var isChecked = $(this).prop('checked');
            $('.fileCheckbox').prop('checked', isChecked);
        });
    });
    </script>
@endsection

