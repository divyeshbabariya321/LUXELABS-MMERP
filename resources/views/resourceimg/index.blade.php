@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection
@section('content')
    <link rel="stylesheet" href="{{ mix('webpack-dist/css/treeview.css') }} ">
    <div class="row">
        <div class="col-12">
            <h2 class="page-heading">
                List Resources Center (<span id="translation_count">{{ $allresources->total() }}</span>)

                <div class="pull-right">
                    
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#email-receive-modal">Email Receive</button>
                    
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#status-create">Add Status</button>
                    <button class="btn btn-secondary" data-toggle="modal" data-target="#newStatusColor"> Status Color</button>
                    <a href="{{ url('/resourceimg/pending/1') }}"><button type="button" class="btn btn-secondary">Pending</button></a>
                    <button type="button" class="btn btn-secondary" title="Add Category" data-toggle="modal" data-target="#addcategory">Add Category</button>
                    <button type="button" class="btn btn-secondary" title="Edit Category" data-toggle="modal" data-target="#editcategory">Edit Category</button>
                    <button type="button" class="btn btn-secondary" title="Add Resource" data-toggle="modal" data-target="#addresource">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </h2>
        </div>
        
        <div class="col-lg-12 ">
            <div class="form-group">
                <div class="col-md-2">
                    <input name="term" type="text" class="form-control"
                        value="{{ isset($term) ? $term : '' }}" placeholder="Search keyword"
                        id="term">
                </div>
                <div class="col-md-2">
                    <select name="category" id="filter_category">
                        @foreach ($categories as $category)
                            <option value="{{$category->id}}">{{$category->title}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="category" id="filter_sub_category">
                        @foreach ($sub_categories as $s_category)
                            <option value="{{$s_category->id}}">{{$s_category->title}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-image" id='submitSearch'><img src="/images/filter.png" /></button>
                    <button type="button" class="btn btn-image" id="resetFilter"><img src="/images/resend2.png" /></button>
                </div>
            </div>
        </div>

        <div class="col-lg-12 margin-tb">            
            @if ($message = Session::get('danger'))
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            @include('partials.flash_messages')

            <div class="table-responsive col-md-12" style="margin-top : 30px;">
                <table class="table table-striped table-bordered" id='tblImageResource' style="border: 1px solid #ddd;">
                    <thead>
                        <tr>
                            <th style="width: 2%;">#</th>
                            <th style="width: 10%;">Category</th>
                            <th style="width: 15%;">Sub Category</th>
                            <th style="width: 30%;">Url</th>
                            <th style="">Subject</th>
                            <th style="">Description</th>
                            <th style="width: 5%;">Images</th>
                            <th style="width: 10%;">Status</th>
                            <th style="width: 15%;">Remarks</th>
                            <th style="width: 10%;">Created at</th>
                            <th style="width: 10%;">Created by</th>
                        </tr>
                    </thead>
                    <tbody>
                        @include('resourceimg.partial_index')

                    </tbody>
                </table>
            </div>
            {{ $allresources->render() }}
        </div>
    </div>

    <div id="status-create" class="modal fade in" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
          <h4 class="modal-title">Add Stauts</h4>
          <button type="button" class="close" data-dismiss="modal">×</button>
          </div>
          <form  method="POST" id="status-create-form">
            @csrf
            @method('POST')
              <div class="modal-body">
                <div class="form-group">
                  {{ html()->label('Name', 'status_name')->class('form-control-label') }}
                  {{ html()->text('status_name')->class('form-control')->required()->attribute('rows', 3) }}
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary status-save-btn">Save</button>
              </div>
            </div>
          </form>
        </div>

      </div>
    </div>
    @include("resourceimg.partials.modal-status-color")
    @include('resourceimg.partials.status-history')
    @include('resourceimg.partials.remarks-history')
    @include('resourceimg.partials.modal-create-resource-center')
    @include('resourceimg.partials.modal-create-edit-category')
    @include('resourceimg.partials.modal-images')
    @include('resourceimg.partial_email_description')
    
    @include('resourceimg.partials.modal-email-receive')
    
    <input type="hidden" name='hiddenShowImage' id='hiddenShowImage'data-target="#showresource" data-toggle="modal">
    <div id='modelShowImage'></div>

@endsection
@section('scripts')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script>
        $(function() {
            $('.selectpicker').selectpicker();
        });

        $('#filter-date').datetimepicker({
            format: 'YYYY-MM-DD'
        });
        $("#filter_sub_category").select2({width: "100%", placeholder: "Select Subcategory", multiple: true})
        $("#filter_category").select2({width: "100%", placeholder: "Select Category", multiple: true})
        $("#filter_sub_category, #filter_category").val(null).trigger('change')
        $(document).ready(function() {
            $('#category_id').select2({ width: "100%" });
            $('#category_id').val(null).trigger('change');
            $('#category_id').change(function (e) { 
                e.preventDefault();
                $('#sub_cat_id').html('');
                // $('#sub_cat_id').multiselect('rebuild');
                // console.log($(this).val());
                // return
                var selected = $(this).val();
                if (selected.length > 0) {
                    $.ajax({
                        url: "{{ url('/api/values-as-per-category') }}",
                        method: "POST",
                        data: {
                            selected: selected,
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(data) {

                            $('#sub_cat_id').html(data);
                            // $('#sub_cat_id').multiselect('rebuild');
                            $("#sub_cat_id").select2("destroy").select2({width: "100%"});
                            $("#sub_cat_id").val(null).trigger('change');
                        }
                    })
                }
            });
            // $('#category_id').multiselect({
            //     nonSelectedText: 'Select Category',
            //     buttonWidth: '300px',
            //     includeSelectAllOption: true,
            //     enableFiltering: true,
            //     enableCaseInsensitiveFiltering: true,

            //     onChange: function(option, checked) {

            //         $('#sub_cat_id').html('');
            //         $('#sub_cat_id').multiselect('rebuild');

            //         var selected = this.$select.val();
            //         if (selected.length > 0) {
            //             $.ajax({
            //                 url: "{{ url('/api/values-as-per-category') }}",
            //                 method: "POST",
            //                 data: {
            //                     selected: selected,
            //                     '_token': '{{ csrf_token() }}'
            //                 },
            //                 success: function(data) {

            //                     $('#sub_cat_id').html(data);
            //                     $('#sub_cat_id').multiselect('rebuild');

            //                 }
            //             })
            //         }
            //     }
            // });
            $('#sub_cat_id').select2({
                width: "100%"    
            });
            // $('#sub_cat_id').multiselect({
            //     nonSelectedText: 'Please Sub Category',
            //     buttonWidth: '300px',
            //     includeSelectAllOption: true,
            //     enableFiltering: true,
            //     enableCaseInsensitiveFiltering: true,
            // });

            $(document).on('click', '#myShowImg', function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ url('show-images/resource') }}",
                    method: "POST",
                    data: {
                        id: $(this).attr("img-id")
                    },
                    success: function(data) {
                        $("#modelShowImage").html(data.html);
                        $("#hiddenShowImage").click();
                    }
                })
            });

            $(document).on('click', '#submitSearch', function() {
                //term = $("#term").val();
                 term =  $("input[name='term']").val();
                 category =  $("#filter_category").val();
                 sub_category =  $("#filter_sub_category").val();
                $.ajax({
                    url: "{{ url('resourceimg') }}",
                    dataType: "json",
                    data: {
                        term: term,
                        sub_category,
                        category
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                    },

                }).done(function(data) {
                    $("#loading-image").hide();
                    $("#tblImageResource tbody").empty().html(data.tbody);
                    $("#translation_count").text(data.count);
                    if (data.links.length > 10) {
                        $('ul.pagination').replaceWith(data.links);
                    } else {
                        $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                    }

                }).fail(function(jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });
            });
            $(document).on('click', '#resetFilter', function() {
                blank = '';
                $.ajax({
                    url: "{{ url('resourceimg') }}",
                    dataType: "json",
                    data: {
                        blank: blank,
                    },
                    beforeSend: function() {
                        $("#loading-image").show();
                    },

                }).done(function(data) {
                    $("#loading-image").hide();
                    $('#term').val('')
                    $("#filter_sub_category, #filter_category").val(null).trigger('change')
                    $('#translation-select').val('')
                    $("#tblImageResource tbody").empty().html(data.tbody);
                    $("#translation_count").text(data.count);
                    if (data.links.length > 10) {
                        $('ul.pagination').replaceWith(data.links);
                    } else {
                        $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                    }

                }).fail(function(jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });
            });
        });
    </script>
    <script type="text/javascript">
        function PasteImage() {
            var e = document.getElementById("my_canvas").toDataURL();
            $("#cpy_img").val(e), $("#save_img").fadeIn(200), $(".msg").empty(), $(".msg").css("color", "green"), $(".msg")
                .text("Image Loaded Successfully."), $(".can_id").attr("placeholder",
                    "Image Loaded Successfully, Paste another to change."), $("#src_img").attr("src", e)
        }
        var CLIPBOARD = new CLIPBOARD_CLASS("my_canvas", !0);

        function CLIPBOARD_CLASS(e, t) {
            var a = this,
                n = document.getElementById(e),
                i = document.getElementById(e).getContext("2d");
            document.addEventListener("paste", function(e) {
                "can_id" == e.target.id && (console.log(e), a.paste_auto(e))
            }, !1), this.paste_auto = function(e) {
                if (e.clipboardData) {
                    var t = e.clipboardData.items;
                    if (!t) return;
                    for (var a = !1, n = 0; n < t.length; n++)
                        if ($("#cpy_img").val(""), -1 !== t[n].type.indexOf("image")) {
                            var i = t[n].getAsFile(),
                                c = (window.URL || window.webkitURL).createObjectURL(i);
                            this.paste_createImage(c), a = !0
                        } 1 == a ? (e.preventDefault(), $(".msg").text("Image Loading, Please Wait."), $(".msg").css(
                        "color", "red"), setTimeout(PasteImage, 5e3)) : (e.preventDefault(), $(".can_id").attr(
                        "placeholder", "Please paste only image."))
                }
            }, this.paste_createImage = function(e) {
                var a = new Image;
                a.onload = function() {
                    1 == t ? (n.width = a.width, n.height = a.height) : i.clearRect(0, 0, n.width, n.height), i
                        .drawImage(a, 0, 0)
                }, a.src = e
            }
        }

        $(document).on("click", ".status-save-btn", function(e) {
            e.preventDefault();
            var $this = $(this);
            $.ajax({
                url: "{{route('resourceimg.status.create')}}",
                type: "post",
                data: $('#status-create-form').serialize()
            }).done(function(response) {
                if (response.code = '200') {
                    $('#loading-image').hide();
                    $('#addPostman').modal('hide');
                    toastr['success']('Status  Created successfully!!!', 'success');
                    location.reload();
                } else {
                    toastr['error'](response.message, 'error');
                }
            }).fail(function(errObj) {
                $('#loading-image').hide();
                toastr['error'](errObj.message, 'error');
            });
        });

        $('.status-dropdown').change(function(e) {
            e.preventDefault();
            var postId = $(this).data('id');
            var selectedStatus = $(this).val();
            console.log("Dropdown data-id:", postId);
            console.log("Selected status:", selectedStatus);


            // Make an AJAX request to update the status
            $.ajax({
                url: '/resourceimg/resourceimg-update-status',
                method: 'POST',
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                  postId: postId,
                  selectedStatus: selectedStatus
                },
                success: function(response) {
                    toastr['success']('Status has been updated successfully!!!', 'success');
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    // Handle the error here
                    console.error(error);
                }
            });
        });

        $(document).on('click', '.status-history-show', function() {
            var id = $(this).attr('data-id');
            $.ajax({
                method: "GET",
                url: `{{ route('resourceimg.status.histories', [""]) }}/` + id,
                dataType: "json",
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${(v.old_value != null) ? v.old_value.status_name : ' - ' } </td>
                                        <td> ${(v.new_value != null) ? v.new_value.status_name : ' - ' } </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#resourceimg-status-histories-list").find(".resourceimg-status-histories-list-view").html(html);
                        $("#resourceimg-status-histories-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        function saveRemarks(resource_images_id){

            var remarks = $("#remark_"+resource_images_id).val();

            if(remarks==''){
                alert('Please enter remarks.');
                return false;
            }

            $.ajax({
                url: "{{route('resourceimg.saveremarks')}}",
                type: 'POST',
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'resource_images_id' :resource_images_id,
                    'remarks' :remarks,
                },
                beforeSend: function() {
                    $(this).text('Loading...');
                    $("#loading-image").show();
                },
                success: function(response) {
                    toastr['success']('Remarks hase been added successfully!!!', 'success');
                    $("#loading-image").hide();
                }
            }).fail(function(response) {
                $("#loading-image").hide();
                toastr['error'](response.responseJSON.message);
            });
        }

        $(document).on('click', '.remarks-history-show', function() {
            var resource_images_id = $(this).attr('data-id');
            $.ajax({
                url: "{{route('resourceimg.getremarks')}}",
                type: 'POST',
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'resource_images_id' :resource_images_id,
                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $("#resourceimg-remarks-histories-list").find(".resourceimg-remarks-histories-list-view").html(response.html);
                        $("#resourceimg-remarks-histories-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

        $(document).on('click', '.view-resources-center-images', function() {
            var resource_images_id = $(this).attr('data-id');
            $.ajax({
                url: "{{route('resourceimg.getimages')}}",
                type: 'POST',
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'resource_images_id' :resource_images_id,
                },
                success: function(response) {
                    if (response.status) {
                        $("#resourceimg-images-histories-list").find(".resourceimg-images-histories-list-view").html(response.html);
                        $("#resourceimg-images-histories-list").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });
    </script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/treeview.js') }} "></script>
@endsection
