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
        .buttons_div .btn{margin-bottom: 10px; width: 100%;}
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
    <style>
               .product-list-card::-webkit-scrollbar-track, .image-diamention-rasio::-webkit-scrollbar-track
        {
            border: 1px solid transparent;
	background-color: #F5F5F5;
        }

        .product-list-card::-webkit-scrollbar, .image-diamention-rasio::-webkit-scrollbar
        {
            width: 5px;
	background-color: #F5F5F5;
        }
        .add-remark-button
        {
            /*position: absolute;*/
            top: 0;
            /*right : -40px;*/
            background: transparent;
        }
        .btn-add-action
        {
            /*position: absolute;*/
            top: 0;
            /*right : -10px;*/
            background: transparent;
        }
               .add-remark-button i{
                   font-size: 18px;
               }
        .image-diamention-rasio-desktop
        {
            text-align: center;
            width: fit-content;
            /* display: flex; */
            overflow: auto;
            margin-bottom: 30px;
            /*padding: 0 20px;*/
        }
        .image-diamention-rasio-mobile
        {
            text-align: left;
            width:100%;
            /* display: flex; */
            overflow-y: auto;
            overflow-x: hidden;
            margin-bottom: 30px;
            /*padding: 0 20px;*/
        }

        .manage-product-image
        {
            padding-bottom: 20px;
            border-bottom: 1px solid;
            margin-bottom: 20px;
            /* object-fit:cover; */
            object-fit:contain;

        }
        .product-list-card::-webkit-scrollbar-thumb, .image-diamention-rasio::-webkit-scrollbar-thumb
        {
            background-color: transparent;	
        }
        .infinite-scroll-images{
            max-width:1150px;
            margin:0 auto;
         

        }

        .infinite-scroll-images-mobile{
            max-width:360px;
            margin:0 auto;
         
        }

        .infinite-scroll-images-tablet{
            max-width:767px;
            margin:0 auto;
         
        }
        
    </style>
@endsection

@section('content')
<!-- START - Purpose : Add scroll Interval - DEVTASK-4271 -->
<br/>
<div style="position:fixed;z-index:1"><button class="btn btn-secondary hide start-again" onclick="callinterval();" disabled>Start Scroll</button>
<button class="btn btn-secondary stopfunc hide pause" id="clearInt">Stop Scroll</button></div>
<div class="row">
    <div class="col-sm-2">  
       
    </div>
    <div class="col-sm-3">  
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



