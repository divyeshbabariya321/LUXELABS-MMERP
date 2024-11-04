@extends('layouts.app')

@section('large_content')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput-typeahead.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<style type="text/css">
    #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
	.nav-item a{
		color:#555;
	}
  Route::resource('zabbix', 'ZabbixController');
	a.btn-image{
		padding:2px 2px;
	}
	.text-nowrap{
		white-space:nowrap;
	}
	.search-rows .btn-image img{
		width: 12px!important;
	}
	.search-rows .make-remark
	{
		border: none;
		background: none
	}
  .table-responsive select.select {
    width: 110px !important;
  }

  @media (max-width: 1280px) {
    table.table {
        width: 0px;
        margin:0 auto;
    }

    /** only for the head of the table. */
    table.table thead th {
        padding:10px;
    }

    /** only for the body of the table. */
    table.table tbody td {
        padding:10 px;
    }

    .text-nowrap{
      white-space: normal !important;
    }
  }

</style>
@endsection
<div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
<div class="row">
	<div class="col-md-12 p-0">
		<h2 class="page-heading">Host Item List</h2>
	</div>
  <div class="col-lg-12 margin-tb" id="page-view-result">
      <div class="col-lg-12 pl-5 pr-5">
          <div style="display: flex !important; float: right !important;">
              <div>
                  <a href="{{ route('zabbix.user.index') }}" class="btn m-1 btn-xs btn-secondary create-new-user">Manage users</a>
              </div>
              <div>
                  <a href="{{ route('zabbix.trigger.index') }}" class="btn m-1 btn-xs btn-secondary create-new-user">Triggers</a>
              </div>
              <div>
                  <a href="#" class="btn btn-xs m-1 btn-secondary create-new-host">Create new HOST</a>
              </div>
              <div>
                  <a href="{{ route('zabbix.item.index') }}" class="btn btn-xs m-1 btn-secondary">Items</a>
              </div>
          </div>
      </div>
  </div>
</div>

<div class="table-responsive mt-3" style="margin-top:20px;">
      <table class="table table-bordered text-nowrap" style="border: 1px solid #ddd;" id="zabbix-table">
        <thead>       
            <tr>
                <th>Actions</th>
                <th>Host</th>
                <th>Free inodes in %</th>
                <th>Space utilization</th>
                <th>Total space</th>
                <th>Used space</th>
                <th>Available memory</th>
                <th>Available memory in</th>
                <th>CPU idle time</th>
                <th>CPU utilization</th> 
                <th>Interrupts per second</th>                               
            </tr>
        </thead>
        <tbody> 
            
        </tbody>
      </table>
      <div class="pagination-custom">
       
      </div> 
</div>

<div class="modal fade zabbix" id="task-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title float-left position-absolute">Detail History</h4>
          </div>
          <div id="task-table-body" class="modal-body">
              <div class="panel">
                  <table class="table">
                      <thead class="modal-table">
                            <tr>
                              <th>Host</th>
                              <th>Free inodes in %</th>
                              <th>Space utilization</th>
                              <th>Total Space</th>
                              <th>Used Space</th>
                              <th>Available memory</th>
                              <th>Available memory in %</th>
                              <th>CPU Idle time</th>
                              <th>CPU utlization</th>
                              <th>Interrupts per second</th>
                              <th>Created At</th>
                              <th>Updated At</th>
                            </tr>   
                      </thead>
                      <tbody id="renderData">
                     
                          
                      </tbody>
                  </table>
              </div>
          </div>
          <div class="modal-footer clearfix">
          </div>
      </div>
  </div>
</div>

