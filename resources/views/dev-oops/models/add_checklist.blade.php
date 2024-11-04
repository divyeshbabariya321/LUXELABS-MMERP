<div id="CreateCheckList" class="modal fade " role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="task_form" class="form mb-15" >
            @csrf
            <input type="hidden" value="1" name="category_type" id="category_type">
            <div class="modal-header">
                <h4 class="modal-title">Add {{ $title }}</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row ml-2 mr-2">
                    <div class="col-xs-12 col-sm-12">
                        <div class="form-group">
                            <strong >Category :</strong>
                            {{ html()->text('category_name')->id('category_name')->placeholder('Enter Category')->class('form-control')->required()->attribute('autocomplete', 'off') }}
                            @if ($errors->has('category_name'))
                                <span style="color:red">{{ $errors->first('category_name') }}</span>
                            @endif
                        </div>
                    </div>
                </div>                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-secondary">Add</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div id="EditCheckList" class="modal fade " role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="edit_task_form" method="POST" class="form mb-15" >
            @csrf
            @method('PUT')
                <input type="hidden" name="category_type" id="category_type" value="1">
                <div class="modal-header">
                    <h4 class="modal-title">Edit {{ $title }}</h4>
                    <button type="button" class="close" id="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="checklist_id">
                    <div class="row ml-2 mr-2">
                        <div class="col-xs-12 col-sm-12">
                            <div class="form-group">
                                <strong >Category Name:</strong>
                                {{ html()->text('category_name')->id('category_name')->placeholder('Enter Category')->class('form-control')->required()->attribute('autocomplete', 'off') }}
                                @if ($errors->has('category_name'))
                                    <span style="color:red">{{ $errors->first('category_name') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default close_modal" id = "close_modal" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="CreateSubCheckList" class="modal fade " role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="sub_task_form" class="form mb-15" >
            @csrf
            <input type="hidden" value="2" name="category_type" id="category_type">
            <div class="modal-header">
                <h4 class="modal-title">Add Sub Category</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row ml-2 mr-2">
                    <div class="col-xs-12 col-sm-12">
                        <div class="form-group">
                            <strong >Category :</strong>
                            {{ html()->select("devoops_category_id", $devoops_categories, request('devoops_category_id'))->class("form-control select2")->required()->id('devoops_category_id') }}
                            @if ($errors->has('devoops_category_id'))
                                <span style="color:red">{{ $errors->first('devoops_category_id') }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-xs-12 col-sm-12">
                        <div class="form-group" id = "hidetask">
                            <strong>Sub Category :</strong>
                            {{ html()->text('sub_category_name')->id('sub_category_name')->placeholder('Enter Sub Category')->class('form-control')->required()->attribute('autocomplete', 'off') }}
                            @if ($errors->has('sub_category_name'))
                                <span style="color:red">{{ $errors->first('sub_category_name') }}</span>
                            @endif
                        </div>
                    </div>
                </div>                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-secondary">Add</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div id="EditSubCheckList" class="modal fade " role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="sub_edit_task_form" method="POST" class="form mb-15" >
            @csrf
            @method('PUT')
                <input type="hidden" value="2" name="category_type" id="category_type">
                <div class="modal-header">
                    <h4 class="modal-title">Edit {{ $title }}</h4>
                    <button type="button" class="close" id="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="checklist_id">
                    <div class="row ml-2 mr-2">
                        <div class="col-xs-12 col-sm-12">
                            <div class="form-group">
                                <strong >Category Name:</strong>
                                {{ html()->select("devoops_category_id", $devoops_categories, request('devoops_category_id'))->class("form-control select2")->required()->id('devoops_category_id') }}
                                @if ($errors->has('devoops_category_id'))
                                    <span style="color:red">{{ $errors->first('devoops_category_id') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12">
                            <div class="form-group">
                                <strong>Sub Category Name:</strong>
                                {{ html()->text('sub_category_name')->id('sub_category_name')->placeholder('Enter Sub Category')->class('form-control')->required()->attribute('autocomplete', 'off') }}
                                @if ($errors->has('sub_category_name'))
                                    <span style="color:red">{{ $errors->first('sub_category_name') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default close_modal" id = "close_modal" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>