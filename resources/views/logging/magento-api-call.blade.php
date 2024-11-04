@extends('layouts.app')

@section('title', 'Magento Product Api call')

@section("styles")
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link href="https://cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css" rel="stylesheet"/>
  <script src="https://cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js"></script>
  <style type="text/css">

  #loading-image {
    position: fixed;
    top: 50%;
    left: 50%;
    margin: -50px 0px 0px -50px;
  }
  input {
    /* width: 130px; */
  }
  thead tr th{
    /* width: 220px !important; */
  }
  thead tr th:nth-child(5),thead tr th:nth-child(6),thead tr th:nth-child(7),thead tr th:nth-child(8),thead tr th:nth-child(9),thead tr th:nth-child(11),thead tr th:nth-child(12),thead tr th:nth-child(13),thead tr th:nth-child(14)
   ,thead tr th:nth-child(15),thead tr th:nth-child(16),thead tr th:nth-child(17),thead tr th:nth-child(18),thead tr th:nth-child(19),thead tr th:nth-child(20){
    /* width: 80px !important; */
  }
  .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 6px 5px;
  }
  input{
    width:auto !important;
  }
  .flex-grow-1{
    flex-grow:1
  }
  .btn-secondary, .btn-secondary:hover, .btn-secondary:focus{
    background:#fff;
    color:#757575;
    border:1px solid #ddd;
  }
  
  .ajax-loader{
    position: fixed;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.2);
    z-index: 1060;
  }

  .inner_loader {
	top: 30%;
    position: absolute;
    left: 40%;
    width: 100%;
    height: 100%;
}

  @media (min-width: 576px){
    .modal-dialog {
        max-width: 700px;
        margin: 1.75rem auto;
    }
      .modal-dialog {
      width: 700px;
      margin: 30px auto;
  }
  }
  #magento_list_tbl_895_wrapper{
    padding : 10px;
  }
 
</style>
@endsection

@section('content')
<div class="ajax-loader" style="display: none;">
		<div class="inner_loader">
		<img src="{{ asset('/images/loading2.gif') }}">
		</div>
	</div>
  <div class="row m-0">
    <div class="col-lg-12 margin-tb p-0">
      <h2 class="page-heading">Magento Product API Call</h2>
    </div>
  </div>
  <div class="row m-0 pl-3 pr-3" style="margin-bottom: 10px; margin: 10px">
       <input type="text" placeholder="Enter the limit of product" name="product_limit" class="form-control  product-limit-text">
       <input type="text" placeholder="Search Here" name="product_name" class="form-control  product-name-text ml-3">
       <button class="btn btn-secondary check-latest-product ml-2">Check latest product</button>
       <div class="pull-right flex-grow-1" style="display:flex;justify-content:flex-end;">
        <div class="dropdown">
          <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Delete
          </button>
          <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="left: auto !important;right: 0 !important;">
            <li class="dropdown-item delete_api_search_history" value="60">Last Hours</li>
            <li class="dropdown-item delete_api_search_history" value="1">Last 24 Hours</li>
            <li class="dropdown-item delete_api_search_history" value="7">Last 7 Days</li>
            <li class="dropdown-item delete_api_search_history" value="30">Last 4 Weeks</li>
            <li class="dropdown-item delete_api_search_history" value="100">All Time</li>
          </ul>
        </div>
       </div>
    
    
  </div>
  <!--More Product Data Modal-->
  <div id="more-website-data-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
  
      <!-- Modal content-->
      <div class="modal-content modal-lg">
        <div class="modal-header">
          <h4 class="modal-title">Product Name</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <p style="word-wrap: break-word;"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
  
    </div>
  </div>
  
  <div class="row m-0 pt-3">
    <div class="col-md-12 pl-3 pr-3">
      
          <div class="table-responsive" styale="overflow:hidden">
            <table id="magento_list_tbl_895" class="table table-bordered table-hover" style="table-layout:fixed">
              <thead>
                <th style="width:2%;">No</th>
                <th style="width:5%;word-break:break-all">Date</th>
                <th style="width:3%;word-break:break-all">Website</th>
                <th style="width:4%;">Product SKU</th>
                <th style="width:4%;">Product Name</th>
                <th style="width:5%;">Category assigned</th>
                <th style="width:5%;">Size Pushed</th>
                <th style="width:5%;">Brand Pushed</th>
                <th style="width:5%;">Size Chart Pushed</th>
                <th style="width:7%;">Dimensions Pushed</th>
                <th style="width:7%;">Composition Pushed</th>
                <th style="width:5%;">Images Pushed</th>
                <th style="width:3%;word-break:break-all">English</th>
                <th style="width:3%;word-break:break-all">Arabic</th>
                <th style="width:3%;word-break:break-all">German</th>
                <th style="width:3%;word-break:break-all">Spanish</th>
                <th style="width:3%;word-break:break-all">French</th>
                <th style="width:3%;word-break:break-all">Italian</th>
                <th style="width:3%;word-break:break-all">Japanese</th>
                <th style="width:3%;word-break:break-all">Korean</th>
                <th style="width:3%;word-break:break-all">Russian</th>
                <th style="width:3%;word-break:break-all">Chinese</th>
                <th style="width:5%;word-break:break-all">Status</th>
                <th style="width:3%;word-break:break-all">Action</th>
              </thead>
              <tbody class="magento_api_search_data">
                @foreach ($data as $key => $val)
                    <tr data-id="{{ $val->id }}">
                      <td>{{ ++$key }}</td>
                      <td>{{ \Carbon\Carbon::parse($val->created_at)->format('d-m-y H:i:s')  }}</td>
                      <td>{{ $val->website_id }}</td>
                      <td style="word-break:break-all" class="expand-row">
                      
                      <span class="td-mini-container">
                                                {{ strlen( $val->sku) > 10 ? substr( $val->sku , 0, 10).'...' :  $val->sku }}
                                            </span>

                                   <span class="td-full-container hidden">
                                   {{ $val->sku }}
                                            </span>


