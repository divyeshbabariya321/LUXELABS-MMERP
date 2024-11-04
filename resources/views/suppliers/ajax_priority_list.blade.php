@if($supplier_priority_list)
    @foreach($supplier_priority_list as $record)
        <tr>
            <td>{{$record->priority}}</td>
        </tr>
    @endforeach
@endif
