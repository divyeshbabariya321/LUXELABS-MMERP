@foreach ($productsLog as $product)
    <tr>
        <td>{{ $product->in_stock ?? 'N/A' }}</td>
        <td>{{ $product->prev_in_stock ?? 'N/A' }}</td>
        <td>{{ $product->date }}</td>
    </tr>
@endforeach
