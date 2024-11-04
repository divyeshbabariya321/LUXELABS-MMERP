@extends('layouts.app')

@section('title', $title)

@section('content')
@php
$isAdmin = auth()->user()->isAdmin();
$isHod = auth()->user()->hasRole('HOD of CRM');
$hasSiteDevelopment = auth()->user()->hasRole('Site-development');
@endphp
<div class="row">
    <div class="col-md-12">
        <h2 class="page-heading">{{$title}} 
            @if (isset($categories) && $categories->total())
                <span class="count-text">({{$categories->total()}})</span>
            @endif
        </h2>
    </div>
</div>
@if(!empty($categories))
<div class="row" id="common-page-layout">
    <div class="col-lg-12 margin-tb">
        <form>
            {{-- @if ($pagination)
                <input type="hidden"  name="pagination" value="{{$pagination}}">
            @endif
            @if ($page)
                <input type="hidden"  name="page" value="{{$page}}">
            @endif --}}
        <div class="form-group col-lg-2">
            {{ html()->select('show', ['' => '- Show all -', '0' => 'Not Mapped only', '1' => 'Mapped only'], $show)->class('form-control select2') }}
        </div>
        <div class="form-group col-lg-2">
            {{ html()->multiselect('category_id[]', $allCategories, $selectedCategoryIds)->placeholder('Select Categories')->class('select2 globalSelect2')->id('category_id')->style('float:left') }}
        </div>
        <div class="p-0 form-group col-lg-2">
            {{ html()->select('search_master_category_id', ['' => '- Select Master Category -'] + $masterCategories, '')->class('select2 globalSelect2')->id('search_master_category_id')->style('float:left') }}
        </div>
        <div class="form-group col-lg-1" style="margin: 7px 0 0 0">
            <button type="submit" class="btn btn-sm btn-image btn-search-record" style="float:left">
                <img src="/images/send.png" title="Apply filter" style="cursor: pointer; width: 16px;">
            </button>
            <a href="{{route('site-development.store-website-category')}}" class="btn btn-image">
                <img src="/images/resend2.png" style="cursor: nwse-resize;">
            </a>
        </div>
        </form>
        <div>
            <label>Bulk update</label>
            <div>
                <div class="p-0 form-group col-lg-2">
                    {{ html()->select('master_category_id', ['' => '- Select Master Category -'] + $masterCategories, '')->class('select2 globalSelect2')->id('master_category_id')->style('float:left') }}
                </div>
                <div class="form-group col-lg-1" style="margin: 7px 0 0 0"><img src="/images/send.png" title="Bulk update" style="cursor: pointer; width: 16px; float:left" id="bulk_action"></div>
            </div>

        </div>
    </div>
    @if ($isAdmin || $isHod || $hasSiteDevelopment)
    <div class="col-lg-12 margin-tb">
        <div class="col-md-12">
            <div class="col-md-12 margin-tb">
                <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>
                            <div style="display: flex;align-items: center;">
                                <input type="checkbox" name="checkAll" id="checkAll"> 
                                <label for="checkAll" class="ml-2">Category ID</label>
                            </div>
                        </th>
                        <th>Category</th>
                        <th>Master Category</th>
                        <th>Builder IO</th>
                        <th>Created at</th>
                        <th>Updated at</th>
                      </tr>
                    </thead>
                        @foreach($categories as $category)
                        <tbody>
                        <tr>
                            <td style="vertical-align:middle;">
                                <label style="display: flex;align-items: center;">
                                    <input type="checkbox" name="bulk_user_action[]" class="d-inline bulk_user_action m-0 p-0" value="{{ $category->id }}">
                                    <div class="ml-2">
                                        {{ $category->id }}
                                    </div>
                                </label>
                            </td>
                            <td style="vertical-align:middle;">{{ $category->title }}</td>
                            <td style="vertical-align:middle;">
                                {{ html()->select('site_development_master_category_id', ['' => '- Select-'] + $masterCategories, $category->site_development_master_category_id)->class('select2 globalSelect2 master_category_id')->data('websiteid', $category->website_id)->data('site', $category->site_development_id)->data('category', $category->id)->data('title', $category->title) }}    
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <select name="builder_io" class="builder-io-dropdown select2 globalSelect2" data-id="{{$category->id}}">
                                      <option value="">Select</option>
                                      <option value="0" {{$category->builder_io === 0 ? 'selected' : ''}}>No</option>
                                      <option value="1" {{$category->builder_io === 1 ? 'selected' : ''}}>Yes</option>
                                    </select>
                                    <button type="button" data-id="{{ $category->id  }}" class="btn btn-image builder-io-history-show p-0 ml-2"  title="Histories" ><i class="fa fa-info-circle"></i></button>
                                </div>
                            </td>
                            <td>{{ $category->created_at }}</td>
                            <td>{{ $category->updated_at }}</td>
                        </tr>
                        </tbody>
                        @endforeach
                  </table>
            </div>
        </div>
    </div>
    <div class="col-md-12 margin-tb text-center">
        {!! $categories->appends(request()->capture()->except('page', 'pagination') + ['pagination' => true])->render() !!}
    </div>
    @else
    <div class="col-md-12 margin-tb text-center">
        <h4>You are not authorised to view this data</h4>
    </div>
    @endif
