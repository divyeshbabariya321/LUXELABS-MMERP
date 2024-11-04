@forelse ($suppliers as $supplier) 
    <tr>
        <td>{{$supplier->supplier}}</td>
        <td>{{ \App\Http\Controllers\ProductInventoryController::getLastScrappedOn($supplier->id) }}</td>
        <td>{{$supplier->myproducts->count()}}</td>
        <td><a href="javascript:;" data-supplier-id="{{ $supplier->id }}" class="brand-result-page">
            {{count(array_unique($supplier->myproducts->pluck("brand_id")->all()))}}</a></td>
        @foreach($range as $date) 
            <td>{{$supplier->histories->where("date","=",$date->format("Y-m-d"))->sum('in_stock')}}</td>
        @endforeach 
        <td class="showSummary"><a target="_blank" href="{{route('supplier.product.summary',$supplier->id)}}">Details</td>
    </tr>
    @empty

@endforelse