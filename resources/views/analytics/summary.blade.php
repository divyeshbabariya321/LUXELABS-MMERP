@extends('layouts.app')
@section('title', 'Analytics Data')
@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Analytics Data</h2>
    </div>
    <div class="col-lg-12 margin-tb">
        <div class="col-md-4 col-lg-4 col-xl-4">
            <form action="{{route('filteredAnalyticsSummary')}}" method="get" class="form-inline">
                <div class="form-group">
                    <label for="brand">Search By Brands</label>
                    {{ html()->select('brand', $brands, request()->brand)->class('form-control') }}
                </div>
                <div class="form-group mt-4">
                    <button class="btn btn-default">Submit</button>
                </div>
            </form>
        </div>
        <div class="col-md-4 col-lg-4 col-xl-4">
            <form action="{{route('filteredAnalyticsSummary')}}" method="get" class="form-inline">
                <div class="form-group">
                    <label for="gender">Search By Gender</label>
                    {{ html()->select('gender', $genders, request()->gender)->class('form-control') }}
                </div>
                <div class="form-group mt-4">
                    <button class="btn btn-default">Submit</button>
                </div>
            </form>
        </div>
        <div class="col-md-4 col-lg-3 col-xl-3 p-0">
            <form action="{{route('filteredAnalyticsSummary')}}" method="get" class="form-inline">
                <div class="form-group">
                    <label for="location">Search By Country</label>
                    <input name="location" type="text" placeholder="Country" class="form-control"
                        value="{{!empty(request()->location) ? request()->location : ''}}">
                </div>
                <div class="form-group mt-4">
                    <button class="btn btn-default">Submit</button>
                </div>
            </form>
        </div>
    </div>

</div>
<div class="row">
    <div class="container-fluid">
        @php
            $new_data = App\Helpers::customPaginator(request(), $data, 100);
            @endphp
        <div class="col-md-12 text-center">
            {!! $new_data->links() !!}
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th scope="col" class="text-center">Brand</th>
                        <th scope="col" class="text-center">Category</th>
                        <th scope="col" class="text-center">TIme Spent</th>
                        <th scope="col" class="text-center">Location</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($new_data as $key => $item)
                    @php
                    // dd($item['page_path']);
                        $path_array = explode('/', $item['page_path']);
                        $brand_name = "";
                        $gender = "";
                        $cat = "";
                        if (!empty(request()->brand) || !empty(request()->gender)) {
                            $brand_name = $item['brand_name'];   
                        }
                        if (in_array('brands', $path_array)) {
                            $brand_name = str_replace('.html', '', $path_array[2]);
                        }
                        if (in_array('brands', $path_array) || in_array('mens', $path_array) || in_array('women', $path_array)) {
                            $gender = $path_array[1];
                            $cat = str_replace('.html', '', $path_array[2]);
                        }
                    @endphp
                    <tr>
                        <td>{{$brand_name}}</td>
                        <td>{{$gender}} > {{$cat}}</td>
                        <td>{{$item['time']}}mins</td>
                        <td>{{$item['city']}},{{$item['country']}}</td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
            <div class="col-md-12 text-center">
                {!! $new_data->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection