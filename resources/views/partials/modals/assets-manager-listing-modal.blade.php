<style>
    .select-assets-manager {
        padding: 10px;
        border: 1px solid rgb(0,0,0,0.125);
        border-radius: 7px;
    }
</style>
<div id="assetsEditModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="select-assets-manager">
                <label for="select-assets-manager">
                    Choose assets manager from select
                </label>
                <select id="select-assets-manager">
                    @foreach($assets as $asset)
                        <option value="{{ $asset->id }}" data-assets="{{ json_encode($asset) }}">{{ $asset->name }}</option>
                    @endforeach
                </select>
            </div>

            @include('partials.modals.assets-manager-listing')
        </div>
    </div>
</div>

<script>
    $("#select-assets-manager").select2({
        'width': '100%'
    });
    $(document).on('change', "#select-assets-manager", function() {
        var selectedOption = $(this).find('option:selected');
        console.log(selectedOption);
        var asset = selectedOption.data('assets');
        console.log(asset);
        var url = "{{ url('assets-manager') }}/" + asset.id;
        //console.log(asset);
        var d = new Date(asset.start_date);
        var day = (d.getDate() < 10 ? '0' : '') + d.getDate();
        var mon = ((d.getMonth()+1) < 10 ? '0' : '') + (d.getMonth()+1);
        var str =  d.getFullYear()+ '-' + mon + '-' + day;
        $('#assetsEditModal form').attr('action', url);
        $('#asset_name').val(asset.name);
        $('#user_name').val(asset.user_name);
        if(asset.user_name)
            $(".select-multiple").select2("val", asset.user_name);
        else
            $(".select-multiple").select2("val", "");

        $('#old_user_name').val(asset.user_name);
        $('.password-assets-manager').val(asset.password);
        $('.oldpassword-assets-manager').val(asset.password);
        $('#ip').val(asset.ip);
        $('#old_ip').val(asset.ip);
        $('#assigned_to').val(asset.assigned_to);
        $('#provider_name').val(asset.provider_name);
        $('#location').val(asset.location);
        $('#currency').val(asset.currency);
        if(asset.start_date !='0000-00-00' && asset.start_date != null){
            $('.start_date').val(''+str+'');
            $('#old_start_date').val(''+str+'');
        }else{
            $('.start_date').val("dd/mm/yyyy");
            $('#old_start_date').val("yyyy-mm-dd");
        }
        $('#asset_asset_type').val(asset.asset_type);
        $('#category_id2').val(asset.category_id);
        $('#asset_purchase_type').val(asset.purchase_type);
        $('#asset_payment_cycle').val(asset.payment_cycle);
        $('#asset_amount').val(asset.amount);
        $('#usage').val(asset.usage);
        $('#capacity').val(asset.capacity);
        $('#link').val(asset.link);
        $('#client_id').val(asset.client_id);
        $('#account_username').val(asset.account_username);
        $('#account_password').val(asset.account_password);
        $('#monit_api_url').val(asset.monit_api_url);
        $('#monit_api_username').val(asset.monit_api_username);
        $('#monit_api_password').val(asset.monit_api_password);

        $('#ip_name_ins').val(asset.ip_name);

        $(".addServerUpdate").html("");
        var addserver = '';
        let folderName = JSON.parse(asset.folder_name);
        $.each(folderName,function(key,value){
            addserver = addserver+'<input type="text" name="folder_name[]" id="folder_name'+key+'" class="form-control"  value="'+value+'" >';

        });
        $(".addServerUpdate").append(addserver);
        $('#server_password').val(asset.server_password);
        $('.show-user-history-btn').attr('data-id', asset.id);
    });
</script>