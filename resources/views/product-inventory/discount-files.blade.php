@extends('layouts.app')

@section('title', 'Supplier Inventory History')

@section('large_content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-md-12 p-0">
            <h2 class="page-heading">Supplier Discount Files 

                <div class="pull-right">
                    
                    <button type="button" class="btn btn-secondary mr-2" data-toggle="modal" data-target="#Log-details" style="background: #fff !important;
                    border: 1px solid #ddd !important;
                    color: #757575 !important;">Log Details</button>
                </div>

               </h2>
        </div>

        <div class="col-12">
          <div class="pull-left">
            <div class="form-group" style="margin-bottom:2px;">
                <div class="row">
                    <form method="GET" action="{{ route('supplier.discount.files') }}">
                        <div class="flex" style="margin-bottom:-7px;">
                           
                            <div class="form-group" style="width:300px; padding-left:16px;">
                               
                                <select class="form-control select-multiple globalSelect2" id="supplier-selects" tabindex="-1" aria-hidden="true" name="supplier">
                                    <option value="">Search Supplier...</option>
                                    @foreach($suppliers as $supplier)
                                        @if(isset($supplier->id))
                                            @if(isset($request->supplier) && $supplier->id==$request->supplier)
                                                <option value="{{$supplier->id}}" selected="selected">{{$supplier->supplier}}</option>
                                            @else
                                                <option value="{{$supplier->id}}">{{$supplier->supplier}}</option>
                                            @endif
                                        @endif
                                    @endforeach
                                </select>

                              </div>
                              <div class="form-group" style="width:300px; padding-left:10px;">
                                <select class="form-control select-multiple globalSelect2" id="brand-selects" tabindex="-1" aria-hidden="true" name="brands" >
                                    <option value="">Search Brands...</option>
                                    @foreach ($brand_data as $key=> $row ) 
                                        @if(isset($row->brand->id) && isset($row->brand->name))
                                            @if(isset($request->brands) && $row->brand->id==$request->brands)
                                                <option value="{{$row->brand->id}}" selected="selected">{{$row->brand->name}}</option>
                                            @else
                                                <option value="{{$row->brand->id}}">{{$row->brand->name}}</option>
                                            @endif
                                        @endif
                                    @endforeach
                                </select>
                                
                              </div>

                              <div class="col-md-4">
                                <button type="submit" class="btn btn-image ml-3"><img src="{{asset('images/filter.png')}}" style="margin-left: -10px;"/></button>
        
                                <a href="{{ route('supplier.discount.files') }}" class="fa fa-refresh" aria-hidden="true" style="margin-left: 3px;"></a>

                                <p class="btn btn-secondary add_save_excel ml-3" data-toggle="modal" data-target="#importExcelModal" >Import Excel</p>
                            </div>

                            
                        </div>
                    </form>
                </div>

            </div>
          </div>

          <div class="pull-right">
            <div class="form-group">
              &nbsp;
            </div>
          </div>
        </div>
    </div>

    @include('partials.flash_messages')

<!-- <form method="post" action="{{route('supplier.discount.files.post')}}" enctype="multipart/form-data" class="excel_form">
@csrf 
     <div class="form-group">
       
        <div class="row"> 
            
            <div class="col-md-3">
                <select class="form-control select-multiple" id="supplier-select" tabindex="-1" aria-hidden="true" name="supplier" >
                    <option value="">Select Supplier</option>
                    @foreach($suppliers as $supplier)
                        @if(isset($supplier->id))
                            <option value="{{$supplier->id}}">{{$supplier->supplier}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input class="form-control" type="file" id="file" name="excel" placeholder="Select File..." accept=".xlsx, .xls"/>
            </div>
            <div class="col-md-3 d-flex justify-content-between">
                <button type="submit" class="btn btn-secondary add_save_excel" >Import</button>
            </div> 

            <div id="loading-image_" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
            </div>

            </div>
    </div>
</form> -->

    <div class="row">
        <div class="col-md-12">
          
            <table id="table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th width="16%">Brand</th>
                        <th width="10%">Supplier Name</th> 
                        <th width="6%">Gender </th>
                        <th width="10%">Category</th>
                        <th width="12%">Generice price</th>
                        <th width="10%">Exceptions</th>
                        <th width="16%">Condition from retail</th>
                        <th width="20%">Retail condition for exceptions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $key=> $row ) 
                    <tr data-id="{{ $row->id }}">
                       <td>{{ (isset($row->brand->name)) ? $row->brand->name : ''}}</td> 
                       <td>{{$row->supplier->supplier}}</td>
                       <td>{{$row->gender}}</td> 
                       <td>{{$row->category}}</td> 
                       <td>
                        <div class="price" style="display: flex;">
                            <input type="text" name="generic_price_data" id="generic_price_data" class="form-control generic-price-input"  value="{{ $row->generic_price }}" style="width: calc(100% - 30px);">  
                            @if($row->generic_price && $row->generic_price != '-') 
                                 <a title="Log Details" class="fa fa-info-circle discount_log" data-id="{{ $row->id }}" data-header="generic_price" style="font-size:15px;color: #757575;width: 25px;display: flex;align-items: center;padding-left: 10px;"></a>
                            @endif
                        </div>
                        </td> 
                       <td>{{$row->exceptions ?? '-'}}</td> 
                       <td>
                       <div class="condition" style="display: flex;">
                            <input type="text" name="condition_from_retail" id="condition_from_retail" class="form-control condition-from-retail-input" value="{{$row->condition_from_retail}}" style="width: calc(100% - 30px);" />
                            @if($row->condition_from_retail && $row->condition_from_retail != '-')
                                 <a title="Log Details" class="fa fa-info-circle discount_log" data-id="{{ $row->id }}" data-header="condition_from_retail" style="font-size:15px; display: flex;align-items: center;padding-left: 10px; color: #757575;"></a>
                            @endif
                        </div>
                        </td> 
                        <td>
                        <div class="condition_exception" style="display: flex;">
                            <input type="text" name="condition_from_retail_exceptions" id="condition_from_retail_exceptions" class="form-control condition-from-retail-exceptions-input" data-id="{{ $row->id }}" value="{{$row->condition_from_retail_exceptions}}" style="width: calc(100% - 30px);" />
                            @if($row->condition_from_retail_exceptions && $row->condition_from_retail_exceptions != '-')
                                  <a title="Log Details" class="fa fa-info-circle discount_log" data-id="{{ $row->id }}" data-header="condition_from_retail_exceptions" style="font-size:15px;  display: flex;align-items: center;padding-left: 10px; color: #757575;"></a>
                            @endif
                        </div>
                        </td> 
                    </tr>
                    @endforeach
                    <tr>
                         <td colspan="11">
                            {{ $rows->appends($request->except('page'))->links() }}.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

 <div id="brand-history-model" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title">Brand History</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
      </div>
  </div>
</div>

<div id="log-history-model" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Brand History</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="" id="approve-log-btn" method="GET">
                @csrf
                <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="supplier_brand_discounts_id" id="supplier_brand_discounts_id">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                   
                                    <th>Old Value</th>
                                    <th>New Value</th>
                                    <th>Updated By</th>
                                    <th>Updated At</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>             
        </div>
    </div>
  </div>

  
  <!-- Modal -->
  <div class="modal fade" id="Log-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Log Details</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            
            <form action="" id="excel-log-btn" method="POST" >
                @csrf

                <div class="modal-body">
                    
                <div class="row">
                   
                    <div class="col-md-12" id="log-details">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="word-wrap: break-word; width=50%">Excel Name</th>
                                    <th width=20%>Updated by</th>
                                    <th width=20%>Created at</th>
                                    <th width=10%>Action</th>
                                                        
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($excel_data as $key => $value)
                                <tr>
                                     <td> {{$value->excel_name}} </td>
                                     <td> {{ (isset($value->users->name)) ? $value->users->name : ''}} </td>
                                     <td> {{$value->created_at}} </td>
                                    <td>
                                        <a href='/product/discount/excel/files/?filename={{$value->excel_name}}' title='Download Excel' class='btn btn-image ml-1 download_excel' ><i class='fa fa-download' aria-hidden='true'></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                   
                </div>
            </form>
        </div>
      
      </div>
    </div>
  </div>

  <!-- Import Excel Modal -->
<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" >Import Excel</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <form name="upload_excel" id="uploadExcelFile">
                @csrf
                <div class="col-md-6">
                    <select class="form-control select-multiple" id="supplier-select" tabindex="-1" aria-hidden="true" name="supplier" >
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            @if(isset($supplier->id))
                                <option value="{{$supplier->id}}">{{$supplier->supplier}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <input class="form-control" type="file" id="dis_excel_file" name="excel" placeholder="Select File..." accept=".xlsx, .xls"/>
                </div>

                <div class="col-md-2 d-flex justify-content-between">
                    <button type="submit" class="btn btn-secondary mapping_excel" >Mapping</button>
                </div> 
            </form>

            <form name="mapping_upload_excel" id="mappinguploadExcelFile">
                @csrf
                <div class="row">
                    <div class="col-lg-12 mt-5">
                        <h2 class="page-heading">Select Fields</h2>
                    </div>
                </div>

                <div id="term">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <thead>
                                    
                                </thead>
                                <tbody>
                                    <tr>
                                        <input type="hidden" class="column_index" name="column_index" value="" />
                                        <input type="hidden" class="supplier_name" name="supplier_name" value="" />

                                        <td>Brand</td>
                                        <td>
                                            <div class="form-group">
                                                <select name="brand_dropdown" class="form-control excel_header_drop_down" id="brand_dropdown">
                                                    <option value="" >Select Field</option>
                                                    
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Gender</td>
                                        <td>
                                            <div class="form-group">
                                                <select name="gender_dropdown" class="form-control excel_header_drop_down" id="gender_dropdown">
                                                    <option value="">Select Field</option>
                                                    
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Category</td>
                                        <td>
                                            <div class="form-group">
                                                <select name="category_dropdown" class="form-control excel_header_drop_down" id="category_dropdown">
                                                    <option value="">Select Field</option>
                                                    
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Generice price</td>
                                        <td>
                                            <div class="form-group">
                                                <select name="generice_price_dropdown" class="form-control excel_header_drop_down" id="generice_price_dropdown">
                                                    <option value="">Select Field</option>
                                                    
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Exceptions</td>
                                        <td>
                                            <div class="form-group">
                                                <select name="exceptions_dropdown" class="form-control excel_header_drop_down" id="exceptions_dropdown">
                                                    <option value="">Select Field</option>
                                                    
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Condition from Retail</td>
                                        <td>
                                            <div class="form-group">
                                                <select name="condition_from_retail_dropdown" class="form-control excel_header_drop_down" id="condition_from_retail_dropdown">
                                                    <option value="">Select Field</option>
                                                    
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Condition from Exceptions</td>
                                        <td>
                                            <div class="form-group">
                                                <select name="condition_from_exceptions_dropdown" class="form-control excel_header_drop_down" id="condition_from_exceptions_dropdown">
                                                    <option value="">Select Field</option>
                                                    
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">Import</button>
                </div>
            </form>
      </div>
      
    </div>
  </div>
</div>
@endsection

@section('scripts')
<div id="loading-image1" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
  50% 50% no-repeat;display:none;">
    </div>
<script type="text/javascript">
 
    
    $('.excel_form').submit(function(e){
     
        if($('#supplier-select').val() == ''){
            toastr['error']('Please select supplier');
            e.preventDefault();
        }else if($('#file').val() == ''){
            toastr['error']('Please upload file');
            e.preventDefault();
        }else{
            $("#loading-image_").css('display', 'block');
        }
    });
    

    $(document).on("click", ".discount_log", function(e) {

        var id = $(this).data('id');
        var header = $(this).data('header');
        
        $('#log-history-model table tbody').html('');
        $.ajax({
        url: "{{ route('log-history/discount/brand') }}",
        data: {id: id,header : header },
        success: function (data) {
            if(data != 'error') {
                $('input[name="supplier_brand_discounts_id"]').val(id);
             
                $.each(data, function(i, item) {
                    
                    $('#log-history-model table tbody').append(
                        '<tr>\
                            <td>'+ ((item['old_value']) ? item['old_value'] : '-') +'</td>\
                            <td>'+item['new_value']+'</td>\<td>'+item['name']+'</td>\
                            <td>'+ moment(item['updated_at']).format('DD/MM/YYYY') +'</td>\
                        </tr>'
                    );
                });
            }
        }
        });

        $('#log-history-model').modal('show');
    });
   
    $(document).on('keyup', '.generic-price-input', function () {
        let generic_price_data = $(this).val();
        let thiss = $(this);
        if (event.keyCode != 13) {
            return;
        }

        $.ajax({
            url: "{{route('discount.file.update')}}",
            type: 'post',
            data: {
                _token: '{{csrf_token()}}',
                generic_price_data: generic_price_data,
                generic_id: $(this).closest('tr').attr('data-id'),
            },
            success: function(response){
                var length_info  = thiss.siblings('a').length;
                              
                if(response && length_info == 0){   
                                
                     $(thiss).after('<a title="Log Details" class="fa fa-info-circle discount_log" data-id="'+ response.brand_disc.id +'" data-header="condition_from_retail_exceptions" style="font-size:15px;  display: flex;align-items: center;padding-left: 10px; color: #757575;"></a>');
                }
                toastr.success('Generic Price Data Updated ');
                
            }
            
        });        

    }); 

     $(document).on('keyup', '.condition-from-retail-input', function () {
        let condition_from_retail_data = $(this).val();
        let thiss = $(this);
        if (event.keyCode != 13) {
            return;
        }

        $.ajax({
            url: "{{route('condition.file.update')}}",
            type: 'post',
            data: {
                _token: '{{csrf_token()}}',
              
                condition_from_retail_data: condition_from_retail_data,
                condition_id: $(this).closest('tr').attr('data-id'),
            },
            success: function(response){
               
                var length_info  = thiss.siblings('a').length;
                              
                if(response && length_info == 0){                  
                     $(thiss).after('<a title="Log Details" class="fa fa-info-circle discount_log" data-id="'+ response.condition_disc.id +'" data-header="condition_from_retail_exceptions" style="font-size:15px;  display: flex;align-items: center;padding-left: 10px; color: #757575;"></a>');
                }
                
                toastr.success('Condition from Retail Data Updated ')
            }
        });       

    });        

    $(document).on('keyup', '.condition-from-retail-exceptions-input', function () {
        let condition_from_retail_exceptions_data = $(this).val();
        let thiss = $(this);
        if (event.keyCode != 13) {
            return;
        }
       
        $.ajax({
            url: "{{route('condition-exceptions.file.update')}}",
            type: 'post',
            data: {
                _token: '{{csrf_token()}}',
              
                condition_from_retail_exceptions_data: condition_from_retail_exceptions_data,
                condition_exceptions_id: $(this).closest('tr').attr('data-id'),
            },
            success: function(response){

                var length_info  = thiss.siblings('a').length;
                              
                if(response && length_info == 0){                    
                     $(thiss).after('<a title="Log Details" class="fa fa-info-circle discount_log" data-id="'+ response.exceptions_discount.id +'" data-header="condition_from_retail_exceptions" style="font-size:15px;  display: flex;align-items: center;padding-left: 10px; color: #757575;"></a>');
                   
                }

                toastr.success('Condition from Retail Exceptions Data Updated ');
            }
        })

    });  

    let discountFile_ = null;
   
    $('#dis_excel_file').on('change', function(){
        discountFile_ = this.files[0];
    });

    $('#importExcelModal').on('hidden.bs.modal', function (e) {
        location.reload();
    })
    $("#mappinguploadExcelFile").hide();



    $('#uploadExcelFile').on('submit',(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var option_drp_dwn = '';
        $("#mappinguploadExcelFile").hide();

        var supplier = $('#supplier-select :selected').val();
        var file_upload = $('#dis_excel_file').val();

        if(supplier == ''){
            toastr.error('Please Select a supplier');
            return false;
        }
        if(file_upload == ''){
            toastr.error('Please Upload a Excel File');
            return false;
        }

        $.ajax({
            type:'POST',
            url: "{{route('product.mapping.excel')}}",
            data:formData,
            cache:false,
            contentType: false,
            processData: false,
            success:function(response){
                if(response.code == 200){
                    toastr.success(response.message);
                    
                    $("#mappinguploadExcelFile").show();

                    var column_index = response.column_index;
                    $('.column_index').val(column_index);

                    $.each( response.header_data, function( key, value ) {
                        option_drp_dwn += '<option value="'+key+'" data-column="'+column_index+'" >'+value+'</option>'
                    });

                    $('.excel_header_drop_down').append(option_drp_dwn);
                    
                }
            },
            error: function(data){
            }
        });
    }));

      

    $('#mappinguploadExcelFile').on('submit',(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('file', discountFile_ );


        var brand_drp_dwn = $('#brand_dropdown :selected').val();
        var gender_drp_dwn = $('#gender_dropdown :selected').val();
        var category_drp_dwn = $('#category_dropdown :selected').val();

        if(brand_drp_dwn == ''){
            toastr.error('Please Mapping Brand');
            return false;
        }
        if(gender_drp_dwn == ''){
            toastr.error('Please Mapping Gender');
            return false;
        }
        if(category_drp_dwn == ''){
            toastr.error('Please Mapping Category');
            return false;
        }


        var supplier = $('#supplier-select :selected').val();
        formData.append('supplier', supplier );
        
        $.ajax({
            type:'POST',
            url: "{{route('product.mapping.export.excel')}}",
            data:formData,
            cache:false,
            contentType: false,
            processData: false,
            beforeSend: function() {
              $("#loading-image1").show();
            },
            success:function(response){
                if(response.code == 200){
                    toastr.success(response.message);
                   
                    setTimeout(function(){  location.reload(); }, 2000);
                }
                $("#loading-image1").hide();
            },
            error: function(data){
                $("#loading-image1").hide()
            }
        });
        ;
    }));   

</script>

@endsection



