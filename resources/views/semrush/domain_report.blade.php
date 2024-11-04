@extends('layouts.app')

@section('favicon' , 'task.png')

@section('title', 'Domain Report')

@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css"> --}}
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"> -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
    <style>
    
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
        <li class="active tab_cls" data-tab="domain_organic_search_keywords">
          <a href="#domain_organic_search_keywords" data-toggle="tab">DOMAIN ORGANIC SEARCH KEYWORDS</a>
        </li>
        <li class="tab_cls" data-tab="domain_paid_search_keywords">
          <a href="#domain_paid_search_keywords" data-toggle="tab">DOMAIN PAID SEARCH KEYWORDS</a>
        </li>
        <li class="tab_cls" data-tab="ads_copies">
          <a href="#ads_copies" data-toggle="tab">ADS COPIES</a>
        </li>
        <li class="tab_cls" data-tab="competitors_in_organic_search">
          <a href="#competitors_in_organic_search" data-toggle="tab">COMPETITORS IN ORGANIC SEARCH</a>
        </li>
        <li class="tab_cls" data-tab="competitors_in_paid_search">
          <a href="#competitors_in_paid_search" data-toggle="tab">COMPETITORS IN PAID SEARCH</a>
        </li>
        <li class="tab_cls" data-tab="domain_ad_history">
          <a href="#domain_ad_history" data-toggle="tab">DOMAIN AD HISTORY</a>
        </li>
        <li class="tab_cls" data-tab="domain_vs_domain">
          <a href="#domain_vs_domain" data-toggle="tab">DOMAIN VS. DOMAIN</a>
        </li>
        <li class="tab_cls" data-tab="domain_pla_search_keywords">
          <a href="#domain_pla_search_keywords" data-toggle="tab">DOMAIN PLA SEARCH KEYWORDS</a>
        </li>
        <li class="tab_cls" data-tab="pla_cpoies">
          <a href="#pla_cpoies" data-toggle="tab">PLA COPIES</a>
        </li>
        <li class="tab_cls" data-tab="pla_competitors">
          <a href="#pla_competitors" data-toggle="tab">PLA COMPETITORS</a>
        </li>
        <li class="tab_cls" data-tab="domain_organic_pages">
          <a href="#domain_organic_pages" data-toggle="tab">DOMAIN ORGANIC PAGES</a>
        </li>
        <li class="tab_cls" data-tab="domain_organic_subdomains">
          <a href="#domain_organic_subdomains" data-toggle="tab">DOMAIN ORGANIC SUBDOMAINS</a>
        </li>
      </u>
    </div>

    <div class="tab-content" style="margin-top:20px;">
    
        <!-- <div class="tab-pane active mt-3" id="domain_organic_search_keywords">
            sdfds
        </div>

        <div class="tab-pane mt-3" id="domain_paid_search_keywords">
            sdfdsdd
        </div> -->
        

        <form>
            <div class="form-row">
                <div class="form-group col-md-3 col-sm-6 d-none" >
                    <input type="text" class="form-control" name="type" id="type" placeholder="Type" >
                </div>
                <div class="form-group col-md-3 col-sm-6 d-none" >
                    <input type="text" class="form-control" name="key" id="key" placeholder="Key" >
                </div>
                <div class="form-group col-md-3 col-sm-6 d-none" >
                    <input type="text" class="form-control" name="domain" id="domain" placeholder="Domain" >
                </div>
                <div class="form-group col-md-3 col-sm-6 d-none" >
                    <input type="text" class="form-control" name="database" id="database" placeholder="Database" >
                </div>
                <div class="form-group col-md-3 col-sm-6 d-none" >
                    <input type="text" class="form-control" name="display_limit" id="display_limit" placeholder="Display Limit" >
                </div>
                <div class="form-group col-md-3 col-sm-6 d-none" >
                    <input type="text" class="form-control" name="display_offset" id="display_offset" placeholder="Display Offset" >
                </div>
                <div class="form-group col-md-3 col-sm-6 d-none" >
                    <input type="text" class="form-control" name="export_escape" id="export_escape" placeholder="Export Escape" >
                </div>
                <div class="form-group col-md-3 col-sm-6 d-none" >
                    <input type="text" class="form-control" name="export_decode" id="export_decode" placeholder="Export Decode" >
                </div>
                <div class="form-group col-md-3 col-sm-6 d-none" >
                    <input type="text" class="form-control" name="display_date" id="display_date" placeholder="Display Date" >
                </div>
                <div class="form-group col-md-3 col-sm-6 d-none" >
                    <input type="text" class="form-control" name="display_daily" id="display_daily" placeholder="Display Daily" >
                </div>
                <div class="form-group col-md-3 col-sm-6 d-none" >
                    <input type="text" class="form-control" name="export_columns" id="export_columns" placeholder="Export Columns" >
                </div>
                <div class="form-group col-md-3 col-sm-6 d-none" >
                    <input type="text" class="form-control" name="display_sort" id="display_sort" placeholder="Display sort" >
                </div>
                <div class="form-group col-md-3 col-sm-6 d-none" >
                    <input type="text" class="form-control" name="display_positions" id="display_positions" placeholder="Display Position" >
                </div>
                <div class="form-group col-md-3 col-sm-6 d-none" >
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
    <script type="text/javascript">

       
    $( document ).ready(function() {
        var tab  = "domain_organic_search_keywords";
        getdata(tab)
    });

    $(document).on("click",".tab_cls",function(e){
        var tab = $(this).attr("data-tab");
        getdata(tab)
    });

    function getdata(tab){
        console.log("---- tab ----");
        console.log(tab);
        // $("input").prop('required',false);

        let current_tab = tab;
        var all_field = {
            'type' : [
                'domain_organic_search_keywords', 'domain_paid_search_keywords', 'ads_copies', 'competitors_in_organic_search', 'competitors_in_paid_search', 'domain_ad_history', 'domain_vs_domain', 'domain_pla_search_keywords', 'pla_cpoies', 'pla_competitors', 'domain_organic_pages', 'domain_organic_subdomains'
            ],
            'key' : [
                'domain_organic_search_keywords', 'domain_paid_search_keywords', 'ads_copies', 'competitors_in_organic_search', 'competitors_in_paid_search', 'domain_ad_history', 'domain_vs_domain', 'domain_pla_search_keywords', 'pla_cpoies', 'pla_competitors', 'domain_organic_pages', 'domain_organic_subdomains'
            ],
            'domain' : [
                'domain_organic_search_keywords', 'domain_paid_search_keywords', 'ads_copies', 'competitors_in_organic_search', 'competitors_in_paid_search', 'domain_ad_history', 'domain_vs_domain', 'domain_pla_search_keywords', 'pla_cpoies', 'pla_competitors', 'domain_organic_pages', 'domain_organic_subdomains'
            ],
            'database' : [
                'domain_organic_search_keywords', 'domain_paid_search_keywords', 'ads_copies', 'competitors_in_organic_search', 'competitors_in_paid_search', 'domain_ad_history', 'domain_vs_domain', 'domain_pla_search_keywords', 'pla_cpoies', 'pla_competitors', 'domain_organic_pages', 'domain_organic_subdomains'
            ],
            'display_limit' : [
                'domain_organic_search_keywords', 'domain_paid_search_keywords', 'ads_copies', 'competitors_in_organic_search', 'competitors_in_paid_search', 'domain_ad_history', 'domain_vs_domain', 'domain_pla_search_keywords', 'pla_cpoies', 'pla_competitors', 'domain_organic_pages', 'domain_organic_subdomains'
            ],
            'display_offset' : [
                'domain_organic_search_keywords', 'domain_paid_search_keywords', 'ads_copies', 'competitors_in_organic_search', 'competitors_in_paid_search', 'domain_ad_history', 'domain_vs_domain', 'domain_pla_search_keywords', 'pla_cpoies', 'pla_competitors', 'domain_organic_pages', 'domain_organic_subdomains'
            ],
            'export_escape' : [
                'domain_organic_search_keywords', 'domain_paid_search_keywords', 'ads_copies', 'competitors_in_organic_search', 'competitors_in_paid_search', 'domain_ad_history', 'domain_vs_domain', 'domain_pla_search_keywords', 'pla_cpoies', 'pla_competitors', 'domain_organic_pages', 'domain_organic_subdomains'
            ],
            'export_decode' : [
                'domain_organic_search_keywords', 'domain_paid_search_keywords', 'ads_copies', 'competitors_in_organic_search', 'competitors_in_paid_search', 'domain_ad_history', 'domain_vs_domain', 'domain_pla_search_keywords', 'pla_cpoies', 'pla_competitors', 'domain_organic_pages', 'domain_organic_subdomains'
            ],
            'display_date' : [
                'domain_organic_search_keywords', 'domain_paid_search_keywords', 'competitors_in_organic_search', 'competitors_in_paid_search', 'domain_ad_history', 'domain_vs_domain', 'domain_organic_pages', 'domain_organic_subdomains'
            ],
            'export_columns' : [
                'domain_organic_search_keywords', 'domain_paid_search_keywords', 'ads_copies', 'competitors_in_organic_search', 'competitors_in_paid_search', 'domain_ad_history', 'domain_vs_domain', 'domain_pla_search_keywords', 'pla_cpoies', 'pla_competitors', 'domain_organic_pages', 'domain_organic_subdomains'
            ],
            'display_sort' : [
                'domain_organic_search_keywords', 'domain_paid_search_keywords', 'ads_copies', 'competitors_in_organic_search', 'competitors_in_paid_search', 'domain_ad_history', 'domain_vs_domain', 'domain_pla_search_keywords', 'pla_cpoies', 'pla_competitors', 'domain_organic_pages'
            ],
            'display_positions' : [
                'domain_organic_search_keywords', 'domain_paid_search_keywords'
            ],
            'display_filter' : [
                'domain_organic_search_keywords', 'domain_paid_search_keywords', 'ads_copies', 'domain_ad_history', 'domain_vs_domain', 'domain_pla_search_keywords', 'pla_cpoies', 'domain_organic_pages'
            ], 
        }

        var domain_report_data = {
            "type" : {
                "domain_organic_search_keywords":"domain_organic",
                "domain_paid_search_keywords":"domain_adwords",
                "ads_copies":"domain_adwords_unique",
                "competitors_in_organic_search":"domain_organic_organic",
                "competitors_in_paid_search":"domain_adwords_adwords",
                "domain_ad_history":"domain_adwords_historical",
                "domain_vs_domain":"domain_domains",
                "domain_pla_search_keywords":"domain_shopping",
                "pla_cpoies":"domain_shopping_unique",
                "pla_competitors":"domain_shopping_shopping",
                "domain_organic_pages":"domain_organic_unique",
                "domain_organic_subdomains":"domain_organic_subdomains",
            }
        }

        console.log("++++++++++++++++++");
        console.log(domain_report_data);

        $.each( all_field, function( index, value ){

            if(domain_report_data[index] != undefined && domain_report_data[index][tab] != undefined)
            {
                console.log("++++++++index++++++++++");
                console.log(index);
                console.log(domain_report_data[index][tab]);
            }

            if(value.includes(tab))
            {
                $('#'+index).parent().removeClass('d-none');
            }else{
                $('#'+index).parent().addClass('d-none');            
            }
        });
    }
    </script>
@endsection
