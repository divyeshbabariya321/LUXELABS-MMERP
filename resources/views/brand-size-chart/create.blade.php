@extends('layouts.app')
@section('link-css')
<style>


/*PRELOADING------------ */
#overlayer {
  width:100%;
  height:100%;  
  position:absolute;
  z-index:1;
  background:#4a4a4a33;
}
.loader {
  display: inline-block;
  width: 30px;
  height: 30px;
  position: absolute;
  z-index:3;
  border: 4px solid #Fff;
  top: 50%;
  animation: loader 2s infinite ease;
  margin-left : 50%;
}

.loader-inner {
  vertical-align: top;
  display: inline-block;
  width: 100%;
  background-color: #fff;
  animation: loader-inner 2s infinite ease-in;
}

@keyframes loader {
  0% {
    transform: rotate(0deg);
  }
  
  25% {
    transform: rotate(180deg);
  }
  
  50% {
    transform: rotate(180deg);
  }
  
  75% {
    transform: rotate(360deg);
  }
  
  100% {
    transform: rotate(360deg);
  }
}

@keyframes loader-inner {
  0% {
    height: 0%;
  }
  
  25% {
    height: 0%;
  }
  
  50% {
    height: 100%;
  }
  
  75% {
    height: 100%;
  }
  
  100% {
    height: 0%;
  }
}
</style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12 ">
            
            <h2 class="page-heading">Create Brand Size Chart</h2>
           
        </div>
    </div>
    @include('partials.flash_messages')
    <div class="row">
        <div class="col-md-6">
            <form action="{{ route('brand/store/size/chart')  }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="d-flex" style="justify-content: space-between;">
                    <div class="col-xs-12 col-sm-12 col-md-3">
                        <div class="form-group pl-5" style="width: 200px;">
                            <select class="form-contorl select2" name="category" onchange="getChild(this.value)">
                                @if($category)
                                    <option value="">Select Category</option>
                                    @foreach($category as $key => $val)    
                                        <option value="{{ $key }}">{{ $val }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-3">
                        <div class="form-group">
                            <select name="brand_id" class="form-control select2" id="brand_id"placeholder="Brand"  style="width: 200px;">
                                <option value="">Select Brand</option>
                                @forelse ($brands as $key => $item)
                                    <option value="{{ $key }}">{{ $item }}</option>
                                @empty
                                @endforelse
                            </select>
                            @if ($errors->has('brand_id'))
                                <div class="alert alert-danger">{{$errors->first('brand_id')}}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-3">
                        <div class="form-group">
                            <select name="store_website_id"  style="width: 200px;" placeholder="Store website" class="form-control select2" id="brand_id" required>
                                <option value="">Select Website</option>
                                @forelse ($storeWebsite as $key => $item)
                                    <option value="{{ $key }}">{{ $item }}</option>
                                @empty
                                @endforelse
                            </select>
                            @if ($errors->has('store_website_id'))
                                <div class="alert alert-danger">{{$errors->first('store_website_id')}}</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-3">
                        <div class="form-group">
                            <input type="file" placeholder="Upload Size Chart" class="form-control" name="size_img"  style="width: 300px;" required/>
                            @if ($errors->has('size_img'))
                                <div class="alert alert-danger">{{$errors->first('size_img')}}</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-3 text-center">
                        <a href="{{ route('brand/size/chart') }}" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-secondary">Save</button>
                    </div>
                </div>
                </div>
                <div class="row">
                    <div class="col-md-12" id="category_table">

                    </div>
                    @if ($errors->has('category_id'))
                        <div class="alert alert-danger">{{$errors->first('category_id')}}</div>
                    @endif
                </div>
            </form>
        </div>
    </div>
    <img class="infinite-scroll-products-loader center-block" src="{{asset('/images/loading.gif')}}" alt="Loading..." style="display: none" />
@endsection

@section('scripts')
<script type="text/javascript">
$(".select2").select2();

function getChild(val){
    $("#category_table").html('');
    var loader = $('.infinite-scroll-products-loader');
    if(val != ''){
        var token = "{{ csrf_token() }}";
        $.ajax({
            url : '{{ route("brand.getChild") }}',
            method : 'POST',
            dataType : 'JSON',
            data : { 
                _token: token,
                category_id : val
            },
            beforeSend: function() {
                loader.show();
            },
            success: function (data){
                if(data.data != ''){
                    loader.hide();
                    var tableBody = '<table class="table table-striped table-bordered"><thead><tr><td class="text-center">Action</td><td class="text-center">Category Title</td></tr><thead><tbody>';
                    tableBody = tableBody + data.data;
                    tableBody = tableBody + '</tbody><table>';
                    $("#category_table").html(tableBody);                    
                }
            },
            error: function () {
                loader.hide();
            }
        });   
    }
}

</script>
@endsection