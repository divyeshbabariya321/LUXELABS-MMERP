 @if(empty($pages))
    <tr>
        <td colspan="4" style="text-align: center;">
            No Result Found
        </td>
    </tr>
@else

@foreach ($pages as $page)
    <tr>
        <td>{{ $page->id }}</td>
        <td>{{ $page->page }}</td>
        <td>{{ $page->time }}</td>
        <td>{{ $page->user->name }}</td>
        <td>{{ $page->created_at }}</td>
        <td >
           <button class="btn btn-default edit-page" data-id="{{$page->id}}" style="border: none;"><i class="fa fa-edit"></i></button>
            <a href="/system/auto-refresh/{{$page->id}}/delete" onclick="return confirm('Are you sure you want to delete ?')">
            <i class="fa fa-trash"style="color: gray;"></i>
            </a>
        </td>
    </tr>   
@endforeach
@endif