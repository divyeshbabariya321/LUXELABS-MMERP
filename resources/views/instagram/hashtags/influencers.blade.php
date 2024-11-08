@extends('layouts.app')

@section('favicon' , 'instagram.png')

@section('title', 'Influencer Info')

@section('styles')
<style type="text/css">
         #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }

        /* .navbar{
          height: 60px;
          background:#f1f1f1ad; 
        } */


        h1{
            font-size:30px;
            font-weight:600;
            padding: 20px 0;

        }

h2{
	font-size:24px;
	font-weight:600;

}

h3 {
	font-size:20px;
	font-weight:600;
        

}

p{ 
	font-size: 14px;
	font-weight: 400;
	margin-bottom: 15px;
}
/* 
a.navbar-brand{
    font-size: 22px;
	font-weight: 600;
	color: #5a5555;  
} */

.navbar-light .navbar-nav .nav-link{
    font-size: 14px;
	color: #757575;
}

#search_li input{
    height:35px !important;
	border: 1px !important;
	font-size:14px !important;
	/* max-width: */

}

.btn{
	padding: 6px 12px;
    font-size: 14px;
    font-weight: 400;
    text-align: center;
    white-space: nowrap;
    border-radius: 4px;
    color: #fff;
    cursor: pointer;
    
}

.btn:hover{
 background:#565656;
}

.btn:disabled{
	opacity : 0.5;
    pointer-events: none;
}

button[disabled]:hover {
    background: none;
}

.action-icon{
    width:20px;
    height:20px;
	padding:3px;
	border-radius: 4px;
}

.action-icon:hover{
    background-color: #ccc; 
    cursor: pointer;
}



        .chat-righbox a{
            color: #555 !important;
            font-size: 18px;
        }
        .type_msg.message_textarea {
            width: 90%;
            height: 60px;
        }
        .cls_remove_rightpadding{
            padding-right: 0px !important;
        }
        .cls_remove_leftpadding{
            padding-left: 0px !important;
        }
        .cls_remove_padding{
            padding-right: 0px !important;
            padding-left: 0px !important;
        }
        .cls_quick_commentadd_box{
            padding-left: 5px !important;   
            margin-top: 3px;
        }
        .cls_quick_commentadd_box button{
            font-size: 12px;
            padding: 5px 9px;
            margin-left: -8px;
            background: none;
        }
        .send_btn {
            margin-left: -5px; 
        }
        .cls_message_textarea{
            height: 35px !important;
            width: 100% !important;
        }
        .cls_quick_reply_box{
            margin-top: 5px;
        }
        .cls_addition_info {
            padding: 0px 0px;
            margin-top: -8px;
        }
        #circle {
        background: green;
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    .pd-2 {
        padding:2px;
    }

    table{
        border: 1px;
	    border-radius: 4px;  
        /* font-size: 13px;  */
        word-break: break-all;
    }
    
    .table>thead>tr>th{
        font-weight: normal;
        font-size: 15px;
        color: #000;
    }

    .table>tbody>tr>td{
        font-weight: normal;
        font-size: 14px;
        color: #757575;
    }

   .form-control{
        height:35px !important;
	    font-size:14px !important;
	    border-radius: 4px !important;
    }

    </style>
