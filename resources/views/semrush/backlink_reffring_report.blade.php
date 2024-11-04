@extends('layouts.app')

@section('favicon' , 'task.png')

@section('title', 'Backlink & Reffring Domain')

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
          <a href="#backlinks_overview" data-toggle="tab">BACKLINKS OVERVIEW</a>
        </li>
        <li>
          <a href="#backlinks" data-toggle="tab">BACKLINKS</a>
        </li>
        <li>
          <a href="#referring_domains" data-toggle="tab">REFERRING DOMAINS</a>
        </li>
        <li>
          <a href="#referring_ips" data-toggle="tab">REFERRING IPS</a>
        </li>
        <li>
          <a href="#tld_distribution" data-toggle="tab">TLD DISTRIBUTION</a>
        </li>
        <li>
          <a href="#referring_domains_by_country" data-toggle="tab">REFERRING DOMAINS BY COUNTRY</a>
        </li>
        <li>
          <a href="#anchors" data-toggle="tab">ANCHORS</a>
        </li>
        <li>
          <a href="#indexed_pages" data-toggle="tab">INDEXED PAGES</a>
        </li>
        <li>
          <a href="#competitors" data-toggle="tab">COMPETITORS</a>
        </li>
        <li>
          <a href="#comparison_bu_referring_domains" data-toggle="tab">COMPARISON BY REFERRING DOMAINS</a>
        </li>
        <li>
          <a href="#batch_comparision" data-toggle="tab">BATCH COMPARISON</a>
        </li>
        <li>
          <a href="#authority_score_profile" data-toggle="tab">AUTHORITY SCORE PROFILE</a>
        </li>
        <li>
          <a href="#categories_profile" data-toggle="tab">CATEGORIES PROFILE</a>
        </li>
        <li>
          <a href="#categories" data-toggle="tab">CATEGORIES</a>
        </li>
        <li>
          <a href="#historical_data" data-toggle="tab">HISTORICAL DATA</a>
        </li>

      </u>
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
    
@endsection
