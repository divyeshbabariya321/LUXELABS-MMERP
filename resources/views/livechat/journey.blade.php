@extends('layouts.app')

@section('styles')
  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
@endsection
@section('large_content')
   <div class = "row">
		<div class="col-lg-12 margin-tb">
			<h2 class="page-heading">Chatbot log Journey</h2>
		</div>
		<!-- pawan added the all filter -->
		<div class="col-md-2">
			<input name="term_1" type="text" class="form-control filter-apply"
					value="{{ isset($term) ? $term : '' }}"
					placeholder="Search Reply" id="term_1" data-id="1">
		</div>
		<div class="col-md-2">
			<input name="term_2" type="text" class="form-control filter-apply"
					value="{{ isset($term) ? $term : '' }}"
					placeholder="Sender Name" id="term_2" data-id="2">
		</div>
		<div class="col-md-2">
			<input name="term_3" type="text" class="form-control filter-apply"
					value="{{ isset($term) ? $term : '' }}"
					placeholder="Sender Phone" id="term_3" data-id="3">
		</div>
		<div class="col-md-2">
			<input name="term_4" type="text" class="form-control filter-apply"
					value="{{ isset($term) ? $term : '' }}"
					placeholder="Message Received" id="term_4" data-id="4">
		</div>
		<div class="col-md-2">
			<select data-id="5" name="term" id="term_5" class="form-control select2" data-placeholder="Select Chat Enter">
				<option value="">Select Chat Enter</option>
				<option value="1">Yes</option>
				<option value="0">No</option>
			</select>			
		</div>
		<div class="col-md-2">
			<select data-id="6" name="term" id="term_6" class="form-control select2" data-placeholder="Select Reply Found">
				<option value="">Select Reply Found</option>
				<option value="1">Yes</option>
				<option value="0">No</option>
			</select>			
		</div>
		<div class="col-md-2">
			<select data-id="7" name="term" id="term_7" class="form-control select2" data-placeholder="Select Reply Searched">
				<option value="">Select Reply Searched</option>
				<option value="1">Yes</option>
				<option value="0">No</option>
			</select>			
		</div>
		<div class="col-md-2">
			<select data-id="8" name="term" id="term_8" class="form-control select2" data-placeholder="Select Response Sent">
				<option value="">Select Response Sent</option>
				<option value="1">Yes</option>
				<option value="0">No</option>
			</select>			
		</div>
		<div class="col-md-4">
			<button type="button" class="btn btn-image" title="Reset Filter" id="resetFilter" onclick="resetSearch()"><img src="/images/resend2.png"/></button>    
		</div>
		<!-- end -->
	</div>
	
    <div class="row">
        <div class="col-lg-12 margin-tb">
			<div class="panel-group" style="margin-bottom: 5px;">
                <div class="panel mt-3 panel-default">
                    <div class="panel-body">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>#</th>
									<th>Chat id</th>
									<th>Chat entered</th>
									<th>Message received</th>
									<th>Reply found in database</th>
									<th>Reply searched in watson</th>
									<th>Reply</th>
									<th>Response sent to customer</th>
									<!-- pawan added for sender details for watsonJourney-->
									<th>Sender Name</th>
									<th>Sender Mobile</th>
									<!-- end -->
								</tr>
							</thead>
							<tbody>
								@include('livechat.partials.list-journey')
							</tbody>
						</table>
						
                    </div>
                </div>
            </div>
		</div>
	</div>
	{!! $watsonJourney->render() !!}	
@endsection
<!-- pawan added for ajax call on filter search -->
@section('scripts')
<script type="text/javascript">
    // $('.select2').select2({placeholder: "Select",allowClear: true});
	function resetSearch(){
		url = "{{route('watson.ajax')}}";
        blank = ''
        $.ajax({
            url:url,
            dataType: "json",
            data: {
               blank : blank, 
            },
            beforeSend: function () {
                $("#loading-image").show();
            },
        }).done(function (response) {
            $("#loading-image").hide();
            $('#term_1').val('');
            $('#term_2').val('');
            $('#term_3').val('');
            $('#term_4').val('');
            $('#term_5').val('');
			$('#term_6').val('');
			$('#term_7').val('');
			$('#term_8').val('');
            $('tbody').html('');
            $('tbody').html(response.livechat);
            // $("#Referral_count").text(response.count);
        
            if (response.links.length > 10) {
                $('ul.pagination').replaceWith(response.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }
        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
    }
    // pawan added for calling the function on change for filter & ajax call
    function onInput(value,applyId){
        url = "{{route('watson.ajax')}}";
        $.ajax({
            url: url,
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET',
            data: {
                term : value,
                apply_id: applyId
            },
            beforeSend: function () {
                $("#loading-image").show();
            },
        }).done(function (response) {
            $("#loading-image").hide();
            if(applyId == 1){
                // $('#term_1').val('');
                $('#term_2').val('');
                $('#term_3').val('');
                $('#term_4').val('');
                $('#term_5').val('');
				$('#term_6').val('');
				$('#term_7').val('');
            } else if(applyId == 2){
                $('#term_1').val('');
                // $('#term_2').val('');
                $('#term_3').val('');
                $('#term_4').val('');
                $('#term_5').val('');
				$('#term_6').val('');
				$('#term_7').val('');
            } else if(applyId == 3){
                $('#term_1').val('');
                $('#term_2').val('');
                // $('#term_3').val('');
                $('#term_4').val('');
                $('#term_5').val('');
				$('#term_6').val('');
				$('#term_7').val('');
            } else if(applyId == 4){
                $('#term_1').val('');
                $('#term_2').val('');
                $('#term_3').val('');
                // $('#term_4').val('');
                $('#term_5').val('');

				$('#term_6').val('');
				$('#term_7').val('');
            } else if(applyId == 5){
                $('#term_1').val('');
                $('#term_2').val('');
                $('#term_3').val('');
                $('#term_4').val('');
                // $('#term_5').val('');

				$('#term_6').val('');
				$('#term_7').val('');
            } else if(applyId == 6){
                $('#term_1').val('');
                $('#term_2').val('');
                $('#term_3').val('');
                $('#term_4').val('');
                $('#term_5').val('');
				// $('#term_6').val('');
				$('#term_7').val('');
            }else if(applyId == 7){
                $('#term_1').val('');
                $('#term_2').val('');
                $('#term_3').val('');
                $('#term_4').val('');
                $('#term_5').val('');
				$('#term_6').val('');
				// $('#term_7').val('');
            }else {
                $('#term_1').val('');
                $('#term_2').val('');
                $('#term_3').val('');
                $('#term_4').val('');
                $('#term_5').val('');
				$('#term_6').val('');
				$('#term_7').val('');
            }
            
            $('#Referral-select').val('');
            $('tbody').html('');
            $('tbody').html(response.livechat);
            // $("#Referral_count").text(response.count);
        
            if (response.links.length > 10) {
                $('ul.pagination').replaceWith(response.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }
        }).fail(function (errObj) {
            alert('No response from server');
            console.log(errObj);
        });
    }
    $('.select2').on('change', function(e){
        e.preventDefault();
        id = $(this).data('id');
        value = $('#term_'+id).val();
        onInput(value,id);
    });
    $(document).on("input", ".filter-apply", function (e) {
        e.preventDefault();
        id = $(this).data('id');
        value = $('#term_'+id).val();
        // alert(value); //filter-apply
        onInput(value,id);
       
    });
    
</script>
@endsection