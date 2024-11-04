<div class="row">
    <div class="col-md-12">
        <div class="row row_next_action">
            <div class="col-6 form-inline pr-0 m-0">
                <input type="text" name="add_next_action" placeholder="Add New Next Action" class="form-control add_next_action_txt w-75">
                <button class="btn btn-xs text-gray add_next_action w-25 m-0"><i class="fa fa-plus"></i></button>
            </div>
            <div class="col-6 form-inline orm-inline pl-0 m-0">
                <select name="next_action" class="form-control next_action w-75" data-id="{{$customer->id}}">
                    <option value="">Select Next Action</option> 
                    @foreach ($nextActionArr as $value => $option) {
                        <option value="{{$value}}" {{$value == $customer->customer_next_action_id ? 'selected' : ''}}>{{$option}}</option>
                    }@endforeach
                </select>
                <a class="btn btn-xs delete_next_action w-25 m-0"><i class="fa fa-trash" style="color:gray"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-12 expand-row dis-none">
    </div>
</div>