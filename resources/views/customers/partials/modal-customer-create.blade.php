<div id="customerCreateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Customer</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('customer.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Website:</strong>
                        <Select name="store_website_id" class="form-control">
                            @foreach ($storeWebsites as $number => $name)
                                <option value="{{ $number }}" {{ old('store_website_id') == $number ?  'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </Select>
                        @if ($errors->has('store_website_id'))
                            <div class="alert alert-danger">{{$errors->first('store_website_id')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>Client Name:</strong>
                        <input type="text" class="form-control" name="name" placeholder="Client Name" value="{{old('name')}}" required/>
                        @if ($errors->has('name'))
                            <div class="alert alert-danger">{{$errors->first('name')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>Email:</strong>
                        <input type="email" class="form-control" name="email" placeholder="example@example.com" value="{{old('email')}}"/>
                        @if ($errors->has('email'))
                            <div class="alert alert-danger">{{$errors->first('email')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>Phone:</strong>
                        <input type="number" class="form-control" name="phone" placeholder="900000000" value="{{old('phone')}}"/>
                        @if ($errors->has('phone'))
                            <div class="alert alert-danger">{{$errors->first('phone')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>Solo phone:</strong>
                        <Select name="whatsapp_number" class="form-control">
                            <option value>None</option>
                            @foreach ($solo_numbers as $number => $name)
                                <option value="{{ $number }}" {{ old('whatsapp_number') == $number ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </Select>
                        @if ($errors->has('whatsapp_number'))
                            <div class="alert alert-danger">{{$errors->first('whatsapp_number')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>Instagram Handle:</strong>
                        <input type="text" class="form-control" name="instahandler" placeholder="instahandle" value="{{old('instahandler')}}"/>
                        @if ($errors->has('instahandler'))
                            <div class="alert alert-danger">{{$errors->first('instahandler')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>Rating:</strong>
                        <Select name="rating" class="form-control">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                        </Select>
                    </div>
                    <div class="form-group">
                        <strong>Address:</strong>
                        <input type="text" class="form-control" name="address" placeholder="Street, Apartment" value="{{old('address')}}"/>
                        @if ($errors->has('address'))
                            <div class="alert alert-danger">{{$errors->first('address')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>City:</strong>
                        <input type="text" class="form-control" name="city" placeholder="Mumbai" value="{{old('city')}}"/>
                        @if ($errors->has('city'))
                            <div class="alert alert-danger">{{$errors->first('city')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>Country:</strong>
                        <input type="text" class="form-control" name="country" placeholder="India" value="{{old('country')}}"/>
                        @if ($errors->has('country'))
                            <div class="alert alert-danger">{{$errors->first('country')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>Pincode:</strong>
                        <input type="number" class="form-control" name="pincode" max="999999" placeholder="411060" value="{{ old('pincode') }}"/>
                        @if ($errors->has('pincode'))
                            <div class="alert alert-danger">{{$errors->first('pincode')}}</div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-secondary" id="submitButton">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
