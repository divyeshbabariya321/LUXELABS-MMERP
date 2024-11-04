@if($todoLists->count())
    @foreach($todoLists as $todoList)
        <tr>
            <td>{{ $todoList->title }}</td>
            <td>{{ $todoList->subject }}</td>
            <td>{{ isset($todoList->category->name) ? $todoList->category->name : ''; }}</td>
            <td>
                <select name="status" class="form-control" onchange="todoHomeStatusChange({{$todoList->id}}, this.value)" >
                    @foreach ($statuses as $status )
                        <option value="{{$status->id}}" @if ($todoList->status == $status->id) selected @endif>{{$status->name}}</option>
                    @endforeach
                </select>
            </td>
            <td>{{ $todoList->todo_date}}</td>
        </tr>
    @endforeach
@else
    <tr><td colspan="5">No Records</td></tr>
@endif