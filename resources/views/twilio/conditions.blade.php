@extends('layouts.app')


@section('favicon' , 'productstats.png')


@section('title', 'Twilio Conditions')


@section('content')
    <?php $base_url = URL::to('/'); ?>
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Twilio Conditions ({{$conditions->count()}})</h2>
            <div class="pull-left cls_filter_box pb-4">
              <form class="form-inline" action="{{ route('twilio.conditions') }}" method="GET">
                <div class="pd-2">
                    <div class="form-group">
                        <label for="with_archived">Select Condition</label>
                        <select class="form-control select select2 required" name="condition"  >
                            <option value="">Please select Condition</option>
                            @foreach($drConditions as $condition)
                                @php
                                    $sel = (isset($_GET['condition']) && $_GET['condition'] == $condition->condition)?" selected='selected' ":"";
                                @endphp
                                <option value="{{ $condition->condition }}" {{ $sel }}>{{ $condition->condition }}</option>
                            @endforeach
                        </select>
                    </div>
               </div>
                  <div class="form-group ml-3 cls_filter_inputbox">
                      <label for="with_archived">Search Description</label>
                      <input name="description" type="text" class="form-control" placeholder="Search" id="description-search" value="{{ @$_GET['description'] }}">
                  </div>
                  <br>
                  &nbsp;&nbsp;&nbsp;
                  <div class="pd-2">
                    <div class="form-group">
                        <label for="with_archived">Select Status</label>
                        <select class="form-control select select2 required" name="active_status">
                            <option value="">Please select Condition</option>
                            <option value="1">Enable</option>
                            <option value="0">Disable</option>
                        </select>
                    </div>
                </div>
                <div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
                    <button type="submit" style="margin-top: 20px;padding: 5px;" class="btn btn-image"><img src="<?php echo $base_url; ?>/images/filter.png"/></button>
                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#status-colour-update"> Create Status </button>
                 <br>
                </div>
              </form>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered" id="twilio-condition-list">
                <tr>
                    <th width="10%">#</th>
                    <th width="20%">Condition</th>
                    <th width="50%">Description</th>
                    <th width="10%">Action</th>
                </tr>
                @foreach($conditions as $i=>$condition)   
                    <tr data-id="{{ $condition->id }}" style="background-color: {{$condition->twilioStatusColour?->color}};">
                        <td width="10%">{{ $i+1 }}</td>
                        <td width="20%">
                            {{ $condition['condition'] }}
                        </td>
                        <td width="50%">
                           {{ $condition['description'] }}
                        </td>
                        <td width="10%"> 
                            {{ html()->select('status', [1 => 'Enable', 0 => 'Disable'], $condition['status'])->class('form-control status')->data('id', $condition['id']) }}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection

<div id="status-colour-update" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Status Updates</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Status Name</b></td>
                            <td class="text-center"><b>Color</b></td>
                        </tr>
                        <?php
                       $verified_status = ['enable', 'disable'];
    foreach ($verified_status as $key => $status) { ?>
                        <tr>
                            <td data-status="{{ $status }}">&nbsp;&nbsp;&nbsp;<?php echo $status ?></td>
                            <td>
                                <input type="color" name="color" class="form-control color-input" data-status="{{ $status }}" style="height:30px;padding:0px;">
                            </td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submit-status-color">Save changes</button>
                </div>
        </div>
    </div>
</div>

@section('scripts')
    <script>
        $( ".status" ).change(function() {
            var status = $(this).val();
            var id = $(this).data('id');
            $.ajax({
              url: '{{ url("twilio/conditions/status/update") }}'+'?id='+id+'&status='+status,
              method: 'GET'
            }).done(function(response) {
                $(`#twilio-condition-list tr[data-id="${id}"]`).css('background-color', response.color);
                toastr["success"](response.message, "Message")
            });
        });

            $(".submit-status-color").click(function() {
                alert('sghdf');
                let formData = [];
                $(".color-input").each(function() {
                    let status = $(this).data("status");
                    let colorCode = $(this).val();
                    formData.push({ status: status, colorCode: colorCode });
                });

                // Send formData to the server using AJAX
                $.ajax({
                    url: "{{ route('twilio-status-colour-update') }}",
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    dataType: "json",
                    data: { formData: formData },
                    success: function(response) {
                        // Handle success response if needed
                        console.log(response);
                        toastr["success"](response.message, "Message")
                    },
                    error: function(error) {
                        // Handle error response if needed
                        toastr["error"]("Something went Wrong!", "Message")
                    }
                });
            });

    </script>
@endsection
