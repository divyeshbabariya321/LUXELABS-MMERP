@extends('layouts.app')

@section('title', 'Category | Map')

@section("styles")

<style>
.dropdown-submenu {
  position: relative;
}

.dropdown-submenu>.dropdown-menu {
  top: 0;
  left: 100%;
  margin-top: -6px;
  margin-left: -1px;
  -webkit-border-radius: 0 6px 6px 6px;
  -moz-border-radius: 0 6px 6px;
  border-radius: 0 6px 6px 6px;
}

.dropdown-submenu:hover>.dropdown-menu {
  display: block;
}

.dropdown-submenu>a:after {
  display: block;
  content: " ";
  float: right;
  width: 0;
  height: 0;
  border-color: transparent;
  border-style: solid;
  border-width: 5px 0 5px 5px;
  border-left-color: #ccc;
  margin-top: 5px;
  margin-right: -10px;
}

.dropdown-submenu:hover>a:after {
  border-left-color: #fff;
}

.dropdown-submenu.pull-left {
  float: none;
}

.dropdown-submenu.pull-left>.dropdown-menu {
  left: -100%;
  margin-left: 10px;
  -webkit-border-radius: 6px 0 6px 6px;
  -moz-border-radius: 6px 0 6px 6px;
  border-radius: 6px 0 6px 6px;
}

.cat-dropdown .btn .caret {
    display: inline-block !important;
}
</style>

@endsection

@section('large_content')
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
        50% 50% no-repeat;display:none;">
</div>

