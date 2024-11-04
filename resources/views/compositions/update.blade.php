@extends('layouts.app')
@section('title')
Compositions
@endsection
@section('content')
<style type="text/css">
    .form-inline label {
        display: inline-block;
    }

    .form-control {
        height: 25px !important;
    }
    .composition .select2-container .select2-selection--single,
    .compositions .select2-container .select2-selection--single {
        height: 34px !important;
        border: 1px solid #ccc !important;
    }

    .composition .select2-container--default .select2-selection--single .select2-selection__rendered,
    .compositions .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px !important;
    }

</style>
<div class="row">
    <div class="col-md-12">
        <h2 class="page-heading">Compositions ({{count($compositions)}})</h2>
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
    <div class="col-md-12 ml-1 compositions">
        <div class="form-group small-field change-list-all-compostion-wrap">
            {{ html()->select('replace_with', $listcompostions)->class("form-control change-list-all-compostion select2")->style('width:282px') }}
            <button type="button" class="btn btn-secondary update-composition-selected">Update Selected</button>
        </div>
    </div>
    <div class="col-md-12 pl-5 pr-5 mt-5">
        <table class="table table-bordered composition">
            <tr>
                <th width="3%"><span><input type="checkbox" checked class="check-all-btn mr-2">&nbsp;</span></th>
                <th width="7%">SN</th>
                <th width="25%">Composition</th>
            </tr>
            @foreach($compositions as $key=>$composition)
            <tr>
                <td><input type="checkbox" name="composition[]"  checked value="{{ $composition->id }}" class="composition-checkbox mr-2"></td>
                <td>{{ $composition->id }} </td>
                <td>{{ $composition->name }}</td>
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


    $(document).on('click', '.update-composition-selected', function() {
        $("#loading-image").show();
        var changeto = $(".change-list-all-compostion").val();
        var changesFrom = $(".composition-checkbox:checked");
        var ids = [];
        $.each(changesFrom, function(k, v) {
            ids.push($(v).val());
        });

        $.ajax({
            type: 'POST',
            url: '/compositions/update-multiple-composition',
            beforeSend: function() {
                $("#loading-image").show();
            },
            data: {
                _token: "{{ csrf_token() }}",
                from: ids,
                to: changeto
            },
            dataType: "json"
        }).done(function(response) {
            console.log(response);
            $("#loading-image").hide();
            if (response.code == 200) {
                if (response.html != "") {
                    toastr['success'](response.message, 'success');
                    var redirectUrl = '/compositions/groups';
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

    $(document).on("click", ".change-selectbox", function() {
        $('#select' + $(this).data('id')).trigger('change');
    });


    $(document).on("click", ".check-all-btn", function() {
        $(".composition-checkbox").trigger("click");
    });



</script>
@endsection
@endsection