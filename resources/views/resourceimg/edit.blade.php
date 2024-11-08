@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default">
              <div class="panel-heading">Resources</div>
              <div class="panel-body">
                <h2>Edit Resource Center</h2><hr>
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
                {{ html()->form('POST', route('add.resource'))->acceptsFiles()->open() }}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('cat_id') ? 'has-error' : '' }}">
                                {{ html()->label('Category:') }}
                                <?=@$allCategoriesDropdown;?>
                                <span class="text-danger">{{ $errors->first('cat_id') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('image') ? 'has-error' : '' }}">
                                {{ html()->label('Image:') }}
                                {{ html()->file('image', ['class' => 'form-control'])->attributes(old('image')) }}
                                <span class="text-danger">{{ $errors->first('image') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-secondary">Update</button>
                    </div>
                {{ html()->form()->close() }}
              </div>
            </div>
          </div>
        </div>
    </div>
@endsection