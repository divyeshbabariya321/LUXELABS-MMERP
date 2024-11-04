 @if(empty($managers))

            <tr>
                <td colspan="6" style="text-align: center;">
                    No Result Found
                </td>
            </tr>
@else

@foreach ($managers as $manager)
    @php
        $mainCategory='-';
        if($manager->category_parent_id !=0){
            $mainCategory = getCategory($manager->category_parent_id);
        }
    @endphp
    <tr>
        <td>{{ $mainCategory}}</td>
        <td>{{ $manager->category}}</td>
        <td>{{ $manager->erp_size}}</td>
        <td>{{ $manager->sizes }}</td>
        <td>{{ date('d-m-Y',strtotime($manager->created_at)) }}</td>
        <td>{{ date('d-m-Y H:i:s', strtotime($manager->updated_at))}}</td>
        <td>
            <button class="btn btn-default editmanager" data-id="{{$manager->id}}"><i class="fa fa-edit"></i></button>
            <button class="btn btn-default deletemanager" data-id="{{$manager->id}}"><i class="fa fa-trash"></i></button>
            <button class="btn btn-default push-system-size" data-id="{{$manager->id}}"><i class="fa fa-upload" title="Push System Size"></i></button>
        </td>
    </tr>   
@endforeach
@endif