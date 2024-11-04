 @if($whatsAppConfigs->isEmpty())

 <tr>
  <td>
    No Result Found
  </td>
</tr>
@else

@foreach ($whatsAppConfigs as $whatsAppConfig)

<tr>
 
  <td class="p-2 align-middle">{{ $whatsAppConfig->username }}</td>
  <td class="p-2 align-middle"></td>
  <td class="p-2 align-middle">{{ $whatsAppConfig->number }}</td>
  <td class="p-2 align-middle">{{ $whatsAppConfig->provider }}</td>
  <td class="p-2 align-middle">{{ $whatsAppConfig->frequency }}</td>
  <td class="p-2 align-middle">@if($whatsAppConfig->is_customer_support == 1) Yes @else No @endif</td>
  <td class="p-2 align-middle">{{ $whatsAppConfig->send_start }}</td>
  <td class="p-2 align-middle">{{ $whatsAppConfig->send_end }}</td>
  <td class="p-2 align-middle">{{ $whatsAppConfig->device_name }}</td>
  <!-- <td>{{ $whatsAppConfig->simcard_number }}</td>
  <td>{{ $whatsAppConfig->simcard_owner }}</td>
  <td>{{ $whatsAppConfig->payment }}</td>
  <td>{{ $whatsAppConfig->recharge_date }}</td> -->
  <td class="p-2 align-middle">@if($whatsAppConfig->status == 1) Active @elseif($whatsAppConfig->status == 2) Blocked @elseif($whatsAppConfig->status == 3)  Scan Barcode @else Inactive @endif</td>
  <td class="p-2 align-middle">{{ date('d-m-Y', strtotime($whatsAppConfig->created_at))}}</td>
  <td class="p-2 align-middle">{{ $whatsAppConfig->instance_id }}</td>
  <td class="whatsAppConfig-action p-2 align-middle">
    <button onclick="changewhatsAppConfig({{ $whatsAppConfig->id }})" class="btn btn-sm"><i class="fa fa-edit"></i></button>
    @if(Auth::user()->hasRole('Admin'))
    <button onclick="deleteConfig({{ $whatsAppConfig->id }})" class="btn btn-sm"><i class="fa fa-trash"></i></button>
    @endif
    <a href="{{route('whatsapp.config.history', $whatsAppConfig->id)}}" title="History"><i class="fa fa-history" aria-hidden="true"></i></a>
    <a href="{{route('whatsapp.config.queue', $whatsAppConfig->id)}}" title="Queue"><i class="fa fa-list" aria-hidden="true"></i></a>
    <button class="btn btn-link" onclick="getBarcode({{ $whatsAppConfig->id }})" data-toggle="tooltip" data-placement="top" title="Barcode For Whatsapp"><i class="fa fa-barcode"></i></button>
    <button class="btn btn-link" onclick="getScreen({{ $whatsAppConfig->id }})" data-toggle="tooltip" data-placement="top" title="WhatsApp Screen"><i class="fa fa-desktop"></i></button>
    <button class="btn btn-link" onclick="deleteChrome({{ $whatsAppConfig->id }})" data-toggle="tooltip" data-placement="top" title="Delete Chrome Config"><i class="fa fa-trash"></i></button>
    <button class="btn btn-link" onclick="restartScript({{ $whatsAppConfig->id }})" data-toggle="tooltip" data-placement="top" title="Restart"><i class="fa fa-refresh"></i></button>
    @if($whatsAppConfig->is_use_own == 1)
      <button class="btn btn-link" onclick="logoutScript({{ $whatsAppConfig->id }})" data-toggle="tooltip" data-placement="top" title="Logout script">
        <i class="fa fa-sign-out"></i>
      </button>
      <button class="btn btn-link" onclick="getInfo({{ $whatsAppConfig->id }})" data-toggle="tooltip" data-placement="top" title="Get status">
        <i class="fa fa-info"></i>
      </button>
    @endif
  </td>
</tr>

@include('marketing.whatsapp-configs.partials.edit-modal')
@endforeach

@endif