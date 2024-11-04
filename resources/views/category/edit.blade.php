
@extends('layouts.app')

@section('content')

    <link rel="stylesheet" href="{{ mix('webpack-dist/css/treeview.css') }} ">
    <div class="panel panel-primary">
        
        <div class="panel-body">
            <div class="row">
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h3>Edit Category</h3>

                    {{ html()->form('POST', route('update.category', [$id]))->open() }}

                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif

                    <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                        {{ html()->label('New Title:') }}
                        {{ html()->text('title', old('title') ? old('title') : $title)->class('form-control')->placeholder('Enter New Title') }}
                        <span class="text-danger">{{ $errors->first('title') }}</span>
                    </div>

                    <div class="form-group {{ $errors->has('magento_id') ? 'has-error' : '' }}">
                        {{ html()->label('Magento Id:') }}
                        {{ html()->text('magento_id', old('magento_id') ? old('magento_id') : $magento_id)->class('form-control')->placeholder('Enter Magento Id') }}
                        <span class="text-danger">{{ $errors->first('magento_id') }}</span>
                    </div>

                    <div class="form-group {{ $errors->has('show_all_id') ? 'has-error' : '' }}">
                        {{ html()->label('Show all Id:') }}
                        {{ html()->text('show_all_id', old('show_all_id') ? old('show_all_id') : $show_all_id)->class('form-control')->placeholder('Enter Show All Id') }}
                        <span class="text-danger">{{ $errors->first('show_all_id') }}</span>
                    </div>

                    <div class="form-group">
                        {{ html()->label('Category Segment:') }}
                        {{ html()->select('category_segment_id', $category_segments, old('category_segment_id') ? old('category_segment_id') : $category_segment_id)->class('form-control')->placeholder('Select Category Segment') }}
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-secondary">Edit</button>
                    </div>

                    {{ html()->form()->close() }}
                </div>
            </div>

        </div>
    </div>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/treeview.js') }} "></script>
@endsection