<div>
    <form method="get" >
        <div class="form-group">
            <div class="row">
                <div class="col-md-2">
                    
                </div>
                <div class="col-md-2">
                    <select class="form-control select-multiple globalSelect2" id="web-select" tabindex="-1" aria-hidden="true" name="website" onchange="showStores(this)">
                        <option value="">Select Website</option>
                        @foreach($allWebsites as $websiteRow)
                            @if(isset($_REQUEST['web_id']) && $websiteRow->id==$_REQUEST['web_id'])
                                <option value="{{$websiteRow->id}}" selected="selected">{{$websiteRow->name}}</option>
                            @else
                                <option value="{{$websiteRow->id}}">{{$websiteRow->name}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-md-" style="    padding-right: 15px;">
                    <select class="form-control select-multiple globalSelect2" id="web_store" tabindex="-1" aria-hidden="true" name="store" onchange="showLanguages(this)">
                        <option value="">Select Store</option>
                    </select>
                </div>
                <div class="col-md-">
                    <select class="form-control select-multiple globalSelect2" id="web_language" tabindex="-1" aria-hidden="true" name="language">
                        <option value="">Select Language</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <select class="form-control select-multiple" id="web_device" tabindex="-1" aria-hidden="true" name="device">
                        <option value="">Select Device</option>
                        <option {{ (isset($_REQUEST['device']) && $_REQUEST['device'] == "desktop" ? 'selected' :'' ) }} value="desktop">Desktop</option>
                        <option {{ (isset($_REQUEST['device']) && $_REQUEST['device'] == "mobile" ? 'selected' :'' ) }} value="mobile">Mobile</option>
                        <option {{ (isset($_REQUEST['device']) && $_REQUEST['device'] == "tablet" ? 'selected' :'' ) }} value="tablet">Tablet</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <input type="hidden" class="range_start_filter" value="<?php echo date("Y-m-d"); ?>" name="range_start" />
                    <input type="hidden" class="range_end_filter" value="<?php echo date("Y-m-d"); ?>" name="range_end" />
                    <div id="filter_date_range_" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ddd; width: 100%;border-radius:4px;">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span class="d-none" id="date_current_show"></span> <p style="display:contents;" id="date_value_show"> {{ $startDate .' '.$endDate}}</p><i class="fa fa-caret-down"></i>
                    </div>
                </div>

                <div class="col-md-1">
                    <select class="form-control select-multiple" id="si_status" tabindex="-1" aria-hidden="true" name="si_status">
                        <option value="">Select Status</option>
                        <option {{ (isset($_REQUEST['si_status']) && $_REQUEST['si_status'] == 2 ? 'selected' :'' ) }} value="2">Approved</option>
                        <option {{ (isset($_REQUEST['si_status']) && $_REQUEST['si_status'] == 3 ? 'selected' :'' ) }} value="3">Rejected</option>
                    </select>
                </div>
               
                <div class="col-md-1">
                    <button type="button" class="btn btn-image filter_img" ><img src="/images/filter.png"></button>
               
                    <!-- <button type="button" onclick="resetForm(this)" class="btn btn-image" id=""><img src="/images/resend2.png"></button>     -->
                 </div>

                
            </div>
        </div>
    </form>
</div>
<!-- END - DEVTASK-4271 -->
@if(!empty($images))
{{ $images->appends(request()->except('page'))->links() }}
@endif
<!-- Purpose : Add class infinite-scroll - DEVTASK-4271 -->
<div class="infinite-scroll customer-count infinite-scroll-data customer-list-{{$website_id}} customer-{{$website_id}}" style="padding: 0px 10px;display: grid">
    @include("scrapper-phyhon.list-image-products_ajax")   
      
</div>
<img class="infinite-scroll-products-loader center-block" src="{{config('app.url')}}/images/loading.gif" alt="Loading..." style="display: none" />
<!--Remark Modal-->
<div id="remark-area-list" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Remarks</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
			<div class="modal-body">
				<div class="col-md-12">
                    <select name="" id="" class="col-md-2 form-control site-development-category" data-website_id="{{ $website_id }}">
                        <option value="">Select</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->title }}</option>
                        @endforeach
                    </select>
					<div class="col-md-8 pr-0" style="padding-bottom: 10px;">
						<textarea class="form-control" col="5" name="remarks" data-id="" id="remark-field" placeholder="Enter Remarks" style="height:34px"></textarea>
					</div>
					<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-remark-field pl-0" data-website_id="{{ $website_id }}">
						<img src="/images/send.png" style="cursor: default;">
					</button>
				</div>
				<div class="col-md-12">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th width="5%">No</th>
								<th width="45%">Remark</th>
								<th width="25%">BY</th>
								<th width="25%">Date</th>
							</tr>
						</thead>
						<tbody class="remark-action-list-view">
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<!--Remark Load More Data Modal -->
<div id="remark-load-more-data" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Remark</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<div id="bugtrackingCreateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="edit-h3">Add Bug Tracking</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            {{ html()->form('POST', route('bug-tracking.store', ))->open() }}

            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            <div class="form-group {{ $errors->has('summary') ? 'has-error' : '' }}">
                <label> Summary </label>
                  <textarea class="form-control" id="summary" name="summary"></textarea>
                <span class="text-danger">{{ $errors->first('summary') }}</span>
            </div>

            <div class="form-group {{ $errors->has('step_to_reproduce') ? 'has-error' : '' }}">
                <label> Step To Reproduce </label>                
                <textarea class="form-control" id="step_to_reproduce" name="step_to_reproduce"></textarea>
                <span class="text-danger">{{ $errors->first('step_to_reproduce') }}</span>
            </div>

            

            <div class="form-group" {{ $errors->has('bug_type_id') ? 'has-error' : '' }}>
                <label> Type of Bug </label>
                <select class="form-control" id="bug_type_id_bug" name="bug_type_id">
                    <option value="">Select Type of Bug</option>
                    @foreach($bugTypes as  $bugType)
                        <option value="{{$bugType->id}}">{{$bugType->name}} </option>
                    @endforeach
                </select>
                <span class="text-danger"></span>
            </div>

             <div class="form-group" style="padding-bottom: 58px !important;">
                <div class="col-md-6" style="padding-left: 0px !important;" {{ $errors->has('bug_environment_id') ? 'has-error' : '' }}>
                    <label> Environment </label>
                    <select class="form-control" id="bug_environment_id_bug" name="bug_environment_id">
                        <option value="">Select Environment</option>
                        @foreach($bugEnvironments as  $bugEnvironment)
                            <option value="{{$bugEnvironment->id}}">{{$bugEnvironment->name}} </option>
                        @endforeach
                    </select>
                    <span class="text-danger"></span>
                </div>

                <div class="col-md-6"  style="padding-right: 0px !important;"  {{ $errors->has('bug_environment_id') ? 'has-error' : '' }}>
                    <label> Environment Version </label>
                    <input class="form-control" id="bug_environment_ver_bug" name="bug_environment_ver" type="text">
                    <span class="text-danger">{{ $errors->first('bug_environment_ver') }}</span>
                </div>
                
            </div>
           
            <div class="form-group" {{ $errors->has('assign_to') ? 'has-error' : '' }}>
                <label> Assign To </label>
                <select class="form-control" id="assign_to_bug" name="assign_to">
                    <option value="">Select Assign To</option>
                    @foreach($users as  $user)
                        <option value="{{$user->id}}">{{$user->name}} </option>
                    @endforeach
                </select>
                <span class="text-danger"></span>
            </div>
            <div class="form-group" {{ $errors->has('bug_severity_id') ? 'has-error' : '' }}>
                <label> Severity </label>
                <select class="form-control" id="bug_severity_id_bug" name="bug_severity_id">
                    <option value="">Select Severity</option>
                    @foreach($bugSeveritys as  $bugSeverity)
                        <option value="{{$bugSeverity->id}}">{{$bugSeverity->name}} </option>
                    @endforeach
                </select>
                <span class="text-danger"></span>
            </div>
            <div class="form-group" {{ $errors->has('bug_status_id') ? 'has-error' : '' }}>
                <label> Status </label>
                <select class="form-control" id="bug_status_id_bug" name="bug_status_id">
                    <option value="">Select Status</option>
                    @foreach($bugStatuses as  $bugStatus)
                        <option value="{{$bugStatus->id}}">{{$bugStatus->name}} </option>
                    @endforeach
                </select>
                <span class="text-danger"></span>
            </div>
            <div class="form-group" {{ $errors->has('module_id') ? 'has-error' : '' }}>
                <label> Module/Feature </label>
                <select class="form-control" id="module_id_bug" name="module_id">
                    <option value="">Select Module/Feature</option>
                    @foreach($filterCategories as  $filterCategory)
                        <option value="{{$filterCategory}}">{{$filterCategory}} </option>
                    @endforeach
                </select>
                <span class="text-danger"></span>
            </div>

            <div class="form-group  {{ $errors->has('remark') ? 'has-error' : '' }}">
                <label> Remark </label>
                <textarea class="form-control" id="remark_bug" name="remark"></textarea>
                <span class="text-danger">{{ $errors->first('remark') }}</span>
                <span class="text-danger"></span>
            </div>
            <div class="form-group" {{ $errors->has('website') ? 'has-error' : '' }}>
                <label> Website </label>
                <select class="form-control" id="website_bug" name="website">
                    <option value="">Select Website</option>
                    @foreach($filterWebsites as  $filterWebsite)
                        <option value="{{$filterWebsite->id}}">{{$filterWebsite->title}} </option>
                    @endforeach
                </select>
                <span class="text-danger"></span>
            </div>
            <div class="form-group  {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                <label> Reference Bug ID </label>
                 <input class="form-control" name="parent_id" id="parent_id_bug" type="text">
                <span class="text-danger">{{ $errors->first('parent_id') }}</span>
            </div> 
            <div class="form-group">
                <button type="submit" class="btn btn-secondary btn-save-bug">Store</button>
            </div>
            {{ html()->form()->close() }}

            </div>
        </div>
    </div>
