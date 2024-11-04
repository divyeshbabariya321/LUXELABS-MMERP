<style>
    .campaign.card{
        /*max-width: 1200px;*/
        /*margin: 20px auto;*/
    }
    .create-compaign-form {
        padding: 10px;
    }
    label.col-form-label{
        font-weight: 100 !important;
    }
    label.col-form-label{
        /*min-width:200px;*/
        padding-top: 0;
    }
    .lagend{
        padding: 0 5px;
        font-weight: bold !important;
    }
    .create-compaign-form h3, .create-compaign-form h2 {
        font-size: 16px !important;
        font-weight: 700 !important;
    }
    select.globalSelect2 + span.select2 , .create-compaign-form input[type="text"]{
        min-width: 100% !important;
    }
    #main_div_campaign_url_options div label{
        font-weight: 100 !important;
        /*min-width: 120px;*/
    }
    input{
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        height: 34px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
        border-radius: 4px;
    }
    #main_div_campaign_url_options div input{
        /*min-width: 200px;*/
    }
    hr {
        margin-top: 15px;
        margin-bottom: 15px;
    }
    legend {
        display: block;
        width: auto;
        max-width: 100%;
        padding: 0;
        margin-bottom: 0;
        font-size: 1.5rem;
        line-height: inherit;
        color: inherit;
        white-space: normal;
        border-bottom:none !important;
    }
    fieldset {
        padding: 10px 10px;
        margin: 0 2px;
        border: 1px solid #c0c0c07a;
        border-radius: 4px;
    }
    .select2-container .select2-selection--single {
        height: 34px !important;
    }
    #target_cost_per_action{
        min-width: 15px !important;
        height: 14px;
        margin-right: 7px;
    }

    .select2-container.select2-container--default.select2-container--open {
        width: 100% !important;
    }

    .select2.select2-container.select2-container--default {
        width: 100% !important;
    }

    div.pac-container {
        z-index: 99999999999 !important;
    }
</style>
{{--@extends('layouts.app')--}}
{{--@section('favicon' , 'task.png')--}}

