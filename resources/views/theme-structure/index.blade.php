@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Theme Structure</h2>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif
<div class="row m-3">
    <div class="col-lg-12">
        <div class="ml-2">
            <div class="form-group">
                <label for="button">Select Theme</label>
                {{ html()->select("theme", ['' => 'Please Select Theme'] + $themes, request("id"))->class("form-control select-theme") }}
            </div>
        </div> 
    </div>
</div> 
<div class="row m-3">
    <div class="col-lg-5">
        <div class="card-header">
            <a href="#" class="collapse-all">Collapse All</a> |
            <a href="#" class="expand-all">Expand All</a>
        </div>
        <div id="jstree" style="font-size: 16px;"></div>
    </div>
    <div class="col-lg-7">
        <h2 class="page-heading">Theme Structure Logs ({{ $themeStructureLogs->total() }})</h2>
        <div class="table-responsive">
            <table class="table table-bordered" style="table-layout: fixed;" id="theme-structure-logs-list">
                <tr>
                    <th width="3%">ID</th>
                    <th width="15%">Command</th>
                    <th width="15%">Message</th>
                    <th width="10%">Status</th>
                </tr>
                @foreach ($themeStructureLogs as $key => $themeStructureLog)
                    <tr data-id="{{ $themeStructureLog->id }}">
                        <td>{{ $themeStructureLog->id }}</td>
                        <td class="expand-row" style="word-break: break-all">
                            <span class="td-mini-container">
                               {{ strlen($themeStructureLog->command) > 30 ? substr($themeStructureLog->command, 0, 30).'...' :  $themeStructureLog->command }}
                            </span>
                            <span class="td-full-container hidden">
                                {{ $themeStructureLog->command }}
                            </span>
                        </td>
                        <td class="expand-row" style="word-break: break-all">
                            <span class="td-mini-container">
                               {{ strlen($themeStructureLog->message) > 30 ? substr($themeStructureLog->message, 0, 30).'...' :  $themeStructureLog->message }}
                            </span>
                            <span class="td-full-container hidden">
                                {{ $themeStructureLog->message }}
                            </span>
                        </td>
                        <td class="expand-row" style="word-break: break-all">
                            {{ $themeStructureLog->status }}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        {!! $themeStructureLogs->appends(request()->except('page'))->links() !!}
    </div>
</div>   
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
<script>
    $(document).ready(function () {
            $(document).on('click', '.expand-row', function () {
                var selection = window.getSelection();
                if (selection.toString().length === 0) {
                    $(this).find('.td-mini-container').toggleClass('hidden');
                    $(this).find('.td-full-container').toggleClass('hidden');
                }
            });
            
            $(document).on('change', '.select-theme', function () {
                    var lan=$(this).val()
                    window.location.href = '<?php echo url("/"); ?>'+"/theme-structure/"+lan;
            });
            
           var jstree=$('#jstree').jstree({
                'core': {
                    'data': {!! $tree !!}
                },
                'plugins': ['contextmenu'],
                'contextmenu': {
                    'items': function (node) {
                        var menuItems = {
                            'create_folder': {
                                'label': 'Create Folder',
                                'action': function (data) {
                                    if (node.original.type == 'file') {
                                        toastr['error']('Cannot create a folder or file inside a file.', 'error');
                                    } else {
                                        var inst = $.jstree.reference(data.reference),
                                        obj = inst.get_node(data.reference);
                                        var fullpath = inst.get_path(inst.get_selected(),'/');
                                        createFolder(node,fullpath);
                                    }
                                }
                            },
                            'create_file': {
                                'label': 'Create File',
                                'action': function (data) {
                                    if (node.original.type == 'file') {
                                        toastr['error']('Cannot create a folder or file inside a file.', 'error');
                                    } else {
                                        var inst = $.jstree.reference(data.reference),
                                        obj = inst.get_node(data.reference);
                                        var fullpath = inst.get_path(inst.get_selected(),'/');
                                        createFile(node,fullpath);
                                    }
                                }
                            },
                            'delete': {
                                'label': 'Delete',
                                'action': function (data) {
                                    if (node.original.is_root) {
                                        toastr['error']('Root folder cannot be deleted.', 'error');
                                    } else if (confirm('Are you sure you want to delete this item?')) {
                                        var inst = $.jstree.reference(data.reference),
                                        obj = inst.get_node(data.reference);
                                        var fullpath = inst.get_path(inst.get_selected(),'/');
                                        deleteItem(node.id,fullpath);
                                    }
                                }
                            }
                        };

                        if (node.id === '#') {
                            delete menuItems.create_file;
                        }

                        return menuItems;
                    }
                }
            });
            $(document).on('click', '.collapse-all', function (e) {
                e.preventDefault();
                jstree.jstree("close_all");
            });
            $(document).on('click', '.expand-all', function (e) {
                e.preventDefault();
                jstree.jstree("open_all");
            });
       
        function deleteItem(itemId,fullpath) {
            $.ajax({
                url: '/theme-structure/delete-item',
                type: 'POST',
                data: { id: itemId, fullpath: fullpath }, // Pass the item ID to delete
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if(response.code==200){
                        toastr['success'](response.message);
                        reloadTree();
                    }else{
                        toastr['error'](response.message);
                    }
                     // Reload the JSTree after deleting the item
                },
                error: function(error) {
                    console.log('Error deleting item:', error);
                }
            });
        }

        function reloadTree() {
            $.ajax({
                url: '/theme-structure/reload-tree/{{$theme_id}}',
                type: 'GET',
                success: function(data) {
                    $('#jstree').jstree(true).settings.core.data = data;
                    $('#jstree').jstree(true).refresh();
                },
                error: function(error) {
                    console.log('Error reloading tree: ', error);
                }
            });
        }

        function createFolder(node,fullpath) {
           // let path=$("#jstree").jstree().get_path(node[0], '/');
            //console.log(path);
            var folderName = prompt('Enter the folder name:');
            if (folderName) {
                var newNode = { name: folderName, is_file: 0, parent_id: node.id, theme_id: node.original.theme_id,fullpath:fullpath };
                $.ajax({
                    type: 'POST',
                    url: '/theme-structure',
                    data: newNode,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if(response.code==200){
                            toastr['success'](response.message);
                        }else{
                            toastr['error'](response.message);
                        }
                        reloadTree();
                    }
                });
            }
        }

        function createFile(node,fullpath) {
            var fileName = prompt('Enter the file name:');
            if (fileName) {
                var newNode = { name: fileName, is_file: 1, parent_id: node.id,theme_id: node.original.theme_id,fullpath:fullpath };
                $.ajax({
                    type: 'POST',
                    url: '/theme-structure/theme-file-store',
                    data: newNode,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if(response.code==200){
                            toastr['success'](response.message);
                        }else{
                            toastr['error'](response.message);
                        }
                        reloadTree();
                    }
                });
            }
        }
    });
    </script>
@endsection