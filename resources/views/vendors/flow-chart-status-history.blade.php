@foreach($datas as $key => $data)
    <tr>
        <td>{{$key+1}}</td>
        <td>{{(!is_null($data->old_value))?$data->old_value->status_name:'-'}}</td>
        <td>{{(!is_null($data->new_value))?$data->new_value->status_name:'-'}}</td>
        <td>{{(isset($data->user))?$data->user->name:'-'}}</td>
        <td>{{$data->created_at}}</td>
    </tr>
@endforeach


