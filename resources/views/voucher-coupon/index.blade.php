@extends('layouts.app')


@section('favicon' , 'task.png')

@section('title', 'Voucher Coupons')

@section("styles")
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
  <style type="text/css">
    .preview-category input.form-control {
      width: auto;
    }

    #loading-image {
              position: fixed;
              top: 50%;
              left: 50%;
              margin: -50px 0px 0px -50px;
          }

      .dis-none {
              display: none;
          }
      .pd-5 {
        padding: 3px;
      }
      .toggle.btn {
        min-height:25px;
      }
      .toggle-group .btn {
        padding: 2px 12px;
      }
      .latest-remarks-list-view tr td {
        padding:3px !important;
      }
      .select2-search--dropdown.select2-search--hide {display: block !important;}
  </style>

@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb mb-3">
            <h2 class="page-heading">
                Voucher Coupons
                <div style="float:right;">
                    <button type="button" class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#status-create">Add Status</button>
                    <button class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#newStatusColor"> Status Color</button>
                    <button type="button" class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#list-coupon-type-Modal" onclick="listCouponTypes()">List Coupon Codes</button>
                    <button type="button" class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#coupontypeModal"><i class="fa fa-plus"></i>Add Coupon Type</button>
                    <button type="button" class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#plateformModal"><i class="fa fa-plus"></i>Add Platform</button>
                    <button type="button" class="btn btn-secondary btn-xs" data-toggle="modal" data-target="#addvoucherModel"><i class="fa fa-plus"></i>Add Voucher</button>
                </div>
            </h2>
        </div>
    </div>
    @include('partials.flash_messages')
    <div class="row mb-3">
      <div class="col-sm-12">
        <form action="{{ route('list.voucher') }}" method="GET" class="form-inline align-items-start voucher-search" id="searchForm">
          <div class="row m-0 full-width" style="width: 100%;">
              <div class="col-md-2 col-sm-12">
              <select class="form-control select" name="plateform_id">
                  <option value="">Select Platform</option>
                  @foreach($platform as $key => $plate)
                    <option value="{{ $key }}" @if(request('plateform_id') == $plate) selected @endif >{{ $plate }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2 col-sm-12">
                <select class="form-control select" name="email_add" id="email_add">
                  <option value="">Select Email</option>
                  @foreach($emails as $ekey => $emailid)
                    <option value="{{ $emailid }}" @if(request('email_add') == $emailid) selected @endif>{{ $ekey }}</option>
                  @endforeach
                </select>
                    @if($errors->has('email_add'))
                      <div class="form-control-plateform">{{$errors->first('email_add')}}</div>
                    @endif
              </div>

              <div class="col-md-2 col-sm-12">
                  <select class="form-control select" name="whatsapp_id" id="whatsapp_id">
                    <option value="">Select Number</option>
                    @foreach($whatsapp_configs as $key => $num)
                      <option value="{{ $key }}" @if(request('whatsapp_id') == $key) selected @endif >{{ $num }}</option>
                    @endforeach
                  </select>
                    @if($errors->has('whatsapp_id'))
                      <div class="form-control-plateform">{{$errors->first('whatsapp_id')}}</div>
                    @endif
              </div>
                
            <div class="col-md-1"><button type="submit" class="btn btn-image"><img src="/images/search.png" /></button></div>
          </div>
        </form>
      </div>
    </div>


<div class="col-sm">
    <div class="table-responsive vendor-payments-list">
        <table class="table table-bordered"style="table-layout: fixed;">
            <tr>
                <th style="width:5%;">SR. No</th>                
                <th style="width:10%";>Platform</th>
                <th style="width:15%;">Email Address</th>
                <th style="width:15%;">Whatsapp Number</th>
                <th>Remark</th>
                <th style="width:10%;">Status</th>
                <th style="width:10%;">Action</th>
            </tr>
            @php
            $totalRateEstimate = 0;
            $totalCount = 0;
            $totalBalance = 0;
            $index = 0;
            @endphp

            @foreach ($voucher as $vou)
                @php
                    $status_color = $status->where('id', $vou->status_id)->first();
                    if ($status_color == null) {
                        $status_color = new stdClass();
                    }
                @endphp
                <?php $totalCount++; ?>

                <tr style="background-color: {{$status_color->status_color ?? ""}}!important;">
                    <td>{{$vou->id}}</td>

                    
                    <td>{{ $vou->plateform_name}}</td>

                    <td class="Website-task">{{$vou->from_address}}</td>

                    <td>{{ $vou->number }} </td>

                    <td>
                        <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                            <input style="margin-top: 0px;width:80% !important;" type="text" class="form-control " name="message" placeholder="Remarks" value="" id="remark_{{$vou->id}}">
                            <div style="margin-top: 0px;" class="d-flex p-0">
                                <button class="btn pr-0 btn-xs btn-image " onclick="saveRemarks({{$vou->id}})"><img src="/images/filled-sent.png"></button>
                                <button type="button" data-id="{{$vou->id}}" class="btn btn-image remarks-history-show p-0 ml-2" title="Status Histories"><i class="fa fa-info-circle"></i></button>
                            </div>
                        </div>
                    </td>

                    <!-- @if($vou->voucherCouponRemarks->count())
                        @foreach ($vou->voucherCouponRemarks as $voucherCouponRemark)
                            @if(!is_null($voucherCouponRemark->remark))
                                <td class="expand-row-msg" data-name="remark" data-id="{{$vou->id}}">
                                    <span class="show-short-remark-{{$vou->id}}">{{ Str::limit($voucherCouponRemark->remark, 150, '..')}}</span>
                                    <span style="word-break:break-all;" class="show-full-remark-{{$vou->id}} hidden">{{$voucherCouponRemark->remark}}</span>
                                </td>
                            @else
                                <td> - </td>
                            @endif
                            @break
                        @endforeach
                    @else
                        <td> - </td>
                    @endif -->

                    <td style="width: 25%;">
                        <div class="d-flex align-items-center">
                            <select name="status" class="status-dropdown form-control" data-id="{{$vou->id}}">
                            <option value="">Select Status</option>
                            @foreach ($status as $stat)
                            <option value="{{$stat->id}}" {{$vou->status_id == $stat->id ? 'selected' : ''}}>{{$stat->status_name}}</option>
                            @endforeach
                            </select>
                            <button type="button" data-id="{{ $vou->id  }}" class="btn btn-image status-history-show p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>
                        </div>
                    </td>
                    <td>
                        <button type="button" data-toggle="tooltip" title="edit" data-id="{{$vou->id}}" class="btn btn-edit pd-5">
                        <i class="fa fa-edit" aria-hidden="true"></i>
                        </button>
                        <!-- <button type="button" data-id="{{ $vou->id }}"  title="Remark" class="btn btn-store-development-remark pd-5">
                        <i class="fa fa-comment" aria-hidden="true"></i>
                        </button> -->
                        <button type="button" class="btn btn-xs  link-delete" title="Delete Record"  data-id="{{ $vou->id }}" >
                        <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="btn btn-xs voucher-code-list-model" title="Coupon Codes"  data-id="{{ $vou->id }}" >
                        <b>C</b>
                        </button>                        
                        <button type="button" class="btn btn-xs voucher-code-order-list-model"  title="Orders" data-id="{{ $vou->id }}" >
                        <b>O</b>
                        </button>
                    </td>
                </tr>
          @endforeach
      </table>
      {{ $voucher->links()}}
    </div>
    </div>
    @php
            $index = 0;
    @endphp
    @foreach($voucher as $vou)
          @if($vou->voucherCouponRemarks->count())
                  <div class="modal fade" id="exampleModal{{ $index++ }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                          <div class="modal-content">
                              <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="false">Remark for Platform - {{ $vou->plateform_name }}</span>
                                  </button>
                              </div>
                              <div class="modal-body">
                       <ul>
                                  @foreach ($vou->voucherCouponRemarks as $voucherCouponRemark)
                             @if(!is_null($voucherCouponRemark->remark))
                                    <li>{{ $voucherCouponRemark->remark }}</li>
                                     @endif
                                  @endforeach
                        </ul>
                    </div>
                   </div>
                  </div>
              </div>
           @endif
    @endforeach
    <div id="paymentModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content" id="payment-content">
                
            </div>
        </div>
    </div>
    
     <!-- Coupon type Modal content-->
    <div id="coupontypeModal" class="modal fade in" role="dialog">
      <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Add Coupon Type</h4>
              <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
              <form action="#" method="POST" id="coupon_type_form">
                  @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            {{ html()->label('Name', 'coupon_type_name')->class('form-control-label') }}
                            {{ html()->text('coupon_type_name')->class('form-control  ' . ($errors->has('coupon_type_name') ? 'form-control-danger' : (count($errors->all()) > 0 ? 'form-control-success' : '')))->required()->attribute('rows', 3) }}
                                @if($errors->has('coupon_type_name'))
                        <div class="form-control-feedback">{{$errors->first('coupon_type_name')}}</div>
                                    @endif
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-default save-coupon-type">Save</button>
                    </div>
                  </div>
              </form>
          </div>

      </div>
  </div>

    <div id="plateformModal" class="modal fade in" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Add Plateform</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
              </div>
                <form action="#" method="POST" id="plateform_form">
                    @csrf
                      <div class="modal-body">
                          <div class="form-group">
                              {{ html()->label('Name', 'plateform_name')->class('form-control-label') }}
                              {{ html()->text('plateform_name')->class('form-control  ' . ($errors->has('plateform_name') ? 'form-control-danger' : (count($errors->all()) > 0 ? 'form-control-success' : '')))->required()->attribute('rows', 3) }}
                                  @if($errors->has('rplateform_name'))
                          <div class="form-control-feedback">{{$errors->first('plateform_name')}}</div>
                                      @endif
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          <button type="submit" class="btn btn-default save-plateform">Save</button>
                      </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <div id="editvoucherModel" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <form action="" method="POST" id="addupdate" >
                    @csrf
                    <input type="hidden" name="id" id="idadd">
                    <div class="modal-header">
                        <h4 class="modal-title">Add / Update Voucher</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                            <div class="form-group">
                                {{ html()->label('Plateform', 'plateform_id')->class('form-control-label') }}
                                <select class="form-control select" name="plateform_id" id="plateformadd_id" required style="width: 100%;">
                                  <option value="">Select Plateform Name</option>
                                  @foreach($platform as $key => $plat)
                                    <option value="{{ $key }}">{{ $plat }}</option>
                                  @endforeach
                                </select>
                                    @if($errors->has('plateform_id'))
                                      <div class="form-control-plateform">{{$errors->first('plateform_id')}}</div>
                                    @endif
                            </div>


                            <div class="form-group">
                              {{ html()->label('Email', 'email_id')->class('form-control-label') }}
                              <select class="form-control select" name="email_id" id="emailadd_id" required style="width: 100%;">
                                <option value="">Select Email</option>
                                @foreach($emails as $ekey => $emailid)

                                  <option value="{{ $emailid }}">{{ $ekey }}</option>
                                @endforeach
                              </select>
                                  @if($errors->has('email_id'))
                                    <div class="form-control-plateform">{{$errors->first('email_id')}}</div>
                                  @endif
                            </div>

                            <div class="form-group">
                                {{ html()->label('Number', 'whatsapp_config_id')->class('form-control-label') }}
                                <select class="form-control select" name="whatsapp_config_id" id="whatsapp_configadd_id" required style="width: 100%;">
                                  <option value="">Select Number</option>
                                  @foreach($whatsapp_configs as $key => $num)
                                    <option value="{{ $key }}">{{ $num }}</option>
                                  @endforeach
                                </select>
                                  @if($errors->has('whatsapp_config_id'))
                                    <div class="form-control-plateform">{{$errors->first('whatsapp_config_id')}}</div>
                                  @endif
                            </div>
                            <div class="form-group">
                              {{ html()->label('Password', 'password')->class('form-control-label') }}
                              <input type="text" class="form-control" name="password" id="passwordadd" style="width: 100%;"/>
                                
                                @if($errors->has('password'))
                                  <div class="form-control-plateform">{{$errors->first('password')}}</div>
                                @endif
                          </div>
                        </div>
                    
                      <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          <button type="submit" class="btn btn-danger save-voucher-update">Submit</button>
                      </div>
                    </div>
                </form>
            </div>

        </div>

        <div id="remarkModel" class="modal fade in" role="dialog">
          <div class="modal-dialog">
  
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title">Add Plateform</h4>
                  <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                  <form action="#" method="POST" id="plateform_form">
                      @csrf
                      <input type="hidden" id="hidden-id">
                        <div class="modal-body">
                            <div class="form-group">
                                {{ html()->label('Remark', 'remark_name')->class('form-control-label') }}
                                {{ html()->text('remark_name')->class('form-control  ' . ($errors->has('remark_name') ? 'form-control-danger' : (count($errors->all()) > 0 ? 'form-control-success' : '')))->required()->attribute('rows', 3) }}
                                    @if($errors->has('remark_name'))
                            <div class="form-control-feedback">{{$errors->first('remark_name')}}</div>
                                        @endif
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-default save-remark">Save</button>
                        </div>
                      </div>
                  </form>
              </div>
  
          </div>
      </div>
    </div>
    
      <div id="voucher-code-list-model" class="modal fade in" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">List Coupon Code</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
          <button type="button" class="btn btn-xs btn-store-code" title="Add Coupon Code"  >
            <i class="fa fa-plus btn-store-code-i"></i>
          </button>
          <table class="table">
            <thead class="thead-light">
              <tr>
                <th>ID</th>
                <th>Coupon code</th>
                <th>Platform Name</th>
                <th>Coupon Type</th>
                <th>Added By</th>
                <th>Valid Date</th>
                <th>Remark</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody class="voucher-code-list">
              
            </tbody>
          </table>
        </div>
      </div>
    </div>
    
  <!-- List Coupon type Modal-->
  <div id="list-coupon-type-Modal" class="modal fade in" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">List Coupon codes</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <table class="table">
            <thead class="thead-light">
              <tr>
                <th>S.No</th>
                <th>Coupon code</th>
                <th>Platform Name	</th>
                <th>Coupon Type	</th>
                <th>Added By	</th>
                <th>Valid Date</th>
                <th>Remarks</th>
                <th> Action </th>
              </tr>
            </thead>
            <tbody class="coupon-type-list">
              
            </tbody>
          </table>
          <!-- Pagination links -->
          <div class="pagination-container"></div>
        </div>
      </div>
    </div>
  </div>


    <div id="addVoucherCouponCodeModel" class="modal fade" role="dialog">
      <div class="modal-dialog">
          <!-- Modal content-->
          <div class="modal-content">
              <form action="" method="POST" id="addupdateCode" >
                  @csrf
                  <input type="hidden" name="voucher_coupons_id" class="voucher_coupons_id">
                  <div class="modal-header">
                      <h4 class="modal-title">Add Voucher Coupon Code</h4>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <div class="modal-body">
                          <div class="form-group">
                              {{ html()->label('Coupon Code', 'coupon_code')->class('form-control-label') }}
                              <input type="text" class="form-control coupon_code" name="coupon_code" id="coupon_code" required style="width: 100%;">
                                  @if($errors->has('coupon_code'))
                                    <div class="form-control-plateform">{{$errors->first('coupon_code')}}</div>
                                  @endif
                          </div>
                          <div class="form-group">
                            {{ html()->label('Valid Date', 'valid_date')->class('form-control-label') }}
                            <input type="text" class="form-control valid_date" name="valid_date" id="valid_date" style="width: 100%;" required/>
                              
                              @if($errors->has('valid_date'))
                                <div class="form-control-plateform">{{$errors->first('valid_date')}}</div>
                              @endif
                        </div>
                        <div class="form-group">
                          {{ html()->label('CouponType', 'CouponType')->class('form-control-label') }}
                          <select class="form-control select" name="coupon_type_id" id="coupon_type_id" required style="width: 100%;">
                            <option value="">Select Coupon Type</option>
                            @foreach($coupontypes as $key => $coupontype)
                              <option value="{{ $key }}">{{ $coupontype }}</option>
                            @endforeach
                          </select>
                              @if($errors->has('coupon_type_id'))
                                <div class="form-control-plateform">{{$errors->first('coupon_type_id')}}</div>
                              @endif
                        </div>
                        <div class="form-group">
                          {{ html()->label('Remark', 'code_remark')->class('form-control-label') }}
                          <input type="text" class="form-control datepicker" name="code_remark" id="code_remark" style="width: 100%;" required/>
                            
                            @if($errors->has('valid_date'))
                              <div class="form-control-plateform">{{$errors->first('code_remark')}}</div>
                            @endif
                      </div>
                      </div>
                  
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-default save-voucher-code">Submit</button>
                    </div>
                  </div>
              </form>
          </div>
      </div>

      <div id="voucher-code-order-list-model" class="modal fade in" role="dialog">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">List Coupon orders</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
            <button type="button" class="btn btn-xs store-code-order" title="Add Order"  >
              <i class="fa fa-plus store-code-order-i"></i>
            </button>
            <table class="table">
              <thead class="thead-light">
                <tr>
                  <th>ID</th>
                  <th>Date Order Placed</th>
                  <th>Added By</th>
                  <th>Order No</th>
                  <th>Order Amount</th>
                  <th>Discount</th>
                  <th>Final Amount</th>
                  <th>Refund Amount</th>
                  <th>Remark</th>
                  <th >Action</th>
                </tr>
              </thead>
              <tbody class="voucher-code-order-list">
                
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div id="addVoucherCouponCodeOrderModel" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
              <form action="" method="POST" id="addupdateCodeOrderForm" >
                  @csrf
                  <input type="hidden" name="voucher_coupons_id" class="voucher_coupons_order_id">
                  <div class="modal-header">
                      <h4 class="modal-title">Add Voucher Coupon Code Order</h4>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <div class="modal-body">
                    <div class="form-group">
                      {{ html()->label('Date Order Placed', 'date_order_placed')->class('form-control-label') }}
                      <input type="text" class="form-control date_order_placed" name="date_order_placed" id="date_order_placed" style="width: 100%;" required/>
                        @if($errors->has('date_order_placed'))
                          <div class="form-control-plateform">{{$errors->first('date_order_placed')}}</div>
                        @endif
                    </div>
                  
                    <div class="form-group">
                        {{ html()->label('Order No', 'order_no')->class('form-control-label') }}
                        <input type="text" class="form-control" name="order_no" id="order_no" required style="width: 100%;">
                            @if($errors->has('order_no'))
                              <div class="form-control-plateform">{{$errors->first('order_no')}}</div>
                            @endif
                    </div>

                    <div class="form-group">
                      {{ html()->label('Order Amount', 'order_amount')->class('form-control-label') }}
                      <input type="text" class="form-control" name="order_amount" id="order_amount" required style="width: 100%;">
                          @if($errors->has('order_amount'))
                            <div class="form-control-plateform">{{$errors->first('order_amount')}}</div>
                          @endif
                    </div>

                    <div class="form-group">
                      {{ html()->label('Discount', 'discount')->class('form-control-label') }}
                      <input type="text" class="form-control" name="discount" id="discount" required style="width: 100%;">
                          @if($errors->has('discount'))
                            <div class="form-control-plateform">{{$errors->first('discount')}}</div>
                          @endif
                    </div>

                    <div class="form-group">
                      {{ html()->label('Final Amount', 'final_amount')->class('form-control-label') }}
                      <input type="text" class="form-control" name="final_amount" id="final_amount" required style="width: 100%;">
                          @if($errors->has('final_amount'))
                            <div class="form-control-plateform">{{$errors->first('final_amount')}}</div>
                          @endif
                    </div>

                    <div class="form-group">
                      {{ html()->label('Refund Amount', 'refund_amount')->class('form-control-label') }}
                      <input type="text" class="form-control" name="refund_amount" id="refund_amount" required style="width: 100%;">
                          @if($errors->has('refund_amount'))
                            <div class="form-control-plateform">{{$errors->first('refund_amount')}}</div>
                          @endif
                    </div>

                    <div class="form-group">
                      {{ html()->label('Remark', 'code_remark')->class('form-control-label') }}
                      <input type="text" class="form-control datepicker" name="code_remark" id="code_remark" style="width: 100%;" required/>
                        
                        @if($errors->has('valid_date'))
                          <div class="form-control-plateform">{{$errors->first('code_remark')}}</div>
                        @endif
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-default save-voucher-code-order">Submit</button>
                  </div>
                </div>
                </form>
              </div>
          </div>
        </div>

        <div id="status-create" class="modal fade in" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
      <h4 class="modal-title">Add Stauts</h4>
      <button type="button" class="close" data-dismiss="modal">×</button>
      </div>
      <form  method="POST" id="status-create-form">
        @csrf
        @method('POST')
          <div class="modal-body">
            <div class="form-group">
              {{ html()->label('Name', 'status_name')->class('form-control-label') }}
              {{ html()->text('status_name')->class('form-control')->required()->attribute('rows', 3) }}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary status-save-btn">Save</button>
          </div>
        </div>
      </form>
    </div>

  </div>
</div>

    <div id="vouchers-coupons-remarks-histories-list" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Remarks Histories</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="30%">Remarks</th>
                                    <th width="30%">Created Date</th>
                                </tr>
                            </thead>
                            <tbody class="vouchers-coupons-remarks-histories-list-view">
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
        @include('voucher-coupon.modal-status-color')
        @include('voucher-coupon.status-history')
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
<script type="text/javascript" src="{{ mix('webpack-dist/js/bootstrap-datetimepicker.min.js') }} "></script>
<script type="text/javascript">
    
$(document).on("click", ".status-save-btn", function(e) {
    e.preventDefault();
    var $this = $(this);
    $.ajax({
        url: "{{route('voucher.status.create')}}",
        type: "post",
        data: $('#status-create-form').serialize()
    }).done(function(response) {
        if (response.code = '200') {
            $('#loading-image').hide();
            $('#addPostman').modal('hide');
            toastr['success']('Status  Created successfully!!!', 'success');
            location.reload();
        } else {
            toastr['error'](response.message, 'error');
        }
    }).fail(function(errObj) {
        $('#loading-image').hide();
        toastr['error'](errObj.message, 'error');
    });
});

$(document).ready(function () {

    $('.assign-to.select2').select2({
        width: "100%"
    });

    $(".valid_date").datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $(".date_order_placed").datetimepicker({
        format: 'YYYY-MM-DD'
    });
    
    var uploadedDocumentMap = {}
    Dropzone.options.documentDropzone = {
        url: '{{ route("voucher.upload-documents") }}',
        maxFilesize: 20, // MB
        addRemoveLinks: true,
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        success: function (file, response) {
            $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">')
            uploadedDocumentMap[file.name] = response.name
        },
        removedfile: function (file) {
            file.previewElement.remove()
            var name = ''
            if (typeof file.file_name !== 'undefined') {
                name = file.file_name
            } else {
                name = uploadedDocumentMap[file.name]
            }
            $('form').find('input[name="document[]"][value="' + name + '"]').remove()
        },
            init: function () {
        }
    }
});
   
$(document).on("click",".save-coupon-type",function(e){
    e.preventDefault();
    var $this = $(this);
    var formData = new FormData($this.closest("form")[0]);
    $.ajax({
        url: '{{route("voucher.coupon.type.create")}}',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        dataType:"json",
        data: $this.closest("form").serialize(),
        beforeSend: function() {
            $("#loading-image").show();
        }
    }).done(function (data) {
        $("#loading-image").hide();
        toastr["success"](data.message);
        location.reload();
    }).fail(function (jqXHR, ajaxOptions, thrownError) {      
        toastr["error"](jqXHR.responseJSON.message);
        $("#loading-image").hide();
    });
});

  $(document).on("click",".save-plateform",function(e){
    e.preventDefault();
    var $this = $(this);
    var formData = new FormData($this.closest("form")[0]);
    $.ajax({
      url: '{{route("voucher.plateform.create")}}',
      type: 'POST',
      headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        dataType:"json",
      data: $this.closest("form").serialize(),
      beforeSend: function() {
        $("#loading-image").show();
            }
    }).done(function (data) {
      $("#loading-image").hide();
      toastr["success"]("Document uploaded successfully");
      location.reload();
    }).fail(function (jqXHR, ajaxOptions, thrownError) {      
      toastr["error"](jqXHR.responseJSON.message);
      $("#loading-image").hide();
    });
  });

  $(document).on("click",".save-voucher-update",function(e){
    e.preventDefault();
    var $this = $(this);
    var formData = new FormData($this.closest("form")[0]);
    $.ajax({
      url: '{{route("voucher.coupon.store")}}',
      type: 'POST',
      headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        dataType:"json",
      data: $this.closest("form").serialize(),
      beforeSend: function() {
        $("#loading-image").show();
            }
    }).done(function (data) {
      $("#loading-image").hide();
      toastr["success"](data.message);
      location.reload();
    }).fail(function (jqXHR, ajaxOptions, thrownError) {      
      toastr["error"](jqXHR.responseJSON.message);
      $("#loading-image").hide();
    });
  });
  


  $(document).on("click",".btn-edit",function(e){
    e.preventDefault();
    var $this = $(this);
    let id = $this.data('id');
    var formData = new FormData($this.closest("form")[0]);
    $.ajax({
      url: '{{route("voucher.coupon.edit")}}',
      type: 'POST',
      headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        
      data: {
        id : id
      },
      beforeSend: function() {
        $("#loading-image").show();
            }
    }).done(function (response) {
      $("#loading-image").hide();
        form = $('#addupdate');
        
        $.each(response.data, function(key, v) {
          console.log(key);  
          if(key == 'platform_id'){
            $("#plateformadd_id").select2().val(v).trigger("change");
          }else if(key == 'email_address_id'){
            $("#emailadd_id").select2().val(v).trigger("change");
          }else if(key == 'whatsapp_config_id'){
            $("#whatsapp_configadd_id").select2().val(v).trigger("change");
          }else if(key == 'password') {
            $("#passwordadd").val(v)
          }else if(key == 'id') {
            $("#idadd").val(v)
          }       

        });
      $('#editvoucherModel').modal('show');
      toastr["success"](response.message);
    }).fail(function (jqXHR, ajaxOptions, thrownError) {      
      toastr["error"](jqXHR.responseJSON.message);
      $("#loading-image").hide();
    });
  });

  $("#plateform_id_new").select2();
  $("#email_add").select2();
  $("#whatsapp_id").select2();
  $("#plateformadd_id").select2()
  $("#whatsapp_config_id").select2();
  $("#email_id").select2()
  $("#coupon_type_id").select2();

  $(document).on("click",".btn-store-development-remark",function() {
      var $this = $(this);
      $("#remarkModel").modal("show");
       $("#hidden-id").val($this.data("id"));
  });
  $(document).on('click', '.save-remark', function(e) {
      e.preventDefault();
      var thiss = $(this);
      
      let id = $("#hidden-id").val();
      let remark = $("#remark_name").val();
      var type = 'post';
        $.ajax({
          url: '/vouchers-coupons/voucher/remark/'+id,
          type: type,
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        
          data: {
            id : id,
            remark : remark
          },
          beforeSend: function() {
            $("#loading-image").show();
          }
        }).done( function(response) {
          $("#remarkModel").modal("hide");
          toastr["success"](response.message);
        }).fail(function (jqXHR, ajaxOptions, thrownError) {      
          console.log(jqXHR.responseJSON.message);
          toastr["error"](jqXHR.responseJSON.message);
          $("#loading-image").hide();
        });
    });

    $(document).on("click",".link-delete",function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        var $this = $(this);
        if(confirm("Are you sure you want to delete records ?")) {
          $.ajax({
            url: '/vouchers-coupons/voucher/delete',
            type: 'POST',
            headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
              },
              dataType:"json",
            data: { id : id},
            beforeSend: function() {
              $("#loading-image").show();
                  }
          }).done(function (data) {
            $("#loading-image").hide();
            toastr["success"]("Record deleted successfully");
            $this.closest("tr").remove();
          }).fail(function (jqXHR, ajaxOptions, thrownError) {
            toastr["error"]("Oops,something went wrong");
            $("#loading-image").hide();
          });
        }
      });
      $(document).on("click",".btn-store-code",function() {
          var $this = $(this);
          let voucherId = $this.attr("data-vocid");
          $(".voucher_coupons_id").val(voucherId);
         
          $("#addVoucherCouponCodeModel").modal("show");
      });
      $(document).on("click",".save-voucher-code",function(e){
        e.preventDefault();
        var $this = $(this);
        var formData = new FormData($this.closest("form")[0]);
        $.ajax({
          url: '{{route("voucher.code.create")}}',
          type: 'POST',
          headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType:"json",
            data: $this.closest("form").serialize(),
          beforeSend: function() {
            $("#loading-image").show();
                }
        }).done(function (data) {
          $("#loading-image").hide();
            $.ajax({
            url: '{{route("voucher.code.list")}}',
            type: 'post',
            headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
              },
            dataType:"json",
            data :{
              voucher_coupons_id : $(".voucher_coupons_id").val()
            },
            beforeSend: function() {
              $("#loading-image").show();
                  }
          }).done(function (response) {
            $("#loading-image").hide();
            var html = "";
            $.each(response.data,function(k,v){
              html += "<tr>";
                html += "<td>"+v.id+"</td>";
                html += "<td>"+v.coupon_code+"</td>";
                html += "<td>"+v.plateform_name+"</td>";
                var couponType = v.couponType || "-";
                html += "<td>"+v.couponType+"</td>";
                html += "<td>"+v.userName+"</td>";
                html += "<td><div class='form-row'>"+v.valid_date+"</div></td>";
                html += "<td><div class='form-row'>"+v.remark+"</div></td>";
                html += '<td><a class="code-delete" data-type="code" data-id='+v.id+'><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
              html += "</tr>";
            });
            $(".voucher-code-list").html(html);
            $("#voucher-code-list-model").modal("show");
            toastr["success"](response.message);
          }).fail(function (response, ajaxOptions, thrownError) {
            toastr["error"](response.message);
            $("#loading-image").hide();
          });
          toastr["success"](data.message);
        }).fail(function (response) {      
          toastr["error"](response.message);
          $("#loading-image").hide();
        });
      });

      $(document).on("click",".store-code-order",function(e) {
          let $this = $(this);
          //debugger;
          var vocoid = '';
          vocoid = $(this).attr("data-vcoid");
          $(".voucher_coupons_order_id").val(vocoid);
          $("#addVoucherCouponCodeOrderModel").modal("show");
      });
      $(document).on("click",".save-voucher-code-order",function(e){
        e.preventDefault();
        var $this = $(this);
        var formData = new FormData($this.closest("form")[0]);
        $.ajax({
          url: '{{route("voucher.code.order.create")}}',
          type: 'POST',
          headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType:"json",
            data: $this.closest("form").serialize(),
          beforeSend: function() {
            $("#loading-image").show();
                }
        }).done(function (data) {
          $("#loading-image").hide();
          $.ajax({
            url: '{{route("voucher.code.order.list")}}',
            type: 'post',
            headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
              },
            dataType:"json",
            data :{
              voucher_coupons_id : $(".voucher_coupons_order_id").val()
            },
            beforeSend: function() {
              $("#loading-image").show();
                  }
          }).done(function (response) {
            $("#loading-image").hide();
            var html = "";
            $.each(response.data,function(k,v){
              html += "<tr>";
                html += "<td>"+v.id+"</td>";
                html += "<td>"+v.date_order_placed+"</td>";
                html += "<td>"+v.userName+"</td>";
                html += "<td>"+v.order_no+"</td>";
                html += "<td>"+v.order_amount+"</td>";
                html += "<td>"+v.discount+"</td>";
                html += "<td>"+v.final_amount+"</td>";
                html += "<td>"+v.refund_amount+"</td>";
                html += "<td><div class='form-row'>"+v.remark+"</div></td>";
                html += '<td><a class="code-order-delete" data-type="code" data-id='+v.id+'><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
              html += "</tr>";
            });
            $(".voucher-code-order-list").html(html);
            $("#voucher-code-order-list-model").modal("show");
            toastr["success"](response.message);
          }).fail(function (response, ajaxOptions, thrownError) {
            toastr["error"](response.message);
            $("#loading-image").hide();
          });
          toastr["success"](data.message);
        }).fail(function (response) {      
          toastr["error"](response.message);
          $("#loading-image").hide();
        });
      });


    $(document).on('click', '.expand-row', function() {
      var selection = window.getSelection();
      if (selection.toString().length === 0) {
        $(this).find('.td-mini-container').toggleClass('hidden');
        $(this).find('.td-full-container').toggleClass('hidden');
      }
    });
    $('#rejectVoucherModal').on('show.bs.modal', function (event) {
        var modal = $(this)
        var button = $(event.relatedTarget)
        var voucher = button.data('voucher')
        var url = "{{ url('voucher') }}/" + voucher.id + '/reject';
        modal.find('form').attr('action', url);
    });
    
    $(document).on("click",".voucher-code-list-model",function(e) {
        e.preventDefault();
        var $this = $(this);
        var id = $(this).data("id");
        $(".btn-store-code").attr("data-vocid", $this.data("id"));
        $(".btn-store-code-i").attr("data-vocid", $this.data("id"));
        $.ajax({
          url: '{{route("voucher.code.list")}}',
          type: 'post',
          headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
          dataType:"json",
          data :{
            voucher_coupons_id : id
          },
          beforeSend: function() {
            $("#loading-image").show();
                }
        }).done(function (response) {
          $("#loading-image").hide();
          var html = "";
          $.each(response.data,function(k,v){
            html += "<tr>";
              html += "<td>"+v.id+"</td>";
              html += "<td>"+v.coupon_code+"</td>";
              html += "<td>"+v.plateform_name+"</td>";
              var couponType = v.couponType || "-";
              html += "<td>"+couponType+"</td>";
              html += "<td>"+v.userName+"</td>";
              html += "<td><div class='form-row'>"+v.valid_date+"</div></td>";
              html += "<td><div class='form-row'>"+v.remark+"</div></td>";
              html += '<td><a class="code-delete" data-type="code" data-id='+v.id+'><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
            html += "</tr>";
          });
          $(".voucher-code-list").html(html);
          $("#voucher-code-list-model").modal("show");
          toastr["success"](response.message);
        }).fail(function (response, ajaxOptions, thrownError) {
          toastr["error"](response.message);
          $("#loading-image").hide();
        });
      });

      function listCouponTypes(pageNumber = 1) {
        $.ajax({
          url: '{{route("voucher.coupon.type.list")}}',
          type: 'GET',
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          data: {
            page: pageNumber
          },
          dataType: "json",
          beforeSend: function () {
            $("#loading-image").show();
          }
        }).done(function (response) {
          console.log(response.data);
          $("#loading-image").hide();
          var html = "";
          var startIndex = (response.data.current_page - 1) * response.data.per_page;

          $.each(response.data.data, function (index, couponType) {
            var sNo = startIndex + index + 1; 
            html += "<tr>";
            html += "<td>" + sNo + "</td>";
            html += "<td>" + couponType.coupon_code + "</td>";
            html += "<td>" + couponType.plateform_name + "</td>";
            var CouponType = couponType.couponType || "-"
            html += "<td>" + CouponType	 + "</td>";
            var couponremark = couponType.remark || "-";
            html += "<td>" + couponType.userName + "</td>";
            html += "<td>" + couponType.valid_date + "</td>";
            html += "<td>" + couponType.remark + "</td>";
            html += '<td><a class="code-delete" data-type="code" data-id='+couponType.id+'><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
            html += "</tr>";
          });
          $(".coupon-type-list").html(html);
          $("#list-coupon-type-Modal").modal("show");
          renderPagination(response.data);
        }).fail(function (response, ajaxOptions, thrownError) {
          toastr["error"](response.message);
          $("#loading-image").hide();
        });

      }

      function renderPagination(data) {
          var paginationContainer = $(".pagination-container");
          var currentPage = data.current_page;
          var totalPages = data.last_page;

          var html = "";
          if (totalPages > 1) {
            html += "<ul class='pagination'>";
            if (currentPage > 1) {
              html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changePage(" + (currentPage - 1) + ")'>Previous</a></li>";
            }
            for (var i = 1; i <= totalPages; i++) {
              html += "<li class='page-item " + (currentPage == i ? "active" : "") + "'><a class='page-link' href='javascript:void(0);' onclick='changePage(" + i + ")'>" + i + "</a></li>";
            }
            if (currentPage < totalPages) {
              html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changePage(" + (currentPage + 1) + ")'>Next</a></li>";
            }
            html += "</ul>";
          }

        paginationContainer.html(html);
      }

      function changePage(pageNumber) {
        listCouponTypes(pageNumber);
      }
  
      $(document).on("click",".code-delete",function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        var $this = $(this);
        if(confirm("Are you sure you want to delete records ?")) {
          $.ajax({
            url:'{{route("voucher.code.delete")}}',
            type: 'POST',
            headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
              },
              dataType:"json",
            data: { id : id},
            beforeSend: function() {
              $("#loading-image").show();
                  }
          }).done(function (data) {
            $("#loading-image").hide();
            toastr["success"]("Record deleted successfully");
            $this.closest("tr").remove();
          }).fail(function (jqXHR, ajaxOptions, thrownError) {
            toastr["error"]("Oops,something went wrong");
            $("#loading-image").hide();
          });
        }
      });

      $(document).on("click",".voucher-code-order-list-model",function(e) {
        e.preventDefault();
        var $this = $(this);
        //debugger;
        var id = $(this).data("id");
        $(".store-code-order").attr("data-vcoid","");
        $(".store-code-order").attr("data-vcoid", id);
        $(".store-code-order-i").attr("data-vcoid", id);

        $.ajax({
          url: '{{route("voucher.code.order.list")}}',
          type: 'post',
          headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
          dataType:"json",
          data :{
            voucher_coupons_id : id
          },
          beforeSend: function() {
            $("#loading-image").show();
                }
        }).done(function (response) {
          $("#loading-image").hide();
          var html = "";
          $.each(response.data,function(k,v){
            html += "<tr>";
              html += "<td>"+v.id+"</td>";
              html += "<td>"+v.date_order_placed+"</td>";
              html += "<td>"+v.userName+"</td>";
              html += "<td>"+v.order_no+"</td>";
              html += "<td>"+v.order_amount+"</td>";
              html += "<td>"+v.discount+"</td>";
              html += "<td>"+v.final_amount+"</td>";
              html += "<td>"+v.refund_amount+"</td>";
              html += "<td><div class='form-row'>"+v.remark+"</div></td>";
              html += '<td><a class="code-order-delete" data-type="code" data-id='+v.id+'><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
            html += "</tr>";
          });
          $(".voucher-code-order-list").html(html);
          $("#voucher-code-order-list-model").modal("show");
          toastr["success"](response.message);
        }).fail(function (response, ajaxOptions, thrownError) {
          toastr["error"](response.message);
          $("#loading-image").hide();
        });
      });

      
      $(document).on("click",".code-order-delete",function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        var $this = $(this);
        if(confirm("Are you sure you want to delete records ?")) {
          $.ajax({
            url:'{{route("voucher.code.order.delete")}}',
            type: 'POST',
            headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
              },
              dataType:"json",
            data: { id : id},
            beforeSend: function() {
              $("#loading-image").show();
                  }
          }).done(function (data) {
            $("#loading-image").hide();
            toastr["success"]("Record deleted successfully");
            $this.closest("tr").remove();
          }).fail(function (jqXHR, ajaxOptions, thrownError) {
            toastr["error"]("Oops,something went wrong");
            $("#loading-image").hide();
          });
        }
      });

      $('.status-dropdown').change(function(e) {
          e.preventDefault();
          var voucherId = $(this).data('id');
          var selectedStatus = $(this).val();

          // Make an AJAX request to update the status
          $.ajax({
            url:'{{route("voucher.update-status")}}',
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
              voucherId: voucherId,
              selectedStatus: selectedStatus
            },
            success: function(response) {
              toastr['success']('Status  Created successfully!!!', 'success');
              console.log(response);
            },
            error: function(xhr, status, error) {
              // Handle the error here
              console.error(error);
            }
          });
        });

      $(document).on('click', '.status-history-show', function() {
            var id = $(this).attr('data-id');
                $.ajax({
                    method: "GET",
                    url: `{{ route('voucher.status.histories', [""]) }}/` + id,
                    dataType: "json",
                    success: function(response) {
                        if (response.status) {
                            var html = "";
                            $.each(response.data, function(k, v) {
                                html += `<tr>
                                            <td> ${k + 1} </td>
                                            <td> ${(v.old_value != null) ? v.old_value.status_name : ' - ' } </td>
                                            <td> ${(v.new_value != null) ? v.new_value.status_name : ' - ' } </td>
                                            <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                            <td> ${v.created_at} </td>
                                        </tr>`;
                            });
                            $("#voucher_coupons-status-histories-list").find(".voucher_coupons-status-histories-list-view").html(html);
                            $("#voucher_coupons-status-histories-list").modal("show");
                        } else {
                            toastr["error"](response.error, "Message");
                        }
                    }
                });
      });

      $(document).on('click', '.expand-row-msg', function() {
        var name = $(this).data('name');
        var id = $(this).data('id');
        console.log(name);
        var full = '.expand-row-msg .show-short-' + name + '-' + id;
        var mini = '.expand-row-msg .show-full-' + name + '-' + id;
        $(full).toggleClass('hidden');
        $(mini).toggleClass('hidden');
    });

      function saveRemarks(voucher_coupons_id){

        var remarks = $("#remark_"+voucher_coupons_id).val();

        if(remarks==''){
            alert('Please enter remarks.');
            return false;
        }

        $.ajax({
            url: "{{route('voucher.saveremarks')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'voucher_coupons_id' :voucher_coupons_id,
                'remark' :remarks,
            },
            beforeSend: function() {
                $(this).text('Loading...');
                $("#loading-image").show();
            },
            success: function(response) {
                $("#loading-image").hide();
                toastr['success']('Remarks  Created successfully!!!', 'success');
            }
        }).fail(function(response) {
            $("#loading-image").hide();
            toastr['error'](response.responseJSON.message);
        });
    }

    $(document).on('click', '.remarks-history-show', function() {
        var voucher_coupons_id = $(this).attr('data-id');
        $.ajax({
            url: "{{route('voucher.getremarks')}}",
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'voucher_coupons_id' :voucher_coupons_id,
            },
            success: function(response) {
                if (response.status) {
                    var html = "";
                    $.each(response.data, function(k, v) {
                        html += `<tr>
                                    <td> ${k + 1} </td>
                                    <td> ${(v.remark != null) ? v.remark : ' - ' } </td>
                                    <td> ${v.created_at} </td>
                                </tr>`;
                    });
                    $("#vouchers-coupons-remarks-histories-list").find(".vouchers-coupons-remarks-histories-list-view").html(html);
                    $("#vouchers-coupons-remarks-histories-list").modal("show");
                } else {
                    toastr["error"](response.error, "Message");
                }
            }
        });
    });
  </script>
@endsection
