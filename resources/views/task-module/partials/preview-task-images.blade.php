@foreach($records as $key => $record)
<tr>
    <td>{{$key + 1}}</td>
    <td>
    @if($record['isImage'])
    <img class="zoom-img" style="max-height:150px;max-width:100%;" src="{{$record['url']}}" alt="">
    @else 
        <span style="word-break: break-all">{{$record['url']}}</span>
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
            <select class="form-control globalSelect2 send-task-to-id" id="selector_id"  data-ajax="{{ route('select2.tasks',['sort'=>true]) }}">
            <option value="" > Select Task </option>
          
        </select>
        &nbsp;<a class=" link-send-task" title="forward to"  data-id="{{$record['id']}}" data-media-id="{{$record['media_id']}}"><i class="fa fa-forward" aria-hidden="true" style="margin-top:10px; margin-left:10px;"></i></a>
    </span>

    </td>
    <td>{{$record['userName']}}</td>
   <td>{{$record['created_at']}}</td>
     <td>
    <a class="" href="{{$record['url']}}" target="__blank" style="color: #333333"><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;
    <a class="send-to-sop-page ml-3 mr-3" data-id="{{$record['id']}}" data-media-id="{{$record['media_id']}}"><i class="fa fa-plus-square" aria-hidden="true" title="Add To Sop"></i></a>
    <a class="previewDoc" @if($record['isImage'])data-type="image" data-docUrl="{{$record['url']}}" @else data-type="doc" data-docUrl="{{urlencode($record['url'])}}" @endif><i class="fa fa-eye" aria-hidden="true"></i></a>
   
	<!--<a class="btn-secondary previewDoc" data-type="doc" data-docUrl="https://erpdev1.theluxuryunlimited.com/uploads/product/29/297558/6103a7f0650f7_60d0961505250_docker-erp-setup (2).doc" ><i class="fa fa-eye" aria-hidden="true"></i></a>
    -->
    </td>
</tr>
@endforeach

