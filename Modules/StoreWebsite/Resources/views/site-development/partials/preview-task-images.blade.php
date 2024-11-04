@foreach($records as $key => $record)
<tr>
    <td>{{$key + 1}}</td>
    <td>
    @if($record['isImage'])
    <img class="zoom-img" style="max-height:150px;max-width:100%;" src="{{$record['url']}}" alt="">
    @else 
        <p style="word-break: break-all">{{$record['url']}}</p>
     @endif   
    </td>
    <td>
    <span style="display: flex">
        <select name="" id="" class="form-control send-message-to-id" style="margin-bottom:10px;">
            <option value="" > Select User </option>
            @foreach($record['userList'] as $key => $u)
            <option value="{{$key}}" > {{$u}} </option>
            @endforeach
        </select>
        &nbsp;<a class=" link-send-document" title="forward to" data-id="{{$record['id']}}" data-media-id="{{$record['media_id']}}"><i class="fa fa-forward" aria-hidden="true" style="margin-top:10px; margin-left:10px;"></i></a>
    </span>

    <span style="display: flex">
        {{-- <select name="" id="" class="form-control select-multiple globalSelect2 send-message-to-id"> --}}
            <select class="form-control globalSelect2 send-task-to-id" id="selector_id"  data-ajax="{{ route('select2.tasks',['sort'=>true]) }}">
            <option value="" > Select Task </option>
          
        </select>
        &nbsp;<a class=" link-send-task" title="forward to"  data-id="{{$record['id']}}" data-media-id="{{$record['media_id']}}"><i class="fa fa-forward" aria-hidden="true" style="margin-top:10px; margin-left:10px;"></i></a>
    </span>

    </td>
    <td>{{$record['userName']}}</td>
   <td>{{$record['created_at']}}</td>
     <td>
    <a class="btn-secondary" href="{{$record['url']}}" target="__blank"><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;
    <a class="btn-secondary send-to-sop-page" data-id="{{$record['id']}}" data-media-id="{{$record['media_id']}}"><i style="margin-left: 5px; margin-right:5px;" title="Add To Sop">+</i></a>&nbsp;
    <a class="btn-secondary previewDoc" @if($record['isImage'])data-type="image" data-docUrl="{{$record['url']}}" @else data-type="doc" data-docUrl="{{urlencode($record['url'])}}" @endif><i class="fa fa-eye" aria-hidden="true"></i></a>
   
	<!--<a class="btn-secondary previewDoc" data-type="doc" data-docUrl="https://erpdev1.theluxuryunlimited.com/uploads/product/29/297558/6103a7f0650f7_60d0961505250_docker-erp-setup (2).doc" ><i class="fa fa-eye" aria-hidden="true"></i></a>
    -->
    
    {{-- &nbsp;<a class="btn-secondary link-send-document" title="forward to" data-id="{{$record['id']}}" href="_blank"><i class="fa fa-forward" aria-hidden="true"></i></a> --}}
    <a class="btn-secondary save-document-as" title="Save a copy of document in site assets" data-id="{{$record['id']}}"><i class="fa fa-forward" aria-hidden="true"></i></a>
    </td>
</tr>
@endforeach

