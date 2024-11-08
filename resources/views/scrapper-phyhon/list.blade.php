@extends('layouts.app')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <style type="text/css">
        .select-multiple-cat-list .select2-container {
            position: relative;
            z-index: 2;
            float: left;
            width: 100%;
            margin-bottom: 0;
            display: table;
            table-layout: fixed;
        }
        /*.update-product + .select2-container--default{
            width: 60% !important;
        }*/
        .no-pd {
            padding:0px;
        }

        .select-multiple-cat-list + .select2-container {
            width:100% !important;
        }

        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }

        .row .btn-group .btn {
            margin: 0px;
        }
        .btn-group-actions{
            text-align: right;
        }

        .multiselect-supplier + .select2-container{
            width: 198px !important;
        }
        .size-input{
            width: 155px !important;
        }
        .quick-sell-multiple{
            width: 98px !important;
        }
        .image-filter-btn{
            padding: 10px;
            margin-top: -12px;
        }
        .update-product + .select2-container{
            width: 150px !important;
        }
        .product-list-card > .btn, .btn-sm {
            padding: 5px;
        }

        .select2-container {
            width:100% !important;
            min-width:200px !important;   
        }
        .no-pd {
            padding:3px;
        }
        .mr-3 {
            margin:3px;
        }
        td{
            padding: 4px !important;
        }
         .c-error{ 
            border: 1px solid #c51244 !important; 
        }
        .multiselect-native-select{
            display: none;
        }
    </style>
@endsection

