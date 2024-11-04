@foreach($datas as $index => $remark)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $remark->remarks ?? ' - ' }}</td>
        <td>{{ $remark->user->name ?? ' - ' }}</td>
        <td>{{ $remark->created_at }}</td>
    </tr>
@endforeach