@endsection
@section('large_content')
    <div id="myDiv">
      <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
   </div>
    <div class="row">
        <div class="col-md-12 p-0">
           <h2 class="page-heading">Influencers (<span id="total">{{ $influencers->total() }}</span>)</h2>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    <form class="form-inline" action="{{ route('influencers.index') }}" method="GET">
                        <div class="form-group mr-2">
                            <input name="term" type="text" class="form-control" id="term"
                                value="{{ isset($term) ? $term : '' }}"
                                placeholder="Search..." style="width:267px !important">
                        </div>
                        <div class="form-group mr-2">
                            <select class="form-control" name="posts" id="" style="width:267px !important">
                                <option value="">Posts</option>
                                <option value="10" {{$posts == 10 ? 'selected' : ''}}>Greater than 10</option>
                                <option value="20" {{$posts == 20 ? 'selected' : ''}}>Greater than 20</option>
                                <option value="30" {{$posts == 30 ? 'selected' : ''}}>Greater than 30</option>
                                <option value="50" {{$posts == 50 ? 'selected' : ''}}>Greater than 50</option>
                                <option value="100" {{$posts == 100 ? 'selected' : ''}}>Greater than 100</option>
                            </select>
                        </div>
                        <div class="form-group mr-2">
                            <select class="form-control" name="followers" id="" style="width:267px !important">
                                <option value="">Followers</option>
                                <option value="10" {{$followers == 10 ? 'selected' : ''}}>Greater than 10</option>
                                <option value="20" {{$followers == 20 ? 'selected' : ''}}>Greater than 20</option>
                                <option value="30" {{$followers == 30 ? 'selected' : ''}}>Greater than 30</option>
                                <option value="50" {{$followers == 50 ? 'selected' : ''}}>Greater than 50</option>
                                <option value="100" {{$followers == 100 ? 'selected' : ''}}>Greater than 100</option>
                            </select>
                        </div>
                        <div class="form-group mr-2">
                            <select class="form-control" name="following" id="" style="width:267px !important">
                                <option value="">Following</option>
                                <option value="10" {{$following == 10 ? 'selected' : ''}}>Greater than 10</option>
                                <option value="20" {{$following == 20 ? 'selected' : ''}}>Greater than 20</option>
                                <option value="30" {{$following == 30 ? 'selected' : ''}}>Greater than 30</option>
                                <option value="50" {{$following == 50 ? 'selected' : ''}}>Greater than 50</option>
                                <option value="100" {{$following == 100 ? 'selected' : ''}}>Greater than 100</option>
                            </select>
                        </div>
                        <div class="form-group mr-2">
                            <input type="date" name="date" id="date11" class="form-control"style="width:276px !important">
                            <button type="submit" class="btn btn-image3 btn-sm text-dark">
                                <i class="fa fa-filter"></i>
                            </button>
                            <!-- <button type="button" class="btn btn-image" onclick="resetSearch()"><img src="/images/clear-filters.png"/></button>  -->
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-inline" >
                    <div class="form-group mr-2" >
                        {{ html()->form('POST', route('instagram.change.mailing', ))->style('display:inline')->open() }}
                        @if($runMailingCommand==1)
                        <button type="submit" class="btn btn-xs btn-secondary">Stop Mailing Command</button>
                        @else
                        <button type="submit" class="btn btn-xs btn-secondary">Start Mailing Command</button>
                        @endif
                        {{ html()->form()->close() }}
                        <a href="{{url('instagram/addmailinglist')}}" class="btn btn-secondary " style="width:180px !important;line-height: 1.5; background-color: #f5f5f5;color: #555; border-color: #ddd; text-align:left;" >Ceate Mailing List</a> 
                    </div>
                    <div class="form-group mr-2" >    
                        <a href="{{url('instagram/addmailinglist')}}" class="btn btn-secondary " style="width:180px !important;line-height: 1.5; background-color: #f5f5f5;color: #555; border-color: #ddd; text-align:left;" >Ceate Mailing List</a> 
                    </div>      
                    <div class="form-group mr-2" >    
                        <a href="#" class="btn btn-secondary  mailToInfluencers" style="width:180px !important; line-height: 1.5; background-color: #f5f5f5;color: #555; border-color: #ddd; text-align: left;" >Send Mail</a> 
                    </div>      
                    <div class="form-group mr-2">    
                        <button class="btn btn-secondary " onclick="sortData()" style="width:180px !important; line-height: 1.5; background-color: #f5f5f5;color: #555;border-color: #ddd;text-align: left;">Sort Data</button> 
                    </div>        
                    <div class="form-group mr-2" >
                            <input name="name" type="text" class="form-control" style="width:180px !important;" id="keywordname" placeholder="New Keyword">
                    </div>
                    <div class="form-group mr-2"style="width:180px !important;">
                            {{ html()->select('instagram_account_id', $accountsLists)->class("form-control select2")->id('instagram_account_id') }}
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-image btn-xs text-dark" onclick="submitKeywork()" style="margin-left:11px;"><i class="fa fa-plus"></i></button> 
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-10">
                     <div class="panel-group">
                        <div class="panel mt-3 panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title"style="color: #555;">
                            <a data-toggle="collapse" href="#collapse1">Keywords</a>
                        </h4> 
                            </div>
                        </div>
                     </div>
                </div>
                <div class="col-md-2 mt-3" style="padding:0px;">
                       <a href="#" class="btn direct-message btn-secondary" style="width:209px;line-height: 1.6;background-color: #f5f5f5;color: #555; text-align: left;margin-top: 0;margin-bottom: 0;border-color: #ddd;"> Message</a>
                    </div>
            </div>
        </div>


        
        <div class="col-md-12 ">
             <div class="row">
                <div id="collapse1" class="panel-collapse collapse">
                       <table class="table table-bordered table-striped" id="phone-table">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Platform</th>
                                    <th>Wait time</th>
                                    <th>No of Request</th>
                                    <th>Account</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                               @foreach($keywords as $keyword)     
                                <tr data-keyword-id="{{$keyword->id}}" data-keyword-name="{{ $keyword->name }}">
                                   <td>{{ $keyword->name }}</td>
                                   <td>
                                        <div class="">
                                             {{ html()->select('platform', ["py_instagram" => "Py Instagram", "py_facebook" => "Py Facebook"])->class("form-control select2 platform-request") }}
                                        </div>
                                   </td>
                                   <td>
                                        <div class="">
                                             {{ html()->text('wait_time', $keyword->wait_time)->class("form-control wait-time") }}
                                        </div>
                                   </td>
                                   <td>
                                        <div class="">
                                             {{ html()->text('no_of_request', $keyword->no_of_requets)->class("form-control no-of-request") }}
                                        </div>
                                   </td>
                                   <td>
                                        <div class="">
                                             {{ html()->select('instagram_account_id', $accountsList, $keyword->instagram_account_id)->class("form-control select2")->id('instagram_account_id_change') }}
                                        </div>
                                   </td>
                                   <td><button class="btn btn-link btn-xs text-dark" onclick="getImage('{{ $keyword->name }}')" data-toggle="tooltip" data-placement="top" title="Image From Scrapper"><i class="fa fa-picture-o"></i></button>
                                   <button  class="btn btn-link btn-xs text-dark" title="Get Status" onclick="getStatus('{{ $keyword->name }}')" title="Get Status Of Scrapper"><i class="fa fa-info-circle" aria-hidden="true"></i></button> 
                                   <button class="btn btn-link btn-xs text-dark" onclick="startScript('{{ $keyword->name }}',this)" data-toggle="tooltip" data-placement="top" title="Start Script"><i class="fa fa-play"></i></button> 
                                   <button class="btn btn-link btn-xs text-dark" onclick="stopScript('{{ $keyword->name }}',this)" data-toggle="tooltip" data-placement="top" title="Stop Script From Server"><i class="fa fa-pause"></i></button> 
                                   <button class="btn btn-link btn-xs text-dark" onclick="restartScript('{{ $keyword->name }}')" data-toggle="tooltip" data-placement="top" title="Restart Script From Server"><i class="fa fa-refresh"></i></button> 
                                   <!--<button class="btn btn-link btn-xs text-dark" onclick="getLog('{{ $keyword->name }}')" data-toggle="tooltip" data-placement="top" title="Get Log From Server"><i class="fa fa-history"></i></button> !-->
                                   <button type="button" data-id="{{$keyword->name}}" class="btn btn-log-instances btn-xs text-dark"><i class="fa fa-history" aria-hidden="true"></i></button>
                                   <button class="btn btn-link btn-xs task-history text-dark" data-id="{{ $keyword->name }}" data-placement="top" title="Show server history"><i class="fa fa-history"></i></button>
                                  
                                 
                  
                                </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    
