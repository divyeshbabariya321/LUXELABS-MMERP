<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th width="2%"> No</th>
            <th width="20%">Status</th>
            <th width="10%">Color</th>
            <th width="20%">Sttaus for color</th>
            <th width="20%">Created Date</th>
        </tr>
    </thead>
    <tbody class="show-search-password-list">
        @foreach($orderStatus as $stat)
        <tr>
            <td>{{ $stat->id }}</td>
            <td>{{ $stat->status }}</td>
        </td>
        <td style="display: flex;padding: 1px;">
            <input type="color" name="status_color" class="form-control" value="{{$stat->color}}" id="colorInput_{{$stat->id}}">                                   
                  <button type="button" class="btn btn-image edit-vendor" onclick="updateOrderColor({{$stat->id}})" style="float: right;">
                    <i class="fa fa-arrow-circle-right fa-lg"></i>
                </button>  
        </td>   
        <td>
            @if($stat->color == null)
            <span class="">This status color not update</span>
            @endif
        </td>
            <td>{{ $stat->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="pagination-container-order-status"></div>

<script>
function updateOrderColor(orderId) {
        var colorValue = $("#colorInput_" + orderId).val();
        $.ajax({
            url: "{{route('order.status.color.Update')}}",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: {
                orderId: orderId,
                colorValue: colorValue
            },
            success: function(response) {
                toastr["success"](response.message, "Message");
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }
</script>