<div class="container-fluid" style="height: 100vh;">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">{{$title}} ({{ $unmapped_categories_count }})<span class="count-text"></span></h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="form-inline">
                <form action="" id="nodeCategoryMapForm">
                    <div class="form-group input-group-lg">
                        <div class="p-0">
                            <select class="form-control" name="supplier">
                                <option value="">Select supplier</option>
                                @foreach ($suppliers as $k => $supplier)
                                    <option value="{{ $supplier }}">{{ $supplier }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="hidden" id="filterCategoryVal" name="filter_categories_val">
                        <input type="hidden" id="filterCategory" name="filter_categories">
                        {{ html()->select("category_id", [], null)->class("form-control select2 filter-category-cls")->data('placeholder', "Select Category ")->style('width:100%') }}
                    </div>
                    
                    <div class="form-group">
                        <button class="btn btn-default m-0" id="resetFilterCategory"><span><i class="fa fa-undo" aria-hidden="true"></i></span></button>
                    </div>

                    <div class="form-group">
                            <button type="button" class="btn btn-info btn-search-action">
                                <span><i class="fa fa-search" aria-hidden="true"></i></span>
                            </button>
                    </div>
                </form>
                <div class="form-group inline ml-5">
                    <b>Pagination:</b>
                </div>
                <div class="form-group inline">
                    <select class="form-control" name="paging" id="paging">
                        <option value="">Select pagination</option>
                        @for($page = 10 ; $page <= 100; $page+=10 )
                            <option value="{{ $page }}">{{ $page }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <button type="button" class="btn btn-primary map-category-action" data-type="checked-rows">
                    Map Category
                </button>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="row" id="tableDataWrapper">
                @include('node-category-map.partials.table-data')
            </div>
        </div>
    </div>

</div>

@include('node-category-map.partials.map-category-modal')

@endsection

@section('scripts')
<script>
    $("#paging").on('change',function(){
        $("#loading-image").show();
        $.ajax({
            type: 'POST',
            url: '/scrapper-category-map-paging',
            data: {paging: $(this).val()},
        }).done(response => {
            $('#tableDataWrapper').html(response);
            $("#loading-image").hide();
        }).fail(function(response) {
            $("#loading-image").hide();
        });
    });
</script>
<script>
    let currentPageIndex = 0;
    let checkedRows = [];

    $('.category-cls').select2({
        ajax: {
            url: '/scrapper-category-map/get-category-by-search',
            dataType: 'json'
        }
    });

    $('.filter-category-cls').select2({
        ajax: {
            url: '/scrapper-category-map/get-category-by-search',
            dataType: 'json'
        }
    });

    $(document).on('change', '.category-cls', function() {
        // var cateId = $(this).val();
        // $("#loading-image-preview").hide();
        $('#selected_map').html($(this).text());
        // if (cateId != "") {
        //     $.ajax({
        //         type: 'GET',
        //         url: '/scrapper-category-map/get-categories-by-id',
        //         data: {category_id: cateId},
        //     }).done(response => {
        //         $("#loading-image-preview").hide();
        //         $('#mappedInfo').show();
        //         $('#selected_map').html(response.results);
        //         $('#mapped-categories-val').val(JSON.stringify(response.mapped_categories_val));
        //         $('#mapped-categories').val(JSON.stringify(response.category_ids));
        //     }).fail(function(response) {
        //         $("#loading-image-preview").hide();
        //     });
        // }
    });

    $(document).on('change', '.filter-category-cls', function() {
        var cateId = $(this).val();
        $("#loading-image-preview").hide();
        if (cateId != "") {
            $.ajax({
                type: 'GET',
                url: '/scrapper-category-map/get-categories-by-id',
                data: {category_id: cateId},
            }).done(response => {
                $("#loading-image-preview").hide();
                $('#filterCategoryVal').val(JSON.stringify(response.mapped_categories_val));
                $('#filterCategory').val(JSON.stringify(response.category_ids));
            }).fail(function(response) {
                $("#loading-image-preview").hide();
            });
        }
    });

    $(document).on('click','.select-cat-a',function(e){
        e.preventDefault();  
        let selected_cat = $(this);

        var selected_cats = [];
        var travesred_val = [];
        travesred_val.push($(this).data("id"));
        selected_cats.push(selected_cat.html());
        selected_cat.parents(".dropdown-submenu").each(function(){
            let parent_cat = $(this).find("a").first();
            if(!(travesred_val.includes(parent_cat.data("id")))) {
                selected_cats.push(parent_cat.html())
                travesred_val.push(parent_cat.data("id"));
            }
        })

        let selected_cat_arr = selected_cats.reverse();
        $("#selected_map").html(selected_cat_arr.join(" > "));
        $("#mapped-categories-val").val(JSON.stringify(selected_cat_arr));
        $("#mapped-categories").val(JSON.stringify(travesred_val.reverse()));
    });

    $(document).on('click','.map-category-action',function(e) {
        e.preventDefault();  
        $("#unmapped-id").val($(this).data('id'));
        $("#unmapped-category").val($(this).data('cat'));

        $('#isCheckedRows').val('');
        $('#checked').val('')
        if ($(this).data('type') === 'checked-rows') {
            $('#isCheckedRows').val('checked');
            $('#checkedRows').val(checkedRows);
        }

        $("#selected_map").html('');
        if ($(this).data('mapped')) {
            var option = '<option value="'+$(this).data('id')+'" selected="selected">'+$(this).data('mapped')+'</option>';
            $(".category-cls").append(option);
            $('#mapped-categories').val(JSON.stringify($(this).data('mapped_categories')));
        } else {
            $(".category-cls").val('').trigger('change');
        }
        if($(this).data('mapped')) $("#selected_map").html($(this).data('mapped'));
        $("#map-category-modal").modal("show");

    });

    $(document).on('click','#submit-mapping-form',function(){
        let form_url = $("#cat-mapping-form").attr("action");
        let update_id = $("#unmapped-id").val();
        let ajax_url = form_url.replace(":id",update_id);
        if($(".category-cls").val()) {
            $("#map-category-modal").modal("hide");
            $("#loading-image-preview").show();

            let data = $("#cat-mapping-form").serialize();
            if ($('#isCheckedRows').val() === 'checked'){
                ajax_url = "{{ route('scrapper-category-map.mutliple.update') }}";
                if(!$('#checkedRows').val()) { 
                    $("#loading-image-preview").hide();
                    toastr['error']('Please Select at least one Row');
                    return false;
                }
            }

            $.ajax({
                    type: 'POST',
                    url: ajax_url,
                    data,
                }).done(response => {
                    $("#loading-image-preview").hide();
                    toastr['success']('Mapping Saved Successfully');
                    fetchData(currentPageIndex)
                    
                }).fail(function(response) {
                    $("#loading-image-preview").hide();
                    toastr['error']('Error Ocuured! Please try again');
                });

        } else {
            toastr['error']('Please select Category');
        }
    });
    
    $(document).on('click','.select-cat-a-filter',function(e){
        e.preventDefault();  
        let selected_cat = $(this);

        var selected_cats = [];
        var travesred_val = [];
        travesred_val.push($(this).data("id"));
        selected_cats.push(selected_cat.html());
        selected_cat.parents(".dropdown-submenu").each(function(){
            let parent_cat = $(this).find("a").first();
            if(!(travesred_val.includes(parent_cat.data("id")))) {
                selected_cats.push(parent_cat.html())
                travesred_val.push(parent_cat.data("id"));
            }
        })

        let selected_cat_arr = selected_cats.reverse();
        $('#selectCategoryFilterText').html(selected_cat_arr.join(" > "));
        $("#filterCategoryVal").val(JSON.stringify(selected_cat_arr));
        $("#filterCategory").val(JSON.stringify(travesred_val.reverse()));
        console.log(selected_cat_arr.join(" > "));
    });

    $(document).on('click', '.pagination a', function(event) {
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        fetchData(page);
    });

    $(document).on('click', '.btn-search-action', function(event) {
        event.preventDefault();
        fetchData(1);
    })
    
    $(document).on('click', '#resetFilterCategory', function(event) {
        event.preventDefault(); 
        // $('#selectCategoryFilterText').html('Select Category');
        $(".filter-category-cls").val('').trigger('change');
        $("#filterCategoryVal").val('');
        $("#filterCategory").val('');
    });

    $(document).on('click', '#checkAllRow', function(event) {
        var checked = $(this).prop('checked');
        if (checked) {
            $('.check-row').prop('checked', true);
        } else {
            $('.check-row').prop('checked', false);
        }

        $('.check-row').trigger('change');
    });

    $(document).on('change', '.check-row', function(event) {
        var checked = $(this).prop('checked');
        var id = $(this).data('id');
        if (checked) {
            checkedRows.push(id);
        } else {
            var index = checkedRows.indexOf(id);
            checkedRows.splice(index, 1);
        }
    });

    $(document).on('click', '.map-category-checked-rows', function(event) {
        event.preventDefault();
        $('#updateMappedCategoryModal').modal('show');
    });

    $(document).on('click', '#updateMappedCategoryModalSubmit', function(event) {
        event.preventDefault();
        let form_url = $("#cat-mapping-form").attr("action");
        console.log(form_url);
    });

    function fetchData(page) {
        currentPageIndex = page;
        $("#loading-image").show();

        let options = {
            url: "{{ route('scrapper-category-map.index') }}" + "?page=" + page,
            data: $('#nodeCategoryMapForm').serialize(),
            success: function(data) {
                $("#loading-image").hide();
                $('#tableDataWrapper').html(data);
            },
            error: function(error) {
                $("#loading-image").hide();
                toastr.error("Error while fetching data");
            }
        };

        $.ajax(options);
    }

    function clearChecked(){
        checkedRows = [];
    }
</script>
@endsection