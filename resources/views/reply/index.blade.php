@extends('layouts.app')

@section('styles')
<style>
    .line-breaks {
        white-space: pre-line;
    }
</style>
@endsection

@section('content')
<div class="row ">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Quick Replies List</h2>
        <div class="row m-4">
            <div class="col-md-6">            
                <form action="{{ route('reply.index') }}" method="get" class="search">
                    <div class="row">
                        <div class="col-md-4 p-0">
                            <select class="form-control select2" multiple name="category_id[]" id="category_id_filter">
                                <option value="">Select Category</option>
                                @foreach($reply_categories as $cat)
                                    <option {{ in_array($cat->id, (array)request()->get('category_id')) ? 'selected' : '' }} value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 pd-sm">
                            <select class="form-control" name="sub_category_id" id="sub_category_id_filter">
                                <option value="">Select Sub Category</option>
                                @if(!empty($replysubcategories))
                                    @foreach($replysubcategories as $cat)
                                        <option {{request()->get('sub_category_id')==$cat->id ? 'selected' : ''}} value="{{$cat->id}}">{{$cat->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                        <div class="col-md-3 pd-sm">
                            <input type="text" name="keyword" placeholder="keyword" class="form-control" value="{{ request()->get('keyword') }}">
                        </div>

                        <div class="col-md-1 pd-sm">
                             <button type="submit" class="btn btn-image search mt-0" onclick="document.getElementById('download').value = 1;">
                                <img src="{{ asset('images/search.png') }}" alt="Search">
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <div class="pull-right">
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#categoryModal">Create Category</button>
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#categorySubModal">Create Sub Category</button>
                    <!-- <a class="btn btn-secondary" href="{{ route('reply.create') }}">+</a> -->
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#createQuickModal">Create Quick Reply</button>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<div class="row ml-4 mr-4">
    <div class="col-md-12 p-0">
        <div class="panel-group" style="margin-bottom: 5px;">
            <div class="panel mt-3 panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" href="#collapse1">Category Assignments</a>
                    </h4>
                </div>
                <div id="collapse1" class="panel-collapse collapse">
                    <div class="panel-body">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th>Model</th>
                                <th>Category</th>
                            </tr>
                            
                            <tr>
                                <td>Users</td>
                                <td>
                                    <select class="form-control update-default-category" name="users_category" data-id="users">
                                        <option value="">None</option>
                                        @foreach($reply_categories as $cat)
                                        <option value="{{$cat->id }}" {{$cat->default_for=='users' ? 'selected': ''}}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Customers</td>
                                <td>
                                    <select class="form-control update-default-category" name="customers_category" data-id="customers">
                                        <option value="">None</option>
                                        @foreach($reply_categories as $cat)
                                        <option value="{{$cat->id }}" {{$cat->default_for=='customers' ? 'selected': ''}}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Vendors</td>
                                <td>
                                    <select class="form-control update-default-category" name="vendors_category" data-id="vendors">
                                        <option value="">None</option>
                                        @foreach($reply_categories as $cat)
                                        <option value="{{$cat->id }}" {{$cat->default_for=='vendors' ? 'selected': ''}}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                           
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tab-content ">
    <!-- Pending task div start -->
    <div class="tab-pane active" id="1">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered expandable-table">
                    <tr>
                        <th width="2%">ID</th>
                        <th width="10%">Category</th>
                        <th width="10%">Sub Category</th>
                        <th width="50%">Quick Reply</th>
                        <th width="10%">Model</th>
                        <th width="20%">Action</th>
                    </tr>
                    @foreach ($replies as $key => $reply)
                    {{-- {{ dd($reply) }} --}}
                        <tr>
                            <td id="reply_id">{{ $reply->id }}</td>

                            @if(!isset($reply->category->parent))
                                <td id="reply_category_name">{{ ($reply->category) ?  $reply->category->name : '-' }}</td>
                                <td>-</td>
                                @php 
                                    $category_id_data = $reply->category_id;
                                    $sub_category_id_data = 0;
                                @endphp
                            @else
                                <td id="reply_category_name">{{ ($reply->category->parent) ?  $reply->category->parent->name : '-' }}</td>
                                <td id="reply_category_name">{{ ($reply->category) ?  $reply->category->name : '-' }}</td>

                                @php 
                                    $category_id_data = $reply->category->parent_id;
                                    $sub_category_id_data = $reply->category->id;
                                @endphp
                            @endif
                            <td id="reply_text">
                                <span class="collapsable" style="color: rgb(15, 15, 81); text-decoration: underline;" onclick="expandable(this)">
                                    {{ \Illuminate\Support\Str::limit($reply->reply, 50) }}
                                </span>
                                <span class="line-breaks expandable" style="display: none;" onclick="collapsable(this)">
                                    {!! $reply->reply !!}
                                </span>
                            </td>
                            <td id="reply_model">{{ $reply->model }}</td>
                            <td>
                                <!-- <a class="btn btn-image" href="{{ route('reply.edit',$reply->id) }}"><img src="/images/edit.png" /></a> -->

                                <a class="btn btn-image intent-edit" data-toggle="modal" data-target="#auto-reply-popup" title="Add Popup">
                                  <i class="fa fa-plus" aria-hidden="true"></i>
                                </a>
                                
                                <a href="javascript:;" data-note-id = "{{$reply->id}}" data-c-id = "{{$category_id_data}}" data-sc-id ="{{$sub_category_id_data}}" class="reply_edit btn-xs btn btn-image p-2"><img src="/images/edit.png"></a>

                                {{ html()->form('DELETE', route('reply.destroy', [$reply->id]))->style('display:inline')->open() }}
                                <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                                {{ html()->form()->close() }}

                                <button type="button"  class="btn-xs btn btn-image p-2 btn btn-copy-reply" data-id="{{ $reply->reply }} ">
                                  <i class="fa fa-clone" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
                {!! $replies->links() !!}
        </div>
    </div>
</div>                        

<div id="categoryModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Category</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('reply.category.store') }}" method="POST" enctype="multipart/form-data" id="approvalReplyForm">
                @csrf

                <div class="modal-body">

                    <div class="form-group">
                        <strong>Category Name:</strong>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        @if ($errors->has('name'))
                            <div class="alert alert-danger">{{$errors->first('name')}}</div>
                        @endif
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Create</button>
                </div>
            </form>
        </div>

    </div>
</div>

<div id="categorySubModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Sub Category</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('reply.subcategory.store') }}" method="POST" enctype="multipart/form-data" id="approvalReplyForm">
                @csrf

                <div class="modal-body">

                    <div class="form-group">
                        <strong>Category:</strong>
                        <select class="form-control" name="parent_id" id="parent_id">
                            <option value="">Select Category</option>
                            @foreach($reply_main_categories as $cat)
                                <option value="{{$cat->id}}">{{$cat->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <strong>Sub Category Name:</strong>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        @if ($errors->has('name'))
                            <div class="alert alert-danger">{{$errors->first('name')}}</div>
                        @endif
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Create</button>
                </div>
            </form>
        </div>

    </div>
</div>

<div id="createQuickModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Quick Reply</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{route('reply.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div class="form-group">
                        <strong>Quick Reply</strong>
                        <textarea class="form-control" name="reply" placeholder="Quick Reply" required></textarea>
                        @if ($errors->has('reply'))
                            <div class="alert alert-danger">{{$errors->first('reply')}}</div>
                        @endif
                    </div>
                    
                    <div class="form-group">
                        <strong>Model</strong>
                        <select class="form-control" name="model" required>
                          <option value="">Select Model</option>
                          <option value="Approval Lead">Approval Lead</option>
                          <option value="Internal Lead">Internal Lead</option>
                          <option value="Approval Order">Approval Order</option>
                          <option value="Internal Order">Internal Order</option>
                          <option value="Approval Purchase">Approval Purchase</option>
                          <option value="Internal Purchase">Internal Purchase</option>
                        </select>
                        @if ($errors->has('model'))
                            <div class="alert alert-danger">{{$errors->first('model')}}</div>
                        @endif
                    </div>
                    
                    <div class="form-group">
                        <strong>Category</strong>
                        <select class="form-control" name="category_id" required id="category_id_dropdown_create">
                          @foreach ($reply_categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                          @endforeach
                        </select>
                        @if ($errors->has('model'))
                            <div class="alert alert-danger">{{$errors->first('model')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Sub category</strong>
                        <select class="form-control" name="sub_category_id" id="subcategory_create">
                            <option value="">Select Subcategory</option>
                        </select>
                    </div>        
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Create</button>
                </div>

            </form>
        </div>

    </div>
</div>

<div id="erp-notes" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Quick Reply</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            
            </div>
        </div>
    </div>
</div>

@include('partials.modals.auto-reply')

{{-- @endsection

@section('scripts') --}}
<script type="text/javascript">
function expandable(span) {
    span.style.display = 'none';
    span.nextElementSibling.style.display = 'inline';
}

function collapsable(span) {
    span.style.display = 'none';
    span.previousElementSibling.style.display = 'inline';
}

$( document ).ready(function() {

    $('#category_id_filter').select2();

    $(document).on("change",".update-default-category",function() {
        var model=$(this).attr('data-id');
        var cat_id=$(this).val();
        console.log(model);
        console.log(cat_id);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            url: '/reply/category/setDefault',
            data: {
                "model": model,
                "cat_id": cat_id,
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    toastr['success'](response.message);
                } else {
                    
                    toastr['error'](response.message);
                }
            },
            error: function() {
                toastr['error']('Something might be wrong please try again later!');
            }
        });

    });
});

$(document).on("click",".intent-edit",function() {
    var reply_model = $(this).closest("tr").children('#reply_model').text();
    var reply_text = $(this).closest("tr").children('#reply_text').text();
    var reply_category_name = $(this).closest("tr").children('#reply_category_name').text();
    var reply_id = $(this).closest("tr").children('#reply_id').text();
    $('#reply_id_edit').val(reply_id);
    $('#intentValues').val(reply_text);
    $('#intentReply').val(reply_text);
    $('#intentModel').val(reply_model);
    $("#intentCategory option").each(function() {
        if($(this).text() == reply_category_name) {
            $(this).attr('selected', 'selected');
        }
    });
});

var searchForIntent = function(ele) {
    var intentBox = ele.find(".search-intent");
    if (intentBox.length > 0) {
        intentBox.select2({
            placeholder: "Enter intent name or create new one",
            width: "100%",
            tags: true,
            allowClear: true,
            ajax: {
                url: '/chatbot/question/search',
                dataType: 'json',
                processResults: function(data) {
                    return {
                        results: data.items
                    };
                }
            }
        }).on("change.select2", function() {
            var $this = $(this);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: '/chatbot/question/submit',
                data: {
                    "name": $this.val(),
                    "question": $("#dialog-save-response-form").find(".question-insert").val(),
                    "category_id" : $("#dialog-save-response-form").find(".search-category").val()
                },
                dataType: "json",
                success: function(response) {
                    if (response.code != 200) {
                        toastr['error']('Can not store intent please review or use diffrent name!');
                    } else {
                        toastr['success']('Success!');
                    }
                },
                error: function() {
                    toastr['error']('Can not store intent name please review!');
                }
            });
        });
    }
};
searchForIntent($("#auto-reply-popup"));

$(document).on("click",".btn-copy-reply",function() {
    var password = $(this).data('id');
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(password).select();
    document.execCommand("copy");
    $temp.remove();
    alert("Copied!");
});

$(document).on('click', '.reply_edit', function () {

   var $this = $(this);
    $.ajax({
        type: "GET",
        data : {
          id : $this.data("note-id"),
          c_id : $this.data("c-id"),
          sc_id : $this.data("sc-id"),
        },
        url: "{{ route('editReply') }}"
    }).done(function (data) {
       $("#erp-notes").find(".modal-body").html(data);
       
       $("#erp-notes").modal("show");
    }).fail(function (response) {
        console.log(response);
    });
});

$(document).ready(function () {
    $('#category_id_dropdown_create').on('change', function () {
        var categoryId = $(this).val();
        console.log(categoryId);
        if (categoryId) {
            $.ajax({
                url: '/get-subcategories',
                type: 'GET',
                data: { category_id: categoryId },
                success: function (data) {
                    $('#subcategory_create').empty();
                    $.each(data, function (key, value) {
                        $('#subcategory_create').append('<option value="' + key + '">' + value + '</option>');
                    });
                }
            });
        } else {
            $('#subcategory_create').empty();
        }
    });
});

$(document).ready(function () {
    $('#category_id_filter').on('change', function () {
        var categoryId = $(this).val();
        if (categoryId) {
            $.ajax({
                url: '/get-subcategories',
                type: 'GET',
                data: { category_id: categoryId },
                success: function (data) {
                    $('#sub_category_id_filter').empty();
                        $('#sub_category_id_filter').append('<option value="">Select Sub Category</option>');
                    $.each(data, function (key, value) {
                        $('#sub_category_id_filter').append('<option value="' + key + '">' + value + '</option>');
                    });
                }
            });
        } else {
            $('#sub_category_id_filter').empty();
        }
    });
});
</script>
@endsection