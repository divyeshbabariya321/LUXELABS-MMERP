@extends('layouts.app')
@section('title')
New Category Reference Group
@endsection
@section('content')
<style type="text/css">
    .form-inline label {
        display: inline-block;
    }
    .form-control {
        height: 25px !important;
    }
    .category .select2-container .select2-selection--single,
    .category .select2-container .select2-selection--single {
        height: 34px !important;
        border: 1px solid #ccc !important;
    }
    .category .select2-container--default .select2-selection--single .select2-selection__rendered,
    .category .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <h2 class="page-heading">New Category Reference Group ({{count($categories)}})</h2>
    </div>
    @if ($message = Session::get('success'))
    <div class="col-md-12">
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    </div>
    @endif
</div>

</div>


<div class="row">
    <div class="col-md-12 ml-1 category">
        <div class="form-group small-field change-list-all-category-wrap">
            <select class="select2 form-control change-list-all-category" style="width: 500px;">
                @foreach ($categoryAll as $cat)
                    <option value="{{ $cat['id'] }}">{{ $cat['value'] }}</option>
                @endforeach
            </select>
            <button type="button" class="btn btn-secondary update-category-selected">Update Selected</button>
        </div>
    </div>
    <div class="col-md-12 pl-5 pr-5 mt-5">
        <table class="table table-bordered category">
            <tr>
                <th width="3%"><span><input type="checkbox" checked class="check-all-btn mr-2">&nbsp;</span></th>
                <th width="7%">SN</th>
                <th width="25%">Category</th>
            </tr>
            @foreach($categories as $key=>$category)
            <tr>
                <td><input type="checkbox" name="category[]"  checked value="{{ $category }}" class="category-checkbox mr-2"></td>
                <td>{{ $category->id }} </td>
                <td>{{ $category->name }}</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>

@section('scripts')
<script type="text/javascript">
    $(".select2").select2({
        "tags": true
    });
    $(document).on('click', '.update-category-selected', function() {
        $("#loading-image").show();
        var changeto = $(".change-list-all-category").val();
        var changesFrom = $(".category-checkbox:checked");
        var ids = [];
        $.each(changesFrom, function(k, v) {
            ids.push($(v).val());
        });
        var oldCatid = {{ $unKnownCategoryId }};
        $.ajax({
            type: 'POST',
            url: '/category/references/update-multiple-category',
            beforeSend: function() {
                $("#loading-image").show();
            },
            data: {
                _token: "{{ csrf_token() }}",
                from: ids,
                to: changeto,
                old_cat_id: oldCatid
            },
            dataType: "json"
        }).done(function(response) {
            console.log(response);
            $("#loading-image").hide();
            if (response.code == 200) {
                if (response.html != "") {
                    toastr['success'](response.message, 'success');
                    var redirectUrl = '/category/new-references-group';
                    window.location.href = redirectUrl;
                } else {
                    $("#loading-image").hide();
                    toastr['error']('Sorry, something went wrong', 'error');
                }
            }
        }).fail(function(response) {
            $("#loading-image").hide();
            toastr['error']('Sorry, something went wrong', 'error');
        });
    });

    $(document).ready(function() {
    // When the check-all-btn is clicked
    $(".check-all-btn").on("change", function() {
        // If check-all-btn is checked, check all category-checkbox
        if ($(this).prop("checked")) {
            $(".category-checkbox").prop("checked", true);
        } else {
            // If check-all-btn is unchecked, uncheck all category-checkbox
            $(".category-checkbox").prop("checked", false);
        }
    });

    // When any category-checkbox is clicked
    $(".category-checkbox").on("change", function() {
        // Check if all category-checkbox are checked, then check the check-all-btn
        if ($(".category-checkbox:checked").length === $(".category-checkbox").length) {
            $(".check-all-btn").prop("checked", true);
        } else {
            // If any category-checkbox is unchecked, uncheck the check-all-btn
            $(".check-all-btn").prop("checked", false);
        }
    });
});

    // $(document).on("click", ".change-selectbox", function() {
    //     $('#select' + $(this).data('id')).trigger('change');
    // });
    // $(document).on("click", ".check-all-btn", function() {
    //     $(".category-checkbox").trigger("click");
    // });
</script>
@endsection
@endsection