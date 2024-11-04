@php
    $special_learning = $learning;
    // $user = App\User::find($learning->learning_user);
    // $provider = App\User::find($learning->learning_vendor);
    // $module = App\LearningModule::find($learning->learning_module);
    // $submodule = App\LearningModule::find($learning->learning_submodule);
    // $assignment = App\Contact::find($learning->learning_assignment);
    // $status = App\TaskStatus::find($learning->learning_status);
@endphp
<tr class="learning_and_activity" data-id="{{ $learning->id }}">
    <td>{{ $learning->id }}</td>
    <td>{{ $learning->created_at->format('m/d/Y') }}</td>
    <td>
        <select class="form-control updateUser" name="user">
            @foreach($usersForView as $user)
                <option value="{{ $user->id }}" {{ $learning->learning_user == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <select class="form-control updateProvider" name="provider">
            @foreach($usersForView  as $provider)
                <option value="{{ $provider->id }}" {{ $learning->learning_vendor == $provider->id ? 'selected' : '' }}>{{ $provider->name }}</option>
            @endforeach
        </select>
    </td>
    <td><div style="display: flex"><input type="text" class="form-control send-message-textbox" name="learning_subject" value="{{ $learning->learning_subject }}"> <img src="{{asset('/images/filled-sent.png')}}" class="updateSubject"style="cursor: pointer; object-fit: contain; height: auto; width: 16px; margin-left: 4px;"></div></td>
    <td>
        <select class="form-control updateModule" name="module">
            @foreach($modulesForView as $module)
                <option value="{{ $module->id }}" {{ $learning->learning_module == $module->id ? 'selected' : '' }}>{{ $module->title }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <select class="form-control updateSubmodule" name="submodule">
            <option value="">Select</option>
            @foreach($submodulesForView as $submodule)
                <option class="submodule" value="{{ $submodule->id }}" {{ $learning->learning_submodule == $submodule->id ? 'selected' : '' }}>{{ $submodule->title }}</option>
            @endforeach
        </select>
    </td>
    <td><div style="display: flex"><input type="text" class="form-control send-message-textbox" name="learning_assignment" value="{{ $learning->learning_assignment }}" maxlength="15"> <img src="{{asset('/images/filled-sent.png')}}" class="updateAssignment" style="cursor: pointer; object-fit: contain; height: auto; width: 16px; margin-left: 4px;"></div></td>

    <td>
        <div style="display: flex">
            <input style="min-width: 30px;" placeholder="E.Date" 
                value="{{ $learning->learning_duedate }}" 
                type="text" 
                class="form-control learning-overdue-datetime due-date-update" 
                name="due_date_{{$learning->id}}" 
                data-id="{{$learning->id}}" 
                id="due_date_{{$learning->id}}"
            >
               
            
            <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-due-history" title="Show Due Date" data-learningid="{{ $learning->id }}"><i class="fa fa-info-circle"></i></button>
        </div>    
    </td>

    <td>
        <div style="display: flex">
        <select class="form-control updateStatus" name="status">
            @foreach($statusesForView as $status)
                <option value="{{ $status->id }}" {{ $learning->learning_status == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
            @endforeach
        </select>

        <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-time-history" title="Show History" data-learningid="{{ $learning->id }}"><i class="fa fa-info-circle"></i></button>
        </div>
    </td>
    <td class="communication-td">
        <!-- class="expand-row" -->
      
       
        <input type="text" class="form-control send-message-textbox" data-id="{{$learning->id}}" id="send_message_{{$learning->id}}" name="send_message_{{$learning->id}}" style="margin-bottom:5px;width:70%;display:inline;"/>
       
        <button style="display: inline-block;padding:0px;" class="btn btn-sm btn-image send-message-open" type="submit" id="submit_message"  data-id="{{$learning->id}}" ><img src="{{asset('/images/filled-sent.png')}}"/></button>
        <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='learning' data-id="{{ $learning->id }}" style="mmargin-top: -0%;margin-left: -2%;" title="Load messages"><img src="{{asset('/images/chat.png')}}" alt=""></button>
        
        <div class="expand-row-msg" data-id="{{$learning->id}}">
            <span class="td-full-container-{{$learning->id}} hidden">
                
                <br>
                <div class="td-full-container">
                    <button class="btn btn-secondary btn-xs" onclick="sendImage({{ $learning->id }})">Send Attachment</button>
                    <button class="btn btn-secondary btn-xs" onclick="sendUploadImage({{$learning->id}})">Send Images</button>
                    <input id="file-input{{ $learning->id }}" type="file" name="files" style="display: none;" multiple/>
                </div> 
            </span>
        </div>
    </td>

    <td>
        <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Learningbtn('{{$learning->id}}')"><i class="fa fa-arrow-down"></i></button>
    </td>

    
</tr>

<tr>

</tr>
<tr class="action-learningbtn-tr-{{$learning->id}} d-none">
    <td class="font-weight-bold">Action</td>
    <td colspan="11">
        
        <button type="button"  data-id="{{ $learning->id }}" class="btn btn-xs btn-file-upload pd-5 p-0">
            <i class="fa fa-upload" aria-hidden="true"></i>
        </button>
        
        <a href="{{ route('learning.show', $learning->id) }}" class="btn btn-xs btn-image pd-5 p-0 mt-auto" href=""><img src="{{asset('images/view.png')}}"/></a>
        
    </td>
</tr>

<script>
    function Learningbtn(id){
        $(".action-learningbtn-tr-"+id).toggleClass('d-none')
    }
</script>
