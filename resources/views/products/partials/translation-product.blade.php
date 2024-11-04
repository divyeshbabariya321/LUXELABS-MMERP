<table class="table table-bordered table-hover">
    <thead>
        <tr>
          <th>Product ID</th>
          <th>Locale</th>
          <th>Title</th>
          <th>Description</th>
          <th>Composition</th>
          <th>Color</th>
          <th>Size</th>
          <th>Country of manufacture</th>
          <th>Dimension</th>
        </tr>
    </thead>
    <tbody class="error-log-data">
        @foreach($translation as $t)
            <tr>
                <td>{{$t->product_id}}</td>
                <td>{{$t->locale}}</td>
                <td>{{$t->title}}</td>
                <td>{{$t->description}}</td>
                <td>{{$t->composition}}</td>
                <td>{{$t->color}}</td>
                <td>{{$t->size}}</td>
                <td>{{$t->country_of_manufacture}}</td>
                <td>{{$t->dimension}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
