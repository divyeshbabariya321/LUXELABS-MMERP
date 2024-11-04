@extends('layouts.app')

@section('title', 'Account History')

@section('styles')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.min.css">
    
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Account History : {{$account->name}} ({{$history->total()}})</h2>
                <form id="formsearch" >
                <div class="form-group">
                        <div class="row">
                        <div class="col-md-4">
                        <input name="daterange" type="text" class="form-control" value="" placeholder="Name of keybard" id="term">
                        </div>
                            <div class="col-md-3">
                                <select class="form-control select-multiple " name="pricerange" tabindex="-1" aria-hidden="true">
                                    <option value="">Select Amount range </option>
                                            <option value="1">0-1000</option>
                                            <option value="2">1000-2000</option>
                                            <option value="3">2000-5000</option>
                                            <option value="4">5000-10000</option>
                                            <option value="5">Above 10000</option>
                                    </select></div>
                           
                            <div class="col-md-2">
                               <button type="button" class="btn btn-image" onclick="$('#formsearch').submit()"><img src="<?php echo asset('/images/filter.png');?>"></button>
                            </div>
                            
                        </div>
                    </div>
              </form>
            <div class="row">
                <div class="col-xs-12 col-md-12 col-lg-12 border">
                    <div class="clearfix"></div>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Beneficiary </th>
                                <th>Note</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Balance</th>
                                
                               
                            </tr>
                            </thead>

                            <tbody>
                            <?php $balance=0;?>    
                            @forelse ($history as $h)
                                 <?php 
                                      $balance+=$h->amount;
                                 ?>
                                <tr>
                                    <td><?php echo date("Y-m-d",strtotime($h->created_at)); ?></td>
                                    <td>{{ $h->model ? $h->model->name : '' }}</td>
                                    <td>{{ $h->note }}</td>
                                    <td>@if($h->amount<=0) 
                                          {{$h->amount}}
                                        @endif 
                                    </td>
                                    <td>@if($h->amount>0) 
                                          {{$h->amount}}
                                        @endif </td>
                                    <td>{{ $balance }}</td>
                                    
                                   
                                </tr>
                            @empty
                                <tr>
                                    <th colspan="6" class="text-center text-danger">No Account History Found.</th>
                                </tr>
                            @endforelse
                            </tbody>
                            {{ $history->render() }}
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! $history->appends(Request::except('page'))->links() !!}
@endsection
@section('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
$(function() {
  $('input[name="daterange"]').daterangepicker({
    opens: 'left'
  }, function(start, end, label) {
    console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
  });
});
</script>
@endsection

