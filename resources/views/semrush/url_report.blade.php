@extends('layouts.app')

@section('favicon' , 'task.png')

@section('title', 'URL Report')

@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
    <style>
        #message-wrapper {
            height: 450px;
            overflow-y: scroll;
        }
        .dis-none {
            display: none;
        }
        .pd-5 {
            padding:3px;
        }
        .cls_task_detailstextarea{
            height: 30px !important;
        }
        .cls_remove_allpadding{
            padding-right: 0px !important;
            padding-left: 0px !important;
        }
        .cls_right_allpadding{
            padding-right: 0px !important;
        }
        .cls_left_allpadding{
            padding-left: 0px !important;
        }
        #addNoteButton{
            margin-top: 2px;
        }
        #saveNewNotes{
            margin-top: 2px;
        }
        .col-xs-12.col-md-2{
            padding-left:5px !important; 
            padding-right:5px !important;
            height: 38px;
        }
        .cls_task_subject{
            padding-left: 9px;
        }
        #recurring-task .col-xs-12.col-md-6{
            padding-left:5px !important; 
            padding-right:5px !important;
        }
        #appointment-container .col-xs-12.col-md-6{
            padding-left:5px !important; 
            padding-right:5px !important;
        }
        #taskCreateForm .form-group{
            margin-bottom: 0px;
        }
        .cls_action_box .btn-image img{
            width: 12px !important;
        }
        .cls_action_box .btn.btn-image {
            padding: 2px;
        }
        .btn.btn-image {
            padding: 5px 3px;
        }
        .td-mini-container {
            margin-top: 9px;
        }
        .td-full-container{
            margin-top: 9px;   
        }
        .cls_textbox_notes{
            width: 100% !important;
        }
        .cls_multi_contact .btn-image img {
            width: 12px !important;
        }
        .cls_multi_contact{
            width: 100%;
        }
        .cls_multi_contact_first{
            width: 80%;
            display: inline-block;
        }
        .cls_multi_contact_second{
            width: 7%;
            display: inline-block;
        }
        .cls_categoryfilter_box .btn-image img {
            width: 12px !important;
        }
        .cls_categoryfilter_box{
            width: 100%;
        }
        .cls_categoryfilter_first{
            width: 80%;
            display: inline-block;
        }
        .cls_categoryfilter_second{
            width: 7%;
            display: inline-block;
        }
        .cls_comm_btn {
            margin-left: 3px;
            padding: 4px 8px;
        }
        .btn.btn-image.btn-call-data {
            margin-top: -9px;
        }
        .dis-none {
        display: none;
    }
    .no-due-date {
        background-color: #f1f1f1 !important;
    }
    .over-due-date {
        background-color: #777 !important;
        color:white;
    }
    .over-due-date .btn {
        background-color: #777 !important;
    }
    .over-due-date .btn .fa {
        color: black !important;
    }
    .no-due-date .btn {
        background-color: #f1f1f1 !important;
    }
    .pd-2 {
        padding:2px;
    }
    .zoom-img:hover {
    -ms-transform: scale(1.5); /* IE 9 */
    -webkit-transform: scale(1.5); /* Safari 3-8 */
    transform: scale(1.5); 
    }

    </style>
@endsection

