@extends('layouts.app')

@section('title','GT Metrix')

@section('content')

<style>
    .model-width{
        max-width: 1250px !important;
    }
</style>
<div class = "row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">GTMetrix</h2>
    </div>
</div>


@include('partials.flash_messages')
<div class = "row">
    <div class="col-md-10 margin-tb">
        <div class="pull-left cls_filter_box">
            <form class="form-inline" action="" method="GET">
                <div class="form-group ml-3 cls_filter_inputbox">
                    <input type="date" name="date" value="{{ request('date') }}" class="form-control">
                </div>
                <div class="form-group ml-3 cls_filter_inputbox">
                    <select name="process" class="form-control">
                        <option value="">select process</option>
                        <option {{ (request('process') == 'no') ? 'selected' : ''  }} value="no">No</option>
                        <option {{ (request('process') == 'yes') ? 'selected' : ''  }} value="yes">Yes</option>
                    </select>
                </div>
                <div class="form-group ml-3 cls_filter_inputbox">
                    <input type="text" name="website_url" class="form-control" value="{{ request('website_url') }}" placeholder="Website Url">
                </div>
                <div class="form-group ml-3 cls_filter_inputbox">
                    <select name="sortby" class="form-control">
                        <option disabled="true" selected="true" >Select Sorting</option>
                        <option {{ (request('sortby') == 'desc') ? 'selected' : ''  }} value="desc">DESC</option>
                        <option {{ (request('sortby') == 'asc') ? 'selected' : ''  }} value="asc">ASC</option>
                    </select>
                </div>


                <button type="submit" class="btn btn-image"><img src="/images/filter.png"></button>
            </form> 
        </div>
    </div>  
    <div class="col-md-2 margin-tb">
        <div class="pull-right">
        <button class="btn multi-run-test-btn btn-xs text-dark" onclick="checkCheckbox()" title="Add InProcess">
            <i class="fa fa-play" aria-hidden="true"></i>
        </button>
        <button type="button" class="btn btn-secondary" btn="" btn-success="" btn-block="" btn-publish="" mt-0="" data-toggle="modal" data-target="#setCron" title="" data-id="1">
                Add
            </button>
            
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="panel-group" style="margin-bottom: 5px;">
            <div class="panel mt-3 panel-default">
                <table id="site-gtMetrix-data" class="table table-bordered table-striped table-responsive site-gtMetrix-data">
                    <thead>
                        <tr>
                            @php
                              $url=url('gtmetrix');
                              $p='';
                              if (isset($_GET['date']))
                                 {
                                     if ($p=='')
                                       $p="?date=".$_GET['date'];
                                     else
                                        $p.= "&date=".$_GET['date']; 
                                 }
                                 if (isset($_GET['website_url']))
                                 {
                                     if ($p=='')
                                       $p="?website_url=".$_GET['website_url'];
                                     else
                                        $p.= "&website_url=".$_GET['website_url']; 
                                 } 
                                 if (isset($_GET['process']))
                                 {
                                     if ($p=='')
                                       $p="?process=".$_GET['process'];
                                     else
                                        $p.= "&process=".$_GET['process']; 
                                 } 
                                 
                                 $ord1='';
                                 if (isset($_GET['sortby']) && $_GET['sortby']=='pagespeed_score' && $_GET['ord']=='asc' )
                                 {
                                     if ($p=='')
                                     $ord1="?sortby=pagespeed_score&ord=desc";
                                     else
                                     $ord1.= "&sortby=pagespeed_score&ord=desc"; 
                                 }
                                 else
                                 {
                                    if ($p=='')
                                     $ord1="?sortby=pagespeed_score&ord=asc";
                                     else
                                     $ord1.= "&sortby=pagespeed_score&ord=asc"; 
                                 }

                                 $ord2='';
                                 if (isset($_GET['sortby']) && $_GET['sortby']=='yslow_score' && $_GET['ord']=='asc' )
                                 {
                                     if ($p=='')
                                     $ord2="?sortby=yslow_score&ord=desc";
                                     else
                                     $ord2.= "&sortby=yslow_score&ord=desc"; 
                                 }
                                 else
                                 {
                                    if ($p=='')
                                     $ord2="?sortby=yslow_score&ord=asc";
                                     else
                                     $ord2.= "&sortby=yslow_score&ord=asc"; 
                                 }
                                 $url1=$url.$p.$ord1;
                                 $url2=$url.$p.$ord2;

                            @endphp
                            <th width="1%">
                                <input type="checkbox" id="selectAll" />
                            </th>
							<th width="20%">Created At</th>
							<th width="20%">Store View</th>
                            <th width="30%">Website</th>
                            <th width="10%">Process</th>
                            <th width="20%">Action</th>
                        </tr>
                    </thead>
                    <tbody id="infinite-scroll-cashflow-inner" class="pending-row-render-view infinite-scroll-cashflow-inner">
                        @foreach ($list as $key)
                        
                            <tr>
                                <td>
                                    <input @if($key->process == '1') checked @endif type="checkbox" name ="multi-run-test-type" class= "multi-run-test" value ="{{ $key->id }}">
                                </td>
								<td>
								{{ \Carbon\Carbon::parse($key->created_at)->format('Y-m-d')}}
								</td>
								<td>
								{{ $key->store_name }}
								</td>
                                <td>
                                    <a class="text-dark" href="{{ $key->website_url }}" target="_blank" title="Goto website"> {{ !empty($key->website_url) ? $key->website_url : $key->store_view_id }} </a>
                                </td>
                                <td class="processToggle">
									@if($key->process == '1')
									<label class="switch">
										<input type="checkbox" checked value ="{{ $key->id }}">
										<span class="slider round"></span>
									</label>
									@else
									<label class="switch">
										<input type="checkbox" value ="{{ $key->id }}">
										<span class="slider round"></span>
									</label>
									@endif
								</td>
                                <td>  
                                    <a id="delete-url" href="javascript:void(0)" data-value="{{ $key->id }}">Delete</a>
                                    <a id="run-current-url" href="javascript:void(0)" data-value="{{ $key->id }}">Run Current Page</a>
		                        </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div colspan="12">
                {{ $list->links() }}
            </div>
        </div>
        <img class="infinite-scroll-products-loader center-block" src="{{asset('/images/loading.gif')}}" alt="Loading..." style="display: none" />

       
    </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
   50% 50% no-repeat;display:none;">
