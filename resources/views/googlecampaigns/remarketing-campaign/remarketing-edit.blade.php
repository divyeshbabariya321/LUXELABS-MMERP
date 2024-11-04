<style>
    .campaign.card {
        /*max-width: 1200px;*/
        /*margin: 20px auto;*/
    }

    .create-remarketing-campaign-form {
        padding: 10px;
    }

    label.col-form-label {
        font-weight: 100 !important;
    }

    label.col-form-label {
        /*min-width:200px;*/
        padding-top: 0;
    }

    .lagend {
        padding: 0 5px;
        font-weight: bold !important;
    }

    .create-compaigncreate-remarketing-campaign-form h3, .create-remarketing-campaign-form h2 {
        font-size: 16px !important;
        font-weight: 700 !important;
    }

    select.globalSelect2 + span.select2, .create-remarketing-campaign-form input[type="text"] {
        min-width: 100% !important;
    }

    #main_div_campaign_url_options div label {
        font-weight: 100 !important;
        /*min-width: 120px;*/
    }

    input {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        height: 34px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
        border-radius: 4px;
    }

    #main_div_campaign_url_options div input {
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
        border-bottom: none !important;
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

    #target_cost_per_action {
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
<div class="campaign-card">
    <form method="POST" action="/google-remarketing-campaigns/update" class="create-remarketing-campaign-form"
          enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="campaignId">

        <div class="row m-0 mb-3">
            <div class="col-md-6">
                <div class="form-group m-0 top">
                    <label for="campaign-name" class="col-form-label">Campaign name</label><br>
                    <div class="form-input">
                        <input type="text" class="form-control" id="campaign-name" name="campaignName"
                               placeholder="Campaign name">
                        @if ($errors->has('campaignName'))
                            <span class="text-danger">{{$errors->first('campaignName')}}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group m-0 mb-3">
                    <label for="budget-amount" class=" col-form-label">Budget amount ($)</label>
                    <div>
                        <input type="text" class="form-control" id="budget-amount" name="budgetAmount"
                               placeholder="Budget amount ($)">
                        @if ($errors->has('budgetAmount'))
                            <span class="text-danger">{{$errors->first('budgetAmount')}}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group m-0 mb-3">
                    <label for="start-date" class="col-form-label">Start Date</label>
                    <div>
                        <input type="date" class="form-control" id="start-date" name="start_date"
                               placeholder="Start Date E.g {{date('Ymd', strtotime('+1 day'))}}">
                        @if ($errors->has('start_date'))
                            <span class="text-danger">{{$errors->first('start_date')}}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group m-0 mb-3">
                    <label for="start-date" class="col-form-label">End Date</label>
                    <div>
                        <input type="date" class="form-control" id="end-date" name="end_date"
                               placeholder="End Date E.g {{date('Ymd', strtotime('+1 month'))}}">
                        @if ($errors->has('end_date'))
                            <span class="text-danger">{{$errors->first('end_date')}}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group m-0 mb-5">
                    <label for="campaign-status" class="col-form-label">Campaign status</label>
                    <div>
                        <select class="browser-default custom-select globalSelect2" id="campaign-status"
                                name="campaignStatus" style="height: auto">
                            <option value="1" selected>Enabled</option>
                            <option value="2">Paused</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" style="position: absolute;right: 100px;bottom: 17px;"
                class="btn btn-primary mb-2 ">Update
        </button>
    </form>
</div>


<script>
    $(document).on('change', '#update-remarketing-campaign [name="start_date"]' ,function () {
        $('#update-remarketing-campaign [name="end_date"]').val('');
        $('#update-remarketing-campaign [name="end_date"]')[0].min = this.value;
    })
</script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
