@if (count($allblogCentralize) > 0)
@foreach ($allblogCentralize as $key => $blogCentrelize)
    <tr >
        <td>{{ $blogCentrelize->id }}</td>
       
        <td>{{$blogCentrelize->title}}</td>
        <td  data-toggle="modal" data-target="#resource-email-description"  style="cursor: pointer;" onclick="showResDescription({{$blogCentrelize->id}})"> {{ substr(strip_tags($blogCentrelize->content), 0,  120) }} {{strlen(strip_tags($blogCentrelize->content)) > 110 ? '...' : '' }}</td>
        <td>{{$blogCentrelize->receive_from}}</td>
        <td>{{$blogCentrelize->created_by}}</td>
        <td>{{ date('l, d/m/Y', strtotime($blogCentrelize['created_at'])) }}</td>
    </tr>
@endforeach
@else
<tr>
    <td class="text-center" colspan="6">No Record found.</td>
</tr>
@endif