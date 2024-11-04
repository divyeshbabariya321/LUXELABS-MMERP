<form id="add-group-form" method="POST" enctype="multipart/form-data" action="{{route('store.scrapper.apk')}}">
    @csrf
    <div class="modal-body">
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Upload APK File</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="application_name" name="application_name"
                       placeholder="Application Name" value="{{ old('application_name') }}">
                @if ($errors->has('application_name'))
                    <span class="text-danger">{{$errors->first('application_name')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">APK file</label>
            <div class="col-sm-10">
                <input type="file" accept="application/apk" name="service_file"/>
                @if ($errors->has('service_file'))
                    <span class="text-danger">{{$errors->first('service_file')}}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                aria-label="Close">Close
        </button>
        <button type="submit" class="float-right custom-button btn">Store</button>
    </div>
</form>