</div>
<div class="modal fade" id="gtmetrix-stats-modal" role="dialog">
    <div class="modal-dialog modal-lg model-width">
      <!-- Modal content-->
        <div class="modal-content message-modal" style="width: 100%;">
            
        </div>
    </div>
</div>

<div class="modal fade" id="gtmetrix-comparison-modal" role="dialog">
    <div class="modal-dialog modal-lg model-width">
      <!-- Modal content-->
        <div class="modal-content message-modal" style="width: 100%;">
            
        </div>
    </div>
</div>

<div class="modal fade" id="gtmetrix-history-modal" role="dialog">
    <div class="modal-dialog modal-xl model-width w-100">
      <!-- Modal content-->
        <div class="modal-content message-modal" style="width: 100%;">
            
        </div>
    </div>
</div>
@include('gtmetrix.setWebsite')
@endsection
    
@section('scripts')
<script>

$(document).on('click', '.expand-row-msg', function () {
    var name = $(this).data('name');
    var id = $(this).data('id');
    console.log(name);
    var full = '.expand-row-msg .show-short-'+name+'-'+id;
    var mini ='.expand-row-msg .show-full-'+name+'-'+id;
    $(full).toggleClass('hidden');
    $(mini).toggleClass('hidden');
  });


//$(".site-gtmetrix-data tbody>tr").append("<input type='checkbox' />");
    function setactive(id){
//   $(".nav-tabs li.nav-item a.nav-link").removeClass('active');

        if(id =="a_tab"){
            $("#PageSpeed").addClass("active");
            $("#YSlow").removeClass("active");
            console.log(id);
            
            }else{
            $("#YSlow").addClass("active");
            $("#PageSpeed").removeClass("active");
            console.log(id);

            }
        };


    $(document).on('click', '.show-pagespeed', function(e){
        e.preventDefault();
        var url = $(this).data('url');
        $('.message-modal').html(''); 
        $('#loading-image').show();     
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'html'
        })
        .done(function(data){
            $('.message-modal').html('');    
            $('.message-modal').html(data); // load response 
            $("#gtmetrix-stats-modal").modal("show");
            $('#loading-image').hide();        // hide ajax loader 
        })
        .fail(function(){
            toastr["error"]("Something went wrong please check log file");
            $('#loading-image').hide();
        });
    });
	
	$(document).on('click','#selectAll',function(){ 
		if($(this).prop('checked'))
		{
			$('.multi-run-test').prop('checked',true);
		}
		else
		{
			$('.multi-run-test').prop('checked',false);
		}
        
	});
   
	$(document).on('click', '.show-comparison', function(e){
        e.preventDefault();
        var url = $(this).data('url');
        $('#gtmetrix-comparison-modal .message-modal').html(''); 
        $('#loading-image').show();     
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'html'
        })
        .done(function(data){
            $('#gtmetrix-comparison-modal .message-modal').html('');    
            $('#gtmetrix-comparison-modal .message-modal').html(data); // load response 
            $("#gtmetrix-comparison-modal").modal("show");
            $('#loading-image').hide();        // hide ajax loader 
        })
        .fail(function(){
            toastr["error"]("Something went wrong please check log file");
            $('#loading-image').hide();
        });
    });

    const showHistory = (url) => {
        $('.message-modal').html(''); 
        $('#loading-image').show();     
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'html'
        })
        .done(function(data){
            $('#gtmetrix-history-modal .message-modal').html('');    
            $('#gtmetrix-history-modal .message-modal').html(data); // load response 
            $("#gtmetrix-history-modal").modal("show");

            $('#loading-image').hide();        // hide ajax loader 
            pageClick();
        })
        .fail(function(){
            toastr["error"]("Something went wrong please check log file");
            $('#loading-image').hide();
        });
    }

    const pageClick = () => {
        $('#gtmetrix-history-modal .pagination a.page-link').click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            let href = $(this).attr('href');
            showHistory(href);
            return false;
        });
    }

    $(document).on('click', '.show-history', function(e){
        e.preventDefault();
        var url = $(this).data('url');
        showHistory(url);
    });

    $(document).on("click",".run-test",function(e) {
        e.preventDefault();
        var btn = $(this);
        var id = $(this).data("id");
        $.ajax({
            url: "/gtmetrix/run-event",
            type: 'POST',
            data : { _token: "{{ csrf_token() }}", id : id },
            dataType: 'json',
            beforeSend: function () {
                $("#loading-image").show();
            },
            success: function(result){
                $("#loading-image").hide();
                if(result.code == 200) {
                    toastr["success"](result.message);
                }else{
                    toastr["error"](result.message);
                }
            },
            error: function (){
                $("#loading-image").hide();
                toastr["error"]("Something went wrong please check log file");
            }
        });
    });
    
        
	$(document).on('click','#delete-url',function(){
		var rowid = $(this).attr('data-value');
		$.ajax({
                url: "/gtmetrix/deleteurl",
                type: 'POST',
                data : { _token: "{{ csrf_token() }}", rowid : rowid},
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function(result){
                    $("#loading-image").hide();
                    if(result.code == 200) {
                        $('#infinite-scroll-cashflow-inner').load(window.location.href+'#infinite-scroll-cashflow-inner', function() {
							toastr["success"](result.message);
						});
                        
                    }else{
                        toastr["error"](result.message);
                    }
                },
                error: function (){
                    $("#loading-image").hide();
                    toastr["error"]("Something went wrong please check log file");
                }
            });
	});

    $(document).on('click','#run-current-url',function(){
		var rowid = $(this).attr('data-value');
		$.ajax({
                url: "/gtmetrix/run-current-url",
                type: 'POST',
                data : { _token: "{{ csrf_token() }}", rowid : rowid},
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function(result){
                    $("#loading-image").hide();
                    if(result.code == 200) {
                        $('#infinite-scroll-cashflow-inner').load(window.location.href+'#infinite-scroll-cashflow-inner', function() {
							toastr["success"](result.message);
						});
                        
                    }else{
                        toastr["error"](result.message);
                    }
                },
                error: function (){
                    $("#loading-image").hide();
                    toastr["error"]("Something went wrong please check log file");
                }
            });
	});
	$(document).on('click','.processToggle input',function(){
		
		var rowid = $(this).val();
		if ($(this).is(":checked"))
		{
			$('input[value='+rowid+']').prop('checked', true);
		}
		else
		{
			$('input[value='+rowid+']').prop('checked', false);
		}
		
	});
	
    function checkCheckbox() {  
	
		arrayList = [];
        $("input:checkbox[class=multi-run-test]:checked").each(function () {
            if(arrayList.length == 0){
                arrayList.push($(this).val()); 
            }
            else{
               let findValue = arrayList.find(item =>{
                if(item!==$(this).val()) 
                arrayList.push($(this).val());
                })
            }
         });
        console.log(arrayList);
        
        if(arrayList.length){
           
            $.ajax({
                url: "/gtmetrix/multi-add-in-process",
                type: 'POST',
                data : { _token: "{{ csrf_token() }}", arrayList : arrayList},
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function(result){
                    $("#loading-image").hide();
                    if(result.code == 200) {
                        $('#infinite-scroll-cashflow-inner').load(window.location.href+'#infinite-scroll-cashflow-inner', function() {
							toastr["success"](result.message);
						});
                        
                    }else{
                        toastr["error"](result.message);
                    }
                },
                error: function (){
                    $("#loading-image").hide();
                    toastr["error"]("Something went wrong please check log file");
                }
            })
			}else{

            toastr["error"]("Please select atleast one checkbox");
        }
        
    };
	