{{--@section('content')--}}
  <div class="campaign-card">
{{--      <h2 class="spage-heading">Create Campaign</h2>--}}
      <form method="POST" id="CreateCompaign" class="create-compaign-form" enctype="multipart/form-data">
          @csrf
          <input type="hidden" value="<?php echo $_GET['account_id']; ?>" id="accountID" name="account_id"/>

         <div class="row m-0 mb-3">
             <div class="col-md-4 pl-0 pr-2">
                 <div class="form-group m-0 top">
                     <label for="campaign-name" class="col-form-label">Campaign name</label><br>
                     <div class="form-input">
                         <input type="text" class="form-control" id="campaign-name" name="campaignName" placeholder="Campaign name">
                         @if ($errors->has('campaignName'))
                             <span class="text-danger">{{$errors->first('campaignName')}}</span>
                         @endif
                     </div>
                 </div>
             </div>
             <div class="col-md-3 pl-2 pr-2">
                 <div class="form-group m-0 top">
                     <label for="campaign-status" class="  col-form-label">Channel Type</label><br>
                     <div class="form-input">
                         <select class="browser-default custom-select globalSelect2" id="channel_type" name="channel_type" style="height: auto">
                             <option value="SEARCH" selected>Search</option>
                             <option value="DISPLAY">Display</option>
                             <option value="SHOPPING">Shopping</option>
                             <option value="MULTI_CHANNEL">Multi Channel</option>
                             {{-- <option value="PERFORMANCE_MAX">Performance Max</option> --}}
                         </select>
                     </div>
                 </div>
             </div>
             <div class="col-md-5 pl-2 pr-0">
                 <div class="form-group m-0 top">
                     <label for="campaign-status" class="col-form-label">ChannelSub Type</label>
                     <div class="form-input">
                         <select class="browser-default custom-select" id="channel_sub_type" name="channel_sub_type" style="height: auto">
                             <option value="">Select subtype</option>
                             <option value="UNSPECIFIED" selected>Unspecified</option>
                             <option value="SEARCH_MOBILE_APP">Mobile App Campaigns for search</option>
                             <option value="DISPLAY_MOBILE_APP">Mobile App Campaigns for display</option>
                             <option value="SEARCH_EXPRESS">AdWords Express campaigns for search</option>
                             <option value="DISPLAY_EXPRESS">AdWords Express campaigns for display</option>
                             {{-- <option value="UNIVERSAL_APP_CAMPAIGN">Google manages the keywords and ads for these campaigns</option> --}}
                             <option value="DISPLAY_SMART_CAMPAIGN">Smart display campaign</option>
                             <option value="SHOPPING_GOAL_OPTIMIZED_ADS">Optimize automatically towards the retailer's business objectives</option>
                             <option value="DISPLAY_GMAIL_AD">Gmail Ad Campaigns</option>
                             <option value="APP_CAMPAIGN">App Campaign</option>
                             {{-- <option value="APP_CAMPAIGN_FOR_ENGAGEMENT">App Campaign for engagement</option> --}}
                             <option value="APP_CAMPAIGN_FOR_PRE_REGISTRATION">App Campaign for pre-registration (Android only)</option>
                         </select>
                     </div>
                 </div>
             </div>
         </div>


        <div class="mb-3" id="div_shipping_setting" style="display:none;">
            <fieldset>
                <legend class="lagend">Settings</legend>
                <div class="form-group m-0 row">
{{--                    <div class="col-md-6 pl-0 pr-3">--}}
{{--                        <label for="campaign-status" class=" col-form-label">Merchant Id</label><br>--}}
{{--                            <input type="text" id="merchant_id" name="merchant_id" value="">--}}
{{--                    </div>--}}
                    <div class="col-md-6 pl-0 pr-0">
                        <label for="campaign-status" class=" col-form-label">Select Country</label><br>
                            <input type="text" id="sales_country" name="sales_country" value="">
                            <br><span>E.g IN </span>
                    </div>
                </div>
            </fieldset>
        </div>

        <div class="mb-3" id="div_app_setting" style="display:none;">
            <fieldset>
                <legend class="lagend">App Settings</legend>
                <div class="form-group m-0 row">
                    <div class="col-md-6 pl-0 pr-3">
                        <label for="campaign-status" class=" col-form-label">App Id</label><br>
                            <input type="text" id="app_id" name="app_id" value="">
                    </div>
                    <div class="col-md-6 pl-0 pr-0">
                        <label for="campaign-status" class="col-sm- col-form-label">App Store</label>
                        <div>
                            <select class="browser-default custom-select globalSelect2" id="app_store" name="app_store" style="height: auto">
                                <option value="GOOGLE_APP_STORE" selected>Google App Store</option>
                                <option value="APPLE_APP_STORE">Apple App Store</option>
                            </select>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>


          <fieldset>
              <legend class="lagend">Bidding</legend> <!-- biddingstrategy -->
            <div class="row m-0">
                <div class="col-md-6 pl-0 pr-3">
                    <div class="form-group m-0">
                        <label for="campaign-status" class="col-sm- col-form-label">what do you want to focus on?</label>
                        <div>
                            <select class="browser-default custom-select globalSelect2" id="bidding_focus_on" name="bidding_focus_on" style="height: auto">
                                <option value="conversions" selected>Conversions</option>
                                <option value="conversions_value">Conversions value</option>
                                <option value="viewable_impressions">Viewable impressions</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 pr-0 pl-0">
                    <div class="form-group m-0">
                        <label for="campaign-status" class="col-form-label">Bidding Strategy</label>
                        <div id="biddingStrategyType_second_div">
                            <select class="browser-default custom-select" id="biddingStrategyType" name="biddingStrategyType" style="height: auto">
                                <option value="">Select bidding strategy</option>
                                @foreach($biddingStrategyTypes as $bskey=>$bs)
                                    <option value="{{$bskey}}">{{$bs}}</option>
                                @endforeach
                            </select>

                            <div id="maindiv_for_target" style="display:none;"><input type="checkbox" name="target_cost_per_action" id="target_cost_per_action" value="1"> Set a target cost per action
                                <div id="div_html_append_1" style="display:none;">
                                    <label>Target CPA</label>
                                    <input type="text" name="txt_target_cpa" id="txt_target_cpa" value="">
                                    <!-- <label>Pay For</lable>
                                    <select name="pay_for" id="pay_for">
                                    <option value="clicks">Clicks</option>
                                    <option value="viewable_impressions">Viewable Impressions</option>
                                    </select> -->
                                </div></div>
                            <div id="div_roas" style="display:none; margin-top:20px;">
                                <label>Target ROAS (This field must be between 0.01 and 1000.0, inclusive)</label>
                                <input type="text" name="txt_target_roas" id="txt_target_roas" value="0.01"> %
                            </div>
                            <div id="div_targetspend" style="display:none; margin-top:20px;">
                                <label>Maximize clicks (This field must be greater than or equal to 0)</label>
                                <input type="text" name="txt_maximize_clicks" id="txt_maximize_clicks" value="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
              <a href="javascript:void(0);" class="btn btn-link pl-0" id="directiBiddingSelect">Or, select a bid strategy directly (not recommended)</a>

              <a href="javascript:void(0);" class="btn btn-link" id="resetBiddingSection">Reset</a>

          </fieldset>



          <div class="form-group mt-3 m-0" id="main_div_campaign_url_options">
              <fieldset>
                  <legend> <label style=" font-weight: bold !important;" for="campaign-status" class="col-form-label lagend">Campaign URL options</label></legend>

              <div class="row m-0">
                  <div class="col-md-6 pl-0 pr-3">
                      <label>Tracking template</label>
                      <input type="text" name="tracking_template_url" id="tracking_template_url" />
                      <br><span style="display:block;color: #afabab">Example: https://www.trackingtemplate.foo/?url={lpurl}&id=5</span>
                  </div>
                  <div class="col-md-6 pr-0 pl-0">
                      <label>Final URL suffix</label>
                      <input type="text" name="final_url_suffix" id="final_url_suffix" /><br>
                      <span style="display:block;color: #afabab">Example: param1=value1&amp;param2=value2</span>
                  </div>

              </div>
              </fieldset>
          </div>
          <!-- <div class="form-group row" id="main_div_ad_rotation">
              <label for="campaign-status" class="col-sm-2 col-form-label">Ad rotation</label>
              <div class="col-sm-3">
              <input type="radio" name="ad_rotation" id="ad_rotation1" value="OPTIMIZE"><label id="ad_rotation_label1">Optimize: Prefer best performing ads</label> <br>
              <input type="radio" name="ad_rotation" id="ad_rotation2" value="ROTATE_INDEFINITELY"><label id="ad_rotation_label2">Do not optimize: Rotate ads indefinitely</label><br>
              <input type="radio" name="ad_rotation" id="ad_rotation3" value="CONVERSION_OPTIMIZE"><label id="ad_rotation_label3">Optimize for conversions</label><br>
              <input type="radio" name="ad_rotation" id="ad_rotation3" value="ROTATE"><label id="ad_rotation_label4">Rotate evenly</label>
              </div>
          </div> -->

        <div class="row m-0 mt-3">
            <div class="col-md-6 pl-0 pr-3">
                <div class="form-group m-0 mb-3">
                    <label for="budget-amount" class=" col-form-label">Budget amount ($)</label>
                    <div >
                        <input type="text" class="form-control" id="budget-amount" name="budgetAmount" placeholder="Budget amount ($)">
                        @if ($errors->has('budgetAmount'))
                            <span class="text-danger">{{$errors->first('budgetAmount')}}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6 pr-0 pl-0">
                <div class="form-group m-0 mb-3">
                    <label for="start-date" class="col-form-label">Start Date</label>
                    <div>
                        <input type="date" class="form-control" id="start-date" name="start_date" placeholder="Start Date E.g {{date('Ymd', strtotime('+1 day'))}}">
                        @if ($errors->has('start_date'))
                            <span class="text-danger">{{$errors->first('start_date')}}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row m-0">
            <div class="col-md-6 pl-0 pr-3">
                <div class="form-group m-0 mb-3">
                    <label for="start-date" class="col-form-label">End Date</label>
                    <div>
                        <input type="date" class="form-control" id="end-date" name="end_date" placeholder="End Date E.g {{date('Ymd', strtotime('+1 month'))}}">
                        @if ($errors->has('end_date'))
                            <span class="text-danger">{{$errors->first('end_date')}}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6 pr-0 pl-0" id="div-language-fields">
                <div class="form-group m-0 mb-5">
                    <label for="target_languages" class="col-form-label">Target Languages</label>
                    <div class="status-selection">
                        <select class="form-control multiselect" id="target_languages" name="target_languages[]" style="height: auto" multiple>
                            @foreach(\App\Models\GoogleLanguageConstant::whereIsTargetable(true)->orderBy('name', 'ASC')->get() as $lang)
                                <option value="{{ $lang->google_language_constant_id }}">{{ $lang->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row m-0" id="div-location-fields">
            <div class="col-md-6 pl-0 pr-3">
                <div class="form-group m-0 mb-3">
                    <label for="start-date" class="col-form-label">Location</label>
                    <div>
                        <input type="radio" class="" name="target_location" value="all" checked> All countries and territories
                        <input type="radio" class="" name="target_location" value="other"> Enter another location
                        @if ($errors->has('target_location'))
                            <span class="text-danger">{{$errors->first('target_location')}}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6 pr-0 pl-0 other_location_div" style="display: none;">

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group m-0 mb-5">
                            <div>
                                <input type="radio" class="" name="target_location_type" value="location" checked> Location
                                <input type="radio" class="" name="target_location_type" value="radius"> Radius
                            </div>
                        </div>
                    </div>
                </div>

                <div class="advance_type_location_div" style="display: none;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group m-0 mb-5">
                                <label for="country_id" class="col-form-label">Country</label>
                                <select class="form-control" id="" name="country_id" style="height: auto">

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group m-0 mb-5">
                                <label for="state_id" class="col-form-label">State</label>
                                <select class="form-control" id="" name="state_id" style="height: auto">

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group m-0 mb-5">
                                <label for="city_id" class="col-form-label">City</label>
                                <select class="form-control" id="" name="city_id" style="height: auto">

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group m-0 mb-5">
                                <div>
                                    <input type="radio" class="" name="is_target" value="1" checked> Target
                                    <input type="radio" class="" name="is_target" value="0"> Exclude
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="advance_type_radius_div" style="display: none;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group m-0 mb-5">
                                <label for="target_location_address" class="col-form-label">Address</label>

                                <input type="text" class="form-control" id="target_location_address" name="target_location_address" placeholder="Enter a place name, address or coordinates">

                               {{--  <select class="form-control" id="" name="target_location_address" style="height: auto">

                                </select> --}}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group m-0 mb-5">
                                <label for="target_location_distance" class="col-form-label">Distance</label>
                                <input type="number" name="target_location_distance" class="form-control" placeholder="Distance" min="1" max="500">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group m-0 mb-5">
                                <label for="target_location_radius_units" class="col-form-label">Radius Units</label>
                                <select class="form-control" id="" name="target_location_radius_units" style="height: auto">
                                    <option value="mi">mi</option>
                                    <option value="km">km</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row m-0 mb-4">
            <div class="col-md-6 pr-0 pl-0">
                <div class="form-group m-0 mb-5">
                    <label for="campaign-status" class="col-form-label">Campaign status</label>
                    <div>
                        <select class="browser-default custom-select globalSelect2" id="campaign-status" name="campaignStatus" style="height: auto">
                            <option value="1" selected>Enabled</option>
                            <option value="2">Paused</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>


          <button type="submit" id="create_comapagin" style="position: absolute;right: 100px;bottom: 17px;" class="btn btn-primary mb-2 ">Create</button>
      </form>
      <div class="container">

      </div>
  </div>

<script>
    $(document).on('click','#create_comapagin', function(event){
        event.preventDefault();

        var formulario =  $("#CreateCompaign");
        var formData = new FormData($(formulario)[0]);

        $.ajax({
            url: "/google-campaigns/create",
            type:"POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
            data:formData,
            processData: false,
            contentType: false,
            success:function(response){
                if(response.status !== undefined && response.status == false){
                    toastr.error("Someting went to wrong! Please check logs.");
                    location.reload();
                }else{
                    $('#create-compaign').modal('hide');
                    location.reload();
                }
            },

            error: function(jqXHR){
                if(jqXHR.responseJSON.message !== undefined){
                    toastr.error(jqXHR.responseJSON.message);
                }
            },
        });
    });

    var bidding_focus_on=$("#bidding_focus_on");
    var channel_type=$("#channel_type");
    var channel_sub_type=$("#channel_sub_type");

    $(document).ready(function(){
        biddingFocusBaseStrategy();
        channelTypeChangeFunc();

        function biddingFocusBaseStrategy(){
            /* var biddingStrategyArray= '<?php //echo json_encode(array_keys($biddingStrategyTypes)); ?>';
            biddingStrategyArray=JSON.parse(biddingStrategyArray); */
            var biddingStrategyArray=[];
            //start re-arranging everything
            var bidding_focus_on_val=bidding_focus_on.val();
            //$("#biddingStrategyType").children('option').hide();

            $("#biddingStrategyType").removeAttr('selected');
            $("#biddingStrategyType option").hide();

            //end re-arranging everything

            biddingStrategyArray=['MANUAL_CPC','MAXIMIZE_CONVERSION_VALUE'];

            // if(bidding_focus_on_val=="conversions"){
            //     biddingStrategyArray=['MANUAL_CPC','MAXIMIZE_CONVERSION_VALUE'];
            // }

            if(channel_type.val()=="MULTI_CHANNEL"){
                biddingStrategyArray=['TARGET_CPA'];
            }

            if(biddingStrategyArray.length>0){
                $(biddingStrategyArray).each(function(i,v){
                    $("#biddingStrategyType option[value=" + v + "]").show();
                });
            }

            $('#biddingStrategyType option:not([hidden]):eq(0)').prop('selected', true).change();

        }


        $("#biddingStrategyType").on('change',function(){
            var biddingStrategyTypeVal=$(this).val();
            $("#maindiv_for_target").hide();
            $("#div_html_append_1").hide();
            $("#target_cost_per_action").prop('checked',false);
            $("#div_roas").hide();
            $("#div_targetspend").hide();
            if(biddingStrategyTypeVal=="TARGET_CPA"){
                //append HTML into form
                /* var html='<div id="maindiv_for_target"><input type="checkbox" name="target_cost_per_action" id="target_cost_per_action" value="1"> Set a target cost per action\n\
                <div id="div_html_append_1" style="display:none;">\n\
                <label>Target CPA</lable> \n\
                <input type="text" name="txt_target_cpa" id="txt_target_cpa" value=""> \n\
                <label>Pay For</lable> \n\
                <select name="pay_for id="pay_for">\n\
                <option value="clicks">Clicks</option>\n\
                <option value="viewable_impressions">Viewable Impressions</option>\n\
                </select>\n\
                </div></div>';
                $("#biddingStrategyType_second_div").append(html); */
                $("#maindiv_for_target").css('display','block');
            }
            if(biddingStrategyTypeVal=="TARGET_ROAS" /*|| biddingStrategyTypeVal=="MAXIMIZE_CONVERSIONS"*/){
                $("#div_roas").show();
            }

            if(biddingStrategyTypeVal=="TARGET_SPEND"){
                $("#div_targetspend").show();
            }

        });

        //$(document).on("click", '#target_cost_per_action', function() {
        $("#target_cost_per_action").click(function(){
            if($("#target_cost_per_action").is(":checked")){
                $("#div_html_append_1").show();
            }else{
                $("#div_html_append_1").hide();
            }
        });

        $("#directiBiddingSelect").click(function(){
            $("#maindiv_for_target").hide();
            $("#div_html_append_1").hide();
            $("#target_cost_per_action").prop('checked',false);

            $("#div_roas").hide();
            $("#div_targetspend").hide();

            var bidding_focus_on_val=bidding_focus_on.val();
            if(bidding_focus_on_val=="conversions"){
                biddingStrategyArray=['TARGET_CPA','TARGET_ROAS','TARGET_SPEND','MAXIMIZE_CONVERSIONS','MANUAL_CPM','MANUAL_CPC','UNSPECIFIED'];
            }

            if(channel_type.val()=="MULTI_CHANNEL"){
                biddingStrategyArray=['TARGET_CPA'];
            }

            if(biddingStrategyArray.length>0){
                $(biddingStrategyArray).each(function(i,v){
                    $("#biddingStrategyType option[value=" + v + "]").show();
                });
            }

            $('#biddingStrategyType option:not([hidden]):eq(0)').prop('selected', true).change();
        });

        $("#resetBiddingSection").click(function(){
            $("#biddingStrategyType").removeAttr('selected');
            $("#biddingStrategyType option").hide();

            biddingStrategyArray=['MANUAL_CPC','MAXIMIZE_CONVERSION_VALUE'];

            if(channel_type.val()=="MULTI_CHANNEL"){
                biddingStrategyArray=['TARGET_CPA'];
            }

            if(biddingStrategyArray.length>0){
                $(biddingStrategyArray).each(function(i,v){
                    $("#biddingStrategyType option[value=" + v + "]").show();
                });
            }

            $('#biddingStrategyType option:not([hidden]):eq(0)').prop('selected', true).change();

        });

        $("#channel_type").on('change',function(){
            channelTypeChangeFunc();
            biddingFocusBaseStrategy();
        });

        function channelTypeChangeFunc(){

            console.log("channelTypeChangeFunc called");
            var channelSubTypeyArray=["UNSPECIFIED"];
            //start re-arranging everything
            var channel_type_val=channel_type.val();
            $("#channel_sub_type").removeAttr('selected');
            $("#channel_sub_type option").hide();
            $("#div_shipping_setting").hide();
            $("#div_app_setting").hide()
            $("#div-language-fields").show();
            $("#div-location-fields").show();
            //end re-arranging everything
            resetAdOptimization();
            if(channel_type_val=="SEARCH"){
                // channelSubTypeyArray.push('SEARCH_MOBILE_APP','SEARCH_EXPRESS');
            }
            if(channel_type_val=="DISPLAY"){
                // channelSubTypeyArray.push('DISPLAY_MOBILE_APP','DISPLAY_EXPRESS','DISPLAY_SMART_CAMPAIGN','DISPLAY_GMAIL_AD');
            }
            if(channel_type_val=="MULTI_CHANNEL"){
                channelSubTypeyArray = [];
                channelSubTypeyArray.push('APP_CAMPAIGN','APP_CAMPAIGN_FOR_ENGAGEMENT','APP_CAMPAIGN_FOR_PRE_REGISTRATION');
            }


            if(channel_type_val=="SHOPPING"){
                $("#div_shipping_setting").show();
                $("#div-language-fields").hide();
                $("#div-location-fields").hide();
            }

            if(channel_type_val=="MULTI_CHANNEL"){
                $("#div_app_setting").show();
            }

            if(channelSubTypeyArray.length>0){
                $(channelSubTypeyArray).each(function(i,v){
                    $("#channel_sub_type option[value=" + v + "]").show();
                });
            }

            $('#channel_sub_type option:not([hidden]):eq(0)').prop('selected', true).change();
            // $("#channel_sub_type").select2("destroy");
            // $("#channel_sub_type").select2();
        }

        function resetAdOptimization(){
            channelType=channel_type.val();
            channelSubType=channel_sub_type.val();
            if(channelSubType=="UNIVERSAL_APP_CAMPAIGN" || (channelType=="DISPLAY" && channelSubType=="DISPLAY_SMART_CAMPAIGN")){
                $("#ad_rotation3").hide();
                $("#ad_rotation_label3").hide();
            }else{
                $("#ad_rotation3").show();
                $("#ad_rotation_label3").show();
            }
        }


        $("#target_languages").multiselect({
            allSelectedText: 'All',
            includeSelectAllOption: true,
            selectAllName: 'all_target_languages',
            enableFiltering: true,
            includeFilterClearBtn: false
        });

        $("#target_languages").multiselect('selectAll', false);
        $("#target_languages").multiselect('updateButtonText');
    });
</script>

{{-- Start Target Locations --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{@$google_map_api_key}}&libraries=places"></script>

<script>
    function gm_authFailure() {
        toastr["error"]('Google maps failed to load!');
    }

    function initialize() {
      var input = document.getElementById('target_location_address');
      new google.maps.places.Autocomplete(input);
    }

    google.maps.event.addDomListener(window, 'load', initialize);

    $(document).ready(function() {
        $(document).on('change', '[name="target_location"]', function(event) {
            event.preventDefault();

            if($('[name="target_location"]:checked').val() == "other"){
                $('.other_location_div').show();
                $('.advance_type_location_div').show();
            }else{
                $('.other_location_div').hide();
                $('.advance_type_location_div').hide();
            }
        });

        $(document).on('change', '[name="target_location_type"]', function(event) {
            event.preventDefault();

            if($('[name="target_location_type"]:checked').val() == "location"){
                $('.advance_type_location_div').show();
                $('.advance_type_radius_div').hide();
            }else{
                $('.advance_type_location_div').hide();
                $('.advance_type_radius_div').show();
            }
        });

        $('[name="country_id"]').select2({
            ajax: {
                url: '{{ route('google-campaign-location.countries') }}',
                dataType: 'json',
                delay: 250, // wait 250 milliseconds before triggering the request
                data: function (params) {
                    var query = {
                        search: params.term,
                        page: params.page || 1
                    }
                    // Query parameters will be ?search=[term]&page=[page]
                    return query;
                },
                processResults: function (data) {
                    return {
                        results: data.result,
                        pagination: data.pagination
                    };
                },
            },
            cache: true,
            allowClear: true,
            placeholder: 'Select a country',
        });

        $('[name="state_id"]').select2({
            ajax: {
                url: '{{ route('google-campaign-location.states') }}',
                dataType: 'json',
                delay: 250, // wait 250 milliseconds before triggering the request
                data: function (params) {
                    var query = {
                        search: params.term,
                        country_id: $('[name="country_id"]').select2().find(":selected").val(),
                        page: params.page || 1
                    }
                    // Query parameters will be ?search=[term]&page=[page]
                    return query;
                },
                processResults: function (data) {
                    return {
                        results: data.result,
                        pagination: data.pagination
                    };
                },
            },
            cache: true,
            placeholder: 'Select a state',
        });

        $('[name="city_id"]').select2({
            ajax: {
                url: '{{ route('google-campaign-location.cities') }}',
                dataType: 'json',
                delay: 250, // wait 250 milliseconds before triggering the request
                data: function (params) {
                    var query = {
                        search: params.term,
                        state_id: $('[name="state_id"]').select2().find(":selected").val(),
                        page: params.page || 1
                    }
                    // Query parameters will be ?search=[term]&page=[page]
                    return query;
                },
                processResults: function (data) {
                    return {
                        results: data.result,
                        pagination: data.pagination
                    };
                },
            },
            cache: true,
            placeholder: 'Select a city',
        });

        /*$('[name="target_location_address"]').select2({
            // dropdownParent: $("#create-compaign"),
            ajax: {
                url: '{{ route('google-campaign-location.address') }}',
                dataType: 'json',
                delay: 250, // wait 250 milliseconds before triggering the request
                data: function (params) {
                    var query = {
                        search: params.term,
                        account_id: {{ $account_id }},
                    }
                    // Query parameters will be ?search=[term]&page=[page]
                    return query;
                },
                processResults: function (data) {
                    return {
                        results: data.result
                    };
                },
            },
            cache: true,
            minimumInputLength: 3,
            placeholder: 'Enter a place name, address or coordinates',
        });*/
    });
</script>
{{-- End Target Locations --}}
{{--@endsection--}}