<div class="row" style="padding:0px;margin:0px;">
        
    </div>
            <div class="col-md-12"style="padding-right: 28px;">
            <div class="table-responsive mt-2">
                <table class="table-striped table table-bordered" id="data-table" style="table-layout:fixed;">
                    <thead >
                    <tr>

                        <th style="width:6%">#</th>
                        <th style="width:7%">Platform</th>
                        <th style="width:5%">Date</th>
                        <th style="width:7%">Username</th>
                        <th style="width:6%">Email</th>
                        <th style="width:7%">Hashtag</th>
                        <th style="width:5%">Posts</th>
                        <th style="width:7%">Followers</th>
                        <th style="width:7%">Following</th>
                        <th style="width:6%">Country</th>
                        <th style="width:8%">Description</th>
                        <th style="width:9%">Sender</th>
                        <th style="width:20%">Communication</th>
                        <th style="width:10%">Auto Reply</th>
                        <th style="width:5%">Action</th>
                        <!-- <th>Phone</th>
                        <th>Website</th>
                        <th>Twitter</th>
                        <th>Facebook</th>
                        <th>Keyword</th> -->
                    </tr>
                   </thead>
                   <tbody class="pending-row-render-view infinite-scroll-cashflow-inner">
                   @include('instagram.hashtags.partials.influencer-data')
                   
                    </tbody>
                </table>
                
                 {!! $influencers->render() !!}
            </div>
        </div>
        </div>

        
    </div>
    <img class="infinite-scroll-products-loader center-block" src="{{asset('/images/loading.gif')}}" alt="Loading..." style="display: none" />

    <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog" style="width: 1000px; max-width: 1000px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                </div>
                <div class="modal-body" style="background-color: #999999;" id="direct-modal-chat">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
	
	<div id="mailingListTemplate" class="modal fade" role="dialog">
          <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content ">
      <div class="modal-header">
                    <h4 class="modal-title">Mailing Template</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="" id="mailToInfluencersForm" method="POST">
                    @csrf
                    <div class="modal-body">
                            <div class="col-md-12">
                                <div class="col-md-2">
                                    <strong>Template:</strong>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
										{{ html()->select('mailing_list', $mailingListTemplates)->class('form-control') }}
                                    </div>
                                </div>
                            </div>

                        </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Send</button>
                    </div>
                </form>
      </div>
    </div>
    </div>


    <div id="directMessageModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content ">
      <div class="modal-header">
                    <h4 class="modal-title">Direct message</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="" id="directMessageForm" method="POST">
                    @csrf
                    <div class="modal-body">
                            <div class="col-md-12">
                                <div class="col-md-2">
                                    <strong>Account:</strong>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                    <select class="form-control account-search select2" name="account_id" data-placeholder="Sender...">
                                        <option value="">Select sender...</option>
                                        @foreach ($accounts as $key => $account)
                                            <option value="{{ $key }}">{{ $account }}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="col-md-2">
                                    <strong>Message:</strong>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                    <textarea cols="45" class="form-control" name="message"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Send</button>
                    </div>
                </form>
      </div>
    </div>
