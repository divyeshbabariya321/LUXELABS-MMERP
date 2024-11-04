   @if ($productPushSummeries->count())

       @foreach ($productPushSummeries as $summery)
           <tr>

               <td>{{ $summery->storeWebsite ? $summery->storeWebsite->title : '' }}</td>
               <td>{{ $summery->brand ? $summery->brand->name : '' }}</td>
               <td>{{ $summery->category ? $summery->category->title : '' }}</td>
               <td>{{ $summery->product_push_count }}</td>
               <td>{{ $summery->created_at->format('Y-m-d') }}</td>
           </tr>

       @endforeach

   @else


       <tr>
           <td class="text-center">No data found</td>
       </tr>

   @endif
