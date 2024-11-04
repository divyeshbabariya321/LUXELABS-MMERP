@extends('layouts.app')

@section('styles')
  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

  @endsection

@section('content')
    <style>
        .btn-secondary{
            color: #757575;
            border: 1px solid #ddd;
            background-color: #fff;
        }
        @media only screen and (max-width: 1315px) {
            .cashflow_date
            {
                width: 3%;
            }
            .delete_button
            {
                position: absolute;
                margin-top: auto;
            }
        }

    </style>
    <div class="row  pr-4 pl-4 cashflow-table">
        <div class="col-md-12 margin-tb p-0">
            <h2 class="page-heading">Cash Flow ({{count($cash_flows)}})</h2>
        </div>
        <div class="row cashflow-table">
            <div class="col-md-12">
            <div class="pull-left">
              <button type="button" class="btn btn-secondary mr-2"style="padding: 4px 12px;" data-toggle="modal" data-target="#cashCreateModal">+</button>
            </div>
            <form class="form-search-data">
                <div class="row">
                    <div class="col">
                        <div class="form-group cls_task_subject">
                            <select class="form-control input-sm ui-autocomplete-input globalSelect2" name="site_name[]" id="site_name" data-placeholder="Search Website Name.." multiple>
                                @foreach($website_name as $web_name)
                                    <option value="{{$web_name->website}}" {{ Request::get('site_name') && in_array($web_name->website, Request::get('site_name')) ? 'selected' : '' }}>{{$web_name->website}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <input type="text" name="daterange" placeholder="Search date" id="daterange" class="form-control input-sm ui-autocomplete-input" value="<?php if (isset($_GET['daterange'])) { echo $_GET['daterange'] ;} ?>" autocomplete="off">
                            <input type="hidden" name="hidden_daterange" id="hidden_daterange" class="form-control hidden_daterange">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <select onchange="get_bname();" name="module_type" id="module_type" class="form-control input-sm">
                                <option selected="" value="0"> Filter By Module / Type</option>
                                <option value="order"  <?php if (isset($_GET['module_type']) && $_GET['module_type']=='order') {  echo "selected='selected'" ;} ?> >Order</option>
                                <option value="payment_receipt"  <?php if (isset($_GET['module_type']) && $_GET['module_type']=='payment_receipt') {  echo "selected='selected'" ;} ?> >Payment Receipt</option>
                                <option value="assent_manager" <?php if (isset($_GET['module_type']) && $_GET['module_type']=='assent_manager') {  echo "selected='selected'" ;} ?> >Assent Manager</option>
                                <option value="vendor_frequency" <?php if (isset($_GET['module_type']) && $_GET['module_type']=='vendor_frequency') {  echo "selected='selected'" ;} ?> >Vendor Payment Manager</option>

                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <select name="b_name" id="b_name" class="form-control input-sm">
                                <option value="">Benefiiciary</option>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <select name="type" id="type" class="form-control input-sm">
                                <option value="">Filter by Type</option>
                                <option value="Pending"  <?php if (isset($_GET['type']) && $_GET['type']=='Pending') {  echo "selected='selected'" ;} ?> >Pending</option>
                                <option value="Received"  <?php if (isset($_GET['type']) && $_GET['type']=='Received') {  echo "selected='selected'" ;} ?> >Received</option>
                                <option value="Paid" <?php if (isset($_GET['type']) && $_GET['type']=='Paid') {  echo "selected='selected'" ;} ?> >Paid</option>

                            </select>
                        </div>
                    </div>
                    <div class="pt-2">
                        <button type="button" onclick="$('.form-search-data').submit();" class="btn btn-image btn-call-data"><img src="{{asset('/images/filter.png')}}"style="margin-top:-6px;"></button>
                    </div>
                </div>
            </form>
            </div>
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

   <div class="row pr-4 pl-4 cashflow-table">
        <div class="col-md-12">
          <div class="table-responsive ">
             <table class="table table-bordered" style="table-layout: fixed;">
                 <thead>
                 <tr>
                     <th class="cashflow_date" width="2%">Date</th>
                     <th width="2%">Module</th>
                     <th width="2%">Website</th>
                     <th width="3%">Bene Name</th>
                     <th width="3%">Category</th>
                     <th width="3%">Description</th>
                     <th width="2%">Amount</th>
                     <th width="2%">Am(EUR)</th>
                     <th width="2%">Erp A</th>
                     <th width="3%">Erp A(EUR)</th>
                     <th width="3%">Monetary Ac</th>
                     <th width="2%">Type</th>
                     <th width="3%">Bill Date </th>
                     <th width="2%">Actions</th>
                 </tr>
                 </thead>
                 <tbody class="pending-row-render-view infinite-scroll-cashflow-inner">
                 @foreach ($cash_flows as $cash_flow)
                     <tr>
                         <td class="small align-middle pt-0">{{ date('Y-m-d', strtotime($cash_flow->date)) }}</td>
                         <td class="voucher">{!! $cash_flow->getLink() !!}</td>
                         <td style="word-break: break-all">
                             @switch($cash_flow->cash_flow_able_type)
                               @case('App\Order')
                                    <a href="{{ @$cash_flow->cashFlowAble->storeWebsiteOrder->storeWebsite->website_url }}" target="_blank">{{$cash_flow->cashFlowAble->storeWebsiteOrder->storeWebsite->website ?? ''}}</a></p>
                                    @break
                             @default

                             @endswitch
                         </td>


                         <td class="expand-row">
                             @if(strlen($cash_flow->get_bname()) > 4)
                                 @php
                                     $dns = $cash_flow->get_bname();
                                     $dns = str_replace('"[', '', $dns);
                                     $dns = str_replace(']"', '', $dns);
                                 @endphp

                                 <div class="td-mini-container brand-supplier-mini-{{ $cash_flow->id }}">
                                     {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                                 </div>
                                 <div class="td-full-container hidden brand-supplier-full-{{ $cash_flow->id }}">
                                     {{ $dns }}
                                 </div>
                             @else
                                 N/A
                             @endif
                         </td>


                         <td class="expand-row" style="word-wrap: break-word;">
                             @if(strlen(class_basename($cash_flow->cashFlowAble)) > 4)
                                 @php
                                     $dns = class_basename($cash_flow->cashFlowAble);
                                     $dns = str_replace('"[', '', $dns);
                                     $dns = str_replace(']"', '', $dns);
                                 @endphp

                                 <div class="td-mini-container brand-supplier-mini-{{ $cash_flow->id }}">
                                     {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                                 </div>
                                 <div class="td-full-container hidden brand-supplier-full-{{ $cash_flow->id }}">
                                     {{ $dns }}
                                 </div>
                             @else
                                 N/A
                             @endif
                         </td>

                         <td class="expand-row">
                             @if(strlen($cash_flow->description) > 4)
                                 @php
                                     $dns = class_basename($cash_flow->description);
                                     $dns = str_replace('"[', '', $dns);
                                     $dns = str_replace(']"', '', $dns);
                                 @endphp

                                 <div class="td-mini-container brand-supplier-mini-{{ $cash_flow->id }}">
                                     {{ strlen($dns) > 10 ? substr($dns, 0, 10).'...' : $dns }}
                                 </div>
                                 <div class="td-full-container hidden brand-supplier-full-{{ $cash_flow->id }}">
                                     {{ $dns }}
                                 </div>
                             @else
                                 N/A
                             @endif
                         </td>




                         <td>@if(!is_numeric($cash_flow->currency))  {{$cash_flow->currency}}  @endif{{ $cash_flow->amount }}
                            @if($cash_flow->cash_flow_able_type =="App\HubstaffActivityByPaymentFrequency")
                              <button  type="button" class="btn btn-xs show-calculation"style="margin-top: -2px;" title="Show History" data-id="{{ $cash_flow->id }}"><i class="fa fa-info-circle"></i></button>
                            @endif
                         </td>

                         <td>{{ $cash_flow->amount_eur }}</td>
                         <td>{{$cash_flow->currency}} {{ $cash_flow->erp_amount }}</td>
                         <td>{{ $cash_flow->erp_eur_amount }}</td>
                         <td>
                          {{($cash_flow->monetaryAccount)?$cash_flow->monetaryAccount->name: "N/A"}}
                         </td>
                         <td>{{ ucwords($cash_flow->type) }}</td>
                         <td>{{ \Carbon\Carbon::parse($cash_flow->billing_due_date)->format('d-m-Y') }}</td>
                         <td>
                             <button type="button" class="btn btn-secondary btn-sm" onclick="CashFlowbtn('{{$cash_flow->id}}')"><i class="fa fa-arrow-down"></i></button>
                         </td>

                     </tr>
                     <tr class="action-cashflowbtn-tr-{{$cash_flow->id}} d-none">
                         <td class="font-weight-bold">Action</td>
                         <td colspan="13">
                             <a title="Do Payment" data-id="{{ $cash_flow->id }}" data-mnt-amount="{{ $cash_flow->amount }}" data-mnt-account="{{ $cash_flow->monetary_account_id }}" class="do-payment-btn"><span><i class="fa fa-money" aria-hidden="true"></i></span></a>
                             {{ html()->form('DELETE', route('cashflow.destroy', [$cash_flow->id]))->style('display:inline')->open() }}
                             <button type="submit" class="delete_button btn btn-image"><img src="{{asset('/images/delete.png')}}" /></button>
                             {{ html()->form()->close() }}
                         </td>
                     </tr>
                 @endforeach
                 </tbody>
             </table>
          </div>
       <img class="infinite-scroll-products-loader center-block" src="{{asset('/images/loading.gif')}}" alt="Loading..." style="display: none" />


   </div>

    <div id="cashCreateModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <form action="{{ route('cashflow.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="modal-header">
              <h4 class="modal-title">Store a Record</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">


              <div class="form-group">
                <strong>Description:</strong>
                <textarea name="description" class="form-control" rows="8" cols="80">{{ old('description') }}</textarea>

                @if ($errors->has('description'))
                  <div class="alert alert-danger">{{$errors->first('description')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Date:</strong>
                <div class='input-group date' id='date-datetime'>
                  <input type='text' class="form-control" name="date" value="{{ date('Y-m-d H:i') }}" required />

                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div>

                @if ($errors->has('date'))
                  <div class="alert alert-danger">{{$errors->first('date')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Amount:</strong>
                <input type="number" name="amount" class="form-control" value="{{ old('amount') }}" required>

                @if ($errors->has('amount'))
                  <div class="alert alert-danger">{{$errors->first('amount')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Type:</strong>
                <select class="form-control" name="type" required id="cashflow_type">
                  <option value="received" {{ 'received' == old('type') ? 'selected' : '' }}>Received</option>
                  <option value="paid" {{ 'paid' == old('type') ? 'selected' : '' }}>Paid</option>
                </select>

                @if ($errors->has('type'))
                  <div class="alert alert-danger">{{$errors->first('type')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Category:</strong>
                <select class="form-control" name="cash_flow_category_id" id="cashflow_category">
                  <option value="">Select Category</option>
                  @foreach ($categories['received'] as $id => $category)
                    <option value="{{ $id }}" {{ $id == old('cash_flow_category_id') ? 'selected' : '' }}>{{ $category }}</option>
                  @endforeach
                </select>

                @if ($errors->has('cash_flow_category_id'))
                  <div class="alert alert-danger">{{$errors->first('cash_flow_category_id')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Files:</strong>
                <input type="file" name="file[]" class="form-control" value="" multiple>

                @if ($errors->has('file'))
                  <div class="alert alert-danger">{{$errors->first('file')}}</div>
                @endif
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Add</button>
            </div>
          </form>
        </div>

      </div>
    </div>


<div id="do-payment-model" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('cashflow.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h4 class="modal-title">Create a receipt for <span class="text-cashflow-id"></span></h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
              <input type="hidden" name="cash_flow_id" id="cashflow-id-txt">
              <strong>Amount:</strong>
              {{ html()->text('amount')->placeholder('Insert Amount')->class('form-control')->id('cashflow-amount') }}
            </div>
            <div class="form-group">
              <strong>Type:</strong>
            {{ html()->select('type', ["received" => "Received", "paid" => "Paid"])->placeholder('Select a type')->class('form-control') }}
            </div>
            <div class="form-group">
              <strong>Date:</strong>
              {{ html()->text('date')->placeholder('Enter date')->class('form-control enter-date') }}
            </div>
            <div class="form-group">
              <strong>Monetary Account:</strong>
              <?php
                  $monetaryAccount = \App\MonetaryAccount::pluck("name","id")->toArray();
                  ?>
                  {{ html()->select('monetary_account_id', $monetaryAccount)->placeholder('Select a Account')->class('form-control')->id("monetary-account-id-txt") }}
            </div>
            <div class="form-group">
              <strong>Note:</strong>
              {{ html()->textarea('description')->placeholder('Insert note')->class('form-control') }}    
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary submit-cashflow">Add</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="show_counting" class="modal fade"  style="width:100%">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Payment Details</h4>
                </div>
                <div class="modal-body">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>User</th>
                      <th>Date</th>
                      <th>Details</th>
                      <th>Category</th>
                      <th>Tm St</th>
                      <th>Hours</th>
                      <th>Rate</th>
                      <th>Amount</th>
                      <th>Currency</th>

                    </tr>
                  </thead>
                 <tbody class="show_counting_data"><tbody>

                </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
  {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script> --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script>

      function CashFlowbtn(id){
          $(".action-cashflowbtn-tr-"+id).toggleClass('d-none')
      }


  $(function() {
    $(document).on('click', '.show-calculation', function() {
        var issueId = $(this).data('id');

        $.ajax({
            url: "{{route('cashflow.getPaymentDetails')}}",
            data: {id: issueId},
            success: function (data) {
              $(".show_counting_data").html(data);
              $("#show_counting").modal();
            }
        });


    });


  $('input[name="daterange"]').daterangepicker({
      autoUpdateInput: false,
      opens: 'left'
    }, function(start, end, label) {
      console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
      $('#hidden_daterange').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
    });
  });

</script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('#date-datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
      });

      $('.enter-date').datetimepicker({
        format: 'YYYY-MM-DD'
      });
    });

    $('#cashflow_type').on('change', function() {
      var type = $(this).val();
      var categories = @json($categories);

      $('#cashflow_category').empty();

      $('#cashflow_category').append($('<option>', {
        value: '',
        text: 'Select Category'
      }));

      Object.keys(categories[type]).forEach(function(category_id) {
        $('#cashflow_category').append($('<option>', {
          value: category_id,
          text: categories[type][category_id]
        }));
      });
    });


    $(document).on("click",".do-payment-btn",function() {
      var $this = $(this);
      var id  = $this.data("id");
      var mnt = $this.data("mnt-account");
      var amount = $this.data("mnt-amount");
      $("#do-payment-model").find(".text-cashflow-id").html("#"+id);
      $("#do-payment-model").find("#cashflow-id-txt").val(id);
      $("#do-payment-model").find("#monetary-account-id-txt").val(mnt);
      $("#do-payment-model").find("#cashflow-amount").val(amount);
      $("#do-payment-model").modal("show");
    });

    $(document).on('click', '.expand-row', function() {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });

    $(document).on("click",".submit-cashflow",function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        $.ajax({
            url: "{{url('/cashflow/do-payment')}}",
            type: 'POST',
            data : form.serialize(),
            dataType: 'json',
            beforeSend: function () {
                $("#loading-image").show();
            },
            success: function(result){
                $("#loading-image").hide();
                if(result.code == 200) {
                  $("#do-payment-model").modal("hide");
                  toastr["success"](result.message);
                }else if(result.code == 401) {
                   var html = result.message+"</br>";
                    $.each(result.data,function(i,k) {
                        $.each(k,function(p,m) {
                            html += m+"</br>";
                        });
                    });
                    toastr["error"](html);
                }
            },
            error: function (){
                $("#loading-image").hide();
            }
        });
    });

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
                $.ajax({
                    url: "{{url('cashflow')}}?ajax=1&page="+page,
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

        function get_bname()
        {
          module_type=$("#module_type").val();
          $("#b_name").empty();
          if (module_type!='')
          {

            $.ajax({
                    url: "{{url('cashflow/getbnamelist')}}?model_type="+ module_type,
                    type: 'GET',
                    success: function (data) {
                      $("#b_name").append("<option value=''>Benefiiciary</option>");
                      Object.entries(data).forEach(entry => {
                        const [key, value] = entry;
                        $("#b_name").append("<option value='"+data[key]+"'>"+data[key]+"</option>");
                     });

                    },
                    error: function () {

                    }
                });



          }
        }

  </script>
@endsection
