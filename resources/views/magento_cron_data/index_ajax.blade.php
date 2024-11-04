@php $i=1; @endphp
   @foreach ($data as $dat) 
       <tr style="background-color: {{$dat?->cronStatus?->color}}!important;" data-id="{{$i}}" class="tr_{{$i++}}">
           <td class="expand-row" style="word-break: break-all">
               <span class="td-mini-container">
                  {{ strlen( $dat['website']) > 22 ? substr( $dat['website'], 0, 22).'...' :  $dat['website'] }}
               </span>

               <span class="td-full-container hidden">
                   {{ $dat['website'] }}
               </span>
            </td>

            <td class="expand-row" style="word-break: break-all">
               <span class="td-mini-container">
                  {{ strlen( $dat['cron_id']) > 9 ? substr( $dat['cron_id'], 0, 8).'...' :  $dat['cron_id'] }}
               </span>

               <span class="td-full-container hidden">
                   {{ $dat['cron_id'] }}
               </span>
            </td>

           <td class="expand-row" style="word-break: break-all">
                <span class="td-mini-container">
                            {{ strlen( $dat['job_code']) > 18 ? substr( $dat['job_code'], 0, 18).'...' :  $dat['job_code'] }}
                 </span>

               <span class="td-full-container hidden">
                           {{ $dat['job_code'] }}
                        </span>
           </td>
           <td class="expand-row" style="word-break: break-all">
                <span class="td-mini-container">
                            {{ strlen( $dat['cron_message']) > 15 ? substr( $dat['cron_message'], 0, 15).'...' :  $dat['cron_message'] }}
                 </span>

               <span class="td-full-container hidden">
                           {{ $dat['cron_message'] }}
                        </span>
           </td>
        
           <td>{{ $dat['cronstatus'] }}</td>

           <td class="expand-row" style="word-break: break-all">
                <span class="td-mini-container">
                            {{ strlen( $dat['cron_created_at']) > 15 ? substr( $dat['cron_created_at'], 0, 15).'...' :  $dat['cron_created_at'] }}
                 </span>

               <span class="td-full-container hidden">
                           {{ $dat['cron_created_at'] }}
                        </span>
           </td>

           <td class="expand-row" style="word-break: break-all">
                <span class="td-mini-container">
                            {{ strlen( $dat['cron_scheduled_at']) > 15 ? substr( $dat['cron_scheduled_at'], 0, 15).'...' :  $dat['cron_scheduled_at'] }}
                 </span>

               <span class="td-full-container hidden">
                           {{ $dat['cron_scheduled_at'] }}
                        </span>
           </td>

           <td class="expand-row" style="word-break: break-all">
                <span class="td-mini-container">
                            {{ strlen( $dat['cron_executed_at']) > 15 ? substr( $dat['cron_executed_at'], 0, 15).'...' :  $dat['cron_executed_at'] }}
                 </span>

               <span class="td-full-container hidden">
                           {{ $dat['cron_executed_at'] }}
                        </span>
           </td>

           <td class="expand-row" style="word-break: break-all">
                <span class="td-mini-container">
                            {{ strlen( $dat['cron_finished_at']) > 15 ? substr( $dat['cron_finished_at'], 0, 15).'...' :  $dat['cron_finished_at'] }}
                 </span>

               <span class="td-full-container hidden">
                           {{ $dat['cron_finished_at'] }}
                        </span>
           </td>
           <td class="expand-row" style="word-break: break-all">
               <a title="Run Cron" class="btn btn-image magentoCom-run-btn pd-5     btn-ht" data-id="{{ $dat['id']}}" href="javascript:;">
                  <i class="fa fa-paper-plane" aria-hidden="true"></i>
               </a>
               <a title="Preview Response" data-id="{{ $dat['id']}}" class="btn btn-image preview_response pd-5 btn-ht" href="javascript:;">
                  <i class="fa fa-product-hunt" aria-hidden="true"></i>
              </a>
            </td>

       </tr> 
   @endforeach