<div class="modal fade" id="host-create-new" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><b>Save Host</b></h5>
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
                                               placeholder="Enter id" id="host-id">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" class="form-control" name="name"
                                                   placeholder="Enter name" id="host-name">
                                        </div>
                                        <div class="form-group">
                                            <label>IP</label>
                                            <input type="text" class="form-control" name="ip"
                                                   placeholder="Enter ip" id="host-ip">
                                        </div>
                                        <div class="form-group">
                                            <label>Port</label>
                                            <input type="text" class="form-control" name="port"
                                                   placeholder="Enter port" id="host-port">
                                        </div>
                                        <div class="form-group">
                                            <label>Templates</label>
                                            <select id="host-template-ids" class="form-control input-sm career-store-websites"
                                                    name="template_ids[]" multiple required>
                                                @foreach ($templates as $template)
                                                    <option value="{{ $template['templateid'] }}">{{ $template['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit"
                                                class="btn btn-danger delete-host float-left float-lg-left"
                                                data-id="">
                                            Delete
                                        </button>
                                        <button type="submit"
                                                class="btn btn-secondary submit-save-host float-right float-lg-right">
                                            Save
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
@endsection

@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript">

      var oTable;
        $(document).ready(function() {
            oTable = $('#zabbix-table').DataTable({
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                responsive: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                sScrollX:false,
                searching: true,
               
                targets: 'no-sort',
                bSort: false,
                ajax: {
                    "url": "{{ route('zabbix.index') }}",
                    data: function(d) {
                       console.log(d)
                    },
                },
                columnDefs: [{
                    targets: [],
                    orderable: true,
                    searchable: true
                }],
                columns: [
                    {
                        data: 'id',
                        render: function(data, type, row, meta) {
                            return '<div class="singleline-flex"><button type="button" data-id="'+data+'"class="btn btn-secondary edit-host-btn">Actions</button></div>';
                        }
                    },
                    {
                      data: 'name',                                             
                      render: function(data, type, row, meta) {
                        return '<div class="singleline-flex"><a href="{{ route('zabbix.item.index') }}/'+row.hostid+'">'+data+'</a><a href="#" data-id="'+row.hostid+'" class="btn btn-primary infobtn float-right"> <i class="fa fa-info"></i></a></div>';
                      }
                    },
                    {
                      data: 'items.free_inode_in',                                             
                      render: function(data, type, row, meta) {
                        return data+"%";
                      }
                    },
                    {
                      data: 'items.space_utilization',                                             
                      render: function(data, type, row, meta) {
                        return data+"%";
                      }
                    },
                    {
                      data: 'items.total_space',                                             
                      render: function(data, type, row, meta) {
                        var digit = data/1000000000;
                        return digit.toFixed(2);
                      }
                    },
                    {
                      data: 'items.used_space',                                             
                      render: function(data, type, row, meta) {
                        var digit = data/1000000000;
                        return digit.toFixed(2);
                      }
                    },
                    {
                      data: 'items.available_memory',                                             
                      render: function(data, type, row, meta) {
                        var digit = data/1000000000;
                        return digit.toFixed(2);
                      }
                    },
                    {
                      data: 'items.available_memory_in',                                             
                      render: function(data, type, row, meta) {
                        return data+"%";
                      }
                    },
                    {
                      data: 'items.cpu_idle_time',                                             
                      render: function(data, type, row, meta) {
                        return data+"%";
                      }
                    },
                    {
                      data: 'items.cpu_utilization',                                             
                      render: function(data, type, row, meta) {
                        return data+"%";
                      }
                    },
                    {
                      data: 'items.interrupts_per_second',                                             
                      render: function(data, type, row, meta) {
                        return data;
                      }
                    },                     
                ],
            });
        });

        
        $(document).on('click','.infobtn',function(){
          var hostId = $(this).data('id');
          $.ajax({
            url:'/zabbix/history',
            method:'GET',
            data:{hostid:hostId},
            success:function(response){
              var html;
              $("#renderData").html('');
              $.each(response.data,function(key,value){
                  html += `<tr>
                    <td>${value.hostname}</td>
                    <td>${value.free_inode_in}</td>
                    <td>${value.space_utilization}</td>
                    <td>${value.total_space}</td>
                    <td>${value.used_space}</td>
                    <td>${value.available_memory}</td>
                    <td>${value.available_memory_in}</td>
                    <td>${value.cpu_idle_time}</td>
                    <td>${value.cpu_utilization}</td>
                    <td>${value.interrupts_per_second}</td>
                    <td>${value.created_at}</td>
                    <td>${value.updated_at}</td>
                    </tr>`;
                })
              $("#renderData").append(html);
                $('#task-modal').modal('show');
            }
          })
        })
    </script>

    <script>
        $(document).on("click", ".create-new-host", function (e) {
            e.preventDefault();
            $('#host-create-new').modal('show');
            restoreForm();
        });
        $("#host-template-ids").select2();
        $(document).on("click", ".delete-host", function (e) {
            e.preventDefault();
            let hostId = $(this).attr('data-id');
            var url = "{{ route('zabbix.host.delete') }}?id="+hostId+"";

            var formData = $(this).closest('form').serialize();

            $('#loading-image-preview').show();
            $.ajax({
                url: url,
                method: 'DELETE',
                data: formData,
                success: function (resp) {
                    $('#loading-image-preview').hide();
                    if (resp.code == 200) {
                        toastr["success"](resp.message);
                    } else {
                        toastr["error"](resp.message);
                    }
                },
                error: function (err) {
                    $('#loading-image-preview').hide();
                    toastr["error"](err.responseJSON.message);
                }
            })
        });

        $(document).on("click", ".submit-save-host", function (e) {
            e.preventDefault();
            var url = "{{ route('zabbix.host.save') }}";
            var formData = $(this).closest('form').serialize();

            $('#loading-image-preview').show();
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                success: function (resp) {
                    $('#loading-image-preview').hide();
                    if (resp.code == 200) {
                        toastr["success"](resp.message);
                    } else {
                        toastr["error"](resp.message);
                    }
                },
                error: function (err) {
                    $('#host-create-new').modal('hide');
                    toastr["error"](err.responseJSON.message);
                }
            })
        });

        $(document).on('click', 'button.edit-host-btn', function(e) {
            e.preventDefault();
            console.log('Test');
            $('#host-create-new').modal('show');

            restoreForm();

            let hostId = $(this).attr('data-id');

            var url = "{{ route('zabbix.host.detail') }}?id=" + hostId;

            $('#loading-image-preview').show();
            let data;
            $.ajax({
                url: url,
                method: 'GET',
                success: function (resp) {
                    $('#loading-image-preview').hide();
                    if (resp.code == 200) {
                        data = resp.data;
                        $('#host-name').val(data.name);
                        $('#host-ip').val(data.ip);
                        $('#host-port').val(data.port);
                        $('#host-url').val(data.url);
                        $('#host-id').val(data.id);
                        $('.delete-host').attr('data-id', data.id);
                    } else {
                        toastr["error"](resp.message);
                    }
                },
                error: function (err) {
                    $('#host-create-new').modal('hide');
                    $('#loading-image-preview').hide();
                    toastr["error"](err.responseJSON.message);
                }
            })

            
        });

        var restoreForm = function() {
            $('.submit_delete_host').val('');
            $('#host-id').val('');
            $('#host-name').val('');
            $('#host-ip').val('');
            $('#host-port').val('');
            $('#host-template-ids').val('');
        }
    </script>


@endsection

