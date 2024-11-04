@extends('layouts.app')

@section('favicon' , 'attributeedit.png')
@section('title', 'Approved Product Listing - ERP Sololuxury')

@section('title', 'Product Listing')

@section('styles')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css">

    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropme@latest/dist/cropme.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
    
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style type="text/css">
    .quick-edit-color{transition:1s ease-in-out}
    span.multiselect-native-select{display:none;width:100%}
    .thumbnail-pic{position:relative;display:inline-block}
    .thumbnail-pic:hover .thumbnail-edit{display:block}
    .thumbnail-edit{padding-top:12px;padding-right:7px;position:absolute;left:0;top:0;display:none}
    .thumbnail-edit a{color:red}
    .thumbnail-pic{position:relative;padding-top:10px;display:inline-block}
    .notify-badge{position:absolute;top:10px;text-align:center;border-radius:30px 30px 30px 30px;color:#fff;padding:5px 10px;font-size:10px}
    .notify-red-badge{background:red}
    .notify-green-badge{background:green}
    .cropme-container{margin-left:35px!important;top:0!important;width:300px!important;height:300px!important;display:inline-block!important;vertical-align:middle!important}
    .cropme-slider{margin-top:0!important;transform:translate3d(550px,155px,0px) rotate(-90deg)!important;transform-origin:unset!important}
    .product_filter .row > div:not(:first-child):not(:last-child){padding-left:10px;padding-right:10px}
    .product_filter .row > div:first-child{padding-right:10px}
    .product_filter .row > div:last-child{padding-left:10px}
    .select2-container .select2-selection--single{height:34px}
    .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:32px}
    .select2-container--default .select2-selection--single .select2-selection__arrow{height:32px;right:5px}
    .select2-container--default .select2-selection--single,.select2-container--default .select2-selection--multiple{border:1px solid #ccc}
    .select2-container .select2-selection--multiple{min-height:34px}
    .select2-selection select2-selection--multiple{padding:0 5px}
    .select2-container .select2-search--inline .select2-search__field{padding:0 5px}
    td.action > div,td.action > button{margin-top:8px}
    .lmeasurement-container,.dmeasurement-container,.hmeasurement-container{display:block;margin-bottom:10px}
    .quick-name{display:block;text-overflow:ellipsis;overflow:hidden;width:90px;height:1.2em;white-space:nowrap}
    .quick-description{display:block;text-overflow:ellipsis;overflow:hidden;width:100%;max-width:140px;height:1.2em;white-space:nowrap}
    td{padding:3px!important}
    .quick-edit-category,.quick-edit-composition-select,.quick-edit-color,.post-remark,.approved_by{height:26px;padding:2px 12px;font-size:12px}
    .lmeasurement-container input{height:26px;padding:2px 12px;font-size:12px}
    .infinite-scroll-data .badge{display:inline-block;min-width:5px;padding:0 4px}
    .quick-edit-category,.quick-edit-composition-select,.quick-edit-color,.post-remark,.approved_by{height:26px;padding:2px 12px;font-size:12px}
    .lmeasurement-container input{height:26px;padding:2px 12px;font-size:12px}
    .infinite-scroll-data .badge{display:inline-block;min-width:5px;padding:0 4px}
    .toggle.btn{margin:0}
    input[type=checkbox]{height:12px}
    .carousel{margin:10px}
    .lightboxpreview{transition:all .3s linear;padding-top:60%;cursor:pointer;background-size:cover}
    .lightbox-content{max-height:75vh;height:75vh;width:100%;max-width:1000px}
    .lightbox-close{cursor:pointer;margin-left:auto;position:absolute;right:-30px;top:-30px;color:#fff;font-size:2rem;font-weight:700;line-height:1}
    .modal_inner_image{min-height:400px;z-index:1000}
    .modal-content{width:100%}
    .modalscale{transform:scale(0);opacity:0}
    .lightbox-container,.lightbox-btn,.lightbox-image-wrapper,.lightbox-enabled{transition:all .4s ease-in-out}
    .lightbox_img_wrap{padding-top:65%;position:relative;overflow:hidden}
    .lightbox-enabled:hover{transform:scale(1.1)}
    .lightbox-enabled{width:100%;height:100%;position:absolute;top:0;object-fit:cover;cursor:pointer}
    .lightbox-container{width:100vw;height:100vh;position:fixed;top:0;left:0;display:flex;align-items:center;justify-content:center;background-color:rgba(0,0,0,.6);z-index:9999;opacity:0;pointer-events:none}
    .lightbox-container.active{opacity:1;pointer-events:all}
    .lightbox-image-wrapper{display:flex;transform:scale(0);align-items:center;justify-content:center;max-width:90vw;max-height:90vh;position:relative}
    .lightbox-container.active .lightbox-image-wrapper{transform:scale(1)}
    .lightbox-btn,#close{color:#fff;z-index:9999999;cursor:pointer;position:absolute;font-size:50px}
    .lightbox-btn:focus{outline:none}
    .left{left:50px}
    .right{right:50px}
    #close{top:50px;right:50px}
    .lightbox-image{width:100%;-webkit-box-shadow:5px 5px 20px 2px rgba(0,0,0,0.19);box-shadow:5px 5px 20px 2px rgba(0,0,0,0.19);max-height:95vh;object-fit:cover}
    @keyframes slideleft {
    33%{transform:translateX(-300px);opacity:0}
    66%{transform:translateX(300px);opacity:0}
    }
    .slideleft{animation-name:slideleft;animation-duration:.5s;animation-timing-function:ease}
    @keyframes slideright {
    33%{transform:translateX(300px);opacity:0}
    66%{transform:translateX(-300px);opacity:0}
    }
    .slideright{animation-name:slideright;animation-duration:.5s;animation-timing-function:ease}
    </style>
@endsection

@section('large_content')
<div style="position:fixed;z-index:1"><button class="btn btn-secondary hide start-again" onclick="callinterval();" disabled>Start Scroll</button>
<button class="btn btn-secondary stopfunc hide pause" id="clearInt">Stop Scroll</button></div>
    <div class="row">
        <div class="col-lg-12 margin-tb p-0">
            <h2 class="page-heading">
                Scrapper Product Images ({{ $products_count }}) 

                <div style="float: right;">
                    <button type="button" class="btn btn-secondary truncate-tables-btn" style=" float: right;">
                        Truncate Scrapper Images Records
                    </button> 
                </div>
            </h2>

            <form class="product_filter" action="{{ action([\App\Http\Controllers\ProductController::class, 'approvedScrapperImages']) }}/images" method="GET">
                <div class="row p-0 m-0">
                    <div class="col-md-8">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <input name="url" type="text" class="form-control" value="{{ (isset($_REQUEST['url']) ? $_REQUEST['url'] :'' ) }}" placeholder="URL">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <select class="form-control select-multiple" id="si_status" tabindex="-1" aria-hidden="true" name="si_status">
                                <option {{ (isset($_REQUEST['si_status']) && $_REQUEST['si_status'] == 1 ? 'selected' :'' ) }} value="1">Remaining to check</option>
                                <option {{ (isset($_REQUEST['si_status']) && $_REQUEST['si_status'] == 2 ? 'selected' :'' ) }} value="2">Approved</option>
                                <option {{ (isset($_REQUEST['si_status']) && $_REQUEST['si_status'] == 3 ? 'selected' :'' ) }} value="3">Rejected</option>
                                <option {{ (isset($_REQUEST['si_status']) && $_REQUEST['si_status'] == 4 ? 'selected' :'' ) }} value="4">Manually Approve or Reject</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <select class="form-control websites globalSelect2" name="store_website_id[]" data-placeholder="Please select website" style="width:200px !important;" multiple>
                                <option value=""></option>
                                @foreach($all_store_websites as $wId => $wTitle)
                                    <option value="{{ $wId }}" @if(!empty($_REQUEST['store_website_id'])) @if(in_array($wId, $_REQUEST['store_website_id'])) selected @endif @endif>{{ $wTitle }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <button type="submit" class="btn btn-secondary" title="Filter">
                                    <i type="submit" class="fa fa-filter" aria-hidden="true"></i>
                                </button>
                                <a href="{{url()->current()}}" class="btn  btn-secondary" title="Clear">
                                    <i type="submit" class="fa fa-times" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">                    
                        <div class="col-sm-10 ml-2">  
                            <div class="form-group">
                                <input type="text" class="form-control" id="scrolltime" placeholder="scroll interval in second"/>
                            </div>
                        </div>
                        <div class="col-sm-1">  
                            <div class="form-group">
                            <input type="button" onclick="callinterval()" class="btn btn-secondary" value="Start"/>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
   
    @include('partials.flash_messages')

    <div class="row">
        <div class="col-md-12">
            <div class="infinite-scroll table-responsive infinite-scroll-data">
                @include("products.scrapper_listing_image_ajax")
            </div>
            <img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..." style="display: none" />
        </div>
    </div>  

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

<script type="text/javascript">       
var i=0;
var scroll = true;
var old_product;
function start_scroll_down() { 
    if(scroll){
        console.log("in scroll")
        let product_id = $(".infinite-scroll-data .gallery .col-md-2").eq(i-1).attr("productid");
        console.log(product_id)
      
        $("html, body").animate({
            scrollTop: $(".infinite-scroll-data .gallery .col-md-2").eq(i).offset().top
        }, 500).delay(500); // First value is a speed of scroll, and second time break
        i++;
    }else{
        console.log("no scroll")
    }
}

var stop;
function callinterval(){
    if($("#scrolltime").val() == ""){
        toastr["error"]("please add time interval for scroll");
        return;
    }
        
    $(".start-again").removeClass("hide")
    $(".pause").removeClass("hide")

    $(".start-again").attr("disabled","disabled")
    $(".pause").attr("disabled",false)
    $("html, body").animate({
        scrollTop: $(".infinite-scroll-data .gallery .col-md-2").eq(i).offset().top
    }, 500).delay(500); // First value is a speed of scroll, and second time break
    i++;
    stop = setInterval(function(){ console.log("Running");start_scroll_down() }, $("#scrolltime").val()*1000);
}

$('#clearInt').click(function(){ 
    $(".start-again").attr("disabled",false)
    $(".pause").attr("disabled","disabled")
    clearInterval(stop);
    console.log("Stopped");
});

var productIds = [
    @foreach ( $products as $product )
    {{ $product->id }},
    @endforeach
];

var page = 1;
var pagee = 1;
var isLoadingProducts;
$(document).ready(function () {

    $(window).scroll(function() {
        if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 10000 ) ) {
            loadMoreProducts();
        }
    });

    function loadMoreProducts() {
        if (isLoadingProducts)
            return;
        isLoadingProducts = true;
        if(!$('.pagination li.active + li a').attr('href'))
        return;

        var $loader = $('.infinite-scroll-products-loader');
        $.ajax({
            url: $('.pagination li.active + li a').attr('href'),
            type: 'GET',
            beforeSend: function() {
                $loader.show();
                $('ul.pagination').remove();
            }
        })
        .done(function(data) {

            $('.hideApprovebtn').addClass('page__'+pagee);

            if(pagee>4){
                var hiddenClass = parseInt(pagee)-4;

                if(hiddenClass>0){
                    $('.page__'+hiddenClass).addClass('hide');
                }
            }

            if('' === data.trim())
                return;

            $loader.hide();

            $('.infinite-scroll-data').append(data);

            isLoadingProducts = false;
        })
        .fail(function(jqXHR, ajaxOptions, thrownError) {
            console.error('something went wrong');

            isLoadingProducts = false;
        });

        pagee++;
    }
    $('.dropify').dropify();
    // $(".select-multiple").multiselect();
    $(".select-multiple").select2({
        minimumResultsForSearch: -1,
        width: '100%'
    });
    $("body").tooltip({selector: '[data-toggle=tooltip]'});
});

$(document).on('click','.reject-scrap-image',function(){
    var image_id = $(this).data('id');
    var type = $(this).data('type');        

    var msg = "Are you sure you want to approve this image?";
    if(type==3){
        var msg = "Are you sure you want to reject this image?";
    }

    if (confirm(msg)) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            url: "{{ route('scrapper.reject,image') }}",
            data: {
                id: image_id,
                si_status: type
            },
            success: function(response){
                toastr.success('Scrapper image status has been updated successfully.');
                location.reload();
            }
        })
    }
});
</script>