@section('large_content')

    <div class="row">
        <div class="col-lg-12 text-center">
            <h2 class="page-heading">{{$title}}</h2>
        </div>
    </div>

    <div id="exTab2" class="mt-3">
      <ul class="nav nav-tabs">
        <li class="active">
          <a href="#url_organic_search_keywords" data-toggle="tab">URL ORGANIC SEARCH KEYWORDS</a>
        </li>
        <li>
          <a href="#url_paid_search_keywords" data-toggle="tab">URL PAID SEARCH KEYWORDS</a>
        </li> 
      </u>
    </div>
 
    <div class="tab-content" style="margin-top:20px;">
        <form>
            <div class="form-row">
                <div class="form-group col-md-3 col-sm-6" >
                    <input type="text" class="form-control" name="type" id="type" placeholder="Type" >
                </div>
                <div class="form-group col-md-3 col-sm-6" >
                    <input type="text" class="form-control" name="key" id="key" placeholder="Key" >
                </div>
                <div class="form-group col-md-3 col-sm-6" >
                    <input type="text" class="form-control" name="url" id="url" placeholder="Url" >
                </div>
                <div class="form-group col-md-3 col-sm-6" >
                    <input type="text" class="form-control" name="database" id="database" placeholder="Database" >
                </div>
                <div class="form-group col-md-3 col-sm-6" >
                    <input type="text" class="form-control" name="display_limit" id="display_limit" placeholder="Display Limit" >
                </div>
                <div class="form-group col-md-3 col-sm-6" >
                    <input type="text" class="form-control" name="display_offset" id="display_offset" placeholder="Display Offset" >
                </div>
                <div class="form-group col-md-3 col-sm-6" >
                    <input type="text" class="form-control" name="export_escape" id="export_escape" placeholder="Export Escape" >
                </div>
                <div class="form-group col-md-3 col-sm-6" >
                    <input type="text" class="form-control" name="export_decode" id="export_decode" placeholder="Export Decode" >
                </div>
                <div class="form-group col-md-3 col-sm-6" >
                    <input type="text" class="form-control" name="display_date" id="display_date" placeholder="Display Date" >
                </div> 
                <div class="form-group col-md-3 col-sm-6" >
                    <input type="text" class="form-control" name="export_columns" id="export_columns" placeholder="Export Columns" >
                </div>
                <div class="form-group col-md-3 col-sm-6" >
                    <input type="text" class="form-control" name="display_sort" id="display_sort" placeholder="Display sort" >
                </div> 
                <div class="form-group col-md-3 col-sm-6" >
                    <input type="text" class="form-control" name="display_filter" id="display_filter" placeholder="Display Filter" >
                </div>
            </div>
            <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
        </form>
    </div> 

    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
              50% 50% no-repeat;display:none;">
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script> --}}
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
    <script>
    let res = `Keyword;Position;Search Volume;CPC;Competition;Traffic (%);Traffic Cost;Number of Results;Trends;Title;Description
amazon;1;83100000;0.02;0.16;0.68;78114;81;1.00,0.67,0.67,0.67,0.67,0.67,0.67,0.81,0.67,0.67,0.67,0.81;Amazon.com Official Site | Huge Selection & Great Prices;Free Two-Day Shipping with Prime. Read Ratings & Reviews. Try Prime for Free. Explore Amazon Devices. Shop Best Sellers & Deals. Save with Our Low Prices. Shop Our Huge Selection. Fast Shipping.
amazon;1;83100000;0.02;0.16;0.68;78114;75;1.00,0.67,0.67,0.67,0.67,0.67,0.67,0.81,0.67,0.67,0.67,0.81;Amazon.com Official Site | Huge Selection & Great Prices;Free Two-Day Shipping with Prime. Shop Our Huge Selection. Try Prime for Free.
amazon;1;83100000;0.02;0.16;0.68;78114;2680000000;1.00,0.67,0.67,0.67,0.67,0.67,0.67,0.81,0.67,0.67,0.67,0.81;Amazon.com | Amazon® Official Site | Huge Selection & Great Prices;Free Two-Day Shipping with Prime. Explore Amazon Devices. Shop Our Huge Selection. Read Ratings & Reviews. Try Prime for Free. Fast Shipping. Save with Our Low Prices.
amazon;1;83100000;0.02;0.16;0.68;78114;84;1.00,0.67,0.67,0.67,0.67,0.67,0.67,0.81,0.67,0.67,0.67,0.81;Amazon® Official Site | Huge Selection & Great Prices;Free Two-Day Shipping with Prime. Shop Our Huge Selection. Fast Shipping. Read Ratings & Reviews. Shop Best Sellers & Deals. Stream Videos Instantly. Save with Our Low Prices.
amazon;1;83100000;0.02;0.16;0.68;78114;76;1.00,0.67,0.67,0.67,0.67,0.67,0.67,0.81,0.67,0.67,0.67,0.81;Amazon.com Official Site | Free 2-Day Shipping with Prime;Earth's biggest selection of books, electronics, apparel & more at low prices.
`;
    console.log(res, 9999999999999999999);
    </script>
@endsection