</div>


<!-- START - Purpose : Add scroll Interval - DEVTASK-4271 -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
<script>
    var i=1;
    $(document).ready(function() { 
        $("#scrolltime").val('2');
        setTimeout(() => {
        callinterval();
        }, 0);
        showStores();
        showLanguages();
    });

    var scroll = true;
        var old_product;
        function start_scroll_down() { 
            var img_count = $(".infinite-scroll-data img").length;
            if(scroll && i < img_count){
                if(i == 1)
                {
                    $("html, body").animate({
                    scrollTop: $(".infinite-scroll-data div").eq(1).offset().top
                    }, 300).delay(300); 
                    i+=1;
                }else{
                    $("html, body").animate({
                    scrollTop: $(".infinite-scroll-data div img").eq(i).offset().top
                    }, 500).delay(500); 
                    i+=1;
                }
                
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

        if(i == 1)
        {
            $("html, body").animate({
            scrollTop: $(".infinite-scroll-data div").eq(1).offset().top
            }, 300).delay(300); 
            i+=1;
        }else{
            $("html, body").animate({
            scrollTop: $(".infinite-scroll-data div img").eq(i).offset().top
            }, 500).delay(500); 
            i+=1;
        }

        stop = setInterval(function(){ console.log("Running");start_scroll_down() }, $("#scrolltime").val()*1000);
    }

    
    $('#clearInt').click(function(){ 
        $(".start-again").attr("disabled",false)
        $(".pause").attr("disabled","disabled")
        clearInterval(stop);
        console.log("Stopped");
    });

    function showStores(selector)
    {
       
        var website=$(selector).val();
       
        if(website == undefined)
        {
            var website="{{ (isset($_REQUEST['web_id']) ? ($_REQUEST['web_id']) : '' )}}";
        }

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

                    if(v.id=="{{$_REQUEST['id']??0}}")
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

    function showLanguages(selector)
    {
        var store=$(selector).val();
        $('[name="language"]').find('option').eq(1).not().remove();

        if(store == undefined)
        {   
            @if(!empty($_REQUEST['id']))
            var store="{{ $_REQUEST['id'] }}";
            @endif
        }

        if(store)
        {
            $.get('{{route("store.language.list")}}'+'/'+store,function(res)
            {
                if(res.status && res.list.length)
                {
                    var html = '<option value="">Select Language</option>';
                    for (let i = 0; i < res.list.length; i++) {
                        selected='';

                        if(res.list[i].code=="{{$_REQUEST['code']??''}}")
                        {
                            selected='selected'
                        }
                        html += '<option value="'+res.list[i].code+'" '+selected+'>'+res.list[i].name+' ('+res.list[i].code+') '+'</option>';
                    }
                    $('[name="language"]').html(html);

                    // $.each(res.list,function(k,v){

                    // //console.log(k,v);
                    // var selected='';

                    // if(v.code=="{{$_REQUEST['code']??''}}")
                    // {
                    //     selected='selected'
                    // }

                    // $('[name="language"]').html('<option value="">Select Language</option><option value="'+v.code+'" '+selected+'>'+v.name+' ('+v.code+') '+'</option>');

                    // })
                    
                }
                else
                {
                    $('[name="language"]').find('option').eq(1).not().remove();
                }
            })
        }
    }

    $(document).on('click', '.filter_img', function (e) {     
        var website = $('#web-select').find(":selected").val();
        var webstore = $('#web_store').find(":selected").val();
        var weblanguage = $('#web_language').find(":selected").val();
        var webdevice = $('#web_device').find(":selected").val();
        var startDate = $('input[name="range_start"]').val();
        var endDate = $('input[name="range_end"]').val();
        var si_status = $('#si_status').find(":selected").val();

        if(website == '' ){
             toastr['error']('Please Select Website');
             return false;
        }
        else if(webstore == ''){
            toastr['error']('Please Select Store');
             return false;
        }
        else if(weblanguage == ''){
            toastr['error']('Please Select Language');
             return false;
        }

        if(webdevice != '' && (website == '' || webstore == '' || weblanguage == ''))
        {
            var full_url   = window.location.href; 
            const myArr = full_url.split("&");
                
            var url_path = myArr[0]+'&'+myArr[1];
            var url = full_url+"&device="+webdevice;
        }else{

            if(website == '' || webstore == '' || weblanguage == '')
            {
                var full_url   = window.location.href; 
                const myArr = full_url.split("&");
                
                var url_path = myArr[0]+'&'+myArr[1];
                var url = url_path+"&device="+webdevice;
            }else{
                var origin   = window.location.origin;
                var url = origin+"/scrapper-python/list-images?web_id="+website+"&id="+webstore+"&code="+weblanguage+"&device="+webdevice;
            }
        }

        if(startDate != ''&& endDate != '' ){
            var origin   = window.location.origin;
            var url = origin+"/scrapper-python/list-images?web_id="+website+"&id="+webstore+"&code="+weblanguage+"&device="+webdevice+"&startDate="+startDate+"&endDate="+endDate;
        }

        if(si_status != ''){            
            url += url+"&si_status="+si_status;
        }
       
        window.location.href = url;
    });

    // $(document).ready(function() {
    //     var screenHeight = screen.height;
        
    //     $('.manage-product-image').css('height',  screenHeight );
    //     $(window).resize(function(){
    //         var screenHeight = screen.height;
    //         $('.manage-product-image').css('height',  screenHeight );
    //     });
    // })


    $(document).on("click", ".btn-remark-field", function() {
        var cat_id = $(this).parents('.modal-body').find('.site-development-category').val();
        var remark = $(this).parents('.modal-body').find('textarea').val();
        var website_id = $(this).data('website_id');
        if (!cat_id) {
            alert('Please Select Categories')
            return;
        }
        if (!remark) {
            alert('Please Enter Remarks')
            return;
        }
        $.ajax({
            type: "get",
            url: "{{ route('image-remark.store') }}",
            data: {
                remark: remark,
                cat_id:cat_id,
                website_id:website_id,
            },
            success: function(response){
                toastr.success(response.message);
                var html = '<tr><td>'+response.remark.id+'</td><td class="load-more-remarks" data-remark="'+response.remark.remarks+'">'+response.remark.remarks+'</td><td>'+response.username+'</td><td>'+response.remark.created_at+'</td></tr>';
                $('.remark-action-list-view').append(html);
            }
        })
    });
    $(document).on('change','.site-development-category',function(){
        $('.remark-action-list-view').show();
        var remark = $(this).val();
        var website_id = $(this).data('website_id');
        $('.remark-action-list-view').html('');
        $.ajax({
            type: "get",
            url: "{{ route('change-category.remarks-show') }}",
            data: {
                remark: remark,
                website_id:website_id,
            },
            success: function(response){
                const shorter = (a,b)=>  a.id>b.id ? -1: 1;
			    response.remarks.flat().sort(shorter);
                for (let i = 0; i < response.remarks.flat().sort(shorter).length; i++) {
                    var remark = response.remarks.flat().sort(shorter)[i];
                    if (remark.remarks.length > 80) {
                        remark.remarks = remark.remarks.substring(0, 80)+'...';
                    }
                    var html = '<tr><td>'+remark.id+'</td><td class="load-more-remarks" data-remark="'+remark.remarks+'">'+remark.remarks+'</td><td>'+remark.username+'</td><td>'+remark.created_at+'</td></tr>';
                    $('.remark-action-list-view').append(html);
                }
            }
        })
    });
    $('#remark-area-list').on('hide.bs.modal', function (e) {
        $('.site-development-category').val('');
        $('#remark-field').val('');
        $('.remark-action-list-view').hide();
    });


    $(document).on('click','.load-more-remarks', function (){
        var remark = $(this).data('remark');
        if (remark.length <= 80) {
            return;
        }
        $('#remark-load-more-data').modal('show');
        $('#remark-load-more-data').find('p').text(remark);
    })



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

        $("#date_current_show").removeClass("d-none");
        $("#date_value_show").css("display", "none");

    });

    $(window).scroll(function() {
    if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
        console.log("ffff");
        loadMoreProducts();
    }
    });
        var isLoadingProducts ;
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
            }
    
    $(document).on("click", ".btn-save-bug", function () {
  $(".text-danger").html("");
  if ($("#name_bug").val() == "") {
    $("#name_bug").next().text("Please enter the name");
    return false;
  }
  if ($("#test_cases_bug").val() == "") {
    $("#test_cases_bug").next().text("Please enter the test cases");
    return false;
  }
  if ($("#step_to_reproduce_bug").val() == "") {
    $("#step_to_reproduce_bug").next().text("Please enter the steps");
    return false;
  }

  if ($("#url_bug").val() == "") {
    $("#url_bug").next().text("Please enter the url");

    return false;
  }

  if (
    $("#bug_environment_id_bug").val() == "" ||
    $("#bug_environment_id_bug").val() == null ||
    $("#bug_environment_id_bug").val() == "null"
  ) {
    $("#bug_environment_id_bug").next().text("Please enter the environment");
    return false;
  }

  if (
    $("#assign_to_bug").val() == "" ||
    $("#assign_to_bug").val() == null ||
    $("#assign_to_bug").val() == "null"
  ) {
    $("#assign_to_bug").next().text("Please enter the assign to");
    return false;
  }

  if (
    $("#bug_status_id_bug").val() == "" ||
    $("#bug_status_id_bug").val() == null ||
    $("#bug_status_id_bug").val() == "null"
  ) {
    $("#bug_status_id_bug").next().text("Please enter the status");
    return false;
  }
  if (
    $("#module_id_bug").val() == "" ||
    $("#module_id_bug").val() == null ||
    $("#module_id_bug").val() == "null"
  ) {
    $("#module_id_bug").next().text("Please enter the module");
    return false;
  }
  if ($("#remark_bug").val() == "") {
    $("#remark_bug").next().text("Please enter the remark");
    return false;
  }
  if (
    $("#website_bug").val() == "" ||
    $("#website_bug").val() == null ||
    $("#website_bug").val() == "null"
  ) {
    $("#website_bug").next().text("Please enter the website");
    return false;
  }
  return true;
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
                    toastr.success('Scrapper image has been successfully rejected.');
                    location.reload();
                }
            })
        }
    });
</script>
<!-- START - Purpose : Add scroll Interval - DEVTASK-4271 -->
@endsection