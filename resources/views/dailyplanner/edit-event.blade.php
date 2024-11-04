@extends('layouts.app')

@section('title', 'Daily Planner')

@section('styles')
  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
@endsection

@section('content')

@include('partials.flash_messages')

				<div class="row justify-content-center pt-5">
					<div class="col-md-6">
						<form id="edit-notification-submit-form" action="<?php echo route('calendar.event.update') ?>" method="post">
							@csrf    
							<div class="form-group">
								<label for="notification-date">Date</label>
								<input id="edit-notification-date" name="date" value="{{ $edit->date ?? null }}" class="form-control" type="text">
							</div>
							<input type="hidden" name="daily_activity_id" value="{{ $edit->daily_activity_id }}">
							<input type="hidden" name="edit_id" value="{{ $edit->id ?? null }}">
							<div class="form-group">
								<label for="notification-time">Time</label>
								<input id="edit-notification-time" name="time" value="{{ date('H:i', strtotime($edit->start)) }}" class="form-control" type="text">
							</div>    
							<div class="row">
							</div> 
							<div class="row">
							</div>
							<div class="form-group">
								<label for="notification-subject">Subject</label>
								<input id="notification-subject" name="subject" value="{{ $edit->subject ?? null }}" class="form-control" type="text">
								<span id="subject_error" class="text-danger"></span>
							</div>
							<div class="form-group">
								<label for="notification-description">Description</label>
								<input id="notification-description" name="description" value="{{ $edit->description ?? null }}" class="form-control" type="text">
								<span id="description_error" class="text-danger"></span>
							</div>
							<div class="form-group">
								<label for="notification-participants">Participants(vendor)</label>
								<select name="vendors[]" id="vendors" class="form-control selectx-vendor" multiple style="width:100%">
									@foreach ($vendors as $key => $item)
										<option value="{{ $key }}" {{ in_array( $key , $vendor) ? 'selected' : '' }}> {{ $item }} </option>
									@endforeach
								</select>
								
							</div>
							<div class="form-group">
								<label for="check"> Edit all next recurring events </label>
								<input type="checkbox" class="" name="edit_next_recurring" value="1" id="check">
							</div>
							<div class="form-group">
								<input id="edit-notification-submit" class="btn btn-secondary" type="submit">
							</div>
					   </form> 

					</div>
			
@endsection
@section('scripts')
  {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script> --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
		$('#edit-notification-date').datetimepicker({
			format: 'YYYY-MM-DD'
      	});

		$('#edit-notification-time').datetimepicker({
			format: 'HH:mm'
		});
    });
  </script>
@endsection