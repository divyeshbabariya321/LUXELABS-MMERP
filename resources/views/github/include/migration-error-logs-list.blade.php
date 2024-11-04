@foreach($gitDbError as $dbError)
<tr>
    <td>{{$dbError['id']}}</td>
    <td class="expand-row" style="overflow-wrap: anywhere;">
        <span class="td-mini-container">
            {{ strlen( $dbError['branch_name'] ) > 30 ? substr( $dbError['branch_name'] , 0, 30).'...' :  $dbError['branch_name'] }}
        </span>
        <span class="td-full-container hidden">
            {{$dbError['branch_name']}}
        </span>
    </td>
    <td class="expand-row" style="overflow-wrap: anywhere;">
        <span class="td-mini-container">
            {{ strlen( $dbError['error'] ) > 150 ? substr( $dbError['error'] , 0, 150).'...' :  $dbError['error'] }}
        </span>
        <span class="td-full-container hidden">
            {{$dbError['error']}}
        </span>
    </td>
</tr>
@endforeach