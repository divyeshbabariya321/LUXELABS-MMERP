@foreach($mailings as $item)
    <tr>
        <td>{{$item["name"]}}</td>
        <td>{{$item["mail_tpl"]}}</td>
        <td>{{$item["subject"]}}</td>
        <td>{{$item["static_template"]}}</td>
        <td>{{$item->category !== null ? $item->category->title : '-' }}</td>
        <td>{{$item->storeWebsite !== null ? $item->storeWebsite->title : '-' }}</td>
    <!-- <td>{{$item["image_count"]}}</td> -->
    <!-- <td>{{$item["text_count"]}}</td> -->
        <td>
            @if($item['example_image'])
                <img style="width: 100px" src="{{ asset($item['example_image']) }}">
            @endif
        </td>
        <td>{{$item["salutation"]}}</td>
        <td>{{$item["introduction"]}}</td>
        <td>
            @if($item['logo'])
                <img style="width: 100px" src="{{ asset($item['logo']) }}">
            @endif
        </td>
        <td>
            <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn({{$item["id"]}})"><i class="fa fa-arrow-down"></i></button>
        </td>
    </tr>
    <tr class="action-btn-tr-{{$item["id"]}} d-none">
        <td colspan="10">
            <a data-id="{{ $item['id'] }}" class="delete-template-act" href="javascript:;">
                <i class="fa fa-trash"></i>
            </a>
            | <a data-id="{{ $item['id'] }}" data-storage="{{ $item }}" class="edit-template-act" href="javascript:;">
                <i class="fa fa-edit"></i>
            </a>
        </td>
    </tr>
@endforeach

