@extends('layouts.app')

@section('title', 'Magento Order Status')

@section('styles')
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <h2 class="page-heading">Magento Order Status Mapping</h2>
          </div>
          <div class="col-12 mb-3">
            <div class="pull-left">

            </div>
            <div class="pull-right">
                
            </div>
        </div>
    </div>
    <div class="col-12">
    <div class="table-responsive">
      
      <table class="table table-bordered" style="padding:5px;">
        <thead>
          <tr>
            <th style="width: 3%; padding: 5px;">ID</th>
            <th style="width: 18%;padding: 5px;">Status</th>
            <th style="width: 10%;padding: 5px;">Magento Status</th>
            <th style="width: 75%;padding: 5px;">Message Text Template</th>
          </tr>
        </thead>

        <tbody>
         @foreach($orderStatusList as $orderStatus)
            <tr>
              <td style="padding: 5px;vertical-align: middle;">{{ $orderStatus->id }}</td>
              <td style="padding: 5px;vertical-align: middle;">{{ $orderStatus->status }}</td>
              <td style="padding: 5px;vertical-align: middle;"><input type="text" value="{{ $orderStatus->magento_status }}" class="form-control" onfocusout="updateStatus({{ $orderStatus->id }})" id="status{{ $orderStatus->id }}"></td>
              <td style="display: flex;padding: 5px;">
                <textarea class="form-control message-text-tpl" name="message_text_tpl" style="    height: 34px;">{{ !empty($orderStatus->message_text_tpl) ? $orderStatus->message_text_tpl : \App\Order::ORDER_STATUS_TEMPLATE }}</textarea>
                <button type="button" class="btn btn-image edit-vendor" onclick="updateStatus({{ $orderStatus->id }})"><i class="fa fa-arrow-circle-right fa-lg"></i></button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    </div>
@endsection

@section('scripts')
<script type="text/javascript">
  function updateStatus(id){
    status = $('#status'+id).val();
    var tr = $('#status'+id).closest("tr");
    $.ajax({
        url: "{{ route('magento.save.status') }}",
        dataType: "json",
        type: 'POST',
        data: {
             id: id,
             status : status,
             message_text_tpl : tr.find(".message-text-tpl").val(),
             _token: "{{ csrf_token() }}",
        },
        beforeSend: function () {
            $("#loading-image").show();
        },

    }).done(function (data) {
      //console.log(data);
      toastr['success']('Order status updated successfully', 'success');
    }).fail(function (jqXHR, ajaxOptions, thrownError) {
        alert('No response from server');
    });  
    
  }
</script>

@endsection