</td>
                      <td style="word-break:break-all" data-website="{{ $val->website}}" class="website-data-popup">{{ strlen($val->website) > 26 ? substr($val->website,0, 26)."..." : $val->website }}</td>
                      <td style="word-break:break-all"  >{{ $val->category_names }}</td>
                      <td>{{ $val->size }}</td>
                      <td>{{ $val->brands }}</td>
                      <td>{{ $val->size_chart_url }}</td>
                      <td>{{ $val->dimensions }}</td>
                      <td  style="word-break:break-all">{{ $val->composition }}</td>
                      <td>
                        @if ($val->images)
                          <img src="{{ $val->images}}" style="height:100px;">
                        @endif
                      </td>
                      <td>{{ $val->english }}</td>
                      <td>{{ $val->arabic }}</td>
                      <td>{{ $val->german }}</td>
                      <td>{{ $val->spanish }}</td>
                      <td>{{ $val->french }}</td>
                      <td>{{ $val->italian }}</td>
                      <td>{{ $val->japanese }}</td>
                      <td>{{ $val->korean }}</td>
                      <td>{{ $val->russian }}</td>
                      <td>{{ $val->chinese }}</td>
                      <td>{{ $val->status }}</td>
                      <td><button class="btn btn-image delete_api_search_history p-0" data-id="{{ $val->id }}"><i class="fa fa-trash"></i></button>
                      <button class="btn btn-image view_error p-0" data-toggle="modal" data-id="{{ $val->log_refid }}"> <i class="fa fa-eye"></i> </button>
                    </td>
                    </tr>
                @endforeach
              </tbody>
            </table>
            
          </div>
          <tr>{{ $data->links() }}</tr>
        
      @if ($data->count() === 0)
        <div class="text-center"><span class=""><h2 style="color:gray;">No Data Found </h2></span></div>
      @endif
    </div>
  </div>
  <div id="view_error" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <form action="{{ url('logging/assign') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">View Logs</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Index</th>
                            <th>Time</th>
                            <th>Log</th>
                            <th>Message</th>
                        </tr>
                        <tbody class="content">
                            
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default close-setting" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
      </div>
    </div>
    @endsection
  @section('scripts')
    <script type="text/javascript">
  $(document).on('click','.view_error',function(event){
      event.preventDefault();
      console.log($(this).data('id'));
      $.ajax({
            url: '{{ route("logging.magento.logMagentoApisAjax") }}',
            dataType: "json",
            data: {
                id: $(this).data('id')
            },
            beforeSend: function () {
              $(".ajax-loader").show();
            },
        }).done(function (data) {
            $(".ajax-loader").hide();
            var $html = '';
            if(data.data.length > 0){
                $.each(data.data, function(i, item) {
                  $html += '<tr>';
                  $html += '<td>'+parseInt(i+1)+'</td>';
                  $html += '<td>'+item.created_at+'</td>';
                  $html += '<td>'+item.api_log+'</td>';
                  $html += '<td>'+wordWrap(item.message, 50)+'</td>';
                  $html += '</tr>';
              });
            }
            $('#view_error table tbody.content').html($html);
            $('#view_error').modal('show');
        }).fail(function (jqXHR, ajaxOptions, thrownError) {
          $(".ajax-loader").hide();
        });
      
  });

