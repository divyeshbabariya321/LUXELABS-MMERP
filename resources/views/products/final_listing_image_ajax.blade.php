@php $imageCropperRole = Auth::user()->hasRole('ImageCropers');

$categoryArray = [];
foreach ($categoryAll as $category) {
    $categoryArray[] = array('id' => $category->id, 'value' => $category->title);
    // $childs = Category::where('parent_id', $category->id)->get();
    foreach ($category->childs as $child) {
        $categoryArray[] = array('id' => $child->id, 'value' => $category->title . ' > ' . $child->title);
        // $grandChilds = Category::where('parent_id', $child->id)->get();
        if ($child->childLevelSencond != null) {
            foreach ($child->childLevelSencond as $grandChild) {
                $categoryArray[] = array('id' => $grandChild->id, 'value' => $category->title . ' > ' . $child->title . ' > ' . $grandChild->title);
            }
        }
    }
} 
$categoryArray = collect($categoryArray)->pluck("value", "id")->toArray();
@endphp
<table class="table table-bordered table-striped" style="table-layout:fixed;">
        @foreach ($products as $key => $product)
        @php 
            $websiteList = $product->getWebsites();
            $gridImage = $product->getCroppingGridImageByCategoryId($product->category);
        @endphp
        <thead productid="{{ $product->id }}">
            <tr>
                <th>#{{ $product->id }} [{{$product->sku}}] {{$product->name}}
                    <p class="card-text"></p>
                    <div class="row" style="float:left;">
                        <div class="col-md-9">
                            {{ html()->select("category", $categoryArray, $product->category)->class("form-control change-category-product")->id("category_" . $product->id)->data('product-id', $product->id) }}
                         </div>
                    </div>
                    <div class="row" style="float:right;">
                        <div class="col-md-8">
                                @if(count($product->croppedImages) > 0)
                                     <button type="button" value="reject" id="reject-all-cropping{{$product->id}}" data-product_id="{{$product->id}}" class="btn btn-xs btn-secondary pull-right reject-all-cropping">
                           Reject All - Re Crop </button>
                                @else 
									@if($product->status_id == 4)  <button type="button" value="reject" class="btn btn-xs btn-secondary pull-right">
                           Rejected </button> <br> <br>@endif
                                     <button type="button" value="reject" id="reject-all-cropping{{$product->id}}" data-product_id="{{$product->id}}" class="btn btn-xs btn-secondary pull-right reject-all-cropping">
                           All Rejected - Re Crop </button>
                                @endif
                            </button>
                         </div>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody productid="{{ $product->id }}">
            <tr>
                <td>
                    
                    <div class="row"> 
                            @if(!$websiteList->isEmpty())
                                @foreach($websiteList as $index => $site)
                                    <div class="col-md-12 site_list_box" productid="{{ $product->id }}" siteid="{{ $site->id }}">
                                            @php
                                                $tag        = 'gallery_'.$site->cropper_color;
                                                $testing    = false;
                                            @endphp
                                            @if ($product->hasMedia($tag))
                                            <h5 style="text-decoration: underline; width: 100%;">{{ $site->title }} {{ $site->id }}</h5>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button type="button" value="reject" id="reject-product-cropping{{$site->id}}{{$product->id}}" data-product_id="{{$product->id}}" data-site_id="{{$site->id}}" class="btn btn-xs btn-secondary pull-right reject-product-cropping">
                                                        @if(count($product->croppedImages) > 0)
                                                            Reject All - Re Crop for this website
                                                        @else 
                                                            All Rejected - Re Crop for this website
                                                        @endif
                                                    </button>
                                                    @if(request("submit_for_image_approval") =="on" )
                                                        Last Approved By : {{isset($users_list[$product->last_approve_user])?$users_list[$product->last_approve_user]:""}}
                                                    @endif
                                                    @if(request("rejected_image_approval") =="on" )
                                                   
                                                        Rejected By :  {{isset($users_list[$product->rejected_user_id])?$users_list[$product->rejected_user_id]:""}}
                                                        Rejected Time : {{isset($product->rejected_date)?$product->rejected_date:""}}
                                                    @endif
                                                </div>   
                                            </div>
                                            
                                                @foreach($product->getMedia($tag) as $media)
                                                    @if(strpos($media->filename, 'CROP') !== false || $testing == 1)
                                                        <?php
                                                            $width = 0;
                                                            $height = 0;
                                                            if (file_exists($media->getAbsolutePath())) {
                                                                list($width, $height) = getimagesize($media->getAbsolutePath());
                                                                $badge = "notify-red-badge";
                                                                if ($width == 1000 && $height == 1000) {
                                                                    $badge = "notify-green-badge";
                                                                }
                                                            } else {
                                                                $badge = "notify-red-badge";
                                                            }
                                                        ?>    
                                                        <div class="col-md-2 col-xs-4 text-center product-list-card mb-4 single-image-{{ $product->id }}_{{ $media->id }}" style="padding:0px 5px;margin-bottom:2px !important;">
                                                          <div style="border: 1px solid #bfc0bf;padding:0px 5px;">
                                                             <div data-interval="false" id="carousel_{{ $product->id }}_{{ $media->id }}" class="carousel slide" data-ride="carousel">
                                                                   <div class="carousel-inner maincarousel">
                                                                      <div class="item" style="display: block;"> 
                                                                        <span class="notify-badge {{$badge}}">{{ $width."X".$height}}</span>
                                                                        <img src="<?php echo getMediaUrl($media); ?>" style="height: 150px; width: 150px;display: block;margin-left: auto;margin-right: auto;"> 
                                                                       </div>
                                                                   </div>
                                                             </div>
                                                             <div class="row pl-4 pr-4" style="padding: 0px; margin-bottom: 8px;">
                                                                
                                                                <div class="col-md-4 p-0">
                                                                    @php 
                                                                    $md=$product->getMediables($product->id, $media->id);
                                                                    @endphp
                                                                    @if($md)
                                                                    <input type="text" value="{{$md->order}}" onchange="changeordervalue('{{$md->media_id}}','{{$md->mediable_id}}',this.value);" class="form-control">
                                                                    @endif
                                                                </div>
                                                                
                                                                <div class="col-md-4">
                                                                    @php $gridSrc = asset('images/'.$gridImage); @endphp
                                                                    <a onclick="shortCrop('{{ getMediaUrl($media) }}','{{ $product->id }}','{{ $site->id }}','{{ $gridSrc }}')" 
                                                                        class="btn btn-sm">
                                                                        <i class="fa fa-crop" aria-hidden="true"></i>
                                                                    </a>
                                                                </div>
                                                                
                                                                <div class="col-md-4">
                                                                    <a href="javascript:;" title="Remove" class="btn btn-sm delete-thumbail-img"
                                                                        data-product-id="{{ $product->id }}"
                                                                        data-media-id="{{ $media->id }}">
                                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                                    </a>
                                                                </div>
                                                                
                                                            </div>
                                                          </div>
                                                       </div>
                                                   @endif
                                                @endforeach

                                            @else
                                               
                                            @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="col-md-12">Product is not assigned to any store</div>
                            @endif
                    </div>
                </td>
            </tr>
        </tbody>    
        @endforeach
    </tbody>
</table>
<p class="mb-5">&nbsp;</p>
<?php echo $products->appends(request()->except("page"))->links(); ?>