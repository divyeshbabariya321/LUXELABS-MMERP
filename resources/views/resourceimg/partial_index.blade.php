@if (count($allresources) > 0)
@foreach ($allresources as $key => $resources)

    @php
        $status_color = \App\Helpers\StatusHelper::getResourceStatus($resources['status_id']);
        if ($status_color == null) {
            $status_color = new stdClass();
        }
    @endphp
    <tr style="background-color: {{$status_color->status_color ?? ""}}!important;">
        <td>{{ $resources->id }}</td>
        <td>
            @if (isset($resources->category->title))
                {{ $resources->category->title }}
            @endif
        </td>
        <td>
            @if (isset($resources->sub_category->title))
                {{ $resources->sub_category->title }}
            @endif
        </td>
        <td><a href="{{ $resources['url'] }}" title="View Url"
                target="_blank">{{ isset($resources['url']) ? $resources['url'] : '-' }}</a>
        </td>
        <td>{{$resources->subject}}</td>
        <td  data-toggle="modal" data-target="#resource-email-description"  style="cursor: pointer;" onclick="showResDescription({{$resources->id}})"> {{ substr(strip_tags($resources->description), 0,  120) }} {{strlen(strip_tags($resources->description)) > 110 ? '...' : '' }}</td>
        <td> 
            <a href="javascript:void(0)" title="View Images" data-id="{{ $resources->id }}" class="view-resources-center-images"><i class="fa fa-eye" aria-hidden="true"></i></a>
            <!-- @isset($allresources['image1'])
    				<div class="col-md-6">
		        		<img onclick="OpenModel(this.id)" 
		        			 id="myImg1" class="myImg" src="{{URL::to('/category_images/'.$allresources['image1'])}}" 
		        						alt="{{URL::to('/category_images/'.$allresources['image1'])}}" 
		        						style="width: 50% !important;height: 50px !important;">
		        	</div>
		        	@endisset
		        	@isset($allresources['image2'])
		        	<div class="col-md-6">
		        		<img onclick="OpenModel(this.id)" 
		        			 id="myImg2" class="myImg" src="{{URL::to('/category_images/'.$allresources['image2'])}}" 
		        						alt="{{URL::to('/category_images/'.$allresources['image2'])}}" 
		        						style="width: 50% !important;height: 50px !important;">
		        	</div>
		        	@endisset
            @isset($resources['images'])
                @if ($resources['images'] != null)
                <div class="" style="margin-top: 15px">
                    @foreach (json_decode($resources['images']) as $image)                        
                            <img id="myShowImg" img-id='{{ $resources['id'] }}'
                                src="{{ URL::to('/category_images/' . $image) }}"
                                style="width: auto% !important;height: 50px !important;">
                    @endforeach
                </div>
                @endif
            @endisset -->
        </td>
        <td>
            <div class="d-flex align-items-center">
                <select name="status" class="status-dropdown form-control" data-id="{{$resources['id']}}">
                    <option value="">Select Status</option>
                    @foreach ($ResourceStatus as $stat)
                        <option value="{{$stat->id}}" {{$resources['status_id'] == $stat->id ? 'selected' : ''}}>{{$stat->status_name}}</option>
                    @endforeach
                </select>
                <button type="button" data-id="{{ $resources['id']  }}" class="btn btn-image status-history-show p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>
            </div>
        </td>
        <td>
            <div class="d-flex">
                <input type="text" class="form-control" name="message" placeholder="Remarks" value="" id="remark_{{$resources['id']}}">
                <div class="d-flex p-0">
                    <button class="btn pr-0 btn-xs btn-image " onclick="saveRemarks({{$resources['id']}})"><img src="/images/filled-sent.png"></button>
                    <button type="button" data-id="{{$resources['id']}}" class="btn btn-image remarks-history-show p-0 ml-2" title="Status Histories"><i class="fa fa-info-circle"></i></button>
                </div>
            </div>
        </td>
        <td>{{ date('l, d/m/Y', strtotime($resources['updated_at'])) }}</td>
        <td>{{ ucwords($resources['created_by']) }}</td>
    </tr>
@endforeach
@else
<tr>
    <td class="text-center" colspan="11">No Record found.</td>
</tr>
@endif