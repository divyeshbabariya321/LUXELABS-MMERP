<!-- Purpose : Add class infinite-scroll - DEVTASK-4271 -->
<div class="infinite-scroll scrapper-python-image customer-count infinite-scroll-data customer-list-{{$website_id}} customer-{{$website_id}}" style="padding: 0px 10px;display: grid">
        @php
            $oldDate = null;
            $count   = 0;
           
            $device = ((isset($_REQUEST['device'])) ? $_REQUEST['device'] : '' );

            if($device == "desktop" || $device == ""){
                 $imageHeight = 'fit-content';
                 $imageWidth = 'infinite-scroll-images';
                 $imageDimensioClass ='image-diamention-rasio-desktop';
                 $width = 'fit-content';
            }
            else if($device == "tablet"){
                 $imageHeight = '800px';
                 $imageWidth = 'infinite-scroll-images-tablet';
                 $imageDimensioClass ='image-diamention-rasio-desktop';
                 $width = '100%';

            }
            else if($device == "mobile"){
                 $imageHeight = '600px';
                 $imageWidth = 'infinite-scroll-images-mobile';
                 $imageDimensioClass ='image-diamention-rasio-mobile';
                 $width = '100%';
            }
            
        @endphp

        <div class="row">
            <div class="col-md-12">
                <br>
                <h5 class="product-attach-date" style="margin: 5px 0px;"> Number Of Images:{{count($images)}}</h5> 

                <hr style="margin: 5px 0px;">
            </div>
        </div>
        <div class="image-1"> 
        @if(!empty($images))
            @foreach($images as $image)
                    <?php $image = $image->toArray();?>                    

                    <?php
                        if ( date( 'Y-m-d' ,strtotime($image['created_at'])) !== $oldDate ) { 
                            $count = 0; 
                            $oldDate = date( 'Y-m-d' ,strtotime($image['created_at']));
                        ?>
                            <!-- <div class="row">
                                <div class="col-md-12">
                                    <br>
                                    <h5 class="product-attach-date" style="margin: 5px 0px;">{{$image['created_at']}} || Number Of Images:{{count($images)}}</h5> 

                                    <hr style="margin: 5px 0px;">
                                </div>
                            </div>  -->

                        <?php } ?>
                        
                    @if ($image['img_name'] )
                        @php
                        if($count == 6){
                            $count = 0;
                        }
                        
                        @endphp                            
                            <div class="{{ $imageWidth }}">               
                                @if ($image['coordinates'])
                                    @php 
                                        $x = 0;
                                        $coordinates = explode(',',$image['coordinates']);
                                        array_push($coordinates,$image['height']);
                                        $total_img_height = $image['height'];
                                    @endphp


                                    <div  style="position: relative;display: flex">
                                        <div class="col-md-11 image-diamention-rasio {{ $imageDimensioClass }}"  style="max-height: {{ $imageHeight }};">
                                            @foreach ($coordinates as $z)
                                            @php
                                                if($device == "mobile"){
                                                    $z = ceil((2372*$z)/$total_img_height);
                                                }
                                                if($device == "tablet"){
                                                    $z = ceil((5070*$z)/$total_img_height);
                                                }
                                            @endphp
                                            <td>
                                                <img data-coordinates="{{ $z}}" class="manage-product-image" src="{{ asset( 'scrappersImages/'.$image['img_name']) }}" style="object-position: 100% -{{ $x }}px;height:{{ $z - $x+20 }}px;width:{{ $width }}">
                                            </td>
                                            @php $x = $z; @endphp
                                        @endforeach
                                        </div>
                                        <div class="buttons_div col-md-1" style="text-align: left; padding-right: 0px;">
                                            <button class="btn btn-secondarys add-remark-button btn-default" data-toggle="modal" data-target="#remark-area-list"><i class="fa fa-comments"></i></button></br>  
                                            <button class="btn btn-secondarys btn-add-action btn-default" data-toggle="modal" data-target="#bugtrackingCreateModal">
                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                            </button></br>       
                                            <a class="btn btn-secondarys btn-default" href="{{$image['img_url']}}" target="_blank">Go to Url</a></br>  
                                            @if($image['url'] != "")
                                            <a class="btn btn-secondarys btn-default" href="{{config('app.url')}}/scrapper-python/image/url_list?flagUrl={{$image['id']}}" target="_blank">Go to Link</a></br>  
                                            @endif
                                            {{ \Carbon\Carbon::parse($image['created_at'])->format('d-m-y') }} </br>

                                            @if($image['si_status']==3 && $image['manually_approve_flag']==0)
                                                <button class="btn btn-secondarys reject-scrap-image btn-default" data-id="{{$image['id']}}" data-type="2">Approve</button>
                                                </br>
                                                <span class="text-danger">image is rejected.</span> 
                                            @elseif($image['si_status']==2 && $image['manually_approve_flag']==0)
                                                <button class="btn btn-secondarys reject-scrap-image btn-default" data-id="{{$image['id']}}" data-type="3">Reject</button> 
                                            @elseif($image['manually_approve_flag']==1 || $image['si_status']==1)
                                                <button class="btn btn-secondarys reject-scrap-image btn-default" data-id="{{$image['id']}}" data-type="2">Approve</button>
                                                <button class="btn btn-secondarys reject-scrap-image btn-default" data-id="{{$image['id']}}" data-type="3">Reject</button> 
                                                </br>
                                                @if($image['manually_approve_flag']==1)
                                                    <span class="text-danger">You need to approve or reject this image manually.</span> 
                                                @endif
                                            @endif

                                            <!-- @if($image['si_status']==3)
                                                <button class="btn btn-secondarys reject-scrap-image btn-default" data-id="{{$image['id']}}" data-type="2">Approve</button>
                                                </br>
                                                <span class="text-danger">image is rejected.</span> 
                                            @elseif($image['manually_approve_flag']==1)
                                                <button class="btn btn-secondarys reject-scrap-image btn-default" data-id="{{$image['id']}}" data-type="2">Approve</button>
                                                <button class="btn btn-secondarys reject-scrap-image btn-default" data-id="{{$image['id']}}" data-type="3">Reject</button> 
                                                </br>
                                                <span class="text-danger">You need to approve or reject this image manually.</span> 
                                            @else 
                                                <button class="btn btn-secondarys reject-scrap-image btn-default" data-id="{{$image['id']}}" data-type="3">Reject</button> 
                                            @endif -->
                                        </div>
                                    </div>
                                @else
                                    <div class="col-md-12 col-xs-12 text-center product-list-card mb-4 p-0" style="position: relative;display: flex">
                                        <div class="product-list-card col-md-11" style="position: relative;padding:0;margin-bottom:2px !important;max-height:{{ $imageHeight }};overflow-y:auto;overflow-x: hidden">

                                            <div data-interval="false" id="carousel_{{ $image['id'] }}" class="carousel slide" data-ride="carousel">
                                                <a href="#" data-toggle="tooltip" data-html="true" data-placement="top" >
                                                    <div class="carousel-inner maincarousel">
                                                        <div class="item" style="display: block;"> <a data-fancybox="gallery" href="{{ urldecode(asset( 'scrappersImages/'.$image['img_name']))}}" ><img src="{{ urldecode(asset( 'scrappersImages/'.$image['img_name']))}}" style="height: 100%; width: 100%; max-width:fit-content; display: block;margin-left: auto;margin-right: auto;"> </a> </div>
                                                    </div>
                                                </a>
                                            </div>

                                            <div class="row pl-4 pr-4" style="padding: 0px; margin-bottom: 8px;">
                            
                                            </div>
                                        </div>
                                        <div class="buttons_div col-md-1" style="text-align: left; padding-right: 0px;">
                                            <button class="btn btn-secondarys add-remark-button btn-default" data-toggle="modal" data-target="#remark-area-list"><i class="fa fa-comments"></i></button></br>  
                                            <button class="btn btn-secondarys btn-add-action btn-default" data-toggle="modal" data-target="#bugtrackingCreateModal">
                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                            </button></br>
                                            <a class="btn btn-secondarys btn-default" href="{{$image['url']}}" target="_blank">Go to Url</a> </br>
                                            @if($image['url'] != "")
                                            <a class="btn btn-secondarys btn-default" href="{{config('app.url')}}/scrapper-python/image/url_list?flagUrl={{$image['id']}}" target="_blank">Go to Link</a>  </br>
                                            @endif
                                            {{ \Carbon\Carbon::parse($image['created_at'])->format('d-m-y') }} </br>

                                            @if($image['si_status']==3 && $image['manually_approve_flag']==0)
                                                <button class="btn btn-secondarys reject-scrap-image btn-default" data-id="{{$image['id']}}" data-type="2">Approve</button>
                                                </br>
                                                <span class="text-danger">image is rejected.</span> 
                                            @elseif($image['si_status']==2 && $image['manually_approve_flag']==0)
                                                <button class="btn btn-secondarys reject-scrap-image btn-default" data-id="{{$image['id']}}" data-type="3">Reject</button> 
                                            @elseif($image['manually_approve_flag']==1 || $image['si_status']==1)
                                                <button class="btn btn-secondarys reject-scrap-image btn-default" data-id="{{$image['id']}}" data-type="2">Approve</button>
                                                <button class="btn btn-secondarys reject-scrap-image btn-default" data-id="{{$image['id']}}" data-type="3">Reject</button> 
                                                </br>
                                                @if($image['manually_approve_flag']==1)
                                                    <span class="text-danger">You need to approve or reject this image manually.</span> 
                                                @endif
                                            @endif

                                            <!-- @if($image['si_status']==3)
                                                <button class="btn btn-secondarys reject-scrap-image btn-default" data-id="{{$image['id']}}" data-type="2">Approve</button>
                                                </br>
                                                <span class="text-danger">image is rejected.</span> 
                                            @elseif($image['manually_approve_flag']==1)
                                                <button class="btn btn-secondarys reject-scrap-image btn-default" data-id="{{$image['id']}}" data-type="2">Approve</button>
                                                <button class="btn btn-secondarys reject-scrap-image btn-default" data-id="{{$image['id']}}" data-type="3">Reject</button> 
                                                </br>
                                                <span class="text-danger">You need to approve or reject this image manually.</span> 
                                            @else 
                                                <button class="btn btn-secondarys reject-scrap-image btn-default" data-id="{{$image['id']}}" data-type="3">Reject</button> 
                                            @endif -->
                                        </div>
                                    </div>
                                @endif
                            </div>
                        

                        

                    @endif
                @php $count++;  @endphp
            
            @endforeach
        </div>
        @else
        <div class="col-md-12">No more Images</div>
        @endif
        <br>
        @if(!empty($images))
        {{ $images->appends(request()->except('page'))->links() }}
        @endif
    
</div>
<img class="infinite-scroll-products-loader center-block" src="{{config('app.url')}}/images/loading.gif" alt="Loading..." style="display: none" />
<!--Remark Modal-->