<script type="text/javascript">
// Lightbox Gallery

// query selectors
const lightboxEnabled = document.querySelectorAll('.lightbox-enabled');
const lightboxArray = Array.from(lightboxEnabled);
const lastImage = lightboxArray.length-1;
const lightboxContainer = document.querySelector('.lightbox-container');
const lightboxImage = document.querySelector('.lightbox-image');
const lightboxBtns = document.querySelectorAll('.lightbox-btn');
const lightboxBtnRight = document.querySelector('#right');
const lightboxBtnLeft = document.querySelector('#left');
const close = document.querySelector('#close');
let activeImage;
// Functions
const showLightBox = () => {lightboxContainer.classList.add('active')}

const hideLightBox = () => {lightboxContainer.classList.remove('active')}

const setActiveImage = (image) => {
    lightboxImage.src = image.dataset.imgsrc;
    activeImage= lightboxArray.indexOf(image);
}

const transitionSlidesLeft = () => {
    lightboxBtnLeft.focus();
    $('.lightbox-image').addClass('slideright'); 
    setTimeout(function() {
        activeImage === 0 ? setActiveImage(lightboxArray[lastImage]) : setActiveImage(lightboxArray[activeImage-1]);
    }, 250); 


    setTimeout(function() {
        $('.lightbox-image').removeClass('slideright');
    }, 500);   
}

