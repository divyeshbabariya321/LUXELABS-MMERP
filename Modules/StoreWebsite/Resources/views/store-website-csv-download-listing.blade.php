@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Store Websites ({{$storeWebsites->total()}})</h2>
		</div>
	</div>
    <div class="mt-3 col-md-12">
        <form action="{{ route('store-website.listing') }}" method="get" class="search">
            <!-- Form fields go here -->
    
            <div class="col-md-2 pd-sm">
                 <label class="control-label">Search websites</label>
                {{ html()->multiselect("store_ids[]", \App\StoreWebsite::pluck('title', 'id')->toArray(), request('store_ids'))->class("form-control globalSelect2")->data('placeholder', "Select Website") }}
            </div>

            <div class="col-lg-2">
				<button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
				   <img src="{{ asset('images/search.png') }}" alt="Search">
			   </button>
			   <a href="{{route('store-website.listing')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
			</div>

            <div class="col-lg-2 pull-right" style="display: flex; align-items: center;">
                <!-- This div wraps the "Csv download" and "Truncate Data" buttons -->
                <button type="button" class="btn btn-secondary csv-download" onclick="return confirm('{{ __('Are you sure you want to Download') }}')">Pull Multiple Websites</button>
                <a href="{{ route('store-website-csv-truncate') }}" class="btn btn-secondary ml-2" onclick="return confirm('{{ __('Are you sure you want to Truncate Data? Note: It will remove google translate data and csv files') }}')">Truncate Data</a>

            </div>
        </div>
    </div>
    <div class="mt-3 col-md-12">
        
    </div>
    
	<div class="mt-3 col-md-12">
		<table class="table table-bordered table-striped" id="log-table">
		    <thead>
			    <tr>
			    	<th width="3%">No</th>
			    	<th width="15%">Websites</th>
			        <th width="10%">File Name</th>
                    <th width="5%">Action</th>

                </tr>
		    	<tbody>
                    @foreach ($storeWebsites as $storeWeb)
                        <tr>
                            <td><input type="checkbox" name="storewebsite" id="storewebsite" value="{{$storeWeb->id}}" data-id="{{ $storeWeb->id }}" data-select="true"></td>
                            <td>{{$storeWeb->title}}</td>
                            @if ($storeWeb->csvFiles->isNotEmpty())
                            <td> {{ $storeWeb->csvFiles->last()->path }}  </td> 
                            @else
                            <td> - </td>
                            @endif
                            <td><button type="button" id="ip_log" class="btn btn-xs btn-image  process-magento-csv-btn" title="pull Csv" data-id="{{$storeWeb->id}}" onclick="return confirm('{{ __('Are you Want to execute') }}')"> 
                                <i class="fa fa-paper-plane " aria-hidden="true"></i></button>
                                {{-- <button type="button" class="btn btn-xs btn-image load-pull-history ml-2" data-id="{{$storeWeb->id}}" title="View pull Histories" style="cursor: default;"> <i class="fa fa-info-circle"> </i></button> --}}
                                <a href="{{ route('store-website.push.csv', ['id' => $storeWeb->id]) }}" target="_blank" class="btn btn-xs btn-image">
                                    <img src="/images/view.png" style="cursor: default;">
                                </a>
                                <button type="button" class="btn btn-xs btn-image load-pull-logs ml-2" data-id="{{$storeWeb->id}}" title="View pull Logs" style="cursor: default;"> <i class="fa fa-info-circle"> </i></button>
                            </td>
						</tr>                        
                    @endforeach
		    	</tbody>
		    </thead>
		</table>
		{!! $storeWebsites->appends(Request::except('page'))->links() !!}
	</div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
</div>

<div id="store-listing" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Pull Request History</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="30%">store website</th>
                                <th width="25%">Updated by</th>
                                <th width="25%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="store-listing-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div id="store-log-listing" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Pull Logs</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="30%">request</th>
                                <th width="25%">message</th>
                                <th width="25%">Updated by</th>
                                <th width="25%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="store-log-listing-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
    $(document).on('click', '.csv-download', function() {
        var websiteIDS = [];
        var selectedCheckboxes = [];

        $('input[name="storewebsite"]:checked').each(function() {
            var websiteID = $(this).data('id');
            var checkboxValue = $(this).val();

            websiteIDS.push(websiteID);
            selectedCheckboxes.push(checkboxValue);
        });

        if (selectedCheckboxes.length === 0) {
            alert('Please select at least one checkbox.');
            return;
        }

        var website_ids = selectedCheckboxes.join(',');

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '{{route('mulitiple.store-website.listing')}}',
            type: 'POST',
            data: {
                website_ids: website_ids,
            },
            dataType: 'json',
                beforeSend: function () {
                    $("#loading-image-preview").show();
                },
                success: function (response) {
                    $("#loading-image-preview").hide();
                    toastr["success"](response.message);
                    location.reload();
                },
                error: function (xhr, status, error) {
                    toastr['error']("Invalid JSON response", 'error');
                },
                complete: function () {
                    $("#loading-image-preview").hide();
                }
        });
    });

    $(document).on("click", ".process-magento-csv-btn", function(e) {
        e.preventDefault();
        var id = $(this).data("id");

        $.ajax({
            url: '{{route('store-website.single.command.run')}}',
            type: "post",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, 
            data: {
                id: id,
            },
            beforeSend: function() {
                $('#loading-image').show();
            },
        }).done(function(response) {
            if (response.code == '200') {
                toastr['success']('Command Run successfully!!!', 'success');
            } else if(response.code == '500') {
                toastr['error'](response.message, 'error');
            }
            else {
                toastr['error'](response.message, 'error');
            }
            $('#loading-image').hide();
        }).fail(function(errObj) {
            $('#loading-image').hide();
                toastr['error']("Invalid JSON response", 'error');

        });
     });

     $(document).on('click', '.load-pull-history', function() {
        var id = $(this).attr('data-id');

        $.ajax({
            url: '{{ route("pull-request.histories.show", '') }}/' + id,
            dataType: "json",
            data: {
                id:id,

            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${v.storewebsite ? v.storewebsite.title : ''} </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${new Date(v.created_at).toISOString().slice(0, 10)} </td>
                                </tr>`;
                    });
                    $("#store-listing").find(".store-listing-view").html(html);
                    $("#store-listing").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });

    $(document).on('click', '.load-pull-logs', function() {
        var id = $(this).attr('data-id');

        $.ajax({
            url: '{{route('pull-request.log.show')}}',
            dataType: "json",
            data: {
                id:id,
                action:"pull",
            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${v.command ? v.command : ''} </td>
                                    <td> ${v.message ? v.message : ''} </td>
                                    <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                    <td> ${new Date(v.created_at).toISOString().slice(0, 10)} </td>
                                </tr>`;
                    });
                    $("#store-log-listing").find(".store-log-listing-view").html(html);
                    $("#store-log-listing").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });
</script> 
@endsection
    