</script>

<script>
        
        var isLoading = false;
        var page = 1;
        $(document).ready(function () {
            
            $(window).scroll(function() {
                if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
                    loadMore();
                }
            });

            function loadMore() { 
              if (isLoading)
                    return;
                isLoading = true;
                var $loader = $('.infinite-scroll-products-loader');
                page = page + 1;
                const params = new URLSearchParams(window.location.search); 
                keyword = params.get('keyword');
                status = params.get('status');
                date = params.get('date');
                if(keyword == null) {
                    keyword = '';
                }
                if(status == null || status == 'null') { 
                    status = '';
                }
                if(date == null) {
                    date = '';
                }
                 var url = "{{url('gtmetrix')}}?ajax=1&page="+page+"&date="+date+"&status="+status+"&keyword="+keyword;
                
                console.log(url);

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: $('.form-search-data').serialize(),
                    beforeSend: function() {
                        $loader.show();
                    },
                    success: function (data) {
                        $loader.hide();
                        if('' === data.trim())
                            return;
                        $('.infinite-scroll-cashflow-inner').append(data);
                        

                        isLoading = false;
                    },
                    error: function () {
                        $loader.hide();
                        isLoading = false;
                    }
                });
            }            
        });


      $(document).on("click",".active-task",function(e) {
        let flag = $(this).attr('data-active');
        let id = $(this).attr('data-id');
        $.ajax({
            type: "POST",
            url: "{{route('gtmetrix.toggle.flag')}}", 
            data: {
                flag: flag,id: id, _token: "{{ csrf_token() }}", 
            }, 
            
            success: function (response) {
                if(response.status){
                    toastr['success'](response.message);
                }else{
                    toastr['error'](response.message);
                }
                setTimeout(function(){
                    window.location.reload(1);
                }, 1000);
            },
            error: function () {
                toastr['error']('Something went wrong!');
            }
        });
    });  

       

  </script>      
<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>

@endsection