function wordWrap(str, maxWidth) {
    var newLineStr = "\n"; done = false; res = '';
    while (str.length > maxWidth) {                 
        found = false;
        // Inserts new line at first whitespace of the line
        for (i = maxWidth - 1; i >= 0; i--) {
            if (testWhite(str.charAt(i))) {
                res = res + [str.slice(0, i), newLineStr].join('');
                str = str.slice(i + 1);
                found = true;
                break;
            }
        }
        // Inserts new line at maxWidth position, the word is too long to wrap
        if (!found) {
            res += [str.slice(0, maxWidth), newLineStr].join('');
            str = str.slice(maxWidth);
        }

    }

    return res + str;
}

function testWhite(x) {
    var white = new RegExp(/^\s$/);
    return white.test(x.charAt(0));
};

$(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            // if ($(this).data('switch') == 0) {
            //   $(this).text($(this).data('details'));
            //   $(this).data('switch', 1);
            // } else {
            //   $(this).text($(this).data('subject'));
            //   $(this).data('switch', 0);
            // }
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });


      $(document).on('click','.delete_api_search_history',function(){
        var days = $(this).val();
        var id = $(this).data('id');
        var $this = $(this);
        $.ajax({
          method: "POST",
          url: "{{ route('delete.magento.api-search-history') }}",
          data: {
            "_token": "{{ csrf_token() }}",
            id:id,
            days:days,
          },
          success: function(response){
            if (response.status == true) {
              $this.closest('tr').remove();
              toastr.success('Data Deleted Successfully')
            }
            if (response.code == 200) {
              toastr.success(response.message)
              setTimeout(function(){
                location.reload();
              }, 1000);

            }
          }
        });
      });
    // if (localStorage.getItem("luxury-product-data-asin") !== null) {
    //   var data = JSON.parse(localStorage.getItem('luxury-product-data-asin'));
    //   var example = $("#magento_list_tbl_895").DataTable({
    //     dom: 'flBrtip',
    //     stateSave: true,
    //     paging: true,
    //     processing: true,
    //     serverSide: true,
    //     bJQueryUI: true,
    //     ordering: false,
    //     lengthMenu: [[10, 25, 50,100,200, -1], [10, 25, 50,100,200, "All"]],
    //     ajax:{
    //       method: "POST",
    //       url: "/logging/magento-product-skus-ajax/",
    //       data: {
    //         "_token": "{{ csrf_token() }}",
    //         productSkus:JSON.stringify(data)
    //       }
    //     },  fixedColumns: true,
    //     language: {
    //       searchPlaceholder: "Search..."
    //     },columns:
    //     [
    //       {
    //         mRender: function (data, type, row)
    //         {
    //           return row.websites.join(', ')
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.sku
    //         }
    //       }, {
    //         mRender: function (data, type, row)
    //         {
    //           return row.product_name
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.category_names.join(', ')
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.size
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.brands ?  row.brands  :'Not Provided'
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //         return row.size_chart_url ? row.size_chart_url : "No"
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.dimensions ? row.dimensions : 'Not Provided'
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.composition ? row.composition : 'Not Provided'
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           if(row.media_gallery_entries.length > 0 ){
    //             return row.media_gallery_entries[0].file
    //           }else{
    //             return 'Not Provided'
    //           }
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.english
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.arabic
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.german
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.spanish
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.french
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.italian
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.japanese
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.korean
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.russian
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.chinese
    //         }
    //       },{
    //         mRender: function (data, type, row)
    //         {
    //           return row.success ? "Success" : "Product not found in Website."
    //         }
    //       }
    //     ]
    //   });
    // }

     $(document).on("click",".check-latest-product",function() {
        let limit = $(".product-limit-text").val();
        let website_name = $(".product-name-text").val();

        $.ajax({
          method: "GET",
          url: "/logging/get-latest-product-for-push",
          data: {
            "limit": limit,
            "website_name": website_name
          },
          // dataType: 'json'
        })
        .done(function(result) {
          $('.pagination').hide();
          $('.magento_api_search_data').html(result);
          // if(result.code == 200){ 
          //   localStorage.setItem('luxury-product-data-asin', JSON.stringify(result.products));
          // }
          // window.location.href = `?limit=${limit}&website_name=${website_name}`;
        });

    });

    $(document).on('click','.website-data-popup',function(){
      var website_data = $(this).data('website');
      if (website_data.length <= 150) {
        return
      }
      $('#more-website-data-modal').modal('show');
      $('#more-website-data-modal').find('p').text(website_data);
    })
    </script>
    @if (Session::has('errors'))
      <script>
      toastr["error"]("{{ $errors->first() }}", "Message")
      </script>
    @endif
    @if (Session::has('success'))
      <script>
      toastr["success"]("{{Session::get('success')}}", "Message")
      </script>
    @endif
  @endsection
