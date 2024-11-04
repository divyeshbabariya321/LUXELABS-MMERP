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
                <h2 class="page-heading">Scrapper Url list </h2>
                <!--pending products count-->
                <!--attach Product-->
                <!--Product Search Input -->
            </div>
        </div>
    </div>
        <div class="row">
        <div class="col-lg-12 margin-tb mb-3">
            <?php $base_url = URL::to('/'); ?>
            <div class=" cls_filter_box" style="margin-left: -13px;">
                <form class="form-inline form-search-data" action="{{ route('scrapper.image.urlList') }}" method="GET">
                    <div class="col-md-2 pl-5">
                       <select class="form-control" name="flt_website" id="flt_website">
                            <option value>Select Website</option>
                            @foreach($storeWebsites  as $storeWebsite)
                            <option value="<?php echo $storeWebsite->id; ?>"><?php echo $storeWebsite->title; ?></option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 pd-sm pd-rt">
                        <input type="text" style="width:100%;" name="scrapper_url" id="scrapper_url"
                            placeholder="URL" class="form-control">
                    </div>
                    <button type="submit" style="padding: 5px;margin-top:-1px;margin-left: 10px;" class="btn btn-image"
                        id="show"><img src="<?php echo $base_url; ?>/images/filter.png" /></button>                    
                </form>
                
            </div>
        </div>

    </div>

    @include('partials.flash_messages')

 

    <div class="col-md-12 margin-tb">
        <div class="table-responsive">
            <table class="table table-bordered" >
                <thead>
                    <th style="width:5%">Id</th>
                    <th style="width:15%">Website</th>
                    <th style="width:60%">URL</th>
                    <th style="width: 20%;">Action</th>
                </thead>
                <tbody class="infinite-scroll-data">
                @foreach ($urls  as $item)
                    <tr id = "{{$item->id}}">
                        <td>{{$item->id}}</td>
                        <td>{{$item->wtitle}}</td>
                        <td>{{$item->url}}</td>
                        <td>
                        @if ($item->is_flaged_url==1)
                        {{ html()->form('POST', route('scrapper.url.flag', [$item->id]))->style('display:inline')->open() }}
                        <button type="submit" class="btn btn-image"><img src="{{config('app.url')}}/images/starred.png" /></button>
                        {{ html()->form()->close() }}
                        @else
                        {{ html()->form('POST', route('scrapper.url.flag', [$item->id]))->style('display:inline')->open() }}
                        <button type="submit" class="btn btn-image"><img src="{{config('app.url')}}/images/unstarred.png" /></button>
                        {{ html()->form()->close() }}
                        @endif
                        <button type="button" class="btn count-dev-customer-tasks" title="Show task history" data-id="{{$item->store_website}}"><i class="fa fa-info-circle"></i></button>


                          

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
        {{ $urls->appends(request()->except('page'))->links() }}
    </div>

    <div id="dev_task_statistics" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Dev Task statistics</h2>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Task type</th>
                                    <th>Task Id</th>
                                    <th>Assigned to</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="dev_task_statistics_content">

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
 <script>
      
      $(document).ready(function() { 
        
     <?php if($flagUrl!= "") { ?>
        if($('{{$flagUrl}}').length){
            var rowpos = $('{{$flagUrl}}').position();
          //  alert(rowpos);
         $(window).scrollTop(rowpos.top);
            $('{{$flagUrl}}').css('background-color', 'yellow');
        }
     <?php } ?>
     $(document).on("click",".delete-dev-task-btn",function() {
		var x = window.confirm("Are you sure you want to delete this ?");
            if(!x) {
                return;
            }
            var $this = $(this);
            var taskId = $this.data("id");
			var tasktype = $this.data("task-type");
            if(taskId > 0) {
                $.ajax({
                    beforeSend : function() {
                        $("#loading-image").show();
                    },
                    type: 'get',
                    url: "{{route('site.development.delete.task')}}",
                    headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
                    data: {id : taskId,tasktype:tasktype},
                    dataType: "json"
                }).done(function (response) {
                    $("#loading-image").hide();
                    if(response.code == 200) {
                        $this.closest("tr").remove();
                    }
                }).fail(function (response) {
                    $("#loading-image").hide();
                    alert('Could not update!!');
                });
            }

        });

   
     $(document).on("click", ".count-dev-customer-tasks", function() {

         $this = $(this);
// var user_id = $(this).closest("tr").find(".ucfuid").val();
        var site_id = $(this).data("id");
        $.ajax({
                type: 'get',
                url: '{{route("get.site.development.task")}}',
                data: {site_id:site_id},
                dataType: "json",
                beforeSend: function() {
                    $("#loading-image").show();
                },
                success: function(data) {
                    var table='';
                    for (var i = 0; i < data.taskStatistics.length; i++) {
                        var str = data.taskStatistics[i].subject;
                        var res = str.substr(0, 100);
                        var status = data.taskStatistics[i].status;
                        if(typeof status=='undefined' || typeof status=='' || typeof status=='0' ){ status = 'In progress'};
                        table = table + '<tr><td>' + data.taskStatistics[i].task_type + '</td><td>#' + data.taskStatistics[i].id + '</td><td class="expand-row-msg" data-name="asgTo" data-id="'+data.taskStatistics[i].id+'"><span class="show-short-asgTo-'+data.taskStatistics[i].id+'">'+data.taskStatistics[i].assigned_to_name.replace(/(.{6})..+/, "$1..")+'</span><span style="word-break:break-all;" class="show-full-asgTo-'+data.taskStatistics[i].id+' hidden">'+data.taskStatistics[i].assigned_to_name+'</span></td><td class="expand-row-msg" data-name="res" data-id="'+data.taskStatistics[i].id+'"><span class="show-short-res-'+data.taskStatistics[i].id+'">'+res.replace(/(.{7})..+/, "$1..")+'</span><span style="word-break:break-all;" class="show-full-res-'+data.taskStatistics[i].id+' hidden">'+res+'</span></td><td>' + status + '</td><td><div class="col-md-10 pl-0 pr-1"><textarea rows="1" style="width: 100%; float: left;" class="form-control quick-message-field input-sm" name="message" placeholder="Message"></textarea></div><div class="p-0"><button class="btn btn-sm btn-xs send-message" title="Send message" data-taskid="'+ data.taskStatistics[i].id +'"><i class="fa fa-paper-plane"></i></button></div></td><td><button type="button" class="btn btn-xs load-communication-modal load-body-class" data-object="' + data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i].id + '" title="Load messages" data-dismiss="modal"><i class="fa fa-comments"></i></button>';
                        table = table + '<a href="javascript:void(0);" data-task-type="'+data.taskStatistics[i].task_type +'" data-id="' + data.taskStatistics[i].id + '" class="delete-dev-task-btn btn btn-xs"><i class="fa fa-trash"></i></a>';
                        table = table + '<button type="button" class="btn btn-xs  preview-img pd-5" data-object="' + data.taskStatistics[i].message_type + '" data-id="' + data.taskStatistics[i].id + '" data-dismiss="modal"><i class="fa fa-list"></i></button></td>';
                        table = table + '</tr>';
                    }
                   // table = table + '</table></div>';
                    $("#loading-image").hide();
                    $(".modal").css("overflow-x", "hidden");
                    $(".modal").css("overflow-y", "auto");
                    $("#dev_task_statistics_content").html(table);
                    $("#dev_task_statistics").modal();
                },
                error: function(error) {
                    console.log(error);
                    $("#loading-image").hide();
                }
            });


    });
      });

     
 </script>
 @endsection