</div>

<div id="manage-log-instance" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Instagram Logs</h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@include("marketing.whatsapp-configs.partials.image")
@include('instagram.hashtags.partials.influencer-history')
@section('scripts')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script type="text/javascript">

        $(".select2-quick-reply").select2( { tags: true } );
        $(document).on("change", ".quickComments", function (e) {
            var message = $(this).val();
            var select = $(this);
            if ($.isNumeric(message) == false) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/instagram/influencers/reply/add",
                    dataType: "json",
                    method: "POST",
                    data: {reply: message}
                }).done(function (data) {
                    var vendors_id =$(select).find("option[value='']").data("vendorid");
                    var message_re = data.data.reply;
                    $("textarea#message"+vendors_id).val(message_re);
                    console.log(data)
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });
            }
            var vendors_id =$(select).find("option[value='']").data("vendorid");
            var message_re = $(this).find("option:selected").html();
            $("textarea#message"+vendors_id).val($.trim(message_re));
        });

        $(document).on("click", ".delete_quick_comment", function (e) {
            var deleteAuto = $(this).closest(".d-flex").find(".quickComments").find("option:selected").val();
            if (typeof deleteAuto != "undefined") {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: BASE_URL+"/instagram/influencers/reply/delete",
                    dataType: "json",
                    method: "POST",
                    data: {id: deleteAuto}
                }).done(function (data) {
                    if (data.code == 200) {
                        $(".quickComment").each(function(){
                        var selecto=  $(this)
                            $(this).children("option").not(':first').each(function(){
                            $(this).remove();
                            });
                            $.each(data.data, function (k, v) {
                                $(selecto).append("<option  value='" + k + "'>" + v + "</option>");
                            });
                            $(selecto).select2({tags: true});
                        });
                    }
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });
            }
        });

        $(document).on("click",".task-history",function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            $.ajax({
                url: '/instagram/influencers/history',
                type: 'POST',
                data : { _token: "{{ csrf_token() }}", id : id },
                dataType: 'json',
                beforeSend: function () {
                    $("#loading-image").show();
                },
                success: function(result){
                    $("#loading-image").hide();
                    if(result.code == 200) {
                        var t = '';
                        $.each(result.data,function(k,v) {
                            t += `<tr><td>`+v.influencers_name+`</td>`;
                            t += `<td>`+v.title+`</td>`;
                            t += `<td>`+v.description+`</td>`;
                            t += `<td>`+v.created_at+`</td></tr>`;
                        });
                        if( t == '' ){
                            t = '<tr><td colspan="5" class="text-center">No data found</td></tr>';
                        }
                    }
                    $("#category-history-modal").find(".show-list-records").html(t);
                    $("#category-history-modal").modal("show");
                },
                error: function (){
                    $("#loading-image").hide();
                }
            });
        });

        $(document).on('click', '.expand-row', function () {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });

        $('select.select2').select2({
                tags: true,
                width: "100%"
            });

         $(document).ready(function() {
        src = "{{ route('influencers.index') }}";
        $(".global").autocomplete({
        source: function(request, response) {
            term = $('#term').val();
            
            
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    term : term,
                },
                beforeSend: function() {
                       $("#loading-image").show();
                },
            
            }).done(function (data) {
                 $("#loading-image").hide();
                console.log(data);
                $('#total').val(data.total)
                $("#data-table tbody").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
                
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        },
        minLength: 1,
       
        });
    });

     function resetSearch(){
         blank = '';
         $.ajax({
                url: src,
                dataType: "json",
                data: {
                    blank : blank,
                },
                beforeSend: function() {
                       $("#loading-image").show();
                },
            
            }).done(function (data) {
                 $("#loading-image").hide();
                console.log(data);
                $('#total').val(data.total)
                $("#data-table tbody").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
                
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
     }

     $(document).on("change","#instagram_account_id_change",function() {
           name = $(this).closest("tr").data("keyword-name");
           instagram_account_id = $(this).val();
           $.ajax({
               url: '{{ route('influencers.keyword.save') }}',
               type: 'POST',
               dataType: 'json',
               data: {
                    name: name,
                    instagram_account_id: instagram_account_id,
                    "_token": "{{ csrf_token() }}",
                },
           })
           .done(function(response) {
               alert(response.message);
               //location.reload();
           })
           .fail(function() {
               console.log("error");
           });
     }); 

     function submitKeywork() {
           name = $('#keywordname').val();
           instagram_account_id = $('#instagram_account_id').val();
           $.ajax({
               url: '{{ route('influencers.keyword.save') }}',
               type: 'POST',
               dataType: 'json',
               data: {
                    name: name,
                    instagram_account_id: instagram_account_id,
                    "_token": "{{ csrf_token() }}",
                },
           })
           .done(function(response) {
               $('#keywordname').val('')
               alert(response.message);
               location.reload();
           })
           .fail(function() {
               console.log("error");
           });
        } 

     function getImage(name) {
              $.ajax({
               url: '{{ route('influencers.image') }}',
               type: 'POST',
               dataType: 'json',
               data: {
                    name: name,
                    "_token": "{{ csrf_token() }}",
                },
               })
               .done(function(response) {
                    if(response.success == true){
                        $('#image_crop').attr('src',response.message);
                        $('#largeImageModal').modal('show');
                    }else{
                        alert(response.message)
                    }
                    
               })
               .fail(function() {
                   console.log("error");
               });
          }
        function getStatus(name) {
              $.ajax({
               url: '{{ route('influencers.status') }}',
               type: 'POST',
               dataType: 'json',
               data: {
                    name: name,
                    "_token": "{{ csrf_token() }}",
                },
               })
               .done(function(response) {
                   alert(response.message);
               })
               .fail(function() {
                   console.log("error");
               });
          }
          function startScript(name,ele) {
            var platform = $(ele).closest("tr").find(".platform-request").val();
            var wait_time = $(ele).closest("tr").find(".wait-time").val();
            var no_of_request = $(ele).closest("tr").find(".no-of-request").val();

            var result = confirm("You Want to start this script "+name+"?");
            if(result){
                $.ajax({
                   url: '{{ route('influencers.start') }}',
                   type: 'POST',
                   dataType: 'json',
                   data: {
                        name: name,
                        platform : platform,
                        wait_time : wait_time,
                        no_of_request : no_of_request,
                        "_token": "{{ csrf_token() }}",
                    },
                   })
                   .done(function(response) {
                       alert(response.message);
                   })
                   .fail(function() {
                       console.log("error");
                }); 
            }
             
          }

          function getLog(name) {
            var result = confirm("You Want the log for this script "+name+"?");
            if(result){
                $.ajax({
                   url: '{{ route('influencers.log') }}',
                   type: 'POST',
                   dataType: 'json',
                   data: {
                        name: name,
                        "_token": "{{ csrf_token() }}",
                    },
                   })
                   .done(function(response) {
                       if(response.message == 'No Logs Available'){
                          alert(response.message);
                       }else{
                          openInNewTab(response.message)
                       } 
                   })
                   .fail(function() {
                       console.log("error");
                }); 
            }
             
          }
          function restartScript(name) {
            var result = confirm("You Want to re-start this script "+name+"?");
            if(result){
                $.ajax({
                   url: '{{ route('influencers.restart') }}',
                   type: 'POST',
                   dataType: 'json',
                   data: {
                        name: name,
                        "_token": "{{ csrf_token() }}",
                    },
                   })
                   .done(function(response) {
                       alert(response.message);
                   })
                   .fail(function() {
                       console.log("error");
                }); 
            }
             
          }
          function stopScript(name,ele) {
            var platform = $(ele).closest("tr").find(".platform-request").val();
            var result = confirm("You Want to stop this script "+name+"?");
            if(result){
                $.ajax({
                   url: '{{ route('influencers.stop') }}',
                   type: 'POST',
                   dataType: 'json',
                   data: {
                        name: name,
                        platform : platform,
                        "_token": "{{ csrf_token() }}",
                    },
                   })
                   .done(function(response) {
                       alert(response.message);
                   })
                   .fail(function() {
                       console.log("error");
                }); 
            }
             
          } 

          function openInNewTab(url) {
            var win = window.open(url, '_blank');
            win.focus();
          }   
          
          $(document).on('click', '.expand-row-msg', function () {
            var name = $(this).data('name');
			var id = $(this).data('id');
            var full = '.expand-row-msg .show-short-'+name+'-'+id;
            var mini ='.expand-row-msg .show-full-'+name+'-'+id;
            $(full).toggleClass('hidden');
            $(mini).toggleClass('hidden');
        });

        $(document).on('click', '.send_btn', function () {
			var id = $(this).data('id');
            var account_id = $(".account-search-"+id).val();
            var message = $('#message'+id).val();
            $.ajax({
                url: '{{ route('direct.send-message') }}',
                type: 'POST',
                dataType: 'json',
                data: {
                            "_token": "{{ csrf_token() }}", 
                            "message" : message,
                            "influencer_id" : id,
                            "account_id" : account_id,
                       },
                    })
                    .done(function() {
                        $('#message'+id).val('');
                        toastr['success']('Successfull', 'success');
                    })
                    .fail(function(error) {
                        toastr['error'](error.responseJSON.message, 'error');
                    })
        });


        
        function sendMessage(id){
                message = $('#message'+id).val();
                if(sendMessage){
                    $.ajax({
                        url: '{{ route('direct.send') }}',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            "_token": "{{ csrf_token() }}", 
                            "message" : message,
                            "thread_id" : id,
                       },
                    })
                    .done(function() {
                        $('#message'+id).val('');
                        console.log("success");
                    })
                    .fail(function() {
                        console.log("error");
                    })
                    .always(function() {
                        console.log("complete");
                    });
                    
                }else{
                    alert('Please Select Text')
                }
            }

        var selectedInfluencers = [];
         $(document).on('click', '.selectedInfluencers', function () {
            var checked = $(this).prop('checked');
            var id = $(this).val();
             if (checked) {
                selectedInfluencers.push(id);
            } else {
                var index = selectedInfluencers.indexOf(id);
                selectedInfluencers.splice(index, 1);
            }
        });

        $(document).on("click",".direct-message",function(e){
          e.preventDefault();
          if(selectedInfluencers.length < 1) {
            toastr['error']("Select few influencers first");
            return;
          }
          $('#directMessageModal').modal('show');
        });
		
		 $(document).on("click",".mailToInfluencers",function(e){
          e.preventDefault();
          if(selectedInfluencers.length < 1) {
            toastr['error']("Select few influencers first");
            return;
          }
          $('#mailingListTemplate').modal('show');
        });

        $(document).on('submit', '#directMessageForm', function (e) {
                e.preventDefault();
                var data = $(this).serializeArray();
                var account_id = $('.account-search').val();
                data.push({name: 'selectedInfluencers', value: selectedInfluencers});
                $.ajax({
                    url: "{{route('direct.group-message')}}",
                    type: 'POST',
                    data: data,
                    success: function (response) {
                        toastr['success']('Successful', 'success');
                        $('#directMessageModal').modal('hide');
                        $("#directMessageForm").trigger("reset");
                        $("#data-table tr").find('.selectedInfluencers').each(function () {
                          if ($(this).prop("checked") == true) {
                            $(this).prop("checked", false);
                          }
                        });
                        selectedInfluencers = [];
                    },
                    error: function (error) {
                        toastr['error'](error.responseJSON.message, 'error');
                    }
                });
        });
		
		$(document).on('submit', '#mailToInfluencersForm', function (e) {
                e.preventDefault();
                var data = $(this).serializeArray();
                var account_id = $('.account-search').val();
                data.push({name: 'selectedInfluencers', value: selectedInfluencers});
                $.ajax({
                    url: "{{route('send.mail-influencer')}}",
                    type: 'POST',
                    data: data,
                    success: function (response) {
                        toastr['success']('Successful', 'success'); 
                        $('#mailingListTemplate').modal('hide');
                        $("#mailToInfluencersForm").trigger("reset");
                        $("#data-table tr").find('.selectedInfluencers').each(function () {
                          if ($(this).prop("checked") == true) {
                             $(this).prop("checked", false);
                          }
                        });
                        selectedInfluencers = [];
                    },
                    error: function (error) {
                        toastr['error'](error.responseJSON.message, 'error');
                    }
                });
        });

        $(document).on('click', '.load-direct-chat-model', function () {
            $.ajax({
                url: '{{ route('direct.infulencers-messages') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        id : $(this).data("id")
                    },
                beforeSend: function() {
                    
                },

                }).done(function (data) {
                    $('#direct-modal-chat').empty().append(data.messages);
                    $('#chat-list-history').modal('show');
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });

            });
            $(document).on('click', '.latest-post', function () {
            $.ajax({
                url: '{{ route('direct.latest-posts') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        id : $(this).data("id")
                    },
                beforeSend: function() {
                    $("#loading-image").show();
                },

                }).done(function (data) {
                    $("#loading-image").hide();
                }).fail(function (error) {
                    console.log(error);
                    $("#loading-image").hide();
                    toastr['error'](error.responseJSON.message);
                });

            });

            $(document).on('click', '.expand-row-btn', function () {
            var id = '#expand-'+$(this).data('id');
            console.log($(this).data('id'));
            console.log(id);
            $(id).toggleClass('dis-none');
        });


        function sortData() {
                
                $.ajax({
                url: '{{ route('influencers.sort') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        '_token': "{{ csrf_token() }}",
                    },
                beforeSend: function() {
                    $("#loading-image").show();
                },

                }).done(function (data) {
                    $("#loading-image").hide();
                    toastr['success'](data);
                }).fail(function (error) {
                    console.log(error);
                    $("#loading-image").hide();
                    toastr['error'](error);
                });

            
        }   


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
                $.ajax({
                    url: "{{url('instagram/influencers')}}?ajax=1&page="+page,
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


        $(document).on("click",".btn-log-instances",function(e) {
            e.preventDefault();
            var $id = $(this).data("id");
            var $date=  $('#date11').val();
            $.ajax({
                url: '{{url("instagram/influencers/get-log")}}',
                method:"get",
                data : {
                    id : $id,
                    date : $date
                },
                success: function (data) {
                    if(data.type=="success"){
                          $("#manage-log-instance").find(".modal-body").html(data.response);
                          $("#manage-log-instance").modal('show'); 
                    }else{
                        alert(data.response)                     }
                },
            });
        });
    </script>

@endsection