@foreach($keywords as $keyword)
<tr>
    <td>{{$keyword->id}}</td>
    <td>{{$keyword->keyword}}</td>
    <td>{{$keyword->created_at}}</td>
    <td>
    <div class="d-flex justify-content-between">
        {{ html()->form('DELETE', route('ad-group-keyword.deleteKeyword', [$campaignId, $keyword['google_adgroup_id'], $keyword['google_keyword_id']]))->style('display:inline')->open() }}
        <button type="submit" class="btn-image"><img src="/images/delete.png"></button>
        {{ html()->form()->close() }}
    </div>
    </td>
</tr>
@endforeach 