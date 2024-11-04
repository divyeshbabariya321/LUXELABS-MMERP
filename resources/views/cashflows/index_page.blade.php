
               @foreach ($cash_flows as $cash_flow)
                   <tr>
                       <td class="small">{{ date('Y-m-d', strtotime($cash_flow->date)) }}</td>
                       <td>{!! $cash_flow->getLink() !!}</td>
                       <td>@switch($cash_flow->cash_flow_able_type)
                             @case('\App\Order')
                                  <a href="{{ @$cash_flow->cashFlowAble->storeWebsiteOrder->storeWebsite->website_url }}" target="_blank">{{$cash_flow->cashFlowAble->storeWebsiteOrder->storeWebsite->website ?? ''}}</a></p>        
                                  @break
                           @default
                             
                           @endswitch 

                       </td>
                       <td>{!! $cash_flow->get_bname()!!} </td>
                       <td>{{ class_basename($cash_flow->cashFlowAble) }}</td>
                       <td>
                           {{ $cash_flow->description }}
                           @if ($cash_flow->files)
                               <ul>
                                   @foreach ($cash_flow->files as $file)
                                       <li><a href="{{ route('cashflow.download', $file->id) }}" class="btn-link">{{ $file->filename }}</a></li>
                                   @endforeach
                               </ul>
                           @endif
                       </td>
                       <td>
                           @if(!is_numeric($cash_flow->currency))  {{$cash_flow->currency}}  @endif{{ $cash_flow->amount }}
                           @if($cash_flow->cash_flow_able_type =="App\HubstaffActivityByPaymentFrequency")
                           <button  type="button" class="btn btn-xs show-calculation"style="margin-top: -2px;" title="Show History" data-id="{{ $cash_flow->id }}"><i class="fa fa-info-circle"></i></button> 
                           @endif             
                       </td>
                       <td>{{ $cash_flow->amount_eur }}</td>
                       <td>{{$cash_flow->currency}} {{ $cash_flow->erp_amount }}</td>
                       <td>{{ $cash_flow->erp_eur_amount }}</td>
                       <td>
                        {{($cash_flow->monetaryAccount)?$cash_flow->monetaryAccount->name: "N/A"}}
                       </td>
                       <td>{{ ucwords($cash_flow->type) }}</td>
                       <td>{{ \Carbon\Carbon::parse($cash_flow->billing_due_date)->format('d-m-Y') }}</td>
                       <td>
                           <a title="Do Payment" data-id="{{ $cash_flow->id }}" data-mnt-amount="{{ $cash_flow->amount }}" data-mnt-account="{{ $cash_flow->monetary_account_id }}" class="do-payment-btn"><span><i class="fa fa-money" aria-hidden="true"></i></span></a>
                           {{ html()->form('DELETE', route('cashflow.destroy', [$cash_flow->id]))->style('display:inline')->open() }}
                           <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                           {{ html()->form()->close() }}
                       </td>
                      
                   </tr>
               @endforeach
        