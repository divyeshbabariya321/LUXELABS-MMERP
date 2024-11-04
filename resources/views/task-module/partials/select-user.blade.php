<select id="{{ $selectBoxId }}" class="form-control {{$selectClass}} select2" data-id="{{$task->id}}" data-lead="1" name="master_user_id" id="user_{{$task->id}}">
    <option value="">Select...</option>
    
    @php 
        if ($type == "assign-user") $masterUser = isset($task->assign_to) ? $task->assign_to : 0; 
        if ($type == "master-user") $masterUser = isset($task->master_user_id) ? $task->master_user_id : 0;
        if ($type == "second-master-user") $masterUser = isset($task->second_master_user_id) ? $task->second_master_user_id : 0;
    @endphp

    @foreach($users as $id=>$name)
        @if( $masterUser == $id )
            <option value="{{$id}}" selected>{{ $name }}</option>
        @else
            <option value="{{$id}}">{{ $name }}</option>
        @endif
    @endforeach
</select>