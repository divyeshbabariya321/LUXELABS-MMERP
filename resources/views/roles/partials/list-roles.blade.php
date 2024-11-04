@foreach ($roles as $key => $role)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ $role->name }}</td>
        <td>
            <button type="button" class="btn  btn-image view" value="{{$role->id}}" data-toggle="modal" data-target="#view_model" id="view"><img src="{{asset('/images/view.png')}}"/></button>
            @if(auth()->user()->isAdmin())
                <button type="button" class="btn  btn-image edit" value="{{$role->id}}" data-toggle="modal" data-target="#edit_model" id="edit"><img src="{{asset('/images/edit.png')}}"/></button>
            @endif            
        </td>
    </tr>
@endforeach
