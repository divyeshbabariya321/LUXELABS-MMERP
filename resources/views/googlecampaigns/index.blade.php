@extends('layouts.app')
@section('favicon' , 'task.png')

@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">

<style type="text/css">
    .status-selection .btn-group {
        padding: 0;
        width: 100%;
    }

    .status-selection .multiselect {
        width: 100%;
    }

    #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
    #create-compaign .modal-dialog, #create-remarketing-campaign .modal-dialog, #update-remarketing-campaign .modal-dialog {
        max-width: 1024px !important;
        width: 1024px !important;
    }
    .btn-secondary, .btn-secondary:focus, .btn-secondary:hover{
        background: #fff;
        color: #757575;
        border: 1px solid #ddd;
        height: 32px;
        border-radius: 4px;
        padding: 5px 10px;
        font-size: 14px;
        font-weight: 100;
        line-height: 10px;
    }

</style>
@endsection

@section('content')
    <h2 class="page-heading">Google AdWords - Campaigns ( <span id="ads_campaign_count">{{$totalNumEntries}} </span>)

        <a class="btn-image float-right custom-button" href="{{ route('googleadsaccount.index') }}">Back to Account</a>
    </h2>
    <div class="container-fluid p-0" style="margin-top: 10px">

    <div>
        <div class="form-group" style="margin-bottom: 10px;">
            <div class="row m-0">

                <div class="col-md-1 pl-3">
                    <input name="googlecampaign_id" type="text" class="form-control" value="{{ isset($googlecampaign_id) ? $googlecampaign_id : '' }}" placeholder="Campaign Id" id="googlecampaign_id">
                </div>

                <div class="col-md-2 pl-0">
                    <input name="googlecampaign_name" type="text" class="form-control" value="{{ isset($googlecampaign_name) ? $googlecampaign_name : '' }}" placeholder="Campaign Name" id="googlecampaign_name">
                </div>

                <div class="col-md-1 pl-0">
                    <input name="googlecampaign_budget" type="text" class="form-control" value="{{ isset($googlecampaign_budget) ? $googlecampaign_budget : '' }}" placeholder="Budget" id="googlecampaign_budget">
                </div>

                <div class="col-md-1 pl-0">
                    <input name="start_date" type="text" class="form-control" value="{{ isset($start_date) ? $start_date : '' }}" placeholder="Start Date" id="start_date">
                </div>

                <div class="col-md-1 pl-0">
                    <input name="end_date" type="text" class="form-control" value="{{ isset($end_date) ? $end_date : '' }}" placeholder="End Date" id="end_date">
                </div>

                <div class="col-md-2 pl-0">
                    <input name="budget_uniq_id" type="text" class="form-control" value="{{ isset($budget_uniq_id) ? $budget_uniq_id : '' }}" placeholder="Budget Uniq Id" id="budget_uniq_id">
                </div>

                <div class="col-md-1 pl-0">
                    <select class="browser-default custom-select" id="campaign_status" name="campaign_status" style="height: auto">
                    <option value="">--Status--</option>
                    <option value="ENABLED">Enabled</option>
                    <option value="PAUSED">Paused</option>
                </select>

                </div>

                <div class="col-md-1 pl-0">
                    <button type="button" class="btn btn-image" onclick="submitSearch()"><img src="/images/filter.png" /></button>
                    <button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img src="/images/resend2.png" /></button>
                </div>

                <div class="col-md-12">
                    <div class="col-md-3 pl-0 float-right">
                        <form method="get" data-toggle="modal" data-target="#create-remarketing-campaign">
                            <input type="hidden" value="<?php echo $_GET['account_id']; ?>" id="accountID" name="account_id"/>
                            <button type="button" class="float-right mb-3 btn-secondary">New Remarketing Campaign</button>
                        </form>
                    </div>
                    <div class="col-md-2 pl-0 float-right">
                        <form method="get" data-toggle="modal" data-target="#create-compaign" {{--action="/google-campaigns/create"--}}>
                            <input type="hidden" value="<?php echo $_GET['account_id']; ?>" id="accountID" name="account_id"/>
                            <button type="button" class="float-right mb-3 btn-secondary">New Campaign</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>


      <div class="pl-3 pr-3">
          <div class="table-responsive mt-3">

              <table class="table table-bordered" id="adscampaign-table">
                  <thead>
                  <tr>
                      <th>#ID</th>
                      <th>Google Campaign Id</th>
                      <th>Campaign Name</th>
                      <th>Budget</th>
                      <th>Start Date</th>
                      <th>End Date</th>
                      <th>Budget Uniq Id</th>
                      <th>Status</th>
                      <th>Created At</th>
                      <th>Actions</th>
                  </tr>
                  </thead>

                  <tbody>
                  @foreach($campaigns as $campaign)
                      <tr>
                          <td>{{$campaign->id}}</td>
                          <td>{{$campaign->google_campaign_id}}</td>
                          <td>{{$campaign->campaign_name}}</td>
                          <td>{{$campaign->budget_amount}}</td>
                          <td>{{$campaign->start_date}}</td>
                          <td>{{$campaign->end_date}}</td>
                          <td>{{$campaign->budget_uniq_id}}</td>
                          <td>{{$campaign->status}}</td>
                          <td>{{$campaign->created_at}}</td>
                          <td>
                              <form method="GET" action="/google-campaigns/{{$campaign['google_campaign_id']}}/adgroups">
                                  <button type="submit" class="btn btn-sm btn-link">Ad Groups</button>
                              </form>
                              @if ($campaign->type != 'remarketing' && $campaign->channel_type !== 'SHOPPING')
                                  <form method="GET" action="/google-campaigns/{{$campaign['google_campaign_id']}}/google-campaign-location">
                                      <button type="submit" class="btn btn-sm btn-link">Location</button>
                                  </form>
                              @endif
                              {{ html()->form('DELETE', route('googlecampaigns.deleteCampaign', [$campaign['google_campaign_id']]))->style('display:inline')->open() }}
                              <input type="hidden" id="delete_account_id" name="delete_account_id" value='{{$campaign->account_id}}'/>
                              <button type="submit" class="btn-image"><img src="{{asset('/images/delete.png')}}"></button>
                              {{ html()->form()->close() }}
                              @if($campaign->type == 'remarketing')
                                  <button type="button" onclick="editRemarketing('{{$campaign->google_campaign_id}}')" class="btn-image" data-toggle="modal" data-target="#update-remarketing-campaign"><img src="{{asset('/images/edit.png')}}"></button>
                              @else
                                  <button type="button" onclick="editDetails('{{$campaign->google_campaign_id}}')" class="btn-image" data-toggle="modal" data-target="#updateCampaignModal"><img src="{{asset('/images/edit.png')}}"></button>
                              @endif
                          </td>
                      </tr>
                  @endforeach
                  </tbody>
              </table>
          </div>
      </div>
        {{ $campaigns->links() }}

    </div>

    <div class="modal" id="create-remarketing-campaign" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 style="font-size: 20px" class="modal-title">Create Remarketing Campaign</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pt-0">
                    @include('googlecampaigns.remarketing-campaign.remarketing-create')
                </div>
                <div class="modal-footer" style="padding: 0;border-top:none;">
                    <button style="position: absolute;bottom: 22px;right: 26px" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="update-remarketing-campaign" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 style="font-size: 20px" class="modal-title">Update Remarketing Campaign</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pt-0">
                    @include('googlecampaigns.remarketing-campaign.remarketing-edit')
                </div>
                <div class="modal-footer" style="padding: 0;border-top:none;">
                    <button style="position: absolute;bottom: 22px;right: 26px" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="create-compaign" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 style="font-size: 20px" class="modal-title">Create Campaign</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pt-0">
{{--                    {!! view('googlecampaigns.create')  !!}--}}
                    @include('googlecampaigns.create')
                </div>
                <div class="modal-footer" style="padding: 0;border-top:none;">
{{--                    <button type="button" class="btn btn-primary">Save changes</button>--}}
                    <button style="position: absolute;bottom: 22px;right: 26px" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateCampaignModal" role="dialog" style="z-index: 3000;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="container">
                    <div class="page-header" style="width: 69%">
                        <h2>Update Campaign</h2>
                    </div>
                    <form method="POST" action="/google-campaigns/update" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="campaignId">
                        <input type="hidden" name="channel_type" id="channel_type">
                        <div class="form-group row">
                            <label for="campaign-name" class="col-sm-2 col-form-label">Campaign name</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="campaign-name" name="campaignName" placeholder="Campaign name">
                                @if ($errors->has('campaignName'))
                                    <span class="text-danger">{{$errors->first('campaignName')}}</span>
                                @endif
                            </div>
                        </div>


                        <h3>Bidding</h3> <!-- biddingstrategy -->
                        <div class="form-group row">
                            <label for="campaign-status" class="col-sm-2 col-form-label">what do you want to focus on?</label>
                            <div class="col-sm-3">
                                <select class="browser-default custom-select" id="bidding_focus_on" name="bidding_focus_on" style="height: auto">
                                    <option value="conversions" selected>Conversions</option>
                                    <option value="conversions_value">Conversions value</option>
                                    <option value="viewable_impressions">Viewable impressions</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="campaign-status" class="col-sm-2 col-form-label">Bidding Strategy</label>
                            <div class="col-sm-3" id="biddingStrategyType_second_div">
                                <select class="browser-default custom-select" id="biddingStrategyType" name="biddingStrategyType" style="height: auto">
                                    <option value="">Select bidding strategy</option>
                                    @foreach($biddingStrategyTypes as $bskey=>$bs)
                                        <option value="{{$bskey}}">{{$bs}}</option>
                                    @endforeach
                                </select>

                                <div id="edit_maindiv_for_target" style="display:none;"><input type="checkbox" name="target_cost_per_action" id="edit_target_cost_per_action" value="1"> Set a target cost per action
                                    <div id="edit_div_html_append_1" style="display:none;">
                                        <label>Target CPA</lable>
                                            <input type="text" name="txt_target_cpa" id="edit_txt_target_cpa" value="">
                                            <!-- <label>Pay For</lable>
                                            <select name="pay_for" id="pay_for">
                                            <option value="clicks">Clicks</option>
                                            <option value="viewable_impressions">Viewable Impressions</option>
                                            </select> -->
                                    </div></div>
                                <div id="edit_div_roas" style="display:none; margin-top:20px;">
                                    <label>Target ROAS (This field must be between 0.01 and 1000.0, inclusive)</lable>
                                        <input type="text" name="txt_target_roas" id="edit_txt_target_roas" value="0.01"> %
                                </div>
                                <div id="edit_div_targetspend" style="display:none; margin-top:20px;">
                                    <label>Maximize clicks (This field must be greater than or equal to 0)</lable>
                                        <input type="text" name="txt_maximize_clicks" id="edit_txt_maximize_clicks" value="0">
                                </div>
                                <br><br>
                                <a href="javascript:void(0);" class="btn btn-link" id="edit_directiBiddingSelect">Or, select a bid strategy directly (not recommended)</a>

                                <a href="javascript:void(0);" class="btn btn-link" id="edit_resetBiddingSection">Reset</a>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="budget-amount" class="col-sm-2 col-form-label">Budget amount</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="budget-amount" name="budgetAmount" placeholder="Budget amount">
                                @if ($errors->has('budgetAmount'))
                                    <span class="text-danger">{{$errors->first('budgetAmount')}}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="start-date" class="col-sm-2 col-form-label">Start Date</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="start-date" name="start_date" placeholder="Start Date E.g {{date('Ymd', strtotime('+1 day'))}}" >
                                @if ($errors->has('start_date'))
                                    <span class="text-danger">{{$errors->first('start_date')}}</span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="start-date" class="col-sm-2 col-form-label">End Date</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="end-date" name="end_date" placeholder="End Date E.g {{date('Ymd', strtotime('+1 month'))}}" >
                                @if ($errors->has('end_date'))
                                    <span class="text-danger">{{$errors->first('end_date')}}</span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="start-date" class="col-sm-2 col-form-label">Target Languages</label>
                            <div class="col-sm-6 status-selection">
                                <select class="form-control multiselect" id="edit_target_languages" name="target_languages[]" style="height: auto" multiple>
                                    @foreach(\App\Models\GoogleLanguageConstant::whereIsTargetable(true)->orderBy('name', 'ASC')->get() as $lang)
                                        <option value="{{ $lang->google_language_constant_id }}">{{ $lang->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="campaign-status" class="col-sm-2 col-form-label">Campaign status</label>
                            <div class="col-sm-6">
                                <select class="browser-default custom-select" id="campaign-status" name="campaignStatus" style="height: auto">
                                    <option value="1">Enabled</option>
                                    <option value="2">Paused</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-8">
                                <button type="button" class="btn btn-secondary float-right ml-2" data-dismiss="modal" aria-label="Close">Close</button>
                                <button type="submit" class="btn btn-primary mb-2 float-right">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>

<script type="text/javascript">
    $('.select-multiple').select2({width: '100%'});

    function submitSearch(){
        src = '/google-campaigns?account_id=<?php echo $_GET['account_id']; ?>';
        googlecampaign_id = $('#googlecampaign_id').val();
        googlecampaign_name = $('#googlecampaign_name').val();
        googlecampaign_budget = $('#googlecampaign_budget').val();
        start_date = $('#start_date').val();
        end_date = $('#end_date').val();
        budget_uniq_id = $('#budget_uniq_id').val();
        campaign_status = $('#campaign_status').val();

        $.ajax({
            url: src,
            dataType: "json",
            data: {
                googlecampaign_id : googlecampaign_id,
                googlecampaign_name :googlecampaign_name,
                googlecampaign_budget :googlecampaign_budget,
                start_date :start_date,
                end_date :end_date,
                budget_uniq_id :budget_uniq_id,
                campaign_status :campaign_status,
            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $("#adscampaign-table tbody").empty().html(data.tbody);
            $("#ads_campaign_count").text(data.count);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });

    }

    function resetSearch(){
        src = '/google-campaigns?account_id=<?php echo $_GET['account_id']; ?>';
        blank = ''
        $.ajax({
            url: src,
            dataType: "json",
            data: {

               blank : blank,

            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $('#googlecampaign_id').val('');
            $('#googlecampaign_name').val('');
            $('#googlecampaign_budget').val('');
            $('#start_date').val('');
            $('#end_date').val('');
            $('#budget_uniq_id').val('');
            $('#campaign_status').val('');

            $("#adscampaign-table tbody").empty().html(data.tbody);
            $("#ads_campaign_count").text(data.count);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
    }

    function editRemarketing(id) {
        let url = "{{ route('googlecampaigns.updatePage', [":id"]) }}";
        url = url.replace(':id', id);
        $.ajax({
            method: "GET",
            url: url,
            dataType: "json",
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            success: function (data) {
                $('#update-remarketing-campaign [name="campaignId"]').val(data.google_campaign_id);
                $('#update-remarketing-campaign [name="campaignName"]').val(data.campaign_name);
                $('#update-remarketing-campaign [name="budgetAmount"]').val(data.budget_amount);
                $('#update-remarketing-campaign [name="start_date"]')[0].min = data.start_date;
                $('#update-remarketing-campaign [name="end_date"]')[0].min = data.start_date;
                $('#update-remarketing-campaign [name="start_date"]').val(data.start_date);
                $('#update-remarketing-campaign [name="end_date"]').val(data.end_date);
                if (data.status == "ENABLED") {
                    $('#update-remarketing-campaign [name="campaignStatus"] option:eq(0)').prop('selected', true).change();
                } else {
                    $('#update-remarketing-campaign [name="campaignStatus"] option:eq(1)').prop('selected', true).change();
                }
                $('#update-remarketing-campaign').show();
            }
        });
    }
    // Start Update Model
    function editDetails(id) {
        $('#updateCampaignModal').hide();

        $('#edit_target_languages').multiselect('deselectAll', false);
        $('#edit_target_languages').multiselect('updateButtonText');

        var url = "{{ route('googlecampaigns.updatePage', [":id"]) }}";
        url = url.replace(':id', id);
        $.ajax({
            method: "GET",
            url: url,
            dataType: "json",
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            success: function (data) {
                // alert($('#updateCampaignModal [name="adGroupStatus"] option:eq(1)').text());
                $('#updateCampaignModal [name="campaignId"]').val(data.google_campaign_id);
                $('#updateCampaignModal [name="channel_type"]').val(data.channel_type);
                $('#updateCampaignModal [name="campaignName"]').val(data.campaign_name);
                $('#updateCampaignModal [name="budgetAmount"]').val(data.budget_amount);
                $('#updateCampaignModal [name="start_date"]').val(data.start_date);
                $('#updateCampaignModal [name="end_date"]').val(data.end_date);

                $('#updateCampaignModal [name="txt_target_cpa"]').val(data.target_cpa_value);
                $('#updateCampaignModal [name="txt_target_roas"]').val(data.target_roas_value);
                $('#updateCampaignModal [name="maximize_clicks"]').val(data.maximize_clicks);

                if(data.status == "ENABLED"){
                    $('#updateCampaignModal [name="campaignStatus"] option:eq(0)').prop('selected', true).change();
                }else{
                    $('#updateCampaignModal [name="campaignStatus"] option:eq(1)').prop('selected', true).change();
                }

                $("#edit_maindiv_for_target").hide();
                $("#edit_div_html_append_1").hide();
                $("#edit_target_cost_per_action").prop('checked',false);

                $("#edit_div_roas").hide();
                $("#edit_div_targetspend").hide();

                biddingFocusBaseStrategyEdit();

                $('#updateCampaignModal [name="biddingStrategyType"] option[value="'+ data.bidding_strategy_type+'"]').prop('selected', true).change();

                if(data.bidding_strategy_type == "TARGET_CPA"){
                    $("#edit_target_cost_per_action").prop('checked', true);
                    $("#edit_maindiv_for_target").show();
                    $("#edit_div_html_append_1").show();
                }
                if(data.bidding_strategy_type == "TARGET_ROAS" /*|| biddingStrategyTypeVal=="MAXIMIZE_CONVERSIONS"*/){
                    $("#edit_div_roas").show();
                }

                if(data.bidding_strategy_type == "TARGET_SPEND"){
                    $("#edit_div_targetspend").show();
                }

                if(data.target_languages.length){
                    $.each(data.target_languages, function(index, val) {
                        $("#edit_target_languages").find('option[value="'+val.google_language_constant_id+'"]').prop('selected', true);
                    });
                    $("#edit_target_languages").multiselect('refresh');
                }else{
                    $("#edit_target_languages").multiselect('selectAll', false);
                    $("#edit_target_languages").multiselect('updateButtonText');
                }

                $('#updateCampaignModal').show();
            }
        });
    }

    function biddingFocusBaseStrategyEdit(){
        /* var biddingStrategyArray= '<?php //echo json_encode(array_keys($biddingStrategyTypes)); ?>';
        biddingStrategyArray=JSON.parse(biddingStrategyArray); */
        var biddingStrategyArray=[];
        //start re-arranging everything
        var bidding_focus_on_val=$("#updateCampaignModal [name='bidding_focus_on']").val();
        //$("#updateCampaignModal [name='biddingStrategyType']").children('option').hide();

        $("#updateCampaignModal [name='biddingStrategyType']").removeAttr('selected');
        $("#updateCampaignModal [name='biddingStrategyType'] option").hide();

        //end re-arranging everything

        // if(bidding_focus_on_val=="conversions"){
            biddingStrategyArray=['MANUAL_CPC','MAXIMIZE_CONVERSION_VALUE'];
        // }

        if($("#updateCampaignModal [name='channel_type']").val()=="MULTI_CHANNEL"){
            biddingStrategyArray=['TARGET_CPA'];
        }

        if(biddingStrategyArray.length>0){
            $(biddingStrategyArray).each(function(i,v){
                $("#updateCampaignModal [name='biddingStrategyType'] option[value=" + v + "]").show();
            });
        }

       $('#updateCampaignModal [name="biddingStrategyType"] option:not([hidden]):eq(0)').prop('selected', true).change();

    }

    $("#updateCampaignModal [name='biddingStrategyType']").on('change',function(){
        var biddingStrategyTypeVal=$(this).val();
        $("#edit_maindiv_for_target").hide();
        $("#edit_div_html_append_1").hide();
        $("#edit_target_cost_per_action").prop('checked',false);
        $("#edit_div_roas").hide();
        $("#edit_div_targetspend").hide();
        if(biddingStrategyTypeVal=="TARGET_CPA"){
            $("#edit_maindiv_for_target").css('display','block');
        }
        if(biddingStrategyTypeVal=="TARGET_ROAS" /*|| biddingStrategyTypeVal=="MAXIMIZE_CONVERSIONS"*/){
            $("#edit_div_roas").show();
        }

        if(biddingStrategyTypeVal=="TARGET_SPEND"){
            $("#edit_div_targetspend").show();
        }
    });

    $("#edit_target_cost_per_action").click(function(){
        if($("#edit_target_cost_per_action").is(":checked")){
            $("#edit_div_html_append_1").show();
        }else{
            $("#edit_div_html_append_1").hide();
        }
    });

    $("#edit_directiBiddingSelect").click(function(){
        $("#edit_maindiv_for_target").hide();
        $("#edit_div_html_append_1").hide();
        $("#edit_target_cost_per_action").prop('checked',false);

        $("#edit_div_roas").hide();
        $("#edit_div_targetspend").hide();

        var bidding_focus_on_val=$("#updateCampaignModal [name='bidding_focus_on']").val();
        if(bidding_focus_on_val=="conversions"){
            var biddingStrategyArray=['TARGET_CPA','TARGET_ROAS','TARGET_SPEND','MAXIMIZE_CONVERSIONS','MANUAL_CPM','MANUAL_CPC','UNSPECIFIED'];
        }

        if($("#updateCampaignModal [name='channel_type']").val()=="MULTI_CHANNEL"){
            var biddingStrategyArray=['TARGET_CPA'];
        }

        if(biddingStrategyArray.length>0){
            $(biddingStrategyArray).each(function(i,v){
                $("#updateCampaignModal [name='biddingStrategyType'] option[value=" + v + "]").show();
            });
        }
        $('#updateCampaignModal [name="biddingStrategyType"] option:not([hidden]):eq(0)').prop('selected', true).change();
    });

    $("#edit_resetBiddingSection").click(function(){
        $("#updateCampaignModal [name='biddingStrategyType']").removeAttr('selected');
        $("#updateCampaignModal [name='biddingStrategyType'] option").hide();

        var biddingStrategyArray=['MANUAL_CPC','MAXIMIZE_CONVERSION_VALUE'];

        if($("#updateCampaignModal [name='channel_type']").val()=="MULTI_CHANNEL"){
            biddingStrategyArray=['TARGET_CPA'];
        }

        if(biddingStrategyArray.length>0){
            $(biddingStrategyArray).each(function(i,v){
                $("#updateCampaignModal [name='biddingStrategyType'] option[value=" + v + "]").show();
            });
        }
        $('#updateCampaignModal [name="biddingStrategyType"] option:not([hidden]):eq(0)').prop('selected', true).change();
    });

    $("#edit_target_languages").multiselect({
        allSelectedText: 'All',
        includeSelectAllOption: true,
        selectAllName: 'all_target_languages',
        enableFiltering: true,
        includeFilterClearBtn: false
    });

    // End Update Model
</script>

@endsection
