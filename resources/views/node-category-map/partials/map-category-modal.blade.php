<div id="map-category-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Map Category to <span id="unmapped-category"></span></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-sm-6 mt-4">
                    <form id="cat-mapping-form" action="{{ route('scrapper-category-map.update', ':id') }}"
                        method="POST">
                        @method('PUT')
                        @csrf
                        <input type="hidden" id="unmapped-id" name="id">
                        <input type="hidden" id="mapped-categories-val" name="mapped_categories_val">
                        <input type="hidden" id="mapped-categories" name="mapped_categories">
                        <input type="hidden" id="isCheckedRows" name="is_checked_rows">
                        <input type="hidden" id="checkedRows" name="checked">
                        <input type="hidden" name="action" value="assign_category">
                        <div class="form-group">
                            {{ html()->select("category_id", [], null)->class("form-control select2 category-cls")->data('placeholder', "Select Category ")->style('width:50%') }}
                        </div>
                        {{-- <div>
                        {{ html()->select("category_id", $categories_list, '')->class("form-control globalSelect2")->data('placeholder', "Select") }}
                        </div> --}}
                        <div class="form-group">
                            <div id="mappedInfo">
                                <b>Selected Category : </b><span id="selected_map"></span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-sm-6 mt-4">
                    <b>Category List Hint: </b></span>
                    <div class="border rounded p-3" style="height:200px;overflow-y:scroll;"> <!-- inline-css -->
                        {!! generateTreeView($categories_list) !!}
                            {{-- @foreach ($categories_list as $key => $value)
                            {{ $value }} <br/>
                        @endforeach --}}
                       </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit-mapping-form">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
