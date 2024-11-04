@extends('layouts.app')

@section('styles')
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@endsection

@section('large_content')
	<?php $base_url = URL::to('/');?>
	<div class = "row">
		<div class="col-lg-12 margin-tb p-0">
			<h2 class="page-heading">Project Directory Manager (Total size Today : {{$totalSize}} MB)</h2>
        </div>
	</div>
	@if(Session::has('message'))
		<p class="alert alert-info">{{ Session::get('message') }}</p>
	@endif

	<div class = "row">
		<div class="col-lg-12 margin-tb p-0">
			<div class="pull-left cls_filter_box">
                <form class="form-inline" action="{{ route('project-file-manager.index') }}" method="GET">
                    <div class="form-group ml-3 cls_filter_inputbox d-flex">
                        <label for="with_archived">Search</label>
                       <input type="text" name="search" class="ml-3 form-control-sm cls_commu_his form-control" value="{{request()->get('search')}}">
                    </div>
                </form>
            </div>

            <div class = "row" >
                <div class="col-lg-3 margin-tb pl-0" >

                    <form class="form-inline" method="POST">
                        <div class="form-group ml-3 ">
                            <input type="text" name="limit_size" id="limit_size" class="form-control-sm form-control limit_size"  placeholder="Enter size here..."  value="{{ $limit_rec }}">
                            <button type="button" class="ml-2 btn btn-secondary submitsize" >Update  </button>
                        </div>
                    </form>

                </div>
            </div>
		</div>	
	</div>

    <div class="row">
        <div class="col-lg-12 margin-tb pl-3 pr-3">
			<div class="panel-group" style="margin-bottom: 5px;">
                <div class="panel mt-3 panel-default border-0">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                           Directory
                        </h4>
                    </div>
					<div class="panel-body p-0 mt-3 table-responsive">
						<table class="table table-bordered table-striped" style="table-layout: fixed">
							<thead>
                            <tr>
                                <th style="width: 22%">Directory Name</th>
                                <th style="width: 17%">Parent Directory</th>
                                <th style="width: 12%">Size</th>
                                <th style="width: 20%">Expected Size</th>
                                <th style="width: 12%">Created</th>
                                <th style="width: 12%">Updated</th>
                                <th style="width: 5%">Action</th>
                            </tr>
                            </thead>
                            <tbody>
							@foreach ($projectDirectoryData as $data )
								<tr>
									<td class="expand-row"  style="word-break: break-all">
                                    <span class="td-mini-container">
                                        {{ strlen($data->name) > 33 ? substr($data->name, 0, 33).'...' :  $data->name }}
                                   </span>

                                                                    <span class="td-full-container hidden">
                                         {{  $data->name }}
                                  </span>

                                       </td>
									<td class="expand-row" style="word-break: break-all">

                                        <span class="td-mini-container">
                                        {{ strlen($data->parent) > 24 ? substr($data->parent, 0, 26).'...' :  $data->parent }}
                                   </span>

                                        <span class="td-full-container hidden">
                                         {{  $data->parent }}
                                  </span>

                                      </td>
									<td class="setup-size">{{$data->size}}(MB)
                                        <a title="File Size Details" class="fa fa-info-circle Size_log" data-id="{{$data->id}}" style="float:right;font-size:15px; margin-left:10px; color: #757575;"></a>
                                    </td>
									<td>
										<div class="col-md-12 p-0" style="display: flex">
											<div class="pl-0 pr-2">
												<input class="form-control" id="expected_{{$data->id}}" name="notification_at" placeholder="Expected Size" value="{{$data->notification_at}}">
											</div>
											<div style="display: flex;">
												<button class="btn btn-sm btn-image send-message1 pl-1 pr-1" data-id="{{$data->id}}"><img src="images/filled-sent.png"></button>
                                                <button class="btn btn-sm btn-image get-latest-size pl-1 pr-1" data-id="{{$data->id}}" title="Get latest size"><i class="fa fa-file"></i></button>
                                                <button class="btn btn-sm btn-image delete-file pl-2 pr-1" style="padding-top: 8px" data-id="{{$data->id}}" title="Delete"><i style="font-size: 15px" class="fa fa-trash"></i></button>
											</div>
										</div>
									</td>
									<td>{{$data->created_at}}</td>
                                    <td>{{$data->updated_at}}</td>
                                    <td>
                                        <i type="button" style="color: #757575" class="fa fa-eye" data-name="{{ $data->name }}" data-toggle="modal" data-target="#GetFileSizeAndName">

                                           </i>
                                    </td>
								</tr>
							@endforeach
                            </tbody>
						</table>
						

                    </div>
                    {{ $projectDirectoryData->links() }}
                </div>
            </div>
		</div>
	</div>

    <div id="size_log-history-model" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">File Size History</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
    
                <form action="" id="approve-log-btn" method="GET">
                    @csrf
                    <div class="modal-body">
                    <div class="row">
                       
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                       
                                        <th>Old Size</th>
                                        <th>New Size</th>
                                        <th>Updated By</th>
                                        <th>Updated At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>             
            </div>
        </div>
      </div>

      {{---- Modal For get files list ------------------------------------------------------------------------------- --}}

      <div class="modal fade" id="GetFileSizeAndName" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Uploads folder list of files</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="" id="FileSizeAndName" method="GET">
                @csrf
                <div class="modal-body">
                <div class="row">
                   
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                   
                                    <th style="word-wrap: break-word; width=50%">File Name</th>
                                    <th>File Size</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>             
          </div>
        </div>
      </div>

