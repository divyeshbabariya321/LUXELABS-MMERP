@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Project Themes ({{ $projectThemes->total() }})</h2>
        @if($errors->any())
        <div class="row m-2">
        {!! implode('', $errors->all('<div class="alert alert-danger">:message</div>')) !!}
        </div>
        @endif
        @include('partials.flash_messages', ['extraDiv' => true])
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-12">
                    <form action="{{ route('project-theme.index') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-3 pd-sm">
                                <?php 
                                    if(request('search_project_id')){   $search_project_id = request('search_project_id'); }
                                    else{ $search_project_id = ''; }
                                ?>
                                <select name="search_project_id" id="search_project_id" class="form-control select2">
                                    <option value="" @if($search_project_id=='') selected @endif>-- Select a project --</option>
                                    @forelse($projects as $id => $name)
                                    <option value="{{ $id }}" @if($search_project_id==$id) selected @endif>{!! $name !!}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                            <div class="col-md-2 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control h-100" value="{{ request()->get('keyword') }}">
                            </div>
                            <div class="col-md-1 pd-sm pl-0 mt-2">
                                 <button type="submit" class="btn btn-image search">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                                <a href="{{ route('project-theme.index') }}" class="btn btn-image" id="">
                                    <img src="/images/resend2.png" style="cursor: nwse-resize;">
                                </a>
                            </div>
                        </div>
                    </form>
                    <div class="pull-right" style="display: flex">
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#project-theme-create"> Create </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tab-content">
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;" id="project-theme-list">
                        <tr>
                            <th width="2%">ID</th>
                            <th width="10%">Project Name</th>
                            <th width="10%">Theme Name</th>
                            <th width="6%">Action</th>
                        </tr>
                        @foreach ($projectThemes as $key => $projectTheme)
                            <tr data-id="{{ $projectTheme->id }}">
                                <td>{{ $projectTheme->id }}</td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($projectTheme->project?->name) > 30 ? substr($projectTheme->project?->name, 0, 30).'...' :  $projectTheme->project?->name }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $projectTheme->project?->name }}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($projectTheme->name) > 30 ? substr($projectTheme->name, 0, 30).'...' :  $projectTheme->name }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $projectTheme->name }}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" title="Edit Theme" data-id="{{ $projectTheme->id }}" class="btn btn-xs btn-edit-project-theme">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    {{ html()->form('DELETE', route('project-theme.destroy', [$projectTheme->id]))->class('delete-form')->style('display:inline')->open() }}
                                    <button type="submit" title="Delete Theme" class="btn btn-xs delete-button" onclick="return confirmDelete(event)">
                                        <i class="fa fa-trash" style="color: #808080;"></i>
                                    </button>
                                    {{ html()->form()->close() }}
                                    <a title="View Folder Structure" class="btn btn-xs btn-view-theme-structure" href="{{ route('theme-structure.index', $projectTheme->id) }}">
                                        <i class="fa fa-folder"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                {!! $projectThemes->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>

{{-- project-theme-create --}}
@include('project-theme.partials.create-modal')
{{-- project-theme-edit --}}
@include('project-theme.partials.edit-modal')

<script type="text/javascript">
    $('.select2').select2();

    function confirmDelete(event) {
        event.preventDefault();
        var confirmDelete = confirm("Are you sure you want to delete this item?");
        if (confirmDelete) {
            event.target.closest('.delete-form').submit();
        }
        return false;
    }

    $(document).ready(function(){
        $(".btn-edit-project-theme").on('click', function(e) {
            var ajaxUrl = "{{ route('project-theme.edit', ['project_theme' => ':id']) }}";
            ajaxUrl = ajaxUrl.replace(':id', $(this).data("id"));

            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: "GET",
                url: ajaxUrl,
            }).done(function(response) {
                $("#project-theme-edit-form #id").val(response.data.id);
                $("#project-theme-edit-form #project_id").val(response.data.project_id).trigger('change');
                $("#project-theme-edit-form #name").val(response.data.name);
                $("#project-theme-edit").modal("show");
            }).fail(function(response) {});
        });
    });

    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });
</script>
@endsection