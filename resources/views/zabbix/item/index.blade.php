@extends('layouts.app')

@section('content')
    <div class="row" id="common-page-layout">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Zabbix items <span class="count-text"></span></h2>
        </div>
        <br>
        <div class="col-lg-12 margin-tb" id="page-view-result">
            <div class="col-lg-12 pl-5 pr-5">
                <div style="display: flex !important; float: right !important;">
                    <div>
                        <a href="#" class="btn btn-xs btn-secondary create-new-item">Create</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 pl-5 pr-5">
            <form action="/store-website/generate-api-token" method="post">
                <?php echo csrf_field(); ?>

                <div class="col-md-12">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered overlay api-token-table" id="zabbix-items">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th width="15%">Name</th>
                                <th width="20%">Key</th>
                                <th width="20%">Type</th>
                                <th>Value Type</th>
                                <th width="45%">Delay</th>
                                <th width="45%">Interface ID</th>
                                <th width="45%">Host ID</th>
                                <th>Units</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php /** @var \App\Models\Zabbix\Item $item */ ?>
                            @foreach($items as $item)
                                <tr>
                                    <td class="td-id-{{ $item->getId() }}">
                                        {{ $item->getId() }}
                                    </td>
                                    <td class="td-name-{{ $item->getId() }}">
                                        {{ $item->getName() }}
                                    </td>
                                    <td class="td-key-{{ $item->getId() }}">
                                        {{ $item->getKey() }}
                                    </td>
                                    <td class="td-type-{{ $item->getId() }}">
                                        {{ \App\Models\Zabbix\Item::TYPES[$item->getType()] }}
                                    </td>
                                    <td class="td-value-type-{{ $item->getId() }}">
                                        {{ \App\Models\Zabbix\Item::VALUE_TYPES[$item->getValueType()] }}
                                    </td>
                                    <td class="td-delay-{{ $item->getId() }}">
                                        {{ $item->getDelay() }}
                                    </td>
                                    <td class="td-interface-id-{{ $item->getId() }}">
                                        {{ $item->getInterfaceid() }}
                                    </td>
                                    <td class="td-host-id-{{ $item->getId() }}">
                                        {{ $item->getHostId() }}
                                    </td>
                                    <td class="td-units-{{ $item->getId() }}">
                                        {{ $item->getUnits() }}
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-xs btn-secondary btn-edit-item td-edit-{{ $item->getId() }}" data-id="{{ $item->getId() }}" data-json='@json($item)'>Edit</a>
                                    </td>
                                    <td>
                                    <a href="#" class="btn btn-xs btn-danger btn-delete-item td-delete-{{ $item->getId() }}" data-id="{{ $item->getId() }}" data-json='@json($item)'>Delete</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <div class="modal fade" id="item-create-new" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Create new item</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form action="" method="post">
                                <?php echo csrf_field(); ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive mt-3">
                                            <input hidden type="text" class="form-control" name="id"
                                                   placeholder="Enter id" id="item-id">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input type="text" class="form-control" name="name"
                                                       placeholder="Enter name" id="item-name">
                                            </div>
                                            <div class="form-group">
                                                <label>Type</label>
                                                <select id="item-type" class="form-control input-sm"
                                                        name="type" required>
                                                        @foreach(\App\Models\Zabbix\Item::TYPES as $key => $value)
                                                            <option value="{{ $key }}">{{ $value }}</option>
                                                        @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Value Type</label>
                                                <select id="item-value-type" class="form-control input-sm"
                                                name="value_type" required>
                                                @foreach(\App\Models\Zabbix\Item::VALUE_TYPES as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Key</label>
                                                <input type="text" class="form-control" name="key"
                                                       placeholder="Enter Key" id="item-key">
                                            </div>
                                            <div class="form-group">
                                                <label>Template</label>
                                                <select id="item-host-id" class="form-control input-sm"
                                                        name="host_id" required>
                                                    <option value="0">Choose template</option>
                                                    @foreach ($templates as $template)
                                                        <option value="{{ $template['templateid'] }}">{{ $template['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Delay</label>
                                                <input type="text" class="form-control" name="delay"
                                                       placeholder="Enter delay" id="item-delay">
                                            </div>
                                            <div class="form-group">
                                                <label>Units</label>
                                                <input type="text" class="form-control" name="units"
                                                       placeholder="Enter units" id="item-units">
                                            </div>
                                            <div class="form-group">
                                                <label>Interface id</label>
                                                <input type="text" class="form-control" name="interfaceid"
                                                       placeholder="Enter Interface ID" id="item-units" value="1">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button type="submit"
                                                    class="btn btn-secondary submit_create_item float-right float-lg-right">
                                                Submit
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

     <script type="text/javascript">
        $(document).ready( function () {
            $('#zabbix-items').DataTable();
        });
        $("#item-template-id").select2({ width: '100%' });
        $(document).on("click", ".create-new-item", function (e) {
            e.preventDefault();
            $('#item-create-new').modal('show');
            restoreForm();
        });

        $(document).on("click", ".btn-delete-item", function (e) {
            e.preventDefault();
            let userId = $(this).attr('data-id');
            var url = "{{ route('zabbix.item.delete') }}?id="+userId+"";
            var formData = $(this).closest('form').serialize();

            $('#loading-image-preview').show();
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                success: function (resp) {
                    $('#loading-image-preview').hide();
                    $('#website-project-name').val("");
                    $('#store-create-project').modal('hide');
                    if (resp.code == 200) {
                        toastr["success"](resp.message);
                    } else {
                        toastr["error"](resp.message);
                    }
                },
                error: function (err) {
                    $('#loading-image-preview').hide();
                    $('#website-project-name').val("");
                    $('#user-create-new').modal('hide');
                    toastr["error"](err.responseJSON.message);
                }
            })
        });

        $(document).on("click", ".submit_create_item", function (e) {
            e.preventDefault();
            var url = "{{ route('zabbix.item.save') }}";
            var formData = $(this).closest('form').serialize();

            $('#loading-image-preview').show();
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                success: function (resp) {
                    $('#loading-image-preview').hide();
                    $('#website-project-name').val("");
                    $('#store-create-project').modal('hide');
                    if (resp.code == 200) {
                        toastr["success"](resp.message);
                        let item = resp.item;
                        let itemId = item.id;
                        console.log('.td-description-' + itemId);
                        $('.td-description-' + itemId).text(item.description);
                        $('.td-type-' + itemId).text(item.type);
                        $('.td-location-' + itemId).text(item.location);
                        $('.td-created-at-' + itemId).text(item.created_at);
                        $('.td-store-websites-' + itemId).text(item.store_website_id);
                        $('.td-edit-' + itemId).attr('data-json', resp.item_json);
                        if (!item.is_active) {
                            $('.td-is-active-' + itemId).removeAttr('checked');
                        } else {
                            $('.td-is-active-' + itemId).attr('checked', item.is_active);
                        }


                    } else {
                        toastr["error"](resp.message);
                    }
                },
                error: function (err) {
                    $('#loading-image-preview').hide();
                    $('#website-project-name').val("");
                    $('#item-create-new').modal('hide');
                    toastr["error"](err.responseJSON.message);
                }
            })
        });

        $('a.btn-edit-item').click(function(e) {
            e.preventDefault();
            $('#item-create-new').modal('show');

            restoreForm();

            $('#item-id').val($(this).attr('data-id'));

            let data = JSON.parse($(this).attr('data-json'));

            $('#item-name').val(data.name);
            $('#item-key').val(data.key);
            $('#item-host-id').val(data.host_id ? data.host_id : 0);
            $('#item-type').val(data.type);
            $('#item-value-type').val(data.value_type);
            $('#item-delay').val(data.delay);
            $('#item-units').val(data.units);
            $('#item-interfaceid').val(data.intarfaceid);
            $("#item-template-id option[value='" + data.templateid + "']").prop("selected", true);
            $('#item-host-id').select2({ width: '100%' });
            $("#item-template-id").select2({ width: '100%' });
        });

        var restoreForm = function() {
            $('#item-id').val('');
            $('#item-name').val('');
            $('#item-key').val('');
            $('#item-host-id').val('');
            $('#item-host-id').select2({ width: '100%' });
            $('#item-type').val('');
            $('#item-value-type').val('');
            $('#item-delay').val('');
            $('#item-units').val('');
            $('#item-interfaceid').val('');
            $('#item-template-id').val('');
        }
    </script>
@endsection