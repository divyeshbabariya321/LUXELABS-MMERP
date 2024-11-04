  @if($magento_modules->isEmpty())
    <tr>
        <td>
            No Result Found
        </td>
    </tr>

  @else

  @php
    $status_array = ['Disabled', 'Enable'];
  @endphp
    @foreach ($magento_modules as $magento_module)
      <tr id="row{{ $magento_module->id }}">
        <td> {{ isset($magento_module->module_category->category_name)?$magento_module->module_category->category_name:' - ' }} </td>
        <td> {{ $magento_module->module }} </td>
        <td> {{ $magento_module->current_version }} </td>
        <td> {{ $magento_module->module_type }} </td>
        <td> {{ $magento_module->payment_status }} </td>
        <td> {{ $status_array[$magento_module->status] }} </td>
        <td> {{ $magento_module->developer_name }} </td>
        <td> {{ ($magento_module->is_customized == 1) ? 'Yes' : 'No' }} </td>
        <td>
          <a href="{{ route('magento_modules.edit', $magento_module->id)}}" class="btn btn-image"><img src="/images/edit.png" /></a>
          <button type="button" onclick="deleteData({{ $magento_module->id }})" class="btn btn-image"><img src="/images/delete.png" /></button>
        </td>
      </tr>
    @endforeach

  @endif

