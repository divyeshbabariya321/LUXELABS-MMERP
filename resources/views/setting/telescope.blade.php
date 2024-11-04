@extends('layouts.app')

@section('styles')
  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
@endsection
@section('large_content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            @endif

			<div class="panel-group" style="margin-bottom: 5px;">
                <div class="panel mt-3 panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                           Telescope Settings
                        </h4>
                    </div>
					<div class="panel-body">
                        <form action="{{ url('settings/telescope/update') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <strong>Enable Telescope:</strong>
                                <input type="checkbox" name="telescope_enabled" id="telescope_enabled" class="form-control" type="checkbox" value="1" style="width: 5%;" {{ (!empty($setting) && $setting->val == 1 ? 'checked' : '') }}>
                                @if ($errors->has('telescope_enabled'))
                                    <div class="alert alert-danger">{{$errors->first('telescope_enabled')}}</div>
                                @endif
                            </div>
                            
				            <button type="submit" class="btn btn-secondary">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>
@endsection


@section('scripts')
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	$('.edit_setting').on('click', function() {
		var id = $(this).data('id');
		$("#setting_id").val(id);
		$("#setting_name").val($("#row_"+id+" td.name").text());
		$("#setting_val").val($("#row_"+id+" td.val").text());
		$("#setting_type").val($("#row_"+id+" td.type").text());
		$("#setting_welcome_message").text($("#row_"+id+" td.msg").text());
    });
	
	$('.close-setting').on('click', function() {
		console.log("hello");
		$("#setting_id, #setting_name, #setting_val, #setting_type").val('');
		$("#setting_welcome_message").text('');
	});
	
});
</script>
@endsection
