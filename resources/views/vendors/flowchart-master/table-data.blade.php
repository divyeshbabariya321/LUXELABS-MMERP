@foreach($flowchart_master as $flowchart)
    <tr>
        <td>{{ $flowchart->id}}</td>
        <td>{{ $flowchart->title}}</td>
        <td></td>
    </tr>
@endforeach