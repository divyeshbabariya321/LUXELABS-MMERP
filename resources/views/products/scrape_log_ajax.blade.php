
              @foreach($logs as $item)
          <tr>
          <td>
                    @if(isset($item->created_at))
                      {{ date('M d, Y',strtotime($item->created_at))}}
                    @endif
                  </td>        
          <td> {{$item->website}} </td>
                  <td> {{$item->total_product}} </td>
                  <td> {{$item->missing_category}} </td>
                  <td> {{$item->missing_color}} </td>
                                
                  <td> {{$item->missing_composition}} </td>
                  <td> {{$item->missing_name}} </td>
                  <td> {{$item->missing_short_description}} </td>
                  <td> {{$item->missing_price}} </td>
                  <td> {{$item->missing_size}} </td>
                 
                  
                  
                </tr>
              @endforeach()
        