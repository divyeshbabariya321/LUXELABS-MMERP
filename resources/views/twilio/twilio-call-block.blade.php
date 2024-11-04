@extends('layouts.app')

@section('styles')
  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
@endsection
@section('large_content')
   <div class = "row">
		<div class="col-lg-12 margin-tb">
			<h2 class="page-heading">Twilio Call Blocks</h2>
			<div class="pull-left cls_filter_box">
				{{ html()->modelForm($input, 'GET', route('twilio.call.blocks'))->class('form-inline')->open() }}
                    <div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_archived">Twilio Number</label>
						{{ html()->text('search_twilio_number')->class('form-control') }}
                    </div>
					<div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_archived">Customer Number</label>
						{{ html()->text('search_customer_number')->class('form-control') }}
                    </div>
					<div class="form-group ml-3 mt-4">
						<button type='submit' class="btn btn-default">Search</button>
						<a href="{{route('twilio.call.blocks')}}" class="btn btn-default">Clear</a>
                    </div>
				</form>
            </div>
        </div>
	</div>
	<div class="row mt-3">
		<div class="col-11 ">
			<button type="button" class="delete-all-record btn btn-xs btn-secondary my-3 mx-4" id="select-all-product">Delete All</button>
		</div>
	</div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
			<div class="panel-group" style="margin-bottom: 5px;">
                <div class="panel mt-3 panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
							Twilio Call Blocks
                        </h4>
                    </div>
					<div class="panel-body">
						<table class="table table-bordered table-hover" style="table-layout:fixed;">
							<thead>
								<tr>
									<th style="width:5%"><input type="checkbox" id="callIdAll"/></th>
									<th style="width:5%">ID</th>
									<th style="width:10%">Customer Number</th>
									<th style="width:10%">Twilio Number</th>
									<th style="width:10%">Customer </th>
									<th style="width:10%">Twilio Credential</th>
									<th style="width:10%">Customer Website</th>
									<th style="width:10%">Twilio Number Website</th>
									<th style="width:10%">Date</th>
								</tr>
							</thead>
							<tbody>
							@foreach ($twilioCallBlocks as $val )
								<tr id = "row_{{$val->id}}">
									<td style="width:5%"><Input type="checkbox" id="callId[]" name="callId[]" value="{{$val->id}}"/></td>
									<td style="width:5%">{{$val->id}}</td>
									<td style="width:10%;overflow-wrap: break-word">{{$val->customer_number}}</td>
									<td style="width:10%;overflow-wrap: break-word">{{$val->twilio_number}}</td>
									
									<td style="width:10%;overflow-wrap: break-word">{{$val->customerName}}</td>
									<td style="width:10%;overflow-wrap: break-word">{{$val->twilio_email}}</td>
									<td style="width:10%;overflow-wrap: break-word">{!! $val->customerWebsite !!}</td> 
									<td style="width:10%;overflow-wrap: break-word">{!! $val->twWebsite !!}</td>
									<td style="width:10%;overflow-wrap: break-word">{{date('d-M-Y', strtotime($val->created_at))}}</td>
								</tr>
							@endforeach
							</tbody>
						</table>
						{{ $twilioCallBlocks->appends($input)->links() }}
                    </div>
                </div>
            </div>
		</div>
	</div>	

	<script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-multiselect.min.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.jscroll.min.js') }} "></script>
    <script>
        $(function(){
            $('#callIdAll').click(function(){
                var idchecked = $('input:checkbox').not(this).prop('checked', this.checked);
                if($("#sugIdAll").prop('checked') == true){
                    //alert('Yes');
                }
            });
        });
            
        $('.delete-all-record').on('click', function(e) {
            var val = [];
            $('input[name="callId[]"]:checkbox:checked').each(function(i, elem) {
                val[i] = $(this).val();
            });

            if(val.length == 0) {
                alert("Please select any one row you want to delete record!!!");
            } else {
                if(confirm('Are you sure really want to Delete records?')) {
                    e.preventDefault();
                    var ids = val.toString();
                    $.ajax({
                        url: '{{route("twilio.call.block.delete")}}',
                        type:"get",
                        data: { 
                                "_token": $('meta[name="csrf-token"]').attr('content'),
                                ids : ids
                                },
                        dataType: 'json',
                    }).done(function (response) {
                        if(response.code == 200) {
                            toastr['success'](response.message);
                            location.reload();
                        }else{
                            errorMessage = response.message ? response.message : 'Record not found!';
                            toastr['error'](errorMessage);
                        }        
                    }).fail(function (response) {
                        toastr['error'](response.message);
                    });
                }

            }
        });
	</script>
@endsection
