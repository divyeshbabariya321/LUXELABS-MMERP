
<div class="row p-0 m-0" style=" width: 100%;  display: inline-block;">
    <section class="gallery">
        <div class="row d-flex p-0 m-0">
            @foreach ($products as $key => $product)
                <div class=" col-12 col-md-2 p-3">
                    <div class="lightbox_img_wrap">
                        <img class="lightbox-enabled" src="{{asset( 'scrappersImages/'.$product->img_name)}}" data-imgsrc="{{asset( 'scrappersImages/'.$product->img_name)}}">
                    </div>
                    <div class="scrapper-btn">
                        <span><strong>Created Date:</strong> {{$product->created_at}}</span><br>
                        <span><strong>Width:</strong> {{$product->width}}, <strong>Height:</strong> {{$product->height}}</span><br>
                        @if($product['si_status']==3 && $product['manually_approve_flag']==0)
                            <button class="btn btn-secondarys reject-scrap-image btn-default" data-id="{{$product['id']}}" data-type="2"><i class="fa fa-check" aria-hidden="true" title="Approve"></i></button>
                        @elseif($product['si_status']==2 && $product['manually_approve_flag']==0)
                            <button class="btn btn-secondarys reject-scrap-image btn-default" data-id="{{$product['id']}}" data-type="3"><i class="fa fa-times" aria-hidden="true" title="Reject"></i></button>
                        @elseif($product['manually_approve_flag']==1 || $product['si_status']==1)
                            <button class="btn btn-secondarys reject-scrap-image btn-default" data-id="{{$product['id']}}" data-type="2"><i class="fa fa-check" aria-hidden="true" title="Approve"></i></button>
                            <button class="btn btn-secondarys reject-scrap-image btn-default" data-id="{{$product['id']}}" data-type="3"><i class="fa fa-times" aria-hidden="true" title="Reject"></i></button>
                        @endif

                        @if(isset($product['url']) && $product['url'] !== null && !empty($product['url']))
                            <a href="{{$product['url']}}" target="_blank" class="btn btn-secondarys btn-default" alt="Visit Page" title="Visit Page"><i class="fa fa-link" aria-hidden="true"></i></a>           
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    <section class="lightbox-container">
        <span class="material-symbols-outlined material-icons lightbox-btn left" id="left">
        arrow_back
        </span>
        <span class="material-symbols-outlined material-icons lightbox-btn right" id="right">
        arrow_forwards
        </span>
        <span id="close" class="close material-icons material-symbols-outlined">
        close
        </span>
        <div class="lightbox-image-wrapper">
            <img alt="lightboximage" class="lightbox-image">
        </div>
    </section> 
</div>
{{ $products->appends(request()->except("page"))->links(); }}