const transitionSlidesRight = () => {
    lightboxBtnRight.focus();
    $('.lightbox-image').addClass('slideleft');  
      setTimeout(function() {
       activeImage === lastImage ? setActiveImage(lightboxArray[0]) : setActiveImage(lightboxArray[activeImage+1]);
    }, 250); 
      setTimeout(function() {
        $('.lightbox-image').removeClass('slideleft');
    }, 500);  
}

const transitionSlideHandler = (moveItem) => {
moveItem.includes('left') ? transitionSlidesLeft() : transitionSlidesRight();
}

// Event Listeners
lightboxEnabled.forEach(image => {
    image.addEventListener('click', (e) => {
        showLightBox();
        setActiveImage(image);
    })                     
})
lightboxContainer.addEventListener('click', () => {hideLightBox()})
close.addEventListener('click', () => {hideLightBox()})
lightboxBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
    e.stopPropagation();
        transitionSlideHandler(e.currentTarget.id);
    })
})

lightboxImage.addEventListener('click', (e) => {
    e.stopPropagation();
})

$(document).on("click",".truncate-tables-btn",function() {

    if (confirm('Are you sure you want to truncate the Scrapper Images Records & Media?')) {

        $("#loading-image-preview").show();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: '{{ route('products.listing.scrapper.images.truncate') }}',                
            success: function(response) {
                $("#loading-image-preview").hide();
                toastr["success"]("Scrapper Images Table Truncate & Scrapper Images Media remove from the directory.");
                location.reload();
            },
            error: function(error) {
                console.error('Error:', error);
                location.reload();
            }
        }); 
    }
});
</script>
@endsection