</div>
@endif
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div id="records-modal" class="modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 1200px !important; width: 100% !important;">
        <div class="modal-content" id="record-content">
        </div>
    </div>
</div>

<div id="builder-io-histories-list" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Histories</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="15%">Old Value</th>
                                <th width="15%">New Value</th>
                                <th width="30%">Updated BY</th>
                                <th width="30%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="builder-io-histories-list-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $("#checkAll").click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });
});

$(document).on("change", ".master_category_id" , function(){
        $.ajax({
            type: "POST",
            url: "{{ route('site-development.update-category') }}",   
            data: { 
                websiteId: $(this).data("websiteid"), 
                category: $(this).data("category"), 
                type: 'site_development_master_category_id', 
                text:$(this).val(),
                site:$(this).data("site"), 
                _token: "{{ csrf_token() }}"
            },  
            success: function(response) {
                if (response.code == 200) {
                    toastr['success'](response.messages);
                } else {
                    toastr['error'](response.messages);
                }
            }
        });
});

$(document).on("click", "#bulk_action" , function(){
    var mcid = $('#master_category_id').val();
    if (mcid != '') {
        let checkIds = [];
        $('.bulk_user_action').each(function(){
            if($(this).is(':checked')) {
                checkIds.push($(this).val());
            }
        });

        if (checkIds.length) {
            $.ajax({
                type: "POST",
                url: "{{ route('site-development.update-category-bulk') }}",   
                data: { 
                    categories: checkIds, 
                    master_category_id: mcid,
                    _token: "{{ csrf_token() }}"
                },  
                success: function(response) {
                    if (response.code == 200) {
                        toastr['success'](response.messages);
                    } else {
                        toastr['error'](response.messages);
                    }
                }
            });
        } else {
            toastr['error']('Please select atleast one category id.');
        }
    } else {
        toastr['error']('Select master category');
    }
});

$('.builder-io-dropdown').change(function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    var selectedValue = $(this).val();

    // Make an AJAX request to update the status
    $.ajax({
    url: "{{ route('site-development.update-builder-io') }}",
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: {
        id: id,
        selectedValue: selectedValue
    },
    success: function(response) {
        toastr['success'](response.message, 'success');
    },
    error: function(xhr, status, error) {
        // Handle the error here
        console.error(error);
    }
    });
});

$(document).on('click', '.builder-io-history-show', function() {
    var id = $(this).attr('data-id');
    $.ajax({
        method: "GET",
        url: `{{ route('site-development.builder-io.histories', [""]) }}/` + id,
        dataType: "json",
        success: function(response) {
            if (response.status) {
                var html = "";
                $.each(response.data, function(k, v) {
                    html += `<tr>
                                <td> ${k + 1} </td>
                                <td> ${v.old_value_text} </td>
                                <td> ${v.new_value_text} </td>
                                <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                <td> ${v.created_at} </td>
                            </tr>`;
                });
                $("#builder-io-histories-list").find(".builder-io-histories-list-view").html(html);
                $("#builder-io-histories-list").modal("show");
            } else {
                toastr["error"](response.error, "Message");
            }
        }
    });
});
</script>
@endsection