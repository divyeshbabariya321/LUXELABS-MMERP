@extends('layouts.app')

@section('favicon' , 'password-manager.png')

@section('title', 'SimplyDuty Category')


@section('content')

<div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">SimplyDuty Country</h2>
            <div class="pull-left">
           
            

            <div class="form-inline ml-5 pl-5">
            <form>
                <div class="form-group">
                   
                <select id="segment_1" style="width:200px !important" >   
           
        @foreach($segments as $s)
            
                <option value="{{$s->id}}">{{$s->segment}}</option>
             @endforeach   
       </select>      
        <input type="text" value="{{ request('default_value') }}" name="default_value" id="default_value_segment" class="form-control" placeholder="Setup Default value for segment">
                </div>
                <button type="submit" class="btn btn-secondary btn-assign-default-val ml-3">Assign</button>
            </form>
        </div>
          
       
         

        
         
            
            
            </div>

       
            <div class="pull-right">
            <a  class="btn btn-secondary" onclick="opensegment();" >Segment</a>
                <button type="button" class="btn btn-secondary" onclick="approve()">Approve</button>
                <button type="button" class="btn btn-secondary" onclick="getCategoryData()">Load from SimplyDuty</button>
                <button type="button" class="btn btn-image" onclick="resetSearch()"><img src="/images/resend2.png"/></button>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    

    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="category-table">
            <thead>
            <tr>
                 <th ><input id="checkAll" type="checkbox" ></th>
                <th style="width:10%">Country Code</th>
                <th style="width:60%">Country</th>
                <th>Segment</th>
                <th>Default Duty</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
            <tr>
                 <th></th>
            <th><input type="text" id="code" class="search form-control"></th>    
            <th><input type="text" id="country" class="search form-control"></th>
            <th></th>
            <th></th>
          </tr>
            </thead>
             {!! $countries->appends(Request::except('page'))->links() !!}
            <tbody>
            @include('simplyduty.country.partials.data')
            </tbody>
        </table>
    </div>
    {!! $countries->appends(Request::except('page'))->links() !!}

@endsection


<div id="segment" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-body" id="segment_data">
       
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        
      </div>
    </div>
  </div>
</div>

@section('scripts')

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
 $(document).ready(function() {
        $(document).on('focusout','.dutyinput',function(){
            let id = $(this).data('id');
            let duty = $(this).val();

          //  if (duty != '0' && duty != 0 && duty != null && duty != undefined){
            if (duty != undefined){
                $.ajax({
                    url:'{{route("simplyduty.country.updateduty")}}',
                    dataType:'json',
                    data:{
                        id: id,
                        duty : duty
                    },
                    success:function(result){
                        // console.log(result);
                        toastr["success"]("Value assigned!", "Message");
                    },
                    error:function(exx){
                        alert('Something went wrong!')
                        //window.location.reload();
                    }
                })
            }
        });
        src = "{{ route('simplyduty.country.index') }}";
        $(".search").autocomplete({
        source: function(request, response) {
            code = $('#code').val();
            country = $('#country').val();
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    code : code,
                    country : country,
                },
                beforeSend: function() {
                       $("#loading-image").show();
                },
            
            }).done(function (data) {
                 $("#loading-image").hide();
                console.log(data);
                $("#category-table tbody").empty().html(data.tbody);
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

       function getCategoryData() {
           src = "{{ route('simplyduty.country.update') }}"
           $.ajax({
                url: src,
                dataType: "json",
                data: {
                    
                },
                beforeSend: function() {
                       
                },
            
            }).done(function (data) {
                alert('Category Updated');
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
           
       }

       function resetSearch() {
           src = "{{ route('simplyduty.country.index') }}";
            reset = '';
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    reset : reset,
                },
                beforeSend: function() {
                        $("#loading-image").show();
                },
            
            }).done(function (data) {
                    $("#loading-image").hide();
                console.log(data);
                $("#category-table tbody").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
                
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
           
       }

       function addsegment(cid,sid)
       {
        $.ajax({
                url: "{{url('duty/country/addsegment')}}",
                dataType: "json",
                data: {
                    cid : cid,
                    sid : sid
                },
                beforeSend: function() {
                        $("#loading-image").show();
                },
            
            }).done(function (data) {
                    $("#loading-image").hide();
                console.log(data);
                alert('Segment updated');
                
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
       }

       function opensegment()
       {
           src = "{{ url('duty/segment') }}";
            reset = '';
            $.ajax({
                url: src,
                data: {
                    reset : reset,
                },
                beforeSend: function() {
                        $("#loading-image").show();
                },
            
            }).done(function (data) {
                  
                $('#segment_data').html(data);
                $("#segment").modal("show"); 
                
            })
       }

       function savesegment(id)
       {
           src = "{{ url('duty/segment/add') }}";
           segment=$('#segment_txt').val();
           segment_id=$('#segment_id').val();
           price=$('#price').val();

            $.ajax({
                url: src,
                data: {
                    segment_id : segment_id,
                    segment:segment,
                    price:price
                },
                beforeSend: function() {
                        $("#loading-image").show();
                },
            
            }).done(function (data) {
                  
                $("#segmentadd").modal("hide");  
                $("#segment").modal("hide");
                opensegment();
               
                
            })
       }

       function deletesegment(segment_id)
       {
           src = "{{ url('duty/segment/delete') }}";
           
            $.ajax({
                url: src,
                data: {
                    segment_id : segment_id,
                   
                },
                beforeSend: function() {
                        $("#loading-image").show();
                },
            
            }).done(function (data) {
                  
                $("#segmentadd").modal("hide");  
                $("#segment").modal("hide");
                opensegment();
               
                
            })
       }


       $(document).on("click",".btn-assign-default-val",function(e) {
        e.preventDefault();
        var $this = $(this);
        $.ajax({
            method : "POST",
            url: "{{url('/duty/country/assign-default-value')}}",
            data: {
                _token: "{{ csrf_token() }}",
                value: $("#default_value_segment").val(),
                segment: $("#segment_1").val()
                
            },
            dataType: "json",
            beforeSend : function(){
                $("#loading-image").show();
            },
            success: function (response) {
                $("#loading-image").hide();
                if(response.code == 200) {
                    toastr["success"]("Default value assigned!", "Message")
                    location.reload();
                }else{
                    toastr["error"](response.message, "Message");
                }
            }
        });
    });



    function approve()
    {
            str='';
            $(".checkboxClass:checked").each(function(){
               
                if (str=='')
                   str=$(this).val();
                else
                   str= str + "," + $(this).val();  
            });
            if (str=='')
              alert('First Select Country')
            else
            {
                    $.ajax({
                        method : "POST",
                        url: "{{url('/duty/country/approve')}}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            ids: str,
                         },
                        dataType: "json",
                        beforeSend : function(){
                            $("#loading-image").show();
                        },
                        success: function (response) {
                            $("#loading-image").hide();
                            if(response.code == 200) {
                                toastr["success"]("approved successfully", "Message")
                                location.reload();
                            }else{
                                toastr["error"](response.message, "Message");
                            }
                        }
                    });
            }
            
        
    }

    $("#checkAll").click(function(){
         $('input:checkbox').not(this).prop('checked', this.checked);
    });
        </script>
@endsection