@endsection

@section('scripts')
<script type="text/javascript">


    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });

$(document).on('click', '.send-message1', function () {
	console.log("Hello");
	var thiss = $(this);
	var id = $(this).data('id');
	var size = $("#expected_"+id).val();
	
	console.log(size);
	
	if (!$(thiss).is(':disabled')) {
		$.ajax({
			url: "/project-file-manager/update",
			type: 'POST',
			data: {"_token": "{{ csrf_token() }}", id: id, size:size},
			beforeSend: function () {
				$(thiss).attr('disabled', true);
			},
			success: function (response) {
				$(thiss).attr('disabled', false);
				toastr['success'](response, 'success');
			},
			error: function (response) {
				console.log(response.responseText);
				$(thiss).attr('disabled', false);
				alert('Oops, Something went wrong!!');
			}
		});
	}
});

$(document).on("click",".get-latest-size",function() {
   
    var id = $(this).data("id");
    var $this = $(this);
    $.ajax({
        url: "/project-file-manager/get-latest-size",
        type: 'POST',
        data: {"_token": "{{ csrf_token() }}", id: id},
        beforeSend: function () {
            $("#loading-image").show();
        },
        dataType:"json",
        success: function (response) {
            $("#loading-image").hide();
            if(response.code == 200) {
                var html = '';
                html += response.size;
                html += ' <a title="File Size Details" class="fa fa-info-circle Size_log" data-id="'+id+'" style="font-size:15px; margin-left:10px; color: #757575;"></a>';
                $this.closest("tr").find(".setup-size").html(html);
                toastr['success'](response.message, 'success');
            }else{
                toastr['error'](response.message, 'error');
            }
        },
        error: function (response) {
            $("#loading-image").hide();
            toastr['error']("Requested faield , please check log file.", 'error');
        }
    });
});

$(document).on("click",".delete-file",function() {
    var id = $(this).data("id");
    var $this = $(this);
    if(confirm("Are you sure you want to delete this file ? you will not able to roll back after delete")) {
        $.ajax({
            url: "/project-file-manager/delete-file",
            type: 'POST',
            data: {"_token": "{{ csrf_token() }}", id: id},
            beforeSend: function () {
                $("#loading-image").show();
            },
            dataType:"json",
            success: function (response) {
                $("#loading-image").hide();
                $this.closest("tr").remove();
                if(response.code == 200) {
                    toastr['success'](response.message, 'success');
                }else{
                    toastr['error'](response.message, 'error');
                }
            },
            error: function (response) {
                $("#loading-image").hide();
                toastr['error']("Requested faield , please check log file.", 'error');
            }
        });
    }
});
</script>
<script>
$(document).on("click", ".Size_log", function(e) {

        var id = $(this).data('id');
               
        $('#size_log-history-model table tbody').html('');
        $.ajax({
        url: "{{ route('size/log-history/discount') }}",
        data: {id: id},
        success: function (data) {
            if(data != 'error') {
                           
                $.each(data, function(i, item) {
                    
                    $('#size_log-history-model table tbody').append(
                        '<tr>\
                            <td>'+ ((item['old_size']) ? item['old_size'] : '-') +'</td>\
                            <td>'+item['new_size']+'</td>\<td>'+((item['name']) ? item['name'] : '-')+'</td>\
                            <td>'+ moment(item['created_at']).format('DD/MM/YYYY') +'</td>\
                        </tr>'
                    );
                });
            }
        }
        });

        $('#size_log-history-model').modal('show');
});


$(document).on("click", ".submitsize", function(e) {

    let size = $("#limit_size").val();
    
    if ((size === "") || ($.isNumeric(size))) {
   
    
    let _token = $("input[name=_token]").val();

    $.ajax({
        url: "{{ route('project-file-manager.insertsize') }}",
        type: "POST",
        data: {
            size: size,
            _token: _token
        },
        success: function(response) {
            toastr["success"]("Data Updated Successfully!", "Message")
            
        }
});
    }else{
        alert('Not Valid NUmber');
        }
});

    $(document).on("click", ".getnameandsize", function(){

    var name = $(this).data('name');
    // $('#GetFileSizeAndName table tbody').append('');  

    $.ajax({
        url: "{{ route('file-name/file-size.get') }}",
        type: "GET",
        data: {
            name:name,
            _token: "{{ csrf_token() }}"

            },
            success: function (response) {
                
                var  html_data = ''
                $.each(response.file_size_arr, function(i, item) {
                    
                    html_data += '<tr>\
                            <td>'+item['file_name']+'</td>\
                            <td>'+item['file_size']+'</td>\
                        </tr>';
                   
                });

                $('#GetFileSizeAndName table tbody').html(html_data);  
                   
                $('#GetFileSizeAndName').modal('show');
            }
        });

    });

</script>
@endsection