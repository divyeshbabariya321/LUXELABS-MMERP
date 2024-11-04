
@foreach ($customers_all as $c) 
@php 
         $used_credit = (string) $c->usedCredit->sum('used_credit');
         $credit_in = (string) $c->creditIn->sum('used_in');
        
         @endphp
           <tr>
             <td>{{ $c->name }}</td>
             <td>{{ $c->email }}</td>
             <td>{{ $c->phone }}</td>
             <td>{{ $c->title }}</td>
              <td>@if($c->date != null) {{ date("d-m-Y",strtotime($c->date)) }} @endif</td>
             <td>{{ $c->credit  + $credit_in }}</td>
             <td>{{ $used_credit }}</td>
             <td>{{ ($c->credit + $credit_in ) - $used_credit }}</td>
             <td><a href="#" onclick="getLogs('{{ $c->id}}')"><i class="fa fa-eye"></i></a></td>
             <td><a href="#" onclick="getHistories('{{ $c->id}}')"><i class="fa fa-eye"></i></a></td>
             
           
           </tr>
         @endforeach

