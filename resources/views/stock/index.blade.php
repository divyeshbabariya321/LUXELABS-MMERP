@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Inward Stock</h2>
            <div class="pull-left">                
            </div>
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('stock.create') }}">+</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
      <div class="alert alert-success">
        <p>{{ $message }}</p>
      </div>
    @endif

    <div class="table-responsive mt-3">
        <table class="table table-bordered">
        <tr>
          <th><a href="/stock{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=courier{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Courier</a></th>
          <th><a href="/stock{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=package_from{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">From</a></th>
          <th><a href="/stock{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=awb{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">AWB</a></th>
          <th><a href="/stock{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=pcs{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Pcs</a></th>
          <th>Product Count</th>
          <th><a href="/stock{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=created_at{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Created at</a></th>
          <th width="280px">Action</th>
        </tr>
        @foreach ($stocks as $key => $stock)
            <tr>
                <td>{{ $stock->courier }}</td>
                <td>{{ $stock->package_from }}</td>
                <td>{{ $stock->awb }}</td>
                <td>{{ $stock->pcs }}</td>
                <td>{{ $stock->products()->count() }}</td>
                <td>{{ Carbon\Carbon::parse($stock->created_at)->format('d-m-Y') }}</td>
                <td>
                  <a class="btn btn-image" href="{{ route('stock.show', $stock->id) }}"><img src="/images/view.png" /></a>

                  {{ html()->form('DELETE', route('stock.destroy', [$stock->id]))->style('display:inline')->open() }}
                  <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
                  {{ html()->form()->close() }}

                  {{ html()->form('DELETE', route('stock.permanentDelete', [$stock->id]))->style('display:inline')->open() }}
                  <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                  {{ html()->form()->close() }}
                </td>
            </tr>
        @endforeach
    </table>
    </div>

    {!! $stocks->appends(Request::except('page'))->links() !!}
@endsection
