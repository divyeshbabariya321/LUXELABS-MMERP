@extends('layouts.app')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
    <style type="text/css">
        #ckbCheck{
            display: none;
        }
    </style>
@endsection
@section('content')
    <link rel="stylesheet" href="{{ mix('webpack-dist/css/treeview.css') }} ">

    <div class="col-12 p-0">
        <h2 class="page-heading">
            List Resources Center (<span id="translation_count">{{ $allresources->total() }}</span>)

            <div class="pull-right">
                <a href="{{ route('resourceimg.index') }}"><button type="button" class="btn btn-secondary">Active</button></a>
                <button type="button" class="btn btn-secondary" id="ckbCheck">Activate Selected</button>
                <button type="button" class="btn btn-secondary" id="ckbCheckAll">Select All</button>
            </div>
        </h2>
    </div>

    <div class="col-lg-12 margin-tb">
        <div class="form-group">
            <div class="row">
                <div class="col-md-2">
                    <input name="term" type="text" class="form-control" value="{{ isset($term) ? $term : '' }}" placeholder="Search Referral Program" id="term">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-image" id='submitSearch'><img src="/images/filter.png" /></button>
                    <button type="button" class="btn btn-image" id="resetFilter"><img src="/images/resend2.png" /></button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12 margin-tb">
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
        @if ($message = Session::get('danger'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
      
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id='tblImageResource' style="border: 1px solid #ddd;">
	            <thead>
                      <tr>
                        <th style="width: 2%;">#</th>
                        <th style="width: 2%;">Checkbox</th>
                        <th style="width: 10%;">Category</th>
                        <th style="width: 10%;">Sub Category</th>
                        <th>Subject</th>
                        <th>Description</th>
                        <th>Url</th>
                        <th style="width: 5%;">Images</th>
                        <th style="width: 10%;">Created at</th>
                        <th style="width: 10%;">Created by</th>
		                </tr>
	            </thead>
                <tbody>
                    @include('resourceimg.partial_pending')
                </tbody>
            </table>
        </div>
        {{ $allresources->render() }}
    </div>

	@include('resourceimg.partials.modal-create-resource-center')
	@include('resourceimg.partials.modal-create-edit-category')
    @include('resourceimg.partials.modal-images')
    @include('resourceimg.partial_email_description')
    <input type="hidden" name='hiddenShowImage' id='hiddenShowImage'data-target="#showresource" data-toggle="modal">
    <div id='modelShowImage'></div>
			
@endsection
@section('scripts')
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script>
$(function() {
    $('.selectpicker').selectpicker();
});

$('#filter-date').datetimepicker({
    format: 'YYYY-MM-DD'
});

$(document).ready(function() {
    $('#category_id').multiselect({
        nonSelectedText:'Select Category',
        buttonWidth:'300px',
        includeSelectAllOption: true,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,

        onChange:function(option, checked){

            $('#sub_cat_id').html('');
            $('#sub_cat_id').multiselect('rebuild');

            var selected = this.$select.val();
            if(selected.length > 0)
            {
                $.ajax({
                    url:"{{ url('/api/values-as-per-category') }}",
                    method:"POST",
                    data:{selected:selected,'_token':'{{ csrf_token() }}'},
                    success:function(data)
                    {

                        $('#sub_cat_id').html(data);
                        $('#sub_cat_id').multiselect('rebuild');

                    }
                })
            }
        }
    });
    $('#sub_cat_id').multiselect({
        nonSelectedText:'Please Sub Category',
        buttonWidth:'300px',
        includeSelectAllOption: true,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
    });
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
        $.ajax({
            url: "{{ url('resourceimg/pending/1') }}",
            dataType: "json",
            data: {
                term: term,
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
            url: "{{ url('resourceimg/pending/1') }}",
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

$(document).ready(function () {
    
    $(".checkBoxClass").change(function() {
        if(this.checked) {
            $('#ckbCheck').show();
            $("#ckbCheck").click(function () {
                var id = [];
                $.each($("input[name='id']:checked"), function(){
                    id.push($(this).val());
                });
                if(id.length == 0){
                    alert('Please Select');
                }else{
                    console.log(id);
                    $.ajax({
                        url:"{{ route('activate.resourceCat') }}",
                        method:"POST",
                        data:{id:id,'_token':'{{ csrf_token() }}'},
                        success:function(data)
                        {
                            alert('Resources Image Approved');
                            location.reload(true);
                        }
                    })
                 }
            });
        }
    });
}); 
       
$(document).ready(function () {
    $("#ckbCheckAll").click(function () {
        $(".checkBoxClass").prop('checked', true);
        $(this).html('Activate Images');
        $("#ckbCheckAll").click(function () {
             var id = [];
            $.each($("input[name='id']:checked"), function(){
            id.push($(this).val());
            });
            if(id.length == 0){
                alert('Please Select');
            }else{
                console.log(id);
                $.ajax({
                    url:"{{ route('activate.resourceCat') }}",
                    method:"POST",
                    data:{id:id,'_token':'{{ csrf_token() }}'},
                    success:function(data)
                    {
                        alert('Resources Image Approved');
                        location.reload(true);
                    }
                })
            }
        });
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
<script type="text/javascript">
function PasteImage(){var e=document.getElementById("my_canvas").toDataURL();$("#cpy_img").val(e),$("#save_img").fadeIn(200),$(".msg").empty(),$(".msg").css("color","green"),$(".msg").text("Image Loaded Successfully."),$(".can_id").attr("placeholder","Image Loaded Successfully, Paste another to change."),$("#src_img").attr("src",e)}var CLIPBOARD=new CLIPBOARD_CLASS("my_canvas",!0);function CLIPBOARD_CLASS(e,t){var a=this,n=document.getElementById(e),i=document.getElementById(e).getContext("2d");document.addEventListener("paste",function(e){"can_id"==e.target.id&&(console.log(e),a.paste_auto(e))},!1),this.paste_auto=function(e){if(e.clipboardData){var t=e.clipboardData.items;if(!t)return;for(var a=!1,n=0;n<t.length;n++)if($("#cpy_img").val(""),-1!==t[n].type.indexOf("image")){var i=t[n].getAsFile(),c=(window.URL||window.webkitURL).createObjectURL(i);this.paste_createImage(c),a=!0}1==a?(e.preventDefault(),$(".msg").text("Image Loading, Please Wait."),$(".msg").css("color","red"),setTimeout(PasteImage,5e3)):(e.preventDefault(),$(".can_id").attr("placeholder","Please paste only image."))}},this.paste_createImage=function(e){var a=new Image;a.onload=function(){1==t?(n.width=a.width,n.height=a.height):i.clearRect(0,0,n.width,n.height),i.drawImage(a,0,0)},a.src=e}}
</script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/treeview.js') }} "></script>
@endsection