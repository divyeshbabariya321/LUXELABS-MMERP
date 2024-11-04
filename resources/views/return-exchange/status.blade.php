@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'List | Return Exchange')

@section('large_content')

<div class="row" id="return-exchange-page">
    <div class="col-lg-12 margin-tb">
         
        <div class="row">
            <div class="col-12">
                <h2 class="page-heading">Return Exchange Orders Status</h2>
              </div>
    
              <div class="col-12 mb-3">
                <div class="pull-left">
                <form class="form-inline" action="{{ route('store-website.all.status') }}" method="GET">
                <div class="form-group ml-4">
                        <select class="form-control select2" id="change-website">
                          <option value="">Select a store</option>
    
                            @foreach ($websites as $website)
                                <option value="{{$website->id}}">{{$website->title}}</option>
                            @endforeach
                        </select>
                      </div>
                    </form>
                </div>
                <div class="pull-right">
                    <a class="btn btn-secondary" id="fetch-store-status">Fetch Store Status</a>
                    <a class="btn btn-secondary" id="add-new-btn">+</a>
                </div>
            </div>
        </div> 
        <div class="col-md-12 margin-tb infinite-scroll" id="page-view-result">
            <table class="table table-bordered" style="table-layout:fixed;">
            <thead>
              <tr>
                <th width="2%">Id</th>
                <th>Status</th>
                <th>Message</th>
                <th width="5%">Action</th>
              </tr>
            </thead>
            <tbody id="content_data">
                
            </tbody>
          </table>          
        </div>
    </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>

<div id="new-status" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <input type="text" id="website_status_name" class="form-control validate" placeholder="Please enter status">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="saveStatus()">Save changes</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
    <script type="text/javascript">

        function saveStatus(){

            if($('#change-website').val() === ''){
                toastr["error"]("Please select website");
                return false;
            }

            websiteid = $('#change-website').val() 
               
            var status = $('#website_status_name').val();

            if(status == ''){
                toastr["error"]("Please enter status");
                return false;
            }
            $.ajax({
                url: "/return-exchange/status/save",
                type: "post",
                data: {
                    id: websiteid,
                    status: status,
                    _token: '{{csrf_token()}}'
                },
                beforeSend: function () {
                    $("#loading-image").show();
                }  
            }).done(function (data) {
                $("#loading-image").hide();
                $('#new-status').modal('hide')
                $('#website_status_name').val('')
                $('#content_data').append(data);
                toastr["success"]("data updated successfully");
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                $("#loading-image").hide();
                toastr["error"](thrownError);
            });
        };

        $('#change-website').change(
            function(){
                websiteid = $(this).val()

                if(websiteid === '' || websiteid === null){
                    $('#content_data').html('')
                    return false
                }

                $.ajax({
                    url: "/return-exchange/status/",
                    type: "post",
                    data: {
                        "id" : websiteid,
                        "_token" : "{{ csrf_token() }}"
                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    }  
                }).done(function (data) {
                    $("#loading-image").hide();
                    $('#content_data').append(data);
                    toastr["success"]("fetch data successfully");
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    $("#loading-image").hide();
                    toastr["error"](jqXHR.responseText);
                });
            }
        );

        $('#fetch-store-status').click(
            function(){
                
               if($('#change-website').val() === ''){
                toastr["error"]("Please select website");
                return false;
               } 
               websiteid = $('#change-website').val() 
               if (confirm("Are you sure?")) {
                    $.ajax({
                        url: "/return-exchange/status/fetch-store-status",
                        type: "post",
                        data: {
                            "id" : websiteid,
                            "_token" : "{{ csrf_token() }}"
                        },
                        beforeSend: function () {
                            $("#loading-image").show();
                        }  
                    }).done(function (data) {
                        $("#loading-image").hide();
                        $('#content_data').html(data);
                        toastr["success"]("data updated successfully");
                    }).fail(function (jqXHR, ajaxOptions, thrownError) {
                        $("#loading-image").hide();
                        toastr["error"](jqXHR.responseText);
                    });
                }
               }
        );

        $('#add-new-btn').click(
            function(){
                if($('#change-website').val() === ''){
                    toastr["error"]("Please select website");
                    return false;
                }
                $('#new-status').modal('show')
            }

        );
        

        
        
        $(document).on("keyup",".text-editor",function(e) {
            var $this = $(this);
            if(e.keyCode == 13) {
                var field   = $this.data("field");
                var value   = $this.val();
                var id      = $this.closest("tr").data("id");
                $.ajax({
                    url: "/return-exchange/status/store",
                    dataType: "json",
                    type: "post",
                    data: {
                        "field" : field,
                        "value": value,
                        "id" : id,
                        "_token" : "{{ csrf_token() }}"
                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    }
                }).done(function (data) {
                    $("#loading-image").hide();
                    toastr["success"]("data updated successfully");
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    $("#loading-image").hide();
                    alert('No response from server');
                });
            }
        });

        $(document).on("focusout",".text-editor-textarea",function(e) {
            var $this = $(this);
            var field   = $this.data("field");
            var value   = $this.val();
            var id      = $this.closest("tr").data("id");
            $.ajax({
                url: "/return-exchange/status/store",
                dataType: "json",
                type: "post",
                data: {
                    "field" : field,
                    "value": value,
                    "id" : id,
                    "_token" : "{{ csrf_token() }}"
                },
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function (data) {
                $("#loading-image").hide();
                toastr["success"]("data updated successfully");
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                $("#loading-image").hide();
                alert('No response from server');
            });
        });

        $(document).on("click",".btn-delete-template",function(e) {
            if(confirm("Are you sure you want to delete this request ?")) {
                var id = $(this).data("id");
                $.ajax({
                    url: "/return-exchange/status/delete",
                    dataType: "json",
                    type: "post",
                    data: {
                        "id" : id,
                        "_token" : "{{ csrf_token() }}"
                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    }
                }).done(function (data) {
                    $("#loading-image").hide();
                    toastr["success"]("data updated successfully");
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    $("#loading-image").hide();
                    alert('No response from server');
                });
            }

        });
    </script>
@endsection