@foreach($datas as $key => $data)
<tr>
    <td>{{ $key + 1 }}</td>
    <td>{{ $data->remarks ?? ' - ' }}</td>
    <td>{{ $data->user->name ?? ' - ' }}</td>
    <td>{{ $data->created_at }}</td>
</tr>
@endforeach
