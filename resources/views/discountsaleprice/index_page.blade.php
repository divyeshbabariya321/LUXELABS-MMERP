
                @foreach ($discountsaleprice as $d)
                   <tr>
                       <td class="small">{{ $d->id }}</td>
                       <td>{{ $d->type }} </td>
                       <td>
                         @php
                              if ($d->type == 'brand') {
                                echo $d->brand_name;
                            } elseif ($d->type == 'product') {
                                echo $d->product_name;
                            } elseif ($d->type == 'category') {
                                echo $d->category_title;
                            } elseif ($d->type == 'store_website') {
                                echo $d->store_website_title;
                            } 
                         @endphp
                       </td>
                       <td>{{ $d->supplier }}</td>
                       <td>{{ date('d-m-Y',strtotime($d->start_date)) }}</td>
                       <td>{{date('d-m-Y',strtotime($d->end_date)) }}</td>
                       <td>{{ $d->amount }}</td>
                       <td>{{ $d->amount_type }}</td>
                       <td>
                         @php
                       //  $d->start_date=date('d-m-Y',strtotime($d->start_date));
                       //  $d->end_date=date('d-m-Y',strtotime($d->end_date));

                         @endphp
                       <button type="button" class="btn btn-image edit-form d-inline"  data-toggle="modal" data-target="#cashCreateModal" data-edit="{{ json_encode($d) }}"><img src="/images/edit.png" /></button>  
                       {{ html()->form('DELETE', url('discount-sale-price', [$d->id]))->style('display:inline')->open() }}
                           <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                           {{ html()->form()->close() }}

                       </td>
                       
                   </tr>
               @endforeach