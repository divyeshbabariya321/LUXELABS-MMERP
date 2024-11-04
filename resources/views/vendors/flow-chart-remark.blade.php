@foreach($datas as $index => $data)
    <tr>
        <td>{{$index+1}}</td>
        <td>{{(!is_null($data->remarks))?$data->remarks:'-'}}</td>
        <td>{{(!is_null($data->user))?$data->user:'-'}}</td>
        <td>{{$data->created_at}}</td>
    </tr>
@endforeach


