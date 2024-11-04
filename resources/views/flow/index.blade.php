@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="{{ mix('webpack-dist/flow/style.css') }} ">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }

        #Collector {
            list-style-type: none;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        #Collector cross {
            margin: 0 3px 3px 3px;
            padding: 0.4em;
            padding-left: 1.5em;
            font-size: 1.4em;
            height: 18px;
        }

        #Collector li span {
            position: absolute;
            margin-left: -1.3em;
        }

    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Flows</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="mt-3 col-md-12">
            <a class="ml-2">
                <button type="button" data-toggle="modal" data-target="#flowModal"
                    class="btn btn-secondary btn-xs">Flow</button>
            </a>
            <a class="ml-2" style="display:none;">
                <button type="button" data-toggle="modal" data-target="#flowTypeModal" class="btn btn-secondary btn-xs">Flow
                    Type</button>
            </a>
        </div>
    </div>
    <div class="row mb-3">
        <div class="mt-3 col-md-12">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th scope="col" class="text-center">Website</th>
                        <th scope="col" class="text-center">Flow Name</th>
                        <th scope="col" class="text-center">Description</th>
                        <th scope="col" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-center task_queue_list">
                    @foreach ($flows as $key => $flow)
                        <tr class="worker_row_{{ $flow->id }}">
                            <td>{{ $flow['title'] }}</td>
                            <td>{{ $flow['flow_name'] }}</td>
                            <td>{{ $flow['flow_description'] }}</td>
                            <td>
                                <a href="#" onclick="showFlow('{{ $flow->id }}')"> Flow Detail</a>
                                <i style="cursor: pointer;" class="fa fa-trash trigger-delete"
                                    data-route="{{ route('flow-delete') }}" data-id="{{ $flow->id }}"
                                    aria-hidden="true"></i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="flowModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
                <div class="modal-header p-0 pt-3 pb-3 pl-3 pr-3">
                    <h5 class="modal-title">Flow</h5>
                    <button type="button" class="close pt-0 pb-0" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {{ html()->form('POST', route('flow-create'))->id('flow-create')->class('ajax-submit w-100')->open() }}
                        <div class="col-md-3">
                            {{ html()->select('store_website_id', $websites)->class('form-control')->placeholder('Website') }}
                        </div>
                        <div class="col-md-3">
                            {{ html()->select('flow_name', ['add_to_cart' => 'Add to cart', 'wishlist' => 'Wish List', 'newsletters' => 'Newsletters', 'customer_post_purchase' => 'Customer post purchase', 'customer_win_back' => 'Customer Win Back', 'attach_images_for_product' => 'Attach images for product', 'dispatch_send_price' => 'Dispatch send price', 'new_erp_lead' => 'New Erp Lead', 'out_of_stock_subscribe' => 'Out of stock subscribe', 'payment_failed' => 'Payment Failed', 'order_reviews' => 'Order reviews', 'task_pr' => 'Check if PR is merged', 'site_dev' => 'Site Development', 'order received' => 'Order Received', 'product shipped to client' => 'Product Shipped To Client', 'delivered' => 'Delivered', 'cancel' => 'Canceled', 'Refund to be processed' => 'Refund to be processed', 'Refund Dispatched' => 'Refund Dispatched', 'Refund Credited' => 'Refund Credited'])->class('form-control')->placeholder('Flow Name') }}
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control " name="flow_description"
                                placeholder="Flow Description" />
                        </div>
                        <div class="col-md-2">
                            <input type="hidden" name="id" value="">
                            <button type="submit" class="btn btn-secondary btn-xs pull-right mt-2">Create</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="flowTypeModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title">Flow Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ html()->form('POST', route('flow-type-create'))->id('flow-create')->class('ajax-submit')->open() }}
                    <div class="row">
                        <div class="col-md-9">
                            <label>Flow Type</label>
                            <input type="text" class="form-control " name="type" placeholder="Flow Description" />
                        </div>
                        <div class="col-md-3">
                            <input type="hidden" name="id" value="">
                            <button type="submit" class="btn btn-secondary">Create</button>
                        </div>
                    </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title">Set Email Message</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="">
                    {{ html()->form('POST', route('flow-action-message'))->class('ajax-submit')->attribute('novalidate', 'true')->id('message_content_form')->open() }}
                    <div id="message_content">
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="flowPathModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title">Flow Type</h5>
                    <a href="#" id="sendEmail"> Add Email </a>
                    <a href="#" id="dateTime"> Add Time Delay </a>
                    <a href="#" id="whatsapp"> &nbsp;Add Whatsapp </a>
                    <a href="#" id="sms"> &nbsp;Add SMS</a>
                    <a href="#" id="condition"> &nbsp;Add Condition</a>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ html()->form('POST', route('flow-actions-update'))->class('ajax-submit')->open() }}
                    <div class="row" id='Collector'>

                    </div>
                    <button type="submit" class="btn btn-secondary mt-3">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="time_delay" style="display:none;">
        <div class="col-md-12 cross cross_first border-bottom bg-light text-dark pt-3 pb-3  m-0">
            <div class="form-group row m-0">
                <label class="col-lg-2 col-form-label">Time Delay</label>
                <div class="col-lg-2">
                    {{ html()->number('time_delay')->class('form-control')->attribute('required', ) }}
                    <input type="hidden" name="action_type[]" value="Time Delay">
                </div>
                <label class="col-lg-3 col-form-label">Time Delay Type</label>
                <div class="col-lg-2">
                    {{ html()->select('time_delay_type', ['days' => 'Days', 'hours' => 'Hours', 'minutes' => 'Minutes'])->class('form-control') }}
                </div>
                <div class="col-lg-3 text-right pt-3">
                    <i class="fa fa-trash fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    <div id="send_message" style="display:none;">
        <div class="col-md-12 cross cross_sec border-bottom bg-white text-dark pt-3 pb-3  m-0">
            <div class="col-md-10  text-dark">
                <input type="hidden" name="action_type" value="Send Message">
                <label> <i class="fa fa-envelope"></i> Here will be Email <a
                        href="{{ url('link_template') }}"></a></label>
                <div class="cross_sub_label">
                    <label> <i class="fa fa-envelope"></i> Email #1 Subject <a
                            href="{{ url('link_template') }}"></a></label>
                </div>
            </div>
            <div class="col-md-2 cross_sec_remove pt-3 text-right">
                <i class="fa fa-pencil-square-o fa-lg p-0" aria-hidden="true"></i>
                <i class="fa fa-remove fa-lg"></i>
            </div>
        </div>
    </div>
    <div id="whatsapp" style="display:none;">
        <div class="col-md-12 cross cross_first border-bottom bg-light text-dark pt-3 pb-3  m-0">
            <div class="form-group row m-0">
                <label class="col-lg-3 col-form-label">Whatsapp Message</label>
                <div class="col-lg-8">
                    {{ html()->text('message_title')->class('form-control')->required() }}
                    <input type="hidden" name="action_type[]" value="Whatsapp">
                </div>
                <div class="col-lg-1 text-right pt-3">
                    <i class="fa fa-trash fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    <div id="sms" style="display:none;">
        <div class="col-md-12 cross cross_first border-bottom bg-light text-dark pt-3 pb-3  m-0">
            <div class="form-group row m-0">
                <label class="col-lg-3 col-form-label">SMS</label>
                <div class="col-lg-8">
                    {{ html()->text('message_title')->class('form-control')->required() }}
                    <input type="hidden" name="action_type[]" value="SMS">
                </div>
                <div class="col-lg-1 text-right pt-3">
                    <i class="fa fa-trash fa-lg"></i>
                </div>
            </div>
            <div class="col-md-11 cross_first_label_time">
                <label></label>

            </div>
            <div class="col-md-1 cross_first_remove pt-3 text-right">

            </div>
        </div>
    </div>

    <div class="modal fade" id="condition_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title">Select where would you like to append</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-6 cross_first_label_time">
                        <label>Under Yes</label>
                        <input type="radio" name="path_for" class="yes_no" value="yes">
                        <div class="col-md-12" class="yes_conditions" id="yes_conditions" style="display:none;">
                            <div class="col-md-6 cross_first_label_time">
                                <label>Under Yes</label>
                                <input type="radio" name="path_for_yes" class="yes_yes_no" value="yes">
                            </div>
                            <div class="col-md-6 cross_first_label_time">
                                <label>Under No</label>
                                <input type="radio" name="path_for_yes" class="yes_yes_no" value="no">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 cross_first_label_time">
                        <label>Under No</label>
                        <input type="radio" name="path_for" class="yes_no" value="no">
                        <div class="col-md-12" class="no_conditions" id="no_conditions" style="display:none;">
                            <div class="col-md-6 cross_first_label_time">
                                <label>Under Yes</label>
                                <input type="radio" name="path_for_no" class="no_yes_no" value="yes">
                            </div>
                            <div class="col-md-6 cross_first_label_time">
                                <label>Under No</label>
                                <input type="radio" name="path_for_no" class="no_yes_no" value="no">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.tiny.cloud/1/{{config('settings.tiny_mce_api_key')}}/tinymce/5/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#html_content'
            //menubar: false
        });
    </script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        jQuery(document).ready(function($) {


            $('#dateTime').on('click', function() {
                //var parent_div = getParentDivId();
                var time = $('#time_delay').html();
                //$(parent_div).append(time);
                var flowId = $('#flow_id').val();
                var pathId = $('#path_id').val();
                // saveFlowAction(flowId, pathId, 'Time Delay');
                saveFlowActionRedefined(true, flowId, pathId, 'Time Delay');
            });

            $('#sendEmail').on('click', function() {
                //var parent_div = getParentDivId();
                var sendEmail = $('#send_message').html();
                //$(parent_div).append(sendEmail);
                var flowId = $('#flow_id').val();
                var pathId = $('#path_id').val();
                // saveFlowAction(flowId, pathId, 'Send Message');
                saveFlowActionRedefined(true, flowId, pathId, 'Send Message');
            });

            $('#whatsapp').on('click', function() {
                //var parent_div = getParentDivId();
                var sendWhatapp = $('#whatsapp').html();
                // $(parent_div).append(sendWhatapp);
                var flowId = $('#flow_id').val();
                var pathId = $('#path_id').val();
                // saveFlowAction(flowId, pathId, 'Whatsapp');
                saveFlowActionRedefined(true, flowId, pathId, 'Whatsapp');
            });

            $('#sms').on('click', function() {
                //var parent_div = getParentDivId();
                var sendSms = $('#sms').html();
                // $(parent_div).append(sendSms);
                var flowId = $('#flow_id').val();
                var pathId = $('#path_id').val();
                // saveFlowAction(flowId, pathId, 'SMS');
                saveFlowActionRedefined(true, flowId, pathId, 'SMS');
            });

            $('#condition').on('click', function() {
                //var parent_div = getParentDivId();
                var sendSms = $('#condition').html();
                // $(parent_div).append(sendSms);
                var flowId = $('#flow_id').val();
                var pathId = $('#path_id').val();
                // saveFlowAction(flowId, pathId, 'Condition');
                saveFlowActionRedefined(true, flowId, pathId, 'Condition');
            });


            //flow conditions and actions button inside popup - S 
            $("body").on('click', '.actions_inside_condition',function() {
                //(isParent, flowId, pathId, action_type, action_id) {
                var parent_div = '#'+$(this).attr('data-yes_no')+'_' + $(this).data('action_id');
                var flowId = $(this).attr('data-flowId');
                var pathId = $(this).attr('data-pathId');
                var action_type = $(this).attr('data-action_type');

                submitFlowActionDetails(flowId, pathId, action_type, parent_div);
                
                setTimeout(function() {
                    showFlow($("#flow_id").val());
                }, 1500);
            });
            //flow conditions and actions button inside popup - E

            /*  $(document).on('click','.cross div i',function(){
                  // event.preventDefault();
                 
              });*/
            function getParentDivId(id) {
                if (id == null) {
                    id = 'Collector';
                }
                var lastDiv = $('#' + id + ' > div.cross_first').last().data('type');
                if (lastDiv == 'condition') {
                    parent_div = '#' + $('#' + id + ' > div.cross_first').last().attr('id');
                } else {
                    parent_div = '#' + id;
                }
                return parent_div;
                /*var lastDiv = $('#Collector > div.cross_first').last().data('type'); 
                if(lastDiv == 'condition') {
                	parent_div =  '#'+$('#Collector > div.cross_first').last().attr('id');
                } else {
                	parent_div = '#Collector';
                }
                return parent_div;*/
            }

            function saveFlowAction(flowId, pathId, action_type) {
                var parent_div = getParentDivId();
                if (parent_div != '#Collector') {
                    $('#condition_modal').modal({
                        backdrop: 'static',
                        keyboard: false
                    }).one('click', '.yes_no', function(e) {
                        var parent_div = getParentDivId();
                        if ($(this).val() == 'yes') {
                            var action_id = $(parent_div).data('action_id');
                            var yes = 1;
                            var parent_div_new = getParentDivId('yes_' + action_id);
                            if (yes == 1 && parent_div != parent_div_new) {
                                //var result = $(parent_div_new +' > div.cross_first').last().data('type'); console.log(result);
                                var result = $(parent_div_new).data('type');
                                console.log(result);
                                if (result == 'condition') {
                                    $('#yes_conditions').show();
                                    $('#no_conditions').hide();
                                    //var parent_div = getParentDivId('yes_'+action_id); 
                                    parent_div = parent_div_new;
                                    var action_id = $(parent_div).data('action_id');
                                    yes++;
                                    $('#condition_modal').modal({
                                        backdrop: 'static',
                                        keyboard: false
                                    }).one('click', '.yes_yes_no', function(e) {
                                        var parent_div = getParentDivId('yes_' + action_id);
                                        if ($(this).val() == 'yes') {
                                            parent_div = '#yes_' + action_id;
                                            pathId = $('#yes_' + action_id).data('path_id');
                                            submitFlowActionDetails(flowId, pathId, action_type,
                                                parent_div);
                                        } else {
                                            parent_div = '#no_' + action_id;
                                            pathId = $('#no_' + action_id).data('path_id');
                                            submitFlowActionDetails(flowId, pathId, action_type,
                                                parent_div);
                                        }
                                    });
                                } else {
                                    parent_div = '#yes_' + action_id;
                                    pathId = $('#yes_' + action_id).data('path_id');
                                    submitFlowActionDetails(flowId, pathId, action_type, parent_div);
                                }
                            } else {
                                if (yes > 1) {
                                    alert('Max conditions added');
                                    return false;
                                }
                                //parent_div = '#yes_'+action_id;
                                //pathId = $('#yes_'+action_id).data('path_id'); 
                                submitFlowActionDetails(flowId, pathId, action_type, parent_div);
                            }
                        } else {
                            var action_id = $(parent_div).data('action_id');
                            var no = 1;
                            var parent_div_new = getParentDivId('no_' + action_id);
                            if (no == 1 && parent_div != parent_div_new) {
                                var result = $(parent_div_new).data('type');
                                console.log(result + ' - ' + parent_div_new);
                                if (result == 'condition') {
                                    $('#yes_conditions').hide();
                                    $('#no_conditions').show();
                                    parent_div = parent_div_new;
                                    var action_id = $(parent_div).data('action_id');
                                    no++;
                                    $('#condition_modal').modal({
                                        backdrop: 'static',
                                        keyboard: false
                                    }).one('click', '.no_yes_no', function(e) {
                                        var parent_div = getParentDivId('no_' + action_id);
                                        if ($(this).val() == 'yes') {
                                            parent_div = '#yes_' + action_id;
                                            pathId = $('#yes_' + action_id).data('path_id');
                                            submitFlowActionDetails(flowId, pathId, action_type,
                                                parent_div);
                                        } else {
                                            parent_div = '#no_' + action_id;
                                            pathId = $('#no_' + action_id).data('path_id');
                                            submitFlowActionDetails(flowId, pathId, action_type,
                                                parent_div);
                                        }
                                    });
                                } else {
                                    parent_div = '#no_' + action_id;
                                    pathId = $('#no_' + action_id).data('path_id');
                                    submitFlowActionDetails(flowId, pathId, action_type, parent_div);
                                }
                            } else {
                                alert('Max conditions added');
                                return false;
                                //parent_div = '#no_'+action_id;
                                //pathId = $('#no_'+action_id).data('path_id');
                                submitFlowActionDetails(flowId, pathId, action_type, parent_div);
                            }
                        }
                    });
                    return false;
                } else {
                    submitFlowActionDetails(flowId, pathId, action_type, parent_div);
                }
            }

            /**
             * New Function Flow Redefined - 04/Dec/2023 - CHECKPOINT
             * isParent = if click is happened from parent/main Header
             */
            function saveFlowActionRedefined(isParent, flowId, pathId, action_type, action_id) {
                var parent_div = getParentDivId();
          
                if (isParent !== true) {

               
                    $('#condition_modal').modal({
                        backdrop: 'static',
                        keyboard: false
                    }).one('click', '.yes_no', function(e) {
                        var parent_div = getParentDivId();
                        if ($(this).val() == 'yes') {
                            var action_id = $(parent_div).data('action_id');
                            var yes = 1;
                            var parent_div_new = getParentDivId('yes_' + action_id);
                            if (yes == 1 && parent_div != parent_div_new) {
                                //var result = $(parent_div_new +' > div.cross_first').last().data('type'); console.log(result);
                                var result = $(parent_div_new).data('type');
                                console.log(result);
                                if (result == 'condition') {
                                    $('#yes_conditions').show();
                                    $('#no_conditions').hide();
                                    //var parent_div = getParentDivId('yes_'+action_id); 
                                    parent_div = parent_div_new;
                                    var action_id = $(parent_div).data('action_id');
                                    yes++;
                                    $('#condition_modal').modal({
                                        backdrop: 'static',
                                        keyboard: false
                                    }).one('click', '.yes_yes_no', function(e) {
                                        var parent_div = getParentDivId('yes_' + action_id);
                                        if ($(this).val() == 'yes') {
                                            parent_div = '#yes_' + action_id;
                                            pathId = $('#yes_' + action_id).data('path_id');
                                            submitFlowActionDetails(flowId, pathId, action_type,
                                                parent_div);
                                        } else {
                                            parent_div = '#no_' + action_id;
                                            pathId = $('#no_' + action_id).data('path_id');
                                            submitFlowActionDetails(flowId, pathId, action_type,
                                                parent_div);
                                        }
                                    });
                                } else {
                                    parent_div = '#yes_' + action_id;
                                    pathId = $('#yes_' + action_id).data('path_id');
                                    submitFlowActionDetails(flowId, pathId, action_type, parent_div);
                                }
                            } else {
                                if (yes > 1) {
                                    alert('Max conditions added');
                                    return false;
                                }
                                //parent_div = '#yes_'+action_id;
                                //pathId = $('#yes_'+action_id).data('path_id'); 
                                submitFlowActionDetails(flowId, pathId, action_type, parent_div);
                            }
                        } else {
                            var action_id = $(parent_div).data('action_id');
                            var no = 1;
                            var parent_div_new = getParentDivId('no_' + action_id);
                            if (no == 1 && parent_div != parent_div_new) {
                                var result = $(parent_div_new).data('type');
                                console.log(result + ' - ' + parent_div_new);
                                if (result == 'condition') {
                                    $('#yes_conditions').hide();
                                    $('#no_conditions').show();
                                    parent_div = parent_div_new;
                                    var action_id = $(parent_div).data('action_id');
                                    no++;
                                    $('#condition_modal').modal({
                                        backdrop: 'static',
                                        keyboard: false
                                    }).one('click', '.no_yes_no', function(e) {
                                        var parent_div = getParentDivId('no_' + action_id);
                                        if ($(this).val() == 'yes') {
                                            parent_div = '#yes_' + action_id;
                                            pathId = $('#yes_' + action_id).data('path_id');
                                            submitFlowActionDetails(flowId, pathId, action_type,
                                                parent_div);
                                        } else {
                                            parent_div = '#no_' + action_id;
                                            pathId = $('#no_' + action_id).data('path_id');
                                            submitFlowActionDetails(flowId, pathId, action_type,
                                                parent_div);
                                        }
                                    });
                                } else {
                                    parent_div = '#no_' + action_id;
                                    pathId = $('#no_' + action_id).data('path_id');
                                    submitFlowActionDetails(flowId, pathId, action_type, parent_div);
                                }
                            } else {
                                alert('Max conditions added');
                                return false;
                                //parent_div = '#no_'+action_id;
                                //pathId = $('#no_'+action_id).data('path_id');
                                submitFlowActionDetails(flowId, pathId, action_type, parent_div);
                            }
                        }
                    });
                    return false;
                } else {
                    submitFlowActionDetails(flowId, pathId, action_type, parent_div);
                }
            }

            function submitFlowActionDetails(flowId, pathId, action_type, parent_div) {
                var data = {
                    "_token": "{{ csrf_token() }}",
                    'flow_id': flowId,
                    'path_id': pathId,
                    'action_type': action_type
                };
                $.ajax({
                    type: 'POST',
                    url: "{{ route('flow-update') }}",
                    data: data,
                    success: function(data) {
                        if (data.statusCode == 500) {
                            toastr["error"](data.message);
                        } else {
                            $('#Collector').html(data.message);
                        }
                    },
                    done: function(data) {
                        console.log('success ' + data);
                    }
                });
                setTimeout(function() {
                    $('#condition_modal').modal('hide');
                    $('.yes_no').prop('checked', false);
                    $('.no_yes_no').prop('checked', false);
                    $('.yes_yes_no').prop('checked', false);
                }, 1000);

            }

        });

        function showFlow(flow_id) {
            $.get(window.location.origin + "/flow/detail/" + flow_id, function(data) {
                $('#Collector').html(data);
                $('#flowPathModal').modal('show');
                $('#flowPathModal').addClass('in');
            });
        }

        function showMessagePopup(actionId) {
            $.get(window.location.origin + "/flow/action/message/" + actionId, function(data) {
                $('#message_content').html(data);
                $('#flow_message_action_id').val(actionId);
                $('#flowPathModal').modal('hide');
                $('#flowPathModal').removeClass('in');
                $('#messageModal').modal('show');
                $('#messageModal').addClass('in');
                tinymce.init({
                    selector: '#html_content'
                });
            });
        }
        $('.ajax-submit').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: $(this).attr('method'),
                url: $(this).attr('action'),
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.statusCode == 500) {
                        toastr["error"](data.message);
                    } else {
                        toastr["success"](data.message);
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    }
                },
                done: function(data) {
                    console.log('success ' + data);
                }
            });
        });
        $(document).on('change', '.condition_select', function(e) {
            //$('.condition_select').on('change', function(e) {  alert('changed');
            var flow_id = $('#flow_id').val();
            e.preventDefault();
            var option = {
                _token: "{{ csrf_token() }}",
                action_id: $(this).data('action_id'),
                'condition': $(this).val()
            };
            var route = $(this).attr('data-route');
            $.ajax({
                type: 'POST',
                url: "{{ route('update-condition') }}",
                data: option,
                success: function(response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        toastr["success"](response.message);
                    } else if (response.statusCode == 500) {
                        toastr["error"](response.message);
                    }
                    showFlow(flow_id);
                },
                error: function(data) {
                    $("#loading-image").hide();
                    alert('An error occurred.');
                }
            });
        });


        $(function() {
            $("#Collector").sortable();
            $("#Collector").disableSelection();
        });

        $(document).on('click', '.trigger-delete', function(e) {
            var id = $(this).attr('data-id');
            var flow_id = $('#flow_id').val();
            e.preventDefault();
            var option = {
                _token: "{{ csrf_token() }}",
                id: id
            };
            var route = $(this).attr('data-route');
            $("#loading-image").show();
            $.ajax({
                type: 'post',
                url: route,
                data: option,
                success: function(response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        $(this).closest('tr').remove();
                        toastr["success"](response.message);

                    } else if (response.statusCode == 500) {
                        toastr["error"](response.message);
                    }

                    showFlow(flow_id);
                    location.reload();
                },
                error: function(data) {
                    $("#loading-image").hide();
                    alert('An error occurred.');
                }
            });
        });
    </script>
@endsection
