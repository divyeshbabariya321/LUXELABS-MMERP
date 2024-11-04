<div class="row">
    <div class="col-6 d-inline form-inline">
        <input style="width: 87%" type="text" name="category_name" placeholder="Enter New Category" class="form-control mb-3 quick_category">
        <button class="btn custom-button quick_category_add" style="position: absolute; padding: 5px;"><i class="fa fa-plus" aria-hidden="true"></i></button>
    </div>
    <div class="col-6 d-inline form-inline" style="padding-left: 0px;">
        <div style="float: left; width: 86%">
            <select name="quickCategory" class="form-control mb-3 quickCategory">
                <option value="">Select Category</option>

                @php
                $reply_categories = \App\ReplyCategory::select('id', 'name')->with('approval_leads', 'sub_categories')->where('parent_id', 0)->where('id', 44)->orderby('name', 'ASC')->get();
                @endphp

                @foreach($reply_categories as $category)
                    <option value="{{ $category->approval_leads }}" data-id="{{$category->id}}" data-value="{{ $category->sub_categories }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="float: right; width: 14%;">
            <a class="btn custom-button delete_category" style="padding: 5px;"><i class="fa fa-trash" aria-hidden="true"></i></a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-6 d-inline form-inline">
        <input style="width: 87%" type="text" name="quick_comment" placeholder="Enter New Quick Comment" class="form-control mb-3 quick_comment">
        <button class="btn custom-button quick_comment_add" style="position: absolute; padding: 5px;"><i class="fa fa-plus" aria-hidden="true"></i></button>
    </div>
    <div class="col-6 d-inline form-inline" style="padding-left: 0px;">
        <div style="float: left; width: 86%">
            <select name="quickSubCategory" class="form-control quickSubCategory" data-id="{{$value->id}}">
                <option value="">Select Sub Category</option>
            </select>
        </div>
        <div style="float: right; width: 14%;">
            <a class="btn custom-button delete_sub_category" style="padding: 5px;"><i class="fa fa-trash" aria-hidden="true"></i></a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-6 d-inline form-inline p-0" style="padding-left: 0px;">
    </div>
    <div class="col-6 d-inline form-inline" style="padding-left: 0px;">
        <div style="float: left; width: 86%">
            <select name="quickComment" class="form-control quickCommentEmail" data-id="{{$value->id}}">
                <option value="">Quick Reply</option>
            </select>
        </div>
        <div style="float: right; width: 14%;">
            <a class="btn custom-button delete_quick_comment" style="padding: 5px;"><i class="fa fa-trash" aria-hidden="true"></i></a>
        </div>
    </div>
</div>