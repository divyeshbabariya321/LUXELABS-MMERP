@extends('layouts.app')
@section('content')
<style>
    iframe 
{
 display: block; 
 width: 100%; 
 border: none; 
 overflow-y: auto; 
 overflow-x: hidden;
}
.full-height {
  height: 100vh;
}
</style>
<div class="row" id="product-template-page">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Load Testing Result</h2>
        <div class="pull-right">
            <a href="{{route('load-testing.index')}}" class="btn btn-secondary create-product-template-btn"style="margin-right: 9px;">Back</a>
        </div>
    </div>
    <br>
    
</div>
<div id="display-area">
    <div class="col-md-12 full-height" id="page-view-result">
        @php
            $filename = pathinfo($getLoadTesting->jmx_file_path, PATHINFO_FILENAME).'.html/index.html';
        @endphp
        <iframe src="{{asset('storage/jmx').'/'.$filename}}" width='100%' height='100%' allowfullscreen frameborder='0'>Your browser isn't compatible</iframe>
    </div>
</div>
@endsection