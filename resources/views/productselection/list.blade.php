@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Selections</h2>
            </div>
        </div>
    </div>


    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Sku</th>
            <th>Image</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($productselection as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->sku }}</td>
                <td><img src="{{ getMediaUrl($product->getMedia($media_tags)->first())  }}" class="img-responsive" style="max-width: 200px;" alt=""></td>
                <td>
                    <form action="{{ route('productselection.destroy',$product->id) }}" method="POST">                        
                         @if(auth()->user()->checkPermission('productselection-edit'))
                            <a class="btn btn-image" href="{{ route('productselection.edit',$product->id) }}"><img src="/images/edit.png" /></a>
                        @endif

                        @csrf
                        @method('DELETE')
                        @if(auth()->user()->checkPermission('productselection-delete'))                            
                        @endif
                    </form>
                </td>
            </tr>
        @endforeach
    </table>


    {!! $productselection->links() !!}


@endsection