@section('content')
 <div id="myDiv">
       <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
   </div>
    <div class="row m-0">
        <div class="col-lg-12 margin-tb p-0">
            <div class="">
                <!--roletype-->
                <h2 class="page-heading">Scrapper python list<span id="products_count"></span> </h2>
                <!--pending products count-->
                <!--attach Product-->
                <!--Product Search Input -->
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="row m-0">
        <div class="col-md-12 margin-tb p-0">
            <form method="get" action="{{route('scrapper.phyhon.index')}}">
                <div class="form-group">
                    <div class="row m-0">
                        <div class="col-md-2">
                            <input name="search" type="text" class="form-control" value="{{$query}}"  placeholder="search" id="search">
                        </div>
                        <div class="col-md-2">
                            {{ html()->select("store_website_id", [null => "- select website -"] + $storewebsite->toArray(), request('store_website_id'))->class("form-control") }}
                        </div>
                        <?php /* <div class="col-md-2">
                            <select class="form-control select-multiple" id="web-select" tabindex="-1" aria-hidden="true" name="website" onchange="showStores(this)">
                                <option value="">Select Website</option>
                                @foreach($allWebsites as $websiteRow)
                                    @if(isset($request->website) && $websiteRow->id==$request->website)
                                        <option value="{{$websiteRow->id}}" selected="selected">{{$websiteRow->name}}</option>
                                    @else
                                    <option value="{{$websiteRow->id}}">{{$websiteRow->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div> */ ?>
                        <div class="col-md-2">
                            {{ html()->select('device', ["desktop" => "Desktop", "mobile" => "Mobile", "tablet" => "Tablet"], request('device'))->class("form-control") }}
                        </div>
                        <?php /* <div class="col-md-2">
                            <select class="form-control select-multiple" id="store-select" tabindex="-1" aria-hidden="true" name="store">
                                <option value="">Select Store</option>
                            </select>
                        </div> */ ?>
                        <div class="col-md-2 mt-3  pl-19">
                            <button type="submit" class="btn btn-xs btn-image" ><img src="/images/filter.png"></button>
                            <button type="button" onclick="resetForm(this)" class="btn btn-image btn-xs" id=""><img src="/images/resend2.png"></button>
                        </div>
                         
                        <div class='input-group mr-2' id='log-created-date1' style="width: 226px; float:left;">
                            <input type='text' class="form-control " name="delete_date" value="" placeholder="Date for delete" id="delete_date" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <button type="submit" class="btn btn-secondary ml-5 delete-image-btn custom-button btn-xs" style="width: 200px;" > Delete Images </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row m-3">
        <div class="col-md-1">
            <div class="form-group">
                <button class="btn btn-secondary ml-2" data-target="#addAPK" data-toggle="modal">Upload APK File</button>
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <select class="form-control select-multiple" id="store_apk" tabindex="-1" aria-hidden="true" name="store_apk" onchange="showStores(this)">
                    <option value="">Select APK</option>
                    @foreach($scraperApks as $id=>$apk)
                    <option value="{{$id}}">{{$apk}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row ">
        <div class="col-md-12 margin-tb">
            <div class="form-group">    
                <div class="row">
                    <div class="col-md-10 m-3">
                        <form action="" method="POST" id="scrapper-python-form">
                            @csrf
                            <div class="col-md-2">
                                <select class="form-control select-multiple" id="store_website" tabindex="-1" aria-hidden="true" name="store_website" onchange="showStores(this)">
                                    <option value="">Select Website</option>
                                    @foreach($storewebsite as $key=>$web)
                                    <option value="{{$key}}">{{$web}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-7">
                                <div class="radio-inline">
                                    <input class="mt-2" type="radio" name="name" id="start" value="start" checked>
                                    <label class="form-check-label pr-1 mt-3" for="start">
                                        Start
                                    </label>
                                </div>
                                <div class="radio-inline">
                                    <input class="mt-2" type="radio" name="name" id="stop" value="stop">
                                    <label class="form-check-label pr-1 mt-3" for="stop">
                                        Stop
                                    </label>
                                </div>
                                <div class="radio-inline">
                                    <input class="mt-2" type="radio" name="name" id="get-status" value="get-status">
                                    <label class="form-check-label pr-1  mt-2" for="get-status">
                                        Get status
                                    </label>
                                </div>
                                
                                <div class="radio-inline">
                                    <input class="mt-2" type="radio" name="type" id="desktop" value="desktop" checked>
                                    <label class="form-check-label pr-1 mt-3" for="desktop">
                                        Desktop
                                    </label>
                                </div>
                                <div class="radio-inline">
                                <input class="mt-2" type="radio" name="type" id="mobile" value="mobile">
                                    <label class="form-check-label pr-1 mt-3" for="mobile">
                                        Mobile
                                    </label>
                                </div>
                                <div class="radio-inline">
                                    <input class="mt-2" type="radio" name="type" id="tablet" value="tablet">
                                    <label class="form-check-label pr-1 mt-3" for="tablet">
                                        Tablet
                                    </label>
                                </div>
                                <div class="radio-inline">
                                <input class="mt-2 align-bottom" type="checkbox" name="is_flag" id="is_flag" value="is_flag">
                                    <label class="form-check-label pr-1 mt-3" for="is_flag">
                                        Is Flag
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-secondary custom-button btn-xs ml-3"style="height: 34px; width: 200px !important;" >Send Request</button>
                            </div>
                            <div class="col-md-3 " style="display: flex;">
                                
                                <button type="submit"  class="btn ml-5 btn-secondary custom-button action_history btn-xs"style="height: 34px; width: 300px !important;">Action History</button>
                                <button type="submit" class="btn btn-secondary ml-5 custom-button view_history btn-xs" style="height: 34px; width: 300px !important;">History</button>
                                
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Add APK Modal -->
    <div class="modal fade" id="addAPK" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLabel">Add APK</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @include('scrapper-phyhon._partials.add_scrap_apk')
        </div>
    </div>
    </div>
    <div id="history" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="history_img">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">History</h5>

                    <div class="col-md-6">
                        <input type="hidden" class="range_start_filter" value="<?php echo date('Y-m-d'); ?>" name="range_start" />
                        <input type="hidden" class="range_end_filter" value="<?php echo date('Y-m-d'); ?>" name="range_end" />
                        <div id="filter_date_range_" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ddd; width: 100%;border-radius:4px;">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span id="date_current_show"></span><i class="fa fa-caret-down"></i>
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary view_history">Submit</button>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                        <th style="width:30%">Website</th>
                        <th style="width:20%">Device</th>
                        <th style="width:30%">Date</th>
                        <th style="width:20%">No. Of Images</th>
                        </thead>
                        <tbody  class="history_data">
                            
                        </tbody>
                    </table>
                </div>
                <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-approve-pdf">PDF</button>
                    <button type="button" class="btn btn-secondary btn-ignore-pdf">Images</button>
                </div> -->
            </div>
        </div>
    </div>


    <div class="col-md-12 margin-tb">
        <div class="table-responsive">
            <table class="table table-bordered"style="table-layout:fixed;">
                <thead>
                <th style="width:4%">Date</th>
                
                <th style="width:19%">Website</th>
                <th style="width:12%">Name</th>
                <th style="width:9%">Language</th>
                <th style="width:5%">Desktop</th>
                <th style="width:5%">Mobile</th>
                <th style="width:5%">Tablet</th>
                <th style="width:5%">Set as Default</th>
                <th style="width:3%">Is Flag</th>
                <th style="width:4%">Action</th>
                </thead>
                <tbody class="infinite-scroll-data">
                    @include('scrapper-phyhon.attached-image-load_new')
                </tbody>
            </table>

        </div>
        {{ $images->appends(request()->except('page'))->links() }}
    </div>






    
    <!-- <div class="productGrid" id="productGrid">        
    </div> -->
    
    
    <div id="confirmPdf" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <p>Choose the format for sending</p>
                    <div class="form-group mr-3">
                        <strong class="mr-3">Custom File Name</strong>
                        <input type="text" name="file_name" id="pdf-file-name" />
                    </div>
                    <div class="form-group mr-3">
                        <strong class="mr-3">Is Queue?</strong>
                        <select class="form-control" id="is_queue_option" name="is_queue_option">
                            <option>Select queue</option>
                            <option value="1">in Queue</option>
                            <option value="0">Send later</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-approve-pdf">PDF</button>
                    <button type="button" class="btn btn-secondary btn-ignore-pdf">Images</button>
                </div>
            </div>
        </div>
    </div>


    <div id="action-list-history" class="modal" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Action Log</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <td>User</td>
                            <td>Action</td>
                            <td>Website</td>
                            <td>Device</td>
                            <td>Action</td>
                            <td>Url</td>
                            <td>Request</td>
                            <td>Response</td>
                            <td>Date</td>
                        </thead>
                        <tbody  class="action-list-history_data">
                            
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-multiselect.min.js') }} "></script>
    <script type="text/javascript" src="{{ mix('webpack-dist/js/jquery.jscroll.min.js') }} "></script>
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
         <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script>
         
            $('.customer-search').select2({
                width: "100%"
            });

            // $(window).scroll(function() {
            //     if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
            //         loadMore();
            //     }
            // });



            var isLoading;
            function loadMore() {
                if (isLoading)
                    return;
                    isLoading = true;
                if(!$('.pagination li.active + li a').attr('href')) {
                    return;
                }
                

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
                    isLoading = false;

                    // if('' === data.trim())
                    //     return;

                    // $loader.hide();

                    console.log(data);
                    $('.infinite-scroll-data').append(data);

                })
                .fail(function(jqXHR, ajaxOptions, thrownError) {
                    isLoading = false;
                });
            }

        // var infinteScroll = function() {
        //     $('.infinite-scroll').jscroll({
        //         autoTrigger: true,
        //         loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
        //         padding: 2500,
        //         nextSelector: '.pagination li.active + li a',
        //         contentSelector: 'div.infinite-scroll',
        //         callback: function () {
        //            $('.lazy').Lazy({
        //                 effect: 'fadeIn'
        //            });
        //            $('ul.pagination:visible:first').remove();
        //             var next_page = $('.pagination li.active + li a');
        //             var page_number = next_page.attr('href').split('page=');
        //             var current_page = page_number[1] - 1;
        //             $('#page-goto option[data-value="' + current_page + '"]').attr('selected', 'selected');
        //             categoryChange();
        //         }
        //     });

        // };

        var categoryChange = function() 
        {   

            $("select.select-multiple-cat-list:not(.select2-hidden-accessible)").select2();
            $('select.select-multiple-cat-list:not(.select2-hidden-accessible)').on('select2:close', function (evt) {
                var uldiv = $(this).siblings('span.select2').find('ul')
                var count = uldiv.find('li').length - 1;
                if (count == 0) {
                } else {
                    uldiv.html('<li class="select2-selection__choice">' + count + ' item selected</li>');
                }
            });

        };

        categoryChange();

        $(".select-multiple2").select2();
        
        var image_array = [];
        $(document).ready(function () {
            
            // infinteScroll();
            $(".select-multiple").select2();
            //$(".select-multiple-cat").multiselect();
            $("body").tooltip({selector: '[data-toggle=tooltip]'});
            $('.lazy').Lazy({
                effect: 'fadeIn'
            });
            $(document).on("click",".select-all-same-page-btn",function(e){
                e.preventDefault();
                var $this = $(this);
                if($this.hasClass("has-all-selected") === false) {
                    $this.html("Deselect All From Current Page");
                    $(".select-pr-list-chk").prop("checked", true).trigger('change');
                    $this.addClass("has-all-selected");
                }else{
                    $this.html("Select All Current Page");
                    $(".select-pr-list-chk").prop("checked", false).trigger('change');
                    $this.removeClass("has-all-selected");
                }
            });

            var selectAllBtn = $(".select-all-product-btn");
            selectAllBtn.on("click", function (e) {
                var $this = $(this);
                var vcount = 0;
                vcount = $this.data('count');
                if (vcount == 0) {
                    vcount = 'all';
                }
                var productCardCount = $(".product-list-card").length;
               
                // if((vcount == "all" || 1 == 1) && $this.hasClass("has-all-selected") === false && (productCardCount < vcount || vcount == "all") ) {
                    // console.log("if");
                    // e.preventDefault();

                    // $('#selected_products').val(JSON.stringify(image_array));
                    // var formData = $('#searchForm').serializeArray();
                    // formData.push({name: "limit", value: vcount}) ;
                    // formData.push({name: "page", value: 1}) ;
                    
                    // if (isQuickProductsFrom) {
                    //     formData.push({name: "quick_product", value: 'true'});
                    // };
                    
                    // var url = "{{ url()->current() }}";


                    // $.ajax({
                    //     url: url,
                    //     data : formData,
                    //     beforeSend: function() {
                    //         $('#productGrid').html('<img id="loading-image" src="/images/pre-loader.gif"/>');
                    //     }
                    // }).done(function (data) {
                    //     $('#productGrid').html(data.html);
                    //     $('#products_count').text(data.products_count);
                    //     $('.lazy').Lazy({
                    //         effect: 'fadeIn'
                    //     });

                    //     infinteScroll();

                    //     if ($this.hasClass("has-all-selected") === false) {
                    //         $this.html("Deselect " + vcount);
                    //         if (vcount == 'all') {
                    //             $(".select-pr-list-chk").prop("checked", true).trigger('change');
                    //         } else {
                    //             var boxes = $(".select-pr-list-chk");
                    //             for (i = 0; i < vcount; i++) {
                    //                 try {
                    //                     $(boxes[i]).prop("checked", true).trigger('change');
                    //                 } catch (err) {
                    //                 }
                    //             }
                    //         }
                    //         $this.addClass("has-all-selected");
                    //     } 
                    // }).fail(function () {
                    //     alert('Error searching for products');
                    // });

                // }else {
                    if ($this.hasClass("has-all-selected") === false) {
                        $this.html("Deselect " + vcount);
                        if (vcount == 'all') {
                            $(".select-pr-list-chk").prop("checked", true).trigger('change');
                        } else {
                            var customers = $(".customer-count").length;
                            for (i = 0; i < customers; i++) {
                                var boxes = ".customer-list-"+i+" .select-pr-list-chk";
                                for (j = 0; j < vcount; j++) {
                                    try {
                                        $(boxes).eq(j).prop("checked", true).trigger('change');
                                    } catch (err) {
                                    }
                                }
                            }
                        }
                        $this.addClass("has-all-selected");
                    }else {
                        $this.html("Select " + vcount);
                        if (vcount == 'all') {
                            $(".select-pr-list-chk").prop("checked", false).trigger('change');
                        } else {
                            // var boxes = $(".select-pr-list-chk");
                            // for (i = 0; i < vcount; i++) {
                            //     try {
                            //         $(boxes[i]).prop("checked", false).trigger('change');
                            //     } catch (err) {
                            //     }
                            // }
                            var customers = $(".customer-count").length;
                            for (i = 0; i < customers; i++) {
                                var boxes = ".customer-list-"+i+" .select-pr-list-chk";
                                for (j = 0; j < vcount; j++) {
                                    try {
                                        $(boxes).eq(j).prop("checked", false).trigger('change');
                                    } catch (err) {
                                    }
                                }
                            }
                        }
                        $this.removeClass("has-all-selected");
                    }
                // }
            })
        });

        function unique(list) {
            var result = [];
            $.each(list, function (i, e) {
                if ($.inArray(e, result) == -1) result.push(e);
            });
            return result;
        }

        // $(document).on('change', '.select-pr-list-chk', function (e) {
        //     var $this = $(this);
        //     var productCard = $this.closest(".product-list-card").find(".attach-photo");
        //     if (productCard.length > 0) {
        //         var image = productCard.data("image");
        //         if ($this.is(":checked") === true) {
        //             //Object.keys(image).forEach(function (index) {
        //             image_array.push(image);
        //             //});
        //             image_array = unique(image_array);

        //         } else {
        //             //Object.keys(image).forEach(function (key) {
        //             var index = image_array.indexOf(image);
        //             image_array.splice(index, 1);
        //             //});
        //             image_array = unique(image_array);
        //         }
        //     }
        // });
        

        // $('#product-search').autocomplete({
        //   source: function(request, response) {
        //     var results = $.ui.autocomplete.filter(searchSuggestions, request.term);
        //
        //     response(results.slice(0, 10));
        //   }
        // });

        /*$(document).on('click', '.pagination a', function (e) {
            e.preventDefault();
            var url = $(this).attr('href') + '&selected_products=' + JSON.stringify(image_array);

            getProducts(url);
        });*/

        /*function getProducts(url) {
            $.ajax({
                url: url
            }).done(function (data) {
                console.log(data);
                $('#productGrid').html(data.html);
                $('.lazy').Lazy({
                    effect: 'fadeIn'
                });
            }).fail(function () {
                alert('Error loading more products');
            });
        }*/

        $(document).on('click', '.attach-photo', function (e) {
            e.preventDefault();
            var image = $(this).data('image');

            if ($(this).data('attached') == 0) {
                $(this).data('attached', 1);
                image_array.push(image);
            } else {
                var index = image_array.indexOf(image);

                $(this).data('attached', 0);
                image_array.splice(index, 1);
            }

            $(this).toggleClass('btn-success');
            $(this).toggleClass('btn-secondary');

            console.log(image_array);
        });


        // $(document).on('click', '.preview-attached-img-btn', function (e) {     
        //     console.log('load product');
        //     e.preventDefault();
        //     var customer_id = $(this).data('id');
        //     var suggestedproductid = $(this).data('suggestedproductid');
        //     // $.ajax({
        //     //     url: '/attached-images-grid/get-products/attach/'+suggestedproductid+'/'+customer_id,
        //     //     data: $('#searchForm').serialize(),
        //     //     dataType: 'html',
        //     // }).done(function (data) {
        //     //     $('#attach-image-list-'+suggestedproductid).html(data);
        //     // }).fail(function () {
        //     //     alert('Error searching for products');
        //     // });
            
        //     var expand = $('.expand-'+suggestedproductid);
        //     $(expand).toggleClass('hidden');

        //     //to hide image area
        //     $(expand).each(function(){

        //         var imageArea=$(this).find('.show-scrape-images').attr('data-suggestedproductid');

        //         $('.expand-images-'+imageArea).addClass('hidden');


        //     })
           

        // });


        // function to show scrape images
         $(document).on('click', '.show-scrape-images', function (e) {     
            console.log('load images');
            e.preventDefault();
             window.location.href = $(this).data('url');
           
            // var suggestedproductid = $(this).data('suggestedproductid');
            //
            //
            // var expand = $('.expand-images-'+suggestedproductid);
            // $(expand).toggleClass('hidden');

        });

        $(document).on('click', '.attach-photo-all', function (e) {
            e.preventDefault();
            var image = $(this).data('image');

            if ($(this).data('attached') == 0) {
                $(this).data('attached', 1);

                Object.keys(image).forEach(function (index) {
                    image_array.push(image[index]);
                });
            } else {
                Object.keys(image).forEach(function (key) {
                    var index = image_array.indexOf(image[key]);

                    image_array.splice(index, 1);
                });

                $(this).data('attached', 0);
            }

            $(this).toggleClass('btn-success');
            $(this).toggleClass('btn-secondary');

            console.log(image_array);
        });

        // $('#attachImageForm').on('submit', function(e) {
        //   e.preventDefault();
        //
        //   if (image_array.length == 0) {
        //     alert('Please select some images');
        //   } else {
        //     $('#images').val(JSON.stringify(image_array));
        //     alert(JSON.stringify(image_array));
        //     // $('#attachImageForm')[0].submit();
        //   }
        // });

        $('#searchForm button[type="submit"]').on('click', function (e) {
            e.preventDefault();
            isQuickProductsFrom = false;
            $('#selected_products').val(JSON.stringify(image_array));

            var url = "{{ url()->current() }}";
            var formData = $('#searchForm').serialize();
            $('#searchForm').submit();

            /*$.ajax({
                url: url,
                data: formData
            }).done(function (data) {
                //all_product_ids = data.all_product_ids;
                $('#productGrid').html(data.html);
                $('#products_count').text(data.products_count);
                $('.lazy').Lazy({
                    effect: 'fadeIn'
                });
                infinteScroll();

            }).fail(function () {
                alert('Error searching for products');
            });*/
        });
        var isQuickProductsFrom = false;
        $('#quickProducts').on('submit', function (e) {
            e.preventDefault();
            isQuickProductsFrom = true;
            var url = "{{ url()->current() }}?quick_product=true";
            var formData = $('#searchForm').serialize();

            $.ajax({
                url: url,
                data: formData
            }).done(function (data) {
                $('#productGrid').html(data.html);
                $('#products_count').text(data.products_count);
                $('.lazy').Lazy({
                    effect: 'fadeIn'
                });
                // infinteScroll();
            }).fail(function () {
                alert('Error searching for products');
            });
        });


        // $('#product-search').on('keyup', function() {
        //   alert('t');
        // });

        

        jQuery('.btn-attach').click(function (e) {

            e.preventDefault();

            let btn = jQuery(this);
            let product_id = btn.attr('data-id');
            let model_id = btn.attr('model-id');
            let model_type = btn.attr('model-type');


            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/attachProductToModel/' + model_type + '/' + model_id + '/' + product_id,

                success: function (response) {

                    if (response.msg === 'success') {
                        btn.toggleClass('btn-success');
                        btn.html(response.action);
                    }
                }
            });
        });

        $(document).on('click', '.sendImageMessage', function () {
            var customer_id = $(this).data("id");
            var suggestedproductid = $(this).data("suggestedproductid");
            var cus_cls = ".customer-"+suggestedproductid;
            var total = $(cus_cls).find(".select-pr-list-chk").length;
            image_array = [];
            for (i = 0; i < total; i++) {
             var customer_cls = ".customer-"+suggestedproductid+" .select-pr-list-chk";
             var $input = $(customer_cls).eq(i);
            var productCard = $input.parent().parent().find(".attach-photo");
                if (productCard.length > 0) {
                    var image = productCard.data("image");
                    var product = productCard.data("product");
                    if ($input.is(":checked") === true) {
                        image_array.push(product);
                        image_array = unique(image_array);
                    }
                }
            }
            if (image_array.length == 0) {
                alert('Please select some images');
            } else {
                $('#images').val(JSON.stringify(image_array));
                var form = $('#attachImageForm');
                var modelType = form.data("model-type");
                if(modelType == "selected_customer" || modelType == "customer" || modelType == "customers" || modelType == "livechat") {
                    $("#confirmPdf").modal("show");
                    $("#hidden-customer-id").val(customer_id);
                    $("#hidden-type").val('customer-attach');
                    // if(modelType == "customer") {
                    //     $("#hidden-return-url").val('/attached-images-grid/sent-products?customer_id='+customer_id);
                    // }
                    
                }else{
                    $('#attachImageForm').submit();
                }
            }
        });

        $(".btn-approve-pdf").on("click",function() {
            $("#send_pdf").val("1");
            $("#is_queue_setting").val($("#is_queue_option").val());
            $("#pdf_file_name").val($("#pdf-file-name").val());
            $("#hidden-json").val(true);
            $('#attachImageForm').submit();
        });

        $(".btn-ignore-pdf").on("click",function() {
            $("#send_pdf").val("0");
            $("#is_queue_setting").val($("#is_queue_option").val());
            $("#pdf_file_name").val($("#pdf-file-name").val());
            $("#hidden-json").val(true);
            $('#attachImageForm').submit();
        });
        // });

        $("#attachImageForm").on("submit",function(e) {
            e.preventDefault();
            var url = $('#attachImageForm').attr('action');
            var data = $(this).serialize();
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    beforeSend: function (success) {
                        console.log(success);
                                $("#loading-image").show();
                            },
                    success: function(result){
                        $("#loading-image").hide();
                        $("#confirmPdf").modal('hide');
                    toastr['success'](result.message, 'success');
                    $(".select-pr-list-chk").prop("checked", false).trigger('change');
                },
                error: function(error){
                        $("#loading-image").hide();
                }
            });
        });

       

        $('#attachAllButton').on('click', function () {
            var url = "{{ route('customer.attach.all') }}";

            $('#searchForm').attr('action', url);
            $('#searchForm').attr('method', 'POST');

            $('#searchForm').submit();
        });

        function replaceUrlParam(url, paramName, paramValue)
        {
            if (paramValue == null) {
                paramValue = '';
            }
            var pattern = new RegExp('\\b('+paramName+'=).*?(&|#|$)');
            if (url.search(pattern)>=0) {
                return url.replace(pattern,'$1' + paramValue + '$2');
            }
            url = url.replace(/[?#]$/,'');
            return url + (url.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue;
        }

        $(document).on('change', '.update-product', function () {    
            product_id = $(this).attr('data-id');
            category = $(this).find('option:selected').text();
            category_id = $(this).val();
            //Getting Scrapped Category
            $.ajax({
                url: '/products/'+product_id+'/originalCategory',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                beforeSend: function () {
                  $("#loading-image").show();
                },
                success: function(result){
                    $("#loading-image").hide();
                    $('#categoryUpdate').modal('show');
                    if(result[0] == 'success'){
                        $('#old_category').text(result[1]);
                        $('#changed_category').text(category);
                        $('#product_id').val(product_id);
                        $('#category_id').val(category_id);
                        if(typeof result[2] != "undefined") {
                            $("#no_of_product_will_affect").html(result[2]);
                        }
                    }else{
                        $('#old_category').text('No Scraped Product Present');
                        $('#changed_category').text(category);
                        $('#product_id').val(product_id);
                        $('#category_id').val(category_id);
                        $("#no_of_product_will_affect").html(0);
                    }
                },
                error: function (){
                    $("#loading-image").hide();
                    $('#categoryUpdate').modal('show');
                    $('#old_category').text('No Scraped Product Present');
                    $('#changed_category').text(category);
                    $('#product_id').val(product_id);
                    $('#category_id').val(category_id);
                    $("#no_of_product_will_affect").html(0);
                }
            });

            
            //$('#categoryUpdate').modal('show');
            
        });        
        
        function changeSelected(){
            product_id = $('#product_id').val();
            category = $('#category_id').val();
            $.ajax({
                url: '/products/'+product_id+'/updateCategory',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    category : category
                },
                beforeSend: function () {
                              $('#categoryUpdate').modal('hide');  
                              $("#loading-image").show();
                              $("#loading-image").hide();
                          },
                });
        
        }

        function changeAll(){
            product_id = $('#product_id').val();
            category = $('#category_id').val();
            $.ajax({
                url: '/products/'+product_id+'/changeCategorySupplier',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    category : category
                },
                beforeSend: function () {
                              $('#categoryUpdate').modal('hide');  
                              $("#loading-image").show();
                          },
                success: function(result){
                     $("#loading-image").hide();
             }
         });
        }
        
        $('body').on("click",'.select_row', function (event) {
        $(".select-pr-list-chk").prop("checked", false).trigger('change');
           var $input = $(this);
           var checkBox = $input.parent().parent().parent().parent().find(".select-pr-list-chk");
           checkBox.prop("checked", true).trigger('change');
        });

        $('body').on("click",'.select_multiple_row', function (event) {
        // $(".select-pr-list-chk").prop("checked", false).trigger('change');
           var $input = $(this);
           var checkBox = $input.parent().parent().parent().parent().find(".select-pr-list-chk");
           checkBox.prop("checked", true).trigger('change');
        });

        
    </script>

@endsection

@section('scripts')
<script type="text/javascript">
    function myFunction(id){
        $('#description'+id).toggle();
        $('#description_full'+id).toggle();
    }

    $(document).on("click",".attach-thumb-created .item",function(e){
        e.preventDefault();
        var imageID = $(this).find(".thumb").data("image");
        var card = $(this).closest(".product-list-card");
            card.find(".attach-photo").attr("data-image",imageID);
    });
    
            $('body').on('click', '.load-chat-images-actions', function (event) {
            if ($(this).parent().hasClass('open')) {
                $(this).parent().removeClass('open');
            } else {
                $('.load-chat-images-actions').parent().removeClass('open');
                $(this).parent().toggleClass('open');
            }
        });

        $(document).on("click", function (event) {
            var container = $(".load-chat-images-dropdown-menu");
            if (container.has(event.target).length === 0) {
                $('.load-chat-images-actions').parent().removeClass('open');
            }
        });

        $(document).on("click", ".add-more-products", function (event) {
            customer_id = $(this).data('id');
            suggested_products_id = $(this).data('suggestedproductid');
            $.ajax({
                url: '/attached-images-grid/add-products/'+suggested_products_id,
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function () {  
                              $("#loading-image").show();
                },
                success: function(result){
                     $("#loading-image").hide();
                     console.log(result.url);
                     location.reload();
                    //  window.location.href = result.url;
             }
         });
        });

        $(document).on("click", ".remove-products", function (event) {
            var suggested_products_id = $(this).data("id");
            var cus_cls = ".customer-"+suggested_products_id;
            var total = $(cus_cls).find(".select-pr-list-chk").length;
            product_array = [];
            for (i = 0; i < total; i++) {
             var customer_cls = ".customer-"+suggested_products_id+" .select-pr-list-chk";
             var $input = $(customer_cls).eq(i);
            var productCard = $input.parent().parent().find(".attach-photo");
                if (productCard.length > 0) {
                    var product = productCard.data("product");
                    if ($input.is(":checked") === true) {
                        product_array.push(product);
                    }
                }
            }
            if (product_array.length == 0) {
                alert('Please select some images');
                return;
            }

            console.log(product_array);
            var confirm = window.confirm('Are you sure ?');
            if(!confirm) {
                return;
            }
            $.ajax({
                url: '/attached-images-grid/remove-products/'+suggested_products_id,
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    products: JSON.stringify(product_array)
                },
                beforeSend: function () {  
                    $("#loading-image").show();
                },
                success: function(result){
                     $("#loading-image").hide();
                     location.reload();
             }
         });
        });

        $(document).on("click", ".delete-message", function (event) {
            var listed_id = $(this).data("listed_id");
            var customer_id = $(this).data("customer");
            var product_id = $(this).data("id");
            $.ajax({
                url: '/attached-images-grid/remove-single-product/'+customer_id,
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    product_id: listed_id
                },
                beforeSend: function () {  
                    $("#loading-image").show();
                },
                success: function(result){
                     $("#loading-image").hide();
                     toastr['success']("Successfull", 'success');
                     var cls = '.single-image-'+listed_id+'-'+customer_id+'-'+product_id;
                     $(cls).hide();
                    //  location.reload();
             }
         });
        });

        $(document).on("click", ".forward-all-products", function (event) {
            image_array = [];
            var products = $(".select-pr-list-chk:checked");
                if(products.length > 0) {
                    $.each(products,function(k,v) {
                        var p = $(v).parent().parent().find(".attach-photo")
                        if(p && p.data("product")) {
                            image_array.push(p.data("product"));
                        }
                    });
                }
            if (image_array.length == 0) {
                alert('Please select some images');
                return;
            }
            
            $('#forward-products-form').find('#product_lists').val(JSON.stringify(image_array));
            $('#forward-products-form').find('#forward_type').val('attach');
            $("#forwardProductsModal").modal('show');
            $('select.select2').select2({
                width: "100%"
            });    
        });

        $(document).on("click", ".forward-products", function (event) {
            var customer_id = $(this).data("id");
            var suggestedproductid = $(this).data("suggestedproductid");
            $("#forward_suggestedproductid").val(suggestedproductid);
            /* alert(suggestedproductid); 
            return false; */
            var cus_cls = ".customer-"+suggestedproductid;
            var total = $(cus_cls).find(".select-pr-list-chk").length;
            image_array = [];
            for (i = 0; i < total; i++) {
             var customer_cls = ".customer-"+suggestedproductid+" .select-pr-list-chk";
             var $input = $(customer_cls).eq(i);
            var productCard = $input.parent().parent().find(".attach-photo");
            if (productCard.length > 0) {
                    var image = productCard.data("product");
                    if ($input.is(":checked") === true) {
                        image_array.push(image);
                        image_array = unique(image_array);
                    }
                }
            }
            if (image_array.length == 0) {
                alert('Please select some images');
                return;
            }
            
            $('#forward-products-form').find('#product_lists').val(JSON.stringify(image_array));
            $('#forward-products-form').find('#forward_type').val('attach');
            $("#forwardProductsModal").modal('show');
            $('select.select2').select2({
                width: "100%"
            });
        });


        $(document).on("submit", "#forward-products-form", function (e) {
            e.preventDefault();
            $.ajax({
                url: '/attached-images-grid/forward-products',
                type: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                beforeSend: function () {  
                    $("#loading-image").show();
                },
                success: function(result){
                     $("#loading-image").hide();
                    toastr['success'](result.message, 'success');
                     location.reload();
             }
            });
        });

        $(document).on("click", ".expand-row-btn", function (e) {
            var id = $(this).data('id');
            console.log(id);
            console.log($('.toggle-div-'+id).length);
            $('.toggle-div-'+id).toggleClass('hidden');
        });

    
        $(document).on("click",".select-customer-all-products", function (e) {
                    var customer_id = $(this).data('id');
                    var suggestedproductid = $(this).data('suggestedproductid');
                    var $this = $(this);
                    var custCls = '.customer-'+suggestedproductid;
                    if ($this.hasClass("has-all-selected") === false) {
                        // $this.html("Deselect all");
                        $(this).find('img').attr("src", "/images/completed-green.png");
                        $(custCls).find(".select-pr-list-chk").prop("checked", true).trigger('change');
                        $this.addClass("has-all-selected");
                    }else {
                        // $this.html("Select all");
                        $(this).find('img').attr("src", "/images/completed.png");
                        $(custCls).find(".select-pr-list-chk").prop("checked", false).trigger('change');
                        $this.removeClass("has-all-selected");
                    }
    })

    

    $('#customer-search').select2({
            tags: true,
            width : '100%',
            ajax: {
                url: '/erp-leads/customer-search',
                dataType: 'json',
                delay: 750,
                data: function (params) {
                    return {
                        q: params.term, // search term
                    };
                },
                processResults: function (data, params) {
                    for (var i in data) {
                        if(data[i].name) {
                            var combo = data[i].name+'/'+data[i].id;
                        }
                        else {
                            var combo = data[i].text;
                        }
                        data[i].id = combo;
                    }
                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
            },
            placeholder: 'Search for Customer by id, Name, No',
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 1,
            templateResult: function (customer) {
                if (customer.loading) {
                    return customer.name;
                }
                if (customer.name) {
                    return "<p> " + (customer.name ? " <b>Name:</b> " + customer.name : "") + (customer.phone ? " <b>Phone:</b> " + customer.phone : "") + "</p>";
                }
            },
            templateSelection: (customer) => customer.text || customer.name,
        });


        $(document).on('click', '.expand-row-msg', function () {
            var name = $(this).data('name');
            var id = $(this).data('id');
            var full = '.expand-row-msg .show-short-'+name+'-'+id;
            var mini ='.expand-row-msg .show-full-'+name+'-'+id;
            $(full).toggleClass('hidden');
            $(mini).toggleClass('hidden');
        });

        $(document).on("click",".btn-event-order",function(e) {
            e.preventDefault();
            var form  = $(this).closest("form");
            $.ajax({
                type: "POST",
                url: "/erp-customer/move-order",
                data : form.serialize(),
                dataType : "json",  
                beforeSend : function() {
                    $(this).text('Loading...');
                    },
            }).done(function (response) {
                if(response.code == 1) {
                    window.location = "/order/create?key="+response.key;
                }
            }).fail(function (response) {
                console.log(response);
            });
        });

        function resetForm(selector)
        {
            
           $(selector).closest('form').find('[name="search"]').val('');

           $(selector).closest('form').submit();
        }

        function setStoreAsDefault(selector)
        {
            var website=$(selector).attr('data-website-id');
            var store=$(selector).attr('data-store-id');
            var checked=0;
            var thisSelector=$(selector);

            if($(selector).prop('checked')==true)
            {
                var checked=1;
            }

            $('.expand-'+website).each(function(){

                console.log($(this).find('.defaultInput'));

                $(this).find('.defaultInput').prop('checked',false);
            })
            

            if(checked)
            {
                $(selector).prop('checked',true);
            }

            

            $.get('{{route("set.default.store")}}'+'/'+website+'/'+store+'/'+checked,function(res)
            {
                if(res.status==1)
                {
                   toastr['success'](res.message, 'success');
                }
                else
                {
                    toastr['error'](res.message, 'error');
                }


            })

        }

        $(document).ready(function()
        {
            $('[name="website"]').trigger('change');

            $(document).on('click', '.expand-row-msg', function () {
                var name = $(this).data('name');
                var id = $(this).data('id');
                var full = '.expand-row-msg .show-short-'+name+'-'+id;
                var mini ='.expand-row-msg .show-full-'+name+'-'+id;
                $(full).toggleClass('hidden');
                $(mini).toggleClass('hidden');
            });
        })

        function showStores(selector)
        {
            var website=$(selector).val();
            $('[name="store"]').find('option').eq(1).not().remove();


            if(website)
            {
                $.get('{{route("website.store.list")}}'+'/'+website,function(res)
                {
                   if(res.status && res.list.length)
                   {
                      $.each(res.list,function(k,v){

                        //console.log(k,v);
                        var selected='';

                        if(v.id=='{{$request->store??0}}')
                        {
                            selected='selected'
                        }

                        $('[name="store"]').append('<option value="'+v.id+'" '+selected+'>'+v.name+'</option>');

                      })
                     
                   }
                   else
                   {
                     $('[name="store"]').find('option').eq(1).not().remove();
                   }
                })
            }
        }

        let r_s = "";
        let r_e = "";

        let start = r_s ? moment(r_s,'YYYY-MM-DD') : moment().subtract(0, 'days');
        let end =   r_e ? moment(r_e,'YYYY-MM-DD') : moment();

        jQuery('input[name="range_start"]').val();
        jQuery('input[name="range_end"]').val();

        function cb(start, end) {
            $('#filter_date_range_ span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#filter_date_range_').daterangepicker({
            startDate: start,
            maxYear: 1,
            endDate: end,
            //parentEl: '#filter_date_range_',
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);

        $('#filter_date_range_').on('apply.daterangepicker', function(ev, picker) {
            let startDate=   jQuery('input[name="range_start"]').val(picker.startDate.format('YYYY-MM-DD'));
            let endDate =    jQuery('input[name="range_end"]').val(picker.endDate.format('YYYY-MM-DD'));
        });

        
        $(document).on("click",".view_history",function(e) {
            e.preventDefault();

            let startDate=   jQuery('input[name="range_start"]').val();
            let endDate =    jQuery('input[name="range_end"]').val();

            $.ajax({
                type: "GET",
                url: "{{route('scrapper.history')}}",
                data: {
                    _token: "{{ csrf_token() }}",
                    startDate:startDate,
                    endDate:endDate,
                },
                dataType : "json",  
                beforeSend : function() {
                    $(this).text('Loading...');
                },
            }).done(function (response) {
                
                var html_data = '';
                $.each(response.history,function(k,v){
                    html_data += '<tr>';
                    html_data += '<td>'+v.website+'</td>';
                    html_data += '<td>'+(v.device ?? '')+'</td>';
                    html_data += '<td>'+ moment(v.created_date).format('YYYY-MM-DD') +'</td>';
                    
                    html_data += '<td>'+v.no_image+'</td>';
                    html_data += '</tr>';
                })
                $('.history_data').html(html_data);
                $('#history').modal('show');

            }).fail(function (response) {
                console.log(response);
            });
        });

        $(document).on("click",".action_history",function(e) {
            e.preventDefault();

            let startDate=   jQuery('input[name="range_start"]').val();
            let endDate =    jQuery('input[name="range_end"]').val();

            $.ajax({
                type: "GET",
                url: "{{route('scrapper.action.history')}}",
                data: {
                    _token: "{{ csrf_token() }}",
                    startDate:startDate,
                    endDate:endDate,
                },
                dataType : "json",  
                beforeSend : function() {
                    $(this).text('Loading...');
                },
            }).done(function (response) {
                
             //   var html_data = '';
               
                $('.action-list-history_data').html(response.message);
                $('#action-list-history').modal('show');

            }).fail(function (response) {
                console.log(response);
            });
        });

        
        $('#delete_date').datetimepicker({ format: 'YYYY-MM-DD' });
         $(document).on('click', '.delete-image-btn', function(e) {

            e.preventDefault();
            if( $('#delete_date').val()==""){
                alert("Please select delete date to delete the images");
                return false;
            }
            if(!confirm("Do you really want to do this?")) {
                return false;
            }
             $.ajax({
                type: 'POST',
                url: "{{route('scrapper.phyhon.delete')}}",
                beforeSend: function() {
                    $("#loading-image").show();
                },
                data: {
                    _token: "{{ csrf_token() }}",
                    delete_date: $('#delete_date').val(),
                    
                },
                dataType: "json"
            }).done(function(response) {
                $("#loading-image").hide();
                if (response.message) {
                    toastr['success'](response.message, 'success');
                } else {
                    toastr['error'](response.err, 'error');
                }
                
            }).fail(function(response) {
                $("#loading-image").hide();
                $('#scrapper-python-modal').modal('hide')

                console.log("Sorry, something went wrong");
            });

        });

         function setStoreAsFlag(selector)
        {
            var website=$(selector).attr('data-website-id');
            var store=$(selector).attr('data-store-id');
            var checked=0;
            var thisSelector=$(selector);

            if($(selector).prop('checked')==true)
            {
                var checked=1;
            }

            $('.expand-'+website).each(function(){

                console.log($(this).find('.defaultInput'));

                $(this).find('.defaultInput').prop('checked',false);
            })
            

            if(checked)
            {
                $(selector).prop('checked',true);
            }

            

            $.get('{{route("set.flag.store")}}'+'/'+website+'/'+store+'/'+checked,function(res)
            {
                if(res.status==1)
                {
                   toastr['success'](res.message, 'success');
                }
                else
                {
                    toastr['error'](res.message, 'error');
                }


            })

        }
        
</script>

@endsection