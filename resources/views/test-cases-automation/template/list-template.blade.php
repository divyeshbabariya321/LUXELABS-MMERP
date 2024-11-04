<div class="mt-3">
    <div class="text-left">
        {{ $test_cases->links() }}
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">

            <thead>
                <tr>
                    <th width="4%">
                        {{-- <input type="checkbox" id="chkTaskDeleteCommon" name="chkTaskDeleteCommon"
                            class="chkTaskDeleteCommon" /> --}}
                        ID
                    </th>
                    <th width="6%">Date</th>
                    <th width="7%">Name</th>
                    <th width="6%">Suite</th>
                    <th width="6%">Module</th>
                    <th width="5%">Step To Reproduce</th>
                    <th width="7%">Expected Result</th>
                    <th width="7%">Website Name</th>
                    <th width="7%">Website</th>
                    <th width="7%">Command</th>
                    <th width="7%">Action</th>
                </tr>
            </thead>
            <tbody class="pending-row-render-view infinite-scroll-pending-inner">
                @foreach ($test_cases as $key => $test_case)
                    <tr>
                        <td class='break'>
                            {{-- <input type="checkbox" id="chkTaskChange{{ $test_case->id }}" name="chkTaskNameChange[]"
                                class="chkTaskNameClsChange" value="{{ $test_case->id }}" /></br> --}}
                            {{ $test_case->id }}
                        </td>
                        <td class='break'>{{ $test_case->created_at }}</td>
                        <td class='break'>{{ $test_case->name }}</td>
                        <td class='break'>{{ $test_case->suite }}</td>
                        <td class='break'>{{ $test_case->module_id }}</td>
                        
                        <td class='break expand-row-msg' data-name="step_to_reproduce" data-id="{{ $test_case->id }}">
                            <span
                                class="show-short-Steps to reproduce-{{ $test_case->id }}">{{ $test_case->step_to_reproduce_short }}</span>
                            <span
                                class="show-full-step_to_reproduce-{{ $test_case->id }} hidden">{{ $test_case->step_to_reproduce }}</span>
                        </td>
                        <td class='break'>{{ $test_case->expected_result }}</td>
                        
                        <td class='break'>{{ $test_case->website }}</td>
                        <td class='break'>{{ $test_case->website_url }}</td>
                        <td class='break'>
                            @if (!empty($test_case->command))
                                {{ $test_case->command }}
                                <div class="btn btn-default delete-command" data-url="{{ url('/test-case-automation/delete-command/'.$test_case->id) }}" href="return:void(0)" title="Delete token">
                                    <i class="fa fa-trash"></i>
                                </div>   
                            @else                                
                                <button class="btn btn-secondary ml-2 btn-add-command" data-target="#addCommand" data-toggle="modal" data-id="{{ $test_case->id }}" data-url="{{ $test_case->website_url }}" data-name="{{ $test_case->website }}">Add Command</button></td>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex">
                                @if (!empty($test_case->command))
                                    
                                        <button type="button" title="Process Command" data-id="{{ $test_case->id }}"
                                            class="btn btn-primary btn-process-command">
                                            <i class="fa fa-terminal" aria-hidden="true"></i>
                                        </button>
                                @endif
                                @if (!empty($test_case->html_file_path))
                                    <a href="{{ route('test-case-automation.view-result',$test_case->id) }}" title="View Result"
                                        class="ml-2 btn btn-primary btn-view-result">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                @endif
                            </div>
                            <div class="d-flex hidden">
                                <button type="button" title="Edit" data-id="{{ $test_case->id }}" data-action="edit"
                                    class="btn btn-edit-template openTestCasesModal">
                                    <i class="fa fa-edit" aria-hidden="true"></i>
                                </button>
                                <button type="button" title="Push" data-id="{{ $test_case->id }}"
                                    class="btn btn-push">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </button>

                                <button type="button" title="Delete" data-id="{{ $test_case->id }}"
                                    class="btn btn-delete-template">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="text-left">
        {{ $test_cases->links() }}
    </div>
</div>
<!--Add Account Modal -->
<div class="modal fade" id="addCommand" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
   <div class="modal-content">
       <div class="modal-header">
           <h3 class="modal-title" id="exampleModalLabel">Add Command</h3>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
           </button>
       </div>
       @include('test-cases-automation.template.add-command')
   </div>
</div>
</div>
<script>
    $('.btn-add-command').on('click',function(){
        $('#test_case_id').val(($(this).data('id')))
        $('#site_to_use').val(($(this).data('url')))
        $('#website_name').val(($(this).data('name')))
    });
    $('.btn-process-command').on('click',function(){
        var id = $(this).data('id');
        $.ajax({
            type: 'GET',
            url: "/test-case-automation/run-command/"+id,
            beforeSend: function () {
                $("#loading-image").show();
            },
            dataType: "json"
        }).done(function (response) {
            $("#loading-image").hide();
            if(response.code == 1){
                toastr['success'](response.message, 'success');
            }else{
                toastr['error'](response.message, 'error');
            }
        });
    });
    $(document).on('click','.delete-command',function(){
        if(confirm("Are you sure you want to delete this command?")){
            var api_url = $(this).data('url')
            $.ajax({
                method: "GET",
                url: api_url,
                data: [],
                dataType: "json",
                success: function(response){
                    if (response.status == true) {
                        toastr.success('Command Deleted Successfully');
                        location.reload();
                    } else {
                        toastr.error(response.message);
                    }
                    
                },
                beforeSend: function() {
                },
                error: function() {
                    alert("There was an error sending the message...");
                }
                
            });
        }

        return false;
    });
</script>