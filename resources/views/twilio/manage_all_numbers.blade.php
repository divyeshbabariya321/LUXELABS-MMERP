@extends('layouts.app')
@section('favicon' , 'productstats.png')
@section('title', 'Twilio Message Tones')
@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Twilio Manage All Numbers</h2>
    </div>
</div>

<table class="table table-bordered table-hover">
    <tr>
        <th>Website</th>
        <th class="text-center">Workspace && Workflow</th>
        <th class="text-center">Message when agent available</th>
        <th class="text-center">Message when agent not available</th>
        <th class="text-center">Message when agent is busy</th>
        <th class="text-center">Message when Working Hours is Over</th>
        <th class="text-center">Category Menu Message</th>
        <th class="text-center">Sub Category Menu Message</th>
        <th class="text-center">Message if Speech Response not available</th>
        <th class="text-center">Action</th>
    </tr>
    
    @foreach ($numbers as $number)
        <tr>
            <td>
                <div class="input-group">
                    <select class="form-control store_websites" id="store_website_1">
                        <option value="">Select store website</option>
                        @if(isset($store_websites))
                            @foreach($store_websites as $websites)
                                <option value="{{ $websites->id }}" @if(isset($number->assigned_stores)) @if($number->assigned_stores->store_website_id == $websites->id) selected @endif @endif>{{ $websites->title }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </td>
            <td>
                <label>Workspace:</label>
                <div class="input-group">
                    <select class="form-control change-workspace" id="workspace_sid_1">
                        <option value="">Select Workspace</option>
                        @if(isset($workspace))
                            @foreach($workspace as $wsp)
                                <option value="{{ $wsp->workspace_sid }}"@if($number->workspace_sid == $wsp->workspace_sid) selected @endif>{{ $wsp->workspace_name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <label>Workflow:</label>
                <div class="input-group">
                    <select class="form-control change-workflow" id="workflow_sid_1">
                        @if(isset($workflow))
                            @foreach($workflow as $wsp)
                                <option value="{{ $wsp->workflow_sid }}" @if($number->workflow_sid == $wsp->workflow_sid) selected @endif>{{ $wsp->workflow_name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </td>
            <td>
                <input type="text" class="form-control" name="message_available" id="message_available_1" value="{{ @$number->assigned_stores->message_available }}"/>
            </td>
            <td>
                <input type="text" class="form-control" name="message_not_available" id="message_not_available_1" value="{{ @$number->assigned_stores->message_not_available }}"/>
            </td>
            <td>
                <input type="text" class="form-control" name="message_busy" id="message_busy_1" value="{{ @ $number->assigned_stores->message_busy }}"/>
            </td>
            <td>
                <input type="text" class="form-control" name="end_work_message" id="end_work_message_1" value="{{ @ $number->assigned_stores->end_work_message }}"/>
            </td>
            <td>
                <input type="text" class="form-control" name="category_menu_message" id="category_menu_message_1" value="{{ @ $number->assigned_stores->category_menu_message }}"/>
            </td>
            <td>
                <input type="text" class="form-control" name="sub_category_menu_message" id="sub_category_menu_message_1" value="{{ @ $number->assigned_stores->sub_category_menu_message }}"/>
            </td>
            <td>
                <input type="text" class="form-control" name="speech_response_not_available_message" id="speech_response_not_available_message_1" value="{{ @ $number->assigned_stores->speech_response_not_available_message }}"/>
            </td>
            <td>
                <button class="btn btn-sm btn-image save-number-to-store" id="save_1" data-number-id="{{ @ $number->id }}"><img src="/images/filled-sent.png" style="cursor: default;"></button>
            </td>
        </tr>
        <tr class="call_forwarding_1">
            <td colspan="3">
                <label>Select Agent</label>
                <div class="input-group">
                    <select class="form-control" id="agent_1">
                        <option value="">Select agent</option> 
                        @if(isset($customer_role_users))
                            @foreach($customer_role_users as $user)
                                @if(isset($user->user))
                                    <option value="{{ $user->user->id }}">{{ $user->user->name }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>
            </td>
            <td colspan="3">
                <button class="btn btn-sm btn-image call_forwarding_save" id="forward_1"><img src="/images/filled-sent.png" style="cursor: default;"></button>
            </td>
        </tr>
    @endforeach
</table>

@endsection

@section('scripts')

<script type="text/javascript">
    $('.save-number-to-store').on("click", function() {
        var key_no = $(this).data("id");
        var pathname = window.location.pathname;

        path_arr = pathname.split('/');
        var credential_id = path_arr[path_arr.length-1];

        var selected_no = $(this).attr('id');
        selected_no = selected_no.split('_');
        var num_id = selected_no[1];
        $.ajax({
            url: '{{ route('assign-number-to-store-website') }}',
            method: 'POST',
            data: {
                '_token' : "{{ csrf_token() }}",
                'twilio_number_id' : $(this).data('number-id'),
                'store_website_id' : $('#store_website_'+num_id).val(),
                'message_available' : $('#message_available_'+num_id).val(),
                'message_not_available' : $('#message_not_available_'+num_id).val(),
                'message_busy' : $('#message_busy_'+num_id).val(),
                'end_work_message' : $('#end_work_message_'+num_id).val(),
                'category_menu_message' : $('#category_menu_message_'+num_id).val(),
                'sub_category_menu_message' : $('#sub_category_menu_message_'+num_id).val(),
                'speech_response_not_available_message' : $('#speech_response_not_available_message_'+num_id).val(),
                'credential_id' : credential_id,
                "workspace_sid" :$('#workspace_sid_'+num_id).val(),
                "workflow_sid" :$('#workflow_sid_'+num_id).val()
            }
        }).done(function(response) {
            if(response.status == 1) {
                toastr['success'](response.message);
                location.reload();
            } else { 
                toastr['error'](response.message);

            }
            console.log(response);
        });
    });

    $('.call_forwarding_save').on("click", function(){ 
        var selected_no = $(this).attr('id');
        selected_no = selected_no.split('_');
        var num_id = selected_no[1];
        var agent_id = $('#agent_'+num_id).val();
        if(agent_id == '' || agent_id == 'undefined') {
            alert('Please select agent');
            return false;
        }
        $.ajax({
            url: '{{ route('manage-twilio-call-forward') }}',
            method: 'POST',
            data: {
                '_token' : "{{ csrf_token() }}",
                'twilio_account_id' : '{{ $account_id }}',
                'twilio_number_id' : num_id,
                'agent_id' : agent_id
            }
        }).done(function(response) {
            if(response.status == 1){
                toastr['success'](response.message);
            }else{
                toastr['error'](response.message);
            }
        });
    });

    $(".change-workspace").on("change", function() {
        changeWorkflow($(this).val(), null)
    })

    function changeWorkflow(workspace, workflow) {
        $.ajax({
            url: "{{ route('get-workflow-list') }}",
            type: 'POST',
            data : {
                _token :  "{{ csrf_token() }}",
                workspace_sid : workspace,
            },
            success: (res) => {
                let html = '<option>Select Workflow</option>';
                res.workflows.forEach((item) => {
                    html += `<option value="${item.workflow_sid}" ${item.workflow_sid == workflow ? 'selected' : ''}>${item.workflow_name}</option>`
                })
                $('.change-workflow').html(html)
            }
        })
    }
</script>
@endsection