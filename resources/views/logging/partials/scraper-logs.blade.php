@foreach ($scraperLogs as $log)
    <tr>
        <td>{{ $log->id }}</td>
        <td>{{ $log->ip_address }}</td>
        <td>{{ $log->website }}</td>
        <td class="cotton-td"><a href="{{ $log->url }}" target="__blank">{{ $log->url }}</a></td>
        <td>{{ $log->sku }}</td>
        <td>{{ $log->original_sku }}</td>
        
        <td style="display:flex;justify-content:space-between;align-items: center;">
            {{ $log->created_at }} 
        </td>
        <td>
            <button type="button" onclick="showScrappedProduct()" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                View
            </button>
        </td>
    </tr>
@endforeach


<script>
  function showScrappedProduct(){

  }
</script>