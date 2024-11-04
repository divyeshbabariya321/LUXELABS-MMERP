@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb ">
      <h2 class="page-heading">Products Push List</h2>
      <div class="pull-left">
        <form action="{{url('products/pushproductlist')}}" method="GET">
          <div class="form-group mb-3">
            <div class="row">
              <div class="col-md-3 pr-0">
                 
                <select name="website" class="form-control">
                    <option value="">Select Website</option>
                    @foreach($websiteList as $w)
                    @php
                      $sel='';
                      if (isset($_GET['website']) && $_GET['website']==$w->id)
                        $sel="selected='selected'";
                    @endphp
                       <option {{$sel}} value="{{$w->id}}">{{$w->title}}</option>
                    @endforeach
                </select>
                              
               </div>
               <div class="col-md-3 pr-0">
                <select name="category" class="form-control">
                    <option value="">Select Category</option>
                    @foreach($categoryList as $c)
                    @php
                      $sel='';
                      if (isset($_GET['category']) && $_GET['category']==$c->id)
                        $sel="selected='selected'";
                    @endphp
                       <option {{$sel}} value="{{$c->id}}">{{$c->title}}</option>
                    @endforeach
                </select>
                              
               </div>
               <div class="col-md-3 pr-0">
                <select name="brand" class="form-control">
                    <option value="">Select Brand</option>
                    @foreach($brandList as $b)
                    @php
                      $sel='';
                      if (isset($_GET['brand']) && $_GET['brand']==$b->id)
                        $sel="selected='selected'";
                    @endphp
                       <option {{$sel}} value="{{$b->id}}">{{$b->name}}</option>
                    @endforeach
                </select>
                              
               </div>
              <div class="col-md-3  pt-2">
                <button type="submit" class="btn btn-image"><img src="/images/search.png" /></button>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="pull-right">
       
      </div>
    </div>
</div>


@if ($message = Session::get('success'))
<div class="alert alert-success">
  <p>{{ $message }}</p>
</div>
@endif
<div class="pl-3 pr-3 pt-0">
  <div class="table-responsive">
    <table class="table table-bordered" style="table-layout: fixed;">
      <tr>
        <th width="3%">Product Id</th>
        <th width="7%">Product Name</th>
        <th width="6%">Category</th>
        <th width="7%">Brand</th>
        <th width="10%">Store Website </th>
        <th width="7%">Magento Url</th>
        <th width="7%">Published On</th>
        
      </tr>
      @foreach ($products as $product)
      <tr>
        <td>{{ $product->product_id }}</td>
        <td class="Website-task" title="{{ $product->product_name }}">{{ $product->product_name }}</td>
        <td class="Website-task" title="{{ $product->category }}">{{ $product->category }}</td>
        <td class="Website-task" title="{{ $product->brand }}">{{ $product->brand }}</td>
        <td class="Website-task" title="{{ $product->store_website_name  }}">{{ $product->store_website_name  }}</td>
        <td class="Website-task" title="{{ $product->store_website_url }}">{{ $product->store_website_url }}</td>
        <td class="Website-task" title="{{ $product->created_at }}">{{ $product->created_at }}</td>
        
      </tr>
      @endforeach
    </table>
  </div>
</div>
{!! $products->appends(Request::except('page'))->links() !!}

<script type="text/javascript">
  

</script>

@endsection
