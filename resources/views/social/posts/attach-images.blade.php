<div class="customer-count customer-list- customer-{{ request('websiteId') }}" style="padding: 0px 10px;">
<!-- <ul> -->
    @php
        $count = 0;
    @endphp
    @php
        $left = count($media->toArray());
    @endphp
    
@foreach($media as $list)


@if(sizeof($list->toArray()) > 0)
<!-- <li>
        <div>
            <input type="radio" name="image" value="{{ urldecode(getMediaUrl($list)) }}">
            <img src="{{ urldecode(getMediaUrl($list))}}" alt="{{ $list->name }}">
        </div>

        </li> -->
    @php
        $left--;
    @endphp
        @php
            if($count == 6){
                $count = 0;
            }
        @endphp
        @if($count == 0)
            <div class="row parent-row">
        @endif
            
        <div class="col-md-2 col-xs-4 text-center product-list-card mb-4 single-image-{{$list->id ?? 0}}" style="padding:0px 5px;margin-bottom:2px !important;">
            <div style="border: 1px solid #bfc0bf;padding:0px 5px;">
                <div data-interval="false" id="carousel_{{ request('websiteId') }}" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner maincarousel">
                        <div class="item" style="display: block;"> 
                        <img src="{{ urldecode(getMediaUrl($list))}}" style="height: 150px; width: 150px;display: block;margin-left: auto;margin-right: auto;">    
                        <input type="checkbox" name="image[]" value="{{ urldecode(getMediaUrl($list)) }}">
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        @php
            $count++;
            if($left == 0) {
            $count = 0;
            }
            if($count == 6 || $left == 0){
            echo '</div>';
            }
        @endphp
@endif
@endforeach
<br>

<!-- </ul> -->
    </div>