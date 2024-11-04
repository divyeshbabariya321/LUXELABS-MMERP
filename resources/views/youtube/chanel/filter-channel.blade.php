@foreach($googleadsaccount as $googleadsac)
<tr>
    <td>{{$loop->iteration}}</td>
    <td>{{$googleadsac->chanel_name}}</td>
    <td>{{$googleadsac->store_websites}}</td>
    <td>{{$googleadsac->status}}</td>
    <td>{{$googleadsac->created_at}}</td>
    <td>
        <button type="button" onclick="editaccount('{{$googleadsac->id}}')" class="btn-image" data-toggle="modal" data-target="#EditModal"><img src="{{asset('/images/edit.png')}}"></button>
    </td>
</tr>
@endforeach