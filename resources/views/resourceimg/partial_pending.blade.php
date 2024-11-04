@if (count($allresources) > 0)
    @foreach ($allresources as $key => $resources)
        <tr>
            <td>{{ $resources->id }}</td>
            <td><input type="checkbox" value="{{ $resources->id }}" name="id" class="checkBoxClass" style="height: auto;">
            <td>{{ !empty($resources->category->title) ? $resources->category->title : '' }}</td>
            <td>{{ !empty($resources->category->childs->title) ? $resources->category->childs->title : '' }}</td>
            <td>{{$resources->subject}}</td>
            <td data-toggle="modal" data-target="#resource-email-description"  style="cursor: pointer;" onclick="showResDescription({{$resources->id}})"   style="cursor: pointer;"> {{ substr(strip_tags($resources->description), 0,  120) }} {{strlen(strip_tags($resources->description)) > 110 ? '...' : '' }}</td>
            <td>
                <a href="{{ $resources['url'] }}" title="View Url" target="_blank">{{ isset($resources['url']) ? $resources['url'] : '-' }}</a>
            </td>
            <td> 
                <a href="javascript:void(0)" title="View Images" data-id="{{ $resources->id }}" class="view-resources-center-images"><i class="fa fa-eye" aria-hidden="true"></i></a>
            </td>
            <td>{{ date('l, d/m/Y', strtotime($resources['updated_at'])) }}</td>
            <td>{{ ucwords($resources['created_by']) }}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td class="text-center" colspan="10">No Record found.</td>
    </tr>
@endif
